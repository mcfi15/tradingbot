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
                    {{ __('CRON JOBS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Cron Jobs') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Set up automated background schedules and monitor cron execution') }}
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
                
                {{-- SECTION 1: CRON COMMAND RUNNER --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-6 text-xs">
                        <div class="pb-3 border-b border-white/[0.06] flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Server Cron Setup') }}</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ __('Schedule automated background execution in your server\'s crontab or task scheduler (e.g., cPanel, Laravel Forge, Plesk, or Linux cron).') }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-bold uppercase tracking-wider self-start md:self-auto shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                {{ __('Add 1 Command Only') }}
                            </span>
                        </div>

                        {{-- Prominent "Choose Only One" Warning Banner --}}
                        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/25 flex items-start gap-3.5 text-amber-300">
                            <div class="p-2 rounded-xl bg-amber-500/20 text-amber-400 shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-black uppercase tracking-wider text-amber-300">{{ __('Important: Choose ONLY ONE Option (Option A, Option B, or Option C)') }}</h4>
                                <p class="text-[11px] text-amber-200/80 leading-relaxed">
                                    {{ __('Add ONLY ONE of the three options below to your server crontab. Do NOT add multiple commands simultaneously, otherwise your background tasks and schedules will run multiple times.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Options Deck --}}
                        <div class="space-y-4">
                            @foreach ($cron_job_curl_commands as $index => $command)
                                @php
                                    $isWget = str_starts_with(trim($command), 'wget');
                                    $methodName = $isWget ? __('Option A: WGET Command') : __('Option B: cURL Command');
                                    $methodDesc = $isWget ? __('Recommended for standard Linux crontab & cPanel Cron Manager via web route') : __('Recommended for Cloud VPS, Docker, or modern webhook schedulers via web route');
                                    $badgeColor = $isWget ? 'bg-cyan-500/10 border-cyan-500/20 text-cyan-400' : 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400';
                                @endphp

                                @if ($index > 0)
                                    <div class="relative py-2 flex items-center justify-center">
                                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-white/[0.08]"></div></div>
                                        <span class="relative px-4 py-1 rounded-full bg-[#05070d] border border-white/[0.1] text-[9px] md:text-[10px] uppercase font-black tracking-widest text-slate-400">
                                            {{ __('— OR CHOOSE ALTERNATIVE (CHOOSE ONLY ONE) —') }}
                                        </span>
                                    </div>
                                @endif

                                <div class="p-4 rounded-2xl bg-[#05070d] border border-white/[0.08] hover:border-white/[0.15] transition-all space-y-3">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div class="flex items-center gap-2.5">
                                            <span class="px-2.5 py-1 rounded-lg {{ $badgeColor }} border text-[10px] font-black uppercase tracking-wider">
                                                {{ $methodName }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 hidden sm:inline">{{ $methodDesc }}</span>
                                        </div>
                                        <span class="text-[9px] font-bold uppercase text-slate-500 tracking-wider">
                                            {{ __('Interval: * * * * * (Every Minute)') }}
                                        </span>
                                    </div>

                                    <div class="p-3 bg-[#020408] rounded-xl border border-white/[0.06] flex items-center justify-between gap-3">
                                        <code class="text-cyan-300 text-[11px] font-mono break-all select-all" id="cron-cmd-{{ $index }}">{{ $command }}</code>
                                        <button type="button" onclick="copyCmd('cron-cmd-{{ $index }}')"
                                            class="px-3.5 py-1.5 rounded-lg bg-white/[0.06] hover:bg-cyan-400 hover:text-black text-cyan-300 font-black uppercase text-[9px] transition-all shrink-0 cursor-pointer flex items-center gap-1.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                            {{ __('Copy') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Separator for Option C --}}
                            <div class="relative py-2 flex items-center justify-center">
                                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-white/[0.08]"></div></div>
                                <span class="relative px-4 py-1 rounded-full bg-[#05070d] border border-white/[0.1] text-[9px] md:text-[10px] uppercase font-black tracking-widest text-slate-400">
                                    {{ __('— OR CHOOSE ALTERNATIVE (CHOOSE ONLY ONE) —') }}
                                </span>
                            </div>

                            {{-- Option C: Direct Artisan CLI Command --}}
                            @php
                                $cliCronCommand = $artisan_cron_command ?? ('cd ' . sandBoxCredentials(base_path()) . ' && php artisan foyana:scheduler-daemon >/dev/null 2>&1');
                            @endphp
                            <div class="p-4 rounded-2xl bg-[#05070d] border border-white/[0.08] hover:border-white/[0.15] transition-all space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <span class="px-2.5 py-1 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-400 text-[10px] font-black uppercase tracking-wider">
                                            {{ __('Option C: Direct Artisan CLI Command') }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 hidden sm:inline">{{ __('Recommended for VPS, Dedicated Servers, or direct server crontab without HTTP web route overhead') }}</span>
                                    </div>
                                    <span class="text-[9px] font-bold uppercase text-slate-500 tracking-wider">
                                        {{ __('Interval: * * * * * (Every Minute)') }}
                                    </span>
                                </div>

                                <div class="p-3 bg-[#020408] rounded-xl border border-white/[0.06] flex items-center justify-between gap-3">
                                    <code class="text-purple-300 text-[11px] font-mono break-all select-all" id="cron-cmd-cli">{{ $cliCronCommand }}</code>
                                    <button type="button" onclick="copyCmd('cron-cmd-cli')"
                                        class="px-3.5 py-1.5 rounded-lg bg-white/[0.06] hover:bg-purple-400 hover:text-black text-purple-300 font-black uppercase text-[9px] transition-all shrink-0 cursor-pointer flex items-center gap-1.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                        {{ __('Copy') }}
                                    </button>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-white/[0.04] text-[10px]">
                                    <div class="flex items-center gap-2 text-slate-400">
                                        <span class="text-slate-500 font-semibold">{{ __('Direct Terminal Execution:') }}</span>
                                        <code class="text-purple-300 font-mono select-all bg-white/[0.04] px-2 py-0.5 rounded border border-white/[0.06]" id="terminal-cmd">php artisan foyana:scheduler-daemon</code>
                                    </div>
                                    <button type="button" onclick="copyCmd('terminal-cmd')" class="text-[9px] text-purple-400 hover:text-purple-300 hover:underline uppercase font-bold cursor-pointer self-start sm:self-auto">
                                        {{ __('Copy Terminal Command') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: CRON TELEMETRY HEALTH MONITOR --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-6 text-xs">
                        <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Automated Services Status') }}</h3>
                                <p class="text-[10px] text-slate-400">{{ __('Monitor health, intervals, and last execution times for background financial and automation services.') }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold">
                                {{ count($cron_health) }} {{ __('Active Services') }}
                            </span>
                        </div>

                        <div class="space-y-3">
                            @foreach ($cron_health as $job)
                                @php
                                    $isHealthy = time() - $job->last_run <= $job->recommended;
                                    $seconds = $job->recommended;
                                    if ($seconds < 60) {
                                        $intervalText = $seconds . ' sec';
                                    } elseif ($seconds < 3600) {
                                        $intervalText = round($seconds / 60) . ' min';
                                    } else {
                                        $intervalText = round($seconds / 3600) . ' hr';
                                    }
                                    $serviceName = $job->name ?: ucwords(str_replace(['_', '-'], ' ', $job->command));
                                    $serviceDesc = $job->description ?: __('Automated background service.');
                                @endphp

                                @if ($job->module && !moduleEnabled($job->module))
                                    @continue
                                @endif

                                <div class="p-4 rounded-2xl bg-white/[0.015] border border-white/[0.04] flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold {{ $isHealthy ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                            @if ($isHealthy)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-white font-bold block text-[12px]">{{ __($serviceName) }}</span>
                                                <span class="text-[9px] font-mono text-slate-500 bg-white/[0.04] px-1.5 py-0.5 rounded border border-white/[0.06]">{{ $intervalText }}</span>
                                            </div>
                                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ __($serviceDesc) }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 text-[10px]">
                                        <div class="text-right">
                                            <span class="text-slate-500 block text-[9px] uppercase font-bold">{{ __('Last Run') }}</span>
                                            <span class="font-bold {{ $isHealthy ? 'text-emerald-400' : 'text-rose-400' }}">
                                                {{ \Carbon\Carbon::createFromTimestamp($job->last_run)->diffForHumans() }}
                                            </span>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-md text-[9px] font-bold uppercase {{ $isHealthy ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                            {{ $isHealthy ? __('Active') : __('Pending / Overdue') }}
                                        </span>
                                    </div>
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
        function copyCmd(id) {
            const text = document.getElementById(id).innerText;
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ __('Cron command copied to clipboard.') }}',
                    showConfirmButton: false,
                    timer: 1500,
                    customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                });
            });
        }
    </script>
@endpush
