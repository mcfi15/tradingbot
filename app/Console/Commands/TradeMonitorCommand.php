<?php

namespace App\Console\Commands;

use App\Services\Background\TradeMonitorWorker;
use Illuminate\Console\Command;

/**
 * Drives take-profit / stop-loss enforcement and order reconciliation.
 *
 * Registered on Laravel's scheduler in bootstrap/app.php. This is a separate
 * entry point from foyana:scheduler-daemon on purpose: the daemon's task list is
 * assembled by the licensing VM, so a module that must run reliably cannot
 * depend on being registered there.
 */
class TradeMonitorCommand extends Command
{
    protected $signature = 'trading:monitor
                            {--once : Run a single pass and exit (default)}';

    protected $description = 'Enforce take-profit/stop-loss on open trades and reconcile pending orders with the exchange';

    public function handle(TradeMonitorWorker $worker): int
    {
        $worker->execute();

        $this->info('Trade monitor pass complete.');

        return self::SUCCESS;
    }
}
