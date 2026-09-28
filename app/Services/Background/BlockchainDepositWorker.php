<?php

namespace App\Services\Background;

use App\Services\BlockchainMonitoringService;
use Illuminate\Support\Facades\Log;

/**
 * Crypto Deposit Monitor Worker
 *
 * Polls active blockchains (Solana, EVM, TRON, Bitcoin) for customer deposits,
 * automatically credits user account balances, and prepares funds for sweep.
 */
class BlockchainDepositWorker
{
    /**
     * Unique identifier for health tracking in cron_jobs table.
     */
    public const IDENTIFIER = 'deposit_monitor';

    /**
     * Execute the blockchain monitoring routine.
     */
    public function execute(): void
    {
        $service = new BlockchainMonitoringService();
        $service->monitorActiveBlockchains();

        if (function_exists('updateLastCronJob')) {
            updateLastCronJob(self::IDENTIFIER);
        }
    }
}
