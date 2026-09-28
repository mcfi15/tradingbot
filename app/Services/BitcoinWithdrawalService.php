<?php

namespace App\Services;

use App\Models\Blockchain;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitcoinWithdrawalService
{
    /**
     * Process automated Bitcoin withdrawal payout.
     *
     * @param Withdrawal $withdrawal
     * @return string  Transaction hash/signature on success
     * @throws \Exception
     */
    public function processPayout(Withdrawal $withdrawal): string
    {
        $blockchain = Blockchain::where('code', 'bitcoin')->first();
        if (!$blockchain) {
            throw new \Exception(__('Bitcoin blockchain configuration not found.'));
        }

        $masterAddress = (string) ($blockchain->master_wallet_address ?? '');
        $masterPrivateKeyEncrypted = $blockchain->master_private_key;

        if (empty($masterAddress) || empty($masterPrivateKeyEncrypted)) {
            throw new \Exception(__('Bitcoin master wallet is not configured.'));
        }

        try {
            $masterPrivateKey = Crypt::decryptString($masterPrivateKeyEncrypted);
        } catch (\Exception $e) {
            Log::error('Failed to decrypt Bitcoin master wallet private key: ' . $e->getMessage());
            throw new \Exception(__('Unable to read the Bitcoin master wallet state.'));
        }

        $destinationAddress = trim($withdrawal->address);
        if (!BitcoinWalletGeneratorService::isValidAddress($destinationAddress)) {
            throw new \Exception(__('Invalid Bitcoin destination address.'));
        }

        $rpcUrl = $blockchain->rpc_url ?: 'https://blockstream.info/api';
        $token = $withdrawal->blockchainToken;
        $symbol = $token?->symbol ? strtoupper($token->symbol) : 'BTC';

        // 1. Fetch Master Wallet UTXOs
        $utxos = BitcoinTransactionSigner::fetchUtxos($rpcUrl, $masterAddress);
        if (empty($utxos)) {
            throw new \Exception(__('Insufficient funds: Bitcoin master wallet has no unspent outputs (UTXOs).'));
        }

        // 2. Convert withdrawal amount to Satoshis
        $decimals = (int) ($token?->decimals ?? 8);
        $amountSat = (int) round($withdrawal->amount * pow(10, $decimals));

        // 3. Fetch recommended fee rate
        $feeRate = 20;
        try {
            $url = rtrim($rpcUrl, '/') . '/fee-estimates';
            $response = Http::withoutVerifying()->timeout(8)->get($url);
            if ($response->successful()) {
                $estimates = $response->json();
                $feeRate = max(1, (int) round($estimates['2'] ?? $estimates['3'] ?? 20));
            }
        } catch (\Exception $e) {
            Log::warning("Bitcoin payout fee estimation failed, defaulting to 20 sat/vB: " . $e->getMessage());
        }

        // 4. Build transaction
        $unsignedTx = BitcoinTransactionSigner::buildUnsignedSegwitTx(
            $utxos,
            $destinationAddress,
            $amountSat,
            $masterAddress, // change goes back to master
            $feeRate
        );

        if (!$unsignedTx) {
            throw new \Exception(__('Failed to build Bitcoin payout transaction. Check if master wallet has sufficient balance.'));
        }

        // 5. Sign transaction
        $signedHex = BitcoinTransactionSigner::signTransaction($unsignedTx, $masterPrivateKey);
        if (!$signedHex) {
            throw new \Exception(__('Failed to sign Bitcoin payout transaction.'));
        }

        // 6. Broadcast transaction
        $txHash = BitcoinTransactionSigner::broadcastTransaction($rpcUrl, $signedHex);
        if (!$txHash) {
            throw new \Exception(__('Failed to broadcast Bitcoin payout transaction.'));
        }

        Log::info("Bitcoin payout successfully sent: {$withdrawal->amount} {$symbol} to {$destinationAddress}. Tx: {$txHash}");
        return $txHash;
    }
}
