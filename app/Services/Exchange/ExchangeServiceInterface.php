<?php

namespace App\Services\Exchange;

use App\Services\Exchange\Exceptions\ExchangeException;

/**
 * The contract every exchange adapter fulfils.
 *
 * Design rules for implementors:
 *
 *  - Every method throws ExchangeException on failure. Adapters never leak
 *    vendor-specific payloads to callers; the raw body is available via
 *    ExchangeException::context() for logging, not for control flow.
 *  - No method returns or logs secret material. Implementations receive the
 *    decrypted key/secret through the constructor and must not echo them.
 *  - Prices and quantities are returned as the exchange's native precision.
 *    Rounding to a symbol's step size happens before submission, not after.
 */
interface ExchangeServiceInterface
{
    /**
     * Canonical exchange identifier, matching config('exchange.supported').
     */
    public function name(): string;

    /**
     * Whether the adapter can open perpetual positions on this exchange.
     */
    public function supportsFutures(): bool;

    /**
     * Fetch wallet balances.
     *
     * Returned shape (normalised across all three exchanges):
     *
     * [
     *   'market_type'  => 'spot'|'futures',
     *   'quote_asset'  => 'USDT',
     *   'total'        => 1234.56,          // total in quote_asset
     *   'free_quote'   => 1000.00,          // spendable balance
     *   'assets'       => [
     *        ['asset' => 'BTC', 'free' => 0.5, 'locked' => 0.0, 'total' => 0.5, 'usd_value' => 30000.0],
     *   ],
     *   'fetched_at'   => 1750000000,
     * ]
     *
     * @throws ExchangeException
     */
    public function fetchBalances(string $marketType): array;

    /**
     * Fetch the latest price for a symbol.
     *
     * @return array{last: float, bid: float|null, ask: float|null, change_percent: float|null}
     *
     * @throws ExchangeException
     */
    public function fetchTicker(string $symbol, string $marketType = 'spot'): array;

    /**
     * Submit a spot order.
     *
     * @param array{
     *     symbol: string,
     *     side: string,
     *     type: string,
     *     quantity: float,
     *     price: float|null,
     *     client_order_id: string
     * } $params
     *
     * @return array{
     *     exchange_order_id: string|null,
     *     client_order_id: string,
     *     status: string,
     *     filled_price: float|null,
     *     filled_quantity: float,
     *     raw: array
     * }
     *
     * @throws ExchangeException
     */
    public function placeSpotOrder(array $params): array;

    /**
     * Submit a futures order. Implementations set leverage first when the
     * requested value differs from the current one.
     *
     * @param array{
     *     symbol: string,
     *     side: string,
     *     type: string,
     *     quantity: float,
     *     price: float|null,
     *     leverage: int,
     *     reduce_only: bool,
     *     client_order_id: string
     * } $params
     *
     * @return array Same shape as placeSpotOrder().
     *
     * @throws ExchangeException
     */
    public function placeFuturesOrder(array $params): array;

    /**
     * Set leverage for a perpetual symbol. Idempotent on all three exchanges.
     *
     * @throws ExchangeException
     */
    public function setLeverage(string $symbol, int $leverage, string $marketType = 'futures'): array;

    /**
     * Cancel a single resting order.
     *
     * @throws ExchangeException
     */
    public function cancelOrder(string $symbol, string $orderId, string $marketType = 'spot'): array;

    /**
     * Close an entire position at market, used by the stop-loss monitor and the
     * manual close action.
     *
     * @return array Same shape as placeSpotOrder().
     *
     * @throws ExchangeException
     */
    public function closePosition(string $symbol, string $marketType = 'futures', ?float $quantity = null): array;

    /**
     * Currently open orders for a symbol, used to reconcile after a crash or a
     * dropped websocket.
     *
     * @return array<int, array>
     *
     * @throws ExchangeException
     */
    public function fetchOpenOrders(string $symbol, string $marketType = 'spot'): array;

    /**
     * Round a quantity down to the symbol's tradable step size.
     *
     * Rounds *down* by design: rounding up can push an order past the user's
     * available balance and get it rejected.
     *
     * @throws ExchangeException
     */
    public function roundQuantity(string $symbol, float $quantity, string $marketType = 'spot'): float;

    /**
     * Round a price to the symbol's tick size.
     *
     * @throws ExchangeException
     */
    public function roundPrice(string $symbol, float $price, string $marketType = 'spot'): float;
}
