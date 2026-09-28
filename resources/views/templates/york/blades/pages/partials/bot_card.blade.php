@php
    $minRoi = (float)($bot->daily_return_min ?? 1.0);
    $maxRoi = (float)($bot->daily_return_max ?? 3.5);
    $avgRoi = ($minRoi + $maxRoi) / 2;
    $durationDays = $bot->duration ?? 30;
    $durationType = ucfirst($bot->duration_type ?? 'day');
    $pairs = is_array($bot->traded_pairs) ? $bot->traded_pairs : [];
    $exchanges = is_array($bot->exchanges) ? $bot->exchanges : [];
    $tradingDays = is_array($bot->trading_days) ? $bot->trading_days : [];
@endphp

<div class="bot-card group relative rounded-3xl p-[1px] bg-gradient-to-b from-white/[0.12] via-white/[0.04] to-transparent hover:from-cyan-500/40 hover:via-cyan-500/10 hover:to-transparent transition-all duration-500 transform hover:-translate-y-1.5 hover:shadow-[0_20px_50px_rgba(0,245,255,0.08)] flex flex-col h-full"
    data-type="{{ strtolower($bot->type) }}" 
    data-roi="{{ $avgRoi }}"
    data-markets='{{ json_encode($pairs) }}' 
    data-name="{{ strtolower($bot->name) }}"
    data-id="{{ $bot->id }}">

    {{-- Internal Double-Bezel Hardened Surface --}}
    <div class="relative bg-[#090c14]/95 rounded-[calc(1.5rem-1px)] p-6 sm:p-7 h-full flex flex-col justify-between overflow-hidden backdrop-blur-2xl">
        
        {{-- Ambient Corner Glow --}}
        <div class="absolute -top-16 -right-16 w-36 h-36 bg-cyan-500/10 group-hover:bg-cyan-500/20 rounded-full blur-2xl pointer-events-none transition-all duration-700"></div>
        <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-emerald-500/5 group-hover:bg-emerald-500/15 rounded-full blur-2xl pointer-events-none transition-all duration-700"></div>

        <div>
            {{-- Top HUD Bar: Category, ID & Live Status --}}
            <div class="flex items-center justify-between gap-2 mb-5 relative z-10">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white/[0.04] border border-white/[0.08] text-[10px] font-mono font-bold uppercase tracking-widest text-slate-300 group-hover:text-cyan-300 group-hover:border-cyan-500/30 transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full {{ $bot->type === 'crypto' ? 'bg-cyan-400' : 'bg-emerald-400' }}"></span>
                        {{ $bot->type }}
                    </span>
                    <span class="text-[9px] font-mono font-bold text-slate-500 uppercase tracking-widest">
                        #{{ str_pad($bot->id, 3, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-mono font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ __('ACTIVE') }}
                </div>
            </div>

            {{-- Bot Avatar & Name Header --}}
            <div class="flex items-start gap-4 mb-5 relative z-10">
                <div class="relative w-12 h-12 rounded-2xl bg-gradient-to-br from-white/10 to-white/[0.02] p-[1px] shrink-0 group-hover:from-cyan-500/50 group-hover:to-indigo-500/50 transition-all duration-500">
                    <div class="w-full h-full rounded-[15px] bg-[#050507] p-2 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('assets/images/bots/' . $bot->logo) }}" 
                             alt="{{ $bot->name }}" 
                             loading="lazy"
                             onerror="this.src='{{ asset('assets/templates/york/images/avatar.png') }}'" 
                             class="w-full h-full object-contain filter group-hover:scale-110 transition-transform duration-500">
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-white group-hover:text-cyan-300 transition-colors truncate tracking-tight mb-1">
                        {{ $bot->name }}
                    </h3>
                    <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">
                        {{ $bot->description ?? __('Automated trading bot designed to capture daily profits across crypto and forex markets 24/7.') }}
                    </p>
                </div>
            </div>

            {{-- Visual Mini Yield Trajectory Curve --}}
            <div class="mb-5 p-3 rounded-2xl bg-black/40 border border-white/[0.04] relative overflow-hidden">
                <div class="flex items-center justify-between text-[10px] font-mono text-slate-400 mb-1.5">
                    <span>{{ __('Estimated Growth') }}</span>
                    <span class="text-emerald-400 font-bold flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        +{{ number_format($avgRoi * 7, 1) }}% {{ __('7-Day Est.') }}
                    </span>
                </div>
                <div class="h-9 w-full">
                    <svg class="w-full h-full" viewBox="0 0 100 25" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="curveGrad{{ $bot->id }}" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#10b981" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <path d="M0 22 Q 25 18, 45 14 T 75 8 T 100 3 L 100 25 L 0 25 Z" fill="url(#curveGrad{{ $bot->id }})"/>
                        <path d="M0 22 Q 25 18, 45 14 T 75 8 T 100 3" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            {{-- Core Metrics Grid (HUD Matrix) --}}
            <div class="grid grid-cols-2 gap-2.5 mb-5 font-mono text-xs relative z-10">
                <div class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.06] group-hover:border-cyan-500/20 transition-colors">
                    <span class="text-[9px] uppercase tracking-wider text-slate-500 block mb-1">{{ __('Daily Profit') }}</span>
                    <span class="text-sm font-black text-cyan-300">
                        {{ $minRoi }}% - {{ $maxRoi }}%
                    </span>
                </div>

                <div class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.06] group-hover:border-cyan-500/20 transition-colors">
                    <span class="text-[9px] uppercase tracking-wider text-slate-500 block mb-1">{{ __('Min / Max Deposit') }}</span>
                    <span class="text-sm font-black text-white">
                        ${{ number_format($bot->min_amount, 0) }} - ${{ number_format($bot->max_amount, 0) }}
                    </span>
                </div>

                <div class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.06]">
                    <span class="text-[9px] uppercase tracking-wider text-slate-500 block mb-1">{{ __('Duration') }}</span>
                    <span class="text-xs font-bold text-slate-300">
                        {{ $durationDays }} {{ $durationType }}(s)
                    </span>
                </div>

                <div class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.06]">
                    <span class="text-[9px] uppercase tracking-wider text-slate-500 block mb-1">{{ __('Capital Return') }}</span>
                    <span class="text-xs font-bold {{ $bot->is_capital_returned ? 'text-emerald-400' : 'text-slate-400' }}">
                        {{ $bot->is_capital_returned ? __('100% Capital Returned') : __('Included in Daily Payouts') }}
                    </span>
                </div>
            </div>

            {{-- Traded Pairs Chips & Exchange Badges --}}
            <div class="space-y-3 mb-6 relative z-10">
                @if (!empty($pairs))
                    <div>
                        <div class="text-[9px] font-mono uppercase tracking-widest text-slate-500 mb-1.5">{{ __('Traded Pairs') }}</div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach (array_slice($pairs, 0, 4) as $pair)
                                <span class="px-2 py-0.5 rounded bg-white/[0.04] border border-white/[0.08] text-[10px] font-mono font-bold text-slate-300 group-hover:text-white group-hover:border-cyan-500/30 transition-all">
                                    {{ $pair }}
                                </span>
                            @endforeach
                            @if (count($pairs) > 4)
                                <span class="px-2 py-0.5 rounded bg-white/[0.02] border border-white/[0.06] text-[9px] font-mono text-slate-500">
                                    +{{ count($pairs) - 4 }} more
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($bot->type === 'crypto' && !empty($exchanges))
                    <div>
                        <div class="text-[9px] font-mono uppercase tracking-widest text-slate-500 mb-1.5">{{ __('Supported Exchanges') }}</div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @foreach (array_slice($exchanges, 0, 4) as $exch)
                                <span class="px-2 py-0.5 rounded-md bg-cyan-500/5 border border-cyan-500/15 text-[9px] font-mono text-cyan-400">
                                    {{ $exch }}
                                </span>
                            @endforeach
                            @if (count($exchanges) > 4)
                                <span class="text-[9px] font-mono text-slate-500">
                                    +{{ count($exchanges) - 4 }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Action CTA Bar --}}
        <div class="pt-4 border-t border-white/[0.06] relative z-10">
            <a href="{{ route('user.trading-bots.index') }}" 
               class="group/btn relative flex items-center justify-between w-full px-5 py-3.5 rounded-2xl bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-slate-950 font-mono font-bold text-xs uppercase tracking-widest shadow-[0_0_20px_rgba(0,245,255,0.2)] hover:shadow-[0_0_30px_rgba(0,245,255,0.4)] transition-all duration-300 active:scale-[0.98]">
                <span>{{ __('Start Bot') }}</span>
                <span class="w-7 h-7 rounded-full bg-black/20 flex items-center justify-center transform group-hover/btn:translate-x-1 transition-transform">
                    <svg class="w-3.5 h-3.5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            </a>
        </div>
    </div>
</div>
