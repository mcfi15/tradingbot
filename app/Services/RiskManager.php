<?php

namespace App\Services;

use App\Services\Exchange\ExchangeBalanceService;
use App\Services\Exchange\Exceptions\ExchangeException;
use App\Models\ExchangeConnection;
use App\Models\TradeOrder;
use App\Models\User;
use App\Models\UserTradingPreference;
use App\Models\TradingSignal;
use Illuminate\Support\Facades\Log;

/**
 * Pre-trade risk gate.
 *
 * Every order — manual or automated — passes through approve() before a single
 * byte goes to an exchange. Centralising the checks here is what makes them
 * enforceable: a new call site cannot accidentally skip the concurrent-trade cap
 * or the daily loss breaker because there is only one implementation.
 *
 * approve() returns a decision, never a side effect. The caller submits the
 * order; the gate only decides whether it is allowed to proceed.
 */
class RiskManager
{
    public function __construct(protected ExchangeBalanceService $balances)
    {
    }

    /**
     * @return array{
     *     allowed: bool,
     *     reason: string|null,
     *     code: string|null,
     *     quantity: float|null,
     *     notional: float|null,
     *     entry_price: float|null,
     *     take_profit_price: float|null,
     *     stop_loss_price: float|null,
     *     leverage: int
     * }
     */
    public function approve(
        User $user,
        ExchangeConnection $connection,
        string $pair,
        string $side,
        ?float $quantity = null,
        ?float $entryPrice = null,
        ?float $leverage = null,
        ?float $takeProfit = null,
        ?float $stopLoss = null,
        bool $automated = false
    ): array {
        $preferences = UserTradingPreference::forUser((int) $user->id);

        $reject = fn (string $code, string $reason) => [
            'allowed' => false,
            'reason' => $reason,
            'code' => $code,
            'quantity' => null,
            'notional' => null,
            'entry_price' => null,
            'take_profit_price' => null,
            'stop_loss_price' => null,
            'leverage' => 1,
        ];

        if (! $connection->is_active) {
            return $reject('connection_inactive', __('This exchange connection is disabled.'));
        }

        if ((int) $connection->user_id !== (int) $user->id) {
            // Defensive: a connection belonging to another user must never be
            // usable, even if a caller passes one in directly.
            return $reject('connection_forbidden', __('This exchange connection does not belong to you.'));
        }

        if ($automated && ! $preferences->auto_trading_mode) {
            return $reject('auto_disabled', __('Auto-Trading Mode is switched off.'));
        }

        $isLong = in_array(strtolower($side), ['buy', 'long'], true);
        $leverage = max(1, min(125, (int) ($leverage ?? 1)));

        // Futures only: leverage is meaningless on spot and a spot order with
        // leverage 10 would misrepresent the position to the user.
        if ($connection->market_type === 'spot') {
            $leverage = 1;
        }

        $cap = (int) $preferences->max_concurrent_trades;

        if ($cap > 0) {
            $open = TradeOrder::where('user_id', $user->id)->open()->count();

            if ($open >= $cap) {
                return $reject(
                    'max_concurrent_trades',
                    __('You already have :count open trades. Your limit is :cap.', [
                        'count' => $open,
                        'cap' => $cap,
                    ])
                );
            }
        }

        try {
            $breaker = $this->dailyLossBreached($user, $preferences);

            if ($breaker['breached']) {
                return $reject('max_daily_loss', $breaker['reason']);
            }
        } catch (\Throwable $e) {
            // A balance endpoint failure must not silently authorise a trade.
            // Fail closed: the user retries when the exchange is reachable.
            Log::error('risk.daily_loss_check_failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return $reject(
                'risk_check_unavailable',
                __('Could not verify your risk limits right now. Please try again shortly.')
            );
        }

        $price = $entryPrice;

        if ($price === null || $price <= 0) {
            try {
                $ticker = $connection->adapter()->fetchTicker($pair, $connection->market_type);
                $price = $ticker['last'];
            } catch (ExchangeException $e) {
                return $reject('price_unavailable', __('Could not read the current price for :pair.', ['pair' => $pair]));
            }
        }

        if ($price <= 0) {
            return $reject('price_invalid', __('The exchange returned an invalid price for :pair.', ['pair' => $pair]));
        }

        $free = $this->balances->freeQuoteBalance($connection);

        if ($free <= 0) {
            return $reject(
                'no_balance',
                __('Your :exchange :market wallet has no spendable :quote balance.', [
                    'exchange' => strtoupper($connection->exchange),
                    'market' => $connection->market_type,
                    'quote' => 'USDT',
                ])
            );
        }

        $resolvedQuantity = $quantity;

        if ($resolvedQuantity === null || $resolvedQuantity <= 0) {
            $resolvedQuantity = $preferences->resolveQuantity($free, $price, $leverage);
        }

        if ($resolvedQuantity === null || $resolvedQuantity <= 0) {
            return $reject(
                'quantity_unresolved',
                __('Your risk settings produced a trade size of zero. Increase the risk percentage or max trade size.')
            );
        }

        $notional = $resolvedQuantity * $price;

        $maxNotional = (float) $preferences->max_trade_size;

        if ($maxNotional > 0 && $notional > $maxNotional) {
            // Clamp rather than reject: the user asked for a size their own
            // rules forbid, and silently shrinking to the cap is the behaviour
            // they configured. The clamp is logged so it is never invisible.
            $clampedQuantity = $preferences->resolveQuantity($free, $price, $leverage);
            $clampedNotional = (float) $clampedQuantity * $price;

            Log::info('risk.size_clamped', [
                'user_id' => $user->id,
                'requested_notional' => round($notional, 2),
                'allowed_notional' => round($clampedNotional, 2),
                'max_trade_size' => $maxNotional,
            ]);

            $resolvedQuantity = $clampedQuantity;
            $notional = $clampedNotional;
        }

        $margin = $notional / $leverage;

        if ($margin > $free) {
            return $reject(
                'insufficient_margin',
                __('This trade needs :needed :quote of margin but only :available is available.', [
                    'needed' => number_format($margin, 2),
                    'available' => number_format($free, 2),
                    'quote' => 'USDT',
                ])
            );
        }

        // Explicit targets from the caller (a signal, or the manual form) win.
        // Only when absent are they derived from the user's percentages.
        $bracket = $preferences->resolveBracket($price, $isLong);

        $takeProfit = $takeProfit ?? $bracket['take_profit_price'];
        $stopLoss = $stopLoss ?? $bracket['stop_loss_price'];

        // A stop on the wrong side of entry is a guaranteed loss or an invalid
        // order, depending on the venue. Correct rather than reject, because the
        // user set the percentages, not the derived price.
        if ($stopLoss !== null && $stopLoss > 0) {
            $isValid = $isLong ? $stopLoss < $price : $stopLoss > $price;

            if (! $isValid) {
                Log::warning('risk.stop_loss_corrected', [
                    'user_id' => $user->id,
                    'pair' => $pair,
                    'side' => $side,
                    'price' => $price,
                    'stop_loss' => $stopLoss,
                ]);

                $stopLoss = null;
            }
        }

        if ($takeProfit !== null && $takeProfit > 0) {
            $isValid = $isLong ? $takeProfit > $price : $takeProfit < $price;

            if (! $isValid) {
                $takeProfit = null;
            }
        }

        return [
            'allowed' => true,
            'reason' => null,
            'code' => null,
            'quantity' => round((float) $resolvedQuantity, 8),
            'notional' => round($notional, 8),
            'entry_price' => round($price, 8),
            'take_profit_price' => $takeProfit,
            'stop_loss_price' => $stopLoss,
            'leverage' => $leverage,
        ];
    }

    /**
     * Evaluate the daily loss circuit breaker.
     *
     * The trading day rolls at 00:00 UTC, so the window is derived from the
     * timestamp rather than a cron-driven reset column, which keeps the breaker
     * correct even if the scheduler does not run.
     *
     * @return array{breached: bool, reason: string|null, realised: float, window_start: int}
     */
    public function dailyLossBreached(User $user, ?UserTradingPreference $preferences = null): array
    {
        $preferences = $preferences ?: UserTradingPreference::forUser((int) $user->id);

        $windowStart = strtotime('today midnight', time());

        $closed = TradeOrder::where('user_id', $user->id)
            ->where('status', 'closed')
            ->whereNotNull('closed_at')
            ->where('closed_at', '>=', $windowStart);

        $realised = (float) $closed->sum('realised_pnl');

        if ($preferences->daily_loss_reset_at !== null && $preferences->daily_loss_reset_at < $windowStart) {
            $preferences->forceFill(['daily_loss_reset_at' => $windowStart])->saveQuietly();
        }

        $startingBalance = max(
            0.0,
            (float) ExchangeConnection::where('user_id', $user->id)->where('is_active', true)->sum('last_total_balance')
        );

        $breached = $realised < 0 && $preferences->dailyLossBreached(abs($realised), $startingBalance);

        return [
            'breached' => $breached,
            'reason' => $breached
                ? __('Daily loss limit reached (:limit% of your wallet). New trades are paused until tomorrow.', [
                    'limit' => (float) $preferences->max_daily_loss_percentage,
                ])
                : null,
            'realised' => $realised,
            'window_start' => $windowStart,
        ];
    }

    /**
     * Build the risk snapshot persisted alongside an automated trade, so an
     * audit can explain the size long after the user edits their settings.
     */
    public function snapshot(User $user, ExchangeConnection $connection, array $decision, ?TradingSignal $signal = null): array
    {
        $preferences = UserTradingPreference::forUser((int) $user->id);

        return [
            'captured_at' => time(),
            'connection_id' => $connection->id,
            'exchange' => $connection->exchange,
            'market_type' => $connection->market_type,
            'signal_id' => $signal?->id,
            'signal_pair' => $signal?->pair,
            'risk_percentage' => (float) $preferences->risk_percentage,
            'max_trade_size' => (float) $preferences->max_trade_size,
            'max_concurrent_trades' => (int) $preferences->max_concurrent_trades,
            'auto_stop_loss' => (bool) $preferences->auto_stop_loss,
            'auto_trading_mode' => (bool) $preferences->auto_trading_mode,
            'leverage' => $decision['leverage'] ?? 1,
            'entry_price' => $decision['entry_price'] ?? null,
            'notional' => $decision['notional'] ?? null,
            'quantity' => $decision['quantity'] ?? null,
        ];
    }
}
