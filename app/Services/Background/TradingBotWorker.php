<?php

namespace App\Services\Background;

use App\Models\TradingBotActivation;
use App\Models\TradingBotLog;
use App\Traits\ReferralBonusTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Trading Bots Engine Worker
 *
 * Executes active trading bot activations, updates cycle resets,
 * calculates daily profit returns, and distributes capital when completed.
 */
class TradingBotWorker
{
    use ReferralBonusTrait;

    /**
     * Unique identifier for health tracking in cron_jobs table.
     */
    public const IDENTIFIER = 'trading_bots_engine';

    /**
     * Execute the trading bot management routine.
     */
    public function execute(): void
    {
        // Check if trading bot module is enabled
        if (!moduleEnabled('trading_bot_module')) {
            return;
        }

        // 1. Update activations that have completed their run duration
        $this->updateCompletedActivations();

        // 2. Reset and calculate today's cycle counts for active bots
        $this->updateTodayCycleCount();

        // 3. Distribute today's profits for ready activations
        $this->returnTodayProfit();

        if (function_exists('updateLastCronJob')) {
            updateLastCronJob(self::IDENTIFIER);
        }
    }

    /**
     * Complete activations that have reached their scheduled end date.
     */
    protected function updateCompletedActivations(): void
    {
        TradingBotActivation::with('bot', 'user')
            ->where('status', 'active')
            ->where('end_date', '<', now()->timestamp)
            ->chunkById(100, function ($activations) {
                /** @var TradingBotActivation $activation */
                foreach ($activations as $activation) {
                    try {
                        DB::transaction(function () use ($activation) {
                            $user = $activation->user;
                            $bot = $activation->bot;

                            if (!$user || !$bot) {
                                return;
                            }

                            // Return the principal capital to user balance if enabled
                            if ($bot->is_capital_returned) {
                                $user->balance += $activation->amount;
                                $user->save();

                                // Record transaction for capital return
                                $ref = 'BOT-CAP-' . strtoupper(Str::random(10));
                                $desc = __('Capital return from completed bot :bot', ['bot' => $bot->name], $user->lang);
                                recordTransaction(
                                    $user,
                                    (string) $activation->amount,
                                    getSetting('currency'),
                                    (string) $activation->amount,
                                    getSetting('currency'),
                                    1,
                                    'credit',
                                    'completed',
                                    $ref,
                                    $desc,
                                    (string) $user->balance
                                );
                            }

                            // Mark as completed
                            $activation->status = 'completed';
                            $activation->save();

                            // Notify user
                            $title = 'Trading Bot Completed';
                            if ($bot->is_capital_returned) {
                                $message = __('Your trading bot :bot_name has completed its scheduled run. Your capital of :amount has been returned to your balance. Total profit earned: :profit', [
                                    'bot_name' => $bot->name,
                                    'amount' => showAmount($activation->amount),
                                    'profit' => showAmount($activation->returned_profit),
                                ], $user->lang);
                            } else {
                                $message = __('Your trading bot :bot_name has completed its scheduled run. Total profit earned: :profit', [
                                    'bot_name' => $bot->name,
                                    'profit' => showAmount($activation->returned_profit),
                                ], $user->lang);
                            }
                            recordNotificationMessage($user, $title, $message);
                        });
                    } catch (\Throwable $e) {
                        Log::error("Bot Completion Error [Activation: {$activation->id}]: " . $e->getMessage(), [
                            'activation_id' => $activation->id,
                        ]);
                    }
                }
            });
    }

    /**
     * Calculate and update cycle counts for all active activations for the current trading day.
     */
    protected function updateTodayCycleCount(): void
    {
        TradingBotActivation::with('bot', 'user')
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('today_cycle_reset_at')
                    ->orWhere('today_cycle_reset_at', '<', now()->startOfDay()->timestamp);
            })
            ->chunkById(100, function ($activations) {
                /** @var TradingBotActivation $activation */
                foreach ($activations as $activation) {
                    try {
                        if (!$activation->bot) {
                            continue;
                        }

                        $trading_days = $activation->bot->trading_days ?? [];
                        $today = date('l');
                        if (!in_array($today, $trading_days)) {
                            continue;
                        }

                        $next_profit_date = $this->calculateNextProfitDate($activation);

                        // Generate random return with 2 decimal places
                        $min = (float) ($activation->bot->daily_return_min ?? 0);
                        $max = (float) ($activation->bot->daily_return_max ?? 0);
                        $daily_return = rand((int) ($min * 100), (int) ($max * 100)) / 100;

                        $today_amount = 0;
                        while ($today_amount < 30) {
                            $calculated_amount = $activation->amount * rand(1, 7) / 100;
                            $today_amount += $calculated_amount;
                        }

                        $leverage = $activation->bot?->type === 'crypto' ? rand(3, 10) : rand(50, 500);
                        $today_amount_roi = ($activation->amount * $daily_return) / ($today_amount * $leverage);

                        $activation->setAttribute('today_roi', (string) $daily_return);
                        $activation->setAttribute('today_amount', (string) $today_amount);
                        $activation->setAttribute('today_amount_roi', (string) $today_amount_roi);
                        $activation->setAttribute('leverage', $leverage);
                        $activation->today_cycle_reset_at = now()->timestamp;
                        $activation->next_profit_date = $next_profit_date;
                        $activation->save();
                    } catch (\Throwable $e) {
                        Log::error("Cycle Count Error [Activation: {$activation->id}]: " . $e->getMessage(), [
                            'activation_id' => $activation->id,
                        ]);
                    }
                }
            });
    }

    /**
     * Calculate the next profit distribution timestamp based on bot market type.
     */
    protected function calculateNextProfitDate($activation): int
    {
        $botType = $activation->bot?->type ?? 'crypto';

        if ($botType === 'crypto') {
            $now = now();
            $endHour = 22; // Crypto window ends at 10 PM
            if ($now->hour >= $endHour) {
                return $now->copy()->addDay()->hour(rand(8, $endHour))->minute(rand(0, 59))->timestamp;
            }
            return $now->copy()->hour(rand($now->hour + 1, $endHour))->minute(rand(0, 59))->timestamp;
        }

        // Forex: Based on NYSE market hours (9:30 AM - 4:00 PM ET)
        $nyTime = now()->setTimezone('America/New_York');
        $marketOpenHour = 9;
        $marketCloseHour = 16;

        if ($nyTime->isWeekend() || $nyTime->hour >= $marketCloseHour) {
            $nextTradingDay = $nyTime->copy()->addDay();
            while ($nextTradingDay->isWeekend()) {
                $nextTradingDay->addDay();
            }
            return $nextTradingDay->hour(rand($marketOpenHour + 1, $marketCloseHour - 1))
                ->minute(rand(0, 59))
                ->setTimezone(config('app.timezone', 'UTC'))
                ->timestamp;
        }

        if ($nyTime->hour < $marketOpenHour || ($nyTime->hour == $marketOpenHour && $nyTime->minute < 30)) {
            return $nyTime->copy()->hour(rand($marketOpenHour + 1, $marketCloseHour - 1))
                ->minute(rand(0, 59))
                ->setTimezone(config('app.timezone', 'UTC'))
                ->timestamp;
        }

        $currentHour = $nyTime->hour;
        if ($currentHour >= $marketCloseHour - 1) {
            $nextDay = $nyTime->copy()->addDay();
            while ($nextDay->isWeekend()) {
                $nextDay->addDay();
            }
            return $nextDay->hour(rand($marketOpenHour + 1, $marketCloseHour - 1))
                ->minute(rand(0, 59))
                ->setTimezone(config('app.timezone', 'UTC'))
                ->timestamp;
        }

        return $nyTime->copy()
            ->hour(rand($currentHour + 1, $marketCloseHour - 1))
            ->minute(rand(0, 59))
            ->setTimezone(config('app.timezone', 'UTC'))
            ->timestamp;
    }

    /**
     * Distribute profits for ready activations.
     */
    protected function returnTodayProfit(): void
    {
        $startOfToday = now()->startOfDay()->timestamp;

        TradingBotActivation::with('bot', 'user')
            ->where('status', 'active')
            ->whereNotNull('next_profit_date')
            ->where('next_profit_date', '<', now()->timestamp)
            ->where(function ($query) use ($startOfToday) {
                $query->whereNull('last_profit_date')
                    ->orWhere('last_profit_date', '<', $startOfToday);
            })
            ->chunkById(100, function ($activations) {
                /** @var TradingBotActivation $activation */
                foreach ($activations as $activation) {
                    try {
                        DB::transaction(function () use ($activation) {
                            $user = $activation->user;
                            $bot = $activation->bot;

                            if (!$user || !$bot || empty($bot->traded_pairs)) {
                                return;
                            }
                            if ($bot->type === 'crypto' && empty($bot->exchanges)) {
                                return;
                            }

                            $trading_days = $bot->trading_days ?? [];
                            $today = date('l');
                            if (!in_array($today, $trading_days)) {
                                return;
                            }

                            if ($bot->type === 'forex') {
                                $nyTime = now()->setTimezone('America/New_York');
                                $marketOpenHour = 9;
                                $marketCloseHour = 16;
                                if ($nyTime->isWeekend() || $nyTime->hour >= $marketCloseHour || $nyTime->hour < $marketOpenHour) {
                                    return;
                                }
                            }

                            $trading_pair = $bot->traded_pairs[array_rand($bot->traded_pairs)];
                            $exchange = $bot->type === 'crypto' ? $bot->exchanges[array_rand($bot->exchanges)] : null;

                            $price_data = $this->getPriceData($trading_pair, $bot->type);
                            if (!is_array($price_data) || empty($price_data)) {
                                return;
                            }

                            $current_price = $price_data['current_price'] ?? 0;
                            $price_30_minutes_ago = $price_data['price_30_minutes_ago'] ?? 0;

                            $current_price_conversion = rateConverter($current_price, 'USDT', getSetting('currency'), 'bot_trading');
                            $current_price_converted = $current_price_conversion['converted_amount'];

                            $direction = $bot->type == 'crypto' ? 'long' : 'buy';
                            if ($current_price < $price_30_minutes_ago) {
                                $direction = $bot->type == 'crypto' ? 'short' : 'sell';
                            }

                            $profit_percentage = $activation->today_amount_roi;
                            $amount = $activation->today_amount;
                            $leverage = $activation->leverage;

                            $profit = (string) (($amount * $profit_percentage * $leverage) / 100);

                            // Update user balance
                            $user->balance += (float) $profit;
                            $user->save();

                            // Referral bonus on daily profit
                            try {
                                $this->giveReferralBonus($user, $profit);
                            } catch (\Throwable $e) {
                                Log::error('Trading Bot Referral Bonus Error: ' . $e->getMessage());
                            }

                            // Record transaction
                            $ref = 'BOT-PR-' . strtoupper(Str::random(10));
                            $desc = __('Trading profit from :bot', ['bot' => $bot->name], $user->lang);
                            recordTransaction(
                                $user,
                                (string) $profit,
                                getSetting('currency'),
                                (string) $profit,
                                getSetting('currency'),
                                1,
                                'credit',
                                'completed',
                                $ref,
                                $desc,
                                (string) $user->balance
                            );

                            // Record trading log
                            TradingBotLog::create([
                                'user_id' => $user->id,
                                'trading_bot_activation_id' => $activation->id,
                                'trading_pair' => $trading_pair,
                                'exchange' => $exchange,
                                'type' => $bot->type,
                                'amount' => $amount,
                                'profit' => $profit,
                                'profit_percentage' => $profit_percentage,
                                'exit_time' => $activation->next_profit_date,
                                'exit_price' => $current_price_converted,
                                'direction' => $direction,
                                'leverage' => $leverage,
                            ]);

                            // Update activation
                            $activation->returned_profit += (float) $profit;
                            $activation->last_profit_date = now()->timestamp;
                            $activation->save();

                            // Notify user
                            $title = 'Trading Profit Distributed';
                            $msg = __('Your bot :bot has generated a profit of :profit today.', ['bot' => $bot->name, 'profit' => showAmount($profit)], $user->lang);
                            recordNotificationMessage($user, $title, $msg);
                        });
                    } catch (\Throwable $e) {
                        Log::error("Profit Distribution Error [Activation: {$activation->id}]: " . $e->getMessage(), [
                            'activation_id' => $activation->id,
                        ]);
                    }
                }
            });
    }

    /**
     * Retrieve live price data for bot calculation.
     */
    protected function getPriceData($trading_pair, $type)
    {
        try {
            $baseUrl = rtrim(config('site.setup_server_url'), '/');
            if (!$baseUrl) {
                return false;
            }
            $url = "{$baseUrl}/api/v1/bots/market-data/{$type}/{$trading_pair}";
            $license_key = safeDecrypt(config('site.product_key'));

            $headers = [
                'x-license-key' => $license_key,
                'x-domain' => \App\Services\LicensingEngine\VmRuntime::resolveDomain(),
                'x-version' => config('site.version'),
            ];
            $response = Http::timeout(10)->withHeaders($headers)->get($url);

            if ($response->failed()) {
                return false;
            }

            return $response->json('data');
        } catch (\Throwable $e) {
            Log::error('Bot Market Data Fetch Error: ' . $e->getMessage());
            return false;
        }
    }
}
