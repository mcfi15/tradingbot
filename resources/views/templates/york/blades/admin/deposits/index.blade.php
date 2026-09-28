@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12">

        {{-- Top Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                    {{ __('DEPOSITS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    @if (request('status') == 'pending')
                        {{ __('Pending Deposits') }}
                    @elseif(request('status') == 'completed')
                        {{ __('Completed Deposits') }}
                    @elseif(request('status') == 'failed')
                        {{ __('Failed Deposits') }}
                    @elseif(request('status') == 'partial_payment')
                        {{ __('Partial Deposits') }}
                    @else
                        {{ __('All Deposits') }}
                    @endif
                </h1>
                <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Track and manage user deposit requests and payment history.') }}
                </p>
            </div>

            {{-- Quick Filter Tabs Ribbon --}}
            <div class="flex items-center gap-1.5 bg-white/[0.02] border border-white/[0.08] p-1.5 rounded-2xl overflow-x-auto max-w-full font-mono text-xs" id="status-tabs-container">
                @php
                    $currentStatus = request('status', 'all');
                    $statusTabs = [
                        'all' => ['label' => 'All Deposits', 'color' => 'cyan'],
                        'pending' => ['label' => 'Pending', 'color' => 'amber'],
                        'completed' => ['label' => 'Completed', 'color' => 'emerald'],
                        'partial_payment' => ['label' => 'Partial', 'color' => 'purple'],
                        'failed' => ['label' => 'Failed', 'color' => 'rose'],
                    ];
                @endphp
                @foreach ($statusTabs as $stKey => $tab)
                    @php
                        $isActive = $currentStatus === $stKey;
                        $tabUrl = $stKey === 'all' ? route('admin.deposits.index') : route('admin.deposits.index', ['status' => $stKey]);
                    @endphp
                    <a href="{{ $tabUrl }}"
                        data-status="{{ $stKey }}"
                        class="status-tab-btn px-3 py-1.5 rounded-xl font-bold uppercase tracking-wider transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer
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
        {{-- TOP STATISTIC METRICS --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Card 1: Total Deposited --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-cyan-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Total Deposited') }}</span>
                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black font-mono text-white tracking-tight">
                            {{ showAmount($stats['total_deposited']) }}
                        </div>
                        <div class="text-[9px] font-mono text-cyan-400/80 mt-1 uppercase tracking-wider">
                            {{ __('Total money deposited across all accounts') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Completed Settlements --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-emerald-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-emerald-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Completed Deposits') }}</span>
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
                            {{ __('Successfully added to user balances') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Pending Queue --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-amber-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-amber-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Pending Deposits') }}</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black font-mono text-amber-400 tracking-tight">
                            {{ number_format($stats['pending_count']) }} <span class="text-xs font-normal text-slate-400">txs</span>
                        </div>
                        <div class="text-[9px] font-mono text-amber-400/80 mt-1 uppercase tracking-wider">
                            {{ showAmount($stats['pending_amount']) }} {{ __('Awaiting confirmation') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Failed / Terminated --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-rose-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-rose-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Failed Deposits') }}</span>
                        <div class="w-8 h-8 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black font-mono text-rose-400 tracking-tight">
                            {{ number_format($stats['failed_count']) }} <span class="text-xs font-normal text-slate-400">txs</span>
                        </div>
                        <div class="text-[9px] font-mono text-rose-400/80 mt-1 uppercase tracking-wider">
                            {{ showAmount($stats['failed_amount']) }} {{ __('Cancelled or rejected deposits') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================================================================================== --}}
        {{-- ANALYTICS (Trend Line Graph + Status Doughnut) --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ── Left: Inbound Trend Graph (8 cols) ── --}}
            <div class="lg:col-span-8 p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-6 pb-4 border-b border-white/[0.06]">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f5ff]"></span>
                                <h3 id="graph-title" class="text-base font-bold text-white font-mono tracking-wide">
                                    {{ __('Deposit Activity Over Time') }}
                                </h3>
                            </div>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest font-mono">
                                {{ __('Deposit volume') }} · {{ getSetting('currency', 'USD') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap font-mono">
                            <div class="flex items-center gap-1 bg-white/[0.03] border border-white/[0.08] rounded-xl p-1">
                                @foreach (['7d' => '7D', '30d' => '30D', '60d' => '60D', '90d' => '90D', '1y' => '1Y', 'ytd' => 'YTD'] as $key => $label)
                                    <button data-period="{{ $key }}"
                                        class="graph-period-btn cursor-pointer px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase transition-all
                                            {{ $key === '7d' ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-500 hover:text-slate-300' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Legend --}}
                    <div id="graph-legend" class="flex items-center gap-4 mb-4 flex-wrap font-mono text-xs"></div>

                    {{-- Chart Canvas --}}
                    <div class="relative h-64 sm:h-72 w-full">
                        <canvas id="depositTrendChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- ── Right: Status Distribution Doughnut (4 cols) ── --}}
            <div class="lg:col-span-4 p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-purple-400 shadow-[0_0_8px_#a855f7]"></span>
                            <h3 class="text-base font-bold text-white font-mono tracking-wide">{{ __('Deposit Breakdown') }}</h3>
                        </div>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest font-mono mb-6">
                            {{ __('Deposits by status') }}
                        </p>

                        <div class="relative flex items-center justify-center my-4" style="height: 190px;">
                            <canvas id="statusDistributionChart"></canvas>
                        </div>
                    </div>

                    {{-- Mini Status Indicators --}}
                    <div class="pt-4 border-t border-white/[0.06] space-y-2 font-mono">
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white/[0.02] border border-white/[0.04]">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_6px_#10b981]"></span>
                                <span class="text-xs text-slate-300">{{ __('Completed') }}</span>
                            </div>
                            <span class="text-xs font-bold text-emerald-400">{{ showAmount($stats['total_completed']) }}</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white/[0.02] border border-white/[0.04]">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-400 shadow-[0_0_6px_#f59e0b]"></span>
                                <span class="text-xs text-slate-300">{{ __('Pending') }}</span>
                            </div>
                            <span class="text-xs font-bold text-amber-400">{{ showAmount($stats['pending_amount']) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================================================================================== --}}
        {{-- DEPOSITS LIST TABLE --}}
        {{-- ==================================================================================== --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden" id="deposits-wrapper">

            {{-- Loading Spinner Overlay --}}
            <div id="loading-spinner"
                class="hidden absolute inset-0 bg-[#090c14]/90 backdrop-blur-md z-50 flex-col items-center justify-center transition-opacity duration-300">
                <div class="w-12 h-12 border-4 border-cyan-400 border-t-transparent rounded-full animate-spin"></div>
                <p class="mt-4 text-cyan-300 font-mono text-xs uppercase tracking-widest font-bold">{{ __('Loading deposits...') }}</p>
            </div>

            <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] overflow-hidden">
                
                {{-- Table Filter Header --}}
                <div class="p-6 border-b border-white/[0.06] flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white/[0.01]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white font-mono uppercase tracking-wide flex items-center gap-2">
                                <span>{{ __('Deposits List') }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 text-[10px]">
                                    {{ $deposits->total() }}
                                </span>
                            </h3>
                            <p class="text-xs text-slate-400">{{ __('All deposit transactions and their current status.') }}</p>
                        </div>
                    </div>

                    {{-- Search & Filters Form --}}
                    <form id="filter-form" action="{{ route('admin.deposits.index') }}" method="GET"
                        class="flex flex-wrap gap-2.5 items-center font-mono">
                        @if (request('user_id'))
                            <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                        @endif

                        {{-- Search Input --}}
                        <div class="relative min-w-[220px] flex-1 sm:flex-initial">
                            <input type="text" name="search" placeholder="{{ __('Search user, transaction ID...') }}"
                                value="{{ request('search') }}"
                                class="w-full bg-white/[0.03] border border-white/[0.1] focus:border-cyan-400/60 rounded-xl px-4 py-2 text-white text-xs placeholder-slate-500 focus:outline-none transition-all">
                        </div>

                        {{-- Status Dropdown --}}
                        <div class="relative custom-dropdown" id="status-filter-dropdown">
                            <input type="hidden" name="status" id="status-input" value="{{ request('status', 'all') }}">
                            <button type="button"
                                class="bg-white/[0.03] border border-white/[0.1] hover:border-white/[0.2] rounded-xl px-4 py-2 text-xs text-white transition-all flex items-center justify-between gap-3 min-w-[130px] dropdown-btn cursor-pointer">
                                <span class="selected-label font-bold text-cyan-300">
                                    @if (request('status') == 'pending')
                                        {{ __('Pending') }}
                                    @elseif(request('status') == 'partial_payment')
                                        {{ __('Partial Payment') }}
                                    @elseif(request('status') == 'completed')
                                        {{ __('Completed') }}
                                    @elseif(request('status') == 'failed')
                                        {{ __('Failed') }}
                                    @else
                                        {{ __('All Status') }}
                                    @endif
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="absolute top-full right-0 mt-2 w-48 bg-[#0b0e17] border border-white/[0.1] rounded-2xl shadow-2xl z-[60] py-1.5 hidden dropdown-menu animate-in fade-in slide-in-from-top-2 duration-200">
                                <div class="dropdown-option px-4 py-2.5 text-xs text-slate-300 hover:text-white hover:bg-cyan-500/10 cursor-pointer transition-colors" data-value="all">{{ __('All Status') }}</div>
                                <div class="dropdown-option px-4 py-2.5 text-xs text-amber-400 hover:bg-amber-500/10 cursor-pointer transition-colors" data-value="pending">{{ __('Pending') }}</div>
                                <div class="dropdown-option px-4 py-2.5 text-xs text-purple-400 hover:bg-purple-500/10 cursor-pointer transition-colors" data-value="partial_payment">{{ __('Partial Payment') }}</div>
                                <div class="dropdown-option px-4 py-2.5 text-xs text-emerald-400 hover:bg-emerald-500/10 cursor-pointer transition-colors" data-value="completed">{{ __('Completed') }}</div>
                                <div class="dropdown-option px-4 py-2.5 text-xs text-rose-400 hover:bg-rose-500/10 cursor-pointer transition-colors" data-value="failed">{{ __('Failed') }}</div>
                            </div>
                        </div>

                        {{-- Export Dropdown --}}
                        <div class="relative custom-dropdown" id="export-dropdown">
                            <button type="button"
                                class="bg-white/[0.03] border border-white/[0.1] hover:border-white/[0.2] rounded-xl px-4 py-2 text-xs text-slate-300 hover:text-white transition-all flex items-center justify-between gap-2 dropdown-btn cursor-pointer">
                                <span>{{ __('Export') }}</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="absolute top-full right-0 mt-2 w-44 bg-[#0b0e17] border border-white/[0.1] rounded-2xl shadow-2xl z-[60] py-1.5 hidden dropdown-menu animate-in fade-in slide-in-from-top-2 duration-200">
                                <div class="dropdown-option px-4 py-2 text-xs text-slate-300 hover:text-white hover:bg-white/[0.06] cursor-pointer" data-export="csv">CSV Spreadsheet</div>
                                <div class="dropdown-option px-4 py-2 text-xs text-slate-300 hover:text-white hover:bg-white/[0.06] cursor-pointer" data-export="sql">SQL Dump</div>
                                <div class="dropdown-option px-4 py-2 text-xs text-slate-300 hover:text-white hover:bg-white/[0.06] cursor-pointer" data-export="pdf">PDF Document</div>
                            </div>
                        </div>

                        {{-- Search Button --}}
                        <button type="submit"
                            class="p-2.5 bg-cyan-500/10 border border-cyan-500/30 hover:border-cyan-400 text-cyan-300 rounded-xl hover:bg-cyan-500/20 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Table Body --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[1050px]">
                        <thead>
                            <tr class="border-b border-white/[0.06] bg-white/[0.01]">
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('User') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Payment Method') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Transaction ID') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Deposit Amount') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Fee') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Total Credited') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono text-center">{{ __('Status') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono text-right">{{ __('Date') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.03]">
                            @forelse ($deposits as $deposit)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    {{-- User --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($deposit->user?->photo)
                                                <div class="w-9 h-9 rounded-2xl border border-white/10 shadow-lg overflow-hidden shrink-0">
                                                    <img src="{{ asset('storage/profile/' . $deposit->user->photo) }}"
                                                        alt="{{ $deposit->user->username }}"
                                                        class="w-full h-full object-cover">
                                                </div>
                                            @else
                                                <div class="w-9 h-9 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 font-mono font-bold text-xs shrink-0">
                                                    {{ substr($deposit->user?->username ?? 'NA', 0, 2) }}
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ $deposit->user ? route('admin.users.detail', $deposit->user->id) : '#' }}"
                                                    class="text-xs text-white font-bold font-mono hover:text-cyan-400 transition-colors block">
                                                    {{ $deposit->user?->username ?? __('Deleted User') }}
                                                </a>
                                                <span class="text-[10px] font-mono text-slate-400 block mt-0.5">
                                                    {{ $deposit->user?->email ?? '—' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Gateway / Chain --}}
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/[0.03] border border-white/[0.06] text-xs text-slate-200 font-mono">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                            <span>{{ $deposit->gatewayName() }}</span>
                                        </span>
                                    </td>

                                    {{-- Trx Reference --}}
                                    <td class="px-6 py-4">
                                        <div class="inline-flex items-center gap-1.5 bg-black/40 px-2.5 py-1 rounded-lg border border-white/[0.06]">
                                            <code class="text-xs text-cyan-300 font-mono select-all">{{ $deposit->transaction_reference }}</code>
                                            <button onclick="navigator.clipboard.writeText('{{ $deposit->transaction_reference }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Ref Copied') }}', showConfirmButton:false, timer:1500});"
                                                class="text-slate-500 hover:text-white transition-colors cursor-pointer p-0.5" title="{{ __('Copy reference') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Amount --}}
                                    <td class="px-6 py-4">
                                        <span class="text-xs text-white font-bold font-mono">{{ showAmount($deposit->amount) }}</span>
                                    </td>

                                    {{-- Fee --}}
                                    <td class="px-6 py-4">
                                        <span class="text-xs text-rose-400 font-bold font-mono">-{{ showAmount($deposit->fee_amount) }}</span>
                                        <span class="text-[9px] font-mono text-slate-500 block mt-0.5">({{ $deposit->fee_percent }}%)</span>
                                    </td>

                                    {{-- Total Amount --}}
                                    <td class="px-6 py-4">
                                        <span class="text-xs text-emerald-400 font-bold font-mono">{{ showAmount($deposit->total_amount) }}</span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 text-center">
                                        @if ($deposit->status == 'pending')
                                            <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-mono font-bold uppercase tracking-wider">
                                                ● {{ __('Pending') }}
                                            </span>
                                        @elseif($deposit->status == 'partial_payment')
                                            <span class="px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20 text-[10px] font-mono font-bold uppercase tracking-wider">
                                                ● {{ __('Partial') }}
                                            </span>
                                        @elseif($deposit->status == 'completed')
                                            <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-mono font-bold uppercase tracking-wider">
                                                ● {{ __('Completed') }}
                                            </span>
                                        @elseif($deposit->status == 'failed')
                                            <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-mono font-bold uppercase tracking-wider">
                                                ● {{ __('Failed') }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full bg-slate-500/10 text-slate-400 border border-slate-500/20 text-[10px] font-mono font-bold uppercase tracking-wider">
                                                {{ $deposit->status }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Date --}}
                                    <td class="px-6 py-4 text-right font-mono">
                                        <span class="text-xs text-slate-200 block">
                                            {{ $deposit->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="text-[10px] text-slate-500 block mt-0.5">
                                            {{ $deposit->created_at->format('H:i:s') }} UTC
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            {{-- View Details --}}
                                            <a href="{{ route('admin.deposits.view', $deposit->id) }}"
                                                class="p-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/20 transition-all cursor-pointer"
                                                title="{{ __('View Deposit Details') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>

                                            {{-- Edit Status --}}
                                            <button type="button"
                                                class="edit-deposit-btn p-2 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 border border-purple-500/20 transition-all cursor-pointer"
                                                data-id="{{ $deposit->id }}" data-status="{{ $deposit->status }}"
                                                title="{{ __('Update Status') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>

                                            {{-- Delete --}}
                                            <button type="button"
                                                class="delete-deposit-btn p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-all cursor-pointer"
                                                data-id="{{ $deposit->id }}"
                                                title="{{ __('Delete Record') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-16 text-center text-slate-500 font-mono text-xs uppercase tracking-widest">
                                        {{ __('No deposit transactions found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                @if ($deposits->hasPages())
                    <div class="px-6 py-4 border-t border-white/[0.06] ajax-pagination bg-white/[0.01]">
                        {{ $deposits->links('templates.york.blades.partials.pagination') }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ==================================================================================== --}}
    {{-- MODALS --}}
    {{-- ==================================================================================== --}}

    {{-- 1. Edit Status Modal --}}
    <div id="edit-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-[#05070d]/80 backdrop-blur-xl transition-opacity modal-close"></div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.9)] relative w-full max-w-md transform transition-all text-left">
                <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8 relative">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Update Deposit Status') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('Change the status of this deposit.') }}</p>
                        </div>
                        <button type="button" class="w-8 h-8 rounded-full bg-white/[0.04] text-slate-400 hover:text-white transition-colors modal-close flex items-center justify-center cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <form id="edit-status-form" class="space-y-6">
                        @csrf
                        <input type="hidden" name="deposit_id" id="edit-deposit-id">

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
                                    @foreach (['pending', 'partial_payment', 'completed', 'failed'] as $st)
                                        <div class="dropdown-option px-4 py-3 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer capitalize"
                                            data-value="{{ $st }}">
                                            @if ($st == 'partial_payment')
                                                {{ __('Partial Payment') }}
                                            @else
                                                {{ ucfirst($st) }}
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-white/[0.06] flex gap-3 font-mono">
                            <button type="button"
                                class="flex-1 px-4 py-2.5 rounded-full border border-white/[0.1] text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-white/[0.05] transition-all modal-close cursor-pointer">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-full bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(0,245,255,0.3)] transition-all cursor-pointer">
                                {{ __('Save Status') }}
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

                    <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide mb-2">{{ __('Delete Deposit') }}</h3>
                    <p class="text-xs text-slate-400 mb-6 leading-relaxed">
                        {{ __('Are you sure you want to delete this deposit record? This action cannot be undone.') }}
                    </p>

                    <div class="flex gap-3 font-mono">
                        <input type="hidden" id="delete-deposit-id">
                        <button type="button"
                            class="flex-1 px-4 py-2.5 rounded-full border border-white/[0.1] text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-white/[0.05] transition-all modal-close cursor-pointer">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" id="confirm-delete"
                            class="flex-1 px-4 py-2.5 rounded-full bg-rose-500 hover:bg-rose-400 text-white font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(244,63,94,0.3)] transition-all cursor-pointer">
                            {{ __('Delete Record') }}
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
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Export Deposits') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('Select the columns you want to include in the download.') }}</p>
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
                                        'payment_method_name' => ['label' => 'Payment Method', 'default' => true],
                                        'amount' => ['label' => 'Amount', 'default' => true],
                                        'fee_amount' => ['label' => 'Fee', 'default' => true],
                                        'total_amount' => ['label' => 'Total Credited', 'default' => true],
                                        'converted_amount' => ['label' => 'Converted', 'default' => false],
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

            // ── Deposit Trend Chart ───────────────────────────────────────────────
            const trendCtx = document.getElementById('depositTrendChart').getContext('2d');
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
                'partial_payment': {
                    color: '#a855f7',
                    bg: 'rgba(168, 85, 247, 0.1)',
                    border: 'rgba(168, 85, 247, 0.3)',
                    label: '{{ __('Partial') }}'
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
                if (!GRAPH_DATA[period]) return;

                const periodData = GRAPH_DATA[period];

                let allLabels = new Set();
                Object.values(periodData).forEach(statusData => {
                    statusData.labels.forEach(l => allLabels.add(l));
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
                const foundItem = STATUS_CHART_DATA.find(item => item.status.toLowerCase() === key);
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
                    'partial_payment': '{{ __('Partial Payment') }}',
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
                $('#deposits-wrapper .overflow-x-auto').css('opacity', '0.3');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        const newContent = $(data).find('#deposits-wrapper').html();
                        if (newContent) {
                            $('#deposits-wrapper').html(newContent);
                        }
                        window.history.pushState({}, '', url);
                    },
                    error: function() {
                        Swal.fire({toast:true, position:'top-end', icon:'error', title:'{{ __('Error loading data') }}', showConfirmButton:false, timer:2000});
                    },
                    complete: function() {
                        $('#loading-spinner').addClass('hidden').removeClass('flex');
                        $('#deposits-wrapper .overflow-x-auto').css('opacity', '1');
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
            $(document).on('click', '.edit-deposit-btn', function() {
                const id = $(this).data('id');
                const status = $(this).data('status');

                $('#edit-deposit-id').val(id);
                $('#edit-status-value').val(status);

                let statusLabel = status;
                if (status === 'partial_payment') {
                    statusLabel = '{{ __('Partial Payment') }}';
                } else {
                    statusLabel = status.charAt(0).toUpperCase() + status.slice(1);
                }
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
                const id = $('#edit-deposit-id').val();
                const status = $('#edit-status-value').val();

                const $submitBtn = $(this).find('button[type="submit"]');
                const originalText = $submitBtn.text();
                $submitBtn.text('{{ __('Saving...') }}').prop('disabled', true);

                $.ajax({
                    url: `{{ url('admin/deposits/edit') }}/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(res) {
                        if (res.success || res.status === 'success') {
                            closeModal('edit-modal');
                            Swal.fire({toast:true, position:'top-end', icon:'success', title: res.message || 'Deposit status updated', showConfirmButton:false, timer:2000});
                            $submitBtn.text(originalText).prop('disabled', false);
                            loadTable(window.location.href);
                        } else {
                            Swal.fire({toast:true, position:'top-end', icon:'error', title: res.message || 'An error occurred', showConfirmButton:false, timer:2000});
                            $submitBtn.text(originalText).prop('disabled', false);
                        }
                    },
                    error: function(err) {
                        let error = err.responseJSON?.message || 'An error occurred';
                        Swal.fire({toast:true, position:'top-end', icon:'error', title: error, showConfirmButton:false, timer:2000});
                        $submitBtn.text(originalText).prop('disabled', false);
                    }
                });
            });

            // ── Delete Deposit ────────────────────────────────────────────────────
            $(document).on('click', '.delete-deposit-btn', function() {
                const id = $(this).data('id');
                $('#delete-deposit-id').val(id);
                openModal('delete-modal');
            });

            $('#confirm-delete').on('click', function() {
                const id = $('#delete-deposit-id').val();
                const $btn = $(this);
                const originalText = $btn.text();

                $btn.text('{{ __('Deleting...') }}').prop('disabled', true);

                $.ajax({
                    url: `{{ url('admin/deposits/delete') }}/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            closeModal('delete-modal');
                            Swal.fire({toast:true, position:'top-end', icon:'success', title: res.message || 'Deposit deleted', showConfirmButton:false, timer:2000});
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
