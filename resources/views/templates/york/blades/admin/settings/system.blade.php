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
                    {{ __('SYSTEM & SERVER') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('System & Server') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('View server specifications, PHP extensions, and environment settings') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button onclick="clearSystemCache()"
                    class="px-4 py-2.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/[0.1] text-slate-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>{{ __('Clear Cache') }}</span>
                </button>

                <button onclick="openConfigModal()"
                    class="px-5 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ __('Environment Settings') }}</span>
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
                
                {{-- Sub-Navigation Tabs --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs">
                    @foreach ([
                        'core' => __('Server & App'),
                        'env' => __('Drivers'),
                        'extensions' => __('PHP Extensions'),
                        'storage' => __('Folder Permissions'),
                    ] as $key => $label)
                        <button type="button" onclick="switchSysTab('{{ $key }}')" id="tab-btn-{{ $key }}"
                            class="sys-tab-btn px-4 py-2 rounded-xl transition-all font-bold uppercase tracking-wider cursor-pointer {{ $loop->first ? 'bg-cyan-400 text-black shadow-[0_0_15px_rgba(0,245,255,0.3)]' : 'bg-white/[0.02] text-slate-400 border border-white/[0.06] hover:bg-white/[0.05] hover:text-white' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                {{-- TAB 1: PLATFORM CORE --}}
                <div id="sys-tab-core" class="sys-tab-pane">
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-6 text-xs">
                            <div class="pb-3 border-b border-white/[0.06]">
                                <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Application & Server Details') }}</h3>
                                <p class="text-[10px] text-slate-400">{{ __('Installed framework and runtime versions.') }}</p>
                            </div>

                            @if (config('app.debug'))
                                <div class="p-4 rounded-2xl bg-rose-500/[0.08] border border-rose-500/30 flex items-center gap-4 text-xs">
                                    <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="space-y-0.5">
                                        <h4 class="text-rose-400 font-bold uppercase tracking-wider text-[11px] flex items-center gap-2">
                                            <span>{{ __('Debug Mode is Currently Active') }}</span>
                                            <span class="px-2 py-0.5 rounded bg-rose-500 text-black font-black text-[9px] uppercase">{{ __('Danger') }}</span>
                                        </h4>
                                        <p class="text-slate-400 text-[10px]">
                                            {{ __('Debug mode exposes stack traces, database queries, and environment secrets on errors. Ensure this is disabled in production.') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ([
                                    'App Name' => config('app.name'),
                                    'App URL' => config('app.url'),
                                    'App Environment' => config('app.env'),
                                    'Debug Mode' => config('app.debug'),
                                    'PHP Version' => PHP_VERSION,
                                    'Laravel Version' => app()->version(),
                                    'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
                                    'Timezone' => config('app.timezone'),
                                ] as $label => $val)
                                    @if ($label === 'Debug Mode')
                                        @if ($val)
                                            <div class="p-4 rounded-2xl bg-rose-500/[0.08] border border-rose-500/40 flex items-center justify-between shadow-[0_0_20px_rgba(244,63,94,0.15)]">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    <span class="text-rose-300 font-bold uppercase text-[10px]">{{ __('Debug Mode') }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5 px-3 py-1 rounded-lg bg-rose-500 text-black font-black text-[10px] uppercase shadow-[0_0_10px_rgba(244,63,94,0.5)]">
                                                    <svg class="w-3.5 h-3.5 text-black shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    <span>{{ __('ENABLED (DANGER)') }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                                                <span class="text-slate-400 font-bold uppercase text-[10px]">{{ __('Debug Mode') }}</span>
                                                <code class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold text-[11px]">{{ __('DISABLED') }}</code>
                                            </div>
                                        @endif
                                    @else
                                        <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                                            <span class="text-slate-400 font-bold uppercase text-[10px]">{{ __($label) }}</span>
                                            <code class="px-2 py-0.5 rounded bg-white/[0.04] text-cyan-300 font-bold text-[11px]">{{ $val }}</code>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: ENVIRONMENT DRIVERS --}}
                <div id="sys-tab-env" class="sys-tab-pane hidden">
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-6 text-xs">
                            <div class="pb-3 border-b border-white/[0.06]">
                                <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('System Drivers') }}</h3>
                                <p class="text-[10px] text-slate-400">{{ __('Configured drivers for database, cache, sessions, and queues.') }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ([
                                    'Database Driver' => config('database.default'),
                                    'Cache Store' => config('cache.default'),
                                    'Session Driver' => config('session.driver'),
                                    'Queue Connection' => config('queue.default'),
                                    'Mail Transport' => config('mail.default'),
                                    'Broadcast Driver' => config('broadcasting.default'),
                                ] as $label => $val)
                                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                                        <span class="text-slate-400 font-bold uppercase text-[10px]">{{ __($label) }}</span>
                                        <code class="px-2 py-0.5 rounded bg-white/[0.04] text-cyan-300 font-bold text-[11px]">{{ $val }}</code>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: PHP EXTENSIONS --}}
                <div id="sys-tab-extensions" class="sys-tab-pane hidden">
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-6 text-xs">
                            <div class="pb-3 border-b border-white/[0.06]">
                                <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Required PHP Extensions') }}</h3>
                                <p class="text-[10px] text-slate-400">{{ __('PHP extensions required for full platform functionality.') }}</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach ([
                                    'OpenSSL' => extension_loaded('openssl'),
                                    'PDO' => extension_loaded('pdo'),
                                    'Mbstring' => extension_loaded('mbstring'),
                                    'Tokenizer' => extension_loaded('tokenizer'),
                                    'XML' => extension_loaded('xml'),
                                    'cURL' => extension_loaded('curl'),
                                    'JSON' => extension_loaded('json'),
                                    'BCMath' => extension_loaded('bcmath'),
                                    'Sodium' => extension_loaded('sodium'),
                                    'FileInfo' => extension_loaded('fileinfo'),
                                    'GD Library' => extension_loaded('gd'),
                                    'ZipArchive' => class_exists('ZipArchive'),
                                ] as $ext => $loaded)
                                    <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                                        <span class="text-slate-300 font-bold text-[11px]">{{ $ext }}</span>
                                        @if ($loaded)
                                            <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-bold uppercase">
                                                {{ __('Active') }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[9px] font-bold uppercase">
                                                {{ __('Missing') }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 4: STORAGE & PERMISSIONS --}}
                <div id="sys-tab-storage" class="sys-tab-pane hidden">
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-6 text-xs">
                            <div class="pb-3 border-b border-white/[0.06]">
                                <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Folder Permissions') }}</h3>
                                <p class="text-[10px] text-slate-400">{{ __('Verify required folders have write permissions.') }}</p>
                            </div>

                            <div class="space-y-3">
                                @foreach ([
                                    'storage/app' => storage_path('app'),
                                    'storage/framework' => storage_path('framework'),
                                    'storage/logs' => storage_path('logs'),
                                    'bootstrap/cache' => base_path('bootstrap/cache'),
                                ] as $label => $path)
                                    @php $writable = is_writable($path); @endphp
                                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                                        <div>
                                            <span class="text-white font-bold block">{{ $label }}</span>
                                            <span class="text-[9px] text-slate-500">{{ $path }}</span>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-lg {{ $writable ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }} text-[10px] font-bold uppercase">
                                            {{ $writable ? __('Writable') : __('Not Writable') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- Configure Modal --}}
    <div id="config-modal" class="hidden fixed inset-0 bg-[#05070d]/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.05] border border-white/[0.1] w-full max-w-md font-mono">
            <div class="rounded-[2rem] bg-[#090c14] p-8 space-y-5 text-xs">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 class="text-base font-bold text-white uppercase">{{ __('Environment Settings') }}</h3>
                    <button type="button" onclick="closeModal('config-modal')" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>
                
                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('App Environment') }}</label>
                        <select id="config-app-env" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none">
                            <option value="production">production</option>
                            <option value="local">local</option>
                            <option value="sandbox">sandbox</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Debug Mode (APP_DEBUG)') }}</label>
                        <select id="config-app-debug" onchange="checkDebugWarning(this.value)" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none">
                            <option value="false">false (Recommended for Production)</option>
                            <option value="true">true (Development Only - Security Risk)</option>
                        </select>
                        <p id="debug-danger-warning" class="hidden text-[10px] text-rose-400 font-bold flex items-center gap-1.5 pt-1">
                            <span>⚠️</span> <span>{{ __('Danger: Enabling debug mode exposes database credentials, API secrets, and sensitive error traces.') }}</span>
                        </p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Log Level') }}</label>
                        <select id="config-log-level" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none">
                            <option value="debug">debug</option>
                            <option value="info">info</option>
                            <option value="warning">warning</option>
                            <option value="error">error</option>
                            <option value="critical">critical</option>
                        </select>
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('config-modal')" class="px-4 py-2 rounded-xl bg-white/[0.04] text-slate-300 font-bold uppercase text-[10px] cursor-pointer">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" onclick="saveSystemConfig()" class="px-6 py-2 rounded-xl bg-cyan-400 text-black font-black uppercase text-[10px] cursor-pointer">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function checkDebugWarning(val) {
            if (val === 'true') {
                $('#debug-danger-warning').removeClass('hidden');
                $('#config-app-debug').addClass('border-rose-500 text-rose-300').removeClass('border-white/[0.1]');
            } else {
                $('#debug-danger-warning').addClass('hidden');
                $('#config-app-debug').removeClass('border-rose-500 text-rose-300').addClass('border-white/[0.1]');
            }
        }

        function switchSysTab(tabKey) {
            $('.sys-tab-pane').addClass('hidden');
            $(`#sys-tab-${tabKey}`).removeClass('hidden');

            $('.sys-tab-btn').removeClass('bg-cyan-400 text-black shadow-[0_0_15px_rgba(0,245,255,0.3)]')
                .addClass('bg-white/[0.02] text-slate-400 border border-white/[0.06] hover:bg-white/[0.05] hover:text-white');

            $(`#tab-btn-${tabKey}`).addClass('bg-cyan-400 text-black shadow-[0_0_15px_rgba(0,245,255,0.3)]')
                .removeClass('bg-white/[0.02] text-slate-400 border border-white/[0.06] hover:bg-white/[0.05] hover:text-white');
        }

        function openConfigModal() {
            const currentDebug = "{{ config('app.debug') ? 'true' : 'false' }}";
            const currentEnv = "{{ config('app.env') }}";
            const currentLogLevel = "{{ config('app.log_level', 'debug') }}";

            $('#config-app-debug').val(currentDebug);
            $('#config-app-env').val(currentEnv);
            $('#config-log-level').val(currentLogLevel);
            checkDebugWarning(currentDebug);

            $('#config-modal').removeClass('hidden');
        }

        function closeModal(id) {
            $('#' + id).addClass('hidden');
        }

        function clearSystemCache() {
            Swal.fire({
                title: '{{ __('Clearing system cache...') }}',
                didOpen: () => { Swal.showLoading(); },
                customClass: { popup: 'bg-[#090c14] border border-white/10 text-white font-mono' }
            });

            $.ajax({
                url: "{{ route('admin.settings.system.clear-cache') }}",
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message || '{{ __('Cache cleared successfully.') }}',
                        showConfirmButton: false,
                        timer: 2000,
                        customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Error') }}',
                        text: xhr.responseJSON?.message || '{{ __('Failed to clear cache.') }}',
                        customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                    });
                }
            });
        }

        function saveSystemConfig() {
            const data = {
                _token: "{{ csrf_token() }}",
                APP_DEBUG: $('#config-app-debug').val(),
                APP_ENV: $('#config-app-env').val(),
                LOG_LEVEL: $('#config-log-level').val()
            };

            $.ajax({
                url: "{{ route('admin.settings.system.update-env') }}",
                type: "POST",
                data: data,
                success: function(response) {
                    closeModal('config-modal');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message || '{{ __('Configuration updated.') }}',
                        showConfirmButton: false,
                        timer: 2000,
                        customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Error') }}',
                        text: xhr.responseJSON?.message || '{{ __('Failed to update configuration.') }}',
                        customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                    });
                }
            });
        }
    </script>
@endpush
