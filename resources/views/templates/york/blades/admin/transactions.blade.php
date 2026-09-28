@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div id="transactions-content" class="space-y-8 mb-12">

        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('TRANSACTIONS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Transactions') }}
                </h1>
                <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('View all user transactions, deposits, withdrawals, and balance updates') }}
                </p>
            </div>

            {{-- Action Cluster & Export --}}
            <div class="flex flex-wrap items-center gap-2.5 font-mono text-xs">
                <button type="button" onclick="openExportModal()"
                    class="px-5 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black uppercase tracking-wider transition-all flex items-center gap-2 shadow-[0_0_20px_rgba(0,245,255,0.3)] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>{{ __('Export') }}</span>
                </button>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- 4-CARD STATS DECK --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 font-mono">
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Total Volume') }}</span>
                        <span class="text-xl font-black text-white">{{ showAmount($stats['total_volume']) }}</span>
                        <span class="text-[10px] text-slate-500 block mt-0.5">{{ number_format($stats['total_count']) }} {{ __('records') }}</span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Total Deposits / Credits') }}</span>
                        <span class="text-xl font-black text-emerald-400">+{{ showAmount($stats['total_credit_volume']) }}</span>
                        <span class="text-[10px] text-slate-500 block mt-0.5">{{ number_format($stats['total_credit_count']) }} {{ __('credits') }}</span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Total Withdrawals / Debits') }}</span>
                        <span class="text-xl font-black text-rose-400">-{{ showAmount($stats['total_debit_volume']) }}</span>
                        <span class="text-[10px] text-slate-500 block mt-0.5">{{ number_format($stats['total_debit_count']) }} {{ __('debits') }}</span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Today\'s Volume') }}</span>
                        <span class="text-xl font-black text-purple-400">{{ showAmount($stats['today_volume']) }}</span>
                        <span class="text-[10px] text-slate-500 block mt-0.5">{{ number_format($stats['today_count']) }} {{ __('today') }}</span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- VISUALIZATION DECK --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-mono">
            {{-- Inflow vs Outflow Trend --}}
            <div class="lg:col-span-2 p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Cash Flow Over Time') }}</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ __('Daily credits and debits comparison') }}</p>
                        </div>
                        <div class="flex items-center bg-white/[0.03] border border-white/[0.08] rounded-xl p-1 gap-1 text-[10px]">
                            @foreach (['7d' => '7D', '30d' => '30D', '90d' => '90D', '1y' => '1Y'] as $p => $lbl)
                                <button type="button" onclick="updateTrxChart('{{ $p }}', this)"
                                    class="px-2.5 py-1 rounded-lg font-bold transition-all chart-period-btn {{ $p === '7d' ? 'bg-cyan-400 text-black' : 'text-slate-400 hover:text-white' }}">
                                    {{ $lbl }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div id="trxTrendChart" class="min-h-[260px]"></div>
                </div>
            </div>

            {{-- Type Distribution --}}
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 space-y-4 flex flex-col justify-between">
                    <div class="pb-3 border-b border-white/[0.06]">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Transaction Types') }}</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ __('Breakdown by transaction type') }}</p>
                    </div>
                    <div id="typeDistributionChart" class="min-h-[260px] flex items-center justify-center"></div>
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
                    <form action="{{ route('admin.transactions.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search user, reference...') }}" 
                            class="bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2 text-xs focus:outline-none transition-all w-full sm:w-64">

                        <select name="type" onchange="this.form.submit()"
                            class="bg-[#05070d] border border-white/[0.1] text-white rounded-xl px-3 py-2 text-xs focus:outline-none cursor-pointer">
                            <option value="all">{{ __('All Types') }}</option>
                            <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>{{ __('Credits (+)') }}</option>
                            <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>{{ __('Debits (-)') }}</option>
                        </select>

                        <button type="submit" class="p-2 rounded-xl bg-cyan-400 text-black hover:bg-cyan-300 font-bold transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>

                    {{-- Bulk Actions --}}
                    <div class="flex items-center gap-2 w-full lg:w-auto justify-end">
                        <button type="button" id="bulk-delete-btn" disabled
                            class="px-4 py-2 rounded-xl bg-rose-500/10 text-rose-400 border border-rose-500/20 font-bold uppercase tracking-wider transition-all disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>{{ __('Delete Selected') }}</span>
                        </button>
                    </div>
                </div>

                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-white/[0.02] border-b border-white/[0.06] text-[10px] text-slate-400 uppercase tracking-widest">
                                <th class="p-5 w-12 text-center">
                                    <input type="checkbox" id="select-all" class="rounded border-white/20 bg-white/5 text-cyan-400 accent-cyan-400 cursor-pointer">
                                </th>
                                <th class="p-5">{{ __('User') }}</th>
                                <th class="p-5">{{ __('Reference') }}</th>
                                <th class="p-5 text-right">{{ __('Amount') }}</th>
                                <th class="p-5 text-right">{{ __('Balance After') }}</th>
                                <th class="p-5">{{ __('Description') }}</th>
                                <th class="p-5 text-right">{{ __('Date') }}</th>
                                <th class="p-5 text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.04]">
                            @forelse($transactions as $trx)
                                <tr class="hover:bg-white/[0.015] transition-colors group">
                                    {{-- Checkbox --}}
                                    <td class="p-5 text-center">
                                        <input type="checkbox" class="trx-checkbox rounded border-white/20 bg-white/5 text-cyan-400 accent-cyan-400 cursor-pointer" value="{{ $trx->id }}">
                                    </td>

                                    {{-- User --}}
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ substr($trx->user?->username ?? 'U', 0, 2) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.users.detail', $trx->user_id) }}" class="text-white hover:text-cyan-300 font-bold block leading-tight">
                                                    {{ $trx->user?->fullname ?? $trx->user?->username ?? 'N/A' }}
                                                </a>
                                                <span class="text-[10px] text-slate-500 block">@<span>{{ $trx->user?->username ?? 'user' }}</span></span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Reference --}}
                                    <td class="p-5">
                                        <span class="px-2.5 py-1 rounded-lg bg-white/[0.04] border border-white/[0.08] text-slate-300 font-bold text-[10px] tracking-wider">
                                            {{ $trx->reference }}
                                        </span>
                                    </td>

                                    {{-- Amount --}}
                                    <td class="p-5 text-right font-black {{ $trx->type === 'credit' ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $trx->type === 'credit' ? '+' : '-' }}{{ showAmount($trx->amount) }}
                                    </td>

                                    {{-- Post Balance --}}
                                    <td class="p-5 text-right text-slate-200 font-bold">
                                        {{ showAmount($trx->new_balance) }}
                                    </td>

                                    {{-- Description --}}
                                    <td class="p-5 text-slate-400 max-w-xs truncate text-[11px]">
                                        {{ $trx->description ?? '--' }}
                                    </td>

                                    {{-- Date --}}
                                    <td class="p-5 text-right text-slate-300 text-[11px]">
                                        {{ $trx->created_at ? $trx->created_at->format('M d, Y H:i') : '--' }}
                                    </td>

                                    {{-- Action --}}
                                    <td class="p-5 text-center">
                                        <button type="button"
                                            class="delete-trx-btn p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-all cursor-pointer"
                                            data-url="{{ route('admin.transactions.delete', $trx->id) }}"
                                            title="{{ __('Delete Record') }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-12 text-center text-slate-500 font-mono">
                                        {{ __('No transactions found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($transactions->hasPages())
                    <div class="p-6 border-t border-white/[0.06]">
                        {{ $transactions->links('templates.york.blades.partials.pagination') }}
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
                    <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Export Transactions') }}</h3>
                    <button type="button" onclick="closeExportModal()" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs" id="export-columns-container">
                    @foreach ([
                        'username' => 'User',
                        'amount' => 'Amount',
                        'new_balance' => 'Balance After',
                        'type' => 'Type',
                        'reference' => 'Reference',
                        'description' => 'Description',
                        'created_at' => 'Date & Time',
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
        const graphData = @json($graph_data);
        const typeChartData = @json($type_chart_data);
        let trxChart;

        document.addEventListener('DOMContentLoaded', function() {
            // Trend
            const initialPeriod = graphData['7d'] || { credits: { labels: [], data: [] }, debits: { labels: [], data: [] } };
            trxChart = new ApexCharts(document.querySelector("#trxTrendChart"), {
                series: [
                    { name: 'Credits (+)', data: initialPeriod.credits.data },
                    { name: 'Debits (-)', data: initialPeriod.debits.data }
                ],
                chart: { type: 'area', height: 260, toolbar: { show: false }, background: 'transparent' },
                colors: ['#10b981', '#f43f5e'],
                theme: { mode: 'dark' },
                stroke: { curve: 'smooth', width: 2 },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.02 } },
                xaxis: { categories: initialPeriod.credits.labels, labels: { style: { colors: '#64748b', fontFamily: 'monospace' } } },
                yaxis: { labels: { style: { colors: '#64748b', fontFamily: 'monospace' } } },
                grid: { borderColor: 'rgba(255, 255, 255, 0.05)' }
            });
            trxChart.render();

            // Type Donut
            const typeSeries = typeChartData.map(item => item.total);
            const typeLabels = typeChartData.map(item => item.name);
            new ApexCharts(document.querySelector("#typeDistributionChart"), {
                series: typeSeries.length > 0 ? typeSeries : [1],
                chart: { type: 'donut', height: 260, background: 'transparent' },
                labels: typeLabels.length > 0 ? typeLabels : ['None'],
                colors: ['#10b981', '#f43f5e', '#00f5ff', '#a855f7'],
                theme: { mode: 'dark' },
                stroke: { show: false },
                legend: { position: 'bottom', labels: { colors: '#94a3b8' }, fontFamily: 'monospace' },
                dataLabels: { enabled: false },
                plotOptions: { pie: { donut: { size: '75%', background: 'transparent' } } }
            }).render();
        });

        function updateTrxChart(period, btn) {
            $('.chart-period-btn').removeClass('bg-cyan-400 text-black').addClass('text-slate-400 hover:text-white');
            $(btn).removeClass('text-slate-400 hover:text-white').addClass('bg-cyan-400 text-black');

            const pData = graphData[period];
            if (pData) {
                trxChart.updateSeries([
                    { name: 'Credits (+)', data: pData.credits.data },
                    { name: 'Debits (-)', data: pData.debits.data }
                ]);
                trxChart.updateOptions({ xaxis: { categories: pData.credits.labels } });
            }
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

            window.location.href = "{{ route('admin.transactions.index') }}?" + params.toString();
            closeExportModal();
        }

        $(document).ready(function() {
            // Checkbox selection
            $('#select-all').on('change', function() {
                $('.trx-checkbox').prop('checked', $(this).prop('checked'));
                toggleBulkButton();
            });

            $('.trx-checkbox').on('change', function() {
                toggleBulkButton();
            });

            function toggleBulkButton() {
                const count = $('.trx-checkbox:checked').length;
                $('#bulk-delete-btn').prop('disabled', count === 0);
            }

            // Single Delete
            $('.delete-trx-btn').on('click', function() {
                const url = $(this).data('url');
                Swal.fire({
                    title: '{{ __('Delete Transaction?') }}',
                    text: '{{ __('Are you sure you want to delete this transaction record? This action cannot be undone.') }}',
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
                            data: { _token: '{{ csrf_token() }}' },
                            success: function() {
                                Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Deleted successfully') }}', showConfirmButton:false, timer:1500});
                                setTimeout(() => { location.reload(); }, 600);
                            }
                        });
                    }
                });
            });

            // Bulk Delete
            $('#bulk-delete-btn').on('click', function() {
                const ids = [];
                $('.trx-checkbox:checked').each(function() {
                    ids.push($(this).val());
                });

                if (ids.length === 0) return;

                Swal.fire({
                    title: '{{ __('Delete Selected Transactions?') }}',
                    text: `{{ __('Are you sure you want to delete ') }}${ids.length}{{ __(' transaction(s)? This action cannot be undone.') }}`,
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
                            url: "{{ route('admin.transactions.bulk-delete') }}",
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}', ids: ids },
                            success: function() {
                                Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Deleted successfully') }}', showConfirmButton:false, timer:1500});
                                setTimeout(() => { location.reload(); }, 600);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
