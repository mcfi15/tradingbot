<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blockchain extends Model
{
    protected $fillable = [
        'name',
        'code',
        'rpc_url',
        'status',
        'instructions',
        'explorer_url_live',
        'explorer_url_devnet',
        'master_wallet_address',
        'master_private_key',
        'logo',
        'priority',
        'symbol',
        'network',
    ];

    public function tokens()
    {
        return $this->hasMany(BlockchainToken::class);
    }

    public function userWallets()
    {
        return $this->hasMany(UserBlockchainWallet::class);
    }

    public function isEvm(): bool
    {
        $evmCodes = [
            'ethereum',
            'bsc',
            'base',
            'polygon',
            'arbitrum',
            'optimism',
            'avalanche',
            'fantom',
            'cronos',
            'linea',
            'scroll',
            'zksync',
            'celo',
            'mantle'
        ];
        return in_array($this->code, $evmCodes);
    }

    public function isTron(): bool
    {
        return $this->code === 'tron';
    }

    public function isBitcoin(): bool
    {
        return $this->code === 'bitcoin';
    }
}
