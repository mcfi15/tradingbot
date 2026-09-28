@extends('templates.york.blades.admin.layouts.auth')

@section('content')
    <div class="space-y-6">
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block mb-3 group">
                <div class="inline-flex items-center justify-center p-2.5 rounded-2xl bg-white/[0.02] border border-white/[0.08] group-hover:border-cyan-500/30 transition-all backdrop-blur-xl shadow-inner">
                    @php
                        $logo = getSetting('logo_rectangle');
                    @endphp
                    @if ($logo)
                        <img src="{{ asset('assets/images/' . $logo) }}" alt="{{ getSetting('name') }}"
                            class="h-8 w-auto object-contain">
                    @else
                        <span class="text-lg font-mono font-bold text-white tracking-tight">{{ getSetting('name') }}<span class="text-cyan-400">_ADMIN</span></span>
                    @endif
                </div>
            </a>
            <div class="flex items-center justify-center mb-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[9px] font-bold uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('ADMINISTRATOR CONSOLE') }}
                </span>
            </div>
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 mb-3 shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white tracking-tight">{{ __('Two-Factor Authentication') }}</h2>
            <p class="text-slate-400 text-xs mt-1 font-body">
                {{ __('Enter the 6-digit security code sent to your administrator email.') }}
            </p>
        </div>

        <form action="{{ route('admin.login.otp.validate') }}" method="POST" class="ajax-form space-y-5"
            data-action="redirect">
            @csrf

            {{-- OTP Code Input --}}
            <div class="space-y-2">
                <label for="otp_code" class="sr-only">
                    {{ __('Verification Code') }}
                </label>
                <div class="relative">
                    <input type="text" name="otp_code" id="otp_code"
                        class="w-full bg-[#05070d]/90 border border-white/[0.12] rounded-2xl px-4 py-4 text-white placeholder-slate-700 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all text-center tracking-[0.75em] text-2xl font-mono font-bold shadow-inner"
                        placeholder="••••••" required autofocus maxlength="6"
                        @if (session('admin_login_otp_code') && config('app.env') == 'sandbox') value="{{ session('admin_login_otp_code') }}" @endif>
                </div>

                @if (config('app.env') == 'sandbox')
                    <div class="flex items-center justify-center gap-1.5 text-[10px] font-mono text-cyan-400 bg-cyan-500/10 border border-cyan-500/20 px-3 py-1 rounded-xl">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        <span>{{ __('Sandbox Auto-fill Active') }}</span>
                    </div>
                @endif
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full bg-gradient-to-r from-cyan-400 via-cyan-500 to-blue-600 hover:from-cyan-300 hover:to-blue-500 text-black font-extrabold py-3.5 px-4 rounded-2xl shadow-[0_0_30px_rgba(0,245,255,0.25)] transition-all duration-300 transform hover:-translate-y-0.5 relative overflow-hidden group cursor-pointer text-sm font-mono tracking-wide">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <span>{{ __('Verify & Access Console') }}</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </button>
        </form>

        {{-- Resend OTP --}}
        <div class="pt-2 text-center">
            <p class="text-xs text-slate-400">
                {{ __("Didn't receive the code?") }}
                <button type="button" id="resend-otp-btn"
                    class="font-mono font-bold text-cyan-400 hover:text-cyan-300 transition-colors ml-1 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                    {{ $throttle ? 'disabled' : '' }}>
                    {{ __('Resend Code') }}
                </button>
            </p>
            <div id="resend-timer" class="text-[11px] font-mono text-slate-500 mt-1.5 {{ $throttle ? '' : 'hidden' }}">
                {{ __('Resend available in') }} <span id="timer-seconds" class="text-cyan-400 font-bold">60</span>s
            </div>
        </div>

        {{-- Logout / Cancel Session --}}
        <div class="text-center pt-2 border-t border-white/[0.06]">
            <form action="{{ route('admin.logout') }}" method="POST" class="ajax-form" data-action="redirect"
                data-redirect="{{ route('admin.login') }}">
                @csrf
                <button type="submit"
                    class="text-xs font-mono text-slate-500 hover:text-rose-400 transition-colors bg-transparent border-0 p-0 cursor-pointer flex items-center justify-center gap-1.5 mx-auto">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>{{ __('Cancel & Sign Out') }}</span>
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let timerSeconds = 60;
            let timerInterval;

            const $resendBtn = $('#resend-otp-btn');
            const $resendTimer = $('#resend-timer');
            const $timerSeconds = $('#timer-seconds');

            @if ($throttle)
                startTimer();
            @endif

            function startTimer() {
                timerSeconds = 60;
                $resendBtn.prop('disabled', true);
                $resendTimer.removeClass('hidden');

                clearInterval(timerInterval);
                timerInterval = setInterval(() => {
                    timerSeconds--;
                    $timerSeconds.text(timerSeconds);

                    if (timerSeconds <= 0) {
                        clearInterval(timerInterval);
                        $resendBtn.prop('disabled', false);
                        $resendTimer.addClass('hidden');
                    }
                }, 1000);
            }

            $resendBtn.on('click', function() {
                if ($(this).prop('disabled')) return;

                const $btn = $(this);
                const originalHtml = $btn.html();

                $btn.prop('disabled', true).html('<span class="inline-block animate-spin">⟳</span> {{ __('Sending...') }}');

                $.ajax({
                    url: "{{ route('admin.login.otp.resend') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            toastNotification(response.message, 'success');
                            startTimer();
                        } else {
                            toastNotification(response.message, 'error');
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON ? xhr.responseJSON.message :
                            "{{ __('Something went wrong') }}";
                        toastNotification(message, 'error');
                        $btn.prop('disabled', false);
                    },
                    complete: function() {
                        $btn.html(originalHtml);
                    }
                });
            });
        });
    </script>
@endsection
