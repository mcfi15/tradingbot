<?php

namespace App\Services\Background;

/**
 * System Log Cleaner Worker
 *
 * Checks storage log file sizes and trims oversized logs (>= 1MB)
 * to conserve server disk space and prevent I/O degradation.
 */
class LogManagerWorker
{
    /**
     * Unique identifier for health tracking in cron_jobs table.
     */
    public const IDENTIFIER = 'log_cleaner';

    /**
     * Clean system log files.
     */
    public function execute(): void
    {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile) && filesize($logFile) >= 1024 * 1024) {
            unlink($logFile);
        }

        if (function_exists('updateLastCronJob')) {
            updateLastCronJob(self::IDENTIFIER);
        }
    }
}
