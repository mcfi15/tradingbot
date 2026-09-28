<div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl font-mono">
    <div class="rounded-[2rem] bg-[#090c14]/95 p-3 space-y-1.5">
        <a href="{{ route('admin.account.profile') }}"
            class="flex items-center gap-3.5 px-4 py-3.5 rounded-2xl transition-all duration-300 {{ request()->routeIs('admin.account.profile') ? 'bg-cyan-500/10 border border-cyan-500/30 text-white shadow-[0_0_20px_rgba(0,245,255,0.15)]' : 'bg-transparent border border-transparent text-slate-400 hover:bg-white/[0.03] hover:text-white' }}">
            <div class="w-9 h-9 rounded-xl {{ request()->routeIs('admin.account.profile') ? 'bg-cyan-400 text-black shadow-[0_0_15px_rgba(0,245,255,0.4)]' : 'bg-white/5 text-slate-400' }} flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-black uppercase tracking-wider {{ request()->routeIs('admin.account.profile') ? 'text-white' : 'text-slate-300' }}">{{ __('Profile Info') }}</p>
                <p class="text-[9px] text-slate-500 uppercase tracking-widest mt-0.5 truncate">{{ __('Personal Details & Language') }}</p>
            </div>
        </a>

        <a href="{{ route('admin.account.security') }}"
            class="flex items-center gap-3.5 px-4 py-3.5 rounded-2xl transition-all duration-300 {{ request()->routeIs('admin.account.security') ? 'bg-cyan-500/10 border border-cyan-500/30 text-white shadow-[0_0_20px_rgba(0,245,255,0.15)]' : 'bg-transparent border border-transparent text-slate-400 hover:bg-white/[0.03] hover:text-white' }}">
            <div class="w-9 h-9 rounded-xl {{ request()->routeIs('admin.account.security') ? 'bg-cyan-400 text-black shadow-[0_0_15px_rgba(0,245,255,0.4)]' : 'bg-white/5 text-slate-400' }} flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-black uppercase tracking-wider {{ request()->routeIs('admin.account.security') ? 'text-white' : 'text-slate-300' }}">{{ __('Password & Security') }}</p>
                <p class="text-[9px] text-slate-500 uppercase tracking-widest mt-0.5 truncate">{{ __('Password & Two-Factor Auth') }}</p>
            </div>
        </a>
    </div>
</div>
