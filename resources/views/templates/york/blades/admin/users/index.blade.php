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
                    {{ __('USERS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('All Users') }}
                </h1>
                <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('View, manage, and verify all registered user accounts.') }}
                </p>
            </div>

            {{-- Top Actions & Filter Ribbon --}}
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.users.bulk-email') }}"
                    class="px-4 py-2 rounded-xl bg-purple-500/15 hover:bg-purple-500/25 border border-purple-500/30 text-purple-300 hover:text-white font-mono text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-2 shadow-[0_0_15px_rgba(168,85,247,0.15)] cursor-pointer">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ __('Send Group Email') }}</span>
                </a>

                {{-- Status Tabs Ribbon --}}
                <div class="flex items-center gap-1.5 bg-white/[0.02] border border-white/[0.08] p-1.5 rounded-2xl overflow-x-auto max-w-full font-mono text-xs" id="status-tabs-container">
                    @php
                        $currentStatus = request('status', 'all');
                        $statusTabs = [
                            'all' => ['label' => 'All Users', 'color' => 'cyan'],
                            'active' => ['label' => 'Active', 'color' => 'emerald'],
                            'banned' => ['label' => 'Banned', 'color' => 'rose'],
                        ];
                    @endphp
                    @foreach ($statusTabs as $stKey => $tab)
                        @php
                            $isActive = $currentStatus === $stKey && !request('kyc_status') && !request('email_verified');
                            $tabUrl = $stKey === 'all' ? route('admin.users.index') : route('admin.users.index', ['status' => $stKey]);
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
        </div>

        {{-- ==================================================================================== --}}
        {{-- METRIC SUMMARY CARDS --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Card 1: Total Users --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-cyan-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Total Users') }}</span>
                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black font-mono text-white tracking-tight">
                            {{ number_format($stats['total']) }}
                        </div>
                        <div class="text-[9px] font-mono text-cyan-400/80 mt-1 uppercase tracking-wider">
                            {{ __('Registered on platform') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Active Accounts --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-emerald-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-emerald-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Active Users') }}</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black font-mono text-emerald-400 tracking-tight">
                            {{ number_format($stats['active']) }}
                        </div>
                        <div class="text-[9px] font-mono text-emerald-400/70 mt-1 uppercase tracking-wider">
                            {{ __('Can log in and trade') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Banned / Suspended --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-rose-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-rose-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Banned Users') }}</span>
                        <div class="w-8 h-8 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black font-mono text-rose-400 tracking-tight">
                            {{ number_format($stats['banned']) }}
                        </div>
                        <div class="text-[9px] font-mono text-rose-400/70 mt-1 uppercase tracking-wider">
                            {{ __('Account suspended') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Email Unverified --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-amber-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-amber-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Unverified Emails') }}</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black font-mono text-amber-400 tracking-tight">
                            {{ number_format($stats['email_unverified']) }}
                        </div>
                        <div class="text-[9px] font-mono text-amber-400/70 mt-1 uppercase tracking-wider">
                            {{ __('Awaiting confirmation') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 5: KYC Pending --}}
            <div class="p-1.5 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] hover:border-purple-400/40 backdrop-blur-xl transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-8 right-8 h-[1px] bg-gradient-to-r from-transparent via-purple-400 to-transparent"></div>
                <div class="rounded-[1.6rem] bg-[#090c14]/95 p-5 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">{{ __('Pending KYC') }}</span>
                        <div class="w-8 h-8 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-black font-mono text-purple-400 tracking-tight flex items-center gap-2">
                            <span>{{ number_format($stats['kyc_pending']) }}</span>
                            @if ($stats['kyc_pending'] > 0)
                                <span class="w-2 h-2 rounded-full bg-purple-400 animate-ping"></span>
                            @endif
                        </div>
                        <div class="text-[9px] font-mono text-purple-400/70 mt-1 uppercase tracking-wider">
                            {{ __('Identity waiting for review') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- USERS TABLE --}}
        {{-- ==================================================================================== --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden" id="users-wrapper">

            {{-- Loading Spinner Overlay --}}
            <div id="loading-spinner" class="hidden absolute inset-0 bg-[#090c14]/85 backdrop-blur-md z-50 flex flex-col items-center justify-center transition-all duration-300">
                <div class="w-10 h-10 border-2 border-cyan-400 border-t-transparent rounded-full animate-spin"></div>
                <p class="mt-3 text-cyan-300 font-mono text-xs tracking-widest uppercase">{{ __('Loading users...') }}</p>
            </div>

            <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] overflow-hidden">

                {{-- Table Control Bar --}}
                <div class="p-6 border-b border-white/[0.06] flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white/[0.01]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white font-mono uppercase tracking-wide flex items-center gap-2">
                                <span>{{ __('Users List') }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 text-[10px]">
                                    {{ $users->total() }}
                                </span>
                            </h3>
                            <p class="text-xs text-slate-400">{{ __('All registered user accounts and wallet balances.') }}</p>
                        </div>
                    </div>

                    {{-- Filters & Tools Form --}}
                    <form id="filter-form" action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap gap-2.5 items-center font-mono">
                        {{-- Search Input --}}
                        <div class="relative min-w-[200px]">
                            <input type="text" name="search" placeholder="{{ __('Search name, email, or username...') }}"
                                value="{{ request('search') }}"
                                class="w-full bg-white/[0.03] border border-white/[0.1] focus:border-cyan-400/60 rounded-xl px-4 py-2 text-white text-xs placeholder-slate-500 focus:outline-none transition-all">
                        </div>

                        {{-- Status Dropdown --}}
                        <div class="relative custom-dropdown" id="status-filter-dropdown">
                            <input type="hidden" name="status" id="status-input" value="{{ request('status', 'all') }}">
                            <button type="button"
                                class="dropdown-btn flex items-center justify-between gap-2.5 px-3.5 py-2 rounded-xl bg-white/[0.03] border border-white/[0.1] text-white text-xs hover:border-cyan-400/50 transition-all cursor-pointer min-w-[120px]">
                                <span class="selected-label font-bold text-cyan-300">
                                    {{ request('status') && request('status') != 'all' ? ucfirst(request('status')) : __('All Status') }}
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-500 dropdown-icon transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="dropdown-menu absolute right-0 z-[120] mt-2 w-36 bg-[#0b0e17] border border-white/[0.1] rounded-2xl shadow-2xl py-2 hidden animate-in fade-in slide-in-from-top-2 duration-200">
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="all">{{ __('All Status') }}</div>
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="active">{{ __('Active') }}</div>
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="banned">{{ __('Banned') }}</div>
                            </div>
                        </div>

                        {{-- KYC Status Dropdown --}}
                        <div class="relative custom-dropdown" id="kyc-filter-dropdown">
                            <input type="hidden" name="kyc_status" id="kyc-status-input" value="{{ request('kyc_status', 'all') }}">
                            <button type="button"
                                class="dropdown-btn flex items-center justify-between gap-2.5 px-3.5 py-2 rounded-xl bg-white/[0.03] border border-white/[0.1] text-white text-xs hover:border-cyan-400/50 transition-all cursor-pointer min-w-[120px]">
                                <span class="selected-label text-slate-300">
                                    {{ request('kyc_status') && request('kyc_status') != 'all' ? 'KYC: ' . ucfirst(request('kyc_status')) : __('All KYC') }}
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-500 dropdown-icon transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="dropdown-menu absolute right-0 z-[120] mt-2 w-40 bg-[#0b0e17] border border-white/[0.1] rounded-2xl shadow-2xl py-2 hidden animate-in fade-in slide-in-from-top-2 duration-200">
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="all">{{ __('All KYC') }}</div>
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="approved">{{ __('Approved') }}</div>
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="pending">{{ __('Pending') }}</div>
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="rejected">{{ __('Rejected') }}</div>
                            </div>
                        </div>

                        {{-- Email Verified Filter --}}
                        <div class="relative custom-dropdown" id="email-filter-dropdown">
                            <input type="hidden" name="email_verified" id="email-verified-input" value="{{ request('email_verified', 'all') }}">
                            <button type="button"
                                class="dropdown-btn flex items-center justify-between gap-2.5 px-3.5 py-2 rounded-xl bg-white/[0.03] border border-white/[0.1] text-white text-xs hover:border-cyan-400/50 transition-all cursor-pointer min-w-[120px]">
                                <span class="selected-label text-slate-300">
                                    @if (request('email_verified') === '1')
                                        {{ __('Email Verified') }}
                                    @elseif(request('email_verified') === '0')
                                        {{ __('Email Unverified') }}
                                    @else
                                        {{ __('All Email') }}
                                    @endif
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-500 dropdown-icon transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="dropdown-menu absolute right-0 z-[120] mt-2 w-44 bg-[#0b0e17] border border-white/[0.1] rounded-2xl shadow-2xl py-2 hidden animate-in fade-in slide-in-from-top-2 duration-200">
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="all">{{ __('All Email') }}</div>
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="1">{{ __('Verified Only') }}</div>
                                <div class="dropdown-option px-4 py-2 text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-300 transition-colors cursor-pointer" data-value="0">{{ __('Unverified Only') }}</div>
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

                        {{-- Submit Search Button --}}
                        <button type="submit"
                            class="p-2 bg-cyan-400 hover:bg-cyan-300 text-[#050507] rounded-xl transition-all cursor-pointer shadow-[0_0_15px_rgba(0,245,255,0.25)]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Bulk Action Ribbon (Revealed on select) --}}
                <div id="bulk-actions-bar" class="hidden px-6 py-3 bg-cyan-500/10 border-b border-cyan-500/20 flex items-center justify-between flex-wrap gap-3 font-mono text-xs">
                    <div class="flex items-center gap-2 text-cyan-300 font-bold">
                        <span id="selected-count" class="px-2 py-0.5 rounded-md bg-cyan-500/20 border border-cyan-500/30">0</span>
                        <span>{{ __('users selected') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="bulk-action-btn px-3 py-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500 text-emerald-300 hover:text-white font-bold transition-all" data-action="activate">
                            {{ __('Activate') }}
                        </button>
                        <button type="button" class="bulk-action-btn px-3 py-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white font-bold transition-all" data-action="ban">
                            {{ __('Ban') }}
                        </button>
                        <button type="button" class="bulk-action-btn px-3 py-1.5 rounded-lg bg-cyan-500/20 hover:bg-cyan-500 text-cyan-300 hover:text-black font-bold transition-all" data-action="verify_email">
                            {{ __('Verify Email') }}
                        </button>
                    </div>
                </div>

                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[1050px]">
                        <thead>
                            <tr class="border-b border-white/[0.06] bg-white/[0.01]">
                                <th class="px-4 py-4 w-10 text-center">
                                    <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-white/20 bg-white/5 text-cyan-400 focus:ring-cyan-400 focus:ring-offset-0 cursor-pointer accent-cyan-400">
                                </th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('User') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Balance') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono text-center">{{ __('Identity (KYC)') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono text-center">{{ __('Status') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono text-right">{{ __('Joined') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.03]">
                            @forelse ($users as $user)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    {{-- Checkbox --}}
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox w-4 h-4 rounded border-white/20 bg-white/5 text-cyan-400 focus:ring-cyan-400 focus:ring-offset-0 cursor-pointer accent-cyan-400">
                                    </td>

                                    {{-- User Profile --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($user->photo)
                                                <div class="w-10 h-10 rounded-2xl border border-white/10 shadow-sm overflow-hidden shrink-0">
                                                    <img src="{{ asset('storage/profile/' . $user->photo) }}" alt="{{ $user->username }}" class="w-full h-full object-cover">
                                                </div>
                                            @else
                                                <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-xs font-bold font-mono text-cyan-400 shrink-0">
                                                    {{ strtoupper(substr($user->username ?? 'U', 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="flex items-center gap-1.5">
                                                    <a href="{{ route('admin.users.detail', $user->id) }}" class="text-xs font-bold font-mono text-white hover:text-cyan-400 transition-colors">
                                                        {{ $user->fullname ?? $user->username }}
                                                    </a>
                                                    @if ($user->email_verified_at)
                                                        <span title="{{ __('Email Verified') }}" class="text-cyan-400">
                                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-[10px] text-slate-400 font-mono flex items-center gap-2 mt-0.5">
                                                    <span>{{ '@' . $user->username }}</span>
                                                    <span>·</span>
                                                    <span>{{ $user->email }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Balance / Equity --}}
                                    <td class="px-6 py-4 font-mono">
                                        <div class="text-xs font-black text-white">
                                            {{ showAmount($user->balance) }}
                                        </div>
                                        <span class="text-[10px] text-slate-500 block mt-0.5">
                                            {{ __('Available') }}
                                        </span>
                                    </td>

                                    {{-- KYC Status --}}
                                    <td class="px-6 py-4 text-center font-mono">
                                        @php
                                            $kycRecord = $user->kyc->first();
                                            $kycStatus = $kycRecord ? $kycRecord->status : 'unverified';
                                        @endphp
                                        @if ($kycStatus === 'approved')
                                            <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold uppercase tracking-wider">
                                                ● {{ __('Verified') }}
                                            </span>
                                        @elseif($kycStatus === 'pending')
                                            <span class="px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20 text-[10px] font-bold uppercase tracking-wider">
                                                ● {{ __('Pending') }}
                                            </span>
                                        @elseif($kycStatus === 'rejected')
                                            <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-bold uppercase tracking-wider">
                                                ● {{ __('Rejected') }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full bg-slate-500/10 text-slate-400 border border-white/[0.08] text-[10px] font-bold uppercase tracking-wider">
                                                {{ __('Unverified') }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Account Status --}}
                                    <td class="px-6 py-4 text-center font-mono">
                                        @if ($user->status === 'active')
                                            <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold uppercase tracking-wider">
                                                ● {{ __('Active') }}
                                            </span>
                                        @elseif($user->status === 'banned')
                                            <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-bold uppercase tracking-wider">
                                                ● {{ __('Banned') }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full bg-slate-500/10 text-slate-400 border border-slate-500/20 text-[10px] font-bold uppercase tracking-wider">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Joined Date --}}
                                    <td class="px-6 py-4 text-right font-mono">
                                        <span class="text-xs text-slate-200 block">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="text-[10px] text-slate-500 block mt-0.5">
                                            {{ $user->created_at->format('H:i') }} UTC
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            {{-- Dossier / Detail --}}
                                            <a href="{{ route('admin.users.detail', $user->id) }}"
                                                class="p-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/20 transition-all cursor-pointer"
                                                title="{{ __('View User Details') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>

                                            {{-- Login as User (POST AJAX) --}}
                                            <button type="button"
                                                class="login-as-btn p-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/20 transition-all cursor-pointer"
                                                data-url="{{ route('admin.users.login-as', $user->id) }}"
                                                data-name="{{ $user->fullname ?? $user->username }}"
                                                title="{{ __('Log in as User') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                                </svg>
                                            </button>

                                            {{-- Quick Ban/Unban or Status Modal --}}
                                            <button type="button"
                                                class="edit-user-status-btn p-2 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 border border-purple-500/20 transition-all cursor-pointer"
                                                data-id="{{ $user->id }}" data-status="{{ $user->status }}"
                                                title="{{ __('Edit Status') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>

                                            {{-- Direct Email --}}
                                            <button type="button"
                                                class="send-user-email-btn p-2 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white border border-white/[0.08] transition-all cursor-pointer"
                                                data-id="{{ $user->id }}" data-email="{{ $user->email }}" data-name="{{ $user->fullname ?? $user->username }}"
                                                title="{{ __('Send Email') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center text-slate-500 font-mono text-xs uppercase tracking-widest">
                                        {{ __('No users found matching your filters.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                @if ($users->hasPages())
                    <div class="px-6 py-4 border-t border-white/[0.06] ajax-pagination bg-white/[0.01]">
                        {{ $users->links('templates.york.blades.partials.pagination') }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ==================================================================================== --}}
    {{-- MODALS --}}
    {{-- ==================================================================================== --}}

    {{-- 1. Edit User Status Modal --}}
    <div id="user-status-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-[#05070d]/80 backdrop-blur-xl transition-opacity modal-close"></div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.9)] relative w-full max-w-md transform transition-all text-left">
                <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8 relative">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Change User Status') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('Update whether this user can access their account.') }}</p>
                        </div>
                        <button type="button" class="w-8 h-8 rounded-full bg-white/[0.04] text-slate-400 hover:text-white transition-colors modal-close flex items-center justify-center cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <form id="user-status-form" class="space-y-6 font-mono">
                        @csrf
                        <input type="hidden" name="user_id" id="edit-status-user-id">

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">
                                {{ __('Account Status') }}
                            </label>
                            <select name="status" id="edit-user-status-select"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-all cursor-pointer">
                                <option value="active">{{ __('Active (Can access account)') }}</option>
                                <option value="banned">{{ __('Banned (Access blocked)') }}</option>
                            </select>
                        </div>

                        <div class="pt-4 border-t border-white/[0.06] flex gap-3">
                            <button type="button"
                                class="flex-1 px-4 py-2.5 rounded-full border border-white/[0.1] text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-white/[0.05] transition-all modal-close cursor-pointer">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-full bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(0,245,255,0.3)] transition-all cursor-pointer">
                                {{ __('Save Changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Direct Email Modal --}}
    <div id="direct-email-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-[#05070d]/80 backdrop-blur-xl transition-opacity modal-close"></div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.9)] relative w-full max-w-lg transform transition-all text-left">
                <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8 relative">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Send Email to User') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5" id="email-recipient-label">{{ __('Send a direct email message to this user.') }}</p>
                        </div>
                        <button type="button" class="w-8 h-8 rounded-full bg-white/[0.04] text-slate-400 hover:text-white transition-colors modal-close flex items-center justify-center cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <form id="direct-email-form" class="space-y-4 font-mono">
                        @csrf
                        <input type="hidden" name="user_id" id="direct-email-user-id">

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                                {{ __('Email Subject') }}
                            </label>
                            <input type="text" name="subject" required placeholder="{{ __('Account Notice / Update...') }}"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                                {{ __('Message') }}
                            </label>
                            <textarea name="message" rows="5" required placeholder="{{ __('Type your message here...') }}"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all resize-none"></textarea>
                        </div>

                        <div class="pt-4 border-t border-white/[0.06] flex gap-3">
                            <button type="button"
                                class="flex-1 px-4 py-2.5 rounded-full border border-white/[0.1] text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-white/[0.05] transition-all modal-close cursor-pointer">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-full bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(0,245,255,0.3)] transition-all cursor-pointer">
                                {{ __('Send Email') }}
                            </button>
                        </div>
                    </form>
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
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Export Users') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('Choose which columns to include in your export.') }}</p>
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
                                        'id' => ['label' => 'ID', 'default' => true],
                                        'username' => ['label' => 'Username', 'default' => true],
                                        'fullname' => ['label' => 'Full Name', 'default' => true],
                                        'email' => ['label' => 'Email', 'default' => true],
                                        'balance' => ['label' => 'Balance', 'default' => true],
                                        'status' => ['label' => 'Status', 'default' => true],
                                        'kyc_status' => ['label' => 'KYC Status', 'default' => true],
                                        'referral_code' => ['label' => 'Referral Code', 'default' => false],
                                        'referred_by' => ['label' => 'Referred By', 'default' => false],
                                        'created_at' => ['label' => 'Joined Date', 'default' => true],
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Dropdowns
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

            // Modals
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

            // Status Filter Select
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

            // KYC Filter Select
            $(document).on('click', '#kyc-filter-dropdown .dropdown-option', function() {
                const val = $(this).data('value');
                const label = $(this).text();
                $('#kyc-status-input').val(val);
                $('#kyc-filter-dropdown .selected-label').text(label);
                $('#filter-form').submit();
            });

            // Email Filter Select
            $(document).on('click', '#email-filter-dropdown .dropdown-option', function() {
                const val = $(this).data('value');
                const label = $(this).text();
                $('#email-verified-input').val(val);
                $('#email-filter-dropdown .selected-label').text(label);
                $('#filter-form').submit();
            });

            // Fast Filter Ribbon Tab Clicks
            $(document).on('click', '.status-tab-btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const status = $(this).data('status');

                $('.status-tab-btn').removeClass('bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-[0_0_15px_rgba(0,245,255,0.2)] active-tab')
                                    .addClass('text-slate-400 hover:text-white hover:bg-white/[0.04]');
                $('.status-tab-btn .tab-dot').removeClass('bg-cyan-400 animate-pulse').addClass('bg-slate-600');
                
                $(this).addClass('bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-[0_0_15px_rgba(0,245,255,0.2)] active-tab')
                       .removeClass('text-slate-400 hover:text-white hover:bg-white/[0.04]');
                $(this).find('.tab-dot').removeClass('bg-slate-600').addClass('bg-cyan-400 animate-pulse');

                $('#status-input').val(status);
                const statusLabels = {
                    'all': '{{ __('All Status') }}',
                    'active': '{{ __('Active') }}',
                    'banned': '{{ __('Banned') }}'
                };
                $('#status-filter-dropdown .selected-label').text(statusLabels[status] || status);

                loadTable(url);
            });

            // AJAX Filtering & Pagination
            $(document).on('click', '.ajax-pagination a', function(e) {
                e.preventDefault();
                loadTable($(this).attr('href'));
            });

            $(document).on('submit', '#filter-form', function(e) {
                e.preventDefault();
                loadTable($(this).attr('action') + '?' + $(this).serialize());
            });

            function loadTable(url) {
                $('#loading-spinner').removeClass('hidden').addClass('flex');
                $('#users-wrapper .overflow-x-auto').css('opacity', '0.3');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        const newContent = $(data).find('#users-wrapper').html();
                        if (newContent) {
                            $('#users-wrapper').html(newContent);
                        }
                        window.history.pushState({}, '', url);
                        updateSelectedCount();
                    },
                    error: function() {
                        Swal.fire({toast:true, position:'top-end', icon:'error', title:'{{ __('Error loading records') }}', showConfirmButton:false, timer:2000});
                    },
                    complete: function() {
                        $('#loading-spinner').addClass('hidden').removeClass('flex');
                        $('#users-wrapper .overflow-x-auto').css('opacity', '1');
                    }
                });
            }

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

                const kycVal = $('#kyc-status-input').val();
                if (kycVal && kycVal !== 'all') params.set('kyc_status', kycVal);

                const emailVal = $('#email-verified-input').val();
                if (emailVal && emailVal !== 'all') params.set('email_verified', emailVal);

                params.set('export', exportType);
                params.set('columns', selectedCols.join(','));

                closeModal('export-modal');
                window.location.href = baseUrl + '?' + params.toString();
            });

            // Checkbox Multi-Select Handling
            $(document).on('change', '#select-all', function() {
                $('.user-checkbox').prop('checked', $(this).prop('checked'));
                updateSelectedCount();
            });

            $(document).on('change', '.user-checkbox', function() {
                updateSelectedCount();
            });

            function updateSelectedCount() {
                const count = $('.user-checkbox:checked').length;
                $('#selected-count').text(count);
                if (count > 0) {
                    $('#bulk-actions-bar').removeClass('hidden');
                } else {
                    $('#bulk-actions-bar').addClass('hidden');
                    $('#select-all').prop('checked', false);
                }
            }

            // Bulk Actions Execution
            $(document).on('click', '.bulk-action-btn', function() {
                const action = $(this).data('action');
                const selectedIds = [];
                $('.user-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    Swal.fire({toast:true, position:'top-end', icon:'error', title:'{{ __('No users selected') }}', showConfirmButton:false, timer:2000});
                    return;
                }

                Swal.fire({
                    title: '{{ __('Confirm Bulk Action') }}',
                    text: '{{ __('Are you sure you want to execute this action on selected accounts?') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#00f5ff',
                    cancelButtonColor: '#334155',
                    confirmButtonText: '{{ __('Yes, Execute') }}',
                    customClass: {
                        popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono',
                        title: 'text-white font-mono',
                        htmlContainer: 'text-slate-400 font-mono',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.users.bulk') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                action: action,
                                ids: selectedIds
                            },
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire({toast:true, position:'top-end', icon:'success', title: res.message || 'Action executed successfully', showConfirmButton:false, timer:2500});
                                    loadTable(window.location.href);
                                } else {
                                    Swal.fire({toast:true, position:'top-end', icon:'error', title: res.message || 'An error occurred', showConfirmButton:false, timer:2500});
                                }
                            },
                            error: function() {
                                Swal.fire({toast:true, position:'top-end', icon:'error', title: 'An error occurred', showConfirmButton:false, timer:2500});
                            }
                        });
                    }
                });
            });

            // Edit User Status Single
            $(document).on('click', '.edit-user-status-btn', function() {
                const id = $(this).data('id');
                const status = $(this).data('status');
                $('#edit-status-user-id').val(id);
                $('#edit-user-status-select').val(status);
                openModal('user-status-modal');
            });

            $('#user-status-form').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit-status-user-id').val();
                const status = $('#edit-user-status-select').val();

                $.ajax({
                    url: `{{ url('admin/users') }}/${id}/status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(res) {
                        if (res.success) {
                            closeModal('user-status-modal');
                            Swal.fire({toast:true, position:'top-end', icon:'success', title: res.message || 'Status updated', showConfirmButton:false, timer:2000});
                            loadTable(window.location.href);
                        } else {
                            Swal.fire({toast:true, position:'top-end', icon:'error', title: res.message || 'An error occurred', showConfirmButton:false, timer:2000});
                        }
                    },
                    error: function() {
                        Swal.fire({toast:true, position:'top-end', icon:'error', title: 'An error occurred', showConfirmButton:false, timer:2000});
                    }
                });
            });

            // Send Direct Email Single
            $(document).on('click', '.send-user-email-btn', function() {
                const id = $(this).data('id');
                const email = $(this).data('email');
                const name = $(this).data('name');

                $('#direct-email-user-id').val(id);
                $('#email-recipient-label').text(`{{ __('Recipient') }}: ${name} (${email})`);
                openModal('direct-email-modal');
            });

            $('#direct-email-form').on('submit', function(e) {
                e.preventDefault();
                const id = $('#direct-email-user-id').val();
                const $btn = $(this).find('button[type="submit"]');
                const originalText = $btn.text();

                $btn.text('{{ __('Sending...') }}').prop('disabled', true);

                $.ajax({
                    url: `{{ url('admin/users') }}/${id}/email`,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            closeModal('direct-email-modal');
                            Swal.fire({toast:true, position:'top-end', icon:'success', title: res.message || 'Email dispatched', showConfirmButton:false, timer:2500});
                            $('#direct-email-form')[0].reset();
                        } else {
                            Swal.fire({toast:true, position:'top-end', icon:'error', title: res.message || 'An error occurred', showConfirmButton:false, timer:2500});
                        }
                    },
                    error: function() {
                        Swal.fire({toast:true, position:'top-end', icon:'error', title: 'An error occurred', showConfirmButton:false, timer:2500});
                    },
                    complete: function() {
                        $btn.text(originalText).prop('disabled', false);
                    }
                });
            });

            // Login As User (POST)
            $(document).on('click', '.login-as-btn', function() {
                const url = $(this).data('url');
                const name = $(this).data('name') || 'User';

                Swal.fire({
                    title: '{{ __('Login as User?') }}',
                    text: `{{ __('You will be authenticated as') }} ${name} {{ __('and redirected to their dashboard.') }}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#334155',
                    confirmButtonText: '{{ __('Yes, Login') }}',
                    customClass: {
                        popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono',
                        title: 'text-white font-mono',
                        htmlContainer: 'text-slate-400 font-mono',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                if (res.success && res.redirect_url) {
                                    window.open(res.redirect_url, '_blank');
                                } else {
                                    Swal.fire({toast:true, position:'top-end', icon:'error', title: res.message || 'Error occurred', showConfirmButton:false, timer:2500});
                                }
                            },
                            error: function() {
                                Swal.fire({toast:true, position:'top-end', icon:'error', title: 'An error occurred', showConfirmButton:false, timer:2500});
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
