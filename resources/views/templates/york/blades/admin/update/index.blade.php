@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12 font-mono">

        {{-- Top Header --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-[0.2em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('System Update') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('System Update') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-wider uppercase">
                    {{ __('Install software updates, security fixes, and database migrations.') }}
                </p>
            </div>

            {{-- Version Badges --}}
            <div class="flex flex-wrap items-center gap-2.5 text-xs shrink-0">
                <div class="px-3.5 py-2 rounded-xl bg-[#090c14] border border-white/[0.08] flex items-center gap-3">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-500 block leading-none mb-1">{{ __('Installed Version') }}</span>
                        <span class="text-xs font-black text-white font-mono">v{{ $current_version }}</span>
                    </div>
                    <span class="w-1.5 h-1.5 rounded-full {{ $is_update_available ? 'bg-amber-400' : 'bg-emerald-400' }} animate-pulse"></span>
                </div>

                <div class="px-3.5 py-2 rounded-xl bg-[#090c14] border border-white/[0.08] flex items-center gap-3">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-500 block leading-none mb-1">{{ __('Available Version') }}</span>
                        <span class="text-xs font-black font-mono {{ $is_update_available ? 'text-cyan-400' : 'text-emerald-400' }}">v{{ $latest_version }}</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $is_update_available ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                        {{ $is_update_available ? __('Update Available') : __('Up to Date') }}
                    </span>
                </div>

                <button type="button" onclick="window.location.reload()"
                    class="px-3.5 py-2.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.08] text-slate-300 hover:text-white text-xs font-bold font-mono uppercase tracking-wider transition-all flex items-center gap-1.5 cursor-pointer"
                    title="{{ __('Check for updates') }}">
                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>{{ __('Check for Updates') }}</span>
                </button>
            </div>
        </div>

        {{-- Alerts and Notices --}}
        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-start gap-3.5 text-xs text-rose-200">
                <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-rose-400 uppercase tracking-wide">{{ __('Update Error') }}</h4>
                    <p class="text-slate-300 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if (isset($error))
            <div class="p-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                <div class="flex items-start gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-rose-400 uppercase tracking-wide">{{ __('Could not reach update server') }}</h4>
                        <p class="text-slate-300 mt-0.5 leading-relaxed">{{ $error }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.update.index') }}"
                    class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-black font-black uppercase text-[10px] tracking-wider transition-all shrink-0 self-start sm:self-auto flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>{{ __('Try Again') }}</span>
                </a>
            </div>
        @elseif (!$is_update_available)
            {{-- Up to Date Banner --}}
            <div class="rounded-2xl bg-gradient-to-r from-emerald-950/30 via-[#090c14] to-[#090c14] border border-emerald-500/20 p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-5 text-xs shadow-[0_0_30px_rgba(16,185,129,0.05)]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shrink-0 shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Your system is up to date') }}</h3>
                            <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-bold uppercase tracking-wider">{{ __('Current') }}</span>
                        </div>
                        <p class="text-slate-400 mt-1 max-w-xl text-[11px] leading-relaxed">
                            {{ __('You are running version :version. No updates are required right now.', ['version' => $current_version]) }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="px-3.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>{{ __('Up to date') }}</span>
                    </span>
                </div>
            </div>
        @else
            {{-- Update Available Banner --}}
            <div class="rounded-2xl bg-gradient-to-r from-cyan-950/40 via-[#090c14] to-[#090c14] border border-cyan-500/30 p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-5 text-xs shadow-[0_0_30px_rgba(0,245,255,0.1)]">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center shrink-0 shadow-[0_0_20px_rgba(0,245,255,0.2)] mt-0.5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Update available: Version :latest', ['latest' => $latest_version]) }}</h3>
                            <span class="px-2 py-0.5 rounded bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold font-mono">v{{ $latest_version }}</span>
                        </div>
                        <p class="text-slate-300 mt-1 max-w-2xl text-[11px] leading-relaxed">
                            {{ __('A new version is ready to install. Check your server compatibility below, then start the update.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0 self-start md:self-auto">
                    <a href="#checks-section" class="px-4 py-2 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-[10px] tracking-wider transition-all shadow-[0_0_15px_rgba(0,245,255,0.3)] flex items-center gap-1.5 cursor-pointer">
                        <span>{{ __('View Details') }}</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Incompatible PHP Notice --}}
            @if (!$php_version_supported)
                <div class="p-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-rose-400 uppercase tracking-wide">{{ __('PHP update required') }}</h4>
                            <p class="text-slate-300 mt-0.5">{{ __('This release requires PHP :required or higher. Your server runs PHP :current. Upgrade PHP before updating.', ['required' => $required_php, 'current' => PHP_VERSION]) }}</p>
                        </div>
                    </div>
                    <button onclick="window.location.reload()"
                        class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-[10px] font-bold uppercase tracking-wider cursor-pointer shrink-0">
                        {{ __('Recheck PHP') }}
                    </button>
                </div>
            @endif

            {{-- Main Layout: Server Checks (Left) + Release Notes (Right) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start" id="checks-section">

                {{-- Left: Server Checks (7 Cols) --}}
                <div class="lg:col-span-7 space-y-5">

                    <div class="rounded-2xl bg-[#090c14] border border-white/[0.08] overflow-hidden shadow-2xl">
                        {{-- Header --}}
                        <div class="px-5 py-4 border-b border-white/[0.06] bg-white/[0.015] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xs font-black text-white uppercase tracking-wider">{{ __('Server Checks') }}</h3>
                                    <p class="text-[10px] text-slate-500">{{ __('Test your server limits and PHP extensions before updating.') }}</p>
                                </div>
                            </div>

                            <button id="btn-verify-requirements" onclick="verifyRequirements()"
                                @if (!$php_version_supported || !$is_update_available) disabled @endif
                                class="px-4 py-2 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-[10px] tracking-wider transition-all flex items-center justify-center gap-2 shadow-[0_0_15px_rgba(0,245,255,0.25)] disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer shrink-0 active:scale-[0.98]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ __('Run Server Checks') }}</span>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="p-5 sm:p-6 space-y-5">
                            {{-- Server Info Row --}}
                            <div class="grid grid-cols-3 gap-3 text-center">
                                <div class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.06]">
                                    <span class="text-[9px] uppercase font-bold text-slate-500 block mb-1">{{ __('PHP Version') }}</span>
                                    <span class="text-xs font-black font-mono {{ $php_version_supported ? 'text-white' : 'text-rose-400' }}">PHP {{ PHP_VERSION }}</span>
                                </div>
                                <div class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.06]">
                                    <span class="text-[9px] uppercase font-bold text-slate-500 block mb-1">{{ __('Memory Limit') }}</span>
                                    <span class="text-xs font-black font-mono text-cyan-300">{{ ini_get('memory_limit') ?: 'N/A' }}</span>
                                </div>
                                <div class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.06]">
                                    <span class="text-[9px] uppercase font-bold text-slate-500 block mb-1">{{ __('Max Execution Time') }}</span>
                                    <span class="text-xs font-black font-mono text-slate-300">{{ ini_get('max_execution_time') }}s</span>
                                </div>
                            </div>

                            {{-- Verification Result Box --}}
                            <div id="requirements-container" class="space-y-4">
                                <div class="p-8 rounded-xl bg-white/[0.015] border border-dashed border-white/[0.08] text-center space-y-2">
                                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center mx-auto">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </div>
                                    <h5 class="text-xs font-bold text-white uppercase tracking-wider">{{ __('Server checks pending') }}</h5>
                                    <p class="text-[10px] text-slate-500 max-w-sm mx-auto leading-relaxed">
                                        {{ __('Click "Run Server Checks" to test your PHP version, required extensions, and server memory.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Safety Note --}}
                    <div class="p-4 rounded-2xl bg-white/[0.015] border border-white/[0.06] flex items-start gap-3.5 text-xs text-slate-400">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 shrink-0 mt-1.5 shadow-[0_0_8px_#00f5ff]"></span>
                        <div class="space-y-1">
                            <span class="text-white font-bold uppercase text-[10px] tracking-wider block">{{ __('Safety Notice') }}</span>
                            <p class="text-[11px] leading-relaxed text-slate-400">
                                {{ __('Your database credentials, uploaded files, and .env configuration remain untouched during updates. Always back up your database before installing.') }}
                            </p>
                        </div>
                    </div>

                </div>

                {{-- Right: Release Notes & Download (5 Cols) --}}
                <div class="lg:col-span-5 space-y-5">
                    
                    <div class="rounded-2xl bg-[#090c14] border border-white/[0.08] overflow-hidden shadow-2xl flex flex-col">
                        {{-- Header --}}
                        <div class="px-5 py-4 border-b border-white/[0.06] bg-white/[0.015] flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-black text-white uppercase tracking-wider">{{ __('Release Notes') }}</h3>
                                <p class="text-[10px] text-slate-500">{{ __('Changes included in version :version', ['version' => $latest_version]) }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold">
                                    v{{ $latest_version }}
                                </span>
                            </div>
                        </div>

                        {{-- Metadata Grid --}}
                        <div class="grid grid-cols-2 gap-px bg-white/[0.04] border-b border-white/[0.06] text-xs">
                            <div class="p-3 bg-[#090c14]">
                                <span class="text-[8px] uppercase font-bold text-slate-500 block mb-0.5">{{ __('Version') }}</span>
                                <span class="text-xs font-black text-white font-mono">v{{ $latest_version }}</span>
                            </div>
                            <div class="p-3 bg-[#090c14]">
                                <span class="text-[8px] uppercase font-bold text-slate-500 block mb-0.5">{{ __('Minimum PHP') }}</span>
                                <span class="text-xs font-black text-cyan-300 font-mono">PHP {{ $required_php }}+</span>
                            </div>
                        </div>

                        {{-- Changelog List --}}
                        <div class="p-5 space-y-3">
                            <span class="text-[9px] uppercase font-bold text-slate-500 tracking-wider block">{{ __('What is new in this update') }}</span>
                            
                            <div class="p-4 rounded-xl bg-[#05070d] border border-white/[0.06] max-h-72 overflow-y-auto text-xs font-mono text-slate-300 space-y-2 select-text">
                                @if (isset($update_data['changelog']))
                                    @if (is_array($update_data['changelog']))
                                        <ul class="space-y-2 text-xs">
                                            @foreach ($update_data['changelog'] as $change)
                                                <li class="flex items-start gap-2 leading-relaxed">
                                                    <span class="text-cyan-400 font-bold shrink-0">→</span>
                                                    <span class="text-slate-300">{!! $change !!}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="text-slate-300 text-xs leading-relaxed">
                                            {!! $update_data['changelog'] !!}
                                        </div>
                                    @endif
                                @else
                                    <p class="text-slate-500 italic text-[11px]">{{ __('No release notes provided for this update.') }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Action Footer --}}
                        <div class="p-5 border-t border-white/[0.06] bg-white/[0.01] space-y-3">
                            <button id="btn-download-updater" type="button" onclick="handleDownloadClick()"
                                class="w-full py-3.5 px-4 rounded-xl text-xs font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer bg-white/[0.04] text-slate-500 border border-white/[0.06] hover:border-white/[0.12]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>{{ __('Download & Install Update') }}</span>
                            </button>
                            <div class="flex items-center justify-center gap-2 text-[9px] font-bold uppercase tracking-wider" id="download-hint-container">
                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span id="download-hint" class="text-slate-500">
                                    {{ __('Run server checks above to unlock this button.') }}
                                </span>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let isCompatibilityVerified = false;

        function handleDownloadClick() {
            if (!isCompatibilityVerified) {
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __('Run Server Checks First') }}',
                    text: '{{ __('Check your server compatibility before downloading the update.') }}',
                    confirmButtonText: '{{ __('OK') }}',
                    customClass: { popup: 'bg-[#090c14] border border-amber-500/30 text-white font-mono' }
                });
                return;
            }
            updateUpdater();
        }

        function verifyRequirements() {
            const btn = $('#btn-verify-requirements');
            const container = $('#requirements-container');
            const downloadBtn = $('#btn-download-updater');
            const downloadHint = $('#download-hint');

            btn.prop('disabled', true).addClass('opacity-50');

            container.html(`
                <div class="p-8 rounded-xl bg-white/[0.015] border border-white/[0.06] text-center space-y-3 font-mono">
                    <div class="w-7 h-7 border-2 border-cyan-500/20 border-t-cyan-400 rounded-full animate-spin mx-auto"></div>
                    <p class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider">{{ __('Checking server requirements...') }}</p>
                </div>
            `);

            $.ajax({
                url: "{{ route('admin.update.verify-requirements') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    renderRequirements(response);
                    if (response.status === 'success') {
                        isCompatibilityVerified = true;
                        downloadBtn.removeClass('bg-white/[0.04] text-slate-500 border-white/[0.06] hover:border-white/[0.12]')
                            .addClass('bg-cyan-400 hover:bg-cyan-300 text-black font-black shadow-[0_0_20px_rgba(0,245,255,0.25)] active:scale-[0.98]');
                        downloadHint.html("{{ __('Server checks passed. Ready to install.') }}")
                            .removeClass('text-slate-500')
                            .addClass('text-emerald-400 font-bold');
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: '{{ __('Server checks passed.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/30 text-white font-mono text-xs shadow-2xl' }
                        });
                    } else {
                        isCompatibilityVerified = false;
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __('Requirements Unmet') }}',
                            text: response.message || '{{ __('Some server requirements were not satisfied.') }}',
                            customClass: { popup: 'bg-[#090c14] border border-amber-500/30 text-white font-mono' }
                        });
                    }
                },
                error: function(xhr) {
                    isCompatibilityVerified = false;
                    container.html(`
                        <div class="p-5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-center text-rose-400 text-xs font-mono">
                            ${xhr.responseJSON?.message || '{{ __('Failed to check server requirements.') }}'}
                        </div>
                    `);
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Check Failed') }}',
                        text: xhr.responseJSON?.message || '{{ __('Could not complete server checks.') }}',
                        customClass: { popup: 'bg-[#090c14] border border-rose-500/30 text-white font-mono' }
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).removeClass('opacity-50');
                }
            });
        }

        function renderRequirements(data) {
            let html = '<div class="space-y-3 font-mono text-xs">';

            // Server Limits
            if (data.server_config) {
                html += '<div>';
                html += '<div class="text-[9px] uppercase font-bold text-slate-500 tracking-wider mb-2">{{ __('Server Settings') }}</div>';
                html += '<div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">';
                Object.entries(data.server_config).forEach(([key, info]) => {
                    html += `
                        <div class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                            <div>
                                <span class="text-slate-400 font-bold uppercase text-[9px] block">${key.replace(/_/g, ' ')}</span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-xs font-black ${info.status ? 'text-white' : 'text-rose-400'}">${info.current}</span>
                                    ${!info.status ? `<span class="text-[9px] text-slate-500 font-bold">/ ${info.recommended}</span>` : ''}
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-md ${info.status ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'} text-[9px] font-bold uppercase">
                                ${info.status ? 'Pass' : 'Fail'}
                            </span>
                        </div>
                    `;
                });
                html += '</div></div>';
            }

            // PHP Extensions
            if (data.extensions) {
                html += '<div class="pt-2">';
                html += '<div class="text-[9px] uppercase font-bold text-slate-500 tracking-wider mb-2">{{ __('PHP Extensions') }}</div>';
                html += '<div class="grid grid-cols-2 sm:grid-cols-3 gap-2">';
                Object.entries(data.extensions).forEach(([ext, status]) => {
                    html += `
                        <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                            <span class="text-slate-300 font-bold text-[10px]">${ext}</span>
                            <span class="px-1.5 py-0.5 rounded ${status ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400'} text-[9px] font-bold uppercase">
                                ${status ? 'Pass' : 'Missing'}
                            </span>
                        </div>
                    `;
                });
                html += '</div></div>';
            }

            html += '</div>';
            $('#requirements-container').html(html);
        }

        function updateUpdater() {
            const btn = $('#btn-download-updater');
            btn.prop('disabled', true).addClass('opacity-50');

            Swal.fire({
                title: '{{ __('Preparing Update...') }}',
                text: '{{ __('Downloading updater files from the server...') }}',
                didOpen: () => { Swal.showLoading(); },
                allowOutsideClick: false,
                customClass: { popup: 'bg-[#090c14] border border-white/10 text-white font-mono' }
            });

            $.ajax({
                url: "{{ route('admin.update.download-updater') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('Ready to Install') }}',
                        text: response.message || '{{ __('Starting update installation...') }}',
                        timer: 1200,
                        showConfirmButton: false,
                        customClass: { popup: 'bg-[#090c14] border border-cyan-500/30 text-white font-mono' }
                    }).then(() => {
                        window.location.href = response.redirect_url;
                    });
                },
                error: function(xhr) {
                    btn.prop('disabled', false).removeClass('opacity-50');
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Download Failed') }}',
                        text: xhr.responseJSON?.message || '{{ __('Could not download the updater.') }}',
                        customClass: { popup: 'bg-[#090c14] border border-rose-500/30 text-white font-mono' }
                    });
                }
            });
        }
    </script>
@endpush
