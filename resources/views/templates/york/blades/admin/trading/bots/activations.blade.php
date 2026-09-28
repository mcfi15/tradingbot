@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div id="activations-content" class="space-y-8 mb-12">

        {{-- Disabled Warning --}}
        @if (!moduleEnabled('trading_bot_module'))
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-8 text-center font-mono">
                    <h3 class="text-lg font-bold text-white uppercase">{{ __('Trading Bot Module Disabled') }}</h3>
                    <p class="text-xs text-slate-400 mt-2 mb-4">{{ __('The trading bot module is currently turned off. You can turn it on in module settings.') }}</p>
                    <a href="{{ route('admin.settings.modules.index') }}" class="px-6 py-2 rounded-full bg-cyan-400 text-black font-black text-xs uppercase">{{ __('Settings') }}</a>
                </div>
            </div>
        @else

            {{-- ==================================================================================== --}}
            {{-- TOP HEADER --}}
            {{-- ==================================================================================== --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
                        {{ __('USER SUBSCRIPTIONS') }}
                    </div>
                    <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-purple-100 to-purple-400/70 tracking-tight leading-tight">
                        {{ __('Bot Subscriptions') }}
                    </h1>
                    <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                        {{ __('View and manage user bot subscriptions and profits') }}
                    </p>
                </div>

                {{-- Action Cluster --}}
                <div class="flex flex-wrap items-center gap-2.5 font-mono text-xs">
                    <a href="{{ route('admin.trading-bots.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/[0.1] text-slate-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>{{ __('Trading Bots') }}</span>
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
                            <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Active Investments') }}</span>
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
                            <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Total Profit Paid') }}</span>
                            <span class="text-xl font-black text-emerald-400">+{{ showAmount($stats['total_profit']) }}</span>
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
                            <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Active Subscriptions') }}</span>
                            <span class="text-xl font-black text-purple-400">{{ number_format($stats['total_active']) }}</span>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between font-mono">
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Total Subscriptions') }}</span>
                            <span class="text-xl font-black text-slate-200">{{ number_format($stats['total_activations']) }}</span>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-white/[0.04] border border-white/10 text-slate-300 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================================================================================== --}}
            {{-- ANALYTICS & VISUALIZATION DECK --}}
            {{-- ==================================================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-mono">
                {{-- Profit Area Chart --}}
                <div class="lg:col-span-2 p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                            <div>
                                <h3 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Profit Over Time') }}</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ __('Daily profit paid out to users') }}</p>
                            </div>
                            <div class="flex items-center bg-white/[0.03] border border-white/[0.08] rounded-xl p-1 gap-1 text-[10px]">
                                @foreach([7 => '7D', 30 => '30D', 90 => '90D', 365 => '1Y'] as $d => $label)
                                    <button type="button" onclick="updateProfitTrend({{ $d }}, this)"
                                        class="px-2.5 py-1 rounded-lg font-bold transition-all interval-btn {{ request('chart_days', 7) == $d ? 'bg-cyan-400 text-black' : 'text-slate-400 hover:text-white' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div id="profitTrendChart" class="min-h-[260px]"></div>
                    </div>
                </div>

                {{-- Asset Distribution Chart --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 space-y-4 flex flex-col justify-between">
                        <div class="pb-3 border-b border-white/[0.06]">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Investments by Bot') }}</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ __('Total investment distribution across bots') }}</p>
                        </div>
                        <div id="distributionChart" class="min-h-[260px] flex items-center justify-center"></div>
                    </div>
                </div>
            </div>

            {{-- ==================================================================================== --}}
            {{-- DATA TABLE CHASSIS (Double-Bezel) --}}
            {{-- ==================================================================================== --}}
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 overflow-hidden">

                    {{-- Table Toolbar --}}
                    <div class="p-6 border-b border-white/[0.06] flex flex-col lg:flex-row justify-between items-center gap-4 font-mono text-xs">
                        <form action="{{ route('admin.trading-bots.activations.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search user or bot...') }}" 
                                class="bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2 text-xs focus:outline-none transition-all w-full sm:w-64">
                            
                            <select name="status" class="bg-[#05070d] border border-white/[0.1] text-white rounded-xl px-3 py-2 text-xs focus:outline-none cursor-pointer">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('All Statuses') }}</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                            </select>

                            <button type="submit" class="p-2 rounded-xl bg-cyan-400 text-black hover:bg-cyan-300 font-bold transition-all cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    {{-- Table Content --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse font-mono text-xs">
                            <thead>
                                <tr class="bg-white/[0.02] border-b border-white/[0.06] text-[10px] text-slate-400 uppercase tracking-widest">
                                    <th class="p-5">{{ __('User') }}</th>
                                    <th class="p-5">{{ __('Trading Bot') }}</th>
                                    <th class="p-5 text-right">{{ __('Investment') }}</th>
                                    <th class="p-5 text-right">{{ __('Profit Earned') }}</th>
                                    <th class="p-5 text-center">{{ __('Status') }}</th>
                                    <th class="p-5 text-right">{{ __('Started Date') }}</th>
                                    <th class="p-5 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.04]">
                                @forelse($activations as $activation)
                                    <tr class="hover:bg-white/[0.015] transition-colors group">
                                        {{-- Client --}}
                                        <td class="p-5">
                                             <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                    {{ substr($activation->user?->username ?? 'U', 0, 2) }}
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.users.detail', $activation->user_id) }}" class="text-white hover:text-cyan-300 font-bold block leading-tight">
                                                        {{ $activation->user?->fullname ?? $activation->user?->username ?? 'N/A' }}
                                                    </a>
                                                    <span class="text-[10px] text-slate-500 block">@<span>{{ $activation->user?->username ?? 'user' }}</span></span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Strategy --}}
                                        <td class="p-5">
                                            <span class="text-white font-bold block">{{ $activation->bot?->name ?? 'N/A' }}</span>
                                            <span class="text-[10px] text-slate-400 uppercase">{{ $activation->bot?->type ?? 'Crypto' }}</span>
                                        </td>

                                        {{-- Capital --}}
                                        <td class="p-5 text-right font-black text-white">
                                            {{ showAmount($activation->amount) }}
                                        </td>

                                        {{-- Profit --}}
                                        <td class="p-5 text-right font-black text-emerald-400">
                                            +{{ showAmount($activation->returned_profit) }}
                                        </td>

                                        {{-- Status --}}
                                        <td class="p-5 text-center">
                                            @if ($activation->status === 'active')
                                                <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-bold uppercase tracking-wider">
                                                    ● {{ __('Active') }}
                                                </span>
                                            @elseif($activation->status === 'suspended')
                                                <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[9px] font-bold uppercase tracking-wider">
                                                    ● {{ __('Suspended') }}
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full bg-slate-500/10 text-slate-400 border border-slate-500/20 text-[9px] font-bold uppercase tracking-wider">
                                                    {{ ucfirst($activation->status) }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Date --}}
                                        <td class="p-5 text-right text-slate-300 text-[11px]">
                                            {{ $activation->start_date ? date('M d, Y', $activation->start_date) : 'N/A' }}
                                        </td>

                                        {{-- Actions --}}
                                        <td class="p-5 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                {{-- Status Toggle --}}
                                                <button type="button"
                                                    class="update-status-btn p-2 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 border border-purple-500/20 transition-all cursor-pointer"
                                                    data-id="{{ $activation->id }}"
                                                    data-status="{{ $activation->status }}"
                                                    title="{{ __('Update Status') }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                {{-- Delete --}}
                                                <button type="button"
                                                    class="delete-activation-btn p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-all cursor-pointer"
                                                    data-url="{{ route('admin.trading-bots.activations.delete', $activation->id) }}"
                                                    title="{{ __('Delete Subscription') }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-12 text-center text-slate-500 font-mono">
                                            {{ __('No subscriptions found.') }}
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

        @endif

    </div>

    {{-- Export Modal --}}
    <div id="exportModal" class="hidden fixed inset-0 bg-[#05070d]/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.05] border border-white/[0.1] w-full max-w-md">
            <div class="rounded-[2rem] bg-[#090c14] p-8 space-y-6 font-mono">
                <div class="flex items-center justify-between pb-4 border-b border-white/[0.06]">
                    <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Export Subscriptions') }}</h3>
                    <button type="button" onclick="closeExportModal()" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs" id="export-columns-container">
                    @foreach ([
                        'username' => 'User',
                        'bot_name' => 'Trading Bot',
                        'amount' => 'Investment',
                        'returned_profit' => 'Profit',
                        'status' => 'Status',
                        'start_date' => 'Started Date',
                        'end_date' => 'End Date',
                    ] as $key => $label)
                        <label class="flex items-center gap-2.5 p-2 rounded-xl bg-white/[0.02] border border-white/[0.08] cursor-pointer hover:border-cyan-400/40">
                            <input type="checkbox" name="export_cols[]" value="{{ $key }}" checked 
                                class="w-3.5 h-3.5 rounded border-white/20 bg-white/5 text-cyan-400 accent-cyan-400 cursor-pointer">
                            <span class="text-slate-300 text-[11px]">{{ __($label) }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-white/[0.06] grid grid-cols-3 gap-2 text-xs">
                    @foreach(['pdf' => 'PDF', 'csv' => 'CSV', 'sql' => 'SQL'] as $type => $label)
                        <button type="button" onclick="handleExport('{{ $type }}')" 
                            class="py-3 rounded-xl bg-white/[0.04] hover:bg-cyan-400 hover:text-black border border-white/[0.1] text-white font-bold uppercase tracking-wider transition-all cursor-pointer">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let profitChart;

        document.addEventListener('DOMContentLoaded', function() {
            const chartDistributionData = @json($chart_distribution);
            const chartTrendData = @json($chart_trend);

            // Distribution
            new ApexCharts(document.querySelector("#distributionChart"), {
                series: chartDistributionData.series,
                chart: { type: 'donut', height: 260, background: 'transparent' },
                labels: chartDistributionData.labels,
                theme: { mode: 'dark' },
                colors: ['#00f5ff', '#10b981', '#a855f7', '#f59e0b', '#ec4899', '#3b82f6'],
                stroke: { show: false },
                legend: { position: 'bottom', labels: { colors: '#94a3b8' }, fontFamily: 'monospace' },
                dataLabels: { enabled: false },
                plotOptions: { pie: { donut: { size: '75%', background: 'transparent' } } }
            }).render();

            // Trend
            profitChart = new ApexCharts(document.querySelector("#profitTrendChart"), {
                series: [{ name: 'Profit', data: chartTrendData.series }],
                chart: { type: 'area', height: 260, toolbar: { show: false }, zoom: { enabled: false }, background: 'transparent' },
                theme: { mode: 'dark' },
                colors: ['#00f5ff'],
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
            $('.interval-btn').removeClass('bg-cyan-400 text-black').addClass('text-slate-400 hover:text-white');
            $(btn).removeClass('text-slate-400 hover:text-white').addClass('bg-cyan-400 text-black');

            $.ajax({
                url: "{{ route('admin.trading-bots.chart-data') }}",
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

            window.location.href = "{{ route('admin.trading-bots.activations.index') }}?" + params.toString();
            closeExportModal();
        }

        $(document).ready(function() {
            // Update Status
            $('.update-status-btn').on('click', function() {
                const id = $(this).data('id');
                const currentStatus = $(this).data('status');

                Swal.fire({
                    title: '{{ __('Update Status') }}',
                    input: 'select',
                    inputOptions: {
                        'active': '{{ __('Active') }}',
                        'suspended': '{{ __('Suspended') }}',
                        'completed': '{{ __('Completed') }}'
                    },
                    inputValue: currentStatus,
                    showCancelButton: true,
                    confirmButtonText: '{{ __('Update') }}',
                    confirmButtonColor: '#00f5ff',
                    cancelButtonColor: '#334155',
                    customClass: {
                        popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono',
                        title: 'text-white font-mono',
                        input: 'bg-[#05070d] text-white border-white/10 font-mono',
                    }
                }).then((res) => {
                    if (res.isConfirmed && res.value) {
                        $.ajax({
                            url: `{{ url('admin/trading-bots/activations/status') }}/${id}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: res.value
                            },
                            success: function() {
                                Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Status updated') }}', showConfirmButton:false, timer:1500});
                                setTimeout(() => { location.reload(); }, 600);
                            }
                        });
                    }
                });
            });

            // Delete
            $('.delete-activation-btn').on('click', function() {
                const url = $(this).data('url');
                Swal.fire({
                    title: '{{ __('Delete Subscription?') }}',
                    text: '{{ __('Are you sure you want to delete this subscription? This action cannot be undone.') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f43f5e',
                    cancelButtonColor: '#334155',
                    confirmButtonText: '{{ __('Yes, Delete') }}',
                    customClass: {
                        popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono',
                        title: 'text-white font-mono',
                        htmlContainer: 'text-slate-400 font-mono',
                    }
                }).then((res) => {
                    if (res.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Terminated') }}', showConfirmButton:false, timer:1500});
                                setTimeout(() => { location.reload(); }, 600);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
