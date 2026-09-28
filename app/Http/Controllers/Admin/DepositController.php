<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blockchain;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DepositController extends Controller
{
    /**
     * Display a listing of deposits.
     */
    public function index(Request $request)
    {
        $page_title = __('Deposits');

        // Base Query
        $query = Deposit::with(['user', 'blockchain', 'blockchainToken']);

        // Stats
        $stats = [
            'total_deposited' => Deposit::sum('amount'),
            'total_completed' => Deposit::completed()->sum('amount'),
            'pending_count' => Deposit::pending()->count(),
            'pending_amount' => Deposit::pending()->sum('amount'),
            'failed_count' => Deposit::failed()->count(),
            'failed_amount' => Deposit::failed()->sum('amount'),
        ];

        // Filters
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('search') && $request->search) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->whereHas('user', function ($uq) use ($term) {
                    $uq->where('username', 'like', "%$term%")
                        ->orWhere('email', 'like', "%$term%")
                        ->orWhere('first_name', 'like', "%$term%")
                        ->orWhere('last_name', 'like', "%$term%");
                })->orWhere('transaction_reference', 'like', "%$term%");
            });
        }

        // Export Handling
        if ($request->has('export')) {
            $exportType = $request->export;
            $exportDeposits = (clone $query)->latest()->get();
            $template = config('site.template', 'york');

            // Dynamic Column Selection
            $requestedCols = $request->get('columns');
            if (is_string($requestedCols)) {
                $requestedCols = array_map('trim', explode(',', $requestedCols));
            } else {
                $requestedCols = (array) ($requestedCols ?? ['username', 'payment_method_name', 'amount', 'total_amount', 'transaction_reference', 'status', 'created_at']);
            }

            // Header whitelist and mapping
            $columnMap = [
                'username' => 'User',
                'payment_method_name' => 'Gateway',
                'amount' => 'Amount',
                'fee_amount' => 'Fee',
                'total_amount' => 'Total Amount',
                'converted_amount' => 'Converted',
                'exchange_rate' => 'Exchange Rate',
                'transaction_reference' => 'Trx Ref',
                'status' => 'Status',
                'created_at' => 'Date',
            ];

            // Filter columns based on whitelist
            $selectedCols = [];
            foreach ($requestedCols as $col) {
                $col = trim($col);
                if (array_key_exists($col, $columnMap)) {
                    $selectedCols[$col] = $columnMap[$col];
                }
            }

            if (empty($selectedCols)) {
                $selectedCols = [
                    'username' => 'User',
                    'payment_method_name' => 'Gateway',
                    'amount' => 'Amount',
                    'total_amount' => 'Total Amount',
                    'transaction_reference' => 'Trx Ref',
                    'status' => 'Status',
                    'created_at' => 'Date',
                ];
            }

            if ($exportType == 'pdf') {
                $orientation = count($selectedCols) <= 8 ? 'portrait' : 'landscape';
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView("templates.$template.blades.admin.pdf.deposits", [
                    'deposits' => $exportDeposits,
                    'page_title' => $page_title,
                    'columns' => $selectedCols,
                    'orientation' => $orientation
                ]);
                return $pdf->download('deposits-report-' . now()->format('Y-m-d_His') . '.pdf');
            }

            if ($exportType == 'sql') {
                return response()->streamDownload(function () use ($exportDeposits) {
                    $file = fopen('php://output', 'w');
                    fwrite($file, "-- Deposits Table Dump\n");
                    fwrite($file, "-- Generated at: " . now() . "\n\n");

                    try {
                        $createTable = DB::select("SHOW CREATE TABLE deposits")[0]->{'Create Table'};
                        fwrite($file, "DROP TABLE IF EXISTS deposits;\n");
                        fwrite($file, $createTable . ";\n\n");
                    } catch (\Exception $e) {
                        fwrite($file, "-- Failed to generate CREATE TABLE statement: " . $e->getMessage() . "\n\n");
                    }

                    foreach ($exportDeposits as $deposit) {
                        $attributes = is_object($deposit) && method_exists($deposit, 'getAttributes') ? $deposit->getAttributes() : (array) $deposit;
                        $columns = array_keys($attributes);
                        $values = array_map(function ($value) {
                            if (is_null($value))
                                return 'NULL';
                            return "'" . addslashes((string) $value) . "'";
                        }, array_values($attributes));

                        $sql = "INSERT INTO deposits (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                        fwrite($file, $sql);
                    }
                    fclose($file);
                }, 'deposits-dump-' . now()->format('Y-m-d_His') . '.sql', [
                    'Content-Type' => 'application/sql; charset=UTF-8',
                ]);
            }

            if ($exportType == 'csv') {
                return response()->streamDownload(function () use ($exportDeposits, $selectedCols) {
                    $file = fopen('php://output', 'w');
                    // UTF-8 BOM
                    fputs($file, "\xEF\xBB\xBF");
                    fputcsv($file, array_values($selectedCols));

                    foreach ($exportDeposits as $deposit) {
                        $row = [];
                        foreach (array_keys($selectedCols) as $key) {
                            switch ($key) {
                                case 'username':
                                    $row[] = $deposit->user?->username ?? 'N/A';
                                    break;
                                case 'payment_method_name':
                                    $row[] = $deposit->gatewayName();
                                    break;
                                case 'status':
                                    $row[] = ucfirst($deposit->status);
                                    break;
                                case 'created_at':
                                    $row[] = $deposit->created_at ? $deposit->created_at->format('Y-m-d H:i:s') : '';
                                    break;
                                default:
                                    $row[] = $deposit->$key ?? '';
                                    break;
                            }
                        }
                        fputcsv($file, $row);
                    }
                    fclose($file);
                }, 'deposits-list-' . now()->format('Y-m-d_His') . '.csv', [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                ]);
            }
        }

        $deposits = $query->latest()->paginate(getSetting('pagination', 15));

        // ── Visualizations Data ─────────────────────────────────────────────
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
                '60d' => $sliceDays($map, 60),
                '90d' => $sliceDays($map, 90),
                '1y' => $sliceDays($map, 365),
                'ytd' => $sliceYtd($map),
                default => $sliceDays($map, 7),
            };
            return [
                'labels' => array_keys($slice),
                'data' => array_values($slice),
            ];
        };

        $periods = ['7d', '30d', '60d', '90d', '1y', 'ytd'];
        $depositStatuses = ['pending', 'partial_payment', 'completed', 'failed'];

        $depositRaw = Deposit::selectRaw('DATE(created_at) as day, status, SUM(amount) as total')
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

        $graph_data = [];
        foreach ($periods as $p) {
            $graph_data[$p] = [];
            foreach ($depositStatuses as $status) {
                $graph_data[$p][$status] = $period($depositMaps[$status], $p);
            }
        }

        // Status Distribution Data for Pie/Doughnut Chart
        $status_chart_data = Deposit::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => ucfirst($item->status),
                    'count' => $item->count,
                    'amount' => (float) $item->total_amount
                ];
            });

        $template = config('site.template');

        return view('templates.' . $template . '.blades.admin.deposits.index', compact(
            'page_title',
            'deposits',
            'stats',
            'graph_data',
            'status_chart_data'
        ));
    }

    public function viewDeposit($id)
    {
        $page_title = __('Deposit Details');
        $deposit = Deposit::with(['user', 'blockchain', 'blockchainToken'])->findOrFail($id);

        $template = config('site.template');

        return view('templates.' . $template . '.blades.admin.deposits.view', compact('page_title', 'deposit'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,partial_payment,completed,failed'
        ]);



        $deposit = Deposit::findOrFail($id);

        // guard rails
        $allowed = ['pending', 'partial_payment', 'failed'];
        if (!in_array($deposit->status, $allowed)) {
            return response()->json(['status' => 'error', 'message' => __('Deposit Already processed')], 400);
        }

        // current status cannot be same as old status
        if ($deposit->status === $request->status) {
            return response()->json(['status' => 'error', 'message' => __('Deposit status cannot be same as old status')], 400);
        }

        $status = $request->status;
        if ($status === 'completed') {
            $user = $deposit->user;

            // Record deposit update
            $deposit->status = 'completed';
            $deposit->save();

            // Credit user balance
            $user->refresh();
            $new_balance = $user->balance + $deposit->amount;
            $user->balance = $new_balance;
            $user->save();

            // Record transaction
            $reference = \Str::random(12);
            $description = "Deposit via " . $deposit->gatewayName();
            recordTransaction($user, $deposit->amount, getSetting('currency'), $deposit->converted_amount, $deposit->currency, $deposit->exchange_rate, 'credit', $deposit->status, $reference, $description, $new_balance);

            // send deposit email
            $custom_subject = "Deposit Completed";
            $custom_message = "Deposit has been completed find the details below.";
            sendDepositEmail($custom_subject, $custom_message, $deposit);

            // Record notification
            $title = "Deposit Completed";
            $body = "Deposit via Nowpayments.io has been completed";
            recordNotificationMessage($user, $title, $body);

            return response()->json(['status' => 'success', 'message' => __('Payment processed successfully')], 200);
        }



        $deposit->status = $request->status;
        $deposit->save();

        // send deposit email
        $custom_subject = "Deposit " . ucfirst($request->status);
        $custom_message = "Deposit has been " . $request->status . " find the details below.";
        sendDepositEmail($custom_subject, $custom_message, $deposit);

        // Record notification
        $title = "Deposit " . ucfirst($request->status);
        $body = "Deposit via Nowpayments.io has been " . $request->status;
        recordNotificationMessage($deposit->user, $title, $body);

        return response()->json([
            'success' => true,
            'message' => __('Deposit status updated successfully.')
        ]);
    }

    public function delete($id)
    {
        $deposit = Deposit::findOrFail($id);
        $deposit->delete();

        return response()->json([
            'success' => true,
            'message' => __('Deposit deleted successfully.')
        ]);
    }

    public function userWallets(Request $request)
    {
        $page_title = __('User Wallets');

        $query = \App\Models\UserBlockchainWallet::with(['user', 'blockchain']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        $wallets = $query->latest()->paginate(getSetting('pagination', 10));

        $template = config('site.template');

        return view('templates.' . $template . '.blades.admin.deposits.wallets', compact('page_title', 'wallets'));
    }

    public function revealUserWalletKey(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $admin = auth('admin')->user();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'status' => 'error',
                'message' => __('Invalid admin password.'),
            ], 422);
        }

        $wallet = \App\Models\UserBlockchainWallet::with(['user', 'blockchain'])->findOrFail($id);

        if (!$wallet->private_key) {
            return response()->json([
                'status' => 'error',
                'message' => __('Wallet private key not found.'),
            ], 404);
        }

        try {
            $privateKey = \Illuminate\Support\Facades\Crypt::decryptString($wallet->private_key);

            return response()->json([
                'status' => 'success',
                'private_key' => $privateKey,
                'address' => $wallet->address,
                'blockchain' => $wallet->blockchain?->name ?? 'Unknown',
                'user' => $wallet->user?->username ?? ($wallet->user?->name ?? 'User'),
            ]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            \Illuminate\Support\Facades\Log::error("Failed to decrypt private key for user wallet {$wallet->id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => __('Failed to decrypt the private key. Make sure the APP_KEY has not changed.'),
            ], 500);
        }
    }

    public function resetMasterWallets(Request $request)
    {
        $password = trim((string) $request->input('password', ''));

        if ($password === '') {
            return response()->json([
                'status' => 'error',
                'message' => __('Admin password is required.'),
            ], 422);
        }

        $admin = auth('admin')->user();

        if (!$admin || !Hash::check($password, $admin->password)) {
            return response()->json([
                'status' => 'error',
                'message' => __('Invalid admin password.'),
            ], 422);
        }

        try {
            $updatedCount = Blockchain::query()
                ->where(function ($query) {
                    $query->whereNotNull('master_wallet_address')
                        ->orWhereNotNull('master_private_key');
                })
                ->update([
                    'master_wallet_address' => null,
                    'master_private_key' => null,
                ]);

            return response()->json([
                'status' => 'success',
                'message' => $updatedCount > 0
                    ? __('All master wallets have been reset successfully.')
                    : __('No master wallets were configured to reset.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to reset master wallets: ') . $e->getMessage(),
            ], 500);
        }
    }

    public function masterWallets(Request $request)
    {
        $page_title = __('Master Wallets');
        $blockchains = \App\Models\Blockchain::latest()->get();
        $template = config('site.template');

        return view('templates.' . $template . '.blades.admin.deposits.master_wallets', compact('page_title', 'blockchains'));
    }
}
