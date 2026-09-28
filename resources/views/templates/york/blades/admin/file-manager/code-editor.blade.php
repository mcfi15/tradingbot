@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-6 mb-12 font-mono">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
            <div class="rounded-[2rem] bg-[#090c14]/95 overflow-hidden flex flex-col min-h-[calc(100vh-180px)]">
                
                {{-- Editor Header --}}
                <div class="px-6 py-4 border-b border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/[0.01]">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.file-manager.index') }}"
                            class="p-2.5 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-400 hover:text-white transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                                <h2 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Code Editor') }}</h2>
                            </div>
                            <div class="flex items-center gap-2 text-xs mt-0.5">
                                <code class="text-cyan-300 font-bold bg-cyan-500/10 px-2 py-0.5 rounded text-[11px]">{{ basename($path) }}</code>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div id="save-status" class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest hidden flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ __('Writing to Disk...') }}
                        </div>
                        <button type="button" id="save-btn"
                            class="px-5 py-2.5 bg-cyan-400 hover:bg-cyan-300 text-[#050507] text-xs font-black uppercase tracking-wider rounded-xl shadow-[0_0_20px_rgba(0,245,255,0.3)] transition-all flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            <span>{{ __('Save (Ctrl+S)') }}</span>
                        </button>
                    </div>
                </div>

                {{-- Editor Container --}}
                <div class="flex-1 relative min-h-[620px]" id="editor-container">
                    <div id="monaco-editor" class="absolute inset-0"></div>

                    {{-- Loading Overlay --}}
                    <div id="editor-loading"
                        class="absolute inset-0 bg-[#090c14] flex flex-col items-center justify-center z-50 transition-opacity duration-300">
                        <div class="w-10 h-10 border-2 border-cyan-500/20 border-t-cyan-400 rounded-full animate-spin mb-3"></div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest animate-pulse">{{ __('Initializing Monaco Engine...') }}</p>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="px-6 py-3 border-t border-white/[0.06] bg-white/[0.01] flex items-center justify-between text-xs">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-500 uppercase text-[10px]">{{ __('Lang:') }}</span>
                            <span id="lang-label" class="text-emerald-400 font-bold uppercase text-[10px]">...</span>
                        </div>
                        <div class="flex items-center gap-1.5 truncate max-w-md">
                            <span class="text-slate-500 uppercase text-[10px]">{{ __('Path:') }}</span>
                            <span class="text-slate-400 text-[10px] truncate">{{ $path }}</span>
                        </div>
                    </div>
                    <span class="text-slate-600 text-[10px] uppercase tracking-widest hidden sm:inline">
                        {{ __('UTF-8 · LF') }}
                    </span>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/loader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            let editor;
            const filePath = @json($path);
            const fileExt = filePath.split('.').pop().toLowerCase();

            const langMap = {
                'php': 'php',
                'blade': 'php',
                'js': 'javascript',
                'css': 'css',
                'json': 'json',
                'html': 'html',
                'md': 'markdown'
            };

            const language = langMap[fileExt] || 'plaintext';
            $('#lang-label').text(language === 'php' && filePath.includes('.blade.php') ? 'blade' : language);

            require.config({
                paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs' }
            });

            require(['vs/editor/editor.main'], function() {
                monaco.editor.defineTheme('york-dark', {
                    base: 'vs-dark',
                    inherit: true,
                    rules: [
                        { token: '', background: '090c14' },
                        { token: 'comment', foreground: '64748b' },
                        { token: 'keyword', foreground: '00f5ff' },
                        { token: 'string', foreground: '10b981' },
                        { token: 'variable', foreground: 'a855f7' }
                    ],
                    colors: {
                        'editor.background': '#090c14',
                        'editor.lineHighlightBackground': '#ffffff05',
                        'editorCursor.foreground': '#00f5ff',
                        'editor.selectionBackground': '#00f5ff33',
                        'editorLineNumber.foreground': '#475569',
                        'editorLineNumber.activeForeground': '#00f5ff'
                    }
                });

                editor = monaco.editor.create(document.getElementById('monaco-editor'), {
                    value: @json($code),
                    language: language,
                    theme: 'york-dark',
                    fontSize: 13,
                    fontFamily: "'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace",
                    lineHeight: 22,
                    minimap: { enabled: true, scale: 0.75 },
                    automaticLayout: true,
                    padding: { top: 20, bottom: 20 },
                    scrollbar: {
                        vertical: 'visible',
                        horizontal: 'visible',
                        useShadows: false,
                        verticalScrollbarSize: 8,
                        horizontalScrollbarSize: 8
                    },
                    roundedSelection: true,
                    smoothScrolling: true
                });

                $('#editor-loading').addClass('opacity-0 pointer-events-none');

                const saveChanges = function() {
                    const $btn = $('#save-btn');
                    const $status = $('#save-status');
                    const code = editor.getValue();

                    $btn.prop('disabled', true).addClass('opacity-50');
                    $status.removeClass('hidden');

                    $.ajax({
                        url: "{{ route('admin.file-manager.code-editor.update') }}",
                        type: "POST",
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        data: {
                            _token: "{{ csrf_token() }}",
                            path: filePath,
                            code: code
                        },
                        success: function(response) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: response.message || '{{ __('File saved successfully!') }}',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                            });
                        },
                        error: function(xhr) {
                            let msg = "{{ __('Failed to save file.') }}";
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('Save Error') }}',
                                text: msg,
                                customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                            });
                        },
                        complete: function() {
                            $btn.prop('disabled', false).removeClass('opacity-50');
                            $status.addClass('hidden');
                            editor.focus();
                        }
                    });
                };

                // Bind save button click
                $('#save-btn').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    saveChanges();
                });

                // Bind Monaco editor Ctrl+S / Cmd+S
                editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, function() {
                    saveChanges();
                });

                // Global Window Ctrl+S listener
                window.addEventListener('keydown', function(e) {
                    if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
                        e.preventDefault();
                        e.stopPropagation();
                        saveChanges();
                    }
                }, true);
            });
        });
    </script>
@endpush
