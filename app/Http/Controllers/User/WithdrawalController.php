<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class WithdrawalController extends Controller
{
    // scope
    public function byScope(Request $request)
    {
        $user = Auth::user();
        $routeParts = explode('.', $request->route()->getName());
        $scopeName = end($routeParts); // e.g., 'approved', 'pending', 'failed', 'partial'

        $statusMap = [
            'approved' => 'completed',
            'pending' => 'pending',
            'failed' => 'failed',
            'partial' => 'partial_payment'
        ];

        $status = $statusMap[$scopeName] ?? $scopeName;

        $query = Withdrawal::with(['blockchain', 'blockchainToken'])->where('user_id', $user->id)->where('status', $status);

        // Filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_reference', 'like', "%$search%")
                    ->orWhere('transaction_hash', 'like', "%$search%")
                    ->orWhere('amount', 'like', "%$search%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->get('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->get('to_date'));
        }

        if ($request->filled('blockchain_id')) {
            $query->where('blockchain_id', $request->get('blockchain_id'));
        }

        // Total for current filters (using amount_payable for withdrawals)
        $totalAmount = (clone $query)->sum('amount_payable');

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = in_array(strtolower($request->get('direction')), ['asc', 'desc']) ? $request->get('direction') : 'desc';
        $query->orderBy($sort, $direction);

        $withdrawals = $query->paginate(getSetting('pagination', 15))->appends($request->all());

        $blockchains = \App\Models\Blockchain::where('status', 'enabled')->get();

        $template = config('site.template');
        $page_title = __(ucfirst($scopeName) . ' Withdrawals');

        return view("templates.$template.blades.user.withdrawals.scope", compact(
            'status',
            'scopeName',
            'page_title',
            'withdrawals',
            'totalAmount',
            'blockchains'
        ));
    }

    public function index(Request $request)
    {
        $template = config('site.template');
        $page_title = __('Withdrawal History');
        $user_id = Auth::id();

        // Withdrawals list query
        $withdrawals_query = Withdrawal::with(['blockchain', 'blockchainToken'])->where('user_id', $user_id);

        // search - reference or hash
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $withdrawals_query->where(function ($q) use ($searchTerm) {
                $q->where('transaction_reference', 'like', '%' . $searchTerm . '%')
                    ->orWhere('transaction_hash', 'like', '%' . $searchTerm . '%');
            });
        }

        $withdrawals = $withdrawals_query->latest()->paginate(getSetting('pagination', 10));

        // Analytics query
        $stats = Withdrawal::where('user_id', $user_id)
            ->select([
                DB::raw("COUNT(*) as total_count"),
                DB::raw("SUM(amount) as total_sum"),
                DB::raw("SUM(status = 'pending') as pending_count"),
                DB::raw("SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending_sum"),
                DB::raw("SUM(status = 'completed') as completed_count"),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as completed_sum"),
                DB::raw("SUM(status = 'failed') as failed_count"),
                DB::raw("SUM(CASE WHEN status = 'failed' THEN amount ELSE 0 END) as failed_sum"),
                DB::raw("SUM(status = 'partial_payment') as partial_count"),
                DB::raw("SUM(CASE WHEN status = 'partial_payment' THEN amount ELSE 0 END) as partial_sum"),
            ])
            ->first();

        $withdrawals_analytics = [
            'total' => ['count' => (int) $stats->total_count, 'total' => $stats->total_sum],
            'pending' => ['count' => (int) $stats->pending_count, 'total' => $stats->pending_sum],
            'completed' => ['count' => (int) $stats->completed_count, 'total' => $stats->completed_sum],
            'failed' => ['count' => (int) $stats->failed_count, 'total' => $stats->failed_sum],
            'partial_payment' => ['count' => (int) $stats->partial_count, 'total' => $stats->partial_sum],
        ];

        // Advanced Analytics
        $avg_processing_time = Withdrawal::where('user_id', $user_id)
            ->where('status', 'completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_seconds')
            ->value('avg_seconds');

        $fastest_withdrawal = Withdrawal::where('user_id', $user_id)
            ->where('status', 'completed')
            ->orderByRaw('TIMESTAMPDIFF(SECOND, created_at, updated_at) ASC')
            ->first();

        $slowest_withdrawal = Withdrawal::where('user_id', $user_id)
            ->where('status', 'completed')
            ->orderByRaw('TIMESTAMPDIFF(SECOND, created_at, updated_at) DESC')
            ->first();

        $fee_stats = Withdrawal::where('user_id', $user_id)
            ->selectRaw('SUM(fee_amount) as total_fees, AVG(fee_percent) as avg_fee_percent')
            ->first();

        $method_stats = Withdrawal::where('user_id', $user_id)
            ->whereNotNull('blockchain_id')
            ->selectRaw('blockchain_id, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('blockchain_id')
            ->with('blockchain')
            ->get();

        $currency_stats = Withdrawal::where('user_id', $user_id)
            ->selectRaw('currency, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('currency')
            ->get();

        $largest_withdrawal = Withdrawal::where('user_id', $user_id)
            ->orderByDesc('amount')
            ->first();

        $highest_daily_total = Withdrawal::where('user_id', $user_id)
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->orderByDesc('total')
            ->first();

        return view("templates.$template.blades.user.withdrawals.index", compact(
            'page_title',
            'withdrawals',
            'withdrawals_analytics',
            'avg_processing_time',
            'fastest_withdrawal',
            'slowest_withdrawal',
            'fee_stats',
            'method_stats',
            'currency_stats',
            'largest_withdrawal',
            'highest_daily_total'
        ));
    }

    // view withdrawal
    public function viewWithdrawal(Request $request)
    {
        $template = config('site.template');
        $page_title = __('Withdrawal Details');
        $transaction_reference = $request->route('transaction_reference');
        $withdrawal = Withdrawal::where('transaction_reference', $transaction_reference)->where('user_id', Auth::id())->first();
        if (!$withdrawal) {
            return redirect()->route('user.withdrawals.index')->with('error', __('Withdrawal not found'));
        }
        return view("templates.$template.blades.user.withdrawals.view", compact(
            'page_title',
            'withdrawal'
        ));
    }


    // new withdrawal
    public function newWithdrawal()
    {
        $page_title = __('New Withdrawal');
        $template = config('site.template');
        $blockchains = \App\Models\Blockchain::with(['tokens' => function ($q) {
            $q->where('status', 'enabled')->orderBy('priority', 'asc');
        }])->where('status', 'enabled')->orderByRaw('COALESCE(priority, 9999) ASC')->get();
        $token_blockchain_map = buildDepositTokenBlockchainMap($blockchains);

        return view('templates.' . $template . '.blades.user.withdrawals.new', compact(
            'page_title',
            'blockchains',
            'token_blockchain_map'
        ));
    }

    // new withdrawal validate
    public function newWithdrawalValidate(Request $request)
    {
        $request->validate([
            'blockchain_id' => 'required|exists:blockchains,id',
            'amount' => 'required|numeric|min:0.01',
            'wallet_address' => 'required|string',
        ]);

        $blockchain = \App\Models\Blockchain::where('status', 'enabled')->find($request->blockchain_id);
        if (!$blockchain) {
            return response()->json([
                'status' => 'error',
                'message' => __('Selected blockchain is not available.'),
            ], 422);
        }

        $tokenId = $request->blockchain_token_id ?? $request->token_id;
        $token = \App\Models\BlockchainToken::where('blockchain_id', $blockchain->id)
            ->where('status', 'enabled')
            ->find($tokenId)
            ?? \App\Models\BlockchainToken::where('status', 'enabled')->find($tokenId);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => __('Selected token is not supported.'),
            ], 422);
        }


        $min_withdrawal = getSetting('min_withdrawal');
        $max_withdrawal = getSetting('max_withdrawal');
        $withdrawal_fee = getSetting('withdrawal_fee');
        $website_currency = getSetting('currency');

        $amount = $request->amount;
        if ($amount > $max_withdrawal) {
            return response()->json([
                'status' => 'error',
                'message' => __("Maximum withdrawal amount is :amount :currency", ['amount' => $max_withdrawal, 'currency' => $website_currency])
            ], 422);
        }

        if ($amount < $min_withdrawal) {
            return response()->json([
                'status' => 'error',
                'message' => __("Minimum withdrawal amount is :amount :currency", ['amount' => $min_withdrawal, 'currency' => $website_currency])
            ], 422);
        }

        $user = Auth::user();
        if ($amount > $user->balance) {
            return response()->json([
                'status' => 'error',
                'message' => __("Insufficient balance")
            ], 422);
        }

        // Calculate fees and payable
        $fee_percent = $withdrawal_fee;
        $fee_amount = $amount * ($fee_percent / 100);
        $amount_payable = $amount - $fee_amount;

        // Convert amount_payable to token currency
        $token_currency = $token->symbol;
        $conversion = rateConverter($amount_payable, $website_currency, $token_currency, 'token');
        $rate = $conversion['exchange_rate'];
        $converted_amount = $conversion['converted_amount'];
        $ref = Str::orderedUuid();

        // Perform address validation for supported non-EVM blockchains
        if (strtoupper($blockchain->code) === 'SOLANA') {
            try {
                $walletBytes = \App\Services\SolanaBase58::decode($request->wallet_address);
                if (strlen($walletBytes) !== 32) {
                    throw new \Exception();
                }
                if (!\App\Services\SolanaWalletGeneratorService::isOnCurve($walletBytes)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => __('Recipient address is not a valid Solana wallet address.')
                    ], 422);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Invalid Solana wallet address format.')
                ], 422);
            }
        } elseif (strtoupper($blockchain->code) === 'TRON') {
            $tronService = new \App\Services\TronWalletGeneratorService();
            if (!$tronService->isValidAddress($request->wallet_address)) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Recipient address is not a valid Tron wallet address.')
                ], 422);
            }
        }

        // Debit User
        $user->refresh();
        $user->decrement('balance', $amount);

        // Populate withdrawal record
        $withdrawal = new Withdrawal();
        $withdrawal->user_id = Auth::id();
        $withdrawal->blockchain_id = $blockchain->id;
        $withdrawal->blockchain_token_id = $token->id;
        $withdrawal->amount = $amount;
        $withdrawal->converted_amount = $converted_amount;
        $withdrawal->fee_percent = $fee_percent;
        $withdrawal->fee_amount = $fee_amount;
        $withdrawal->amount_payable = $amount_payable;
        $withdrawal->exchange_rate = $rate;
        $withdrawal->transaction_reference = $ref;
        $withdrawal->transaction_hash = null;
        $withdrawal->payment_proof = null;
        $withdrawal->currency = $token_currency;

        $structured_data = [
            'wallet_address' => $request->wallet_address,
            'currency' => $token_currency,
            'network' => $blockchain->name,
        ];
        $withdrawal->structured_data = json_encode($structured_data);
        $withdrawal->auto_res_dump = null;

        // Check auto approve status
        $autoApprove = getSetting('auto_approve_withdrawal', 'disabled');
        if ($autoApprove === 'enabled') {
            try {
                if ($blockchain->code === 'solana') {
                    $withdrawalService = new \App\Services\SolanaWithdrawalService();
                } elseif ($blockchain->isEvm()) {
                    $withdrawalService = new \App\Services\EthereumWithdrawalService();
                } elseif ($blockchain->isTron()) {
                    $withdrawalService = new \App\Services\TronWithdrawalService();
                } elseif ($blockchain->isBitcoin()) {
                    $withdrawalService = new \App\Services\BitcoinWithdrawalService();
                } else {
                    throw new \Exception(__('Blockchain network is not supported for automatic payouts.'));
                }
                $txHash = $withdrawalService->processPayout($withdrawal);
                $withdrawal->transaction_hash = $txHash;
                $withdrawal->status = 'completed';
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Auto-Payout Execution Failed: ' . $e->getMessage());
                // Refund user
                $user->increment('balance', $amount);

                $msg = $e->getMessage();
                if (strpos(strtolower($msg), 'simulation failed') !== false) {
                    if (app()->environment('production')) {
                        $msg = __('Withdrawal using :token is not available, try other tokens for withdrawal.', ['token' => $token->name]);
                    } else {
                        $msg = __('Insufficient balance in master wallet (Simulation failed).');
                    }
                }

                return response()->json([
                    'status' => 'error',
                    'message' => $msg,
                ], 400);
            }
        } else {
            $withdrawal->status = 'pending';
        }

        $withdrawal->save();

        if ($withdrawal->status === 'completed') {
            // record complete transaction
            recordTransaction($user, $amount, $website_currency, $converted_amount, $token_currency, $rate, 'debit', 'completed', $ref, "Completed Blockchain Payout", $user->balance);

            // send notification
            $title = 'Withdrawal Processed';
            $message = __('Your withdrawal of :amount :currency has been successfully processed and sent to your wallet on-chain.', ['amount' => $amount_payable, 'currency' => $website_currency]);
            recordNotificationMessage($user, $title, $message);

            // send completed email
            sendWithdrawalEmail("Withdrawal Completed", "Your on-chain payout request has been successfully processed and broadcast.", $withdrawal);
        } else {
            // record pending transaction
            recordTransaction($user, $amount, $website_currency, $converted_amount, $token_currency, $rate, 'debit', 'completed', $ref, "Pending Blockchain Payout", $user->balance);

            // send notification
            $title = 'Withdrawal Submitted';
            $message = __('Your withdrawal of :amount :currency is pending administrative review.', ['amount' => $amount_payable, 'currency' => $website_currency]);
            recordNotificationMessage($user, $title, $message);

            // send pending email
            sendWithdrawalEmail("Withdrawal Pending Review", "Your on-chain withdrawal request is submitted and under review.", $withdrawal);
        }

        return response()->json([
            'status' => 'success',
            'message' => $withdrawal->status === 'completed'
                ? __('Withdrawal payout completed successfully.')
                : __('Withdrawal request submitted for review.'),
            'redirect' => route('user.withdrawals.view', $withdrawal->transaction_reference)
        ]);
    }

    public function getRate(Request $request)
    {
        $request->validate([
            'blockchain_token_id' => 'required|exists:blockchain_tokens,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $token = \App\Models\BlockchainToken::find($request->blockchain_token_id);
        $website_currency = getSetting('currency');
        $withdrawal_fee = getSetting('withdrawal_fee');

        $amount = $request->amount;
        $fee_amount = $amount * ($withdrawal_fee / 100);
        $amount_payable = max(0, $amount - $fee_amount);

        $conversion = rateConverter($amount_payable, $website_currency, $token->symbol, 'token');

        return response()->json([
            'status' => 'success',
            'fee_amount' => $fee_amount,
            'amount_payable' => $amount_payable,
            'exchange_rate' => $conversion['exchange_rate'],
            'converted_amount' => $conversion['converted_amount'],
            'symbol' => $token->symbol,
        ]);
    }
}
