<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ config('languages.' . app()->getLocale() . '.rtl') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page_title ?? 'User Portal' }} | {{ getSetting('name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/' . getSetting('favicon')) }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#050507">
    <meta name="author" content="{{ getSetting('name') }} Team">
    <meta name="publisher" content="{{ getSetting('name') }} Team">

    @if (config('site.use_vite'))
        @vite(['resources/views/templates/york/css/app.css'])
    @else
        <link rel="stylesheet"
            href="{{ asset('assets/templates/york/css/main.css' . '?v=' . filemtime(public_path('assets/templates/york/css/main.css'))) }}">
    @endif
    {!! getSetting('header_scripts') !!}
</head>

<body class="bg-[#050507] text-slate-200 font-sans antialiased min-h-screen relative isolate overflow-x-hidden flex flex-col justify-between selection:bg-cyan-500 selection:text-black">
    {{-- Animated Preloader --}}
    @include('templates.york.blades.partials.dashoard-preloader')

    {{-- Global Ambient Glows & Grid Pattern --}}
    <div class="fixed inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.025] pointer-events-none -z-20"></div>
    <div class="fixed top-[-10%] left-1/2 -translate-x-1/2 w-[700px] h-[500px] bg-cyan-500/10 rounded-full blur-[180px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 8s;"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-purple-600/10 rounded-full blur-[180px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 10s;"></div>

    {{-- Top Bar: Back to Home + Language Switcher --}}
    <div class="fixed top-5 inset-x-0 max-w-7xl mx-auto px-4 sm:px-8 flex items-center justify-between z-50 pointer-events-none">
        <a href="{{ route('home') }}" class="pointer-events-auto inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.08] text-slate-400 hover:text-white text-xs font-mono transition-all backdrop-blur-xl group shadow-lg">
            <svg class="w-3.5 h-3.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>{{ __('Return to Site') }}</span>
        </a>

        {{-- Language Switcher Dropdown --}}
        <div class="pointer-events-auto relative group" id="lang-switcher">
            <button id="lang-btn"
                class="flex items-center gap-2 text-slate-400 hover:text-white transition-all bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.08] hover:border-cyan-500/30 rounded-full px-3.5 py-1.5 backdrop-blur-xl cursor-pointer text-xs font-mono shadow-lg"
                onclick="toggleLanguageMenu(event)">
                @php
                    $locale = app()->getLocale();
                    $languages = config('languages');
                    $currentLang = $languages[$locale] ?? ['name' => $locale, 'flag' => 'us'];
                @endphp
                <img src="{{ asset('assets/flags/' . $currentLang['flag'] . '.svg') }}"
                    alt="{{ $currentLang['name'] }}" class="w-3.5 h-3.5 rounded-full object-cover">
                <span class="font-medium hidden sm:inline-block">{{ $currentLang['name'] }}</span>
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="lang-menu"
                class="absolute right-0 top-full mt-2 w-40 bg-[#090c14]/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right overflow-hidden z-50 p-1 font-mono text-xs">
                @foreach ($languages as $code => $lang)
                    <a href="{{ route('lang.switch', $code) }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/10 transition-colors {{ app()->getLocale() == $code ? 'bg-cyan-500/10 text-cyan-400 font-bold' : '' }}">
                        <img src="{{ asset('assets/flags/' . $lang['flag'] . '.svg') }}" alt="{{ $lang['name'] }}"
                            class="w-3.5 h-3.5 rounded-full object-cover">
                        <span>{{ $lang['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Central Form Card Wrapper --}}
    <div class="max-w-[460px] w-full mx-auto my-auto relative z-10 pt-20 pb-10 px-4 sm:px-6">
        
        {{-- Brand Header --}}
        <div class="mb-8 text-center">
            <a href="{{ route('home') }}" class="inline-block mb-3 group">
                <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-white/[0.02] border border-white/[0.08] group-hover:border-cyan-500/30 transition-all backdrop-blur-xl shadow-inner">
                    @php
                        $logo = getSetting('logo_rectangle');
                        $logoPath = public_path('assets/images/' . $logo);
                        $version = $logo && file_exists($logoPath) ? filemtime($logoPath) : time();
                    @endphp
                    @if ($logo)
                        <img src="{{ asset('assets/images/' . $logo) . '?v=' . $version }}"
                            alt="{{ getSetting('name') }}" class="h-9 w-auto object-contain">
                    @else
                        <span class="text-2xl font-mono font-bold text-white tracking-tight">{{ getSetting('name') }}</span>
                    @endif
                </div>
            </a>
        </div>

        {{-- Double-Bezel Glass Card --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_25px_70px_rgba(0,0,0,0.9)] relative overflow-hidden group">
            {{-- Laser Top Border Accent --}}
            <div class="absolute top-0 inset-x-10 h-[1.5px] bg-gradient-to-r from-transparent via-cyan-400/60 to-transparent"></div>

            <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 relative">
                @yield('content')
            </div>
        </div>

        {{-- Security Badge & Footer --}}
        <div class="mt-8 text-center space-y-2">
            <div class="flex items-center justify-center gap-2 text-[10px] font-mono text-slate-500">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>{{ __('256-Bit SSL Encrypted & Non-Custodial Safeguards') }}</span>
            </div>
            <p class="font-mono text-[10px] text-slate-600">&copy; {{ date('Y') }} {{ getSetting('name') }}. {{ __('All rights reserved.') }}</p>
        </div>

    </div>

    {{-- Empty footer spacer to maintain layout equilibrium --}}
    <div></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (config('site.use_vite'))
        @vite(['resources/views/templates/york/js/app.js'])
    @else
        <script
            src="{{ asset('assets/templates/york/js/main.js' . '?v=' . filemtime(public_path('assets/templates/york/js/main.js'))) }}">
        </script>
    @endif

    <script>
        function toggleLanguageMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('lang-menu');
            if (menu.classList.contains('invisible')) {
                menu.classList.remove('invisible', 'opacity-0');
            } else {
                menu.classList.add('invisible', 'opacity-0');
            }
        }

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('lang-menu');
            const btn = document.getElementById('lang-btn');
            if (menu && !menu.classList.contains('invisible') && btn && !btn.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('invisible', 'opacity-0');
            }
        });
    </script>

    @include('templates.york.blades.partials.notifications')
    @yield('scripts')

    {!! Blade::render(getSetting('livechat_scripts')) !!}
    {!! getSetting('footer_scripts') !!}
</body>

</html>
