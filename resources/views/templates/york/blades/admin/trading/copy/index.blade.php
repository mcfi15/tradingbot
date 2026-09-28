@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div id="copy-trading-content" class="space-y-8 mb-12">

        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ __('COPY TRADING') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-emerald-100 to-emerald-400/70 tracking-tight leading-tight">
                    {{ __('Copy Trading') }}
                </h1>
                <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Manage copy trading signals and view follower activity') }}
                </p>
            </div>

            {{-- Action Cluster --}}
            <div class="flex flex-wrap items-center gap-2.5 font-mono text-xs">
                <a href="{{ route('admin.copy-trading.history') }}"
                    class="px-4 py-2.5 rounded-xl bg-purple-500/15 hover:bg-purple-500/25 border border-purple-500/30 text-purple-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center gap-2 shadow-[0_0_15px_rgba(168,85,247,0.15)] cursor-pointer">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ __('Copy Trade History') }}</span>
                </a>

                <a href="{{ route('admin.copy-trading.create') }}"
                    class="px-5 py-2.5 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-[#050507] font-black uppercase tracking-wider transition-all flex items-center gap-2 shadow-[0_0_20px_rgba(16,185,129,0.3)] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('Create Copy Trade') }}</span>
                </a>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- STRATEGIES GRID (Double-Bezel) --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 font-mono">
            @forelse($strategies as $strategy)
                @php
                    $isExpired = $strategy->expires_at && $strategy->expires_at < time();
                @endphp
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] hover:border-emerald-400/40 backdrop-blur-2xl transition-all duration-300 relative overflow-hidden group flex flex-col justify-between">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 flex flex-col justify-between h-full relative space-y-6">

                        {{-- Card Header --}}
                        <div class="flex items-start justify-between gap-4 pb-4 border-b border-white/[0.06]">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-black text-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                                        <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-black text-white group-hover:text-emerald-300 transition-colors">
                                            {{ $strategy->code }}
                                        </span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded bg-white/[0.04] text-slate-300 border border-white/[0.08] text-[10px] font-bold uppercase tracking-wider block w-fit mt-1">
                                        {{ $strategy->pair }}
                                    </span>
                                </div>
                            </div>

                            {{-- ROI Box --}}
                            <div class="text-right">
                                <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block">{{ __('Target Profit') }}</span>
                                <span class="text-xl font-black text-emerald-400">+{{ number_format($strategy->roi, 2) }}%</span>
                            </div>
                        </div>

                        {{-- Strategy Details --}}
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/[0.06]">
                                <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Investment Type') }}</span>
                                <span class="font-bold text-white uppercase">{{ $strategy->amount_type }}</span>
                                @if($strategy->amount_type === 'percentage')
                                    <span class="text-[10px] text-emerald-400 block font-normal">({{ $strategy->percentage }}% balance)</span>
                                @endif
                            </div>

                            <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/[0.06]">
                                <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Expires') }}</span>
                                @if($isExpired)
                                    <span class="text-rose-400 font-bold uppercase text-[10px]">{{ __('Expired') }}</span>
                                @else
                                    <span class="text-emerald-400 font-bold uppercase text-[10px]">{{ $strategy->expires_at ? date('M d, H:i', $strategy->expires_at) : __('Never') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions Footer --}}
                        <div class="flex items-center gap-2 pt-4 border-t border-white/[0.06] text-xs">
                            <a href="{{ route('admin.copy-trading.edit', $strategy->id) }}"
                                class="flex-1 py-2.5 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white font-bold uppercase tracking-wider border border-white/[0.08] transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>{{ __('Edit') }}</span>
                            </a>

                            <button type="button"
                                onclick="openSignalModal('{{ $strategy->id }}')"
                                class="flex-1 py-2.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/20 font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                <span>{{ __('Share Signal') }}</span>
                            </button>

                            <button type="button"
                                class="delete-strategy-btn p-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-all cursor-pointer"
                                data-url="{{ route('admin.copy-trading.delete', $strategy->id) }}"
                                title="{{ __('Delete Trade') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-span-full p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1]">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-12 text-center font-mono">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white uppercase tracking-wide mb-2">{{ __('No Copy Trades Found') }}</h3>
                        <p class="text-xs text-slate-400 max-w-md mx-auto mb-6">
                            {{ __('Create your first copy trade signal for users to follow.') }}
                        </p>
                        <a href="{{ route('admin.copy-trading.create') }}"
                            class="px-8 py-3 rounded-full bg-emerald-400 hover:bg-emerald-300 text-[#050507] font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all">
                            {{ __('Create Copy Trade') }}
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($strategies->hasPages())
            <div class="pt-4">
                {{ $strategies->links('templates.york.blades.partials.pagination') }}
            </div>
        @endif

    </div>

    {{-- Signal Message Modal --}}
    <div id="signalModal" class="hidden fixed inset-0 bg-[#05070d]/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.05] border border-white/[0.1] w-full max-w-lg">
            <div class="rounded-[2rem] bg-[#090c14] p-8 space-y-6 font-mono">
                <div class="flex items-center justify-between pb-4 border-b border-white/[0.06]">
                    <div>
                        <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Share Signal') }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ __('Copy the trade signal message to share with your users') }}</p>
                    </div>
                    <button type="button" onclick="closeSignalModal()" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>

                {{-- Language Selector --}}
                <div id="signal-languages" class="flex flex-wrap gap-2 text-xs"></div>

                {{-- Textbox Preview --}}
                <div>
                    <textarea id="signal-message-text" rows="8" readonly
                        class="w-full bg-[#05070d] border border-white/[0.1] rounded-2xl p-4 text-xs text-emerald-300 font-mono focus:outline-none select-all"></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" id="copy-signal-btn"
                        class="flex-1 py-3 rounded-xl bg-emerald-400 text-black font-black text-xs uppercase tracking-wider hover:bg-emerald-300 transition-all shadow-[0_0_20px_rgba(16,185,129,0.3)] cursor-pointer">
                        {{ __('Copy Signal') }}
                    </button>
                    <button type="button" onclick="closeSignalModal()"
                        class="px-5 py-3 rounded-xl bg-white/[0.04] text-slate-300 hover:text-white text-xs font-bold uppercase transition-all cursor-pointer">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentMessages = {};

        function openSignalModal(id) {
            $.ajax({
                url: "{{ route('admin.copy-trading.get-signal-messages', ':id') }}".replace(':id', id),
                type: 'GET',
                success: function(res) {
                    if (res.success && res.data.messages) {
                        currentMessages = res.data.messages;
                        const langKeys = Object.keys(currentMessages);
                        let buttonsHtml = '';

                        langKeys.forEach((lang, index) => {
                            buttonsHtml += `<button type="button" onclick="switchLanguage('${lang}')" class="lang-btn px-3 py-1 rounded-lg border text-[11px] font-bold uppercase transition-all cursor-pointer ${index === 0 ? 'bg-emerald-400 text-black border-emerald-400' : 'bg-white/[0.03] text-slate-400 border-white/[0.08] hover:text-white'}">${lang}</button>`;
                        });

                        $('#signal-languages').html(buttonsHtml);
                        if (langKeys.length > 0) {
                            switchLanguage(langKeys[0]);
                        }

                        $('#signalModal').removeClass('hidden');
                    }
                },
                error: function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: '{{ __('Failed to load signal messages') }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        }

        function switchLanguage(lang) {
            $('.lang-btn').removeClass('bg-emerald-400 text-black border-emerald-400').addClass('bg-white/[0.03] text-slate-400 border-white/[0.08]');
            $(`.lang-btn:contains('${lang}')`).removeClass('bg-white/[0.03] text-slate-400 border-white/[0.08]').addClass('bg-emerald-400 text-black border-emerald-400');
            $('#signal-message-text').val(currentMessages[lang] || '');
        }

        function closeSignalModal() {
            $('#signalModal').addClass('hidden');
        }

        $('#copy-signal-btn').on('click', function() {
            const text = $('#signal-message-text').val();
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Signal copied!') }}', showConfirmButton:false, timer:1500});
            });
        });

        $(document).ready(function() {
            $('.delete-strategy-btn').on('click', function() {
                const url = $(this).data('url');
                Swal.fire({
                    title: '{{ __('Delete Copy Trade?') }}',
                    text: '{{ __('Are you sure you want to delete this copy trade? This action cannot be undone.') }}',
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
