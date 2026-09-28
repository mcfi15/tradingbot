@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12">

        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                    {{ __('WITHDRAWALS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    @if (request('status') == 'pending')
                        {{ __('Pending Withdrawals') }}
                    @elseif(request('status') == 'completed')
                        {{ __('Completed Withdrawals') }}
                    @elseif(request('status') == 'failed')
                        {{ __('Failed Withdrawals') }}
                    @else
                        {{ __('All Withdrawals') }}
                    @endif
                </h1>
                <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Track and manage user withdrawal requests and payout history') }}
                </p>
            </div>

            {{-- Quick Filter Tabs Ribbon --}}
            <div class="flex items-center gap-1.5 bg-white/[0.02] border border-white/[0.08] p-1.5 rounded-2xl overflow-x-auto max-w-full font-mono text-xs" id="status-tabs-container">
                @php
                    $currentStatus = request('status', 'all');
                    $statusTabs = [
                        'all' => ['label' => 'All Withdrawals', 'color' => 'cyan'],
                        'pending' => ['label' => 'Pending', 'color' => 'amber'],
                        'completed' => ['label' => 'Completed', 'color' => 'emerald'],
                        'failed' => ['label' => 'Failed', 'color' => 'rose'],
                    ];
                @endphp
                @foreach ($statusTabs as $stKey => $tab)
                    @php
                        $isActive = $currentStatus === $stKey;
                        $tabUrl = $stKey === 'all' ? route('admin.withdrawals.index') : route('admin.withdrawals.index', ['status' => $stKey]);
                    @endphp
                    <a href="{{ $tabUrl }}"
                        data-status="{{ $stKey }}"
                        class="status-tab-btn px-3.5 py-1.5 rounded-xl font-bold uppercase tracking-wider transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer
                        {{ $isActive 
                            ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-[0_0_15px_rgba(0,245,255,0.2)] active-tab' 
                            : 'text-slate-400 hover:text-white hover:bg-white/[0.04]' }}">
                        <span class="w-1.5 h-1.5 rounded-full tab-dot {{ $isActive ? 'bg-cyan-400 animate-pulse' : 'bg-slate-600' }}"></span>
                        <span>{{ __($tab['label']) }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- METRIC CARDS (Double-Bezel Architecture) --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Total Withdrawn --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-cyan-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Total Withdrawn') }}</span>
                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black font-mono text-white tracking-tight">
                            {{ showAmount($stats['total_withdrawn']) }}
                        </div>
                        <div class="text-[9px] font-mono text-cyan-400/80 mt-1 uppercase tracking-wider">
                            {{ __('Total money withdrawn across all accounts') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Settled Total --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-emerald-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-emerald-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Completed Withdrawals') }}</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black font-mono text-emerald-400 tracking-tight">
                            {{ showAmount($stats['total_completed']) }}
                        </div>
                        <div class="text-[9px] font-mono text-emerald-400/70 mt-1 uppercase tracking-wider">
                            {{ __('Successfully sent to user wallets') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Pending Queue --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-amber-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-amber-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Pending Withdrawals') }}</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black font-mono text-amber-400 tracking-tight flex items-baseline gap-2">
                            <span>{{ number_format($stats['pending_count']) }}</span>
                            <span class="text-xs font-normal text-amber-300/70">({{ showAmount($stats['pending_amount']) }})</span>
                        </div>
                        <div class="text-[9px] font-mono text-amber-400/70 mt-1 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                            <span>{{ __('Awaiting review and approval') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Failed / Rejected --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-rose-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-rose-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Failed / Cancelled') }}</span>
                        <div class="w-8 h-8 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black font-mono text-rose-400 tracking-tight flex items-baseline gap-2">
                            <span>{{ number_format($stats['failed_count']) }}</span>
                            <span class="text-xs font-normal text-rose-300/70">({{ showAmount($stats['failed_amount']) }})</span>
                        </div>
                        <div class="text-[9px] font-mono text-rose-400/70 mt-1 uppercase tracking-wider">
                            {{ __('Rejected or cancelled and refunded') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- ANALYTICS (Line Chart + Status Doughnut) --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Outflow Trend Chart (2/3 width) --}}
            <div class="lg:col-span-2 p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-7 h-full flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-base font-bold text-white font-mono uppercase tracking-wide flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f5ff]"></span>
                                    <span>{{ __('Withdrawal Activity Over Time') }}</span>
                                </h3>
                                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-mono mt-0.5">
                                    {{ __('Withdrawal volume') }} · {{ getSetting('currency', 'USD') }}
                                </p>
                            </div>

                            {{-- Timeframe Switcher --}}
                            <div class="flex items-center gap-1 bg-white/[0.03] border border-white/[0.08] p-1 rounded-xl font-mono text-[10px]">
                                @foreach (['7d' => '7D', '30d' => '30D', '60d' => '60D', '90d' => '90D', '1y' => '1Y', 'ytd' => 'YTD'] as $periodKey => $periodLabel)
                                    <button type="button" data-period="{{ $periodKey }}"
                                        class="graph-period-btn px-2.5 py-1 rounded-lg font-bold transition-all cursor-pointer
                                        {{ $periodKey === '7d' ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-500 hover:text-slate-300' }}">
                                        {{ $periodLabel }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Graph Legend --}}
                        <div id="graph-legend" class="flex items-center gap-4 mb-3 flex-wrap"></div>
                    </div>

                    {{-- Canvas Container --}}
                    <div class="relative h-64 w-full">
                        <canvas id="withdrawalTrendChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Status Distribution (1/3 width) --}}
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-7 h-full flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-white font-mono uppercase tracking-wide flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-400 shadow-[0_0_8px_#a855f7]"></span>
                            <span>{{ __('Withdrawal Breakdown') }}</span>
                        </h3>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-mono mt-0.5">
                            {{ __('Withdrawals by status') }}
                        </p>
                    </div>

                    <div class="relative flex-1 flex items-center justify-center p-2 min-h-[220px]">
                        <canvas id="statusDistributionChart" class="max-h-[200px]"></canvas>
                    </div>

                    <div class="pt-3 border-t border-white/[0.04] grid grid-cols-3 gap-2 text-center font-mono">
                        <div>
                            <span class="text-[10px] text-slate-500 block uppercase">{{ __('Pending') }}</span>
                            <span class="text-xs font-bold text-amber-400">{{ $stats['pending_count'] }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500 block uppercase">{{ __('Completed') }}</span>
                            <span class="text-xs font-bold text-emerald-400">{{ \App\Models\Withdrawal::approved()->count() }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500 block uppercase">{{ __('Failed') }}</span>
                            <span class="text-xs font-bold text-rose-400">{{ $stats['failed_count'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================================================================================== --}}
        {{-- WITHDRAWALS TABLE (Double-Bezel Chassis) --}}
        {{-- ==================================================================================== --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden" id="withdrawals-wrapper">
            
            {{-- Loading Spinner Overlay --}}
            <div id="loading-spinner" class="hidden absolute inset-0 bg-[#090c14]/85 backdrop-blur-md z-50 flex flex-col items-center justify-center transition-all duration-300">
                <div class="w-10 h-10 border-2 border-cyan-400 border-t-transparent rounded-full animate-spin"></div>
                <p class="mt-3 text-cyan-300 font-mono text-xs tracking-widest uppercase">{{ __('Loading withdrawals...') }}</p>
            </div>

            <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] overflow-hidden">
                
                {{-- Table Control Bar --}}
                <div class="p-6 border-b border-white/[0.06] flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white/[0.01]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white font-mono uppercase tracking-wide flex items-center gap-2">
                                <span>{{ __('Withdrawals List') }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 text-[10px]">
                                    {{ $withdrawals->total() }}
                                </span>
                            </h3>
                            <p class="text-xs text-slate-400">{{ __('All withdrawal requests and their current status.') }}</p>
                        </div>
                    </div>

                    {{-- Filters & Export Tools --}}
                    <form id="filter-form" action="{{ route('admin.withdrawals.index') }}" method="GET" class="flex flex-wrap gap-2.5 items-center font-mono">
                        @if (request('user_id'))
                            <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                        @endif

                        {{-- Search Input --}}
                        <div class="relative min-w-[220px]">
                            <input type="text" name="search" placeholder="{{ __('Search user, transaction ID...') }}"
                                value="{{ request('search') }}"
                                class="w-full bg-white/[0.03] border border-white/[0.1] focus:border-cyan-400/60 rounded-xl px-4 py-2 text-white text-xs placeholder-slate-500 focus:outline-none transition-all">
                        </div>

                        {{-- Custom Status Dropdown --}}
                        <div class="relative custom-dropdown" id="status-filter-dropdown">
                            <input type="hidden" name="status" id="status-input" value="{{ request('status', 'all') }}">
                            <button type="button"
                                class="dropdown-btn flex items-center justify-between gap-3 px-3.5 py-2 rounded-xl bg-white/[0.03] border border-white/[0.1] text-white text-xs hover:border-cyan-400/50 transition-all cursor-pointer min-w-[130px]">
                                <span class="selected-label font-bold text-cyan-300">
                                    @if (request('status') == 'pending')
                                        {{ __('Pending') }}
                                    @elseif(request('status') == 'completed')
                                        {{ __('Completed') }}
                                    @elseif(request('status') == 'failed')
                                        {{ __('Failed') }}
                                    @else
                                        {{ __('All Status') }}
                                    @endif
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-500 dropdown-icon transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="dropdown-menu absolute right-0 z-[120] mt-2 w-44 bg-[#0b0e17] border border-white/[0.1] rounded-2xl shadow-2xl py-2 hidden animate-in fade-in slide-in-from-top-2 duration-200">
                                <div class="dropdown-option px-4 py-2.5 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="all">{{ __('All Status') }}</div>
                                <div class="dropdown-option px-4 py-2.5 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="pending">{{ __('Pending') }}</div>
                                <div class="dropdown-option px-4 py-2.5 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="completed">{{ __('Completed') }}</div>
                                <div class="dropdown-option px-4 py-2.5 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="failed">{{ __('Failed') }}</div>
                            </div>
                        </div>

                        {{-- Export Dropdown --}}
                        <div class="relative custom-dropdown" id="export-dropdown">
                            <button type="button"
                                class="dropdown-btn flex items-center justify-between gap-2.5 px-3.5 py-2 rounded-xl bg-white/[0.03] border border-white/[0.1] text-white text-xs hover:border-cyan-400/50 transition-all cursor-pointer">
                                <span>{{ __('Export') }}</span>
                                <svg class="w-3.5 h-3.5 text-slate-500 dropdown-icon transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="dropdown-menu absolute right-0 z-[120] mt-2 w-36 bg-[#0b0e17] border border-white/[0.1] rounded-2xl shadow-2xl py-2 hidden animate-in fade-in slide-in-from-top-2 duration-200">
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer flex items-center gap-2" data-export="csv">
                                    <span>CSV</span>
                                </div>
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer flex items-center gap-2" data-export="sql">
                                    <span>SQL</span>
                                </div>
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer flex items-center gap-2" data-export="pdf">
                                    <span>PDF</span>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                            class="p-2 bg-cyan-400 hover:bg-cyan-300 text-[#050507] rounded-xl transition-all cursor-pointer shadow-[0_0_15px_rgba(0,245,255,0.25)]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[1000px]">
                        <thead>
                            <tr class="border-b border-white/[0.06] bg-white/[0.01]">
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('User') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Method') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Amount') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Transaction ID') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Status') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono text-right">{{ __('Date') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.03]">
                            @forelse ($withdrawals as $withdrawal)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    {{-- User --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($withdrawal->user?->photo)
                                                <div class="w-9 h-9 rounded-2xl border border-white/10 shadow-sm overflow-hidden shrink-0">
                                                    <img src="{{ asset('storage/profile/' . $withdrawal->user->photo) }}"
                                                        alt="{{ $withdrawal->user->username }}" class="w-full h-full object-cover">
                                                </div>
                                            @else
                                                <div class="w-9 h-9 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-xs font-bold font-mono text-cyan-400 shrink-0">
                                                    {{ strtoupper(substr($withdrawal->user->username ?? 'NA', 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                @if ($withdrawal->user)
                                                    <a href="{{ route('admin.users.detail', $withdrawal->user_id) }}"
                                                        class="text-xs font-bold font-mono text-white hover:text-cyan-400 transition-colors block">
                                                        {{ $withdrawal->user->username }}
                                                    </a>
                                                    <span class="text-[10px] text-slate-400 font-mono block mt-0.5">{{ $withdrawal->user->email }}</span>
                                                @else
                                                    <span class="text-xs font-mono text-slate-400">{{ __('Deleted Account') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Blockchain Network --}}
                                    <td class="px-6 py-4">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-300 text-xs font-mono font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                                            <span>{{ $withdrawal->gatewayName() }}</span>
                                        </div>
                                    </td>

                                    {{-- Amount & Net Amount Payable --}}
                                    <td class="px-6 py-4 font-mono">
                                        <div class="text-xs font-black text-white">
                                            {{ showAmount($withdrawal->amount) }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                            <span>{{ __('Net') }}:</span>
                                            <span class="text-cyan-300 font-bold">{{ showAmount($withdrawal->amount_payable) }}</span>
                                            @if ($withdrawal->fee_amount > 0)
                                                <span class="text-rose-400/80">(-{{ showAmount($withdrawal->fee_amount) }})</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Trx Ref --}}
                                    <td class="px-6 py-4">
                                        <div class="inline-flex items-center gap-2 bg-black/40 px-2.5 py-1 rounded-xl border border-white/[0.04]">
                                            <code class="font-mono text-xs text-slate-300 select-all">{{ $withdrawal->transaction_reference }}</code>
                                            <button type="button" onclick="navigator.clipboard.writeText('{{ $withdrawal->transaction_reference }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Copied') }}', showConfirmButton:false, timer:1500});"
                                                class="p-1 text-slate-500 hover:text-white transition-colors cursor-pointer" title="{{ __('Copy Reference') }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4">
                                        @if ($withdrawal->status == 'pending')
                                            <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-mono font-bold uppercase tracking-wider">
                                                ● {{ __('Pending') }}
                                            </span>
                                        @elseif($withdrawal->status == 'completed')
                                            <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-mono font-bold uppercase tracking-wider">
                                                ● {{ __('Completed') }}
                                            </span>
                                        @elseif($withdrawal->status == 'failed')
                                            <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-mono font-bold uppercase tracking-wider">
                                                ● {{ __('Failed') }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full bg-slate-500/10 text-slate-400 border border-slate-500/20 text-[10px] font-mono font-bold uppercase tracking-wider">
                                                {{ $withdrawal->status }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Date --}}
                                    <td class="px-6 py-4 text-right font-mono">
                                        <span class="text-xs text-slate-200 block">
                                            {{ $withdrawal->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="text-[10px] text-slate-500 block mt-0.5">
                                            {{ $withdrawal->created_at->format('H:i:s') }} UTC
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            {{-- View Details --}}
                                            <a href="{{ route('admin.withdrawals.view', $withdrawal->id) }}"
                                                class="p-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/20 transition-all cursor-pointer"
                                                title="{{ __('View Withdrawal Details') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>

                                            {{-- Edit Status --}}
                                            <button type="button"
                                                class="edit-withdrawal-btn p-2 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 border border-purple-500/20 transition-all cursor-pointer"
                                                data-id="{{ $withdrawal->id }}" data-status="{{ $withdrawal->status }}"
                                                title="{{ __('Update Status') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>

                                            {{-- Delete --}}
                                            <button type="button"
                                                class="delete-withdrawal-btn p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-all cursor-pointer"
                                                data-id="{{ $withdrawal->id }}"
                                                title="{{ __('Delete Withdrawal') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center text-slate-500 font-mono text-xs uppercase tracking-widest">
                                        {{ __('No withdrawal records found matching your criteria.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                @if ($withdrawals->hasPages())
                    <div class="px-6 py-4 border-t border-white/[0.06] ajax-pagination bg-white/[0.01]">
                        {{ $withdrawals->links('templates.york.blades.partials.pagination') }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ==================================================================================== --}}
    {{-- MODALS (Double-Bezel Glass Architecture) --}}
    {{-- ==================================================================================== --}}

    {{-- 1. Edit / Process Status Modal --}}
    <div id="edit-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-[#05070d]/80 backdrop-blur-xl transition-opacity modal-close"></div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.9)] relative w-full max-w-md transform transition-all text-left">
                <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8 relative">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Update Withdrawal Status') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('Change the status of this withdrawal.') }}</p>
                        </div>
                        <button type="button" class="w-8 h-8 rounded-full bg-white/[0.04] text-slate-400 hover:text-white transition-colors modal-close flex items-center justify-center cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <form id="edit-status-form" class="space-y-6">
                        @csrf
                        <input type="hidden" name="withdrawal_id" id="edit-withdrawal-id">

                        <div>
                            <label class="block text-xs font-mono font-bold text-slate-400 uppercase tracking-widest mb-2">
                                {{ __('New Status') }}
                            </label>
                            <div class="relative custom-dropdown" id="edit-status-dropdown">
                                <button type="button"
                                    class="dropdown-btn w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl bg-white/[0.03] border border-white/[0.1] text-white text-xs font-mono focus:border-cyan-400 transition-all cursor-pointer">
                                    <span class="selected-label font-bold text-cyan-300">{{ __('Select Status') }}</span>
                                    <svg class="w-4 h-4 text-slate-500 dropdown-icon transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <input type="hidden" name="status" id="edit-status-value">
                                <div class="dropdown-menu absolute z-[120] mt-2 w-full bg-[#0b0e17] border border-white/[0.1] rounded-2xl shadow-2xl py-2 hidden animate-in fade-in slide-in-from-top-2 duration-200 font-mono text-xs">
                                    <div class="dropdown-option px-4 py-3 text-emerald-400 hover:bg-emerald-500/10 transition-colors cursor-pointer font-bold flex items-center gap-2" data-value="completed">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        <span>{{ __('Completed (Approve Payout)') }}</span>
                                    </div>
                                    <div class="dropdown-option px-4 py-3 text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer font-bold flex items-center gap-2" data-value="failed">
                                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                        <span>{{ __('Failed (Reject & Refund)') }}</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-500 font-mono mt-2 leading-relaxed">
                                {{ __('Selecting Completed will execute automated blockchain dispatch from the network master wallet.') }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-white/[0.06] flex gap-3 font-mono">
                            <button type="button"
                                class="flex-1 px-4 py-2.5 rounded-full border border-white/[0.1] text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-white/[0.05] transition-all modal-close cursor-pointer">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-full bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(0,245,255,0.3)] transition-all cursor-pointer">
                                {{ __('Update Status') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Delete Confirmation Modal --}}
    <div id="delete-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-[#05070d]/80 backdrop-blur-xl transition-opacity modal-close"></div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-rose-500/20 backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.9)] relative w-full max-w-md transform transition-all text-center">
                <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8">
                    <div class="w-14 h-14 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>

                    <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide mb-2">{{ __('Delete Withdrawal') }}</h3>
                    <p class="text-xs text-slate-400 mb-6 leading-relaxed">
                        {{ __('Are you sure you want to delete this withdrawal record? This action cannot be undone.') }}
                    </p>

                    <div class="flex gap-3 font-mono">
                        <input type="hidden" id="delete-withdrawal-id">
                        <button type="button"
                            class="flex-1 px-4 py-2.5 rounded-full border border-white/[0.1] text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-white/[0.05] transition-all modal-close cursor-pointer">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" id="confirm-delete"
                            class="flex-1 px-4 py-2.5 rounded-full bg-rose-500 hover:bg-rose-400 text-white font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(244,63,94,0.3)] transition-all cursor-pointer">
                            {{ __('Confirm Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Export Modal --}}
    <div id="export-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-[#05070d]/80 backdrop-blur-xl transition-opacity modal-close cursor-pointer"></div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.9)] relative w-full max-w-md transform transition-all text-left">
                <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Export Withdrawals') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('Choose columns to include in your export.') }}</p>
                        </div>
                        <button type="button" class="w-8 h-8 rounded-full bg-white/[0.04] text-slate-400 hover:text-white transition-colors modal-close flex items-center justify-center cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <p class="text-xs font-mono font-bold text-slate-400 uppercase tracking-widest mb-3">
                                {{ __('Columns to Include') }}
                            </p>
                            <div class="grid grid-cols-2 gap-2.5" id="export-columns-container">
                                @php
                                    $columns = [
                                        'username' => ['label' => 'User', 'default' => true],
                                        'withdrawal_method_name' => ['label' => 'Payment Method', 'default' => true],
                                        'amount' => ['label' => 'Amount', 'default' => true],
                                        'fee_amount' => ['label' => 'Fee', 'default' => true],
                                        'amount_payable' => ['label' => 'Net Amount', 'default' => true],
                                        'converted_amount' => ['label' => 'Converted Amount', 'default' => false],
                                        'exchange_rate' => ['label' => 'Exchange Rate', 'default' => false],
                                        'transaction_reference' => ['label' => 'Transaction ID', 'default' => true],
                                        'status' => ['label' => 'Status', 'default' => true],
                                        'created_at' => ['label' => 'Date', 'default' => true],
                                    ];
                                @endphp
                                @foreach ($columns as $key => $col)
                                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-white/[0.06] bg-white/[0.02] hover:border-cyan-400/40 transition-all cursor-pointer group">
                                        <input type="checkbox" name="export_cols[]" value="{{ $key }}"
                                            {{ $col['default'] ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-white/20 bg-white/5 text-cyan-400 focus:ring-cyan-400 focus:ring-offset-0 cursor-pointer accent-cyan-400">
                                        <span class="text-xs font-mono text-slate-300 group-hover:text-white transition-colors">{{ __($col['label']) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-4 border-t border-white/[0.06] flex gap-3 font-mono">
                            <input type="hidden" id="pending-export-type" value="">
                            <button type="button"
                                class="flex-1 px-4 py-2.5 rounded-full border border-white/[0.1] text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-white/[0.05] transition-all modal-close cursor-pointer">
                                {{ __('Cancel') }}
                            </button>
                            <button type="button" id="confirm-export"
                                class="flex-1 px-4 py-2.5 rounded-full bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(0,245,255,0.3)] transition-all cursor-pointer">
                                {{ __('Download File') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // ── Data from PHP ────────────────────────────────────────────────────────
            const GRAPH_DATA = @json($graph_data);
            const STATUS_CHART_DATA = @json($status_chart_data);

            // ── Shared Chart.js defaults ─────────────────────────────────────────────
            const CURRENCY = "{{ getSetting('currency', 'USD') }}";
            Chart.defaults.color = 'rgba(148,163,184,0.7)';
            Chart.defaults.borderColor = 'rgba(255,255,255,0.05)';
            Chart.defaults.font.family = "'JetBrains Mono', monospace";
            Chart.defaults.font.size = 10;

            const tooltipOptions = {
                backgroundColor: 'rgba(9,12,20,0.95)',
                borderColor: 'rgba(255,255,255,0.1)',
                borderWidth: 1,
                padding: 12,
                titleFont: {
                    size: 11,
                    weight: 'bold',
                    family: "'JetBrains Mono', monospace"
                },
                bodyFont: {
                    size: 10,
                    family: "'JetBrains Mono', monospace"
                },
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null && context.parsed.y !== undefined) {
                            label += new Intl.NumberFormat('en-US', {
                                style: 'currency',
                                currency: CURRENCY
                            }).format(context.parsed.y);
                        } else if (context.parsed !== null && context.chart.config.type === 'doughnut') {
                            label += new Intl.NumberFormat('en-US', {
                                style: 'currency',
                                currency: CURRENCY
                            }).format(context.parsed);
                        }
                        return label;
                    }
                }
            };

            // ── Withdrawal Trend Chart ───────────────────────────────────────────────
            const trendCtx = document.getElementById('withdrawalTrendChart').getContext('2d');
            const trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: tooltipOptions,
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(255,255,255,0.03)'
                            },
                            ticks: {
                                maxTicksLimit: 8,
                                maxRotation: 0
                            },
                        },
                        y: {
                            grid: {
                                color: 'rgba(255,255,255,0.04)'
                            },
                            ticks: {
                                maxTicksLimit: 5
                            },
                            beginAtZero: true,
                        },
                    },
                },
            });

            // Status Colors Mapping
            const statusConfig = {
                'pending': {
                    color: '#f59e0b',
                    bg: 'rgba(245, 158, 11, 0.1)',
                    border: 'rgba(245, 158, 11, 0.3)',
                    label: '{{ __('Pending') }}'
                },
                'completed': {
                    color: '#10b981',
                    bg: 'rgba(16, 185, 129, 0.1)',
                    border: 'rgba(16, 185, 129, 0.3)',
                    label: '{{ __('Completed') }}'
                },
                'failed': {
                    color: '#f43f5e',
                    bg: 'rgba(244, 63, 94, 0.1)',
                    border: 'rgba(244, 63, 94, 0.3)',
                    label: '{{ __('Failed') }}'
                }
            };

            function updateTrendChart(period) {
                if (!GRAPH_DATA || !GRAPH_DATA[period]) return;

                const periodData = GRAPH_DATA[period];

                let allLabels = new Set();
                Object.values(periodData).forEach(statusData => {
                    if (statusData && statusData.labels) {
                        statusData.labels.forEach(l => allLabels.add(l));
                    }
                });

                let sortedLabels = Array.from(allLabels).sort();

                if (sortedLabels.length === 0) {
                    sortedLabels = [new Date().toISOString().split('T')[0]];
                }

                const datasets = Object.keys(statusConfig).map(status => {
                    const cfg = statusConfig[status];
                    const dataObj = periodData[status];

                    const mappedData = sortedLabels.map(label => {
                        const idx = dataObj ? dataObj.labels.indexOf(label) : -1;
                        return idx !== -1 ? dataObj.data[idx] : 0;
                    });

                    return {
                        label: cfg.label,
                        data: mappedData,
                        borderColor: cfg.color,
                        backgroundColor: cfg.bg,
                        borderWidth: 2,
                        pointBackgroundColor: '#090c14',
                        pointBorderColor: cfg.color,
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        fill: true,
                        tension: 0.4
                    };
                });

                const displayLabels = sortedLabels.map(dateStr => {
                    const d = new Date(dateStr);
                    return d.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric'
                    });
                });

                trendChart.data.labels = displayLabels;
                trendChart.data.datasets = datasets;
                trendChart.update();

                updateGraphLegend(datasets);
            }

            function updateGraphLegend(datasets) {
                const legendContainer = document.getElementById('graph-legend');
                if (!legendContainer) return;
                legendContainer.innerHTML = '';

                datasets.forEach(ds => {
                    const item = document.createElement('div');
                    item.className = 'flex items-center gap-1.5 text-[10px] font-bold tracking-wider uppercase text-slate-300';
                    item.innerHTML = `
                        <span class="w-2 h-2 rounded-full" style="background-color: ${ds.borderColor}; box-shadow: 0 0 6px ${ds.borderColor}"></span>
                        ${ds.label}
                    `;
                    legendContainer.appendChild(item);
                });
            }

            updateTrendChart('7d');

            $('.graph-period-btn').on('click', function() {
                $('.graph-period-btn').removeClass('bg-cyan-500/20 text-cyan-300 border border-cyan-500/30').addClass('text-slate-500 hover:text-slate-300');
                $(this).removeClass('text-slate-500 hover:text-slate-300').addClass('bg-cyan-500/20 text-cyan-300 border border-cyan-500/30');

                const period = $(this).data('period');
                updateTrendChart(period);
            });

            // ── Status Distribution Chart ──────────────────────────────────────────────
            const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');

            const doughnutLabels = [];
            const doughnutData = [];
            const doughnutColors = [];

            Object.keys(statusConfig).forEach(key => {
                const foundItem = (STATUS_CHART_DATA || []).find(item => item.status.toLowerCase() === key);
                doughnutLabels.push(statusConfig[key].label);
                doughnutData.push(foundItem ? foundItem.amount : 0);
                doughnutColors.push(statusConfig[key].color);
            });

            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: doughnutLabels,
                    datasets: [{
                        data: doughnutData,
                        backgroundColor: doughnutColors,
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: tooltipOptions
                    }
                }
            });

            // ── Custom Dropdowns ──────────────────────────────────────────────────
            $(document).on('click', '.dropdown-btn', function(e) {
                e.stopPropagation();
                $('.dropdown-menu').not($(this).siblings('.dropdown-menu')).addClass('hidden');
                $('.dropdown-icon').not($(this).find('.dropdown-icon')).removeClass('rotate-180');

                const menu = $(this).siblings('.dropdown-menu');
                const icon = $(this).find('.dropdown-icon');

                menu.toggleClass('hidden');
                icon.toggleClass('rotate-180');
            });

            $(document).on('click', function() {
                $('.dropdown-menu').addClass('hidden');
                $('.dropdown-icon').removeClass('rotate-180');
            });

            // Status Filter Dropdown Select
            $(document).on('click', '#status-filter-dropdown .dropdown-option', function() {
                const val = $(this).data('value');
                const label = $(this).text();

                $('#status-input').val(val);
                $('#status-filter-dropdown .selected-label').text(label);

                // Sync status ribbon tabs
                $('.status-tab-btn').removeClass('bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-[0_0_15px_rgba(0,245,255,0.2)] active-tab')
                                    .addClass('text-slate-400 hover:text-white hover:bg-white/[0.04]');
                $('.status-tab-btn .tab-dot').removeClass('bg-cyan-400 animate-pulse').addClass('bg-slate-600');
                
                const $matchingTab = $(`.status-tab-btn[data-status="${val}"]`);
                if ($matchingTab.length) {
                    $matchingTab.addClass('bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-[0_0_15px_rgba(0,245,255,0.2)] active-tab')
                                .removeClass('text-slate-400 hover:text-white hover:bg-white/[0.04]');
                    $matchingTab.find('.tab-dot').removeClass('bg-slate-600').addClass('bg-cyan-400 animate-pulse');
                }

                $('#filter-form').submit();
            });

            // Fast Filter Ribbon Tab Clicks
            $(document).on('click', '.status-tab-btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const status = $(this).data('status');

                // Update tab styling
                $('.status-tab-btn').removeClass('bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-[0_0_15px_rgba(0,245,255,0.2)] active-tab')
                                    .addClass('text-slate-400 hover:text-white hover:bg-white/[0.04]');
                $('.status-tab-btn .tab-dot').removeClass('bg-cyan-400 animate-pulse').addClass('bg-slate-600');
                
                $(this).addClass('bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-[0_0_15px_rgba(0,245,255,0.2)] active-tab')
                       .removeClass('text-slate-400 hover:text-white hover:bg-white/[0.04]');
                $(this).find('.tab-dot').removeClass('bg-slate-600').addClass('bg-cyan-400 animate-pulse');

                $('#status-input').val(status);

                // Update dropdown label
                const statusLabels = {
                    'all': '{{ __('All Status') }}',
                    'pending': '{{ __('Pending') }}',
                    'completed': '{{ __('Completed') }}',
                    'failed': '{{ __('Failed') }}'
                };
                $('#status-filter-dropdown .selected-label').text(statusLabels[status] || status);

                loadTable(url);
            });

            // Export Actions
            $(document).on('click', '#export-dropdown .dropdown-option', function() {
                const exportType = $(this).data('export');
                $('#pending-export-type').val(exportType);
                $('.dropdown-menu').addClass('hidden');
                $('.dropdown-icon').removeClass('rotate-180');
                openModal('export-modal');
            });

            $(document).on('click', '#confirm-export', function() {
                const exportType = $('#pending-export-type').val();

                const selectedCols = [];
                $('input[name="export_cols[]"]:checked').each(function() {
                    selectedCols.push($(this).val());
                });

                if (selectedCols.length === 0) {
                    Swal.fire({toast:true, position:'top-end', icon:'error', title:'{{ __('Select at least one column') }}', showConfirmButton:false, timer:2000});
                    return;
                }

                const baseUrl = $('#filter-form').attr('action') || window.location.pathname;
                let params = new URLSearchParams(window.location.search);
                
                const searchVal = $('input[name="search"]').val();
                if (searchVal) params.set('search', searchVal);
                
                const statusVal = $('#status-input').val();
                if (statusVal && statusVal !== 'all') params.set('status', statusVal);

                params.set('export', exportType);
                params.set('columns', selectedCols.join(','));

                closeModal('export-modal');
                window.location.href = baseUrl + '?' + params.toString();
            });

            // ── Ajax Pagination ───────────────────────────────────────────────────
            $(document).on('click', '.ajax-pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                loadTable(url);
            });

            // AJAX Filtering
            $(document).on('submit', '#filter-form', function(e) {
                e.preventDefault();
                var url = $(this).attr('action') + '?' + $(this).serialize();
                loadTable(url);
            });

            function loadTable(url) {
                $('#loading-spinner').removeClass('hidden').addClass('flex');
                $('#withdrawals-wrapper .overflow-x-auto').css('opacity', '0.3');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        const newContent = $(data).find('#withdrawals-wrapper').html();
                        if (newContent) {
                            $('#withdrawals-wrapper').html(newContent);
                        }
                        window.history.pushState({}, '', url);
                    },
                    error: function() {
                        Swal.fire({toast:true, position:'top-end', icon:'error', title:'{{ __('Error loading data') }}', showConfirmButton:false, timer:2000});
                    },
                    complete: function() {
                        $('#loading-spinner').addClass('hidden').removeClass('flex');
                        $('#withdrawals-wrapper .overflow-x-auto').css('opacity', '1');
                    }
                });
            }

            // ── Modals Handling ───────────────────────────────────────────────────
            function openModal(id) {
                const $modal = $(`#${id}`);
                $modal.removeClass('hidden');
                setTimeout(() => {
                    $modal.find('.transform').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
                }, 10);
            }

            function closeModal(id) {
                const $modal = $(`#${id}`);
                $modal.find('.transform').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
                setTimeout(() => {
                    $modal.addClass('hidden');
                }, 200);
            }

            $('.modal-close').on('click', function() {
                const modalId = $(this).closest('.fixed.inset-0.z-\\[100\\]').attr('id');
                closeModal(modalId);
            });

            // ── Edit Status ───────────────────────────────────────────────────────
            $(document).on('click', '.edit-withdrawal-btn', function() {
                const id = $(this).data('id');
                const status = $(this).data('status');

                $('#edit-withdrawal-id').val(id);
                $('#edit-status-value').val(status);

                let statusLabel = status.charAt(0).toUpperCase() + status.slice(1);
                $('#edit-status-dropdown .selected-label').text(statusLabel);

                openModal('edit-modal');
            });

            $('#edit-status-dropdown .dropdown-option').on('click', function() {
                const val = $(this).data('value');
                const label = $(this).text().trim();

                $('#edit-status-value').val(val);
                $('#edit-status-dropdown .selected-label').text(label);
            });

            $('#edit-status-form').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit-withdrawal-id').val();
                const status = $('#edit-status-value').val();

                if (!status) {
                    Swal.fire({toast:true, position:'top-end', icon:'error', title:'{{ __('Please select a target status') }}', showConfirmButton:false, timer:2000});
                    return;
                }

                const $submitBtn = $(this).find('button[type="submit"]');
                const originalText = $submitBtn.text();
                $submitBtn.text('{{ __('Processing...') }}').prop('disabled', true);

                $.ajax({
                    url: `{{ url('admin/withdrawals/edit') }}/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(res) {
                        if (res.success || res.status === 'success') {
                            closeModal('edit-modal');
                            Swal.fire({toast:true, position:'top-end', icon:'success', title: res.message || 'Withdrawal processed successfully', showConfirmButton:false, timer:2500});
                            $submitBtn.text(originalText).prop('disabled', false);
                            loadTable(window.location.href);
                        } else {
                            Swal.fire({toast:true, position:'top-end', icon:'error', title: res.message || 'An error occurred', showConfirmButton:false, timer:2500});
                            $submitBtn.text(originalText).prop('disabled', false);
                        }
                    },
                    error: function(err) {
                        let error = err.responseJSON?.message || 'An error occurred';
                        Swal.fire({toast:true, position:'top-end', icon:'error', title: error, showConfirmButton:false, timer:3000});
                        $submitBtn.text(originalText).prop('disabled', false);
                    }
                });
            });

            // ── Delete Withdrawal ─────────────────────────────────────────────────
            $(document).on('click', '.delete-withdrawal-btn', function() {
                const id = $(this).data('id');
                $('#delete-withdrawal-id').val(id);
                openModal('delete-modal');
            });

            $('#confirm-delete').on('click', function() {
                const id = $('#delete-withdrawal-id').val();
                const $btn = $(this);
                const originalText = $btn.text();

                $btn.text('{{ __('Deleting...') }}').prop('disabled', true);

                $.ajax({
                    url: `{{ url('admin/withdrawals/delete') }}/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            closeModal('delete-modal');
                            Swal.fire({toast:true, position:'top-end', icon:'success', title: res.message || 'Withdrawal deleted', showConfirmButton:false, timer:2000});
                            $btn.text(originalText).prop('disabled', false);
                            loadTable(window.location.href);
                        } else {
                            Swal.fire({toast:true, position:'top-end', icon:'error', title: res.message || 'An error occurred', showConfirmButton:false, timer:2000});
                            $btn.text(originalText).prop('disabled', false);
                        }
                    },
                    error: function(err) {
                        let error = err.responseJSON?.message || 'An error occurred';
                        Swal.fire({toast:true, position:'top-end', icon:'error', title: error, showConfirmButton:false, timer:2000});
                        $btn.text(originalText).prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
