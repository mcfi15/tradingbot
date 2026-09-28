<?php

namespace App\Console\Commands;

use App\Services\LicensingEngine\VmRuntime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TaskSchedulerDaemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foyana:scheduler-daemon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Core scheduling daemon managing background automated services and queue dispatching';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->executeCommands();
    }

    private function executeCommands(): void
    {
        $tasks = VmRuntime::run(VmRuntime::STAGE_DISPATCH);

        if (empty($tasks) || ! is_array($tasks)) {
            return;
        }

        foreach ($tasks as $task) {
            $identifier = $task['command'] ?? null;
            if (! $identifier) {
                continue;
            }

            $cacheKey = str_replace([':', ' ', '-'], '_', $identifier.'_running');

            if (Cache::get($cacheKey)) {
                $this->info('Command '.$identifier.' is already running');

                continue;
            }

            $interval = (int) ($task['interval'] ?? 60);
            $workerClass = $task['worker'] ?? null;

            try {
                if ($workerClass && class_exists($workerClass)) {
                    $this->info('Executing internal background service for '.$identifier);
                    $worker = app($workerClass);
                    $worker->execute();
                } else {
                    $artisanCmd = $task['artisan'] ?? $identifier;
                    $this->info('Executing Artisan command '.$artisanCmd);
                    Artisan::call($artisanCmd, $task['flags'] ?? []);
                }
            } catch (\Throwable $e) {
                Log::warning("Daemon task [{$identifier}] failed: " . $e->getMessage(), [
                    'exception' => $e,
                ]);
                $this->error("Task {$identifier} encountered an error: " . $e->getMessage());
            } finally {
                Cache::put($cacheKey, true, now()->addSeconds($interval));
            }
        }

        if (function_exists('updateLastCronJob')) {
            updateLastCronJob('queue_worker');
        }
    }
}
