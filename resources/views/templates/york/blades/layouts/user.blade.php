<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ config('languages.' . app()->getLocale() . '.rtl') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page_title ?? 'User' }} | {{ getSetting('name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/' . getSetting('favicon')) }}">
    {{-- indexing --}}

    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0f172a">
    <meta name="author" content="{{ getSetting('name') }} Team">
    <meta name="publisher" content="{{ getSetting('name') }} Team">
    @if (config('site.use_vite'))
        @vite(['resources/views/templates/york/css/app.css'])
    @else
        <link rel="stylesheet"
            href="{{ asset('assets/templates/york/css/main.css' . '?v=' . filemtime(public_path('assets/templates/york/css/main.css'))) }}">
    @endif
    {{-- custom scrollbar design --}}
    <style>
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(0, 245, 255, 0.2);
            /* cyan with low opacity */
            border-radius: 10px;
            transition: background 0.3s ease;
            cursor: pointer;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 245, 255, 0.5);
            /* brighter on hover */
        }

        /* Firefox support */
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
            padding-left: 2.5rem !important;
        }

        /* 6. Text Labels visibility */
        .sidebar-label {
            opacity: 0;
            width: 0;
            display: none;
            transition: opacity 0.3s ease;
        }
        .york-sidebar:hover .sidebar-label {
            opacity: 1;
            width: auto;
            display: inline-block;
            margin-left: 0.75rem !important;
        }

        /* 7. Submenu arrow visibility */
        .submenu-arrow {
            opacity: 0;
            width: 0;
            display: none;
            transition: opacity 0.3s ease;
        }
        .york-sidebar:hover .submenu-arrow {
            opacity: 1;
            width: auto;
            display: inline-block;
        }

        /* 8. User profile footer panel layout */
        .york-profile-btn {
            justify-content: center !important;
            transition: all 0.3s ease !important;
        }
        .york-sidebar:hover .york-profile-btn {
            justify-content: flex-start !important;
            padding-left: 0.75rem !important;
        }
        .york-profile-details {
            opacity: 0;
            width: 0;
            display: none;
            transition: opacity 0.3s ease;
        }
        .york-sidebar:hover .york-profile-details {
            opacity: 1;
            width: auto;
            display: block;
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

<body
    class="bg-primary-dark text-text-primary font-body antialiased min-h-screen flex selection:bg-accent-primary selection:text-white overflow-hidden">

    {{-- Dashboard Preloader --}}
    @include('templates.york.blades.partials.dashoard-preloader')

    <!-- Sidebar DOCK -->
    <aside
        class="hidden lg:flex flex-col w-20 hover:w-72 bg-gradient-to-b from-[#0a0a0c] to-[#040404] border-r border-white/[0.08] h-screen sticky top-0 z-30 transition-all duration-300 items-center hover:items-stretch py-6 group/sidebar york-sidebar">
        <!-- Logo Area -->
        <div class="h-16 flex items-center px-4 mb-8 shrink-0 w-full overflow-hidden relative">
            <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 w-full max-w-full">
                {{-- Collapsed State Logo (Square) --}}
                <div class="flex items-center justify-center shrink-0 group-hover/sidebar:hidden transition-all duration-300 w-10 h-10">
                    @if (getSetting('logo_square'))
                        <img src="{{ asset('assets/images/' . getSetting('logo_square')) }}"
                            alt="{{ getSetting('name') }}"
                            class="max-w-[40px] max-h-[40px] w-auto h-auto object-contain rounded-xl">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent-primary to-accent-secondary flex items-center justify-center text-white font-bold text-lg shadow-inner shrink-0">
                            {{ substr(getSetting('name'), 0, 1) }}
                        </div>
                    @endif
                </div>

                {{-- Expanded State Logo (Rectangle) --}}
                <div class="hidden group-hover/sidebar:flex items-center shrink-0 transition-all duration-300 max-w-[180px] overflow-hidden">
                    @if (getSetting('logo_rectangle'))
                        <img src="{{ asset('assets/images/' . getSetting('logo_rectangle')) }}"
                            alt="{{ getSetting('name') }}"
                            class="max-w-[170px] max-h-[44px] w-auto h-auto object-contain">
                    @elseif (getSetting('logo_square'))
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('assets/images/' . getSetting('logo_square')) }}"
                                alt="{{ getSetting('name') }}"
                                class="max-w-[36px] max-h-[36px] w-auto h-auto object-contain rounded-xl shrink-0">
                            <span class="text-white font-heading font-black text-xl tracking-tight whitespace-nowrap truncate">
                                {{ getSetting('name') }}<span class="text-accent-primary">.</span>
                            </span>
                        </div>
                    @else
                        <span class="text-white font-heading font-black text-xl tracking-tight whitespace-nowrap truncate">
                            {{ getSetting('name') }}<span class="text-accent-primary">.</span>
                        </span>
                    @endif
                </div>
            </a>
        </div>

        <!-- Navigation DOCK items -->
        <nav class="flex-1 w-full px-2 hover:px-4 flex flex-col items-center hover:items-stretch gap-4 overflow-y-auto scrollbar-hide york-nav">
            @foreach ($user_menu_items as $item)
                @php
                    // Determine active state for parent or any child
                    $isActive = ($item->route_name && request()->routeIs($item->route_name)) ||
                                ($item->route_wildcard && request()->routeIs($item->route_wildcard)) ||
                                request()->url() == $item->url ||
                                $item->children->contains(fn($c) => ($c->route_name && request()->routeIs($c->route_name)));
                    
                    // Target link
                    $targetLink = $item->children->isNotEmpty() ? $item->children->first()->link : $item->link;
                @endphp
                
                @if ($item->children->isNotEmpty())
                    <div class="submenu-container w-full flex flex-col items-center group-hover/sidebar:items-stretch relative shrink-0" data-expanded="{{ $isActive ? 'true' : 'false' }}">
                        <button class="submenu-toggle flex items-center justify-center group-hover/sidebar:justify-start w-12 group-hover/sidebar:w-full h-12 group-hover/sidebar:px-4 rounded-xl transition-all duration-300 border border-white/[0.06] hover:border-white/20 text-text-secondary hover:text-white hover:bg-white/[0.02] cursor-pointer z-10 relative">
                            <div class="flex items-center shrink-0">
                                {{-- Icon --}}
                                <div class="w-5 h-5 flex items-center justify-center shrink-0 [&>svg]:w-5 [&>svg]:h-5 [&>svg]:stroke-[1.8] [&>svg]:fill-none">
                                    {!! $item->icon !!}
                                </div>
                                {{-- Label --}}
                                <span class="sidebar-label opacity-0 w-0 group-hover/sidebar:opacity-100 group-hover/sidebar:w-auto group-hover/sidebar:ml-3 transition-all duration-300 whitespace-nowrap text-sm font-semibold overflow-hidden">
                                    {{ __($item->label) }}
                                </span>
                            </div>
                            {{-- Arrow --}}
                            <svg class="submenu-arrow w-4 h-4 ml-auto text-slate-500 opacity-0 w-0 group-hover/sidebar:opacity-100 group-hover/sidebar:w-4 transition-all duration-200 {{ $isActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        {{-- Children List --}}
                        <div class="submenu-content pl-12 pr-2 space-y-1 mt-1 w-full {{ $isActive ? '' : 'hidden' }}">
                            @foreach ($item->children as $child)
                                @php
                                    $isChildActive = ($child->route_name && request()->routeIs($child->route_name)) ||
                                                     ($child->route_name && request()->routeIs($child->route_name . '*'));
                                @endphp
                                <a href="{{ $child->link }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs transition-all duration-200 group/child relative
                                    {{ $isChildActive ? 'text-white font-bold bg-white/5' : 'text-text-secondary hover:text-white hover:bg-white/[0.02]' }}">
                                    <span class="w-1.5 h-1.5 rounded-full ring-2 ring-primary-dark transition-all duration-300
                                        {{ $isChildActive ? 'bg-accent-primary shadow-[0_0_8px_rgba(var(--color-accent-primary),0.6)]' : 'bg-white/20 group-hover/child:bg-white/50' }}"></span>
                                    <span class="sidebar-label opacity-0 w-0 group-hover/sidebar:opacity-100 group-hover/sidebar:w-auto transition-all duration-300 whitespace-nowrap overflow-hidden">
                                        {{ __($child->label) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Single menu item --}}
                    <div class="w-full flex justify-center group-hover/sidebar:justify-start shrink-0 relative">
                        <a href="{{ $item->link }}"
                            class="flex items-center justify-center group-hover/sidebar:justify-start w-12 group-hover/sidebar:w-full h-12 group-hover/sidebar:px-4 rounded-xl transition-all duration-300 border
                            {{ $isActive ? 'bg-accent-primary/10 border-accent-primary/30 text-accent-primary shadow-[0_0_15px_rgba(var(--color-accent-primary),0.15)]' : 'bg-white/[0.02] border-white/[0.06] text-text-secondary hover:text-white hover:border-white/20' }}">
                            {{-- Render icon --}}
                            <div class="w-5 h-5 flex items-center justify-center shrink-0 [&>svg]:w-5 [&>svg]:h-5 [&>svg]:stroke-[1.8] [&>svg]:fill-none">
                                {!! $item->icon !!}
                            </div>
                            {{-- Label --}}
                            <span class="sidebar-label opacity-0 w-0 group-hover/sidebar:opacity-100 group-hover/sidebar:w-auto group-hover/sidebar:ml-3 transition-all duration-300 whitespace-nowrap text-sm font-semibold overflow-hidden">
                                {{ __($item->label) }}
                            </span>
                        </a>

                        {{-- Tooltip (Only visible when sidebar NOT hovered) --}}
                        <div class="absolute left-full top-1/2 -translate-y-1/2 ml-3 px-3 py-1.5 rounded-lg bg-black border border-white/[0.08] text-white text-xs font-semibold whitespace-nowrap shadow-2xl opacity-0 scale-95 pointer-events-none group-hover:opacity-100 group-hover:scale-100 group-hover:pointer-events-auto group-hover/sidebar:hidden transition-all duration-200 z-50">
                            {{ __($item->label) }}
                            <div class="absolute right-full top-1/2 -translate-y-1/2 border-8 border-transparent border-r-black"></div>
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>

        <!-- User Profile Simplified Footer -->
        <div class="mt-auto pt-4 border-t border-white/[0.08] w-full shrink-0 px-3">
            <div class="relative w-full">
                <button onclick="toggleDropdown('desktop-profile-menu')" class="w-full flex items-center p-2 rounded-xl bg-white/[0.02] border border-white/[0.06] hover:border-white/20 transition-all cursor-pointer overflow-hidden york-profile-btn">
                    <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-accent-primary to-accent-secondary flex items-center justify-center text-white font-bold text-sm shadow-inner shrink-0">
                        {{ substr(Auth::user()->first_name, 0, 1) }}
                    </div>
                    <div class="text-left ml-3 opacity-0 w-0 group-hover/sidebar:opacity-100 group-hover/sidebar:w-auto transition-all duration-300 whitespace-nowrap overflow-hidden york-profile-details">
                        <p class="text-xs font-bold text-white truncate max-w-[120px]">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                        <p class="text-[9px] text-text-secondary truncate max-w-[120px]">{{ Auth::user()->email }}</p>
                    </div>
                </button>
                <div id="desktop-profile-menu"
                    class="hidden absolute bottom-full left-0 mb-3 w-48 bg-secondary-dark/95 backdrop-blur-xl border border-white/10 rounded-xl shadow-2xl overflow-hidden z-50">
                    <div class="p-1">
                        <div class="px-3 py-2 border-b border-white/5 mb-1">
                            <p class="text-sm font-bold text-white truncate">{{ Auth::user()->first_name }}
                                {{ Auth::user()->last_name }}</p>
                            <p class="text-xs text-text-secondary truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('user.account.profile') }}"
                            class="flex items-center gap-3 px-3 py-2 text-sm text-text-secondary hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            {{ __('Profile Settings') }}
                        </a>
                        <a href="{{ route('user.account.security') }}"
                            class="flex items-center gap-3 px-3 py-2 text-sm text-text-secondary hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2"
                                    ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            {{ __('Security Settings') }}
                        </a>
                        <div class="my-1 border-t border-white/5"></div>
                        <form action="{{ route('user.logout') }}" method="POST" class="ajax-form"
                            data-action="redirect" data-redirect="{{ route('home') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-3 py-2 text-sm text-accent-error hover:bg-accent-error/10 rounded-lg transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                    <polyline points="16 17 21 12 16 7" />
                                    <line x1="21" x2="9" y1="12" y2="12" />
                                </svg>
                                {{ __('Logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden bg-primary-dark relative">

        @php
            $locale = app()->getLocale();
            $languages = config('languages');
            $currentLang = $languages[$locale] ?? ['name' => $locale, 'flag' => 'us'];
        @endphp

        <!-- Mobile Header -->
        <header
            class="lg:hidden h-16 flex items-center justify-between px-4 bg-secondary-dark backdrop-blur-md border-b border-border-primary z-20 gap-3">
            <!-- Logo -->
            <a href="{{ route('user.dashboard') }}" class="block shrink-0 max-w-[160px] overflow-hidden">
                @if (getSetting('logo_rectangle'))
                    <img src="{{ asset('assets/images/' . getSetting('logo_rectangle')) }}"
                        alt="{{ getSetting('name') }}" class="max-w-[150px] max-h-[36px] w-auto h-auto object-contain">
                @elseif (getSetting('logo_square'))
                    <img src="{{ asset('assets/images/' . getSetting('logo_square')) }}"
                        alt="{{ getSetting('name') }}" class="max-w-[36px] max-h-[36px] w-auto h-auto object-contain rounded-lg">
                @else
                    <h1 class="font-heading text-xl font-bold text-white">
                        {{ substr(getSetting('name'), 0, 1) }}<span class="text-accent-primary">.</span>
                    </h1>
                @endif
            </a>

            <!-- Right Actions -->
            <div class="flex items-center gap-3">

                <!-- Language Switcher -->
                <div class="relative">
                    <button onclick="toggleDropdown('mobile-header-lang-menu')"
                        class="flex items-center justify-center w-8 h-8 rounded-full bg-white/[0.02] border border-border-primary hover:border-accent-primary/30 transition-colors cursor-pointer">
                        <img src="{{ asset('assets/flags/' . $currentLang['flag'] . '.svg') }}"
                            class="w-4 h-4 rounded-full object-cover">
                    </button>
                    <div id="mobile-header-lang-menu"
                        class="hidden absolute top-full right-0 mt-2 w-40 bg-[#0c0c0f]/95 backdrop-blur-xl border border-white/5 rounded-xl shadow-2xl overflow-hidden z-50">
                        <div class="p-1">
                            @foreach ($languages as $code => $lang)
                               <a href="{{ route('lang.switch', $code) }}"
                                    class="flex items-center gap-3 px-3 py-2 text-sm text-text-secondary hover:text-white hover:bg-white/[0.02] rounded-lg transition-colors {{ app()->getLocale() == $code ? 'bg-accent-primary/10 text-accent-primary' : '' }}">
                                    <img src="{{ asset('assets/flags/' . $lang['flag'] . '.svg') }}"
                                        alt="{{ $lang['name'] }}" class="w-4 h-4 rounded-full object-cover">
                                    {{ $lang['name'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                @if ($unread_notification_messages->count() > 0)
                    <div class="relative">
                        <button
                            class="notification-btn relative w-8 h-8 flex items-center justify-center rounded-full bg-white/[0.02] border border-border-primary text-text-secondary hover:text-white transition-colors cursor-pointer">
                            <span
                                class="absolute -top-1 -right-0.5 flex min-w-[16px] h-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white ring-2 ring-primary-dark">
                                {{ $unread_notification_messages->count() }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                            </svg>
                        </button>

                        <!-- Mobile Notification Dropdown -->
                        <div
                            class="notification-dropdown hidden absolute right-0 top-full mt-2 w-72 bg-[#0c0c0f]/95 backdrop-blur-xl border border-white/5 rounded-xl shadow-2xl overflow-hidden z-50 origin-top-right">
                            <!-- List View -->
                            <div class="notification-list-view">
                                <div
                                    class="px-4 py-3 border-b border-white/[0.03] flex items-center justify-between bg-white/[0.02]">
                                    <h3 class="text-sm font-bold text-white">{{ __('Notifications') }}</h3>
                                    <span
                                        class="text-xs text-text-secondary">{{ $unread_notification_messages->count() }}
                                        {{ __('New') }}</span>
                                </div>
                                <div class="max-h-64 overflow-y-auto scrollbar-thin scrollbar-thumb-white/5">
                                    @foreach ($unread_notification_messages as $notification)
                                        <div class="notification-item-trigger cursor-pointer p-3 border-b border-white/[0.03] hover:bg-white/[0.02] transition-colors group"
                                            data-id="{{ $notification->id }}">
                                            <div class="flex flex-col gap-1">
                                                <p
                                                    class="text-xs font-bold text-white group-hover:text-accent-primary transition-colors flex items-start gap-2">
                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full bg-accent-primary mt-1 shrink-0"></span>
                                                    {{ __($notification->title) }}
                                                </p>
                                                <p class="text-[10px] text-text-secondary pl-3.5">
                                                    {{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Detail Views -->
                            @foreach ($unread_notification_messages as $notification)
                                <div class="notification-detail-view hidden bg-[#0c0c0f]/95"
                                    data-id="{{ $notification->id }}">
                                    <div class="px-4 py-3 border-b border-white/[0.03] flex items-center gap-3 bg-white/[0.02]">
                                        <button
                                            class="notification-back-btn p-1 -ml-1 text-text-secondary hover:text-white transition-colors hover:bg-white/[0.02] rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m15 18-6-6 6-6" />
                                            </svg>
                                        </button>
                                        <h3 class="text-sm font-bold text-white truncate max-w-[200px]">
                                            {{ __($notification->title) }}</h3>
                                    </div>
                                    <div class="p-4">
                                        <p class="text-xs text-text-secondary leading-relaxed whitespace-pre-line">
                                            {{ __($notification->body) }}</p>
                                        <p
                                            class="text-[10px] text-text-secondary/60 mt-4 pt-4 border-t border-white/[0.03]">
                                            {{ __('Received') }}:
                                            {{ $notification->created_at->format('M d, Y h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Profile -->
                <div class="relative">
                    <button onclick="toggleDropdown('mobile-header-profile-menu')"
                        class="w-8 h-8 rounded-full bg-gradient-to-br from-accent-primary to-accent-secondary flex items-center justify-center text-white font-bold text-xs shadow-inner ring-2 ring-transparent hover:ring-white/5 transition-all">
                        {{ substr(Auth::user()->first_name, 0, 1) }}
                    </button>
                    <div id="mobile-header-profile-menu"
                        class="hidden absolute top-full right-0 mt-2 w-48 bg-[#0c0c0f]/95 backdrop-blur-xl border border-white/5 rounded-xl shadow-2xl overflow-hidden z-50">
                        <div class="p-1">
                            <div class="px-3 py-2 border-b border-white/[0.03] mb-1">
                                <p class="text-sm font-bold text-white truncate">{{ Auth::user()->first_name }}
                                    {{ Auth::user()->last_name }}</p>
                                <p class="text-xs text-text-secondary truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('user.account.profile') }}"
                                class="flex items-center gap-3 px-3 py-2 text-sm text-text-secondary hover:text-white hover:bg-white/[0.02] rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                {{ __('Profile Settings') }}
                            </a>
                            <a href="{{ route('user.account.security') }}"
                                class="flex items-center gap-3 px-3 py-2 text-sm text-text-secondary hover:text-white hover:bg-white/[0.02] rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2"
                                        ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                {{ __('Security Settings') }}
                            </a>
                            <div class="my-1 border-t border-white/[0.03]"></div>
                            <form action="{{ route('user.logout') }}" method="POST" class="ajax-form"
                                data-action="redirect" data-redirect="{{ route('home') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2 text-sm text-accent-error hover:bg-accent-error/10 rounded-lg transition-colors cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                        <polyline points="16 17 21 12 16 7" />
                                        <line x1="21" x2="9" y1="12" y2="12" />
                                    </svg>
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Hamburger -->
                <button id="mobile-menu-toggle"
                    class="p-1.5 text-text-secondary hover:text-white transition-colors bg-white/[0.02] rounded-lg border border-border-primary">
                    <svg class="icon" viewBox="0 0 32 32" width="20" height="20">
                        <rect x="6" y="9" width="20" height="2" rx="1" fill="currentColor" />
                        <rect x="10" y="15" width="16" height="2" rx="1" fill="currentColor" />
                        <rect x="6" y="21" width="12" height="2" rx="1" fill="currentColor" />
                    </svg>
                </button>
            </div>
        </header>

        <!-- Top Bar (Desktop) -->
        <header
            class="hidden lg:flex h-20 items-center justify-between px-8 border-b border-border-primary bg-primary-dark/80 backdrop-blur-md z-20">
            <div>
                <h2 class="text-2xl font-bold text-white font-heading tracking-tight">
                    {{ $page_title ?? __('Dashboard') }}
                </h2>
                <p class="text-sm text-text-secondary mt-1">
                    {{ __('Welcome back, :name', ['name' => Auth::user()->first_name]) }} 👋
                </p>
            </div>





            <div class="flex items-center gap-6">
                <!-- Language Switcher -->
                <div class="relative group" id="desktop-lang-switcher">
                    <button
                        class="flex items-center gap-2 px-3 py-2 rounded-lg border border-border-primary hover:bg-white/[0.03] bg-white/[0.01] transition-all duration-300">
                        <img src="{{ asset('assets/flags/' . $currentLang['flag'] . '.svg') }}"
                            alt="{{ $currentLang['name'] }}" class="w-4 h-4 rounded-full object-cover">
                        <span
                            class="text-sm font-medium text-text-secondary group-hover:text-white transition-colors">{{ $currentLang['name'] }}</span>
                        <svg class="w-3 h-3 text-text-secondary" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <div
                        class="absolute right-0 top-full mt-2 w-40 bg-[#0c0c0f]/95 backdrop-blur-xl border border-white/5 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right overflow-hidden z-50">
                        <div class="p-1">
                            @foreach ($languages as $code => $lang)
                                <a href="{{ route('lang.switch', $code) }}"
                                    class="flex items-center gap-3 px-3 py-2 text-sm text-text-secondary hover:text-white hover:bg-white/[0.02] rounded-lg transition-colors {{ app()->getLocale() == $code ? 'bg-accent-primary/10 text-accent-primary' : '' }}">
                                    <img src="{{ asset('assets/flags/' . $lang['flag'] . '.svg') }}"
                                        alt="{{ $lang['name'] }}" class="w-4 h-4 rounded-full object-cover">
                                    {{ $lang['name'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                @if ($unread_notification_messages->count() > 0)
                    <div class="relative">
                        <button
                            class="notification-btn relative p-2 text-text-secondary hover:text-white transition-colors group">
                            <span
                                class="absolute top-0 right-0 flex min-w-[16px] h-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-[#030303] transform translate-x-1/4 -translate-y-1/4">
                                {{ $unread_notification_messages->count() }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="transition-transform group-hover:scale-110">
                                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                            </svg>
                        </button>

                        <!-- Desktop Notification Dropdown -->
                        <div
                            class="notification-dropdown hidden absolute right-0 top-full mt-2 w-80 bg-[#0c0c0f]/95 backdrop-blur-xl border border-white/5 rounded-xl shadow-2xl overflow-hidden z-50 origin-top-right">
                            <!-- List View -->
                            <div class="notification-list-view">
                                <div
                                    class="px-5 py-3 border-b border-white/[0.03] flex items-center justify-between bg-white/[0.02]">
                                    <h3 class="text-sm font-bold text-white">{{ __('Notifications') }}</h3>
                                    <span
                                        class="text-xs text-text-secondary bg-white/[0.02] px-2 py-0.5 rounded-full border border-white/[0.03]">{{ $unread_notification_messages->count() }}
                                        {{ __('Total') }}</span>
                                </div>
                                <div class="max-h-80 overflow-y-auto scrollbar-thin scrollbar-thumb-white/5">
                                    @foreach ($unread_notification_messages as $notification)
                                        <div class="notification-item-trigger cursor-pointer px-5 py-4 border-b border-white/[0.03] hover:bg-white/[0.02] transition-colors group"
                                            data-id="{{ $notification->id }}">
                                            <div class="flex flex-col gap-1">
                                                <p
                                                    class="text-sm font-bold text-white group-hover:text-accent-primary transition-colors flex items-start gap-3">
                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full bg-accent-primary mt-1.5 shrink-0 animate-pulse"></span>
                                                    {{ __($notification->title) }}
                                                </p>
                                                <p class="text-[10px] font-mono text-text-secondary pl-4.5">
                                                    {{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="p-2 bg-white/[0.02] border-t border-white/[0.03] text-center">
                                    <span
                                        class="text-[10px] text-text-secondary italic">{{ __('Click to see summary') }}</span>
                                </div>
                            </div>

                            <!-- Detail Views -->
                            @foreach ($unread_notification_messages as $notification)
                                <div class="notification-detail-view hidden bg-[#0c0c0f]/95 w-full h-80 flex flex-col"
                                    data-id="{{ $notification->id }}">
                                    <div
                                        class="px-4 py-3 border-b border-white/[0.03] flex items-center gap-3 bg-white/[0.02] shrink-0">
                                        <button
                                            class="notification-back-btn p-1.5 -ml-1 text-text-secondary hover:text-white transition-colors hover:bg-white/[0.02] rounded-lg border border-transparent hover:border-white/[0.03]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m15 18-6-6 6-6" />
                                            </svg>
                                        </button>
                                        <span
                                            class="text-xs font-bold text-text-secondary uppercase tracking-wider">{{ __('Message Details') }}</span>
                                    </div>
                                    <div class="p-5 flex-1 overflow-y-auto">
                                        <h3 class="text-base font-heading font-bold text-white mb-2">
                                            {{ __($notification->title) }}</h3>
                                        <p class="text-[11px] font-mono text-accent-primary mb-4">
                                            {{ $notification->created_at->format('M d, Y h:i A') }}</p>
                                        <div
                                            class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.03] text-sm text-text-secondary leading-relaxed">
                                            {{ __($notification->body) }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- User Profile Dropdown -->
                <div class="relative group" id="user-profile-dropdown">
                    <button
                        class="flex items-center gap-3 pl-3 pr-2 py-1.5 rounded-full border border-border-primary hover:bg-white/[0.03] bg-white/[0.01] transition-all duration-300 group-hover:border-accent-primary/30">
                        <div class="flex flex-col items-end mr-1 hidden sm:flex">
                            <span
                                class="text-xs font-bold text-white leading-tight">{{ Auth::user()->first_name }}</span>
                            <span class="text-[10px] text-text-secondary leading-tight">{{ __('User') }}</span>
                        </div>
                        <div
                            class="h-8 w-8 rounded-full bg-gradient-to-br from-accent-primary to-accent-secondary flex items-center justify-center text-white font-bold text-xs shadow-inner">
                            {{ substr(Auth::user()->first_name, 0, 1) }}
                        </div>
                        <svg class="w-3 h-3 text-text-secondary" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <div
                        class="absolute right-0 top-full mt-2 w-48 bg-[#0c0c0f]/95 backdrop-blur-xl border border-white/5 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right overflow-hidden z-50">
                        <div class="p-1">
                            <div class="px-3 py-2 border-b border-white/5 mb-1">
                                <p class="text-sm font-bold text-white truncate">{{ Auth::user()->first_name }}
                                    {{ Auth::user()->last_name }}</p>
                                <p class="text-xs text-text-secondary truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('user.account.profile') }}"
                                class="flex items-center gap-3 px-3 py-2 text-sm text-text-secondary hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                {{ __('Profile Settings') }}
                            </a>
                            <a href="{{ route('user.account.security') }}"
                                class="flex items-center gap-3 px-3 py-2 text-sm text-text-secondary hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2"
                                        ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                {{ __('Security Settings') }}
                            </a>

                            <div class="my-1 border-t border-white/5"></div>

                            <form action="{{ route('user.logout') }}" method="POST" class="ajax-form"
                                data-action="redirect" data-redirect="{{ route('home') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2 text-sm text-accent-error hover:bg-accent-error/10 rounded-lg transition-colors cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                        <polyline points="16 17 21 12 16 7" />
                                        <line x1="21" x2="9" y1="12" y2="12" />
                                    </svg>
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- import onboarding.blade.php if the user has not completed the onboarding process --}}
        @if (!Auth::user()->onboarding)
            @include('templates.york.blades.partials.onboarding')
        @endif

        {{-- background elements --}}
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-accent-primary/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-accent-secondary/5 rounded-full blur-3xl"></div>
        </div>

        <!-- Main Content Area -->
        <main
            class="flex-1 overflow-y-auto p-4 lg:p-8 scrollbar-thin scrollbar-thumb-white/5 scrollbar-track-transparent">
            <div class="max-w-7xl mx-auto w-full min-h-full flex flex-col">
                <div class="space-y-8 flex-1 pb-10">
                    @yield('content')
                </div>

                <!-- Copyright Footer -->
                <div class="pt-8 border-t border-border-primary text-center mt-auto">
                    <p class="text-xs text-text-secondary opacity-60">
                        &copy; {{ date('Y') }} {{ getSetting('name') }}. {{ __('All rights reserved.') }}
                    </p>
                </div>
            </div>
        </main>

    </div>

    <!-- Mobile Sidebar Overlay & Menu -->
    <div id="mobile-menu-overlay"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity opacity-0"></div>

    <div id="mobile-sidebar"
        class="fixed inset-y-0 left-0 w-72 bg-secondary-dark/95 backdrop-blur-xl z-50 transform -translate-x-full transition-transform duration-300 lg:hidden flex flex-col border-r border-border-primary">
        <div class="h-16 flex items-center justify-between px-6 border-b border-white/[0.03]">
            <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                @if (getSetting('logo_rectangle'))
                    <img src="{{ asset('assets/images/' . getSetting('logo_rectangle')) }}"
                        alt="{{ getSetting('name') }}"
                        class="h-6 w-auto transition-transform duration-300 group-hover:scale-105">
                @else
                    <h1 class="font-heading text-xl font-bold text-white tracking-tight">
                        {{ getSetting('name') }}<span class="text-accent-primary">.</span>
                    </h1>
                @endif
            </a>
            <button id="mobile-menu-close" class="text-text-secondary hover:text-accent-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            @foreach ($user_menu_items as $item)
                @if ($item->children->isNotEmpty())
                    {{-- Mobile Menu item with sub-menu --}}
                    @php
                        $isActive =
                            ($item->route_name && request()->routeIs($item->route_name)) ||
                            ($item->url && request()->url() == $item->url) ||
                            $item->children->contains(function ($child) {
                                $cActive = $child->route_name && request()->routeIs($child->route_name);
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
                    <div class="mb-1 submenu-container relative" data-expanded="{{ $isActive ? 'true' : 'false' }}">
                        <button
                            class="submenu-toggle flex w-full items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-colors z-10 relative
                            {{ $isActive ? 'text-white bg-white/5 shadow-inner' : 'text-text-secondary hover:text-white hover:bg-white/[0.02]' }}">
                            <div class="flex items-center gap-3">
                                {!! $item->icon !!}
                                <span>{{ __($item->label) }}</span>
                            </div>
                            <svg class="submenu-arrow w-4 h-4 transition-transform duration-200 {{ $isActive ? 'rotate-180' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        {{-- Connector Line --}}
                        <div
                            class="absolute left-[2.2rem] top-10 bottom-4 w-[1px] bg-white/[0.03] submenu-line {{ $isActive ? '' : 'hidden' }}">
                        </div>

                        <div class="submenu-content pl-12 pr-2 space-y-1 mt-1 {{ $isActive ? '' : 'hidden' }}"
                            style="{{ $isActive ? 'display: block;' : 'display: none;' }}">
                            @foreach ($item->children as $child)
                                @php
                                    $isChildActive = $child->route_name && request()->routeIs($child->route_name);

                                    if ($isChildActive && $child->params && is_array($child->params)) {
                                        foreach ($child->params as $key => $value) {
                                            if (request()->query($key) != $value) {
                                                $isChildActive = false;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <a href="{{ $child->link }}"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 group/child
                                    {{ $isChildActive ? 'text-white font-medium bg-white/5' : 'text-text-secondary hover:text-white hover:bg-white/[0.02]' }}">

                                    {{-- Dot Indicator --}}
                                    <span
                                        class="w-1.5 h-1.5 rounded-full ring-2 ring-[#030303] transition-all duration-300
                                        {{ $isChildActive ? 'bg-accent-primary scale-110 shadow-[0_0_8px_rgba(var(--color-accent-primary),0.6)]' : 'bg-white/20 group-hover/child:bg-white/50' }}"></span>

                                    <span>{{ __($child->label) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Mobile Single menu item --}}
                    @php
                        $isSingleActive =
                            ($item->route_name && request()->routeIs($item->route_name)) ||
                            request()->url() == $item->url;
                        if ($isSingleActive && $item->params && is_array($item->params)) {
                            foreach ($item->params as $key => $value) {
                                if (request()->query($key) != $value) {
                                    $isSingleActive = false;
                                    break;
                                }
                            }
                        }
                    @endphp
                    <a href="{{ $item->link }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium mb-1 {{ $isSingleActive ? 'bg-accent-primary text-white' : 'text-text-secondary hover:text-white hover:bg-white/[0.02]' }}">
                        {!! $item->icon !!}
                        <span>{{ __($item->label) }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="p-4 border-t border-border-primary space-y-4">
            <!-- Mobile Language Switcher (Dropdown Style) -->
            <div class="relative group" id="mobile-lang-switcher">
                <button onclick="document.getElementById('mobile-lang-menu').classList.toggle('hidden')"
                    class="flex w-full items-center justify-between px-4 py-3 rounded-xl bg-white/[0.02] hover:bg-white/[0.04] text-text-secondary hover:text-white transition-colors">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/flags/' . $currentLang['flag'] . '.svg') }}"
                            alt="{{ $currentLang['name'] }}" class="w-5 h-5 rounded-full object-cover">
                        <span class="text-sm font-medium">{{ $currentLang['name'] }}</span>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>

                <div id="mobile-lang-menu"
                    class="hidden mt-2 bg-black/40 rounded-xl overflow-hidden border border-border-primary shadow-xl">
                    @foreach ($languages as $code => $lang)
                        <a href="{{ route('lang.switch', $code) }}"
                            class="flex items-center gap-3 px-4 py-3 text-sm text-text-secondary hover:text-white hover:bg-white/[0.02] transition-colors {{ app()->getLocale() == $code ? 'bg-accent-primary/10 text-accent-primary' : '' }}">
                            <img src="{{ asset('assets/flags/' . $lang['flag'] . '.svg') }}"
                                alt="{{ $lang['name'] }}" class="w-5 h-5 rounded-full object-cover">
                            {{ $lang['name'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <form action="{{ route('user.logout') }}" method="POST" class="ajax-form" data-action="redirect"
                data-redirect="{{ route('home') }}">
                @csrf
                <button type="submit"
                    class="flex w-full items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-500/10 text-red-500 hover:bg-red-500/20 transition-colors cursor-pointer border border-red-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" x2="9" y1="12" y2="12" />
                    </svg>
                    <span>{{ __('Logout') }}</span>
                </button>
            </form>
        </div>

        </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (config('site.use_vite'))
        @vite(['resources/views/templates/york/js/app.js'])
    @else
        <script
            src="{{ asset('assets/templates/york/js/main.js' . '?v=' . filemtime(public_path('assets/templates/york/js/main.js'))) }}">
        </script>
    @endif

    {{-- clock --}}
    <script>
        $(document).ready(function() {
            // Toggle Notification Dropdown
            $('.notification-btn').click(function(e) {
                e.stopPropagation();
                var dropdown = $(this).siblings('.notification-dropdown');
                var isAlreadyOpen = !dropdown.hasClass('hidden');

                // Close all other dropdowns first
                $('.notification-dropdown').addClass('hidden');
                $('.notification-detail-view').addClass('hidden');
                $('.notification-list-view').removeClass('hidden');

                // If it wasn't open, open it now (if it was open, the code above just closed it, which is what we want)
                if (!isAlreadyOpen) {
                    dropdown.removeClass('hidden');
                }
            });

            // Prevent closing when clicking inside content
            $('.notification-dropdown').click(function(e) {
                e.stopPropagation();
            });

            // Switch to Detail View & Mark as Read
            $('.notification-item-trigger').click(function() {
                var id = $(this).data('id');
                var container = $(this).closest('.notification-dropdown');
                var $this = $(this);

                // Hide list, show detail
                container.find('.notification-list-view').addClass('hidden');
                container.find('.notification-detail-view[data-id="' + id + '"]').removeClass('hidden');

                // Mark as read via AJAX if not already marked (optional check, but good for UI state)
                if (!$this.hasClass('read-marked')) {
                    $.ajax({
                        url: "{{ route('user.notification-mark-as-read') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            notification_id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                $this.addClass('read-marked');
                                // Optional: Decrement counter locally
                                // Find closest counter
                                var btn = container.siblings('.notification-btn');
                                var badge = btn.find('span.bg-red-500');
                                var count = parseInt(badge.text().trim());
                                if (count > 0) {
                                    count--;
                                    badge.text(count);
                                    // Update inner count
                                    container.find('.text-xs.font-bold + span').text(count +
                                        ' {{ __('New') }}');
                                    // If 0, maybe hide badge?
                                    if (count === 0) {
                                        badge.addClass('hidden');
                                        // Optional: Hide entire button if requirement was to hide bell on 0
                                        // btn.parent().addClass('hidden'); 
                                    }
                                }
                                // Remove 'New' visual indicator from list item
                                $this.find('.rounded-full.bg-accent-primary').removeClass(
                                    'bg-accent-primary').addClass('bg-transparent');
                                // Darken Title
                                $this.find('p.font-bold').removeClass('text-white').addClass(
                                    'text-text-secondary');
                            }
                        }
                    });
                }
            });

            // Back to List View
            $('.notification-back-btn').click(function() {
                var container = $(this).closest('.notification-dropdown');

                // Hide detail, show list
                container.find('.notification-detail-view').addClass('hidden');
                container.find('.notification-list-view').removeClass('hidden');
            });

            // Close everything on outside click
            $(document).click(function() {
                $('.notification-dropdown').addClass('hidden');
            });
            // Mobile Menu Toggle
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileOverlay = document.getElementById('mobile-menu-overlay');
            const mobileMenuClose = document.getElementById('mobile-menu-close');

            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    mobileOverlay.classList.remove('hidden', 'opacity-0');
                    mobileOverlay.classList.add('opacity-100');
                    mobileSidebar.classList.remove('-translate-x-full');
                });
            }

            function closeMobileMenu() {
                mobileOverlay.classList.remove('opacity-100');
                mobileOverlay.classList.add('opacity-0');
                setTimeout(() => {
                    mobileOverlay.classList.add('hidden');
                }, 300);
                mobileSidebar.classList.add('-translate-x-full');
            }

            if (mobileMenuClose) {
                mobileMenuClose.addEventListener('click', closeMobileMenu);
            }
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', closeMobileMenu);
            }

            // Sidebar Submenu Toggle
            $('.submenu-toggle').click(function(e) {
                e.preventDefault();
                var $this = $(this);
                var $container = $this.closest('.submenu-container');
                var $content = $container.find('.submenu-content');
                var $arrow = $this.find('.submenu-arrow');
                var isExpanded = $container.attr('data-expanded') === 'true';

                if (isExpanded) {
                    $content.slideUp(200, function() {
                        $(this).addClass('hidden');
                    });
                    $container.find('.submenu-line').addClass('hidden');
                    $arrow.removeClass('rotate-180');
                    $container.attr('data-expanded', 'false');
                } else {
                    $content.removeClass('hidden').slideDown(200);
                    $container.find('.submenu-line').removeClass('hidden');
                    $arrow.addClass('rotate-180');
                    $container.attr('data-expanded', 'true');
                }
            });
        });

        // Existing Clock Logic...
        function toggleDropdown(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.toggle('hidden');
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = ['mobile-header-lang-menu', 'mobile-header-profile-menu'];
            dropdowns.forEach(id => {
                const el = document.getElementById(id);
                const btn = el ? el.previousElementSibling : null;
                if (el && !el.classList.contains('hidden') && !el.contains(event.target) && !btn.contains(
                        event.target)) {
                    el.classList.add('hidden');
                }
            });
        });


    </script>


    {{-- Page Specific Scripts --}}
    @yield('scripts')

    @stack('scripts')

    {{-- Notification --}}
    @include('templates.york.blades.partials.notifications')






    {!! Blade::render(getSetting('livechat_scripts')) !!}
    {!! getSetting('footer_scripts') !!}
</body>

</html>
