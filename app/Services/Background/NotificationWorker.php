<?php

namespace App\Services\Background;

use App\Models\NotificationMessage;
use Illuminate\Support\Facades\Log;

/**
 * Notification Cleaner Worker
 *
 * Prunes old, already-read notification records from the database
 * when enabled in site settings to maintain high database query speed.
 */
class NotificationWorker
{
    /**
     * Unique identifier for health tracking in cron_jobs table.
     */
    public const IDENTIFIER = 'notification_cleaner';

    /**
     * Delete read notification messages.
     */
    public function execute(): void
    {
        if (getSetting('delete_notification_message') === 'disabled') {
            return;
        }

        NotificationMessage::where('status', 'read')->delete();

        if (function_exists('updateLastCronJob')) {
            updateLastCronJob(self::IDENTIFIER);
        }
    }
}
