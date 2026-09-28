<?php

namespace App\Jobs;

use App\Services\Exchange\ExchangeBalanceService;
use App\Services\Exchange\Exceptions\ExchangeException;
use App\Services\RiskManager;
use App\Services\TradeLogger;
use App\Models\ExchangeConnection;
use App\Models\SignalFollow;
use App\Models\TradeOrder;
use App\Models\TradingSignal;
use App\Models\User;
use App\Models\UserTradingPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Place one order on behalf of a user who is following a signal automatically.
 *
 * Reliability contract:
 *
 *  - Idempotent. The client_order_id is derived from the follow id, so a retry
 *    after a timeout reuses the same id. Binance and Bybit both reject a
 *    duplicate client id, which turns a double-submit into a clean no-op
 *    instead of a doubled position.
 *  - The database row is written *before* the exchange call. If the process
 *    dies mid-request the order is already visible as pending, and the
 *    reconciler in TradeMonitorWorker can resolve it, rather than an order
 *    existing on the exchange with no local record.
 *  - Rate limits retry with exponential backoff; 4xx validation and auth errors
 *    fail immediately, because retrying them just burns the user's quota.
 */
class ExecuteAutoTradeJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * 4 attempts with 15/60/240 second backoff — roughly 5 minutes of total
     * patience, which matches a typical exchange rate-limit window.
     */
    public int $tries = 4;

    /** @var array<int, int> */
    public array $backoff = [15, 60, 240];

    public int $timeout = 90;

    public int $uniqueFor = 120;

    public function __construct(
        public int $followId,
        public int $connectionId
    ) {
        $this->onQueue(config('exchange.queues.trades', 'trades'));
    }

    /**
     * Unique per follow, so the same signal is never executed twice concurrently
     * for one user.
     */
    public function uniqueId(): string
    {
        return 'auto-trade:' . $this->followId;
    }

    public function handle(
        RiskManager $risk,
        TradeLogger $logger,
        ExchangeBalanceService $balances
    ): void {
        $follow = SignalFollow::with('signal')->find($this->followId);

        if (! $follow) {
            Log::info('auto_trade.follow_missing', ['follow_id' => $this->followId]);

            return;
        }

        if ($follow->status === 'executed') {
            // Already done. This is the idempotency guard that makes a retry
            // safe.
            return;
        }

        $signal = $follow->signal;

        if (! $signal) {
            $follow->update(['status' => 'failed', 'error_message' => 'The signal no longer exists.']);

            return;
        }

        if ($signal->status !== 'active' || $signal->is_expired) {
            $follow->update(['status' => 'skipped', 'error_message' => 'The signal is no longer active.']);

            $logger->log(
                (int) $follow->user_id,
                sprintf('Skipped signal %s: %s', $signal->pair, $signal->is_expired ? 'expired' : 'closed'),
                'info',
                ['signal_id' => $signal->id],
                'signal_skipped',
                null,
                $signal->id,
                $connection->exchange ?? config('exchange.supported')[0],
                $signal->pair,
                $signal->market_type
            );

            return;
        }

        $user = User::find($follow->user_id);
        $connection = ExchangeConnection::find($this->connectionId);

        if (! $user || ! $connection) {
            $follow->update([
                'status' => 'failed',
                'error_message' => 'The account or exchange connection is no longer available.',
            ]);

            return;
        }

        $preferences = UserTradingPreference::forUser((int) $user->id);

        if (! $preferences->auto_trading_mode) {
            // The user turned auto-trading off between dispatch and execution.
            // Respect it and do not open the position.
            $follow->update(['status' => 'skipped', 'error_message' => 'Auto-Trading Mode was switched off.']);

            return;
        }

        $isLong = $signal->is_long;
        $side = $isLong ? 'buy' : 'sell';
        $direction = $signal->market_type === 'futures'
            ? ($isLong ? 'long' : 'short')
            : $side;

        $logger->log(
            (int) $user->id,
            sprintf('Auto-executing %s %s from signal #%d', strtoupper($direction), $signal->pair, $signal->id),
            'info',
            ['signal_id' => $signal->id, 'mode' => 'auto'],
            'auto_trade_started',
            null,
            $signal->id,
            $connection->exchange,
            $signal->pair,
            $signal->market_type
        );

        $decision = $risk->approve(
            user: $user,
            connection: $connection,
            pair: $signal->pair,
            side: $side,
            quantity: null,
            entryPrice: (float) $signal->entry_price,
            leverage: $signal->market_type === 'futures' ? 10 : 1,
            takeProfit: $signal->target_2 !== null ? (float) $signal->target_2 : (float) $signal->target_1,
            stopLoss: $signal->stop_loss !== null ? (float) $signal->stop_loss : null,
            automated: true
        );

        if (! $decision['allowed']) {
            $follow->update(['status' => 'skipped', 'error_message' => $decision['reason']]);

            $logger->log(
                (int) $user->id,
                'Auto-trade blocked by risk rules: ' . $decision['reason'],
                'warning',
                ['code' => $decision['code'], 'signal_id' => $signal->id],
                'auto_trade_blocked',
                null,
                $signal->id,
                $connection->exchange,
                $signal->pair,
                $signal->market_type
            );

            return;
        }

        $clientOrderId = $this->clientOrderId($follow);

        $order = $this->reserveOrder($user, $follow, $signal, $connection, $decision, $clientOrderId, $risk);

        if (! $order) {
            // A previous attempt already created the row and is still in
            // flight. Let that attempt finish.
            return;
        }

        try {
            $adapter = $connection->adapter();

            $params = [
                'symbol' => $signal->pair,
                'side' => $side,
                'type' => 'market',
                'quantity' => $decision['quantity'],
                'price' => null,
                'leverage' => $decision['leverage'],
                'client_order_id' => $clientOrderId,
                'reduce_only' => false,
                'take_profit_price' => $decision['take_profit_price'],
                'stop_loss_price' => $decision['stop_loss_price'],
            ];

            $result = $signal->market_type === 'futures' || $connection->market_type === 'futures'
                ? $adapter->placeFuturesOrder($params)
                : $adapter->placeSpotOrder($params);

            $filledPrice = $result['filled_price'] ?? null;
            $filledQuantity = $result['filled_quantity'] ?? 0.0;

            // Market orders normally report a fill. When a venue returns none,
            // fall back to the planned price so the position maths and the
            // unrealised PnL are not silently zero.
            if (! $filledPrice || $filledPrice <= 0) {
                $filledPrice = $decision['entry_price'];
            }

            if (! $filledQuantity || $filledQuantity <= 0) {
                $filledQuantity = $decision['quantity'];
            }

            $status = match ($result['status']) {
                'filled' => 'filled',
                'cancelled', 'rejected' => 'rejected',
                default => 'open',
            };

            $order->forceFill([
                'exchange_order_id' => $result['exchange_order_id'],
                'status' => $status,
                'filled_price' => $filledPrice,
                'quantity' => $filledQuantity,
                'notional' => $filledPrice * $filledQuantity,
                'submitted_at' => time(),
                'filled_at' => $status === 'filled' ? time() : null,
                'error_message' => null,
            ])->save();

            $follow->update(['status' => 'executed', 'trade_order_id' => $order->id, 'error_message' => null]);

            $logger->log(
                (int) $user->id,
                sprintf(
                    '%s %s %s at %s on %s (%s)',
                    strtoupper($direction),
                    rtrim(rtrim(number_format((float) $filledQuantity, 8, '.', ''), '0'), '.'),
                    $signal->pair,
                    $filledPrice,
                    strtoupper($connection->exchange),
                    $status
                ),
                'info',
                ['order_id' => $order->id, 'exchange_order_id' => $result['exchange_order_id']],
                'auto_trade_filled',
                $order->id,
                $signal->id,
                $connection->exchange,
                $signal->pair,
                $signal->market_type
            );

            $logger->notify(
                $user,
                __('Trade opened'),
                sprintf(
                    __('Your automatic :direction trade in :pair is open at :price.'),
                    ['direction' => strtoupper($direction), 'pair' => $signal->pair, 'price' => $filledPrice]
                )
            );

            // The wallet just changed, so the cached balance is stale.
            $balances->forget($connection);
        } catch (ExchangeException $e) {
            $order->forceFill([
                'status' => 'rejected',
                'error_message' => mb_substr($e->getMessage(), 0, 2000),
                'closed_at' => time(),
            ])->save();

            // A credential or validation error will never succeed on retry.
            // Mark the follow failed now and release the retry budget.
            if (! $e->isRetryable()) {
                $follow->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

                $logger->logFailure(
                    $user,
                    $e,
                    ['order_id' => $order->id, 'signal_id' => $signal->id],
                    $order->id,
                    $signal->id,
                    $connection->exchange,
                    $signal->pair,
                    $signal->market_type
                );

                // Swallow the exception: the queue must not burn three more
                // attempts on a deterministic failure.
                return;
            }

            $follow->update(['status' => 'queued', 'error_message' => $e->getMessage()]);

            $logger->log(
                (int) $user->id,
                'Auto-trade attempt failed, will retry: ' . $e->getMessage(),
                'warning',
                ['order_id' => $order->id, 'attempt' => $this->tries, 'retry_after' => $e->retryAfter()],
                'auto_trade_retry',
                $order->id,
                $signal->id,
                $connection->exchange,
                $signal->pair,
                $signal->market_type
            );

            // Rethrow so the queue applies $backoff.
            throw $e;
        }
    }

    /**
     * Insert the pending order row, or return null when one already exists for
     * this follow.
     *
     * The unique index on client_order_id is the real concurrency control; the
     * duplicate-key catch is what makes that index safe under a race between
     * two workers.
     */
    protected function reserveOrder(
        User $user,
        SignalFollow $follow,
        TradingSignal $signal,
        ExchangeConnection $connection,
        array $decision,
        string $clientOrderId,
        RiskManager $risk
    ): ?TradeOrder {
        try {
            return DB::transaction(function () use ($user, $follow, $signal, $connection, $decision, $clientOrderId, $risk) {
                $order = TradeOrder::create([
                    'user_id' => $follow->user_id,
                    'exchange_connection_id' => $connection->id,
                    'trading_signal_id' => $signal->id,
                    'client_order_id' => $clientOrderId,
                    'exchange' => $connection->exchange,
                    'market_type' => $signal->market_type,
                    'pair' => $signal->pair,
                    'side' => $signal->is_long ? 'buy' : 'sell',
                    'direction' => $signal->market_type === 'futures'
                        ? ($signal->is_long ? 'long' : 'short')
                        : ($signal->is_long ? 'buy' : 'sell'),
                    'order_type' => 'market',
                    'leverage' => $decision['leverage'],
                    'quantity' => $decision['quantity'],
                    'notional' => $decision['notional'],
                    'take_profit_price' => $decision['take_profit_price'],
                    'stop_loss_price' => $decision['stop_loss_price'],
                    'status' => 'pending',
                    'origin' => 'auto',
                ]);

                $follow->forceFill([
                    'status' => 'queued',
                    'trade_order_id' => $order->id,
                    'risk_snapshot' => $risk->snapshot($user, $connection, $decision, $signal),
                ])->save();

                return $order;
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            Log::info('auto_trade.duplicate_reserved', ['client_order_id' => $clientOrderId]);

            return null;
        }
    }

    /**
     * Deterministic client order id. Stable across retries, which is what makes
     * the exchange reject a duplicate submission.
     */
    protected function clientOrderId(SignalFollow $follow): string
    {
        return 'ft' . Str::padLeft((string) $follow->id, 12, '0') . substr(md5('foyana:' . $follow->id), 0, 8);
    }

    /**
     * Terminal failure handler. Runs only after every attempt is exhausted.
     */
    public function failed(\Throwable $exception): void
    {
        $follow = SignalFollow::with('signal')->find($this->followId);

        if (! $follow) {
            return;
        }

        $follow->update([
            'status' => 'failed',
            'error_message' => mb_substr($exception->getMessage(), 0, 2000),
        ]);

        $order = $follow->trade_order_id ? TradeOrder::find($follow->trade_order_id) : null;

        if ($order && $order->status === 'pending') {
            // The exchange never confirmed it, so the order stays unfilled.
            $order->forceFill([
                'status' => 'rejected',
                'error_message' => mb_substr($exception->getMessage(), 0, 2000),
                'closed_at' => time(),
            ])->save();
        }

        Log::error('auto_trade.failed', [
            'follow_id' => $this->followId,
            'order_id' => $order?->id,
            'error' => $exception->getMessage(),
        ]);

        $user = User::find($follow->user_id);

        if ($user) {
            app(TradeLogger::class)->notify(
                $user,
                __('Automated trade failed'),
                __('We could not open the :pair trade automatically after several attempts. Check your exchange connection and risk settings.', [
                    'pair' => $follow->signal?->pair ?? '',
                ])
            );
        }
    }
}
