<?php

namespace App\Console\Commands;

use App\Jobs\SyncExchangeBalanceJob;
use App\Models\ExchangeConnection;
use Illuminate\Console\Command;

/**
 * Queues a balance refresh for every active exchange connection.
 *
 * Dispatching rather than fetching inline is deliberate: one user with six
 * connections must not hold the scheduler for six sequential round trips, and
 * SyncExchangeBalanceJob is unique per connection, so re-running this command
 * every minute collapses into at most one in-flight job per connection.
 */
class SyncExchangeBalancesCommand extends Command
{
    protected $signature = 'trading:sync-balances
                            {--user= : Only sync connections belonging to this user id}
                            {--connection= : Only sync this connection id}';

    protected $description = 'Queue balance refreshes for active exchange connections';

    public function handle(): int
    {
        $query = ExchangeConnection::where('is_active', true);

        if ($this->option('user')) {
            $query->where('user_id', (int) $this->option('user'));
        }

        if ($this->option('connection')) {
            $query->where('id', (int) $this->option('connection'));
        }

        $connections = $query->get();

        if ($connections->isEmpty()) {
            $this->info('No active exchange connections to sync.');

            return self::SUCCESS;
        }

        foreach ($connections as $connection) {
            SyncExchangeBalanceJob::dispatch($connection->id);
        }

        $this->info(sprintf('Queued %d balance sync job(s).', $connections->count()));

        return self::SUCCESS;
    }
}
