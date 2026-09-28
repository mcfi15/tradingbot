<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ config('languages.' . app()->getLocale() . '.rtl') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page_title ?? 'Admin Console' }} | {{ getSetting('name', config('app.name', 'Foyana')) }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/' . getSetting('favicon', 'favicon.png')) }}">

    {{-- Indexing --}}
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#050507">
    <meta name="author" content="{{ getSetting('name') }} Team">

    {{-- Typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    @if (config('site.use_vite'))
        @vite(['resources/views/templates/york/css/app.css'])
    @else
        <link rel="stylesheet"
            href="{{ asset('assets/templates/york/css/main.css' . '?v=' . (file_exists(public_path('assets/templates/york/css/main.css')) ? filemtime(public_path('assets/templates/york/css/main.css')) : time())) }}">
    @endif

    {{-- York Custom Scrollbar & Sidebar Dock Physics --}}
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(0, 245, 255, 0.2);
            border-radius: 10px;
            transition: background 0.3s ease;
            cursor: pointer;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 245, 255, 0.5);
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 245, 255, 0.2) transparent;
        }

        /* 1. Sidebar width and items alignment */
        .york-sidebar {
            width: 5rem !important; /* w-20 */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .york-sidebar:hover {
            width: 18rem !important; /* w-72 */
            align-items: stretch !important;
        }

        /* 2. Logo name visibility */
        .york-logo-name {
            opacity: 0;
            width: 0;
            display: none;
            transition: opacity 0.3s ease, width 0.3s ease;
        }
        .york-sidebar:hover .york-logo-name {
            opacity: 1;
            width: auto;
            display: inline-block;
        }

        /* 3. Navigation items padding and alignment */
        .york-nav {
            align-items: center !important;
            transition: all 0.3s ease;
        }
        .york-sidebar:hover .york-nav {
            align-items: stretch !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        /* 4. Link buttons width and alignment */
        .york-nav a, .york-nav .submenu-toggle {
            justify-content: center !important;
            width: 3rem !important; /* w-12 */
            transition: all 0.3s ease !important;
        }
        .york-sidebar:hover .york-nav a, .york-sidebar:hover .york-nav .submenu-toggle {
            justify-content: flex-start !important;
            width: 100% !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        /* 5. Submenu content padding correction */
        .york-sidebar:hover .submenu-content {
            padding-left: 1rem !important;
            padding-right: 0.5rem !important;
        }

        /* 6. Text label visibility */
        .sidebar-label {
            display: none;
            opacity: 0;
            transition: opacity 0.2s ease 0.1s;
        }
        .york-sidebar:hover .sidebar-label {
            display: inline-block;
            opacity: 1;
        }

        /* 7. Submenu arrow visibility */
        .submenu-arrow {
            display: none;
        }
        .york-sidebar:hover .submenu-arrow {
            display: block;
        }

        /* 8. Profile info in footer */
        .york-profile-info {
            display: none;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .york-sidebar:hover .york-profile-info {
            display: block;
            opacity: 1;
            margin-left: 0.75rem !important;
        }

        /* 9. Hide submenu child links completely when sidebar is collapsed */
        aside:not(:hover) .submenu-content {
            display: none !important;
        }
    </style>

    {!! getSetting('header_scripts') !!}
    @stack('css')
</head>

<body class="bg-[#050507] text-slate-200 antialiased min-h-screen flex selection:bg-[#00f5ff] selection:text-[#050507] overflow-hidden">

    {{-- Admin Preloader --}}
    @include('templates.york.blades.partials.dashoard-preloader')

    {{-- Ambient Lighting Orbs --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute top-[-15%] left-[20%] w-[650px] h-[650px] bg-cyan-500/5 rounded-full blur-[180px] pointer-events-none animate-pulse" style="animation-duration: 9s;"></div>
        <div class="absolute bottom-[-15%] right-[20%] w-[650px] h-[650px] bg-purple-600/5 rounded-full blur-[180px] pointer-events-none animate-pulse" style="animation-duration: 11s;"></div>
    </div>

    <!-- ==================================================================================== -->
    <!-- DESKTOP SIDEBAR DOCK (Collapsible w-20 -> w-72) -->
    <!-- ==================================================================================== -->
    <aside class="hidden lg:flex flex-col w-20 hover:w-72 bg-gradient-to-b from-[#0a0a0c] to-[#040404] border-r border-white/[0.08] h-screen sticky top-0 z-30 transition-all duration-300 items-center hover:items-stretch py-6 group/sidebar york-sidebar shadow-[20px_0_40px_rgba(0,0,0,0.8)]">
        
        <!-- Logo Area -->
        <div class="h-16 flex items-center px-4 mb-6 shrink-0 w-full overflow-hidden relative">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 w-full max-w-full">
                {{-- Collapsed State Logo (Square) --}}
                <div class="flex items-center justify-center shrink-0 group-hover/sidebar:hidden transition-all duration-300 w-10 h-10">
                    @if (getSetting('logo_square'))
                        <img src="{{ asset('assets/images/' . getSetting('logo_square')) }}"
                            alt="{{ getSetting('name') }}"
                            class="max-w-[40px] max-h-[40px] w-auto h-auto object-contain rounded-xl">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-purple-600 flex items-center justify-center text-[#050507] font-black text-lg shadow-inner shrink-0">
                            {{ substr(getSetting('name', 'F'), 0, 1) }}
                        </div>
                    @endif
                </div>

                {{-- Expanded State Logo (Rectangle) --}}
                <div class="hidden group-hover/sidebar:flex items-center shrink-0 transition-all duration-300 max-w-[190px] overflow-hidden">
                    @if (getSetting('logo_rectangle'))
                        <img src="{{ asset('assets/images/' . getSetting('logo_rectangle')) }}"
                            alt="{{ getSetting('name') }}"
                            class="max-w-[170px] max-h-[40px] w-auto h-auto object-contain">
                    @elseif (getSetting('logo_square'))
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('assets/images/' . getSetting('logo_square')) }}"
                                alt="{{ getSetting('name') }}"
                                class="max-w-[36px] max-h-[36px] w-auto h-auto object-contain rounded-xl shrink-0">
                            <span class="text-white font-bold text-lg tracking-tight whitespace-nowrap truncate font-mono">
                                {{ getSetting('name') }}<span class="text-cyan-400">_ADM</span>
                            </span>
                        </div>
                    @else
                        <span class="text-white font-bold text-lg tracking-tight whitespace-nowrap truncate font-mono">
                            {{ getSetting('name', 'FOYANA') }}<span class="text-cyan-400">_ADM</span>
                        </span>
                    @endif
                </div>
            </a>
        </div>

        <!-- Navigation DOCK items -->
        <nav class="flex-1 w-full px-2 hover:px-4 flex flex-col items-center hover:items-stretch gap-3 overflow-y-auto scrollbar-hide york-nav">
            
            {{-- Admin Menu Section Label (Visible on Expand) --}}
            <div class="w-full px-2 pt-2 pb-1 hidden group-hover/sidebar:block">
                <span class="text-[9px] font-mono font-bold text-slate-500 uppercase tracking-widest">{{ __('MAIN DIRECTORY') }}</span>
            </div>

            @foreach ($admin_menu_items as $item)
                @php
                    $isActive = ($item->route_name && request()->routeIs($item->route_name)) ||
                                ($item->route_wildcard && request()->routeIs($item->route_wildcard)) ||
                                ($item->url && request()->url() == $item->url) ||
                                $item->children->contains(function ($child) {
                                    $cActive = ($child->route_name && request()->routeIs($child->route_name)) ||
                                               ($child->route_wildcard && request()->routeIs($child->route_wildcard)) ||
                                               ($child->route_name && request()->routeIs($child->route_name . '*'));

                                    if ($cActive && $child->params && is_array($child->params)) {
                                        foreach ($child->params as $k => $v) {
                                            if (request()->query($k) != $v) {
                                                return false;
                                            }
                                        }
                                    }
                                    return $cActive;
                                });
                @endphp

                @if ($item->children->isNotEmpty())
                    <div class="submenu-container w-full flex flex-col items-center group-hover/sidebar:items-stretch relative shrink-0" data-expanded="{{ $isActive ? 'true' : 'false' }}">
                        <button class="submenu-toggle flex items-center justify-center group-hover/sidebar:justify-start w-12 group-hover/sidebar:w-full h-12 group-hover/sidebar:px-4 rounded-xl transition-all duration-300 border {{ $isActive ? 'border-cyan-500/40 bg-cyan-500/10 text-cyan-400 shadow-[0_0_20px_rgba(0,245,255,0.15)]' : 'border-white/[0.06] hover:border-white/20 text-slate-400 hover:text-white hover:bg-white/[0.03]' }} cursor-pointer z-10 relative">
                            <div class="flex items-center shrink-0">
                                {{-- Icon --}}
                                <div class="w-5 h-5 flex items-center justify-center shrink-0 [&>svg]:w-5 [&>svg]:h-5 [&>svg]:stroke-[1.8] [&>svg]:fill-none">
                                    {!! $item->icon !!}
                                </div>
                                {{-- Label --}}
                                <span class="sidebar-label opacity-0 w-0 group-hover/sidebar:opacity-100 group-hover/sidebar:w-auto group-hover/sidebar:ml-3 transition-all duration-300 whitespace-nowrap text-xs font-bold overflow-hidden">
                                    {{ __($item->label) }}
                                </span>
                            </div>
                            {{-- Arrow --}}
                            <svg class="submenu-arrow w-4 h-4 ml-auto text-slate-500 opacity-0 w-0 group-hover/sidebar:opacity-100 group-hover/sidebar:w-4 transition-all duration-200 {{ $isActive ? 'rotate-180 text-cyan-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        {{-- Children List --}}
                        <div class="submenu-content pl-10 pr-2 space-y-1 mt-1 w-full {{ $isActive ? '' : 'hidden' }}">
                            @foreach ($item->children as $child)
                                @php
                                    $isChildActive = ($child->route_name && request()->routeIs($child->route_name)) ||
                                                     ($child->route_wildcard && request()->routeIs($child->route_wildcard)) ||
                                                     ($child->route_name && request()->routeIs($child->route_name . '*'));

                                    if ($isChildActive && $child->params && is_array($child->params)) {
                                        foreach ($child->params as $k => $v) {
                                            if (request()->query($k) != $v) {
                                                $isChildActive = false;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <a href="{{ $child->link }}"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-mono transition-all {{ $isChildActive ? 'bg-cyan-500/10 text-cyan-300 font-bold border-l-2 border-cyan-400' : 'text-slate-400 hover:text-white hover:bg-white/[0.03]' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isChildActive ? 'bg-cyan-400 shadow-[0_0_8px_#00f5ff]' : 'bg-slate-600' }}"></span>
                                    <span>{{ __($child->label) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item->link }}"
                        class="flex items-center justify-center group-hover/sidebar:justify-start w-12 group-hover/sidebar:w-full h-12 group-hover/sidebar:px-4 rounded-xl transition-all duration-300 border {{ $isActive ? 'border-cyan-500/40 bg-cyan-500/10 text-cyan-400 shadow-[0_0_20px_rgba(0,245,255,0.15)] font-bold' : 'border-white/[0.06] hover:border-white/20 text-slate-400 hover:text-white hover:bg-white/[0.03]' }} shrink-0">
                        <div class="w-5 h-5 flex items-center justify-center shrink-0 [&>svg]:w-5 [&>svg]:h-5 [&>svg]:stroke-[1.8] [&>svg]:fill-none">
                            {!! $item->icon !!}
                        </div>
                        <span class="sidebar-label opacity-0 w-0 group-hover/sidebar:opacity-100 group-hover/sidebar:w-auto group-hover/sidebar:ml-3 transition-all duration-300 whitespace-nowrap text-xs font-bold overflow-hidden">
                            {{ __($item->label) }}
                        </span>
                    </a>
                @endif
            @endforeach
        </nav>

        <!-- Admin Profile Footer Dock -->
        <div class="p-3 border-t border-white/[0.08] w-full shrink-0">
            <div class="flex items-center justify-center group-hover/sidebar:justify-start p-2 rounded-2xl bg-white/[0.02] border border-white/[0.06] overflow-hidden">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-cyan-500/20 to-purple-600/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 font-mono font-bold text-sm shrink-0 shadow-[0_0_15px_rgba(0,245,255,0.2)]">
                    @if (Auth::guard('admin')->user() && Auth::guard('admin')->user()->image)
                        <img src="{{ asset('storage/profile/' . Auth::guard('admin')->user()->image) }}"
                            alt="{{ Auth::guard('admin')->user()->name }}" class="w-full h-full object-cover rounded-xl">
                    @else
                        {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}
                    @endif
                </div>

                <div class="york-profile-info flex-1 min-w-0 pr-1">
                    <div class="flex items-center gap-1.5">
                        <p class="text-xs font-bold text-white truncate">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</p>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    </div>
                    <span class="text-[9px] font-mono text-cyan-400 block truncate">SUPER_ADMIN</span>
                </div>

                <a href="{{ route('admin.account.profile') }}"
                    class="hidden group-hover/sidebar:flex p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors"
                    title="{{ __('Account Settings') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- ==================================================================================== -->
    <!-- MAIN CONTENT WRAPPER -->
    <!-- ==================================================================================== -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden bg-[#050507] relative">

        @php
            $locale = app()->getLocale();
            $languages = config('languages') ?? [];
            $currentLang = $languages[$locale] ?? ['name' => strtoupper($locale), 'flag' => 'us'];
        @endphp

        <!-- Desktop Cyber-Command Top Bar -->
        <header class="hidden lg:flex h-16 items-center justify-between px-8 border-b border-white/[0.08] bg-[#090c14]/80 backdrop-blur-xl z-20 shrink-0">
            {{-- Breadcrumb & Route Telemetry --}}
            <div class="flex items-center gap-3 font-mono text-xs">
                <span class="px-2.5 py-1 rounded-full bg-white/[0.03] border border-white/[0.08] text-slate-400">
                    ADMIN_CONSOLE // <strong class="text-cyan-400">{{ strtoupper($page_title ?? 'DASHBOARD') }}</strong>
                </span>
                <span class="text-slate-600">/</span>
                <div class="flex items-center gap-2 text-emerald-400 font-bold text-[10px]">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>SYSTEM_ONLINE</span>
                </div>
            </div>

            {{-- Right Actions Dock --}}
            <div class="flex items-center gap-3">
                {{-- View Frontend Live Preview Button --}}
                <a href="{{ route('home') }}" target="_blank"
                    class="px-3.5 py-1.5 rounded-full bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.1] hover:border-cyan-400/40 text-xs font-mono text-slate-300 hover:text-white transition-all flex items-center gap-2">
                    <span class="text-cyan-400">↗</span>
                    <span>{{ __('View Site') }}</span>
                </a>

                {{-- Language Selector Dropdown --}}
                <div class="relative group">
                    <button class="flex items-center gap-2 text-xs font-mono text-slate-300 hover:text-white transition-all bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.1] rounded-full px-3.5 py-1.5 backdrop-blur-md">
                        <img src="{{ asset('assets/flags/' . ($currentLang['flag'] ?? 'us') . '.svg') }}"
                            alt="{{ $currentLang['name'] }}" class="w-3.5 h-3.5 rounded-full object-cover">
                        <span class="text-xs">{{ $currentLang['name'] }}</span>
                        <svg class="w-3 h-3 text-slate-400 group-hover:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    @if(count($languages) > 1)
                    <div class="absolute right-0 top-full mt-2 w-40 bg-[#090c14]/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right overflow-hidden p-1 z-50">
                        @foreach ($languages as $code => $lang)
                            <a href="{{ route('lang.switch', $code) }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-xs font-mono text-slate-300 hover:text-white hover:bg-white/10 rounded-xl transition-colors {{ app()->getLocale() == $code ? 'bg-cyan-500/10 text-cyan-400 font-bold' : '' }}">
                                <img src="{{ asset('assets/flags/' . ($lang['flag'] ?? 'us') . '.svg') }}"
                                    alt="{{ $lang['name'] }}" class="w-3.5 h-3.5 rounded-full object-cover">
                                {{ $lang['name'] }}
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Notification Bell Dropdown --}}
                @php
                    $unreadNotificationsCount = isset($admin_unread_notifications) ? $admin_unread_notifications->count() : 0;
                @endphp
                <div class="relative">
                    <button type="button" class="notification-btn relative w-9 h-9 flex items-center justify-center rounded-full bg-white/[0.03] border border-white/[0.1] hover:border-cyan-400/40 text-slate-300 hover:text-white transition-all cursor-pointer">
                        <span class="notification-badge absolute -top-1 -right-1 flex min-w-[16px] h-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-mono font-black text-white shadow-[0_0_10px_rgba(244,63,94,0.5)] {{ $unreadNotificationsCount > 0 ? '' : 'hidden' }}">
                            {{ $unreadNotificationsCount }}
                        </span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </button>

                    <div class="notification-dropdown hidden absolute right-0 top-full mt-2 w-80 sm:w-96 bg-[#090c14]/98 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden z-50 origin-top-right">
                        <!-- List View -->
                        <div class="notification-list-view">
                            <div class="px-4 py-3 border-b border-white/[0.06] flex items-center justify-between bg-white/[0.02]">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <h3 class="text-xs font-mono font-bold text-white tracking-wider">{{ __('NOTIFICATIONS') }}</h3>
                                </div>
                                <span class="notification-count-text text-[10px] font-mono px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                    {{ $unreadNotificationsCount }} {{ __('New') }}
                                </span>
                            </div>

                            <div class="notification-items-container max-h-80 overflow-y-auto scrollbar-thin divide-y divide-white/[0.04]">
                                @if(isset($admin_unread_notifications) && $admin_unread_notifications->count() > 0)
                                    @foreach ($admin_unread_notifications as $notification)
                                        <div class="notification-item-trigger p-3.5 hover:bg-white/[0.03] transition-colors cursor-pointer group" data-id="{{ $notification->id }}">
                                            <div class="flex items-start gap-2.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 mt-1.5 shrink-0 animate-pulse"></span>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-2 mb-1">
                                                        <div class="flex items-center gap-1.5 min-w-0">
                                                            @if ($notification->user)
                                                                <a href="{{ route('admin.users.detail', ['id' => $notification->user_id]) }}"
                                                                   class="text-xs font-mono font-bold text-cyan-400 hover:text-cyan-300 hover:underline truncate transition-colors z-10"
                                                                   onclick="event.stopPropagation();"
                                                                   title="{{ __('View user profile') }}">
                                                                    {{ '@' . ($notification->user->username ?: ($notification->user->first_name ?: 'User')) }}
                                                                </a>
                                                            @else
                                                                <span class="text-xs font-mono text-slate-500">{{ __('System') }}</span>
                                                            @endif
                                                        </div>
                                                        <span class="text-[10px] font-mono text-slate-500 shrink-0">{{ $notification->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-xs font-semibold text-white group-hover:text-cyan-300 transition-colors line-clamp-2 leading-relaxed">
                                                        {{ __($notification->title) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <div id="admin-no-notifications" class="admin-no-notifications p-6 text-center text-xs font-mono text-slate-500 {{ isset($admin_unread_notifications) && $admin_unread_notifications->count() > 0 ? 'hidden' : '' }}">
                                    <svg class="w-8 h-8 text-slate-600 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    {{ __('No unread notifications') }}
                                </div>
                            </div>
                        </div>

                        <!-- Detail Views -->
                        @if(isset($admin_unread_notifications) && $admin_unread_notifications->count() > 0)
                            @foreach ($admin_unread_notifications as $notification)
                                <div class="notification-detail-view hidden bg-[#090c14] flex flex-col h-80" data-id="{{ $notification->id }}">
                                    <div class="px-4 py-3 border-b border-white/[0.06] flex items-center justify-between shrink-0 bg-white/[0.02]">
                                        <div class="flex items-center gap-2">
                                            <button type="button" class="notification-back-btn p-1.5 -ml-1 text-slate-400 hover:text-white transition-colors hover:bg-white/10 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="m15 18-6-6 6-6" />
                                                </svg>
                                            </button>
                                            <span class="text-xs font-mono font-bold text-white uppercase">{{ __('Message Details') }}</span>
                                        </div>
                                        @if ($notification->user)
                                            <a href="{{ route('admin.users.detail', ['id' => $notification->user_id]) }}"
                                               class="text-xs font-mono text-cyan-400 hover:text-cyan-300 hover:underline flex items-center gap-1"
                                               onclick="event.stopPropagation();"
                                               title="{{ __('View user profile') }}">
                                                <span>{{ '@' . ($notification->user->username ?: ($notification->user->first_name ?: 'User')) }}</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="p-4 flex-1 overflow-y-auto">
                                        <h4 class="text-sm font-bold text-white mb-1.5 leading-snug">
                                            {{ __($notification->title) }}
                                        </h4>
                                        <div class="flex items-center justify-between gap-2 mb-3">
                                            <span class="text-[10px] font-mono text-slate-500">{{ $notification->created_at->format('M d, Y h:i A') }}</span>
                                            <span class="text-[10px] font-mono text-emerald-400/80 bg-emerald-400/10 px-2 py-0.5 rounded-full border border-emerald-400/20">{{ __('Marked as read') }}</span>
                                        </div>
                                        <div class="p-3 rounded-xl bg-white/[0.03] border border-white/[0.06] text-xs text-slate-300 leading-relaxed whitespace-pre-line font-sans">
                                            {{ __($notification->body) }}
                                        </div>
                                    </div>
                                    <div class="p-2 border-t border-white/[0.06] bg-white/[0.01] flex justify-end shrink-0">
                                        <button type="button" class="notification-back-btn px-3 py-1.5 rounded-lg bg-white/[0.05] hover:bg-white/[0.1] text-xs font-mono text-slate-300 hover:text-white transition-colors">
                                            {{ __('Back to Notifications') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Admin Profile Menu --}}
                <div class="relative group">
                    <button class="flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-full bg-white/[0.03] border border-white/[0.1] hover:border-cyan-400/40 text-white transition-all">
                        <div class="w-6 h-6 rounded-full bg-cyan-500/20 text-cyan-400 font-mono font-bold text-xs flex items-center justify-center">
                            {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="text-xs font-bold">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                        <svg class="w-3 h-3 text-slate-400 group-hover:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div class="absolute right-0 top-full mt-2 w-48 bg-[#090c14]/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right overflow-hidden p-1.5 z-50 font-mono text-xs">
                        <a href="{{ route('admin.account.profile') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-colors">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>{{ __('Profile & Security') }}</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-colors">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                            <span>{{ __('System Settings') }}</span>
                        </a>
                        <div class="h-[1px] bg-white/[0.06] my-1"></div>
                        <form action="{{ route('admin.logout') }}" method="POST" class="ajax-form" data-action="redirect" data-redirect="{{ route('admin.login') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-rose-400 hover:bg-rose-500/10 transition-colors text-left cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span>{{ __('Sign Out') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Mobile Header -->
        <header class="lg:hidden h-16 flex items-center justify-between px-4 bg-[#090c14]/90 border-b border-white/[0.08] backdrop-blur-xl z-20 shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                @if (getSetting('logo_square'))
                    <img src="{{ asset('assets/images/' . getSetting('logo_square')) }}" alt="{{ getSetting('name') }}" class="h-8 w-auto">
                @else
                    <span class="font-mono font-bold text-white text-lg">{{ substr(getSetting('name', 'F'), 0, 1) }}<span class="text-cyan-400">_ADM</span></span>
                @endif
            </a>

            <div class="flex items-center gap-2">
                <button onclick="toggleAdminMobileNav()" class="p-2 rounded-xl bg-white/[0.03] border border-white/[0.1] text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Mobile Drawer Navigation -->
        <div id="admin-mobile-drawer" class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 hidden lg:hidden flex-col">
            <div class="p-4 bg-[#090c14] border-b border-white/10 flex items-center justify-between">
                <span class="font-mono font-bold text-white text-sm">{{ __('ADMIN MENU') }}</span>
                <button onclick="toggleAdminMobileNav()" class="text-slate-400 hover:text-white text-xl">&times;</button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                @foreach ($admin_menu_items as $item)
                    @if ($item->children->isNotEmpty())
                        <div class="p-2 rounded-xl bg-white/[0.02] border border-white/[0.06]">
                            <p class="text-xs font-bold text-cyan-400 mb-2">{{ __($item->label) }}</p>
                            <div class="pl-3 space-y-1">
                                @foreach ($item->children as $child)
                                    <a href="{{ $child->link }}" class="block py-1.5 text-xs text-slate-300 font-mono">{{ __($child->label) }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->link }}" class="block p-3 rounded-xl bg-white/[0.02] border border-white/[0.06] text-xs font-bold text-white">{{ __($item->label) }}</a>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Main Workspace Area -->
        <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8 scrollbar-thin relative z-10">
            @if(!empty($platformLicenseRequired))
                <div class="mb-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 backdrop-blur-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 font-mono">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                        <div>
                            <p class="text-xs font-bold text-amber-300 uppercase tracking-wider">{{ __('Platform License Activation Required') }}</p>
                            <p class="text-[11px] text-amber-200/70">{{ __('Your installation is running in unactivated mode. Please activate your product key to unlock full platform features.') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.settings.activation') }}" class="shrink-0 px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_0_15px_rgba(251,191,36,0.3)]">
                        {{ __('Activate License') }}
                    </a>
                </div>
            @endif
            @yield('content')
        </main>

        <!-- Footer Bar -->
        <footer class="h-10 border-t border-white/[0.06] bg-[#090c14]/40 flex items-center justify-between px-8 shrink-0 font-mono text-[10px] text-slate-500 z-10">
            <span>FOYANA PHP Script</span>
            <span>© {{ date('Y') }} {{ getSetting('name', 'FOYANA') }} · ALL_RIGHTS_RESERVED</span>
        </footer>

    </div>

    <!-- Global Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.toastNotification = function(message, type = 'success') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 2000,
                    background: '#090c14',
                    color: '#fff'
                });
            } else {
                alert(message);
            }
        };

        window.copyToClipboard = function(text, successMsg = '{{ __('Copied to clipboard') }}') {
            if (!text) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: successMsg,
                            showConfirmButton: false,
                            timer: 1500,
                            background: '#090c14',
                            color: '#fff'
                        });
                    } else {
                        window.toastNotification(successMsg, 'success');
                    }
                }).catch(() => {
                    fallbackCopy(text, successMsg);
                });
            } else {
                fallbackCopy(text, successMsg);
            }

            function fallbackCopy(val, msg) {
                const textarea = document.createElement('textarea');
                textarea.value = val;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: msg,
                        showConfirmButton: false,
                        timer: 1500,
                        background: '#090c14',
                        color: '#fff'
                    });
                }
            }
        };

        // QR Code Lib Loader
        (function loadQrLib() {
            if (window.QRCode) return;
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js';
            document.head.appendChild(s);
        })();

        window.toggleWalletQr = function(panelId, canvasId, address) {
            const panel = document.getElementById(panelId);
            if (!panel) return;
            const isHidden = panel.classList.contains('hidden');
            if (isHidden) {
                panel.classList.remove('hidden');
                if (canvasId && address) {
                    const canvas = document.getElementById(canvasId);
                    if (canvas && !canvas.innerHTML.trim()) {
                        function renderQr() {
                            new QRCode(canvas, {
                                text: address,
                                width: 140,
                                height: 140,
                                colorDark: '#000000',
                                colorLight: '#ffffff',
                                correctLevel: QRCode.CorrectLevel.M,
                            });
                        }
                        if (window.QRCode) {
                            renderQr();
                        } else {
                            const timer = setInterval(() => {
                                if (window.QRCode) {
                                    clearInterval(timer);
                                    renderQr();
                                }
                            }, 50);
                        }
                    }
                }
            } else {
                panel.classList.add('hidden');
            }
        };

        window.showMasterWalletQrModal = function(networkName, address, typeLabel) {
            if (!address) return;
            const label = typeLabel ? typeLabel : '{{ __('Master Wallet') }}';
            const containerId = 'swal-qr-code-' + Math.random().toString(36).substring(2, 9);
            Swal.fire({
                title: `<span class="text-white text-sm font-black uppercase tracking-wider font-mono">${networkName} ${label}</span>`,
                html: `
                    <div class="flex flex-col items-center gap-3 py-3">
                        <div id="${containerId}" class="p-3 bg-white rounded-2xl shadow-2xl flex items-center justify-center"></div>
                        <div class="w-full bg-black/50 p-2.5 rounded-xl border border-white/10 font-mono text-xs text-cyan-300 break-all select-all text-center mt-2">
                            ${address}
                        </div>
                        <p class="text-[11px] text-slate-400 font-mono">{{ __('Scan with your mobile wallet to send funds or transaction fees') }}</p>
                    </div>
                `,
                background: '#090c14',
                color: '#fff',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonText: '{{ __('Close') }}',
                confirmButtonText: '{{ __('Copy Address') }}',
                customClass: {
                    popup: 'border border-white/10 rounded-3xl shadow-2xl font-mono',
                    confirmButton: 'px-4 py-2 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all cursor-pointer mr-2',
                    cancelButton: 'px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold uppercase text-xs tracking-wider transition-all cursor-pointer'
                },
                didOpen: () => {
                    const el = document.getElementById(containerId);
                    if (el) {
                        function renderModalQr() {
                            new QRCode(el, {
                                text: address,
                                width: 160,
                                height: 160,
                                colorDark: '#000000',
                                colorLight: '#ffffff',
                                correctLevel: QRCode.CorrectLevel.M,
                            });
                        }
                        if (window.QRCode) {
                            renderModalQr();
                        } else {
                            const timer = setInterval(() => {
                                if (window.QRCode) {
                                    clearInterval(timer);
                                    renderModalQr();
                                }
                            }, 50);
                        }
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.copyToClipboard(address, '{{ __('Address copied to clipboard') }}');
                }
            });
        };

        function toggleAdminMobileNav() {
            const drawer = document.getElementById('admin-mobile-drawer');
            drawer.classList.toggle('hidden');
            drawer.classList.toggle('flex');
        }

        // Submenu accordion handler for sidebar dock
        document.querySelectorAll('.submenu-toggle').forEach(button => {
            button.addEventListener('click', function(e) {
                const container = this.closest('.submenu-container');
                const content = container.querySelector('.submenu-content');
                const arrow = this.querySelector('.submenu-arrow');
                
                const isHidden = content.classList.contains('hidden');
                
                if (isHidden) {
                    content.classList.remove('hidden');
                    if (arrow) arrow.classList.add('rotate-180');
                    container.setAttribute('data-expanded', 'true');
                } else {
                    content.classList.add('hidden');
                    if (arrow) arrow.classList.remove('rotate-180');
                    container.setAttribute('data-expanded', 'false');
                }
            });
        });

        // Notification Dropdown Toggle
        $(document).on('click', '.notification-btn', function(e) {
            e.stopPropagation();
            var dropdown = $(this).siblings('.notification-dropdown');
            var isAlreadyOpen = !dropdown.hasClass('hidden');

            $('.notification-dropdown').addClass('hidden');
            $('.notification-detail-view').addClass('hidden');
            $('.notification-list-view').removeClass('hidden');

            if (!isAlreadyOpen) {
                dropdown.removeClass('hidden');
            }
        });

        // Prevent closing when clicking inside dropdown
        $(document).on('click', '.notification-dropdown', function(e) {
            e.stopPropagation();
        });

        // Close dropdown when clicking outside
        $(document).on('click', function() {
            $('.notification-dropdown').addClass('hidden');
        });

        // Switch to Detail View & Mark as Read (Filtering out for admin)
        $(document).on('click', '.notification-item-trigger', function(e) {
            var id = $(this).data('id');
            var container = $(this).closest('.notification-dropdown');
            var $item = $(this);

            // Hide list view, show detail view
            container.find('.notification-list-view').addClass('hidden');
            container.find('.notification-detail-view[data-id="' + id + '"]').removeClass('hidden');

            // Send AJAX to mark as read
            $.ajax({
                url: "{{ route('admin.notifications.seen') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        // Filter the notification out for admin by removing the item from the list view
                        $item.remove();
                        $('.notification-item-trigger[data-id="' + id + '"]').remove();

                        // Update remaining count
                        var remainingCount = container.find('.notification-item-trigger').length;
                        $('.notification-count-text').text(remainingCount + ' {{ __('New') }}');

                        // Update bell badges
                        if (remainingCount > 0) {
                            $('.notification-badge').text(remainingCount).removeClass('hidden');
                        } else {
                            $('.notification-badge').text(0).addClass('hidden');
                            $('.admin-no-notifications, #admin-no-notifications').removeClass('hidden');
                        }
                    }
                }
            });
        });

        // Back to List View
        $(document).on('click', '.notification-back-btn', function(e) {
            e.stopPropagation();
            var container = $(this).closest('.notification-dropdown');

            container.find('.notification-detail-view').addClass('hidden');
            container.find('.notification-list-view').removeClass('hidden');
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
