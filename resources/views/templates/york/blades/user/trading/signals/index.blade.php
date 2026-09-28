@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
    <div class="min-h-screen relative space-y-12 pb-24">
        <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
        <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-emerald-500/5 via-cyan-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

        <div class="relative z-10 max-w-7xl mx-auto space-y-12">

            {{-- ══ HERO + AUTO-TRADING TOGGLE ═══════════════════════════════════ --}}
            <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                     style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">

                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                        <div class="max-w-2xl space-y-4">
                            <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary">
                                {{-- The dot is the live socket indicator. It turns
                                     grey when the feed has fallen back to polling,
                                     so a silent WebSocket never looks like a live
                                     feed. --}}
                                <span id="feed-status-dot" class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span id="feed-status-label">{{ __('Live Signals') }}</span>
                            </div>

                            <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                                {{ __('Trading Signals') }}
                            </h1>

                            <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                                {{ __('Trade these manually, or switch on Auto-Follow and let the risk engine size and place each one for you.') }}
                            </p>
                        </div>

                        <div class="flex flex-col items-start lg:items-end gap-4 shrink-0">

                            {{-- Auto-Trading Mode master switch --}}
                            <button type="button" id="auto-mode-toggle"
                                class="px-6 py-3.5 rounded-2xl border font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 flex items-center gap-3
                                {{ $preferences->auto_trading_mode
                                    ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300'
                                    : 'bg-white/5 border-white/10 text-slate-300 hover:bg-white/10' }}"
                                data-enabled="{{ $preferences->auto_trading_mode ? '1' : '0' }}">

                                <span class="relative inline-block w-11 h-5 rounded-full transition-colors {{ $preferences->auto_trading_mode ? 'bg-emerald-500' : 'bg-slate-600' }}">
                                    <span class="absolute top-0.5 w-4 h-4 rounded-full bg-white transition-all {{ $preferences->auto_trading_mode ? 'left-6' : 'left-0.5' }}"></span>
                                </span>

                                <span id="auto-mode-label">
                                    {{ $preferences->auto_trading_mode ? __('Auto-Trading On') : __('Auto-Trading Off') }}
                                </span>
                            </button>

                            @unless ($preferences->auto_trading_mode)
                                <p class="text-[10px] text-slate-500 max-w-[16rem] text-left lg:text-right leading-relaxed">
                                    {{ __('Auto-Follow needs Auto-Trading Mode on and an active exchange connection.') }}
                                </p>
                            @endunless
                        </div>
                    </div>

                    {{-- Floating island nav --}}
                    <div class="relative z-10 mt-8 flex flex-wrap items-center gap-2.5 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl w-fit">
                        <a href="{{ route('user.trading.signals.index') }}"
                           class="px-5 py-2.5 rounded-full bg-accent-primary text-black font-black text-xs uppercase tracking-wider transition-all">
                            {{ __('Feed') }}
                        </a>
                        <a href="{{ route('user.trading.orders.index') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                            {{ __('Trade') }}
                        </a>
                        <a href="{{ route('user.trading.orders.logs') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                            {{ __('Logs') }}
                        </a>
                        <a href="{{ route('user.trading.preferences.index') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                            {{ __('Risk') }}
                        </a>
                        <a href="{{ route('user.trading.exchanges.index') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                            {{ __('Exchanges') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- ══ FILTERS ═══════════════════════════════════════════════════════ --}}
            <form method="GET" action="{{ route('user.trading.signals.index') }}" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="filter-market" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                            {{ __('Market') }}
                        </label>
                        <select id="filter-market" name="market_type"
                            class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-bold text-white focus:border-accent-primary focus:outline-none">
                            <option value="">{{ __('All') }}</option>
                            <option value="spot" @selected($marketType === 'spot')>{{ __('Spot') }}</option>
                            <option value="futures" @selected($marketType === 'futures')>{{ __('Futures / Perp') }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="filter-direction" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                            {{ __('Direction') }}
                        </label>
                        <select id="filter-direction" name="direction"
                            class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-bold text-white focus:border-accent-primary focus:outline-none">
                            <option value="">{{ __('All') }}</option>
                            @foreach (['buy' => __('Buy'), 'sell' => __('Sell'), 'long' => __('Long'), 'short' => __('Short')] as $value => $label)
                                <option value="{{ $value }}" @selected($direction === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter-pair" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                            {{ __('Pair') }}
                        </label>
                        <input type="text" id="filter-pair" name="pair" value="{{ $pair }}"
                            placeholder="BTCUSDT"
                            class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-mono font-bold text-white placeholder:text-slate-600 focus:border-accent-primary focus:outline-none uppercase">
                    </div>

                    <div class="flex items-end gap-3">
                        <button type="submit"
                            class="flex-1 px-5 py-3 rounded-xl bg-accent-primary text-black font-black text-[10px] uppercase tracking-widest hover:bg-accent-primary/90 active:scale-95 transition-all">
                            {{ __('Filter') }}
                        </button>

                        @if ($marketType || $direction || $pair)
                            <a href="{{ route('user.trading.signals.index') }}"
                                class="px-5 py-3 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:bg-white/10 transition-all">
                                {{ __('Reset') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- ══ FEED ══════════════════════════════════════════════════════════ --}}
            <div class="space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h2 class="text-xl font-black text-white tracking-tight">
                        {{ __('Latest Signals') }}
                        <span id="feed-count" class="ml-2 px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-black text-slate-400 font-mono">
                            {{ $signals->total() }}
                        </span>
                    </h2>

                    <div id="open-trades-badge"
                        class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-400">
                        {{ __('Open Trades') }} <span class="text-accent-primary font-mono ml-1">{{ $openTrades }}</span>
                    </div>
                </div>

                {{-- Cards land here. Server-rendered first, then extended in place
                     by the WebSocket/poll path so the feed never jumps. --}}
                <div id="signal-feed" class="space-y-6">
                    @forelse ($signals as $signal)
                        @include('templates.' . config('site.template') . '.blades.user.trading.signals.partials.card', [
                            'signal' => $signal->toFeedArray() + ['follow' => $follows[$signal->id] ?? null],
                            'follow' => $follows,
                        ])
                    @empty
                        <div class="rounded-[2rem] p-1.5 bg-white/[0.03] border border-white/10 max-w-lg mx-auto text-center">
                            <div class="rounded-[calc(2rem-0.375rem)] p-12">
                                <div class="w-16 h-16 mx-auto rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-white mb-2">{{ __('No Signals Yet') }}</h3>
                                <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                                    {{ __('Signals will appear here as soon as they are published.') }}
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if ($signals->hasPages())
                    <div class="flex justify-center">
                        {{ $signals->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const TOKEN = "{{ csrf_token() }}";
            const FEED = $('#signal-feed');

            const ENDPOINTS = {
                poll: "{{ route('user.trading.signals.poll') }}",
                autoMode: "{{ route('user.trading.preferences.toggle-auto-mode') }}",
                status: "{{ route('user.trading.preferences.status') }}",
                orders: "{{ route('user.trading.orders.index') }}",
            };

            // The signal feed is live over a WebSocket when one is available and
            // polls otherwise. Both paths funnel through ingest(), so the card
            // markup and de-duplication logic exist in exactly one place.
            const BROADCAST = {
                enabled: {{ $broadcastEnabled ? 'true' : 'false' }},
                key: @json($broadcastKey),
                channel: 'private-user.trading-signals.{{ auth()->id() }}',
            };

            let cursor = {{ (int) $cursor }};
            const seen = new Set();

            // Seed the seen set with what the server already rendered, otherwise
            // the first poll replays the whole page as new cards.
            FEED.find('.signal-card').each(function() {
                seen.add($(this).data('signal-id'));
            });

            // ── Helpers ───────────────────────────────────────────────────────
            //
            // Both signal routes put the id in the *middle* of the path
            // (/{id}/ticket, /{id}/follow), so it cannot be located by a regex
            // hunting for trailing digits. A named token is substituted instead:
            // it is unambiguous, survives route() unescaped, and cannot be
            // confused with the digits of a port or a query string.
            const ID_TOKEN = '__ID__';

            const ticketUrl = "{{ route('user.trading.signals.ticket', ['id' => '__ID__']) }}";
            const followUrl = "{{ route('user.trading.signals.follow', ['id' => '__ID__']) }}";

            const withId = (template, id) => template.replace(ID_TOKEN, encodeURIComponent(id));

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

            function setStatus(mode, label) {
                const dot = $('#feed-status-dot');
                const text = $('#feed-status-label');

                if (mode === 'live') {
                    dot.removeClass('bg-slate-600').addClass('bg-emerald-400 animate-pulse');
                } else {
                    dot.removeClass('bg-emerald-400 animate-pulse').addClass('bg-amber-400');
                }

                text.text(label);
            }

            // ── Card rendering ────────────────────────────────────────────────
            // A card arriving over the socket has to be built in the browser,
            // so this mirrors signals/partials/card.blade.php. The two must be
            // kept in step: a card that looks different depending on how it
            // arrived is worse than either.
            function renderCard(signal) {
                const follow = (window.__followMap || {})[signal.id] || null;
                const isLong = signal.is_long;
                const isAuto = follow && follow.mode === 'auto';
                const isManual = follow && follow.mode === 'manual';

                const tone = isLong
                    ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
                    : 'bg-rose-500/10 text-rose-400 border-rose-500/20';

                const reward = signal.reward_pct || {};

                const level = (label, price, key) => {
                    if (price === null || price === undefined) return '';

                    const pct = reward[key];
                    const pctLabel = pct === undefined || pct === null
                        ? ''
                        : `<div class="text-[9px] font-bold text-emerald-400 font-mono mt-0.5">${pct > 0 ? '+' : ''}${Number(pct).toFixed(2)}%</div>`;

                    return `<div class="p-3.5 rounded-2xl bg-emerald-500/[0.06] border border-emerald-500/15">
                        <div class="text-[9px] font-black uppercase tracking-widest text-emerald-400/80">${label}</div>
                        <div class="text-[11px] font-black text-white font-mono mt-0.5 break-all">${fmt(price, 8)}</div>
                        ${pctLabel}
                    </div>`;
                };

                const stopLoss = signal.stop_loss === null || signal.stop_loss === undefined
                    ? ''
                    : `<div class="p-3.5 rounded-2xl bg-rose-500/[0.06] border border-rose-500/15">
                        <div class="text-[9px] font-black uppercase tracking-widest text-rose-400/80">{{ __('Stop Loss') }}</div>
                        <div class="text-[11px] font-black text-white font-mono mt-0.5 break-all">${fmt(signal.stop_loss, 8)}</div>
                        ${reward.stop_loss === undefined || reward.stop_loss === null
                            ? ''
                            : `<div class="text-[9px] font-bold text-rose-400 font-mono mt-0.5">${Number(reward.stop_loss).toFixed(2)}%</div>`}
                    </div>`;

                let followBlock = '';

                if (isAuto) {
                    followBlock = `<div class="flex items-start gap-2.5 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
                        <p class="text-[10px] font-bold text-emerald-200 leading-relaxed">{{ __('Auto-Follow is on. The automated engine is handling this signal.') }}</p>
                    </div>`;
                } else if (isManual) {
                    followBlock = `<div class="flex items-start gap-2.5 p-3.5 rounded-2xl bg-blue-500/10 border border-blue-500/20">
                        <p class="text-[10px] font-bold text-blue-200 leading-relaxed">{{ __('You took this trade manually from the signal.') }}</p>
                    </div>`;
                }

                if (follow && follow.error_message) {
                    followBlock += `<p class="mt-2 text-[10px] font-bold text-rose-300 font-mono break-words">${esc(follow.error_message)}</p>`;
                }

                const elapsed = signal.signal_time
                    ? relativeTime(signal.signal_time)
                    : '';

                return `
                <article class="signal-card group relative rounded-[2rem] p-1.5 bg-white/[0.03] border border-white/10 transition-all duration-300 hover:border-accent-primary/40" data-signal-id="${signal.id}">
                    <div class="rounded-[calc(2rem-0.375rem)] p-6 relative overflow-hidden" style="background: linear-gradient(135deg, rgba(15,17,21,0.98) 0%, rgba(8,9,14,0.99) 100%);">
                        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full blur-3xl pointer-events-none ${isLong ? 'bg-emerald-500/10' : 'bg-rose-500/10'}"></div>
                        <div class="relative z-10 space-y-6">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-xl font-black text-white font-mono tracking-tight">${esc(signal.display_pair || signal.pair)}</h3>
                                        <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border ${tone}">${String(signal.direction || '').toUpperCase()}</span>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest ${signal.market_type === 'futures' ? 'bg-purple-400/10 text-purple-400 border border-purple-400/20' : 'bg-blue-400/10 text-blue-400 border border-blue-400/20'}">${esc(signal.market_type || 'spot')}</span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 mt-1.5 text-[9px] text-slate-500 font-mono uppercase tracking-widest">
                                        <span>${elapsed}</span>
                                        ${signal.confidence > 0 ? `<span class="w-1 h-1 rounded-full bg-slate-700"></span><span>{{ __('Confidence') }} ${Number(signal.confidence).toFixed(0)}%</span>` : ''}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Entry') }}</span>
                                    <div class="text-lg font-black text-white font-mono tracking-tight">${fmt(signal.entry_price, 8)}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                ${level('TP1', signal.target_1, 'target_1')}
                                ${level('TP2', signal.target_2, 'target_2')}
                                ${level('TP3', signal.target_3, 'target_3')}
                                ${stopLoss}
                            </div>

                            <div class="follow-state ${followBlock ? '' : 'hidden'}">${followBlock}</div>

                            <div class="flex flex-wrap items-center gap-3 pt-1">
                                <button type="button" class="btn-trade-now px-5 py-2.5 rounded-xl bg-accent-primary text-black font-black text-[10px] uppercase tracking-widest hover:bg-accent-primary/90 active:scale-95 transition-all shadow-[0_0_20px_rgba(226,177,60,0.25)] flex items-center gap-2" data-signal-id="${signal.id}">
                                    {{ __('Trade Now') }}
                                </button>
                                <button type="button" class="btn-auto-follow px-5 py-2.5 rounded-xl border font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 flex items-center gap-2.5 ${isAuto ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' : 'bg-white/5 border-white/10 text-slate-300 hover:bg-white/10 hover:text-white'}" data-signal-id="${signal.id}" data-following="${isAuto ? '1' : '0'}">
                                    <span class="relative inline-block w-8 h-4 rounded-full transition-colors ${isAuto ? 'bg-emerald-500' : 'bg-slate-600'}">
                                        <span class="absolute top-0.5 w-3 h-3 rounded-full bg-white transition-all ${isAuto ? 'left-4' : 'left-0.5'}"></span>
                                    </span>
                                    <span class="btn-label">${isAuto ? "{{ __('Auto-Follow On') }}" : "{{ __('Auto-Follow Signal') }}"}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>`;
            }

            function fmt(value, decimals) {
                const n = parseFloat(value);

                if (!isFinite(n)) return '0';

                return n.toLocaleString(undefined, {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: decimals
                });
            }

            function esc(value) {
                return $('<div>').text(value === null || value === undefined ? '' : String(value)).html();
            }

            function relativeTime(unix) {
                const seconds = Math.floor(Date.now() / 1000) - parseInt(unix, 10);

                if (seconds < 60) return "{{ __('just now') }}";
                if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
                if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';

                return Math.floor(seconds / 86400) + 'd ago';
            }

            // ── Push new signals in ───────────────────────────────────────────
            function accept(payload) {
                const incoming = Array.isArray(payload) ? payload : [payload];
                const fresh = [];

                incoming.forEach(function(signal) {
                    if (!signal || !signal.id || seen.has(signal.id)) return;

                    seen.add(signal.id);
                    fresh.push(signal);
                });

                if (fresh.length === 0) return;

                if (FEED.find('.empty-state').length || FEED.children().length === 0) {
                    FEED.empty();
                }

                const html = fresh.reverse().map(renderCard).join('');

                FEED.prepend(html);

                const $count = $('#feed-count');
                const total = (parseInt($count.text(), 10) || 0) + fresh.length;
                $count.text(total);

                notify("{{ __('New signal received.') }}", 'info');
            }

            // ── WebSocket, with a polling fallback ────────────────────────────
            function startPolling() {
                setStatus('poll', "{{ __('Live Updates (Polling)') }}");

                setInterval(function() {
                    axios.get(ENDPOINTS.poll, {
                        params: { cursor: cursor, limit: 20 },
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }).then(function(res) {
                        const data = res.data.data || {};

                        cursor = data.cursor || cursor;
                        window.__followMap = data.follows || window.__followMap || {};

                        if (data.signals && data.signals.length) {
                            accept(data.signals);
                        }

                        if (typeof data.active_order_count === 'number') {
                            $('#open-trades-badge span').text(data.active_order_count);
                        }

                        // An order that closed on a TP/SL while the tab was
                        // backgrounded only shows up in the poll payload.
                        if (data.orders && data.orders.length) {
                            data.orders.forEach(function(order) {
                                if (order.status === 'closed' || order.status === 'rejected' || order.status === 'cancelled') {
                                    notify(
                                        order.pair + ' ' + String(order.status).toUpperCase(),
                                        order.status === 'closed' ? 'success' : 'warning'
                                    );
                                }
                            });
                        }
                    }).catch(function() {
                        // A dropped poll is not worth a toast every 20 seconds;
                        // the next successful one silently recovers.
                        setStatus('poll', "{{ __('Reconnecting…') }}");
                    });
                }, 20000);
            }

            if (BROADCAST.enabled) {
                try {
                    const wire = new window.Echo.private(BROADCAST.channel);

                    wire.listen('.trading.signal.created', function(event) {
                        setStatus('live', "{{ __('Live Signals') }}");
                        accept(event.signal || (event.payload || {}).signal || event);
                    });

                    setStatus('live', "{{ __('Live Signals') }}");
                } catch (e) {
                    startPolling();
                }
            } else {
                startPolling();
            }

            // ── Auto-Trading Mode master switch ───────────────────────────────
            $('#auto-mode-toggle').on('click', function() {
                const $button = $(this);

                $button.prop('disabled', true);

                axios.post(ENDPOINTS.autoMode, {}, {
                    headers: { 'X-CSRF-TOKEN': TOKEN }
                }).then(function(res) {
                    const on = !!res.data.auto_trading_mode;

                    $button
                        .data('enabled', on ? '1' : '0')
                        .removeClass('bg-emerald-500/10 border-emerald-500/30 text-emerald-300 bg-white/5 border-white/10 text-slate-300')
                        .addClass(on
                            ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300'
                            : 'bg-white/5 border-white/10 text-slate-300');

                    $button.find('span > span').toggleClass('bg-emerald-500', on).toggleClass('bg-slate-600', !on)
                        .toggleClass('left-6', on).toggleClass('left-0.5', !on);

                    $('#auto-mode-label').text(on ? "{{ __('Auto-Trading On') }}" : "{{ __('Auto-Trading Off') }}");
                    notify(res.data.message, on ? 'success' : 'info');
                }).catch(function(xhr) {
                    notify(errorMessage(xhr), 'error');
                }).finally(function() {
                    $button.prop('disabled', false);
                });
            });

            // ── Auto-Follow toggle ────────────────────────────────────────────
            $(document).on('click', '.btn-auto-follow', function() {
                const $button = $(this);
                const id = $button.data('signal-id');

                $button.prop('disabled', true);

                axios.post(withId(followUrl, id), {}, {
                    headers: { 'X-CSRF-TOKEN': TOKEN }
                }).then(function(res) {
                    const on = !!res.data.following;

                    window.__followMap = window.__followMap || {};
                    window.__followMap[id] = on
                        ? { mode: 'auto', status: (res.data.data || {}).status, error_message: (res.data.data || {}).error_message }
                        : null;

                    $button
                        .data('following', on ? '1' : '0')
                        .removeClass(on
                            ? 'bg-white/5 border-white/10 text-slate-300'
                            : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300')
                        .addClass(on
                            ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300'
                            : 'bg-white/5 border-white/10 text-slate-300');

                    $button.find('span > span').toggleClass('bg-emerald-500', on).toggleClass('bg-slate-600', !on)
                        .toggleClass('left-4', on).toggleClass('left-0.5', !on);

                    $button.find('.btn-label').text(on ? "{{ __('Auto-Follow On') }}" : "{{ __('Auto-Follow Signal') }}");

                    // The follow banner is derived server-side, so the card is
                    // re-rendered from the updated map rather than patched here.
                    $button.closest('.signal-card').find('.follow-state').toggleClass('hidden', !on);

                    notify(res.data.message, 'success');
                }).catch(function(xhr) {
                    const body = xhr.responseJSON || {};

                    // Auto mode off or no connection: the server says exactly
                    // which prerequisite is missing, so link straight to it.
                    if (body.requires_auto_mode) {
                        notify(body.message, 'warning');

                        Swal.fire({
                            title: "{{ __('Auto-Trading Mode is off') }}",
                            text: body.message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: "{{ __('Open Risk Settings') }}",
                            cancelButtonText: "{{ __('Cancel') }}",
                            background: 'rgba(31, 31, 34, 0.95)',
                            customClass: {
                                popup: 'backdrop-blur-xl border border-white/10 rounded-2xl',
                                confirmButton: 'rounded-xl font-black text-xs uppercase tracking-widest',
                                cancelButton: 'rounded-xl font-black text-xs uppercase tracking-widest',
                            }
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('user.trading.preferences.index') }}";
                            }
                        });

                        return;
                    }

                    if (body.requires_connection) {
                        notify(body.message, 'warning');

                        Swal.fire({
                            title: "{{ __('No exchange connected') }}",
                            text: body.message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: "{{ __('Connect Exchange') }}",
                            cancelButtonText: "{{ __('Cancel') }}",
                            background: 'rgba(31, 31, 34, 0.95)',
                            customClass: {
                                popup: 'backdrop-blur-xl border border-white/10 rounded-2xl',
                                confirmButton: 'rounded-xl font-black text-xs uppercase tracking-widest',
                                cancelButton: 'rounded-xl font-black text-xs uppercase tracking-widest',
                            }
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('user.trading.exchanges.index') }}";
                            }
                        });

                        return;
                    }

                    notify(errorMessage(xhr), 'error');
                }).finally(function() {
                    $button.prop('disabled', false);
                });
            });

            // ── Trade Now → order ticket ──────────────────────────────────────
            $(document).on('click', '.btn-trade-now', function() {
                const $button = $(this);
                const id = $button.data('signal-id');

                $button.prop('disabled', true);

                axios.get(withId(ticketUrl, id), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(function(res) {
                    window.location.href = ENDPOINTS.orders +
                        '?pair=' + encodeURIComponent(res.data.data.pair) +
                        '&market_type=' + encodeURIComponent(res.data.data.market_type) +
                        '&side=' + encodeURIComponent(res.data.data.side) +
                        '&signal_id=' + encodeURIComponent(id) +
                        '&price=' + encodeURIComponent(res.data.data.price) +
                        '&take_profit=' + encodeURIComponent(res.data.data.take_profit_price || '') +
                        '&stop_loss=' + encodeURIComponent(res.data.data.stop_loss_price || '') +
                        '&leverage=' + encodeURIComponent(res.data.data.leverage);
                }).catch(function(xhr) {
                    notify(errorMessage(xhr), 'error');
                }).finally(function() {
                    $button.prop('disabled', false);
                });
            });
        });
    </script>
@endsection
