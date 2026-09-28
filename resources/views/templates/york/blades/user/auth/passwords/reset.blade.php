@extends('templates.york.blades.layouts.auth')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h3 class="text-2xl font-bold text-white tracking-tight">
                {{ __('Set New Password') }}
            </h3>
            <p class="text-slate-400 text-xs mt-1.5 font-body">
                {{ __('Choose a strong new password to protect your automated trading account.') }}
            </p>
        </div>

        {{-- Form --}}
        <form action="{{ route('user.reset-password.update') }}" method="POST" class="ajax-form space-y-4"
            data-action="redirect">
            @csrf

            {{-- New Password --}}
            <div class="space-y-1.5">
                <label for="password"
                    class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">
                    {{ __('New Password') }}
                </label>
                <div class="relative">
                    <input type="password" name="password" id="password"
                        class="w-full bg-[#05070d]/80 border border-white/[0.1] rounded-2xl px-4 py-3.5 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all font-body text-sm shadow-inner"
                        placeholder="••••••••" required autofocus>
                </div>
                @error('password')
                    <p class="text-rose-400 text-xs mt-1 font-mono">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="space-y-1.5">
                <label for="password_confirmation"
                    class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">
                    {{ __('Confirm New Password') }}
                </label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full bg-[#05070d]/80 border border-white/[0.1] rounded-2xl px-4 py-3.5 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all font-body text-sm shadow-inner"
                        placeholder="••••••••" required>
                </div>
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full bg-gradient-to-r from-cyan-400 via-cyan-500 to-blue-600 hover:from-cyan-300 hover:to-blue-500 text-black font-extrabold py-3.5 px-4 rounded-2xl shadow-[0_0_30px_rgba(0,245,255,0.25)] transition-all duration-300 transform hover:-translate-y-0.5 relative overflow-hidden group cursor-pointer text-sm font-mono tracking-wide mt-2">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <span>{{ __('Update & Secure Password') }}</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </button>
        </form>
    </div>
@endsection
