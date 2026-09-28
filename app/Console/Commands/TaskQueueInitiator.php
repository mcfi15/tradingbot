<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TaskQueueInitiator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foyana:queue-initiate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiate cron jobs for local development';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->components->info('Foyana Local Cron Ecosystem');

        $this->line('  <bg=blue;fg=white> INFO </> Local Scheduler is active.');
        $this->line('  <bg=yellow;fg=black> WARN </> Simulating system cron every 60 seconds.');
        $this->line('  <bg=red;fg=white> STOP </> Press Ctrl+C to terminate the process.');
        $this->newLine();

        while (true) {
            $startTime = microtime(true);
            $timestamp = now()->toDateTimeString();
            $outputBuffer = new \Symfony\Component\Console\Output\BufferedOutput();

            $this->components->task(
                "<fg=gray>$timestamp</> <fg=blue>foyana:scheduler-daemon</>",
                function () use ($outputBuffer) {
                    \Illuminate\Support\Facades\Artisan::call('foyana:scheduler-daemon', ['-v' => true], $outputBuffer);
                    return true;
                }
            );

            $output = trim($outputBuffer->fetch());

            // Display all output for debugging
            if ($output) {
                $lines = explode("\n", $output);
                foreach ($lines as $line) {
                    $cleanLine = trim($line);
                    if ($cleanLine) {
                        $this->line("    <fg=gray>│</> <fg=magenta>LOG:</> $cleanLine");
                    }
                }
            } else {
                $this->line("    <fg=gray>│</> <fg=gray>IDLE: No commands executed this cycle.</>");
            }

            $duration = round(microtime(true) - $startTime, 2);
            $this->line("  <fg=gray>└ Cycle duration: {$duration}s. Sleeping...</>");
            $this->newLine();

            sleep(10);
        }
    }
}
