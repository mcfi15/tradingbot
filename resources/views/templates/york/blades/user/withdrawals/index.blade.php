@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-12 pb-24">

    {{-- Global Ethereal Ambient Mesh Gradients --}}
    <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-emerald-500/5 via-cyan-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-12">

        {{-- ══ HEADER HERO & FLOATING ISLAND NAV DECK ══════════════════════════ --}}
        <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                 style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">
                
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div class="max-w-2xl space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary shadow-[0_0_15px_rgba(226,177,60,0.2)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                            <span>{{ __('Withdrawals') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                            {{ __('Withdrawal History') }}
                        </h1>

                        <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                            {{ __('Track your withdrawal requests, processing times, and payout status.') }}
                        </p>
                    </div>

                    {{-- Floating Island Navigation Sub-Pills & New Payout CTA --}}
                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <div class="flex items-center gap-2 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl">
                            <a href="{{ route('user.withdrawals.index') }}"
                               class="px-5 py-2.5 rounded-full bg-accent-primary text-black font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(226,177,60,0.3)] transition-all">
                                <span>{{ __('Overview') }}</span>
                            </a>
                            <a href="{{ route('user.withdrawals.pending') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                <span>{{ __('Pending') }}</span>
                            </a>
                            <a href="{{ route('user.withdrawals.approved') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
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

        {{-- ══ ANALYTICS HUD GRID (4 CARDS) ═══════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('user.withdrawals.index') }}" class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8 hover:border-accent-primary/40 transition-all group">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Withdrawn') }}</span>
                    <div class="text-3xl font-black text-white font-mono tracking-tight">{{ number_format($withdrawals_analytics['total']['total'], 2) }} <span class="text-xs text-slate-500 font-sans">{{ getSetting('currency') }}</span></div>
                    <div class="text-[10px] text-slate-400 font-medium pt-1">{{ $withdrawals_analytics['total']['count'] }} {{ __('Total Transactions') }}</div>
                </div>
            </a>

            <a href="{{ route('user.withdrawals.pending') }}" class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8 hover:border-yellow-500/40 transition-all group">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Pending') }}</span>
                    <div class="text-3xl font-black text-yellow-400 font-mono tracking-tight">{{ number_format($withdrawals_analytics['pending']['total'], 2) }} <span class="text-xs text-slate-500 font-sans">{{ getSetting('currency') }}</span></div>
                    <div class="text-[10px] text-yellow-500/80 font-medium pt-1">{{ $withdrawals_analytics['pending']['count'] }} {{ __('Processing') }}</div>
                </div>
            </a>

            <a href="{{ route('user.withdrawals.approved') }}" class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8 hover:border-emerald-500/40 transition-all group">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Completed') }}</span>
                    <div class="text-3xl font-black text-emerald-400 font-mono tracking-tight">{{ number_format($withdrawals_analytics['completed']['total'], 2) }} <span class="text-xs text-slate-500 font-sans">{{ getSetting('currency') }}</span></div>
                    <div class="text-[10px] text-emerald-500/80 font-medium pt-1">{{ $withdrawals_analytics['completed']['count'] }} {{ __('Settled') }}</div>
                </div>
            </a>

            <a href="{{ route('user.withdrawals.failed') }}" class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8 hover:border-red-500/40 transition-all group">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Failed / Partial') }}</span>
                    <div class="text-3xl font-black text-red-400 font-mono tracking-tight">{{ number_format($withdrawals_analytics['failed']['total'] + $withdrawals_analytics['partial_payment']['total'], 2) }} <span class="text-xs text-slate-500 font-sans">{{ getSetting('currency') }}</span></div>
                    <div class="text-[10px] text-red-400/80 font-medium pt-1">{{ $withdrawals_analytics['failed']['count'] }} {{ __('Failed') }} • {{ $withdrawals_analytics['partial_payment']['count'] }} {{ __('Partial') }}</div>
                </div>
            </a>
        </div>

        {{-- ══ PAYOUT SPEED TELEMETRY ══════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-4" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                    <h3 class="text-base font-black text-white tracking-tight flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-accent-primary animate-pulse"></span>
                        {{ __('Processing Times') }}
                    </h3>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-black/40 border border-white/5">
                            <span class="text-xs text-slate-400 font-medium">{{ __('Average Processing Time') }}</span>
                            <span class="text-sm font-black text-white font-mono">
                                @if ($avg_processing_time)
                                    {{ $avg_processing_time < 60 ? round($avg_processing_time) . 's' : round($avg_processing_time / 60) . 'm' }}
                                @else
                                    --
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-black/40 border border-white/5">
                            <span class="text-xs text-slate-400 font-medium">{{ __('Fastest Execution') }}</span>
                            <span class="text-sm font-black text-emerald-400 font-mono">
                                @if ($fastest_withdrawal)
                                    @php $f_seconds = $fastest_withdrawal->created_at->diffInSeconds($fastest_withdrawal->updated_at); @endphp
                                    {{ $f_seconds < 60 ? $f_seconds . 's' : round($f_seconds / 60) . 'm' }}
                                @else
                                    --
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ WITHDRAWAL HISTORY TABLE ════════════════════════════════════ --}}
            <div class="lg:col-span-2 rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl overflow-hidden">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-8 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight">{{ __('Recent Withdrawals') }}</h3>
                            <p class="text-xs text-slate-400 font-medium mt-1">{{ __('Detailed records of your withdrawal requests.') }}</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-y-2">
                            <thead>
                                <tr class="text-[10px] text-slate-500 uppercase tracking-[0.2em] font-black">
                                    <th class="px-4 py-3">{{ __('Reference ID') }}</th>
                                    <th class="px-4 py-3">{{ __('Source / Method') }}</th>
                                    <th class="px-4 py-3 text-right">{{ __('Amount') }}</th>
                                    <th class="px-4 py-3 text-right">{{ __('Requested Date') }}</th>
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
                                            {{ __('No withdrawal transactions logged.') }}
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
</div>
@endsection
