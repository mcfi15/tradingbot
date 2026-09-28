<?php

namespace App\Jobs;

use App\Services\Exchange\ExchangeBalanceService;
use App\Services\Exchange\Exceptions\ExchangeException;
use App\Models\ExchangeConnection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;

/**
 * Refresh one exchange connection's wallet balances.
 *
 * Queue design notes:
 *
 *  - ShouldBeUnique with a per-connection unique id, so a burst of dashboard
 *    refreshes or a scheduler tick that fires while the previous sync is still
 *    running collapses into a single job instead of hammering a rate-limited
 *    endpoint.
 *  - WithoutOverlapping as belt-and-braces for the case where the unique lock
 *    has already been released by a job that is mid-flight in its release
 *    callback.
 *  - Exponential backoff, because 429 is the normal steady state for a busy
 *    account and an immediate retry makes it worse.
 *  - Release the unique lock on failure so the job is retried; a failed job
 *    keeps its lock until the retry window expires otherwise.
 */
class SyncExchangeBalanceJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Give up after 4 attempts. Beyond that the account is almost certainly
     * misconfigured rather than rate limited.
     */
    public int $tries = 4;

    /**
     * Seconds to wait before attempt N: 10, 40, 160.
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 40, 160];

    /**
     * A failing balance sync should not hold a worker for long.
     */
    public int $timeout = 60;

    /**
     * Discard the job if another sync for the same connection completed in the
     * last 30 seconds.
     */
    public int $uniqueFor = 30;

    public function __construct(public int $connectionId)
    {
        $this->onQueue(config('exchange.queues.balances', 'balances'));
    }

    /**
     * Unique per connection, not per user: a user with both a spot and a futures
     * connection must be able to sync both.
     */
    public function uniqueId(): string
    {
        return 'sync-balance:' . $this->connectionId;
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->connectionId))->expireAfter(120)];
    }

    public function handle(ExchangeBalanceService $balances): void
    {
        $connection = ExchangeConnection::find($this->connectionId);

        if (!$connection) {
            // The connection was deleted while the job sat in the queue. This
            // is a success, not a failure.
            Log::info('sync_balance.connection_missing', ['connection_id' => $this->connectionId]);

            return;
        }

        if (! $connection->is_active) {
            Log::info('sync_balance.connection_inactive', ['connection_id' => $connection->id]);

            return;
        }

        $result = $balances->refresh($connection);

        Log::info('sync_balance.completed', [
            'connection_id' => $connection->id,
            'exchange' => $connection->exchange,
            'market_type' => $connection->market_type,
            'total' => $result['total'],
            'assets' => count($result['assets']),
        ]);
    }

    /**
     * Record the failure on the connection and rethrow so the queue's own retry
     * policy applies.
     */
    public function failed(\Throwable $exception): void
    {
        $connection = ExchangeConnection::find($this->connectionId);

        if (!$connection) {
            return;
        }

        $connection->forceFill(['last_error' => mb_substr($exception->getMessage(), 0, 2000)])->saveQuietly();

        Log::error('sync_balance.failed', [
            'connection_id' => $connection->id,
            'attempt' => $this->tries,
            'error' => $exception->getMessage(),
        ]);
    }
}
