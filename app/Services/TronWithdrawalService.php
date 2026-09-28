<?php

namespace App\Services;

use App\Models\Blockchain;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TronWithdrawalService
{
    /**
     * Build and broadcast a Tron payout transaction for the given withdrawal.
     *
     * This implementation validates the destination address and performs a
     * preflight check against the configured master wallet. Full signing and
     * broadcast support can be added later by wiring a Tron signing library.
     *
     * @param Withdrawal $withdrawal
     * @return string transaction hash
     * @throws \Exception
     */
    public function processPayout(Withdrawal $withdrawal): string
    {
        $details = json_decode($withdrawal->structured_data, true);
        $destinationAddress = $details['wallet_address'] ?? null;

        if (!$destinationAddress) {
            throw new \Exception(__('Recipient wallet address is missing.'));
        }

        $generator = new TronWalletGeneratorService();
        if (!$generator->isValidAddress($destinationAddress)) {
            throw new \Exception(__('Recipient address is not a valid Tron wallet address.'));
        }

        $blockchain = $withdrawal->blockchain;
        if (!$blockchain) {
            $blockchain = Blockchain::where('code', 'tron')->first();
        }

        if (!$blockchain) {
            throw new \Exception(__('TRON blockchain not found in database.'));
        }

        $masterAddress = $blockchain->master_wallet_address;
        $masterPrivateKeyEncrypted = $blockchain->master_private_key;

        if (!$masterAddress || !$masterPrivateKeyEncrypted) {
            throw new \Exception(__('TRON Master Wallet is not configured.'));
        }

        try {
            $masterPrivateKey = Crypt::decryptString($masterPrivateKeyEncrypted);
        } catch (\Exception $e) {
            Log::error('Failed to decrypt TRON master wallet private key: ' . $e->getMessage());
            throw new \Exception(__('Failed to decrypt TRON master wallet private key.'));
        }

        if (empty($masterPrivateKey)) {
            throw new \Exception(__('TRON Master Wallet private key is empty.'));
        }

        $rpcUrl = $blockchain->rpc_url ?: getSetting('tron_rpc_url', 'https://api.trongrid.io');
        $token = $withdrawal->blockchainToken;
        $symbol = $token?->symbol ? strtoupper($token->symbol) : 'TRX';

        try {
            $response = Http::withoutVerifying()->timeout(10)->post($rpcUrl . '/wallet/getaccount', [
                'address' => $masterAddress,
                'visible' => true,
            ]);

            if (!$response->successful()) {
                throw new \Exception(__('Unable to read the TRON master wallet account state.'));
            }

            $payload = $response->json();
            if (!is_array($payload)) {
                throw new \Exception(__('Unable to read the TRON master wallet account state.'));
            }
        } catch (\Exception $e) {
            Log::warning('TRON payout preflight failed: ' . $e->getMessage());
            throw new \Exception(__('Unable to read the TRON master wallet account state.'));
        }

        $decimals = (int) ($token?->decimals ?? 6);
        $convertedAmount = (float) ($withdrawal->converted_amount ?? $withdrawal->amount);
        $amountStr = sprintf('%.0f', round($convertedAmount * pow(10, $decimals)));

        if ($token && !empty($token->mint_address)) {
            // Build TRC-20 payout
            $txObj = TronTransactionSigner::buildTrc20Transfer(
                $rpcUrl,
                $masterAddress,
                $destinationAddress,
                $token->mint_address,
                $amountStr
            );
        } else {
            // Build TRX payout
            $txObj = TronTransactionSigner::buildTrxTransfer(
                $rpcUrl,
                $masterAddress,
                $destinationAddress,
                (int) $amountStr
            );
        }

        if (!$txObj) {
            throw new \Exception(__('Failed to build TRON payout transaction.'));
        }

        $signedTx = TronTransactionSigner::signTransaction($txObj, $masterPrivateKey);
        if (!$signedTx) {
            throw new \Exception(__('Failed to sign TRON payout transaction.'));
        }

        $txHash = TronTransactionSigner::broadcastTransaction($rpcUrl, $signedTx);
        if (!$txHash) {
            throw new \Exception(__('Failed to broadcast TRON payout transaction.'));
        }

        Log::info("TRON payout successfully sent: {$withdrawal->amount} {$symbol} to {$destinationAddress}. Tx: {$txHash}");
        return $txHash;
    }
}
