@extends('templates.' . config('site.template') . '.blades.layouts.front')

@section('title', $page_title . ' - ' . getSetting('name'))
@section('page_title', $page_title)

@section('content')
    <div class="relative bg-[#050507] py-16 sm:py-24 overflow-hidden isolate">
        
        {{-- Ambient Glow --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-[radial-gradient(circle_at_50%_0%,rgba(0,245,255,0.08),transparent_70%)] pointer-events-none -z-10"></div>
        <div class="fixed inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.03] pointer-events-none -z-20"></div>

        <div class="max-w-7xl mx-auto px-4 lg:px-8 relative z-10">
            
            {{-- Header Hero --}}
            <div class="max-w-4xl mx-auto text-center mb-16 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-6 shadow-[0_0_20px_rgba(0,245,255,0.15)]">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('TERMS OF SERVICE') }}
                </div>
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight leading-[1.08] mb-6">
                    {{ __('Terms & Conditions of') }} <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#00f5ff] via-white to-emerald-400">
                        {{ __('Service.') }}
                    </span>
                </h1>
                <p class="text-base sm:text-xl text-slate-400 font-body leading-relaxed max-w-2xl mx-auto mb-4">
                    {{ __('Please review the rules and guidelines governing the use of :site_name, trading bots, and copy trading services.', ['site_name' => getSetting('name')]) }}
                </p>
                <div class="flex items-center justify-center gap-4 text-xs font-mono text-slate-500">
                    <span>{{ __('Effective Date') }}: {{ date('F d, Y') }}</span>
                    <span>•</span>
                    <span>{{ __('Version') }}: 2.0</span>
                </div>
            </div>

            {{-- Policy Prose Container --}}
            <div class="max-w-4xl mx-auto rounded-3xl bg-[#090c14]/95 border border-white/[0.1] backdrop-blur-2xl p-8 sm:p-14 shadow-2xl mb-20 space-y-12">
                
                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-white font-mono uppercase tracking-wide flex items-center gap-3">
                        <span class="w-2 h-6 bg-[#00f5ff] rounded-full"></span>
                        {{ __('1. Eligibility & Account Security') }}
                    </h2>
                    <p class="text-slate-300 text-sm font-body leading-relaxed pl-5">
                        {{ __('To use :site_name, you must be at least 18 years old. You are responsible for keeping your login credentials safe. Identity verification may be required to process withdrawals.', ['site_name' => getSetting('name')]) }}
                    </p>
                </div>

                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-white font-mono uppercase tracking-wide flex items-center gap-3">
                        <span class="w-2 h-6 bg-[#00f5ff] rounded-full"></span>
                        {{ __('2. Trading Bots & Copy Trading Plans') }}
                    </h2>
                    <p class="text-slate-300 text-sm font-body leading-relaxed pl-5">
                        {{ __('Funds allocated to automated trading bots or copy trading plans remain active for the selected plan duration. Daily returns and profits are credited directly to your account balance.') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-white font-mono uppercase tracking-wide flex items-center gap-3">
                        <span class="w-2 h-6 bg-[#00f5ff] rounded-full"></span>
                        {{ __('3. Market Conditions & Execution') }}
                    </h2>
                    <p class="text-slate-300 text-sm font-body leading-relaxed pl-5">
                        {{ __('Trades are executed automatically through connected cryptocurrency exchanges. Unforeseen market volatility or third-party exchange downtime may affect order timing and prices.') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-white font-mono uppercase tracking-wide flex items-center gap-3">
                        <span class="w-2 h-6 bg-[#00f5ff] rounded-full"></span>
                        {{ __('4. Prohibited Activities') }}
                    </h2>
                    <p class="text-slate-300 text-sm font-body leading-relaxed pl-5">
                        {{ __('Users may not engage in fraudulent activities, platform exploitation, or unauthorized account sharing. Violations may result in immediate account suspension.') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-white font-mono uppercase tracking-wide flex items-center gap-3">
                        <span class="w-2 h-6 bg-[#00f5ff] rounded-full"></span>
                        {{ __('5. Limitation of Liability') }}
                    </h2>
                    <p class="text-slate-300 text-sm font-body leading-relaxed pl-5">
                        {{ __(':site_name provides automated trading tools and software. We are not liable for market fluctuations, losses caused by cryptocurrency price volatility, or compromised user passwords.', ['site_name' => getSetting('name')]) }}
                    </p>
                </div>

                <div class="pt-6 border-t border-white/[0.08] flex items-center justify-between text-xs font-mono">
                    <span class="text-slate-400">{{ __('TERMS STATUS: ACTIVE') }}</span>
                    <span class="text-cyan-400 font-bold">{{ __('VERSION 2.0') }}</span>
                </div>

            </div>

            {{-- Final Call to Action --}}
            <div class="relative rounded-3xl bg-[#090c14]/95 border border-cyan-500/30 p-10 sm:p-16 text-center overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/10 blur-3xl rounded-full pointer-events-none"></div>

                <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight mb-6">
                    {{ __('Ready to Get Started?') }}
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
