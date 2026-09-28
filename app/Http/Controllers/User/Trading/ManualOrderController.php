<?php

namespace App\Http\Controllers\User\Trading;

use App\Http\Controllers\Controller;
use App\Services\Exchange\ExchangeBalanceService;
use App\Services\Exchange\ExchangeManager;
use App\Services\Exchange\Exceptions\ExchangeException;
use App\Services\RiskManager;
use App\Services\TradeLogger;
use App\Models\ExchangeConnection;
use App\Models\TradeLog;
use App\Models\TradeOrder;
use App\Models\TradingSignal;
use App\Models\UserTradingPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Manual order entry and position management.
 *
 * Manual orders are submitted synchronously, not queued. The user is watching
 * the screen and needs an immediate, authoritative answer about whether the
 * exchange accepted the order; a queued submission would return "pending" and
 * leave them guessing. The write-ahead trade_orders row still happens first, so
 * a crash mid-request cannot lose the order.
 */
class ManualOrderController extends Controller
{
    public function __construct(
        protected RiskManager $risk,
        protected TradeLogger $logger,
        protected ExchangeBalanceService $balances,
        protected ExchangeManager $exchanges
    ) {
    }

    /**
     * Order ticket and position blotter.
     */
    public function index(Request $request)
    {
        $page_title = __('Trade');
        $template = config('site.template');

        $user = Auth::user();

        $connections = ExchangeConnection::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('exchange')
            ->orderBy('market_type')
            ->get()
            ->map(fn (ExchangeConnection $connection) => $connection->toDisplayArray())
            ->values();

        $orders = TradeOrder::where('user_id', $user->id)
            ->open()
            ->latest('id')
            ->paginate((int) config('site.pagination', 15));

        $preferences = UserTradingPreference::forUser((int) $user->id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'orders' => $orders->getCollection()->map(fn (TradeOrder $order) => $order->toDisplayArray())->values(),
                    'open_count' => TradeOrder::where('user_id', $user->id)->open()->count(),
                ],
            ]);
        }

        return view("templates.{$template}.blades.user.trading.orders.index", compact(
            'page_title',
            'orders',
            'connections',
            'preferences'
        ));
    }

    /**
     * Place a manual spot or futures order.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'connection_id' => ['required', 'integer', 'min:1'],
            'signal_id' => ['nullable', 'integer', 'min:1'],
            'pair' => ['required', 'string', 'max:20'],
            'market_type' => ['required', Rule::in(['spot', 'futures'])],
            'side' => ['required', Rule::in(['buy', 'sell'])],
            'order_type' => ['required', Rule::in(['market', 'limit'])],
            'quantity' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'leverage' => ['nullable', 'integer', 'min:1', 'max:125'],
            'take_profit_price' => ['nullable', 'numeric', 'min:0'],
            'stop_loss_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $connection = ExchangeConnection::where('id', $validated['connection_id'])
            ->where('user_id', $user->id)
            ->first();

        if (! $connection) {
            return response()->json(['success' => false, 'message' => __('Exchange connection not found.')], 404);
        }

        if (! $connection->is_active) {
            return response()->json([
                'success' => false,
                'message' => __('This exchange connection is disabled.'),
            ], 422);
        }

        if ($validated['market_type'] !== $connection->market_type) {
            // Silently trading spot on a futures credential would fail at the
            // exchange with an opaque error, so reject it here.
            return response()->json([
                'success' => false,
                'message' => __('The selected connection is for :market, but you chose :type.', [
                    'market' => $connection->market_type,
                    'type' => $validated['market_type'],
                ]),
            ], 422);
        }

        $orderType = $validated['order_type'];

        if ($orderType === 'limit' && empty($validated['price'])) {
            return response()->json([
                'success' => false,
                'message' => __('A limit order needs a price.'),
            ], 422);
        }

        $pair = strtoupper(str_replace(['/', '-', '_'], '', $validated['pair']));

        $signal = !empty($validated['signal_id'])
            ? TradingSignal::where('id', $validated['signal_id'])
                ->where('pair', $pair)
                ->first()
            : null;

        $decision = $this->risk->approve(
            user: $user,
            connection: $connection,
            pair: $pair,
            side: $validated['side'],
            quantity: $validated['quantity'] !== null ? (float) $validated['quantity'] : null,
            entryPrice: $orderType === 'limit' ? (float) $validated['price'] : null,
            leverage: $validated['leverage'] ?? null,
            takeProfit: $validated['take_profit_price'] ?? null,
            stopLoss: $validated['stop_loss_price'] ?? null,
            automated: false
        );

        if (! $decision['allowed']) {
            return response()->json([
                'success' => false,
                'code' => $decision['code'],
                'message' => $decision['reason'],
            ], 422);
        }

        $clientOrderId = $this->clientOrderId();

        $order = DB::transaction(function () use (
            $user, $connection, $signal, $decision, $pair, $validated, $orderType, $clientOrderId
        ) {
            return TradeOrder::create([
                'user_id' => $user->id,
                'exchange_connection_id' => $connection->id,
                'trading_signal_id' => $signal?->id,
                'client_order_id' => $clientOrderId,
                'exchange' => $connection->exchange,
                'market_type' => $connection->market_type,
                'pair' => $pair,
                'side' => $validated['side'],
                'direction' => $connection->market_type === 'futures'
                    ? ($validated['side'] === 'buy' ? 'long' : 'short')
                    : $validated['side'],
                'order_type' => $orderType,
                'leverage' => $decision['leverage'],
                'quantity' => $decision['quantity'],
                'limit_price' => $orderType === 'limit' ? $validated['price'] : null,
                'take_profit_price' => $decision['take_profit_price'],
                'stop_loss_price' => $decision['stop_loss_price'],
                'notional' => $decision['notional'],
                'status' => 'pending',
                'origin' => 'manual',
            ]);
        });

        try {
            $adapter = $connection->adapter();

            $params = [
                'symbol' => $pair,
                'side' => $validated['side'],
                'type' => $orderType,
                'quantity' => $decision['quantity'],
                'price' => $orderType === 'limit' ? (float) $validated['price'] : null,
                'leverage' => $decision['leverage'],
                'client_order_id' => $clientOrderId,
                'reduce_only' => false,
                'take_profit_price' => $decision['take_profit_price'],
                'stop_loss_price' => $decision['stop_loss_price'],
            ];

            $result = $connection->market_type === 'futures'
                ? $adapter->placeFuturesOrder($params)
                : $adapter->placeSpotOrder($params);

            $filledPrice = $result['filled_price'] ?: $decision['entry_price'];
            $filledQuantity = $result['filled_quantity'] ?: $decision['quantity'];

            $status = match ($result['status']) {
                'filled' => 'filled',
                'cancelled', 'rejected' => 'rejected',
                default => 'open',
            };

            $order->forceFill([
                'exchange_order_id' => $result['exchange_order_id'],
                'status' => $status,
                'filled_price' => $filledPrice,
                'quantity' => $filledQuantity,
                'notional' => $filledPrice * $filledQuantity,
                'submitted_at' => time(),
                'filled_at' => $status === 'filled' ? time() : null,
            ])->save();

            $this->balances->forget($connection);

            $this->logger->log(
                (int) $user->id,
                sprintf(
                    'Manual %s %s %s at %s on %s (%s).',
                    strtoupper($order->direction),
                    rtrim(rtrim(number_format((float) $filledQuantity, 8, '.', ''), '0'), '.'),
                    $pair,
                    $filledPrice,
                    strtoupper($connection->exchange),
                    $status
                ),
                'info',
                [
                    'order_id' => $order->id,
                    'exchange_order_id' => $result['exchange_order_id'],
                    'origin' => 'manual',
                ],
                'manual_order_placed',
                $order->id,
                $signal?->id,
                $connection->exchange,
                $pair,
                $connection->market_type
            );

            if ($signal) {
                SignalFollow::updateOrCreate(
                    ['user_id' => $user->id, 'trading_signal_id' => $signal->id],
                    [
                        'mode' => 'manual',
                        'status' => 'executed',
                        'trade_order_id' => $order->id,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => $status === 'filled'
                    ? __('Order filled.')
                    : __('Order placed and is resting on the exchange.'),
                'data' => $order->fresh()->toDisplayArray(),
            ]);
        } catch (ExchangeException $e) {
            $order->forceFill([
                'status' => 'rejected',
                'error_message' => mb_substr($e->getMessage(), 0, 2000),
                'closed_at' => time(),
            ])->save();

            $this->logger->logFailure(
                $user,
                $e,
                ['order_id' => $order->id, 'origin' => 'manual'],
                $order->id,
                $signal?->id,
                $connection->exchange,
                $pair,
                $connection->market_type
            );

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Close an open position at market.
     */
    public function close(Request $request, int $id)
    {
        $user = Auth::user();

        $order = TradeOrder::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $order) {
            return response()->json(['success' => false, 'message' => __('Order not found.')], 404);
        }

        if (! $order->is_open) {
            return response()->json([
                'success' => false,
                'message' => __('This trade is already closed.'),
            ], 422);
        }

        $connection = $order->connection()->first();

        if (! $connection) {
            return response()->json([
                'success' => false,
                'message' => __('The exchange connection for this trade is gone, so it cannot be closed automatically. Close it on the exchange directly.'),
            ], 422);
        }

        try {
            $adapter = $connection->adapter();

            $result = $connection->market_type === 'futures'
                ? $adapter->closePosition($order->pair, 'futures', (float) $order->quantity)
                : $this->closeSpot($adapter, $order);

            $exitPrice = $result['filled_price'] ?: (float) $order->filled_price;

            $realised = $this->realisedPnl($order, $exitPrice);

            $order->forceFill([
                'exchange_order_id' => $result['exchange_order_id'] ?? $order->exchange_order_id,
                'status' => 'closed',
                'realised_pnl' => $realised,
                'closed_at' => time(),
                'close_reason' => 'manual',
            ])->save();

            $this->balances->forget($connection);

            $this->logger->log(
                (int) $user->id,
                sprintf('Closed %s %s at %s. Realised %s USDT.', $order->pair, strtoupper((string) $order->direction), $exitPrice, $realised),
                'info',
                ['order_id' => $order->id],
                'position_closed',
                $order->id,
                $order->trading_signal_id,
                $connection->exchange,
                $order->pair,
                $order->market_type
            );

            return response()->json([
                'success' => true,
                'message' => __('Position closed.'),
                'data' => [
                    'order' => $order->fresh()->toDisplayArray(),
                    'realised_pnl' => $realised,
                ],
            ]);
        } catch (ExchangeException $e) {
            $this->logger->logFailure(
                $user,
                $e,
                ['order_id' => $order->id, 'action' => 'close'],
                $order->id,
                $order->trading_signal_id,
                $connection->exchange,
                $order->pair,
                $order->market_type
            );

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Cancel a resting limit order that has not filled.
     */
    public function cancel(int $id)
    {
        $user = Auth::user();

        $order = TradeOrder::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $order) {
            return response()->json(['success' => false, 'message' => __('Order not found.')], 404);
        }

        if ($order->status !== 'open' || ! $order->exchange_order_id) {
            return response()->json([
                'success' => false,
                'message' => __('Only a resting order can be cancelled. Use close for a filled position.'),
            ], 422);
        }

        $connection = $order->connection()->first();

        if (! $connection) {
            return response()->json(['success' => false, 'message' => __('Connection not found.')], 404);
        }

        try {
            $connection->adapter()->cancelOrder($order->pair, $order->exchange_order_id, $order->market_type);

            $order->forceFill([
                'status' => 'cancelled',
                'closed_at' => time(),
                'close_reason' => 'cancelled',
            ])->save();

            return response()->json([
                'success' => true,
                'message' => __('Order cancelled.'),
                'data' => $order->fresh()->toDisplayArray(),
            ]);
        } catch (ExchangeException $e) {
            $this->logger->logFailure(
                $user,
                $e,
                ['order_id' => $order->id, 'action' => 'cancel'],
                $order->id,
                $order->trading_signal_id,
                $connection->exchange,
                $order->pair,
                $order->market_type
            );

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Trade log viewer, filtered by level and pair.
     */
    public function logs(Request $request)
    {
        $page_title = __('Trade Logs');
        $template = config('site.template');

        $query = TradeLog::where('user_id', Auth::id())
            ->with('order')
            ->latest('id');

        if ($request->query('level') === 'problems') {
            $query->problems();
        }

        $query->forPair($request->query('pair'));

        $logs = $query->paginate((int) config('site.pagination', 25));

        return view("templates.{$template}.blades.user.trading.orders.logs", compact('page_title', 'logs'));
    }

    /**
     * Spot has no close-position endpoint, so closing means selling the held
     * quantity back to the quote asset.
     */
    protected function closeSpot(\App\Services\Exchange\ExchangeServiceInterface $adapter, TradeOrder $order): array
    {
        $quantity = (float) $order->quantity;

        if ($quantity <= 0) {
            throw new ExchangeException(
                'The exchange reported a quantity of zero, so there is nothing to close.',
                422,
                'quantity_unknown'
            );
        }

        return $adapter->placeSpotOrder([
            'symbol' => $order->pair,
            'side' => $order->side === 'buy' ? 'sell' : 'buy',
            'type' => 'market',
            'quantity' => $quantity,
            'price' => null,
            'client_order_id' => $this->clientOrderId(),
        ]);
    }

    /**
     * Realised PnL in quote currency, net of nothing — fees are not reliably
     * available on every venue response, so they are logged separately rather
     * than guessed here.
     */
    protected function realisedPnl(TradeOrder $order, float $exitPrice): float
    {
        $entry = (float) ($order->filled_price ?: 0);

        if ($entry <= 0 || $exitPrice <= 0) {
            return 0.0;
        }

        $sign = $order->side === 'buy' ? 1 : -1;

        return round(($exitPrice - $entry) * (float) $order->quantity * $sign, 8);
    }

    /**
     * Unique per submission. Exchanges cap this at 36-40 characters.
     */
    protected function clientOrderId(): string
    {
        return 'mn' . Str::upper(Str::random(20));
    }
}
