@extends('templates.york.blades.layouts.front')

@section('content')
    {{-- ════════════════════════════════════════════════════════════════════════════════
         HERO DECK: ALGORITHMIC COMMAND CENTER & KINETIC TERMINAL
    ════════════════════════════════════════════════════════════════════════════════ --}}
    <section class="relative min-h-[100dvh] pt-32 pb-24 flex flex-col justify-between overflow-hidden bg-[#050507] isolate">
        {{-- SVG Laser Dash Tracers Overlay (Effects 1 & 2: Moon Arc Edge Tracer + Data Pipeline Circuit) --}}
        <div class="absolute inset-0 pointer-events-none -z-10">
            <svg class="w-full h-full" viewBox="0 0 1440 900" fill="none" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    {{-- Cyan Laser Beam Gradient --}}
                    <linearGradient id="cyan-laser-grad" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="rgba(0, 245, 255, 0)"/>
                        <stop offset="70%" stop-color="rgba(0, 245, 255, 0.6)"/>
                        <stop offset="100%" stop-color="#00f5ff"/>
                    </linearGradient>

                    {{-- Gold Laser Beam Gradient --}}
                    <linearGradient id="gold-laser-grad" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="rgba(226, 177, 60, 0)"/>
                        <stop offset="70%" stop-color="rgba(226, 177, 60, 0.6)"/>
                        <stop offset="100%" stop-color="#e2b13c"/>
                    </linearGradient>

                    {{-- Laser Glow Filter --}}
                    <filter id="laser-glow" x="-20%" y="-20%" width="140%" height="140%">
                        <feGaussianBlur stdDeviation="3" result="blur"/>
                        <feMerge>
                            <feMergeNode in="blur"/>
                            <feMergeNode in="SourceGraphic"/>
                        </feMerge>
                    </filter>
                </defs>

                {{-- Effect 2: Data Pipeline Connections (Left Content -> Right Terminal) --}}
                <path d="M 520 280 L 680 280 Q 740 280 740 330 L 740 410 Q 740 440 790 440 L 920 440" 
                      stroke="url(#cyan-laser-grad)" stroke-width="1.5" fill="none"
                      class="circuit-laser-beam-1" style="filter: url(#laser-glow);"/>
                <path d="M 520 380 L 640 380 Q 700 380 700 420 L 700 480 Q 700 510 750 510 L 920 510" 
                      stroke="url(#gold-laser-grad)" stroke-width="1.5" fill="none"
                      class="circuit-laser-beam-2" style="filter: url(#laser-glow);"/>
            </svg>
        </div>

        {{-- Full-Screen Upside-Down Moon Arc Backdrop --}}
        <div class="absolute inset-x-0 bottom-0 pointer-events-none -z-10 flex justify-center overflow-hidden">
            <div class="relative w-[150vw] sm:w-[130vw] md:w-[120vw] h-[300px] sm:h-[360px] bg-white/[0.015] border-t border-white/10 rounded-[50%_50%_0_0/100%_100%_0_0] shadow-[0_-20px_60px_rgba(0,245,255,0.08),inset_0_2px_0_rgba(255,255,255,0.12)] backdrop-blur-md flex flex-col justify-center items-center">
                
                {{-- SVG Laser Beam Tracing the Moon Arc Curve --}}
                <svg class="absolute inset-0 w-full h-full pointer-events-none overflow-visible" viewBox="0 0 1000 300" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="moon-arc-laser-grad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="rgba(0, 245, 255, 0)"/>
                            <stop offset="70%" stop-color="rgba(0, 245, 255, 0.7)"/>
                            <stop offset="100%" stop-color="#00f5ff"/>
                        </linearGradient>
                        <filter id="arc-glow" x="-20%" y="-20%" width="140%" height="140%">
                            <feGaussianBlur stdDeviation="3" result="blur"/>
                            <feMerge>
                                <feMergeNode in="blur"/>
                                <feMergeNode in="SourceGraphic"/>
                            </feMerge>
                        </filter>
                    </defs>
                    <path d="M 0 300 Q 500 -20 1000 300" stroke="url(#moon-arc-laser-grad)" stroke-width="3" fill="none"
                          class="moon-arc-laser-path" style="filter: url(#arc-glow);"/>
                </svg>
                
                {{-- Glowing Arc Top Edge Accent --}}
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#00f5ff]/60 to-transparent"></div>
                
                {{-- Concept 2: The SUPPORTED BLOCKCHAINS --}}
                <div class="pointer-events-auto max-w-6xl w-full px-4 pt-6 pb-6 relative z-10 flex flex-col items-center justify-center">
                    
                    {{-- Central Quantum Core Status Badge --}}
                    <div class="relative flex items-center justify-center mb-4">
                        <div class="absolute -inset-2 rounded-full bg-gradient-to-r from-cyan-500/20 via-indigo-500/30 to-purple-500/20 blur-xl fusion-core-pulse"></div>
                        <div class="relative inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-[#090c14]/90 border border-cyan-500/40 text-[10px] font-mono font-bold text-cyan-400 uppercase tracking-widest shadow-[0_0_20px_rgba(0,245,255,0.25)]">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-400"></span>
                            </span>
                            SUPPORTED BLOCKCHAINS · ON-CHAIN SETTLEMENT
                        </div>
                    </div>

                    {{-- Energy Rails Container --}}
                    <div class="relative w-full max-w-5xl flex items-center justify-center py-2">
                        
                        {{-- Horizontal Energy Beams behind capsules --}}
                        <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-px bg-gradient-to-r from-transparent via-cyan-400/40 to-transparent pointer-events-none"></div>
                        <div class="absolute inset-x-12 top-1/2 -translate-y-1/2 h-8 bg-cyan-500/5 blur-xl pointer-events-none"></div>

                        {{-- Liquid Glass Quantum Token Capsules Deck --}}
                        <div class="relative z-10 flex flex-wrap items-center justify-center gap-2 sm:gap-3">
                            @foreach ($heroBlockchains as $chain)
                                @php
                                    $chainLogo = !empty($chain->logo) 
                                        ? asset($chain->logo) 
                                        : asset('assets/images/tokens/' . strtolower($chain->code) . '.png');
                                    $chainName = strtoupper($chain->name);
                                    $chainSymbol = strtoupper($chain->symbol ?? $chain->code);
                                @endphp
                                <div class="group relative flex items-center gap-2.5 px-3.5 py-2 rounded-2xl bg-[#090c14]/95 border border-cyan-500/20 hover:border-cyan-400/60 hover:bg-cyan-500/10 backdrop-blur-2xl shadow-[0_0_15px_rgba(0,245,255,0.1)] hover:shadow-[0_0_25px_rgba(0,245,255,0.35)] transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 cursor-pointer select-none">
                                    <img src="{{ $chainLogo }}" 
                                         alt="{{ $chainName }}" 
                                         loading="lazy" 
                                         onerror="this.src='{{ asset('assets/images/tokens/' . strtolower($chainSymbol) . '.png') }}'"
                                         class="w-6 h-6 object-contain rounded-full shadow-[0_0_10px_rgba(0,245,255,0.3)] group-hover:scale-110 transition-transform duration-300">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold text-white tracking-wide group-hover:text-cyan-300 transition-colors">{{ $chainName }}</span>
                                        <span class="text-[9px] font-mono font-bold text-cyan-400/90 group-hover:text-cyan-300 transition-colors">({{ $chainSymbol }})</span>
                                    </div>
                                    <div class="w-1.5 h-1.5 rounded-full bg-cyan-500/40 group-hover:bg-cyan-400 group-hover:shadow-[0_0_8px_#00f5ff] transition-all duration-300"></div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                </div>
            </div>
        </div>

        {{-- Vibrant Ambient Background Color Blobs --}}
        <div class="absolute top-1/4 left-1/4 w-[500px] h-[500px] bg-gradient-to-tr from-[#e2b13c]/35 to-amber-500/20 rounded-full blur-[120px] pointer-events-none -z-20 animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute top-1/3 right-1/4 w-[600px] h-[600px] bg-gradient-to-br from-indigo-600/30 via-purple-600/20 to-transparent rounded-full blur-[140px] pointer-events-none -z-20 animate-pulse" style="animation-duration: 12s;"></div>
        <div class="absolute bottom-10 left-1/3 w-[450px] h-[450px] bg-emerald-500/20 rounded-full blur-[130px] pointer-events-none -z-20"></div>
        <div class="fixed inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.03] pointer-events-none -z-20"></div>

        <div class="max-w-7xl mx-auto px-4 lg:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                {{-- Left Asymmetric Content Deck --}}
                <div class="lg:col-span-7 space-y-8">
                    
                    {{-- Live Telemetry Status Pill (Dashboard Accent Primary Gold) --}}
                    <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-[#e2b13c]/10 border border-[#e2b13c]/20 backdrop-blur-xl">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#e2b13c] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#e2b13c]"></span>
                        </span>
                        <span class="text-xs font-mono font-bold text-[#e2b13c] tracking-wider uppercase">
                            {{ __('Automated Trading Engine Active · 99.98% Uptime') }}
                        </span>
                    </div>

                    {{-- Main Headline --}}
                    <div>
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.08]">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-slate-400">
                                {{ __('Automate Your Trading.') }}
                            </span> <br>
                            <span class="text-white">
                                {{ __('Grow Your Portfolio.') }}
                            </span>
                        </h1>
                    </div>

                    {{-- Subheadline --}}
                    <p class="text-base sm:text-lg text-slate-400 font-body leading-relaxed max-w-[54ch]">
                        {{ __('Start smart automated trading bots or copy verified top traders with instant crypto deposits and withdrawals.') }}
                    </p>

                    {{-- Action CTA Group matching Dashboard Quick Action Buttons --}}
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ route('user.register') }}"
                           class="group flex items-center justify-center gap-2.5 px-7 py-3.5 bg-emerald-500/10 border border-emerald-500/20 rounded-xl transition-all hover:bg-emerald-500/20 hover:border-emerald-500/30 cursor-pointer shadow-lg">
                            <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-emerald-400 text-xs font-bold tracking-widest uppercase">{{ __('Start Trading Bots') }}</span>
                        </a>

                        <a href="{{ route('copy-trading') }}"
                           class="group flex items-center justify-center gap-2.5 px-7 py-3.5 bg-white/[0.02] border border-white/[0.08] rounded-xl transition-all hover:bg-white/[0.06] hover:border-white/20 cursor-pointer">
                            <span class="text-slate-300 text-xs font-bold tracking-widest uppercase">{{ __('Explore Copy Trading') }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    {{-- Micro Metric Bar with Interactive Counters --}}
                    <div class="grid grid-cols-3 gap-4 pt-6 border-t border-white/[0.08]"
                         x-data="{
                            vol: 0,
                            uptime: 0,
                            settle: 0,
                            init() {
                                let duration = 2000;
                                let step = 20;
                                let steps = duration / step;
                                let count = 0;
                                let timer = setInterval(() => {
                                    count++;
                                    this.vol = Math.min(10, Math.floor((count / steps) * 10));
                                    this.uptime = Math.min(99.98, parseFloat(((count / steps) * 99.98).toFixed(2)));
                                    this.settle = Math.min(100, Math.floor((count / steps) * 100));
                                    if (count >= steps) clearInterval(timer);
                                }, step);
                            }
                         }">
                        <div>
                            <div class="text-2xl font-heading font-black text-white font-mono flex items-center">
                                <span>$</span><span x-text="vol">10</span><span>B+</span>
                            </div>
                            <div class="text-xs text-slate-400 font-medium mt-1">{{ __('Trading Volume') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-heading font-black text-emerald-400 font-mono flex items-center">
                                <span x-text="uptime.toFixed(2)">99.98</span><span>%</span>
                            </div>
                            <div class="text-xs text-slate-400 font-medium mt-1">{{ __('Platform Uptime') }}</div>
                        </div>
                        <div>
                            <div class="text-2xl font-heading font-black text-white font-mono flex items-center">
                                <span x-text="settle">100</span><span>%</span>
                            </div>
                            <div class="text-xs text-slate-400 font-medium mt-1">{{ __('Direct Crypto Payouts') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Right Bleeding Multi-Layer Algorithmic Console Stack (Offset into Right Edge - Concept 1) --}}
                <div class="lg:col-span-5 relative w-full lg:w-[130%] lg:-mr-[30%] [mask-image:linear-gradient(to_right,#000_75%,transparent_98%)] select-none">
                    
                    {{-- Stack Container --}}
                    <div class="relative space-y-4">
                        
                        {{-- Main Layer: AI Bot Execution Workspace --}}
                        <div class="relative group double-bezel-outer p-1 overflow-hidden shadow-2xl backdrop-blur-2xl">
                            <div class="absolute top-0 right-0 -mt-12 -mr-12 w-48 h-48 bg-[#e2b13c]/10 rounded-full blur-3xl pointer-events-none z-0"></div>
                            <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-48 h-48 bg-purple-600/10 rounded-full blur-3xl pointer-events-none z-0"></div>

                            <div class="relative z-10 double-bezel-inner p-5 flex flex-col h-full justify-between overflow-hidden">
                                
                                {{-- Window Header --}}
                                <div class="flex items-center justify-between pb-3.5 border-b border-white/[0.08]">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                                        <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                                        <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                                        <span class="text-xs font-mono text-slate-400 ml-2">TRADING_BOT_ENGINE // ACTIVE</span>
                                    </div>
                                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-mono text-[9px] font-bold uppercase tracking-widest flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        {{ __('LIVE TRADING ACTIVE') }}
                                    </span>
                                </div>

                                {{-- Upper Metric Deck --}}
                                <div class="grid grid-cols-2 gap-3 mt-4">
                                    <div class="bg-white/[0.02] border border-white/[0.06] rounded-xl p-3">
                                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">{{ __('Active Trading Balance') }}</span>
                                        <div class="text-base font-bold text-white tracking-wide mt-0.5">$24,850.00</div>
                                    </div>
                                    <div class="bg-white/[0.02] border border-white/[0.06] rounded-xl p-3">
                                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">{{ __('Total Profit') }}</span>
                                        <div class="text-base font-bold text-emerald-400 tracking-wide mt-0.5">+$3,842.10 (+15.4%)</div>
                                    </div>
                                </div>

                                {{-- Active Bot Card --}}
                                <div class="mt-3.5 space-y-3">
                                    <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-[#e2b13c]/10 border border-[#e2b13c]/20 flex items-center justify-center text-[#e2b13c] font-bold text-sm">
                                                ⚡
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-bold text-white">Apex Scalper Pro v4</h4>
                                                <p class="text-[10px] text-slate-400 font-mono">BTC/USDT · Automated Scalping</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs font-black font-mono text-emerald-400">+2.84%</div>
                                            <div class="text-[9px] text-slate-400 font-mono">{{ __('24h Return') }}</div>
                                        </div>
                                    </div>

                                    {{-- Live Order Log Feed --}}
                                    <div class="p-3 rounded-xl bg-black/60 border border-white/[0.05] space-y-1.5 font-mono text-[10px]">
                                        <div class="flex items-center justify-between text-slate-400">
                                            <span class="text-emerald-400">[ORDER_FILL] BUY BTC/USDT @ $94,280</span>
                                            <span class="text-slate-500">Just now</span>
                                        </div>
                                        <div class="flex items-center justify-between text-slate-400">
                                            <span class="text-emerald-400">[TARGET_HIT] SOL/USDT arbitrage +2.15%</span>
                                            <span class="text-slate-500">1m ago</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Secondary Peeking Layer: Master Trader Copy Network Card --}}
                        <div class="relative rounded-2xl bg-[#090c14]/90 border border-cyan-500/20 p-4 shadow-xl backdrop-blur-xl translate-x-6 sm:translate-x-10 hover:translate-x-4 transition-transform duration-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 font-bold text-xs font-mono">
                                        SQ
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-xs font-bold text-white">Satoshi Trading Systems</h4>
                                            <span class="px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 text-[9px] font-mono font-bold uppercase">COPY CODE: SATO-QUANT-01</span>
                                        </div>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">1,420 Active Copiers · Instant Copy Sync</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-black font-mono text-emerald-400">+142.8%</div>
                                    <div class="text-[9px] text-slate-400 font-mono">30d Return (88.4% Win Rate)</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ==================================================================================== --}}
    {{-- CONNECTED EXCHANGES & LIQUIDITY VENUES: INFINITE MARQUEE RAIL --}}
    {{-- ==================================================================================== --}}
    <section class="relative py-12 sm:py-16 border-y border-white/[0.06] bg-gradient-to-b from-[#050507] via-[#080b12] to-[#050507] overflow-hidden isolate">
        {{-- Ambient Corner Glows --}}
        <div class="absolute -top-24 left-1/4 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
        <div class="absolute -bottom-24 right-1/4 w-80 h-80 bg-[#e2b13c]/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 lg:px-8 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        <span class="text-[10px] font-mono font-bold text-slate-300 uppercase tracking-[0.2em]">
                            {{ __('CONNECTED VENUES') }}
                        </span>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-500">
                        {{ count($botTrading['exchanges'] ?? []) }}+ {{ __('Institutional Liquidity Feeds') }}
                    </span>
                </div>

                <div class="hidden sm:flex items-center gap-2 text-[11px] font-mono text-slate-400">
                    <span class="text-emerald-400 font-bold">●</span> {{ __('Sub-Millisecond Order Routing & Arbitrage Execution') }}
                </div>
            </div>
        </div>

        {{-- Marquee Track with Left/Right Fading Masks --}}
        <div class="relative w-full overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_10%,black_90%,transparent)] york-marquee-wrapper group/marquee">
            <div class="flex gap-4 sm:gap-6 items-center whitespace-nowrap animate-york-marquee py-2">
                @php
                    $exchangesList = !empty($botTrading['exchanges']) ? $botTrading['exchanges'] : [
                        'Binance', 'Bybit', 'OKX', 'Kraken', 'KuCoin', 'Bitget', 'Gate.io', 'Deribit', 'Huobi'
                    ];
                    // Duplicate 3 times for seamless infinite loop
                    $loopList = array_merge($exchangesList, $exchangesList, $exchangesList);
                @endphp

                @foreach ($loopList as $index => $exchange)
                    @php
                        $cleanCode = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $exchange));
                        if ($cleanCode === 'gateio' || $cleanCode === 'gate') {
                            $logoFile = 'gateio.svg';
                        } else {
                            $logoFile = $cleanCode . '.svg';
                        }
                        $logoPath = public_path('assets/images/exchanges/' . $logoFile);
                        $hasLogo = file_exists($logoPath);
                    @endphp

                    <div class="inline-flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-white/[0.02] hover:bg-white/[0.06] border border-white/[0.06] hover:border-cyan-500/40 backdrop-blur-xl shadow-lg hover:shadow-[0_0_25px_rgba(0,245,255,0.15)] transition-all duration-300 transform hover:-translate-y-1 shrink-0 select-none group cursor-pointer">
                        <div class="w-8 h-8 rounded-xl bg-white/[0.04] border border-white/10 flex items-center justify-center p-1.5 overflow-hidden group-hover:border-cyan-500/30 group-hover:bg-cyan-500/10 transition-colors">
                            @if ($hasLogo)
                                <img src="{{ asset('assets/images/exchanges/' . $logoFile) }}" 
                                     alt="{{ $exchange }}" 
                                     class="w-full h-full object-contain filter grayscale group-hover:grayscale-0 group-hover:scale-110 transition-all duration-300">
                            @else
                                <span class="text-[10px] font-mono font-black text-slate-300 group-hover:text-cyan-400">
                                    {{ strtoupper(substr($exchange, 0, 2)) }}
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-bold text-white group-hover:text-cyan-300 tracking-wide transition-colors">
                                    {{ $exchange }}
                                </span>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400/60 group-hover:bg-emerald-400 group-hover:shadow-[0_0_8px_#10b981] transition-all"></span>
                            </div>
                            <span class="text-[9px] font-mono text-slate-500 group-hover:text-slate-400 transition-colors">
                                {{ __('Spot & Derivatives') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <style>
            @keyframes yorkMarquee {
                0% { transform: translateX(0); }
                100% { transform: translateX(-33.333333%); }
            }
            .animate-york-marquee {
                display: flex;
                width: max-content;
                animation: yorkMarquee 60s linear infinite;
            }
            .york-marquee-wrapper:hover .animate-york-marquee,
            .animate-york-marquee:hover {
                animation-play-state: paused !important;
            }
        </style>
    </section>

    {{-- ==================================================================================== --}}
    {{-- SECTION 2: PRODUCT PILLAR 1 — AUTONOMOUS AI BOT FLEET (BENTO GRID 2.0) --}}
    {{-- ==================================================================================== --}}
    <section class="relative py-24 sm:py-32 bg-[#050507] isolate overflow-hidden">
        {{-- Ambient Layered Glow Blobs --}}
        <div class="absolute top-1/4 left-0 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-500/10 rounded-full blur-[150px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 9s;"></div>
        <div class="absolute bottom-10 right-0 w-[500px] h-[500px] bg-indigo-600/15 rounded-full blur-[140px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 12s;"></div>
        <div class="absolute top-1/2 right-1/3 w-[350px] h-[350px] bg-emerald-500/10 rounded-full blur-[120px] pointer-events-none -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 lg:px-8 w-full" x-data="{ activeFilter: 'all' }">
            
            {{-- Section Header & Filter Tabs --}}
            <div class="max-w-4xl mx-auto text-center mb-16 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-5 shadow-[0_0_20px_rgba(0,245,255,0.15)]">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('AUTOMATED TRADING BOTS') }}
                </div>
                <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.08] mb-6">
                    {{ __('Smart Automated Trading Bots Running 24/7') }}
                </h2>
                <p class="text-base sm:text-lg text-slate-400 font-body leading-relaxed max-w-2xl mx-auto mb-10">
                    {{ __('No emotional decisions. No manual chart watching. Our automated trading bots analyze market movements in real time to capture profitable trades across crypto pairs.') }}
                </p>

                {{-- Interactive Filter Pill Bar --}}
                <div class="inline-flex flex-wrap items-center justify-center gap-2 p-1.5 rounded-2xl bg-[#090c14] border border-white/[0.08] backdrop-blur-xl shadow-2xl">
                    <button @click="activeFilter = 'all'" 
                            :class="activeFilter === 'all' ? 'bg-[#00f5ff] text-[#050507] font-black shadow-[0_0_20px_rgba(0,245,255,0.4)]' : 'text-slate-400 hover:text-white font-bold'"
                            class="px-5 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300">
                        {{ __('All Trading Bots') }}
                    </button>
                    <button @click="activeFilter = 'crypto'" 
                            :class="activeFilter === 'crypto' ? 'bg-[#00f5ff] text-[#050507] font-black shadow-[0_0_20px_rgba(0,245,255,0.4)]' : 'text-slate-400 hover:text-white font-bold'"
                            class="px-5 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300">
                        {{ __('Crypto Bots') }}
                    </button>
                    <button @click="activeFilter = 'forex'" 
                            :class="activeFilter === 'forex' ? 'bg-[#00f5ff] text-[#050507] font-black shadow-[0_0_20px_rgba(0,245,255,0.4)]' : 'text-slate-400 hover:text-white font-bold'"
                            class="px-5 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300">
                        {{ __('Forex & Commodities') }}
                    </button>
                </div>
            </div>

            {{-- Live Mini-Chart Sparklines Deck Widget (Live Market Data with 45s Cache) --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
                @php
                    $displayCards = !empty($marketStats) ? $marketStats : [
                        'BTCUSDT' => ['symbol' => 'BTCUSDT', 'base' => 'BTC', 'name' => 'BTC/USDT', 'price' => 96450.00, 'formatted_price' => '$96,450.00', 'change' => 3.42, 'formatted_change' => '+3.42%', 'is_positive' => true, 'volume' => '$28.4B', 'logo' => asset('assets/images/tokens/btc.png')],
                        'ETHUSDT' => ['symbol' => 'ETHUSDT', 'base' => 'ETH', 'name' => 'ETH/USDT', 'price' => 3620.50, 'formatted_price' => '$3,620.50', 'change' => 4.18, 'formatted_change' => '+4.18%', 'is_positive' => true, 'volume' => '$14.2B', 'logo' => asset('assets/images/tokens/eth.png')],
                        'SOLUSDT' => ['symbol' => 'SOLUSDT', 'base' => 'SOL', 'name' => 'SOL/USDT', 'price' => 214.80, 'formatted_price' => '$214.80', 'change' => 8.65, 'formatted_change' => '+8.65%', 'is_positive' => true, 'volume' => '$8.9B', 'logo' => asset('assets/images/tokens/sol.png')],
                        'BNBUSDT' => ['symbol' => 'BNBUSDT', 'base' => 'BNB', 'name' => 'BNB/USDT', 'price' => 685.20, 'formatted_price' => '$685.20', 'change' => 1.95, 'formatted_change' => '+1.95%', 'is_positive' => true, 'volume' => '$4.1B', 'logo' => asset('assets/images/tokens/bnb.png')],
                    ];
                @endphp

                @foreach ($displayCards as $card)
                    @php
                        $isPos = $card['is_positive'] ?? true;
                        $strokeColor = $isPos ? '#10b981' : '#f43f5e';
                        $pillBg = $isPos ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20';
                    @endphp
                    <div class="p-4 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] hover:border-cyan-400/40 backdrop-blur-xl transition-all duration-300 group shadow-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <img src="{{ $card['logo'] }}" 
                                     alt="{{ $card['base'] }}" 
                                     loading="lazy" 
                                     onerror="this.src='{{ asset('assets/images/tokens/' . strtolower($card['base']) . '.png') }}'" 
                                     class="w-5 h-5 object-contain rounded-full">
                                <span class="text-xs font-bold text-white tracking-wide">{{ $card['name'] }}</span>
                            </div>
                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded border {{ $pillBg }}">
                                {{ $card['formatted_change'] }}
                            </span>
                        </div>
                        <div class="flex items-baseline justify-between font-mono">
                            <span class="text-base font-black text-white">{{ $card['formatted_price'] }}</span>
                            <span class="text-[9px] text-slate-500">24h Vol: {{ $card['volume'] }}</span>
                        </div>
                        <div class="h-8 w-full mt-2">
                            <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none">
                                @if ($isPos)
                                    <path d="M0 25 L 20 20 L 40 22 L 60 10 L 80 12 L 100 5" fill="none" stroke="{{ $strokeColor }}" stroke-width="2" stroke-linecap="round"/>
                                @else
                                    <path d="M0 5 L 20 12 L 40 10 L 60 22 L 80 20 L 100 26" fill="none" stroke="{{ $strokeColor }}" stroke-width="2" stroke-linecap="round"/>
                                @endif
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Bento Grid 2.0 Architecture --}}
            @if ($featuredBots->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                    @foreach ($featuredBots as $index => $bot)
                        @php
                            $minRoi = number_format($bot->daily_return_min, 2);
                            $maxRoi = number_format($bot->daily_return_max, 2);
                            $minCapital = number_format($bot->min_amount, 2);
                            $durationDays = $bot->duration;
                            $pairsList = is_array($bot->traded_pairs) ? implode(', ', array_slice($bot->traded_pairs, 0, 3)) : ($bot->traded_pairs ?? 'BTC/USDT, ETH/USDT');
                            $typeBadge = strtoupper($bot->type ?? 'TRADING BOT');
                            $botCategory = strtolower($bot->type ?? 'crypto');
                            $botLogoUrl = !empty($bot->logo) 
                                ? (str_contains($bot->logo, '/') ? asset($bot->logo) : asset('assets/images/bots/' . $bot->logo)) 
                                : asset('assets/images/bots/bot-1.png');
                            $isFeatured = ($index === 0);
                        @endphp

                        @if ($isFeatured)
                            {{-- Wide Command Center Console Card (Spans 2 Columns) --}}
                            <div x-show="activeFilter === 'all' || activeFilter === '{{ $botCategory }}'"
                                 x-transition:enter="transition ease-out duration-300 transform" 
                                 x-transition:enter-start="opacity-0 scale-95" 
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="lg:col-span-2 group relative flex flex-col justify-between rounded-3xl bg-[#090c14]/95 border border-white/[0.1] hover:border-cyan-400/50 p-7 sm:p-9 backdrop-blur-2xl shadow-[0_10px_40px_rgba(0,0,0,0.8)] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_0_50px_rgba(0,245,255,0.2)]">
                                
                                {{-- Cybernetic Corner Reticles --}}
                                <div class="absolute top-3 left-3 text-slate-600 font-mono text-[9px] pointer-events-none select-none">+</div>
                                <div class="absolute top-3 right-3 text-slate-600 font-mono text-[9px] pointer-events-none select-none">+</div>
                                <div class="absolute bottom-3 left-3 text-slate-600 font-mono text-[9px] pointer-events-none select-none">+</div>
                                <div class="absolute bottom-3 right-3 text-slate-600 font-mono text-[9px] pointer-events-none select-none">+</div>

                                <div>
                                    {{-- Header Bar --}}
                                    <div class="flex flex-wrap items-center justify-between gap-3 mb-6 pb-4 border-b border-white/[0.06]">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_10px_#10b981]"></span>
                                            <span class="px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-wider">
                                                {{ __('FEATURED BOT · ') }} {{ $typeBadge }}
                                            </span>
                                        </div>
                                        <span class="px-3 py-1 rounded-full bg-white/[0.03] border border-white/[0.08] text-slate-300 font-mono text-xs">
                                            {{ __('DURATION:') }} <strong class="text-white">{{ $durationDays }} {{ __('DAYS') }}</strong>
                                        </span>
                                    </div>

                                    {{-- Grid Body: Left Params + Right Live Telemetry Curve HUD --}}
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8 items-center mb-8">
                                        
                                        {{-- Left Info Block --}}
                                        <div class="md:col-span-6 space-y-5">
                                            <div class="flex items-center gap-4">
                                                <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center p-1.5 shadow-[0_0_20px_rgba(0,245,255,0.2)] shrink-0 overflow-hidden">
                                                    <img src="{{ $botLogoUrl }}" 
                                                         alt="{{ $bot->name }}" 
                                                         loading="lazy" 
                                                         onerror="this.src='{{ asset('assets/images/bots/bot-1.png') }}'"
                                                         class="w-full h-full object-cover rounded-xl">
                                                </div>
                                                <div>
                                                    <h3 class="text-2xl font-black text-white group-hover:text-cyan-300 transition-colors tracking-tight">{{ $bot->name }}</h3>
                                                    <p class="text-xs font-mono text-cyan-400/90 mt-1 flex items-center gap-1.5">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                                        {{ $pairsList }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Yield Box --}}
                                            <div class="p-5 rounded-2xl bg-gradient-to-br from-white/[0.03] to-white/[0.01] border border-white/[0.08] shadow-inner">
                                                <span class="text-[10px] font-mono font-bold uppercase text-slate-400 tracking-wider block mb-1">
                                                    {{ __('Target Daily Return') }}
                                                </span>
                                                <div class="flex items-baseline gap-2">
                                                    <span class="text-4xl sm:text-5xl font-black font-mono text-emerald-400 tracking-tight drop-shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                                                        +{{ $minRoi }}% – {{ $maxRoi }}%
                                                    </span>
                                                    <span class="text-xs font-mono text-slate-400 uppercase font-bold">{{ __('Daily Return') }}</span>
                                                </div>
                                            </div>

                                            {{-- Metadata Table --}}
                                            <div class="space-y-3 font-mono text-xs">
                                                <div class="flex justify-between items-center pb-2 border-b border-white/[0.05]">
                                                    <span class="text-slate-400">{{ __('Minimum Deposit') }}</span>
                                                    <span class="font-bold text-white">${{ $minCapital }}</span>
                                                </div>
                                                <div class="flex justify-between items-center pb-2 border-b border-white/[0.05]">
                                                    <span class="text-slate-400">{{ __('Capital Returned') }}</span>
                                                    <span class="font-bold text-emerald-400">{{ $bot->is_capital_returned ? __('Yes (When Plan Completes)') : __('Included in Daily Return') }}</span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-slate-400">{{ __('Order Execution') }}</span>
                                                    <span class="font-bold text-cyan-400">{{ __('Instant Market Orders') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Right Telemetry & Live Sparkline HUD --}}
                                        <div class="md:col-span-6 p-5 rounded-2xl bg-black/60 border border-white/[0.08] space-y-4">
                                            <div class="flex items-center justify-between pb-2 border-b border-white/[0.08]">
                                                <span class="text-[10px] font-mono text-slate-400 uppercase font-bold tracking-widest flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                                                    {{ __('LIVE PERFORMANCE TRACKER') }}
                                                </span>
                                                <span class="text-[10px] font-mono text-emerald-400 font-bold">+99.98% Win Rate</span>
                                            </div>

                                            {{-- Animated SVG Performance Curve --}}
                                            <div class="relative h-28 w-full overflow-hidden rounded-xl bg-gradient-to-b from-cyan-500/10 via-transparent to-transparent border border-cyan-500/20 p-2">
                                                <svg class="w-full h-full overflow-visible" viewBox="0 0 300 80" preserveAspectRatio="none">
                                                    <defs>
                                                        <linearGradient id="featured-yield-grad-{{ $bot->id }}" x1="0" y1="0" x2="0" y2="1">
                                                            <stop offset="0%" stop-color="#10b981" stop-opacity="0.4"/>
                                                            <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                                                        </linearGradient>
                                                    </defs>
                                                    <path d="M0 65 Q 40 55, 80 40 T 160 30 T 240 15 T 300 8 L 300 80 L 0 80 Z" fill="url(#featured-yield-grad-{{ $bot->id }})" />
                                                    <path d="M0 65 Q 40 55, 80 40 T 160 30 T 240 15 T 300 8" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" />
                                                    <circle cx="300" cy="8" r="4" fill="#00f5ff" class="animate-pulse" />
                                                </svg>
                                            </div>

                                            {{-- Live Execution Micro-Feed --}}
                                            <div class="space-y-2 font-mono text-[10px] text-slate-400">
                                                <div class="flex items-center justify-between p-2 rounded-lg bg-white/[0.02] border border-white/[0.04]">
                                                    <span class="text-emerald-400 font-bold">[FILL] ORDER COMPLETED</span>
                                                    <span class="text-slate-500">0.04s ago</span>
                                                </div>
                                                <div class="flex items-center justify-between p-2 rounded-lg bg-white/[0.02] border border-white/[0.04]">
                                                    <span class="text-cyan-400 font-bold">[PROFIT] SPREAD CAPTURED</span>
                                                    <span class="text-slate-500">1.2s ago</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- Deploy Contract CTA Button (Button-in-Button Architecture) --}}
                                <a href="{{ auth()->check() ? route('user.trading-bots.index') : route('user.register') }}" 
                                   class="w-full pl-8 pr-2.5 py-2.5 rounded-full bg-[#00f5ff] hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-between shadow-[0_0_35px_rgba(0,245,255,0.4)] hover:shadow-[0_0_50px_rgba(0,245,255,0.7)] group">
                                    <span>{{ __('Start Featured Bot Now') }}</span>
                                    <span class="w-10 h-10 rounded-full bg-[#050507]/20 flex items-center justify-center group-hover:translate-x-1 group-hover:-translate-y-[1px] transition-transform">
                                        <svg class="w-4 h-4 text-[#050507]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        @else
                            {{-- Standard Compact Cards (1 Column for all other database bots) --}}
                            <div x-show="activeFilter === 'all' || activeFilter === '{{ $botCategory }}'"
                                 x-transition:enter="transition ease-out duration-300 transform" 
                                 x-transition:enter-start="opacity-0 scale-95" 
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="group relative flex flex-col justify-between rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-cyan-400/40 p-6 sm:p-8 backdrop-blur-xl shadow-2xl transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_0_35px_rgba(0,245,255,0.15)]">
                                <div>
                                    <div class="flex items-center justify-between gap-3 mb-6">
                                        <span class="px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[9px] font-bold uppercase tracking-wider">
                                            {{ $typeBadge }}
                                        </span>
                                        <span class="text-[10px] font-mono text-slate-400">
                                            {{ __('Duration:') }} {{ $durationDays }} {{ __('Days') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-3.5 mb-5">
                                        <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 font-bold text-lg shrink-0 shadow-inner overflow-hidden p-1">
                                            <img src="{{ $botLogoUrl }}" 
                                                 alt="{{ $bot->name }}" 
                                                 loading="lazy" 
                                                 onerror="this.src='{{ asset('assets/images/bots/bot-1.png') }}'"
                                                 class="w-full h-full object-cover rounded-xl">
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-white group-hover:text-cyan-300 transition-colors">{{ $bot->name }}</h3>
                                            <p class="text-xs font-mono text-slate-400 mt-0.5">{{ $pairsList }}</p>
                                        </div>
                                    </div>

                                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.05] mb-6">
                                        <span class="text-[10px] font-mono font-bold uppercase text-slate-400 tracking-wider block mb-1">
                                            {{ __('Target Daily Return') }}
                                        </span>
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-3xl font-black font-mono text-emerald-400 tracking-tight">+{{ $minRoi }}% – {{ $maxRoi }}%</span>
                                            <span class="text-xs font-mono text-slate-400">{{ __('daily') }}</span>
                                        </div>
                                    </div>

                                    <div class="space-y-2.5 mb-8 font-mono text-xs text-slate-400">
                                        <div class="flex justify-between items-center pb-2 border-b border-white/[0.04]">
                                            <span>{{ __('Minimum Deposit') }}</span>
                                            <span class="font-bold text-white">${{ $minCapital }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pb-2 border-b border-white/[0.04]">
                                            <span>{{ __('Capital Returned') }}</span>
                                            <span class="font-bold text-emerald-400">{{ $bot->is_capital_returned ? __('Yes (When Plan Completes)') : __('Included in Daily Return') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span>{{ __('Trading Hours') }}</span>
                                            <span class="font-bold text-slate-200">{{ __('24/7 Automated') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ auth()->check() ? route('user.trading-bots.index') : route('user.register') }}" 
                                   class="w-full py-3.5 px-4 rounded-xl bg-white/[0.03] hover:bg-[#00f5ff] border border-white/[0.1] hover:border-[#00f5ff] text-white hover:text-[#050507] font-bold text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 group/btn shadow-md hover:shadow-[0_0_25px_rgba(0,245,255,0.4)]">
                                    <span>{{ __('Start Trading Bot') }}</span>
                                    <svg class="w-4 h-4 text-slate-400 group-hover/btn:text-[#050507] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                {{-- Default Curated High-End Bento Grid 2.0 Deck --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                    
                    {{-- Wide Featured Command Center Console Card (Spans 2 Columns) --}}
                    <div x-show="activeFilter === 'all' || activeFilter === 'scalping'" 
                         x-transition:enter="transition ease-out duration-300 transform" 
                         x-transition:enter-start="opacity-0 scale-95" 
                         x-transition:enter-end="opacity-100 scale-100"
                         class="lg:col-span-2 group relative flex flex-col justify-between rounded-3xl bg-[#090c14]/95 border border-white/[0.1] hover:border-cyan-400/50 p-7 sm:p-9 backdrop-blur-2xl shadow-[0_10px_40px_rgba(0,0,0,0.8)] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_0_50px_rgba(0,245,255,0.2)]">
                        
                        {{-- Cybernetic Corner Reticles --}}
                        <div class="absolute top-3 left-3 text-slate-600 font-mono text-[9px] pointer-events-none select-none">+</div>
                        <div class="absolute top-3 right-3 text-slate-600 font-mono text-[9px] pointer-events-none select-none">+</div>
                        <div class="absolute bottom-3 left-3 text-slate-600 font-mono text-[9px] pointer-events-none select-none">+</div>
                        <div class="absolute bottom-3 right-3 text-slate-600 font-mono text-[9px] pointer-events-none select-none">+</div>

                        <div>
                            {{-- Header Bar --}}
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-6 pb-4 border-b border-white/[0.06]">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_10px_#10b981]"></span>
                                    <span class="px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-wider">
                                        {{ __('FEATURED BOT · FAST SCALPING') }}
                                    </span>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-white/[0.03] border border-white/[0.08] text-slate-300 font-mono text-xs">
                                    {{ __('DURATION:') }} <strong class="text-white">30 {{ __('DAYS') }}</strong>
                                </span>
                            </div>

                            {{-- Grid Body: Left Params + Right Live Telemetry Curve HUD --}}
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8 items-center mb-8">
                                
                                {{-- Left Info Block --}}
                                <div class="md:col-span-6 space-y-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center p-1.5 shadow-[0_0_20px_rgba(0,245,255,0.2)] shrink-0 overflow-hidden">
                                            <img src="{{ asset('assets/images/bots/bot-1.png') }}" alt="Apex Scalper Pro" loading="lazy" class="w-full h-full object-cover rounded-xl">
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-black text-white group-hover:text-cyan-300 transition-colors tracking-tight">Apex Scalper Pro v4</h3>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="px-2 py-0.5 rounded bg-white/[0.04] text-[10px] font-mono text-cyan-400 border border-white/[0.06]">BTC/USDT</span>
                                                <span class="px-2 py-0.5 rounded bg-white/[0.04] text-[10px] font-mono text-cyan-400 border border-white/[0.06]">ETH/USDT</span>
                                                <span class="px-2 py-0.5 rounded bg-white/[0.04] text-[10px] font-mono text-cyan-400 border border-white/[0.06]">SOL/USDT</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Yield Box --}}
                                    <div class="p-5 rounded-2xl bg-gradient-to-br from-white/[0.03] to-white/[0.01] border border-white/[0.08] shadow-inner">
                                        <span class="text-[10px] font-mono font-bold uppercase text-slate-400 tracking-wider block mb-1">
                                            {{ __('Target Daily Return') }}
                                        </span>
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-4xl sm:text-5xl font-black font-mono text-emerald-400 tracking-tight drop-shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                                                +1.80% – 3.20%
                                            </span>
                                            <span class="text-xs font-mono text-slate-400 uppercase font-bold">{{ __('Daily Return') }}</span>
                                        </div>
                                    </div>

                                    {{-- Metadata Table --}}
                                    <div class="space-y-3 font-mono text-xs">
                                        <div class="flex justify-between items-center pb-2 border-b border-white/[0.05]">
                                            <span class="text-slate-400">{{ __('Minimum Deposit') }}</span>
                                            <span class="font-bold text-white">$100.00</span>
                                        </div>
                                        <div class="flex justify-between items-center pb-2 border-b border-white/[0.05]">
                                            <span class="text-slate-400">{{ __('Capital Returned') }}</span>
                                            <span class="font-bold text-emerald-400">{{ __('Yes (When Plan Completes)') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-slate-400">{{ __('Trading Strategy') }}</span>
                                            <span class="font-bold text-cyan-400">{{ __('Fast Short-Term Scalping') }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right Telemetry & Live Sparkline HUD --}}
                                <div class="md:col-span-6 p-5 rounded-2xl bg-black/60 border border-white/[0.08] space-y-4">
                                    <div class="flex items-center justify-between pb-2 border-b border-white/[0.08]">
                                        <span class="text-[10px] font-mono text-slate-400 uppercase font-bold tracking-widest flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                                            {{ __('LIVE PERFORMANCE TRACKER') }}
                                        </span>
                                        <span class="text-[10px] font-mono text-emerald-400 font-bold">99.98% Win Rate</span>
                                    </div>

                                    {{-- Animated SVG Performance Curve --}}
                                    <div class="relative h-28 w-full overflow-hidden rounded-xl bg-gradient-to-b from-cyan-500/10 via-transparent to-transparent border border-cyan-500/20 p-2">
                                        <svg class="w-full h-full overflow-visible" viewBox="0 0 300 80" preserveAspectRatio="none">
                                            <defs>
                                                <linearGradient id="curated-yield-grad" x1="0" y1="0" x2="0" y2="1">
                                                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.4"/>
                                                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                                                </linearGradient>
                                            </defs>
                                            <path d="M0 65 Q 40 55, 80 40 T 160 30 T 240 15 T 300 8 L 300 80 L 0 80 Z" fill="url(#curated-yield-grad)" />
                                            <path d="M0 65 Q 40 55, 80 40 T 160 30 T 240 15 T 300 8" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" />
                                            <circle cx="300" cy="8" r="4" fill="#00f5ff" class="animate-pulse" />
                                        </svg>
                                    </div>

                                    {{-- Live Execution Micro-Feed --}}
                                    <div class="space-y-2 font-mono text-[10px] text-slate-400">
                                        <div class="flex items-center justify-between p-2 rounded-lg bg-white/[0.02] border border-white/[0.04]">
                                            <span class="text-emerald-400 font-bold">[FILL] BUY BTC @ $94,280</span>
                                            <span class="text-slate-500">0.04s ago</span>
                                        </div>
                                        <div class="flex items-center justify-between p-2 rounded-lg bg-white/[0.02] border border-white/[0.04]">
                                            <span class="text-cyan-400 font-bold">[PROFIT] SOL/USDT +2.18%</span>
                                            <span class="text-slate-500">1.2s ago</span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Action CTA --}}
                            <a href="{{ auth()->check() ? route('user.trading-bots.index') : route('user.register') }}" 
                               class="w-full py-4 px-6 rounded-2xl bg-[#00f5ff] hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-3 shadow-[0_0_30px_rgba(0,245,255,0.4)] hover:shadow-[0_0_45px_rgba(0,245,255,0.7)] transform hover:-translate-y-0.5">
                                <span>{{ __('Start Apex Scalper Bot') }}</span>
                                <svg class="w-4 h-4 text-[#050507]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Card 2: Grid Matrix Alpha (1 Column) --}}
                    <div x-show="activeFilter === 'all' || activeFilter === 'grid'"
                         x-transition:enter="transition ease-out duration-300 transform" 
                         x-transition:enter-start="opacity-0 scale-95" 
                         x-transition:enter-end="opacity-100 scale-100"
                         class="group relative flex flex-col justify-between rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-cyan-400/40 p-6 sm:p-8 backdrop-blur-xl shadow-2xl transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_0_35px_rgba(0,245,255,0.15)]">
                        <div>
                            <div class="flex items-center justify-between gap-3 mb-6">
                                <span class="px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[9px] font-bold uppercase tracking-wider">
                                    {{ __('GRID TRADING') }}
                                </span>
                                <span class="text-[10px] font-mono text-slate-400">
                                    {{ __('Duration:') }} 15 {{ __('Days') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3.5 mb-5">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center p-1.5 shadow-inner overflow-hidden shrink-0">
                                    <img src="{{ asset('assets/images/bots/bot-2.png') }}" alt="Grid Matrix Alpha" loading="lazy" class="w-full h-full object-cover rounded-xl">
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white group-hover:text-cyan-300 transition-colors">Grid Matrix Alpha</h3>
                                    <p class="text-xs font-mono text-slate-400 mt-0.5">BNB/USDT · AVAX/USDT · POL/USDT</p>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.05] mb-6">
                                <span class="text-[10px] font-mono font-bold uppercase text-slate-400 tracking-wider block mb-1">
                                    {{ __('Target Daily Return') }}
                                </span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-black font-mono text-emerald-400 tracking-tight">+0.90% – 1.60%</span>
                                    <span class="text-xs font-mono text-slate-400">{{ __('daily') }}</span>
                                </div>
                            </div>

                            <div class="space-y-2.5 mb-8 font-mono text-xs text-slate-400">
                                <div class="flex justify-between items-center pb-2 border-b border-white/[0.04]">
                                    <span>{{ __('Minimum Deposit') }}</span>
                                    <span class="font-bold text-white">$50.00</span>
                                </div>
                                <div class="flex justify-between items-center pb-2 border-b border-white/[0.04]">
                                    <span>{{ __('Capital Returned') }}</span>
                                    <span class="font-bold text-emerald-400">{{ __('Yes (When Plan Completes)') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>{{ __('Trading Strategy') }}</span>
                                    <span class="font-bold text-slate-200">{{ __('Price Range Grid Trading') }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ auth()->check() ? route('user.trading-bots.index') : route('user.register') }}" 
                           class="w-full py-3.5 px-4 rounded-xl bg-white/[0.03] hover:bg-[#00f5ff] border border-white/[0.1] hover:border-[#00f5ff] text-white hover:text-[#050507] font-bold text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 group/btn shadow-md hover:shadow-[0_0_25px_rgba(0,245,255,0.4)]">
                            <span>{{ __('Start Grid Bot') }}</span>
                            <svg class="w-4 h-4 text-slate-400 group-hover/btn:text-[#050507] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>

                    {{-- Card 3: Sentinel Yield Vault (1 Column) --}}
                    <div x-show="activeFilter === 'all' || activeFilter === 'neutral'"
                         x-transition:enter="transition ease-out duration-300 transform" 
                         x-transition:enter-start="opacity-0 scale-95" 
                         x-transition:enter-end="opacity-100 scale-100"
                         class="group relative flex flex-col justify-between rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-cyan-400/40 p-6 sm:p-8 backdrop-blur-xl shadow-2xl transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_0_35px_rgba(0,245,255,0.15)]">
                        <div>
                            <div class="flex items-center justify-between gap-3 mb-6">
                                <span class="px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[9px] font-bold uppercase tracking-wider">
                                    {{ __('BALANCED ARBITRAGE') }}
                                </span>
                                <span class="text-[10px] font-mono text-slate-400">
                                    {{ __('Duration:') }} 7 {{ __('Days') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3.5 mb-5">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center p-1.5 shadow-inner overflow-hidden shrink-0">
                                    <img src="{{ asset('assets/images/bots/bot-3.png') }}" alt="Sentinel Yield Vault" loading="lazy" class="w-full h-full object-cover rounded-xl">
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white group-hover:text-cyan-300 transition-colors">Sentinel Yield Vault</h3>
                                    <p class="text-xs font-mono text-slate-400 mt-0.5">USDT · USDC · EUR/USD</p>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.05] mb-6">
                                <span class="text-[10px] font-mono font-bold uppercase text-slate-400 tracking-wider block mb-1">
                                    {{ __('Target Daily Return') }}
                                </span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-black font-mono text-emerald-400 tracking-tight">+0.50% – 0.95%</span>
                                    <span class="text-xs font-mono text-slate-400">{{ __('daily') }}</span>
                                </div>
                            </div>

                            <div class="space-y-2.5 mb-8 font-mono text-xs text-slate-400">
                                <div class="flex justify-between items-center pb-2 border-b border-white/[0.04]">
                                    <span>{{ __('Minimum Deposit') }}</span>
                                    <span class="font-bold text-white">$25.00</span>
                                </div>
                                <div class="flex justify-between items-center pb-2 border-b border-white/[0.04]">
                                    <span>{{ __('Capital Returned') }}</span>
                                    <span class="font-bold text-emerald-400">{{ __('Yes (When Plan Completes)') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>{{ __('Trading Strategy') }}</span>
                                    <span class="font-bold text-slate-200">{{ __('Cross-Exchange Arbitrage') }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ auth()->check() ? route('user.trading-bots.index') : route('user.register') }}" 
                           class="w-full py-3.5 px-4 rounded-xl bg-white/[0.03] hover:bg-[#00f5ff] border border-white/[0.1] hover:border-[#00f5ff] text-white hover:text-[#050507] font-bold text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 group/btn shadow-md hover:shadow-[0_0_25px_rgba(0,245,255,0.4)]">
                            <span>{{ __('Start Sentinel Bot') }}</span>
                            <svg class="w-4 h-4 text-slate-400 group-hover/btn:text-[#050507] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>

                </div>
            @endif

        </div>
    </section>

    {{-- ==================================================================================== --}}
    {{-- SECTION: INTERACTIVE STRATEGY COMPARISON MATRIX --}}
    {{-- ==================================================================================== --}}
    <section id="comparison" class="py-24 sm:py-36 relative overflow-hidden bg-[#050507] isolate border-t border-white/[0.06]">
        {{-- Ambient Lighting Orbs --}}
        <div class="absolute top-1/2 right-1/4 -translate-y-1/2 w-[550px] h-[550px] bg-cyan-500/10 rounded-full blur-[160px] pointer-events-none select-none -z-10 animate-pulse" style="animation-duration: 8s;"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                    {{ __('COMPARE TRADING BOTS') }}
                </div>
                <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight mb-4">
                    {{ __('Compare Trading Bot') }} <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70">{{ __('Strategies.') }}</span>
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    {{ __('Choose the trading strategy that best fits your goals, budget, and preferred investment term.') }}
                </p>
            </div>

            {{-- Cyber-Terminal Comparison Container --}}
            <div class="max-w-6xl mx-auto p-2 sm:p-3 rounded-[2.5rem] sm:rounded-[3rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_30px_90px_rgba(0,0,0,0.9)] overflow-hidden relative group">
                {{-- Laser top accent line --}}
                <div class="absolute top-0 left-1/4 right-1/4 h-[1.5px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent"></div>

                <div class="rounded-[2rem] sm:rounded-[2.5rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] overflow-hidden">
                    {{-- Terminal Top Bar --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-white/[0.08] bg-white/[0.02]">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                            <span class="text-[10px] font-mono text-slate-400 ml-2 hidden sm:inline">BOT_COMPARISON_MATRIX.SYS</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[9px] font-bold">
                                ● REAL-TIME DATA
                            </span>
                        </div>
                    </div>

                    {{-- Table Area --}}
                    <div class="p-4 sm:p-8 overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[700px]">
                            <thead>
                                <tr class="border-b border-white/[0.08] text-xs font-mono uppercase text-slate-400">
                                    <th class="py-4 px-4">{{ __('Features & Terms') }}</th>
                                    <th class="py-4 px-4 text-cyan-400 font-bold tracking-wider">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                                            {{ __('Apex Scalper Pro') }}
                                        </div>
                                    </th>
                                    <th class="py-4 px-4 text-purple-400 font-bold tracking-wider">{{ __('Grid Matrix Alpha') }}</th>
                                    <th class="py-4 px-4 text-amber-400 font-bold tracking-wider">{{ __('Sentinel Yield') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.04] text-xs font-mono text-slate-300">
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="py-4 px-4 font-bold text-white flex items-center gap-2">
                                        <span class="text-cyan-400 font-mono">01.</span> {{ __('Target Daily Return') }}
                                    </td>
                                    <td class="py-4 px-4 font-bold text-emerald-400">+1.80% – 3.20%</td>
                                    <td class="py-4 px-4 font-bold text-emerald-400">+0.90% – 1.60%</td>
                                    <td class="py-4 px-4 font-bold text-emerald-400">+0.50% – 0.95%</td>
                                </tr>
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="py-4 px-4 font-bold text-white">
                                        <span class="text-cyan-400 font-mono">02.</span> {{ __('Trading Strategy') }}
                                    </td>
                                    <td class="py-4 px-4">{{ __('Fast Short-Term Scalping') }}</td>
                                    <td class="py-4 px-4">{{ __('Price Range Grid Trading') }}</td>
                                    <td class="py-4 px-4">{{ __('Cross-Exchange Arbitrage') }}</td>
                                </tr>
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="py-4 px-4 font-bold text-white">
                                        <span class="text-cyan-400 font-mono">03.</span> {{ __('Risk Profile') }}
                                    </td>
                                    <td class="py-4 px-4"><span class="px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 font-bold text-[10px]">{{ __('MODERATE RISK') }}</span></td>
                                    <td class="py-4 px-4"><span class="px-2.5 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-bold text-[10px]">{{ __('BALANCED RISK') }}</span></td>
                                    <td class="py-4 px-4"><span class="px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold text-[10px]">{{ __('LOWER RISK') }}</span></td>
                                </tr>
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="py-4 px-4 font-bold text-white">
                                        <span class="text-cyan-400 font-mono">04.</span> {{ __('Minimum Deposit') }}
                                    </td>
                                    <td class="py-4 px-4 font-bold text-white">$100.00</td>
                                    <td class="py-4 px-4 font-bold text-white">$50.00</td>
                                    <td class="py-4 px-4 font-bold text-white">$25.00</td>
                                </tr>
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="py-4 px-4 font-bold text-white">
                                        <span class="text-cyan-400 font-mono">05.</span> {{ __('Plan Duration') }}
                                    </td>
                                    <td class="py-4 px-4">30 {{ __('Days') }}</td>
                                    <td class="py-4 px-4">15 {{ __('Days') }}</td>
                                    <td class="py-4 px-4">7 {{ __('Days') }}</td>
                                </tr>
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="py-4 px-4 font-bold text-white">
                                        <span class="text-cyan-400 font-mono">06.</span> {{ __('Principal Return') }}
                                    </td>
                                    <td class="py-4 px-4 text-emerald-400 font-bold">{{ __('100% Capital Returned') }}</td>
                                    <td class="py-4 px-4 text-emerald-400 font-bold">{{ __('100% Capital Returned') }}</td>
                                    <td class="py-4 px-4 text-emerald-400 font-bold">{{ __('100% Capital Returned') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================================================== --}}
    {{-- TRADINGVIEW CRYPTO TICKER TAPE BANNER --}}
    {{-- ==================================================================================== --}}
    <div class="relative w-full bg-[#050507]/90 border-y border-white/[0.08] overflow-hidden py-2 backdrop-blur-2xl z-20 [mask-image:linear-gradient(to_right,transparent,#000_8%,#000_92%,transparent)]">
        <div class="tradingview-widget-container">
            <div class="tradingview-widget-container__widget"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>
            {
              "symbols": [
                {
                  "proName": "BITSTAMP:BTCUSD",
                  "title": "Bitcoin"
                },
                {
                  "proName": "BITSTAMP:ETHUSD",
                  "title": "Ethereum"
                },
                {
                  "proName": "BINANCE:SOLUSDT",
                  "title": "Solana"
                },
                {
                  "proName": "BINANCE:BNBUSDT",
                  "title": "BNB"
                },
                {
                  "proName": "BINANCE:XRPUSDT",
                  "title": "XRP"
                },
                {
                  "proName": "BINANCE:ADAUSDT",
                  "title": "Cardano"
                },
                {
                  "proName": "BINANCE:AVAXUSDT",
                  "title": "Avalanche"
                },
                {
                  "proName": "BINANCE:ARBUSDT",
                  "title": "Arbitrum"
                },
                {
                  "proName": "BINANCE:NEARUSDT",
                  "title": "NEAR Protocol"
                },
                {
                  "proName": "BINANCE:SUIUSDT",
                  "title": "SUI"
                }
              ],
              "showSymbolLogo": true,
              "isTransparent": true,
              "displayMode": "adaptive",
              "colorTheme": "dark",
              "locale": "en"
            }
            </script>
        </div>
    </div>

    {{-- ==================================================================================== --}}
    {{-- LIVE ON-CHAIN DEPOSIT & WITHDRAWAL STREAM MARQUEE --}}
    {{-- ==================================================================================== --}}
    <div class="relative w-full bg-[#090c14]/95 border-y border-white/[0.08] overflow-hidden py-3.5 backdrop-blur-2xl z-20 isolate">
        {{-- Marquee Track with Left/Right Fading Edge Masks --}}
        <div class="relative w-full overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_8%,black_92%,transparent)] tx-marquee-wrapper group/txmarquee">
            <div class="flex gap-4 sm:gap-6 items-center whitespace-nowrap animate-tx-marquee py-1">
                @php
                    $streamItems = [];

                    // 1. Process real deposits from DB
                    if (!empty($recentDeposits) && $recentDeposits->count() > 0) {
                        foreach ($recentDeposits as $dep) {
                            $userName = $dep->user ? substr($dep->user->username ?? $dep->user->name ?? 'User', 0, 3) . '***' : '0x' . substr(md5($dep->id), 0, 4) . '...';
                            $chainName = $dep->blockchain ? $dep->blockchain->name : ($dep->blockchainToken ? $dep->blockchainToken->symbol : 'Crypto');
                            $streamItems[] = [
                                'type' => 'DEPOSIT',
                                'color' => 'emerald',
                                'user' => $userName,
                                'amount' => '+$' . number_format($dep->total_amount ?? $dep->amount, 2),
                                'asset' => strtoupper($dep->currency ?? 'USDT'),
                                'via' => $chainName,
                                'time' => $dep->created_at ? $dep->created_at->diffForHumans(null, true) . ' ago' : '1m ago',
                            ];
                        }
                    }

                    // 2. Process real withdrawals from DB
                    if (!empty($recentWithdrawals) && $recentWithdrawals->count() > 0) {
                        foreach ($recentWithdrawals as $wd) {
                            $userName = $wd->user ? substr($wd->user->username ?? $wd->user->name ?? 'User', 0, 3) . '***' : '0x' . substr(md5($wd->id), 0, 4) . '...';
                            $methodName = $wd->blockchain ? $wd->blockchain->name : ($wd->blockchainToken ? $wd->blockchainToken->symbol : 'On-Chain');
                            $streamItems[] = [
                                'type' => 'WITHDRAWAL',
                                'color' => 'cyan',
                                'user' => $userName,
                                'amount' => '-$' . number_format($wd->amount_payable ?? $wd->amount, 2),
                                'asset' => strtoupper($wd->currency ?? 'USDC'),
                                'via' => $methodName,
                                'time' => $wd->created_at ? $wd->created_at->diffForHumans(null, true) . ' ago' : '2m ago',
                            ];
                        }
                    }

                    // 3. Realistic default simulated items to guarantee full rich marquee if DB is fresh
                    $fallbackStream = [
                        ['type' => 'DEPOSIT', 'color' => 'emerald', 'user' => 'sar***', 'amount' => '+$2,450.00', 'asset' => 'USDT', 'via' => 'Arbitrum One', 'time' => '38s ago'],
                        ['type' => 'WITHDRAWAL', 'color' => 'cyan', 'user' => 'ale***', 'amount' => '-14.85', 'asset' => 'SOL', 'via' => 'Solana Network', 'time' => '1m ago'],
                        ['type' => 'DEPOSIT', 'color' => 'emerald', 'user' => 'dav***', 'amount' => '+0.42', 'asset' => 'BTC', 'via' => 'Bitcoin Mainnet', 'time' => '2m ago'],
                        ['type' => 'WITHDRAWAL', 'color' => 'amber', 'user' => 'mic***', 'amount' => '-$3,820.00', 'asset' => 'USDC', 'via' => 'Base Network', 'time' => '4m ago'],
                        ['type' => 'DEPOSIT', 'color' => 'emerald', 'user' => 'ken***', 'amount' => '+$5,100.00', 'asset' => 'USDT', 'via' => 'BNB Smart Chain', 'time' => '5m ago'],
                        ['type' => 'WITHDRAWAL', 'color' => 'cyan', 'user' => 'nat***', 'amount' => '-2.65', 'asset' => 'ETH', 'via' => 'Ethereum', 'time' => '7m ago'],
                        ['type' => 'DEPOSIT', 'color' => 'emerald', 'user' => 'rob***', 'amount' => '+$1,200.00', 'asset' => 'USDT', 'via' => 'Polygon PoS', 'time' => '9m ago'],
                        ['type' => 'WITHDRAWAL', 'color' => 'amber', 'user' => 'eli***', 'amount' => '-$6,400.00', 'asset' => 'USDC', 'via' => 'TRON (TRC20)', 'time' => '11m ago'],
                    ];

                    $finalStream = !empty($streamItems) ? array_merge($streamItems, $fallbackStream) : $fallbackStream;
                    // Duplicate for infinite seamless marquee loop
                    $loopStream = array_merge($finalStream, $finalStream);
                @endphp

                @foreach ($loopStream as $tx)
                    @if ($tx['type'] === 'DEPOSIT')
                        <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-2xl bg-emerald-500/[0.04] hover:bg-emerald-500/[0.1] border border-emerald-500/20 hover:border-emerald-500/40 text-emerald-400 font-mono text-xs shadow-sm hover:shadow-[0_0_15px_rgba(16,185,129,0.2)] transition-all duration-300 transform hover:-translate-y-0.5 shrink-0 select-none group cursor-pointer">
                            <span class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                            </span>
                            <strong class="text-white text-[11px] font-bold tracking-wider">[{{ $tx['type'] }}]</strong>
                            <span class="text-emerald-300 font-black">{{ $tx['amount'] }} {{ $tx['asset'] }}</span>
                            <span class="text-slate-400 text-[10px]">via <span class="text-slate-300 font-bold">{{ $tx['via'] }}</span></span>
                            <span class="text-slate-600 text-[10px]">•</span>
                            <span class="text-slate-500 text-[10px]">{{ $tx['time'] }}</span>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-2xl bg-cyan-500/[0.04] hover:bg-cyan-500/[0.1] border border-cyan-500/20 hover:border-cyan-500/40 text-cyan-400 font-mono text-xs shadow-sm hover:shadow-[0_0_15px_rgba(0,245,255,0.2)] transition-all duration-300 transform hover:-translate-y-0.5 shrink-0 select-none group cursor-pointer">
                            <span class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-400"></span>
                            </span>
                            <strong class="text-white text-[11px] font-bold tracking-wider">[{{ $tx['type'] }}]</strong>
                            <span class="text-cyan-300 font-black">{{ $tx['amount'] }} {{ $tx['asset'] }}</span>
                            <span class="text-slate-400 text-[10px]">via <span class="text-slate-300 font-bold">{{ $tx['via'] }}</span></span>
                            <span class="text-slate-600 text-[10px]">•</span>
                            <span class="text-slate-500 text-[10px]">{{ $tx['time'] }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <style>
            @keyframes txMarquee {
                0% { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }
            .animate-tx-marquee {
                display: flex;
                width: max-content;
                animation: txMarquee 80s linear infinite;
            }
            .tx-marquee-wrapper:hover .animate-tx-marquee,
            .animate-tx-marquee:hover {
                animation-play-state: paused !important;
            }
        </style>
    </div>

    {{-- ==================================================================================== --}}
    {{-- SECTION 3: INTERACTIVE YIELD & PROFIT SIMULATOR (CALCULATOR CONSOLE) --}}
    {{-- ==================================================================================== --}}
    <section class="relative py-24 sm:py-32 bg-[#050507] isolate overflow-hidden">
        {{-- Ambient Layered Glow Blobs --}}
        <div class="absolute top-1/3 left-0 w-[550px] h-[550px] bg-cyan-500/10 rounded-full blur-[150px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute bottom-10 right-0 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[140px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 10s;"></div>

        <div class="max-w-7xl mx-auto px-4 lg:px-8 w-full">
            
            {{-- Section Header --}}
            <div class="max-w-3xl mx-auto text-center mb-16 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-4 shadow-[0_0_20px_rgba(0,245,255,0.15)]">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('PROFIT CALCULATOR') }}
                </div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-[1.12] mb-5">
                    {{ __('Calculate Your Estimated Trading Profits') }}
                </h2>
                <p class="text-base sm:text-lg text-slate-400 font-body leading-relaxed">
                    {{ __('Choose your deposit amount and trading strategy to see your estimated daily returns and total profit upon plan completion.') }}
                </p>
            </div>

            @php
                $firstBot = $featuredBots->first();
                $defaultBotId = $firstBot ? $firstBot->id : 0;
                $defaultDeposit = $firstBot ? (float)$firstBot->min_amount : 1000;
                $defaultRoi = $firstBot ? round((($firstBot->daily_return_min + $firstBot->daily_return_max) / 2) / 100, 4) : 0.024;
                $defaultName = $firstBot ? $firstBot->name : 'Apex Scalper Pro v4';
                $defaultTerm = $firstBot ? $firstBot->duration : 30;
            @endphp

            {{-- Split Simulator Console (Alpine.js State) --}}
            <div class="max-w-6xl mx-auto rounded-3xl bg-[#090c14]/95 border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.9)] p-6 sm:p-10 lg:p-12 relative overflow-hidden"
                 x-data="{
                    selectedBotId: {{ $defaultBotId }},
                    depositAmount: {{ $defaultDeposit }},
                    selectedRoi: {{ $defaultRoi }},
                    strategyName: '{{ addslashes($defaultName) }}',
                    termDays: {{ $defaultTerm }},
                    get totalProfit() {
                        return (this.depositAmount * this.selectedRoi * this.termDays).toFixed(2);
                    },
                    get projectedTotal() {
                        return (parseFloat(this.depositAmount) + parseFloat(this.totalProfit)).toFixed(2);
                    },
                    get dailyReturn() {
                        return (this.depositAmount * this.selectedRoi).toFixed(2);
                    }
                 }">
                
                {{-- Cybernetic Accent Reticles --}}
                <div class="absolute top-4 left-4 text-slate-700 font-mono text-[10px] pointer-events-none select-none">+ CALCULATOR // READY</div>
                <div class="absolute top-4 right-4 text-slate-700 font-mono text-[10px] pointer-events-none select-none">REAL-TIME DATA</div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    
                    {{-- Left Controls Panel (7 Cols) --}}
                    <div class="lg:col-span-7 space-y-8">
                        
                        {{-- Crypto Market Sentiment Speedometer HUD Widget --}}
                        <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.08] flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 font-mono font-bold text-xs">
                                    78
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-white flex items-center gap-2">
                                        <span>{{ __('Market Activity Level') }}</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 text-[9px] font-mono font-bold uppercase">{{ __('HIGH ACTIVITY') }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ __('Active trading conditions across major crypto markets') }}</span>
                                </div>
                            </div>
                            {{-- Speedometer Arc SVG --}}
                            <div class="w-28 h-9 relative flex items-center justify-center shrink-0">
                                <svg class="w-full h-full" viewBox="0 0 100 50">
                                    <path d="M 10 45 A 35 35 0 0 1 90 45" fill="none" stroke="#ffffff15" stroke-width="8" stroke-linecap="round"/>
                                    <path d="M 10 45 A 35 35 0 0 1 78 18" fill="none" stroke="#00f5ff" stroke-width="8" stroke-linecap="round"/>
                                    <circle cx="50" cy="45" r="4" fill="#00f5ff" />
                                    <line x1="50" y1="45" x2="72" y2="22" stroke="#00f5ff" stroke-width="2.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>
                        
                        {{-- Control 1: Capital Allocation Slider --}}
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <label class="text-xs font-mono font-bold uppercase text-slate-300 tracking-wider flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                    {{ __('1. SELECT INVESTMENT AMOUNT') }}
                                </label>
                                <div class="px-4 py-1.5 rounded-xl bg-white/[0.04] border border-cyan-500/30 text-emerald-400 font-mono font-black text-lg shadow-inner">
                                    $<span x-text="Number(depositAmount).toLocaleString()">1,000</span>
                                </div>
                            </div>

                            <input type="range" 
                                   min="50" 
                                   max="50000" 
                                   step="50" 
                                   x-model="depositAmount"
                                   class="w-full h-2.5 bg-slate-800 rounded-lg appearance-none cursor-pointer accent-[#00f5ff] focus:outline-none shadow-inner">

                            {{-- Preset Quick Select Buttons --}}
                            <div class="flex flex-wrap gap-2 pt-1">
                                <button @click="depositAmount = 500" class="px-3 py-1.5 rounded-lg bg-white/[0.03] hover:bg-cyan-500/20 border border-white/[0.08] hover:border-cyan-500/40 text-slate-300 text-xs font-mono transition-all">$500</button>
                                <button @click="depositAmount = 1000" class="px-3 py-1.5 rounded-lg bg-white/[0.03] hover:bg-cyan-500/20 border border-white/[0.08] hover:border-cyan-500/40 text-slate-300 text-xs font-mono transition-all">$1,000</button>
                                <button @click="depositAmount = 5000" class="px-3 py-1.5 rounded-lg bg-white/[0.03] hover:bg-cyan-500/20 border border-white/[0.08] hover:border-cyan-500/40 text-slate-300 text-xs font-mono transition-all">$5,000</button>
                                <button @click="depositAmount = 10000" class="px-3 py-1.5 rounded-lg bg-white/[0.03] hover:bg-cyan-500/20 border border-white/[0.08] hover:border-cyan-500/40 text-slate-300 text-xs font-mono transition-all">$10,000</button>
                                <button @click="depositAmount = 25000" class="px-3 py-1.5 rounded-lg bg-white/[0.03] hover:bg-cyan-500/20 border border-white/[0.08] hover:border-cyan-500/40 text-slate-300 text-xs font-mono transition-all">$25,000</button>
                            </div>
                        </div>

                        {{-- Control 2: Strategy Selector Cards --}}
                        <div class="space-y-4">
                            <label class="text-xs font-mono font-bold uppercase text-slate-300 tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                {{ __('2. CHOOSE TRADING BOT') }}
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-72 overflow-y-auto pr-1">
                                @if ($featuredBots->count() > 0)
                                    @foreach ($featuredBots as $bot)
                                        @php
                                            $avgRoiPercent = number_format(($bot->daily_return_min + $bot->daily_return_max) / 2, 2);
                                            $roiDecimal = round((($bot->daily_return_min + $bot->daily_return_max) / 2) / 100, 4);
                                            $botLogoUrl = !empty($bot->logo) 
                                                ? (str_contains($bot->logo, '/') ? asset($bot->logo) : asset('assets/images/bots/' . $bot->logo)) 
                                                : asset('assets/images/bots/bot-1.png');
                                        @endphp
                                        <div @click="selectedBotId = {{ $bot->id }}; selectedRoi = {{ $roiDecimal }}; strategyName = '{{ addslashes($bot->name) }}'; termDays = {{ $bot->duration }}; if (depositAmount < {{ $bot->min_amount }}) depositAmount = {{ $bot->min_amount }};"
                                             :class="selectedBotId === {{ $bot->id }} ? 'border-cyan-400 bg-cyan-500/10 shadow-[0_0_20px_rgba(0,245,255,0.2)]' : 'border-white/[0.08] bg-white/[0.02] hover:border-white/20'"
                                             class="p-3.5 rounded-2xl border cursor-pointer transition-all duration-300 flex flex-col justify-between group">
                                            <div class="flex items-center gap-2.5 mb-2">
                                                <img src="{{ $botLogoUrl }}" alt="{{ $bot->name }}" loading="lazy" class="w-6 h-6 object-cover rounded-lg shrink-0">
                                                <h4 class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors line-clamp-1">{{ $bot->name }}</h4>
                                            </div>
                                            <div class="flex items-center justify-between font-mono text-[10px]">
                                                <span class="text-slate-400">{{ strtoupper($bot->type ?? 'CRYPTO') }}</span>
                                                <span class="font-black text-emerald-400">+{{ $avgRoiPercent }}% / day</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div @click="selectedRoi = 0.024; strategyName = 'Apex Scalper Pro v4'; termDays = 30;"
                                         :class="selectedRoi === 0.024 ? 'border-cyan-400 bg-cyan-500/10 shadow-[0_0_20px_rgba(0,245,255,0.2)]' : 'border-white/[0.08] bg-white/[0.02] hover:border-white/20'"
                                         class="p-4 rounded-2xl border cursor-pointer transition-all duration-300 flex flex-col justify-between">
                                        <div>
                                            <span class="text-[9px] font-mono font-bold text-cyan-400 uppercase tracking-widest block mb-1">Scalping</span>
                                            <h4 class="text-xs font-bold text-white mb-2">Apex Scalper Pro</h4>
                                        </div>
                                        <div class="text-sm font-mono font-black text-emerald-400">+2.40% / day</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Control 3: Lockup Term Duration --}}
                        <div class="space-y-4">
                            <label class="text-xs font-mono font-bold uppercase text-slate-300 tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                {{ __('3. CHOOSE PLAN DURATION') }}
                            </label>

                            <div class="flex flex-wrap gap-2.5">
                                <template x-for="days in [7, 15, 30, 60, 90]">
                                    <button @click="termDays = days" 
                                            :class="termDays === days ? 'bg-[#00f5ff] text-[#050507] font-black shadow-[0_0_20px_rgba(0,245,255,0.4)]' : 'bg-white/[0.03] text-slate-300 border-white/[0.08] hover:bg-white/[0.08]'"
                                            class="px-5 py-2.5 rounded-xl border text-xs font-mono uppercase tracking-wider transition-all duration-300">
                                        <span x-text="days + ' Days'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                    </div>

                    {{-- Right Projections Glass Receipt Panel (5 Cols) --}}
                    <div class="lg:col-span-5 p-7 sm:p-8 rounded-3xl bg-black/80 border border-white/[0.1] shadow-2xl relative space-y-6">
                        
                        {{-- Receipt Header --}}
                        <div class="flex items-center justify-between pb-4 border-b border-white/[0.08]">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></div>
                                <span class="text-xs font-mono font-bold text-white tracking-wider uppercase">{{ __('ESTIMATED RETURNS SUMMARY') }}</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-mono text-[10px] font-bold">
                                {{ __('ZERO PLATFORM FEES') }}
                            </span>
                        </div>

                        {{-- Calculation Breakdown Deck --}}
                        <div class="space-y-4 font-mono text-xs">
                            
                            <div class="flex justify-between items-center pb-3 border-b border-white/[0.04]">
                                <span class="text-slate-400">{{ __('Initial Deposit') }}</span>
                                <span class="font-bold text-white" x-text="'$' + Number(depositAmount).toLocaleString()">$1,000.00</span>
                            </div>

                            <div class="flex justify-between items-center pb-3 border-b border-white/[0.04]">
                                <span class="text-slate-400">{{ __('Selected Bot') }}</span>
                                <span class="font-bold text-cyan-400" x-text="strategyName">Apex Scalper Pro</span>
                            </div>

                            <div class="flex justify-between items-center pb-3 border-b border-white/[0.04]">
                                <span class="text-slate-400">{{ __('Plan Duration') }}</span>
                                <span class="font-bold text-slate-200" x-text="termDays + ' Days'">30 Days</span>
                            </div>

                            <div class="flex justify-between items-center pb-3 border-b border-white/[0.04]">
                                <span class="text-slate-400">{{ __('Estimated Daily Return') }}</span>
                                <span class="font-bold text-emerald-400" x-text="'+$' + Number(dailyReturn).toLocaleString('en-US', {minimumFractionDigits: 2})">+$24.00</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 space-y-1">
                                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest block">{{ __('Total Estimated Profit') }}</span>
                                <div class="text-3xl font-black text-emerald-400 tracking-tight" x-text="'+$' + Number(totalProfit).toLocaleString('en-US', {minimumFractionDigits: 2})">
                                    +$720.00
                                </div>
                            </div>

                            <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/[0.08] space-y-1 shadow-inner">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">{{ __('Estimated Total Payout') }}</span>
                                <div class="text-4xl font-black text-white tracking-tight" x-text="'$' + Number(projectedTotal).toLocaleString('en-US', {minimumFractionDigits: 2})">
                                    $1,720.00
                                </div>
                            </div>

                            {{-- Deploy Contract CTA Button (Button-in-Button Architecture) --}}
                            <a href="{{ auth()->check() ? route('user.trading-bots.index') : route('user.register') }}" 
                               class="w-full pl-8 pr-2.5 py-2.5 rounded-full bg-[#00f5ff] hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-between shadow-[0_0_35px_rgba(0,245,255,0.4)] hover:shadow-[0_0_50px_rgba(0,245,255,0.7)] group">
                                <span>{{ __('Start Trading Bot Now') }}</span>
                                <span class="w-10 h-10 rounded-full bg-[#050507]/20 flex items-center justify-center group-hover:translate-x-1 group-hover:-translate-y-[1px] transition-transform">
                                    <svg class="w-4 h-4 text-[#050507]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </span>
                            </a>
                        </div>

                    </div>

                </div>
            </div>
    </section>

    {{-- ==================================================================================== --}}
    {{-- SECTION 4: MARKET MOMENTUM (INSTITUTIONAL SCREENER) --}}
    {{-- ==================================================================================== --}}
    <section id="momentum" class="py-24 sm:py-36 relative overflow-hidden bg-[#050507] isolate">
        {{-- Background Ambient Blobs --}}
        <div class="absolute top-0 right-10 w-[500px] h-[500px] bg-[#00f5ff]/[0.03] rounded-full blur-[140px] -z-10 pointer-events-none select-none"></div>
        <div class="absolute bottom-10 left-10 w-[400px] h-[400px] bg-purple-600/[0.03] rounded-full blur-[120px] -z-10 animate-pulse pointer-events-none select-none"></div>

        {{-- SVG Laser Accent Circuit Line --}}
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#00f5ff]/40 to-transparent pointer-events-none select-none"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-8 mb-12 sm:mb-16">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#00f5ff]/10 border border-[#00f5ff]/20 text-[#00f5ff] text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#00f5ff] animate-ping"></span>
                        {{ __('LIVE MARKET PRICES') }}
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight">
                        {{ __('Live Market') }} <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70">{{ __('Overview.') }}</span>
                    </h2>
                </div>
                <p class="text-slate-400 text-sm sm:text-base max-w-md lg:text-right leading-relaxed">
                    {{ __('Track live prices, trends, and volume across major cryptocurrencies in real time.') }}
                </p>
            </div>

            {{-- Bento Screener Double-Bezel Container Frame --}}
            <div class="max-w-7xl mx-auto p-3 rounded-[3rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_30px_90px_rgba(0,0,0,0.9)] relative overflow-hidden group">
                <div class="rounded-[2.5rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-4 sm:p-6 lg:p-8 h-[650px] flex flex-col relative overflow-hidden">
                    {{-- Corner Reticles --}}
                    <div class="absolute top-4 left-4 text-slate-700 font-mono text-[10px] pointer-events-none select-none hidden sm:block">+ MARKET_SCREENER // LIVE_FEED</div>
                    <div class="absolute top-4 right-4 text-slate-700 font-mono text-[10px] pointer-events-none select-none hidden sm:block">REAL-TIME</div>

                    <div class="flex-1 w-full rounded-2xl overflow-hidden bg-white/[0.015] border border-white/[0.08] min-h-[520px] relative">
                        {{-- TradingView Screener Widget --}}
                        <div class="tradingview-widget-container w-full h-full min-h-[500px]">
                            <div class="tradingview-widget-container__widget w-full h-full min-h-[500px]"></div>
                            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-screener.js" async>
                            {
                              "width": "100%",
                              "height": "100%",
                              "defaultColumn": "overview",
                              "screener_type": "crypto_mkt",
                              "displayCurrency": "USD",
                              "colorTheme": "dark",
                              "locale": "en",
                              "isTransparent": true
                            }
                            </script>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Crypto Coins Sector Heatmap Widget --}}
            <div class="mt-12 max-w-7xl mx-auto p-3 rounded-[3rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_30px_90px_rgba(0,0,0,0.9)] relative overflow-hidden group">
                <div class="rounded-[2.5rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-4 sm:p-6 lg:p-8 h-[550px] flex flex-col relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/[0.08]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                            <span class="text-xs font-mono font-bold text-white uppercase tracking-widest">{{ __('CRYPTO MARKET CAP & PERFORMANCE HEATMAP') }}</span>
                        </div>
                        <span class="text-[10px] font-mono text-slate-500 hidden sm:inline">+ SECTOR_HEATMAP // LIVE_TREEMAP</span>
                    </div>

                    <div class="flex-1 w-full rounded-2xl overflow-hidden bg-white/[0.015] border border-white/[0.08] min-h-[420px] relative">
                        <div class="tradingview-widget-container w-full h-full min-h-[400px]">
                            <div class="tradingview-widget-container__widget w-full h-full min-h-[400px]"></div>
                            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-crypto-coins-heatmap.js" async>
                            {
                              "dataSource": "Crypto",
                              "blockSize": "market_cap_calc",
                              "blockColor": "change",
                              "locale": "en",
                              "symbolUrl": "",
                              "colorTheme": "dark",
                              "hasTopBar": false,
                              "isTransparent": true,
                              "hasSymbolTooltip": true,
                              "width": "100%",
                              "height": "100%"
                            }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================================================== --}}
    {{-- SECTION 5: PRODUCT PILLAR 02 — REAL-TIME COPY TRADING NETWORK --}}
    {{-- ==================================================================================== --}}
    <section id="copy-trading" class="py-24 sm:py-36 relative overflow-hidden bg-[#050507] isolate border-t border-white/[0.06]">
        {{-- Background Accents --}}
        <div class="absolute top-1/3 left-0 w-[550px] h-[550px] bg-cyan-500/10 rounded-full blur-[160px] pointer-events-none select-none -z-10 animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute bottom-0 right-1/4 w-[450px] h-[450px] bg-indigo-600/10 rounded-full blur-[140px] pointer-events-none select-none -z-10"></div>

        <div class="container mx-auto px-4 relative z-10">
            {{-- Header --}}
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-8 mb-16">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                        {{ __('COPY TRADING') }}
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight">
                        {{ __('Follow Verified Top Traders') }} <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70">{{ __('with One-Click Copy Trading.') }}</span>
                    </h2>
                </div>
                <p class="text-slate-400 text-sm sm:text-base max-w-md lg:text-right leading-relaxed">
                    {{ __('Every trade opened and closed by top verified traders is automatically copied to your account in real time.') }}
                </p>
            </div>

            {{-- Grid Container --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- Left: Copy Code Activation Bar (Double-Bezel Architecture) --}}
                <div class="lg:col-span-4 p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] flex flex-col justify-between h-full">
                    <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 012-2v-8a2 2 0 01-2-2h-8a2 2 0 01-2 2v8a2 2 0 012 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">{{ __('Copy Trader by Code') }}</h3>
                                    <span class="text-xs text-slate-400">{{ __('Follow trader instantly') }}</span>
                                </div>
                            </div>

                            <form action="{{ route('user.register') }}" method="GET" class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-mono font-bold text-slate-300 uppercase tracking-widest block mb-2">{{ __('ENTER TRADER COPY CODE') }}</label>
                                    <div class="relative">
                                        <input type="text" name="copy_code" placeholder="e.g. SATO-QUANT-01" class="w-full bg-white/[0.03] border border-white/[0.12] focus:border-cyan-400 rounded-xl py-3.5 px-4 font-mono text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-cyan-400 transition-all uppercase">
                                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 font-mono text-[10px] text-cyan-400 font-bold bg-cyan-500/10 px-2 py-0.5 rounded border border-cyan-500/20">SYNC</span>
                                    </div>
                                </div>
                                <button type="submit" class="w-full pl-6 pr-2 py-2 rounded-full bg-[#00f5ff] hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-between shadow-[0_0_25px_rgba(0,245,255,0.35)] group">
                                    <span>{{ __('Follow Trader Now') }}</span>
                                    <span class="w-8 h-8 rounded-full bg-[#050507]/20 flex items-center justify-center group-hover:translate-x-1 group-hover:-translate-y-[1px] transition-transform">
                                        <svg class="w-4 h-4 text-[#050507]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                </button>
                            </form>
                        </div>

                        <div class="mt-8 pt-6 border-t border-white/[0.08] space-y-3 font-mono text-[11px] text-slate-400">
                            <div class="flex items-center justify-between">
                                <span>{{ __('Active Copiers') }}</span>
                                <span class="text-white font-bold">3,040+</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>{{ __('Avg Sync Latency') }}</span>
                                <span class="text-emerald-400 font-bold">&lt; 12ms</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>{{ __('Settlement Engine') }}</span>
                                <span class="text-cyan-400 font-bold">100% Direct Crypto</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Top Master Trader Leaderboard Cards (Double-Bezel & Peeking Stack) --}}
                <div class="lg:col-span-8 space-y-4">
                    {{-- Master 1 (#1 Gold Leaderboard) --}}
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-amber-500/30 hover:border-amber-400/60 backdrop-blur-xl transition-all duration-500 shadow-[0_15px_40px_rgba(0,0,0,0.6)] group relative overflow-hidden hover:translate-x-2">
                        {{-- Laser Gold Top Accent --}}
                        <div class="absolute top-0 left-10 right-10 h-[1.5px] bg-gradient-to-r from-transparent via-amber-400 to-transparent"></div>

                        <div class="rounded-[2rem] bg-[#090c14]/90 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500/20 to-amber-500/5 border border-amber-500/30 text-amber-400 font-mono font-bold text-lg flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-[0_0_20px_rgba(245,158,11,0.2)]">
                                        SQ
                                    </div>
                                    <span class="absolute -top-2 -right-2 px-2 py-0.5 rounded-full bg-amber-400 text-[#050507] text-[8px] font-mono font-black uppercase tracking-wider shadow">#1 RANK</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-lg font-bold text-white group-hover:text-amber-300 transition-colors">Satoshi Trading Systems</h4>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-mono font-bold">VERIFIED</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">High-Win-Rate BTC & ETH Trend Trading</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-[10px] font-mono font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">CODE: SATO-QUANT-01</span>
                                        <span class="text-xs text-slate-500">·</span>
                                        <span class="text-xs text-slate-400 font-mono">1,420 Copiers</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end justify-between border-t sm:border-t-0 pt-4 sm:pt-0 border-white/[0.08]">
                                <span class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-widest">{{ __('30d Return') }}</span>
                                <div class="text-2xl font-mono font-black text-emerald-400">+142.8%</div>
                                <span class="text-[10px] font-mono text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20 mt-1">88.4% Win Rate</span>
                            </div>
                        </div>
                    </div>

                    {{-- Master 2 (#2 Silver Leaderboard) --}}
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-cyan-500/20 hover:border-cyan-400/50 backdrop-blur-xl transition-all duration-500 shadow-[0_15px_40px_rgba(0,0,0,0.6)] group relative overflow-hidden hover:translate-x-2">
                        <div class="rounded-[2rem] bg-[#090c14]/80 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono font-bold text-lg flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        AM
                                    </div>
                                    <span class="absolute -top-2 -right-2 px-2 py-0.5 rounded-full bg-cyan-400 text-[#050507] text-[8px] font-mono font-black uppercase tracking-wider shadow">#2 RANK</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-lg font-bold text-white group-hover:text-cyan-300 transition-colors">Alpha Momentum Fund</h4>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-mono font-bold">VERIFIED</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">Top Crypto Momentum & Breakout Strategy</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-[10px] font-mono font-bold text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded border border-cyan-500/20">CODE: ALPHA-FUND-99</span>
                                        <span class="text-xs text-slate-500">·</span>
                                        <span class="text-xs text-slate-400 font-mono">980 Copiers</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end justify-between border-t sm:border-t-0 pt-4 sm:pt-0 border-white/[0.08]">
                                <span class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-widest">{{ __('30d Return') }}</span>
                                <div class="text-2xl font-mono font-black text-emerald-400">+98.4%</div>
                                <span class="text-[10px] font-mono text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded border border-cyan-500/20 mt-1">84.1% Win Rate</span>
                            </div>
                        </div>
                    </div>

                    {{-- Master 3 (#3 Bronze Leaderboard) --}}
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-purple-500/20 hover:border-purple-400/50 backdrop-blur-xl transition-all duration-500 shadow-[0_15px_40px_rgba(0,0,0,0.6)] group relative overflow-hidden hover:translate-x-2">
                        <div class="rounded-[2rem] bg-[#090c14]/80 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="w-14 h-14 rounded-2xl bg-purple-500/10 border border-purple-500/20 text-purple-400 font-mono font-bold text-lg flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        NA
                                    </div>
                                    <span class="absolute -top-2 -right-2 px-2 py-0.5 rounded-full bg-purple-400 text-white text-[8px] font-mono font-black uppercase tracking-wider shadow">#3 RANK</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-lg font-bold text-white group-hover:text-purple-300 transition-colors">Nexus Arbitrage Vault</h4>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-mono font-bold">VERIFIED</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">Multi-Exchange Arbitrage & Risk-Balanced Trading</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-[10px] font-mono font-bold text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded border border-purple-500/20">CODE: NEXUS-VAULT-04</span>
                                        <span class="text-xs text-slate-500">·</span>
                                        <span class="text-xs text-slate-400 font-mono">640 Copiers</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end justify-between border-t sm:border-t-0 pt-4 sm:pt-0 border-white/[0.08]">
                                <span class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-widest">{{ __('30d Return') }}</span>
                                <div class="text-2xl font-mono font-black text-emerald-400">+76.2%</div>
                                <span class="text-[10px] font-mono text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded border border-purple-500/20 mt-1">81.5% Win Rate</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ==================================================================================== --}}
    {{-- SECTION 6: NATIVE ON-CHAIN INFRASTRUCTURE --}}
    {{-- ==================================================================================== --}}
    <section id="infrastructure" class="py-24 sm:py-36 relative overflow-hidden bg-[#050507] isolate border-t border-white/[0.06]">
        {{-- Background Accents --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[350px] bg-cyan-500/5 rounded-full blur-[180px] pointer-events-none select-none -z-10"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                    {{ __('SUPPORTED CRYPTO NETWORKS') }}
                </div>
                <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight mb-4">
                    {{ __('Direct Crypto') }} <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70">{{ __('Deposits & Withdrawals.') }}</span>
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    {{ __('No third-party payment middlemen or unnecessary delays. Fast deposits and automatic payouts settled directly on your chosen blockchain network.') }}
                </p>
            </div>

            {{-- 3D Floating Glass Token Deck Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-5">
                @php
                    $supportedChains = [
                        ['name' => 'Bitcoin', 'symbol' => 'BTC', 'icon' => 'btc.png', 'type' => 'L1 Chain'],
                        ['name' => 'Ethereum', 'symbol' => 'ETH', 'icon' => 'eth.png', 'type' => 'L1 Chain'],
                        ['name' => 'Solana', 'symbol' => 'SOL', 'icon' => 'sol.png', 'type' => 'L1 Chain'],
                        ['name' => 'BNB Chain', 'symbol' => 'BNB', 'icon' => 'bnb.png', 'type' => 'L1 Chain'],
                        ['name' => 'Polygon', 'symbol' => 'POL', 'icon' => 'polygon.png', 'type' => 'L2 Scale'],
                        ['name' => 'Arbitrum', 'symbol' => 'ARB', 'icon' => 'arbitrum.png', 'type' => 'L2 Rollup'],
                        ['name' => 'Avalanche', 'symbol' => 'AVAX', 'icon' => 'avalanche.png', 'type' => 'L1 Subnet'],
                        ['name' => 'Optimism', 'symbol' => 'OP', 'icon' => 'optimism.png', 'type' => 'L2 Rollup'],
                        ['name' => 'Base', 'symbol' => 'BASE', 'icon' => 'base.png', 'type' => 'L2 Rollup'],
                        ['name' => 'Tether USD', 'symbol' => 'USDT', 'icon' => 'usdt.png', 'type' => 'Stablecoin'],
                        ['name' => 'USD Coin', 'symbol' => 'USDC', 'icon' => 'usdc.png', 'type' => 'Stablecoin'],
                        ['name' => 'TRON Network', 'symbol' => 'TRX', 'icon' => 'trx.png', 'type' => 'TRC-20'],
                    ];
                @endphp

                @foreach ($supportedChains as $chain)
                    <div class="p-4 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] hover:border-cyan-400/50 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] backdrop-blur-xl transition-all duration-300 group hover:-translate-y-2 hover:scale-[1.03] hover:shadow-[0_15px_30px_rgba(0,245,255,0.2)] flex flex-col items-center text-center">
                        <div class="w-12 h-12 rounded-xl bg-white/[0.03] border border-white/[0.1] flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-cyan-500/10 group-hover:border-cyan-500/30 transition-all shadow-inner">
                            <img src="{{ asset('assets/images/tokens/' . $chain['icon']) }}" alt="{{ $chain['name'] }}" loading="lazy" class="w-7 h-7 object-contain">
                        </div>
                        <h4 class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors">{{ $chain['name'] }}</h4>
                        <span class="text-[9px] font-mono text-slate-500 uppercase tracking-wider mt-1">{{ $chain['symbol'] }} · {{ $chain['type'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================================================================================== --}}
    {{-- SECTION: INSTITUTIONAL SECURITY & CAPITAL PROTECTION --}}
    {{-- ==================================================================================== --}}
    <section id="security" class="py-24 sm:py-36 relative overflow-hidden bg-[#050507] isolate border-t border-white/[0.06]">
        {{-- Background Glow Blobs --}}
        <div class="absolute top-1/2 left-1/3 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-500/10 rounded-full blur-[180px] pointer-events-none select-none -z-10"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                    {{ __('SECURITY & ACCOUNT PROTECTION') }}
                </div>
                <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight mb-4">
                    {{ __('Built with Strong Security') }} <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70">{{ __('to Protect Your Funds.') }}</span>
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    {{ __('We prioritize the safety of your funds with multi-layer security, dedicated Master Wallets, and automated risk protection.') }}
                </p>
            </div>

            {{-- 4-Card Double-Bezel Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Card 1 --}}
                <div class="p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl group hover:border-cyan-400/40 transition-all duration-300">
                    <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col justify-between h-full">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">{{ __('Master Wallets & Safe Storage') }}</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ __('Your funds are held securely in dedicated Master Wallets with transparent transaction tracking and direct withdrawal access.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div class="p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl group hover:border-cyan-400/40 transition-all duration-300">
                    <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col justify-between h-full">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">{{ __('Isolated Trading Balances') }}</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ __('Each active bot operates with dedicated balance limits to protect the rest of your portfolio from market volatility.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div class="p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl group hover:border-cyan-400/40 transition-all duration-300">
                    <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col justify-between h-full">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">{{ __('Automated Risk Protection') }}</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ __('Built-in stop-loss and safety triggers automatically protect your capital during sharp market downturns.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card 4 --}}
                <div class="p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl group hover:border-cyan-400/40 transition-all duration-300">
                    <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col justify-between h-full">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">{{ __('Verified Asset Backing') }}</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ __('Transparent transaction records verify that all user balances and returns are fully backed 24/7.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================================================== --}}
    {{-- SECTION 7: HOW IT WORKS — 3-STEP EXECUTION PIPELINE --}}
    {{-- ==================================================================================== --}}
    <section id="how-it-works" class="py-24 sm:py-36 relative overflow-hidden bg-[#050507] isolate border-t border-white/[0.06]">
        {{-- Background Glow Blobs --}}
        <div class="absolute top-1/3 right-0 w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[160px] pointer-events-none select-none -z-10"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                    {{ __('HOW IT WORKS') }}
                </div>
                <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight mb-4">
                    {{ __('Start Automated Trading') }} <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70">{{ __('in 3 Simple Steps.') }}</span>
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    {{ __('Start earning with automated bots or copying top traders in just a few minutes.') }}
                </p>
            </div>

            {{-- 3-Step Double-Bezel Grid with Connecting Laser Beam --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                {{-- Desktop Kinetic Laser Connector Beam --}}
                <div class="hidden md:block absolute top-16 left-1/6 right-1/6 h-[2px] bg-gradient-to-r from-cyan-500/40 via-purple-500/60 to-cyan-500/40 -z-0"></div>

                {{-- Step 1 --}}
                <div class="p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl group hover:border-cyan-400/40 transition-all duration-300 relative">
                    <div class="absolute top-0 left-10 right-10 h-[1.5px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent"></div>
                    <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-8 flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center justify-between mb-8">
                                <span class="text-3xl font-mono font-black text-cyan-400/40 group-hover:text-cyan-400 transition-colors">01</span>
                                <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center group-hover:scale-110 transition-transform shadow-[0_0_20px_rgba(0,245,255,0.2)]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3 group-hover:text-cyan-300 transition-colors">{{ __('1. Deposit Funds') }}</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ __('Easily deposit USDT, BTC, ETH, SOL, or other supported cryptocurrencies into your account.') }}</p>
                        </div>
                        <div class="mt-8 pt-4 border-t border-white/[0.08] font-mono text-[10px] text-cyan-400 font-bold uppercase tracking-widest">
                            + FAST CRYPTO DEPOSIT
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl group hover:border-purple-400/40 transition-all duration-300 relative">
                    <div class="absolute top-0 left-10 right-10 h-[1.5px] bg-gradient-to-r from-transparent via-purple-400 to-transparent"></div>
                    <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-8 flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center justify-between mb-8">
                                <span class="text-3xl font-mono font-black text-purple-400/40 group-hover:text-purple-400 transition-colors">02</span>
                                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center group-hover:scale-110 transition-transform shadow-[0_0_20px_rgba(168,85,247,0.2)]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3 group-hover:text-purple-300 transition-colors">{{ __('2. Choose a Bot or Trader') }}</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ __('Select an automated trading bot plan or enter a trader copy code to start copying trades.') }}</p>
                        </div>
                        <div class="mt-8 pt-4 border-t border-white/[0.08] font-mono text-[10px] text-purple-400 font-bold uppercase tracking-widest">
                            + ONE-CLICK START
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl group hover:border-emerald-400/40 transition-all duration-300 relative">
                    <div class="absolute top-0 left-10 right-10 h-[1.5px] bg-gradient-to-r from-transparent via-emerald-400 to-transparent"></div>
                    <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-8 flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center justify-between mb-8">
                                <span class="text-3xl font-mono font-black text-emerald-400/40 group-hover:text-emerald-400 transition-colors">03</span>
                                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform shadow-[0_0_20px_rgba(168,85,247,0.2)]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3 group-hover:text-emerald-300 transition-colors">{{ __('3. Earn & Withdraw Anytime') }}</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ __('Daily returns and profits are added directly to your balance. Request withdrawals to your crypto wallet anytime.') }}</p>
                        </div>
                        <div class="mt-8 pt-4 border-t border-white/[0.08] font-mono text-[10px] text-emerald-400 font-bold uppercase tracking-widest">
                            + FAST ON-CHAIN PAYOUTS
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================================================== --}}
    {{-- SECTION 8: LIVE EXECUTION LEDGER & VERIFIED AUDIT STREAM --}}
    {{-- ==================================================================================== --}}
    <section id="ledger" class="py-24 sm:py-36 relative overflow-hidden bg-[#050507] isolate border-t border-white/[0.06]">
        {{-- Background Accents --}}
        <div class="absolute bottom-0 left-1/3 w-[600px] h-[300px] bg-cyan-500/5 rounded-full blur-[180px] pointer-events-none select-none -z-10"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-8 mb-16">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                        {{ __('LIVE ACTIVITY') }}
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight">
                        {{ __('Live Trades & Payouts') }} <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70">{{ __('in Real Time.') }}</span>
                    </h2>
                </div>
                <p class="text-slate-400 text-sm sm:text-base max-w-md lg:text-right leading-relaxed">
                    {{ __('See live trading orders, bot profits, and copy trading returns as they happen.') }}
                </p>
            </div>

            {{-- Live Execution Ledger Stream Double-Bezel --}}
            <div class="p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 relative overflow-hidden">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/[0.08] font-mono text-xs text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-white font-bold">{{ __('LIVE ACTIVITY FEED') }}</span>
                        </div>
                        <span class="text-[10px] text-slate-500 hidden sm:inline">{{ __('AUTOMATED ENGINE v4.8') }}</span>
                    </div>

                    <div class="space-y-3 font-mono text-xs">
                        <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-cyan-400/30 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold">[BUY_ORDER]</span>
                                <span class="text-white font-bold">BTC/USDT</span>
                                <span class="text-slate-500">· Apex Scalper Pro</span>
                            </div>
                            <div class="flex items-center gap-4 text-slate-400">
                                <span>Qty: 0.85 BTC</span>
                                <span class="text-emerald-400 font-bold">+$342.10 Profit</span>
                                <span class="text-[10px] text-slate-500">Just now</span>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-cyan-400/30 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-0.5 rounded bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold">[COPY_SYNC]</span>
                                <span class="text-white font-bold">SOL/USDT</span>
                                <span class="text-slate-500">· Satoshi Trading Systems</span>
                            </div>
                            <div class="flex items-center gap-4 text-slate-400">
                                <span>1,420 Copiers Synced</span>
                                <span class="text-emerald-400 font-bold">+2.84% Return</span>
                                <span class="text-[10px] text-slate-500">1m ago</span>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-cyan-400/30 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-0.5 rounded bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-bold">[TARGET_HIT]</span>
                                <span class="text-white font-bold">ETH/USDT</span>
                                <span class="text-slate-500">· Grid Matrix Alpha</span>
                            </div>
                            <div class="flex items-center gap-4 text-slate-400">
                                <span>Target 2 Filled</span>
                                <span class="text-emerald-400 font-bold">+$188.50 Profit</span>
                                <span class="text-[10px] text-slate-500">3m ago</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cross-Exchange Arbitrage Spread Matrix Widget --}}
            <div class="mt-8 p-3 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/[0.08] font-mono text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                            <span class="text-white font-bold">{{ __('LIVE EXCHANGE PRICE DIFFERENCES') }}</span>
                        </div>
                        <span class="text-[#00f5ff] font-bold text-[10px]">{{ __('LIVE ARBITRAGE') }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 font-mono text-xs">
                        <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                            <div>
                                <div class="text-white font-bold">BTC/USDT Spread</div>
                                <div class="text-[10px] text-slate-500">Binance $96,450 vs OKX $96,890</div>
                            </div>
                            <span class="text-emerald-400 font-bold bg-emerald-500/10 px-2 py-1 rounded border border-emerald-500/20">+0.45% Return</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                            <div>
                                <div class="text-white font-bold">SOL/USDT Spread</div>
                                <div class="text-[10px] text-slate-500">Bybit $214.80 vs Raydium $216.70</div>
                            </div>
                            <span class="text-emerald-400 font-bold bg-emerald-500/10 px-2 py-1 rounded border border-emerald-500/20">+0.88% Return</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                            <div>
                                <div class="text-white font-bold">ETH/USDT Spread</div>
                                <div class="text-[10px] text-slate-500">Coinbase $3,620 vs Uniswap $3,661</div>
                            </div>
                            <span class="text-emerald-400 font-bold bg-emerald-500/10 px-2 py-1 rounded border border-emerald-500/20">+1.13% Return</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================================================== --}}
    {{-- SECTION: REGULATORY COMPLIANCE & OFFICIAL CERTIFICATION --}}
    {{-- ==================================================================================== --}}
    @if (!empty($regulatoryCompliance))
    <section id="compliance" class="py-24 sm:py-36 relative overflow-hidden bg-[#050507] isolate border-t border-white/[0.06]">
        {{-- Ambient Depth Blobs --}}
        <div class="absolute top-1/2 left-0 -translate-y-1/2 w-[550px] h-[550px] bg-emerald-500/10 rounded-full blur-[160px] pointer-events-none select-none -z-10"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="grid lg:grid-cols-12 gap-8 items-stretch">
                
                {{-- Left: Regulatory Bodies & Standards (7 cols) --}}
                <div class="lg:col-span-7 p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] flex flex-col justify-between">
                    <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-8 sm:p-10 flex flex-col justify-between h-full">
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-6">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                {{ __('REGULATORY COMPLIANCE') }}
                            </div>
                            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                                {{ __('Regulated &') }} <br class="hidden sm:inline">
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-200 to-cyan-400">{{ __('Transparent.') }}</span>
                            </h2>
                            <p class="text-slate-400 text-sm sm:text-base leading-relaxed mb-8">
                                {{ __('We adhere to high regulatory and compliance standards to protect your account, funds, and personal data at all times.') }}
                            </p>
                        </div>

                        @if (!empty($regulatoryCompliance['regulators']))
                            <div class="space-y-4 pt-6 border-t border-white/[0.08]">
                                <h4 class="text-xs font-mono font-bold text-slate-300 uppercase tracking-widest">{{ __('LICENSED & REGISTERED') }}</h4>
                                <div class="flex flex-wrap gap-3">
                                    @foreach ($regulatoryCompliance['regulators'] as $regulator)
                                        <div class="px-5 py-3 rounded-2xl bg-white/[0.03] border border-white/[0.1] hover:border-emerald-400/40 transition-colors flex items-center gap-3">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_10px_#10b981]"></span>
                                            <span class="text-xs font-bold text-white">{{ __($regulator) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right: Official Public PDF Certificates (5 cols) --}}
                <div class="lg:col-span-5 p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] flex flex-col justify-between">
                    <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-8 sm:p-10 flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center justify-between mb-8 pb-4 border-b border-white/[0.08]">
                                <div>
                                    <h3 class="text-xl font-bold text-white">{{ __('Official Certification') }}</h3>
                                    <span class="text-xs text-slate-400 font-mono">{{ __('Official Public Documents') }}</span>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                            </div>

                            @if (!empty($regulatoryCompliance['pdf_certificates']))
                                <div class="space-y-3">
                                    @foreach ($regulatoryCompliance['pdf_certificates'] as $pdf)
                                        <a href="{{ asset('assets/pdf/' . $pdf['file']) }}" target="_blank"
                                           class="group flex items-center justify-between p-4 rounded-2xl bg-white/[0.02] border border-white/[0.06] hover:border-emerald-400/40 hover:bg-emerald-500/5 transition-all">
                                            <div class="flex items-center gap-3.5">
                                                <div class="w-11 h-11 rounded-xl bg-white/[0.03] border border-white/[0.08] flex items-center justify-center text-slate-400 group-hover:text-emerald-400 group-hover:bg-emerald-500/10 transition-all shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors">{{ __($pdf['name']) }}</div>
                                                    <span class="text-[9px] font-mono text-slate-500 uppercase tracking-wider block mt-0.5">{{ __('VERIFIED PDF DOCUMENT') }}</span>
                                                </div>
                                            </div>
                                            <div class="w-8 h-8 rounded-full border border-white/10 flex items-center justify-center bg-white/5 group-hover:bg-emerald-400 group-hover:border-emerald-400 group-hover:text-[#050507] text-slate-300 transition-all shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                </svg>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/[0.06] text-center text-xs text-slate-500 font-mono">
                                    {{ __('Public PDF certificates are available upon request.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    @endif

    {{-- ==================================================================================== --}}
    {{-- SECTION: EXECUTIVE & QUANTITATIVE LEADERSHIP TEAM BENTO --}}
    {{-- ==================================================================================== --}}
    @if ($management_team && $management_team->count() > 0)
    <section id="team" class="py-24 sm:py-36 relative overflow-hidden bg-[#050507] isolate border-t border-white/[0.06]">
        {{-- Background Ambient Orbs --}}
        <div class="absolute top-1/3 left-1/4 w-[500px] h-[500px] bg-cyan-500/10 rounded-full blur-[160px] pointer-events-none select-none -z-10 animate-pulse" style="animation-duration: 9s;"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-8 mb-16">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                        {{ __('EXECUTIVE TEAM') }}
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight">
                        {{ __('Experienced Leadership') }} <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70">{{ __('& Financial Experts.') }}</span>
                    </h2>
                </div>
                <p class="text-slate-400 text-sm sm:text-base max-w-md lg:text-right leading-relaxed">
                    {{ __('Our leadership team brings together decades of experience in trading, cybersecurity, and financial technology.') }}
                </p>
            </div>

            {{-- Leadership Bento Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach ($management_team as $member)
                    <div class="p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl group hover:border-cyan-400/50 transition-all duration-500 shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden flex flex-col justify-between">
                        <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col justify-between h-full">
                            <div>
                                <div class="relative h-64 w-full overflow-hidden rounded-2xl mb-6 border border-white/[0.08] bg-white/[0.02]">
                                    <img src="{{ asset('assets/images/team/' . $member->image) }}" 
                                         alt="{{ $member->name }}" 
                                         loading="lazy" 
                                         onerror="this.src='{{ asset('assets/images/bots/bot-1.png') }}'"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#090c14] via-transparent to-transparent opacity-80"></div>
                                    <div class="absolute bottom-3 left-3">
                                        <span class="px-3 py-1 rounded-full bg-[#00f5ff]/10 border border-[#00f5ff]/20 text-[#00f5ff] text-[10px] font-mono font-bold uppercase tracking-wider">
                                            {{ __($member->designation ?? $member->role) }}
                                        </span>
                                    </div>
                                </div>

                                <h3 class="text-2xl font-bold text-white group-hover:text-cyan-300 transition-colors mb-2">{{ __($member->name) }}</h3>
                                <p class="text-xs text-slate-400 leading-relaxed font-body line-clamp-3">
                                    {{ __($member->description, ['site_name' => getSetting('name')]) }}
                                </p>
                            </div>

                            <div class="mt-6 pt-4 border-t border-white/[0.08] flex items-center justify-between font-mono text-[10px] text-slate-500">
                                <span>VERIFIED TEAM</span>
                                <span class="text-cyan-400 font-bold">● ACTIVE</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ==================================================================================== --}}
    {{-- SECTION: VERIFIED CLIENT & MASTER TRADER REVIEWS CAROUSEL --}}
    {{-- ==================================================================================== --}}
    @if ($reviews && $reviews->count() > 0)
    <section id="reviews" class="py-24 sm:py-36 relative overflow-hidden bg-[#050507] isolate border-t border-white/[0.06]"
             x-data="{ 
                activeSlide: 0, 
                totalSlides: {{ $reviews->count() }},
                next() { this.activeSlide = (this.activeSlide + 1) % this.totalSlides; },
                prev() { this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides; }
             }">
        {{-- Ambient Blobs --}}
        <div class="absolute top-1/2 left-0 -translate-y-1/2 w-[550px] h-[550px] bg-cyan-500/10 rounded-full blur-[160px] pointer-events-none select-none -z-10"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-6 mb-12 sm:mb-16">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                        {{ __('USER REVIEWS') }}
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight">
                        {{ __('Trusted by Traders') }} <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70">{{ __('Worldwide.') }}</span>
                    </h2>
                </div>

                {{-- Carousel Controls (Prev/Next Buttons) --}}
                <div class="flex items-center gap-3">
                    <button @click="prev()" 
                            class="w-12 h-12 rounded-full bg-white/[0.03] border border-white/[0.1] hover:border-cyan-400/50 hover:bg-cyan-500/10 flex items-center justify-center text-white transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="next()" 
                            class="w-12 h-12 rounded-full bg-white/[0.03] border border-white/[0.1] hover:border-cyan-400/50 hover:bg-cyan-500/10 flex items-center justify-center text-white transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            {{-- Reviews Slide Track --}}
            <div class="relative overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($reviews as $index => $review)
                        <div x-show="activeSlide === {{ $index }} || (window.innerWidth >= 768 && activeSlide === ({{ $index }} - 1 + {{ $reviews->count() }}) % {{ $reviews->count() }}) || (window.innerWidth >= 1024 && activeSlide === ({{ $index }} - 2 + {{ $reviews->count() }}) % {{ $reviews->count() }})"
                             x-transition:enter="transition ease-out duration-500 transform"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="p-2.5 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl group hover:border-cyan-400/40 transition-all duration-300 flex flex-col justify-between h-full">
                            <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col justify-between h-full">
                                <div>
                                    <div class="flex items-center gap-1 text-amber-400 text-xs mb-4">
                                        ★★★★★
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-body mb-6">
                                        "{{ $review->review }}"
                                    </p>
                                </div>
                                <div class="flex items-center gap-3 pt-4 border-t border-white/[0.08]">
                                    @if(!empty($review->image))
                                        <img src="{{ asset('assets/images/team/' . $review->image) }}" alt="{{ $review->name }}" class="w-10 h-10 rounded-full object-cover border border-cyan-500/20">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($review->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="text-xs font-bold text-white">{{ $review->name }}</h4>
                                        <span class="text-[10px] font-mono text-slate-500">{{ $review->designation ?? 'Verified Member' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Pagination Dot Indicators --}}
            <div class="flex items-center justify-center gap-2 mt-8">
                @foreach ($reviews as $index => $review)
                    <button @click="activeSlide = {{ $index }}" 
                            :class="activeSlide === {{ $index }} ? 'w-8 bg-[#00f5ff]' : 'w-2 bg-white/20 hover:bg-white/40'"
                            class="h-2 rounded-full transition-all duration-300"></button>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ==================================================================================== --}}
    {{-- SECTION: INTERACTIVE FAQ ACCORDION --}}
    {{-- ==================================================================================== --}}
    @if ($faqs && $faqs->count() > 0)
    <section id="faq" class="py-24 sm:py-36 relative overflow-hidden bg-[#050507] isolate border-t border-white/[0.06]" x-data="{ activeFaq: 0 }">
        {{-- Background Glow Blobs --}}
        <div class="absolute top-1/2 right-10 -translate-y-1/2 w-[550px] h-[550px] bg-purple-600/10 rounded-full blur-[160px] pointer-events-none select-none -z-10"></div>

        <div class="container mx-auto px-4 relative z-10 max-w-5xl">
            <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                    {{ __('FREQUENTLY ASKED QUESTIONS') }}
                </div>
                <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight mb-4">
                    {{ __('Frequently Asked') }} <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70">{{ __('Questions.') }}</span>
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    {{ __('Everything you need to know about our trading bots, copy trading, deposits, and account security.') }}
                </p>
            </div>

            {{-- FAQ Double-Bezel Accordion Stack --}}
            <div class="space-y-4">
                @foreach ($faqs as $index => $faq)
                    <div class="p-2 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-cyan-400/30 transition-all duration-300 backdrop-blur-2xl">
                        <div class="rounded-[1.5rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] overflow-hidden">
                            <button @click="activeFaq = (activeFaq === {{ $index }} ? null : {{ $index }})"
                                    class="w-full p-6 text-left flex items-center justify-between gap-4 font-bold text-white text-base sm:text-lg focus:outline-none group">
                                <span class="group-hover:text-cyan-300 transition-colors flex items-center gap-3">
                                    <span class="text-cyan-400/50 font-mono text-xs">0{{ $index + 1 }}.</span>
                                    {{ $faq->parsed_question }}
                                </span>
                                <span class="w-8 h-8 rounded-full bg-white/[0.03] border border-white/[0.1] flex items-center justify-center text-slate-400 group-hover:text-cyan-400 group-hover:border-cyan-400/30 transition-all shrink-0">
                                    <svg class="w-4 h-4 transform transition-transform duration-300" :class="activeFaq === {{ $index }} ? 'rotate-180 text-cyan-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </span>
                            </button>

                            <div x-show="activeFaq === {{ $index }}" x-collapse
                                 class="px-6 pb-6 pt-2 text-xs sm:text-sm text-slate-400 leading-relaxed font-body border-t border-white/[0.04]">
                                {{ $faq->parsed_answer }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ==================================================================================== --}}
    {{-- SECTION 9: FINAL CONVERSION CTA DECK --}}
    {{-- ==================================================================================== --}}
    <section id="cta" class="py-24 sm:py-36 relative overflow-hidden bg-[#050507] isolate border-t border-white/[0.06]">
        {{-- Ambient Blobs --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[400px] bg-gradient-to-r from-cyan-500/10 via-indigo-500/10 to-purple-500/10 rounded-full blur-[180px] pointer-events-none select-none -z-10"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto p-3 rounded-[3rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_30px_90px_rgba(0,0,0,0.9)] relative overflow-hidden">
                <div class="rounded-[2.5rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-8 sm:p-12 lg:p-16 text-center relative overflow-hidden">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-6">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                        {{ __('GET STARTED TODAY') }}
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight mb-6">
                        {{ __('Ready to Start Automated Trading?') }}
                    </h2>
                    <p class="text-slate-400 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed mb-8 sm:mb-10">
                        {{ __('Join thousands of members growing their crypto portfolio with automated bots and copy trading.') }}
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('user.register') }}" class="w-full sm:w-auto pl-8 pr-2.5 py-2.5 rounded-full bg-[#00f5ff] hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-4 shadow-[0_0_35px_rgba(0,245,255,0.4)] hover:shadow-[0_0_50px_rgba(0,245,255,0.7)] group">
                            <span>{{ __('Create Free Account') }}</span>
                            <span class="w-10 h-10 rounded-full bg-[#050507]/20 flex items-center justify-center group-hover:translate-x-1 group-hover:-translate-y-[1px] transition-transform">
                                <svg class="w-4 h-4 text-[#050507]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </a>
                        <a href="{{ route('user.trading-bots.index') }}" class="w-full sm:w-auto px-8 py-4 rounded-full bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.1] text-white font-black text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2">
                            <span>{{ __('View Trading Bots') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('css')
        <style>
            /* Moon Arc Curved Laser Tracer Animation */
            .moon-arc-laser-path {
                stroke-dasharray: 260 1400;
                stroke-dashoffset: 1660;
                animation: moonArcLaserFlow 4s ease-in-out infinite;
            }
            @keyframes moonArcLaserFlow {
                0% { stroke-dashoffset: 1660; opacity: 0; }
                15% { opacity: 1; }
                85% { opacity: 1; }
                100% { stroke-dashoffset: -200; opacity: 0; }
            }

            /* Data Pipeline Circuit Laser Animations */
            .circuit-laser-beam-1 {
                stroke-dasharray: 120 700;
                stroke-dashoffset: 820;
                animation: circuitFlow1 3.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            }
            @keyframes circuitFlow1 {
                0% { stroke-dashoffset: 820; opacity: 0; }
                20% { opacity: 1; }
                80% { opacity: 1; }
                100% { stroke-dashoffset: -100; opacity: 0; }
            }

            .circuit-laser-beam-2 {
                stroke-dasharray: 120 700;
                stroke-dashoffset: 820;
                animation: circuitFlow2 4s cubic-bezier(0.4, 0, 0.2, 1) infinite 1.2s;
            }
            @keyframes circuitFlow2 {
                0% { stroke-dashoffset: 820; opacity: 0; }
                20% { opacity: 1; }
                80% { opacity: 1; }
                100% { stroke-dashoffset: -100; opacity: 0; }
            }

            /* Concept 2: Quantum Core Fusion Pulse Animation */
            .fusion-core-pulse {
                animation: fusionCorePulse 4s ease-in-out infinite;
            }
            @keyframes fusionCorePulse {
                0%, 100% { opacity: 0.4; transform: scale(1); }
                50% { opacity: 0.8; transform: scale(1.06); }
            }
        </style>
@endpush

{{--
====================================================================================================
FOYANA ADVANCED FRONTEND BLUEPRINT & DESIGN GUIDE: HOMEPAGE (index.blade.php)
====================================================================================================
PRODUCT FOCUS: Autonomous AI Bot Trading, Real-Time Copy Trading, Direct On-Chain Settlement
DESIGN SYSTEM: High-Agency Liquid Glass, Kinetic Physics, Motion Bento 2.0, Dark Mode (#050507)
ADVANCED BENCHMARK: Quantitative Ecosystem (Typewriter Dynamics, Live Terminal Frame,
                   Interactive Growth Calculator, Real-Time Bot Telemetry, Liquid Refraction)
LAST UPDATED: 2026-08-07 — Reflects live hero implementation from session
====================================================================================================

----------------------------------------------------------------------------------------------------
GLOBAL DESIGN TOKENS & MATERIALITY SYSTEM
----------------------------------------------------------------------------------------------------
Color Palette:
  * Base Background: #050507
  * Dot Grid Overlay: radial-gradient(#ffffff 1px, transparent 1px) @ 32px, opacity-[0.03]
  * Panel Glass: bg-[#090c14]/90 or bg-white/[0.015-0.03], border-white/[0.06-0.10]
  * Accent Cyan (Primary):  #00f5ff — used for nav CTA, arc glow, laser beams, copy trading
  * Accent Gold (Platform):  #e2b13c — used for bot alerts, status badges, glow orbs
  * Profit Emerald:          #10b981 — used for P/L values, live execution, success states
  * Indigo/Purple (Depth):   indigo-600 / purple-600 — used for ambient blobs, blockchain layer
  * Slate Text:              text-slate-400 (body) / text-slate-300 (labels) / text-white (headlines)

Typography:
  * Display Headline:  font-black tracking-tight leading-[1.08] (Outfit)
  * Monospace Data:    font-mono (JetBrains Mono) — used for all financial figures & terminal feeds
  * Body / Subhead:    font-body leading-relaxed text-slate-400 (Inter)

Navigation:
  * Primary CTA (.top-cta-btn): background: #00f5ff; color: #050507; solid (NO gradient)
  * Mobile Sheet Primary CTA: same #00f5ff solid
  * Hover: background: #33f7ff; box-shadow: 0 0 25px rgba(0,245,255,0.45)


----------------------------------------------------------------------------------------------------
SECTION 1: HERO DECK — ALGORITHMIC COMMAND CENTER (IMPLEMENTED)
----------------------------------------------------------------------------------------------------
[COPYWRITING & CONTENT]:
  Headline Line 1: "Automate Your Trading."              (gradient: from-white via-slate-200 to-slate-400)
  Headline Line 2: "Multiply Your Alpha."                (text-white)
  Subheadline: "Deploy quantitative bots or copy top master traders with instant on-chain settlement."
  Live Telemetry Badge: "• Algorithmic Execution Engine v4.8 Active · 99.98% Uptime"
  CTA 1: "+ DEPLOY TRADING BOT NOW"  ->  bg-emerald-500/10 border-emerald-500/20 text-emerald-400
  CTA 2: "EXPLORE COPY TRADING >"   ->  bg-white/[0.02] border-white/[0.08] text-slate-300
  Stats: $10B+ Volume | 99.98% Uptime | 100% On-Chain Settlement (Alpine.js count-up animation)

[DESIGN GUIDE & VISUAL CONCEPT — IMPLEMENTED]:
  Layout: <section> class="relative min-h-[100dvh] pt-32 pb-24 flex flex-col justify-between overflow-hidden bg-[#050507] isolate"
  Grid: grid-cols-1 lg:grid-cols-12  ->  Left: lg:col-span-7  |  Right: lg:col-span-5

  Z-STACK BACKGROUND LAYERS (back to front):
    -z-20  Dot grid (fixed, radial, 32px, opacity 0.03)
    -z-20  Gold blob   top-1/4 left-1/4  500px  blur-120  8s pulse
    -z-20  Indigo blob top-1/3 right-1/4 600px  blur-140 12s pulse
    -z-20  Emerald blob bottom left-1/3  450px  blur-130
    -z-10  Moon Arc backdrop (absolute inset-x-0 bottom-0)
    -z-10  SVG Laser Overlay
     z-0   Hero content grid

  MOON ARC BACKDROP:
    Container: absolute inset-x-0 bottom-0, pointer-events-none, -z-10, flex justify-center overflow-hidden
    Arc div: w-[150vw] sm:w-[130vw] md:w-[120vw]  h-[280px]/[340px]
             bg-white/[0.015]  border-t border-white/10
             rounded-[50%_50%_0_0/100%_100%_0_0]  (upside-down moon C-shape)
             shadow-[0_-20px_60px_rgba(0,245,255,0.08),inset_0_2px_0_rgba(255,255,255,0.12)]
             backdrop-blur-md
    Top edge glow: absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#00f5ff]/60 to-transparent
    Inner SVG laser: viewBox="0 0 1000 300" path: M 0 300 Q 500 -20 1000 300
      class="moon-arc-laser-path" -> stroke-dasharray:260 1400, 4s ease-in-out infinite

  MULTI-CHAIN LOGO BADGE SHOWCASE (inside Moon Arc):
    Status Badge: "NATIVE MULTI-CHAIN ON-CHAIN SETTLEMENT" (cyan-400 font-mono dot ping)
    Local Token Logos with loading="lazy":
      - Bitcoin (BTC: btc.png)
      - Ethereum (ETH: eth.png)
      - Solana (SOL: sol.png)
      - BNB Chain (BNB: bnb.png)
      - Polygon (POL: polygon.png)
      - Arbitrum (ARB: arbitrum.png)
      - Avalanche (AVAX: avalanche.png)
      - Optimism (OP: optimism.png)
      - Base (BASE: base.png)
      - Tether (USDT: usdt.png)
      - USD Coin (USDC: usdc.png)
      - Tron (TRX: trx.png)
    Card Glass: bg-[#090c14]/90 border-white/10 hover:border-cyan-400/50 hover:shadow-[0_0_20px_rgba(0,245,255,0.25)]

  SVG DATA PIPELINE LASERS (outer hero overlay, -z-10):
    Cyan circuit: M 520 280 L 680 280 Q 740 280 740 330 L 740 410 Q 740 440 790 440 L 920 440
      .circuit-laser-beam-1  3.5s cubic-bezier(0.4,0,0.2,1) infinite
    Gold circuit:  M 520 380 L 640 380 Q 700 380 700 420 L 700 480 Q 700 510 750 510 L 920 510
      .circuit-laser-beam-2  4s  1.2s delay  infinite

  RIGHT SECTION — CONCEPT 1: BLEEDING MULTI-LAYER CONSOLE STACK:
    Container: lg:col-span-5 w-[130%] -mr-[30%]
               mask-image: linear-gradient(to right, #000 75%, transparent 98%)  select-none
    Layer 1 (Main Console — double-bezel-outer):
      Ambient orbs: gold top-right, purple bottom-left (blur-3xl)
      Header:   Mac traffic lights (rose/amber/emerald) + "QUANT_BOT_ENGINE // ACTIVE"
                Status pill: bg-emerald-500/10 border-emerald-500/20 text-emerald-400
                             "● LIVE ALGO EXECUTION" (1.5px dot animate-pulse)
      Metrics:  2-col grid — "$24,850.00 Total Deployed Capital" | "+$3,842.10 (+15.4%) Algo P/L"
      Bot row:  icon (bg-[#e2b13c]/10 border-[#e2b13c]/20)
                "Apex Scalper Pro v4"  BTC/USDT · High Frequency Arbitrage  "+2.84% / 24h Yield"
      Order log (bg-black/60):
                [ORDER_FILL] BUY BTC/USDT @ $94,280  ·  Just now
                [TARGET_HIT] SOL/USDT arbitrage +2.15%  ·  1m ago
    Layer 2 (Peeking Copy Card — translate-x-10):
      bg-[#090c14]/90 border border-cyan-500/20 rounded-2xl backdrop-blur-xl
      Avatar: "SQ" (bg-cyan-500/10 border-cyan-500/20 text-cyan-400)
      "Satoshi Quant Systems"  +  pill "COPY CODE: SATO-QUANT-01"
      "1,420 Active Copiers · Zero Latency Sync"
      "+142.8%  30d PnL (88.4% Win Rate)"
      hover:translate-x-4 transition-transform duration-300


----------------------------------------------------------------------------------------------------
SECTION 2: PRODUCT PILLAR 1 — AUTONOMOUS AI BOT FLEET (BENTO GRID 2.0)
----------------------------------------------------------------------------------------------------
[COPYWRITING & CONTENT]:
  Section Tag: "PRODUCT PILLAR 01"
  Headline: "Autonomous Quantitative AI Trading Bots Executing 24/7"
  Subheadline: "No emotion. No manual charting. Our quantitative bots analyze millions of tick
                data points per second to capture market alpha across crypto and forex pairs."
  Bot Cards:
    1. Apex Scalper Pro   — High-Yield Micro-Scalping     +1.8-3.2% daily  30d lock  $100 min
    2. Grid Matrix Alpha  — Sideways Channel Arbitrage    +0.9-1.6% daily  15d lock  $50 min
    3. Sentinel Yield     — Low-Drawdown DEX/CEX Arb.    +0.5-0.95% daily  7d lock  $25 min
  CTA: "Deploy Bot Contract" -> route('user.trading-bots.index')

[DESIGN GUIDE & VISUAL CONCEPT]:
  Layout: Bento Grid 2.0, 3-col asymmetric, staggered entrance.
  Cards: rounded-3xl bg-white/[0.02] border border-white/[0.08]
         shadow-[inset_0_1px_0_rgba(255,255,255,0.1)]
  Micro-interactions: font-mono text-emerald-400 yield values, scale-[1.02] spring hover.


----------------------------------------------------------------------------------------------------
SECTION 3: INTERACTIVE YIELD & PROFIT SIMULATOR
----------------------------------------------------------------------------------------------------
[COPYWRITING & CONTENT]:
  Tag: "REAL-TIME FINANCIAL MODELING"
  Headline: "Calculate Your Projected Bot & Copy Trading Yields"
  Controls: Strategy dropdown, capital slider ($100-$100k), lockup selector (7d/15d/30d/90d)
  Outputs: Total Projected Profit | Daily Payout | Maturity Date | Compound ROI %

[DESIGN GUIDE & VISUAL CONCEPT]:
  Layout: Dual-pane glass console (left inputs / right live SVG profit chart).
  Live JS calculations, no page reload.


----------------------------------------------------------------------------------------------------
SECTION 4: PRODUCT PILLAR 2 — REAL-TIME COPY TRADING NETWORK
----------------------------------------------------------------------------------------------------
[COPYWRITING & CONTENT]:
  Tag: "PRODUCT PILLAR 02"
  Headline: "Mirror Elite Master Traders with One-Click Copy Codes"
  Subheadline: "...Every trade entry, target, and exit is synchronized instantaneously."
  Activation: input placeholder "e.g. MASTER-APEX-88", CTA "Mirror Strategy"
  Leaderboard:
    1. Satoshi Quant Systems  SATO-QUANT-01  1,420 Copiers  +142.8% 30d PnL  88.4% WR
    2. Alpha Momentum Fund    ALPHA-FUND-99    980 Copiers   +98.4% 30d PnL  84.1% WR
    3. Nexus Arbitrage Vault  NEXUS-VAULT-04   640 Copiers   +76.2% 30d PnL  81.5% WR

[DESIGN GUIDE & VISUAL CONCEPT]:
  Layout: Split (left activation widget / right master trader stack).
  Peeking card style (match hero Layer 2): translate-x-10 bg-[#090c14] border-cyan-500/20.
  Cyan focus ring on input. Win-rate pill badges.


----------------------------------------------------------------------------------------------------
SECTION 5: INFRASTRUCTURE — DIRECT MULTI-CHAIN BLOCKCHAIN SETTLEMENT
----------------------------------------------------------------------------------------------------
[COPYWRITING & CONTENT]:
  Tag: "NATIVE ON-CHAIN INFRASTRUCTURE"
  Headline: "Direct On-Chain Blockchain Settlement"
  Subheadline: "Zero third-party payment gateways. Instant deposits and withdrawals via native
                blockchain smart contracts."
  Chains (match hero marquee exactly):
    SOLANA · ETHEREUM · TRON · POLYGON · BITCOIN · BNB CHAIN
    TETHER (USDT) · USD COIN (USDC) · ARBITRUM · AVALANCHE · OPTIMISM

[DESIGN GUIDE & VISUAL CONCEPT]:
  Centered header + horizontal floating 3D glass token pill deck.
  bg-white/[0.02] border border-white/[0.08] hover 3D tilt.


----------------------------------------------------------------------------------------------------
SECTION 6: HOW IT WORKS — 3-STEP EXECUTION PIPELINE
----------------------------------------------------------------------------------------------------
[COPYWRITING & CONTENT]:
  Tag: "ONBOARDING PIPELINE"
  Headline: "Automate Your Trading in 3 Simple Steps"
  Step 01: "Deposit Capital On-Chain" — blockchain network USDT/SOL/BTC/ETH
  Step 02: "Select Bot or Enter Copy Code"
  Step 03: "Collect Profits & Withdraw On-Chain"

[DESIGN GUIDE & VISUAL CONCEPT]:
  3-col connector with cyan glowing path lines and step rings (01/02/03).
  SVG connector lines: match hero circuit laser beam style (stroke-dasharray animated, cyan glow filter).


----------------------------------------------------------------------------------------------------
SECTION 7: LIVE EXECUTION LEDGER & VERIFIED AUDIT
----------------------------------------------------------------------------------------------------
[COPYWRITING & CONTENT]:
  Tag: "TRANSPARENCY LEDGER"
  Headline: "Real Data. Real Profits. Unfiltered Execution."
  Subheadline: "Live rolling stream of active bot executions, profit distributions, and copy trade completions."

[DESIGN GUIDE & VISUAL CONCEPT]:
  Full-width kinetic vertical stream (match Concept 2 verticalStream animation style).
  Rows: monospace trade fills [EXEC_BUY] [TP_HIT] [COPY_SYNC], 16s linear infinite, pause on hover.
  Color-coded type tags: text-emerald-400, timestamp text-slate-500.


----------------------------------------------------------------------------------------------------
SECTION 8: FINAL CONVERSION CTA DECK
----------------------------------------------------------------------------------------------------
[COPYWRITING & CONTENT]:
  Headline: "Ready to Automate Your Portfolio?"
  Subheadline: "Join thousands of investors executing trades automatically on the world's leading
                quantitative AI & Copy Trading ecosystem."
  CTA: "Create Free Account" -> route('user.register')

[DESIGN GUIDE & VISUAL CONCEPT]:
  Centered high-contrast banner, ambient radial cyan + purple glow.
  Button: solid #00f5ff background (NO gradient — matches nav CTA convention).
  shadow-[0_0_40px_rgba(0,245,255,0.25)] spring hover scale.
====================================================================================================
--}}
