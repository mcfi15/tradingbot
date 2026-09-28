<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TronGasSponsorship extends Model
{
    protected $fillable = [
        'blockchain_id',
        'user_blockchain_wallet_id',
        'funding_tx_hash',
        'sponsored_amount_sun',
        'recovered_amount_sun',
        'status',
        'settled_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'sponsored_amount_sun' => 'integer',
            'recovered_amount_sun' => 'integer',
            'settled_at' => 'datetime',
            'meta' => 'array',
        ];
    }
}
