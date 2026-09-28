<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ config('languages.' . app()->getLocale() . '.rtl') ? 'rtl' : 'ltr' }}" class="h-full bg-[#050507]">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ getSetting('name') ?? config('app.name', 'Foyana') }}</title>
    <link rel="icon" type="image/png"
        href="{{ asset('assets/images/' . getSetting('favicon', 'favicon.png')) }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    @if (config('site.use_vite'))
        @vite(['resources/views/templates/york/css/app.css'])
    @else
        <link rel="stylesheet"
            href="{{ asset('assets/templates/york/css/main.css' . '?v=' . (file_exists(public_path('assets/templates/york/css/main.css')) ? filemtime(public_path('assets/templates/york/css/main.css')) : time())) }}">
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

<body class="bg-[#050507] text-slate-200 antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden selection:bg-[#00f5ff] selection:text-[#050507]" style="background-color: #050507 !important;">

    {{-- Ambient Lighting Orbs --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-[-15%] left-[20%] w-[600px] h-[600px] bg-cyan-500/10 rounded-full blur-[180px] animate-pulse" style="animation-duration: 7s;"></div>
        <div class="absolute bottom-[-15%] right-[20%] w-[600px] h-[600px] bg-purple-600/10 rounded-full blur-[180px] animate-pulse" style="animation-duration: 9s;"></div>
    </div>

    {{-- Top Header --}}
    <header class="w-full max-w-7xl mx-auto px-6 py-8 flex items-center justify-between relative z-20">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            @php
                $logo = getSetting('logo_rectangle');
                $logoPath = public_path('assets/images/' . $logo);
                $version = $logo && file_exists($logoPath) ? filemtime($logoPath) : time();
                $logoUrl = $logo ? asset('assets/images/' . $logo) . '?v=' . $version : null;
            @endphp

            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ getSetting('name') }}"
                    class="h-9 sm:h-11 w-auto transition-transform duration-300 group-hover:scale-105">
            @else
                <span class="text-2xl font-black text-white tracking-tight group-hover:text-cyan-300 transition-colors">
                    {{ getSetting('name', config('app.name', 'FOYANA')) }}
                </span>
            @endif
        </a>

        {{-- Language Switcher Dropdown --}}
        <div class="relative group">
            <button class="flex items-center gap-2.5 text-xs text-slate-300 hover:text-white transition-all bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.1] rounded-full px-4 py-2 backdrop-blur-md">
                @php
                    $locale = app()->getLocale();
                    $languages = config('languages') ?? [];
                    $currentLang = $languages[$locale] ?? ['name' => strtoupper($locale), 'flag' => 'us'];
                @endphp
                <img src="{{ asset('assets/flags/' . ($currentLang['flag'] ?? 'us') . '.svg') }}"
                    alt="{{ $currentLang['name'] }}" class="w-4 h-4 rounded-full object-cover">
                <span class="font-mono text-xs hidden sm:inline-block">{{ $currentLang['name'] }}</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-cyan-400 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            @if(count($languages) > 1)
            <div class="absolute right-0 top-full mt-2 w-44 bg-[#090c14]/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right overflow-hidden p-1 z-50">
                @foreach ($languages as $code => $lang)
                    <a href="{{ route('lang.switch', $code) }}"
                        class="flex items-center gap-3 px-3 py-2 text-xs font-mono text-slate-300 hover:text-white hover:bg-white/10 rounded-xl transition-colors {{ app()->getLocale() == $code ? 'bg-cyan-500/10 text-cyan-400' : '' }}">
                        <img src="{{ asset('assets/flags/' . ($lang['flag'] ?? 'us') . '.svg') }}"
                            alt="{{ $lang['name'] }}" class="w-4 h-4 rounded-full object-cover">
                        {{ $lang['name'] }}
                    </a>
                @endforeach
            </div>
            @endif
        </div>
    </header>

    {{-- Main Error Double-Bezel Card --}}
    <main class="flex-1 flex items-center justify-center px-4 py-8 relative z-10">
        <div class="max-w-3xl w-full p-2.5 sm:p-3 rounded-[2.5rem] sm:rounded-[3rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_30px_90px_rgba(0,0,0,0.9)] relative overflow-hidden group">
            
            {{-- Laser Top Accent Line --}}
            <div class="absolute top-0 left-1/4 right-1/4 h-[1.5px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent"></div>

            <div class="rounded-[2rem] sm:rounded-[2.5rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] overflow-hidden p-8 sm:p-14 text-center relative">
                
                {{-- Terminal Header --}}
                <div class="flex items-center justify-between pb-6 mb-8 border-b border-white/[0.06]">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                        <span class="text-[10px] font-mono text-slate-500 ml-2">{{ __('System Notification') }}</span>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[9px] font-mono font-bold uppercase tracking-wider">
                        ● {{ __('Notice') }}
                    </span>
                </div>

                {{-- Error Code Display --}}
                <div class="relative inline-block mb-4">
                    <h1 class="text-7xl sm:text-9xl font-black font-mono tracking-tighter text-transparent bg-clip-text bg-gradient-to-b from-white via-cyan-100 to-cyan-400/30 leading-none select-none drop-shadow-2xl">
                        @yield('code', 'ERR')
                    </h1>
                    <div class="absolute inset-0 bg-cyan-500/20 blur-[60px] rounded-full -z-10 pointer-events-none"></div>
                </div>

                {{-- Error Title & Description --}}
                <div class="space-y-3 max-w-lg mx-auto mb-10">
                    <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                        @yield('message', __('An Unexpected Error Occurred'))
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed font-body">
                        @yield('description', __('Something went wrong while processing your request. Please try again or return to the home page.'))
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4 border-t border-white/[0.06]">
                    <a href="{{ route('home') }}"
                        class="w-full sm:w-auto pl-7 pr-2.5 py-2.5 rounded-full bg-[#00f5ff] hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider transition-all duration-300 flex items-center justify-between sm:justify-start gap-4 shadow-[0_0_25px_rgba(0,245,255,0.35)] group">
                        <span>{{ __('Return to Home') }}</span>
                        <span class="w-8 h-8 rounded-full bg-[#05070d]/20 flex items-center justify-center group-hover:translate-x-1 group-hover:-translate-y-[1px] transition-transform">
                            <svg class="w-4 h-4 text-[#050507]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </span>
                    </a>

                    <button onclick="window.history.back()"
                        class="w-full sm:w-auto px-6 py-3 rounded-full bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.1] hover:border-white/[0.2] text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all duration-300 font-mono">
                        {{ __('← Go Back') }}
                    </button>
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
