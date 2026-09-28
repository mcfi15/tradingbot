@extends('templates.york.blades.layouts.user')

@section('content')
    @include('templates.york.blades.partials.dashboard-partials')

    {{-- Asymmetrical Split-Console Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-12">
        
        {{-- ==================== LEFT COLUMN: TELEMETRY & WEIGHTS ==================== --}}
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
            
            {{-- 1. TOTAL VALUE (Dominant Double-Bezel Card) --}}
            <div class="relative group double-bezel-outer p-1 overflow-hidden">
                <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-20 pointer-events-none"></div>
                <div class="absolute top-0 right-0 -mt-12 -mr-12 w-48 h-48 bg-accent-primary/5 rounded-full blur-3xl pointer-events-none z-0"></div>
                <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-48 h-48 bg-accent-secondary/5 rounded-full blur-3xl pointer-events-none z-0"></div>

                <div class="relative z-10 double-bezel-inner p-6 flex flex-col h-full justify-between overflow-hidden">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="px-3 py-1 rounded-full bg-accent-primary/10 border border-accent-primary/20 text-accent-primary text-[10px] font-bold uppercase tracking-widest">
                                {{ __('Total Value') }}
                            </div>
                        </div>
                        <h3 class="text-3xl md:text-5xl font-black text-white tracking-tight mb-1">
                            <span class="text-transparent bg-clip-text bg-gradient-to-br from-white to-slate-400">
                                {{ getSetting('currency_symbol', '$') }}{{ number_format($total_equity, 2) }}
                            </span>
                        </h3>
                        <p class="text-slate-400 font-medium text-[10px] flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ __('Combined crypto and trading bot capital') }}
                        </p>
                    </div>

                    {{-- Sub-stats Container --}}
                    <div class="grid grid-cols-1 gap-2.5 mt-6">
                        {{-- Balance --}}
                        <div class="bg-white/[0.02] border border-white/[0.06] rounded-xl p-3.5 hover:bg-white/[0.05] transition-all cursor-pointer backdrop-blur-sm flex justify-between items-center">
                            <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('Wallet Balance') }}</span>
                            <span class="text-sm font-bold text-white tracking-wide">{{ showAmount($balance) }}</span>
                        </div>
                        @if (moduleEnabled('trading_bot_module'))
                            {{-- Bot Balance --}}
                            <div class="bg-white/[0.02] border border-white/[0.06] rounded-xl p-3.5 hover:bg-white/[0.05] transition-all cursor-pointer backdrop-blur-sm flex justify-between items-center">
                                <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('Bot Balance') }}</span>
                                <span class="text-sm font-bold text-white tracking-wide progress-value" data-value="{{ $bot_capital }}">{{ showAmount($bot_capital) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-2 gap-3 w-full">
                <a href="{{ route('user.deposits.new') }}"
                    class="group flex items-center justify-center gap-2 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl transition-all hover:bg-emerald-500/20 hover:border-emerald-500/30 cursor-pointer">
                    <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-emerald-55 text-[10px] font-bold tracking-widest uppercase">{{ __('Deposit') }}</span>
                </a>
                <a href="{{ route('user.withdrawals.new') }}"
                    class="group flex items-center justify-center gap-2 px-4 py-3 bg-rose-500/10 border border-rose-500/20 rounded-xl transition-all hover:bg-rose-500/20 hover:border-rose-500/30 cursor-pointer">
                    <svg class="h-4 w-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span class="text-rose-55 text-[10px] font-bold tracking-widest uppercase">{{ __('Withdraw') }}</span>
                </a>
            </div>

            {{-- 2. Money Distribution (Donut Chart) --}}
            <div class="bg-white/[0.02] border border-white/[0.06] rounded-2xl p-6 relative overflow-hidden flex flex-col items-center justify-center group hover:border-white/10 transition-colors">
                <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                <h4 class="absolute top-6 left-6 text-slate-400 text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    {{ __('Asset Allocation') }}
                </h4>
                <div id="distributionDonutChart" class="w-full h-[240px] mt-6"></div>
            </div>

        </div>

        {{-- ==================== RIGHT COLUMN: CHARTS, INSIGHTS & LOGS ==================== --}}
        <div class="lg:col-span-8 space-y-8">
            
            {{-- Header / Welcome --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-2 md:gap-4">
                <div>
                    <h2 class="text-2xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-slate-400 tracking-tight leading-tight">
                        {{ __('Portfolio Overview') }}
                    </h2>
                    <p class="text-slate-400 font-mono text-[9px] md:text-sm mt-0.5 md:mt-1 tracking-widest uppercase">
                        {{ __('Account status and active trading bots') }}
                    </p>
                </div>
            </div>

            {{-- 3. Financial Pulse / Cash Flow --}}
            <div class="relative rounded-2xl overflow-hidden group"
                 style="background: linear-gradient(145deg, rgba(8,9,14,0.95) 0%, rgba(4,5,8,0.98) 100%); border: 1px solid rgba(255,255,255,0.05);">

                {{-- Month watermark --}}
                <div class="absolute inset-0 flex items-center justify-end pr-6 pointer-events-none select-none overflow-hidden">
                    <span class="text-[7rem] md:text-[9rem] font-black uppercase tracking-tighter text-white/[0.025] leading-none">
                        {{ now()->format('M') }}
                    </span>
                </div>

                {{-- Emerald glow — left --}}
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] group-hover:scale-110 group-hover:opacity-80"></div>
                {{-- Rose glow — right --}}
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-40 h-40 bg-rose-500/10 rounded-full blur-3xl pointer-events-none transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] group-hover:scale-110 group-hover:opacity-80"></div>

                <div class="relative z-10 p-6">

                    {{-- Top bar: label + net badge --}}
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse shadow-[0_0_8px_rgba(226,177,60,0.6)]"></span>
                            <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-slate-400">
                                {{ now()->format('F Y') }} · {{ __('Cash Flow') }}
                            </span>
                        </div>
                        @php
                            $netFlow = $deposits_month - $withdrawals_month_amount;
                            $isPositive = $netFlow >= 0;
                        @endphp
                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)]
                            {{ $isPositive ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : 'bg-rose-500/10 border-rose-500/20 text-rose-400' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                @if($isPositive)
                                    <path d="M18 15l-6-6-6 6"/>
                                @else
                                    <path d="M6 9l6 6 6-6"/>
                                @endif
                            </svg>
                            {{ $isPositive ? '+' : '' }}{{ showAmount($netFlow) }} {{ __('Net') }}
                        </div>
                    </div>

                    {{-- Two metric columns --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">

                        {{-- Inflow --}}
                        <div class="relative rounded-xl p-4 overflow-hidden transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] hover:scale-[1.02]"
                             style="background: rgba(16,185,129,0.05); border: 1px solid rgba(16,185,129,0.12);">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 via-transparent to-transparent pointer-events-none"></div>
                            <div class="relative z-10">
                                <div class="flex items-center gap-1.5 mb-3">
                                    <div class="w-5 h-5 rounded-full bg-emerald-500/15 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 19V5m-7 7 7-7 7 7"/>
                                        </svg>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-emerald-400/70">{{ __('Inflow') }}</span>
                                </div>
                                <div class="font-black text-xl md:text-2xl text-emerald-400 tracking-tight leading-none tabular-nums">
                                    +{{ showAmount($deposits_month) }}
                                </div>
                                <div class="text-[9px] text-slate-500 uppercase tracking-wider mt-2 font-medium">
                                    {{ __('This month') }}
                                </div>
                            </div>
                        </div>

                        {{-- Outflow --}}
                        <div class="relative rounded-xl p-4 overflow-hidden transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] hover:scale-[1.02]"
                             style="background: rgba(244,63,94,0.05); border: 1px solid rgba(244,63,94,0.12);">
                            <div class="absolute inset-0 bg-gradient-to-br from-rose-500/5 via-transparent to-transparent pointer-events-none"></div>
                            <div class="relative z-10">
                                <div class="flex items-center gap-1.5 mb-3">
                                    <div class="w-5 h-5 rounded-full bg-rose-500/15 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 5v14m7-7-7 7-7-7"/>
                                        </svg>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-rose-400/70">{{ __('Outflow') }}</span>
                                </div>
                                <div class="font-black text-xl md:text-2xl text-rose-400 tracking-tight leading-none tabular-nums">
                                    -{{ showAmount($withdrawals_month_amount) }}
                                </div>
                                <div class="text-[9px] text-slate-500 uppercase tracking-wider mt-2 font-medium">
                                    {{ __('This month') }}
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Proportional pulse bar --}}
                    @php
                        $total = $deposits_month + $withdrawals_month_amount;
                        $inPct = $total > 0 ? round(($deposits_month / $total) * 100) : 50;
                        $outPct = 100 - $inPct;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-[8px] font-bold uppercase tracking-widest text-slate-500 mb-1.5">
                            <span class="text-emerald-400/60">{{ $inPct }}% {{ __('in') }}</span>
                            <span class="text-rose-400/60">{{ $outPct }}% {{ __('out') }}</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full overflow-hidden flex gap-px" style="background: rgba(255,255,255,0.04);">
                            <div class="h-full rounded-l-full transition-all duration-1000 ease-[cubic-bezier(0.32,0.72,0,1)]"
                                 style="width: {{ $inPct }}%; background: linear-gradient(90deg, rgba(16,185,129,0.6), rgba(16,185,129,0.9));"></div>
                            <div class="h-full rounded-r-full transition-all duration-1000 ease-[cubic-bezier(0.32,0.72,0,1)]"
                                 style="width: {{ $outPct }}%; background: linear-gradient(90deg, rgba(244,63,94,0.9), rgba(244,63,94,0.5));"></div>
                        </div>
                    </div>

                    {{-- Pending pills --}}
                    @if($deposits_pending > 0 || $withdrawals_pending > 0)
                        <div class="flex items-center gap-2 mt-4 flex-wrap">
                            @if($deposits_pending > 0)
                                <a href="{{ route('user.deposits.index') }}" class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[8px] font-bold uppercase tracking-widest text-amber-400 border border-amber-400/20 bg-amber-400/5 hover:bg-amber-400/10 transition-colors">
                                    <span class="w-1 h-1 rounded-full bg-amber-400 animate-pulse"></span>
                                    {{ $deposits_pending }} {{ __('deposit(s) pending') }}
                                </a>
                            @endif
                            @if($withdrawals_pending > 0)
                                <a href="{{ route('user.withdrawals.index') }}" class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[8px] font-bold uppercase tracking-widest text-amber-400 border border-amber-400/20 bg-amber-400/5 hover:bg-amber-400/10 transition-colors">
                                    <span class="w-1 h-1 rounded-full bg-amber-400 animate-pulse"></span>
                                    {{ $withdrawals_pending }} {{ __('withdrawal(s) pending') }}
                                </a>
                            @endif
                        </div>
                    @endif

                </div>
            </div>

            {{-- 4. Deposit Wallet Addresses (Replaced Smart Insights) --}}
            <div class="bg-gradient-to-br from-[#0c0d12]/60 to-[#040406]/80 border border-white/5 rounded-2xl relative overflow-hidden flex flex-col h-[450px] shadow-2xl w-full p-6">
                <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-accent-primary/5 rounded-full blur-3xl pointer-events-none z-0"></div>

                <div class="flex items-center justify-between mb-6 z-10">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-accent-primary/10 rounded-lg text-accent-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect>
                                <line x1="2" y1="10" x2="22" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-white text-sm uppercase tracking-widest">{{ __('Deposit Addresses') }}</h3>
                            <p class="text-[9px] text-text-secondary uppercase tracking-widest mt-0.5">{{ __('Your deposit wallet addresses') }}</p>
                        </div>
                    </div>
                    @if ($user_wallets->isNotEmpty())
                        <a href="{{ route('user.deposits.new') }}" class="text-[10px] text-accent-primary font-bold uppercase hover:underline z-10">{{ __('New Deposit') }} &rarr;</a>
                    @endif
                </div>

                {{-- Wallet Addresses List --}}
                <div class="flex-1 overflow-y-auto pr-1 space-y-3 z-10 scrollbar-thin scrollbar-thumb-white/5 scrollbar-track-transparent">
                    @forelse ($user_wallets as $wallet)
                        <div class="rounded-xl bg-white/[0.02] border border-white/5 hover:border-white/10 transition-all flex flex-col gap-0">
                            @php
                                $bcLogo = !empty($wallet->blockchain->logo)
                                    ? asset($wallet->blockchain->logo)
                                    : asset('assets/images/tokens/' . strtolower($wallet->blockchain->code) . '.png');
                                $qrId = 'qr-panel-' . $loop->index;
                                $qrTargetId = 'qr-canvas-' . $loop->index;
                            @endphp

                            {{-- Main row --}}
                            <div class="p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center shrink-0 border border-white/5 overflow-hidden p-1">
                                        <img src="{{ $bcLogo }}"
                                             alt="{{ $wallet->blockchain->name }}"
                                             class="w-full h-full object-contain rounded-full"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span style="display:none" class="w-full h-full items-center justify-center text-accent-primary font-mono text-[10px] font-black uppercase">
                                            {{ substr($wallet->blockchain->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-white uppercase tracking-wider truncate">{{ $wallet->blockchain->name }}</h4>
                                        <div class="grid grid-cols-3 gap-1 mt-1">
                                            @forelse ($wallet->blockchain->tokens as $token)
                                                @php
                                                    $tokenLogo = !empty($token->logo)
                                                        ? asset($token->logo)
                                                        : asset('assets/images/tokens/' . strtolower($token->symbol) . '.png');
                                                @endphp
                                                <span class="flex items-center gap-1 text-[8px] font-bold uppercase tracking-wider text-text-secondary bg-white/[0.04] border border-white/5 rounded px-1.5 py-0.5 leading-none overflow-hidden">
                                                    <img src="{{ $tokenLogo }}"
                                                         alt="{{ $token->symbol }}"
                                                         class="w-3 h-3 object-contain rounded-full shrink-0"
                                                         onerror="this.style.display='none'">
                                                    <span class="truncate">{{ $token->symbol }}</span>
                                                </span>
                                            @empty
                                                <span class="col-span-3 text-[8px] text-text-secondary uppercase tracking-widest">{{ __('Native') }}</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                {{-- Address + Actions --}}
                                <div class="flex items-center gap-2 bg-black/40 rounded-lg px-3 py-1.5 border border-white/5 flex-1 max-w-[280px] sm:max-w-md w-full justify-between">
                                    <code class="font-mono text-[11px] text-text-primary truncate select-all flex-1 pr-2">{{ $wallet->address }}</code>
                                    <div class="flex items-center gap-1 shrink-0">
                                        {{-- Copy --}}
                                        <button onclick="navigator.clipboard.writeText('{{ $wallet->address }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Address copied') }}', showConfirmButton:false, timer:1500});"
                                                class="text-text-secondary hover:text-white transition-colors cursor-pointer p-1" title="{{ __('Copy address') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                            </svg>
                                        </button>
                                        {{-- QR Code --}}
                                        <button onclick="toggleQr('{{ $qrId }}', '{{ $qrTargetId }}', '{{ addslashes($wallet->address) }}')"
                                                class="text-text-secondary hover:text-accent-primary transition-colors cursor-pointer p-1" title="{{ __('Show QR code') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                                                <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                                                <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                                                <rect x="14" y="14" width="3" height="3"></rect>
                                                <rect x="18" y="14" width="3" height="3"></rect>
                                                <rect x="14" y="18" width="3" height="3"></rect>
                                                <rect x="18" y="18" width="3" height="3"></rect>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- QR Panel (hidden by default) --}}
                            <div id="{{ $qrId }}" class="hidden border-t border-white/5">
                                <div class="flex flex-col items-center gap-3 py-5 px-4">
                                    <div id="{{ $qrTargetId }}" class="p-3 bg-white rounded-xl shadow-xl shadow-black/40"></div>
                                    <p class="font-mono text-[9px] text-text-secondary break-all text-center max-w-[260px] select-all leading-relaxed">
                                        {{ $wallet->address }}
                                    </p>
                                    <p class="text-[9px] text-text-secondary/50 uppercase tracking-widest">{{ $wallet->blockchain->name }} {{ __('Deposit Address') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- Empty State with Generate Button --}}
                        <div class="h-full flex flex-col items-center justify-center text-center p-6 bg-white/[0.01] border border-dashed border-white/5 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-text-secondary/40 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <p class="text-xs text-text-secondary max-w-sm mb-4 leading-normal">
                                {{ __('You have not created any deposit addresses yet. Create one below to easily fund your account.') }}
                            </p>
                            <a href="{{ route('user.deposits.new') }}" 
                               class="inline-flex items-center gap-2 px-5 py-2 bg-accent-primary hover:bg-accent-primary/90 text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-accent-primary/25 cursor-pointer">
                                <span>{{ __('Create Deposit Address') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"></path>
                                    <path d="m12 5 7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 5. Transaction Trend (Main Graph) --}}
            <div class="bg-white/[0.02] border border-white/[0.06] rounded-2xl p-6 relative overflow-hidden group hover:border-white/10 transition-colors w-full">
                <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                    <div>
                        <h4 class="text-slate-400 text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                            {{ __('Activity Trend') }}
                        </h4>
                        <div class="text-white font-black text-xl mt-1">{{ __('Volume and Transactions') }}</div>
                    </div>
                    {{-- Custom Dropdown Filter --}}
                    <div class="relative" id="tx-chart-filter-container">
                        <button id="tx-chart-filter-button"
                            class="flex items-center gap-2 bg-white/[0.02] px-4 py-2 rounded-xl border border-border-primary text-[10px] font-bold uppercase tracking-widest text-white hover:bg-white/[0.06] transition-all cursor-pointer">
                            <span id="current-filter-label">{{ __('7 Days') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div id="tx-chart-filter-menu"
                            class="absolute right-0 mt-2 w-40 origin-top-right rounded-xl bg-[#0c0c0f]/95 border border-white/5 shadow-2xl opacity-0 invisible scale-95 transition-all z-[100] backdrop-blur-xl">
                            <div class="p-1.5 space-y-1">
                                <button data-days="7" class="w-full text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-white hover:bg-white/[0.02] rounded-lg transition-all">{{ __('7 Days') }}</button>
                                <button data-days="30" class="w-full text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-white hover:bg-white/[0.02] rounded-lg transition-all">{{ __('30 Days') }}</button>
                                <button data-days="90" class="w-full text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-white hover:bg-white/[0.02] rounded-lg transition-all">{{ __('90 Days') }}</button>
                                <button data-days="365" class="w-full text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-white hover:bg-white/[0.02] rounded-lg transition-all">{{ __('1 Year') }}</button>
                                <button data-days="all" class="w-full text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-white hover:bg-white/[0.02] rounded-lg transition-all">{{ __('All Time') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="transactionTrendChart" class="w-full h-[350px]"></div>
            </div>

        {{-- Section 3: Transactions Overview --}}
        <div class="mt-12 pt-12 border-t border-white/5">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-black text-white flex items-center gap-3">
                        <span class="w-2 h-8 bg-emerald-500 rounded-full shadow-[0_0_15px_rgba(16,185,129,0.5)]"></span>
                        {{ __('Transaction Overview') }}
                    </h2>
                    <p class="text-slate-500 text-sm mt-1 uppercase tracking-widest font-bold opacity-70">
                        {{ __('Deposits, withdrawals, and ledger status') }}
                    </p>
                </div>
                <div class="flex items-center gap-6">
                    <div class="hidden md:flex flex-col items-end">
                        <span class="text-[10px] text-slate-500 uppercase font-black">{{ __('Net Cash Flow (MTD)') }}</span>
                        <span
                            class="text-lg font-mono font-bold {{ $movement_stats['net_cashflow'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $movement_stats['net_cashflow'] >= 0 ? '+' : '' }}{{ showAmount($movement_stats['net_cashflow']) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Column 1: Deposits --}}
                <div class="bg-white/[0.02] border border-white/[0.06] rounded-2xl p-6 backdrop-blur-sm flex flex-col h-full">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-sm uppercase tracking-wider">{{ __('Deposits') }}</h3>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span
                                        class="text-[9px] text-slate-500 uppercase font-bold">{{ __('Active Channels') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Grid --}}
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                            <span
                                class="text-[8px] text-slate-500 uppercase font-bold block mb-1">{{ __('Approved') }}</span>
                            <span
                                class="text-sm font-bold text-white">{{ showAmount($movement_stats['deposits']['approved']) }}</span>
                        </div>
                        <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                            <span class="text-[8px] text-slate-500 uppercase font-bold block mb-1">{{ __('Pending') }}</span>
                            <span
                                class="text-sm font-bold text-amber-400">{{ showAmount($movement_stats['deposits']['pending']) }}</span>
                        </div>
                    </div>

                    {{-- Data List --}}
                    <div class="space-y-4 mb-auto">
                        <h4 class="text-[9px] text-slate-500 uppercase font-bold tracking-widest border-b border-white/5 pb-2">
                            {{ __('Recent Deposits') }}</h4>
                        <div class="space-y-3">
                            @forelse($movement_stats['recent_deposits'] as $deposit)
                                <div class="flex items-center justify-between group">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-xs font-bold text-white group-hover:text-emerald-400 transition-colors uppercase tracking-tight">{{ $deposit->gatewayName() }}</span>
                                        <span
                                            class="text-[9px] text-slate-500 font-mono">{{ $deposit->created_at->format('M d, H:i') }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs font-bold text-white">{{ showAmount($deposit->amount) }}</div>
                                        <div
                                            class="text-[9px] {{ $deposit->status == 'completed' ? 'text-emerald-500' : ($deposit->status == 'pending' ? 'text-amber-500' : 'text-rose-500') }} font-bold uppercase">
                                            {{ __($deposit->status) }}</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-[10px] text-slate-500 italic py-2">{{ __('No recent deposits') }}</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Footer Insight --}}
                    <div class="mt-8 pt-4 border-t border-white/5">
                        <div class="flex items-center justify-between opacity-80">
                            <div>
                                <span
                                    class="text-[8px] text-slate-500 uppercase block font-bold tracking-widest">{{ __('Avg Size') }}</span>
                                <span
                                    class="text-xs font-bold text-white">{{ showAmount($movement_stats['deposits']['avg_size']) }}</span>
                            </div>
                            <div class="text-right">
                                <span
                                    class="text-[8px] text-slate-500 uppercase block font-bold tracking-widest">{{ __('Top Method') }}</span>
                                <span
                                    class="text-xs font-bold text-emerald-400">{{ $movement_stats['deposits']['most_used_method'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Column 2: Withdrawals --}}
                <div class="bg-white/[0.02] border border-white/[0.06] rounded-2xl p-6 backdrop-blur-sm flex flex-col h-full">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-accent-primary/10 flex items-center justify-center text-accent-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17l-5 5-5-5M12 4v18" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-sm uppercase tracking-wider">{{ __('Withdrawals') }}
                                </h3>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                                    <span
                                        class="text-[9px] text-slate-500 uppercase font-bold">{{ __('Outbound Queue') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Grid --}}
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                            <span class="text-[8px] text-slate-500 uppercase font-bold block mb-1">{{ __('Paid') }}</span>
                            <span
                                class="text-sm font-bold text-white">{{ showAmount($movement_stats['withdrawals']['paid']) }}</span>
                        </div>
                        <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                            <span
                                class="text-[8px] text-slate-500 uppercase font-bold block mb-1">{{ __('Processing') }}</span>
                            <span
                                class="text-sm font-bold text-accent-primary">{{ showAmount($movement_stats['withdrawals']['pending']) }}</span>
                        </div>
                    </div>

                    {{-- Data List --}}
                    <div class="space-y-4 mb-auto">
                        <h4 class="text-[9px] text-slate-500 uppercase font-bold tracking-widest border-b border-white/5 pb-2">
                            {{ __('Recent Withdrawals') }}</h4>
                        <div class="space-y-3">
                            @forelse($movement_stats['recent_withdrawals'] as $withdrawal)
                                <div class="flex items-center justify-between group">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-xs font-bold text-white group-hover:text-accent-primary transition-colors uppercase tracking-tight">{{ $withdrawal->gatewayName() }}</span>
                                        <span
                                            class="text-[9px] text-slate-500 font-mono">{{ $withdrawal->created_at->format('M d, H:i') }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs font-bold text-white">{{ showAmount($withdrawal->amount) }}</div>
                                        <div
                                            class="text-[9px] {{ $withdrawal->status == 'completed' ? 'text-emerald-500' : ($withdrawal->status == 'pending' ? 'text-accent-primary' : 'text-rose-500') }} font-bold uppercase">
                                            {{ $withdrawal->status == 'completed' ? __('Paid') : __($withdrawal->status) }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-[10px] text-slate-500 italic py-2">{{ __('No recent withdrawals') }}</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Footer Insight --}}
                    <div class="mt-8 pt-4 border-t border-white/5">
                        <div class="flex items-center justify-between opacity-80">
                            <div>
                                <span
                                    class="text-[8px] text-slate-500 uppercase block font-bold tracking-widest">{{ __('Total Fees Paid') }}</span>
                                <span
                                    class="text-xs font-bold text-white">{{ showAmount($movement_stats['withdrawals']['total_fees']) }}</span>
                            </div>
                            <div class="text-right">
                                <span
                                    class="text-[8px] text-slate-500 uppercase block font-bold tracking-widest">{{ __('rejected') }}</span>
                                <span
                                    class="text-xs font-bold text-rose-400">{{ showAmount($movement_stats['withdrawals']['rejected']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Column 3: Transactions --}}
                <div class="bg-white/[0.02] border border-white/[0.06] rounded-2xl p-6 backdrop-blur-sm flex flex-col h-full">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-accent-primary/10 flex items-center justify-center text-accent-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-sm uppercase tracking-wider">{{ __('Transactions') }}</h3>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                                    <span
                                        class="text-[9px] text-slate-500 uppercase font-bold">{{ __('Recent Activity') }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('user.transactions') }}"
                            class="text-[10px] text-accent-primary font-bold uppercase hover:underline">{{ __('View All') }}</a>
                    </div>

                    {{-- Monthly Highlights --}}
                    <div class="bg-white/5 rounded-xl p-4 border border-white/5 mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span
                                class="text-[9px] text-slate-500 uppercase font-bold tracking-widest">{{ __('Monthly Fees') }}</span>
                            <span
                                class="text-xs font-bold text-amber-500">{{ showAmount($movement_stats['total_fees_month']) }}</span>
                        </div>
                        <div class="w-full bg-white/5 h-1 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full w-2/3"></div>
                        </div>
                    </div>

                    {{-- Transactions List --}}
                    <div class="space-y-4 mb-auto overflow-y-auto max-h-[220px] scrollbar-hide">
                        @forelse($recent_transactions_hub as $tx)
                            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 transition-all group">
                                <div
                                    class="w-8 h-8 rounded-lg {{ $tx->type == 'credit' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }} flex items-center justify-center">
                                    @if ($tx->type == 'credit')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <span
                                            class="text-[11px] font-bold text-white truncate pr-2">{{ $tx->description }}</span>
                                        <span
                                            class="text-[11px] font-bold {{ $tx->type == 'credit' ? 'text-emerald-400' : 'text-rose-400' }}">
                                            {{ $tx->type == 'credit' ? '+' : '-' }}{{ showAmount($tx->amount) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-[8px] text-slate-500 font-mono">{{ $tx->created_at->format('d/m H:i') }}</span>
                                        <span
                                            class="text-[8px] text-slate-600 uppercase font-black tracking-tighter">{{ $tx->reference }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-[10px] text-slate-500 italic py-2">{{ __('No recent transactions') }}</p>
                        @endforelse
                    </div>

                    {{-- Footer Action --}}
                    <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-center">
                        <div class="flex -space-x-2">
                            <div
                                class="w-6 h-6 rounded-full border-2 border-secondary bg-slate-800 flex items-center justify-center text-[8px] text-white">
                                B</div>
                            <div
                                class="w-6 h-6 rounded-full border-2 border-secondary bg-slate-700 flex items-center justify-center text-[8px] text-white font-bold">
                                +</div>
                        </div>
                        <span
                            class="ml-3 text-[9px] text-slate-500 uppercase font-bold">{{ __('Live transaction sync active') }}</span>
                    </div>
                </div>
        </div>
        </div>
        </div>
    @endsection

    @section('scripts')
        {{-- Charting & Carousel Dependencies --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // 1. Swiper Initialization (Fanned Cards)
                if (document.querySelector('.mySwiper')) {
                    new Swiper(".mySwiper", {
                        effect: "cards",
                        grabCursor: true,
                        loop: true,
                        centeredSlides: true,
                        slidesPerView: "auto",
                        autoplay: {
                            delay: 3500,
                            disableOnInteraction: false,
                            pauseOnMouseEnter: true,
                        },
                        watchSlidesProgress: true,
                        cardsEffect: {
                            perSlideOffset: 12,
                            perSlideRotate: 4,
                            slideShadows: true,
                        },
                        navigation: {
                            nextEl: ".swiper-button-next-custom",
                            prevEl: ".swiper-button-prev-custom",
                        },
                    });
                }

                // 2. Money Distribution Donut Chart
                const distData = @json($money_distribution);
                const distLabels = Object.keys(distData);
                const distValues = Object.values(distData);

                if (document.querySelector("#distributionDonutChart")) {
                    const distOptions = {
                        chart: {
                            type: 'donut',
                            height: 280,
                            background: 'transparent',
                            sparkline: {
                                enabled: false
                            }
                        },
                        series: distValues,
                        labels: distLabels,
                        colors: ['#e2b13c', '#10b981', '#f59e0b', '#3b82f6', '#ec4899', '#8b5cf6', '#f43f5e'],
                        stroke: {
                            show: false
                        },
                        legend: {
                            position: 'bottom',
                            fontSize: '10px',
                            fontFamily: 'inherit',
                            fontWeight: 700,
                            labels: {
                                colors: '#94a3b8'
                            },
                            markers: {
                                radius: 12
                            },
                            itemMargin: {
                                horizontal: 5,
                                vertical: 5
                            }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '78%',
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: "{{ __('Total Value') }}",
                                            color: '#94a3b8',
                                            fontSize: '10px',
                                            fontWeight: 700,
                                            formatter: function(w) {
                                                const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                                return new Intl.NumberFormat('en-US', {
                                                    style: 'currency',
                                                    currency: '{{ getSetting('currency', 'USD') }}',
                                                    maximumFractionDigits: 0
                                                }).format(total);
                                            }
                                        },
                                        value: {
                                            show: true,
                                            fontSize: '18px',
                                            fontWeight: 900,
                                            color: '#ffffff',
                                            formatter: function(val) {
                                                return new Intl.NumberFormat('en-US', {
                                                    style: 'currency',
                                                    currency: '{{ getSetting('currency', 'USD') }}',
                                                    maximumFractionDigits: 0
                                                }).format(val);
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        tooltip: {
                            theme: 'dark',
                            y: {
                                formatter: (val) => new Intl.NumberFormat('en-US', {
                                    style: 'currency',
                                    currency: '{{ getSetting('currency', 'USD') }}'
                                }).format(val)
                            }
                        }
                    };
                    new ApexCharts(document.querySelector("#distributionDonutChart"), distOptions).render();
                }

                // 3. Transaction Trend Chart
                const chartData = @json($chart_data);
                let trendChart;

                if (document.querySelector("#transactionTrendChart")) {
                    const trendOptions = {
                        series: [{
                            name: "{{ __('Credits') }}",
                            data: chartData.credits
                        }, {
                            name: "{{ __('Debits') }}",
                            data: chartData.debits
                        }],
                        chart: {
                            type: 'area',
                            height: 350,
                            toolbar: {
                                show: false
                            },
                            stacked: false,
                            zoom: {
                                enabled: false
                            }
                        },
                        colors: ['#10b981', '#ef4444'],
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.7,
                                opacityTo: 0.2,
                                stops: [0, 90, 100]
                            }
                        },
                        markers: {
                            size: 4,
                            colors: ['#10b981', '#ef4444'],
                            strokeColors: '#0f172a',
                            strokeWidth: 2,
                            hover: {
                                size: 6
                            }
                        },
                        xaxis: {
                            categories: chartData.labels,
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            },
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '10px'
                                },
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '10px'
                                },
                                formatter: (val) => '{{ getSetting('currency_symbol', '$') }}' + Intl
                                    .NumberFormat().format(val)
                            }
                        },
                        grid: {
                            borderColor: 'rgba(255, 255, 255, 0.03)',
                            padding: {
                                right: 20
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right',
                            labels: {
                                colors: '#94a3b8'
                            }
                        },
                        tooltip: {
                            theme: 'dark'
                        }
                    };
                    trendChart = new ApexCharts(document.querySelector("#transactionTrendChart"), trendOptions);
                    trendChart.render();
                }

                // Chart Scaling Logic
                const filterButton = document.getElementById('tx-chart-filter-button');
                const filterMenu = document.getElementById('tx-chart-filter-menu');
                const filterOptions = document.querySelectorAll('#tx-chart-filter-menu button');
                const filterLabel = document.getElementById('current-filter-label');

                // Set initial active state for 7 Days
                document.querySelector('#tx-chart-filter-menu button[data-days="7"]').classList.add('text-white',
                    'bg-white/[0.02]');


                // Toggle Dropdown
                filterButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    filterMenu.classList.toggle('opacity-0');
                    filterMenu.classList.toggle('invisible');
                    filterMenu.classList.toggle('scale-95');
                });

                // Close on click outside
                document.addEventListener('click', () => {
                    filterMenu.classList.add('opacity-0', 'invisible', 'scale-95');
                });

                // Handle Selection
                filterOptions.forEach(opt => {
                    opt.addEventListener('click', async function() {
                        const days = this.getAttribute('data-days');
                        const label = this.innerText;

                        // UI Update
                        filterLabel.innerText = label;
                        filterOptions.forEach(o => o.classList.remove('text-white',
                            'bg-white/[0.02]'));
                        this.classList.add('text-white', 'bg-white/[0.02]');

                        try {
                            const response = await fetch(
                                `{{ route('user.dashboard') }}?days=${days}`, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                            const newData = await response.json();

                            trendChart.updateOptions({
                                xaxis: {
                                    categories: newData.labels
                                }
                            });
                            trendChart.updateSeries([{
                                name: "{{ __('Credits') }}",
                                data: newData.credits
                            }, {
                                name: "{{ __('Debits') }}",
                                data: newData.debits
                            }]);
                        } catch (error) {
                            console.error('Failed to fetch chart data:', error);
                        }
                    });
                });

                // 3. ROI Generation Trend Chart
                const earningsData = { labels: [], amounts: [] };
                let yieldChart;
                if (document.querySelector("#earningsChart")) {
                    const earningsOptions = {
                        chart: {
                            type: 'area',
                            height: 180,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent',
                            sparkline: {
                                enabled: false
                            }
                        },
                        series: [{
                            name: "{{ __('ROI Payout') }}",
                            data: earningsData.amounts
                        }],
                        xaxis: {
                            categories: earningsData.labels,
                            labels: {
                                show: true,
                                style: {
                                    colors: '#64748b',
                                    fontSize: '10px'
                                },
                                rotate: -45,
                                maxHeight: 30
                            },
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            }
                        },
                        yaxis: {
                            labels: {
                                show: true,
                                style: {
                                    colors: '#64748b',
                                    fontSize: '10px'
                                },
                                formatter: (val) => val.toFixed(2)
                            }
                        },
                        grid: {
                            borderColor: 'rgba(255, 255, 255, 0.05)',
                            strokeDashArray: 4,
                            padding: {
                                left: 0,
                                right: 0
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2,
                            colors: ['#e2b13c']
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0.05,
                                stops: [0, 90, 100],
                                colorStops: [{
                                        offset: 0,
                                        color: '#e2b13c',
                                        opacity: 0.4
                                    },
                                    {
                                        offset: 100,
                                        color: '#e2b13c',
                                        opacity: 0
                                    }
                                ]
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        tooltip: {
                            theme: 'dark'
                        },
                        theme: {
                            mode: 'dark'
                        },
                        colors: ['#e2b13c']
                    };
                    yieldChart = new ApexCharts(document.querySelector("#earningsChart"), earningsOptions);
                    yieldChart.render();
                }

                // Yield Analytics Scaling Logic
                const yieldFilterButtons = document.querySelectorAll('.yield-filter-btn');
                yieldFilterButtons.forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const days = this.getAttribute('data-yield-days');

                        // UI Update
                        yieldFilterButtons.forEach(b => b.classList.remove('bg-white/[0.04]',
                            'text-white'));
                        yieldFilterButtons.forEach(b => b.classList.add('text-slate-500'));
                        this.classList.remove('text-slate-500');
                        this.classList.add('bg-white/[0.04]', 'text-white');

                        try {
                            const response = await fetch(
                                `{{ route('user.dashboard') }}?yield_days=${days}`, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                            const newData = await response.json();

                            yieldChart.updateOptions({
                                xaxis: {
                                    categories: newData.labels
                                }
                            });
                            yieldChart.updateSeries([{
                                name: "{{ __('ROI Payout') }}",
                                data: newData.amounts
                            }]);
                        } catch (error) {
                            console.error('Failed to fetch yield chart data:', error);
                        }
                    });
                });
            });

            // ── QR Code Support ──────────────────────────────────────────
            (function loadQrLib() {
                if (window.QRCode) return;
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js';
                s.onload = () => window._qrLibReady = true;
                document.head.appendChild(s);
            })();

            function toggleQr(panelId, canvasId, address) {
                const panel = document.getElementById(panelId);
                const canvas = document.getElementById(canvasId);
                if (!panel || !canvas) return;

                const isHidden = panel.classList.contains('hidden');

                // Close all other open panels first
                document.querySelectorAll('[id^="qr-panel-"]').forEach(p => p.classList.add('hidden'));

                if (!isHidden) return; // was already open → just close

                panel.classList.remove('hidden');

                // Generate only once
                if (canvas.dataset.generated) return;
                canvas.dataset.generated = '1';

                function doRender() {
                    new QRCode(canvas, {
                        text: address,
                        width: 160,
                        height: 160,
                        colorDark: '#000000',
                        colorLight: '#ffffff',
                        correctLevel: QRCode.CorrectLevel.M,
                    });
                }

                if (window.QRCode) {
                    doRender();
                } else {
                    // Wait for lib to load
                    const timer = setInterval(() => {
                        if (window.QRCode) { clearInterval(timer); doRender(); }
                    }, 50);
                }
            }
        </script>
    @endsection