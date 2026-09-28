<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#050507]">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Platform Activation Required') }} - {{ getSetting('name') ?? config('app.name', 'Foyana') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/' . getSetting('favicon', 'favicon.png')) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    @if (config('site.use_vite'))
        @vite(['resources/views/templates/york/css/app.css'])
    @else
        <link rel="stylesheet" href="{{ asset('assets/templates/york/css/main.css' . '?v=' . (file_exists(public_path('assets/templates/york/css/main.css')) ? filemtime(public_path('assets/templates/york/css/main.css')) : time())) }}">
    @endif

    <style>
        html, body {
            background-color: #050507 !important;
            color: #e2e8f0 !important;
            font-family: 'Space Grotesk', sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>

<body class="bg-[#050507] text-slate-200 antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden selection:bg-[#00f5ff] selection:text-[#050507]">

    {{-- Ambient Lighting Orbs --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-[-15%] left-[20%] w-[600px] h-[600px] bg-cyan-500/10 rounded-full blur-[180px] animate-pulse" style="animation-duration: 7s;"></div>
        <div class="absolute bottom-[-15%] right-[20%] w-[600px] h-[600px] bg-purple-600/10 rounded-full blur-[180px] animate-pulse" style="animation-duration: 9s;"></div>
    </div>

    {{-- Header --}}
    <header class="w-full max-w-7xl mx-auto px-6 py-8 flex items-center justify-between relative z-20">
        <div class="flex items-center gap-3">
            <span class="text-2xl font-black text-white tracking-tight">
                {{ getSetting('name', config('app.name', 'FOYANA')) }}
            </span>
        </div>
        <div>
            <a href="{{ route('admin.login') }}" class="flex items-center gap-2 text-xs font-mono text-cyan-400 hover:text-cyan-300 transition-colors bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 rounded-full px-4 py-2 backdrop-blur-md">
                <span>{{ __('Admin Sign In') }}</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </header>

    {{-- Main Chassis --}}
    <main class="flex-1 flex items-center justify-center px-4 py-8 relative z-10">
        <div class="max-w-2xl w-full p-2.5 sm:p-3 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_30px_90px_rgba(0,0,0,0.9)] relative overflow-hidden">
            
            <div class="absolute top-0 left-1/4 right-1/4 h-[1.5px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent"></div>

            <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] overflow-hidden p-8 sm:p-12 text-center relative">
                
                {{-- Status Pill --}}
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('INITIALIZATION PENDING') }}
                </div>

                <div class="relative inline-block mb-4">
                    <h1 class="text-6xl sm:text-8xl font-black font-mono tracking-tighter text-transparent bg-clip-text bg-gradient-to-b from-white via-cyan-100 to-cyan-400/30 leading-none select-none drop-shadow-2xl">
                        INIT
                    </h1>
                    <div class="absolute inset-0 bg-cyan-500/20 blur-[60px] rounded-full -z-10 pointer-events-none"></div>
                </div>

                <div class="space-y-3 max-w-md mx-auto mb-8">
                    <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                        {{ __('Platform License Required') }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed font-body">
                        {{ __('This platform installation is pending license activation. If you are the system administrator, please log in to activate your platform product key.') }}
                    </p>
                </div>

                {{-- Action Button --}}
                <div class="flex items-center justify-center pt-4 border-t border-white/[0.06]">
                    <a href="{{ route('admin.login') }}"
                        class="pl-7 pr-3 py-3 rounded-full bg-[#00f5ff] hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider transition-all duration-300 flex items-center gap-4 shadow-[0_0_25px_rgba(0,245,255,0.35)] group">
                        <span>{{ __('Administrator Login & Activation') }}</span>
                        <span class="w-7 h-7 rounded-full bg-[#05070d]/20 flex items-center justify-center group-hover:translate-x-1 transition-transform">
                            <svg class="w-3.5 h-3.5 text-[#050507]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </span>
                    </a>
                </div>

            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="w-full max-w-7xl mx-auto px-6 py-6 text-center relative z-20">
        <p class="font-mono text-[10px] text-slate-500 uppercase tracking-widest">
            © {{ date('Y') }} {{ getSetting('name', config('app.name', 'Foyana')) }} · {{ __('All rights reserved.') }}
        </p>
    </footer>

</body>
</html>
