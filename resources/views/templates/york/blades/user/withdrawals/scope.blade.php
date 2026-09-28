@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-12 pb-24">

    {{-- Global Ethereal Ambient Mesh Gradients --}}
    <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-emerald-500/5 via-cyan-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-12">

        {{-- ══ HEADER HERO & SCOPED NAV DECK ═══════════════════════════════════ --}}
        <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                 style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">
                
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div class="max-w-2xl space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary shadow-[0_0_15px_rgba(226,177,60,0.2)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                            <span>{{ __('Filtered View') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                            {{ $page_title }}
                        </h1>

                        <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                            {{ __('Viewing all :status withdrawal records and transaction details associated with your account.', ['status' => __($scopeName)]) }}
                        </p>
                    </div>

                    {{-- Floating Island Navigation Sub-Pills & New Payout CTA --}}
                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <div class="flex items-center gap-2 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl">
                            <a href="{{ route('user.withdrawals.index') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                <span>{{ __('Overview') }}</span>
                            </a>
                            <a href="{{ route('user.withdrawals.pending') }}"
                               class="px-5 py-2.5 rounded-full {{ request()->routeIs('user.withdrawals.pending') ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' : 'hover:bg-white/10 text-slate-300' }} font-bold text-xs uppercase tracking-wider transition-all">
                                <span>{{ __('Pending') }}</span>
                            </a>
                            <a href="{{ route('user.withdrawals.approved') }}"
                               class="px-5 py-2.5 rounded-full {{ request()->routeIs('user.withdrawals.approved') ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'hover:bg-white/10 text-slate-300' }} font-bold text-xs uppercase tracking-wider transition-all">
                                <span>{{ __('Completed') }}</span>
                            </a>
                        </div>

                        <a href="{{ route('user.withdrawals.new') }}"
                           class="px-7 py-4 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_0_30px_rgba(226,177,60,0.35)] active:scale-[0.97] flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>{{ __('New Withdrawal') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ FILTER BAR ═════════════════════════════════════════════════════ --}}
        <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-6 space-y-6" style="background: rgba(8,10,15,0.95);">
                <form id="scope-filter-form" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-4 w-full">
                    <div class="relative flex-1 min-w-[240px]">
                        <input type="text" name="search" placeholder="{{ __('Search by reference or amount...') }}"
                            value="{{ request('search') }}"
                            class="w-full bg-black/50 border border-white/10 rounded-full px-6 py-3 text-xs text-white outline-none focus:border-accent-primary transition-all placeholder:text-slate-500">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="date" name="from_date" value="{{ request('from_date') }}"
                            class="px-4 py-2.5 bg-black/50 border border-white/10 rounded-full text-xs text-white outline-none focus:border-accent-primary transition-all">
                        <span class="text-slate-500 text-xs font-bold uppercase">{{ __('To') }}</span>
                        <input type="date" name="to_date" value="{{ request('to_date') }}"
                            class="px-4 py-2.5 bg-black/50 border border-white/10 rounded-full text-xs text-white outline-none focus:border-accent-primary transition-all">
                    </div>

                    <button type="submit"
                        class="px-6 py-3 bg-accent-primary hover:bg-accent-primary/90 text-black text-xs font-black uppercase tracking-wider rounded-full transition-all active:scale-95 shadow-lg shadow-accent-primary/20 cursor-pointer">
                        {{ __('Filter') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- ══ SCOPED TRANSACTIONS TABLE ═══════════════════════════════════════ --}}
        <div id="withdrawals-history-wrapper" class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl overflow-hidden">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-[10px] text-slate-500 uppercase tracking-[0.2em] font-black">
                                <th class="px-4 py-3">{{ __('Reference ID') }}</th>
                                <th class="px-4 py-3">{{ __('Source / Method') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Amount') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Date') }}</th>
                                <th class="px-4 py-3 text-center">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            @forelse($withdrawals as $withdrawal)
                                <tr class="bg-white/[0.02] border border-white/5 rounded-2xl hover:bg-white/[0.05] transition-all">
                                    <td class="px-4 py-4 font-black font-mono text-white">
                                        <a href="{{ route('user.withdrawals.view', $withdrawal->transaction_reference) }}" class="hover:text-accent-primary transition-colors">
                                            {{ $withdrawal->transaction_reference }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 font-bold text-slate-300">
                                        {{ $withdrawal->gatewayName() }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-mono font-bold text-white">
                                        {{ showAmount($withdrawal->amount) }}
                                    </td>
                                    <td class="px-4 py-4 text-right text-slate-400 font-medium">
                                        {{ date('M d, Y H:i', strtotime($withdrawal->created_at)) }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @php
                                            $statusClasses = [
                                                'approved' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                                'completed' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                                'pending' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                                'rejected' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                                'failed' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                            ];
                                            $st = strtolower($withdrawal->status);
                                            $cls = $statusClasses[$st] ?? 'bg-white/5 text-slate-400 border-white/10';
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $cls }}">
                                            {{ ucfirst($withdrawal->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-xs italic font-medium">
                                        {{ __('No withdrawal records found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $withdrawals->links() }}
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
