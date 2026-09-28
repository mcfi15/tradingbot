@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12 font-mono">
        
        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('UTILITIES & LOCALIZATION') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Utilities & Language Settings') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Configure table pagination, alerts cleanup, preloader, and language options') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="$('#utility-settings-form').submit()"
                    class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- MAIN LAYOUT: SIDEBAR + CONTENT --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Navigation Deck --}}
            <div class="lg:col-span-4 xl:col-span-3">
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        @include("templates.$template.blades.admin.settings.partials.sidebar")
                    </div>
                </div>
            </div>

            {{-- Right Content Chassis --}}
            <div class="lg:col-span-8 xl:col-span-9 space-y-6">
                <form id="utility-settings-form" action="{{ route('admin.settings.utility.update') }}" method="POST">
                    @csrf

                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-10 text-xs">
                            
                            {{-- SECTION 1: GENERAL SYSTEM PREFERENCES --}}
                            <div class="space-y-6">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            01
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('System Preferences') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Set default pagination size, automatic alert cleanup, and dashboard loading screen.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Table Rows Per Page') }}</label>
                                        <input type="number" name="pagination" value="{{ $pagination }}" min="5" max="100" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>

                                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                                        <div class="space-y-0.5">
                                            <span class="text-white font-bold block">{{ __('Auto-Delete Read Notifications') }}</span>
                                            <span class="text-[9px] text-slate-400">{{ __('Automatically delete read notifications') }}</span>
                                        </div>
                                        <input type="checkbox" name="delete_notification_message" value="enabled" class="accent-cyan-400 w-4 h-4 cursor-pointer"
                                            {{ $delete_notification_message === 'enabled' ? 'checked' : '' }}>
                                    </div>

                                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                                        <div class="space-y-0.5">
                                            <span class="text-white font-bold block">{{ __('Page Loading Screen') }}</span>
                                            <span class="text-[9px] text-slate-400">{{ __('Show animated preloader on page load') }}</span>
                                        </div>
                                        <input type="checkbox" name="preloader" value="enabled" class="accent-cyan-400 w-4 h-4 cursor-pointer"
                                            {{ $preloader === 'enabled' ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 2: LOCALIZATION MATRIX --}}
                            <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            02
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Available Languages') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Enable or disable languages available to users on the platform.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach ($all_languages as $code => $lang)
                                        @php $isEnabled = in_array($code, $enabled_languages); @endphp
                                        <div class="p-4 rounded-2xl border transition-all duration-300 flex items-center justify-between {{ $isEnabled ? 'bg-cyan-500/[0.03] border-cyan-500/30' : 'bg-white/[0.015] border-white/[0.04] opacity-50' }}"
                                            id="lang-card-{{ $code }}">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full overflow-hidden border border-white/10 shrink-0">
                                                    <img src="{{ asset('assets/flags/' . $lang['flag'] . '.svg') }}" alt="{{ $lang['name'] }}" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <span class="text-white font-bold block text-xs">{{ $lang['name'] }}</span>
                                                    <span class="text-[9px] text-slate-500 uppercase">{{ $code }} @if ($lang['rtl']) · RTL @endif</span>
                                                </div>
                                            </div>
                                            <input type="checkbox" name="enabled_languages[]" value="{{ $code }}" class="accent-cyan-400 w-4 h-4 cursor-pointer"
                                                {{ $isEnabled ? 'checked' : '' }}
                                                onchange="$('#lang-card-{{ $code }}').toggleClass('bg-cyan-500/[0.03] border-cyan-500/30 bg-white/[0.015] border-white/[0.04] opacity-50')">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- SUBMIT BUTTON --}}
                            <div class="pt-6 border-t border-white/[0.06] flex items-center justify-end">
                                <button type="submit" id="submit-btn"
                                    class="px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4 submit-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="btn-text">{{ __('Save Changes') }}</span>
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#utility-settings-form').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#submit-btn');
                $btn.prop('disabled', true).addClass('opacity-50');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message || '{{ __('Utilities updated.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to save utility settings.') }}',
                            customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).removeClass('opacity-50');
                    }
                });
            });
        });
    </script>
@endpush
