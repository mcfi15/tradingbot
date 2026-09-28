@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-12 pb-24">

    {{-- Global Ethereal Ambient Mesh Gradients --}}
    <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-emerald-500/5 via-cyan-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-12">

        {{-- ══ HEADER HERO ════════════════════════════════════════════════════ --}}
        <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                 style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">
                
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div class="max-w-2xl space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary shadow-[0_0_15px_rgba(226,177,60,0.2)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                            <span>{{ __('Transactions') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                            {{ __('Transaction History') }}
                        </h1>

                        <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                            {{ __('Complete record of your deposits, withdrawals, trading profits, and referral rewards.') }}
                        </p>
                    </div>

                    {{-- Quick Export CTA --}}
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('user.transactions', array_merge(request()->all(), ['export' => 'csv'])) }}" target="_blank"
                           class="px-6 py-3.5 rounded-full bg-white/5 hover:bg-white/10 text-white font-bold text-xs uppercase tracking-wider border border-white/10 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>{{ __('Export CSV') }}</span>
                        </a>
                        <a href="{{ route('user.transactions', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank"
                           class="px-6 py-3.5 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(226,177,60,0.3)] active:scale-[0.98] flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                            <span>{{ __('Export PDF') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ MONEY FLOW HUD CARDS (3 CARDS GRID) ════════════════════════════ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Net Cash Flow Card --}}
            <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-3" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Net Cash Flow') }}</span>
                    <div class="text-3xl sm:text-4xl font-black font-mono tracking-tight {{ $netFlow >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $netFlow >= 0 ? '+' : '' }}{{ number_format($netFlow, getSetting('decimal_places', 2)) }}
                        <span class="text-xs font-normal text-slate-500 font-sans">{{ getSetting('currency') }}</span>
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium pt-2 border-t border-white/5 flex items-center justify-between">
                        <span>{{ __('Account Balance:') }}</span>
                        <strong class="text-white font-bold font-mono">{{ number_format($currentBalance, getSetting('decimal_places', 2)) }} {{ getSetting('currency') }}</strong>
                    </div>
                </div>
            </div>

            {{-- Total Credits Card --}}
            <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-3" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Income (Credits)') }}</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-emerald-400 font-mono tracking-tight">
                        +{{ number_format($moneyFlow->total_credits, getSetting('decimal_places', 2)) }}
                        <span class="text-xs font-normal text-slate-500 font-sans">{{ getSetting('currency') }}</span>
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium pt-2 border-t border-white/5">
                        <span>{{ __('Deposits, trading profits, and referral rewards') }}</span>
                    </div>
                </div>
            </div>

            {{-- Total Debits Card --}}
            <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-3" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Expenses (Debits)') }}</span>
                        <span class="w-2 h-2 rounded-full bg-red-400"></span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-red-400 font-mono tracking-tight">
                        -{{ number_format($moneyFlow->total_debits, getSetting('decimal_places', 2)) }}
                        <span class="text-xs font-normal text-slate-500 font-sans">{{ getSetting('currency') }}</span>
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium pt-2 border-t border-white/5">
                        <span>{{ __('Withdrawals, bot investments, and transfers') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ BENTO GRID TELEMETRY METRICS ═══════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Activity Breakdown --}}
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-3" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Transaction Activity') }}</span>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between"><span class="text-slate-400">{{ __('Today') }}</span><span class="text-white font-bold font-mono">{{ $activity->today_count }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">{{ __('This Week') }}</span><span class="text-white font-bold font-mono">{{ $activity->week_count }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">{{ __('This Month') }}</span><span class="text-white font-bold font-mono">{{ $activity->month_count }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Credit/Debit Ratio --}}
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-3" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Credit vs Debit') }}</span>
                    <div class="h-2 bg-white/10 rounded-full overflow-hidden flex">
                        <div class="h-full bg-emerald-500" style="width: {{ $creditRatio }}%"></div>
                        <div class="h-full bg-red-500" style="width: {{ $debitRatio }}%"></div>
                    </div>
                    <div class="flex justify-between text-[11px] font-mono font-bold">
                        <span class="text-emerald-400">{{ round($creditRatio) }}% {{ __('Credit') }}</span>
                        <span class="text-red-400">{{ round($debitRatio) }}% {{ __('Debit') }}</span>
                    </div>
                </div>
            </div>

            {{-- Operational Health --}}
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-3" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Status Overview') }}</span>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between"><span class="text-slate-400">{{ __('Pending') }}</span><span class="text-yellow-400 font-bold font-mono">{{ $statusCounts->pending_count }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">{{ __('Success Rate') }}</span><span class="text-emerald-400 font-bold font-mono">{{ round($successRate) }}%</span></div>
                    </div>
                </div>
            </div>

            {{-- Transaction Sizes --}}
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-3" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Average Amounts') }}</span>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between"><span class="text-slate-400">{{ __('Avg Deposit') }}</span><span class="text-emerald-400 font-bold font-mono">{{ number_format($sizeStats->avg_credit, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">{{ __('Avg Withdrawal') }}</span><span class="text-red-400 font-bold font-mono">{{ number_format($sizeStats->avg_debit, 2) }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ TRANSACTION HISTORY TABLE ══════════════════════════════════════ --}}
        <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl overflow-hidden" id="transactions-wrapper">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                
                {{-- Table Filter Controls --}}
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-white/5">
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tight">{{ __('All Transactions') }}</h3>
                        <p class="text-xs text-slate-400 font-medium mt-1">{{ __('Search and filter your transactions.') }}</p>
                    </div>

                    <form id="filter-form" action="{{ route('user.transactions') }}" method="GET" class="flex flex-wrap items-center gap-3">
                        <input type="text" name="search" placeholder="{{ __('Search reference or description...') }}"
                            value="{{ request('search') }}"
                            class="bg-black/50 border border-white/10 rounded-full px-5 py-2.5 text-xs text-white outline-none focus:border-accent-primary transition-all placeholder:text-slate-500 min-w-[200px]">

                        {{-- Type Filter --}}
                        <select name="type" onchange="this.form.submit()" class="bg-black/50 border border-white/10 rounded-full px-5 py-2.5 text-xs text-white outline-none focus:border-accent-primary transition-all">
                            <option value="all" {{ request('type', 'all') == 'all' ? 'selected' : '' }}>{{ __('All Types') }}</option>
                            <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>{{ __('Income (Credit)') }}</option>
                            <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>{{ __('Expense (Debit)') }}</option>
                        </select>

                        {{-- Status Filter --}}
                        <select name="status" onchange="this.form.submit()" class="bg-black/50 border border-white/10 rounded-full px-5 py-2.5 text-xs text-white outline-none focus:border-accent-primary transition-all">
                            <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>{{ __('All Statuses') }}</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                        </select>

                        <button type="submit" class="px-6 py-2.5 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_0_15px_rgba(226,177,60,0.2)] active:scale-[0.97]">
                            {{ __('Filter') }}
                        </button>
                    </form>
                </div>

                {{-- Table --}}
                <div id="table-content" class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-[10px] text-slate-500 uppercase tracking-[0.2em] font-black">
                                <th class="px-4 py-3">{{ __('Reference') }}</th>
                                <th class="px-4 py-3">{{ __('Description') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Amount') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Date') }}</th>
                                <th class="px-4 py-3 text-center">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            @forelse($transactions as $tx)
                                <tr class="bg-white/[0.02] border border-white/5 rounded-2xl hover:bg-white/[0.05] transition-all">
                                    <td class="px-4 py-4 font-black font-mono text-white">
                                        {{ $tx->tnx_id ?? $tx->transaction_id ?? $tx->reference ?? '#' . $tx->id }}
                                    </td>
                                    <td class="px-4 py-4 font-bold text-slate-300">
                                        {{ $tx->description ?? $tx->remark ?? __('Transaction') }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-mono font-bold {{ (isset($tx->type) && strtolower($tx->type) == 'credit') || (isset($tx->tx_type) && $tx->tx_type == '+') ? 'text-emerald-400' : 'text-red-400' }}">
                                        {{ (isset($tx->type) && strtolower($tx->type) == 'credit') || (isset($tx->tx_type) && $tx->tx_type == '+') ? '+' : '-' }}{{ showAmount($tx->amount) }}
                                    </td>
                                    <td class="px-4 py-4 text-right text-slate-400 font-medium">
                                        {{ date('M d, Y H:i', strtotime($tx->created_at)) }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @php
                                            $st = strtolower($tx->status ?? 'completed');
                                            $statusClasses = [
                                                'completed' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                                'approved' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                                'pending' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                                'failed' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                            ];
                                            $cls = $statusClasses[$st] ?? 'bg-white/5 text-slate-400 border-white/10';
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $cls }}">
                                            {{ ucfirst($st) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-xs italic font-medium">
                                        {{ __('No transactions found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
