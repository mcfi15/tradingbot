<?php

namespace App\Services\Exchange;

use Illuminate\Support\Facades\Log;

/**
 * Shared redaction, symbol handling and transport contract for exchange adapters.
 *
 * Scope note: this class deliberately does NOT provide a generic HTTP send()
 * helper. Binance, Bybit and MEXC each sign differently — Binance over a query
 * string with the signature inside it, Bybit over a raw body with auth headers,
 * MEXC over a parameter string with the secret appended — so each adapter owns
 * its own send() and normalises errors itself. A shared sender here would have
 * been three sets of conditionals pretending to be one abstraction.
 *
 * What IS shared, and what makes the difference between three adapters and one
 * interchangeable interface, lives here: secret redaction, symbol
 * canonicalisation, and the error-extraction hook.
 */
abstract class AbstractExchangeService implements ExchangeServiceInterface
{
    /**
     * Credential-derived keys that must never reach a log, an exception
     * message, or a database row.
     */
    protected const SENSITIVE_KEYS = [
        'signature',
        'sign',
        'secret',
        'api_secret',
        'api_secret_key',
        'apisecret',
        'x-mbx-apikey',
        'x-bapi-api-key',
        'x-bapi-sign',
        'accesskey',
        'access_token',
        'accesstoken',
        'private_key',
        'token',
        'apikey',
        'api_key',
        'authorization',
    ];

    protected string $apiKey;
    protected string $apiSecret;

    public function __construct(string $apiKey, string $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /*
    |--------------------------------------------------------------------------
    | Transport contract
    |--------------------------------------------------------------------------
    */

    /**
     * Build the auth material for one request.
     *
     * Return shape differs by venue and is documented per adapter: Binance and
     * Bybit return ['headers' => [...]] and fold a signature into the params;
     * MEXC returns flat accessToken/timestamp/sign params. The adapter's own
     * send() knows how to place them.
     */
    abstract protected function authPayload(string $method, string $path, array $params, string $body): array;

    /**
     * Pull the vendor error code and message out of a decoded body.
     *
     * @return array{0: string|null, 1: string|null} [code, message]
     */
    abstract protected function extractError(array $decoded): array;

    /*
    |--------------------------------------------------------------------------
    | Redaction
    |--------------------------------------------------------------------------
    */

    /**
     * Recursively strip credential material from an arbitrary structure.
     *
     * This is the single choke point that makes "secrets never appear in logs"
     * enforceable rather than aspirational. Adapter response bodies are passed
     * through it before being returned or logged.
     */
    public function redact(mixed $value, ?string $key = null): mixed
    {
        if ($key !== null && $this->isSensitiveKey($key)) {
            return '[redacted]';
        }

        if (is_array($value)) {
            $out = [];

            foreach ($value as $k => $v) {
                $out[$k] = $this->redact($v, is_string($k) ? $k : null);
            }

            return $out;
        }

        return $value;
    }

    protected function isSensitiveKey(string $key): bool
    {
        $normalised = strtolower(str_replace(['-', '_'], '', $key));

        foreach (self::SENSITIVE_KEYS as $sensitive) {
            $needle = strtolower(str_replace(['-', '_'], '', $sensitive));

            if ($normalised === $needle) {
                return true;
            }

            // Catch concatenations such as "binance_secret" or
            // "params_signature". The 5-character floor avoids false positives
            // on short tokens like "sign".
            if (strlen($needle) >= 5 && str_contains($normalised, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Replace the literal secret values anywhere in a string.
     *
     * Key-based redaction cannot catch a vendor error message that echoes the
     * credential back inside prose, so this is applied to every message that
     * becomes an exception or a log line.
     */
    protected function scrubSecretValues(string $message): string
    {
        $secret = $this->apiSecret;
        $key = $this->apiKey;

        if ($secret !== '') {
            $message = str_replace($secret, '[redacted]', $message);
        }

        if ($key !== '' && strlen($key) >= 8) {
            $message = str_replace($key, '[redacted]', $message);
        }

        return $message;
    }

    /**
     * Generic scrubber for logs that may contain long opaque tokens (order ids,
     * signatures embedded in a URL).
     *
     * Two passes, because they catch different things. The length rule catches
     * unnamed hashes such as a Binance signature. The name rule catches
     * credential-shaped values that are too short to be recognised by length —
     * an HTTP stack error that echoes the full request URL would otherwise
     * carry a live `signature=` or `accessToken=` straight into a log line.
     */
    protected function scrubOpaqueTokens(string $message): string
    {
        $names = implode('|', array_map(
            static fn ($key) => preg_quote($key, '/'),
            self::SENSITIVE_KEYS
        ));

        $message = preg_replace(
            '/(?<![\w-])(' . $names . ')=([^&\s"\']+)/i',
            '$1=[redacted]',
            $message
        ) ?? $message;

        return preg_replace('/\b[A-Za-z0-9]{32,}\b/', '[redacted]', $message) ?? $message;
    }

    /*
    |--------------------------------------------------------------------------
    | Symbol + asset helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Canonicalise a symbol to the venue's native form.
     *
     * Accepts "BTC/USDT", "BTC-USDT", "BTC_USDT" and "BTCUSDT" and returns
     * "BTCUSDT", so callers can pass whichever form their UI happens to hold.
     */
    public function normalizeSymbol(string $symbol): string
    {
        return strtoupper(str_replace(['/', '-', '_', ':'], '', trim($symbol)));
    }

    /**
     * Split a symbol into base and quote.
     *
     * Resolves against the configured quote list first, so USDT and USDC are
     * both handled correctly. Only if no known quote matches does it fall back
     * to assuming a 4-character quote, which is right for the major pairs these
     * adapters support.
     *
     * @return array{0: string, 1: string} [base, quote]
     */
    public function splitSymbol(string $symbol): array
    {
        $symbol = $this->normalizeSymbol($symbol);

        foreach (config('exchange.quote_assets', ['USDT', 'USDC', 'BUSD']) as $quote) {
            $length = strlen($quote);

            if ($length < strlen($symbol) && str_ends_with($symbol, $quote)) {
                return [substr($symbol, 0, -$length), $quote];
            }
        }

        $base = substr($symbol, 0, -4);

        return [$base, substr($symbol, -4)];
    }

    /**
     * Highest-priority quote asset present in a balance set. USDT is preferred
     * because it is the denominator for the prices used to value holdings.
     */
    protected function resolveQuoteAsset(array $assets): string
    {
        $quotes = config('exchange.quote_assets', ['USDT', 'USDC', 'BUSD']);
        $present = array_column($assets, 'asset');

        foreach ($quotes as $quote) {
            if (in_array($quote, $present, true)) {
                return $quote;
            }
        }

        return $quotes[0] ?? 'USDT';
    }

    /**
     * Empty, fully-shaped balance response. Every adapter returns this rather
     * than an empty array so callers never special-case a new user.
     */
    protected function emptyBalances(string $marketType, string $quote = 'USDT'): array
    {
        return [
            'market_type' => $marketType,
            'quote_asset' => $quote,
            'total' => 0.0,
            'free_quote' => 0.0,
            'assets' => [],
            'fetched_at' => $this->now(),
        ];
    }

    /**
     * Value a normalised asset list against a quote price map, in place.
     *
     * Shared by all three adapters so the wallet widget's shape, ordering and
     * totals are identical regardless of venue.
     */
    protected function valueAssets(array $assets, string $marketType, array $prices): array
    {
        if ($assets === []) {
            return $this->emptyBalances($marketType);
        }

        $quote = $this->resolveQuoteAsset($assets);

        $total = 0.0;
        $freeQuote = 0.0;

        foreach ($assets as &$asset) {
            // A missing price contributes zero rather than throwing: a valuation
            // failure must not discard the balances themselves.
            $price = $prices[$asset['asset'] . $quote] ?? ($asset['asset'] === $quote ? 1.0 : 0.0);

            $asset['usd_value'] = round($asset['total'] * $price, 2);
            $total += $asset['usd_value'];

            if ($asset['asset'] === $quote) {
                $freeQuote = $asset['free'];
            }
        }

        unset($asset);

        // Largest holdings first: the widget only renders the top slice.
        usort($assets, fn ($a, $b) => $b['usd_value'] <=> $a['usd_value']);

        return [
            'market_type' => $marketType,
            'quote_asset' => $quote,
            'total' => round($total, 2),
            'free_quote' => round($freeQuote, 8),
            'assets' => $assets,
            'fetched_at' => $this->now(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Shared helpers
    |--------------------------------------------------------------------------
    */

    protected function now(): int
    {
        return time();
    }

    /**
     * Millisecond timestamp. Binance and Bybit both sign with ms; MEXC accepts
     * seconds but is happy with ms on its v3 endpoints.
     */
    protected function timestampMs(): int
    {
        return (int) round(microtime(true) * 1000);
    }

    /**
     * Shared HTTP tuning from config('exchange.http').
     */
    protected function pendingRequest()
    {
        return \Illuminate\Support\Facades\Http::timeout(config('exchange.http.timeout', 15))
            ->connectTimeout(config('exchange.http.connect_timeout', 8))
            ->withOptions(['verify' => (bool) config('exchange.http.verify', true)]);
    }

    /**
     * Uniform "could not reach the exchange" failure. The message is scrubbed
     * because some HTTP stack messages include the request URL, and a signed
     * Binance URL contains the signature.
     */
    protected function transportFailure(string $path, \Throwable $e, int $httpStatus = null): Exceptions\ExchangeException
    {
        $scrubbed = $this->scrubOpaqueTokens($this->scrubSecretValues($e->getMessage()));
        $advice = self::transportAdvice($scrubbed);

        Log::error('exchange.transport_failure', [
            'exchange' => $this->name(),
            'path' => $path,
            'curl_code' => self::curlCode($scrubbed),
            'error' => $scrubbed,
        ]);

        // The advice leads, because it is the part that tells the reader what to
        // change, and the raw cURL text is the part that gets truncated away.
        return new Exceptions\ExchangeException(
            'Could not reach ' . strtoupper($this->name()) . '. '
                . ($advice ? $advice . ' ' : '')
                . '(' . $scrubbed . ')',
            $httpStatus,
            $advice !== null ? 'transport_' . self::curlCode($scrubbed) : 'connection_failed',
            ['path' => $path],
            $e
        );
    }

    /**
     * Extract the cURL error number from a transport message, if there is one.
     */
    private static function curlCode(string $message): ?int
    {
        return preg_match('/cURL error (\d+)/i', $message, $matches) ? (int) $matches[1] : null;
    }

    /**
     * Turn a raw transport error into something the reader can act on.
     *
     * A permanently "Unavailable" exchange is nearly always a server problem
     * rather than a credential problem, and the raw cURL code does not say so.
     * Error 60 in particular is a missing or out-of-date CA bundle, which no
     * amount of re-entering API keys will ever fix, so it is called out
     * explicitly instead of being left as a number to look up.
     *
     * Returns null when the failure is not one of the recognised shapes, and
     * the caller then reports the transport error unchanged.
     */
    private static function transportAdvice(string $message): ?string
    {
        $code = self::curlCode($message);

        if ($code === null) {
            return null;
        }

        return match ($code) {
            60 => str_contains($message, 'unable to get local issuer certificate')
                ? __('This server cannot verify the exchange\'s TLS certificate because its CA certificate bundle is missing or out of date. Install or update it (Debian/Ubuntu: "apt-get install --reinstall ca-certificates && update-ca-certificates"; Windows: point the curl.cainfo php.ini setting at a current cacert.pem), then reload PHP. Do not disable certificate verification: this connection carries your API key.')
                : __('The server rejected the exchange\'s TLS certificate. This is usually an out-of-date CA bundle or a badly skewed system clock.'),
            6 => __('The server cannot resolve the exchange\'s hostname. Check DNS and any outbound firewall rules.'),
            7 => __('The connection to the exchange was refused. Check outbound firewall rules and whether this server needs an HTTP proxy.'),
            28 => __('The exchange did not respond in time. Check connectivity, and raise EXCHANGE_HTTP_TIMEOUT if the link is slow.'),
            35 => __('The TLS handshake with the exchange failed. Check for a TLS-intercepting proxy and for system clock skew.'),
            default => null,
        };
    }
}
