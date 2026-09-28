<?php

namespace App\Services\Exchange;

use App\Services\Exchange\Exceptions\ExchangeException;
use App\Models\ExchangeConnection;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Balance retrieval with cache-aside semantics.
 *
 * Exchange account endpoints are weight-heavy: Binance charges a large request
 * weight for /api/v3/account, and Bybit and MEXC both rate limit the
 * equivalent. A dashboard refresh must therefore never trigger a live call, so
 * the cache is the primary read path and the queue is the only writer.
 */
class ExchangeBalanceService
{
    public function __construct(protected ExchangeManager $manager)
    {
    }

    /**
     * Cached read. The only path a controller or view should use.
     *
     * A cache miss falls back to a live fetch so the very first page load is
     * not empty; every subsequent load within the TTL is free.
     */
    public function cached(ExchangeConnection $connection): array
    {
        $ttl = (int) config('exchange.balance_cache_ttl', 60);

        return Cache::remember($this->cacheKey($connection), $ttl, function () use ($connection) {
            return $this->refresh($connection);
        });
    }

    /**
     * Force a live fetch, update the denormalised columns, and prime the cache.
     *
     * @throws ExchangeException
     */
    public function refresh(ExchangeConnection $connection): array
    {
        $adapter = $this->manager->forConnection($connection);

        $balances = $adapter->fetchBalances($connection->market_type);

        // Denormalise for dashboards that sort or filter in SQL, and so a
        // balance is visible even while the cache is cold.
        $connection->forceFill([
            'last_total_balance' => $balances['total'],
            'last_synced_at' => $balances['fetched_at'],
            'last_error' => null,
        ])->saveQuietly();

        Cache::put($this->cacheKey($connection), $balances, (int) config('exchange.balance_cache_ttl', 60));

        return $balances;
    }

    /**
     * Full portfolio across every active connection for a user.
     *
     * A single failing exchange must not blank the whole widget, so each
     * connection resolves independently and failures surface as an entry with an
     * error rather than an exception.
     */
    public function portfolioFor(int $userId): array
    {
        $connections = ExchangeConnection::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('exchange')
            ->orderBy('market_type')
            ->get();

        $accounts = [];
        $total = 0.0;
        $failures = 0;

        foreach ($connections as $connection) {
            $key = $connection->exchange . '_' . $connection->market_type;

            try {
                $balances = $this->cached($connection);
            } catch (ExchangeException $e) {
                $failures++;
                $accounts[$key] = [
                    'connection_id' => $connection->id,
                    'exchange' => $connection->exchange,
                    'market_type' => $connection->market_type,
                    'error' => $e->getMessage(),
                    'total' => 0.0,
                    'assets' => [],
                ];

                Log::warning('exchange_balance.connection_failed', [
                    'user_id' => $userId,
                    'connection_id' => $connection->id,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }

            $total += (float) $balances['total'];

            $accounts[$key] = [
                'connection_id' => $connection->id,
                'exchange' => $connection->exchange,
                'market_type' => $connection->market_type,
                'label' => $connection->label,
                'api_key_hint' => $connection->api_key_hint,
                'error' => null,
                'quote_asset' => $balances['quote_asset'],
                'total' => (float) $balances['total'],
                'free_quote' => (float) $balances['free_quote'],
                'assets' => $balances['assets'],
                'fetched_at' => $balances['fetched_at'],
            ];
        }

        return [
            'total' => round($total, 2),
            'quote_asset' => $connections->isNotEmpty()
                ? ($accounts[array_key_first($accounts)]['quote_asset'] ?? 'USDT')
                : 'USDT',
            'accounts' => $accounts,
            'has_failures' => $failures > 0,
            'generated_at' => time(),
        ];
    }

    /**
     * Spendable balance of a specific market, used to size an order.
     *
     * Throws rather than defaulting to zero: an order sized against an unknown
     * balance is exactly the failure mode this system must not have.
     *
     * @throws ExchangeException
     */
    public function freeQuoteBalance(ExchangeConnection $connection): float
    {
        $balances = $this->cached($connection);

        return (float) $balances['free_quote'];
    }

    /**
     * Forget a connection's cached balance, e.g. right after a trade settles.
     */
    public function forget(ExchangeConnection $connection): void
    {
        Cache::forget($this->cacheKey($connection));
    }

    /**
     * Forget every cached balance for a user.
     */
    public function forgetUser(int $userId): void
    {
        Cache::forget("exchange_balances:user:{$userId}");
    }

    protected function cacheKey(ExchangeConnection $connection): string
    {
        return sprintf(
            'exchange_balance:%d:%s:%s',
            $connection->id,
            $connection->exchange,
            $connection->market_type
        );
    }
}
