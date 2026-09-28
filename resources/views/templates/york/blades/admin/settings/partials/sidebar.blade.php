@php
    $settingGroups = [
        'System' => [
            ['title' => 'Legal & Terms', 'route' => 'admin.settings.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['title' => 'General Settings', 'route' => 'admin.settings.core', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
            ['title' => 'System & Server', 'route' => 'admin.settings.system', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['title' => 'Email Settings', 'route' => 'admin.settings.email', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ['title' => 'Cron Jobs', 'route' => 'admin.settings.cronjob', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['title' => 'License & Updates', 'route' => 'admin.settings.activation', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
            ['title' => 'Audit Logs', 'route' => 'admin.settings.audit', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ],
        'Financial' => [
            ['title' => 'Trading & Limits', 'route' => 'admin.settings.financial', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['title' => 'Blockchain Settings', 'route' => 'admin.settings.blockchain.index', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
            ['title' => 'Bonus Settings', 'route' => 'admin.settings.bonus-system', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
            ['title' => 'Certificates', 'route' => 'admin.settings.certificate', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ],
        'Security & Access' => [
            ['title' => 'Security & 2FA', 'route' => 'admin.settings.security', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['title' => 'Social Logins', 'route' => 'admin.settings.login-method', 'icon' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1'],
            ['title' => 'Feature Modules', 'route' => 'admin.settings.modules.index', 'icon' => 'M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z'],
            ['title' => 'Utilities', 'route' => 'admin.settings.utility', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
        ],
        'Content' => [
            ['title' => 'Menu Links', 'route' => 'admin.settings.menu', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
            ['title' => 'SEO Settings', 'route' => 'admin.settings.seo', 'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
            ['title' => 'Live Chat', 'route' => 'admin.settings.livechat', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
            ['title' => 'Team Members', 'route' => 'admin.settings.management-team.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
            ['title' => 'FAQs', 'route' => 'admin.settings.faq.index', 'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['title' => 'Reviews', 'route' => 'admin.settings.reviews.index', 'icon' => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z'],
        ],
    ];
@endphp

<div class="space-y-6 font-mono text-xs" id="sideBarSelector">
    @foreach ($settingGroups as $groupName => $items)
        <div class="space-y-1">
            <div class="px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] text-slate-500">
                {{ str_replace('_', ' ', $groupName) }}
            </div>
            @foreach ($items as $item)
                @php
                    $url = Route::has($item['route']) ? route($item['route']) : '#';
                    $isActive = request()->routeIs($item['route']) || (str_contains($item['route'], 'blockchain') && request()->routeIs('admin.settings.blockchain.*'));
                @endphp
                <a href="{{ $url }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all group {{ $isActive ? 'bg-cyan-500/10 border border-cyan-500/20 text-white shadow-[0_0_15px_rgba(0,245,255,0.1)]' : 'border border-transparent text-slate-400 hover:bg-white/[0.03] hover:text-white' }}">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $isActive ? 'bg-cyan-400 text-black shadow-[0_0_10px_rgba(0,245,255,0.3)]' : 'bg-white/5 text-slate-400 group-hover:bg-white/10 group-hover:text-white' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold truncate">
                        {{ __($item['title']) }}
                    </span>
                    @if ($isActive)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    @endif
                </a>
            @endforeach
        </div>
    @endforeach
</div>
