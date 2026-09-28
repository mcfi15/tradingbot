@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

<style>
.flatpickr-calendar { background: #0b0e11 !important; border: 1px solid rgba(255,255,255,0.1) !important; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5) !important; }
.flatpickr-day.selected { background: var(--accent-primary) !important; border-color: var(--accent-primary) !important; color: #000 !important; font-weight: bold !important; }
</style>

<div class="min-h-screen relative space-y-12 pb-24">

    {{-- Global Ethereal Ambient Mesh Gradients --}}
    <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-emerald-500/10 via-accent-primary/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-purple-500/5 via-cyan-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-12">

        {{-- ══ HEADER HERO & FLOATING ISLAND NAV DECK ══════════════════════════ --}}
        <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                 style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">
                
                {{-- Radial Ambient Glow Node --}}
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div class="max-w-2xl space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-400 shadow-[0_0_15px_rgba(52,211,153,0.2)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>{{ __('Earnings Summary') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                            {{ __('Daily Summary') }}
                        </h1>

                        <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                            {{ __('View your daily bot profits, performance breakdown, and automated payouts.') }}
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
                           class="px-5 py-2.5 rounded-full bg-emerald-500/20 text-white font-black text-xs uppercase tracking-wider border border-emerald-500/30 shadow-[0_0_20px_rgba(16,185,129,0.25)] transition-all flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
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

        {{-- ══ STATS HUD METRICS (4 CARDS) ═════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Profit') }}</span>
                    <div class="text-3xl font-black text-emerald-400 font-mono tracking-tight">{{ showAmount($stats['total_profit']) }}</div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Avg. Daily Profit') }}</span>
                    <div class="text-3xl font-black text-white font-mono tracking-tight">{{ showAmount($stats['avg_daily_profit']) }}</div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Trades') }}</span>
                    <div class="text-3xl font-black text-purple-400 font-mono tracking-tight">{{ number_format($stats['total_trades']) }}</div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Active Bots') }}</span>
                    <div class="text-3xl font-black text-accent-primary font-mono tracking-tight">{{ $stats['total_activations'] }}</div>
                </div>
            </div>
        </div>

        {{-- ══ DISTRIBUTION CHARTS (DONUTS) ═════════════════════════════════════ --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between px-2">
                <div>
                    <h3 class="text-2xl font-black text-white tracking-tight">{{ __('Profit Breakdown') }}</h3>
                    <p class="text-xs text-slate-400 font-medium mt-1">{{ __('Breakdown of bot profits across pairs, exchanges, and categories.') }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- By Pair --}}
                <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-black text-white tracking-tight">{{ __('Trading Pairs') }}</h4>
                                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-500 mt-0.5">{{ __('Profit by Pair') }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-accent-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                            </div>
                        </div>
                        <div id="chart-pair-distribution" class="min-h-[280px]"></div>
                    </div>
                </div>

                {{-- By Exchange --}}
                <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-black text-white tracking-tight">{{ __('Exchanges') }}</h4>
                                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-500 mt-0.5">{{ __('Profit by Exchange') }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-purple-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2-2m14 0V9a2 2 0 01-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            </div>
                        </div>
                        <div id="chart-exchange-distribution" class="min-h-[280px]"></div>
                    </div>
                </div>

                {{-- By Type --}}
                <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-black text-white tracking-tight">{{ __('Bot Type') }}</h4>
                                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-500 mt-0.5">{{ __('Profit by Type') }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-emerald-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            </div>
                        </div>
                        <div id="chart-type-distribution" class="min-h-[280px]"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ DAILY SUMMARY CARDS & DATE FILTER ═══════════════════════════════ --}}
        <div class="space-y-6 relative">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-2">
                <div>
                    <h3 class="text-2xl font-black text-white tracking-tight">{{ __('Daily History') }}</h3>
                    <p class="text-xs text-slate-400 font-medium mt-1">{{ __('History of your daily bot profit payouts and active sessions.') }}</p>
                </div>
                
                {{-- Date Range Selector --}}
                <div class="flex items-center gap-3 bg-white/[0.03] border border-white/10 px-5 py-3 rounded-full shadow-xl">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <input type="text" id="date_range" class="bg-transparent border-none text-xs font-black text-white p-0 focus:ring-0 cursor-pointer min-w-[180px]" placeholder="{{ __('SELECT RANGE') }}" readonly>
                </div>
            </div>

            <div id="summaries-container" class="relative">
                <div id="loading-overlay" class="absolute inset-0 z-20 bg-black/60 backdrop-blur-md flex items-center justify-center rounded-[2.5rem] opacity-0 pointer-events-none transition-all duration-300">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-10 h-10 border-2 border-emerald-400 border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ __('Loading...') }}</span>
                    </div>
                </div>

                <div id="cards-wrapper">
                    @include('templates.' . config('site.template') . '.blades.user.trading.bots.partials.daily_cards', ['summaries' => $summaries])
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function() {
    const decimalPlaces = {{ getSetting('decimal_places', 2) }};
    const currencySymbol = '{{ $currency['symbol'] }}';
    let current_start_date = '{{ request('start_date') }}';
    let current_end_date = '{{ request('end_date') }}';

    const formatAmount = (val) => {
        return currencySymbol + parseFloat(val).toLocaleString('en-US', {
            minimumFractionDigits: decimalPlaces,
            maximumFractionDigits: decimalPlaces
        });
    };

    // Initialize Flatpickr
    flatpickr("#date_range", {
        mode: "range",
        dateFormat: "Y-m-d",
        maxDate: "today",
        defaultDate: [current_start_date, current_end_date],
        theme: "dark",
        onClose: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                current_start_date = instance.formatDate(selectedDates[0], "Y-m-d");
                current_end_date = instance.formatDate(selectedDates[1], "Y-m-d");
                loadSummaries('{{ route('user.trading-bots.daily-summary') }}');
            }
        }
    });

    const donutBaseOptions = {
        chart: {
            type: 'donut',
            height: 280,
            foreColor: '#94a3b8',
            fontFamily: 'Inter, sans-serif'
        },
        stroke: { show: false },
        dataLabels: { enabled: false },
        legend: {
            position: 'bottom',
            fontSize: '11px',
            fontWeight: 'bold',
            markers: { radius: 12 }
        },
        tooltip: {
            theme: 'dark',
            y: { formatter: (val) => formatAmount(val) }
        }
    };

    // 1. By Pair
    const pairChart = new ApexCharts(document.querySelector("#chart-pair-distribution"), {
        ...donutBaseOptions,
        series: @json(array_values($stats['distribution_by_pair'] ?? [])),
        labels: @json(array_keys($stats['distribution_by_pair'] ?? [])),
        colors: ['#e2b13c', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b']
    });
    pairChart.render();

    // 2. By Exchange
    const exchangeChart = new ApexCharts(document.querySelector("#chart-exchange-distribution"), {
        ...donutBaseOptions,
        series: @json(array_values($stats['distribution_by_exchange'] ?? [])),
        labels: @json(array_keys($stats['distribution_by_exchange'] ?? [])),
        colors: ['#8b5cf6', '#3b82f6', '#10b981', '#e2b13c', '#06b6d4', '#f43f5e']
    });
    exchangeChart.render();

    // 3. By Type
    const typeChart = new ApexCharts(document.querySelector("#chart-type-distribution"), {
        ...donutBaseOptions,
        series: @json(array_values($stats['distribution_by_type'] ?? [])),
        labels: @json(array_keys($stats['distribution_by_type'] ?? [])),
        colors: ['#10b981', '#e2b13c', '#8b5cf6', '#06b6d4', '#3b82f6']
    });
    typeChart.render();

    function loadSummaries(url) {
        $('#loading-overlay').removeClass('opacity-0 pointer-events-none');
        $.ajax({
            url: url,
            data: {
                start_date: current_start_date,
                end_date: current_end_date
            },
            success: function(response) {
                $('#cards-wrapper').html(response.html);
                $('#loading-overlay').addClass('opacity-0 pointer-events-none');
            },
            error: function() {
                $('#loading-overlay').addClass('opacity-0 pointer-events-none');
            }
        });
    }

    $(document).on('click', '#cards-wrapper .pagination a', function(e) {
        e.preventDefault();
        loadSummaries($(this).attr('href'));
    });
});
</script>
@endpush
