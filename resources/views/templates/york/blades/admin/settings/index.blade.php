@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12 font-mono">
        
        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('LEGAL & COMPLIANCE') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Legal Disclosures & Terms') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Platform notices, compliance responsibilities, and liability terms') }}
                </p>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- MAIN LAYOUT: SIDEBAR + CONTENT --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Navigation Deck --}}
            <div class="lg:col-span-4 xl:col-span-3">
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        @include("templates.$template.blades.admin.settings.partials.sidebar")
                    </div>
                </div>
            </div>

            {{-- Right Content Chassis --}}
            <div class="lg:col-span-8 xl:col-span-9 space-y-6">
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-8 text-xs">
                        
                        {{-- SECTION 1: ARCHITECTURE DEMONSTRATION --}}
                        <div class="space-y-4">
                            <div class="pb-3 border-b border-white/[0.06] flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                    01
                                </div>
                                <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Software Demonstration Notice') }}</h3>
                            </div>
                            <p class="text-slate-400 leading-relaxed">
                                {{ __('This software is developed as a financial infrastructure and trading engine platform. It illustrates the capabilities, design, and workflows of a modern bot and copy trading system.') }}
                            </p>
                        </div>

                        {{-- SECTION 2: INDEMNIFICATION & LIABILITY --}}
                        <div class="space-y-4 pt-6 border-t border-white/[0.06]">
                            <div class="pb-3 border-b border-white/[0.06] flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 border border-rose-500/20 flex items-center justify-center font-bold">
                                    02
                                </div>
                                <h3 class="text-sm font-black text-rose-400 uppercase tracking-wider">{{ __('Indemnification & Liability Notice') }}</h3>
                            </div>

                            <div class="p-5 rounded-2xl bg-rose-500/[0.03] border border-rose-500/20 space-y-3">
                                <p class="text-white font-bold uppercase tracking-wide leading-relaxed text-[11px]">
                                    {{ __('The platform creators and developers are not liable for any financial losses, damages, or regulatory penalties resulting from how the platform is operated or deployed.') }}
                                </p>
                                <p class="text-slate-400 leading-relaxed text-[10px]">
                                    {{ __('The platform operator assumes full responsibility for ensuring compliance with all local and international laws and regulations governing financial services and trading operations.') }}
                                </p>
                            </div>
                        </div>

                        {{-- SECTION 3: OPERATOR RESPONSIBILITY PILLARS --}}
                        <div class="space-y-4 pt-6 border-t border-white/[0.06]">
                            <div class="pb-3 border-b border-white/[0.06] flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                    03
                                </div>
                                <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Operator Responsibilities') }}</h3>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-4 rounded-2xl bg-white/[0.015] border border-white/[0.04] flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                    <div>
                                        <span class="text-white font-bold block">{{ __('Regulatory Compliance') }}</span>
                                        <span class="text-[9px] text-slate-500">{{ __('Obtain required licensing and provide clear user terms') }}</span>
                                    </div>
                                </div>

                                <div class="p-4 rounded-2xl bg-white/[0.015] border border-white/[0.04] flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                    <div>
                                        <span class="text-white font-bold block">{{ __('Financial Solvency') }}</span>
                                        <span class="text-[9px] text-slate-500">{{ __('Maintain adequate reserve balances and wallet security') }}</span>
                                    </div>
                                </div>

                                <div class="p-4 rounded-2xl bg-white/[0.015] border border-white/[0.04] flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                    <div>
                                        <span class="text-white font-bold block">{{ __('Account Security') }}</span>
                                        <span class="text-[9px] text-slate-500">{{ __('Implement strong admin access controls and two-factor security') }}</span>
                                    </div>
                                </div>

                                <div class="p-4 rounded-2xl bg-white/[0.015] border border-white/[0.04] flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                    <div>
                                        <span class="text-white font-bold block">{{ __('Third-Party Services') }}</span>
                                        <span class="text-[9px] text-slate-500">{{ __('Manage API connections, market data, and payment providers') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
