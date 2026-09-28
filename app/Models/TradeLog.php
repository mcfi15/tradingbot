<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TradeLog extends Model
{
    protected $fillable = [
        'user_id',
        'trade_order_id',
        'trading_signal_id',
        'exchange',
        'pair',
        'market_type',
        'level',
        'action',
        'message',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
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

    public function order()
    {
        return $this->belongsTo(TradeOrder::class, 'trade_order_id');
    }

    public function signal()
    {
        return $this->belongsTo(TradingSignal::class, 'trading_signal_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeProblems(Builder $query): Builder
    {
        return $query->whereIn('level', ['error', 'critical']);
    }

    /**
     * Filter by trading pair. The comparison is case-insensitive because users
     * type "ethusdt" while every writer stores the canonical "ETHUSDT" — an
     * exact match here would return an empty log with no hint of why.
     */
    public function scopeForPair(Builder $query, ?string $pair): Builder
    {
        $pair = trim((string) $pair);

        if ($pair === '') {
            return $query;
        }

        return $query->whereRaw('UPPER(pair) = ?', [strtoupper($pair)]);
    }
}
