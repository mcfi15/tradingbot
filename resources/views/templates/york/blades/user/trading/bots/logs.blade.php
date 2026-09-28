@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html-to-image/1.11.11/html-to-image.min.js"></script>
@endpush
@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-12 pb-24">

    {{-- Global Ethereal Ambient Mesh Gradients --}}
    <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-cyan-500/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-accent-primary/5 via-emerald-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-12">

        {{-- ══ HEADER HERO & FLOATING ISLAND NAV DECK ══════════════════════════ --}}
        <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                 style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">
                
                {{-- Ambient Glow --}}
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div class="max-w-2xl space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-cyan-400 shadow-[0_0_15px_rgba(6,182,212,0.2)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                            <span>{{ __('Trade Logs') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                            {{ __('Trade Logs') }}
                        </h1>

                        <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                            {{ __('View real-time trade executions, profit records, and performance for all your bots.') }}
                        </p>
                    </div>

                    {{-- Floating Island Navigation Sub-Pills --}}
                    <div class="flex flex-wrap items-center gap-2.5 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl shrink-0">
                        <a href="{{ route('user.trading-bots.index') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                            <span>{{ __('Marketplace') }}</span>
                        </a>

                        <a href="{{ route('user.trading-bots.activations') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                            <span>{{ __('Active') }}</span>
                        </a>

                        <a href="{{ route('user.trading-bots.daily-summary') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                            <span>{{ __('Daily Summary') }}</span>
                        </a>

                        <a href="{{ route('user.trading-bots.logs') }}"
                           class="px-5 py-2.5 rounded-full bg-cyan-500/20 text-white font-black text-xs uppercase tracking-wider border border-cyan-500/30 shadow-[0_0_20px_rgba(6,182,212,0.25)] transition-all flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                            <span>{{ __('Logs') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ STATS HUD GRID ═══════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Profit') }}</span>
                    <div class="text-3xl font-black text-emerald-400 font-mono tracking-tight">+{{ showAmount($stats['total_profit']) }}</div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Today\'s Profit') }}</span>
                    <div class="text-3xl font-black text-white font-mono tracking-tight">+{{ showAmount($stats['today_profit']) }}</div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Active Bots') }}</span>
                    <div class="text-3xl font-black text-purple-400 font-mono tracking-tight">{{ $stats['active_bots'] }}</div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Trades') }}</span>
                    <div class="text-3xl font-black text-cyan-400 font-mono tracking-tight">{{ number_format($stats['total_trades']) }}</div>
                </div>
            </div>
        </div>

        {{-- ══ LOGS TABLE ═══════════════════════════════════════════════════════ --}}
        <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl overflow-hidden">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 space-y-8" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-white/5">
                    <div>
                        <h3 class="text-2xl font-black text-white tracking-tight">{{ __('Trade Execution Telemetry') }}</h3>
                        <p class="text-xs text-slate-400 font-medium mt-1">{{ __('Real-time log of all bot executions and profit distributions.') }}</p>
                    </div>

                    {{-- Search Input --}}
                    <div class="relative w-full md:w-80">
                        <input type="text" id="log-search"
                            class="w-full bg-black/50 border border-white/10 rounded-full px-6 py-3 text-xs text-white focus:border-cyan-400 outline-none transition-all placeholder:text-slate-500"
                            placeholder="{{ __('Search pair, exchange...') }}">
                    </div>
                </div>

                <div id="logs-container" class="relative">
                    <div id="table-loading"
                        class="absolute inset-0 z-20 bg-black/60 backdrop-blur-md flex items-center justify-center opacity-0 pointer-events-none transition-opacity">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-10 h-10 border-2 border-cyan-400 border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ __('Filtering Telemetry...') }}</span>
                        </div>
                    </div>
                    
                    <div id="logs-table-container">
                        @include(
                            'templates.' . config('site.template') . '.blades.user.trading.bots.partials.logs_table',
                            ['logs' => $logs]
                        )
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
