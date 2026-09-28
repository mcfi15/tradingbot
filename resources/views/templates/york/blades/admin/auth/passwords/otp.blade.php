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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white tracking-tight">{{ __('Verify Reset Code') }}</h2>
            <p class="text-slate-400 text-xs mt-1 font-body">
                {{ __('A 6-digit recovery code has been sent to your administrator email.') }}
            </p>
        </div>

        <form action="{{ route('admin.forgot-password.otp.validate') }}" method="POST" class="ajax-form space-y-5"
            data-action="redirect">
            @csrf

            {{-- OTP Code --}}
            <div class="space-y-2">
                <label for="otp_code" class="sr-only">
                    {{ __('Verification Code') }}
                </label>
                <div class="relative">
                    <input type="text" name="otp_code" id="otp_code"
                        class="w-full bg-[#05070d]/90 border border-white/[0.12] rounded-2xl px-4 py-4 text-white placeholder-slate-700 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all text-center tracking-[0.75em] text-2xl font-mono font-bold shadow-inner"
                        placeholder="••••••" required autofocus maxlength="6">
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

        <div class="text-center pt-2 border-t border-white/[0.06]">
            <a href="{{ route('admin.forgot-password') }}"
                class="text-xs font-mono text-slate-400 hover:text-white transition-colors inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>{{ __('Back') }}</span>
            </a>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- if google Captcha is enabled --}}
    @if (getSetting('google_recaptcha') == 'enabled')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endsection
