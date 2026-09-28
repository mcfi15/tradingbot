<?php

namespace App\Http\Controllers\User\Trading;

use App\Http\Controllers\Controller;
use App\Services\RiskManager;
use App\Models\ExchangeConnection;
use App\Models\TradeOrder;
use App\Models\UserTradingPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Risk-rule settings for the automated engine.
 *
 * The values here are what ExecuteAutoTradeJob enforces. Editing them mid-flight
 * is safe: the job re-reads preferences at execution time, and each executed
 * trade stores the snapshot that produced it.
 */
class TradingPreferenceController extends Controller
{
    public function __construct(protected RiskManager $risk)
    {
    }

    public function index()
    {
        $page_title = __('Auto-Trading & Risk');
        $template = config('site.template');

        $user = Auth::user();

        $preferences = UserTradingPreference::forUser((int) $user->id);

        $breaker = $this->risk->dailyLossBreached($user, $preferences);

        $stats = [
            'open_trades' => TradeOrder::where('user_id', $user->id)->open()->count(),
            'max_concurrent_trades' => (int) $preferences->max_concurrent_trades,
            'today_realised' => $breaker['realised'],
            'daily_loss_breached' => $breaker['breached'],
            'connections' => ExchangeConnection::where('user_id', $user->id)
                ->where('is_active', true)
                ->get()
                ->map(fn (ExchangeConnection $c) => $c->toDisplayArray())
                ->values(),
        ];

        return view("templates.{$template}.blades.user.trading.preferences.index", compact(
            'page_title',
            'preferences',
            'stats',
            'breaker'
        ));
    }

    /**
     * Persist the risk rules.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $limits = UserTradingPreference::LIMITS;

        $validated = $request->validate([
            'auto_trading_mode' => ['nullable', 'boolean'],
            'default_exchange' => ['nullable', Rule::in(config('exchange.supported', []))],
            'default_market_type' => ['nullable', Rule::in(['spot', 'futures'])],
            'max_trade_size' => ['required', 'numeric', 'min:0', 'max:' . $limits['max_trade_size'][1]],
            'risk_percentage' => ['required', 'numeric', 'min:' . $limits['risk_percentage'][0], 'max:' . $limits['risk_percentage'][1]],
            'auto_stop_loss' => ['nullable', 'boolean'],
            'stop_loss_percentage' => ['required', 'numeric', 'min:' . $limits['stop_loss_percentage'][0], 'max:' . $limits['stop_loss_percentage'][1]],
            'take_profit_percentage' => ['required', 'numeric', 'min:' . $limits['take_profit_percentage'][0], 'max:' . $limits['take_profit_percentage'][1]],
            'max_concurrent_trades' => ['required', 'integer', 'min:' . $limits['max_concurrent_trades'][0], 'max:' . $limits['max_concurrent_trades'][1]],
            'max_daily_loss_percentage' => ['required', 'numeric', 'min:' . $limits['max_daily_loss_percentage'][0], 'max:' . $limits['max_daily_loss_percentage'][1]],
            'slippage_tolerance' => ['required', 'numeric', 'min:' . $limits['slippage_tolerance'][0], 'max:' . $limits['slippage_tolerance'][1]],
        ]);

        $preferences = UserTradingPreference::forUser((int) $user->id);

        $preferences->fill([
            'auto_trading_mode' => $request->boolean('auto_trading_mode'),
            'default_exchange' => $validated['default_exchange'] ?? null,
            'default_market_type' => $validated['default_market_type'] ?? 'spot',
            'max_trade_size' => $validated['max_trade_size'],
            'risk_percentage' => $validated['risk_percentage'],
            'auto_stop_loss' => $request->boolean('auto_stop_loss'),
            'stop_loss_percentage' => $validated['stop_loss_percentage'],
            'take_profit_percentage' => $validated['take_profit_percentage'],
            'max_concurrent_trades' => $validated['max_concurrent_trades'],
            'max_daily_loss_percentage' => $validated['max_daily_loss_percentage'],
            'slippage_tolerance' => $validated['slippage_tolerance'],
        ]);

        $preferences->save();

        // Enabling auto-trading without a usable connection is a trap: signals
        // would queue and fail one at a time. Refuse and say why.
        if ($preferences->auto_trading_mode) {
            $hasConnection = ExchangeConnection::where('user_id', $user->id)
                ->where('is_active', true)
                ->where('market_type', $preferences->default_market_type)
                ->exists();

            if (! $hasConnection) {
                return response()->json([
                    'success' => false,
                    'message' => __('Add an active :market exchange connection before turning on Auto-Trading Mode.', [
                        'market' => $preferences->default_market_type,
                    ]),
                ], 422);
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('Risk settings saved.'),
            'data' => $preferences->fresh()->toDisplayArray(),
        ]);
    }

    /**
     * Toggle auto-trading without editing the whole form. This is the switch on
     * the signal feed header.
     */
    public function toggleAutoMode(Request $request)
    {
        $user = Auth::user();

        $preferences = UserTradingPreference::forUser((int) $user->id);

        $enabled = ! $preferences->auto_trading_mode;

        if ($enabled) {
            $hasConnection = ExchangeConnection::where('user_id', $user->id)
                ->where('is_active', true)
                ->exists();

            if (! $hasConnection) {
                return response()->json([
                    'success' => false,
                    'message' => __('Connect an exchange before turning on Auto-Trading Mode.'),
                    'auto_trading_mode' => false,
                ], 422);
            }
        }

        $preferences->auto_trading_mode = $enabled;
        $preferences->save();

        return response()->json([
            'success' => true,
            'auto_trading_mode' => $enabled,
            'message' => $enabled
                ? __('Auto-Trading Mode is on. Signals you follow will be executed automatically.')
                : __('Auto-Trading Mode is off. No new automated trades will be placed.'),
        ]);
    }

    /**
     * Current risk state for the header widget, without a full page load.
     */
    public function status()
    {
        $user = Auth::user();

        $preferences = UserTradingPreference::forUser((int) $user->id);
        $breaker = $this->risk->dailyLossBreached($user, $preferences);

        return response()->json([
            'success' => true,
            'data' => array_merge($preferences->toDisplayArray(), [
                'open_trades' => TradeOrder::where('user_id', $user->id)->open()->count(),
                'today_realised' => $breaker['realised'],
                'daily_loss_breached' => $breaker['breached'],
                'daily_loss_message' => $breaker['reason'],
            ]),
        ]);
    }
}
