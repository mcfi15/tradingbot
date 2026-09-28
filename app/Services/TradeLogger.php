<?php

namespace App\Services;

use App\Services\Exchange\AbstractExchangeService;
use App\Services\Exchange\Exceptions\ExchangeException;
use App\Models\TradeLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Single entry point for trade-system audit records and user alerts.
 *
 * Every order attempt, retry, rejection and stop-loss execution writes a
 * trade_logs row. Failures additionally raise an in-app notification so the
 * user learns about a rejected order without watching the log.
 *
 * Context passed here is redacted before persistence. That redaction is not
 * optional decoration: exchange error bodies occasionally echo back the
 * signature or the API key, and those bodies end up in this table.
 */
class TradeLogger
{
    /**
     * Keys scrubbed from any context array before it is written.
     */
    private const SENSITIVE = [
        'signature', 'sign', 'secret', 'api_secret', 'api_secret_key', 'apisecret',
        'x-mbx-apikey', 'x-bapi-api-key', 'x-bapi-sign', 'accesskey', 'accesstoken',
        'access_token', 'private_key', 'token', 'api_key', 'apikey', 'authorization',
    ];

    /**
     * Persist an audit record.
     */
    public function log(
        int $userId,
        string $message,
        string $level = 'info',
        array $context = [],
        ?string $action = null,
        ?int $orderId = null,
        ?int $signalId = null,
        ?string $exchange = null,
        ?string $pair = null,
        ?string $marketType = null
    ): ?TradeLog {
        try {
            return TradeLog::create([
                'user_id' => $userId,
                'trade_order_id' => $orderId,
                'trading_signal_id' => $signalId,
                'exchange' => $exchange,
                'pair' => $pair,
                'market_type' => $marketType,
                'level' => in_array($level, ['info', 'warning', 'error', 'critical'], true) ? $level : 'info',
                'action' => $action,
                'message' => mb_substr($message, 0, 2000),
                'context' => $this->sanitize($context),
            ]);
        } catch (\Throwable $e) {
            // Auditing must never be the reason a trade flow dies.
            Log::error('trade_logger.persist_failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Log a failed attempt and notify the user.
     *
     * Credential problems get a different, more actionable message than a
     * transient failure, because only the user can fix the former.
     */
    public function logFailure(
        User $user,
        ExchangeException $exception,
        array $context = [],
        ?int $orderId = null,
        ?int $signalId = null,
        ?string $exchange = null,
        ?string $pair = null,
        ?string $marketType = null
    ): void {
        $credentialProblem = $exception->isCredentialProblem();

        $level = $credentialProblem ? 'critical' : 'error';

        $this->log(
            (int) $user->id,
            $this->scrub($exception->getMessage()),
            $level,
            array_merge($context, $exception->context()),
            'order_failed',
            $orderId,
            $signalId,
            $exchange,
            $pair,
            $marketType
        );

        $title = $credentialProblem
            ? __('Exchange connection needs attention')
            : __('Trade order failed');

        $body = $credentialProblem
            ? __('Your :exchange API credentials were rejected. Open Exchange Connections and re-enter your key and secret.', ['exchange' => strtoupper((string) $exchange)])
            : __('Your :pair order could not be placed: :reason', [
                'pair' => (string) $pair,
                'reason' => $this->scrub($exception->getMessage()),
            ]);

        $this->notify($user, $title, $body);
    }

    /**
     * Raise an in-app notification, tolerating a missing user record.
     */
    public function notify(User $user, string $title, string $body): void
    {
        try {
            if (!function_exists('recordNotificationMessage')) {
                return;
            }

            recordNotificationMessage($user, $title, $body);
        } catch (\Throwable $e) {
            Log::error('trade_logger.notify_failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Recursively drop credential material from a context structure.
     */
    public function sanitize(array $context): array
    {
        $out = [];

        foreach ($context as $key => $value) {
            if (is_string($key) && $this->isSensitive($key)) {
                $out[$key] = '[redacted]';
                continue;
            }

            $out[$key] = is_array($value) ? $this->sanitize($value) : $value;
        }

        return $out;
    }

    private function isSensitive(string $key): bool
    {
        $normalised = strtolower(str_replace(['-', '_'], '', $key));

        foreach (self::SENSITIVE as $sensitive) {
            $needle = strtolower(str_replace(['-', '_'], '', $sensitive));

            if ($normalised === $needle) {
                return true;
            }

            if (strlen($needle) >= 5 && str_contains($normalised, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Strip any literal credential echoed back inside an exchange message.
     */
    private function scrub(string $message): string
    {
        return preg_replace('/\b[A-Za-z0-9]{32,}\b/', '[redacted]', $message) ?? $message;
    }
}
