@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-12 pb-24">

    {{-- Global Ethereal Ambient Mesh Gradients --}}
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
                            <span>{{ __('Copy Trading') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                            {{ __('Copy History') }}
                        </h1>

                        <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                            {{ __('Track your active copy trades, profit history, and performance.') }}
                        </p>
                    </div>

                    {{-- Floating Island Navigation Sub-Pills --}}
                    <div class="flex flex-wrap items-center gap-2.5 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl shrink-0">
                        <a href="{{ route('user.copy-trading.index') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                            <svg class="w-4 h-4 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <span>{{ __('Terminal') }}</span>
                        </a>

                        <a href="{{ route('user.copy-trading.history') }}"
                           class="px-5 py-2.5 rounded-full bg-purple-500/20 text-white font-black text-xs uppercase tracking-wider border border-purple-500/30 shadow-[0_0_20px_rgba(168,85,247,0.25)] transition-all flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                            <span>{{ __('History') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ STATS HUD GRID (4 CARDS) ═══════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Profit') }}</span>
                    <div class="text-3xl font-black {{ $stats['total_profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }} font-mono tracking-tight">
                        {{ $stats['total_profit'] >= 0 ? '+' : '' }}{{ showAmount($stats['total_profit']) }}
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Today\'s Profit') }}</span>
                    <div class="text-3xl font-black text-white font-mono tracking-tight">
                        {{ $stats['today_profit'] >= 0 ? '+' : '' }}{{ showAmount($stats['today_profit']) }}
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Active Trades') }}</span>
                    <div class="text-3xl font-black text-purple-400 font-mono tracking-tight">{{ number_format($stats['active_trades']) }}</div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Trades') }}</span>
                    <div class="text-3xl font-black text-accent-primary font-mono tracking-tight">{{ number_format($stats['total_trades']) }}</div>
                </div>
            </div>
        </div>

        {{-- ══ CHARTS ROW ═══════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Performance Trend --}}
            <div class="lg:col-span-2 rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-black text-white tracking-tight">{{ __('Performance Trend') }}</h3>
                            <p class="text-[9px] font-bold uppercase tracking-widest text-slate-500 mt-0.5">{{ __('Daily Returns') }}</p>
                        </div>
                        <div class="flex gap-2 bg-white/5 p-1 rounded-full border border-white/10">
                            @foreach ([7 => '7D', 30 => '1M', 90 => '3M'] as $d => $label)
                                <button type="button" onclick="updateProfitTrend({{ $d }}, this)"
                                    class="cursor-pointer interval-btn px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all {{ $d == 7 ? 'bg-accent-primary text-black shadow-lg shadow-accent-primary/20' : 'text-slate-400 hover:bg-white/5' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div id="profitTrendChart" class="min-h-[300px]"></div>
                </div>
            </div>

            {{-- Profit Distribution --}}
            <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                    <div>
                        <h3 class="text-lg font-black text-white tracking-tight">{{ __('Profit Distribution') }}</h3>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-slate-500 mt-0.5">{{ __('Earnings by Ticker') }}</p>
                    </div>
                    <div id="distributionChart" class="min-h-[300px]"></div>
                </div>
            </div>
        </div>

        {{-- ══ ACTIVATIONS DEPLOYMENT LIST ═════════════════════════════════════ --}}
        <div class="space-y-6">
            <h3 class="text-2xl font-black text-white tracking-tight px-2">{{ __('Active Copy Trades') }}</h3>

            <div class="space-y-6">
                @forelse ($activations as $activation)
                    <div class="group relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 transition-all duration-500 hover:border-purple-500/40 hover:shadow-[0_20px_50px_rgba(0,0,0,0.9)]">
                        <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-10 space-y-6"
                             style="background: radial-gradient(circle at 90% 10%, rgba(168,85,247,0.06) 0%, rgba(6,8,12,0.99) 70%);">
                            
                            {{-- Header --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-white/5">
                                <div class="flex items-center gap-5 min-w-0">
                                    <div class="w-14 h-14 rounded-2xl bg-accent-primary/10 border border-accent-primary/20 flex items-center justify-center text-accent-primary font-mono font-black text-lg shadow-lg shrink-0">
                                        {{ strtoupper(substr($activation->pair, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-2xl font-black text-white group-hover:text-purple-400 transition-colors tracking-tight truncate">
                                                {{ $activation->copy_code }}
                                            </h3>
                                            <span class="px-3 py-0.5 rounded-full bg-accent-primary/10 border border-accent-primary/20 text-[9px] font-bold text-accent-primary uppercase tracking-widest shrink-0">
                                                {{ $activation->pair }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-4 mt-1.5 text-xs text-slate-400 font-medium">
                                            <span class="flex items-center gap-1.5 font-bold {{ $activation->status === 'active' ? 'text-emerald-400' : 'text-slate-400' }}">
                                                <span class="w-2 h-2 rounded-full {{ $activation->status === 'active' ? 'bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.7)] animate-pulse' : 'bg-slate-500' }}"></span>
                                                {{ $activation->status === 'active' ? __('Active') : ucfirst($activation->status) }}
                                            </span>
                                            <span>• {{ __('Started:') }} <strong class="text-white font-bold">{{ $activation->activated_at->format('M d, Y H:i') }}</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Financial Metrics --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="p-5 rounded-2xl bg-black/40 border border-white/5 space-y-1">
                                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest block">{{ __('Invested Amount') }}</span>
                                    <div class="text-2xl font-black text-white font-mono tracking-tight">{{ showAmount($activation->amount) }}</div>
                                </div>

                                <div class="p-5 rounded-2xl bg-black/40 border border-white/5 space-y-1">
                                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest block">{{ __('Profit / Loss') }}</span>
                                    <div class="text-2xl font-black {{ ($activation->profit ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }} font-mono tracking-tight">
                                        {{ $activation->status === 'active' ? '--' : (($activation->profit >= 0 ? '+' : '') . showAmount($activation->profit)) }}
                                    </div>
                                </div>

                                <div class="p-5 rounded-2xl bg-black/40 border border-white/5 space-y-1">
                                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest block">{{ __('Return') }}</span>
                                    <div class="text-2xl font-black text-white font-mono tracking-tight">
                                        {{ $activation->status === 'active' ? '--' : (($activation->roi > 0 ? '+' : '') . $activation->roi . '%') }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 max-w-lg mx-auto text-center p-12">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 mb-4">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">{{ __('No Copy Trades Found') }}</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed mb-6">
                            {{ __('You have not started any copy trades yet. Enter a trading code on the copy trading page to begin.') }}
                        </p>
                        <a href="{{ route('user.copy-trading.index') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_4px_20px_rgba(226,177,60,0.3)]">
                            {{ __('Start Copy Trading') }}
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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
let profitChart;
let distChart;

document.addEventListener('DOMContentLoaded', function () {
    const trendData = @json($chart_trend);
    const distData = @json($chart_distribution);

    // 1. Performance Trend Chart
    const trendOptions = {
        series: [{
            name: "{{ __('Net Profit') }}",
            data: trendData.data
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false },
            sparkline: { enabled: false },
            foreColor: '#94a3b8'
        },
        colors: ['#e2b13c'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: trendData.labels,
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: { labels: { formatter: (val) => '$' + val.toFixed(2) } },
        grid: { borderColor: 'rgba(255, 255, 255, 0.05)' },
        tooltip: { theme: 'dark' }
    };

    profitChart = new ApexCharts(document.querySelector("#profitTrendChart"), trendOptions);
    profitChart.render();

    // 2. Distribution Donut Chart
    const distOptions = {
        series: distData.data,
        labels: distData.labels,
        chart: { type: 'donut', height: 300, foreColor: '#94a3b8' },
        colors: ['#e2b13c', '#8b5cf6', '#10b981', '#3b82f6', '#ec4899'],
        stroke: { show: false },
        dataLabels: { enabled: false },
        legend: { position: 'bottom', fontSize: '11px', fontWeight: 'bold' },
        tooltip: { theme: 'dark' }
    };

    distChart = new ApexCharts(document.querySelector("#distributionChart"), distOptions);
    distChart.render();
});

function updateProfitTrend(days, btn) {
    $('.interval-btn').removeClass('bg-accent-primary text-black shadow-lg shadow-accent-primary/20').addClass('text-slate-400 hover:bg-white/5');
    $(btn).addClass('bg-accent-primary text-black shadow-lg shadow-accent-primary/20').removeClass('text-slate-400 hover:bg-white/5');

    fetch("{{ route('user.copy-trading.chart-data') }}?interval=" + days)
        .then(res => res.json())
        .then(data => {
            profitChart.updateOptions({
                xaxis: { categories: data.labels },
                series: [{ data: data.data }]
            });
        });
}
</script>
@endpush
