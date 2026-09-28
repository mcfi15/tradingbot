<?php

namespace App\Services\Background;

use App\Models\Deposit;
use Illuminate\Support\Facades\Log;

/**
 * Expired Deposit Cleaner Worker
 *
 * Scans pending deposits, marks abandoned unpaid requests as failed,
 * and notifies users with retry instructions.
 */
class TransactionReconcileWorker
{
    /**
     * Unique identifier for health tracking in cron_jobs table.
     */
    public const IDENTIFIER = 'deposit_reconciler';

    /**
     * Reconcile and expire abandoned pending deposits.
     */
    public function execute(): void
    {
        $deposits = Deposit::whereIn('status', ['pending'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now()->timestamp)
            ->get();

        foreach ($deposits as $deposit) {
            try {
                $deposit->status = 'failed';
                $deposit->save();

                if ($deposit->user) {
                    $title = 'Deposit Failed';
                    $body = 'No payment was received within the expected time frame. Please try again.';
                    recordNotificationMessage($deposit->user, $title, $body);

                    if (function_exists('sendDepositEmail')) {
                        sendDepositEmail($title, $body, $deposit);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Transaction Reconcile Error [Deposit: {$deposit->id}]: " . $e->getMessage());
            }
        }

        if (function_exists('updateLastCronJob')) {
            updateLastCronJob(self::IDENTIFIER);
        }
    }
}
