@extends('templates.york.blades.admin.layouts.admin')

@push('css')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow {
            border-color: rgba(255, 255, 255, 0.1) !important;
            background: rgba(255, 255, 255, 0.02) !important;
            border-radius: 16px 16px 0 0 !important;
            padding: 12px 16px !important;
        }
        .ql-container.ql-snow {
            border-color: rgba(255, 255, 255, 0.1) !important;
            border-radius: 0 0 16px 16px !important;
            font-family: inherit !important;
            font-size: 14px !important;
            min-height: 320px !important;
            background: rgba(5, 7, 13, 0.6) !important;
        }
        .ql-editor {
            min-height: 320px !important;
            color: #f8fafc !important;
            padding: 20px !important;
        }
        .ql-editor.ql-blank::before {
            color: rgba(148, 163, 184, 0.4) !important;
            font-style: normal !important;
            left: 20px !important;
        }
        .ql-snow .ql-stroke {
            stroke: #94a3b8 !important;
        }
        .ql-snow .ql-fill {
            fill: #94a3b8 !important;
        }
        .ql-snow .ql-picker {
            color: #94a3b8 !important;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto space-y-8 mb-12">

        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.index') }}"
                    class="w-11 h-11 rounded-2xl bg-white/[0.03] border border-white/[0.08] hover:border-cyan-400/40 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer shadow-lg group">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 text-[10px] font-mono font-bold uppercase tracking-[0.2em] mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
                        {{ __('GROUP EMAIL') }}
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                        <span>{{ __('Send Group Email') }}</span>
                    </h1>
                    <p class="text-slate-400 font-mono text-xs mt-0.5 tracking-wider uppercase">
                        {{ __('Send an announcement or notification to a group of users.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- MAIN EMAIL FORM --}}
        {{-- ==================================================================================== --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
            <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8">

                <form id="bulk-email-form" action="{{ route('admin.users.send-bulk-email') }}" method="POST" class="space-y-8 font-mono text-xs">
                    @csrf

                    {{-- Audience Selection --}}
                    <div>
                        <label class="block font-bold text-slate-400 uppercase tracking-widest mb-3">
                            {{ __('1. Choose Recipients') }}
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            {{-- All Users --}}
                            <label class="audience-card relative flex flex-col justify-between p-5 rounded-2xl border border-white/[0.08] bg-white/[0.02] hover:border-cyan-400/40 cursor-pointer transition-all group">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold text-white uppercase">{{ __('All Users') }}</span>
                                    <input type="radio" name="audience" value="all" checked class="w-4 h-4 text-cyan-400 accent-cyan-400 cursor-pointer">
                                </div>
                                <p class="text-[10px] text-slate-400 font-sans leading-relaxed">
                                    {{ __('Send to every registered user on the platform.') }}
                                </p>
                            </label>

                            {{-- Active Users --}}
                            <label class="audience-card relative flex flex-col justify-between p-5 rounded-2xl border border-white/[0.08] bg-white/[0.02] hover:border-emerald-400/40 cursor-pointer transition-all group">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold text-emerald-400 uppercase">{{ __('Active Users Only') }}</span>
                                    <input type="radio" name="audience" value="active" class="w-4 h-4 text-emerald-400 accent-emerald-400 cursor-pointer">
                                </div>
                                <p class="text-[10px] text-slate-400 font-sans leading-relaxed">
                                    {{ __('Send only to active users who can log in (excludes banned users).') }}
                                </p>
                            </label>

                            {{-- Email Unverified --}}
                            <label class="audience-card relative flex flex-col justify-between p-5 rounded-2xl border border-white/[0.08] bg-white/[0.02] hover:border-amber-400/40 cursor-pointer transition-all group">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold text-amber-400 uppercase">{{ __('Unverified Users Only') }}</span>
                                    <input type="radio" name="audience" value="unverified" class="w-4 h-4 text-amber-400 accent-amber-400 cursor-pointer">
                                </div>
                                <p class="text-[10px] text-slate-400 font-sans leading-relaxed">
                                    {{ __('Send only to users who have not yet verified their email.') }}
                                </p>
                            </label>
                        </div>
                    </div>

                    {{-- Subject --}}
                    <div>
                        <label class="block font-bold text-slate-400 uppercase tracking-widest mb-2">
                            {{ __('2. Email Subject') }}
                        </label>
                        <input type="text" name="subject" required placeholder="{{ __('Important update regarding your account...') }}"
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-all">
                    </div>

                    {{-- Dynamic Template Variables Helper --}}
                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.06]">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ __('Personalization Tags') }}</span>
                            <span class="text-[9px] text-cyan-400">{{ __('Click to insert into your message') }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" class="tag-insert-btn px-2.5 py-1 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/20 text-[10px] font-bold cursor-pointer" data-tag="{username}">
                                {username}
                            </button>
                            <button type="button" class="tag-insert-btn px-2.5 py-1 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/20 text-[10px] font-bold cursor-pointer" data-tag="{fullname}">
                                {fullname}
                            </button>
                            <button type="button" class="tag-insert-btn px-2.5 py-1 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/20 text-[10px] font-bold cursor-pointer" data-tag="{email}">
                                {email}
                            </button>
                            <button type="button" class="tag-insert-btn px-2.5 py-1 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/20 text-[10px] font-bold cursor-pointer" data-tag="{balance}">
                                {balance}
                            </button>
                        </div>
                    </div>

                    {{-- Rich Editor --}}
                    <div>
                        <label class="block font-bold text-slate-400 uppercase tracking-widest mb-2">
                            {{ __('3. Email Message') }}
                        </label>
                        <div id="email-editor" class="w-full"></div>
                        <input type="hidden" name="message" id="message-input">
                    </div>

                    {{-- Actions --}}
                    <div class="pt-6 border-t border-white/[0.06] flex items-center justify-between">
                        <a href="{{ route('admin.users.index') }}"
                            class="px-6 py-3 rounded-full border border-white/[0.1] text-slate-400 hover:text-white font-bold text-xs uppercase tracking-wider transition-all cursor-pointer">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" id="send-broadcast-btn"
                            class="px-8 py-3 rounded-full bg-gradient-to-r from-cyan-400 to-purple-500 hover:from-cyan-300 hover:to-purple-400 text-[#050507] font-black text-xs uppercase tracking-wider shadow-[0_0_25px_rgba(0,245,255,0.35)] transition-all cursor-pointer flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <span>{{ __('Send Email') }}</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var quill = new Quill('#email-editor', {
                theme: 'snow',
                placeholder: '{{ __('Type your message here...') }}',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'clean']
                    ]
                }
            });

            // Tag Insertion
            $('.tag-insert-btn').on('click', function() {
                const tag = $(this).data('tag');
                const range = quill.getSelection(true);
                quill.insertText(range.index, tag);
                quill.setSelection(range.index + tag.length);
            });

            // Form Submit
            $('#bulk-email-form').on('submit', function(e) {
                const content = quill.root.innerHTML.trim();
                if (quill.getText().trim().length === 0) {
                    e.preventDefault();
                    Swal.fire({toast:true, position:'top-end', icon:'error', title:'{{ __('Please enter a message to send.') }}', showConfirmButton:false, timer:2500});
                    return false;
                }

                $('#message-input').val(content);
                $('#send-broadcast-btn').prop('disabled', true).html('{{ __('Sending Email...') }}');
            });
        });
    </script>
@endpush
