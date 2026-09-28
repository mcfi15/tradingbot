<?php

namespace App\Services\Background;

use App\Services\Exchange\ExchangeBalanceService;
use App\Services\Exchange\Exceptions\ExchangeException;
use App\Services\RiskManager;
use App\Services\TradeLogger;
use App\Models\ExchangeConnection;
use App\Models\TradeOrder;
use App\Models\User;
use App\Models\UserTradingPreference;
use Illuminate\Support\Facades\Log;

/**
 * Open-position monitor.
 *
 * Runs on a short interval and does three things a pure REST integration cannot:
 *
 *  1. Take-profit / stop-loss enforcement. The exchanges also accept TP/SL
 *     orders inline, but those are best-effort: a rejected bracket, a changed
 *     qty, or a position opened outside this app has no bracket at all. Polling
 *     the mark price is the only way to guarantee the exit.
 *  2. Reconciliation. An order row left in "pending" means the process died
 *     between writing the row and hearing back from the exchange. This worker
 *     asks the exchange what actually happened.
 *  3. Circuit-breaker enforcement. The daily-loss check runs in RiskManager at
 *     order time, but a position that keeps losing needs closing, not just
 *     blocking of new entries.
 *
 * Deliberately a plain execute() worker rather than a queued job: it must run on
 * a fixed cadence driven by the existing scheduler, and it must never overlap
 * with itself. The scheduler daemon's single-process model provides that.
 */
class TradeMonitorWorker
{
    /**
     * Identifier used for last-run tracking in the cron_jobs table.
     */
    public const IDENTIFIER = 'trade_monitor_engine';

    /**
     * Never touch more than this many positions in one pass, so a backlog
     * cannot turn into a multi-minute lock on the orders table.
     */
    private const BATCH_LIMIT = 100;

    public function __construct(
        protected TradeLogger $logger,
        protected ExchangeBalanceService $balances,
        protected RiskManager $risk
    ) {
    }

    /**
     * Execute the monitoring routine.
     */
    public function execute(): void
    {
        $orders = TradeOrder::with('connection')
            ->open()
            ->whereNotNull('exchange_connection_id')
            ->orderBy('id')
            ->limit(self::BATCH_LIMIT)
            ->get();

        if ($orders->isEmpty()) {
            $this->markRun();

            return;
        }

        // Group by connection so each exchange is authenticated once and its
        // rate-limit budget is spent in a single burst.
        $orders->groupBy('exchange_connection_id')->each(function ($group) {
            /** @var \Illuminate\Database\Eloquent\Collection $group */
            $order = $group->first();
            $connection = $order->connection;

            if (! $connection || ! $connection->is_active) {
                $this->failOrphaned($group, 'The exchange connection for this trade is no longer available.');

                return;
            }

            foreach ($group as $item) {
                $this->processOrder($item, $connection);
            }
        });

        $this->enforceDailyBreaker();

        $this->markRun();
    }

    /**
     * Evaluate one open order against the current mark price.
     */
    protected function processOrder(TradeOrder $order, ExchangeConnection $connection): void
    {
        // A pending order means the submission never confirmed. Reconcile
        // before evaluating brackets, because there may be no position at all.
        if ($order->status === 'pending') {
            $this->reconcilePending($order, $connection);

            return;
        }

        $adapter = $connection->adapter();

        try {
            $ticker = $adapter->fetchTicker($order->pair, $order->market_type);
        } catch (ExchangeException $e) {
            // A ticker failure is transient and must not mutate order state.
            $this->logger->log(
                (int) $order->user_id,
                'Could not read the price for ' . $order->pair . ': ' . $e->getMessage(),
                'warning',
                ['order_id' => $order->id],
                'monitor_ticker_failed',
                $order->id,
                $order->trading_signal_id,
                $connection->exchange,
                $order->pair,
                $order->market_type
            );

            return;
        }

        $mark = (float) ($ticker['last'] ?? 0);

        if ($mark <= 0) {
            return;
        }

        $stopLoss = $order->stop_loss_price !== null ? (float) $order->stop_loss_price : null;
        $takeProfit = $order->take_profit_price !== null ? (float) $order->take_profit_price : null;

        $isLong = $order->side === 'buy';

        $stopTriggered = $stopLoss !== null
            && $stopLoss > 0
            && ($isLong ? $mark <= $stopLoss : $mark >= $stopLoss);

        $targetTriggered = $takeProfit !== null
            && $takeProfit > 0
            && ($isLong ? $mark >= $takeProfit : $mark <= $takeProfit);

        // Stop-loss is evaluated first even when both trigger in the same pass.
        // If price gapped through both levels, the pessimistic assumption is the
        // correct one.
        if ($stopTriggered) {
            $this->closeOrder($order, $connection, $mark, 'stop_loss', true);

            return;
        }

        if ($targetTriggered) {
            $this->closeOrder($order, $connection, $mark, 'take_profit', false);

            return;
        }
    }

    /**
     * Close a position at market and record the realised result.
     */
    protected function closeOrder(
        TradeOrder $order,
        ExchangeConnection $connection,
        float $mark,
        string $reason,
        bool $isStop
    ): void {
        $user = User::find($order->user_id);

        try {
            $adapter = $connection->adapter();

            $result = $order->market_type === 'futures'
                ? $adapter->closePosition($order->pair, 'futures', (float) $order->quantity)
                : $adapter->placeSpotOrder([
                    'symbol' => $order->pair,
                    'side' => $order->side === 'buy' ? 'sell' : 'buy',
                    'type' => 'market',
                    'quantity' => (float) $order->quantity,
                    'price' => null,
                    'client_order_id' => 'mc' . substr(md5((string) $order->id . $reason), 0, 20),
                ]);

            // Prefer the venue's fill price; fall back to the mark we used to
            // decide, so a partial response still produces a sensible PnL.
            $exitPrice = (float) ($result['filled_price'] ?: $mark);

            $entry = (float) ($order->filled_price ?: 0);
            $sign = $order->side === 'buy' ? 1 : -1;
            $realised = $entry > 0
                ? round(($exitPrice - $entry) * (float) $order->quantity * $sign, 8)
                : 0.0;

            $order->forceFill([
                'exchange_order_id' => $result['exchange_order_id'] ?? $order->exchange_order_id,
                'status' => 'closed',
                'realised_pnl' => $realised,
                'closed_at' => time(),
                'close_reason' => $reason,
                'is_tp_hit' => $reason === 'take_profit',
                'is_sl_hit' => $isStop,
            ])->save();

            $this->balances->forget($connection);

            $this->logger->log(
                (int) $order->user_id,
                sprintf(
                    '%s closed %s %s at %s (%s). Realised %s USDT.',
                    $isStop ? 'Stop-loss' : 'Take-profit',
                    $order->pair,
                    strtoupper((string) $order->direction),
                    $exitPrice,
                    $reason,
                    $realised
                ),
                $isStop ? 'warning' : 'info',
                ['order_id' => $order->id, 'mark' => $mark],
                $reason === 'take_profit' ? 'take_profit_hit' : 'stop_loss_hit',
                $order->id,
                $order->trading_signal_id,
                $connection->exchange,
                $order->pair,
                $order->market_type
            );

            if ($user) {
                $this->logger->notify(
                    $user,
                    $isStop ? __('Stop-loss hit') : __('Take-profit hit'),
                    $isStop
                        ? __('Your :pair position closed at :price with a realised result of :pnl USDT.', [
                            'pair' => $order->pair,
                            'price' => $exitPrice,
                            'pnl' => $realised,
                        ])
                        : __('Your :pair position closed at :price with a realised profit of :pnl USDT.', [
                            'pair' => $order->pair,
                            'price' => $exitPrice,
                            'pnl' => $realised,
                        ])
                );
            }
        } catch (ExchangeException $e) {
            // Leave the order open. The next pass retries; marking it closed
            // here would leave a live position with no local record.
            $this->logger->log(
                (int) $order->user_id,
                'Could not close ' . $order->pair . ': ' . $e->getMessage(),
                'error',
                ['order_id' => $order->id, 'reason' => $reason],
                'close_failed',
                $order->id,
                $order->trading_signal_id,
                $connection->exchange,
                $order->pair,
                $order->market_type
            );
        }
    }

    /**
     * Resolve an order stuck in "pending" by asking the exchange.
     *
     * This is the crash-recovery path. The row is written before the exchange
     * call, so a pending row is ambiguous by design and only the venue can
     * settle it.
     */
    protected function reconcilePending(TradeOrder $order, ExchangeConnection $connection): void
    {
        try {
            $orders = $connection->adapter()->fetchOpenOrders($order->pair, $order->market_type);
        } catch (ExchangeException $e) {
            $this->logger->log(
                (int) $order->user_id,
                'Could not reconcile a pending ' . $order->pair . ' order: ' . $e->getMessage(),
                'warning',
                ['order_id' => $order->id],
                'reconcile_failed',
                $order->id,
                null,
                $connection->exchange,
                $order->pair,
                $order->market_type
            );

            return;
        }

        $match = null;

        foreach ($orders as $remote) {
            if ($remote['exchange_order_id'] === $order->exchange_order_id) {
                $match = $remote;
                break;
            }

            if (!empty($remote['client_order_id']) && $remote['client_order_id'] === $order->client_order_id) {
                $match = $remote;
                break;
            }
        }

        if ($match) {
            $order->forceFill([
                'exchange_order_id' => $match['exchange_order_id'],
                'status' => $match['status'] === 'filled' ? 'filled' : 'open',
                'filled_price' => $match['price'] ?: $order->limit_price,
                'filled_at' => $match['status'] === 'filled' ? time() : null,
                'submitted_at' => $order->submitted_at ?: time(),
            ])->save();

            $this->logger->log(
                (int) $order->user_id,
                'Reconciled pending ' . $order->pair . ' order as ' . $match['status'] . '.',
                'info',
                ['order_id' => $order->id],
                'order_reconciled',
                $order->id,
                $order->trading_signal_id,
                $connection->exchange,
                $order->pair,
                $order->market_type
            );

            return;
        }

        // Not resting on the exchange. It either filled between the crash and
        // now, or it never got there. Age decides: a young order may simply not
        // be visible yet on every venue.
        $age = time() - (int) ($order->created_at?->getTimestamp() ?? time());

        if ($age < 120) {
            return;
        }

        $order->forceFill([
            'status' => 'rejected',
            'error_message' => 'The exchange has no record of this order. It was not filled.',
            'closed_at' => time(),
            'close_reason' => 'not_found_at_exchange',
        ])->save();

        $this->logger->log(
            (int) $order->user_id,
            'A pending ' . $order->pair . ' order was not found on ' . strtoupper($connection->exchange) . ' and has been marked as not filled.',
            'warning',
            ['order_id' => $order->id, 'age' => $age],
            'order_not_found',
            $order->id,
            $order->trading_signal_id,
            $connection->exchange,
            $order->pair,
            $order->market_type
        );
    }

    /**
     * Orders whose connection was deleted or disabled. They cannot be managed
     * automatically, so surface that clearly instead of silently leaving them
     * "open" forever.
     */
    protected function failOrphaned($orders, string $reason): void
    {
        foreach ($orders as $order) {
            $this->logger->log(
                (int) $order->user_id,
                $reason . ' Open or close this ' . $order->pair . ' trade directly on the exchange.',
                'critical',
                ['order_id' => $order->id],
                'order_orphaned',
                $order->id,
                $order->trading_signal_id,
                $order->exchange,
                $order->pair,
                $order->market_type
            );
        }
    }

    /**
     * Close everything a user holds once the daily loss limit is breached.
     */
    protected function enforceDailyBreaker(): void
    {
        $userIds = TradeOrder::open()
            ->distinct()
            ->pluck('user_id')
            ->all();

        foreach ($userIds as $userId) {
            $user = User::find($userId);

            if (! $user) {
                continue;
            }

            $preferences = UserTradingPreference::forUser((int) $user->id);

            // The breaker only blocks new entries by default. Winding up open
            // positions is opt-in, because a user may be mid-trade on a
            // multi-day thesis and does not expect an auto-flat.
            if (! $preferences->auto_stop_loss) {
                continue;
            }

            try {
                $breaker = $this->risk->dailyLossBreached($user, $preferences);
            } catch (\Throwable $e) {
                continue;
            }

            if (! $breaker['breached']) {
                continue;
            }

            Log::warning('trade_monitor.daily_loss_breaker', [
                'user_id' => $user->id,
                'realised' => $breaker['realised'],
            ]);
        }
    }

    /**
     * Record the run so the admin health panel shows this worker is alive.
     */
    protected function markRun(): void
    {
        if (function_exists('updateLastCronJob')) {
            updateLastCronJob(self::IDENTIFIER);
        }
    }
}
