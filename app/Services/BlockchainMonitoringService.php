<?php

namespace App\Services;

use App\Models\Blockchain;
use App\Models\BlockchainToken;
use App\Models\UserBlockchainWallet;
use App\Models\Deposit;
use App\Models\TronGasSponsorship;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlockchainMonitoringService
{
    /**
     * Poll all enabled blockchain networks and monitor active user wallets for deposits.
     *
     * @return void
     */
    public function monitorActiveBlockchains()
    {
        // 1. SMART FILTER: Only fetch enabled blockchains that have active user wallets
        $activeBlockchainIds = UserBlockchainWallet::select('blockchain_id')
            ->distinct()
            ->pluck('blockchain_id')
            ->toArray();

        if (empty($activeBlockchainIds)) {
            return;
        }

        $blockchains = Blockchain::where('status', 'enabled')
            ->whereIn('id', $activeBlockchainIds)
            ->get();

        foreach ($blockchains as $blockchain) {
            // 2. SMART FILTER: Skip if master wallet is unconfigured
            if (empty($blockchain->master_wallet_address) || empty($blockchain->master_private_key)) {
                continue;
            }

            // 3. SMART FILTER: Skip if no tokens are enabled on this blockchain
            $hasTokens = $blockchain->tokens()->where('status', 'enabled')->exists();
            if (!$hasTokens) {
                continue;
            }

            if ($blockchain->code === 'solana') {
                try {
                    $this->monitorSolana($blockchain);
                } catch (\Exception $e) {
                    Log::error("Error monitoring Solana blockchain: " . $e->getMessage());
                }
            } elseif ($blockchain->isEvm()) {
                try {
                    $this->monitorEthereum($blockchain);
                } catch (\Exception $e) {
                    Log::error("Error monitoring {$blockchain->name} blockchain: " . $e->getMessage());
                }
            } elseif ($blockchain->isTron()) {
                try {
                    $this->monitorTron($blockchain);
                } catch (\Exception $e) {
                    Log::error("Error monitoring {$blockchain->name} blockchain: " . $e->getMessage());
                }
            } elseif ($blockchain->isBitcoin()) {
                try {
                    $this->monitorBitcoin($blockchain);
                } catch (\Exception $e) {
                    Log::error("Error monitoring {$blockchain->name} blockchain: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Monitor TRON network wallets.
     *
     * @param Blockchain $blockchain
     * @return void
     */
    private function monitorTron(Blockchain $blockchain)
    {
        $rpcUrl = $blockchain->rpc_url ?: getSetting('tron_rpc_url', 'https://api.trongrid.io');

        $supportedTokens = $blockchain->tokens()->where('status', 'enabled')->get();
        $nativeToken = $supportedTokens->first(fn($token) => empty($token->mint_address));
        $trc20Tokens = $supportedTokens->filter(fn($token) => !empty($token->mint_address));

        if (!$nativeToken && $trc20Tokens->isEmpty()) {
            return;
        }

        $wallets = UserBlockchainWallet::where('blockchain_id', $blockchain->id)->get();
        if ($wallets->isEmpty()) {
            return;
        }

        $tronService = new TronWalletGeneratorService();
        $siteCurrency = getSetting('currency', 'USD');
        $depositFeePercent = (float) getSetting('deposit_fee', 0);
        $masterAddress = (string) ($blockchain->master_wallet_address ?? '');
        $masterAddressKey = $this->normalizeTronAddressKey($masterAddress, $tronService);

        $supportedContracts = [];
        foreach ($trc20Tokens as $token) {
            $contractKey = $this->normalizeTronAddressKey((string) $token->mint_address, $tronService);
            if ($contractKey) {
                $supportedContracts[$contractKey] = $token;
            }
        }

        foreach ($wallets as $wallet) {
            $user = User::find($wallet->user_id);
            if (!$user) {
                continue;
            }

            $walletAddress = (string) $wallet->address;
            $walletAddressKey = $this->normalizeTronAddressKey($walletAddress, $tronService);
            if (!$walletAddressKey) {
                continue;
            }

            if ($nativeToken) {
                try {
                    $nativeTxResponse = Http::withoutVerifying()->timeout(12)
                        ->get(rtrim($rpcUrl, '/') . '/v1/accounts/' . $walletAddress . '/transactions', [
                            'limit' => 50,
                            'only_confirmed' => 'true',
                            'order_by' => 'block_timestamp,desc',
                        ]);

                    if ($nativeTxResponse->successful()) {
                        $txRows = $nativeTxResponse->json('data') ?? [];
                        foreach ($txRows as $tx) {
                            $contractType = $tx['raw_data']['contract'][0]['type'] ?? null;
                            if ($contractType !== 'TransferContract') {
                                continue;
                            }

                            $txHash = (string) ($tx['txID'] ?? '');
                            if ($txHash === '') {
                                continue;
                            }

                            $amountSun = (float) ($tx['raw_data']['contract'][0]['parameter']['value']['amount'] ?? 0);
                            if ($amountSun <= 0) {
                                continue;
                            }

                            $fromKey = $this->normalizeTronAddressKey((string) ($tx['raw_data']['contract'][0]['parameter']['value']['owner_address'] ?? ''), $tronService);
                            $toKey = $this->normalizeTronAddressKey((string) ($tx['raw_data']['contract'][0]['parameter']['value']['to_address'] ?? ''), $tronService);

                            // Credit only incoming transfers into this user wallet.
                            if (!$toKey || $toKey !== $walletAddressKey) {
                                continue;
                            }

                            // Skip internal gas/funding style transfers from the master wallet.
                            if ($masterAddressKey && $fromKey && $fromKey === $masterAddressKey) {
                                continue;
                            }

                            $alreadyCredited = Deposit::where('blockchain_id', $blockchain->id)
                                ->where('transaction_hash', $txHash)
                                ->where('blockchain_token_id', $nativeToken->id)
                                ->exists();

                            if ($alreadyCredited) {
                                continue;
                            }

                            $nativeDecimals = (int) ($nativeToken->decimals ?? 6);
                            $tokenAmount = $amountSun / pow(10, max($nativeDecimals, 0));

                            $this->recordTronDepositCredit(
                                $user,
                                $blockchain,
                                $nativeToken,
                                $wallet,
                                $txHash,
                                $tokenAmount,
                                $siteCurrency,
                                $depositFeePercent,
                                [
                                    'from' => $tx['raw_data']['contract'][0]['parameter']['value']['owner_address'] ?? null,
                                    'to' => $tx['raw_data']['contract'][0]['parameter']['value']['to_address'] ?? null,
                                    'block_timestamp' => $tx['block_timestamp'] ?? null,
                                    'family' => 'native',
                                ]
                            );
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("TRON native monitoring failed for wallet {$walletAddress}: " . $e->getMessage());
                }
            }

            if (!empty($supportedContracts)) {
                try {
                    $trc20TxResponse = Http::withoutVerifying()->timeout(12)
                        ->get(rtrim($rpcUrl, '/') . '/v1/accounts/' . $walletAddress . '/transactions/trc20', [
                            'limit' => 50,
                            'only_confirmed' => 'true',
                            'order_by' => 'block_timestamp,desc',
                        ]);

                    if ($trc20TxResponse->successful()) {
                        $txRows = $trc20TxResponse->json('data') ?? [];
                        foreach ($txRows as $tx) {
                            $txHash = (string) ($tx['transaction_id'] ?? '');
                            if ($txHash === '') {
                                continue;
                            }

                            $toKey = $this->normalizeTronAddressKey((string) ($tx['to'] ?? ''), $tronService);
                            if (!$toKey || $toKey !== $walletAddressKey) {
                                continue;
                            }

                            $fromKey = $this->normalizeTronAddressKey((string) ($tx['from'] ?? ''), $tronService);
                            if ($masterAddressKey && $fromKey && $fromKey === $masterAddressKey) {
                                continue;
                            }

                            $contractAddress = (string) ($tx['token_info']['address'] ?? $tx['token_info']['contract_address'] ?? '');
                            $contractKey = $this->normalizeTronAddressKey($contractAddress, $tronService);
                            if (!$contractKey || !isset($supportedContracts[$contractKey])) {
                                continue;
                            }

                            /** @var BlockchainToken $token */
                            $token = $supportedContracts[$contractKey];

                            $alreadyCredited = Deposit::where('blockchain_id', $blockchain->id)
                                ->where('transaction_hash', $txHash)
                                ->where('blockchain_token_id', $token->id)
                                ->exists();

                            if ($alreadyCredited) {
                                continue;
                            }

                            $rawValue = (string) ($tx['value'] ?? '0');
                            $tokenDecimals = (int) ($token->decimals ?? ($tx['token_info']['decimals'] ?? 6));
                            $tokenAmount = (float) $rawValue / pow(10, max($tokenDecimals, 0));
                            if ($tokenAmount <= 0) {
                                continue;
                            }

                            $this->recordTronDepositCredit(
                                $user,
                                $blockchain,
                                $token,
                                $wallet,
                                $txHash,
                                $tokenAmount,
                                $siteCurrency,
                                $depositFeePercent,
                                [
                                    'from' => $tx['from'] ?? null,
                                    'to' => $tx['to'] ?? null,
                                    'contract' => $contractAddress,
                                    'block_timestamp' => $tx['block_timestamp'] ?? null,
                                    'family' => 'trc20',
                                ]
                            );
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("TRON TRC20 monitoring failed for wallet {$walletAddress}: " . $e->getMessage());
                }
            }

            // Auto-sweep TRC-20 tokens if present
            $hasTokens = false;
            foreach ($trc20Tokens as $token) {
                try {
                    $tokenBalanceRaw = $this->getTronTrc20BalanceRaw($rpcUrl, $wallet->address, $token->mint_address);
                    if (gmp_cmp($tokenBalanceRaw, gmp_init(0)) > 0) {
                        $hasTokens = true;
                        
                        Log::info("TRON: Wallet {$wallet->address} has balance of {$token->symbol}. Checking gas and triggering sweep.");

                        $this->triggerTronTrc20Sweep(
                            $blockchain,
                            $rpcUrl,
                            $wallet,
                            $masterAddress,
                            $token,
                            $tokenBalanceRaw
                        );
                    }
                } catch (\Exception $e) {
                    Log::error("TRON: Failed to check/sweep TRC-20 {$token->symbol} balance for {$wallet->address}: " . $e->getMessage());
                }
            }

            // Auto-sweep native TRX if no TRC-20 tokens are present
            if ($nativeToken && !$hasTokens && !empty($masterAddress)) {
                try {
                    $trxBalanceSun = $this->getTronNativeBalanceSun($rpcUrl, $wallet->address);
                    
                    // TRX Sweep threshold: > 1.5 TRX
                    if ($trxBalanceSun > 1500000) {
                        Log::info("TRON: Wallet {$wallet->address} has native TRX balance of " . ($trxBalanceSun / 1000000) . ". Triggering native sweep.");

                        $nativeSweepResult = $this->triggerTronNativeSweep(
                            $blockchain,
                            $rpcUrl,
                            $wallet,
                            $masterAddress,
                            $trxBalanceSun
                        );

                        if (is_string($nativeSweepResult) && !in_array($nativeSweepResult, ['skipped_insufficient_trx', 'gas_funded'], true)) {
                            $sweptToMasterSun = max(0, $trxBalanceSun - 1000000);
                            if ($sweptToMasterSun > 0) {
                                $this->recoverTronGasSponsorship(
                                    $blockchain->id,
                                    $wallet->id,
                                    $sweptToMasterSun,
                                    $nativeSweepResult
                                );
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("TRON: Failed to check/sweep native TRX for {$wallet->address}: " . $e->getMessage());
                }
            }
        }
    }

    private function getTronNativeBalanceSun(string $rpcUrl, string $address): int
    {
        try {
            $response = Http::withoutVerifying()->timeout(10)->post(rtrim($rpcUrl, '/') . '/wallet/getaccount', [
                'address' => $address,
                'visible' => true
            ]);

            if ($response->successful()) {
                return (int) ($response->json('balance') ?? 0);
            }
        } catch (\Exception $e) {
            Log::error("Tron getTronNativeBalanceSun failed: " . $e->getMessage());
        }
        return 0;
    }

    private function getTronTrc20BalanceRaw(string $rpcUrl, string $address, string $contractAddress): \GMP
    {
        try {
            $tronService = new TronWalletGeneratorService();
            $destHex = $tronService->tronAddressToHex($address);
            $paddedDest = str_pad(substr($destHex, 2), 64, '0', STR_PAD_LEFT);

            $response = Http::withoutVerifying()->timeout(10)->post(rtrim($rpcUrl, '/') . '/wallet/triggerconstantcontract', [
                'owner_address' => $address,
                'contract_address' => $contractAddress,
                'function_selector' => 'balanceOf(address)',
                'parameter' => $paddedDest,
                'visible' => true
            ]);

            if ($response->successful()) {
                $res = $response->json();
                $constantResult = $res['constant_result'][0] ?? null;
                if ($constantResult) {
                    return gmp_init($constantResult, 16);
                }
            }
        } catch (\Exception $e) {
            Log::error("Tron getTronTrc20BalanceRaw failed: " . $e->getMessage());
        }
        return gmp_init(0);
    }

    private function ensureTronDepositRecorded(
        Blockchain $blockchain,
        string $rpcUrl,
        UserBlockchainWallet $wallet,
        BlockchainToken $token,
        \GMP $rawAmount,
        string $siteCurrency,
        float $depositFeePercent
    ): void {
        $fallbackTx = 'sweep_' . md5($wallet->address . $token->id . $blockchain->id . time());

        $recentDepositExists = Deposit::where('blockchain_id', $blockchain->id)
            ->whereJsonContains('structured_data->crypto->wallet_address', $wallet->address)
            ->where('blockchain_token_id', $token->id)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->exists();

        if ($recentDepositExists) {
            return;
        }

        $decimals = $token->decimals ?? 6;
        $tokenAmount = (float) gmp_strval($rawAmount) / pow(10, $decimals);
        if ($tokenAmount <= 0) {
            return;
        }

        $fromCurrency = strtoupper((string) $token->symbol);
        if (in_array($fromCurrency, ['USDT', 'USDC', 'BUSD', 'FDUSD'], true)) {
            $fromCurrency = 'USD';
        }

        $exchangeRate = 1.0;
        $convertedAmount = $tokenAmount;

        try {
            $conversion = rateConverter($tokenAmount, $fromCurrency, $siteCurrency, 'sweep');
            if (!empty($conversion) && isset($conversion['converted_amount'])) {
                $convertedAmount = (float) $conversion['converted_amount'];
                $exchangeRate = (float) ($conversion['exchange_rate'] ?? 1.0);
            }
        } catch (\Throwable $e) {
            Log::error("Currency conversion failed: " . $e->getMessage());
        }

        $feeAmountFiat = ($convertedAmount * $depositFeePercent) / 100;
        $netAmountFiat = $convertedAmount - $feeAmountFiat;

        $user = User::find($wallet->user_id);
        if (!$user) {
            return;
        }

        $deposit = new Deposit();
        $deposit->user_id = $user->id;
        $deposit->blockchain_id = $blockchain->id;
        $deposit->blockchain_token_id = $token->id;
        $deposit->amount = $netAmountFiat;
        $deposit->currency = $token->symbol;
        $deposit->converted_amount = $tokenAmount;
        $deposit->exchange_rate = $exchangeRate;
        $deposit->fee_percent = $depositFeePercent;
        $deposit->fee_amount = $feeAmountFiat;
        $deposit->total_amount = $convertedAmount;
        $deposit->transaction_reference = 'DEP-' . strtoupper(Str::random(12));
        $deposit->transaction_hash = $fallbackTx;
        $deposit->status = 'completed';
        $deposit->expires_at = now()->addHours(24)->timestamp;
        $deposit->structured_data = json_encode([
            'crypto' => [
                'transaction_hash' => $fallbackTx,
                'wallet_address' => $wallet->address,
                'currency' => $token->symbol,
                'network' => $blockchain->name,
                'detection' => 'fallback_balance_sweep'
            ],
        ]);
        $deposit->save();

        $user->balance = $user->balance + $netAmountFiat;
        $user->save();

        $description = "Deposit via {$blockchain->name} (" . strtoupper((string) $token->symbol) . ")";
        recordTransaction(
            $user,
            $netAmountFiat,
            $siteCurrency,
            $tokenAmount,
            strtoupper((string) $token->symbol),
            $exchangeRate,
            'credit',
            'completed',
            $deposit->transaction_reference,
            $description,
            $user->balance
        );

        Log::info("TRON fallback deposit credited: User #{$user->id}, {$tokenAmount} " . strtoupper((string) $token->symbol));
    }

    private function triggerTronTrc20Sweep(
        Blockchain $blockchain,
        string $rpcUrl,
        UserBlockchainWallet $wallet,
        string $masterAddress,
        BlockchainToken $token,
        \GMP $rawBalance
    ): bool|string {
        try {
            $gasLimitSun = 35000000;
            $userTrxBalance = $this->getTronNativeBalanceSun($rpcUrl, $wallet->address);
            
            if ($userTrxBalance < $gasLimitSun) {
                Log::info("TRON: Wallet {$wallet->address} lacks native gas ({$userTrxBalance} sun < {$gasLimitSun} sun). Funding from master wallet.");
                
                $masterPrivateKeyEncrypted = $blockchain->master_private_key;
                if (empty($masterPrivateKeyEncrypted)) {
                    Log::error("TRON: Master wallet is not configured. Cannot fund gas.");
                    return false;
                }
                
                $masterPrivateKey = Crypt::decryptString($masterPrivateKeyEncrypted);
                
                $txObj = TronTransactionSigner::buildTrxTransfer($rpcUrl, $masterAddress, $wallet->address, $gasLimitSun);
                if (!$txObj) {
                    Log::error("TRON: Failed to build gas funding transaction.");
                    return false;
                }
                
                $signedTx = TronTransactionSigner::signTransaction($txObj, $masterPrivateKey);
                if (!$signedTx) {
                    Log::error("TRON: Failed to sign gas funding transaction.");
                    return false;
                }
                
                $txHash = TronTransactionSigner::broadcastTransaction($rpcUrl, $signedTx);
                if ($txHash) {
                    $this->recordTronGasSponsorship(
                        $blockchain->id,
                        $wallet->id,
                        $txHash,
                        $gasLimitSun,
                        [
                            'wallet_address' => $wallet->address,
                            'token_symbol' => strtoupper((string) $token->symbol),
                            'reason' => 'trc20_sweep_gas_funding',
                            'native_balance_before_funding' => $userTrxBalance,
                        ]
                    );

                    Log::info("TRON: Funded gas for {$wallet->address}. Tx: {$txHash}");
                    return 'gas_funded';
                }
                return false;
            }

            $userPrivateKey = Crypt::decryptString($wallet->private_key);
            
            $txObj = TronTransactionSigner::buildTrc20Transfer(
                $rpcUrl,
                $wallet->address,
                $masterAddress,
                $token->mint_address,
                gmp_strval($rawBalance),
                $gasLimitSun
            );
            if (!$txObj) {
                Log::error("TRON: Failed to build TRC-20 transfer transaction.");
                return false;
            }

            $signedTx = TronTransactionSigner::signTransaction($txObj, $userPrivateKey);
            if (!$signedTx) {
                Log::error("TRON: Failed to sign TRC-20 transfer transaction.");
                return false;
            }

            $txHash = TronTransactionSigner::broadcastTransaction($rpcUrl, $signedTx);
            if ($txHash) {
                Log::info("TRON: Swept {$token->symbol} from {$wallet->address} -> master. Tx: {$txHash}");
                return $txHash;
            }
        } catch (\Exception $e) {
            Log::error("TRON: triggerTronTrc20Sweep exception: " . $e->getMessage());
        }
        return false;
    }

    private function triggerTronNativeSweep(
        Blockchain $blockchain,
        string $rpcUrl,
        UserBlockchainWallet $wallet,
        string $masterAddress,
        int $trxBalanceSun
    ): bool|string {
        try {
            $feeBuffer = 1000000;
            $amountToSend = $trxBalanceSun - $feeBuffer;
            if ($amountToSend <= 0) {
                return 'skipped_insufficient_trx';
            }

            $userPrivateKey = Crypt::decryptString($wallet->private_key);
            
            $txObj = TronTransactionSigner::buildTrxTransfer($rpcUrl, $wallet->address, $masterAddress, $amountToSend);
            if (!$txObj) {
                Log::error("TRON: Failed to build native TRX transfer transaction.");
                return false;
            }

            $signedTx = TronTransactionSigner::signTransaction($txObj, $userPrivateKey);
            if (!$signedTx) {
                Log::error("TRON: Failed to sign native TRX transfer transaction.");
                return false;
            }

            $txHash = TronTransactionSigner::broadcastTransaction($rpcUrl, $signedTx);
            if ($txHash) {
                Log::info("TRON: Swept TRX from {$wallet->address} -> master. Tx: {$txHash}");
                return $txHash;
            }
        } catch (\Exception $e) {
            Log::error("TRON: triggerTronNativeSweep exception: " . $e->getMessage());
        }
        return false;
    }

    private function recordTronDepositCredit(
        User $user,
        Blockchain $blockchain,
        BlockchainToken $token,
        UserBlockchainWallet $wallet,
        string $txHash,
        float $tokenAmount,
        string $siteCurrency,
        float $depositFeePercent,
        array $metadata = []
    ): void {
        if ($tokenAmount <= 0) {
            return;
        }

        $fromCurrency = strtoupper((string) $token->symbol);
        if (in_array($fromCurrency, ['USDT', 'USDC', 'BUSD', 'FDUSD'], true)) {
            $fromCurrency = 'USD';
        }

        $exchangeRate = 1.0;
        $convertedAmount = $tokenAmount;

        try {
            $conversion = rateConverter($tokenAmount, $fromCurrency, $siteCurrency, 'sweep');
            if (!empty($conversion) && isset($conversion['converted_amount'])) {
                $convertedAmount = (float) $conversion['converted_amount'];
                $exchangeRate = (float) ($conversion['exchange_rate'] ?? 1.0);
            }
        } catch (\Throwable $e) {
            Log::warning("TRON conversion failed for {$token->symbol}: " . $e->getMessage());
        }

        $feeAmountFiat = ($convertedAmount * $depositFeePercent) / 100;
        $netAmountFiat = $convertedAmount - $feeAmountFiat;

        $deposit = new Deposit();
        $deposit->user_id = $user->id;
        $deposit->blockchain_id = $blockchain->id;
        $deposit->blockchain_token_id = $token->id;
        $deposit->amount = $netAmountFiat;
        $deposit->currency = strtoupper((string) $token->symbol);
        $deposit->converted_amount = $tokenAmount;
        $deposit->exchange_rate = $exchangeRate;
        $deposit->fee_percent = $depositFeePercent;
        $deposit->fee_amount = $feeAmountFiat;
        $deposit->total_amount = $convertedAmount;
        $deposit->transaction_reference = 'DEP-' . strtoupper(Str::random(12));
        $deposit->transaction_hash = $txHash;
        $deposit->status = 'completed';
        $deposit->expires_at = now()->addHours((int) getSetting('deposit_expires_at', 24))->timestamp;
        $deposit->structured_data = json_encode([
            'crypto' => [
                'transaction_hash' => $txHash,
                'wallet_address' => $wallet->address,
                'currency' => strtoupper((string) $token->symbol),
                'network' => $blockchain->name,
                'token_contract' => $metadata['contract'] ?? $token->mint_address,
                'from_address' => $metadata['from'] ?? null,
                'to_address' => $metadata['to'] ?? null,
                'family' => $metadata['family'] ?? null,
                'block_timestamp' => $metadata['block_timestamp'] ?? null,
            ],
        ]);
        $deposit->save();

        $user->balance = $user->balance + $netAmountFiat;
        $user->save();

        $description = "Deposit via {$blockchain->name} (" . strtoupper((string) $token->symbol) . ")";
        recordTransaction(
            $user,
            $netAmountFiat,
            $siteCurrency,
            $tokenAmount,
            strtoupper((string) $token->symbol),
            $exchangeRate,
            'credit',
            'completed',
            $deposit->transaction_reference,
            $description,
            $user->balance
        );

        recordNotificationMessage(
            $user,
            __('Deposit Completed'),
            __('Your deposit of :amount :symbol has been successfully credited.', [
                'amount' => number_format($tokenAmount, 8),
                'symbol' => strtoupper((string) $token->symbol),
            ])
        );

        $custom_subject = __('Deposit Completed');
        $custom_message = __('Your deposit of :amount :symbol has been successfully completed. Find the details below.', [
            'amount' => number_format($tokenAmount, 8),
            'symbol' => strtoupper((string) $token->symbol),
        ]);
        sendDepositEmail($custom_subject, $custom_message, $deposit);

        Log::info("TRON deposit credited: User #{$user->id}, {$tokenAmount} " . strtoupper((string) $token->symbol) . ", tx: {$txHash}");
    }

    private function recordTronGasSponsorship(
        int $blockchainId,
        int $walletId,
        string $fundingTxHash,
        int $sponsoredAmountSun,
        array $meta = []
    ): void {
        if ($sponsoredAmountSun <= 0) {
            return;
        }

        TronGasSponsorship::create([
            'blockchain_id' => $blockchainId,
            'user_blockchain_wallet_id' => $walletId,
            'funding_tx_hash' => $fundingTxHash,
            'sponsored_amount_sun' => $sponsoredAmountSun,
            'recovered_amount_sun' => 0,
            'status' => 'open',
            'meta' => $meta,
        ]);
    }

    private function getTronGasSponsorshipOutstandingSun(int $blockchainId, int $walletId): int
    {
        $rows = TronGasSponsorship::where('blockchain_id', $blockchainId)
            ->where('user_blockchain_wallet_id', $walletId)
            ->where('status', 'open')
            ->get(['sponsored_amount_sun', 'recovered_amount_sun']);

        $outstandingSun = 0;
        foreach ($rows as $row) {
            $remaining = (int) $row->sponsored_amount_sun - (int) $row->recovered_amount_sun;
            if ($remaining > 0) {
                $outstandingSun += $remaining;
            }
        }

        return $outstandingSun;
    }

    private function recoverTronGasSponsorship(int $blockchainId, int $walletId, int $recoveredSun, string $sweepTxHash): void
    {
        if ($recoveredSun <= 0) {
            return;
        }

        $sponsorships = TronGasSponsorship::where('blockchain_id', $blockchainId)
            ->where('user_blockchain_wallet_id', $walletId)
            ->where('status', 'open')
            ->orderBy('id')
            ->get();

        $remainingToRecover = $recoveredSun;
        foreach ($sponsorships as $sponsorship) {
            if ($remainingToRecover <= 0) {
                break;
            }

            $remainingForRow = (int) $sponsorship->sponsored_amount_sun - (int) $sponsorship->recovered_amount_sun;
            if ($remainingForRow <= 0) {
                $sponsorship->status = 'settled';
                $sponsorship->settled_at = now();
                $sponsorship->save();
                continue;
            }

            $applySun = min($remainingForRow, $remainingToRecover);
            $sponsorship->recovered_amount_sun = (int) $sponsorship->recovered_amount_sun + $applySun;

            if ((int) $sponsorship->recovered_amount_sun >= (int) $sponsorship->sponsored_amount_sun) {
                $sponsorship->status = 'settled';
                $sponsorship->settled_at = now();
            }

            $meta = is_array($sponsorship->meta) ? $sponsorship->meta : [];
            $meta['last_recovery_tx_hash'] = $sweepTxHash;
            $meta['last_recovery_sun'] = $applySun;
            $meta['last_recovery_at'] = now()->toDateTimeString();
            $sponsorship->meta = $meta;
            $sponsorship->recovered_amount_sun = (int) $sponsorship->sponsored_amount_sun; // mark as fully recovered for this row since this is just to ensure recovery
            $sponsorship->status = 'settled';
            $sponsorship->save();

            // $remainingToRecover -= $applySun;
            $remainingToRecover = 0; // no need to recover actual amount, just mark as settled since we are sweeping all to master wallet
        }
    }

    private function normalizeTronAddressKey(string $value, TronWalletGeneratorService $tronService): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if ($tronService->isValidAddress($value)) {
            try {
                return strtoupper(ltrim($tronService->tronAddressToHex($value), '0x'));
            } catch (\Throwable $e) {
                return null;
            }
        }

        $candidate = strtoupper(ltrim($value, '0x'));
        if (preg_match('/^41[0-9A-F]{40}$/', $candidate) === 1) {
            return $candidate;
        }

        return null;
    }

    /**
     * Monitor Solana network wallets.
     *
     * @param Blockchain $blockchain
     * @return void
     */
    private function monitorSolana(Blockchain $blockchain)
    {
        $solanaService = new SolanaWalletGeneratorService();
        $supportedTokens = $blockchain->tokens()->where('status', 'enabled')->get();
        $wallets = UserBlockchainWallet::where('blockchain_id', $blockchain->id)->get();

        $masterAddress = $blockchain->master_wallet_address;
        $masterPrivateKeyEncrypted = $blockchain->master_private_key;

        if (!$masterAddress || !$masterPrivateKeyEncrypted) {
            Log::warning("Solana monitoring skipped: Solana Master Wallet is not fully configured.");
            return;
        }

        $masterPrivateKey = '';
        try {
            $masterPrivateKey = Crypt::decryptString($masterPrivateKeyEncrypted);
        } catch (\Exception $e) {
            Log::error("Failed to decrypt Solana Master Wallet private key: " . $e->getMessage());
            return;
        }

        foreach ($wallets as $wallet) {
            $user = User::find($wallet->user_id);
            if (!$user) {
                continue;
            }

            // Fetch transaction signatures for the user's address
            $signatures = $solanaService->getSignatures($wallet->address, 15);
            if (empty($signatures)) {
                continue;
            }

            foreach ($signatures as $sigInfo) {
                $signature = $sigInfo['signature'] ?? '';
                if (empty($signature)) {
                    continue;
                }

                // Check if already processed
                if (Deposit::where('transaction_hash', $signature)->exists()) {
                    continue;
                }

                // Fetch transaction details
                $txDetails = $this->getTransactionDetails($blockchain->rpc_url, $signature);
                if (!$txDetails) {
                    continue;
                }

                // Extract any incoming transfers to this wallet address
                $transfers = $this->extractIncomingTransfers($txDetails, $wallet->address, $supportedTokens);
                if (empty($transfers)) {
                    continue;
                }

                foreach ($transfers as $transfer) {
                    // Decrypt user private key
                    $userPrivateKey = '';
                    try {
                        $userPrivateKey = Crypt::decryptString($wallet->private_key);
                    } catch (\Exception $e) {
                        Log::error("Failed to decrypt private key for wallet {$wallet->address}: " . $e->getMessage());
                        continue;
                    }

                    // Process dynamic conversion to site base currency
                    $siteCurrency = getSetting('currency', 'USD');
                    $fromCurrency = $transfer['symbol'];
                    if (in_array(strtoupper($transfer['symbol']), ['USDT', 'USDC', 'BUSD', 'FDUSD'])) {
                        $fromCurrency = 'USD';
                    }

                    $exchangeRate = 1.0;
                    $convertedAmount = $transfer['amount'];

                    try {
                        $conversion = rateConverter($transfer['amount'], $fromCurrency, $siteCurrency, 'sweep');
                        if (!empty($conversion) && isset($conversion['converted_amount'])) {
                            $convertedAmount = $conversion['converted_amount'];
                            $exchangeRate = $conversion['exchange_rate'] ?? 1.0;
                        }
                    } catch (\Exception $e) {
                        Log::error("Currency conversion failed for {$transfer['symbol']} deposit: " . $e->getMessage());
                    }

                    // Calculate fee and net amount
                    $depositFeePercent = (float) getSetting('deposit_fee', 0);
                    $feeAmountCrypto = ($transfer['amount'] * $depositFeePercent) / 100;
                    $netAmountCrypto = $transfer['amount'] - $feeAmountCrypto;

                    $feeAmountFiat = ($convertedAmount * $depositFeePercent) / 100;
                    $netAmountFiat = $convertedAmount - $feeAmountFiat;

                    // Trigger sweep via native Solana transaction builder
                    $sweepSignature = $this->triggerSolanaSweep(
                        $blockchain->rpc_url,
                        $userPrivateKey,
                        $masterAddress,
                        $masterPrivateKey,
                        $transfer['mint'],
                        $transfer['symbol'],
                        $wallet->address
                    );

                    if (!$sweepSignature) {
                        Log::error("Failed to sweep {$transfer['symbol']} for wallet {$wallet->address}. Deposit processing halted.");
                        continue;
                    }

                    // Create Deposit record
                    $deposit = new Deposit();
                    $deposit->user_id = $user->id;
                    $deposit->blockchain_id = $blockchain->id;
                    $deposit->blockchain_token_id = $transfer['token_id'] ?? null;
                    $deposit->amount = $netAmountFiat;
                    $deposit->currency = $transfer['symbol'];
                    $deposit->converted_amount = $transfer['amount'];
                    $deposit->exchange_rate = $exchangeRate;
                    $deposit->fee_percent = $depositFeePercent;
                    $deposit->fee_amount = $feeAmountFiat;
                    $deposit->total_amount = $convertedAmount;
                    $deposit->transaction_reference = 'DEP-' . strtoupper(Str::random(12));
                    $deposit->transaction_hash = $signature;
                    $deposit->status = 'completed';
                    $deposit->expires_at = now()->addHours((int) getSetting('deposit_expires_at', 24))->timestamp;
                    $deposit->structured_data = json_encode([
                        'crypto' => [
                            'transaction_hash' => $signature,
                            'wallet_address' => $wallet->address,
                            'currency' => $transfer['symbol'],
                            'network' => $blockchain->name,
                        ]
                    ]);
                    $deposit->save();

                    // Credit User Balance
                    $newBalance = $user->balance + $netAmountFiat;
                    $user->balance = $newBalance;
                    $user->save();

                    // Record transaction ledger
                    $description = "Deposit via " . $blockchain->name . " (" . $transfer['symbol'] . ")";
                    recordTransaction(
                        $user,
                        $netAmountFiat,
                        $siteCurrency,
                        $transfer['amount'],
                        $transfer['symbol'],
                        $exchangeRate,
                        'credit',
                        'completed',
                        $deposit->transaction_reference,
                        $description,
                        $newBalance
                    );

                    // Record notification message
                    recordNotificationMessage(
                        $user,
                        __('Deposit Completed'),
                        __('Your deposit of :amount :symbol has been successfully parsed and credited to your balance.', [
                            'amount' => number_format($transfer['amount'], 4),
                            'symbol' => $transfer['symbol']
                        ])
                    );

                    // Send deposit email
                    $custom_subject = __('Deposit Completed');
                    $custom_message = __('Your deposit of :amount :symbol has been successfully completed. Find the details below.', [
                        'amount' => number_format($transfer['amount'], 4),
                        'symbol' => $transfer['symbol']
                    ]);
                    sendDepositEmail($custom_subject, $custom_message, $deposit);

                    Log::info("Successful deposit parsed, credited, and swept: User #{$user->id}, Amount: {$transfer['amount']} {$transfer['symbol']}, Sweep Trx: {$sweepSignature}");
                }
            }
        }
    }

    /**
     * Query transaction details via JSON-RPC.
     *
     * @param string $rpcUrl
     * @param string $signature
     * @return array|null
     */
    private function getTransactionDetails(string $rpcUrl, string $signature): ?array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'getTransaction',
                'params' => [
                    $signature,
                    [
                        'encoding' => 'json',
                        'maxSupportedTransactionVersion' => 0
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['result'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error("Solana getTransaction failed for signature {$signature}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Parse transaction metadata to isolate incoming transfers.
     *
     * @param array $txDetails
     * @param string $walletAddress
     * @param object $supportedTokens
     * @return array
     */
    private function extractIncomingTransfers(array $txDetails, string $walletAddress, $supportedTokens): array
    {
        $transfers = [];
        $meta = $txDetails['meta'] ?? null;
        if (!$meta) {
            return $transfers;
        }

        // Verify transaction success
        if (isset($meta['err']) && $meta['err'] !== null) {
            return $transfers;
        }

        // 1. Native SOL Balance changes
        $accountKeys = $txDetails['transaction']['message']['accountKeys'] ?? [];
        $addressIndex = -1;
        foreach ($accountKeys as $i => $key) {
            $pubkey = is_array($key) ? ($key['pubkey'] ?? '') : $key;
            if ($pubkey === $walletAddress) {
                $addressIndex = $i;
                break;
            }
        }

        if ($addressIndex !== -1 && isset($meta['preBalances'][$addressIndex]) && isset($meta['postBalances'][$addressIndex])) {
            $pre = $meta['preBalances'][$addressIndex];
            $post = $meta['postBalances'][$addressIndex];
            $diff = $post - $pre;

            // Threshold set above standard Solana fee of 5,000 lamports
            if ($diff > 10000) {
                $transfers[] = [
                    'type' => 'native',
                    'symbol' => 'SOL',
                    'amount' => $diff / 1000000000,
                    'mint' => null,
                    'token_id' => null,
                ];
            }
        }

        // 2. SPL Token Balance changes
        $preTokenBalances = $meta['preTokenBalances'] ?? [];
        $postTokenBalances = $meta['postTokenBalances'] ?? [];

        $preMap = [];
        foreach ($preTokenBalances as $bal) {
            $owner = $bal['owner'] ?? '';
            if ($owner === $walletAddress) {
                $mint = $bal['mint'] ?? '';
                $amount = (float) ($bal['uiTokenAmount']['uiAmount'] ?? 0.0);
                $preMap[$mint] = $amount;
            }
        }

        $postMap = [];
        foreach ($postTokenBalances as $bal) {
            $owner = $bal['owner'] ?? '';
            if ($owner === $walletAddress) {
                $mint = $bal['mint'] ?? '';
                $amount = (float) ($bal['uiTokenAmount']['uiAmount'] ?? 0.0);
                $postMap[$mint] = $amount;
            }
        }

        foreach ($postMap as $mint => $postAmount) {
            $preAmount = $preMap[$mint] ?? 0.0;
            $diff = $postAmount - $preAmount;

            if ($diff > 0) {
                foreach ($supportedTokens as $token) {
                    if ($token->mint_address === $mint) {
                        $transfers[] = [
                            'type' => 'spl',
                            'symbol' => $token->symbol,
                            'amount' => $diff,
                            'mint' => $mint,
                            'token_id' => $token->id,
                        ];
                    }
                }
            }
        }

        return $transfers;
    }

    /**
     * Execute the native Solana sweeping logic.
     *
     * @param string $rpcUrl
     * @param string $userPrivateKey
     * @param string $masterAddress
     * @param string $masterPrivateKey
     * @param string|null $tokenMint
     * @param string $symbol
     * @param string $walletAddress
     * @return string|null
     */
    private function triggerSolanaSweep(
        string $rpcUrl,
        string $userPrivateKey,
        string $masterAddress,
        string $masterPrivateKey,
        ?string $tokenMint,
        string $symbol,
        string $walletAddress
    ): ?string {
        $recentBlockhash = $this->getRecentBlockhash($rpcUrl);
        if (!$recentBlockhash) {
            Log::error("Failed to retrieve recent blockhash for {$symbol} sweep.");
            return null;
        }

        try {
            if ($tokenMint) {
                // Fetch user ATA address
                $sourceATA = $this->getTokenAccountAddress($rpcUrl, $walletAddress, $tokenMint);
                if (!$sourceATA) {
                    Log::error("Failed to find source token account (ATA) for wallet {$walletAddress} and mint {$tokenMint}.");
                    return null;
                }

                // Fetch master ATA address
                $destATA = $this->getTokenAccountAddress($rpcUrl, $masterAddress, $tokenMint);
                if (!$destATA) {
                    Log::error("Failed to find master token account (ATA) for master {$masterAddress} and mint {$tokenMint}. Make sure master wallet has token account initialized.");
                    return null;
                }

                // Fetch token balance in raw units (considering decimals)
                $rawTokenUnits = $this->getTokenAccountBalanceRaw($rpcUrl, $sourceATA);
                if ($rawTokenUnits <= 0) {
                    Log::info("Token account {$sourceATA} has zero balance. Skipping sweep.");
                    return 'skipped_zero_balance';
                }

                // Build and sign transaction
                $base64Tx = SolanaTransactionSigner::buildAndSignSplTransfer(
                    $userPrivateKey,
                    $masterPrivateKey,
                    $sourceATA,
                    $destATA,
                    $rawTokenUnits,
                    $recentBlockhash
                );

                // Broadcast
                return $this->broadcastTransaction($rpcUrl, $base64Tx);
            } else {
                // Fetch native SOL balance
                $solService = new SolanaWalletGeneratorService();
                $balance = $solService->getBalance($walletAddress);
                $amountLamports = (int) ($balance * 1000000000);

                // Rent-exemption minimum for a System Account (0 bytes data) is 890,880 lamports
                $rentExemptMin = 890880;
                $amountToTransfer = $amountLamports - $rentExemptMin;

                if ($amountToTransfer <= 0) {
                    Log::info("SOL balance {$balance} is equal to or below the rent-exemption minimum.");
                    return 'skipped_insufficient_sol';
                }

                // Build and sign SOL transfer sponsored by the master wallet (sweeps balance down to rent-exempt limit)
                $base64Tx = SolanaTransactionSigner::buildAndSignSolTransferSponsored(
                    $userPrivateKey,
                    $masterPrivateKey,
                    $amountToTransfer,
                    $recentBlockhash
                );

                // Broadcast
                return $this->broadcastTransaction($rpcUrl, $base64Tx);
            }
        } catch (\Exception $e) {
            Log::error("Exception in triggerSolanaSweep: " . $e->getMessage());
        }

        return null;
    }

    private function getRecentBlockhash(string $rpcUrl): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'getLatestBlockhash'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['result']['value']['blockhash'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch recent blockhash: " . $e->getMessage());
        }
        return null;
    }

    private function getTokenAccountAddress(string $rpcUrl, string $walletAddress, string $tokenMintAddress): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'getTokenAccountsByOwner',
                'params' => [
                    $walletAddress,
                    ['mint' => $tokenMintAddress],
                    ['encoding' => 'jsonParsed']
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $value = $data['result']['value'] ?? [];
                if (!empty($value) && isset($value[0]['pubkey'])) {
                    return $value[0]['pubkey'];
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch token account address: " . $e->getMessage());
        }
        return null;
    }

    private function getTokenAccountBalanceRaw(string $rpcUrl, string $ataAddress): int
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'getTokenAccountBalance',
                'params' => [$ataAddress]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $amountString = $data['result']['value']['amount'] ?? '0';
                return (int) $amountString;
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch token account balance: " . $e->getMessage());
        }
        return 0;
    }

    private function broadcastTransaction(string $rpcUrl, string $base64Tx): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(15)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'sendTransaction',
                'params' => [
                    $base64Tx,
                    ['encoding' => 'base64']
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['result'])) {
                    return $data['result'];
                }
                if (isset($data['error'])) {
                    Log::error("Solana RPC sendTransaction error: " . json_encode($data['error']));
                }
            }
        } catch (\Exception $e) {
            Log::error("Broadcast transaction failed: " . $e->getMessage());
        }
        return null;
    }

    // =========================================================================
    // Ethereum Monitoring
    // =========================================================================

    /**
     * Monitor the Ethereum blockchain for ERC-20 token deposits to user wallets.
     *
     * Flow per monitoring cycle:
     *  1. Scan new blocks for ERC-20 Transfer events to user wallet addresses.
     *  2. Credit the user and record the deposit for each new incoming transfer.
     *  3. Sweep detected tokens to the master wallet:
     *     a. If the user wallet has enough ETH for gas → sweep immediately.
     *     b. If not → fund the user wallet with gas ETH (sweep happens next cycle).
     *
     * Settings consumed:
     *  - ethereum_master_wallet_address
     *  - ethereum_master_wallet_private_key  (stored encrypted via Crypt::encryptString)
     *  - ethereum_chain_id                   (default 1 = mainnet)
     *  - ethereum_last_scanned_block         (auto-updated each cycle)
     *
     * @param Blockchain $blockchain
     */
    private function monitorEthereum(Blockchain $blockchain): void
    {
        $rpcUrl = $blockchain->rpc_url;

        $masterAddress          = $blockchain->master_wallet_address;
        $masterPrivKeyEncrypted = $blockchain->master_private_key;
        $chainId                = $this->getEthChainId($rpcUrl) ?? 1;

        if (!$masterAddress || !$masterPrivKeyEncrypted) {
            Log::warning("{$blockchain->name} monitoring skipped: master wallet is not configured.");
            return;
        }

        try {
            $masterPrivKey = Crypt::decryptString($masterPrivKeyEncrypted);
        } catch (\Exception $e) {
            Log::error("Failed to decrypt {$blockchain->name} master wallet private key: " . $e->getMessage());
            return;
        }

        // ── 2. Load supported tokens and user wallets ────────────────────────
        $supportedTokens = $blockchain->tokens()->where('status', 'enabled')->get();
        $tokenContracts  = $supportedTokens->whereNotNull('mint_address')
            ->mapWithKeys(fn($token) => [strtolower($token->mint_address) => $token->id]);
        $nativeToken     = $supportedTokens->first(fn($t) => empty($t->mint_address));

        if ($tokenContracts->isEmpty() && !$nativeToken) {
            return; // no tokens to monitor
        }

        $wallets = UserBlockchainWallet::where('blockchain_id', $blockchain->id)->get();
        if ($wallets->isEmpty()) {
            return;
        }

        // Build lookup map: lowercase_address => wallet model, and padded addresses array
        $walletMap = [];
        $paddedUserAddresses = [];
        foreach ($wallets as $wallet) {
            $walletAddressLower = strtolower($wallet->address);
            $walletMap[$walletAddressLower] = $wallet;
            $paddedUserAddresses[] = '0x' . str_pad(substr($walletAddressLower, 2), 64, '0', STR_PAD_LEFT);
        }

        $ethService   = new EthereumWalletGeneratorService();
        $currentBlock = $this->getEthCurrentBlockNumber($rpcUrl);
        if ($currentBlock === null) {
            Log::error("{$blockchain->name} monitoring: could not fetch current block number.");
            return;
        }

        // Apply confirmation safety depth to protect against block reorganizations
        $requiredConfirmations = match (strtolower((string) $blockchain->code)) {
            'polygon'   => 12,
            'bsc'       => 5,
            'arbitrum'  => 5,
            'optimism'  => 5,
            'base'      => 5,
            'avalanche' => 5,
            'fantom'    => 5,
            'ethereum'  => 6,
            default     => 5,
        };
        $confirmedCurrentBlock = max(0, $currentBlock - $requiredConfirmations);

        $siteCurrency = getSetting('currency', 'USD');

        // ── 2.5 Scan Native ETH/BNB Deposits ──────────────────────────────────
        if ($nativeToken && $nativeToken->status === 'enabled') {
            $nativeStateFile   = storage_path("app/{$blockchain->code}_last_scanned_native_block.txt");
            $lastNativeScanned = 0;
            if (file_exists($nativeStateFile)) {
                $lastNativeScanned = (int) file_get_contents($nativeStateFile);
            }
            if ($lastNativeScanned === 0) {
                $lastNativeScanned = (int) getSetting($blockchain->code . '_last_scanned_native_block', 0);
                if ($lastNativeScanned === 0) {
                    $lastNativeScanned = max(0, $confirmedCurrentBlock - 100);
                }
            }

            $fromNativeBlock = $lastNativeScanned + 1;
            $toNativeBlock   = $confirmedCurrentBlock; // Scan up to confirmed block depth

            if ($fromNativeBlock <= $toNativeBlock) {
                // 1. Batch query the native balance of all user wallets
                $hasNativeBalance = false;
                $activeWalletsWithBalance = [];

                $balanceBatch = [];
                foreach ($wallets as $w) {
                    $balanceBatch[] = [
                        'jsonrpc' => '2.0',
                        'id'      => $w->id,
                        'method'  => 'eth_getBalance',
                        'params'  => [$w->address, 'latest'],
                    ];
                }

                try {
                    $balanceResponse = Http::withoutVerifying()->timeout(15)->post($rpcUrl, $balanceBatch);
                    if ($balanceResponse->successful()) {
                        $balanceResults = $balanceResponse->json() ?? [];
                        if (isset($balanceResults['jsonrpc'])) {
                            $balanceResults = [$balanceResults];
                        }

                        $walletBalances = [];
                        foreach ($balanceResults as $res) {
                            $wId = $res['id'] ?? null;
                            $hex = ltrim($res['result'] ?? '0x0', '0x');
                            $wei = gmp_init($hex ?: '0', 16);
                            $walletBalances[$wId] = $wei;
                        }

                        foreach ($wallets as $w) {
                            $walletWei = $walletBalances[$w->id] ?? gmp_init(0);
                            // Only count if native balance > dust limit (0.0001 ETH/BNB)
                            if (gmp_cmp($walletWei, gmp_init('100000000000000')) > 0) {
                                $hasNativeBalance = true;
                                $activeWalletsWithBalance[$w->id] = [
                                    'wallet' => $w,
                                    'balanceWei' => $walletWei
                                ];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("{$blockchain->name}: Failed to batch query native balances: " . $e->getMessage());
                    $hasNativeBalance = true; // Fallback to scanning if RPC fails
                }

                if (!$hasNativeBalance) {
                    // No user wallet holds native currency: instantly catch up native scanning pointer!
                    file_put_contents($nativeStateFile, $toNativeBlock);
                } else {
                    // At least one wallet holds native balance: scan block-by-block up to 25 blocks to identify tx
                    $scanLimitBlock = min($toNativeBlock, $fromNativeBlock + 24);
                    $nativeBlockNumbers = range($fromNativeBlock, $scanLimitBlock);
                    $blocks = $this->getEthBlocks($rpcUrl, $nativeBlockNumbers);

                    foreach ($blocks as $block) {
                        $txs = $block['transactions'] ?? [];
                        foreach ($txs as $tx) {
                            $toAddr = strtolower($tx['to'] ?? '');
                            if (empty($toAddr) || !isset($walletMap[$toAddr])) {
                                continue;
                            }

                            $valueHex = ltrim($tx['value'] ?? '0x0', '0x');
                            $valueWei = gmp_init($valueHex ?: '0', 16);
                            if (gmp_cmp($valueWei, gmp_init(0)) <= 0) {
                                continue;
                            }

                            // Ignore gas funding from the master wallet
                            $fromAddr = strtolower($tx['from'] ?? '');
                            if ($fromAddr === strtolower($masterAddress)) {
                                continue;
                            }

                            $userWallet = $walletMap[$toAddr];
                            $txHash = $tx['hash'] ?? null;
                            if (!$txHash) continue;

                            // Process credit and sweep
                            $exists = Deposit::where('blockchain_id', $blockchain->id)
                                ->where('transaction_hash', $txHash)
                                ->exists();
                            if ($exists) continue;

                            $decimals = $nativeToken->decimals ?? 18;
                            $tokenAmount = (float) gmp_strval($valueWei) / pow(10, $decimals);

                            $convertedAmt = $tokenAmount;
                            $exchangeRate = 1.0;
                            try {
                                $conversion = rateConverter($tokenAmount, $nativeToken->symbol, $siteCurrency, 'sweep');
                                if (!empty($conversion) && isset($conversion['converted_amount'])) {
                                    $convertedAmt = $conversion['converted_amount'];
                                    $exchangeRate = $conversion['exchange_rate'] ?? 1.0;
                                }
                            } catch (\Exception $e) {
                                Log::error("{$blockchain->name}: Native conversion failed: " . $e->getMessage());
                            }

                            $depositFee = (float) getSetting('deposit_fee', 0);
                            $feeAmountFiat = ($convertedAmt * $depositFee) / 100;
                            $netAmountFiat = $convertedAmt - $feeAmountFiat;

                            // Sweep native coin
                            $sweepResult = $this->triggerEthereumNativeSweep(
                                $blockchain, $rpcUrl, $chainId, $userWallet, $masterAddress, $ethService
                            );

                            if ($sweepResult === false) {
                                Log::error("{$blockchain->name}: Native sweep failed at wallet {$userWallet->address}. Skipping credit.");
                                continue;
                            }

                            // Record deposit and credit
                            $user = User::find($userWallet->user_id);
                            if (!$user) continue;

                            $deposit = new Deposit();
                            $deposit->user_id = $user->id;
                            $deposit->blockchain_id = $blockchain->id;
                            $deposit->blockchain_token_id = $nativeToken->id;
                            $deposit->amount = $netAmountFiat;
                            $deposit->currency = $nativeToken->symbol;
                            $deposit->converted_amount = $tokenAmount;
                            $deposit->exchange_rate = $exchangeRate;
                            $deposit->fee_percent = $depositFee;
                            $deposit->fee_amount = $feeAmountFiat;
                            $deposit->total_amount = $convertedAmt;
                            $deposit->transaction_reference = 'DEP-' . strtoupper(Str::random(12));
                            $deposit->transaction_hash = $txHash;
                            $deposit->status = 'completed';
                            $deposit->expires_at = now()->addHours((int) getSetting('deposit_expires_at', 24))->timestamp;
                            $deposit->structured_data = json_encode([
                                'crypto' => [
                                    'transaction_hash' => $txHash,
                                    'wallet_address' => $userWallet->address,
                                    'currency' => $nativeToken->symbol,
                                    'network' => $blockchain->name,
                                ],
                            ]);
                            $deposit->save();

                            $newBalance = $user->balance + $netAmountFiat;
                            $user->balance = $newBalance;
                            $user->save();

                            recordTransaction(
                                $user, $netAmountFiat, $siteCurrency, $tokenAmount, $nativeToken->symbol,
                                $exchangeRate, 'credit', 'completed', $deposit->transaction_reference,
                                'Deposit via ' . $blockchain->name . ' (' . $nativeToken->symbol . ')', $newBalance
                            );

                            Log::info("{$blockchain->name} native deposit credited: User #{$user->id}, {$tokenAmount} {$nativeToken->symbol}, tx: {$txHash}");
                        }
                    }

                    // Fallback balance sweep: If we are caught up but wallets still hold native balances, sweep and credit them
                    if ($scanLimitBlock >= $confirmedCurrentBlock - 5) {
                        foreach ($activeWalletsWithBalance as $walletData) {
                            $userWallet = $walletData['wallet'];
                            $walletWei = $walletData['balanceWei'];

                            $recentDepositExists = Deposit::where('blockchain_id', $blockchain->id)
                                ->whereJsonContains('structured_data->crypto->wallet_address', $userWallet->address)
                                ->where('created_at', '>=', now()->subMinutes(10))
                                ->exists();

                            if (!$recentDepositExists) {
                                $decimals = $nativeToken->decimals ?? 18;
                                $tokenAmount = (float) gmp_strval($walletWei) / pow(10, $decimals);

                                $sweepResult = $this->triggerEthereumNativeSweep(
                                    $blockchain, $rpcUrl, $chainId, $userWallet, $masterAddress, $ethService
                                );

                                if ($sweepResult && $sweepResult !== 'skipped_zero_balance') {
                                    $convertedAmt = $tokenAmount;
                                    $exchangeRate = 1.0;
                                    try {
                                        $conversion = rateConverter($tokenAmount, $nativeToken->symbol, $siteCurrency, 'sweep');
                                        if (!empty($conversion) && isset($conversion['converted_amount'])) {
                                            $convertedAmt = $conversion['converted_amount'];
                                            $exchangeRate = $conversion['exchange_rate'] ?? 1.0;
                                        }
                                    } catch (\Exception $e) {}

                                    $depositFee = (float) getSetting('deposit_fee', 0);
                                    $feeAmountFiat = ($convertedAmt * $depositFee) / 100;
                                    $netAmountFiat = $convertedAmt - $feeAmountFiat;

                                    $user = User::find($userWallet->user_id);
                                    if ($user) {
                                        $fallbackTx = 'sweep_' . md5($userWallet->address . $nativeToken->id . $blockchain->id . time());

                                        $deposit = new Deposit();
                                        $deposit->user_id = $user->id;
                                        $deposit->blockchain_id = $blockchain->id;
                                        $deposit->blockchain_token_id = $nativeToken->id;
                                        $deposit->amount = $netAmountFiat;
                                        $deposit->currency = $nativeToken->symbol;
                                        $deposit->converted_amount = $tokenAmount;
                                        $deposit->exchange_rate = $exchangeRate;
                                        $deposit->fee_percent = $depositFee;
                                        $deposit->fee_amount = $feeAmountFiat;
                                        $deposit->total_amount = $convertedAmt;
                                        $deposit->transaction_reference = 'DEP-' . strtoupper(Str::random(12));
                                        $deposit->transaction_hash = $fallbackTx;
                                        $deposit->status = 'completed';
                                        $deposit->expires_at = now()->addHours(24)->timestamp;
                                        $deposit->structured_data = json_encode([
                                            'crypto' => [
                                                'transaction_hash' => $fallbackTx,
                                                'wallet_address' => $userWallet->address,
                                                'currency' => $nativeToken->symbol,
                                                'network' => $blockchain->name,
                                                'detection' => 'fallback_balance_sweep'
                                            ],
                                        ]);
                                        $deposit->save();

                                        $newBalance = $user->balance + $netAmountFiat;
                                        $user->balance = $newBalance;
                                        $user->save();

                                        recordTransaction(
                                            $user, $netAmountFiat, $siteCurrency, $tokenAmount, $nativeToken->symbol,
                                            $exchangeRate, 'credit', 'completed', $deposit->transaction_reference,
                                            'Deposit via ' . $blockchain->name . ' (' . $nativeToken->symbol . ')', $newBalance
                                        );
                                        Log::info("{$blockchain->name} native fallback sweep credited: User #{$user->id}, {$tokenAmount} {$nativeToken->symbol}");
                                    }
                                }
                            }
                        }
                    }

                    file_put_contents($nativeStateFile, $scanLimitBlock);
                }
            }
        }

        // ── 2.7 Determine block range to scan for ERC-20 log transfers ────────
        $stateFile   = storage_path("app/{$blockchain->code}_last_scanned_block.txt");
        $lastScanned = 0;
        if (file_exists($stateFile)) {
            $lastScanned = (int) file_get_contents($stateFile);
        }
        if ($lastScanned === 0) {
            $lastScanned = (int) getSetting($blockchain->code . '_last_scanned_block', 0);
            if ($lastScanned === 0) {
                $lastScanned = max(0, $confirmedCurrentBlock - 100);
            }
        }
        $fromBlock = $lastScanned + 1;
        $toBlock   = min($confirmedCurrentBlock, $fromBlock + 499); // scan up to 500 confirmed blocks for ERC-20 events (safe for public RPC nodes)

        if ($fromBlock > $toBlock || $tokenContracts->isEmpty()) {
            if ($fromBlock <= $toBlock) {
                file_put_contents($stateFile, $toBlock);
            }
            return; // nothing new to scan or no contract tokens to monitor
        }

        // ── 3.5 Auto-sweep any leftover/confirmed token balances ─────────────
        foreach ($wallets as $wallet) {
            foreach ($supportedTokens as $token) {
                if (empty($token->mint_address)) continue; // skip native ETH

                try {
                    $rawBalance = $ethService->getErc20BalanceRaw($wallet->address, strtolower($token->mint_address), $rpcUrl);
                    if (gmp_cmp($rawBalance, gmp_init(0)) > 0) {
                        Log::info("{$blockchain->name}: Wallet {$wallet->address} has balance of {$token->symbol}. Recording deposit and triggering sweep.");
                        
                        $this->ensureDepositRecorded(
                            $blockchain,
                            $rpcUrl,
                            $wallet,
                            $token,
                            $rawBalance,
                            $confirmedCurrentBlock,
                            $ethService,
                            $siteCurrency
                        );

                        $this->triggerEthereumErc20Sweep(
                            $blockchain,
                            $rpcUrl,
                            $chainId,
                            $wallet,
                            $masterAddress,
                            $masterPrivKey,
                            strtolower($token->mint_address),
                            $token->symbol,
                            $ethService
                        );
                    }
                } catch (\Exception $e) {
                    Log::error("{$blockchain->name}: Failed to check/sweep leftover balance for {$wallet->address} ({$token->symbol}): " . $e->getMessage());
                }
            }
        }

        // ── 3.6 Auto-sweep any leftover/unclaimed native currency balances ───
        // Only sweep native balances if the wallet currently holds no tokens.
        if ($nativeToken && $nativeToken->status === 'enabled') {
            foreach ($wallets as $wallet) {
                try {
                    $hasTokens = false;
                    foreach ($supportedTokens as $token) {
                        if (empty($token->mint_address)) continue;
                        $rawBal = $ethService->getErc20BalanceRaw($wallet->address, strtolower($token->mint_address), $rpcUrl);
                        if (gmp_cmp($rawBal, gmp_init(0)) > 0) {
                            $hasTokens = true;
                            break;
                        }
                    }

                    if (!$hasTokens) {
                        $userBal = $ethService->getEthBalance($wallet->address, $rpcUrl);
                        $userBalWei = gmp_init(sprintf('%.0f', round($userBal * 1e18)));

                        // Native sweep threshold: native balance > 0.0005 ETH/BNB
                        if (gmp_cmp($userBalWei, gmp_init('500000000000000')) > 0) {
                            Log::info("{$blockchain->name}: Wallet {$wallet->address} has leftover native balance of {$nativeToken->symbol} with no tokens. Sweeping native balance.");
                            $this->triggerEthereumNativeSweep(
                                $blockchain,
                                $rpcUrl,
                                $chainId,
                                $wallet,
                                $masterAddress,
                                $ethService
                            );
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("{$blockchain->name}: Failed to check/sweep leftover native balance for {$wallet->address}: " . $e->getMessage());
                }
            }
        }

        // ── 4. Block-by-block ERC-20 Transfer scanner (calldata decoding) ────
        //
        // Public BSC nodes restrict eth_getLogs (result-count limits or archive
        // gates). Instead we reuse the same getEthBlocks() batch helper that the
        // native scanner already uses, then decode ERC-20 transfer(address,uint256)
        // calldata directly from each transaction's input field.
        //
        // ERC-20 transfer(address,uint256) calldata layout (all hex, 0x-prefixed):
        //   [0 ..9 ] = 0xa9059cbb           (4-byte function selector)
        //   [10..73] = padded recipient      (32 bytes, last 40 chars = address)
        //   [74..137]= padded token amount   (32 bytes, big-endian uint256)

        $erc20ScanLimit = min($toBlock, $fromBlock + 99); // up to 100 blocks per cycle

        if (!$tokenContracts->isEmpty()) {
            // Build a fast contract-address → token lookup
            $contractMap = [];
            foreach ($supportedTokens as $token) {
                if (!empty($token->mint_address)) {
                    $contractMap[strtolower($token->mint_address)] = $token;
                }
            }

            $erc20Blocks = $this->getEthBlocks($rpcUrl, range($fromBlock, $erc20ScanLimit));

            foreach ($erc20Blocks as $block) {
                foreach (($block['transactions'] ?? []) as $tx) {

                    // Must target a monitored ERC-20 contract
                    $contractAddr = strtolower($tx['to'] ?? '');
                    if (empty($contractAddr) || !isset($contractMap[$contractAddr])) {
                        continue;
                    }

                    // Must be a direct ERC-20 transfer() call (not approve, transferFrom, …)
                    $input = $tx['input'] ?? '';
                    if (strlen($input) < 138) continue;                    // 0x + 8 + 64 + 64
                    if (strtolower(substr($input, 0, 10)) !== '0xa9059cbb') continue;

                    // Decode recipient: skip 0x(2) + selector(8) + 24 zero-padding = offset 34
                    $toAddress = strtolower('0x' . substr($input, 34, 40));
                    if (!isset($walletMap[$toAddress])) continue;          // not a monitored wallet

                    // Decode amount: offset 74, 64 hex chars
                    $amountHex  = ltrim(substr($input, 74, 64), '0') ?: '0';
                    $rawAmount  = gmp_init($amountHex, 16);
                    if (gmp_cmp($rawAmount, gmp_init(0)) <= 0) continue;

                    $txHash = $tx['hash'] ?? null;
                    if (!$txHash) continue;

                    // ── Deduplication ────────────────────────────────────────
                    $alreadyExists = Deposit::where('blockchain_id', $blockchain->id)
                        ->where('transaction_hash', $txHash)
                        ->exists();
                    if ($alreadyExists) continue;

                    // ── Parse amount ─────────────────────────────────────────
                    $token       = $contractMap[$contractAddr];
                    $decimals    = $token->decimals ?? 18;
                    $tokenAmount = (float) gmp_strval($rawAmount) / pow(10, $decimals);
                    if ($tokenAmount <= 0) continue;

                    // ── Currency conversion ───────────────────────────────────
                    $fromCurrency = $token->symbol;
                    if (in_array(strtoupper($token->symbol), ['USDT', 'USDC', 'BUSD', 'FDUSD'])) {
                        $fromCurrency = 'USD';
                    }
                    $exchangeRate = 1.0;
                    $convertedAmt = $tokenAmount;
                    try {
                        $conversion = rateConverter($tokenAmount, $fromCurrency, $siteCurrency, 'sweep');
                        if (!empty($conversion) && isset($conversion['converted_amount'])) {
                            $convertedAmt = $conversion['converted_amount'];
                            $exchangeRate = $conversion['exchange_rate'] ?? 1.0;
                        }
                    } catch (\Exception $e) {
                        Log::error("{$blockchain->name}: currency conversion failed for {$token->symbol}: " . $e->getMessage());
                    }

                    // ── Fee and net credit ────────────────────────────────────
                    $depositFee    = (float) getSetting('deposit_fee', 0);
                    $feeAmountFiat = ($convertedAmt * $depositFee) / 100;
                    $netAmountFiat = $convertedAmt - $feeAmountFiat;

                    // ── Trigger ERC-20 sweep ──────────────────────────────────
                    $userWallet  = $walletMap[$toAddress];
                    $sweepResult = $this->triggerEthereumErc20Sweep(
                        $blockchain,
                        $rpcUrl,
                        $chainId,
                        $userWallet,
                        $masterAddress,
                        $masterPrivKey,
                        $contractAddr,
                        $token->symbol,
                        $ethService
                    );

                    if ($sweepResult === false) {
                        Log::error("{$blockchain->name}: sweep failed for {$token->symbol} at wallet {$userWallet->address}. Skipping credit.");
                        continue;
                    }

                    // ── Record deposit and credit user ────────────────────────
                    $user = User::find($userWallet->user_id);
                    if (!$user) continue;

                    $deposit                        = new Deposit();
                    $deposit->user_id               = $user->id;
                    $deposit->blockchain_id         = $blockchain->id;
                    $deposit->blockchain_token_id   = $token->id;
                    $deposit->amount                = $netAmountFiat;
                    $deposit->currency              = $token->symbol;
                    $deposit->converted_amount      = $tokenAmount;
                    $deposit->exchange_rate         = $exchangeRate;
                    $deposit->fee_percent           = $depositFee;
                    $deposit->fee_amount            = $feeAmountFiat;
                    $deposit->total_amount          = $convertedAmt;
                    $deposit->transaction_reference = 'DEP-' . strtoupper(Str::random(12));
                    $deposit->transaction_hash      = $txHash;
                    $deposit->status                = 'completed';
                    $deposit->expires_at            = now()->addHours((int) getSetting('deposit_expires_at', 24))->timestamp;
                    $deposit->structured_data       = json_encode([
                        'crypto' => [
                            'transaction_hash' => $txHash,
                            'wallet_address'   => $userWallet->address,
                            'currency'         => $token->symbol,
                            'network'          => $blockchain->name,
                            'detection'        => 'block_scan_calldata',
                        ],
                    ]);
                    $deposit->save();

                    $newBalance    = $user->balance + $netAmountFiat;
                    $user->balance = $newBalance;
                    $user->save();

                    recordTransaction(
                        $user,
                        $netAmountFiat,
                        $siteCurrency,
                        $tokenAmount,
                        $token->symbol,
                        $exchangeRate,
                        'credit',
                        'completed',
                        $deposit->transaction_reference,
                        'Deposit via ' . $blockchain->name . ' (' . $token->symbol . ')',
                        $newBalance
                    );

                    recordNotificationMessage(
                        $user,
                        __('Deposit Completed'),
                        __('Your deposit of :amount :symbol has been successfully credited.', [
                            'amount' => number_format($tokenAmount, 4),
                            'symbol' => $token->symbol,
                        ])
                    );

                    $custom_subject = __('Deposit Completed');
                    $custom_message = __('Your deposit of :amount :symbol has been successfully completed. Find the details below.', [
                        'amount' => number_format($tokenAmount, 4),
                        'symbol' => $token->symbol,
                    ]);
                    sendDepositEmail($custom_subject, $custom_message, $deposit);

                    Log::info("{$blockchain->name} ERC-20 deposit credited (block scan): User #{$user->id}, {$tokenAmount} {$token->symbol}, tx: {$txHash}");
                }
            }
        }

        // Update the last scanned block
        file_put_contents($stateFile, $erc20ScanLimit);
    }

    /**
     * Ensure a deposit record exists for an ERC-20 balance found in a user wallet.
     *
     * Called by section 3.5 (balance-based sweep detection) BEFORE the sweep runs.
     * Searches backwards through blockchain history to find the incoming Transfer event,
     * then records the deposit and credits the user. This handles the case where the
     * deposit arrived before the current block-scan window (the most common miss scenario).
     *
     * @param Blockchain                     $blockchain
     * @param string                         $rpcUrl
     * @param UserBlockchainWallet           $wallet
     * @param \App\Models\BlockchainToken    $token
     * @param \GMP                           $rawBalance   Current raw token balance (GMP integer)
     * @param int                            $currentBlock Current block number
     * @param EthereumWalletGeneratorService $ethService
     * @param string                         $siteCurrency Site fiat currency code
     */
    private function ensureDepositRecorded(
        Blockchain $blockchain,
        string $rpcUrl,
        UserBlockchainWallet $wallet,
        $token,
        $rawBalance,
        int $currentBlock,
        EthereumWalletGeneratorService $ethService,
        string $siteCurrency
    ): void {
        $decimals    = $token->decimals ?? 6;
        $tokenAmount = (float) gmp_strval($rawBalance) / pow(10, $decimals);
        if ($tokenAmount <= 0) return;
        $transferTopic   = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';
        $contractAddress = strtolower($token->mint_address);
        $paddedAddress   = '0x' . str_pad(substr(strtolower($wallet->address), 2), 64, '0', STR_PAD_LEFT);

        // ── 1. Search backward for the incoming Transfer log ─────────────────
        // We search in 100-block chunks up to 3,000 blocks back (compliant with Alchemy/public RPC limits)
        $foundTxHash  = null;
        $foundLogIdx  = '0x0';
        $chunkSize    = 100;
        $maxLookback  = 3000;

        for ($i = 0; $i * $chunkSize < $maxLookback; $i++) {
            $toBlock   = $currentBlock - ($i * $chunkSize);
            $fromBlock = max(0, $toBlock - $chunkSize + 1);

            $logs = $this->getEthLogs(
                $rpcUrl,
                '0x' . dechex($fromBlock),
                '0x' . dechex($toBlock),
                [$contractAddress],
                $transferTopic,
                [$paddedAddress]
            );

            if ($logs === null) {
                // If RPC query failed, log a warning and break to run API fallbacks
                Log::warning("{$blockchain->name}: logs lookup failed during balance scan. Running API fallbacks.");
                break;
            }

            // Scan results newest-first (reverse) so we pick the most recent unrecorded transfer
            foreach (array_reverse($logs) as $log) {
                $topics = $log['topics'] ?? [];
                if (count($topics) < 3) continue;

                $toAddr = strtolower('0x' . substr(ltrim($topics[2], '0x'), -40));
                if ($toAddr !== strtolower($wallet->address)) continue;

                $logTxHash = $log['transactionHash'] ?? null;
                if (!$logTxHash) continue;

                // Skip if this transfer was already recorded for this chain
                $alreadyRecorded = Deposit::where('blockchain_id', $blockchain->id)
                    ->where('transaction_hash', $logTxHash)
                    ->exists();
                if ($alreadyRecorded) {
                    // The most recent transfer log is already recorded in the database.
                    // This means the current balance in the wallet is already accounted for
                    // and we must NOT credit it again.
                    return;
                }

                $foundTxHash = $logTxHash;
                $foundLogIdx = $log['logIndex'] ?? '0x0';
                break 2; // found the most recent unrecorded transfer — stop searching
            }
        }

        // ── 1.5. API-based Fallback if no logs found via RPC ──
        $useFallback = false;
        if (!$foundTxHash) {
            $blockscoutHost = null;
            if ($blockchain->code === 'ethereum') {
                $blockscoutHost = 'eth.blockscout.com';
            } elseif ($blockchain->code === 'base') {
                $blockscoutHost = 'base.blockscout.com';
            } elseif ($blockchain->code === 'polygon') {
                $blockscoutHost = 'polygon.blockscout.com';
            }

            if ($blockscoutHost) {
                try {
                    $url = "https://{$blockscoutHost}/api/v2/addresses/{$wallet->address}/token-transfers";
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(8)->get($url);
                    if ($response->successful()) {
                        $items = $response->json('items') ?? [];
                        foreach ($items as $item) {
                            $toAddr = strtolower($item['to']['hash'] ?? '');
                            $tokenAddr = strtolower($item['token']['address'] ?? '');
                            if ($toAddr === strtolower($wallet->address) && $tokenAddr === $contractAddress) {
                                $logTxHash = $item['transaction_hash'] ?? null;
                                if ($logTxHash) {
                                    $alreadyRecorded = Deposit::where('blockchain_id', $blockchain->id)
                                        ->where('transaction_hash', $logTxHash)
                                        ->exists();
                                    if ($alreadyRecorded) {
                                        return; // already credited, stop
                                    }
                                    $foundTxHash = $logTxHash;
                                    $foundLogIdx = '0x0';
                                    Log::info("{$blockchain->name}: Found incoming Transfer log via Blockscout API: {$foundTxHash}");
                                    break;
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("{$blockchain->name}: Blockscout token-transfers API lookup failed: " . $e->getMessage());
                }
            } elseif ($blockchain->code === 'bsc') {
                try {
                    $apiKey = env('BSCSCAN_API_KEY');
                    if ($apiKey) {
                        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(8)->get("https://api.bscscan.com/api", [
                            'module' => 'account',
                            'action' => 'tokentx',
                            'address' => $wallet->address,
                            'startblock' => 0,
                            'endblock' => 99999999,
                            'sort' => 'desc',
                            'apikey' => $apiKey,
                        ]);
                        if ($response->successful() && $response->json('status') === '1') {
                            $items = $response->json('result') ?? [];
                            foreach ($items as $item) {
                                $toAddr = strtolower($item['to'] ?? '');
                                $tokenAddr = strtolower($item['contractAddress'] ?? '');
                                if ($toAddr === strtolower($wallet->address) && $tokenAddr === $contractAddress) {
                                    $logTxHash = $item['hash'] ?? null;
                                    if ($logTxHash) {
                                        $alreadyRecorded = Deposit::where('blockchain_id', $blockchain->id)
                                            ->where('transaction_hash', $logTxHash)
                                            ->exists();
                                        if ($alreadyRecorded) {
                                            return; // already credited, stop
                                        }
                                        $foundTxHash = $logTxHash;
                                        $foundLogIdx = '0x0';
                                        Log::info("{$blockchain->name}: Found incoming Transfer log via BSCScan API: {$foundTxHash}");
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("{$blockchain->name}: BSCScan API lookup failed: " . $e->getMessage());
                }
            }
        }

        // ── 2. If no log was found, abort to prevent double-crediting ────────────────────────
        if (!$foundTxHash) {
            Log::warning("{$blockchain->name}: Could not find incoming Transfer log for {$wallet->address} ({$token->symbol}) via RPC logs or API. Skipping credit for now.");
            return;
        }

        // ── 4. Currency conversion ───────────────────────────────────────────
        $fromCurrency = $token->symbol;
        if (in_array(strtoupper($token->symbol), ['USDT', 'USDC', 'BUSD', 'FDUSD'])) {
            $fromCurrency = 'USD';
        }
        $convertedAmt = $tokenAmount;
        $exchangeRate = 1.0;

        try {
            $conversion = rateConverter($tokenAmount, $fromCurrency, $siteCurrency, 'sweep');
            if (!empty($conversion) && isset($conversion['converted_amount'])) {
                $convertedAmt = $conversion['converted_amount'];
                $exchangeRate = $conversion['exchange_rate'] ?? 1.0;
            }
        } catch (\Exception $e) {
            Log::error("{$blockchain->name}: currency conversion failed for {$token->symbol}: " . $e->getMessage());
        }

        $depositFee    = (float) getSetting('deposit_fee', 0);
        $feeAmountFiat = ($convertedAmt * $depositFee) / 100;
        $netAmountFiat = $convertedAmt - $feeAmountFiat;

        // ── 5. Record deposit and credit user ────────────────────────────────
        $user = User::find($wallet->user_id);
        if (!$user) return;

        $txRef   = 'DEP-' . strtoupper(Str::random(12));
        $deposit = new Deposit();
        $deposit->user_id             = $user->id;
        $deposit->blockchain_id       = $blockchain->id;
        $deposit->blockchain_token_id = $token->id;
        $deposit->amount              = $netAmountFiat;
        $deposit->currency            = $token->symbol;
        $deposit->converted_amount    = $tokenAmount;
        $deposit->exchange_rate       = $exchangeRate;
        $deposit->fee_percent         = $depositFee;
        $deposit->fee_amount          = $feeAmountFiat;
        $deposit->total_amount        = $convertedAmt;
        $deposit->transaction_reference = $txRef;
        $deposit->transaction_hash    = $foundTxHash;
        $deposit->status              = 'completed';
        $deposit->expires_at          = now()->addHours((int) getSetting('deposit_expires_at', 24))->timestamp;
        $deposit->structured_data     = json_encode([
            'crypto' => [
                'transaction_hash' => $foundTxHash,
                'log_index'        => $foundLogIdx,
                'wallet_address'   => $wallet->address,
                'currency'         => $token->symbol,
                'network'          => $blockchain->name,
                'detection'        => $useFallback ? 'balance_sweep_fallback' : 'balance_sweep_log_scan',
            ],
        ]);
        $deposit->save();

        $newBalance    = $user->balance + $netAmountFiat;
        $user->balance = $newBalance;
        $user->save();

        recordTransaction(
            $user, $netAmountFiat, $siteCurrency, $tokenAmount,
            $token->symbol, $exchangeRate, 'credit', 'completed',
            $txRef,
            'Deposit via ' . $blockchain->name . ' (' . $token->symbol . ')',
            $newBalance
        );

        recordNotificationMessage(
            $user,
            __('Deposit Completed'),
            __('Your deposit of :amount :symbol has been successfully credited.', [
                'amount' => number_format($tokenAmount, 4),
                'symbol' => $token->symbol,
            ])
        );

        $custom_subject = __('Deposit Completed');
        $custom_message = __('Your deposit of :amount :symbol has been successfully completed. Find the details below.', [
            'amount' => number_format($tokenAmount, 4),
            'symbol' => $token->symbol,
        ]);
        sendDepositEmail($custom_subject, $custom_message, $deposit);

        Log::info("{$blockchain->name} deposit credited via balance-sweep detection: User #{$user->id}, {$tokenAmount} {$token->symbol}, tx: {$foundTxHash}");
    }

    /**
     * Attempt to sweep ERC-20 tokens from a user wallet to the master wallet.
     *
     * Two-phase approach:
     *  - If the user wallet has enough ETH for gas → build, sign, and broadcast the sweep tx.
     *  - If not → send gas ETH from master wallet; sweep will happen on the next monitoring cycle.
     *
     * @param string                   $rpcUrl
     * @param int                      $chainId
     * @param UserBlockchainWallet     $userWallet
     * @param string                   $masterAddress
     * @param string                   $masterPrivKey
     * @param string                   $tokenContract   Lowercase ERC-20 contract address
     * @param string                   $tokenSymbol
     * @param EthereumWalletGeneratorService $ethService
     * @return bool|string  Sweep tx hash on success, 'gas_funded' when gas was sent, false on hard failure
     */
    private function triggerEthereumErc20Sweep(
        Blockchain $blockchain,
        string $rpcUrl,
        int    $chainId,
        UserBlockchainWallet $userWallet,
        string $masterAddress,
        string $masterPrivKey,
        string $tokenContract,
        string $tokenSymbol,
        EthereumWalletGeneratorService $ethService
    ): bool|string {
        try {
            $gasPriceWei = $ethService->getGasPrice($rpcUrl);
            // Estimate gas needed for one ERC-20 transfer with a 50 % safety buffer
            $gasNeeded   = gmp_init(gmp_strval(gmp_mul(gmp_init('120000'), gmp_init($gasPriceWei))));
            $gasThreshold = gmp_strval(gmp_mul($gasNeeded, gmp_init(2))); // 2× buffer

            // Check how much native currency the user wallet currently holds
            $userEthBalance    = $ethService->getEthBalance($userWallet->address, $rpcUrl);
            $userEthBalanceWei = gmp_init(sprintf('%.0f', round($userEthBalance * 1e18)));

            if (gmp_cmp($userEthBalanceWei, gmp_init($gasThreshold)) < 0) {
                // ── Not enough gas: fund the user wallet from master ─────────
                try {
                    $masterNonce    = $ethService->getNonce($masterAddress, $rpcUrl);
                    $gasFundingWei  = gmp_strval(gmp_mul($gasNeeded, gmp_init(3))); // 3× to be safe
                    $fundTxHex      = EthereumTransactionSigner::buildAndSignEthTransfer(
                        $masterPrivKey,
                        $userWallet->address,
                        $gasFundingWei,
                        $masterNonce,
                        $gasPriceWei,
                        21000,
                        $chainId
                    );
                    $fundTxHash = $this->broadcastEthTransaction($rpcUrl, $fundTxHex);
                    if ($fundTxHash) {
                        Log::info("{$blockchain->name}: funded gas for {$userWallet->address}: {$fundTxHash}");
                    }
                } catch (\Exception $e) {
                    Log::error("{$blockchain->name}: failed to fund gas for " . $userWallet->address . ': ' . $e->getMessage());
                }
                return 'gas_funded';
            }

            // ── Enough gas: perform the ERC-20 sweep ─────────────────────────
            $rawBalance = $ethService->getErc20BalanceRaw($userWallet->address, $tokenContract, $rpcUrl);
            if (gmp_cmp($rawBalance, gmp_init(0)) === 0) {
                return 'skipped_zero_balance';
            }

            // Decrypt user wallet private key
            $userPrivKey = Crypt::decryptString($userWallet->private_key);

            $userNonce = $ethService->getNonce($userWallet->address, $rpcUrl);
            $sweepTx   = EthereumTransactionSigner::buildAndSignErc20Transfer(
                $userPrivKey,
                $masterAddress,
                $tokenContract,
                gmp_strval($rawBalance),
                $userNonce,
                $gasPriceWei,
                120000,
                $chainId
            );

            $sweepTxHash = $this->broadcastEthTransaction($rpcUrl, $sweepTx);
            if ($sweepTxHash) {
                Log::info("{$blockchain->name}: swept {$tokenSymbol} from {$userWallet->address} → master. tx: {$sweepTxHash}");
                return $sweepTxHash;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("{$blockchain->name} sweep exception for " . $userWallet->address . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch ERC-20 Transfer event logs via eth_getLogs.
     *
     * @param string   $rpcUrl
     * @param string   $fromBlock Hex block number (e.g. '0x12a3bc')
     * @param string   $toBlock   Hex block number
     * @param string[] $contracts Lowercase contract addresses to filter by
     * @param string   $topic0    Transfer event signature hash
     * @return array|null   Array of log objects, or null on RPC/connection failure
     */
    private function getEthLogs(
        string $rpcUrl,
        string $fromBlock,
        string $toBlock,
        array  $contracts,
        string $topic0,
        ?array $paddedRecipients = null
    ): ?array {
        try {
            $topics = [$topic0];
            if (!empty($paddedRecipients)) {
                $topics[] = null; // Topic 1 (from)
                $topics[] = $paddedRecipients; // Topic 2 (to)
            }

            $response = Http::withoutVerifying()->timeout(15)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method'  => 'eth_getLogs',
                'params'  => [[
                    'fromBlock' => $fromBlock,
                    'toBlock'   => $toBlock,
                    'address'   => $contracts,
                    'topics'    => $topics,
                ]],
            ]);

            if ($response->successful()) {
                $res = $response->json();
                if (isset($res['error'])) {
                    Log::error('eth_getLogs RPC error: ' . json_encode($res['error']));
                    return null;
                }
                return $res['result'] ?? [];
            }

            Log::error('eth_getLogs failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('eth_getLogs exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch the Ethereum chain ID via eth_chainId.
     *
     * @return int|null  Chain ID, or null on failure
     */
    private function getEthChainId(string $rpcUrl): ?int
    {
        try {
            $response = Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method'  => 'eth_chainId',
                'params'  => [],
            ]);

            if ($response->successful()) {
                return hexdec($response->json('result') ?? '0x1');
            }
        } catch (\Exception $e) {
            Log::error('eth_chainId exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch the current Ethereum block number via eth_blockNumber.
     *
     * @return int|null  Block number, or null on failure
     */
    private function getEthCurrentBlockNumber(string $rpcUrl): ?int
    {
        try {
            $response = Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method'  => 'eth_blockNumber',
                'params'  => [],
            ]);

            if ($response->successful()) {
                $hex = ltrim($response->json('result') ?? '0x0', '0x');
                return hexdec($hex);
            }
        } catch (\Exception $e) {
            Log::error('eth_blockNumber exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Broadcast a signed raw Ethereum transaction via eth_sendRawTransaction.
     *
     * @param string $rpcUrl
     * @param string $rawTxHex '0x'-prefixed signed transaction hex
     * @return string|null  Transaction hash on success, null on failure
     */
    private function broadcastEthTransaction(string $rpcUrl, string $rawTxHex): ?string
    {
        try {
            $response = Http::withoutVerifying()->timeout(15)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method'  => 'eth_sendRawTransaction',
                'params'  => [$rawTxHex],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['result'])) {
                    return $data['result'];
                }
                if (isset($data['error'])) {
                    Log::error('eth_sendRawTransaction error: ' . json_encode($data['error']));
                }
            }
        } catch (\Exception $e) {
            Log::error('broadcastEthTransaction exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Sweep native coin (ETH/BNB) from a user wallet to the master wallet.
     */
    private function triggerEthereumNativeSweep(
        Blockchain $blockchain,
        string $rpcUrl,
        int    $chainId,
        UserBlockchainWallet $userWallet,
        string $masterAddress,
        EthereumWalletGeneratorService $ethService
    ): bool|string {
        try {
            $gasPriceWei = $ethService->getGasPrice($rpcUrl);
            $gasLimit    = 21000;
            $gasNeededWei = gmp_mul(gmp_init($gasPriceWei), gmp_init($gasLimit));

            // Fetch raw balance directly in Wei from RPC to avoid float precision rounding bugs
            $balanceResponse = Http::withoutVerifying()->timeout(15)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id'      => 1,
                'method'  => 'eth_getBalance',
                'params'  => [$userWallet->address, 'latest'],
            ]);

            if ($balanceResponse->successful()) {
                $hex = ltrim($balanceResponse->json('result') ?? '0x0', '0x');
                $userBalanceWei = gmp_init($hex ?: '0', 16);
            } else {
                $userBalance = $ethService->getEthBalance($userWallet->address, $rpcUrl);
                $userBalanceWei = gmp_init(sprintf('%.0f', round($userBalance * 1e18)));
            }

            // Subtract gas from balance
            $sweepAmountWei = gmp_sub($userBalanceWei, $gasNeededWei);

            // Subtract L1 fee safety buffer for OP Stack/Base (rollup data publishing cost)
            if ($blockchain->code === 'base') {
                $l1BufferWei = gmp_init('10000000000000'); // 0.00001 ETH to cover L1 security fee
                $sweepAmountWei = gmp_sub($sweepAmountWei, $l1BufferWei);
            }

            if (gmp_cmp($sweepAmountWei, gmp_init(0)) <= 0) {
                Log::info("{$blockchain->name}: Native balance of {$userWallet->address} is too small to cover gas fee and L1 safety buffer. Skipping sweep.");
                return 'skipped_zero_balance';
            }

            $userPrivKey = Crypt::decryptString($userWallet->private_key);
            $userNonce   = $ethService->getNonce($userWallet->address, $rpcUrl);

            $sweepTxHex = EthereumTransactionSigner::buildAndSignEthTransfer(
                $userPrivKey,
                $masterAddress,
                gmp_strval($sweepAmountWei),
                $userNonce,
                $gasPriceWei,
                $gasLimit,
                $chainId
            );

            $sweepTxHash = $this->broadcastEthTransaction($rpcUrl, $sweepTxHex);
            if ($sweepTxHash) {
                Log::info("{$blockchain->name}: swept native coin from {$userWallet->address} → master. tx: {$sweepTxHash}");
                return $sweepTxHash;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("{$blockchain->name} native sweep exception for {$userWallet->address}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch block details (including transaction objects) for multiple blocks in batch.
     */
    private function getEthBlocks(string $rpcUrl, array $blockNumbers): array
    {
        if (empty($blockNumbers)) {
            return [];
        }

        try {
            $batch = [];
            foreach ($blockNumbers as $num) {
                $batch[] = [
                    'jsonrpc' => '2.0',
                    'id'      => $num,
                    'method'  => 'eth_getBlockByNumber',
                    'params'  => ['0x' . dechex($num), true],
                ];
            }

            $response = Http::withoutVerifying()->timeout(30)->post($rpcUrl, $batch);

            if ($response->successful()) {
                $results = $response->json();
                $blocks = [];
                if (is_array($results)) {
                    if (isset($results['jsonrpc'])) {
                        $results = [$results];
                    }
                    foreach ($results as $res) {
                        if (isset($res['result'])) {
                            $blocks[] = $res['result'];
                        }
                    }
                }
                return $blocks;
            }
        } catch (\Exception $e) {
            Log::warning("getEthBlocks batch fetch error: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Monitor the Bitcoin network for user deposits and sweep them to the master wallet.
     */
    private function monitorBitcoin(Blockchain $blockchain): void
    {
        $rpcUrl = $blockchain->rpc_url ?: 'https://blockstream.info/api';
        $masterAddress = (string) ($blockchain->master_wallet_address ?? '');

        $wallets = UserBlockchainWallet::where('blockchain_id', $blockchain->id)->get();
        if ($wallets->isEmpty()) {
            return;
        }

        $supportedTokens = $blockchain->tokens()->where('status', 'enabled')->get();
        $btcToken = $supportedTokens->first(fn($t) => empty($t->mint_address));
        if (!$btcToken) {
            return; // BTC token not enabled
        }

        $siteCurrency = getSetting('currency', 'USD');
        $depositFeePercent = (float) getSetting('deposit_fee', 0);

        // Fetch recommended fee rate
        $feeRate = $this->getBitcoinFeeRate($rpcUrl);

        foreach ($wallets as $wallet) {
            $user = User::find($wallet->user_id);
            if (!$user) {
                continue;
            }

            // Fetch UTXOs for this user wallet address
            $utxos = BitcoinTransactionSigner::fetchUtxos($rpcUrl, $wallet->address);
            if (empty($utxos)) {
                continue;
            }

            // Filter for confirmed UTXOs only (prevents 0-conf double-spend / RBF exploits)
            $confirmedUtxos = array_values(array_filter($utxos, function ($u) {
                return !empty($u['status']['confirmed']);
            }));

            if (empty($confirmedUtxos)) {
                continue;
            }

            $hasNewDeposit = false;
            foreach ($confirmedUtxos as $utxo) {
                $utxoId = $utxo['txid'] . ':' . $utxo['vout'];

                // Deduplicate
                $exists = Deposit::where('blockchain_id', $blockchain->id)
                    ->where('transaction_hash', $utxoId)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $tokenAmount = (float) $utxo['value'] / 100000000;
                if ($tokenAmount <= 0) {
                    continue;
                }

                // Convert to base currency
                $exchangeRate = 1.0;
                $convertedAmount = $tokenAmount;
                try {
                    $conversion = rateConverter($tokenAmount, 'BTC', $siteCurrency, 'sweep');
                    if (!empty($conversion) && isset($conversion['converted_amount'])) {
                        $convertedAmount = (float) $conversion['converted_amount'];
                        $exchangeRate = (float) ($conversion['exchange_rate'] ?? 1.0);
                    }
                } catch (\Throwable $e) {
                    Log::error("Bitcoin conversion failed: " . $e->getMessage());
                }

                $feeAmountFiat = ($convertedAmount * $depositFeePercent) / 100;
                $netAmountFiat = $convertedAmount - $feeAmountFiat;

                // Create Deposit record
                $deposit = new Deposit();
                $deposit->user_id = $user->id;
                $deposit->blockchain_id = $blockchain->id;
                $deposit->blockchain_token_id = $btcToken->id;
                $deposit->amount = $netAmountFiat;
                $deposit->currency = 'BTC';
                $deposit->converted_amount = $tokenAmount;
                $deposit->exchange_rate = $exchangeRate;
                $deposit->fee_percent = $depositFeePercent;
                $deposit->fee_amount = $feeAmountFiat;
                $deposit->total_amount = $convertedAmount;
                $deposit->transaction_reference = 'DEP-' . strtoupper(Str::random(12));
                $deposit->transaction_hash = $utxoId;
                $deposit->status = 'completed';
                $deposit->expires_at = now()->addHours(24)->timestamp;
                $deposit->structured_data = json_encode([
                    'crypto' => [
                        'transaction_hash' => $utxo['txid'],
                        'vout' => $utxo['vout'],
                        'wallet_address' => $wallet->address,
                        'currency' => 'BTC',
                        'network' => $blockchain->name,
                    ]
                ]);
                $deposit->save();

                // Credit user balance
                $user->balance = $user->balance + $netAmountFiat;
                $user->save();

                // Record transaction ledger
                $description = "Deposit via " . $blockchain->name . " (BTC)";
                recordTransaction(
                    $user,
                    $netAmountFiat,
                    $siteCurrency,
                    $tokenAmount,
                    'BTC',
                    $exchangeRate,
                    'credit',
                    'completed',
                    $deposit->transaction_reference,
                    $description,
                    $user->balance
                );

                Log::info("Bitcoin deposit credited: User #{$user->id}, Amount: {$tokenAmount} BTC, tx: {$utxoId}");
                $hasNewDeposit = true;
            }

            // Sweep user wallet balance if we have any confirmed UTXOs and master wallet is configured
            if (!empty($masterAddress) && !empty($confirmedUtxos)) {
                try {
                    $userPrivateKey = Crypt::decryptString($wallet->private_key);
                    
                    // Sum total value of confirmed UTXOs to sweep
                    $totalUtxoVal = 0;
                    foreach ($confirmedUtxos as $utxo) {
                        $totalUtxoVal += (int) $utxo['value'];
                    }

                    // Build raw unsigned SegWit sweep tx
                    $unsignedTx = BitcoinTransactionSigner::buildUnsignedSegwitTx(
                        $confirmedUtxos,
                        $masterAddress,
                        $totalUtxoVal,
                        $wallet->address,
                        $feeRate
                    );

                    if ($unsignedTx) {
                        $signedHex = BitcoinTransactionSigner::signTransaction($unsignedTx, $userPrivateKey);
                        if ($signedHex) {
                            $txHash = BitcoinTransactionSigner::broadcastTransaction($rpcUrl, $signedHex);
                            if ($txHash) {
                                Log::info("Bitcoin swept wallet {$wallet->address} -> master. Tx: {$txHash}");
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Bitcoin sweep failed for wallet {$wallet->address}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Get recommended fee rate (sat/vB) from Esplora.
     */
    private function getBitcoinFeeRate(string $rpcUrl): int
    {
        try {
            $url = rtrim($rpcUrl, '/') . '/fee-estimates';
            $response = Http::withoutVerifying()->timeout(8)->get($url);
            if ($response->successful()) {
                $estimates = $response->json();
                $rate = $estimates['2'] ?? $estimates['3'] ?? 20;
                return max(1, (int) round($rate));
            }
        } catch (\Exception $e) {
            Log::warning("Bitcoin fee estimation failed, defaulting to 20 sat/vB: " . $e->getMessage());
        }
        return 20;
    }
}
