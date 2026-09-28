<?php

namespace App\Services;

use App\Models\Withdrawal;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SolanaWithdrawalService
{
    /**
     * Build, sign, and broadcast a Solana payout transaction for the given withdrawal.
     *
     * @param Withdrawal $withdrawal
     * @return string transaction signature/hash
     * @throws \Exception
     */
    public function processPayout(Withdrawal $withdrawal): string
    {
        $details = json_decode($withdrawal->structured_data, true);
        $destinationAddress = $details['wallet_address'] ?? null;

        if (!$destinationAddress) {
            throw new \Exception(__('Recipient wallet address is missing.'));
        }

        $blockchain = \App\Models\Blockchain::where('code', 'solana')->first();
        if (!$blockchain) {
            throw new \Exception(__('Solana blockchain not found in database.'));
        }
        $masterAddress = $blockchain->master_wallet_address;
        $masterPrivateKeyEncrypted = $blockchain->master_private_key;

        if (!$masterAddress || !$masterPrivateKeyEncrypted) {
            throw new \Exception(__('Solana Master Wallet is not configured.'));
        }

        $masterPrivateKey = Crypt::decryptString($masterPrivateKeyEncrypted);

        // Fetch recent blockhash
        $rpcUrl = getSetting('solana_rpc_url', 'https://api.mainnet-beta.solana.com');
        $blockhashResponse = Http::withoutVerifying()->timeout(12)->post($rpcUrl, [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'getLatestBlockhash',
            'params' => []
        ]);

        if (!$blockhashResponse->successful()) {
            throw new \Exception(__('Failed to fetch recent blockhash from Solana RPC.'));
        }

        $blockhashData = $blockhashResponse->json();
        $recentBlockhash = $blockhashData['result']['value']['blockhash'] ?? null;
        if (!$recentBlockhash) {
            throw new \Exception(__('Failed to retrieve recent blockhash from Solana RPC.'));
        }

        $isSol = true;
        $token = $withdrawal->blockchainToken;
        if ($token && strtoupper($token->symbol) !== 'SOL') {
            $isSol = false;
        }

        $signedTxBase64 = '';

        if ($isSol) {
            $amountLamports = (int) round($withdrawal->converted_amount * 1000000000);
            $signedTxBase64 = SolanaTransactionSigner::buildAndSignSolTransfer(
                $masterPrivateKey,
                $destinationAddress,
                $amountLamports,
                $recentBlockhash
            );
        } else {
            // Retrieve token mint address
            $isDevnet = (strpos($rpcUrl, 'devnet') !== false);
            $mint = '';
            $symbol = strtoupper($token->symbol);
            if ($symbol === 'USDC') {
                $mint = $isDevnet
                    ? 'Gh9ZwEmdLJ8DscKNTkTqPbNwLNNBjuSzaG9Vp2KGtKJr'
                    : 'EPjFWdd5AufqSSqeM2qN1xzybapC8G4wEGGkZwyTDt1v';
            } elseif ($symbol === 'USDT') {
                $mint = 'Es9vMFrzaCERmJfrF4H2FYD4KCoNkY11McCe8BenwNYB';
            } else {
                $mint = $token->address;
            }

            if (!$mint) {
                throw new \Exception(__('Selected token mint address is invalid.'));
            }

            // Resolve ATA for master wallet and recipient
            $generator = new SolanaWalletGeneratorService();
            $masterTokenAccount = $generator->getSPLTokenAccount($masterAddress, $mint);
            if (!$masterTokenAccount) {
                throw new \Exception(__('Master Wallet token account (:symbol) is not initialized on-chain.', ['symbol' => $symbol]));
            }

            $destinationTokenAccount = $generator->getSPLTokenAccount($destinationAddress, $mint);
            $hasAta = true;
            if (!$destinationTokenAccount) {
                $destinationTokenAccount = $generator->deriveAssociatedTokenAddress($destinationAddress, $mint);
                $hasAta = false;
            }

            // Calculate decimals units
            $decimals = $token->decimals ?? 6;
            $rawAmount = (int) round($withdrawal->converted_amount * pow(10, $decimals));

            if ($hasAta) {
                $signedTxBase64 = SolanaTransactionSigner::buildAndSignSplTransferDirect(
                    $masterPrivateKey,
                    $masterTokenAccount,
                    $destinationTokenAccount,
                    $rawAmount,
                    $recentBlockhash
                );
            } else {
                $signedTxBase64 = SolanaTransactionSigner::buildAndSignSplTransferWithAtaCreation(
                    $masterPrivateKey,
                    $masterTokenAccount,
                    $destinationAddress,
                    $destinationTokenAccount,
                    $mint,
                    $rawAmount,
                    $recentBlockhash
                );
            }
        }

        // Broadcast transaction
        $sendResponse = Http::withoutVerifying()->timeout(12)->post($rpcUrl, [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'sendTransaction',
            'params' => [
                $signedTxBase64,
                ['encoding' => 'base64']
            ]
        ]);

        if (!$sendResponse->successful()) {
            throw new \Exception(__('Failed to broadcast transaction payload to Solana RPC.'));
        }

        $sendData = $sendResponse->json();
        if (isset($sendData['error'])) {
            $errMsg = $sendData['error']['message'] ?? json_encode($sendData['error']);
            throw new \Exception(__('Solana RPC sendTransaction error: ') . $errMsg);
        }

        $txHash = $sendData['result'] ?? null;
        if (!$txHash) {
            throw new \Exception(__('Failed to retrieve transaction signature after broadcast.'));
        }

        return $txHash;
    }
}
