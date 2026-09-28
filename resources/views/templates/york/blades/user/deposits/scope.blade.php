@extends('templates.york.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-8">

    {{-- Global ambient background blur --}}
    <div class="fixed top-0 right-0 w-[40rem] h-[40rem] bg-accent-primary/5 rounded-full blur-[120px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[30rem] h-[30rem] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-8">

        {{-- ══ HEADER DECK ══════════════════════════════════════════════════════ --}}
        @php
            $statusColors = [
                'completed' => 'emerald',
                'pending' => 'amber',
                'failed' => 'rose',
                'partial_payment' => 'orange',
            ];
            $safeStatus = isset($status) && isset($statusColors[$status]) ? $status : 'completed';
            $color = $statusColors[$safeStatus];
        @endphp

        <div class="relative rounded-2xl overflow-hidden p-6 md:p-8"
             style="background: linear-gradient(145deg, rgba(8,9,14,0.97) 0%, rgba(5,6,10,0.99) 100%); border: 1px solid rgba(255,255,255,0.06);">
            
            <div class="absolute top-0 right-0 w-80 h-80 bg-{{ $color }}-500/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-400 animate-pulse"></span>
                        <span class="text-[9px] font-bold uppercase tracking-[0.25em] text-slate-500">{{ __('Filtered View') }}</span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-slate-500 tracking-tight leading-tight">
                        {{ $page_title }}
                    </h1>
                    <p class="text-slate-500 text-sm mt-2 font-medium max-w-xl">
                        {{ __('Viewing all :status deposit records and settlement receipts.', ['status' => __($scopeName)]) }}
                    </p>
                </div>

                {{-- Total Filtered Summary Card --}}
                <div id="summary-card-container">
                    <div class="p-4 rounded-xl border border-white/5 bg-white/[0.02] min-w-[200px] text-right">
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">
                            {{ __('Total :status', ['status' => __($scopeName)]) }}
                        </p>
                        <div class="text-2xl font-black text-white">
                            {{ number_format($totalAmount, 2) }}
                            <span class="text-xs font-bold text-{{ $color }}-400 uppercase">{{ getSetting('currency') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ FILTER BAR ═══════════════════════════════════════════════════════ --}}
        <div class="p-4 rounded-2xl border border-white/5 bg-white/[0.02] flex flex-wrap items-center gap-4">
            <form id="scope-filter-form" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-3 w-full">
                {{-- Search --}}
                <div class="relative flex-1 min-w-[220px]">
                    <input type="text" name="search" placeholder="{{ __('Search reference or amount...') }}"
                           value="{{ request('search') }}"
                           class="w-full bg-white/[0.03] border border-white/8 rounded-xl pl-9 pr-4 py-2.5 text-xs text-white placeholder:text-slate-600 focus:outline-none focus:border-accent-primary/60 transition-all">
                    <svg class="w-3.5 h-3.5 text-slate-600 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                {{-- Dates --}}
                <div class="flex items-center gap-2">
                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                           class="px-3 py-2 bg-white/[0.03] border border-white/8 rounded-xl text-xs text-white focus:outline-none focus:border-accent-primary/60 transition-all">
                    <span class="text-slate-600 text-[10px] uppercase font-bold">{{ __('To') }}</span>
                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                           class="px-3 py-2 bg-white/[0.03] border border-white/8 rounded-xl text-xs text-white focus:outline-none focus:border-accent-primary/60 transition-all">
                </div>

                {{-- Source Filter --}}
                <select name="method_id" onchange="this.form.submit()" class="bg-[#0a0b10] border border-white/8 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-accent-primary/60 cursor-pointer">
                    <option value="">{{ __('All Deposit Sources') }}</option>
                    @foreach($payment_methods as $pm)
                        <option value="{{ $pm->id }}" {{ request('method_id') == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-xl text-xs font-bold transition-all border border-white/10">
                    {{ __('Filter') }}
                </button>
            </form>
        </div>

        {{-- ══ DEPOSITS TABLE ═══════════════════════════════════════════════════ --}}
        <div id="deposits-history-wrapper" class="relative rounded-2xl overflow-hidden"
             style="background: linear-gradient(145deg, rgba(8,9,14,0.97) 0%, rgba(5,6,10,0.99) 100%); border: 1px solid rgba(255,255,255,0.06);">
            
            <div id="deposits-loading-spinner" class="hidden absolute inset-0 bg-black/80 backdrop-blur-sm z-50 flex flex-col items-center justify-center">
                <div class="w-8 h-8 border-2 border-accent-primary border-t-transparent rounded-full animate-spin"></div>
                <p class="mt-2 text-xs text-slate-400 font-bold animate-pulse">{{ __('Loading records...') }}</p>
            </div>

            <div id="deposits-table-content">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-white/5 bg-white/[0.01]">
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Transaction') }}</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Amount') }}</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Deposit Source') }}</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Date') }}</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-xs">
                            @forelse($deposits as $deposit)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-{{ $color }}-500/10 text-{{ $color }}-400 border border-{{ $color }}-500/20 flex items-center justify-center shrink-0">
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
                                            {{ $deposit->paymentMethod->name ?? __('Unknown') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-white font-medium text-xs">{{ $deposit->created_at->format('M d, Y') }}</div>
                                        <div class="text-slate-500 text-[10px] font-mono mt-0.5">{{ $deposit->created_at->format('H:i A') }}</div>
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
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                            <div class="w-14 h-14 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mb-4 text-slate-500">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v2m16 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2v-1m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                            </div>
                                            <h4 class="text-white font-bold text-base mb-1">{{ __('No Records Found') }}</h4>
                                            <p class="text-slate-500 text-xs mb-6">{{ __('We couldn\'t find any records for :status deposits.', ['status' => strtolower(__($scopeName))]) }}</p>
                                            <a href="{{ route('user.deposits.new') }}"
                                               class="px-5 py-2.5 bg-accent-primary text-black font-black rounded-xl text-xs transition-all shadow-lg">
                                                {{ __('Make a Deposit') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($deposits->hasPages())
                    <div class="p-6 border-t border-white/5">
                        {{ $deposits->links('templates.york.blades.partials.pagination') }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $(document).on('click', '.ajax-pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $('#deposits-loading-spinner').removeClass('hidden');
        $('html, body').animate({ scrollTop: $("#deposits-history-wrapper").offset().top - 100 }, 500);
        $.get(url, function(data) {
            $('#deposits-table-content').html($(data).find('#deposits-table-content').html());
        }).always(function() {
            setTimeout(function() { $('#deposits-loading-spinner').addClass('hidden'); }, 300);
        });
    });
});
</script>
@endsection
