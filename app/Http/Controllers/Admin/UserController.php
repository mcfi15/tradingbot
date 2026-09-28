<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $page_title = "Manage Users";
        $template = config('site.template');

        // Base Query for Stats
        $statsQuery = \App\Models\User::query();

        // 1) User Statistics
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->active()->count(),
            'banned' => (clone $statsQuery)->where('status', 'banned')->count(),
            'email_unverified' => (clone $statsQuery)->emailUnverified()->count(),
            'kyc_pending' => (clone $statsQuery)->whereHas('kyc', function ($q) {
                $q->where('status', 'pending');
            })->count(),
        ];

        // 2) User Table (Filterable)
        $query = \App\Models\User::with([
            'kyc' => function ($q) {
                $q->latest();
            },
            'referrer'
        ]);

        // Filters
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('kyc_status') && $request->kyc_status != 'all') {
            $query->whereHas('kyc', function ($q) use ($request) {
                $q->where('status', $request->kyc_status);
            });
        }

        if ($request->has('email_verified') && $request->email_verified != 'all') {
            if ($request->email_verified == '1') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        if ($request->has('search') && $request->search) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('email', 'like', "%$term%")
                    ->orWhere('first_name', 'like', "%$term%")
                    ->orWhere('last_name', 'like', "%$term%")
                    ->orWhere('username', 'like', "%$term%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        if ($sort == 'user') {
            $query->orderBy('first_name', $direction)->orderBy('last_name', $direction);
        } elseif ($sort == 'kyc') {
            $query->addSelect([
                'kyc_status_sort' => \App\Models\Kyc::select('status')
                    ->whereColumn('user_id', 'users.id')
                    ->latest()
                    ->limit(1)
            ])->orderBy('kyc_status_sort', $direction);
        } elseif (in_array($sort, ['balance', 'status', 'created_at'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Export Handling
        if ($request->has('export')) {
            $exportType = $request->export;
            $exportUsers = $query->get();

            // Dynamic Column Selection
            $requestedCols = $request->get('columns');
            if (is_string($requestedCols)) {
                $requestedCols = array_map('trim', explode(',', $requestedCols));
            } else {
                $requestedCols = (array) ($requestedCols ?? ['id', 'username', 'fullname', 'email', 'status', 'balance', 'created_at']);
            }

            // Header whitelist and mapping
            $columnMap = [
                'id' => 'ID',
                'username' => 'Username',
                'fullname' => 'Full Name',
                'email' => 'Email',
                'balance' => 'Balance',
                'status' => 'Status',
                'kyc_status' => 'KYC Status',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'referral_code' => 'Referral Code',
                'referred_by' => 'Referred By',
                'created_at' => 'Joined At',
            ];

            // Filter columns based on whitelist
            $selectedCols = [];
            foreach ($requestedCols as $col) {
                $col = trim($col);
                if (array_key_exists($col, $columnMap)) {
                    $selectedCols[$col] = $columnMap[$col];
                }
            }

            if ($exportType == 'pdf') {
                $orientation = count($selectedCols) <= 8 ? 'portrait' : 'landscape';
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView("templates.$template.blades.admin.pdf.users", [
                    'users' => $exportUsers,
                    'page_title' => $page_title,
                    'columns' => $selectedCols,
                    'orientation' => $orientation
                ]);
                return $pdf->download('users-report-' . now()->format('Y-m-d_His') . '.pdf');
            }

            if ($exportType == 'sql') {
                return response()->streamDownload(function () use ($exportUsers) {
                    $file = fopen('php://output', 'w');
                    fwrite($file, "-- Users Table Dump\n");
                    fwrite($file, "-- Generated at: " . now() . "\n\n");

                    // Add CREATE TABLE statement
                    try {
                        $createTable = \Illuminate\Support\Facades\DB::select("SHOW CREATE TABLE users")[0]->{'Create Table'};
                        fwrite($file, "DROP TABLE IF EXISTS users;\n");
                        fwrite($file, $createTable . ";\n\n");
                    } catch (\Exception $e) {
                        fwrite($file, "-- Failed to generate CREATE TABLE statement: " . $e->getMessage() . "\n\n");
                    }

                    foreach ($exportUsers as $user) {
                        $attributes = is_object($user) && method_exists($user, 'getAttributes') ? $user->getAttributes() : (array) $user;
                        $columns = array_keys($attributes);
                        $values = array_map(function ($value) {
                            if (is_null($value))
                                return 'NULL';
                            return "'" . addslashes((string) $value) . "'";
                        }, array_values($attributes));

                        $sql = "INSERT INTO users (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                        fwrite($file, $sql);
                    }
                    fclose($file);
                }, 'users-dump-' . now()->format('Y-m-d_His') . '.sql', [
                    'Content-Type' => 'application/sql; charset=UTF-8',
                ]);
            }

            if ($exportType == 'csv') {
                return response()->streamDownload(function () use ($exportUsers, $selectedCols) {
                    $file = fopen('php://output', 'w');
                    // UTF-8 BOM
                    fputs($file, "\xEF\xBB\xBF");

                    // Header row
                    fputcsv($file, array_values($selectedCols));

                    foreach ($exportUsers as $user) {
                        $row = [];
                        foreach (array_keys($selectedCols) as $key) {
                            switch ($key) {
                                case 'fullname':
                                    $row[] = $user->fullname;
                                    break;
                                case 'status':
                                    $row[] = ucfirst($user->status);
                                    break;
                                case 'created_at':
                                    $row[] = $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '';
                                    break;
                                case 'kyc_status':
                                    $kyc = $user->kyc->first();
                                    $row[] = $kyc ? ucfirst($kyc->status) : 'Not Submitted';
                                    break;
                                case 'referred_by':
                                    $row[] = $user->referrer ? $user->referrer->username : 'None';
                                    break;
                                case 'referral_code':
                                    $row[] = $user->referral_code ?? 'N/A';
                                    break;
                                default:
                                    $row[] = $user->$key ?? '';
                                    break;
                            }
                        }
                        fputcsv($file, $row);
                    }
                    fclose($file);
                }, 'users-list-' . now()->format('Y-m-d_His') . '.csv', [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                ]);
            }
        }

        $users = $query->paginate(getSetting('pagination', 15));

        $template = config('site.template', 'york');

        if ($request->ajax()) {
            return view('templates.' . $template . '.blades.admin.users.index', compact('page_title', 'users', 'stats'));
        }

        return view('templates.' . $template . '.blades.admin.users.index', compact('page_title', 'users', 'stats'));
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'action' => 'required|in:delete,ban,unban,verify_email'
        ]);

        $ids = $request->ids;
        $action = $request->action;

        try {
            switch ($action) {
                case 'delete':
                    \App\Models\User::whereIn('id', $ids)->delete();
                    $message = __(':count users deleted successfully.', ['count' => count($ids)]);
                    break;
                case 'ban':
                    $users = \App\Models\User::whereIn('id', $ids)->get();
                    foreach ($users as $user) {
                        $user->status = 'banned';
                        $user->save();
                        sendAccountBanEmail($user, 'ban');
                    }
                    $message = __(':count users banned successfully.', ['count' => count($ids)]);
                    break;
                case 'unban':
                    $users = \App\Models\User::whereIn('id', $ids)->get();
                    foreach ($users as $user) {
                        $user->status = 'active';
                        $user->save();
                        sendAccountBanEmail($user, 'unban');
                    }
                    $message = __(':count users unbanned successfully.', ['count' => count($ids)]);
                    break;
                case 'verify_email':
                    \App\Models\User::whereIn('id', $ids)->update(['email_verified_at' => now()]);
                    $message = __(':count users email verified successfully.', ['count' => count($ids)]);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while processing bulk action.')
            ], 500);
        }
    }

    public function detail($id)
    {
        $user = \App\Models\User::with(['referrer', 'kyc'])->findOrFail($id);
        $page_title = 'User Detail - ' . ($user->fullname ?? $user->username ?? 'User');

        $conversion = rateConverter(1, 'usd', getSetting('currency'), 'gen');
        $exchange_rate = $conversion['exchange_rate'];

        //we need to convert all from usd to website currency

        // 1. Hero Stats (Calculated for this specific user)
        $balance = $user->balance;
        $total_equity = $balance;

        // 2. Activity (Monthly)
        $deposits_month = $user->deposits()->whereMonth('created_at', now()->month)->where('status', 'approved')->sum('amount');
        $deposits_pending = $user->deposits()->where('status', 'pending')->count();
        $withdrawals_month_amount = $user->withdrawals()->whereMonth('created_at', now()->month)->where('status', 'approved')->sum('amount');
        $withdrawals_pending = $user->withdrawals()->where('status', 'pending')->count();

        // Last login from the sessions table (database driver)
        $lastSessionActivity = \DB::table('sessions')
            ->where('user_id', $user->id)
            ->max('last_activity');
        $last_login = $lastSessionActivity
            ? \Carbon\Carbon::createFromTimestamp($lastSessionActivity)
            : null;

        $template = config('site.template');

        return view('templates.' . $template . '.blades.admin.users.detail', compact(
            'page_title',
            'user',
            'balance',
            'total_equity',
            'deposits_month',
            'deposits_pending',
            'withdrawals_month_amount',
            'withdrawals_pending',
            'last_login'
        ));
    }
    public function creditDebit(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|gt:0',
            'description' => 'nullable|string|max:255'
        ]);

        $user = \App\Models\User::findOrFail($id);
        $amount = $request->amount;
        $type = $request->type;
        $description = $request->description;

        if (!$description) {
            $description = $type == 'credit' ? 'Manual Credit' : 'Manual Debit';
        }

        if ($type == 'debit' && $user->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => __('Insufficient balance for debit operation.')
            ], 422);
        }

        try {
            \DB::beginTransaction();

            if ($type == 'credit') {
                $user->balance += $amount;
            } else {
                $user->balance -= $amount;
            }

            $user->save();



            \DB::commit();
            $user->refresh();
            $currency = getSetting('currency');
            $ref = \Str::orderedUuid();

            recordTransaction($user, $amount, $currency, $amount, $currency, 1, $type, 'completed', $ref, $description, $user->balance);
            $title = __($description, [], $user->lang);
            $body = __('Your balance has been adjusted by admin. Amount: :amount :currency', ['amount' => $amount, 'currency' => $currency], $user->lang);
            recordNotificationMessage($user, $title, $body);

            return response()->json([
                'success' => true,
                'message' => __('User balance adjusted successfully.')
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while adjusting balance.')
            ], 500);
        }
    }
    public function loginAs($id)
    {
        $user = \App\Models\User::findOrFail($id);

        try {
            \Illuminate\Support\Facades\Auth::guard('web')->login($user);
            session()->put('admin_impersonation', true);

            return response()->json([
                'success' => true,
                'message' => __('Redirecting to user dashboard...'),
                'redirect_url' => route('user.dashboard')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while trying to login as user.')
            ], 500);
        }
    }

    public function sendEmail(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        $user = \App\Models\User::findOrFail($id);

        try {
            sendRichTextEmail($request->subject, $request->message, $user);

            return response()->json([
                'success' => true,
                'message' => __('Email sent successfully.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while trying to send email.')
            ], 500);
        }
    }

    public function bulkEmail(Request $request)
    {
        $page_title = __('Bulk Email');
        $ids = $request->ids;
        $selected_ids = [];

        if ($ids) {
            if (is_string($ids)) {
                $selected_ids = explode(',', $ids);
            } elseif (is_array($ids)) {
                $selected_ids = $ids;
            }
        }

        $users = \App\Models\User::orderBy('username', 'asc')->get();

        // Prioritize selected users at the top
        if (!empty($selected_ids)) {
            $users = $users->sortBy(function ($user) use ($selected_ids) {
                return !in_array($user->id, $selected_ids);
            })->values();
        }

        $template = config('site.template');

        return view('templates.' . $template . '.blades.admin.users.bulk-email', compact('page_title', 'users', 'selected_ids'));
    }

    public function sendBulkEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'audience' => 'required|in:all,active,selected',
            'ids' => 'required_if:audience,selected|array',
            'ids.*' => 'exists:users,id'
        ]);

        $subject = $request->subject;
        $message = $request->message;
        $audience = $request->audience;

        try {
            if ($audience == 'all') {
                $users = \App\Models\User::all();
            } elseif ($audience == 'active') {
                $users = \App\Models\User::where('status', 'active')->get();
            } else {
                $users = \App\Models\User::whereIn('id', $request->ids)->get();
            }

            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => __('No users found to send email.')
                ], 422);
            }

            foreach ($users as $user) {
                sendRichTextEmail($subject, $message, $user);
            }

            return response()->json([
                'success' => true,
                'message' => __(':count emails sent successfully.', ['count' => count($users)])
            ]);
        } catch (\Exception $e) {
            \Log::error('Bulk Email Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while sending bulk emails.')
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;

        if ($request->has('email_verified')) {
            $user->email_verified_at = $user->email_verified_at ?? now();
        } else {
            $user->email_verified_at = null;
        }

        if ($request->has('two_factor')) {
            $user->ts = 1;
        } else {
            $user->ts = 0;
        }

        $user->save();

        if ($request->country || $request->city || $request->address) {
            $kyc = $user->kyc()->first();
            if ($kyc) {
                $kyc->country = $request->country ?? $kyc->country;
                $kyc->city = $request->city ?? $kyc->city;
                $kyc->address_line_1 = $request->address ?? $kyc->address_line_1;
                $kyc->save();
            }
        }

        return back()->with('success', __('User profile updated successfully.'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,banned'
        ]);

        $user = \App\Models\User::findOrFail($id);
        $user->status = $request->status;
        $user->save();

        if (function_exists('sendAccountBanEmail')) {
            sendAccountBanEmail($user, $request->status === 'banned' ? 'ban' : 'unban');
        }

        return response()->json([
            'success' => true,
            'message' => __('User status updated successfully.')
        ]);
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = \App\Models\User::findOrFail($id);
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return back()->with('success', __('User password updated successfully.'));
    }

    public function destroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => __('User deleted successfully.')
        ]);
    }
}
