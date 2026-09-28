@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-12 pb-24">

    {{-- Global Ethereal Ambient Mesh Gradients --}}
    <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-emerald-500/5 via-cyan-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-10">

        {{-- ══ HEADER HERO DECK WITH INTEGRATED TAB SWITCHER ═══════════════════ --}}
        <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                 style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">
                
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div class="max-w-2xl space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary shadow-[0_0_15px_rgba(226,177,60,0.2)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                            <span>{{ __('Account Security') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                            {{ __('Security Settings') }}
                        </h1>

                        <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed">
                            {{ __('Manage your password, active sessions, and account protection.') }}
                        </p>
                    </div>

                    {{-- Top Segmented Navigation Bar --}}
                    <div class="shrink-0">
                        @include('templates.york.blades.user.account.partials.sidebar')
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ FULL-WIDTH SECURITY CARDS ═════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Left Column: Change Password Vault (7 Cols) --}}
            <div class="lg:col-span-7">
                <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl overflow-hidden h-full">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-10 space-y-8 flex flex-col justify-between h-full" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                        
                        <div class="space-y-6">
                            <div class="flex items-center gap-4 pb-4 border-b border-white/5">
                                <div class="w-12 h-12 rounded-2xl bg-accent-primary/10 border border-accent-primary/20 flex items-center justify-center text-accent-primary shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-white tracking-tight">{{ __('Change Password') }}</h3>
                                    <p class="text-xs text-slate-400 font-medium mt-0.5">{{ __('Choose a strong password to keep your account safe.') }}</p>
                                </div>
                            </div>

                            {{-- SSO Provider Badge --}}
                            <div class="p-4 rounded-2xl bg-black/40 border border-white/5 flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">{{ __('Sign-in Method:') }}</span>
                                <span class="text-xs font-black text-white capitalize font-mono">{{ $user->provider_name ?? 'Email & Password' }}</span>
                            </div>

                            <form id="password-update-form" action="{{ route('user.account.password.update') }}" method="POST" class="space-y-6">
                                @csrf

                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 block">{{ __('Current Password') }}</label>
                                    <div class="relative">
                                        <input type="password" name="current_password" required
                                               class="w-full bg-black/50 border border-white/10 rounded-full pl-6 pr-12 py-3.5 text-xs text-white outline-none focus:border-accent-primary transition-all font-mono"
                                               placeholder="••••••••">
                                        <button type="button" class="password-toggle absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors">
                                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg class="w-4 h-4 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 block">{{ __('New Password') }}</label>
                                        <div class="relative">
                                            <input type="password" name="password" required
                                                   class="w-full bg-black/50 border border-white/10 rounded-full pl-6 pr-12 py-3.5 text-xs text-white outline-none focus:border-accent-primary transition-all font-mono"
                                                   placeholder="••••••••">
                                            <button type="button" class="password-toggle absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors">
                                                <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <svg class="w-4 h-4 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 block">{{ __('Confirm Password') }}</label>
                                        <div class="relative">
                                            <input type="password" name="password_confirmation" required
                                                   class="w-full bg-black/50 border border-white/10 rounded-full pl-6 pr-12 py-3.5 text-xs text-white outline-none focus:border-accent-primary transition-all font-mono"
                                                   placeholder="••••••••">
                                            <button type="button" class="password-toggle absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors">
                                                <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <svg class="w-4 h-4 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-white/5 flex justify-end">
                                    <button type="submit" class="px-8 py-3.5 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(226,177,60,0.3)] active:scale-[0.98] cursor-pointer">
                                        {{ __('Update Password') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Right Column: Active Sessions HUD (5 Cols) --}}
            <div class="lg:col-span-5">
                <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl overflow-hidden h-full">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-10 space-y-6 flex flex-col justify-between h-full" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                        
                        <div class="space-y-6">
                            <div class="flex items-center justify-between pb-4 border-b border-white/5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-accent-primary/10 border border-accent-primary/20 flex items-center justify-center text-accent-primary shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-black text-white tracking-tight">{{ __('Active Sessions') }}</h3>
                                </div>

                                <button type="button" onclick="document.getElementById('logout-others-modal').classList.remove('hidden')"
                                        class="px-4 py-2 rounded-full bg-red-500/10 hover:bg-red-500/20 text-red-400 font-black text-[10px] uppercase tracking-wider border border-red-500/20 transition-all cursor-pointer">
                                    {{ __('Log Out Other Devices') }}
                                </button>
                            </div>

                            {{-- Current Active Device Item --}}
                            <div class="p-5 rounded-2xl bg-accent-primary/5 border border-accent-primary/20 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black font-mono text-white">{{ request()->ip() }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-accent-primary/20 text-accent-primary text-[9px] font-black uppercase tracking-widest border border-accent-primary/30">
                                        {{ __('This Device') }}
                                    </span>
                                </div>
                                <p class="text-[10px] font-mono text-slate-400 truncate">{{ request()->userAgent() }}</p>
                            </div>

                            {{-- Other Active Sessions --}}
                            <div class="space-y-3">
                                @forelse($sessions as $session)
                                    <div class="p-4 rounded-xl bg-black/40 border border-white/5 flex items-center justify-between">
                                        <div>
                                            <span class="block text-xs font-black font-mono text-white">{{ $session->ip_address }}</span>
                                            <span class="text-[9px] font-mono text-slate-500 truncate max-w-[180px] block">{{ $session->user_agent }}</span>
                                        </div>
                                        <span class="text-[9px] font-mono text-slate-400">{{ $session->last_activity->diffForHumans() }}</span>
                                    </div>
                                @empty
                                    <div class="py-6 text-center text-slate-500 text-xs italic font-medium">
                                        {{ __('No other devices are currently logged in.') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Logout Others Modal --}}
<div id="logout-others-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="document.getElementById('logout-others-modal').classList.add('hidden')"></div>
    
    <div class="relative w-full max-w-md p-1.5 rounded-[2.5rem] bg-white/[0.05] border border-white/10 shadow-2xl z-10">
        <div class="rounded-[calc(2.5rem-0.375rem)] p-8 space-y-6" style="background: linear-gradient(145deg, rgba(12,14,22,0.98) 0%, rgba(6,7,12,0.99) 100%);">
            
            <div class="text-center space-y-2">
                <div class="w-16 h-16 mx-auto rounded-3xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-xl font-black text-white">{{ __('Log Out Other Devices') }}</h3>
                <p class="text-xs text-slate-400">{{ __('Enter your current password to log out from all other devices.') }}</p>
            </div>

            <form id="logout-others-form" action="{{ route('user.account.sessions.logout-other') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 block">{{ __('Current Password') }}</label>
                    <input type="password" name="password" required
                           class="w-full bg-black/60 border border-white/10 rounded-full px-6 py-3.5 text-xs text-white outline-none focus:border-accent-primary transition-all font-mono"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('logout-others-modal').classList.add('hidden')"
                            class="flex-1 py-3 rounded-full bg-white/5 hover:bg-white/10 text-white font-bold text-xs uppercase tracking-wider border border-white/10 transition-all cursor-pointer">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 rounded-full bg-red-500 hover:bg-red-600 text-white font-black text-xs uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(239,68,68,0.3)] cursor-pointer">
                        {{ __('Log Out Other Devices') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.password-toggle').on('click', function() {
        const input = $(this).siblings('input');
        const eye = $(this).find('.eye-icon');
        const eyeOff = $(this).find('.eye-off-icon');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            eye.addClass('hidden');
            eyeOff.removeClass('hidden');
        } else {
            input.attr('type', 'password');
            eye.removeClass('hidden');
            eyeOff.addClass('hidden');
        }
    });
});
</script>
@endpush
