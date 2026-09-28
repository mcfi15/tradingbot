@extends('templates.york.blades.layouts.auth')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h3 class="text-2xl font-bold text-white tracking-tight">
                {{ __('Welcome Back') }}
            </h3>
            <p class="text-slate-400 text-xs mt-1.5 font-body">
                {{ __('Sign in to access your automated trading portfolio.') }}
            </p>
        </div>

        {{-- Login Options Selection --}}
        <div id="auth-selection" class="space-y-3" @if ($only_email) style="display: none;" @endif>
            @foreach ($login_methods as $method => $data)
                @if ($method === 'email')
                    <button type="button" onclick="toggleEmailForm()"
                        class="flex items-center justify-center gap-3 w-full bg-white/[0.04] hover:bg-white/[0.08] text-white font-semibold py-3 px-4 rounded-2xl border border-white/10 hover:border-cyan-500/40 transition-all duration-300 transform hover:-translate-y-0.5 cursor-pointer shadow-lg text-sm">
                        {!! $data['icon'] !!}
                        <span>{{ __('Continue with Email') }}</span>
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

        {{-- Email Login Form Container --}}
        <div id="email-login-container" @if (!$only_email) style="display: none;" @endif class="space-y-5">
            @if (!$only_email)
                <button type="button" onclick="toggleSelection()"
                    class="text-cyan-400 hover:text-cyan-300 text-xs font-mono flex items-center gap-2 transition-colors cursor-pointer group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 transform group-hover:-translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12" />
                        <polyline points="12 19 5 12 12 5" />
                    </svg>
                    {{ __('Back to login options') }}
                </button>
            @endif

            <form action="{{ route('user.login.validate') }}" method="POST" class="ajax-form space-y-4"
                data-action="redirect">
                @csrf

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label for="email"
                        class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">
                        {{ __('Email Address') }}
                    </label>
                    <div class="relative">
                        <input type="email" name="email" id="email"
                            class="w-full bg-[#05070d]/80 border border-white/[0.1] rounded-2xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all font-body text-sm shadow-inner"
                            placeholder="{{ __('john@example.com') }}" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                {{-- Password --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password"
                            class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">
                            {{ __('Password') }}
                        </label>
                        <a href="{{ route('user.forgot-password') }}"
                            class="text-xs text-cyan-400 hover:text-cyan-300 transition-colors font-mono">
                            {{ __('Forgot password?') }}
                        </a>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                            class="w-full bg-[#05070d]/80 border border-white/[0.1] rounded-2xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all font-body text-sm shadow-inner"
                            placeholder="••••••••" required>
                    </div>
                </div>

                {{-- Remember Me Checkbox --}}
                <div class="flex items-center pt-1">
                    <label class="relative flex items-center gap-2.5 cursor-pointer text-xs text-slate-400 hover:text-slate-300 transition-colors select-none">
                        <input id="remember" name="remember" type="checkbox"
                            class="w-4 h-4 rounded-lg bg-[#05070d] border border-white/20 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-0 transition-colors">
                        <span>{{ __('Remember this browser') }}</span>
                    </label>
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
                    class="w-full bg-gradient-to-r from-cyan-400 via-cyan-500 to-blue-600 hover:from-cyan-300 hover:to-blue-500 text-black font-extrabold py-3.5 px-4 rounded-2xl shadow-[0_0_30px_rgba(0,245,255,0.25)] transition-all duration-300 transform hover:-translate-y-0.5 relative overflow-hidden group cursor-pointer text-sm font-mono tracking-wide">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <span>{{ __('Sign In to Dashboard') }}</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </span>
                    <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </button>
            </form>
        </div>

        {{-- Register Link --}}
        <div class="pt-4 border-t border-white/[0.06] text-center">
            <p class="text-xs text-slate-400 font-body">
                {{ __("Don't have an account?") }}
                <a href="{{ route('user.register') }}"
                    class="font-mono font-bold text-cyan-400 hover:text-cyan-300 transition-colors ml-1">
                    {{ __('Create Free Account') }}
                </a>
            </p>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- if google Captcha is enabled --}}
    @if (getSetting('google_recaptcha') == 'enabled')
        {!! NoCaptcha::renderJs() !!}
    @endif

    <script>
        function toggleEmailForm() {
            document.getElementById('auth-selection').style.display = 'none';
            document.getElementById('email-login-container').style.display = 'block';
        }

        function toggleSelection() {
            document.getElementById('auth-selection').style.display = 'block';
            document.getElementById('email-login-container').style.display = 'none';
        }
    </script>
@endsection
