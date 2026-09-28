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
            <div class="flex items-center justify-center mb-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[9px] font-bold uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('ADMINISTRATOR CONSOLE') }}
                </span>
            </div>
            <h2 class="text-xl font-bold text-white tracking-tight">{{ __('Password Recovery') }}</h2>
            <p class="text-slate-400 text-xs mt-1 font-body">
                {{ __('Enter your administrator email address and we will send you a 6-digit verification code.') }}
            </p>
        </div>

        <form action="{{ route('admin.forgot-password.send') }}" method="POST" class="ajax-form space-y-5"
            data-action="redirect">
            @csrf

            {{-- Email --}}
            <div class="space-y-1.5">
                <label for="email" class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">
                    {{ __('Admin Email Address') }}
                </label>
                <div class="relative">
                    <input type="email" name="email" id="email"
                        class="w-full bg-[#05070d]/80 border border-white/[0.1] rounded-2xl px-4 py-3.5 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all font-body text-sm shadow-inner"
                        placeholder="{{ __('admin@domain.com') }}" required autofocus>
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
                    <span>{{ __('Send Verification Code') }}</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </button>
        </form>

        <div class="text-center pt-2 border-t border-white/[0.06]">
            <a href="{{ route('admin.login') }}" class="text-xs font-mono text-slate-400 hover:text-white transition-colors inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>{{ __('Back to Login') }}</span>
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
