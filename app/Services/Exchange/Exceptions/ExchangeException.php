<?php

namespace App\Services\Exchange\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Every failure surfaced by an exchange adapter is an ExchangeException.
 *
 * Jobs and controllers can therefore catch one type and reliably distinguish an
 * exchange-side problem (retryable, user notification) from a programming error
 * (not retryable). The optional HTTP status drives the retry decision.
 */
class ExchangeException extends RuntimeException
{
    protected ?int $httpStatus = null;
    protected ?string $errorCode = null;
    protected array $context = [];

    public function __construct(
        string $message,
        ?int $httpStatus = null,
        ?string $errorCode = null,
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, (int) $httpStatus, $previous);

        $this->httpStatus = $httpStatus;
        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    public function httpStatus(): ?int
    {
        return $this->httpStatus;
    }

    public function errorCode(): ?string
    {
        return $this->errorCode;
    }

    public function context(): array
    {
        return $this->context;
    }

    /**
     * Rate limits and 5xx responses are worth another attempt. 4xx auth and
     * validation errors are not — retrying a bad signature just burns quota.
     */
    public function isRetryable(): bool
    {
        if ($this->httpStatus === null) {
            // Network-level failure (DNS, TLS, timeout) — worth a retry.
            return true;
        }

        if ($this->httpStatus === 429 || $this->httpStatus >= 500) {
            return true;
        }

        return false;
    }

    /**
     * Whether the user needs to be told their key is broken, as opposed to
     * just experiencing a transient outage.
     */
    public function isCredentialProblem(): bool
    {
        if (in_array($this->httpStatus, [401, 403], true)) {
            return true;
        }

        $code = strtolower((string) $this->errorCode);

        foreach (['invalid_api_key', 'invalidapikey', '-2014', '-2015', 'api_key_invalid', 'unauthorized'] as $needle) {
            if ($code !== '' && str_contains($code, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Seconds the exchange asked us to wait, if it said.
     */
    public function retryAfter(): ?int
    {
        return isset($this->context['retry_after']) ? (int) $this->context['retry_after'] : null;
    }
}
