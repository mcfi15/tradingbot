<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Services\SolanaWalletGeneratorService;
use App\Services\EthereumWalletGeneratorService;
use App\Services\TronWalletGeneratorService;
use App\Services\UserWalletProvisionerService;
use App\Models\Blockchain;
use App\Models\UserBlockchainWallet;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $template = config('site.template');
        $page_title = __('Deposits');

        $user_id = auth()->id();

        // Deposits list query
        $deposits_query = Deposit::where('user_id', $user_id);

        // check for search - search by transaction reference or transaction hash
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $deposits_query->where(function ($q) use ($searchTerm) {
                $q->where('transaction_reference', 'like', '%' . $searchTerm . '%')
                    ->orWhere('transaction_hash', 'like', '%' . $searchTerm . '%');
            });
        }

        $deposits = $deposits_query->with(['blockchain', 'blockchainToken'])->latest()->paginate(getSetting('pagination', 10));

        // Analytics query
        $stats = Deposit::where('user_id', $user_id)
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

        // Format into your structure
        $deposits_analytics = [
            'total' => [
                'count' => (int) $stats->total_count,
                'total' => $stats->total_sum,
            ],
            'pending' => [
                'count' => (int) $stats->pending_count,
                'total' => $stats->pending_sum,
            ],
            'completed' => [
                'count' => (int) $stats->completed_count,
                'total' => $stats->completed_sum,
            ],
            'failed' => [
                'count' => (int) $stats->failed_count,
                'total' => $stats->failed_sum,
            ],
            'partial_payment' => [
                'count' => (int) $stats->partial_count,
                'total' => $stats->partial_sum,
            ],
        ];



        // Base query
        $deposits_query = Deposit::where('user_id', $user_id);

        // 1. Status Counts & Totals
        $depositStats = $deposits_query->selectRaw("
            COUNT(*) as total_count,
            SUM(amount) as total_amount,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending_total,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
            SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as completed_total,
            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
            SUM(CASE WHEN status = 'failed' THEN amount ELSE 0 END) as failed_total,
            SUM(CASE WHEN status = 'partial_payment' THEN 1 ELSE 0 END) as partial_count,
            SUM(CASE WHEN status = 'partial_payment' THEN amount ELSE 0 END) as partial_total
        ")->first();

        // 2. Average processing time for completed deposits
        $avg_processing_time = Deposit::where('user_id', $user_id)
            ->where('status', 'completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_seconds')
            ->value('avg_seconds');

        // 3. Fastest and slowest completed deposits
        $fastest_deposit = Deposit::where('user_id', $user_id)
            ->where('status', 'completed')
            ->orderByRaw('TIMESTAMPDIFF(SECOND, created_at, updated_at) ASC')
            ->first();

        $slowest_deposit = Deposit::where('user_id', $user_id)
            ->where('status', 'completed')
            ->orderByRaw('TIMESTAMPDIFF(SECOND, created_at, updated_at) DESC')
            ->first();

        // 4. Fees & Effective Rate
        $fee_stats = Deposit::where('user_id', $user_id)
            ->selectRaw('SUM(fee_amount) as total_fees, AVG(fee_percent) as avg_fee_percent')
            ->first();

        // 5. Deposit Method Analytics
        $method_stats = Deposit::where('user_id', $user_id)
            ->with(['blockchain'])
            ->selectRaw('blockchain_id, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('blockchain_id')
            ->get();

        // 6. Currency Breakdown
        $currency_stats = Deposit::where('user_id', $user_id)
            ->selectRaw('currency, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('currency')
            ->get();

        // 7. Personal Records
        $largest_deposit = Deposit::where('user_id', $user_id)
            ->orderByDesc('amount')
            ->first();

        $highest_daily_total = Deposit::where('user_id', $user_id)
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->orderByDesc('total')
            ->first();



        return view("templates.$template.blades.user.deposits.index", compact(
            'page_title',
            'deposits',
            'deposits_analytics',
            'avg_processing_time',
            'fastest_deposit',
            'slowest_deposit',
            'fee_stats',
            'method_stats',
            'currency_stats',
            'largest_deposit',
            'highest_daily_total',
        ));
    }


    // View a single deposit
    public function viewDeposit(Request $request)
    {
        $template = config('site.template');
        $page_title = __('View Deposit');
        $transaction_reference = $request->route('transaction_reference');
        $deposit = Deposit::where('transaction_reference', $transaction_reference)->first();
        if (!$deposit) {
            return redirect()->route('user.deposits.index')->with('error', __('Deposit not found'));
        }
        return view("templates.$template.blades.user.deposits.view", compact(
            'page_title',
            'deposit'
        ));
    }


    // new deposit
    public function newDeposit()
    {
        $template = config('site.template');
        $page_title = __('New Deposit');

        $user = auth()->user();

        // Check if there is any blockchain with missing wallet address, and auto-provision before rendering
        if ($user) {
            app(UserWalletProvisionerService::class)->provisionWallets($user);
        }

        // Fetch all active blockchains with their enabled tokens
        $blockchains = Blockchain::where('status', 'enabled')
            ->orderByRaw('COALESCE(priority, 9999) ASC')
            ->with(['tokens' => function ($query) {
                $query->where('status', 'enabled')->orderBy('priority', 'asc');
            }])
            ->get();

        // Fetch user's wallets for these blockchains
        $user_wallets = UserBlockchainWallet::where('user_id', $user?->id)
            ->get()
            ->keyBy('blockchain_id');

        // Build token -> blockchain rows used by the bento deposit selector UI.
        $token_blockchain_map = buildDepositTokenBlockchainMap($blockchains, $user_wallets);

        return view("templates.$template.blades.user.deposits.new-d", compact(
            'page_title',
            'blockchains',
            'user_wallets',
            'token_blockchain_map'
        ));
    }

    /**
     * Generate a personal deposit wallet for the user for a specific blockchain.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateWallet(Request $request)
    {
        $validated = $request->validate([
            'blockchain_id' => 'required|exists:blockchains,id',
        ]);

        $blockchain = Blockchain::where('id', $request->blockchain_id)
            ->where('status', 'enabled')
            ->first();

        if (!$blockchain) {
            return response()->json([
                'status' => 'error',
                'message' => __('Blockchain not supported or disabled.'),
            ], 404);
        }

        $user = auth()->user();

        // Check if user already has a wallet for this blockchain
        $existingWallet = UserBlockchainWallet::where('user_id', $user->id)
            ->where('blockchain_id', $blockchain->id)
            ->first();

        if ($existingWallet) {
            return response()->json([
                'status' => 'error',
                'message' => __('Wallet already generated for this blockchain.'),
                'address' => $existingWallet->address
            ], 400);
        }

        try {
            app(UserWalletProvisionerService::class)->provisionWallets($user);

            $wallet = UserBlockchainWallet::where('user_id', $user->id)
                ->where('blockchain_id', $blockchain->id)
                ->first();

            if ($wallet) {
                return response()->json([
                    'status' => 'success',
                    'message' => __(':blockchain deposit wallet generated successfully!', ['blockchain' => $blockchain->name]),
                    'address' => $wallet->address
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => __('Wallet generation for this blockchain is under development.'),
            ], 501);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Wallet generation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to generate wallet: ') . $e->getMessage(),
            ], 500);
        }
    }

    // donwload receipt
    public function downloadReceipt(Request $request)
    {
        $transaction_reference = $request->route('transaction_reference');
        $deposit = Deposit::where('transaction_reference', $transaction_reference)->where('user_id', auth()->user()->id)->first();
        if (!$deposit) {
            return redirect()->route('user.deposits.index')->with('error', __('Deposit not found'));
        }

        $template = config('site.template');

        // // tempaoru view in browser to see design
        // return view("templates.$template.blades.pdf.receipt", compact('deposit'));
        $pdf = PDF::loadView("templates.$template.blades.pdf.receipt", compact('deposit'));
        return $pdf->download("receipt-$transaction_reference.pdf");
    }


    // scope
    public function byScope(Request $request)
    {
        $user = auth()->user();
        $routeParts = explode('.', $request->route()->getName());
        $scopeName = end($routeParts); // e.g., 'approved', 'pending', 'failed'

        $status = $scopeName == 'approved' ? 'completed' : $scopeName;

        $query = Deposit::where('user_id', $user->id)->where('status', $status);

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

        if ($request->filled('method_id')) {
            $query->where('blockchain_id', $request->get('method_id'));
        }

        // Total for current filters
        $totalAmount = (clone $query)->sum('amount');

        // Sorting
        $sort = $request->get('sort', 'created_at');
        // direction is only allowed to be 'asc' or 'desc', else default to 'desc'
        $direction = in_array(strtolower($request->get('direction')), ['asc', 'desc']) ? $request->get('direction') : 'desc';
        $query->orderBy($sort, $direction);

        $deposits = $query->paginate(getSetting('pagination', 15))->appends($request->all());

        $payment_methods = Blockchain::where('status', 'enabled')->get();

        $template = config('site.template');
        $page_title = __(ucfirst($scopeName) . ' Deposits');

        return view("templates.$template.blades.user.deposits.scope", compact(
            'status',
            'scopeName',
            'page_title',
            'deposits',
            'totalAmount',
            'payment_methods'
        ));
    }
}
