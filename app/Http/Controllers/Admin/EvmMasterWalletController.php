<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EthereumWalletGeneratorService;
use App\Services\MasterWalletSyncService;
use App\Services\SolanaWalletGeneratorService;
use App\Models\Blockchain;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EvmMasterWalletController extends Controller
{
    /**
     * Display the EVM master wallet setup/status dashboard.
     */
    public function index($blockchainCode)
    {
        $blockchain = Blockchain::where('code', $blockchainCode)->firstOrFail();
        if (!$blockchain->isEvm()) {
            abort(404, 'Not an EVM blockchain');
        }

        $page_title = __(':blockchain Master Wallet Setup & Status', ['blockchain' => $blockchain->name]);
        $template = config('site.template');

        return view('templates.' . $template . '.blades.admin.evm.index', compact('page_title', 'blockchain'));
    }

    /**
     * Generate the EVM master wallet.
     */
    public function generate(Request $request, $blockchainCode)
    {
        $blockchain = Blockchain::where('code', $blockchainCode)->firstOrFail();
        if (!$blockchain->isEvm()) {
            abort(404);
        }

        $force = $request->boolean('force', false);
        $isFirstTime = empty($blockchain->master_wallet_address);

        // Check if master wallet already exists
        if ($blockchain->master_wallet_address && !$force) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __(':blockchain master wallet already exists.', ['blockchain' => $blockchain->name]),
                ], 400);
            }
            return back()->with('error', __(':blockchain master wallet already exists.', ['blockchain' => $blockchain->name]));
        }

        try {
            $existingAddress = null;
            $existingPrivKey = null;

            // Search if any other EVM master wallet is already configured
            $evmBlockchains = Blockchain::get();
            foreach ($evmBlockchains as $bc) {
                if ($bc->isEvm()) {
                    if ($bc->master_wallet_address && $bc->master_private_key) {
                        $existingAddress = $bc->master_wallet_address;
                        $existingPrivKey = $bc->master_private_key;
                        break;
                    }
                }
            }

            if ($existingAddress && $existingPrivKey) {
                // Reuse existing EVM master credentials
                $blockchain->master_wallet_address = $existingAddress;
                $blockchain->master_private_key = $existingPrivKey;
                $blockchain->save();
                $address = $existingAddress;
                $encryptedKey = $existingPrivKey;
            } else {
                // Generate a brand new EVM wallet
                $generator = new EthereumWalletGeneratorService();
                $wallet = $generator->generateWallet();
                $encryptedKey = Crypt::encryptString($wallet['private_key']);

                $blockchain->master_wallet_address = $wallet['address'];
                $blockchain->master_private_key = $encryptedKey;
                $blockchain->save();
                $address = $wallet['address'];
            }

            // Symmetrically write to ALL other EVM networks that do not have a master wallet address set yet
            if ($isFirstTime) {
                foreach ($evmBlockchains as $bc) {
                    if ($bc->isEvm() && $bc->id !== $blockchain->id && empty($bc->master_wallet_address)) {
                        $bc->master_wallet_address = $address;
                        $bc->master_private_key = $encryptedKey;
                        $bc->save();
                    }
                }

                // After first wallet creation, also auto-provision other missing families (e.g., Solana, Tron).
                (new MasterWalletSyncService())->syncMissingMasterWallets();
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __(':blockchain master wallet generated successfully.', ['blockchain' => $blockchain->name]),
                    'address' => $address
                ]);
            }

            return back()->with('success', __(':blockchain master wallet generated successfully.', ['blockchain' => $blockchain->name]));
        } catch (\Exception $e) {
            Log::error("Failed to generate {$blockchain->name} master wallet: " . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Failed to generate master wallet: ') . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', __('Failed to generate master wallet: ') . $e->getMessage());
        }
    }

    /**
     * Reveal the EVM master wallet private key.
     */
    public function reveal(Request $request, $blockchainCode)
    {
        $blockchain = Blockchain::where('code', $blockchainCode)->firstOrFail();
        if (!$blockchain->isEvm()) {
            abort(404);
        }

        // Validate admin password
        $request->validate([
            'password' => 'required|current_password:admin',
        ]);

        $privateKeyEncrypted = $blockchain->master_private_key;

        if (!$privateKeyEncrypted) {
            return response()->json([
                'status' => 'error',
                'message' => __(':blockchain master wallet private key not found.', ['blockchain' => $blockchain->name]),
            ], 404);
        }

        try {
            $privateKey = Crypt::decryptString($privateKeyEncrypted);

            return response()->json([
                'status' => 'success',
                'private_key' => $privateKey,
            ]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error("Failed to decrypt {$blockchain->name} master wallet private key: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => __('Failed to decrypt the private key. Make sure the APP_KEY has not changed.'),
            ], 500);
        }
    }

    /**
     * Get the balance of the EVM master wallet.
     */
    public function getBalance($blockchainCode)
    {
        $blockchain = Blockchain::where('code', $blockchainCode)->firstOrFail();
        if (!$blockchain->isEvm()) {
            abort(404);
        }

        $address = $blockchain->master_wallet_address;

        if (!$address) {
            return response()->json([
                'status' => 'error',
                'message' => __(':blockchain master wallet address not found.', ['blockchain' => $blockchain->name]),
            ], 404);
        }

        try {
            $rpcUrl = $blockchain->rpc_url ?? 'https://eth-mainnet.g.alchemy.com/public';

            $generator = new EthereumWalletGeneratorService();
            $balance = $generator->getEthBalance($address, $rpcUrl);

            // Fetch token balances (USDC, USDT, etc.)
            $tokens = $blockchain->tokens()->where('status', 'enabled')->get();
            $tokenBalances = [];
            
            // Find native currency symbol (e.g. ETH, BNB, POL, AVAX)
            $nativeToken = $tokens->whereNull('mint_address')->first();
            $nativeSymbol = $nativeToken ? strtoupper($nativeToken->symbol) : 'ETH';

            foreach ($tokens as $token) {
                if ($token->mint_address) {
                    try {
                        $rawBal = $generator->getErc20BalanceRaw($address, $token->mint_address, $rpcUrl);
                        $decimals = $token->decimals ?? 6;
                        $tokenBalances[strtoupper($token->symbol)] = (float) gmp_strval($rawBal) / pow(10, $decimals);
                    } catch (\Exception $ex) {
                        Log::error("Failed to query {$blockchain->name} ERC-20 {$token->symbol} balance: " . $ex->getMessage());
                    }
                }
            }

            // Convert native balance to fiat
            $siteCurrency = getSetting('currency', 'USD');
            $converted = rateConverter($balance, $nativeSymbol, $siteCurrency, 'master');

            $fiatBalanceFormatted = '';
            if (!empty($converted) && isset($converted['converted_amount'])) {
                $fiatBalanceFormatted = ' ≈ ' . showAmount($converted['converted_amount']);
            }

            // Sum up USDC/USDC.e balances
            $usdcVal = 0.0;
            foreach ($tokenBalances as $sym => $val) {
                if (str_starts_with($sym, 'USDC')) {
                    $usdcVal += $val;
                }
            }

            return response()->json([
                'status' => 'success',
                'balance' => $balance,
                'formatted_balance' => number_format($balance, 4) . ' ' . $nativeSymbol . $fiatBalanceFormatted,
                'token_balances' => $tokenBalances,
                'usdc' => number_format($usdcVal, 2),
                'usdt' => number_format($tokenBalances['USDT'] ?? 0, 2),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch {$blockchain->name} master wallet balance: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => __('Failed to fetch balance: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show EVM master wallet transaction history and full details.
     */
    public function history($blockchainCode)
    {
        $blockchain = Blockchain::where('code', $blockchainCode)->firstOrFail();
        if (!$blockchain->isEvm()) {
            abort(404);
        }

        $address = $blockchain->master_wallet_address;

        if (!$address) {
            return redirect()->route('admin.evm-master-wallet.index', ['blockchain' => $blockchainCode])
                ->with('error', __(':blockchain master wallet has not been setup yet.', ['blockchain' => $blockchain->name]));
        }

        $page_title = __(':blockchain Master Wallet Details & History', ['blockchain' => $blockchain->name]);
        $template = config('site.template');

        $limit = 15;
        $page = request()->query('page', 1);

        try {
            $rpcUrl = $blockchain->rpc_url ?? 'https://eth-mainnet.g.alchemy.com/public';

            $generator = new EthereumWalletGeneratorService();
            $balance = $generator->getEthBalance($address, $rpcUrl);

            // Find native currency token
            $tokens = $blockchain->tokens()->where('status', 'enabled')->get();
            $nativeToken = $tokens->whereNull('mint_address')->first();
            $nativeSymbol = $nativeToken ? strtoupper($nativeToken->symbol) : 'ETH';

            // Convert to website currency
            $siteCurrency = getSetting('currency', 'USD');
            $converted = rateConverter($balance, $nativeSymbol, $siteCurrency, 'master');

            $fiatBalance = '';
            if (!empty($converted) && isset($converted['converted_amount'])) {
                $fiatBalance = showAmount($converted['converted_amount']);
            }

            // Fetch token balances
            $tokenBalances = [];
            foreach ($tokens as $token) {
                if ($token->mint_address) {
                    try {
                        $rawBal = $generator->getErc20BalanceRaw($address, $token->mint_address, $rpcUrl);
                        $decimals = $token->decimals ?? 6;
                        $tokenBalances[strtoupper($token->symbol)] = (float) gmp_strval($rawBal) / pow(10, $decimals);
                    } catch (\Exception $e) {
                        Log::error("Failed to fetch balance for {$blockchain->name} ERC-20 {$token->symbol} in history: " . $e->getMessage());
                    }
                }
            }

            $usdcValue = 0.0;
            foreach ($tokenBalances as $sym => $val) {
                if (str_starts_with($sym, 'USDC')) {
                    $usdcValue += $val;
                }
            }

            $usdcConverted = rateConverter($usdcValue, 'USD', $siteCurrency, 'master');
            $usdtConverted = rateConverter($tokenBalances['USDT'] ?? 0, 'USD', $siteCurrency, 'master');

            $fiatUsdc = !empty($usdcConverted) && isset($usdcConverted['converted_amount'])
                ? showAmount($usdcConverted['converted_amount'])
                : showAmount($usdcValue);

            $fiatUsdt = !empty($usdtConverted) && isset($usdtConverted['converted_amount'])
                ? showAmount($usdtConverted['converted_amount'])
                : showAmount($tokenBalances['USDT'] ?? 0);

            // Fetch transaction history on-chain
            $onChainTxs = [];

            if ($blockchainCode === 'bsc') {
                // Fetch BSC Scan history using Etherscan API V2 if configured
                $apiKey = env('BSCSCAN_API_KEY');
                if (!empty($apiKey)) {
                    // 1. Normal transactions
                    try {
                        $txResponse = Http::withoutVerifying()->timeout(12)->get("https://api.etherscan.io/v2/api", [
                            'chainid' => 56,
                            'module' => 'account',
                            'action' => 'txlist',
                            'address' => $address,
                            'startblock' => 0,
                            'endblock' => 99999999,
                            'sort' => 'desc',
                            'apikey' => $apiKey,
                        ]);

                        if ($txResponse->successful() && $txResponse->json('status') === '1') {
                            $items = $txResponse->json('result') ?? [];
                            foreach ($items as $item) {
                                $valueWei = gmp_init($item['value'] ?? '0');
                                $valueBnb = (float) gmp_strval($valueWei) / 1e18;

                                $isOutgoing = strtolower($item['from'] ?? '') === strtolower($address);
                                if ($valueBnb > 0 || $isOutgoing) {
                                    $gasUsed = gmp_init($item['gasUsed'] ?? '0');
                                    $gasPrice = gmp_init($item['gasPrice'] ?? '0');
                                    $feeBnb = (float) gmp_strval(gmp_mul($gasUsed, $gasPrice)) / 1e18;

                                    $onChainTxs[] = [
                                        'signature' => $item['hash'],
                                        'block_time' => (int) ($item['timeStamp'] ?? time()),
                                        'slot' => (int) ($item['blockNumber'] ?? 0),
                                        'type' => $isOutgoing ? 'Outgoing' : 'Incoming',
                                        'amount' => $valueBnb,
                                        'token_symbol' => 'BNB',
                                        'error' => ($item['isError'] ?? '0') === '1' ? 'Failed' : null,
                                        'memo' => $isOutgoing ? __('Gas Payment / Transfer') : __('Native Deposit'),
                                        'fee' => $feeBnb
                                    ];
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('BSC Master Wallet normal transactions fetch failed: ' . $e->getMessage());
                    }

                    // 2. Token transfers
                    try {
                        $tokenResponse = Http::withoutVerifying()->timeout(12)->get("https://api.etherscan.io/v2/api", [
                            'chainid' => 56,
                            'module' => 'account',
                            'action' => 'tokentx',
                            'address' => $address,
                            'startblock' => 0,
                            'endblock' => 99999999,
                            'sort' => 'desc',
                            'apikey' => $apiKey,
                        ]);

                        if ($tokenResponse->successful() && $tokenResponse->json('status') === '1') {
                            $items = $tokenResponse->json('result') ?? [];
                            foreach ($items as $item) {
                                $isOutgoing = strtolower($item['from'] ?? '') === strtolower($address);
                                $decimals = (int) ($item['tokenDecimal'] ?? 6);
                                $valWei = gmp_init($item['value'] ?? '0');
                                $valFloat = (float) gmp_strval($valWei) / pow(10, $decimals);

                                $onChainTxs[] = [
                                    'signature' => $item['hash'],
                                    'block_time' => (int) ($item['timeStamp'] ?? time()),
                                    'slot' => (int) ($item['blockNumber'] ?? 0),
                                    'type' => $isOutgoing ? 'Outgoing' : 'Incoming',
                                    'amount' => $valFloat,
                                    'token_symbol' => strtoupper($item['tokenSymbol'] ?? 'TOKEN'),
                                    'error' => null,
                                    'memo' => $isOutgoing ? __('Token Sweep Out') : __('Token Deposit In'),
                                    'fee' => 0
                                ];
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('BSC Master Wallet token transfers fetch failed: ' . $e->getMessage());
                    }
                }
            } else {
                // Blockscout based history retrieval
                $blockscoutHosts = [
                    'ethereum' => 'eth.blockscout.com',
                    'base' => 'base.blockscout.com',
                    'polygon' => 'polygon.blockscout.com',
                    'arbitrum' => 'arbitrum.blockscout.com',
                    'optimism' => 'optimism.blockscout.com',
                    'avalanche' => 'avax.blockscout.com',
                    'fantom' => 'fantom.blockscout.com',
                    'cronos' => 'cronos.blockscout.com',
                    'linea' => 'linea.blockscout.com',
                    'scroll' => 'scroll.blockscout.com',
                    'zksync' => 'zksync.blockscout.com',
                    'celo' => 'celo.blockscout.com',
                    'mantle' => 'mantle.blockscout.com'
                ];
                $blockscoutHost = $blockscoutHosts[$blockchainCode] ?? "{$blockchainCode}.blockscout.com";

                // 1. Fetch normal transactions
                try {
                    $txResponse = Http::withoutVerifying()
                        ->timeout(12)
                        ->get("https://{$blockscoutHost}/api/v2/addresses/{$address}/transactions");

                    if ($txResponse->successful()) {
                        $items = $txResponse->json('items') ?? [];
                        foreach ($items as $item) {
                            $valueWei = gmp_init($item['value'] ?? '0');
                            $valueNative = (float) gmp_strval($valueWei) / 1e18;

                            $isOutgoing = strtolower($item['from']['hash'] ?? '') === strtolower($address);
                            if ($valueNative > 0 || $isOutgoing) {
                                $feeWei = gmp_init($item['fee']['value'] ?? '0');
                                $feeNative = (float) gmp_strval($feeWei) / 1e18;

                                $onChainTxs[] = [
                                    'signature' => $item['hash'],
                                    'block_time' => strtotime($item['timestamp']),
                                    'slot' => $item['block_number'] ?? 0,
                                    'type' => $isOutgoing ? 'Outgoing' : 'Incoming',
                                    'amount' => $valueNative,
                                    'token_symbol' => $nativeSymbol,
                                    'error' => ($item['status'] ?? 'ok') !== 'ok' ? 'Failed' : null,
                                    'memo' => $isOutgoing ? __('Gas Payment / Transfer') : __('Native Deposit'),
                                    'fee' => $feeNative
                                ];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("{$blockchain->name} normal transactions fetch failed: " . $e->getMessage());
                }

                // 2. Fetch ERC-20 token transfers
                try {
                    $tokenResponse = Http::withoutVerifying()
                        ->timeout(12)
                        ->get("https://{$blockscoutHost}/api/v2/addresses/{$address}/token-transfers");

                    if ($tokenResponse->successful()) {
                        $items = $tokenResponse->json('items') ?? [];
                        foreach ($items as $item) {
                            if (($item['token_type'] ?? '') === 'ERC-20' && isset($item['token'])) {
                                $isOutgoing = strtolower($item['from']['hash'] ?? '') === strtolower($address);
                                $decimals = (int) ($item['token']['decimals'] ?? 6);
                                $valWei = gmp_init($item['total']['value'] ?? '0');
                                $valFloat = (float) gmp_strval($valWei) / pow(10, $decimals);

                                $onChainTxs[] = [
                                    'signature' => $item['transaction_hash'],
                                    'block_time' => strtotime($item['timestamp']),
                                    'slot' => $item['block_number'] ?? 0,
                                    'type' => $isOutgoing ? 'Outgoing' : 'Incoming',
                                    'amount' => $valFloat,
                                    'token_symbol' => strtoupper($item['token']['symbol'] ?? 'TOKEN'),
                                    'error' => null,
                                    'memo' => $isOutgoing ? __('Token Sweep Out') : __('Token Deposit In'),
                                    'fee' => 0
                                ];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("{$blockchain->name} token transfers fetch failed: " . $e->getMessage());
                }
            }

            // Sort all transactions by block_time DESC
            usort($onChainTxs, function ($a, $b) {
                return $b['block_time'] <=> $a['block_time'];
            });

            // Fallback to local DB deposits if on-chain history is empty
            if (empty($onChainTxs)) {
                $dbDeposits = Deposit::where('blockchain_id', $blockchain->id)
                    ->where('status', 'completed')
                    ->latest()
                    ->get();
                foreach ($dbDeposits as $dep) {
                    $onChainTxs[] = [
                        'signature' => $dep->transaction_hash,
                        'block_time' => $dep->created_at->timestamp,
                        'slot' => 0,
                        'type' => 'Incoming',
                        'amount' => $dep->converted_amount,
                        'token_symbol' => $dep->currency,
                        'error' => null,
                        'memo' => __('Deposit Logged (Fallback)'),
                        'fee' => 0
                    ];
                }
            }

            // Manual pagination on the sorted array
            $total = count($onChainTxs);
            $offset = ($page - 1) * $limit;
            $paginatedTxs = array_slice($onChainTxs, $offset, $limit);

            $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
                $paginatedTxs,
                $total,
                $limit,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('templates.' . $template . '.blades.admin.evm.history', compact(
                'page_title',
                'blockchain',
                'address',
                'balance',
                'nativeSymbol',
                'fiatBalance',
                'tokenBalances',
                'fiatUsdc',
                'fiatUsdt',
                'transactions'
            ));
        } catch (\Exception $e) {
            Log::error("Error loading {$blockchain->name} Master Wallet history: " . $e->getMessage());
            return redirect()->route('admin.evm-master-wallet.index', ['blockchain' => $blockchainCode])
                ->with('error', __('Error loading wallet details: ') . $e->getMessage());
        }
    }

    /**
     * Synchronize and auto-create missing master wallet addresses.
     */
    public function sync(Request $request)
    {
        try {
            $syncResult = (new MasterWalletSyncService())->syncMissingMasterWallets();
            $updatedChains = $syncResult['updated_chains'];
            $groupedUpdates = $syncResult['grouped_updates'];
            $count = $syncResult['updated_count'];

            if ($count > 0) {
                $message = __('Successfully synchronized master wallets. Generated/linked master addresses for :count networks: :chains.', [
                    'count' => $count,
                    'chains' => implode(', ', $updatedChains)
                ]);
            } else {
                $message = __('All active blockchain networks are already configured with master wallets.');
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'updated_count' => $count,
                'updated_chains' => $updatedChains,
                'updated' => $groupedUpdates,
            ]);
        } catch (\Exception $e) {
            Log::error('Master wallets sync action failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to synchronize master wallets: ') . $e->getMessage()
            ], 500);
        }
    }
}
