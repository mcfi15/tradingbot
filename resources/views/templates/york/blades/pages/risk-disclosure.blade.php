@extends('templates.' . config('site.template') . '.blades.layouts.front')

@section('title', $page_title . ' - ' . getSetting('name'))
@section('page_title', $page_title)

@section('content')
    <div class="relative bg-[#050507] py-16 sm:py-24 overflow-hidden isolate">
        
        {{-- Ambient Glow --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-[radial-gradient(circle_at_50%_0%,rgba(239,68,68,0.08),transparent_70%)] pointer-events-none -z-10"></div>
        <div class="fixed inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.03] pointer-events-none -z-20"></div>

        <div class="max-w-7xl mx-auto px-4 lg:px-8 relative z-10">
            
            {{-- Header Hero --}}
            <div class="max-w-4xl mx-auto text-center mb-16 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-6 shadow-[0_0_20px_rgba(239,68,68,0.15)]">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    {{ __('RISK DISCLOSURE') }}
                </div>
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight leading-[1.08] mb-6">
                    {{ __('Risk & Investment') }} <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 via-white to-[#e2b13c]">
                        {{ __('Disclosure.') }}
                    </span>
                </h1>
                <p class="text-base sm:text-xl text-slate-400 font-body leading-relaxed max-w-2xl mx-auto">
                    {{ __('Trading cryptocurrencies involves risk. Please make sure you understand the risks involved before using automated trading bots or copy trading on :site_name.', ['site_name' => getSetting('name')]) }}
                </p>
            </div>

            {{-- 3 Category Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-20">
                
                <div class="p-8 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-red-500/40 backdrop-blur-2xl transition-all shadow-2xl group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400 mb-6 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">{{ __('Market Volatility Risk') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed font-body">
                            {{ __('Cryptocurrency prices can rise and fall quickly. Only trade with money you can afford to lose.') }}
                        </p>
                    </div>
                </div>

                <div class="p-8 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-amber-500/40 backdrop-blur-2xl transition-all shadow-2xl group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-[#e2b13c] mb-6 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">{{ __('Execution & Slippage') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed font-body">
                            {{ __('During periods of rapid market movement, the final price of an order may differ slightly from the expected price.') }}
                        </p>
                    </div>
                </div>

                <div class="p-8 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-cyan-400/40 backdrop-blur-2xl transition-all shadow-2xl group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 mb-6 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">{{ __('Regulatory & Regional Rules') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed font-body">
                            {{ __('Changes in local laws and exchange policies may affect supported trading pairs or feature availability.') }}
                        </p>
                    </div>
                </div>

            </div>

            {{-- Policy Prose Container --}}
            <div class="max-w-4xl mx-auto rounded-3xl bg-[#090c14]/95 border border-white/[0.1] backdrop-blur-2xl p-8 sm:p-14 shadow-2xl mb-20 space-y-12">
                
                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-white font-mono uppercase tracking-wide flex items-center gap-3">
                        <span class="w-2 h-6 bg-red-500 rounded-full"></span>
                        {{ __('1. General Trading Risk') }}
                    </h2>
                    <p class="text-slate-300 text-sm font-body leading-relaxed pl-5">
                        {{ __('The value of cryptocurrencies can fluctuate significantly. Past performance of any bot or trader does not guarantee future results.') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-white font-mono uppercase tracking-wide flex items-center gap-3">
                        <span class="w-2 h-6 bg-red-500 rounded-full"></span>
                        {{ __('2. Market Liquidity & Volatility') }}
                    </h2>
                    <p class="text-slate-300 text-sm font-body leading-relaxed pl-5">
                        {{ __('During times of high volatility, order books on external exchanges may experience delays or sudden price changes.') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-white font-mono uppercase tracking-wide flex items-center gap-3">
                        <span class="w-2 h-6 bg-red-500 rounded-full"></span>
                        {{ __('3. Not Financial Advice') }}
                    </h2>
                    <div class="p-6 rounded-2xl bg-red-500/10 border border-red-500/20 text-slate-200 text-xs font-mono leading-relaxed italic">
                        {{ __(':site_name provides automated trading tools and copy trading software. Nothing on this website constitutes financial or investment advice. Always evaluate your own risk tolerance.', ['site_name' => getSetting('name')]) }}
                    </div>
                </div>

                <div class="pt-6 border-t border-white/[0.08] flex items-center justify-between text-xs font-mono">
                    <span class="text-slate-400">{{ __('DISCLOSURE STATUS: ACTIVE') }}</span>
                    <span class="text-red-400 font-bold">{{ __('TRADE RESPONSIBLY') }}</span>
                </div>

            </div>

            {{-- Final Call to Action --}}
            <div class="relative rounded-3xl bg-[#090c14]/95 border border-cyan-500/30 p-10 sm:p-16 text-center overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/10 blur-3xl rounded-full pointer-events-none"></div>

                <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight mb-6">
                    {{ __('Ready to Start Trading Smarter?') }}
                </h2>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('user.register') }}"
                        class="w-full sm:w-auto py-4 px-8 bg-[#00f5ff] hover:bg-cyan-300 text-[#050507] font-black uppercase text-xs tracking-widest rounded-2xl shadow-[0_0_30px_rgba(0,245,255,0.4)] transition-all cursor-pointer">
                        {{ __('Create Free Account') }}
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection
