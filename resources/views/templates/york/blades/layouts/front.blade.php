@php
    $locale      = app()->getLocale();
    $languages   = config('languages');
    $currentLang = $languages[$locale] ?? ['name' => $locale, 'flag' => 'us'];
    $siteName    = getSetting('name', config('app.name'));
    $favicon     = asset('assets/images/' . getSetting('favicon', 'favicon.png'));
    $logoRect    = asset('assets/images/' . getSetting('logo_rectangle', 'logo.png'));
    $seoImage    = asset('assets/images/' . getSetting('seo_image', 'seo-banner.png'));

    $title = request()->routeIs('home')
        ? __(':site_name | Automated AI Bot & Copy Trading Platform', ['site_name' => $siteName])
        : ($page_title ?? $siteName) . ' - ' . $siteName;

    $description = $page_description ?? __(getSetting('seo_description',
        'Automate your crypto trading with algorithmic AI bots and mirror verified strategies in real time with copy trading on :site_name. Fast execution, total portfolio control.'
    ), ['site_name' => $siteName]);

    $keywords = $page_keywords ?? __(getSetting('seo_keywords',
        'copy trading, AI trading bots, automated crypto trading, algorithmic trading, crypto copy trading, automated trading strategies, quantitative trading, :site_name'
    ), ['site_name' => $siteName]);

    $socialTitle = $page_title ?? __(getSetting('social_title', ':site_name | Automated AI Bot & Copy Trading Platform'), ['site_name' => $siteName]);
    $socialDescription = $page_description ?? __(getSetting('social_description',
        'Deploy automated AI trading bots and copy top-performing crypto traders in real time on :site_name. Institutional-grade execution built for modern digital asset traders.'
    ), ['site_name' => $siteName]);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ config('languages.' . app()->getLocale() . '.rtl') ? 'rtl' : 'ltr' }}"
      class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $keywords }}">
    <link rel="shortcut icon" href="{{ $favicon }}" type="image/x-icon">

    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $socialTitle }}">
    <meta property="og:description" content="{{ $socialDescription }}">
    <meta property="og:image" content="{{ $seoImage }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $socialTitle }}">
    <meta name="twitter:description" content="{{ $socialDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    {{-- Fonts & Libraries --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Tailwind CSS via Vite (CSS only) --}}
    @if (config('site.use_vite'))
        @vite(['resources/views/templates/york/css/app.css'])
    @else
        <link rel="stylesheet" href="{{ asset('assets/templates/york/css/main.css' . '?v=' . filemtime(public_path('assets/templates/york/css/main.css'))) }}">
    @endif

    <style>
        /* ── BASE & TYPOGRAPHY ─────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background: #050507;
            color: #f1f5f9;
            overflow-x: hidden;
            margin: 0;
        }
        ::-webkit-scrollbar            { width: 6px; }
        ::-webkit-scrollbar-track      { background: #050507; }
        ::-webkit-scrollbar-thumb      { background: rgba(255,255,255,0.07); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover{ background: rgba(255,255,255,0.14); }

        /* ── OPTION B: COMMAND-BAR TOP DECK ──────────────────────────────── */
        #cmd-top-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 3.75rem;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0 max(1rem, 3vw);
            background: rgba(5, 5, 8, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s cubic-bezier(0.32, 0.72, 0, 1);
        }
        #cmd-top-bar.scrolled {
            background: rgba(3, 4, 7, 0.96);
            border-bottom-color: rgba(0, 245, 255, 0.15);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8);
        }

        /* Desktop Command Trigger Button */
        .cmd-trigger-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.85rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.09);
            color: rgba(203, 213, 225, 0.85);
            font-family: 'Outfit', sans-serif;
            font-size: 0.78rem;
            font-weight: 500;
            cursor: pointer;
            flex: 1; max-width: 22rem;
            transition: all 0.22s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .cmd-trigger-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(0, 245, 255, 0.35);
            color: #fff;
            box-shadow: 0 0 20px rgba(0, 245, 255, 0.15);
        }
        .cmd-key-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.62rem;
            font-weight: 700;
            padding: 0.15rem 0.4rem;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.9);
            line-height: 1;
            margin-left: auto;
        }

        /* Mobile Menu Toggle Button */
        #front-ham-btn {
            display: none;
            width: 2.25rem; height: 2.25rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.055);
            border: 1px solid rgba(255, 255, 255, 0.1);
            align-items: center; justify-content: center;
            cursor: pointer; flex-shrink: 0;
            transition: background 0.2s, border-color 0.2s;
        }
        #front-ham-btn:hover { background: rgba(255, 255, 255, 0.12); border-color: rgba(0, 245, 255, 0.3); }

        .ham-line {
            stroke: rgba(255, 255, 255, 0.85);
            stroke-width: 1.8;
            stroke-linecap: round;
            transform-origin: 8px 8px;
            transition: transform 0.35s cubic-bezier(0.32, 0.72, 0, 1), opacity 0.25s;
        }
        .ham-top { transform: translateY(-3px); }
        .ham-bot { transform: translateY(3px); }
        #front-ham-btn.open .ham-top { transform: rotate(45deg); }
        #front-ham-btn.open .ham-bot { transform: rotate(-45deg); }

        /* Responsive Desktop vs Mobile visibility */
        @media (max-width: 767px) {
            .cmd-trigger-btn { display: none !important; }
            #front-ham-btn { display: flex !important; }
            .top-action-pill { display: none !important; }
        }

        /* Action Buttons */
        .top-action-pill {
            font-family: 'Outfit', sans-serif;
            font-size: 0.78rem;
            font-weight: 600;
            color: rgba(203, 213, 225, 0.8);
            text-decoration: none;
            padding: 0.4rem 0.75rem;
            border-radius: 9999px;
            transition: all 0.2s;
        }
        .top-action-pill:hover { color: #fff; background: rgba(255, 255, 255, 0.05); }

        .top-cta-btn {
            font-family: 'Outfit', sans-serif;
            font-size: 0.78rem;
            font-weight: 700;
            color: #050507;
            text-decoration: none;
            padding: 0.45rem 0.95rem;
            border-radius: 9999px;
            background: #00f5ff;
            box-shadow: 0 0 20px rgba(0, 245, 255, 0.25);
            transition: all 0.25s cubic-bezier(0.32, 0.72, 0, 1);
            white-space: nowrap;
        }
        .top-cta-btn:hover {
            transform: scale(1.03);
            background: #33f7ff;
            box-shadow: 0 0 25px rgba(0, 245, 255, 0.45);
            color: #000;
        }

        /* ── ELEGANT SHEET DRAWER FOR MOBILE ──────────────────────────────── */
        #mobile-sheet-backdrop {
            position: fixed; inset: 0; z-index: 60;
            background: rgba(3, 4, 8, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            opacity: 0; pointer-events: none;
            transition: opacity 0.35s cubic-bezier(0.32, 0.72, 0, 1);
        }
        #mobile-sheet-backdrop.open { opacity: 1; pointer-events: all; }

        .mobile-sheet-card {
            position: fixed; top: 4.5rem; left: 1rem; right: 1rem;
            z-index: 61;
            background: rgba(10, 14, 24, 0.94);
            backdrop-filter: blur(32px);
            -webkit-backdrop-filter: blur(32px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            padding: 1.25rem;
            max-height: calc(100vh - 5.5rem);
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8), inset 0 1px 0 rgba(255, 255, 255, 0.12);
            opacity: 0; transform: translateY(-0.75rem) scale(0.98);
            pointer-events: none;
            transition: opacity 0.35s cubic-bezier(0.32, 0.72, 0, 1), transform 0.35s cubic-bezier(0.32, 0.72, 0, 1);
        }
        #mobile-sheet-backdrop.open .mobile-sheet-card {
            opacity: 1; transform: translateY(0) scale(1); pointer-events: all;
        }

        .sheet-nav-group { display: flex; flex-direction: column; gap: 0.25rem; }
        .sheet-nav-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.75rem 1rem; border-radius: 0.9rem;
            font-family: 'Outfit', sans-serif; font-size: 0.92rem; font-weight: 600;
            color: rgba(226, 232, 240, 0.85); text-decoration: none;
            transition: all 0.2s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .sheet-nav-item:hover, .sheet-nav-item.active {
            background: rgba(0, 245, 255, 0.08); color: #00f5ff;
        }

        .sheet-sub-item {
            font-size: 0.82rem; font-weight: 500; color: rgba(148, 163, 184, 0.6);
            padding: 0.6rem 1rem; border-radius: 0.75rem; text-decoration: none;
            display: flex; align-items: center; gap: 0.5rem; transition: color 0.18s;
        }
        .sheet-sub-item:hover { color: #fff; }

        .sheet-divider {
            height: 1px; background: rgba(255, 255, 255, 0.07); margin: 0.75rem 0;
        }

        .sheet-actions {
            display: flex; gap: 0.6rem; margin-top: 0.75rem;
        }
        .sheet-btn-secondary {
            flex: 1; text-align: center; padding: 0.7rem; border-radius: 0.9rem;
            background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.09);
            font-family: 'Outfit', sans-serif; font-size: 0.8rem; font-weight: 700;
            color: #fff; text-decoration: none; transition: background 0.2s;
        }
        .sheet-btn-secondary:hover { background: rgba(255, 255, 255, 0.08); }

        .sheet-btn-primary {
            flex: 1; text-align: center; padding: 0.7rem; border-radius: 0.9rem;
            background: #00f5ff;
            font-family: 'Outfit', sans-serif; font-size: 0.8rem; font-weight: 700;
            color: #050507; text-decoration: none; box-shadow: 0 0 20px rgba(0, 245, 255, 0.25);
        }

        /* ── COMMAND K OVERLAY MODAL (DESKTOP) ─────────────────────────────── */
        #cmd-modal-backdrop {
            position: fixed; inset: 0; z-index: 100;
            background: rgba(3, 4, 8, 0.88);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            display: flex; justify-content: center; align-items: flex-start;
            padding: max(1rem, 5vh) 1rem 1rem;
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s cubic-bezier(0.32, 0.72, 0, 1);
        }
        #cmd-modal-backdrop.open { opacity: 1; pointer-events: all; }

        .cmd-card-dialog {
            width: 100%; max-width: 40rem;
            background: rgba(10, 14, 24, 0.96);
            border: 1px solid rgba(0, 245, 255, 0.25);
            border-radius: 1.5rem;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.9), inset 0 1px 0 rgba(255, 255, 255, 0.12);
            overflow: hidden;
            transform: scale(0.95) translateY(-1rem);
            transition: transform 0.28s cubic-bezier(0.32, 0.72, 0, 1);
            max-height: 85vh;
            display: flex; flex-direction: column;
        }
        #cmd-modal-backdrop.open .cmd-card-dialog { transform: scale(1) translateY(0); }

        .cmd-search-header {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.9rem 1.1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.02);
            flex-shrink: 0;
        }
        .cmd-search-input {
            flex: 1; background: transparent; border: none; outline: none;
            color: #fff; font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 500;
        }
        .cmd-search-input::placeholder { color: rgba(148, 163, 184, 0.5); }

        .cmd-results-list {
            flex: 1; overflow-y: auto;
            padding: 0.6rem;
            -webkit-overflow-scrolling: touch;
        }
        .cmd-group-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.62rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.12em; color: rgba(0, 245, 255, 0.65);
            padding: 0.6rem 0.75rem 0.3rem;
        }
        .cmd-item-link {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.75rem 0.85rem; border-radius: 0.85rem;
            color: rgba(226, 232, 240, 0.9); text-decoration: none;
            font-family: 'Outfit', sans-serif; font-size: 0.88rem; font-weight: 600;
            transition: all 0.15s;
        }
        .cmd-item-link:hover, .cmd-item-link:active {
            background: rgba(0, 245, 255, 0.1);
            color: #fff; transform: translateX(3px);
        }
        .cmd-item-desc {
            font-size: 0.72rem; color: rgba(148, 163, 184, 0.55);
            font-family: 'Inter', sans-serif;
        }

        .cmd-footer-hints {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.65rem 1.1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            background: rgba(0, 0, 0, 0.3);
            font-family: 'JetBrains Mono', monospace; font-size: 0.65rem;
            color: rgba(148, 163, 184, 0.5);
            flex-shrink: 0;
        }

        /* ── FOOTER ─────────────────────────────────────────────────────────── */
        #front-footer {
            background: #030305;
            position: relative; overflow: hidden;
        }
        .f-orb {
            position: absolute; border-radius: 9999px;
            filter: blur(110px); pointer-events: none;
        }
        .f-orb-1 {
            width: 700px; height: 320px;
            background: radial-gradient(ellipse, rgba(0,245,255,0.045), transparent 70%);
            top: 0; left: -8%;
        }
        .f-orb-2 {
            width: 550px; height: 440px;
            background: radial-gradient(ellipse, rgba(99,102,241,0.05), transparent 70%);
            bottom: 0; right: -4%;
        }

        /* Giant display watermark */
        .f-display {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(3.5rem, 12vw, 10.5rem);
            font-weight: 900;
            letter-spacing: -0.04em;
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 1px rgba(255,255,255,0.055);
            user-select: none; pointer-events: none;
        }

        /* Chain ticker */
        .ticker-viewport {
            overflow: hidden;
            mask-image: linear-gradient(to right, transparent, #000 10%, #000 90%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, #000 10%, #000 90%, transparent);
        }
        .ticker-track {
            display: flex;
            width: max-content;
            animation: ticker 22s linear infinite;
        }
        .ticker-viewport:hover .ticker-track { animation-play-state: paused; }
        @keyframes ticker {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }
        .ticker-item {
            display: flex; align-items: center; gap: 0.55rem;
            padding: 0 1.5rem; white-space: nowrap;
        }
        .ticker-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.68rem; font-weight: 700;
            letter-spacing: 0.07em;
            color: rgba(148,163,184,0.65);
            padding: 0.27rem 0.75rem;
            border-radius: 9999px;
            border: 1px solid rgba(255,255,255,0.07);
            background: rgba(255,255,255,0.018);
        }
        .ticker-dot {
            width: 3px; height: 3px; border-radius: 9999px;
            background: rgba(255,255,255,0.09); flex-shrink: 0;
        }

        /* Footer content */
        .f-grid {
            display: grid;
            grid-template-columns: 1.1fr 1fr 1fr;
            gap: 3.5rem;
        }
        @media (max-width: 767px) {
            .f-grid { grid-template-columns: 1fr; gap: 2.25rem; }
        }

        .f-nav-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.62rem; font-weight: 700;
            letter-spacing: 0.15em; text-transform: uppercase;
            color: rgba(0,245,255,0.55);
            margin-bottom: 1.1rem; display: block;
        }
        .f-nav-link {
            display: block;
            font-family: 'Outfit', sans-serif;
            font-size: 0.83rem; font-weight: 500;
            color: rgba(100,116,139,0.7);
            text-decoration: none;
            padding: 0.22rem 0;
            transition: color 0.2s cubic-bezier(0.32,0.72,0,1);
        }
        .f-nav-link:hover { color: rgba(241,245,249,0.88); }

        /* Legal strip */
        .f-legal {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 1rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .f-legal-copy {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.62rem; letter-spacing: 0.025em;
            color: rgba(71,85,105,0.65);
        }
        .f-legal-links { display: flex; gap: 1.25rem; flex-wrap: wrap; }
        .f-legal-link {
            font-family: 'Outfit', sans-serif;
            font-size: 0.68rem; font-weight: 500;
            color: rgba(71,85,105,0.65);
            text-decoration: none;
            transition: color 0.18s;
        }
        .f-legal-link:hover { color: rgba(148,163,184,0.88); }

        /* Utility */
        .glass-panel {
            background: rgba(9,12,20,0.88);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.06), 0 20px 40px rgba(0,0,0,0.4);
        }
    </style>

    {!! getSetting('header_scripts') !!}
    @stack('css')
</head>
<body class="bg-[#050507] text-slate-100 antialiased" style="selection-color:#fff; selection-background:rgba(0,245,255,0.2);">
    {{-- Animated Preloader --}}
    @include('templates.york.blades.partials.dashoard-preloader')

    {{-- ════════════════════════════════════════════════════════════════════
         OPTION B: COMMAND-BAR TOP DECK (WITH ELEGANT MOBILE SHEET)
    ════════════════════════════════════════════════════════════════════ --}}
    <header id="cmd-top-bar">
        {{-- Brand Logo --}}
        <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:0.5rem; text-decoration:none; flex-shrink:0;">
            <img src="{{ $logoRect }}" alt="{{ $siteName }}" style="max-height:26px; max-width:115px; object-fit:contain;">
        </a>

        {{-- Desktop Center ⌘K Trigger Button --}}
        <button class="cmd-trigger-btn" id="open-cmd-btn" type="button" aria-label="{{ __('Open Command Palette') }}">
            <svg style="width:14px; height:14px; stroke:rgba(0,245,255,0.85); fill:none; flex-shrink:0;" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span>{{ __('Search or jump to...') }}</span>
            <span class="cmd-key-badge">⌘ K</span>
        </button>

        {{-- Mobile Hamburger Trigger Button --}}
        <button id="front-ham-btn" type="button" aria-label="{{ __('Open Menu') }}" aria-expanded="false" aria-controls="mobile-sheet-backdrop">
            <svg style="width:16px;height:16px;overflow:visible;" viewBox="0 0 16 16">
                <line class="ham-line ham-top" x1="2" y1="8" x2="14" y2="8"/>
                <line class="ham-line ham-bot" x1="2" y1="8" x2="14" y2="8"/>
            </svg>
        </button>

        {{-- Right Actions --}}
        <div style="display:flex; align-items:center; gap:0.4rem; flex-shrink:0;">
            {{-- Language Switcher Dropdown --}}
            <div class="pointer-events-auto relative group" id="lang-switcher">
                <button id="lang-btn"
                    class="flex items-center gap-2 text-slate-400 hover:text-white transition-all bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.08] hover:border-cyan-500/30 rounded-full px-3.5 py-1.5 backdrop-blur-xl cursor-pointer text-xs font-mono shadow-lg"
                    onclick="toggleLanguageMenu(event)">
                    <img src="{{ asset('assets/flags/' . $currentLang['flag'] . '.svg') }}"
                        alt="{{ $currentLang['name'] }}" class="w-3.5 h-3.5 rounded-full object-cover">
                    <span class="font-medium hidden sm:inline-block">{{ $currentLang['name'] }}</span>
                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="lang-menu"
                    class="absolute right-0 top-full mt-2 w-40 bg-[#090c14]/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right overflow-hidden z-50 p-1 font-mono text-xs max-h-80 overflow-y-auto">
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

            @auth
                <a href="{{ route('user.dashboard') }}" class="top-cta-btn">{{ __('Dashboard') }}</a>
            @else
                <a href="{{ route('user.login') }}" class="top-action-pill hidden md:inline-flex">{{ __('Sign In') }}</a>
                <a href="{{ route('user.register') }}" class="top-cta-btn">{{ __('Open Account') }}</a>
            @endauth
        </div>
    </header>

    {{-- ════════════════════════════════════════════════════════════════════
         ELEGANT FLOATING SHEET DRAWER FOR MOBILE
    ════════════════════════════════════════════════════════════════════ --}}
    <div id="mobile-sheet-backdrop" role="dialog" aria-modal="true">
        <div class="mobile-sheet-card">
            <div class="sheet-nav-group">
                <a href="{{ route('home') }}" class="sheet-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <span>{{ __('Home') }}</span>
                    <svg style="width:14px; height:14px; stroke:currentColor; fill:none;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('trading-bots') }}" class="sheet-nav-item {{ request()->routeIs('trading-bots') ? 'active' : '' }}">
                    <span>{{ __('AI Trading Bots') }}</span>
                    <svg style="width:14px; height:14px; stroke:currentColor; fill:none;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('copy-trading') }}" class="sheet-nav-item {{ request()->routeIs('copy-trading') ? 'active' : '' }}">
                    <span>{{ __('Copy Trading') }}</span>
                    <svg style="width:14px; height:14px; stroke:currentColor; fill:none;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="sheet-divider"></div>

            <div style="display:flex; flex-direction:column; gap:0.15rem;">
                <a href="{{ route('about') }}" class="sheet-sub-item">
                    <span style="color:#00f5ff; font-size:0.75rem;">•</span>
                    <span>{{ __('About Us') }}</span>
                </a>
                <a href="{{ route('license') }}" class="sheet-sub-item">
                    <span style="color:#00f5ff; font-size:0.75rem;">•</span>
                    <span>{{ __('Licenses & Regulation') }}</span>
                </a>
                <a href="{{ route('contact') }}" class="sheet-sub-item">
                    <span style="color:#00f5ff; font-size:0.75rem;">•</span>
                    <span>{{ __('Contact Us') }}</span>
                </a>
            </div>

            <div class="sheet-divider"></div>

            {{-- Mobile Language Selector --}}
            <div style="margin-bottom: 0.5rem;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 0.4rem; padding: 0 0.25rem;">
                    <span style="font-size: 0.7rem; font-family:'JetBrains Mono', monospace; text-transform:uppercase; letter-spacing:0.05em; color:rgba(148, 163, 184, 0.6);">{{ __('Language') }}</span>
                    <span style="display:flex; align-items:center; gap:0.35rem; font-size: 0.75rem; color:#00f5ff; font-family:'JetBrains Mono', monospace;">
                        <img src="{{ asset('assets/flags/' . $currentLang['flag'] . '.svg') }}" alt="{{ $currentLang['name'] }}" style="width:14px; height:14px; border-radius:9999px; object-fit:cover;">
                        {{ $currentLang['name'] }}
                    </span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.35rem; max-height: 140px; overflow-y: auto; padding: 0.25rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem;">
                    @foreach ($languages as $code => $lang)
                        <a href="{{ route('lang.switch', $code) }}"
                            style="display:flex; align-items:center; gap:0.4rem; padding:0.4rem 0.5rem; border-radius:0.5rem; font-size:0.72rem; font-family:'JetBrains Mono', monospace; text-decoration:none; transition:all 0.15s; {{ app()->getLocale() == $code ? 'background:rgba(0,245,255,0.12); color:#00f5ff; font-weight:700;' : 'color:rgba(203,213,225,0.7);' }}">
                            <img src="{{ asset('assets/flags/' . $lang['flag'] . '.svg') }}" alt="{{ $lang['name'] }}" style="width:14px; height:14px; border-radius:9999px; object-fit:cover; flex-shrink:0;">
                            <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $lang['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="sheet-divider"></div>

            <div class="sheet-actions">
                @auth
                    <a href="{{ route('user.dashboard') }}" class="sheet-btn-primary">{{ __('Go to Dashboard') }}</a>
                @else
                    <a href="{{ route('user.login') }}" class="sheet-btn-secondary">{{ __('Sign In') }}</a>
                    <a href="{{ route('user.register') }}" class="sheet-btn-primary">{{ __('Open Account') }}</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         COMMAND-K SEARCH & NAVIGATION MODAL (DESKTOP)
    ════════════════════════════════════════════════════════════════════ --}}
    <div id="cmd-modal-backdrop" role="dialog" aria-modal="true">
        <div class="cmd-card-dialog">
            <div class="cmd-search-header">
                <svg style="width:18px; height:18px; stroke:rgba(0,245,255,0.9); fill:none; flex-shrink:0;" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="cmd-input" class="cmd-search-input" placeholder="{{ __('Search pages or explore features...') }}" autofocus autocomplete="off">
                <span class="cmd-key-badge" style="cursor:pointer;" id="close-cmd-btn">ESC</span>
            </div>

            <div class="cmd-results-list" id="cmd-list">
                <div class="cmd-group-label">{{ __('Products & Features') }}</div>
                <a href="{{ route('home') }}" class="cmd-item-link">
                    <span>{{ __('Home') }}</span>
                    <span class="cmd-item-desc">{{ __('Platform Overview') }}</span>
                </a>
                <a href="{{ route('trading-bots') }}" class="cmd-item-link">
                    <span>{{ __('AI Trading Bots') }}</span>
                    <span class="cmd-item-desc">{{ __('Automated Trading Strategies') }}</span>
                </a>
                <a href="{{ route('copy-trading') }}" class="cmd-item-link">
                    <span>{{ __('Copy Trading') }}</span>
                    <span class="cmd-item-desc">{{ __('Follow Verified Traders') }}</span>
                </a>

                <div class="cmd-group-label">{{ __('Company & Legal') }}</div>
                <a href="{{ route('about') }}" class="cmd-item-link">
                    <span>{{ __('About Us') }}</span>
                    <span class="cmd-item-desc">{{ __('Our Mission & Story') }}</span>
                </a>
                <a href="{{ route('license') }}" class="cmd-item-link">
                    <span>{{ __('Licenses & Regulation') }}</span>
                    <span class="cmd-item-desc">{{ __('Compliance & Safety') }}</span>
                </a>
                <a href="{{ route('contact') }}" class="cmd-item-link">
                    <span>{{ __('Contact Us') }}</span>
                    <span class="cmd-item-desc">{{ __('24/7 Customer Support') }}</span>
                </a>

                <div class="cmd-group-label">{{ __('Account') }}</div>
                @auth
                    <a href="{{ route('user.dashboard') }}" class="cmd-item-link">
                        <span>{{ __('Go to Dashboard') }}</span>
                        <span class="cmd-item-desc">{{ __('User Dashboard') }}</span>
                    </a>
                @else
                    <a href="{{ route('user.login') }}" class="cmd-item-link">
                        <span>{{ __('Sign In') }}</span>
                        <span class="cmd-item-desc">{{ __('Access Your Account') }}</span>
                    </a>
                    <a href="{{ route('user.register') }}" class="cmd-item-link">
                        <span>{{ __('Open Account') }}</span>
                        <span class="cmd-item-desc">{{ __('Create Free Account') }}</span>
                    </a>
                @endauth
            </div>

            <div class="cmd-footer-hints">
                <span>{{ __('Select to navigate') }}</span>
                <span>{{ __('Press') }} <strong style="color:#fff;">ESC</strong> {{ __('to exit') }}</span>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         MAIN CONTENT
    ════════════════════════════════════════════════════════════════════ --}}
    <main class="min-h-[100dvh]">
        @yield('content')
    </main>

    {{-- ════════════════════════════════════════════════════════════════════
         CINEMATIC FOOTER
    ════════════════════════════════════════════════════════════════════ --}}
    <footer id="front-footer">
        {{-- Ambient depth orbs --}}
        <div class="f-orb f-orb-1"></div>
        <div class="f-orb f-orb-2"></div>

        {{-- Giant watermark display name --}}
        <div style="overflow:hidden; padding-top:3.75rem; line-height:1;">
            <p class="f-display" style="padding:0 max(1.5rem, 3.5vw);" aria-hidden="true">
                {{ strtoupper($siteName) }}
            </p>
        </div>

        {{-- Chain ticker marquee --}}
        <div class="ticker-viewport"
             style="margin-top:2rem; border-top:1px solid rgba(255,255,255,0.04); border-bottom:1px solid rgba(255,255,255,0.04); padding:0.8rem 0;">
            <div class="ticker-track">
                @php $chains = ['SOL','ETH','TRX','POL','BTC','BNB','USDT','USDC','ARB','AVAX','OP','MATIC']; @endphp
                @foreach (array_merge($chains, $chains) as $chain)
                    <div class="ticker-item">
                        <span class="ticker-badge">{{ $chain }}</span>
                        <span class="ticker-dot"></span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Main grid --}}
        <div style="max-width:72rem; margin:0 auto; padding:3.75rem max(1.5rem,3.5vw) 2.75rem; position:relative; z-index:1;">
            <div class="f-grid">

                {{-- Brand column --}}
                <div style="display:flex; flex-direction:column; gap:1.2rem;">
                    <a href="{{ route('home') }}" style="line-height:0;">
                        <img src="{{ $logoRect }}" alt="{{ $siteName }}"
                             style="max-width:138px; max-height:34px; object-fit:contain;">
                    </a>
                    <p style="font-size:0.78rem; line-height:1.8; color:rgba(100,116,139,0.72); max-width:24ch; margin:0;">
                        {{ __('Automated crypto trading bots and copy trading, powered by fast and secure multi-chain blockchain settlement.') }}
                    </p>
                    {{-- Chain mini-badges --}}
                    <div style="display:flex; flex-wrap:wrap; gap:0.3rem;">
                        @foreach (['SOL','ETH','TRX','BNB','BTC'] as $t)
                            <span style="font-family:'JetBrains Mono',monospace; font-size:0.58rem; font-weight:700; letter-spacing:0.09em; color:rgba(100,116,139,0.55); padding:0.18rem 0.5rem; border-radius:9999px; border:1px solid rgba(255,255,255,0.055); background:rgba(255,255,255,0.01);">{{ $t }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- Nav columns from config (max 2) --}}
                @php $footerMenus = config('menus.footer_nav', []); $colIdx = 0; @endphp
                @foreach ($footerMenus as $col)
                    @if (($col['is_active'] ?? true) && $colIdx < 2)
                        <div>
                            <span class="f-nav-label">{{ __($col['name']) }}</span>
                            <div style="display:flex; flex-direction:column;">
                                @foreach ($col['items'] ?? [] as $fItem)
                                    @if ($fItem['is_active'] ?? true)
                                        <a href="{{ route($fItem['route_name']) }}" class="f-nav-link">
                                            {{ __($fItem['name']) }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @php $colIdx++; @endphp
                    @endif
                @endforeach
            </div>

            {{-- Legal strip --}}
            <div class="f-legal" style="margin-top:3rem;">
                <span class="f-legal-copy">
                    &copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved.') }}
                </span>
                <div class="f-legal-links">
                    <a href="{{ route('privacy-policy') }}"      class="f-legal-link">{{ __('Privacy') }}</a>
                    <a href="{{ route('terms-and-conditions') }}" class="f-legal-link">{{ __('Terms') }}</a>
                    <a href="{{ route('risk-disclosure') }}"      class="f-legal-link">{{ __('Risk Disclosure') }}</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- ════════════════════════════════════════════════════════════════════
         SCRIPTS
    ════════════════════════════════════════════════════════════════════ --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (config('site.use_vite'))
        @vite(['resources/views/templates/york/js/app.js'])
    @else
        <script src="{{ asset('assets/templates/york/js/main.js' . '?v=' . filemtime(public_path('assets/templates/york/js/main.js'))) }}"></script>
    @endif

    @yield('scripts')
    @stack('scripts')

    <script>
    (function () {
        'use strict';

        var topBar   = document.getElementById('cmd-top-bar');
        var backdrop = document.getElementById('cmd-modal-backdrop');
        var openBtn  = document.getElementById('open-cmd-btn');
        var closeBtn = document.getElementById('close-cmd-btn');
        var input    = document.getElementById('cmd-input');
        var links    = document.querySelectorAll('.cmd-item-link');
        var hamBtn   = document.getElementById('front-ham-btn');
        var sheetBackdrop = document.getElementById('mobile-sheet-backdrop');

        // Scroll listener for top bar
        window.addEventListener('scroll', function () {
            topBar.classList.toggle('scrolled', window.scrollY > 20);
        }, { passive: true });

        // Mobile Sheet Drawer toggle
        function toggleSheet() {
            var isOpen = sheetBackdrop.classList.contains('open');
            sheetBackdrop.classList.toggle('open', !isOpen);
            hamBtn.classList.toggle('open', !isOpen);
            document.body.style.overflow = !isOpen ? 'hidden' : '';
        }

        if (hamBtn && sheetBackdrop) {
            hamBtn.addEventListener('click', toggleSheet);
            sheetBackdrop.addEventListener('click', function(e) {
                if (e.target === sheetBackdrop) toggleSheet();
            });
            sheetBackdrop.querySelectorAll('a').forEach(function(a) {
                a.addEventListener('click', toggleSheet);
            });
        }

        // Desktop Command Modal functions
        function openCmd() {
            backdrop.classList.add('open');
            document.body.style.overflow = 'hidden';
            setTimeout(function() { input.focus(); }, 50);
        }
        function closeCmd() {
            backdrop.classList.remove('open');
            document.body.style.overflow = '';
            input.value = '';
            filterResults('');
        }

        if (openBtn) openBtn.addEventListener('click', openCmd);
        if (closeBtn) closeBtn.addEventListener('click', closeCmd);

        // Backdrop click to close
        backdrop.addEventListener('click', function(e) {
            if (e.target === backdrop) closeCmd();
        });

        // ⌘K / Ctrl+K & Escape Keybinds
        document.addEventListener('keydown', function (e) {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                backdrop.classList.contains('open') ? closeCmd() : openCmd();
            }
            if (e.key === 'Escape') {
                if (backdrop.classList.contains('open')) closeCmd();
                if (sheetBackdrop.classList.contains('open')) toggleSheet();
            }
        });

        // Real-time filtering inside command palette
        function filterResults(query) {
            var q = query.toLowerCase().trim();
            links.forEach(function(link) {
                var text = link.textContent.toLowerCase();
                if (!q || text.indexOf(q) !== -1) {
                    link.style.display = 'flex';
                } else {
                    link.style.display = 'none';
                }
            });
        }

        if (input) {
            input.addEventListener('input', function(e) {
                filterResults(e.target.value);
            });
        }

    }());
    </script>

    <script>
        function toggleLanguageMenu(event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById('lang-menu');
            if (!menu) return;
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

    {!! Blade::render(getSetting('livechat_scripts')) !!}
    {!! getSetting('footer_scripts') !!}
</body>
</html>
