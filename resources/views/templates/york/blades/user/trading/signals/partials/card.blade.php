{{--
    One signal card.

    Expects:
        $signal  array from TradingSignal::toFeedArray() (plus a "follow" key
                 added by SignalController::cardPayload for the initial render)
        $follow  array|null  follow state keyed by signal id, for the
                             server-rendered pass. Null-safe.

    The same markup is rendered twice over: once by the server on page load, and
    once by the client for signals that arrive over the WebSocket or through the
    poll fallback. Keep it free of PHP state that JS cannot supply.
--}}
@php
    $isLong = $signal['is_long'] ?? in_array($signal['direction'] ?? '', ['buy', 'long'], true);
    $side = $signal['side'] ?? ($isLong ? 'buy' : 'sell');
    $followState = $signal['follow'] ?? ($follow[$signal['id']] ?? null);
    $isAuto = ($followState['mode'] ?? null) === 'auto';
    $isManual = ($followState['mode'] ?? null) === 'manual';
    $reward = $signal['reward_pct'] ?? [];

    $tone = $isLong
        ? ['text' => 'text-emerald-400', 'bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20']
        : ['text' => 'text-rose-400', 'bg' => 'bg-rose-500/10', 'border' => 'border-rose-500/20'];

    $levels = [
        ['label' => 'TP1', 'price' => $signal['target_1'] ?? null, 'key' => 'target_1'],
        ['label' => 'TP2', 'price' => $signal['target_2'] ?? null, 'key' => 'target_2'],
        ['label' => 'TP3', 'price' => $signal['target_3'] ?? null, 'key' => 'target_3'],
    ];
@endphp

<article class="signal-card group relative rounded-[2rem] p-1.5 bg-white/[0.03] border border-white/10 transition-all duration-300 hover:border-accent-primary/40"
    data-signal-id="{{ $signal['id'] }}">

    <div class="rounded-[calc(2rem-0.375rem)] p-6 relative overflow-hidden"
        style="background: linear-gradient(135deg, rgba(15,17,21,0.98) 0%, rgba(8,9,14,0.99) 100%);">

        {{-- Direction wash --}}
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full blur-3xl pointer-events-none {{ $isLong ? 'bg-emerald-500/10' : 'bg-rose-500/10' }}"></div>

        <div class="relative z-10 space-y-6">

            {{-- ══ Header ══════════════════════════════════════════════════════ --}}
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-xl font-black text-white font-mono tracking-tight">
                            {{ $signal['display_pair'] ?? $signal['pair'] }}
                        </h3>

                        <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border {{ $tone['bg'] }} {{ $tone['text'] }} {{ $tone['border'] }}">
                            {{ strtoupper($signal['direction'] ?? $side) }}
                        </span>

                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest {{ ($signal['market_type'] ?? 'spot') === 'futures' ? 'bg-purple-400/10 text-purple-400 border border-purple-400/20' : 'bg-blue-400/10 text-blue-400 border border-blue-400/20' }}">
                            {{ $signal['market_type'] ?? 'spot' }}
                        </span>

                        @if (($signal['source'] ?? null) && $signal['source'] !== 'manual')
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-white/5 text-slate-400 border border-white/10">
                                {{ $signal['source'] }}
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-3 mt-1.5 text-[9px] text-slate-500 font-mono uppercase tracking-widest">
                        <span>{{ \Carbon\Carbon::createFromTimestamp((int) $signal['signal_time'])->diffForHumans() }}</span>

                        @if (($signal['confidence'] ?? 0) > 0)
                            <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                            <span>{{ __('Confidence') }} {{ number_format((float) $signal['confidence'], 0) }}%</span>
                        @endif

                        @if (!empty($signal['expires_at']))
                            <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                            <span class="text-amber-500/80">
                                {{ __('Expires') }} {{ \Carbon\Carbon::createFromTimestamp((int) $signal['expires_at'])->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">
                        {{ __('Entry') }}
                    </span>
                    <div class="text-lg font-black text-white font-mono tracking-tight">
                        {{ number_format((float) $signal['entry_price'], 8) }}
                    </div>
                </div>
            </div>

            {{-- ══ Targets ══════════════════════════════════════════════════════ --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach ($levels as $level)
                    @if ($level['price'] !== null)
                        @php $pct = $reward[$level['key']] ?? null; @endphp
                        <div class="p-3.5 rounded-2xl bg-emerald-500/[0.06] border border-emerald-500/15">
                            <div class="text-[9px] font-black uppercase tracking-widest text-emerald-400/80">
                                {{ $level['label'] }}
                            </div>
                            <div class="text-[11px] font-black text-white font-mono mt-0.5 break-all">
                                {{ number_format((float) $level['price'], 8) }}
                            </div>
                            @if ($pct !== null)
                                <div class="text-[9px] font-bold text-emerald-400 font-mono mt-0.5">
                                    {{ $pct > 0 ? '+' : '' }}{{ number_format((float) $pct, 2) }}%
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach

                @if (($signal['stop_loss'] ?? null) !== null)
                    @php $slPct = $reward['stop_loss'] ?? null; @endphp
                    <div class="p-3.5 rounded-2xl bg-rose-500/[0.06] border border-rose-500/15">
                        <div class="text-[9px] font-black uppercase tracking-widest text-rose-400/80">
                            {{ __('Stop Loss') }}
                        </div>
                        <div class="text-[11px] font-black text-white font-mono mt-0.5 break-all">
                            {{ number_format((float) $signal['stop_loss'], 8) }}
                        </div>
                        @if ($slPct !== null)
                            <div class="text-[9px] font-bold text-rose-400 font-mono mt-0.5">
                                {{ number_format((float) $slPct, 2) }}%
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- ══ Follow state ══════════════════════════════════════════════════ --}}
            {{-- Hidden by default and revealed by the JS once it knows the follow
                 state, because a card rebuilt on the client has to re-derive this
                 from the follow map rather than assume it. --}}
            <div class="follow-state {{ ($isAuto || $isManual || !empty($followState['error_message'])) ? '' : 'hidden' }}">
                @if ($isAuto)
                    <div class="flex items-start gap-2.5 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="text-[10px] font-bold text-emerald-200 leading-relaxed">
                            {{ __('Auto-Follow is on. The automated engine is handling this signal.') }}
                        </p>
                    </div>
                @elseif ($isManual)
                    <div class="flex items-start gap-2.5 p-3.5 rounded-2xl bg-blue-500/10 border border-blue-500/20">
                        <svg class="w-4 h-4 text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8L11 17l-4-4-6 6" />
                        </svg>
                        <p class="text-[10px] font-bold text-blue-200 leading-relaxed">
                            {{ __('You took this trade manually from the signal.') }}
                        </p>
                    </div>
                @endif

                @if (!empty($followState['error_message']))
                    <p class="mt-2 text-[10px] font-bold text-rose-300 font-mono break-words">
                        {{ $followState['error_message'] }}
                    </p>
                @endif
            </div>

            {{-- ══ Actions ══════════════════════════════════════════════════════ --}}
            <div class="flex flex-wrap items-center gap-3 pt-1">
                <button type="button" class="btn-trade-now px-5 py-2.5 rounded-xl bg-accent-primary text-black font-black text-[10px] uppercase tracking-widest hover:bg-accent-primary/90 active:scale-95 transition-all shadow-[0_0_20px_rgba(226,177,60,0.25)] flex items-center gap-2"
                    data-signal-id="{{ $signal['id'] }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13 7h8m0 0v8m0-8L11 17l-4-4-6 6" />
                    </svg>
                    {{ __('Trade Now') }}
                </button>

                {{-- The toggle is a switch, not a button, because it is a persistent
                     mode rather than a one-shot action. --}}
                <button type="button" class="btn-auto-follow px-5 py-2.5 rounded-xl border font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 flex items-center gap-2.5
                    {{ $isAuto
                        ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300'
                        : 'bg-white/5 border-white/10 text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    data-signal-id="{{ $signal['id'] }}"
                    data-following="{{ $isAuto ? '1' : '0' }}">

                    <span class="relative inline-block w-8 h-4 rounded-full transition-colors {{ $isAuto ? 'bg-emerald-500' : 'bg-slate-600' }}">
                        <span class="absolute top-0.5 w-3 h-3 rounded-full bg-white transition-all {{ $isAuto ? 'left-4' : 'left-0.5' }}"></span>
                    </span>

                    <span class="btn-label">{{ $isAuto ? __('Auto-Follow On') : __('Auto-Follow Signal') }}</span>
                </button>

                @if ($followState && !empty($followState['order_id']))
                    <a href="{{ route('user.trading.orders.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-white/10 hover:text-white transition-all">
                        {{ __('View Position') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</article>
