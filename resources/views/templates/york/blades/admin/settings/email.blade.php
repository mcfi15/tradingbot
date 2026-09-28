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
                    {{ __('EMAIL SETTINGS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Email Settings') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Configure SMTP server details, automated notification triggers, and email templates') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="$('#email-settings-form').submit()"
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
                <form id="email-settings-form" action="{{ route('admin.settings.email.update') }}" method="POST">
                    @csrf

                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-10 text-xs">
                            
                            {{-- SECTION 1: SMTP SERVER CONFIGURATION --}}
                            <div class="space-y-6">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            01
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('SMTP Server Details') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Enter your email provider\'s SMTP credentials for sending outgoing emails.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Mail Driver') }}</label>
                                        <select name="mail_driver"
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                            <option value="smtp" {{ ($mail_config['driver'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                            <option value="mailgun" {{ ($mail_config['driver'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                            <option value="ses" {{ ($mail_config['driver'] ?? '') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                            <option value="sendmail" {{ ($mail_config['driver'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2 md:col-span-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('SMTP Host') }}</label>
                                        <input type="text" name="mail_host" value="{{ sandBoxCredentials($mail_config['host']) ?? '' }}" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('SMTP Port') }}</label>
                                        <input type="number" name="mail_port" value="{{ $mail_config['port'] ?? 587 }}" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Encryption') }}</label>
                                        <select name="mail_encryption"
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                            <option value="" {{ ($mail_config['encryption'] ?? '') == '' ? 'selected' : '' }}>{{ __('None') }}</option>
                                            <option value="tls" {{ ($mail_config['encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ ($mail_config['encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('SMTP Username') }}</label>
                                        <input type="text" name="mail_username" value="{{ sandBoxCredentials($mail_config['username']) ?? '' }}"
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>

                                    <div class="space-y-2 md:col-span-3">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('SMTP Password') }}</label>
                                        <div class="relative">
                                            <input type="password" name="mail_password" id="mail_password" value="{{ sandBoxCredentials($mail_config['password']) ?? '' }}"
                                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none pr-12">
                                            <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white cursor-pointer">
                                                <svg class="w-4 h-4" id="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 2: SENDER IDENTITY --}}
                            <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            02
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Sender Information') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('The outgoing name and email address that appear in recipients\' inboxes.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('From Name') }}</label>
                                        <input type="text" name="mail_from_name" value="{{ $mail_config['from_name'] ?? '' }}" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('From Email Address') }}</label>
                                        <input type="email" name="mail_from_address" value="{{ sandBoxCredentials($mail_config['from_address']) ?? '' }}" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 3: QUEUE & GLOBAL FEATURES --}}
                            <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            03
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Notification Triggers') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Choose which automated events send emails to users.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                                        <div class="space-y-0.5">
                                            <span class="text-white font-bold block">{{ __('Background Email Queue') }}</span>
                                            <span class="text-[10px] text-slate-400">{{ __('Send emails in the background to speed up page loads.') }}</span>
                                        </div>
                                        <input type="checkbox" name="email_queue" value="enabled" class="accent-cyan-400 w-4 h-4 cursor-pointer" {{ $email_queue == 'enabled' ? 'checked' : '' }}>
                                    </div>

                                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                                        <div class="space-y-0.5">
                                            <span class="text-white font-bold block">{{ __('Include Date in Subject') }}</span>
                                            <span class="text-[10px] text-slate-400">{{ __('Add current date/time to email subjects.') }}</span>
                                        </div>
                                        <input type="checkbox" name="append_date_to_emails" value="enabled" class="accent-cyan-400 w-4 h-4 cursor-pointer" {{ $append_date == 'enabled' ? 'checked' : '' }}>
                                    </div>
                                </div>

                                {{-- Specific Notification Triggers Grid --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2">
                                    @foreach ($notifications['notifications'] ?? [] as $key => $item)
                                        @php
                                            $label_append = str_contains($key, 'email') ? '' : ' Email';
                                            $label = ucwords(str_replace('_', ' ', $key)) . $label_append;
                                        @endphp
                                        <div class="p-3.5 rounded-2xl bg-white/[0.015] border border-white/[0.04] flex items-center justify-between">
                                            <div>
                                                <span class="text-white font-bold block text-[11px]">{{ __($label) }}</span>
                                                <span class="text-[9px] text-slate-500">{{ __($item['tip'] ?? '') }}</span>
                                            </div>
                                            <input type="checkbox" name="notifications[{{ $key }}]" value="enabled" class="accent-cyan-400 w-4 h-4 cursor-pointer"
                                                {{ ($item['status'] ?? 'enabled') == 'enabled' ? 'checked' : '' }}>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- SECTION 4: TEST EMAIL & SUBMISSION --}}
                            <div class="pt-6 border-t border-white/[0.06] flex flex-col md:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-2 w-full md:w-auto">
                                    <input type="email" id="test-email-input" placeholder="{{ __('recipient@domain.com') }}"
                                        class="bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none w-full md:w-64">
                                    <button type="button" id="send-test-btn"
                                        class="px-4 py-2.5 rounded-xl bg-white/[0.04] hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-400 border border-white/[0.08] font-bold uppercase text-[10px] tracking-wider transition-all shrink-0 cursor-pointer">
                                        {{ __('Send Test Email') }}
                                    </button>
                                </div>

                                <button type="submit" id="submit-btn"
                                    class="w-full md:w-auto px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{ __('Save Changes') }}</span>
                                </button>
                            </div>

                        </div>
                    </div>
                </form>

                {{-- Email Templates Accordion --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-6 text-xs">
                        <div class="flex items-center justify-between cursor-pointer" onclick="$('#templates-container').slideToggle(200)">
                            <div>
                                <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Email Templates') }}</h3>
                                <p class="text-[10px] text-slate-400">{{ __('View and edit raw email templates.') }}</p>
                            </div>
                            <button type="button" class="p-2 rounded-xl bg-white/[0.04] text-slate-400 hover:text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>

                        <div id="templates-container" class="space-y-3">
                            @foreach ($email_templates as $template)
                                <div class="p-4 rounded-2xl bg-white/[0.015] border border-white/[0.04] flex items-center justify-between gap-4">
                                    <div>
                                        <span class="text-white font-bold block">{{ $template['name'] }}</span>
                                        <span class="text-[9px] text-slate-500">{{ sandBoxCredentials($template['path']) }}</span>
                                    </div>
                                    <a href="{{ route('admin.file-manager.code-editor', ['path' => config('app.env') == 'sandbox' ? '' : $template['path']]) }}" target="_blank"
                                        class="px-4 py-2 rounded-xl bg-white/[0.04] hover:bg-cyan-400 hover:text-black text-slate-300 font-bold uppercase text-[10px] tracking-wider transition-all flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>{{ __('Edit Template') }}</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#toggle-password').on('click', function() {
                const $input = $('#mail_password');
                const isPass = $input.attr('type') === 'password';
                $input.attr('type', isPass ? 'text' : 'password');
            });

            $('#email-settings-form').on('submit', function(e) {
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
                            title: response.message || '{{ __('Email settings saved.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to update email settings.') }}',
                            customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).removeClass('opacity-50');
                    }
                });
            });

            $('#send-test-btn').on('click', function() {
                const email = $('#test-email-input').val();
                if (!email) {
                    Swal.fire({ icon: 'warning', title: '{{ __('Missing Email') }}', text: '{{ __('Please enter an email address to test.') }}', customClass: { popup: 'bg-[#090c14] text-white font-mono' } });
                    return;
                }

                const $btn = $(this);
                $btn.prop('disabled', true).text('{{ __('Testing...') }}');

                $.ajax({
                    url: "{{ route('admin.settings.email.test') }}",
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", email: email },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('SMTP Connected') }}',
                            text: response.message || '{{ __('Test email delivered successfully!') }}',
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono' }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('SMTP Error') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to send test email. Verify host, port, credentials.') }}',
                            customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('{{ __('Send Test') }}');
                    }
                });
            });
        });
    </script>
@endpush
