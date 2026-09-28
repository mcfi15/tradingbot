<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockchainToken extends Model
{
    protected $fillable = [
        'blockchain_id',
        'symbol',
        'name',
        'mint_address',
        'decimals',
        'status',
        'priority',
        'logo',
    ];

    public function blockchain()
    {
        return $this->belongsTo(Blockchain::class);
    }
}
