<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NotificationMessage;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $template = config('site.template');
        $page_title = __('Dashboard');
        $user = auth()->user();

        // 1. Hero Stats
        $balance = $user->balance;
        $bot_capital = moduleEnabled('trading_bot_module') ? $user->tradingBotActivations()->where('status', 'active')->sum('amount') : 0;
        // Total Equity = Balance + Bot Capital
        $total_equity = $balance + $bot_capital;

        // 2. Activity (Monthly)
        $deposits_month = $user->deposits()->whereMonth('created_at', now()->month)->where('status', 'completed')->sum('amount');
        $deposits_pending = $user->deposits()->where('status', 'pending')->count();

        $withdrawals_month_amount = $user->withdrawals()->whereMonth('created_at', now()->month)->where('status', 'completed')->sum('amount');
        $withdrawals_pending = $user->withdrawals()->where('status', 'pending')->count();

        // 4. Section 2: Analytics & Insights
        // A. Transaction Graph (Dynamic Filtering)
        $filter = request()->get('days', '7');
        $startDate = now();
        $isAllTime = false;
        $labelFormat = 'M d';

        if ($filter == '7') {
            $startDate = now()->subDays(6)->startOfDay();
            $isMonthly = false;
        } elseif ($filter == '30') {
            $startDate = now()->subDays(29)->startOfDay();
            $isMonthly = false;
        } elseif ($filter == '90') {
            $startDate = now()->subDays(89)->startOfDay();
            $isMonthly = false;
        } elseif ($filter == '365') {
            $startDate = now()->subDays(364)->startOfDay();
            $isMonthly = true;
            $labelFormat = 'M Y';
        } else {
            // All Time
            $isAllTime = true;
            $firstTx = $user->transactions()->orderBy('created_at')->first();
            $startDate = $firstTx ? $firstTx->created_at->startOfDay() : now()->subYear()->startOfDay();
            $isMonthly = true;
            $labelFormat = 'M Y';
        }

        $query = $user->transactions()->where('status', 'completed');
        if (!$isAllTime) {
            $query->where('created_at', '>=', $startDate);
        }

        $txHistory = $query->get();
        $labels = [];
        $credits = [];
        $debits = [];

        if ($isMonthly) {
            $current = (clone $startDate)->startOfMonth();
            $end = now()->endOfMonth();
            while ($current <= $end) {
                $monthStr = $current->format('Y-m');
                $labels[] = $current->format($labelFormat);
                $monthGroup = $txHistory->filter(fn($t) => $t->created_at->format('Y-m') == $monthStr);
                $credits[] = (float) $monthGroup->where('type', 'credit')->sum('amount');
                $debits[] = (float) $monthGroup->where('type', 'debit')->sum('amount');
                $current->addMonth();
            }
        } else {
            $current = (clone $startDate)->startOfDay();
            $end = now()->endOfDay();
            while ($current <= $end) {
                $dayStr = $current->format('Y-m-d');
                $labels[] = $current->format($labelFormat);
                $dayGroup = $txHistory->filter(fn($t) => $t->created_at->format('Y-m-d') == $dayStr);
                $credits[] = (float) $dayGroup->where('type', 'credit')->sum('amount');
                $debits[] = (float) $dayGroup->where('type', 'debit')->sum('amount');
                $current->addDay();
            }
        }

        $chart_data = [
            'labels' => $labels,
            'credits' => $credits,
            'debits' => $debits,
        ];

        // If AJAX request for chart data only
        if (request()->ajax() && request()->has('days')) {
            return response()->json($chart_data);
        }

        // B. Money Distribution (Pie Chart)
        $money_distribution = [
            'Wallet' => (float) $balance,
        ];

        if (moduleEnabled('trading_bot_module')) {
            $money_distribution['Bots'] = (float) $bot_capital;
        }

        // C. Smart Insights
        $smart_insights = [];

        // Pending Movements
        if ($deposits_pending > 0 || $withdrawals_pending > 0) {
            $smart_insights[] = [
                'type' => 'trend_down',
                'title' => __('Pending Flow'),
                'text' => __('There are currently ') . ($deposits_pending + $withdrawals_pending) . __(' operations in your pipeline. Your liquidity will update upon approval.'),
                'index' => 1,
            ];
        }

        // Section 6: Money Movement Hub
        $deposits_all = $user->deposits()->with(['blockchain', 'blockchainToken'])->latest()->get();
        $withdrawals_all = $user->withdrawals()->with(['blockchain', 'blockchainToken'])->latest()->get();

        $movement_stats = [
            'deposits' => [
                'pending' => (float) $deposits_all->where('status', 'pending')->sum('amount'),
                'approved' => (float) $deposits_all->where('status', 'completed')->sum('amount'),
                'rejected' => (float) $deposits_all->where('status', 'failed')->sum('amount'),
                'avg_size' => (float) ($deposits_all->where('status', 'completed')->avg('amount') ?? 0),
                'most_used_method' => $deposits_all->count() > 0 ? ($deposits_all->groupBy('blockchain_id')->sortByDesc(fn($g) => $g->count())->first()?->first()?->gatewayName() ?? __('N/A')) : __('N/A'),
            ],
            'withdrawals' => [
                'pending' => (float) $withdrawals_all->where('status', 'pending')->sum('amount'),
                'paid' => (float) $withdrawals_all->where('status', 'completed')->sum('amount'),
                'rejected' => (float) $withdrawals_all->where('status', 'failed')->sum('amount'),
                'total_fees' => (float) $withdrawals_all->where('status', 'completed')->sum('fee_amount'),
            ],
            'net_cashflow' => (float) ($deposits_month - $withdrawals_month_amount),
            'total_fees_month' => (float) ($user->deposits()->whereMonth('created_at', now()->month)->where('status', 'completed')->sum('fee_amount') +
                $user->withdrawals()->whereMonth('created_at', now()->month)->where('status', 'completed')->sum('fee_amount')),
            'recent_deposits' => $deposits_all->take(5),
            'recent_withdrawals' => $withdrawals_all->take(5),
        ];

        $recent_transactions_hub = $user->transactions()->latest()->take(10)->get();
        $user_wallets = \App\Models\UserBlockchainWallet::where('user_id', $user->id)
            ->with(['blockchain', 'blockchain.tokens' => fn($q) => $q->where('status', 'enabled')])
            ->get();

        return view("templates.$template.blades.user.dashboard", compact(
            'page_title',
            'balance',
            'total_equity',
            'bot_capital',
            'deposits_month',
            'deposits_pending',
            'withdrawals_month_amount',
            'withdrawals_pending',
            'chart_data',
            'money_distribution',
            'smart_insights',
            'movement_stats',
            'recent_transactions_hub',
            'user_wallets'
        ));
    }

    public function notificationMarkAsRead(Request $request)
    {
        $notification = NotificationMessage::findOrFail($request->notification_id);
        $notification->update(['status' => 'read']);
        return response()->json(['success' => true]);
    }


    // onboarding
    public function onboarding(Request $request)
    {
        // validate for first
        $request->validate([
            'risk_profile' => 'required|in:conservative,balanced,growth',
        ]);
        $user = auth()->user();
        $user->onboarding()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'risk_profile' => $request->risk_profile,
            ]
        );
        $risk_profile = str_replace('_', ' ', $request->risk_profile);
        $title = "Onboarding Completed"; //this will be translated in the blade when its queried from database
        $body = "Your onboarding has been completed successfully. Your risk level is $risk_profile";
        recordNotificationMessage($user, $title, $body);

        return response()->json([
            'status' => 'success',
            'message' => __('Onboarding completed successfully'),
        ]);
    }
}