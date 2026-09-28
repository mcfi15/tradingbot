<?php

namespace App\Services\Exchange;

use App\Services\Exchange\Exceptions\ExchangeException;
use App\Models\ExchangeConnection;
use InvalidArgumentException;

/**
 * Resolves an exchange identifier to a concrete, credential-bound adapter.
 *
 * Adapters are built per call rather than cached, because they hold decrypted
 * credentials in memory. Nothing here ever returns an adapter to a caller that
 * could serialise it — ExchangeConnection::adapter() hands the instance
 * straight to a job or a controller action and lets it go out of scope.
 */
class ExchangeManager
{
    /**
     * Build an adapter from a plain key/secret pair.
     *
     * @throws ExchangeException|InvalidArgumentException
     */
    public function make(string $exchange, string $apiKey, string $apiSecret): ExchangeServiceInterface
    {
        $class = config('exchange.adapters.' . $exchange);

        if ($class === null || !is_subclass_of($class, ExchangeServiceInterface::class)) {
            throw new InvalidArgumentException(
                'No adapter registered for exchange "' . $exchange . '"'
            );
        }

        return new $class($apiKey, $apiSecret);
    }

    /**
     * Build an adapter for a stored connection, decrypting its credentials.
     *
     * @throws ExchangeException when the connection exists but is unusable
     */
    public function forConnection(ExchangeConnection $connection): ExchangeServiceInterface
    {
        $apiKey = $connection->plainApiKey();
        $apiSecret = $connection->plainApiSecret();

        if (empty($apiKey) || empty($apiSecret)) {
            throw new ExchangeException(
                'The stored credentials for this ' . $connection->exchange . ' connection could not be read. '
                . 'Re-enter the API key and secret.',
                null,
                'credentials_unavailable'
            );
        }

        return $this->make($connection->exchange, $apiKey, $apiSecret);
    }

    /**
     * The default connection for a user, optionally narrowed by market type.
     */
    public function defaultConnection(int $userId, ?string $marketType = null): ?ExchangeConnection
    {
        $query = ExchangeConnection::where('user_id', $userId)->where('is_active', true);

        if ($marketType !== null) {
            $query->where('market_type', $marketType);
        }

        return $query->orderBy('id')->first();
    }

    /**
     * @return array<int, string>
     */
    public function supported(): array
    {
        return config('exchange.supported', []);
    }

    public function supports(string $exchange): bool
    {
        return in_array($exchange, $this->supported(), true);
    }
}
