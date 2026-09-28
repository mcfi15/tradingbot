@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
    <div class="min-h-screen relative space-y-12 pb-24">
        <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
        <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-emerald-500/5 via-cyan-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

        <div class="relative z-10 max-w-7xl mx-auto space-y-12">

            {{-- ══ HERO ══════════════════════════════════════════════════════════ --}}
            <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                     style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">

                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                        <div class="max-w-2xl space-y-4">
                            <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary">
                                <span class="w-1.5 h-1.5 rounded-full bg-accent-primary"></span>
                                <span>{{ __('Risk Engine') }}</span>
                            </div>

                            <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                                {{ __('Auto-Trading & Risk') }}
                            </h1>

                            <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                                {{ __('These limits are enforced on every automated order. Changes apply to the next trade, and each trade stores the settings that produced it.') }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl shrink-0">
                            <a href="{{ route('user.trading.preferences.index') }}"
                               class="px-5 py-2.5 rounded-full bg-accent-primary text-black font-black text-xs uppercase tracking-wider transition-all">
                                {{ __('Risk') }}
                            </a>
                            <a href="{{ route('user.trading.signals.index') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Signals') }}
                            </a>
                            <a href="{{ route('user.trading.orders.index') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Trade') }}
                            </a>
                            <a href="{{ route('user.trading.orders.logs') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Logs') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ CIRCUIT BREAKER ════════════════════════════════════════════════ --}}
            @if ($breaker['breached'])
                <div class="p-6 rounded-[2rem] bg-rose-500/10 border border-rose-500/25 flex items-start gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-rose-200">{{ __('Trading Paused') }}</h3>
                        <p class="text-[11px] text-rose-300/80 font-bold mt-1 leading-relaxed">
                            {{ $breaker['reason'] }}
                        </p>
                        <p class="text-[10px] text-rose-300/60 font-mono mt-2">
                            {{ __('Realised today:') }} {{ number_format((float) $breaker['realised'], 2) }} USDT
                        </p>
                    </div>
                </div>
            @endif

            {{-- ══ LIVE COUNTERS ══════════════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/8 space-y-1">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">
                        {{ __('Open Trades') }}
                    </span>
                    <div class="text-2xl font-black text-white font-mono">
                        {{ $stats['open_trades'] }}
                        <span class="text-sm text-slate-500">/ {{ $stats['max_concurrent_trades'] }}</span>
                    </div>
                    <div class="h-1.5 rounded-full bg-white/5 overflow-hidden mt-2">
                        @php
                            $usage = $stats['max_concurrent_trades'] > 0
                                ? min(100, ($stats['open_trades'] / $stats['max_concurrent_trades']) * 100)
                                : 0;
                        @endphp
                        <div class="h-full rounded-full {{ $usage >= 100 ? 'bg-rose-500' : 'bg-accent-primary' }} transition-all duration-500"
                            style="width: {{ $usage }}%"></div>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/8 space-y-1">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">
                        {{ __('Realised Today') }}
                    </span>
                    <div class="text-2xl font-black font-mono {{ (float) $stats['today_realised'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ number_format((float) $stats['today_realised'], 2) }}
                        <span class="text-sm text-slate-500">USDT</span>
                    </div>
                    <div class="text-[9px] text-slate-600 font-mono">
                        {{ __('Resets at 00:00 UTC') }}
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/8 space-y-1">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">
                        {{ __('Active Connections') }}
                    </span>
                    <div class="text-2xl font-black text-white font-mono">{{ count($stats['connections']) }}</div>
                    <div class="text-[9px] text-slate-600 font-mono">
                        {{ $stats['connections']->pluck('exchange')->map(fn ($e) => strtoupper($e))->join(', ') ?: __('None') }}
                    </div>
                </div>
            </div>

            {{-- ══ SETTINGS ══════════════════════════════════════════════════════ --}}
            <form id="risk-form" class="space-y-8">

                {{-- Auto-Trading Mode --}}
                <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-7 sm:p-9 relative overflow-hidden"
                         style="background: linear-gradient(135deg, rgba(15,17,21,0.98) 0%, rgba(8,9,14,0.99) 100%);">

                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                            <div class="max-w-xl">
                                <h2 class="text-lg font-black text-white tracking-tight">{{ __('Auto-Trading Mode') }}</h2>
                                <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed">
                                    {{ __('The master switch. When on, signals you Auto-Follow are placed automatically within the limits below.') }}
                                </p>
                            </div>

                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" name="auto_trading_mode" value="1" class="peer sr-only"
                                    @checked($preferences->auto_trading_mode)>
                                <span class="w-14 h-8 rounded-full bg-slate-700 peer-checked:bg-emerald-500 transition-colors relative"></span>
                                <span class="absolute left-1 w-6 h-6 rounded-full bg-white transition-all peer-checked:left-7"></span>
                                <span class="ml-4 text-[10px] font-black uppercase tracking-widest text-slate-400 peer-checked:text-emerald-300 transition-colors">
                                    {{ __('Enabled') }}
                                </span>
                            </label>
                        </div>

                        @if ($stats['connections']->isEmpty())
                            <p class="mt-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-[11px] font-bold text-amber-200">
                                {{ __('You need at least one active exchange connection before Auto-Trading Mode can be enabled.') }}
                                <a href="{{ route('user.trading.exchanges.index') }}" class="underline ml-1">{{ __('Connect one') }}</a>
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Position sizing --}}
                <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-7 sm:p-9 relative overflow-hidden"
                         style="background: linear-gradient(135deg, rgba(15,17,21,0.98) 0%, rgba(8,9,14,0.99) 100%);">

                        <h2 class="text-lg font-black text-white tracking-tight">{{ __('Position Sizing') }}</h2>
                        <p class="text-[11px] text-slate-500 mt-1.5 mb-8 leading-relaxed">
                            {{ __('How much of your wallet each automated trade is allowed to risk.') }}
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label for="risk-percentage" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500">
                                        {{ __('Risk Per Trade') }}
                                    </label>
                                    <span class="text-[10px] font-black text-accent-primary font-mono" id="risk-percentage-value">
                                        {{ number_format((float) $preferences->risk_percentage, 2) }}%
                                    </span>
                                </div>
                                <input type="number" id="risk-percentage" name="risk_percentage" step="0.01"
                                    min="{{ App\Models\UserTradingPreference::LIMITS['risk_percentage'][0] }}"
                                    max="{{ App\Models\UserTradingPreference::LIMITS['risk_percentage'][1] }}"
                                    value="{{ number_format((float) $preferences->risk_percentage, 2, '.', '') }}" required
                                    class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-mono font-bold text-white focus:border-accent-primary focus:outline-none">
                                <p class="text-[9px] text-slate-600 mt-2 leading-relaxed">
                                    {{ __('Applied to your available quote balance, then multiplied by leverage.') }}
                                </p>
                            </div>

                            <div>
                                <label for="max-trade-size" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                    {{ __('Max Trade Size') }} <span class="text-slate-600 normal-case tracking-normal">(USDT)</span>
                                </label>
                                <input type="number" id="max-trade-size" name="max_trade_size" step="0.01"
                                    min="{{ App\Models\UserTradingPreference::LIMITS['max_trade_size'][0] }}"
                                    max="{{ App\Models\UserTradingPreference::LIMITS['max_trade_size'][1] }}"
                                    value="{{ number_format((float) $preferences->max_trade_size, 2, '.', '') }}" required
                                    class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-mono font-bold text-white focus:border-accent-primary focus:outline-none">
                                <p class="text-[9px] text-slate-600 mt-2 leading-relaxed">
                                    {{ __('A hard ceiling. The risk percentage can never push a trade past this.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Targets --}}
                <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-7 sm:p-9 relative overflow-hidden"
                         style="background: linear-gradient(135deg, rgba(15,17,21,0.98) 0%, rgba(8,9,14,0.99) 100%);">

                        <h2 class="text-lg font-black text-white tracking-tight">{{ __('Targets') }}</h2>
                        <p class="text-[11px] text-slate-500 mt-1.5 mb-8 leading-relaxed">
                            {{ __('Used when a signal does not carry its own targets. A signal that does always wins.') }}
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="take-profit-percentage" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-emerald-400/80 mb-2">
                                    {{ __('Take Profit') }}
                                </label>
                                <input type="number" id="take-profit-percentage" name="take_profit_percentage" step="0.01"
                                    min="{{ App\Models\UserTradingPreference::LIMITS['take_profit_percentage'][0] }}"
                                    max="{{ App\Models\UserTradingPreference::LIMITS['take_profit_percentage'][1] }}"
                                    value="{{ number_format((float) $preferences->take_profit_percentage, 2, '.', '') }}" required
                                    class="w-full px-4 py-3 rounded-xl bg-emerald-500/[0.06] border border-emerald-500/20 text-sm font-mono font-bold text-white focus:border-emerald-400 focus:outline-none">
                                <p class="text-[9px] text-slate-600 mt-2 leading-relaxed">
                                    {{ __('Distance from entry, in your favour.') }}
                                </p>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label for="stop-loss-percentage" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-rose-400/80">
                                        {{ __('Stop Loss') }}
                                    </label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="auto_stop_loss" value="1" class="peer sr-only"
                                            @checked($preferences->auto_stop_loss)>
                                        <span class="w-9 h-5 rounded-full bg-slate-700 peer-checked:bg-rose-500 transition-colors relative"></span>
                                        <span class="absolute left-0.5 w-4 h-4 rounded-full bg-white transition-all peer-checked:left-[1.125rem]"></span>
                                    </label>
                                </div>
                                <input type="number" id="stop-loss-percentage" name="stop_loss_percentage" step="0.01"
                                    min="{{ App\Models\UserTradingPreference::LIMITS['stop_loss_percentage'][0] }}"
                                    max="{{ App\Models\UserTradingPreference::LIMITS['stop_loss_percentage'][1] }}"
                                    value="{{ number_format((float) $preferences->stop_loss_percentage, 2, '.', '') }}" required
                                    class="w-full px-4 py-3 rounded-xl bg-rose-500/[0.06] border border-rose-500/20 text-sm font-mono font-bold text-white focus:border-rose-400 focus:outline-none">
                                <p class="text-[9px] text-slate-600 mt-2 leading-relaxed">
                                    {{ __('Every automated trade carries a stop unless the signal supplies one.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Circuit breaker + slippage --}}
                <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-7 sm:p-9 relative overflow-hidden"
                         style="background: linear-gradient(135deg, rgba(15,17,21,0.98) 0%, rgba(8,9,14,0.99) 100%);">

                        <h2 class="text-lg font-black text-white tracking-tight">{{ __('Circuit Breaker') }}</h2>
                        <p class="text-[11px] text-slate-500 mt-1.5 mb-8 leading-relaxed">
                            {{ __('The rules that stop the engine, rather than the rules that size it.') }}
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="max-concurrent-trades" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                    {{ __('Max Concurrent Trades') }}
                                </label>
                                <input type="number" id="max-concurrent-trades" name="max_concurrent_trades" step="1"
                                    min="{{ App\Models\UserTradingPreference::LIMITS['max_concurrent_trades'][0] }}"
                                    max="{{ App\Models\UserTradingPreference::LIMITS['max_concurrent_trades'][1] }}"
                                    value="{{ $preferences->max_concurrent_trades }}" required
                                    class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-mono font-bold text-white focus:border-accent-primary focus:outline-none">
                                <p class="text-[9px] text-slate-600 mt-2 leading-relaxed">
                                    {{ __('Signals arriving while you are full up are skipped, not queued forever.') }}
                                </p>
                            </div>

                            <div>
                                <label for="max-daily-loss" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                    {{ __('Max Daily Loss') }}
                                </label>
                                <input type="number" id="max-daily-loss" name="max_daily_loss_percentage" step="0.1"
                                    min="{{ App\Models\UserTradingPreference::LIMITS['max_daily_loss_percentage'][0] }}"
                                    max="{{ App\Models\UserTradingPreference::LIMITS['max_daily_loss_percentage'][1] }}"
                                    value="{{ number_format((float) $preferences->max_daily_loss_percentage, 2, '.', '') }}" required
                                    class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-mono font-bold text-white focus:border-accent-primary focus:outline-none">
                                <p class="text-[9px] text-slate-600 mt-2 leading-relaxed">
                                    {{ __('Percent of wallet. Breaching it pauses the engine until tomorrow.') }}
                                </p>
                            </div>

                            <div>
                                <label for="slippage-tolerance" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                    {{ __('Slippage Tolerance') }}
                                </label>
                                <input type="number" id="slippage-tolerance" name="slippage_tolerance" step="0.01"
                                    min="{{ App\Models\UserTradingPreference::LIMITS['slippage_tolerance'][0] }}"
                                    max="{{ App\Models\UserTradingPreference::LIMITS['slippage_tolerance'][1] }}"
                                    value="{{ number_format((float) $preferences->slippage_tolerance, 2, '.', '') }}" required
                                    class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-mono font-bold text-white focus:border-accent-primary focus:outline-none">
                                <p class="text-[9px] text-slate-600 mt-2 leading-relaxed">
                                    {{ __('Percent. A market fill worse than this is treated as a failure.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Defaults --}}
                <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-7 sm:p-9 relative overflow-hidden"
                         style="background: linear-gradient(135deg, rgba(15,17,21,0.98) 0%, rgba(8,9,14,0.99) 100%);">

                        <h2 class="text-lg font-black text-white tracking-tight">{{ __('Defaults') }}</h2>
                        <p class="text-[11px] text-slate-500 mt-1.5 mb-8 leading-relaxed">
                            {{ __('Which credential the automated engine trades through when a signal does not name one.') }}
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="default-exchange" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                    {{ __('Preferred Exchange') }}
                                </label>
                                <select id="default-exchange" name="default_exchange"
                                    class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-bold text-white focus:border-accent-primary focus:outline-none">
                                    <option value="">{{ __('Any available') }}</option>
                                    @foreach (config('exchange.supported', []) as $slug)
                                        <option value="{{ $slug }}" @selected($preferences->default_exchange === $slug)>
                                            {{ strtoupper($slug) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="default-market" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                    {{ __('Default Market') }}
                                </label>
                                <select id="default-market" name="default_market_type"
                                    class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-bold text-white focus:border-accent-primary focus:outline-none">
                                    <option value="spot" @selected($preferences->default_market_type === 'spot')>{{ __('Spot') }}</option>
                                    <option value="futures" @selected($preferences->default_market_type === 'futures')>{{ __('Futures / Perp') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Save --}}
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <p class="text-[10px] text-slate-600 max-w-md leading-relaxed">
                        {{ __('Limits are read again at the moment each automated order is placed, so a change here affects the next trade rather than the ones already resting.') }}
                    </p>

                    <button type="submit" id="risk-submit"
                        class="px-8 py-4 rounded-xl bg-accent-primary text-black font-black text-[10px] uppercase tracking-widest hover:bg-accent-primary/90 active:scale-95 transition-all shadow-[0_0_25px_rgba(226,177,60,0.25)]">
                        {{ __('Save Risk Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const TOKEN = "{{ csrf_token() }}";
            const ENDPOINT = "{{ route('user.trading.preferences.update') }}";

            function notify(message, type) {
                if (typeof toastNotification === 'function') {
                    toastNotification(message, type || 'success');
                }
            }

            function errorMessage(xhr, fallback) {
                const body = xhr.responseJSON;

                if (body && body.errors) {
                    const first = Object.values(body.errors)[0];
                    if (first && first.length) return first[0];
                }

                return (body && body.message) || fallback || "{{ __('Something went wrong.') }}";
            }

            // Live echo of the risk slider so the number is never a guess.
            $('#risk-percentage').on('input', function() {
                $('#risk-percentage-value').text(parseFloat($(this).val() || 0).toFixed(2) + '%');
            });

            $('#risk-form').on('submit', function(e) {
                e.preventDefault();

                const $submit = $('#risk-submit');

                $submit.prop('disabled', true)
                    .html('<span class="inline-block w-4 h-4 border-2 border-black border-r-transparent rounded-full animate-spin"></span>');

                // Unchecked checkboxes are absent from a form submit, so the
                // booleans are set explicitly. Sending them as 1/0 keeps the
                // controller's boolean validation rule satisfied.
                const payload = {
                    auto_trading_mode: $('input[name="auto_trading_mode"]').is(':checked') ? 1 : 0,
                    default_exchange: $('#default-exchange').val() || null,
                    default_market_type: $('#default-market').val(),
                    max_trade_size: $('#max-trade-size').val(),
                    risk_percentage: $('#risk-percentage').val(),
                    auto_stop_loss: $('input[name="auto_stop_loss"]').is(':checked') ? 1 : 0,
                    stop_loss_percentage: $('#stop-loss-percentage').val(),
                    take_profit_percentage: $('#take-profit-percentage').val(),
                    max_concurrent_trades: $('#max-concurrent-trades').val(),
                    max_daily_loss_percentage: $('#max-daily-loss').val(),
                    slippage_tolerance: $('#slippage-tolerance').val(),
                };

                axios.post(ENDPOINT, payload, {
                    headers: { 'X-CSRF-TOKEN': TOKEN }
                }).then(function(res) {
                    notify(res.data.message, 'success');

                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                }).catch(function(xhr) {
                    $submit.prop('disabled', false).text("{{ __('Save Risk Settings') }}");
                    notify(errorMessage(xhr), 'error');
                });
            });
        });
    </script>
@endsection
