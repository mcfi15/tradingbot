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
                    {{ __('FEATURE MODULES') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Feature Modules') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Enable or disable platform features, trading modules, and optional extensions') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="$('#modules-settings-form').submit()"
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
                <form id="modules-settings-form" action="{{ route('admin.settings.modules.update') }}" method="POST">
                    @csrf

                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-8 text-xs">
                            
                            <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Available Modules') }}</h3>
                                    <p class="text-[10px] text-slate-400">{{ __('Turning a module on or off automatically updates platform menus, user dashboards, and trading features.') }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($modules as $key => $module)
                                    @php $isEnabled = ($module['status'] === 'enabled'); @endphp
                                    <div class="p-5 rounded-2xl border transition-all duration-300 flex items-center justify-between gap-4 {{ $isEnabled ? 'bg-cyan-500/[0.03] border-cyan-500/25 shadow-[0_0_15px_rgba(0,245,255,0.05)]' : 'bg-white/[0.015] border-white/[0.04] opacity-60' }}"
                                        id="module-card-{{ $key }}">
                                        <div class="space-y-1 pr-2">
                                            <span class="text-white font-bold block text-xs">{{ __($module['name']) }}</span>
                                            <span class="text-[10px] text-slate-400 block leading-relaxed">{{ __($module['description']) }}</span>
                                        </div>
                                        <input type="checkbox" name="modules[{{ $key }}]" value="enabled" class="module-toggle accent-cyan-400 w-5 h-5 cursor-pointer shrink-0"
                                            data-key="{{ $key }}" {{ $isEnabled ? 'checked' : '' }}>
                                    </div>
                                @endforeach
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
            $('.module-toggle').on('change', function() {
                const key = $(this).data('key');
                const isChecked = $(this).is(':checked');
                const $card = $('#module-card-' + key);
                
                if (isChecked) {
                    $card.removeClass('bg-white/[0.015] border-white/[0.04] opacity-60')
                         .addClass('bg-cyan-500/[0.03] border-cyan-500/25 shadow-[0_0_15px_rgba(0,245,255,0.05)]');
                } else {
                    $card.removeClass('bg-cyan-500/[0.03] border-cyan-500/25 shadow-[0_0_15px_rgba(0,245,255,0.05)]')
                         .addClass('bg-white/[0.015] border-white/[0.04] opacity-60');
                }

                // Auto-sync on toggle
                $('#modules-settings-form').submit();
            });

            $('#modules-settings-form').on('submit', function(e) {
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
                            title: response.message || '{{ __('Modules updated.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to save module states.') }}',
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
