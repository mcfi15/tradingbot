<?php

namespace App\Http\Controllers\User\Trading;

use App\Http\Controllers\Controller;
use App\Jobs\SyncExchangeBalanceJob;
use App\Services\Exchange\ExchangeBalanceService;
use App\Services\Exchange\ExchangeManager;
use App\Services\Exchange\Exceptions\ExchangeException;
use App\Services\TradeLogger;
use App\Models\ExchangeConnection;
use App\Models\TradeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ExchangeController extends Controller
{
    public function __construct(
        protected ExchangeManager $exchanges,
        protected ExchangeBalanceService $balances,
        protected TradeLogger $logger
    ) {
    }

    /**
     * Exchange connection management screen.
     */
    public function index()
    {
        $page_title = __('Exchange Connections');
        $template = config('site.template');

        $user = Auth::user();

        $connections = ExchangeConnection::where('user_id', $user->id)
            ->orderBy('exchange')
            ->orderBy('market_type')
            ->get()
            ->map(fn (ExchangeConnection $connection) => $connection->toDisplayArray())
            ->values();

        $portfolio = $this->balances->portfolioFor((int) $user->id);

        $recent_logs = TradeLog::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view("templates.{$template}.blades.user.trading.exchange.index", compact(
            'page_title',
            'connections',
            'portfolio',
            'recent_logs'
        ));
    }

    /**
     * Persist a new connection. The secret is sealed before it is written.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'exchange' => ['required', 'string', Rule::in($this->exchanges->supported())],
            'market_type' => ['required', 'string', Rule::in(['spot', 'futures'])],
            'api_key' => ['required', 'string', 'min:8', 'max:255'],
            'api_secret' => ['required', 'string', 'min:8', 'max:255'],
            'label' => ['nullable', 'string', 'max:191'],
        ]);

        $duplicate = ExchangeConnection::where('user_id', $user->id)
            ->where('exchange', $validated['exchange'])
            ->where('market_type', $validated['market_type'])
            ->exists();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => __('You already have a :exchange :market connection. Edit it instead.', [
                    'exchange' => strtoupper($validated['exchange']),
                    'market' => $validated['market_type'],
                ]),
            ], 422);
        }

        $connection = new ExchangeConnection([
            'user_id' => $user->id,
            'exchange' => $validated['exchange'],
            'market_type' => $validated['market_type'],
            'label' => $validated['label'] ?? null,
            'is_active' => true,
        ]);

        $connection->storeCredentials($validated['api_key'], $validated['api_secret']);
        $connection->save();

        $this->logger->log(
            (int) $user->id,
            sprintf('Connected %s (%s).', strtoupper($connection->exchange), $connection->market_type),
            'info',
            ['connection_id' => $connection->id],
            'connection_created',
            null,
            null,
            $connection->exchange,
            null,
            $connection->market_type
        );

        // Warm the balance asynchronously so the page is not blocked on an
        // exchange round trip.
        SyncExchangeBalanceJob::dispatch($connection->id);

        return response()->json([
            'success' => true,
            'message' => __('Connection saved. Your balances are syncing now.'),
            'data' => $connection->toDisplayArray(),
        ]);
    }

    /**
     * Replace the credentials on an existing connection.
     */
    public function update(Request $request, int $id)
    {
        $connection = $this->ownedConnection($id);

        if (! $connection) {
            return response()->json([
                'success' => false,
                'message' => __('Connection not found.'),
            ], 404);
        }

        $validated = $request->validate([
            'api_key' => ['required', 'string', 'min:8', 'max:255'],
            'api_secret' => ['required', 'string', 'min:8', 'max:255'],
            'label' => ['nullable', 'string', 'max:191'],
        ]);

        $connection->storeCredentials($validated['api_key'], $validated['api_secret']);

        if (array_key_exists('label', $validated)) {
            $connection->label = $validated['label'];
        }

        $connection->save();

        // A new key means the previous balance is meaningless and the old error
        // no longer applies.
        $this->balances->forget($connection);

        SyncExchangeBalanceJob::dispatch($connection->id);

        return response()->json([
            'success' => true,
            'message' => __('Credentials updated.'),
            'data' => $connection->fresh()->toDisplayArray(),
        ]);
    }

    public function destroy(int $id)
    {
        $connection = $this->ownedConnection($id);

        if (! $connection) {
            return response()->json([
                'success' => false,
                'message' => __('Connection not found.'),
            ], 404);
        }

        // Refuse while orders are still open: deleting the connection would
        // orphan live positions that only this credential can close.
        $openOrders = $connection->orders()->open()->count();

        if ($openOrders > 0) {
            return response()->json([
                'success' => false,
                'message' => __('You have :count open trades on this connection. Close them before disconnecting.', [
                    'count' => $openOrders,
                ]),
            ], 422);
        }

        $this->balances->forget($connection);
        $connection->delete();

        return response()->json([
            'success' => true,
            'message' => __('Connection removed.'),
        ]);
    }

    /**
     * Verify the stored credentials by making one authenticated call.
     *
     * This is the fastest way for a user to find out they pasted a trading-only
     * key where a read key was needed, which is the most common setup mistake.
     */
    public function test(int $id)
    {
        $connection = $this->ownedConnection($id);

        if (! $connection) {
            return response()->json([
                'success' => false,
                'message' => __('Connection not found.'),
            ], 404);
        }

        try {
            $balances = $connection->adapter()->fetchBalances($connection->market_type);

            $this->balances->forget($connection);
            $connection->forceFill([
                'last_total_balance' => $balances['total'],
                'last_synced_at' => $balances['fetched_at'],
                'last_error' => null,
            ])->saveQuietly();

            return response()->json([
                'success' => true,
                'message' => __('Connection verified.'),
                'data' => [
                    'total' => $balances['total'],
                    'quote_asset' => $balances['quote_asset'],
                    'assets' => count($balances['assets']),
                ],
            ]);
        } catch (ExchangeException $e) {
            $connection->forceFill(['last_error' => mb_substr($e->getMessage(), 0, 2000)])->saveQuietly();

            Log::warning('exchange_connection.test_failed', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);

            // Keep the exchange's own wording and append the guidance rather
            // than replacing one with the other. Binance's "Invalid API-key,
            // IP, or permissions" names an IP restriction, which is a real and
            // common cause that generic advice about keys and permissions
            // would send the user looking in entirely the wrong place.
            $message = $e->isCredentialProblem()
                ? __(':reason Check the key, the secret, and that read permission is enabled.', [
                    'reason' => $e->getMessage(),
                ])
                : $e->getMessage();

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }
    }

    /**
     * Queue a balance refresh.
     */
    public function sync(int $id)
    {
        $connection = $this->ownedConnection($id);

        if (! $connection) {
            return response()->json([
                'success' => false,
                'message' => __('Connection not found.'),
            ], 404);
        }

        SyncExchangeBalanceJob::dispatch($connection->id);

        return response()->json([
            'success' => true,
            'message' => __('Balance sync queued.'),
        ]);
    }

    /**
     * Cached portfolio for the dashboard widget. Never triggers a live call
     * beyond the single cache-miss fetch.
     */
    public function portfolio()
    {
        $portfolio = $this->balances->portfolioFor((int) Auth::id());

        if (request()->ajax()) {
            return response()->json(['success' => true, 'data' => $portfolio]);
        }

        $page_title = __('Wallet');
        $template = config('site.template');

        return view("templates.{$template}.blades.user.trading.exchange.portfolio", compact(
            'page_title',
            'portfolio'
        ));
    }

    /**
     * Fetch a connection owned by the authenticated user, or null.
     *
     * Scoping the lookup by user_id is what prevents an IDOR: a connection id
     * belonging to someone else simply does not match.
     */
    protected function ownedConnection(int $id): ?ExchangeConnection
    {
        return ExchangeConnection::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
    }
}
