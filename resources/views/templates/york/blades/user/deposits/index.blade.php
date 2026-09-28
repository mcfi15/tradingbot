@extends('templates.york.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-8">

    {{-- Global ambient background blur --}}
    <div class="fixed top-0 right-0 w-[40rem] h-[40rem] bg-accent-primary/5 rounded-full blur-[120px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[30rem] h-[30rem] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-8">

        {{-- ══ HEADER DECK ══════════════════════════════════════════════════════ --}}
        <div class="relative rounded-2xl overflow-hidden p-6 md:p-8"
             style="background: linear-gradient(145deg, rgba(8,9,14,0.97) 0%, rgba(5,6,10,0.99) 100%); border: 1px solid rgba(255,255,255,0.06);">
            
            <div class="absolute top-0 right-0 w-80 h-80 bg-accent-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse shadow-[0_0_8px_rgba(226,177,60,0.7)]"></span>
                        <span class="text-[9px] font-bold uppercase tracking-[0.25em] text-slate-500">{{ __('Deposits') }}</span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-slate-500 tracking-tight leading-tight">
                        {{ __('Deposit History') }}
                    </h1>
                    <p class="text-slate-500 text-sm mt-2 font-medium max-w-xl">
                        {{ __('Track your deposits, confirmations, and transaction status across all supported networks.') }}
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('user.deposits.new') }}"
                       class="group relative inline-flex items-center gap-2.5 px-7 py-3.5 bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-sm rounded-xl transition-all shadow-[0_4px_25px_rgba(226,177,60,0.3)] hover:shadow-[0_6px_30px_rgba(226,177,60,0.4)] active:scale-[0.98]">
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>{{ __('New Deposit') }}</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ══ METRICS HUD (4 Stat Cards) ══════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- Total Deposited --}}
            <div class="relative rounded-2xl overflow-hidden p-5 group transition-all duration-300 hover:-translate-y-0.5"
                 style="background: linear-gradient(145deg, rgba(8,9,14,0.95) 0%, rgba(4,5,8,0.98) 100%); border: 1px solid rgba(255,255,255,0.05);">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-accent-primary/10 rounded-full blur-xl pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Total Deposited') }}</span>
                    <div class="w-8 h-8 rounded-xl bg-accent-primary/10 text-accent-primary flex items-center justify-center border border-accent-primary/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-white tracking-tight">
                    {{ number_format($deposits_analytics['total']['total'], 2) }}
                    <span class="text-xs font-bold text-slate-500">{{ getSetting('currency') }}</span>
                </h3>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 bg-white/5 border border-white/5 px-2 py-0.5 rounded-md">
                        {{ $deposits_analytics['total']['count'] }} {{ __('Transactions') }}
                    </span>
                </div>
            </div>

            {{-- Pending --}}
            <div class="relative rounded-2xl overflow-hidden p-5 group transition-all duration-300 hover:-translate-y-0.5"
                 style="background: linear-gradient(145deg, rgba(8,9,14,0.95) 0%, rgba(4,5,8,0.98) 100%); border: 1px solid rgba(255,255,255,0.05);">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-amber-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Pending') }}</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-400/10 text-amber-400 flex items-center justify-center border border-amber-400/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-white tracking-tight">
                    {{ number_format($deposits_analytics['pending']['total'], 2) }}
                    <span class="text-xs font-bold text-slate-500">{{ getSetting('currency') }}</span>
                </h3>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400 bg-amber-400/10 border border-amber-400/20 px-2 py-0.5 rounded-md">
                        {{ $deposits_analytics['pending']['count'] }} {{ __('Processing') }}
                    </span>
                </div>
            </div>

            {{-- Completed --}}
            <div class="relative rounded-2xl overflow-hidden p-5 group transition-all duration-300 hover:-translate-y-0.5"
                 style="background: linear-gradient(145deg, rgba(8,9,14,0.95) 0%, rgba(4,5,8,0.98) 100%); border: 1px solid rgba(255,255,255,0.05);">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Completed') }}</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-white tracking-tight">
                    {{ number_format($deposits_analytics['completed']['total'], 2) }}
                    <span class="text-xs font-bold text-slate-500">{{ getSetting('currency') }}</span>
                </h3>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-md">
                        {{ $deposits_analytics['completed']['count'] }} {{ __('Completed') }}
                    </span>
                    @if ($avg_processing_time)
                        <span class="text-[9px] text-slate-500 font-bold">~{{ round($avg_processing_time / 60) }}m avg</span>
                    @endif
                </div>
            </div>

            {{-- Failed / Partial --}}
            <div class="relative rounded-2xl overflow-hidden p-5 group transition-all duration-300 hover:-translate-y-0.5"
                 style="background: linear-gradient(145deg, rgba(8,9,14,0.95) 0%, rgba(4,5,8,0.98) 100%); border: 1px solid rgba(255,255,255,0.05);">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-rose-500/10 rounded-full blur-xl pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Failed / Partial') }}</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center border border-rose-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-white tracking-tight">
                    {{ number_format($deposits_analytics['failed']['total'] + $deposits_analytics['partial_payment']['total'], 2) }}
                    <span class="text-xs font-bold text-slate-500">{{ getSetting('currency') }}</span>
                </h3>
                <div class="mt-3 flex items-center gap-1.5">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-rose-400 bg-rose-500/10 border border-rose-500/20 px-2 py-0.5 rounded-md">
                        {{ $deposits_analytics['failed']['count'] }} {{ __('Failed') }}
                    </span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-orange-400 bg-orange-500/10 border border-orange-500/20 px-2 py-0.5 rounded-md">
                        {{ $deposits_analytics['partial_payment']['count'] }} {{ __('Partial') }}
                    </span>
                </div>
            </div>

        </div>

        {{-- ══ DEPOSIT SOURCES & DISTRIBUTION ════════════════════════════════════ --}}
        <div class="relative rounded-2xl overflow-hidden p-6 md:p-8"
             style="background: linear-gradient(145deg, rgba(8,9,14,0.95) 0%, rgba(4,5,8,0.98) 100%); border: 1px solid rgba(255,255,255,0.05);">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-accent-primary/10 text-accent-primary flex items-center justify-center border border-accent-primary/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-white tracking-tight">{{ __('Deposit Sources Breakdown') }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ __('Volume and transaction frequency across supported deposit sources.') }}</p>
                    </div>
                </div>
                
                @if ($largest_deposit || $highest_daily_total)
                    <div class="hidden sm:flex items-center gap-4 text-xs border-l border-white/5 pl-6">
                        @if ($largest_deposit)
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase tracking-wider font-bold block">{{ __('Largest Deposit') }}</span>
                                <span class="text-white font-bold">{{ number_format($largest_deposit->amount, 2) }} {{ getSetting('currency') }}</span>
                            </div>
                        @endif
                        @if ($highest_daily_total)
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase tracking-wider font-bold block">{{ __('Peak Daily Volume') }}</span>
                                <span class="text-emerald-400 font-bold">{{ number_format($highest_daily_total->total, 2) }} {{ getSetting('currency') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="methods-container">
                @foreach ($method_stats as $stat)
                    @php $percent = $deposits_analytics['total']['total'] > 0 ? ($stat->total / $deposits_analytics['total']['total']) * 100 : 0; @endphp
                    <div class="method-item p-4 rounded-xl bg-white/[0.02] border border-white/5 hover:border-white/10 transition-all {{ $loop->index > 5 ? 'hidden' : '' }}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-accent-primary"></span>
                                <span class="text-sm font-bold text-white">{{ $stat->gatewayName() }}</span>
                            </div>
                            <span class="text-xs font-bold text-slate-400">{{ number_format($percent, 1) }}%</span>
                        </div>
                        <div class="flex items-baseline justify-between text-xs mb-2">
                            <span class="text-slate-500">{{ $stat->count }} {{ __('transactions') }}</span>
                            <span class="text-white font-bold">{{ number_format($stat->total, 2) }} {{ getSetting('currency') }}</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-accent-primary/60 to-accent-primary h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if (count($method_stats) > 6)
                <div class="mt-4 pt-4 border-t border-white/5 flex justify-center">
                    <button type="button" data-target="#methods-container"
                            class="toggle-load-more cursor-pointer px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                        <span class="label">{{ __('View All Sources') }}</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- ══ TRANSACTION HISTORY TABLE ════════════════════════════════════════ --}}
        <div id="deposits-history-wrapper" class="relative rounded-2xl overflow-hidden"
             style="background: linear-gradient(145deg, rgba(8,9,14,0.97) 0%, rgba(5,6,10,0.99) 100%); border: 1px solid rgba(255,255,255,0.06);">
            
            {{-- Spinner --}}
            <div id="deposits-loading-spinner" class="hidden absolute inset-0 bg-black/80 backdrop-blur-sm z-50 flex flex-col items-center justify-center">
                <div class="w-8 h-8 border-2 border-accent-primary border-t-transparent rounded-full animate-spin"></div>
                <p class="mt-2 text-xs text-slate-400 font-bold animate-pulse">{{ __('Loading records...') }}</p>
            </div>

            {{-- Table header bar --}}
            <div class="p-6 border-b border-white/5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-base font-black text-white tracking-tight">{{ __('Recent Transactions') }}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-white/5 border border-white/10 text-slate-400">
                        {{ $deposits->total() }} {{ __('Records') }}
                    </span>
                </div>

                <form method="GET" action="{{ route('user.deposits.index') }}" class="relative w-full md:w-64">
                    <input type="text" name="search" placeholder="{{ __('Search transaction code...') }}"
                           value="{{ request('search') }}"
                           class="w-full bg-white/[0.03] border border-white/8 rounded-xl pl-9 pr-4 py-2 text-xs text-white placeholder:text-slate-600 focus:outline-none focus:border-accent-primary/60 transition-all">
                    <svg class="w-3.5 h-3.5 text-slate-600 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </form>
            </div>

            {{-- Table content --}}
            <div id="deposits-table-content">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-white/5 bg-white/[0.01]">
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Reference / TxHash') }}</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Amount') }}</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Deposit Source') }}</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Date') }}</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">{{ __('Status') }}</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-xs">
                            @forelse($deposits as $deposit)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-white/[0.03] border border-white/8 flex items-center justify-center text-slate-400 group-hover:border-accent-primary/40 group-hover:text-accent-primary transition-all shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                            </div>
                                            <div>
                                                <a href="{{ route('user.deposits.view', $deposit->transaction_reference) }}"
                                                   class="text-white font-bold text-sm hover:text-accent-primary font-mono tracking-tight transition-colors">
                                                    {{ strlen($deposit->transaction_reference) > 10 ? substr($deposit->transaction_reference, 0, 5) . '...' . substr($deposit->transaction_reference, -4) : $deposit->transaction_reference }}
                                                </a>
                                                @if($deposit->transaction_hash)
                                                    <p class="text-slate-600 text-[10px] font-mono mt-0.5 truncate max-w-[140px]" title="{{ $deposit->transaction_hash }}">
                                                        {{ substr($deposit->transaction_hash, 0, 6) }}...{{ substr($deposit->transaction_hash, -6) }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-white text-sm">
                                            {{ number_format($deposit->amount, 2) }}
                                            <span class="text-[10px] text-slate-500 font-bold uppercase">{{ getSetting('currency') }}</span>
                                        </div>
                                        @if ($deposit->currency != getSetting('currency'))
                                            <div class="text-[10px] text-slate-500 font-medium italic mt-0.5">
                                                ≈ {{ number_format($deposit->converted_amount, 6) }} {{ $deposit->currency }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-white/[0.03] border border-white/8 text-[11px] font-bold text-white">
                                            {{ $deposit->gatewayName() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-white font-medium text-xs">{{ $deposit->created_at->format('M d, Y') }}</div>
                                        <div class="text-slate-500 text-[10px] font-mono mt-0.5">{{ $deposit->created_at->format('H:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @php
                                            $stMap = [
                                                'pending'   => ['clr' => 'amber',   'label' => __('Pending')],
                                                'completed' => ['clr' => 'emerald', 'label' => __('Completed')],
                                                'failed'    => ['clr' => 'rose',    'label' => __('Failed')],
                                                'partial_payment' => ['clr' => 'orange', 'label' => __('Partial')],
                                            ];
                                            $st = $stMap[$deposit->status] ?? ['clr' => 'slate', 'label' => __($deposit->status)];
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border bg-{{ $st['clr'] }}-500/10 text-{{ $st['clr'] }}-400 border-{{ $st['clr'] }}-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-{{ $st['clr'] }}-400 {{ $deposit->status === 'pending' ? 'animate-pulse' : '' }}"></span>
                                            {{ $st['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('user.deposits.view', $deposit->transaction_reference) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-white/[0.03] hover:bg-white/10 text-slate-400 hover:text-white transition-all border border-white/5"
                                           title="{{ __('View Receipt') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                            <div class="w-14 h-14 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mb-4 text-slate-500">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v2m16 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2v-1m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                            </div>
                                            <h4 class="text-white font-bold text-base mb-1">{{ __('No Deposits Found') }}</h4>
                                            <p class="text-slate-500 text-xs mb-6">{{ __('There are no deposit records associated with your account yet.') }}</p>
                                            <a href="{{ route('user.deposits.new') }}"
                                               class="px-5 py-2.5 bg-accent-primary text-black font-black rounded-xl text-xs transition-all shadow-lg">
                                                {{ __('Make Your First Deposit') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($deposits->hasPages())
                    <div class="p-6 border-t border-white/5 ajax-pagination">
                        {{ $deposits->links('templates.york.blades.partials.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
{{-- See all returned data for this page from the index() function on DepositController --}}
@section('scripts')
    {{-- jQuery (Required for AJAX) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check for scroll params on normal load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('page') || urlParams.has('search')) {
                const element = document.getElementById('deposits-history-wrapper');
                if (element) {
                    element.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }

            // AJAX Pagination Logic
            $(document).on('click', '.ajax-pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');

                // Show spinner
                $('#deposits-loading-spinner').removeClass('hidden');

                // Scroll to top of table
                $('html, body').animate({
                    scrollTop: $("#deposits-history-wrapper").offset().top - 100
                }, 500);

                $.get(url, function(data) {
                    // Extract content
                    var newContent = $(data).find('#deposits-table-content').html();

                    // Update DOM
                    $('#deposits-table-content').html(newContent);
                }).always(function() {
                    // Hide spinner
                    setTimeout(function() {
                        $('#deposits-loading-spinner').addClass('hidden');
                    }, 300);
                });
            });

            // Load More / Show Less Toggle for Distribution Stats
            $(document).on('click', '.toggle-load-more', function() {
                const $btn = $(this);
                const $container = $($btn.data('target'));
                const isExpanded = $btn.attr('data-expanded') === 'true';

                if (isExpanded) {
                    // Hide items beyond first 3
                    $container.children().each(function(index) {
                        if (index > 2) $(this).addClass('hidden');
                    });
                    $btn.attr('data-expanded', 'false');
                    $btn.removeAttr('data-expanded'); // Reset using group-data-[expanded]
                    $btn.find('.label').text("{{ __('Load More') }}");
                } else {
                    // Show all items
                    $container.children().removeClass('hidden');
                    $btn.attr('data-expanded', 'true');
                    $btn.find('.label').text("{{ __('Show Less') }}");
                }
            });
        });
    </script>
@endsection
