<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTradingPreference extends Model
{
    protected $fillable = [
        'user_id',
        'auto_trading_mode',
        'default_exchange',
        'default_market_type',
        'max_trade_size',
        'risk_percentage',
        'auto_stop_loss',
        'stop_loss_percentage',
        'take_profit_percentage',
        'max_concurrent_trades',
        'max_daily_loss_percentage',
        'slippage_tolerance',
        'daily_loss_reset_at',
    ];

    protected $casts = [
        'auto_trading_mode' => 'boolean',
        'auto_stop_loss' => 'boolean',
        'max_trade_size' => 'decimal:8',
        'risk_percentage' => 'decimal:2',
        'stop_loss_percentage' => 'decimal:2',
        'take_profit_percentage' => 'decimal:2',
        'max_concurrent_trades' => 'integer',
        'max_daily_loss_percentage' => 'decimal:2',
        'slippage_tolerance' => 'decimal:2',
        'daily_loss_reset_at' => 'integer',
    ];

    /**
     * Hard ceilings. User input can never exceed these, so a mistyped risk
     * percentage cannot escalate into an unbounded order.
     */
    public const LIMITS = [
        'max_trade_size' => [0, 1000000],
        'risk_percentage' => [0.01, 100],
        'stop_loss_percentage' => [0.1, 50],
        'take_profit_percentage' => [0.1, 500],
        'max_concurrent_trades' => [1, 25],
        'max_daily_loss_percentage' => [1, 100],
        'slippage_tolerance' => [0, 10],
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Resolution
    |--------------------------------------------------------------------------
    */

    /**
     * Fetch the user's preferences, creating a safe default row on first use.
     *
     * Reading preferences must never require a separate onboarding step, and it
     * must never be nullable at the call sites, so resolution is centralised
     * here.
     */
    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(['user_id' => $userId]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Derive an order quantity from the risk rules.
     *
     * Quantity is derived from risk_percentage applied to available quote
     * balance, then hard-capped by max_trade_size. Returns null when the inputs
     * cannot produce a tradable size, so the caller can record a clear reason
     * instead of submitting a zero-quantity order.
     */
    public function resolveQuantity(float $availableQuoteBalance, float $entryPrice, float $leverage = 1.0): ?float
    {
        if ($entryPrice <= 0 || $availableQuoteBalance <= 0) {
            return null;
        }

        $riskBudget = $availableQuoteBalance * ((float) $this->risk_percentage / 100);

        $leverage = max(1.0, (float) $leverage);
        $riskBudget *= $leverage;

        $cap = (float) $this->max_trade_size;
        $notional = $cap > 0 ? min($riskBudget, $cap) : $riskBudget;

        if ($notional <= 0) {
            return null;
        }

        // 8 decimals matches the DECIMAL(20,8) quantity columns across all
        // three exchanges, so no rounding drift at the exchange boundary.
        return round($notional / $entryPrice, 8);
    }

    /**
     * Derive take-profit and stop-loss from the entry price and direction.
     *
     * Returns an empty array when auto_stop_loss is disabled and no explicit
     * targets were supplied by the caller.
     */
    public function resolveBracket(float $entryPrice, bool $isLong): array
    {
        $bracket = [];

        $tpPct = (float) $this->take_profit_percentage;
        $slPct = (float) $this->stop_loss_percentage;

        $bracket['take_profit_price'] = $tpPct > 0
            ? round($entryPrice * (1 + (($isLong ? 1 : -1) * $tpPct / 100)), 8)
            : null;

        $bracket['stop_loss_price'] = ($this->auto_stop_loss && $slPct > 0)
            ? round($entryPrice * (1 - (($isLong ? 1 : -1) * $slPct / 100)), 8)
            : null;

        return $bracket;
    }

    /*
    |--------------------------------------------------------------------------
    | Circuit breaker
    |--------------------------------------------------------------------------
    */

    /**
     * True when realised losses today have breached max_daily_loss_percentage.
     */
    public function dailyLossBreached(float $realisedLossToday, float $startingBalance): bool
    {
        $limit = (float) $this->max_daily_loss_percentage;

        if ($limit <= 0 || $startingBalance <= 0) {
            return false;
        }

        return (abs($realisedLossToday) / $startingBalance) * 100 >= $limit;
    }

    public function toDisplayArray(): array
    {
        return [
            'auto_trading_mode' => (bool) $this->auto_trading_mode,
            'default_exchange' => $this->default_exchange,
            'default_market_type' => $this->default_market_type,
            'max_trade_size' => (float) $this->max_trade_size,
            'risk_percentage' => (float) $this->risk_percentage,
            'auto_stop_loss' => (bool) $this->auto_stop_loss,
            'stop_loss_percentage' => (float) $this->stop_loss_percentage,
            'take_profit_percentage' => (float) $this->take_profit_percentage,
            'max_concurrent_trades' => (int) $this->max_concurrent_trades,
            'max_daily_loss_percentage' => (float) $this->max_daily_loss_percentage,
            'slippage_tolerance' => (float) $this->slippage_tolerance,
        ];
    }
}
