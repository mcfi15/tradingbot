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
                    {{ __('LOGIN METHODS & OAUTH') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Login Methods & Social Login') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Manage standard email login and third-party social login providers') }}
                </p>
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
                @foreach ($login_methods as $key => $method)
                    <form action="{{ route('admin.settings.login-method.update') }}" method="POST" class="ajax-oauth-form">
                        @csrf
                        <input type="hidden" name="provider" value="{{ $key }}">
                        <input type="hidden" name="status" id="status_{{ $key }}" value="{{ $method['status'] }}">

                        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl transition-all duration-300 {{ $method['status'] === 'enabled' ? 'border-cyan-500/30' : '' }}" id="card_{{ $key }}">
                            <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-6 text-xs">
                                
                                {{-- Card Header --}}
                                <div class="flex items-center justify-between gap-4 pb-4 border-b border-white/[0.06]">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-white/[0.03] border border-white/[0.08] flex items-center justify-center p-2.5 text-cyan-400">
                                            {!! $method['icon'] ?? '' !!}
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ $method['name'] }} {{ __('Login') }}</h3>
                                            <p class="text-[10px] text-slate-400">
                                                {{ $key === 'email' ? __('Allow users to register and sign in with email and password.') : __('Allow users to sign in with one click using their account.') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" class="provider-toggle accent-cyan-400 w-5 h-5 cursor-pointer"
                                            data-provider="{{ $key }}" {{ $method['status'] === 'enabled' ? 'checked' : '' }}>
                                    </div>
                                </div>

                                {{-- OAuth Fields --}}
                                @if ($key !== 'email')
                                    <div class="provider-details space-y-6 {{ $method['status'] === 'enabled' ? '' : 'hidden' }}" id="details_{{ $key }}">
                                        {{-- Callback URLs --}}
                                        <div class="p-4 rounded-2xl bg-white/[0.015] border border-white/[0.04] space-y-3">
                                            <div>
                                                <span class="text-[9px] uppercase font-bold text-slate-500 block mb-1">{{ __('Authorized Redirect URL (User Login)') }}</span>
                                                <div class="flex items-center gap-2 p-2.5 bg-[#05070d] rounded-xl border border-white/[0.06]">
                                                    <code id="user_cb_{{ $key }}" class="text-[10px] text-cyan-300 break-all flex-1">{{ url("/login/$key/callback") }}</code>
                                                    <button type="button" onclick="copyToClipboard('user_cb_{{ $key }}')" class="px-2 py-1 rounded bg-white/[0.04] hover:bg-cyan-400 hover:text-black text-slate-400 font-bold uppercase text-[9px] transition-all cursor-pointer">
                                                        {{ __('Copy') }}
                                                    </button>
                                                </div>
                                            </div>

                                            @if ($key === 'google')
                                                <div>
                                                    <span class="text-[9px] uppercase font-bold text-slate-500 block mb-1">{{ __('Authorized Redirect URL (Admin Login)') }}</span>
                                                    <div class="flex items-center gap-2 p-2.5 bg-[#05070d] rounded-xl border border-white/[0.06]">
                                                        <code id="admin_cb_{{ $key }}" class="text-[10px] text-cyan-300 break-all flex-1">{{ url('/admin/login/google/callback') }}</code>
                                                        <button type="button" onclick="copyToClipboard('admin_cb_{{ $key }}')" class="px-2 py-1 rounded bg-white/[0.04] hover:bg-cyan-400 hover:text-black text-slate-400 font-bold uppercase text-[9px] transition-all cursor-pointer">
                                                            {{ __('Copy') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        @php $prefix = strtoupper($key); @endphp
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Client ID') }}</label>
                                                <input type="text" name="env[{{ $prefix }}_CLIENT_ID]" value="{{ sandBoxCredentials(config("services.$key.client_id")) }}" placeholder="{{ __('Enter Client ID...') }}"
                                                    class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                            </div>

                                            <div class="space-y-1">
                                                <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Client Secret') }}</label>
                                                <input type="password" name="env[{{ $prefix }}_CLIENT_SECRET]" value="{{ sandBoxCredentials(config("services.$key.client_secret")) }}" placeholder="{{ __('Enter Client Secret...') }}"
                                                    class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Footer Submit --}}
                                <div class="pt-4 border-t border-white/[0.06] flex items-center justify-end">
                                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-[10px] tracking-wider transition-all shadow-[0_0_15px_rgba(0,245,255,0.2)] cursor-pointer">
                                        {{ __('Save Settings') }}
                                    </button>
                                </div>

                            </div>
                        </div>
                    </form>
                @endforeach
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function copyToClipboard(id) {
            const text = document.getElementById(id).innerText;
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ __('Redirect URL copied.') }}',
                    showConfirmButton: false,
                    timer: 1500,
                    customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                });
            });
        }

        $(document).ready(function() {
            $('.provider-toggle').on('change', function() {
                const provider = $(this).data('provider');
                const isEnabled = $(this).is(':checked');
                $('#status_' + provider).val(isEnabled ? 'enabled' : 'disabled');
                
                if (isEnabled) {
                    $('#details_' + provider).slideDown(200);
                    $('#card_' + provider).addClass('border-cyan-500/30');
                } else {
                    $('#details_' + provider).slideUp(200);
                    $('#card_' + provider).removeClass('border-cyan-500/30');
                    $(this).closest('form').submit();
                }
            });

            $('.ajax-oauth-form').on('submit', function(e) {
                e.preventDefault();
                const $btn = $(this).find('button[type="submit"]');
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
                            title: response.message || '{{ __('Provider updated.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to save provider.') }}',
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
