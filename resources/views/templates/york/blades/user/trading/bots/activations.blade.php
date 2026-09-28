@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
<style>
@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}
.animate-shimmer {
    animation: shimmer 3s infinite linear;
}
</style>

<div class="min-h-screen relative space-y-12 pb-24">

    {{-- Global Ambient Mesh Gradients --}}
    <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-purple-600/10 via-accent-primary/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-cyan-500/5 via-emerald-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-12">

        {{-- ══ HEADER HERO & FLOATING ISLAND NAV DECK ══════════════════════════ --}}
        <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                 style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">
                
                {{-- Ambient Glow --}}
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div class="max-w-2xl space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-purple-500/30 bg-purple-500/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-purple-400 shadow-[0_0_15px_rgba(168,85,247,0.2)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
                            <span>{{ __('Active Bots') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                            {{ __('Active Bots') }}
                        </h1>

                        <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                            {{ __('Track your active bots, profits, and trade executions in real time.') }}
                        </p>
                    </div>

                    {{-- Floating Island Navigation Sub-Pills --}}
                    <div class="flex flex-wrap items-center gap-2.5 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl shrink-0">
                        <a href="{{ route('user.trading-bots.index') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                            <span>{{ __('Marketplace') }}</span>
                        </a>

                        <a href="{{ route('user.trading-bots.activations') }}"
                           class="px-5 py-2.5 rounded-full bg-purple-500/20 text-white font-black text-xs uppercase tracking-wider border border-purple-500/30 shadow-[0_0_20px_rgba(168,85,247,0.25)] transition-all flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                            <span>{{ __('Active') }}</span>
                            <span class="px-2 py-0.5 rounded-full bg-purple-400 text-black text-[10px] font-black">{{ $activations->total() }}</span>
                        </a>

                        <a href="{{ route('user.trading-bots.daily-summary') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                            <span>{{ __('Daily Summary') }}</span>
                        </a>

                        <a href="{{ route('user.trading-bots.logs') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                            <span>{{ __('Logs') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ FLEET METRIC HUD (TOP ROW) ═══════════════════════════════════════ --}}
        @php
            $totalActiveCapital = $activations->sum('amount');
            $totalAccruedProfit = $activations->sum('returned_profit');
            $activeCount = $activations->where('status', 'active')->count();
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-1" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Invested') }}</span>
                    <div class="text-3xl font-black text-white font-mono tracking-tight">{{ showAmount($totalActiveCapital) }}</div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-1" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Profit') }}</span>
                    <div class="text-3xl font-black text-emerald-400 font-mono tracking-tight">+{{ showAmount($totalAccruedProfit) }}</div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-1" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Active Bots') }}</span>
                    <div class="text-3xl font-black text-purple-400 font-mono tracking-tight">{{ $activeCount }} {{ __('Active') }}</div>
                </div>
            </div>
        </div>

        {{-- ══ ACTIVATION DEPLOYMENTS LIST ══════════════════════════════════════ --}}
        <div class="space-y-6">
            @forelse ($activations as $activation)
                <div class="group relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 transition-all duration-500 hover:border-purple-500/40 hover:shadow-[0_20px_50px_rgba(0,0,0,0.9)]">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-10 space-y-8 overflow-hidden relative"
                         style="background: radial-gradient(circle at 90% 10%, rgba(168,85,247,0.06) 0%, rgba(6,8,12,0.99) 70%);">
                        
                        {{-- Top Header Row: Strategy Identity & Status --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-white/5">
                            <div class="flex items-center gap-5 min-w-0">
                                <div class="relative shrink-0">
                                    <div class="absolute -inset-1.5 bg-gradient-to-tr from-purple-500 to-accent-primary rounded-2xl blur opacity-25 group-hover:opacity-75 transition-opacity"></div>
                                    <img src="{{ asset('assets/images/bots/' . $activation->bot->logo) }}"
                                         alt="{{ $activation->bot->name }}"
                                         class="relative w-16 h-16 rounded-2xl object-cover border border-white/10 shadow-2xl">
                                </div>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-2xl font-black text-white group-hover:text-purple-400 transition-colors tracking-tight truncate">
                                            {{ $activation->bot->name }}
                                        </h3>
                                        <span class="px-3 py-0.5 rounded-full bg-purple-500/10 border border-purple-500/20 text-[9px] font-bold text-purple-400 uppercase tracking-widest shrink-0">
                                            {{ strtoupper($activation->bot->type) }}
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-4 mt-2">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold {{ $activation->status === 'active' ? 'text-emerald-400' : 'text-slate-400' }}">
                                            <span class="w-2 h-2 rounded-full {{ $activation->status === 'active' ? 'bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.7)] animate-pulse' : 'bg-slate-500' }}"></span>
                                            {{ ucfirst($activation->status) }}
                                        </span>
                                        <span class="text-xs text-slate-500 font-medium">
                                            {{ __('Started:') }} <strong class="text-white font-bold">{{ date('M d, Y H:i', $activation->start_date) }}</strong>
                                        </span>
                                        <div class="hidden sm:block h-1 w-1 rounded-full bg-white/20"></div>
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/[0.03] border border-white/8 text-[10px] font-black text-slate-300">
                                            <svg class="w-3.5 h-3.5 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            <span>{{ number_format($activation->logs_count) }} {{ __('Trades') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <a href="{{ route('user.trading-bots.logs') }}"
                                   class="px-5 py-2.5 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ __('View Logs') }}</span>
                                </a>
                            </div>
                        </div>

                        {{-- Middle Financial Stats Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="p-5 rounded-2xl bg-black/40 border border-white/5 space-y-1">
                                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest block">{{ __('Invested Amount') }}</span>
                                <div class="text-2xl font-black text-white font-mono tracking-tight">{{ showAmount($activation->amount) }}</div>
                            </div>

                            <div class="p-5 rounded-2xl bg-black/40 border border-white/5 space-y-1">
                                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest block">{{ __('Total Profit') }}</span>
                                <div class="flex items-baseline gap-2">
                                    <div class="text-2xl font-black text-emerald-400 font-mono tracking-tight">+{{ showAmount($activation->returned_profit) }}</div>
                                    @if ($activation->amount > 0)
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold font-mono">
                                            +{{ number_format(($activation->returned_profit / $activation->amount) * 100, 2) }}%
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-5 rounded-2xl bg-black/40 border border-white/5 space-y-1">
                                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest block">{{ __('End Date') }}</span>
                                <div class="text-xl font-black text-white tracking-tight">
                                    @if ($activation->end_date > 0)
                                        {{ date('M d, Y', $activation->end_date) }}
                                    @else
                                        <span class="text-purple-400">{{ __('Ongoing') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Progress Timeline Meter --}}
                        @if ($activation->status === 'active' && $activation->end_date > 0)
                            @php
                                $total = $activation->end_date - $activation->start_date;
                                $elapsed = time() - $activation->start_date;
                                $percent = min(100, max(0, ($elapsed / max(1, $total)) * 100));
                            @endphp
                            <div class="pt-4 border-t border-white/5 space-y-2">
                                <div class="flex justify-between items-center text-xs font-bold">
                                    <span class="text-slate-400 uppercase tracking-wider text-[10px]">{{ __('Duration Progress') }}</span>
                                    <span class="text-purple-400 font-mono">{{ round($percent) }}%</span>
                                </div>
                                <div class="h-2.5 w-full bg-black/50 rounded-full overflow-hidden border border-white/5">
                                    <div class="h-full bg-gradient-to-r from-accent-primary via-purple-500 to-emerald-400 bg-[length:200%_100%] animate-shimmer rounded-full transition-all duration-1000"
                                         style="width: {{ $percent }}%"></div>
                                </div>
                                <div class="flex justify-between text-[10px] font-mono text-slate-500">
                                    <span>{{ date('M d, Y', $activation->start_date) }}</span>
                                    <span>{{ date('M d, Y', $activation->end_date) }}</span>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @empty
                <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 max-w-lg mx-auto text-center p-12">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 mb-4">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ __('No Active Bots') }}</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed mb-6">
                        {{ __('You do not have any active bots running. Visit the marketplace to start your first trading bot.') }}
                    </p>
                    <a href="{{ route('user.trading-bots.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_4px_20px_rgba(226,177,60,0.3)]">
                        {{ __('View Bots') }}
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $activations->links() }}
        </div>

    </div>
</div>
@endsection
