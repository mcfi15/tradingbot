<?php

namespace App\Services;

use App\Models\Withdrawal;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EthereumWithdrawalService
{
    /**
     * Build, sign, and broadcast an Ethereum payout transaction for the given withdrawal.
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

        $blockchain = $withdrawal->blockchain;
        if (!$blockchain) {
            throw new \Exception(__('Blockchain model not found for withdrawal.'));
        }

        $masterAddress = $blockchain->master_wallet_address;
        $masterPrivateKeyEncrypted = $blockchain->master_private_key;

        if (!$masterAddress || !$masterPrivateKeyEncrypted) {
            throw new \Exception(__(':blockchain Master Wallet is not configured.', ['blockchain' => $blockchain->name]));
        }

        $masterPrivateKey = Crypt::decryptString($masterPrivateKeyEncrypted);
        $rpcUrl = $blockchain->rpc_url ?? 'https://eth-mainnet.g.alchemy.com/public';

        // Query chain ID
        $chainId = 1;
        try {
            $chainIdResponse = Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'eth_chainId',
            ]);
            if ($chainIdResponse->successful()) {
                $chainId = hexdec($chainIdResponse->json('result') ?? '0x1');
            }
        } catch (\Exception $e) {
            Log::warning("{$blockchain->name} withdrawal: failed to query chain ID, using default: " . $e->getMessage());
        }

        $ethService = new EthereumWalletGeneratorService();
        $token = $withdrawal->blockchainToken;
        
        // Find native currency symbol for gas check
        $nativeSymbol = $blockchain->tokens()->whereNull('mint_address')->first()?->symbol ?? 'ETH';
        
        $isEth = true;
        if ($token && !empty($token->mint_address)) {
            $isEth = false;
        }

        $gasPriceWei = $ethService->getGasPrice($rpcUrl);
        $nonce = $ethService->getNonce($masterAddress, $rpcUrl);

        if ($isEth) {
            // Native payout
            $amountWei = sprintf('%.0f', $withdrawal->converted_amount * 1e18);
            
            // Check master balance
            $masterBalance = $ethService->getEthBalance($masterAddress, $rpcUrl);
            $gasLimit = 21000;
            $gasCostWei = gmp_mul(gmp_init($gasPriceWei), gmp_init($gasLimit));
            $requiredWei = gmp_add(gmp_init($amountWei), $gasCostWei);
            $masterBalanceWei = gmp_init(sprintf('%.0f', $masterBalance * 1e18));

            if (gmp_cmp($masterBalanceWei, $requiredWei) < 0) {
                throw new \Exception(__('Insufficient Master Wallet balance. Has :has :symbol, needs :needs :symbol (including gas).', [
                    'has' => number_format($masterBalance, 6),
                    'needs' => number_format((float) gmp_strval($requiredWei) / 1e18, 6),
                    'symbol' => $nativeSymbol
                ]));
            }

            $signedTxHex = EthereumTransactionSigner::buildAndSignEthTransfer(
                $masterPrivateKey,
                $destinationAddress,
                $amountWei,
                $nonce,
                $gasPriceWei,
                $gasLimit,
                $chainId
            );
        } else {
            // Token payout (ERC-20/BEP-20)
            $decimals = $token->decimals ?? 6;
            $amountRaw = sprintf('%.0f', $withdrawal->converted_amount * pow(10, $decimals));
            $contractAddress = strtolower($token->mint_address);

            // Check master token balance
            $masterTokenBalRaw = $ethService->getErc20BalanceRaw($masterAddress, $contractAddress, $rpcUrl);
            if (gmp_cmp($masterTokenBalRaw, gmp_init($amountRaw)) < 0) {
                throw new \Exception(__('Insufficient Master Wallet token balance.'));
            }

            // Check master native balance for gas
            $masterBalance = $ethService->getEthBalance($masterAddress, $rpcUrl);
            $gasLimit = 120000; // safe transfer limit (e.g. for Polygon/Base ERC-20s)
            $gasCostWei = gmp_mul(gmp_init($gasPriceWei), gmp_init($gasLimit));
            $masterBalanceWei = gmp_init(sprintf('%.0f', $masterBalance * 1e18));

            if (gmp_cmp($masterBalanceWei, $gasCostWei) < 0) {
                throw new \Exception(__('Insufficient Master Wallet :symbol for gas.', ['symbol' => $nativeSymbol]));
            }

            $signedTxHex = EthereumTransactionSigner::buildAndSignErc20Transfer(
                $masterPrivateKey,
                $destinationAddress,
                $contractAddress,
                $amountRaw,
                $nonce,
                $gasPriceWei,
                $gasLimit,
                $chainId
            );
        }

        // Broadcast transaction
        $response = Http::withoutVerifying()->timeout(15)->post($rpcUrl, [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method'  => 'eth_sendRawTransaction',
            'params'  => [$signedTxHex],
        ]);

        if (!$response->successful()) {
            throw new \Exception(__('Failed to broadcast payout transaction payload to :blockchain RPC.', ['blockchain' => $blockchain->name]));
        }

        $data = $response->json();
        if (isset($data['error'])) {
            $errMsg = $data['error']['message'] ?? json_encode($data['error']);
            throw new \Exception(__(':blockchain RPC eth_sendRawTransaction error: ', ['blockchain' => $blockchain->name]) . $errMsg);
        }

        $txHash = $data['result'] ?? null;
        if (!$txHash) {
            throw new \Exception(__('Failed to retrieve transaction hash after broadcast.'));
        }

        return $txHash;
    }
}
