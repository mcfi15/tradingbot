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
                    {{ __('SECURITY & AUTHENTICATION') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Security & Authentication') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Configure login security, email verification, password strength, and reCAPTCHA protection') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="$('#security-settings-form').submit()"
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
                <form id="security-settings-form" action="{{ route('admin.settings.security.update') }}" method="POST">
                    @csrf

                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-10 text-xs">
                            
                            {{-- SECTION 1: AUTHENTICATION & LOGIN GUARDS --}}
                            <div class="space-y-6">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            01
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Authentication & Login Security') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Set verification rules for new user registration, login security, and password requirements.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Email Verification') }}</label>
                                        <select name="email_verification" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                            <option value="enabled" {{ getSetting('email_verification') == 'enabled' ? 'selected' : '' }}>{{ __('Enabled (Require verification code on sign up)') }}</option>
                                            <option value="disabled" {{ getSetting('email_verification') == 'disabled' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Two-Factor Login Code (OTP)') }}</label>
                                        <select name="login_otp" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                            <option value="enabled" {{ getSetting('login_otp') == 'enabled' ? 'selected' : '' }}>{{ __('Enabled (Email verification code on each login)') }}</option>
                                            <option value="disabled" {{ getSetting('login_otp') == 'disabled' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Strong Password Requirement') }}</label>
                                        <select name="require_strong_password" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                            <option value="enabled" {{ getSetting('require_strong_password') == 'enabled' ? 'selected' : '' }}>{{ __('Required (Must include uppercase, lowercase, number, and symbol)') }}</option>
                                            <option value="disabled" {{ getSetting('require_strong_password') == 'disabled' ? 'selected' : '' }}>{{ __('Standard (Minimum 8 characters)') }}</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Google reCAPTCHA v2') }}</label>
                                        <select name="google_recaptcha" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                            <option value="enabled" {{ getSetting('google_recaptcha') == 'enabled' ? 'selected' : '' }}>{{ __('Enabled') }}</option>
                                            <option value="disabled" {{ getSetting('google_recaptcha') == 'disabled' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 2: KYC POLICY --}}
                            @if (moduleEnabled('kyc_module'))
                                <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                    <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                                02
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('KYC Verification Rules') }}</h3>
                                                <p class="text-[10px] text-slate-400">{{ __('Require identity verification before users can perform certain account actions.') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach ($kyc_settings as $setting)
                                            <div class="space-y-2">
                                                <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Require KYC for: ') . __($setting['name']) }}</label>
                                                <select name="kyc[{{ $setting['name'] }}]" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                                    <option value="enabled" {{ ($setting['status'] ?? 'disabled') === 'enabled' ? 'selected' : '' }}>{{ __('Required') }}</option>
                                                    <option value="disabled" {{ ($setting['status'] ?? 'disabled') === 'disabled' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
                                                </select>
                                                <p class="text-[9px] text-slate-500">{{ __($setting['description']) }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- SECTION 3: RECAPTCHA KEYS --}}
                            <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            03
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Google reCAPTCHA v2 API Keys') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Enter your reCAPTCHA v2 site key and secret key from the Google reCAPTCHA console.') }}</p>
                                        </div>
                                    </div>
                                    <a href="https://www.google.com/recaptcha/admin" target="_blank"
                                        class="text-cyan-400 hover:text-cyan-300 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                                        <span>{{ __('Get Keys') }}</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Site Key') }}</label>
                                        <input type="text" name="nocaptcha_sitekey" value="{{ sandBoxCredentials(config('captcha.sitekey')) }}" placeholder="6LdmH..."
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Secret Key') }}</label>
                                        <input type="text" name="nocaptcha_secret" value="{{ sandBoxCredentials(config('captcha.secret')) }}" placeholder="6LdmH..."
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>
                                </div>
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
            $('#security-settings-form').on('submit', function(e) {
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
                            title: response.message || '{{ __('Security rules updated.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to save security settings.') }}',
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
