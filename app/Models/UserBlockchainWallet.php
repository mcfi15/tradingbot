<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBlockchainWallet extends Model
{
    protected $fillable = [
        'user_id',
        'blockchain_id',
        'address',
        'private_key',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blockchain()
    {
        return $this->belongsTo(Blockchain::class);
    }

    public function explorerUrl(): string
    {
        $address = $this->address;
        $rpcUrl = $this->blockchain->rpc_url ?? '';
        $isDevnet = (strpos($rpcUrl, 'devnet') !== false);

        $pattern = $isDevnet
            ? ($this->blockchain->explorer_url_devnet ?? '')
            : ($this->blockchain->explorer_url_live ?? '');

        if (empty($pattern)) {
            return '#';
        }

        return str_replace('{address}', $address, $pattern);
    }
}
