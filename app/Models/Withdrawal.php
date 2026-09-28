<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{

    // fillables
    protected $fillable = [
        'user_id',
        'blockchain_id',
        'blockchain_token_id',
        'amount',
        'converted_amount',
        'fee_percent',
        'fee_amount',
        'amount_payable',
        'exchange_rate',
        'transaction_reference',
        'transaction_hash',
        'payment_proof',
        'currency',
        'structured_data',
        'auto_res_dump',
        'status',
    ];

    // casts
    protected function casts(): array
    {
        $decimal_places = getSetting('decimal_places');
        return [
            'amount' => 'decimal:' . $decimal_places,
            'fee_percent' => 'decimal:' . $decimal_places,
            'fee_amount' => 'decimal:' . $decimal_places,
            'amount_payable' => 'decimal:' . $decimal_places,
            'exchange_rate' => 'decimal:' . $decimal_places,
        ];
    }

    // relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blockchain()
    {
        return $this->belongsTo(Blockchain::class);
    }

    public function blockchainToken()
    {
        return $this->belongsTo(BlockchainToken::class);
    }

    public function withdrawalMethod()
    {
        return null;
    }

    /**
     * Get the display name of the payout channel/blockchain.
     *
     * @return string
     */
    public function gatewayName()
    {
        if ($this->blockchain) {
            $tokenSymbol = $this->blockchainToken ? $this->blockchainToken->symbol : '';
            $net = strtoupper($this->blockchain->network ?? ($this->blockchain->code ?? $this->blockchain->name));
            return $tokenSymbol ? ($tokenSymbol . ' (' . $net . ')') : ($this->blockchain->name . ' (' . $net . ')');
        }

        return __('Direct Payout');
    }

    /**
     * Scope a query to only include approved withdrawals.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending withdrawals.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include rejected withdrawals.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Get the blockchain explorer transaction URL.
     */
    public function explorerTxUrl(): ?string
    {
        if (!$this->blockchain || !$this->transaction_hash) {
            return null;
        }

        $rpcUrl = $this->blockchain->rpc_url ?? '';
        $isDevnet = (strpos($rpcUrl, 'devnet') !== false || strpos($rpcUrl, 'testnet') !== false || strpos($rpcUrl, 'sepolia') !== false || strpos($rpcUrl, 'shasta') !== false);
        $pattern = $isDevnet ? ($this->blockchain->explorer_url_devnet ?: $this->blockchain->explorer_url_live) : $this->blockchain->explorer_url_live;

        if (!$pattern) {
            return null;
        }

        $code = strtolower($this->blockchain->code ?? '');
        if (strpos($pattern, 'tronscan') !== false || $code === 'tron') {
            return 'https://tronscan.org/#/transaction/' . $this->transaction_hash;
        } elseif (strpos($pattern, 'solscan.io') !== false) {
            return str_replace('account/{address}', 'tx/' . $this->transaction_hash, $pattern);
        } elseif (strpos($pattern, 'address/{address}') !== false) {
            return str_replace('address/{address}', 'tx/' . $this->transaction_hash, $pattern);
        } elseif (strpos($pattern, '{address}') !== false) {
            return str_replace('{address}', $this->transaction_hash, $pattern);
        }

        return rtrim($pattern, '/') . '/tx/' . $this->transaction_hash;
    }
}
