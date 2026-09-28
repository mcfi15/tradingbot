<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TradeOrder extends Model
{
    protected $fillable = [
        'user_id',
        'exchange_connection_id',
        'trading_signal_id',
        'client_order_id',
        'exchange_order_id',
        'exchange',
        'market_type',
        'pair',
        'side',
        'direction',
        'order_type',
        'leverage',
        'quantity',
        'limit_price',
        'filled_price',
        'take_profit_price',
        'stop_loss_price',
        'notional',
        'realised_pnl',
        'fee',
        'status',
        'origin',
        'is_tp_hit',
        'is_sl_hit',
        'error_message',
        'close_reason',
        'submitted_at',
        'filled_at',
        'closed_at',
    ];

    protected $casts = [
        'leverage' => 'integer',
        'quantity' => 'decimal:8',
        'limit_price' => 'decimal:8',
        'filled_price' => 'decimal:8',
        'take_profit_price' => 'decimal:8',
        'stop_loss_price' => 'decimal:8',
        'notional' => 'decimal:8',
        'realised_pnl' => 'decimal:8',
        'fee' => 'decimal:8',
        'is_tp_hit' => 'boolean',
        'is_sl_hit' => 'boolean',
        'submitted_at' => 'integer',
        'filled_at' => 'integer',
        'closed_at' => 'integer',
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

    public function connection()
    {
        return $this->belongsTo(ExchangeConnection::class, 'exchange_connection_id');
    }

    public function signal()
    {
        return $this->belongsTo(TradingSignal::class, 'trading_signal_id');
    }

    public function logs()
    {
        return $this->hasMany(TradeLog::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Orders still holding risk. Used by the max-concurrent-trades guard and by
     * the TP/SL monitor.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'open', 'filled']);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->whereIn('status', ['closed', 'cancelled', 'rejected', 'expired']);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsLongAttribute(): bool
    {
        return in_array($this->direction ?? $this->side, ['buy', 'long'], true);
    }

    public function getIsOpenAttribute(): bool
    {
        return in_array($this->status, ['pending', 'open', 'filled'], true);
    }

    /**
     * Unrealised result at a given mark price, in quote currency.
     */
    public function unrealisedPnl(float $markPrice): float
    {
        $entry = (float) ($this->filled_price ?: $this->limit_price ?: 0);

        if ($entry <= 0) {
            return 0.0;
        }

        $quantity = (float) $this->quantity;
        $sign = $this->is_long ? 1 : -1;

        return ($markPrice - $entry) * $quantity * $sign;
    }

    public function toDisplayArray(): array
    {
        return [
            'id' => $this->id,
            'client_order_id' => $this->client_order_id,
            'exchange_order_id' => $this->exchange_order_id,
            'exchange' => $this->exchange,
            'market_type' => $this->market_type,
            'pair' => $this->pair,
            'side' => $this->side,
            'direction' => $this->direction,
            'order_type' => $this->order_type,
            'leverage' => $this->leverage,
            'quantity' => (float) $this->quantity,
            'limit_price' => $this->limit_price !== null ? (float) $this->limit_price : null,
            'filled_price' => $this->filled_price !== null ? (float) $this->filled_price : null,
            'take_profit_price' => $this->take_profit_price !== null ? (float) $this->take_profit_price : null,
            'stop_loss_price' => $this->stop_loss_price !== null ? (float) $this->stop_loss_price : null,
            'notional' => (float) $this->notional,
            'realised_pnl' => (float) $this->realised_pnl,
            'status' => $this->status,
            'origin' => $this->origin,
            'is_open' => $this->is_open,
            'is_tp_hit' => (bool) $this->is_tp_hit,
            'is_sl_hit' => (bool) $this->is_sl_hit,
            'error_message' => $this->error_message,
            'submitted_at' => $this->submitted_at,
            'filled_at' => $this->filled_at,
            'closed_at' => $this->closed_at,
        ];
    }
}
