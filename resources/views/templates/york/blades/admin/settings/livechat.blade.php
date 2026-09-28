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
                    {{ __('LIVE CHAT & SCRIPTS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Live Chat & Custom Scripts') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Configure live chat widget code, Google Analytics, and custom header or footer scripts') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="$('#livechat-settings-form').submit()"
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
                <form id="livechat-settings-form" action="{{ route('admin.settings.livechat.update') }}" method="POST">
                    @csrf

                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-10 text-xs">
                            
                            {{-- SECTION 1: LIVECHAT WIDGET SCRIPT --}}
                            <div class="space-y-4">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            01
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Live Chat Widget') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Paste your chat widget code (such as Crisp, Tawk.to, Zendesk, or Intercom).') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <textarea name="livechat_scripts" rows="6" placeholder="<!-- Paste third-party live chat snippet here -->"
                                        class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-cyan-300 font-mono rounded-xl p-4 text-xs focus:outline-none resize-none leading-relaxed">{{ $scripts['livechat_scripts'] }}</textarea>
                                    <p class="text-[9px] text-slate-500">{{ __('This code will be placed on all public and user pages.') }}</p>
                                </div>
                            </div>

                            {{-- SECTION 2: HEADER INJECTION SCRIPTS --}}
                            <div class="space-y-4 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            02
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Header Scripts (<head>)') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Add verification tags, Google Tag Manager, Meta Pixel, or analytics scripts.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <textarea name="header_scripts" rows="6" placeholder="<!-- <script async src='https://www.googletagmanager.com/gtag/js?id=...'></script> -->"
                                        class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-cyan-300 font-mono rounded-xl p-4 text-xs focus:outline-none resize-none leading-relaxed">{{ $scripts['header_scripts'] }}</textarea>
                                    <p class="text-[9px] text-slate-500">{{ __('This code will be inserted inside the <head> tag on all pages.') }}</p>
                                </div>
                            </div>

                            {{-- SECTION 3: FOOTER SCRIPTS --}}
                            <div class="space-y-4 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            03
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Footer Scripts (Before </body>)') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Add custom JavaScript, affiliate tracking, or conversion tracking scripts.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <textarea name="footer_scripts" rows="6" placeholder="<!-- Custom footer JS scripts -->"
                                        class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-cyan-300 font-mono rounded-xl p-4 text-xs focus:outline-none resize-none leading-relaxed">{{ $scripts['footer_scripts'] }}</textarea>
                                    <p class="text-[9px] text-slate-500">{{ __('This code will be inserted right before the closing </body> tag.') }}</p>
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
            $('#livechat-settings-form').on('submit', function(e) {
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
                            title: response.message || '{{ __('Scripts updated.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to save scripts.') }}',
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
