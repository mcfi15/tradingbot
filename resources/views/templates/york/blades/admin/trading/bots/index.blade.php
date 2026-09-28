@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div id="bots-content" class="space-y-8 mb-12">

        {{-- Disabled Module Warning --}}
        @if (!moduleEnabled('trading_bot_module'))
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-8 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide mb-1">{{ __('Trading Bot Module Disabled') }}</h3>
                    <p class="text-xs text-slate-400 max-w-md mb-6 font-mono">
                        {{ __('The trading bot module is currently turned off. You can turn it on in module settings.') }}
                    </p>
                    <a href="{{ route('admin.settings.modules.index') }}"
                        class="px-6 py-2.5 rounded-full bg-amber-400 hover:bg-amber-300 text-[#050507] font-black text-xs font-mono uppercase tracking-wider transition-all">
                        {{ __('Enable Module in Settings') }}
                    </a>
                </div>
            </div>
        @else

            {{-- ==================================================================================== --}}
            {{-- TOP HEADER --}}
            {{-- ==================================================================================== --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        {{ __('TRADING BOTS') }}
                    </div>
                    <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                        {{ __('Trading Bots') }}
                    </h1>
                    <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                        {{ __('Manage automated trading bots and investment plans') }}
                    </p>
                </div>

                {{-- Action Cluster --}}
                <div class="flex flex-wrap items-center gap-2.5 font-mono text-xs">
                    <a href="{{ route('admin.trading-bots.activations.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-purple-500/15 hover:bg-purple-500/25 border border-purple-500/30 text-purple-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center gap-2 shadow-[0_0_15px_rgba(168,85,247,0.15)] cursor-pointer">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>{{ __('User Subscriptions') }}</span>
                    </a>

                    <a href="{{ route('admin.trading-bots.logs.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/[0.1] text-slate-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>{{ __('Trade History') }}</span>
                    </a>

                    <button type="button" id="import-defaults-btn"
                        data-url="{{ route('admin.trading-bots.import-defaults') }}"
                        class="px-4 py-2.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 text-emerald-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center gap-2 shadow-[0_0_15px_rgba(16,185,129,0.15)] cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>{{ __('Import Defaults') }}</span>
                    </button>

                    <a href="{{ route('admin.trading-bots.create') }}"
                        class="px-5 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black uppercase tracking-wider transition-all flex items-center gap-2 shadow-[0_0_20px_rgba(0,245,255,0.3)] cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ __('Add Bot') }}</span>
                    </a>
                </div>
            </div>

            {{-- ==================================================================================== --}}
            {{-- TRADING BOTS GRID (Double-Bezel Architecture) --}}
            {{-- ==================================================================================== --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($bots as $bot)
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] hover:border-cyan-400/40 backdrop-blur-2xl transition-all duration-300 relative overflow-hidden group flex flex-col justify-between">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 flex flex-col justify-between h-full relative">

                            {{-- Bot Card Header --}}
                            <div class="flex items-start justify-between gap-4 pb-5 border-b border-white/[0.06]">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center overflow-hidden shrink-0 shadow-lg">
                                        @if(str_starts_with($bot->logo, 'bot-'))
                                            <img src="{{ asset('assets/images/bots/' . $bot->logo) }}" alt="{{ $bot->name }}" class="w-9 h-9 object-contain">
                                        @else
                                            <img src="{{ asset('assets/images/bots/' . $bot->logo) }}" alt="{{ $bot->name }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-base font-black text-white font-mono tracking-tight leading-tight group-hover:text-cyan-300 transition-colors">
                                            {{ $bot->name }}
                                        </h3>
                                        <div class="flex items-center gap-2 mt-1 font-mono text-[10px]">
                                            <span class="px-2 py-0.5 rounded-md bg-white/[0.04] text-slate-300 border border-white/[0.08] font-bold uppercase tracking-wider">
                                                {{ strtoupper($bot->type) }}
                                            </span>
                                            <span class="text-slate-500">·</span>
                                            <span class="text-slate-400">{{ $bot->duration }} {{ __($bot->duration_type . '(s)') }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Status Pill --}}
                                @if ($bot->is_active)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-mono font-bold uppercase tracking-wider flex items-center gap-1.5 shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[9px] font-mono font-bold uppercase tracking-wider shrink-0">
                                        {{ __('Inactive') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Bot Metrics Deck --}}
                            <div class="grid grid-cols-2 gap-3 my-5 font-mono">
                                <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/[0.06]">
                                    <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Daily Profit') }}</span>
                                    <span class="text-base font-black text-emerald-400">{{ number_format($bot->daily_return_min, 2) }}% - {{ number_format($bot->daily_return_max, 2) }}%</span>
                                </div>

                                <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/[0.06]">
                                    <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Min Investment') }}</span>
                                    <span class="text-base font-black text-white">{{ showAmount($bot->min_amount) }}</span>
                                </div>

                                <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/[0.06]">
                                    <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Max Investment') }}</span>
                                    <span class="text-sm font-bold text-slate-200">{{ showAmount($bot->max_amount) }}</span>
                                </div>

                                <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/[0.06]">
                                    <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Capital Back') }}</span>
                                    <span class="text-sm font-bold {{ $bot->is_capital_returned ? 'text-cyan-400' : 'text-slate-400' }}">
                                        {{ $bot->is_capital_returned ? __('Yes (100%)') : __('Profit Only') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Traded Pairs & Schedule --}}
                            <div class="space-y-3 pb-5 border-b border-white/[0.06] font-mono text-[10px]">
                                <div>
                                    <span class="text-slate-400 uppercase font-bold tracking-wider block mb-1.5">{{ __('Trading Pairs') }}</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach(array_slice($bot->traded_pairs, 0, 4) as $pair)
                                            <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold uppercase">
                                                {{ $pair }}
                                            </span>
                                        @endforeach
                                        @if(count($bot->traded_pairs) > 4)
                                            <span class="px-2 py-0.5 rounded-lg bg-white/[0.04] text-slate-400 border border-white/[0.08] font-bold">
                                                +{{ count($bot->traded_pairs) - 4 }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Schedule Days --}}
                                <div>
                                    <span class="text-slate-400 uppercase font-bold tracking-wider block mb-1.5">{{ __('Trading Days') }}</span>
                                    <div class="flex flex-wrap gap-1">
                                        @php
                                            $allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            $botDays = is_array($bot->trading_days) ? $bot->trading_days : [];
                                        @endphp
                                        @foreach($allDays as $day)
                                            <span class="px-2 py-0.5 rounded text-[8px] font-bold {{ in_array($day, $botDays) ? 'bg-cyan-400/20 text-cyan-300 border border-cyan-400/30' : 'bg-white/[0.02] text-slate-600 border border-white/[0.04]' }}">
                                                {{ substr($day, 0, 3) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Actions Footer --}}
                            <div class="flex items-center gap-2 pt-4 font-mono text-xs">
                                <a href="{{ route('admin.trading-bots.edit', $bot->id) }}"
                                    class="flex-1 py-2.5 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white font-bold uppercase tracking-wider border border-white/[0.08] transition-all flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>{{ __('Edit Bot') }}</span>
                                </a>

                                <button type="button"
                                    class="delete-bot-btn p-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-all cursor-pointer"
                                    data-id="{{ $bot->id }}"
                                    data-url="{{ route('admin.trading-bots.delete', $bot->id) }}"
                                    title="{{ __('Delete Bot') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1]">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-12 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white font-mono uppercase tracking-wide mb-2">{{ __('No Trading Bots Found') }}</h3>
                            <p class="text-xs text-slate-400 font-mono max-w-md mx-auto mb-6">
                                {{ __('Create your first trading bot so users can start investing.') }}
                            </p>
                            <div class="flex flex-wrap items-center justify-center gap-4">
                                <a href="{{ route('admin.trading-bots.create') }}"
                                    class="px-8 py-3 rounded-full bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black text-xs font-mono uppercase tracking-wider shadow-[0_0_20px_rgba(0,245,255,0.3)] transition-all">
                                    {{ __('Create Trading Bot') }}
                                </a>
                                <button type="button"
                                    data-url="{{ route('admin.trading-bots.import-defaults') }}"
                                    class="import-defaults-btn px-8 py-3 rounded-full bg-emerald-500/20 hover:bg-emerald-500/30 border border-emerald-500/40 text-emerald-300 hover:text-white font-black text-xs font-mono uppercase tracking-wider transition-all cursor-pointer flex items-center gap-2 shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span>{{ __('Import Default Bots (15)') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($bots->hasPages())
                <div class="pt-4">
                    {{ $bots->links('templates.york.blades.partials.pagination') }}
                </div>
            @endif

        @endif

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Import Default Bots
            $('.import-defaults-btn, #import-defaults-btn').on('click', function() {
                const url = $(this).data('url');
                Swal.fire({
                    title: '{{ __('Import Default Bots?') }}',
                    text: '{{ __('This will import/update the 15 standard institutional trading bots into your database.') }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#334155',
                    confirmButtonText: '{{ __('Yes, Import (15)') }}',
                    cancelButtonText: '{{ __('Cancel') }}',
                    customClass: {
                        popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono',
                        title: 'text-white font-mono',
                        htmlContainer: 'text-slate-400 font-mono text-xs',
                        confirmButton: 'rounded-xl font-bold uppercase tracking-wider text-xs px-5 py-2.5',
                        cancelButton: 'rounded-xl font-bold uppercase tracking-wider text-xs px-5 py-2.5'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: '{{ __('Importing...') }}',
                            text: '{{ __('Please wait while default bots are being imported.') }}',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            customClass: {
                                popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono',
                                title: 'text-white font-mono'
                            }
                        });

                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: '{{ __('Success!') }}',
                                    text: response.message || '{{ __('Default bots imported successfully.') }}',
                                    icon: 'success',
                                    customClass: {
                                        popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono',
                                        title: 'text-white font-mono'
                                    }
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: '{{ __('Error!') }}',
                                    text: xhr.responseJSON?.message || '{{ __('Failed to import default bots.') }}',
                                    icon: 'error',
                                    customClass: {
                                        popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono',
                                        title: 'text-white font-mono'
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // Delete Bot
            $('.delete-bot-btn').on('click', function() {
                const url = $(this).data('url');
                Swal.fire({
                    title: '{{ __('Delete Trading Bot?') }}',
                    text: '{{ __('Are you sure you want to delete this trading bot? This action cannot be undone.') }}',
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
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Bot deleted successfully') }}', showConfirmButton:false, timer:2000});
                                setTimeout(() => { location.reload(); }, 800);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
