@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div id="copy-history-content" class="space-y-8 mb-12">

        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
                    {{ __('COPY TRADE HISTORY') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-purple-100 to-purple-400/70 tracking-tight leading-tight">
                    {{ __('Copy Trade History') }}
                </h1>
                <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('View all user copy trade subscriptions and profit distributions') }}
                </p>
            </div>

            {{-- Action Cluster --}}
            <div class="flex flex-wrap items-center gap-2.5 font-mono text-xs">
                <a href="{{ route('admin.copy-trading.index') }}"
                    class="px-4 py-2.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/[0.1] text-slate-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>{{ __('Copy Trades') }}</span>
                </a>

                <button type="button" onclick="openExportModal()"
                    class="px-4 py-2.5 rounded-xl bg-purple-500/15 hover:bg-purple-500/25 border border-purple-500/30 text-purple-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center gap-2 shadow-[0_0_15px_rgba(168,85,247,0.15)] cursor-pointer">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>{{ __('Export') }}</span>
                </button>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- 4-CARD STATS DECK --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between font-mono">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Total Investment') }}</span>
                        <span class="text-xl font-black text-white">{{ showAmount($stats['total_capital']) }}</span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between font-mono">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Total Profit Distributed') }}</span>
                        <span class="text-xl font-black text-emerald-400">{{ $stats['total_profit'] >= 0 ? '+' : '' }}{{ showAmount($stats['total_profit']) }}</span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between font-mono">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Active Trades') }}</span>
                        <span class="text-xl font-black text-purple-400">{{ number_format($stats['total_active']) }}</span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between font-mono">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Total Trades Copied') }}</span>
                        <span class="text-xl font-black text-slate-200">{{ number_format($stats['total_trades']) }}</span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-white/[0.04] border border-white/10 text-slate-300 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- CHARTS DECK --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-mono">
            <div class="lg:col-span-2 p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Profit Over Time') }}</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ __('Daily profit earned by users') }} (<span id="chart-period-display">7D</span>)</p>
                        </div>
                        <div class="flex items-center bg-white/[0.03] border border-white/[0.08] rounded-xl p-1 gap-1 text-[10px]">
                            @foreach ([7 => '7D', 30 => '30D', 90 => '90D', 365 => '1Y'] as $d => $label)
                                <button type="button" onclick="updateProfitTrend({{ $d }}, this)"
                                    class="px-2.5 py-1 rounded-lg font-bold transition-all interval-btn {{ $d == 7 ? 'bg-emerald-400 text-black' : 'text-slate-400 hover:text-white' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div id="profitTrendChart" class="min-h-[260px]"></div>
                </div>
            </div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 space-y-4 flex flex-col justify-between">
                    <div class="pb-3 border-b border-white/[0.06]">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Investments by Trading Pair') }}</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ __('Total investment across trading pairs') }}</p>
                    </div>
                    <div id="distributionChart" class="min-h-[260px] flex items-center justify-center"></div>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- DATA TABLE CHASSIS (Double-Bezel) --}}
        {{-- ==================================================================================== --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
            <div class="rounded-[2rem] bg-[#090c14]/95 overflow-hidden font-mono">

                {{-- Table Toolbar --}}
                <div class="p-6 border-b border-white/[0.06] flex flex-col lg:flex-row justify-between items-center gap-4 text-xs">
                    <form action="{{ route('admin.copy-trading.history') }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search user, code or pair...') }}" 
                            class="bg-[#05070d] border border-white/[0.1] focus:border-emerald-400 text-white rounded-xl px-4 py-2 text-xs focus:outline-none transition-all w-full sm:w-64">

                        <select name="status" onchange="this.form.submit()"
                            class="bg-[#05070d] border border-white/[0.1] text-white rounded-xl px-3 py-2 text-xs focus:outline-none cursor-pointer">
                            <option value="all">{{ __('All Status') }}</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Running') }}</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        </select>

                        <button type="submit" class="p-2 rounded-xl bg-emerald-400 text-black hover:bg-emerald-300 font-bold transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-white/[0.02] border-b border-white/[0.06] text-[10px] text-slate-400 uppercase tracking-widest">
                                <th class="p-5">{{ __('User') }}</th>
                                <th class="p-5">{{ __('Trade Code') }}</th>
                                <th class="p-5">{{ __('Trading Pair') }}</th>
                                <th class="p-5 text-right">{{ __('Investment') }}</th>
                                <th class="p-5 text-right">{{ __('Profit') }}</th>
                                <th class="p-5 text-right">{{ __('Profit (%)') }}</th>
                                <th class="p-5 text-right">{{ __('Started Date') }}</th>
                                <th class="p-5 text-center">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.04]">
                            @forelse($activations as $activation)
                                <tr class="hover:bg-white/[0.015] transition-colors group">
                                    {{-- User --}}
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ substr($activation->user?->username ?? 'U', 0, 2) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.users.detail', $activation->user_id) }}" class="text-white hover:text-emerald-300 font-bold block leading-tight">
                                                    {{ $activation->user?->fullname ?? $activation->user?->username ?? 'N/A' }}
                                                </a>
                                                <span class="text-[10px] text-slate-500 block">@<span>{{ $activation->user?->username ?? 'user' }}</span></span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Code --}}
                                    <td class="p-5">
                                        <span class="px-2.5 py-1 rounded-lg bg-white/[0.04] border border-white/[0.08] text-white font-bold text-[11px] tracking-wider">
                                            {{ $activation->copy_code }}
                                        </span>
                                    </td>

                                    {{-- Pair --}}
                                    <td class="p-5 text-slate-300 font-bold">
                                        {{ $activation->pair }}
                                    </td>

                                    {{-- Capital --}}
                                    <td class="p-5 text-right font-black text-white">
                                        {{ showAmount($activation->amount) }}
                                    </td>

                                    {{-- Profit --}}
                                    <td class="p-5 text-right font-black text-emerald-400">
                                        {{ $activation->status === 'active' ? '--' : showAmount($activation->profit) }}
                                    </td>

                                    {{-- ROI --}}
                                    <td class="p-5 text-right font-bold text-emerald-400">
                                        {{ $activation->status === 'active' ? '--' : ($activation->roi > 0 ? '+' : '') . $activation->roi . '%' }}
                                    </td>

                                    {{-- Date --}}
                                    <td class="p-5 text-right text-slate-300 text-[11px]">
                                        {{ $activation->activated_at->format('M d, Y H:i') }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="p-5 text-center">
                                        @if ($activation->status === 'active')
                                            <span class="px-2.5 py-1 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 text-[9px] font-bold uppercase tracking-wider">
                                                ● {{ __('Running') }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-bold uppercase tracking-wider">
                                                {{ ucfirst($activation->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-12 text-center text-slate-500 font-mono">
                                        {{ __('No copy trade history found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($activations->hasPages())
                    <div class="p-6 border-t border-white/[0.06]">
                        {{ $activations->links('templates.york.blades.partials.pagination') }}
                    </div>
                @endif

            </div>
        </div>

    </div>

    {{-- Export Modal --}}
    <div id="exportModal" class="hidden fixed inset-0 bg-[#05070d]/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.05] border border-white/[0.1] w-full max-w-md">
            <div class="rounded-[2rem] bg-[#090c14] p-8 space-y-6 font-mono">
                <div class="flex items-center justify-between pb-4 border-b border-white/[0.06]">
                    <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Export Copy Trade History') }}</h3>
                    <button type="button" onclick="closeExportModal()" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs" id="export-columns-container">
                    @foreach ([
                        'username' => 'User',
                        'copy_code' => 'Trade Code',
                        'pair' => 'Trading Pair',
                        'amount' => 'Investment',
                        'profit' => 'Profit',
                        'roi' => 'Profit (%)',
                        'status' => 'Status',
                        'activated_at' => 'Started Date',
                    ] as $key => $label)
                        <label class="flex items-center gap-2.5 p-2 rounded-xl bg-white/[0.02] border border-white/[0.08] cursor-pointer hover:border-emerald-400/40">
                            <input type="checkbox" name="export_cols[]" value="{{ $key }}" checked 
                                class="w-3.5 h-3.5 rounded border-white/20 bg-white/5 text-emerald-400 accent-emerald-400 cursor-pointer">
                            <span class="text-slate-300 text-[11px]">{{ __($label) }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-white/[0.06] grid grid-cols-3 gap-2 text-xs">
                    @foreach(['pdf' => 'PDF', 'csv' => 'CSV', 'sql' => 'SQL'] as $type => $label)
                        <button type="button" onclick="handleExport('{{ $type }}')" 
                            class="py-3 rounded-xl bg-white/[0.04] hover:bg-emerald-400 hover:text-black border border-white/[0.1] text-white font-bold uppercase tracking-wider transition-all cursor-pointer">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        let profitChart;

        document.addEventListener('DOMContentLoaded', function() {
            const chartDistributionData = @json($chart_distribution);
            const chartTrendData = @json($chart_trend);

            // Distribution Chart
            new ApexCharts(document.querySelector("#distributionChart"), {
                series: chartDistributionData.series,
                chart: { type: 'donut', height: 260, background: 'transparent' },
                labels: chartDistributionData.labels,
                theme: { mode: 'dark' },
                colors: ['#10b981', '#00f5ff', '#a855f7', '#f59e0b', '#ec4899', '#3b82f6'],
                stroke: { show: false },
                legend: { position: 'bottom', labels: { colors: '#94a3b8' }, fontFamily: 'monospace' },
                dataLabels: { enabled: false },
                plotOptions: { pie: { donut: { size: '75%', background: 'transparent' } } }
            }).render();

            // Trend Chart
            profitChart = new ApexCharts(document.querySelector("#profitTrendChart"), {
                series: [{ name: 'Profit', data: chartTrendData.series }],
                chart: { type: 'area', height: 260, toolbar: { show: false }, zoom: { enabled: false }, background: 'transparent' },
                theme: { mode: 'dark' },
                colors: ['#10b981'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [20, 100] }
                },
                xaxis: {
                    categories: chartTrendData.labels,
                    labels: { style: { colors: '#64748b', fontFamily: 'monospace' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { labels: { style: { colors: '#64748b', fontFamily: 'monospace' } } },
                grid: { borderColor: 'rgba(255, 255, 255, 0.05)', strokeDashArray: 3 }
            });
            profitChart.render();
        });

        function updateProfitTrend(days, btn) {
            $('.interval-btn').removeClass('bg-emerald-400 text-black').addClass('text-slate-400 hover:text-white');
            $(btn).removeClass('text-slate-400 hover:text-white').addClass('bg-emerald-400 text-black');
            $('#chart-period-display').text(days == 365 ? '1Y' : days + 'D');

            $.ajax({
                url: "{{ route('admin.copy-trading.chart-data') }}",
                type: 'GET',
                data: { chart_days: days, type: 'activations' },
                success: function(res) {
                    profitChart.updateSeries([{ name: 'Profit', data: res.series }]);
                    profitChart.updateOptions({ xaxis: { categories: res.labels } });
                }
            });
        }

        function openExportModal() { $('#exportModal').removeClass('hidden'); }
        function closeExportModal() { $('#exportModal').addClass('hidden'); }

        function handleExport(type) {
            const selectedCols = [];
            $('input[name="export_cols[]"]:checked').each(function() {
                selectedCols.push($(this).val());
            });

            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);
            params.set('export', type);
            if (selectedCols.length > 0) {
                params.set('columns', selectedCols.join(','));
            }

            window.location.href = "{{ route('admin.copy-trading.history') }}?" + params.toString();
            closeExportModal();
        }
    </script>
@endpush
