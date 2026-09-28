<?php

namespace App\Services;

use App\Events\SignalCreated;
use App\Jobs\ExecuteAutoTradeJob;
use App\Models\ExchangeConnection;
use App\Models\SignalFollow;
use App\Models\TradingSignal;
use App\Models\User;
use App\Models\UserTradingPreference;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Publishes a signal and hands it to the automated execution engine.
 *
 * The fan-out is the delicate part. Ordering matters:
 *
 *  1. The signal is persisted first, so the broadcast payload always has a
 *     durable id the client can reconcile against.
 *  2. The broadcast is dispatched second.
 *  3. Auto-trade jobs are dispatched last.
 *
 * That order is deliberate. Broadcasting first would let a client click "Trade
 * Now" on a signal whose row does not exist yet, and dispatching jobs before
 * the broadcast would let an automated fill appear with no explanation on screen.
 */
class SignalDispatchService
{
    public function __construct(protected TradeLogger $logger)
    {
    }

    /**
     * Create a signal, broadcast it, and queue it for every auto-following user.
     *
     * @param array $attributes Columns for the trading_signals table, minus
     *                          base_asset/quote_asset/signal_time which are
     *                          derived here when omitted.
     */
    public function publish(array $attributes): TradingSignal
    {
        $attributes += [
            'base_asset' => null,
            'quote_asset' => 'USDT',
            'signal_time' => time(),
            'status' => 'active',
            'confidence' => 0,
        ];

        if (empty($attributes['base_asset'])) {
            [$base, $quote] = $this->splitPair((string) $attributes['pair']);

            $attributes['base_asset'] = $base;
            $attributes['quote_asset'] = $quote;
        }

        $signal = TradingSignal::create($attributes);

        $this->bumpPollCursor($signal);
        $this->broadcast($signal);
        $this->fanOutToAutoFollowers($signal);

        return $signal;
    }

    /**
     * Broadcast the signal. Isolated in its own method so a broadcasting failure
     * cannot prevent the orders from being queued.
     */
    protected function broadcast(TradingSignal $signal): void
    {
        try {
            SignalCreated::dispatch($signal);
        } catch (\Throwable $e) {
            // Reverb not running, or no broadcaster configured. The client
            // polling fallback covers this, so it must not be fatal.
            Log::warning('signal.broadcast_failed', [
                'signal_id' => $signal->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Queue one ExecuteAutoTradeJob per user who has auto-trading on and is
     * following this signal.
     *
     * Each user is matched to their default connection for the signal's market
     * type. Users without a usable connection are logged and skipped rather
     * than being handed a job that cannot succeed.
     */
    protected function fanOutToAutoFollowers(TradingSignal $signal): int
    {
        try {
            $followers = SignalFollow::where('trading_signal_id', $signal->id)
                ->where('mode', 'auto')
                ->whereIn('status', ['pending', 'queued'])
                ->get();
        } catch (\Throwable $e) {
            Log::error('signal.followers_unavailable', [
                'signal_id' => $signal->id,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }

        $queued = 0;

        foreach ($followers as $follow) {
            $user = User::find($follow->user_id);

            if (! $user) {
                $follow->update(['status' => 'failed', 'error_message' => 'The account no longer exists.']);

                continue;
            }

            $preferences = UserTradingPreference::forUser((int) $user->id);

            if (! $preferences->auto_trading_mode) {
                $follow->update(['status' => 'skipped', 'error_message' => 'Auto-Trading Mode is switched off.']);

                continue;
            }

            $connection = $this->resolveConnection($user, $signal, $preferences);

            if (! $connection) {
                $follow->update([
                    'status' => 'failed',
                    'error_message' => 'No active ' . $signal->market_type . ' exchange connection is available.',
                ]);

                $this->logger->log(
                    (int) $user->id,
                    'Skipped auto-trade: no active ' . $signal->market_type . ' exchange connection.',
                    'warning',
                    ['signal_id' => $signal->id],
                    'auto_trade_no_connection',
                    null,
                    $signal->id,
                    null,
                    $signal->pair,
                    $signal->market_type
                );

                continue;
            }

            $follow->update(['status' => 'queued']);

            ExecuteAutoTradeJob::dispatch($follow->id, $connection->id);

            $queued++;
        }

        return $queued;
    }

    /**
     * Pick the connection an automated trade should use: an explicit default
     * from the user's preferences, else their first active connection for the
     * signal's market type.
     */
    protected function resolveConnection(
        User $user,
        TradingSignal $signal,
        UserTradingPreference $preferences
    ): ?ExchangeConnection {
        $query = ExchangeConnection::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('market_type', $signal->market_type);

        if ($preferences->default_exchange) {
            $preferred = (clone $query)->where('exchange', $preferences->default_exchange)->first();

            if ($preferred) {
                return $preferred;
            }
        }

        return $query->orderBy('id')->first();
    }

    /**
     * Register a user's interest in a signal.
     *
     * Toggling off deletes the follow row so re-following later starts from a
     * clean state rather than reviving a stale risk snapshot.
     */
    public function follow(User $user, TradingSignal $signal, string $mode = 'auto'): SignalFollow
    {
        $existing = SignalFollow::where('user_id', $user->id)
            ->where('trading_signal_id', $signal->id)
            ->first();

        if ($existing && $existing->mode === $mode && $existing->status !== 'failed') {
            return $existing;
        }

        $follow = $existing ?: new SignalFollow([
            'user_id' => $user->id,
            'trading_signal_id' => $signal->id,
        ]);

        $follow->mode = $mode;
        $follow->status = 'pending';
        $follow->error_message = null;
        $follow->save();

        // Auto-following a signal that already exists must act now, not wait
        // for the next broadcast.
        if ($mode === 'auto' && $signal->status === 'active' && ! $signal->is_expired) {
            $this->dispatchFollow($follow, $signal);
        }

        return $follow;
    }

    /**
     * Stop following a signal.
     */
    public function unfollow(User $user, TradingSignal $signal): void
    {
        $follow = SignalFollow::where('user_id', $user->id)
            ->where('trading_signal_id', $signal->id)
            ->first();

        if (! $follow) {
            return;
        }

        // Only unexecuted follows are removed. An executed one is a record of a
        // real trade and must survive for the audit trail.
        if (in_array($follow->status, ['executed', 'queued'], true)) {
            $follow->update(['status' => 'executed']);

            return;
        }

        $follow->delete();
    }

    /**
     * Queue a single follow immediately, independent of a new broadcast.
     */
    public function dispatchFollow(SignalFollow $follow, ?TradingSignal $signal = null): bool
    {
        $signal = $signal ?: $follow->signal;

        if (! $signal) {
            return false;
        }

        $user = User::find($follow->user_id);

        if (! $user) {
            return false;
        }

        $preferences = UserTradingPreference::forUser((int) $user->id);

        if ($follow->mode === 'auto' && ! $preferences->auto_trading_mode) {
            $follow->update(['status' => 'skipped', 'error_message' => 'Auto-Trading Mode is switched off.']);

            return false;
        }

        $connection = $this->resolveConnection($user, $signal, $preferences);

        if (! $connection) {
            $follow->update([
                'status' => 'failed',
                'error_message' => 'No active ' . $signal->market_type . ' exchange connection is available.',
            ]);

            return false;
        }

        $follow->update(['status' => 'queued']);

        ExecuteAutoTradeJob::dispatch($follow->id, $connection->id);

        return true;
    }

    /**
     * Mark a signal closed and write off any follows still waiting on it.
     */
    public function close(TradingSignal $signal, string $status = 'closed'): void
    {
        $signal->update(['status' => $status]);

        DB::table('signal_follows')
            ->where('trading_signal_id', $signal->id)
            ->whereIn('status', ['pending', 'queued'])
            ->update([
                'status' => 'skipped',
                'error_message' => 'The signal was ' . $status . ' before execution.',
                'updated_at' => now(),
            ]);

        Log::info('signal.closed', ['signal_id' => $signal->id, 'status' => $status]);
    }

    /**
     * Signals newer than a cursor, for the client polling fallback.
     *
     * @return array{cursor: int, signals: array<int, array>}
     */
    public function since(int $cursor, int $limit = 20, ?string $marketType = null): array
    {
        $query = TradingSignal::query()
            ->where('id', '>', $cursor)
            ->orderBy('id')
            ->limit(min(50, max(1, $limit)));

        $query->active()->forMarket($marketType);

        $signals = $query->get();

        $newest = $cursor;

        $payload = [];

        foreach ($signals as $signal) {
            $newest = max($newest, (int) $signal->id);
            $payload[] = $signal->toFeedArray();
        }

        return ['cursor' => $newest, 'signals' => $payload];
    }

    /**
     * The id the poll endpoint should start from on a cold load.
     */
    public function currentCursor(): int
    {
        $value = cache()->get('trading:latest_signal_id');

        if ($value !== null) {
            return (int) $value;
        }

        return (int) (TradingSignal::max('id') ?? 0);
    }

    /**
     * Cache the newest signal id so a client opening the feed mid-session
     * starts from "now" rather than replaying the entire history.
     */
    protected function bumpPollCursor(TradingSignal $signal): void
    {
        cache()->forever('trading:latest_signal_id', (int) $signal->id);
    }

    /**
     * Split "BTCUSDT" or "BTC/USDT" into base and quote.
     *
     * @return array{0: string, 1: string}
     */
    protected function splitPair(string $pair): array
    {
        $pair = strtoupper(trim($pair));

        if (str_contains($pair, '/')) {
            [$base, $quote] = explode('/', $pair, 2);

            return [$base, $quote];
        }

        foreach (config('exchange.quote_assets', ['USDT', 'USDC', 'BUSD']) as $quote) {
            $length = strlen($quote);

            if ($length < strlen($pair) && str_ends_with($pair, $quote)) {
                return [substr($pair, 0, -$length), $quote];
            }
        }

        return [substr($pair, 0, -4), substr($pair, -4)];
    }
}
