@extends('templates.york.blades.layouts.front')

@section('title', $page_title . ' - ' . getSetting('name'))
@section('page_title', $page_title)

@section('content')
    <div class="relative bg-[#050507] py-16 sm:py-24 overflow-hidden isolate">
        
        {{-- Ambient Background Glows --}}
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-cyan-500/10 rounded-full blur-[150px] -z-10 pointer-events-none animate-pulse" style="animation-duration: 9s;"></div>
        <div class="absolute bottom-1/3 left-0 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[140px] -z-10 pointer-events-none"></div>
        <div class="fixed inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.03] pointer-events-none -z-20"></div>

        <div class="max-w-7xl mx-auto px-4 lg:px-8 relative z-10">
            
            {{-- Header Badge & Hero Narrative --}}
            <div class="max-w-3xl mx-auto text-center mb-20 sm:mb-28">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-6 shadow-[0_0_20px_rgba(0,245,255,0.15)]">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('ABOUT :site_name', ['site_name' => strtoupper(getSetting('name'))]) }}
                </div>
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight leading-[1.08] mb-6">
                    {{ __('Making Automated Crypto Trading') }} <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#00f5ff] via-white to-emerald-400">
                        {{ __('Accessible to Everyone.') }}
                    </span>
                </h1>
                <p class="text-base sm:text-xl text-slate-400 font-body leading-relaxed max-w-2xl mx-auto">
                    {{ __(':site_name connects you to smart AI trading bots and top verified traders, backed by fast and secure multi-chain blockchain settlement.', ['site_name' => getSetting('name')]) }}
                </p>
            </div>

            {{-- Mission & Vision 2-Column Split --}}
            <div class="grid lg:grid-cols-12 gap-8 lg:gap-12 items-center mb-28">
                
                {{-- Left Pane (7 Cols) --}}
                <div class="lg:col-span-7 rounded-3xl bg-[#090c14]/95 border border-white/[0.1] backdrop-blur-2xl p-8 sm:p-12 shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>

                    <div class="flex items-center gap-2 mb-6">
                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                        <span class="text-xs font-mono font-bold text-cyan-400 uppercase tracking-wider">{{ __('OUR MISSION') }}</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-black text-white leading-tight mb-6">
                        {{ __('Empowering Every Trader with Smart Tools.') }}
                    </h2>
                    <div class="space-y-4 text-slate-400 text-sm font-body leading-relaxed">
                        <p>
                            {{ __('Built to break down the barrier between complex trading tools and everyday investors, :site_name makes automated AI bots and copy trading accessible to all.', ['site_name' => getSetting('name')]) }}
                        </p>
                        <p>
                            {{ __('Our platform connects directly with leading cryptocurrency exchanges to execute trades reliably with fast, secure blockchain deposits and withdrawals.') }}
                        </p>
                    </div>
                </div>

                {{-- Right 4 Pillars Grid (5 Cols) --}}
                <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <div class="p-6 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] hover:border-cyan-400/40 transition-all shadow-xl">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 mb-4 shadow-inner">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04Cover1.016l.6 6a11.955 11.955 0 005.618 8.435L12 21.5l1.382-.52a11.955 11.955 0 005.618-8.435l.6-6z"/></svg>
                        </div>
                        <h4 class="text-white font-bold text-sm mb-1">{{ __('Uncompromising Security') }}</h4>
                        <p class="text-slate-400 text-xs leading-relaxed font-body">{{ __('Industry-standard encryption and secure wallet protocols.') }}</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] hover:border-purple-500/40 transition-all shadow-xl">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 mb-4 shadow-inner">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <h4 class="text-white font-bold text-sm mb-1">{{ __('Total Transparency') }}</h4>
                        <p class="text-slate-400 text-xs leading-relaxed font-body">{{ __('Live trade logs and verified performance history.') }}</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] hover:border-emerald-500/40 transition-all shadow-xl">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-4 shadow-inner">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h4 class="text-white font-bold text-sm mb-1">{{ __('Algorithmic Precision') }}</h4>
                        <p class="text-slate-400 text-xs leading-relaxed font-body">{{ __('Fast trade execution and reliable automated algorithms.') }}</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-[#090c14]/90 border border-white/[0.08] hover:border-amber-500/40 transition-all shadow-xl">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-[#e2b13c] mb-4 shadow-inner">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        </div>
                        <h4 class="text-white font-bold text-sm mb-1">{{ __('Global Connectivity') }}</h4>
                        <p class="text-slate-400 text-xs leading-relaxed font-body">{{ __('Deep liquidity across major cryptocurrency pairs.') }}</p>
                    </div>

                </div>
            </div>

            {{-- Metrics Bento Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-28">
                
                <div class="p-8 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] flex flex-col justify-between relative overflow-hidden shadow-2xl">
                    <div class="absolute top-0 right-0 w-28 h-28 bg-cyan-500/10 blur-2xl rounded-full pointer-events-none"></div>
                    <div>
                        <span class="text-cyan-400 font-mono font-bold uppercase tracking-widest text-[10px] block mb-3">{{ __('Trading Volume') }}</span>
                        <div class="text-4xl sm:text-5xl font-black font-mono text-white mb-2">$10B+</div>
                        <p class="text-slate-400 text-xs font-body">{{ __('Total trading volume executed across all supported exchanges.') }}</p>
                    </div>
                </div>

                <div class="p-8 rounded-3xl bg-[#090c14]/90 border border-cyan-500/30 flex flex-col justify-between relative overflow-hidden shadow-2xl">
                    <div class="absolute top-0 right-0 w-28 h-28 bg-emerald-500/10 blur-2xl rounded-full pointer-events-none"></div>
                    <div>
                        <span class="text-emerald-400 font-mono font-bold uppercase tracking-widest text-[10px] block mb-3">{{ __('Platform Uptime') }}</span>
                        <div class="text-4xl sm:text-5xl font-black font-mono text-white mb-2">99.98%</div>
                        <p class="text-slate-400 text-xs font-body">{{ __('High platform reliability and fast automated transaction processing.') }}</p>
                    </div>
                </div>

                <div class="p-8 rounded-3xl bg-[#090c14]/90 border border-white/[0.08] flex flex-col justify-between relative overflow-hidden shadow-2xl">
                    <div class="absolute top-0 right-0 w-28 h-28 bg-amber-500/10 blur-2xl rounded-full pointer-events-none"></div>
                    <div>
                        <span class="text-[#e2b13c] font-mono font-bold uppercase tracking-widest text-[10px] block mb-3">{{ __('Global Reach') }}</span>
                        <div class="text-4xl sm:text-5xl font-black font-mono text-white mb-2">150+</div>
                        <p class="text-slate-400 text-xs font-body">{{ __('Traders worldwide using our automated trading ecosystem.') }}</p>
                    </div>
                </div>

            </div>

            {{-- Leadership Team Section --}}
            <div id="team" class="mb-28">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-4 shadow-[0_0_20px_rgba(0,245,255,0.15)]">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                        {{ __('LEADERSHIP TEAM') }}
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight mb-4">
                        {{ __('Experienced Leaders in Crypto & Finance') }}
                    </h2>
                    <p class="text-slate-400 text-sm sm:text-base max-w-xl mx-auto font-body">
                        {{ __('Combining decades of experience in financial technology, blockchain security, and algorithmic trading systems.') }}
                    </p>
                </div>

                @if (!empty($management_team) && $management_team->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($management_team as $member)
                            @php
                                $imgName = $member->image ?? 'ceo.png';
                                $imgPath = 'assets/images/team/' . $imgName;
                            @endphp
                            <div class="rounded-3xl bg-[#090c14]/95 border border-white/[0.08] hover:border-cyan-400/40 p-5 sm:p-6 backdrop-blur-2xl transition-all duration-500 shadow-2xl group flex flex-col justify-between overflow-hidden">
                                <div>
                                    <div class="relative h-60 w-full overflow-hidden rounded-2xl mb-5 border border-white/[0.08] bg-black/40">
                                        @if (file_exists(public_path($imgPath)))
                                            <img src="{{ asset($imgPath) }}" 
                                                 alt="{{ $member->name }}" 
                                                 loading="lazy" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-cyan-500/10 text-cyan-400 font-mono font-black text-2xl">
                                                {{ strtoupper(substr($member->name ?? 'TM', 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-[#090c14] via-transparent to-transparent opacity-75"></div>
                                        <div class="absolute bottom-3 left-3">
                                            <span class="px-2.5 py-1 rounded-md bg-cyan-500/15 border border-cyan-500/25 text-cyan-300 text-[10px] font-mono font-bold uppercase tracking-wider">
                                                {{ __($member->designation ?? $member->role ?? 'Leadership') }}
                                            </span>
                                        </div>
                                    </div>

                                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">{{ $member->name }}</h3>
                                    <p class="text-xs text-slate-400 leading-relaxed font-body line-clamp-3">
                                        {{ __($member->description, ['site_name' => getSetting('name')]) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Default Team Members with Photos --}}
                    @php
                        $defaultTeam = [
                            ['name' => 'Alexander Vance', 'role' => 'Founder & CEO', 'image' => 'ceo.png', 'desc' => 'With over 18 years in quantitative finance and fintech leadership, Alexander guides the platform\'s mission to make automated trading accessible to all investors.'],
                            ['name' => 'Maya Sterling', 'role' => 'Chief Technology Officer', 'image' => 'cto.png', 'desc' => 'Specializing in ultra-low latency order routing and blockchain infrastructure, Maya directs core architecture and cybersecurity systems.'],
                            ['name' => 'Dr. Nathan Cole', 'role' => 'Head of Quantitative Strategy', 'image' => 'asset_head.png', 'desc' => 'PhD in Computational Finance, Dr. Cole oversees algorithmic model development, predictive volatility forecasting, and risk management protocols.'],
                            ['name' => 'Claire Dupont', 'role' => 'Global Governance & Compliance', 'image' => 'compliance_head.png', 'desc' => 'Expert in international financial compliance and digital asset regulation, Claire directs global governance and institutional operational standards.'],
                        ];
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($defaultTeam as $member)
                            <div class="rounded-3xl bg-[#090c14]/95 border border-white/[0.08] hover:border-cyan-400/40 p-5 sm:p-6 backdrop-blur-2xl transition-all duration-500 shadow-2xl group flex flex-col justify-between overflow-hidden">
                                <div>
                                    <div class="relative h-60 w-full overflow-hidden rounded-2xl mb-5 border border-white/[0.08] bg-black/40">
                                        <img src="{{ asset('assets/images/team/' . $member['image']) }}" 
                                             alt="{{ $member['name'] }}" 
                                             loading="lazy" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                        <div class="absolute inset-0 bg-gradient-to-t from-[#090c14] via-transparent to-transparent opacity-75"></div>
                                        <div class="absolute bottom-3 left-3">
                                            <span class="px-2.5 py-1 rounded-md bg-cyan-500/15 border border-cyan-500/25 text-cyan-300 text-[10px] font-mono font-bold uppercase tracking-wider">
                                                {{ $member['role'] }}
                                            </span>
                                        </div>
                                    </div>

                                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">{{ $member['name'] }}</h3>
                                    <p class="text-xs text-slate-400 leading-relaxed font-body">
                                        {{ $member['desc'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Final CTA Section --}}
            <div class="relative rounded-3xl bg-[#090c14]/95 border border-cyan-500/30 p-10 sm:p-16 text-center overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/10 blur-3xl rounded-full pointer-events-none"></div>

                <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight mb-6">
                    {{ __('Start Trading Smarter Today') }}
                </h2>
                <p class="text-slate-400 text-sm max-w-xl mx-auto mb-8 font-body">
                    {{ __('Join thousands of investors using automated bots and copy trading to build their portfolios.') }}
                </p>
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
