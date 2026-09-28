<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\MasterWalletSyncService;
use App\Models\Blockchain;
use App\Services\TronWalletGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TronMasterWalletController extends Controller
{
    public function index()
    {
        $blockchain = Blockchain::where('code', 'tron')->firstOrFail();
        $page_title = __('Tron Master Wallet Setup & Status');
        $template = config('site.template');

        $supportedTokens = $blockchain->tokens()->where('status', 'enabled')->get();
        $supportedTokenBalances = [];
        $nativeBalance = 0.0;
        $rpcStatus = __('Unavailable');

        if (!empty($blockchain->master_wallet_address)) {
            $tronService = new TronWalletGeneratorService();

            try {
                $rpcUrl = $blockchain->rpc_url ?: getSetting('tron_rpc_url', 'https://api.trongrid.io');
                $response = Http::withoutVerifying()->timeout(10)->post($rpcUrl . '/wallet/getaccount', [
                    'address' => $blockchain->master_wallet_address,
                    'visible' => true,
                ]);

                if ($response->successful()) {
                    $rpcStatus = __('Connected');
                    $payload = $response->json();
                    $nativeBalance = ((float) ($payload['balance'] ?? 0)) / 1000000;

                    foreach ($supportedTokens as $token) {
                        if (empty($token->mint_address)) {
                            $supportedTokenBalances[$token->id] = $nativeBalance;
                            continue;
                        }

                        $rawAmount = $this->getTronTrc20BalanceRaw(
                            $rpcUrl,
                            $blockchain->master_wallet_address,
                            (string) $token->mint_address,
                            $tronService
                        );
                        $decimals = (int) ($token->decimals ?? 6);
                        $supportedTokenBalances[$token->id] = (float) gmp_strval($rawAmount) / pow(10, max($decimals, 0));
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to fetch Tron index balances: ' . $e->getMessage());
            }
        }

        return view('templates.' . $template . '.blades.admin.tron.index', compact(
            'page_title',
            'blockchain',
            'supportedTokens',
            'supportedTokenBalances',
            'nativeBalance',
            'rpcStatus'
        ));
    }

    public function generate(Request $request)
    {
        $blockchain = Blockchain::where('code', 'tron')->firstOrFail();
        $force = $request->boolean('force', false);

        if ($blockchain->master_wallet_address && !$force) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Tron master wallet already exists.'),
                ], 400);
            }

            return back()->with('error', __('Tron master wallet already exists.'));
        }

        try {
            $generator = new TronWalletGeneratorService();
            $wallet = $generator->generateWallet();

            $blockchain->master_wallet_address = $wallet['address'];
            $blockchain->master_private_key = Crypt::encryptString($wallet['private_key']);
            $blockchain->save();

            // After wallet creation, provision any other missing blockchain master wallets.
            (new MasterWalletSyncService())->syncMissingMasterWallets();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Tron master wallet generated successfully.'),
                    'address' => $wallet['address'],
                ]);
            }

            return back()->with('success', __('Tron master wallet generated successfully.'));
        } catch (\Exception $e) {
            Log::error('Failed to generate Tron master wallet: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Failed to generate Tron master wallet: ') . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', __('Failed to generate Tron master wallet: ') . $e->getMessage());
        }
    }

    public function reveal(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password:admin',
        ]);

        $blockchain = Blockchain::where('code', 'tron')->firstOrFail();
        $privateKeyEncrypted = $blockchain->master_private_key;

        if (!$privateKeyEncrypted) {
            return response()->json([
                'status' => 'error',
                'message' => __('Tron master wallet private key not found.'),
            ], 404);
        }

        try {
            $privateKey = Crypt::decryptString($privateKeyEncrypted);

            return response()->json([
                'status' => 'success',
                'private_key' => $privateKey,
            ]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Failed to decrypt Tron master wallet private key: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => __('Failed to decrypt the private key. Make sure the APP_KEY has not changed.'),
            ], 500);
        }
    }

    public function history()
    {
        $blockchain = Blockchain::where('code', 'tron')->firstOrFail();
        $address = $blockchain->master_wallet_address;

        if (!$address) {
            return redirect()->route('admin.tron-master-wallet.index')
                ->with('error', __('Tron master wallet has not been setup yet.'));
        }

        $page_title = __('Tron Master Wallet Details & History');
        $template = config('site.template');

        $balance = 0.0;
        $formattedBalance = '0.0000 TRX';
        $tokenBalances = [];
        $rpcStatus = __('Connected');
        $onChainTransactions = [];

        $enabledTokens = $blockchain->tokens()->where('status', 'enabled')->get();
        $trc20Tokens = $enabledTokens->whereNotNull('mint_address');
        $tronService = new TronWalletGeneratorService();
        $walletHexKey = $this->normalizeTronAddressKey($address, $tronService);
        if (!$walletHexKey) {
            Log::warning('Tron master wallet address is invalid, direction tagging will default to unknown.');
        }

        $supportedTokenByContract = [];
        foreach ($trc20Tokens as $token) {
            $mint = (string) $token->mint_address;
            $mintKey = $this->normalizeTronAddressKey($mint, $tronService);
            if ($mintKey) {
                $supportedTokenByContract[$mintKey] = [
                    'symbol' => strtoupper($token->symbol),
                    'decimals' => (int) ($token->decimals ?? 6),
                ];
            }
        }

        $allowedCurrencies = collect($supportedTokenByContract)
            ->pluck('symbol')
            ->push('TRX')
            ->unique()
            ->values()
            ->all();

        try {
            $rpcUrl = $blockchain->rpc_url ?: getSetting('tron_rpc_url', 'https://api.trongrid.io');

            $response = Http::withoutVerifying()->timeout(10)->post($rpcUrl . '/wallet/getaccount', [
                'address' => $address,
                'visible' => true,
            ]);

            if ($response->successful()) {
                $payload = $response->json();
                $balanceSun = (float) ($payload['balance'] ?? 0);
                $balance = $balanceSun / 1000000;

                foreach ($trc20Tokens as $token) {
                    $symbol = strtoupper($token->symbol);
                    $decimals = (int) ($token->decimals ?? 6);
                    $rawAmount = $this->getTronTrc20BalanceRaw(
                        $rpcUrl,
                        $address,
                        (string) $token->mint_address,
                        $tronService
                    );

                    $tokenBalances[] = [
                        'symbol' => $symbol,
                        'name' => $token->name,
                        'logo' => $token->logo,
                        'balance' => (float) gmp_strval($rawAmount) / pow(10, max($decimals, 0)),
                    ];
                }

                $siteCurrency = getSetting('currency', 'USD');
                $converted = rateConverter($balance, 'TRX', $siteCurrency, 'master');
                $fiatBalanceFormatted = '';
                if (!empty($converted) && isset($converted['converted_amount'])) {
                    $fiatBalanceFormatted = ' ≈ ' . showAmount($converted['converted_amount']);
                }

                $formattedBalance = number_format($balance, 4) . ' TRX' . $fiatBalanceFormatted;
            } else {
                $rpcStatus = __('Unavailable');
            }

            // Fetch on-chain native transactions from TronGrid/compatible gateway.
            try {
                $nativeTx = Http::withoutVerifying()->timeout(12)
                    ->get(rtrim($rpcUrl, '/') . '/v1/accounts/' . $address . '/transactions', [
                        'limit' => 20,
                        'only_confirmed' => 'true',
                        'order_by' => 'block_timestamp,desc',
                    ]);

                if ($nativeTx->successful()) {
                    $txData = $nativeTx->json('data') ?? [];
                    foreach ($txData as $tx) {
                        $contractType = $tx['raw_data']['contract'][0]['type'] ?? null;
                        if ($contractType !== 'TransferContract') {
                            continue;
                        }

                        $contract = $tx['raw_data']['contract'][0]['parameter']['value'] ?? [];
                        $amountSun = (float) ($contract['amount'] ?? 0);
                        if ($amountSun <= 0) {
                            continue;
                        }

                        $amount = $amountSun / 1000000;
                        $txHash = $tx['txID'] ?? null;
                        $ts = (int) ($tx['block_timestamp'] ?? 0);
                        $ret = $tx['ret'][0]['contractRet'] ?? null;
                        $fromKey = $this->normalizeTronAddressKey((string) ($contract['owner_address'] ?? ''), $tronService);
                        $toKey = $this->normalizeTronAddressKey((string) ($contract['to_address'] ?? ''), $tronService);
                        $direction = $this->resolveDirection($walletHexKey, $fromKey, $toKey);

                        if (!$txHash) {
                            continue;
                        }

                        $onChainTransactions[] = [
                            'hash' => $txHash,
                            'created_at' => $ts > 0 ? Carbon::createFromTimestampMs($ts) : now(),
                            'amount' => $amount,
                            'currency' => 'TRX',
                            'status' => $ret === 'SUCCESS' || $ret === null ? 'completed' : 'failed',
                            'reference' => $txHash,
                            'source' => 'onchain',
                            'direction' => $direction,
                            'tx_explorer_url' => $this->buildTronTxExplorerUrl($blockchain->explorer_url_live, $txHash),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to fetch Tron native transactions: ' . $e->getMessage());
            }

            // Fetch TRC20 transfers from TronGrid/compatible gateway.
            try {
                $trc20Tx = Http::withoutVerifying()->timeout(12)
                    ->get(rtrim($rpcUrl, '/') . '/v1/accounts/' . $address . '/transactions/trc20', [
                        'limit' => 20,
                        'only_confirmed' => 'true',
                        'order_by' => 'block_timestamp,desc',
                    ]);

                if ($trc20Tx->successful()) {
                    $txData = $trc20Tx->json('data') ?? [];
                    foreach ($txData as $tx) {
                        $txHash = $tx['transaction_id'] ?? null;
                        if (!$txHash) {
                            continue;
                        }

                        $contractAddress = (string) ($tx['token_info']['address'] ?? $tx['token_info']['contract_address'] ?? '');
                        $contractKey = $this->normalizeTronAddressKey($contractAddress, $tronService);
                        if (!$contractKey || !isset($supportedTokenByContract[$contractKey])) {
                            continue;
                        }

                        $symbol = $supportedTokenByContract[$contractKey]['symbol'];
                        $decimals = $supportedTokenByContract[$contractKey]['decimals'];
                        $rawValue = (string) ($tx['value'] ?? '0');
                        $amount = (float) $rawValue / pow(10, max($decimals, 0));
                        if ($amount <= 0) {
                            continue;
                        }

                        $ts = (int) ($tx['block_timestamp'] ?? 0);
                        $fromKey = $this->normalizeTronAddressKey((string) ($tx['from'] ?? ''), $tronService);
                        $toKey = $this->normalizeTronAddressKey((string) ($tx['to'] ?? ''), $tronService);
                        $direction = $this->resolveDirection($walletHexKey, $fromKey, $toKey);

                        $onChainTransactions[] = [
                            'hash' => $txHash,
                            'created_at' => $ts > 0 ? Carbon::createFromTimestampMs($ts) : now(),
                            'amount' => $amount,
                            'currency' => $symbol,
                            'status' => 'completed',
                            'reference' => $txHash,
                            'source' => 'onchain',
                            'direction' => $direction,
                            'tx_explorer_url' => $this->buildTronTxExplorerUrl($blockchain->explorer_url_live, $txHash),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to fetch Tron TRC20 transactions: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch Tron master wallet balance/history RPC data: ' . $e->getMessage());
            $rpcStatus = __('Unavailable');
        }

        // Merge with local DB deposit records as fallback/context and sort by timestamp.
        $dbTransactions = Deposit::where('blockchain_id', $blockchain->id)
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($deposit) use ($blockchain) {
                return [
                    'hash' => $deposit->transaction_hash ?: $deposit->transaction_reference,
                    'created_at' => $deposit->created_at,
                    'amount' => $deposit->converted_amount,
                    'currency' => strtoupper($deposit->currency),
                    'status' => $deposit->status,
                    'reference' => $deposit->transaction_reference,
                    'source' => 'db',
                    'direction' => 'incoming',
                    'tx_explorer_url' => !empty($deposit->transaction_hash)
                        ? $this->buildTronTxExplorerUrl($blockchain->explorer_url_live, $deposit->transaction_hash)
                        : null,
                ];
            })
            ->filter(fn ($tx) => in_array(strtoupper($tx['currency']), $allowedCurrencies, true))
            ->values()
            ->all();

        $transactions = collect(array_merge($onChainTransactions, $dbTransactions))
            ->filter(fn ($tx) => !empty($tx['hash']))
            ->filter(fn ($tx) => in_array(strtoupper((string) ($tx['currency'] ?? '')), $allowedCurrencies, true))
            ->unique(fn ($tx) => $tx['hash'] . '|' . $tx['currency'])
            ->sortByDesc(fn ($tx) => $tx['created_at'] instanceof Carbon ? $tx['created_at']->timestamp : strtotime((string) $tx['created_at']))
            ->take(20)
            ->values();

        return view('templates.' . $template . '.blades.admin.tron.history', compact(
            'page_title',
            'blockchain',
            'address',
            'balance',
            'formattedBalance',
            'tokenBalances',
            'rpcStatus',
            'transactions'
        ));
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

    private function buildTronTxExplorerUrl(?string $explorerPattern, string $txHash): string
    {
        $pattern = $explorerPattern ?: 'https://tronscan.org/#/address/{address}';

        if (str_contains($pattern, 'address/{address}')) {
            return str_replace('address/{address}', 'transaction/' . $txHash, $pattern);
        }

        if (str_contains($pattern, '{address}')) {
            return str_replace('{address}', 'transaction/' . $txHash, $pattern);
        }

        return rtrim($pattern, '/') . '/#/transaction/' . $txHash;
    }

    private function resolveDirection(?string $walletHexKey, ?string $fromKey, ?string $toKey): string
    {
        if (!$walletHexKey) {
            return 'unknown';
        }

        if ($fromKey && $fromKey === $walletHexKey) {
            return 'outgoing';
        }

        if ($toKey && $toKey === $walletHexKey) {
            return 'incoming';
        }

        return 'unknown';
    }

    private function getTronTrc20BalanceRaw(
        string $rpcUrl,
        string $walletAddress,
        string $contractAddress,
        TronWalletGeneratorService $tronService
    ): \GMP {
        try {
            if (!$tronService->isValidAddress($walletAddress) || !$tronService->isValidAddress($contractAddress)) {
                return gmp_init(0);
            }

            $destHex = $tronService->tronAddressToHex($walletAddress);
            $paddedDest = str_pad(substr($destHex, 2), 64, '0', STR_PAD_LEFT);

            $response = Http::withoutVerifying()->timeout(10)->post(rtrim($rpcUrl, '/') . '/wallet/triggerconstantcontract', [
                'owner_address' => $walletAddress,
                'contract_address' => $contractAddress,
                'function_selector' => 'balanceOf(address)',
                'parameter' => $paddedDest,
                'visible' => true,
            ]);

            if ($response->successful()) {
                $constantResult = $response->json('constant_result.0');
                if (!empty($constantResult)) {
                    return gmp_init((string) $constantResult, 16);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed TRC20 balanceOf call for Tron master wallet: ' . $e->getMessage());
        }

        return gmp_init(0);
    }
}