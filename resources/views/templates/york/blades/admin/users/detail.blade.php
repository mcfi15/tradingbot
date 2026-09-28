@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12">

        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.index') }}"
                    class="w-11 h-11 rounded-2xl bg-white/[0.03] border border-white/[0.08] hover:border-cyan-400/40 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer shadow-lg group">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.2em] mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        {{ __('USER PROFILE') }}
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                        <span>{{ $user->fullname ?? $user->username }}</span>
                        <span class="text-xs font-mono font-normal text-slate-400 px-2 py-0.5 rounded-lg bg-white/[0.04] border border-white/[0.06]">
                            ID: #{{ $user->id }}
                        </span>
                    </h1>
                    <p class="text-slate-400 font-mono text-xs mt-0.5 tracking-wider">
                        {{ '@' . $user->username }} · <span class="text-cyan-300">{{ $user->email }}</span>
                    </p>
                </div>
            </div>

            {{-- Status Pill & Quick Action Bar --}}
            <div class="flex items-center gap-3 font-mono">
                @if ($user->status == 'active')
                    <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold uppercase tracking-wider shadow-[0_0_15px_rgba(16,185,129,0.15)]">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>{{ __('Active Account') }}</span>
                    </div>
                @else
                    <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold uppercase tracking-wider shadow-[0_0_15px_rgba(244,63,94,0.15)]">
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                        <span>{{ __('Banned') }}</span>
                    </div>
                @endif

                <button type="button" id="detail-login-as-btn"
                    class="px-4 py-2 rounded-2xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 text-emerald-300 hover:text-white text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer shadow-[0_0_15px_rgba(16,185,129,0.15)]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    <span>{{ __('Login as User') }}</span>
                </button>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- FINANCIAL OVERVIEW --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Master Identity & Net Equity Card (8 Cols) --}}
            <div class="lg:col-span-8 p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 flex flex-col justify-between h-full relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-white/[0.06]">
                        <div class="flex items-center gap-5">
                            @if ($user->photo)
                                <div class="w-20 h-20 rounded-3xl border border-white/10 shadow-2xl overflow-hidden aspect-square shrink-0">
                                    <img src="{{ asset('storage/profile/' . $user->photo) }}" alt="{{ $user->username }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-20 h-20 rounded-3xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-2xl font-black font-mono text-cyan-400 shrink-0 shadow-lg">
                                    {{ strtoupper(substr($user->username ?? 'U', 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                                        {{ $user->fullname ?? $user->username }}
                                    </h3>
                                    @if ($user->email_verified_at)
                                        <span title="{{ __('Email Verified') }}" class="text-cyan-400">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400 font-mono mt-1">
                                    {{ __('Joined') }}: <span class="text-slate-200">{{ $user->created_at->format('M d, Y H:i') }} UTC</span>
                                </p>
                                <div class="flex flex-wrap gap-2 mt-3 font-mono text-[10px]">
                                    @php $kyc = $user->kyc->first(); @endphp
                                    <span class="px-2.5 py-1 rounded-lg {{ $kyc && $kyc->status === 'approved' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-white/[0.04] text-slate-400 border-white/[0.08]' }} border font-bold uppercase tracking-wider">
                                        KYC: {{ $kyc ? ucfirst($kyc->status) : 'Unverified' }}
                                    </span>
                                    <span class="px-2.5 py-1 rounded-lg bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold uppercase tracking-wider">
                                        2FA: {{ $user->ts ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Total Balance Value --}}
                        <div class="sm:text-right font-mono">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block mb-1">
                                {{ __('Available Balance') }}
                            </span>
                            <div class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                                {{ showAmount($balance) }}
                            </div>
                            <span class="text-[10px] text-cyan-400 font-mono block mt-1">
                                {{ __('Total balance') }}
                            </span>
                        </div>
                    </div>

                    {{-- Financial Details --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 font-mono">
                        <div>
                            <span class="text-[10px] uppercase tracking-widest text-slate-500 block mb-1">{{ __('Total Balance') }}</span>
                            <span class="text-base font-bold text-white">{{ showAmount($total_equity) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase tracking-widest text-slate-500 block mb-1">{{ __('Referred By') }}</span>
                            @if ($user->referrer)
                                <a href="{{ route('admin.users.detail', $user->referrer->id) }}" class="text-xs font-bold text-cyan-400 hover:underline">
                                    {{ '@' . $user->referrer->username }}
                                </a>
                            @else
                                <span class="text-xs text-slate-400">{{ __('None (Direct Signup)') }}</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] uppercase tracking-widest text-slate-500 block mb-1">{{ __('Referred Users') }}</span>
                            <span class="text-base font-bold text-white">{{ $user->referrals->count() }} <span class="text-[10px] text-slate-500 font-normal">{{ __('users') }}</span></span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase tracking-widest text-slate-500 block mb-1">{{ __('Last Login') }}</span>
                            <span class="text-xs text-slate-300">{{ $last_login ? $last_login->diffForHumans() : __('Never') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Monthly Cash Movement Card (4 Cols) --}}
            <div class="lg:col-span-4 p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between pb-4 border-b border-white/[0.06]">
                        <div>
                            <h4 class="text-sm font-bold text-white font-mono uppercase tracking-wide">{{ now()->format('F') }} {{ __('Activity') }}</h4>
                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ __('Deposits and withdrawals this month') }}</p>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-4 my-4 font-mono">
                        {{-- Approved Deposits --}}
                        <div class="p-3.5 rounded-2xl bg-emerald-500/5 border border-emerald-500/15 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-emerald-400/80 block">{{ __('Deposits') }}</span>
                                <span class="text-lg font-black text-emerald-400">+{{ showAmount($deposits_month) }}</span>
                            </div>
                            @if ($deposits_pending > 0)
                                <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-bold">
                                    {{ $deposits_pending }} {{ __('Pending') }}
                                </span>
                            @endif
                        </div>

                        {{-- Approved Withdrawals --}}
                        <div class="p-3.5 rounded-2xl bg-rose-500/5 border border-rose-500/15 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-rose-400/80 block">{{ __('Withdrawals') }}</span>
                                <span class="text-lg font-black text-rose-400">-{{ showAmount($withdrawals_month_amount) }}</span>
                            </div>
                            @if ($withdrawals_pending > 0)
                                <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-bold">
                                    {{ $withdrawals_pending }} {{ __('Pending') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-3 border-t border-white/[0.06] flex items-center justify-between text-xs font-mono">
                        <a href="{{ route('admin.deposits.index', ['search' => $user->username]) }}" class="text-cyan-400 hover:underline flex items-center gap-1 text-[11px]">
                            <span>{{ __('View Deposits') }}</span> →
                        </a>
                        <a href="{{ route('admin.withdrawals.index', ['search' => $user->username]) }}" class="text-rose-400 hover:underline flex items-center gap-1 text-[11px]">
                            <span>{{ __('View Withdrawals') }}</span> →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- QUICK ACTIONS TOOLBAR --}}
        {{-- ==================================================================================== --}}
        <div class="p-2 rounded-[2rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-xl">
            <div class="rounded-[1.6rem] bg-[#090c14]/90 p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 font-mono text-xs">
                    {{-- Credit/Debit Balance --}}
                    <button type="button" id="open-credit-debit-btn"
                        class="p-3 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/25 text-cyan-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ __('Add / Deduct Balance') }}</span>
                    </button>

                    {{-- Send Direct Email --}}
                    <button type="button" id="open-send-email-btn"
                        class="p-3 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/25 text-amber-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>{{ __('Send Email') }}</span>
                    </button>

                    {{-- Ban / Unban --}}
                    @if ($user->status == 'active')
                        <button type="button" class="quick-status-btn p-3 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/25 text-rose-400 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer" data-status="banned">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                            <span>{{ __('Ban User') }}</span>
                        </button>
                    @else
                        <button type="button" class="quick-status-btn p-3 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/25 text-emerald-400 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer" data-status="active">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ __('Unban User') }}</span>
                        </button>
                    @endif

                    {{-- Transactions Shortcut --}}
                    <a href="{{ route('admin.transactions.index', ['search' => $user->username]) }}"
                        class="p-3 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/25 text-purple-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>{{ __('Transactions') }}</span>
                    </a>

                    {{-- Referrals Shortcut --}}
                    <a href="{{ route('admin.referrals.index', ['search' => $user->username]) }}"
                        class="p-3 rounded-xl bg-pink-500/10 hover:bg-pink-500/20 border border-pink-500/25 text-pink-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>{{ __('Referrals') }}</span>
                    </a>

                    {{-- Delete User --}}
                    <button type="button" id="delete-user-btn"
                        class="p-3 rounded-xl bg-white/[0.03] hover:bg-rose-500/20 border border-white/[0.08] hover:border-rose-500/30 text-slate-400 hover:text-rose-300 font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>{{ __('Delete User') }}</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- USER PROFILE EDIT & SECURITY SETTINGS --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Personal Information (8 Cols) --}}
            <div class="lg:col-span-8 p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)]">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8">
                    <div class="flex items-center justify-between pb-4 mb-6 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Edit User Details') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('Update personal details, contact info, and login security.') }}</p>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>

                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6 font-mono text-xs">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('First Name') }}</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Last Name') }}</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Email Address') }}</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Phone Number') }}</label>
                                <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}"
                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all">
                            </div>
                        </div>

                        {{-- Address / Location Fields --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Country') }}</label>
                                <input type="text" name="country" value="{{ old('country', $kyc->country ?? '') }}"
                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('City') }}</label>
                                <input type="text" name="city" value="{{ old('city', $kyc->city ?? '') }}"
                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Street Address') }}</label>
                            <input type="text" name="address" value="{{ old('address', $kyc->address_line_1 ?? '') }}"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all">
                        </div>

                        {{-- Security Flags Switches --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-white/[0.02] border border-white/[0.08] hover:border-cyan-400/40 transition-all cursor-pointer">
                                <input type="checkbox" name="email_verified" value="1" {{ $user->email_verified_at ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-white/20 bg-white/5 text-cyan-400 focus:ring-cyan-400 accent-cyan-400 cursor-pointer">
                                <div>
                                    <span class="text-xs font-bold text-white block">{{ __('Email Verified') }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ __('User does not need to verify email') }}</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-white/[0.02] border border-white/[0.08] hover:border-cyan-400/40 transition-all cursor-pointer">
                                <input type="checkbox" name="two_factor" value="1" {{ $user->ts ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-white/20 bg-white/5 text-cyan-400 focus:ring-cyan-400 accent-cyan-400 cursor-pointer">
                                <div>
                                    <span class="text-xs font-bold text-white block">{{ __('Two-Factor Authentication (2FA)') }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ __('Require 2FA code at login') }}</span>
                                </div>
                            </label>
                        </div>

                        <div class="pt-4 border-t border-white/[0.06] flex justify-end">
                            <button type="submit"
                                class="px-8 py-3 rounded-full bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(0,245,255,0.3)] transition-all cursor-pointer">
                                {{ __('Save Changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Security & Password Management (4 Cols) --}}
            <div class="lg:col-span-4 space-y-6">
                {{-- Reset Password Card --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)]">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6">
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-white/[0.06]">
                            <div>
                                <h3 class="text-base font-bold text-white font-mono uppercase tracking-wide">{{ __('Change Password') }}</h3>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ __('Set a new password for this user.') }}</p>
                            </div>
                            <div class="w-8 h-8 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                        </div>

                        <form action="{{ route('admin.users.password', $user->id) }}" method="POST" class="space-y-4 font-mono text-xs">
                            @csrf
                            <div>
                                <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('New Password') }}</label>
                                <input type="password" name="password" required placeholder="••••••••••••"
                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation" required placeholder="••••••••••••"
                                    class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all">
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full py-2.5 rounded-full bg-purple-500/20 hover:bg-purple-500 text-purple-300 hover:text-white font-bold uppercase tracking-wider border border-purple-500/30 transition-all cursor-pointer">
                                    {{ __('Update Password') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Security Summary Card --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)]">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 font-mono text-xs space-y-3">
                        <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                            <span class="text-slate-400 uppercase text-[10px]">{{ __('Account Status') }}</span>
                            <span class="font-bold {{ $user->status == 'active' ? 'text-emerald-400' : 'text-rose-400' }}">{{ ucfirst($user->status) }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                            <span class="text-slate-400 uppercase text-[10px]">{{ __('Two-Factor Auth') }}</span>
                            <span class="font-bold {{ $user->ts ? 'text-emerald-400' : 'text-slate-500' }}">{{ $user->ts ? 'Active' : 'Disabled' }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                            <span class="text-slate-400 uppercase text-[10px]">{{ __('Referral Code') }}</span>
                            <span class="font-bold text-cyan-300">{{ $user->referral_code ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 uppercase text-[10px]">{{ __('User ID') }}</span>
                            <span class="font-bold text-white">#{{ $user->id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- KYC VERIFICATION DOCUMENTS VIEWER --}}
        {{-- ==================================================================================== --}}
        @if ($kyc)
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)]">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8">
                    <div class="flex items-center justify-between pb-4 mb-6 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Identity (KYC) Documents') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('Uploaded identity documents and status.') }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider
                            @if ($kyc->status == 'approved') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                            @elseif($kyc->status == 'pending') bg-purple-500/10 text-purple-400 border border-purple-500/20
                            @else bg-rose-500/10 text-rose-400 border border-rose-500/20 @endif">
                            {{ $kyc->status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 font-mono">
                        @php
                            $docs = [
                                'document_front' => __('Front of ID'),
                                'document_back' => __('Back of ID'),
                                'selfie' => __('Selfie Photo'),
                                'proof_address' => __('Proof of Address'),
                            ];
                        @endphp
                        @foreach ($docs as $field => $label)
                            @if ($kyc->$field)
                                <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.08] hover:border-cyan-400/40 transition-all flex flex-col justify-between">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">{{ $label }}</span>
                                        <div class="w-full h-36 rounded-xl bg-black/40 border border-white/[0.06] overflow-hidden flex items-center justify-center mb-3">
                                            <img src="{{ asset('storage/kyc/' . $kyc->$field) }}" alt="{{ $label }}" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/kyc/' . $kyc->$field) }}" target="_blank"
                                        class="w-full py-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500 text-cyan-300 hover:text-black font-bold text-[10px] uppercase tracking-wider transition-all text-center">
                                        {{ __('View Full Size') }}
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- ==================================================================================== --}}
    {{-- MODALS --}}
    {{-- ==================================================================================== --}}

    {{-- 1. Credit / Debit Balance Modal --}}
    <div id="credit-debit-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-[#05070d]/80 backdrop-blur-xl transition-opacity modal-close cursor-pointer"></div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.9)] relative w-full max-w-md transform transition-all text-left">
                <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Adjust Balance') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('Add funds to or deduct funds from this user\'s balance.') }}</p>
                        </div>
                        <button type="button" class="w-8 h-8 rounded-full bg-white/[0.04] text-slate-400 hover:text-white transition-colors modal-close flex items-center justify-center cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <form id="credit-debit-form" class="space-y-4 font-mono text-xs">
                        @csrf
                        <div>
                            <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Action') }}</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center justify-center gap-2 p-3 rounded-xl border border-emerald-500/20 bg-emerald-500/5 text-emerald-400 font-bold uppercase cursor-pointer hover:bg-emerald-500/10 transition-all">
                                    <input type="radio" name="type" value="credit" checked class="accent-emerald-400">
                                    <span>{{ __('+ Add Funds (Credit)') }}</span>
                                </label>
                                <label class="flex items-center justify-center gap-2 p-3 rounded-xl border border-rose-500/20 bg-rose-500/5 text-rose-400 font-bold uppercase cursor-pointer hover:bg-rose-500/10 transition-all">
                                    <input type="radio" name="type" value="debit" class="accent-rose-400">
                                    <span>{{ __('— Deduct Funds (Debit)') }}</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Amount') }} ({{ getSetting('currency') }})</label>
                            <input type="number" step="any" name="amount" required placeholder="0.00"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all font-mono">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Reason / Note') }}</label>
                            <input type="text" name="description" placeholder="{{ __('e.g. Manual correction, deposit bonus...') }}"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all">
                        </div>

                        <div class="pt-4 border-t border-white/[0.06] flex gap-3">
                            <button type="button" class="flex-1 px-4 py-2.5 rounded-full border border-white/[0.1] text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-white/[0.05] transition-all modal-close cursor-pointer">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="flex-1 px-4 py-2.5 rounded-full bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(0,245,255,0.3)] transition-all cursor-pointer">
                                {{ __('Confirm Adjustment') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Send Direct Email Modal --}}
    <div id="detail-send-email-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-[#05070d]/80 backdrop-blur-xl transition-opacity modal-close cursor-pointer"></div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.9)] relative w-full max-w-lg transform transition-all text-left">
                <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Send Email to User') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('To') }}: {{ $user->fullname ?? $user->username }} ({{ $user->email }})</p>
                        </div>
                        <button type="button" class="w-8 h-8 rounded-full bg-white/[0.04] text-slate-400 hover:text-white transition-colors modal-close flex items-center justify-center cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <form id="detail-email-form" class="space-y-4 font-mono text-xs">
                        @csrf
                        <div>
                            <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Subject') }}</label>
                            <input type="text" name="subject" required placeholder="{{ __('Account Notice / Update...') }}"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Message') }}</label>
                            <textarea name="message" rows="6" required placeholder="{{ __('Type your message here...') }}"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all resize-none"></textarea>
                        </div>

                        <div class="pt-4 border-t border-white/[0.06] flex gap-3">
                            <button type="button" class="flex-1 px-4 py-2.5 rounded-full border border-white/[0.1] text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-white/[0.05] transition-all modal-close cursor-pointer">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="flex-1 px-4 py-2.5 rounded-full bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(0,245,255,0.3)] transition-all cursor-pointer">
                                {{ __('Send Email') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            function openModal(id) {
                const $modal = $(`#${id}`);
                $modal.removeClass('hidden');
                setTimeout(() => {
                    $modal.find('.transform').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
                }, 10);
            }

            function closeModal(id) {
                const $modal = $(`#${id}`);
                $modal.find('.transform').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
                setTimeout(() => {
                    $modal.addClass('hidden');
                }, 200);
            }

            $('.modal-close').on('click', function() {
                const modalId = $(this).closest('.fixed.inset-0.z-\\[100\\]').attr('id');
                closeModal(modalId);
            });

            // Credit/Debit Modal
            $('#open-credit-debit-btn').on('click', function() {
                openModal('credit-debit-modal');
            });

            $('#credit-debit-form').on('submit', function(e) {
                e.preventDefault();
                const $btn = $(this).find('button[type="submit"]');
                const orig = $btn.text();
                $btn.text('{{ __('Processing...') }}').prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.users.credit-debit', $user->id) }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            closeModal('credit-debit-modal');
                            Swal.fire({toast:true, position:'top-end', icon:'success', title: res.message || 'Balance updated', showConfirmButton:false, timer:2500});
                            setTimeout(() => { location.reload(); }, 1200);
                        } else {
                            Swal.fire({toast:true, position:'top-end', icon:'error', title: res.message || 'Error occurred', showConfirmButton:false, timer:2500});
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'An error occurred';
                        Swal.fire({toast:true, position:'top-end', icon:'error', title: msg, showConfirmButton:false, timer:2500});
                    },
                    complete: function() {
                        $btn.text(orig).prop('disabled', false);
                    }
                });
            });

            // Send Email Modal
            $('#open-send-email-btn').on('click', function() {
                openModal('detail-send-email-modal');
            });

            $('#detail-email-form').on('submit', function(e) {
                e.preventDefault();
                const $btn = $(this).find('button[type="submit"]');
                const orig = $btn.text();
                $btn.text('{{ __('Sending...') }}').prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.users.email', $user->id) }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            closeModal('detail-send-email-modal');
                            Swal.fire({toast:true, position:'top-end', icon:'success', title: res.message || 'Email sent successfully', showConfirmButton:false, timer:2500});
                            $('#detail-email-form')[0].reset();
                        } else {
                            Swal.fire({toast:true, position:'top-end', icon:'error', title: res.message || 'Error occurred', showConfirmButton:false, timer:2500});
                        }
                    },
                    error: function() {
                        Swal.fire({toast:true, position:'top-end', icon:'error', title: 'An error occurred', showConfirmButton:false, timer:2500});
                    },
                    complete: function() {
                        $btn.text(orig).prop('disabled', false);
                    }
                });
            });

            // Quick Status Ban / Unban
            $('.quick-status-btn').on('click', function() {
                const status = $(this).data('status');
                const title = status === 'banned' ? '{{ __('Ban this user?') }}' : '{{ __('Unban this user?') }}';
                
                Swal.fire({
                    title: title,
                    text: '{{ __('Confirm account status update for') }} {{ $user->username }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: status === 'banned' ? '#f43f5e' : '#10b981',
                    cancelButtonColor: '#334155',
                    confirmButtonText: '{{ __('Yes, Update') }}',
                    customClass: {
                        popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono',
                        title: 'text-white font-mono',
                        htmlContainer: 'text-slate-400 font-mono',
                    }
                }).then((res) => {
                    if (res.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.users.status', $user->id) }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: status
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({toast:true, position:'top-end', icon:'success', title: response.message || 'Status updated', showConfirmButton:false, timer:2000});
                                    setTimeout(() => { location.reload(); }, 1000);
                                }
                            }
                        });
                    }
                });
            });

            // Detail Login As User (POST)
            $('#detail-login-as-btn').on('click', function() {
                Swal.fire({
                    title: '{{ __('Login as User?') }}',
                    text: '{{ __('You will be authenticated as') }} {{ $user->fullname ?? $user->username }} {{ __('and redirected to their dashboard.') }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#334155',
                    confirmButtonText: '{{ __('Yes, Login') }}',
                    customClass: {
                        popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono',
                        title: 'text-white font-mono',
                        htmlContainer: 'text-slate-400 font-mono',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.users.login-as', $user->id) }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                if (res.success && res.redirect_url) {
                                    window.open(res.redirect_url, '_blank');
                                } else {
                                    Swal.fire({toast:true, position:'top-end', icon:'error', title: res.message || 'Error occurred', showConfirmButton:false, timer:2500});
                                }
                            },
                            error: function() {
                                Swal.fire({toast:true, position:'top-end', icon:'error', title: 'An error occurred', showConfirmButton:false, timer:2500});
                            }
                        });
                    }
                });
            });

            // Delete User
            $('#delete-user-btn').on('click', function() {
                Swal.fire({
                    title: '{{ __('Delete User?') }}',
                    text: '{{ __('Are you sure you want to permanently delete this user account? This action cannot be undone.') }}',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#f43f5e',
                    cancelButtonColor: '#334155',
                    confirmButtonText: '{{ __('Yes, Delete User') }}',
                    customClass: {
                        popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono',
                        title: 'text-white font-mono',
                        htmlContainer: 'text-slate-400 font-mono',
                    }
                }).then((res) => {
                    if (res.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.users.delete', $user->id) }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({icon:'success', title: 'Deleted', text: response.message || 'User deleted'}).then(() => {
                                        window.location.href = "{{ route('admin.users.index') }}";
                                    });
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
