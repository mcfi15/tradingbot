<?php

namespace App\Services\Background;

use App\Models\CopyTrading;
use App\Models\CopyTradingHistory;
use App\Services\FuturesServices;
use App\Traits\ReferralBonusTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Copy Trading Synchronizer Worker
 *
 * Monitors active copy trading positions, processes completed trades,
 * calculates and credits investor returns and referral shares, and generates demo signals in sandbox.
 */
class CopyTradingWorker
{
    use ReferralBonusTrait;

    /**
     * Unique identifier for health tracking in cron_jobs table.
     */
    public const IDENTIFIER = 'copy_trading_sync';

    /**
     * Execute copy trade settlement routine.
     */
    public function execute(): void
    {
        if (!moduleEnabled('copy_trading_module')) {
            return;
        }

        // Generate sandbox demo signals if needed
        if (config('app.env') === 'sandbox') {
            $this->generateCopyTradingCodes();
        }

        // Process completed copy trades
        $this->processCompletedTrades();

        if (function_exists('updateLastCronJob')) {
            updateLastCronJob(self::IDENTIFIER);
        }
    }

    /**
     * Process activations that have reached completion timestamp.
     */
    protected function processCompletedTrades(): void
    {
        CopyTradingHistory::with('user')
            ->where('status', 'active')
            ->whereNotNull('completes_at')
            ->where('completes_at', '<=', now()->timestamp)
            ->chunkById(100, function ($activations) {
                /** @var CopyTradingHistory $activation */
                foreach ($activations as $activation) {
                    try {
                        DB::transaction(function () use ($activation) {
                            $user = $activation->user;
                            if (!$user) {
                                return;
                            }

                            $profit = (float) ($activation->amount * ($activation->roi / 100));
                            $totalReturn = (float) ($activation->amount + $profit);

                            $user->balance += $totalReturn;
                            $user->save();

                            // Referral bonus on profit
                            try {
                                $this->giveReferralBonus($user, (string) $profit);
                            } catch (\Throwable $e) {
                                Log::error('Copy Trade Referral Bonus Error: ' . $e->getMessage());
                            }

                            // Update history record
                            $activation->status = 'completed';
                            $activation->profit = $profit;
                            $activation->completed_at = now();
                            $activation->save();

                            // Record transaction
                            $ref = 'COPY-COMPL-' . strtoupper(Str::random(10));
                            $desc = __('Return of capital and profit from completed copy trade :code', ['code' => $activation->copy_code], $user->lang);

                            recordTransaction(
                                $user,
                                (string) $totalReturn,
                                getSetting('currency'),
                                (string) $totalReturn,
                                getSetting('currency'),
                                1,
                                'credit',
                                'completed',
                                $ref,
                                $desc,
                                (string) $user->balance
                            );

                            // Record notification
                            $title = __('Copy Trade Completed');
                            $message = __("Your copy trade :code has completed. Your capital of :amount and profit of :profit have been returned to your balance.", [
                                'code' => $activation->copy_code,
                                'amount' => showAmount($activation->amount),
                                'profit' => showAmount($profit),
                            ], $user->lang);

                            recordNotificationMessage($user, $title, $message);
                        });
                    } catch (\Throwable $e) {
                        Log::error("Copy Trade Completion Error [History: {$activation->id}]: " . $e->getMessage(), [
                            'history_id' => $activation->id,
                        ]);
                    }
                }
            });
    }

    /**
     * Generate synthetic signals when running in sandbox environment.
     */
    protected function generateCopyTradingCodes(): void
    {
        $activeCount = CopyTrading::active()->count();
        if ($activeCount >= 3) {
            return;
        }

        $futuresServices = new FuturesServices();
        $tickersResponse = $futuresServices->futureTickers();

        if (!$tickersResponse || ($tickersResponse['status'] ?? null) !== 'success' || empty($tickersResponse['data'])) {
            return;
        }

        $pairs = array_map(fn($item) => $item['ticker'], $tickersResponse['data']);
        if (empty($pairs)) {
            return;
        }

        for ($i = 0; $i < 5; $i++) {
            $code = strtoupper(Str::random(6));
            while (CopyTrading::where('code', $code)->exists()) {
                $code = strtoupper(Str::random(6));
            }

            CopyTrading::create([
                'code' => $code,
                'pair' => $pairs[array_rand($pairs)],
                'roi' => rand(1500, 9700) / 100, // 15% to 97%
                'amount_type' => 'percentage',
                'percentage' => rand(200, 500) / 100, // 2% to 5%
                'expires_at' => now()->addMinutes(rand(5, 15))->timestamp,
            ]);
        }
    }
}
