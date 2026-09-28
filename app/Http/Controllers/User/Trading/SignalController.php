<?php

namespace App\Http\Controllers\User\Trading;

use App\Http\Controllers\Controller;
use App\Services\SignalDispatchService;
use App\Models\ExchangeConnection;
use App\Models\SignalFollow;
use App\Models\TradeOrder;
use App\Models\TradingSignal;
use App\Models\UserTradingPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SignalController extends Controller
{
    public function __construct(protected SignalDispatchService $signals)
    {
    }

    /**
     * The live signal feed.
     */
    public function index(Request $request)
    {
        $page_title = __('Trading Signals');
        $template = config('site.template');

        $marketType = $request->query('market_type');
        $direction = $request->query('direction');
        $pair = $request->query('pair');

        $perPage = (int) config('site.pagination', 15);

        $query = TradingSignal::query()->active()->latest('signal_time');

        $query->forMarket(in_array($marketType, ['spot', 'futures'], true) ? $marketType : null);

        if (in_array($direction, ['buy', 'sell', 'long', 'short'], true)) {
            $query->where('direction', $direction);
        }

        if (!empty($pair)) {
            $query->where('pair', strtoupper(str_replace(['/', '-', '_'], '', $pair)));
        }

        $signals = $query->paginate($perPage);

        // The cursor the client starts polling from. Taking the newest id up
        // front means a page load does not replay the feed history.
        $cursor = $this->signals->currentCursor();

        // The feed header needs both the auto-trading switch and a live count of
        // what is already open against the max-concurrent-trades cap.
        $preferences = UserTradingPreference::forUser((int) Auth::id());

        $openTrades = TradeOrder::where('user_id', Auth::id())->open()->count();

        // WebSocket delivery is opt-in at the host level. When Reverb is absent
        // the view still renders and the client falls back to polling, so a
        // missing broadcaster degrades the feed rather than breaking it.
        $broadcastEnabled = (bool) config('broadcasting.default')
            && (string) config('broadcasting.default') !== 'null'
            && (string) config('broadcasting.default') !== 'log';

        $broadcastKey = (string) config('broadcasting.connections.' . config('broadcasting.default') . '.key');

        $follows = $this->followedMap((int) Auth::id());

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'signals' => $signals->getCollection()->map(fn ($signal) => $this->cardPayload($signal))->values(),
                    'cursor' => $cursor,
                    'follows' => $follows,
                    'current_page' => $signals->currentPage(),
                    'last_page' => $signals->lastPage(),
                ],
            ]);
        }

        return view("templates.{$template}.blades.user.trading.signals.index", compact(
            'page_title',
            'signals',
            'cursor',
            'follows',
            'marketType',
            'direction',
            'pair',
            'preferences',
            'openTrades',
            'broadcastEnabled',
            'broadcastKey'
        ));
    }

    /**
     * Polling fallback for clients that cannot open a WebSocket.
     *
     * The WebSocket path is primary; this exists so the feed still updates on a
     * host where Reverb is not running, and so a reconnecting client can catch
     * up on frames it missed without a full reload.
     */
    public function poll(Request $request)
    {
        $validated = $request->validate([
            'cursor' => ['nullable', 'integer', 'min:0'],
            'market_type' => ['nullable', Rule::in(['spot', 'futures'])],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $user = Auth::user();

        $result = $this->signals->since(
            cursor: (int) ($validated['cursor'] ?? 0),
            limit: (int) ($validated['limit'] ?? 20),
            marketType: $validated['market_type'] ?? null
        );

        // Order states the client needs to re-render open positions after a
        // reconnect. Scoped to recent activity so the payload stays small.
        $recentOrders = TradeOrder::where('user_id', $user->id)
            ->where('updated_at', '>=', now()->subMinutes(30))
            ->whereIn('status', ['pending', 'open', 'filled', 'closed', 'rejected', 'cancelled'])
            ->latest('id')
            ->take(50)
            ->get(['id', 'status', 'pair', 'direction', 'filled_price', 'quantity', 'realised_pnl']);

        return response()->json([
            'success' => true,
            'data' => [
                'cursor' => $result['cursor'],
                'signals' => $result['signals'],
                'follows' => $this->followedMap((int) $user->id),
                'active_order_count' => TradeOrder::where('user_id', $user->id)->open()->count(),
                'orders' => $recentOrders->map(fn (TradeOrder $order) => $order->toDisplayArray())->values(),
            ],
        ]);
    }

    /**
     * Auto-follow or un-follow a signal.
     *
     * This is the "Auto-Follow Signal" toggle on the signal card. Turning it on
     * does two things: it records the intent, and — because the signal already
     * exists — it dispatches the automated execution immediately rather than
     * waiting for the next broadcast.
     */
    public function toggleFollow(Request $request, int $id)
    {
        $signal = TradingSignal::find($id);

        if (! $signal) {
            return response()->json(['success' => false, 'message' => __('Signal not found.')], 404);
        }

        $user = Auth::user();

        $existing = SignalFollow::where('user_id', $user->id)
            ->where('trading_signal_id', $signal->id)
            ->first();

        $isFollowing = $existing && $existing->mode === 'auto';

        if ($isFollowing) {
            $this->signals->unfollow($user, $signal);

            return response()->json([
                'success' => true,
                'following' => false,
                'message' => __('You stopped following this signal.'),
            ]);
        }

        $preferences = UserTradingPreference::forUser((int) $user->id);

        if (! $preferences->auto_trading_mode) {
            return response()->json([
                'success' => false,
                'message' => __('Turn on Auto-Trading Mode in your risk settings before following signals automatically.'),
                'requires_auto_mode' => true,
            ], 422);
        }

        if (! $this->hasConnectionFor($user, $signal->market_type)) {
            return response()->json([
                'success' => false,
                'message' => __('Add an active :market exchange connection before following signals automatically.', [
                    'market' => $signal->market_type,
                ]),
                'requires_connection' => true,
            ], 422);
        }

        $follow = $this->signals->follow($user, $signal, 'auto');

        return response()->json([
            'success' => true,
            'following' => true,
            'message' => $follow->status === 'executed' || $follow->status === 'queued'
                ? __('The automated engine is placing this trade for you.')
                : __('You are now following this signal.'),
            'data' => [
                'id' => $follow->id,
                'status' => $follow->status,
                'error_message' => $follow->error_message,
            ],
        ]);
    }

    /**
     * Pre-fill payload for the "Trade Now" button.
     *
     * Returns everything the order modal needs, including the live price, so
     * the modal opens with a quote that has not gone stale while the user was
     * reading the signal.
     */
    public function ticket(int $id)
    {
        $signal = TradingSignal::find($id);

        if (! $signal) {
            return response()->json(['success' => false, 'message' => __('Signal not found.')], 404);
        }

        $user = Auth::user();

        $connection = $this->defaultConnection($user, $signal->market_type);

        $price = (float) $signal->entry_price;
        $priceSource = 'signal';

        if ($connection) {
            try {
                $ticker = $connection->adapter()->fetchTicker($signal->pair, $signal->market_type);

                if (($ticker['last'] ?? 0) > 0) {
                    $price = (float) $ticker['last'];
                    $priceSource = 'live';
                }
            } catch (\Throwable $e) {
                // Fall back to the signal price rather than blocking the modal.
            }
        }

        $preferences = UserTradingPreference::forUser((int) $user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'signal_id' => $signal->id,
                'pair' => $signal->pair,
                'display_pair' => $signal->base_asset . '/' . $signal->quote_asset,
                'market_type' => $signal->market_type,
                'side' => $signal->is_long ? 'buy' : 'sell',
                'direction' => $signal->market_type === 'futures'
                    ? ($signal->is_long ? 'long' : 'short')
                    : ($signal->is_long ? 'buy' : 'sell'),
                'order_type' => 'market',
                'price' => $price,
                'price_source' => $priceSource,
                'take_profit_price' => $signal->target_2 !== null ? (float) $signal->target_2 : (float) $signal->target_1,
                'stop_loss_price' => $signal->stop_loss !== null ? (float) $signal->stop_loss : null,
                'leverage' => $signal->market_type === 'futures' ? 10 : 1,
                'connection_id' => $connection?->id,
                'available_to_trade' => $preferences->max_trade_size,
                'max_trade_size' => (float) $preferences->max_trade_size,
                'risk_percentage' => (float) $preferences->risk_percentage,
                'auto_stop_loss' => (bool) $preferences->auto_stop_loss,
            ],
        ]);
    }

    /**
     * Signal card payload: the signal plus this user's follow state.
     */
    protected function cardPayload(TradingSignal $signal): array
    {
        $payload = $signal->toFeedArray();

        $follow = SignalFollow::where('user_id', Auth::id())
            ->where('trading_signal_id', $signal->id)
            ->first();

        $payload['follow'] = $follow ? [
            'mode' => $follow->mode,
            'status' => $follow->status,
            'order_id' => $follow->trade_order_id,
            'error_message' => $follow->error_message,
        ] : null;

        return $payload;
    }

    /**
     * Follow state keyed by signal id, for the initial page render.
     */
    protected function followedMap(int $userId): array
    {
        return SignalFollow::where('user_id', $userId)
            ->get(['trading_signal_id', 'mode', 'status', 'trade_order_id', 'error_message'])
            ->mapWithKeys(fn ($follow) => [(int) $follow->trading_signal_id => [
                'mode' => $follow->mode,
                'status' => $follow->status,
                'order_id' => $follow->trade_order_id,
                'error_message' => $follow->error_message,
            ]])
            ->all();
    }

    protected function hasConnectionFor($user, string $marketType): bool
    {
        return ExchangeConnection::where('user_id', $user->id)
            ->where('market_type', $marketType)
            ->where('is_active', true)
            ->exists();
    }

    protected function defaultConnection($user, string $marketType): ?ExchangeConnection
    {
        return ExchangeConnection::where('user_id', $user->id)
            ->where('market_type', $marketType)
            ->where('is_active', true)
            ->orderBy('id')
            ->first();
    }
}
