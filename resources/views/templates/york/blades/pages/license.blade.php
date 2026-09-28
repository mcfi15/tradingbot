@extends('templates.' . config('site.template') . '.blades.layouts.front')

@section('title', $page_title . ' - ' . getSetting('name'))
@section('page_title', $page_title)

@section('content')
    <div class="relative bg-[#050507] py-16 sm:py-24 isolate overflow-hidden">
        
        {{-- Ambient Layered Glows --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-[radial-gradient(circle_at_50%_0%,rgba(0,245,255,0.08),transparent_70%)] pointer-events-none -z-10"></div>
        <div class="fixed inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.03] pointer-events-none -z-20"></div>

        <div class="max-w-7xl mx-auto px-4 lg:px-8 relative z-10">
            
            {{-- Header Hero --}}
            <div class="max-w-4xl mx-auto text-center mb-16 sm:mb-24">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-6 shadow-[0_0_20px_rgba(0,245,255,0.15)]">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ __('LICENSES & REGULATION') }}
                </div>
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight leading-[1.08] mb-6">
                    {{ __('Licenses & Regulatory') }} <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#00f5ff] via-white to-emerald-400">
                        {{ __('Compliance.') }}
                    </span>
                </h1>
                <p class="text-base sm:text-xl text-slate-400 font-body leading-relaxed max-w-2xl mx-auto">
                    {{ __(':site_name is dedicated to operating with total transparency, regulatory compliance, and industry-leading security standards.', ['site_name' => getSetting('name')]) }}
                </p>
            </div>

            {{-- 3 Governance Pillars --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-24">
                
                {{-- Pillar 1 --}}
                <div class="p-8 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-cyan-400/40 backdrop-blur-2xl transition-all shadow-2xl group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 mb-6 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">{{ __('Anti-Money Laundering (AML)') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed font-body">
                            {{ __('Comprehensive AML standards and automated wallet risk checks to prevent fraudulent activity.') }}
                        </p>
                    </div>
                </div>

                {{-- Pillar 2 --}}
                <div class="p-8 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-emerald-500/40 backdrop-blur-2xl transition-all shadow-2xl group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-6 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">{{ __('Data Security & Privacy') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed font-body">
                            {{ __('Industry-standard encryption to protect your personal identity and account data at all times.') }}
                        </p>
                    </div>
                </div>

                {{-- Pillar 3 --}}
                <div class="p-8 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-amber-500/40 backdrop-blur-2xl transition-all shadow-2xl group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-[#e2b13c] mb-6 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 01-6.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">{{ __('Secure Fund Protection') }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed font-body">
                            {{ __('User funds are held securely with multi-signature security protocols and separate storage vaults.') }}
                        </p>
                    </div>
                </div>

            </div>

            {{-- Supervisory Bodies Section --}}
            @if ($regulatoryCompliance && !empty($regulatoryCompliance['regulators']))
                <div class="mb-24">
                    <div class="flex flex-col lg:flex-row gap-12 items-start">
                        <div class="lg:w-1/3">
                            <span class="text-cyan-400 font-mono font-bold uppercase tracking-widest text-[10px] block mb-3">{{ __('Regulatory Oversight') }}</span>
                            <h2 class="text-3xl sm:text-4xl font-black text-white leading-tight mb-4">
                                {{ __('Regulatory Compliance & Standards') }}
                            </h2>
                            <p class="text-slate-400 text-sm leading-relaxed font-body">
                                {{ __('We operate in adherence with recognized financial standards across international jurisdictions.') }}
                            </p>
                        </div>

                        <div class="lg:w-2/3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($regulatoryCompliance['regulators'] as $regulator)
                                <div class="px-6 py-8 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] flex items-center justify-between group hover:border-cyan-400/40 transition-all border-l-4 border-l-cyan-400">
                                    <div class="flex items-center gap-3">
                                        <div class="w-3 h-3 rounded-full bg-cyan-400 animate-pulse"></div>
                                        <h4 class="text-base font-bold text-white font-mono uppercase tracking-wide">{{ __($regulator) }}</h4>
                                    </div>
                                    <span class="text-[10px] font-mono text-emerald-400 font-bold bg-emerald-500/10 border border-emerald-500/20 px-2 py-1 rounded">
                                        {{ __('COMPLIANT') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Certifications & PDF Verification Deck --}}
            @if ($regulatoryCompliance && !empty($regulatoryCompliance['pdf_certificates']))
                <div class="mb-24">
                    <div class="text-center max-w-3xl mx-auto mb-12">
                        <span class="text-emerald-400 font-mono font-bold uppercase tracking-widest text-[10px] block mb-3">{{ __('Official Documentation') }}</span>
                        <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight mb-4">
                            {{ __('Licenses & Certificates') }}
                        </h2>
                        <p class="text-slate-400 text-sm font-body">
                            {{ __('View and download official licensing and corporate verification documents.') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($regulatoryCompliance['pdf_certificates'] as $pdf)
                            <div class="p-7 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] hover:border-cyan-400/40 backdrop-blur-2xl transition-all shadow-2xl relative overflow-hidden group">
                                <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 mb-6 shadow-inner">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>

                                <h3 class="text-lg font-bold text-white mb-1 leading-snug">{{ __($pdf['name']) }}</h3>
                                <div class="text-[10px] text-emerald-400 font-mono font-bold uppercase tracking-widest mb-6">
                                    {{ __('VERIFIED CERTIFICATE') }}
                                </div>

                                <div class="flex items-center gap-3">
                                    <a href="{{ asset('assets/pdf/' . $pdf['file']) }}" target="_blank"
                                        class="flex-1 py-3 px-4 bg-[#00f5ff] hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2 shadow-[0_0_20px_rgba(0,245,255,0.3)] cursor-pointer">
                                        <span>{{ __('View Document') }}</span>
                                        <svg class="w-4 h-4 text-[#050507]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    <a href="{{ asset('assets/pdf/' . $pdf['file']) }}" download
                                        class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white hover:bg-white/10 transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Final Call to Action --}}
            <div class="relative rounded-3xl bg-[#090c14]/95 border border-cyan-500/30 p-10 sm:p-16 text-center overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/10 blur-3xl rounded-full pointer-events-none"></div>

                <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight mb-6">
                    {{ __('Ready to Trade with Confidence?') }}
                </h2>
                <p class="text-slate-400 text-sm max-w-xl mx-auto mb-8 font-body">
                    {{ __('Start trading with automated AI bots and copy trading on a secure, verified platform.') }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('user.register') }}"
                        class="w-full sm:w-auto py-4 px-8 bg-[#00f5ff] hover:bg-cyan-300 text-[#050507] font-black uppercase text-xs tracking-widest rounded-2xl shadow-[0_0_30px_rgba(0,245,255,0.4)] transition-all cursor-pointer">
                        {{ __('Open Account') }}
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection
