@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
    <div class="min-h-screen relative space-y-12 pb-24">
        <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>

        <div class="relative z-10 max-w-7xl mx-auto space-y-12">

            {{-- ══ HERO ══════════════════════════════════════════════════════════ --}}
            <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                     style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">

                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                        <div class="max-w-2xl space-y-4">
                            <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary">
                                <span class="w-1.5 h-1.5 rounded-full bg-accent-primary"></span>
                                <span>{{ __('Audit Trail') }}</span>
                            </div>

                            <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                                {{ __('Trade Logs') }}
                            </h1>

                            <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                                {{ __('Every order attempt, including the ones the exchange rejected and why.') }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl shrink-0">
                            <a href="{{ route('user.trading.orders.index') }}"
                               class="px-5 py-2.5 rounded-full bg-accent-primary text-black font-black text-xs uppercase tracking-wider transition-all">
                                {{ __('Ticket') }}
                            </a>
                            <a href="{{ route('user.trading.signals.index') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Signals') }}
                            </a>
                            <a href="{{ route('user.trading.preferences.index') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Risk') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ FILTERS ═══════════════════════════════════════════════════════ --}}
            <form method="GET" action="{{ route('user.trading.orders.logs') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div>
                    <label for="log-level" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                        {{ __('Level') }}
                    </label>
                    <select id="log-level" name="level"
                        class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-bold text-white focus:border-accent-primary focus:outline-none">
                        <option value="">{{ __('All') }}</option>
                        <option value="problems" @selected(request('level') === 'problems')>{{ __('Errors only') }}</option>
                    </select>
                </div>

                <div>
                    <label for="log-pair" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                        {{ __('Pair') }}
                    </label>
                    <input type="text" id="log-pair" name="pair" value="{{ request('pair') }}" placeholder="BTCUSDT"
                        class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-mono font-bold text-white placeholder:text-slate-600 focus:border-accent-primary focus:outline-none uppercase">
                </div>

                <div class="flex items-end gap-3">
                    <button type="submit"
                        class="flex-1 px-5 py-3 rounded-xl bg-accent-primary text-black font-black text-[10px] uppercase tracking-widest hover:bg-accent-primary/90 active:scale-95 transition-all">
                        {{ __('Filter') }}
                    </button>

                    @if (request('level') || request('pair'))
                        <a href="{{ route('user.trading.orders.logs') }}"
                            class="px-5 py-3 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:bg-white/10 transition-all">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>

            {{-- ══ LOG TABLE ═════════════════════════════════════════════════════ --}}
            <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-6 sm:p-8 overflow-hidden">

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-[9px] text-slate-500 uppercase tracking-[0.2em] font-black">
                                    <th class="px-4 py-2 text-left">{{ __('Level') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('Event') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('Details') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('Context') }}</th>
                                    <th class="px-4 py-2 text-right">{{ __('When') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    @php
                                        $tone = match ($log->level) {
                                            'critical' => ['text-rose-400', 'bg-rose-500/10', 'border-rose-500/20'],
                                            'error' => ['text-rose-400', 'bg-rose-500/10', 'border-rose-500/20'],
                                            'warning' => ['text-amber-400', 'bg-amber-500/10', 'border-amber-500/20'],
                                            default => ['text-slate-400', 'bg-white/5', 'border-white/10'],
                                        };
                                    @endphp

                                    <tr class="group bg-white/[0.02] hover:bg-white/[0.04] border border-white/5 transition-all">

                                        <td class="px-4 py-4 first:rounded-l-xl border-y border-l border-white/5">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border {{ $tone[1] }} {{ $tone[0] }} {{ $tone[2] }}">
                                                {{ $log->level }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4 border-y border-white/5">
                                            <div class="text-[11px] font-black text-white font-mono">
                                                {{ $log->action ?: '—' }}
                                            </div>
                                            <div class="text-[9px] text-slate-500 font-mono uppercase tracking-widest mt-0.5">
                                                {{ $log->pair ?: __('No pair') }}
                                            </div>
                                        </td>

                                        <td class="px-4 py-4 border-y border-white/5 max-w-md">
                                            <p class="text-[11px] font-bold text-slate-200 leading-relaxed break-words">
                                                {{ $log->message }}
                                            </p>

                                            @if (!empty($log->context))
                                                <details class="mt-2 group/details">
                                                    <summary class="cursor-pointer text-[9px] font-black uppercase tracking-widest text-slate-500 hover:text-slate-300 transition-colors">
                                                        {{ __('Raw Context') }}
                                                    </summary>
                                                    <pre class="mt-2 p-3 rounded-xl bg-black/40 border border-white/8 text-[9px] font-mono text-slate-400 overflow-x-auto whitespace-pre-wrap break-all">{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                </details>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4 border-y border-white/5">
                                            <div class="flex flex-wrap items-center gap-2">
                                                @if ($log->exchange)
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-white/5 text-slate-300 border border-white/10">
                                                        {{ $log->exchange }}
                                                    </span>
                                                @endif

                                                @if ($log->market_type)
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-white/5 text-slate-500 border border-white/10">
                                                        {{ $log->market_type }}
                                                    </span>
                                                @endif

                                                @if ($log->trade_order_id)
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-purple-400/10 text-purple-400 border border-purple-400/20 font-mono">
                                                        #{{ $log->trade_order_id }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-4 py-4 last:rounded-r-xl border-y border-r border-white/5 text-right whitespace-nowrap">
                                            <div class="text-[10px] font-bold text-slate-300">
                                                {{ $log->created_at->diffForHumans() }}
                                            </div>
                                            <div class="text-[9px] text-slate-600 font-mono mt-0.5">
                                                {{ $log->created_at->format('M d, H:i:s') }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center">
                                            <div class="w-14 h-14 mx-auto rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 mb-3">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M9 12h6m-6 4h6M9 8h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" />
                                                </svg>
                                            </div>
                                            <p class="text-xs font-bold text-slate-500">
                                                {{ __('No logs match these filters.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($logs->hasPages())
                        <div class="mt-8 flex justify-center">
                            {{ $logs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
