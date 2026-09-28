@extends('templates.york.blades.layouts.auth')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 mb-4 shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white tracking-tight">
                {{ __('Two-Factor Security') }}
            </h3>
            <p class="text-slate-400 text-xs mt-1.5 font-body">
                {{ __('Enter the 6-digit verification code sent to your registered email.') }}
            </p>
        </div>

        {{-- Form --}}
        <form action="{{ route('user.login.otp.validate') }}" method="POST" class="ajax-form space-y-5" data-action="redirect">
            @csrf

            {{-- OTP Input --}}
            <div class="space-y-2 text-center">
                <label for="otp" class="sr-only">{{ __('Verification Code') }}</label>
                <div class="relative">
                    <input type="text" name="otp_code" id="otp"
                        class="w-full bg-[#05070d]/90 border border-white/[0.12] rounded-2xl px-4 py-4 text-white placeholder-slate-700 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all text-center tracking-[0.75em] text-2xl font-mono font-bold shadow-inner"
                        placeholder="••••••" maxlength="6" required autofocus autocomplete="one-time-code"
                        @if (session()->has('login_otp_code') && config('app.env') == 'sandbox') value="{{ session('login_otp_code') }}" @endif>
                </div>
                @error('otp_code')
                    <p class="text-rose-400 text-xs mt-2 font-mono">{{ $message }}</p>
                @enderror
                @if (session('success'))
                    <p class="text-emerald-400 text-xs mt-2 font-mono">{{ session('success') }}</p>
                @endif
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full bg-gradient-to-r from-cyan-400 via-cyan-500 to-blue-600 hover:from-cyan-300 hover:to-blue-500 text-black font-extrabold py-3.5 px-4 rounded-2xl shadow-[0_0_30px_rgba(0,245,255,0.25)] transition-all duration-300 transform hover:-translate-y-0.5 relative overflow-hidden group cursor-pointer text-sm font-mono tracking-wide">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <span>{{ __('Verify & Continue') }}</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </button>
        </form>

        {{-- Resend Code --}}
        <div class="text-center mt-6">
            <p class="text-slate-400 text-xs mb-1">{{ __("Didn't receive the code?") }}</p>
            <form action="{{ route('user.login.resend-otp') }}" method="POST" id="resend-form"
                class="ajax-form inline-block">
                @csrf
                <button type="submit" id="resend-btn"
                    class="text-cyan-400 font-mono font-bold text-xs hover:text-cyan-300 transition-colors disabled:opacity-40 disabled:cursor-not-allowed bg-transparent border-0 p-0 cursor-pointer">
                    {{ __('Resend Security Code') }}
                </button>
            </form>
            <div id="countdown-timer" class="text-[11px] font-mono text-slate-500 mt-1.5 hidden">
                ({{ __('Wait') }} <span id="timer-seconds" class="text-cyan-400 font-bold">60</span>s)
            </div>
        </div>

        {{-- Logout --}}
        <div class="text-center pt-2 border-t border-white/[0.06]">
            <form action="{{ route('user.logout') }}" method="POST" class="ajax-form" data-action="redirect"
                data-redirect="{{ route('home') }}">
                @csrf
                <button type="submit"
                    class="text-xs font-mono text-slate-500 hover:text-rose-400 transition-colors bg-transparent border-0 p-0 cursor-pointer flex items-center justify-center gap-1.5 mx-auto">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>{{ __('Sign Out / Switch Account') }}</span>
                </button>
            </form>
        </div>

    </div>
@endsection

@section('scripts')
    {{-- if google Captcha is enabled --}}
    @if (getSetting('google_recaptcha') == 'enabled')
        {!! NoCaptcha::renderJs() !!}
    @endif

    <script>
        $(document).ready(function() {
            let resendBtn = $('#resend-btn');
            let countdownTimer = $('#countdown-timer');
            let timerSeconds = $('#timer-seconds');
            let timeLeft = 60;
            let timerInterval;

            let storedThrottle = localStorage.getItem('login_resend_throttle_expiry');
            let backendThrottle = @json($throttle ?? false);

            if (backendThrottle) {
                if (!storedThrottle || storedThrottle < new Date().getTime()) {
                    let expiry = new Date().getTime() + (60 * 1000);
                    localStorage.setItem('login_resend_throttle_expiry', expiry);
                    storedThrottle = expiry;
                }
            }

            if (storedThrottle) {
                let now = new Date().getTime();
                let distance = storedThrottle - now;

                if (distance > 0) {
                    timeLeft = Math.floor(distance / 1000);
                    startTimer();
                } else {
                    localStorage.removeItem('login_resend_throttle_expiry');
                }
            }

            function startTimer() {
                resendBtn.prop('disabled', true);
                countdownTimer.removeClass('hidden');

                if (timerInterval) clearInterval(timerInterval);

                timerInterval = setInterval(function() {
                    timeLeft--;
                    timerSeconds.text(timeLeft);

                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        resendBtn.prop('disabled', false);
                        countdownTimer.addClass('hidden');
                        timeLeft = 60;
                        localStorage.removeItem('login_resend_throttle_expiry');
                    }
                }, 1000);
            }

            $('form[action="{{ route('user.login.resend-otp') }}"]').on('submit', function() {
                let expiry = new Date().getTime() + (60 * 1000);
                localStorage.setItem('login_resend_throttle_expiry', expiry);
                startTimer();
            });

        });
    </script>
@endsection
