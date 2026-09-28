<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignalFollow extends Model
{
    protected $fillable = [
        'user_id',
        'trading_signal_id',
        'mode',
        'status',
        'trade_order_id',
        'risk_snapshot',
        'error_message',
    ];

    protected $casts = [
        'risk_snapshot' => 'array',
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

    public function signal()
    {
        return $this->belongsTo(TradingSignal::class, 'trading_signal_id');
    }

    public function order()
    {
        return $this->belongsTo(TradeOrder::class, 'trade_order_id');
    }
}
