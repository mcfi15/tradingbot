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
                    {{ __('ACCOUNT SECURITY') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Password & Active Sessions') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Update your admin password and manage active browser login sessions') }}
                </p>
            </div>

            {{-- Live Security Pill --}}
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 rounded-2xl bg-white/[0.02] border border-white/[0.08] text-right">
                    <span class="text-[9px] uppercase font-bold text-slate-500 block">{{ __('Active Sessions') }}</span>
                    <span class="text-xs font-black text-emerald-400">{{ $sessions->count() + 1 }} {{ __('Devices Active') }}</span>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- MAIN GRID LAYOUT --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Navigation Deck --}}
            <div class="lg:col-span-4 xl:col-span-3">
                @include("templates.$template.blades.admin.account.partials.sidebar")

                {{-- Security Protocol Telemetry --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl mt-6">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 space-y-4 text-xs">
                        <div class="pb-3 border-b border-white/[0.06]">
                            <h4 class="text-[11px] font-black text-white uppercase tracking-wider">{{ __('Password Policy') }}</h4>
                            <p class="text-[10px] text-slate-400 mt-1">{{ __('Requires minimum 8 characters with uppercase, lowercase, number, and symbol.') }}</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 uppercase text-[10px] font-bold">{{ __('Session Timeout') }}</span>
                            <span class="text-cyan-400 font-bold">{{ config('session.lifetime', 120) }}m</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Main Panels --}}
            <div class="lg:col-span-8 xl:col-span-9 space-y-8">
                
                {{-- 1. Password Update Card --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-6">
                        <div class="pb-4 border-b border-white/[0.06]">
                            <h3 class="text-base font-black text-white uppercase tracking-wide">{{ __('Change Password') }}</h3>
                            <p class="text-xs text-slate-400 mt-1">{{ __('Enter your current password and choose a new secure password.') }}</p>
                        </div>

                        <form id="password-update-form" action="{{ route('admin.account.password.update') }}" method="POST" class="space-y-6">
                            @csrf

                            <div class="space-y-2 text-xs">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    {{ __('Current Password') }} <span class="text-rose-400">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="current_password" required
                                        class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 pr-12 text-xs focus:outline-none transition-colors"
                                        placeholder="{{ __('Enter current password') }}">
                                    <button type="button" onclick="togglePasswordVisibility(this)"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors cursor-pointer">
                                        <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ __('New Password') }} <span class="text-rose-400">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="password" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 pr-12 text-xs focus:outline-none transition-colors"
                                            placeholder="{{ __('Enter new password') }}">
                                        <button type="button" onclick="togglePasswordVisibility(this)"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors cursor-pointer">
                                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ __('Confirm New Password') }} <span class="text-rose-400">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="password_confirmation" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 pr-12 text-xs focus:outline-none transition-colors"
                                            placeholder="{{ __('Confirm new password') }}">
                                        <button type="button" onclick="togglePasswordVisibility(this)"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors cursor-pointer">
                                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-white/[0.06] flex items-center justify-end">
                                <button type="submit" id="password-submit-btn"
                                    class="px-8 py-3.5 bg-cyan-400 hover:bg-cyan-300 text-[#050507] text-xs font-black uppercase tracking-wider rounded-xl shadow-[0_0_25px_rgba(0,245,255,0.3)] hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-3 cursor-pointer">
                                    <svg class="w-4 h-4 submit-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg class="w-4 h-4 hidden loading-icon animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="btn-text">{{ __('Change Password') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 2. Active Sessions & Devices --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-white/[0.06]">
                            <div>
                                <h3 class="text-base font-black text-white uppercase tracking-wide">{{ __('Active Login Sessions') }}</h3>
                                <p class="text-xs text-slate-400 mt-1">{{ __('Devices and browser sessions currently logged into your admin account.') }}</p>
                            </div>
                            @if ($sessions->count() > 0)
                                <button type="button" onclick="openLogoutModal()"
                                    class="px-4 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 text-xs font-bold uppercase tracking-wider transition-all cursor-pointer">
                                    {{ __('Log Out Other Devices') }}
                                </button>
                            @endif
                        </div>

                        <div class="space-y-3">
                            {{-- Current Session Card --}}
                            <div class="p-4 rounded-2xl bg-cyan-500/5 border border-cyan-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-cyan-400 text-black flex items-center justify-center font-bold shrink-0 shadow-[0_0_15px_rgba(0,245,255,0.4)]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-white">{{ request()->ip() }}</span>
                                            @if ($current_location)
                                                <span class="text-[10px] text-slate-400">({{ $current_location }})</span>
                                            @endif
                                            <span class="px-2 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 text-[9px] font-black uppercase tracking-wider">
                                                ● {{ __('Current Device') }}
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-slate-500 truncate max-w-xl italic">{{ request()->userAgent() }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Other Sessions --}}
                            @forelse($sessions as $session)
                                <div class="p-4 rounded-2xl bg-white/[0.015] border border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-white/5 text-slate-400 flex items-center justify-center font-bold shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-slate-300">{{ $session->ip_address }}</span>
                                                @if ($session->location)
                                                    <span class="text-[10px] text-slate-500">({{ $session->location }})</span>
                                                @endif
                                            </div>
                                            <p class="text-[10px] text-slate-500 truncate max-w-xl italic">{{ $session->user_agent }}</p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] text-slate-400 whitespace-nowrap">
                                        {{ __('Active:') }} {{ $session->last_activity->diffForHumans() }}
                                    </span>
                                </div>
                            @empty
                                <div class="p-6 rounded-2xl bg-white/[0.01] border border-white/[0.04] text-center text-slate-500 text-xs">
                                    {{ __('No other active sessions detected.') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- Logout Others Modal --}}
    <div id="logout-modal" class="hidden fixed inset-0 bg-[#05070d]/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.05] border border-white/[0.1] w-full max-w-md font-mono">
            <div class="rounded-[2rem] bg-[#090c14] p-8 space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 class="text-base font-bold text-white uppercase">{{ __('Log Out Other Devices') }}</h3>
                    <button type="button" onclick="closeLogoutModal()" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>
                
                <p class="text-xs text-slate-400 leading-relaxed">
                    {{ __('Enter your password to log out of all other devices and browser sessions.') }}
                </p>

                <form id="logout-others-form" action="{{ route('admin.account.sessions.logout-other') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-2 text-xs">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            {{ __('Current Password') }} <span class="text-rose-400">*</span>
                        </label>
                        <input type="password" name="password" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-rose-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none"
                            placeholder="{{ __('Enter current password') }}">
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" onclick="closeLogoutModal()"
                            class="flex-1 py-3 bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-bold uppercase rounded-xl transition-all cursor-pointer">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" id="logout-submit-btn"
                            class="flex-1 py-3 bg-rose-500 hover:bg-rose-400 text-white text-xs font-black uppercase rounded-xl transition-all shadow-[0_0_20px_rgba(244,63,94,0.3)] cursor-pointer">
                            {{ __('Log Out Devices') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePasswordVisibility(el) {
            const input = el.parentElement.querySelector('input');
            const eyeIcon = el.querySelector('svg');

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                `;
            } else {
                input.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        function openLogoutModal() { $('#logout-modal').removeClass('hidden'); }
        function closeLogoutModal() { $('#logout-modal').addClass('hidden'); }

        $(document).ready(function() {
            // Password Update Ajax
            $('#password-update-form').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#password-submit-btn');
                const $btnText = $btn.find('.btn-text');
                const $submitIcon = $btn.find('.submit-icon');
                const $loadingIcon = $btn.find('.loading-icon');
                const originalText = $btnText.text();

                $btn.prop('disabled', true).addClass('opacity-50');
                $btnText.text('{{ __('Updating...') }}');
                $submitIcon.addClass('hidden');
                $loadingIcon.removeClass('hidden');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message || '{{ __('Password updated successfully.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                        $('#password-update-form')[0].reset();
                    },
                    error: function(xhr) {
                        let errorMessage = '{{ __('Something went wrong.') }}';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Security Exception') }}',
                            html: errorMessage,
                            customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).removeClass('opacity-50');
                        $btnText.text(originalText);
                        $submitIcon.removeClass('hidden');
                        $loadingIcon.addClass('hidden');
                    }
                });
            });

            // Logout Others Ajax
            $('#logout-others-form').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#logout-submit-btn');
                const originalText = $btn.text();

                $btn.prop('disabled', true).addClass('opacity-50').text('{{ __('Revoking...') }}');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        closeLogoutModal();
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('Sessions Terminated') }}',
                            text: response.message || '{{ __('Other sessions logged out successfully.') }}',
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono' }
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON?.message || '{{ __('Failed to terminate remote sessions.') }}';
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Verification Failed') }}',
                            text: errorMessage,
                            customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                        });
                        $btn.prop('disabled', false).removeClass('opacity-50').text(originalText);
                    }
                });
            });
        });
    </script>
@endpush
