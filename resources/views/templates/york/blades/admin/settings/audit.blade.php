@extends('templates.york.blades.admin.layouts.admin')

@php
    $root = base_path();

    if (!function_exists('row')) {
        function row(string $label, bool $ok, string $details = ''): array
        {
            return ['label' => $label, 'ok' => $ok, 'details' => $details];
        }
    }

    if (!function_exists('humanBytes')) {
        function humanBytes(int $bytes): string
        {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $i = 0;
            while ($bytes >= 1024 && $i < count($units) - 1) {
                $bytes /= 1024;
                $i++;
            }
            return sprintf('%.2f %s', $bytes, $units[$i]);
        }
    }

    $checks = [];
    $warnings = [];
    $findings = [];

    $start = microtime(true);

    // 1) System Information
    $checks[] = row(
        'PHP version',
        version_compare(PHP_VERSION, '8.3', '>='),
        'Current: ' . PHP_VERSION . ' | Recommended: 8.3+',
    );
    $checks[] = row('Laravel version', true, app()->version());
    $checks[] = row('App version', true, config('site.version', '1.0.0'));
    $checks[] = row('Server software', true, $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown');
    $checks[] = row(
        'Disk free space',
        (disk_free_space($root) ?: 0) > 1024 * 1024 * 200,
        humanBytes((int) (disk_free_space($root) ?: 0)),
    );

    // 1.1) PHP Extensions
    $required_extensions = [
        'bcmath', 'ctype', 'curl', 'dom', 'fileinfo', 'gd', 'gmp',
        'json', 'mbstring', 'openssl', 'pcre', 'pdo', 'tokenizer', 'xml', 'zip',
    ];
    foreach ($required_extensions as $ext) {
        $loaded = extension_loaded($ext);
        $checks[] = row('PHP Ext: ' . strtoupper($ext), $loaded, $loaded ? 'loaded' : 'missing');
    }

    // 1.2) Server Config
    $parse_size = function ($size) {
        $size = (string) $size;
        $unit = preg_replace('/[^bkmgtp]/i', '', $size);
        $sizeValue = preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            return round((float) $sizeValue * pow(1024, stripos('bkmgtp', $unit[0])));
        }
        return round((float) $sizeValue);
    };

    $server_config = [
        'post_max_size' => ['recommended' => '256M', 'current' => ini_get('post_max_size')],
        'upload_max_filesize' => ['recommended' => '256M', 'current' => ini_get('upload_max_filesize')],
        'max_execution_time' => ['recommended' => '300', 'current' => ini_get('max_execution_time')],
        'max_input_time' => ['recommended' => '300', 'current' => ini_get('max_input_time')],
        'memory_limit' => ['recommended' => '512M', 'current' => ini_get('memory_limit')],
    ];

    foreach ($server_config as $key => $config) {
        if ($key == 'max_execution_time' || $key == 'max_input_time') {
            $ok = (int) $config['current'] >= (int) $config['recommended'] || (int) $config['current'] == 0;
        } else {
            $ok = $parse_size($config['current']) >= $parse_size($config['recommended']);
        }
        $checks[] = row("Server: $key", $ok, "Current: {$config['current']} | Recommended: {$config['recommended']}");
    }

    // 2) APP_KEY + cipher sanity
    try {
        $cipher = (string) config('app.cipher');
        $key = (string) config('app.key');

        $supported = ['aes-128-cbc', 'aes-256-cbc', 'aes-128-gcm', 'aes-256-gcm'];
        $checks[] = row('APP_CIPHER supported', in_array($cipher, $supported, true), $cipher ?: '(empty)');

        $keyOk = false;
        $keyDetails = $key ?: '(empty)';
        $decodedLen = null;

        if (str_starts_with($key, 'base64:')) {
            $raw = base64_decode(substr($key, 7), true);
            if ($raw !== false) {
                $decodedLen = strlen($raw);
                if (str_contains($cipher, '256')) {
                    $keyOk = $decodedLen === 32;
                }
                if (str_contains($cipher, '128')) {
                    $keyOk = $decodedLen === 16;
                }
                $keyDetails = "base64 key decoded length: {$decodedLen} bytes";
            } else {
                $keyDetails = 'base64 decode failed';
            }
        } else {
            $decodedLen = strlen($key);
            if (str_contains($cipher, '256')) {
                $keyOk = $decodedLen === 32;
            }
            if (str_contains($cipher, '128')) {
                $keyOk = $decodedLen === 16;
            }
            $keyDetails = "raw key length: {$decodedLen} bytes (consider using base64:...)";
            $warnings[] = 'APP_KEY is not base64-prefixed. Laravel commonly uses base64: keys.';
        }

        $checks[] = row('APP_KEY length matches cipher', $keyOk, $keyDetails);
    } catch (Throwable $e) {
        $checks[] = row('APP_KEY / cipher check', false, $e->getMessage());
    }

    // 3) Critical directory permissions
    $folders = [
        'storage' => storage_path(),
        'storage/framework' => storage_path('framework'),
        'storage/app' => storage_path('app'),
        'storage/logs' => storage_path('logs'),
        'bootstrap/cache' => base_path('bootstrap/cache'),
    ];

    foreach ($folders as $name => $path) {
        $exists = file_exists($path);
        $perms = $exists ? substr(sprintf('%o', fileperms($path)), -3) : 'N/A';
        $status = $exists && is_writable($path) && (int) $perms >= 775;
        $checks[] = row("Perms: $name", $status, "Current: $perms | Recommended: 775");
    }

    $pubStoragePath = $root . '/public/storage';
    $isLink = is_link($pubStoragePath);
    $checks[] = row(
        'public/storage (symlink)',
        $isLink || is_dir($pubStoragePath),
        $isLink ? 'symlink OK' : (is_dir($pubStoragePath) ? 'dir exists' : 'missing'),
    );
    if (!$isLink) {
        $warnings[] = 'public/storage is not a symlink. Consider: php artisan storage:link';
    }

    // 3.1) Required files
    $required_files = [
        '.env' => base_path('.env'),
        'public/.htaccess' => public_path('.htaccess'),
        '.htaccess' => base_path('.htaccess'),
        'storage/app/public' => storage_path('app/public'),
    ];

    foreach ($required_files as $name => $path) {
        $exists = file_exists($path);
        $checks[] = row("File: $name", $exists, $exists ? 'Exists' : 'Missing');
    }

    // 4) .env exposure check
    $envPublic = file_exists($root . '/public/.env') || file_exists($root . '/public/.env.example');
    $checks[] = row('No .env file in public/', !$envPublic, $envPublic ? 'Found .env-like file in public/' : 'OK');

    // 5) DB connectivity
    try {
        $default = (string) config('database.default');
        $checks[] = row('DB default connection', $default !== '', $default ?: '(empty)');
        $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
        $checks[] = row('DB connection', $pdo !== null, 'Connected');
    } catch (Throwable $e) {
        $checks[] = row('DB connection', false, $e->getMessage());
    }

    // 6) Cache / Session / Queue sanity
    try {
        $checks[] = row('CACHE_DRIVER', (string) config('cache.default') !== '', (string) config('cache.default'));
        $checks[] = row('SESSION_DRIVER', (string) config('session.driver') !== '', (string) config('session.driver'));
        $checks[] = row('QUEUE_CONNECTION', (string) config('queue.default') !== '', (string) config('queue.default'));
    } catch (Throwable $e) {
        $checks[] = row('Cache/session/queue config', false, $e->getMessage());
    }

    // 7) App env/debug
    try {
        $env = (string) config('app.env');
        $debug = (bool) config('app.debug');
        $checks[] = row('APP_ENV', $env !== '', $env);
        if ($env === 'production') {
            $checks[] = row('APP_DEBUG off (production)', $debug === false, $debug ? 'TRUE (bad)' : 'false');
        } else {
            $checks[] = row('APP_DEBUG', true, $debug ? 'true' : 'false');
            $warnings[] = "APP_ENV is '{$env}'. Make sure this is intentional.";
        }
    } catch (Throwable $e) {
        $checks[] = row('APP_ENV/DEBUG', false, $e->getMessage());
    }

    // 8) Log file growth
    $logPath = $root . '/storage/logs/laravel.log';
    if (file_exists($logPath)) {
        $size = filesize($logPath) ?: 0;
        $checks[] = row('laravel.log size reasonable', $size < 50 * 1024 * 1024, humanBytes((int) $size));
        if ($size >= 50 * 1024 * 1024) {
            $warnings[] = 'laravel.log is large. Consider rotating/clearing logs.';
        }
    } else {
        $warnings[] = 'No storage/logs/laravel.log found (may be OK depending on logging config).';
    }

    $elapsed = microtime(true) - $start;

    $okCount = 0;
    $badCount = 0;
    foreach ($checks as $c) {
        if ($c['ok']) {
            $okCount++;
        } else {
            $badCount++;
        }
    }
@endphp

@section('content')
    <div class="space-y-8 mb-12 font-mono">
        
        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('SECURITY & AUDIT') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('System Audit & Diagnostics') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Review security checks, file permissions, and server environment health') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <a href="{{ route('admin.settings.audit.pdf') }}" target="_blank"
                    class="px-6 py-2.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/20 font-black uppercase tracking-wider transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>{{ __('Download Audit PDF') }}</span>
                </a>
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
                
                {{-- KPI SUMMARY METRICS --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-2 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[1.5rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                            <div>
                                <span class="text-[9px] uppercase font-bold text-slate-500 block">{{ __('Passed Checks') }}</span>
                                <span class="text-2xl font-black text-emerald-400 font-mono">{{ $okCount }}</span>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                                ✓
                            </div>
                        </div>
                    </div>

                    <div class="p-2 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[1.5rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                            <div>
                                <span class="text-[9px] uppercase font-bold text-slate-500 block">{{ __('Failed / Warnings') }}</span>
                                <span class="text-2xl font-black {{ $badCount > 0 ? 'text-rose-400' : 'text-slate-500' }} font-mono">{{ $badCount }}</span>
                            </div>
                            <div class="w-10 h-10 rounded-xl {{ $badCount > 0 ? 'bg-rose-500/10 border border-rose-500/20 text-rose-400' : 'bg-white/[0.03] border border-white/[0.08] text-slate-500' }} flex items-center justify-center font-bold">
                                !
                            </div>
                        </div>
                    </div>

                    <div class="p-2 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[1.5rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                            <div>
                                <span class="text-[9px] uppercase font-bold text-slate-500 block">{{ __('Scan Duration') }}</span>
                                <span class="text-2xl font-black text-cyan-400 font-mono">{{ number_format($elapsed, 3) }}s</span>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold">
                                ⚡
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SYSTEM CHECKS TABLE --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-6 text-xs">
                        <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Security & Environment Checklist') }}</h3>
                            <span class="text-[10px] text-slate-500">{{ __('System Checks') }}</span>
                        </div>

                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-white/[0.06] text-[10px] text-slate-500 uppercase tracking-widest font-black">
                                        <th class="py-3 px-4">{{ __('Status') }}</th>
                                        <th class="py-3 px-4">{{ __('Check Item') }}</th>
                                        <th class="py-3 px-4">{{ __('Details') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/[0.03]">
                                    @foreach ($checks as $c)
                                        <tr class="hover:bg-white/[0.02] transition-colors">
                                            <td class="py-3 px-4 w-28">
                                                @if ($c['ok'])
                                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-black text-[9px] uppercase">
                                                        {{ __('Pass') }}
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 font-black text-[9px] uppercase">
                                                        {{ __('Fail') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-white font-bold">{{ $c['label'] }}</td>
                                            <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">{{ $c['details'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- SYSTEM WARNINGS NOTICES --}}
                @if (!empty($warnings))
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-amber-500/20 backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-4 text-xs">
                            <h3 class="text-sm font-black text-amber-400 uppercase tracking-wider flex items-center gap-2">
                                <span>⚠️</span> {{ __('System Warnings') }}
                            </h3>
                            <div class="space-y-2">
                                @foreach ($warnings as $w)
                                    <div class="p-3 bg-amber-500/[0.04] border border-amber-500/15 rounded-xl text-amber-300 font-mono text-[11px]">
                                        {{ $w }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>
@endsection
