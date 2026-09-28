@extends('templates.' . config('site.template') . '.blades.layouts.front')

@section('title', $page_title . ' - ' . getSetting('name'))
@section('page_title', $page_title)

@section('content')
    <div class="relative bg-[#050507] py-16 sm:py-24 overflow-hidden isolate">
        
        {{-- Ambient Glow --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-[radial-gradient(circle_at_50%_0%,rgba(0,245,255,0.08),transparent_70%)] pointer-events-none -z-10"></div>
        <div class="fixed inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.03] pointer-events-none -z-20"></div>

        <div class="max-w-7xl mx-auto px-4 lg:px-8 relative z-10">
            
            {{-- Hero Header --}}
            <div class="max-w-4xl mx-auto text-center mb-16 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-6 shadow-[0_0_20px_rgba(0,245,255,0.15)]">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('PRIVACY POLICY') }}
                </div>
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight leading-[1.08] mb-6">
                    {{ __('Privacy & Data') }} <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#00f5ff] via-white to-emerald-400">
                        {{ __('Protection.') }}
                    </span>
                </h1>
                <p class="text-base sm:text-xl text-slate-400 font-body leading-relaxed max-w-2xl mx-auto">
                    {{ __('At :site_name, your privacy and account security are our highest priorities. We never sell your personal data.', ['site_name' => getSetting('name')]) }}
                </p>
            </div>

            {{-- 3 Core Principles --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-20">
                
                <div class="p-8 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-cyan-400/40 backdrop-blur-2xl transition-all shadow-2xl group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 mb-6 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">{{ __('Advanced Encryption') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed font-body">
                            {{ __('We utilize AES-256 bank-grade encryption for all stored records and sensitive credentials.') }}
                        </p>
                    </div>
                </div>

                <div class="p-8 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-emerald-500/40 backdrop-blur-2xl transition-all shadow-2xl group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-6 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">{{ __('Zero Data Monetization') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed font-body">
                            {{ __(':site_name never sells user identity records or trading history to third-party data brokers.', ['site_name' => getSetting('name')]) }}
                        </p>
                    </div>
                </div>

                <div class="p-8 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-amber-500/40 backdrop-blur-2xl transition-all shadow-2xl group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-[#e2b13c] mb-6 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">{{ __('GDPR & Global Standards') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed font-body">
                            {{ __('Strict compliance with international privacy mandates, GDPR, and CCPA guidelines.') }}
                        </p>
                    </div>
                </div>

            </div>

            {{-- Policy Prose Container --}}
            <div class="max-w-4xl mx-auto rounded-3xl bg-[#090c14]/95 border border-white/[0.1] backdrop-blur-2xl p-8 sm:p-14 shadow-2xl mb-20 space-y-12">
                
                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-white font-mono uppercase tracking-wide flex items-center gap-3">
                        <span class="w-2 h-6 bg-[#00f5ff] rounded-full"></span>
                        {{ __('1. Information Collection Scope') }}
                    </h2>
                    <div class="text-slate-300 text-sm font-body leading-relaxed space-y-3 pl-5">
                        <p>{{ __('We collect the necessary information to provide our trading services and ensure account security:') }}</p>
                        <ul class="list-disc pl-5 space-y-1.5 text-slate-400 font-mono text-xs">
                            <li>{{ __('Personal Identification: Verified Name, email address, phone number, and KYC documentation.') }}</li>
                            <li>{{ __('Account & Trading Data: Wallet addresses, trading bot activity, and deposit/withdrawal history.') }}</li>
                            <li>{{ __('Technical Information: IP addresses and device data used strictly for account security and fraud prevention.') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-white font-mono uppercase tracking-wide flex items-center gap-3">
                        <span class="w-2 h-6 bg-[#00f5ff] rounded-full"></span>
                        {{ __('2. Operational Usage') }}
                    </h2>
                    <div class="text-slate-300 text-sm font-body leading-relaxed space-y-3 pl-5">
                        <p>{{ __('Collected data is used solely to operate your account and protect your funds:') }}</p>
                        <ul class="list-disc pl-5 space-y-1.5 text-slate-400 font-mono text-xs">
                            <li>{{ __('Operating automated trading bots and crediting earnings to your balance.') }}</li>
                            <li>{{ __('Verifying user identity in compliance with regulatory standards.') }}</li>
                            <li>{{ __('Preventing unauthorized access attempts or suspicious login activities.') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-white font-mono uppercase tracking-wide flex items-center gap-3">
                        <span class="w-2 h-6 bg-[#00f5ff] rounded-full"></span>
                        {{ __('3. Data Retention & Deletion') }}
                    </h2>
                    <p class="text-slate-300 text-sm font-body leading-relaxed pl-5">
                        {{ __('We retain personal data only as long as necessary to comply with legal obligations and provide platform services. Unneeded data is securely deleted.') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-white font-mono uppercase tracking-wide flex items-center gap-3">
                        <span class="w-2 h-6 bg-[#00f5ff] rounded-full"></span>
                        {{ __('4. Your Privacy Rights') }}
                    </h2>
                    <p class="text-slate-300 text-sm font-body leading-relaxed pl-5">
                        {{ __('You have the right to access, update, or request the deletion of your personal data at any time by contacting our support team.') }}
                    </p>
                </div>

                <div class="pt-6 border-t border-white/[0.08] flex items-center justify-between text-xs font-mono">
                    <span class="text-slate-400">{{ __('STATUS: ACTIVE') }}</span>
                    <span class="text-cyan-400 font-bold">{{ __('LAST UPDATED:') . ' ' . date('F d, Y') }}</span>
                </div>

            </div>

            {{-- Final Call to Action --}}
            <div class="relative rounded-3xl bg-[#090c14]/95 border border-cyan-500/30 p-10 sm:p-16 text-center overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/10 blur-3xl rounded-full pointer-events-none"></div>

                <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight mb-6">
                    {{ __('Trade with Full Privacy & Protection') }}
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
