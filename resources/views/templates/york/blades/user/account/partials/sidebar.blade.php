{{-- Top Segmented Navigation Deck for Account Settings --}}
<div class="inline-flex items-center gap-2 p-1.5 bg-white/[0.03] border border-white/10 rounded-full backdrop-blur-2xl shadow-xl">
    <a href="{{ route('user.account.profile') }}"
       class="flex items-center gap-2.5 px-6 py-3 rounded-full text-xs font-black uppercase tracking-wider transition-all duration-300 {{ request()->routeIs('user.account.profile') ? 'bg-accent-primary/20 border border-accent-primary/40 text-white shadow-[0_0_20px_rgba(226,177,60,0.2)]' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
        <svg class="w-4 h-4 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <span>{{ __('Profile') }}</span>
    </a>

    <a href="{{ route('user.account.security') }}"
       class="flex items-center gap-2.5 px-6 py-3 rounded-full text-xs font-black uppercase tracking-wider transition-all duration-300 {{ request()->routeIs('user.account.security') ? 'bg-accent-primary/20 border border-accent-primary/40 text-white shadow-[0_0_20px_rgba(226,177,60,0.2)]' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
        <svg class="w-4 h-4 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
        <span>{{ __('Security') }}</span>
    </a>
</div>
