@extends('templates.york.blades.layouts.front')

@section('title', $page_title . ' - ' . getSetting('name'))
@section('page_title', $page_title)

@section('content')
    <style>
        /* Pulse and Floating Dynamic FX */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(0.5deg); }
        }
        .animate-float-slow {
            animation: floatSlow 8s ease-in-out infinite;
        }

        /* Order Pulse Simulation */
        @keyframes orderPulse {
            0% { opacity: 0.15; transform: scale(0.98); }
            50% { opacity: 0.4; transform: scale(1.02); }
            100% { opacity: 0.15; transform: scale(0.98); }
        }
        .animate-order-pulse {
            animation: orderPulse 4s ease-in-out infinite;
        }
    </style>

    <div class="relative bg-[#050507] isolate overflow-hidden min-h-screen">
        
        {{-- Ambient Mesh Glow Elements --}}
        <div class="absolute top-10 left-1/4 -translate-x-1/2 w-[650px] h-[650px] bg-cyan-500/10 rounded-full blur-[160px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 9s;"></div>
        <div class="absolute top-1/2 right-0 w-[550px] h-[550px] bg-indigo-600/10 rounded-full blur-[150px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 12s;"></div>
        <div class="absolute bottom-20 left-10 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[140px] pointer-events-none -z-10"></div>
        <div class="fixed inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:40px_40px] opacity-[0.025] pointer-events-none -z-20"></div>

        {{-- ==================================================================================== --}}
        {{-- SECTION 1: HERO & LIVE TRADING ACTIVITY DECK --}}
        {{-- ==================================================================================== --}}
        <section class="relative pt-12 pb-20 sm:pt-20 sm:pb-28 border-b border-white/[0.06]">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                    
                    {{-- Left Column: Hero Narrative & Everyday Metric Chips --}}
                    <div class="lg:col-span-7 space-y-8 text-left">
                        <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/25 text-cyan-400 font-mono text-[11px] font-bold uppercase tracking-widest shadow-[0_0_20px_rgba(0,245,255,0.15)]">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                            <span>{{ __('AUTOMATED TRADING') }}</span>
                            <span class="text-slate-600">•</span>
                            <span class="text-emerald-400">{{ __('RUNNING 24/7') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight leading-[1.05]">
                            {{ __('Automated Crypto &') }}<br>
                            <span class="bg-gradient-to-r from-cyan-400 via-teal-300 to-indigo-400 bg-clip-text text-transparent">
                                {{ __('Forex Trading Bots') }}
                            </span>
                        </h1>

                        <p class="text-base sm:text-lg text-slate-300 font-normal leading-relaxed max-w-2xl">
                            {{ $page_description }}
                        </p>

                        {{-- Metric Chips in Everyday Language --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 font-mono">
                            <div class="p-4 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] backdrop-blur-xl">
                                <span class="text-[10px] text-slate-500 uppercase tracking-widest block mb-1">{{ __('Available Bots') }}</span>
                                <div class="text-2xl font-black text-white">{{ $allBots->count() }} <span class="text-cyan-400 text-xs">{{ __('Plans') }}</span></div>
                            </div>
                            <div class="p-4 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] backdrop-blur-xl">
                                <span class="text-[10px] text-slate-500 uppercase tracking-widest block mb-1">{{ __('Avg Daily Return') }}</span>
                                <div class="text-2xl font-black text-emerald-400">+2.45%</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] backdrop-blur-xl">
                                <span class="text-[10px] text-slate-500 uppercase tracking-widest block mb-1">{{ __('Trade Speed') }}</span>
                                <div class="text-2xl font-black text-cyan-300">&lt; 8ms</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] backdrop-blur-xl">
                                <span class="text-[10px] text-slate-500 uppercase tracking-widest block mb-1">{{ __('Exchanges') }}</span>
                                <div class="text-2xl font-black text-indigo-300">{{ count($botTrading['exchanges']) > 0 ? count($botTrading['exchanges']) : 10 }}+</div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap items-center gap-4 pt-2">
                            <a href="#fleetMatrix" class="px-8 py-4 rounded-2xl bg-cyan-400 hover:bg-cyan-300 text-slate-950 font-mono font-black text-xs uppercase tracking-widest shadow-[0_0_30px_rgba(0,245,255,0.3)] hover:shadow-[0_0_40px_rgba(0,245,255,0.5)] transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                                <span>{{ __('View All Bots') }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            </a>
                            <a href="#profitCalculator" class="px-6 py-4 rounded-2xl bg-white/[0.03] hover:bg-white/[0.08] border border-white/10 hover:border-cyan-500/30 text-white font-mono text-xs uppercase tracking-widest transition-all">
                                {{ __('Calculate Profits') }}
                            </a>
                        </div>
                    </div>

                    {{-- Right Column: Live Trade Activity Feed --}}
                    <div class="lg:col-span-5 relative">
                        <div class="relative rounded-3xl p-[1px] bg-gradient-to-b from-cyan-500/30 via-white/[0.08] to-indigo-500/20 shadow-[0_0_50px_rgba(0,245,255,0.1)]">
                            <div class="bg-[#090c14]/95 rounded-[calc(1.5rem-1px)] p-6 sm:p-7 backdrop-blur-2xl font-mono relative overflow-hidden">
                                
                                {{-- Console Header --}}
                                <div class="flex items-center justify-between pb-4 mb-5 border-b border-white/[0.08]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-rose-500/70 inline-block"></span>
                                        <span class="w-3 h-3 rounded-full bg-amber-500/70 inline-block"></span>
                                        <span class="w-3 h-3 rounded-full bg-emerald-500/70 inline-block"></span>
                                        <span class="text-xs font-bold text-slate-300 ml-2 uppercase tracking-wider">{{ __('Live Trade Activity') }}</span>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 font-bold">
                                        {{ __('LIVE UPDATES') }}
                                    </span>
                                </div>

                                {{-- Live Order Stream Simulation --}}
                                <div class="space-y-2.5 mb-6 text-xs">
                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/[0.02] border border-white/[0.04]">
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-400">BUY</span>
                                            <span class="text-white font-bold">BTC/USDT</span>
                                            <span class="text-slate-500 text-[10px]">Binance</span>
                                        </div>
                                        <span class="text-emerald-400 font-bold">+$482.50</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/[0.02] border border-white/[0.04]">
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-400">BUY</span>
                                            <span class="text-white font-bold">SOL/USDT</span>
                                            <span class="text-slate-500 text-[10px]">Bybit</span>
                                        </div>
                                        <span class="text-emerald-400 font-bold">+$194.20</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/[0.02] border border-white/[0.04]">
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-cyan-500/20 text-cyan-400">SELL</span>
                                            <span class="text-white font-bold">ETH/USDT</span>
                                            <span class="text-slate-500 text-[10px]">OKX</span>
                                        </div>
                                        <span class="text-emerald-400 font-bold">+$320.10</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/[0.02] border border-white/[0.04]">
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-400">BUY</span>
                                            <span class="text-white font-bold">EUR/USD</span>
                                            <span class="text-slate-500 text-[10px]">Forex ECN</span>
                                        </div>
                                        <span class="text-emerald-400 font-bold">+$145.80</span>
                                    </div>
                                </div>

                                {{-- Mini Live Performance Chart --}}
                                <div class="p-4 rounded-2xl bg-black/40 border border-white/[0.06] mb-5">
                                    <div class="flex justify-between items-center text-[10px] text-slate-400 mb-2">
                                        <span>{{ __('Average Bot Returns') }}</span>
                                        <span class="text-emerald-400 font-bold">+18.4% {{ __('This Month') }}</span>
                                    </div>
                                    <div class="h-14 w-full">
                                        <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none">
                                            <path d="M0 25 L 15 20 L 30 22 L 45 14 L 60 16 L 75 8 L 90 10 L 100 2" fill="none" stroke="#00f5ff" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M0 25 L 15 20 L 30 22 L 45 14 L 60 16 L 75 8 L 90 10 L 100 2 L 100 30 L 0 30 Z" fill="rgba(0,245,255,0.08)"/>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Security & Active Status Footer --}}
                                <div class="flex items-center justify-between text-[9px] text-slate-500">
                                    <span>BANK-GRADE ENCRYPTION</span>
                                    <span class="text-emerald-400 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        24/7 ACTIVE
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================================================================================== --}}
        {{-- SECTION 2: FEATURED TOP PERFORMING BOT --}}
        {{-- ==================================================================================== --}}
        @if ($recommendedBots->count() > 0)
            @php $flagship = $recommendedBots->first(); @endphp
            <section class="relative py-16 border-b border-white/[0.06] bg-gradient-to-b from-[#090c14]/40 to-transparent">
                <div class="max-w-7xl mx-auto px-4 lg:px-8">
                    <div class="rounded-3xl p-[1px] bg-gradient-to-r from-cyan-500/40 via-indigo-500/20 to-emerald-500/30 shadow-[0_0_40px_rgba(0,245,255,0.08)]">
                        <div class="bg-[#090c14]/95 rounded-[calc(1.5rem-1px)] p-6 sm:p-10 backdrop-blur-2xl">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                                
                                {{-- Flagship Left Info --}}
                                <div class="lg:col-span-8 space-y-4">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                                        {{ __('FEATURED TOP PERFORMER') }}
                                    </div>
                                    
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 rounded-2xl bg-black/60 border border-white/10 p-2.5 shrink-0 flex items-center justify-center">
                                            <img src="{{ asset('assets/images/bots/' . $flagship->logo) }}" 
                                                 alt="{{ $flagship->name }}" 
                                                 class="w-full h-full object-contain">
                                        </div>
                                        <div>
                                            <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                                                {{ $flagship->name }}
                                            </h2>
                                            <span class="text-xs font-mono uppercase tracking-widest text-slate-400">
                                                {{ $flagship->type }} {{ __('Automated Strategy') }}
                                            </span>
                                        </div>
                                    </div>

                                    <p class="text-sm text-slate-300 leading-relaxed max-w-2xl">
                                        {{ $flagship->description ?? __('Proven top-performing bot designed to automatically trade market trends, capitalize on daily price movements, and distribute profits directly to your account.') }}
                                    </p>

                                    {{-- Flagship Specs Bar in Everyday Language --}}
                                    <div class="flex flex-wrap gap-4 pt-2 font-mono text-xs">
                                        <div class="px-4 py-2 rounded-xl bg-white/[0.03] border border-white/[0.06]">
                                            <span class="text-[9px] text-slate-500 block uppercase">{{ __('Daily Profit Rate') }}</span>
                                            <span class="text-cyan-300 font-bold text-sm">{{ $flagship->daily_return_min }}% - {{ $flagship->daily_return_max }}% {{ __('Daily') }}</span>
                                        </div>
                                        <div class="px-4 py-2 rounded-xl bg-white/[0.03] border border-white/[0.06]">
                                            <span class="text-[9px] text-slate-500 block uppercase">{{ __('Trading Period') }}</span>
                                            <span class="text-white font-bold text-sm">{{ $flagship->duration }} {{ ucfirst($flagship->duration_type) }}(s)</span>
                                        </div>
                                        <div class="px-4 py-2 rounded-xl bg-white/[0.03] border border-white/[0.06]">
                                            <span class="text-[9px] text-slate-500 block uppercase">{{ __('Min / Max Deposit') }}</span>
                                            <span class="text-white font-bold text-sm">${{ number_format($flagship->min_amount, 0) }} - ${{ number_format($flagship->max_amount, 0) }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Flagship Right CTA --}}
                                <div class="lg:col-span-4 text-center lg:text-right space-y-4">
                                    <div class="p-6 rounded-2xl bg-black/40 border border-white/[0.06] text-center space-y-3">
                                        <span class="text-[10px] font-mono uppercase tracking-widest text-slate-400 block">{{ __('Daily Return Ceiling') }}</span>
                                        <div class="text-3xl font-mono font-black text-emerald-400">
                                            {{ $flagship->daily_return_max }}% <span class="text-xs text-slate-400 font-normal">/ {{ __('Max Day') }}</span>
                                        </div>
                                        <a href="{{ route('user.trading-bots.index') }}" 
                                           class="block w-full py-4 px-6 rounded-xl bg-gradient-to-r from-cyan-400 to-teal-400 hover:from-cyan-300 hover:to-teal-300 text-slate-950 font-mono font-black text-xs uppercase tracking-widest shadow-[0_0_30px_rgba(0,245,255,0.3)] transition-all">
                                            {{ __('Start This Bot') }} →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- ==================================================================================== --}}
        {{-- SECTION 3: BOT DIRECTORY & FILTER CONTROLS --}}
        {{-- ==================================================================================== --}}
        <section id="fleetMatrix" class="relative py-20 sm:py-28">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                
                {{-- Directory Header & Controls --}}
                <div class="space-y-6 mb-12">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                                {{ __('BOT DIRECTORY') }}
                            </div>
                            <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                                {{ __('Choose Your Trading Bot') }}
                            </h2>
                        </div>

                        {{-- Total Counter --}}
                        <div class="font-mono text-xs text-slate-400">
                            <span class="text-white font-bold" id="visibleBotCount">{{ $allBots->count() }}</span> {{ __('trading bots ready to activate') }}
                        </div>
                    </div>

                    {{-- Interactive Filter Bar --}}
                    <div class="p-3 sm:p-4 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] backdrop-blur-xl flex flex-col lg:flex-row items-center justify-between gap-4">
                        
                        {{-- Search Input --}}
                        <div class="relative w-full lg:w-72">
                            <input type="text" 
                                   id="botSearchInput"
                                   placeholder="{{ __('Search by bot name or pair...') }}" 
                                   class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 pl-10 text-xs font-mono text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 outline-none transition-all">
                            <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        {{-- Category Tabs --}}
                        <div class="flex items-center gap-2 overflow-x-auto w-full lg:w-auto pb-2 lg:pb-0 scrollbar-none font-mono text-xs">
                            <button class="filter-tab-btn active px-4 py-2 rounded-xl bg-cyan-400 text-slate-950 font-bold transition-all whitespace-nowrap cursor-pointer" data-type="all">
                                {{ __('All Categories') }}
                            </button>
                            @foreach ($botTypes as $type)
                                <button class="filter-tab-btn px-4 py-2 rounded-xl bg-white/[0.03] hover:bg-white/[0.08] text-slate-300 hover:text-white border border-white/[0.06] transition-all whitespace-nowrap capitalize cursor-pointer" data-type="{{ strtolower($type) }}">
                                    {{ __($type) }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Sort Dropdown --}}
                        <div class="flex items-center gap-3 w-full lg:w-auto justify-end font-mono">
                            <label class="text-[10px] uppercase text-slate-500 tracking-wider whitespace-nowrap">{{ __('Sort:') }}</label>
                            <select id="botSortSelector" class="bg-black/50 border border-white/10 rounded-xl px-3 py-2 text-xs font-mono text-white focus:border-cyan-400 outline-none cursor-pointer">
                                <option value="featured">{{ __('Most Popular') }}</option>
                                <option value="roi_desc">{{ __('Highest Daily Profit') }}</option>
                                <option value="min_asc">{{ __('Lowest Minimum Deposit') }}</option>
                                <option value="name">{{ __('Name (A-Z)') }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- Traded Markets Quick Pills Strip --}}
                    @if (!empty($botMarkets) && count($botMarkets) > 0)
                        <div class="flex items-center gap-2 flex-wrap font-mono text-[11px]">
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider mr-1">{{ __('Filter by Pair:') }}</span>
                            @foreach (array_slice($botMarkets->toArray(), 0, 10) as $market)
                                <button class="market-pill-btn px-3 py-1 rounded-lg bg-white/[0.02] border border-white/[0.06] text-slate-400 hover:text-cyan-300 hover:border-cyan-500/30 transition-all cursor-pointer" data-market="{{ $market }}">
                                    {{ $market }}
                                </button>
                            @endforeach
                            <button id="clearMarketFilters" class="hidden px-2.5 py-1 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500/20 transition-all text-[10px] cursor-pointer">
                                {{ __('Clear Filter ✕') }}
                            </button>
                        </div>
                    @endif
                </div>

                {{-- The Fleet Cards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8" id="botsFleetContainer">
                    @foreach ($allBots as $bot)
                        @include('templates.york.blades.pages.partials.bot_card', ['bot' => $bot])
                    @endforeach
                </div>

                {{-- Empty State --}}
                <div id="noFleetBotsFound" class="hidden text-center py-28 px-4 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] mt-8">
                    <div class="w-16 h-16 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 font-mono">{{ __('No Bots Match Your Search') }}</h3>
                    <p class="text-sm text-slate-400 max-w-md mx-auto mb-6">{{ __('Try adjusting your search keywords, category filter, or selected currency pair.') }}</p>
                    <button id="resetAllFiltersBtn" class="px-6 py-3 rounded-xl bg-cyan-400 text-slate-950 font-mono font-bold text-xs uppercase tracking-widest hover:bg-cyan-300 transition-all cursor-pointer">
                        {{ __('Reset All Filters') }}
                    </button>
                </div>
            </div>
        </section>

        {{-- ==================================================================================== --}}
        {{-- SECTION 4: PROFIT CALCULATOR --}}
        {{-- ==================================================================================== --}}
        <section id="profitCalculator" class="relative py-20 sm:py-28 border-t border-white/[0.06] bg-[#090c14]/60">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="max-w-3xl mx-auto text-center mb-12">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        {{ __('PROFIT CALCULATOR') }}
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                        {{ __('Calculate Your Expected Profits') }}
                    </h2>
                    <p class="text-sm sm:text-base text-slate-400 mt-3">
                        {{ __('Estimate your potential earnings based on your deposit amount, bot profit rate, and duration.') }}
                    </p>
                </div>

                <div class="max-w-4xl mx-auto rounded-3xl p-[1px] bg-gradient-to-b from-white/10 via-cyan-500/20 to-transparent shadow-2xl">
                    <div class="bg-[#090c14]/95 rounded-[calc(1.5rem-1px)] p-6 sm:p-10 backdrop-blur-2xl font-mono">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                            
                            {{-- Input Controls --}}
                            <div class="space-y-6">
                                <div>
                                    <div class="flex justify-between items-center text-xs mb-2 text-slate-300">
                                        <span>{{ __('Deposit Amount (USD)') }}</span>
                                        <span class="text-cyan-400 font-black text-sm" id="calcCapitalDisplay">$5,000</span>
                                    </div>
                                    <input type="range" id="calcCapitalSlider" min="100" max="50000" step="100" value="5000" 
                                           class="w-full h-2 bg-black/60 rounded-lg appearance-none cursor-pointer accent-cyan-400">
                                    <div class="flex justify-between text-[9px] text-slate-500 mt-1">
                                        <span>$100</span>
                                        <span>$25,000</span>
                                        <span>$50,000+</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between items-center text-xs mb-2 text-slate-300">
                                        <span>{{ __('Estimated Daily Profit') }}</span>
                                        <span class="text-emerald-400 font-black text-sm" id="calcRoiDisplay">2.5%</span>
                                    </div>
                                    <input type="range" id="calcRoiSlider" min="0.5" max="5.0" step="0.1" value="2.5" 
                                           class="w-full h-2 bg-black/60 rounded-lg appearance-none cursor-pointer accent-emerald-400">
                                    <div class="flex justify-between text-[9px] text-slate-500 mt-1">
                                        <span>0.5% (Conservative)</span>
                                        <span>2.5% (Standard)</span>
                                        <span>5.0% (Aggressive)</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between items-center text-xs mb-2 text-slate-300">
                                        <span>{{ __('Trading Duration') }}</span>
                                        <span class="text-indigo-400 font-black text-sm" id="calcDurationDisplay">30 Days</span>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 text-xs">
                                        <button class="calc-duration-btn py-2 rounded-xl bg-white/[0.03] border border-white/[0.08] text-slate-300 hover:border-cyan-400 transition-all cursor-pointer" data-days="7">7D</button>
                                        <button class="calc-duration-btn py-2 rounded-xl bg-white/[0.03] border border-white/[0.08] text-slate-300 hover:border-cyan-400 transition-all cursor-pointer" data-days="14">14D</button>
                                        <button class="calc-duration-btn active py-2 rounded-xl bg-cyan-400 text-slate-950 font-bold border border-cyan-400 transition-all cursor-pointer" data-days="30">30D</button>
                                        <button class="calc-duration-btn py-2 rounded-xl bg-white/[0.03] border border-white/[0.08] text-slate-300 hover:border-cyan-400 transition-all cursor-pointer" data-days="60">60D</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Projected Results Box --}}
                            <div class="p-6 sm:p-8 rounded-2xl bg-black/60 border border-white/[0.08] text-center space-y-4 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 blur-2xl rounded-full pointer-events-none"></div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-widest block">{{ __('Projected Net Return') }}</span>
                                
                                <div class="text-4xl sm:text-5xl font-black text-emerald-400" id="calcProjectedProfit">
                                    +$3,750.00
                                </div>

                                <div class="pt-4 border-t border-white/[0.08] space-y-2 text-xs text-slate-400">
                                    <div class="flex justify-between">
                                        <span>{{ __('Initial Deposit:') }}</span>
                                        <span class="text-white font-bold" id="calcSummaryCapital">$5,000.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>{{ __('Daily Payout Est.:') }}</span>
                                        <span class="text-cyan-300 font-bold" id="calcSummaryDaily">$125.00/day</span>
                                    </div>
                                    <div class="flex justify-between text-sm font-bold pt-2 border-t border-white/[0.04]">
                                        <span class="text-white">{{ __('Total Payout at End:') }}</span>
                                        <span class="text-emerald-400" id="calcSummaryTotal">$8,750.00</span>
                                    </div>
                                </div>

                                <a href="{{ route('user.trading-bots.index') }}" 
                                   class="block w-full py-4 rounded-xl bg-gradient-to-r from-cyan-400 to-indigo-500 hover:from-cyan-300 hover:to-indigo-400 text-slate-950 font-black text-xs uppercase tracking-widest shadow-[0_0_25px_rgba(0,245,255,0.25)] transition-all">
                                    {{ __('Start Trading Now') }} →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================================================================================== --}}
        {{-- SECTION 5: HOW IT WORKS --}}
        {{-- ==================================================================================== --}}
        <section class="relative py-20 sm:py-28 border-t border-white/[0.06]">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-3">
                        {{ __('HOW IT WORKS') }}
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                        {{ __('How Our Automated Trading Bots Work') }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 font-mono">
                    {{-- Step 1 --}}
                    <div class="p-6 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] relative group hover:border-cyan-500/40 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-black text-sm mb-4">
                            01
                        </div>
                        <h3 class="text-base font-bold text-white mb-2">{{ __('Market Scanning') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            {{ __('Monitors real-time price movements across top crypto and forex exchanges 24/7.') }}
                        </p>
                    </div>

                    {{-- Step 2 --}}
                    <div class="p-6 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] relative group hover:border-cyan-500/40 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-black text-sm mb-4">
                            02
                        </div>
                        <h3 class="text-base font-bold text-white mb-2">{{ __('Signal Analysis') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            {{ __('Identifies low-risk buying and selling opportunities using tested market strategies.') }}
                        </p>
                    </div>

                    {{-- Step 3 --}}
                    <div class="p-6 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] relative group hover:border-cyan-500/40 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-black text-sm mb-4">
                            03
                        </div>
                        <h3 class="text-base font-bold text-white mb-2">{{ __('Automatic Trades') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            {{ __('Opens and closes trades automatically with fast execution and strict risk controls.') }}
                        </p>
                    </div>

                    {{-- Step 4 --}}
                    <div class="p-6 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] relative group hover:border-cyan-500/40 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center font-black text-sm mb-4">
                            04
                        </div>
                        <h3 class="text-base font-bold text-white mb-2">{{ __('Daily Payouts') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            {{ __('Your profits are calculated daily and added directly to your account balance every 24 hours.') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let activeCategory = 'all';
            let activeMarket = null;
            let currentDuration = 30;

            function filterBots() {
                const query = $('#botSearchInput').val().toLowerCase().trim();
                const container = $('#botsFleetContainer');
                const cards = container.children('.bot-card');
                let visibleCount = 0;

                cards.each(function() {
                    const $card = $(this);
                    const type = $card.data('type') ? $card.data('type').toString().toLowerCase() : '';
                    const name = $card.data('name') ? $card.data('name').toString().toLowerCase() : '';
                    const markets = $card.data('markets') || [];

                    let matchesCategory = (activeCategory === 'all' || type === activeCategory);
                    let matchesMarket = (!activeMarket || markets.includes(activeMarket));
                    let matchesQuery = (!query || name.includes(query) || markets.some(m => m.toLowerCase().includes(query)));

                    if (matchesCategory && matchesMarket && matchesQuery) {
                        $card.removeClass('hidden');
                        visibleCount++;
                    } else {
                        $card.addClass('hidden');
                    }
                });

                $('#visibleBotCount').text(visibleCount);

                if (visibleCount === 0) {
                    $('#noFleetBotsFound').removeClass('hidden');
                } else {
                    $('#noFleetBotsFound').addClass('hidden');
                }
            }

            // Category Tab Clicks
            $('.filter-tab-btn').on('click', function() {
                $('.filter-tab-btn').removeClass('active bg-cyan-400 text-slate-950 font-bold')
                    .addClass('bg-white/[0.03] text-slate-300');
                $(this).addClass('active bg-cyan-400 text-slate-950 font-bold')
                    .removeClass('bg-white/[0.03] text-slate-300');
                
                activeCategory = $(this).data('type');
                filterBots();
            });

            // Search Filter
            $('#botSearchInput').on('input', function() {
                filterBots();
            });

            // Market Quick Pills
            $('.market-pill-btn').on('click', function() {
                const market = $(this).data('market');
                if (activeMarket === market) {
                    activeMarket = null;
                    $('.market-pill-btn').removeClass('bg-cyan-500/20 text-cyan-300 border-cyan-500/40');
                    $('#clearMarketFilters').addClass('hidden');
                } else {
                    activeMarket = market;
                    $('.market-pill-btn').removeClass('bg-cyan-500/20 text-cyan-300 border-cyan-500/40');
                    $(this).addClass('bg-cyan-500/20 text-cyan-300 border-cyan-500/40');
                    $('#clearMarketFilters').removeClass('hidden');
                }
                filterBots();
            });

            $('#clearMarketFilters').on('click', function() {
                activeMarket = null;
                $('.market-pill-btn').removeClass('bg-cyan-500/20 text-cyan-300 border-cyan-500/40');
                $(this).addClass('hidden');
                filterBots();
            });

            // Reset Button
            $('#resetAllFiltersBtn').on('click', function() {
                $('#botSearchInput').val('');
                activeCategory = 'all';
                activeMarket = null;
                $('.filter-tab-btn[data-type="all"]').click();
                $('#clearMarketFilters').click();
            });

            // Sorting Handler
            $('#botSortSelector').on('change', function() {
                const sortType = $(this).val();
                const container = $('#botsFleetContainer');
                const cards = container.children('.bot-card').get();

                cards.sort(function(a, b) {
                    const $a = $(a);
                    const $b = $(b);

                    if (sortType === 'roi_desc') return $b.data('roi') - $a.data('roi');
                    if (sortType === 'name') return ($a.data('name') || '').localeCompare($b.data('name') || '');
                    return $b.data('id') - $a.data('id');
                });

                $.each(cards, function(idx, card) {
                    container.append(card);
                });
            });

            // ================= PROFIT CALCULATOR =================
            function updateCalculator() {
                const capital = parseFloat($('#calcCapitalSlider').val());
                const dailyRoi = parseFloat($('#calcRoiSlider').val());
                const days = currentDuration;

                const dailyProfit = (capital * dailyRoi) / 100;
                const totalProfit = dailyProfit * days;
                const totalMaturity = capital + totalProfit;

                $('#calcCapitalDisplay').text('$' + capital.toLocaleString('en-US'));
                $('#calcRoiDisplay').text(dailyRoi.toFixed(1) + '%');
                $('#calcDurationDisplay').text(days + ' Days');

                $('#calcProjectedProfit').text('+$' + totalProfit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#calcSummaryCapital').text('$' + capital.toLocaleString('en-US', { minimumFractionDigits: 2 }));
                $('#calcSummaryDaily').text('$' + dailyProfit.toLocaleString('en-US', { minimumFractionDigits: 2 }) + '/day');
                $('#calcSummaryTotal').text('$' + totalMaturity.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            }

            $('#calcCapitalSlider, #calcRoiSlider').on('input', updateCalculator);

            $('.calc-duration-btn').on('click', function() {
                $('.calc-duration-btn').removeClass('active bg-cyan-400 text-slate-950 font-bold border-cyan-400')
                    .addClass('bg-white/[0.03] text-slate-300 border-white/[0.08]');
                $(this).addClass('active bg-cyan-400 text-slate-950 font-bold border-cyan-400')
                    .removeClass('bg-white/[0.03] text-slate-300');
                
                currentDuration = parseInt($(this).data('days'));
                updateCalculator();
            });

            updateCalculator();
        });
    </script>
@endpush
