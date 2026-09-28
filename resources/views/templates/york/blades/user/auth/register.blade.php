@extends('templates.york.blades.layouts.auth')

@section('content')
    <div class="space-y-6">
        @if (!session('register_info'))
            {{-- Header --}}
            <div>
                <h3 class="text-2xl font-bold text-white tracking-tight">
                    {{ __('Create Account') }}
                </h3>
                <p class="text-slate-400 text-xs mt-1.5 font-body">
                    {{ __('Get started in seconds and deploy intelligent trading algorithms.') }}
                </p>
            </div>

            {{-- Registration Options Selection --}}
            <div id="auth-selection" class="space-y-3" @if ($only_email) style="display: none;" @endif>
                @foreach ($login_methods as $method => $data)
                    @if ($method === 'email')
                        <button type="button" onclick="toggleEmailForm()"
                            class="flex items-center justify-center gap-3 w-full bg-white/[0.04] hover:bg-white/[0.08] text-white font-semibold py-3 px-4 rounded-2xl border border-white/10 hover:border-cyan-500/40 transition-all duration-300 transform hover:-translate-y-0.5 cursor-pointer shadow-lg text-sm">
                            {!! $data['icon'] !!}
                            <span>{{ __('Register with Email') }}</span>
                        </button>
                    @else
                        <a href="{{ route('user.login.social', ['provider' => $method]) }}"
                            class="flex items-center justify-center gap-3 w-full {{ $method === 'google' ? 'bg-white text-slate-900 border-white hover:bg-slate-100' : 'bg-white/[0.04] text-white border-white/10 hover:bg-white/[0.08]' }} font-semibold py-3 px-4 rounded-2xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 border cursor-pointer text-sm">
                            {!! $data['icon'] !!}
                            <span>{{ __('Continue with ' . $data['name']) }}</span>
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- Email Registration Form Container --}}
            <div id="email-register-container" @if (!$only_email) style="display: none;" @endif class="space-y-5">
                @if (!$only_email)
                    <button type="button" onclick="toggleSelection()"
                        class="text-cyan-400 hover:text-cyan-300 text-xs font-mono flex items-center gap-2 transition-colors cursor-pointer group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 transform group-hover:-translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12" />
                            <polyline points="12 19 5 12 12 5" />
                        </svg>
                        {{ __('Back to options') }}
                    </button>
                @endif

                @php
                    $email_verification = getSetting('email_verification');
                @endphp
                <form action="{{ route('user.register-validate') }}" method="POST"
                    data-action="{{ $email_verification == 'disabled' ? 'redirect' : 'reload' }}"
                    @if ($email_verification == 'disabled') data-redirect="{{ route('user.dashboard') }}" @endif
                    class="ajax-form space-y-4">
                    @csrf

                    {{-- Name Fields Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label for="first_name"
                                class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">{{ __('First Name') }}</label>
                            <input type="text" name="first_name" id="first_name"
                                class="w-full bg-[#05070d]/80 border border-white/[0.1] rounded-2xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all font-body text-sm shadow-inner"
                                placeholder="{{ __('John') }}" required>
                        </div>

                        <div class="space-y-1.5">
                            <label for="last_name"
                                class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" id="last_name"
                                class="w-full bg-[#05070d]/80 border border-white/[0.1] rounded-2xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all font-body text-sm shadow-inner"
                                placeholder="{{ __('Doe') }}" required>
                        </div>
                    </div>

                    {{-- Username & Email Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label for="username"
                                class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">{{ __('Username') }}</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-mono text-slate-500">@</span>
                                <input type="text" name="username" id="username"
                                    class="w-full bg-[#05070d]/80 border border-white/[0.1] rounded-2xl pl-8 pr-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all font-mono text-sm shadow-inner"
                                    placeholder="johndoe" required>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label for="email"
                                class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">{{ __('Email Address') }}</label>
                            <input type="email" name="email" id="email"
                                class="w-full bg-[#05070d]/80 border border-white/[0.1] rounded-2xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all font-body text-sm shadow-inner"
                                placeholder="{{ __('john@example.com') }}" required>
                        </div>
                    </div>

                    {{-- Password Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label for="password"
                                class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">{{ __('Password') }}</label>
                            <input type="password" name="password" id="password"
                                class="w-full bg-[#05070d]/80 border border-white/[0.1] rounded-2xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all font-body text-sm shadow-inner"
                                placeholder="••••••••" required>
                        </div>

                        <div class="space-y-1.5">
                            <label for="password_confirmation"
                                class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="w-full bg-[#05070d]/80 border border-white/[0.1] rounded-2xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all font-body text-sm shadow-inner"
                                placeholder="••••••••" required>
                        </div>
                    </div>

                    {{-- Referral Code --}}
                    <div class="space-y-1.5">
                        <label for="referral_code"
                            class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">
                            {{ __('Referral Code') }} <span class="text-slate-500 font-sans font-normal text-xs">({{ __('Optional') }})</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="referral_code" id="referral_code"
                                @if (session()->has('referrer_code')) readonly value="{{ session()->get('referrer_code') }}" @endif
                                class="w-full bg-[#05070d]/80 border border-white/[0.1] rounded-2xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all font-mono text-sm shadow-inner @if(session()->has('referrer_code')) text-emerald-400 font-bold @endif"
                                placeholder="PROMO_CODE">
                            @if (session()->has('referrer_code'))
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] font-mono text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-500/20">
                                    {{ __('Applied') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Google Captcha --}}
                    @if (getSetting('google_recaptcha') == 'enabled')
                        <div class="pt-2 flex justify-center">
                            {!! NoCaptcha::display(['data-theme' => 'dark']) !!}
                            @error('g-recaptcha-response')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-cyan-400 via-cyan-500 to-blue-600 hover:from-cyan-300 hover:to-blue-500 text-black font-extrabold py-3.5 px-4 rounded-2xl shadow-[0_0_30px_rgba(0,245,255,0.25)] transition-all duration-300 transform hover:-translate-y-0.5 relative overflow-hidden group cursor-pointer text-sm font-mono tracking-wide mt-2">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <span>{{ __('Create Free Account') }}</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </span>
                        <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </button>
                </form>
            </div>
        @else
            {{-- Email Verification Step --}}
            <div class="space-y-6 relative">

                {{-- Start Over Button --}}
                <form action="{{ route('user.register-cancel') }}" method="POST"
                    class="ajax-form absolute -top-4 right-0 z-20" data-action="redirect"
                    data-redirect="{{ route('user.register') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-1.5 text-slate-400 hover:text-white text-[11px] font-mono transition-colors bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.08] px-3 py-1.5 rounded-xl cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5M12 19l-7-7 7-7" />
                        </svg>
                        <span>{{ __('Start Over') }}</span>
                    </button>
                </form>

                <div class="text-center pt-4">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 mb-4 shadow-inner">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white tracking-tight mb-2">{{ __('Verify Your Email') }}</h3>
                    <p class="text-slate-400 text-xs font-body">
                        {{ __('We sent a 6-digit code to') }} <br>
                        <span class="text-cyan-400 font-mono font-bold text-sm">{{ session('register_info.email') }}</span>
                    </p>
                </div>

                <form action="{{ route('user.email-verification') }}" method="POST" data-action="redirect"
                    data-redirect="{{ route('user.dashboard') }}" class="ajax-form space-y-5">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('register_info.email') }}">

                    <div class="space-y-2 text-center">
                        <label for="otp_code" class="sr-only">{{ __('Verification Code') }}</label>
                        <div class="relative">
                            <input type="text" name="otp_code" id="otp_code"
                                class="w-full bg-[#05070d]/90 border border-white/[0.12] rounded-2xl px-4 py-4 text-white placeholder-slate-700 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all text-center tracking-[0.75em] text-2xl font-mono font-bold shadow-inner"
                                placeholder="••••••" required maxlength="6" autofocus>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-cyan-400 via-cyan-500 to-blue-600 hover:from-cyan-300 hover:to-blue-500 text-black font-extrabold py-3.5 px-4 rounded-2xl shadow-[0_0_30px_rgba(0,245,255,0.25)] transition-all duration-300 transform hover:-translate-y-0.5 relative overflow-hidden group cursor-pointer text-sm font-mono tracking-wide">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <span>{{ __('Confirm & Activate Account') }}</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </span>
                        <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </button>
                </form>

                <div class="text-center mt-6">
                    <p class="text-slate-400 text-xs mb-1">{{ __("Didn't receive the code?") }}</p>
                    <form action="{{ route('user.resend-verification') }}" method="POST"
                        class="ajax-form inline-block">
                        @csrf
                        <button type="submit" id="resend-btn"
                            class="text-cyan-400 font-mono font-bold text-xs hover:text-cyan-300 transition-colors disabled:opacity-40 disabled:cursor-not-allowed bg-transparent border-0 p-0 cursor-pointer">
                            {{ __('Resend Verification Code') }}
                        </button>
                    </form>
                    <div id="countdown-timer" class="text-[11px] font-mono text-slate-500 mt-1.5 hidden">
                        ({{ __('Wait') }} <span id="timer-seconds" class="text-cyan-400 font-bold">60</span>s)
                    </div>
                </div>
            </div>
        @endif

        {{-- Sign In Footer --}}
        <div class="pt-4 border-t border-white/[0.06] text-center">
            <p class="text-xs text-slate-400 font-body">
                {{ __('Already have an account?') }}
                <a href="{{ route('user.login') }}"
                    class="font-mono font-bold text-cyan-400 hover:text-cyan-300 transition-colors ml-1">
                    {{ __('Sign In') }}
                </a>
            </p>
        </div>
    </div>
@endsection

@section('scripts')
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

            let storedThrottle = localStorage.getItem('resend_throttle_expiry');
            let backendThrottle = @json($throttle ?? false);

            if (backendThrottle) {
                if (!storedThrottle || storedThrottle < new Date().getTime()) {
                    let expiry = new Date().getTime() + (60 * 1000);
                    localStorage.setItem('resend_throttle_expiry', expiry);
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
                    localStorage.removeItem('resend_throttle_expiry');
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
                        localStorage.removeItem('resend_throttle_expiry');
                    }
                }, 1000);
            }

            $('form[action="{{ route('user.resend-verification') }}"]').on('submit', function() {
                let expiry = new Date().getTime() + (60 * 1000);
                localStorage.setItem('resend_throttle_expiry', expiry);
                startTimer();
            });

        });

        function toggleEmailForm() {
            document.getElementById('auth-selection').style.display = 'none';
            document.getElementById('email-register-container').style.display = 'block';
        }

        function toggleSelection() {
            document.getElementById('auth-selection').style.display = 'block';
            document.getElementById('email-register-container').style.display = 'none';
        }
    </script>
@endsection
