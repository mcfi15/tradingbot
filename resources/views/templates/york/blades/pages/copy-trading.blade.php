@extends('templates.york.blades.layouts.front')

@section('title', $page_title . ' - ' . getSetting('name'))
@section('page_title', $page_title)

@section('content')
    <style>
        /* Pulse and Floating Animations */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(0.5deg); }
        }
        .animate-float-slow {
            animation: floatSlow 8s ease-in-out infinite;
        }

        /* Laser Flow */
        .copy-laser-flow {
            stroke-dasharray: 120 700;
            stroke-dashoffset: 820;
            animation: copyLaserFlow 4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
        @keyframes copyLaserFlow {
            0% { stroke-dashoffset: 820; opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 1; }
            100% { stroke-dashoffset: -100; opacity: 0; }
        }
    </style>

    <div class="relative bg-[#050507] isolate overflow-hidden min-h-screen">
        
        {{-- Ambient Mesh Glow Elements --}}
        <div class="absolute top-10 left-1/4 -translate-x-1/2 w-[650px] h-[650px] bg-emerald-500/10 rounded-full blur-[160px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 9s;"></div>
        <div class="absolute top-1/2 right-0 w-[550px] h-[550px] bg-cyan-500/10 rounded-full blur-[150px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 12s;"></div>
        <div class="absolute bottom-20 left-10 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[140px] pointer-events-none -z-10"></div>
        <div class="fixed inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:40px_40px] opacity-[0.025] pointer-events-none -z-20"></div>

        {{-- ==================================================================================== --}}
        {{-- SECTION 1: HERO & LIVE COPY TRADING PERFORMANCE CONSOLE --}}
        {{-- ==================================================================================== --}}
        <section class="relative pt-12 pb-20 sm:pt-20 sm:pb-28 border-b border-white/[0.06]">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                    
                    {{-- Left Column: Headline, Proof Badges, Action CTAs --}}
                    <div class="lg:col-span-7 space-y-8 text-left">
                        <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 font-mono text-[11px] font-bold uppercase tracking-widest shadow-[0_0_20px_rgba(16,185,129,0.15)]">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>{{ __('AUTOMATED COPY TRADING') }}</span>
                            <span class="text-slate-600">•</span>
                            <span class="text-cyan-400">{{ __('VERIFIED RESULTS') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight leading-[1.05]">
                            {{ __('Copy Top Traders') }}<br>
                            <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">
                                {{ __('Automatically 24/7') }}
                            </span>
                        </h1>

                        <p class="text-base sm:text-lg text-slate-300 font-normal leading-relaxed max-w-2xl">
                            {{ $page_description }}
                        </p>

                        {{-- Metric Chips in Everyday Language --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 font-mono">
                            <div class="p-4 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] backdrop-blur-xl">
                                <span class="text-[10px] text-slate-500 uppercase tracking-widest block mb-1">{{ __('Total Profit Paid') }}</span>
                                <div class="text-2xl font-black text-emerald-400">{{ showAmount($stats['total_profit']) }}</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] backdrop-blur-xl">
                                <span class="text-[10px] text-slate-500 uppercase tracking-widest block mb-1">{{ __('Volume Traded') }}</span>
                                <div class="text-2xl font-black text-white">{{ showAmount($stats['total_volume']) }}</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] backdrop-blur-xl">
                                <span class="text-[10px] text-slate-500 uppercase tracking-widest block mb-1">{{ __('Win Rate') }}</span>
                                <div class="text-2xl font-black text-cyan-300">{{ $stats['success_rate'] }}%</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] backdrop-blur-xl">
                                <span class="text-[10px] text-slate-500 uppercase tracking-widest block mb-1">{{ __('Active Traders') }}</span>
                                <div class="text-2xl font-black text-indigo-300">{{ number_format($stats['active_traders']) }}</div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap items-center gap-4 pt-2">
                            <a href="{{ route('user.register') }}" class="px-8 py-4 rounded-2xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 font-mono font-black text-xs uppercase tracking-widest shadow-[0_0_30px_rgba(16,185,129,0.3)] hover:shadow-[0_0_40px_rgba(16,185,129,0.5)] transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                                <span>{{ __('Start Copy Trading') }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            <a href="#results" class="px-6 py-4 rounded-2xl bg-white/[0.03] hover:bg-white/[0.08] border border-white/10 hover:border-emerald-500/30 text-white font-mono text-xs uppercase tracking-widest transition-all">
                                {{ __('View Completed Trades') }}
                            </a>
                        </div>
                    </div>

                    {{-- Right Column: Live Copy Trading Console --}}
                    <div class="lg:col-span-5 relative">
                        <div class="relative rounded-3xl p-[1px] bg-gradient-to-b from-emerald-500/30 via-white/[0.08] to-cyan-500/20 shadow-[0_0_50px_rgba(16,185,129,0.1)]">
                            <div class="bg-[#090c14]/95 rounded-[calc(1.5rem-1px)] p-6 sm:p-7 backdrop-blur-2xl font-mono relative overflow-hidden">
                                
                                {{-- Console Header --}}
                                <div class="flex items-center justify-between pb-4 mb-5 border-b border-white/[0.08]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-rose-500/70 inline-block"></span>
                                        <span class="w-3 h-3 rounded-full bg-amber-500/70 inline-block"></span>
                                        <span class="w-3 h-3 rounded-full bg-emerald-500/70 inline-block"></span>
                                        <span class="text-xs font-bold text-slate-300 ml-2 uppercase tracking-wider">{{ __('Live Copy Stream') }}</span>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold">
                                        {{ __('REAL-TIME') }}
                                    </span>
                                </div>

                                {{-- Live Stream Cards --}}
                                <div class="space-y-2.5 mb-6 text-xs">
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/[0.04]">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center font-bold text-emerald-400 text-xs">
                                                AK
                                            </div>
                                            <div>
                                                <div class="text-white font-bold">{{ __('Alex K. (Pro Trader)') }}</div>
                                                <div class="text-[10px] text-slate-500">BTC/USDT • Long 10x</div>
                                            </div>
                                        </div>
                                        <span class="text-emerald-400 font-bold text-sm">+$640.20</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/[0.04]">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center font-bold text-cyan-400 text-xs">
                                                ST
                                            </div>
                                            <div>
                                                <div class="text-white font-bold">{{ __('Sarah T. (Forex Elite)') }}</div>
                                                <div class="text-[10px] text-slate-500">EUR/USD • Scalp 20x</div>
                                            </div>
                                        </div>
                                        <span class="text-emerald-400 font-bold text-sm">+$285.50</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/[0.04]">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center font-bold text-indigo-400 text-xs">
                                                MW
                                            </div>
                                            <div>
                                                <div class="text-white font-bold">{{ __('Marcus W. (Trend Bot)') }}</div>
                                                <div class="text-[10px] text-slate-500">SOL/USDT • Long 5x</div>
                                            </div>
                                        </div>
                                        <span class="text-emerald-400 font-bold text-sm">+$412.80</span>
                                    </div>
                                </div>

                                {{-- Aggregate Yield Banner --}}
                                <div class="p-4 rounded-2xl bg-black/40 border border-white/[0.06] mb-5 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] text-slate-400 block mb-1 uppercase">{{ __('Average Follower Profit') }}</span>
                                        <span class="text-xl font-black text-emerald-400">+22.8% / mo</span>
                                    </div>
                                    <span class="px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-xs font-bold border border-emerald-500/20">
                                        {{ __('VERIFIED') }}
                                    </span>
                                </div>

                                {{-- Security Footer --}}
                                <div class="flex items-center justify-between text-[9px] text-slate-500">
                                    <span>INSTANT EXECUTION &lt; 15MS</span>
                                    <span class="text-emerald-400 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        ACTIVE COPIES
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================================================================================== --}}
        {{-- SECTION 2: VERIFIED COMPLETED TRADES SHOWCASE --}}
        {{-- ==================================================================================== --}}
        <section id="results" class="relative py-20 sm:py-28 border-b border-white/[0.06]">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-12">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                            {{ __('TRANSPARENT RESULTS') }}
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                            {{ __('Recently Completed Copy Trades') }}
                        </h2>
                    </div>
                    <span class="font-mono text-xs text-slate-400">
                        {{ __('100% verified on-chain execution') }}
                    </span>
                </div>

                @if ($recentTrades->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($recentTrades as $trade)
                            @include('templates.york.blades.pages.partials.copy_trade_result_card', [
                                'trade' => $trade,
                            ])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 bg-[#090c14]/90 border border-white/[0.08] rounded-3xl font-mono text-sm text-slate-400">
                        {{ __('Recent completed trades will display here automatically in real time.') }}
                    </div>
                @endif
            </div>
        </section>

        {{-- ==================================================================================== --}}
        {{-- SECTION 3: HOW COPY TRADING WORKS (3 STEPS) --}}
        {{-- ==================================================================================== --}}
        <section class="relative py-20 sm:py-28 border-b border-white/[0.06] bg-[#090c14]/60">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-3">
                        {{ __('SIMPLE 3-STEP PROCESS') }}
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                        {{ __('How Copy Trading Works') }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 font-mono">
                    {{-- Step 1 --}}
                    <div class="p-8 rounded-3xl bg-[#090c14]/95 border border-white/[0.08] relative group hover:border-emerald-500/40 transition-all shadow-xl">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-black text-base mb-6 group-hover:bg-emerald-400 group-hover:text-slate-950 transition-all">
                            01
                        </div>
                        <h3 class="text-lg font-bold text-white mb-3">{{ __('Choose a Top Trader') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed font-body">
                            {{ __('Browse through verified expert traders and automated strategies. Compare historical win rates, total profits, and risk styles to pick your favorite.') }}
                        </p>
                    </div>

                    {{-- Step 2 --}}
                    <div class="p-8 rounded-3xl bg-[#090c14]/95 border border-white/[0.08] relative group hover:border-cyan-500/40 transition-all shadow-xl">
                        <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-black text-base mb-6 group-hover:bg-cyan-400 group-hover:text-slate-950 transition-all">
                            02
                        </div>
                        <h3 class="text-lg font-bold text-white mb-3">{{ __('Set Your Investment Amount') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed font-body">
                            {{ __('Decide how much capital you want to allocate to copy the trader. You keep 100% control of your funds and can pause or withdraw anytime.') }}
                        </p>
                    </div>

                    {{-- Step 3 --}}
                    <div class="p-8 rounded-3xl bg-[#090c14]/95 border border-white/[0.08] relative group hover:border-indigo-500/40 transition-all shadow-xl">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-black text-base mb-6 group-hover:bg-indigo-400 group-hover:text-slate-950 transition-all">
                            03
                        </div>
                        <h3 class="text-lg font-bold text-white mb-3">{{ __('Earn Automatically') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed font-body">
                            {{ __('Every time the expert opens, manages, or closes a position, your account mirrors it automatically. Profits are paid directly to your balance.') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================================================================================== --}}
        {{-- SECTION 4: INVESTOR SAFETY & CAPITAL PROTECTION --}}
        {{-- ==================================================================================== --}}
        <section class="relative py-20 sm:py-28 border-b border-white/[0.06]">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-4">
                            {{ __('SECURITY & CONTROL') }}
                        </div>
                        <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight mb-6">
                            {{ __('Your Capital Stays in Your Complete Control') }}
                        </h2>
                        <p class="text-sm sm:text-base text-slate-300 leading-relaxed mb-8">
                            {{ __('Copy trading connects directly via secure API execution. Traders cannot withdraw your funds or access your private keys.') }}
                        </p>

                        <div class="space-y-4 font-mono text-xs">
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">✓</div>
                                <div>
                                    <strong class="text-white block mb-0.5">{{ __('Automatic Stop-Loss Protection') }}</strong>
                                    <span class="text-slate-400">{{ __('Preset risk limits protect your account balance against severe market volatility.') }}</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">✓</div>
                                <div>
                                    <strong class="text-white block mb-0.5">{{ __('Instant Pause & Disconnect') }}</strong>
                                    <span class="text-slate-400">{{ __('Stop copying any trader or close individual positions with one click at any time.') }}</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">✓</div>
                                <div>
                                    <strong class="text-white block mb-0.5">{{ __('Zero Management Fees') }}</strong>
                                    <span class="text-slate-400">{{ __('Keep your earnings with transparent profit-sharing only on successful winning trades.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Security Box --}}
                    <div class="p-8 rounded-3xl bg-[#090c14]/95 border border-white/[0.08] font-mono relative overflow-hidden shadow-2xl">
                        <div class="absolute top-0 right-0 w-36 h-36 bg-cyan-500/10 blur-2xl rounded-full pointer-events-none"></div>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between pb-4 border-b border-white/[0.08]">
                                <span class="text-white font-bold text-sm">{{ __('Safety Verification') }}</span>
                                <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold">100% SECURE</span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-xs">
                                <div class="p-4 rounded-2xl bg-black/40 border border-white/[0.04]">
                                    <span class="text-[9px] text-slate-500 uppercase block mb-1">{{ __('Trader Verification') }}</span>
                                    <span class="text-white font-bold text-sm">{{ __('KYC & History Checked') }}</span>
                                </div>
                                <div class="p-4 rounded-2xl bg-black/40 border border-white/[0.04]">
                                    <span class="text-[9px] text-slate-500 uppercase block mb-1">{{ __('Fund Custody') }}</span>
                                    <span class="text-emerald-400 font-bold text-sm">{{ __('Non-Custodial') }}</span>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/15 text-xs text-slate-300 leading-relaxed">
                                {{ __('All trades execute in isolated sub-accounts with strict maximum risk parameters, ensuring your main balance is always safeguarded.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================================================================================== --}}
        {{-- SECTION 5: FINAL ACTION CTA DECK --}}
        {{-- ==================================================================================== --}}
        <section class="relative py-20 sm:py-28">
            <div class="max-w-5xl mx-auto px-4 lg:px-8 text-center">
                <div class="rounded-3xl p-[1px] bg-gradient-to-r from-emerald-500/40 via-cyan-500/30 to-indigo-500/40 shadow-[0_0_50px_rgba(16,185,129,0.15)]">
                    <div class="bg-[#090c14]/95 rounded-[calc(1.5rem-1px)] p-10 sm:p-16 backdrop-blur-2xl space-y-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-widest">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ __('START IN UNDER 2 MINUTES') }}
                        </div>

                        <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                            {{ __('Ready to Start Copy Trading?') }}
                        </h2>

                        <p class="text-sm sm:text-base text-slate-300 max-w-xl mx-auto leading-relaxed">
                            {{ __('Join thousands of investors earning daily crypto and forex profits automatically with verified top traders.') }}
                        </p>

                        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="{{ route('user.register') }}" 
                               class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 font-mono font-black text-xs uppercase tracking-widest shadow-[0_0_30px_rgba(16,185,129,0.4)] transition-all transform hover:-translate-y-0.5 active:scale-95">
                                {{ __('Create Free Account') }} →
                            </a>
                            <a href="{{ route('user.login') }}" 
                               class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white/[0.03] hover:bg-white/[0.08] border border-white/10 text-white font-mono text-xs uppercase tracking-widest transition-all">
                                {{ __('Sign In to Dashboard') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection
