<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MasterWalletSyncService;
use App\Services\SolanaWalletGeneratorService;
use App\Models\Blockchain;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class SolanaMasterWalletController extends Controller
{
    /**
     * Display the Solana master wallet setup/status dashboard.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $page_title = __('Solana Master Wallet Setup & Status');
        $template = config('site.template');

        return view('templates.' . $template . '.blades.admin.solana.index', compact('page_title'));
    }

    /**
     * Generate the Solana master wallet.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function generate(Request $request)
    {
        $force = $request->boolean('force', false);
        $blockchain = Blockchain::where('code', 'solana')->firstOrFail();

        // Check if master wallet already exists
        if ($blockchain->master_wallet_address && !$force) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Solana master wallet already exists.'),
                ], 400);
            }
            return back()->with('error', __('Solana master wallet already exists.'));
        }

        try {
            $generator = new SolanaWalletGeneratorService();
            $wallet = $generator->generateWallet();

            // Save public address and encrypted secret key directly on the blockchain model row
            $blockchain->master_wallet_address = $wallet['address'];
            $blockchain->master_private_key = Crypt::encryptString($wallet['private_key']);
            $blockchain->save();

            // After wallet creation, provision any other missing blockchain master wallets.
            (new MasterWalletSyncService())->syncMissingMasterWallets();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Solana master wallet generated successfully.'),
                    'address' => $wallet['address']
                ]);
            }

            return back()->with('success', __('Solana master wallet generated successfully.'));
        } catch (\Exception $e) {
            Log::error('Failed to generate Solana master wallet: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Failed to generate Solana master wallet: ') . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', __('Failed to generate Solana master wallet: ') . $e->getMessage());
        }
    }

    /**
     * Reveal the Solana master wallet private key.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reveal(Request $request)
    {
        // Validate that admin password matches the current authenticated admin's password
        $request->validate([
            'password' => 'required|current_password:admin',
        ]);

        $blockchain = Blockchain::where('code', 'solana')->firstOrFail();
        $privateKeyEncrypted = $blockchain->master_private_key;

        if (!$privateKeyEncrypted) {
            return response()->json([
                'status' => 'error',
                'message' => __('Solana master wallet private key not found.'),
            ], 404);
        }

        try {
            $privateKey = Crypt::decryptString($privateKeyEncrypted);

            return response()->json([
                'status' => 'success',
                'private_key' => $privateKey,
            ]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Failed to decrypt Solana master wallet private key: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => __('Failed to decrypt the private key. Make sure the APP_KEY has not changed.'),
            ], 500);
        }
    }

    /**
     * Get the balance of the Solana master wallet.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalance()
    {
        $blockchain = Blockchain::where('code', 'solana')->firstOrFail();
        $address = $blockchain->master_wallet_address;

        if (!$address) {
            return response()->json([
                'status' => 'error',
                'message' => __('Solana master wallet address not found.'),
            ], 404);
        }

        try {
            $generator = new SolanaWalletGeneratorService();
            $balanceInSol = $generator->getBalance($address);

            // Fetch token balances (USDC, USDT)
            $tokenBalances = $generator->getSPLTokenBalances($address);

            // Convert SOL balance to fiat
            $siteCurrency = getSetting('currency', 'USD');
            $converted = rateConverter($balanceInSol, 'SOL', $siteCurrency, 'master');

            $fiatBalanceFormatted = '';
            if (!empty($converted) && isset($converted['converted_amount'])) {
                $fiatBalanceFormatted = ' ≈ ' . showAmount($converted['converted_amount']);
            }

            return response()->json([
                'status' => 'success',
                'balance' => $balanceInSol,
                'formatted_balance' => number_format($balanceInSol, 4) . ' SOL' . $fiatBalanceFormatted,
                'token_balances' => $tokenBalances,
                'usdc' => number_format($tokenBalances['USDC'], 2),
                'usdt' => number_format($tokenBalances['USDT'], 2),
            ]);
        } catch (\Exception $e) {
            Log::error('Solana RPC getBalance failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show Solana master wallet transaction history and full details.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function history()
    {
        $blockchain = Blockchain::where('code', 'solana')->firstOrFail();
        $address = $blockchain->master_wallet_address;

        if (!$address) {
            return redirect()->route('admin.solana-master-wallet.index')->with('error', __('Solana master wallet has not been setup yet.'));
        }

        $page_title = __('Solana Master Wallet Details & History');
        $template = config('site.template');

        $before = request()->query('before');
        $stackStr = request()->query('stack');
        $stack = $stackStr ? explode(',', $stackStr) : [];
        $limit = 15;
        $hasMore = false;
        $nextBefore = null;
        $nextStackStr = '';
        $hasPrev = count($stack) > 0;
        $prevBefore = null;
        $prevStackStr = '';

        try {
            $generator = new SolanaWalletGeneratorService();
            $balance = $generator->getBalance($address);

            // Convert to website currency
            $siteCurrency = getSetting('currency', 'USD');
            $converted = rateConverter($balance, 'SOL', $siteCurrency, 'master');

            $fiatBalance = '';
            if (!empty($converted) && isset($converted['converted_amount'])) {
                $fiatBalance = showAmount($converted['converted_amount']);
            }

            // Fetch token balances
            $tokenBalances = $generator->getSPLTokenBalances($address);
            $usdcValue = $tokenBalances['USDC'];
            $usdtValue = $tokenBalances['USDT'];

            $usdcConverted = rateConverter($usdcValue, 'USD', $siteCurrency, 'master');
            $usdtConverted = rateConverter($usdtValue, 'USD', $siteCurrency, 'master');

            $fiatUsdc = !empty($usdcConverted) && isset($usdcConverted['converted_amount'])
                ? showAmount($usdcConverted['converted_amount'])
                : showAmount($usdcValue);

            $fiatUsdt = !empty($usdtConverted) && isset($usdtConverted['converted_amount'])
                ? showAmount($usdtConverted['converted_amount'])
                : showAmount($usdtValue);

            // Fetch transaction history
            $rawTransactions = $generator->getTransactionsHistory($address, $limit + 1, $before);

            $transactions = [];
            if (!empty($rawTransactions)) {
                if (count($rawTransactions) > $limit) {
                    $hasMore = true;
                    // The last item is the signature of the block BEFORE the next page
                    $lastItem = array_pop($rawTransactions);
                    $nextBefore = $lastItem['signature'];
                }
                
                foreach ($rawTransactions as $tx) {
                    $transactions[] = [
                        'signature' => $tx['signature'],
                        'block_time' => $tx['block_time'],
                        'slot' => $tx['slot'],
                        'type' => $tx['type'],
                        'amount' => (float) $tx['amount'],
                        'token_symbol' => $tx['token_symbol'],
                        'error' => $tx['error'],
                        'memo' => $tx['memo'] ?? ($tx['type'] === 'Outgoing' ? __('Gas Payment / Transfer') : ($tx['token_symbol'] === 'SOL' ? __('Native Deposit') : __('Token Deposit In'))),
                        'fee' => $tx['fee']
                    ];
                }
            }

            // Build dynamic pagination stacks
            if ($hasMore) {
                $nextStack = array_merge($stack, [$before ?: 'start']);
                $nextStackStr = implode(',', $nextStack);
            }

            if ($hasPrev) {
                $prevStack = $stack;
                $popped = array_pop($prevStack);
                $prevBefore = $popped === 'start' ? null : $popped;
                $prevStackStr = implode(',', $prevStack);
            }

            if (request()->expectsJson()) {
                $fiatTokenBalances = [];
                foreach ($tokenBalances as $sym => $val) {
                    $symUpper = strtoupper($sym);
                    $tokConv = rateConverter($val, $symUpper, $siteCurrency, 'master');
                    $fiatTokenBalances[$symUpper] = !empty($tokConv) && isset($tokConv['converted_amount'])
                        ? showAmount($tokConv['converted_amount'])
                        : showAmount($val);
                }

                return response()->json([
                    'status' => 'success',
                    'balance' => $balance,
                    'fiat_balance' => $fiatBalance,
                    'token_balances' => $tokenBalances,
                    'fiat_token_balances' => $fiatTokenBalances,
                    'transactions' => $transactions,
                    'has_more' => $hasMore,
                    'next_before' => $nextBefore,
                    'next_stack' => $nextStackStr,
                    'has_prev' => $hasPrev,
                    'prev_before' => $prevBefore,
                    'prev_stack' => $prevStackStr
                ]);
            }

            return view('templates.' . $template . '.blades.admin.solana.history', compact(
                'page_title',
                'blockchain',
                'address',
                'balance',
                'fiatBalance',
                'tokenBalances',
                'usdcValue',
                'usdtValue',
                'fiatUsdc',
                'fiatUsdt',
                'transactions',
                'hasMore',
                'nextBefore',
                'nextStackStr',
                'hasPrev',
                'prevBefore',
                'prevStackStr'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading Solana Master Wallet history: ' . $e->getMessage());
            return redirect()->route('admin.solana-master-wallet.index')
                ->with('error', __('Error loading wallet details: ') . $e->getMessage());
        }
    }
}
