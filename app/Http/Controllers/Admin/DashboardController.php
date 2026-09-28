<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $page_title = 'Dashboard';
        $top_cards = [];
        $users_metrics = [
            'total' => User::count(),
            'banned' => User::where('status', 'banned')->count(),
            'active' => User::where('status', 'active')->count(),
            'email_verified' => User::whereNotNull('email_verified_at')->count(),
            'pending_email_verification' => User::whereNull('email_verified_at')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'kyc_verified' => User::whereHas('kyc', fn($q) => $q->where('status', 'approved'))->count(),
            'pending_kyc' => User::whereDoesntHave('kyc')->orWhereHas('kyc', fn($q) => $q->where('status', 'pending'))->count(),
        ];

        $top_cards['users'] = $users_metrics;
        $system_equity_metrics = [
            'total' => 0,
            'users_balance' => User::sum('balance'),
        ];

        // convert all to system currency,
        $rate = rateConverter(1, 'USD', getSetting('currency'), 'gen')['converted_amount'];

        $system_equity_metrics['total'] = $system_equity_metrics['users_balance'];

        $top_cards['system_equity'] = $system_equity_metrics;

        $chart_data = [
            'account_balance' => $system_equity_metrics['users_balance'],
        ];

        // ── Graph data ──────────────────────────────────────────────────────────
        // Pre-compute all four periods (7d, 30d, 1y, ytd) from a single 1-year
        // window per dataset so the JS can switch filters without any AJAX.
        // Each period entry: [ 'labels' => [...], 'datasets' => [ type => [...] ] ]

        $yearStart = now()->startOfYear();
        $oneYearAgo = now()->subYear()->startOfDay();
        $graphWindowStart = $oneYearAgo->lt($yearStart) ? $oneYearAgo : $yearStart;

        // Helper: slice a keyed ['YYYY-MM-DD' => value] map to the last N days
        $sliceDays = function (array $map, int $days): array {
            $result = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $key = now()->subDays($i)->format('Y-m-d');
                $result[$key] = $map[$key] ?? 0;
            }
            return $result;
        };

        // Helper: slice a keyed map from Jan 1 of this year to today
        $sliceYtd = function (array $map): array {
            $result = [];
            $start = now()->startOfYear();
            $days = (int) $start->diffInDays(now()) + 1;
            for ($i = 0; $i < $days; $i++) {
                $key = $start->copy()->addDays($i)->format('Y-m-d');
                $result[$key] = $map[$key] ?? 0;
            }
            return $result;
        };

        // Helper: build formatted period array for a given slice
        $period = function (array $map, string $p) use ($sliceDays, $sliceYtd): array {
            $slice = match ($p) {
                '7d' => $sliceDays($map, 7),
                '30d' => $sliceDays($map, 30),
                '1y' => $sliceDays($map, 365),
                'ytd' => $sliceYtd($map),
                default => $sliceDays($map, 7),
            };
            return [
                'labels' => array_keys($slice),
                'data' => array_values($slice),
            ];
        };

        $periods = ['7d', '30d', '1y', 'ytd'];

        // ── 1. Transaction history (credit / debit) ──────────────────────────
        $txnRaw = Transaction::selectRaw('DATE(created_at) as day, type, SUM(amount) as total')
            ->where('created_at', '>=', $graphWindowStart)
            ->groupBy('day', 'type')
            ->orderBy('day')
            ->get();

        $txnMaps = ['credit' => [], 'debit' => []];
        foreach ($txnRaw as $row) {
            $txnMaps[$row->type][$row->day] = (float) $row->total;
        }

        $graph_data['transactions'] = [];
        foreach ($periods as $p) {
            $graph_data['transactions'][$p] = [
                'credit' => $period($txnMaps['credit'], $p),
                'debit' => $period($txnMaps['debit'], $p),
            ];
        }

        // ── 2. Deposits ──────────────────────────────────────────────────────
        $depositStatuses = ['pending', 'completed', 'failed', 'partial_payment'];
        $depositRaw = Deposit::selectRaw('DATE(created_at) as day, status, SUM(total_amount) as total, COUNT(*) as count')
            ->where('created_at', '>=', $graphWindowStart)
            ->groupBy('day', 'status')
            ->orderBy('day')
            ->get();

        $depositMaps = array_fill_keys($depositStatuses, []);
        foreach ($depositRaw as $row) {
            if (array_key_exists($row->status, $depositMaps)) {
                $depositMaps[$row->status][$row->day] = (float) $row->total;
            }
        }

        $graph_data['deposits'] = [];
        foreach ($periods as $p) {
            $graph_data['deposits'][$p] = [];
            foreach ($depositStatuses as $status) {
                $graph_data['deposits'][$p][$status] = $period($depositMaps[$status], $p);
            }
        }

        // ── 3. Withdrawals ───────────────────────────────────────────────────
        $withdrawalStatuses = ['pending', 'completed', 'failed', 'partial_payment'];
        $withdrawalRaw = Withdrawal::selectRaw('DATE(created_at) as day, status, SUM(amount_payable) as total, COUNT(*) as count')
            ->where('created_at', '>=', $graphWindowStart)
            ->groupBy('day', 'status')
            ->orderBy('day')
            ->get();

        $withdrawalMaps = array_fill_keys($withdrawalStatuses, []);
        foreach ($withdrawalRaw as $row) {
            if (array_key_exists($row->status, $withdrawalMaps)) {
                $withdrawalMaps[$row->status][$row->day] = (float) $row->total;
            }
        }

        $graph_data['withdrawals'] = [];
        foreach ($periods as $p) {
            $graph_data['withdrawals'][$p] = [];
            foreach ($withdrawalStatuses as $status) {
                $graph_data['withdrawals'][$p][$status] = $period($withdrawalMaps[$status], $p);
            }
        }

        // ── Recent activity data (10 rows each) ──────────────────────────────
        $recent_data = [
            'deposits' => Deposit::with('user')
                ->latest()->limit(10)->get(),
            'withdrawals' => Withdrawal::with('user')
                ->latest()->limit(10)->get(),
            'transactions' => Transaction::with('user')
                ->latest()->limit(10)->get(),
        ];
        // ─────────────────────────────────────────────────────────────────────

        // Check master wallets status for warning alerts on active blockchains
        $master_wallet_warnings = [];
        $active_blockchains = \App\Models\Blockchain::where('status', 'enabled')->get();
        foreach ($active_blockchains as $bc) {
            $address = $bc->master_wallet_address;
            $privateKeyEncrypted = $bc->master_private_key;

            if (empty($address)) {
                $master_wallet_warnings[] = [
                    'blockchain' => $bc->name,
                    'type' => 'missing',
                    'message' => __(':blockchain Master Wallet has not been generated yet! User deposits cannot be swept.', ['blockchain' => $bc->name]),
                    'url' => $bc->code === 'solana' ? route('admin.solana-master-wallet.index') : route('admin.deposits.master-wallets')
                ];
            } else {
                $decryptError = false;
                if (empty($privateKeyEncrypted)) {
                    $decryptError = true;
                } else {
                    try {
                        \Illuminate\Support\Facades\Crypt::decryptString($privateKeyEncrypted);
                    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                        $decryptError = true;
                    }
                }

                if ($decryptError) {
                    $master_wallet_warnings[] = [
                        'blockchain' => $bc->name,
                        'type' => 'decrypt_failed',
                        'message' => __(':blockchain Master Wallet private key decryption failed! Transaction signatures cannot be generated.', ['blockchain' => $bc->name]),
                        'url' => $bc->code === 'solana' ? route('admin.solana-master-wallet.index') : route('admin.deposits.master-wallets')
                    ];
                }
            }
        }

        $master_wallets = \App\Models\Blockchain::with(['tokens' => function ($q) {
            $q->where('status', 'enabled')->orderByRaw('priority IS NULL, priority ASC')->orderBy('id', 'asc');
        }])
        ->where('status', 'enabled')
        ->orderByRaw('priority IS NULL, priority ASC')
        ->orderBy('id', 'asc')
        ->get();
        $template = config('site.template');

        return view('templates.' . $template . '.blades.admin.dashboard', compact(
            'page_title',
            'top_cards',
            'chart_data',
            'graph_data',
            'recent_data',
            'master_wallet_warnings',
            'master_wallets',
        ));
    }

    public function markNotificationSeen(Request $request)
    {
        $id = $request->input('id') ?? $request->input('notification_id');
        if ($id) {
            \App\Models\NotificationMessage::where('id', $id)->update(['admin_seen' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }
}
