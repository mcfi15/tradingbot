@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12">

        {{-- Top Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-3 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                    {{ __('OVERVIEW') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Admin Dashboard') }}
                </h1>
                <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Track your users, balances, and recent platform activity in real time.') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/[0.03] border border-white/[0.08] text-[11px] text-slate-300 font-mono">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>{{ \Carbon\Carbon::now()->format('l, F j, Y · H:i:s') }} UTC</span>
                </div>
            </div>
        </div>

        {{-- Master Wallet Warnings Alert Deck --}}
        @if (!empty($master_wallet_warnings))
            <div class="space-y-3">
                @foreach ($master_wallet_warnings as $warning)
                    <div class="p-1 rounded-[2rem] bg-rose-500/5 border border-rose-500/20 backdrop-blur-xl group hover:border-rose-500/40 transition-all duration-300 relative overflow-hidden">
                        <div class="rounded-[1.75rem] bg-[#090c14]/90 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                                    @if ($warning['type'] === 'missing')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-xs font-mono font-bold text-rose-400 uppercase tracking-widest">
                                        {{ $warning['type'] === 'missing' ? __('Master Wallet Missing') : __('Master Wallet Error') }}
                                    </h4>
                                    <p class="text-xs text-slate-300 leading-relaxed mt-0.5">
                                        {{ $warning['message'] }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ $warning['url'] }}"
                                class="px-5 py-2.5 rounded-full bg-rose-500 hover:bg-rose-400 text-[#050507] font-black text-xs uppercase tracking-wider transition-all duration-300 font-mono shrink-0 shadow-[0_0_20px_rgba(244,63,94,0.3)]">
                                {{ __('Configure Master Wallet') }} ↗
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ==================================================================================== --}}
        {{-- TOP METRIC CARDS (Double-Bezel Architecture) --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ══ CARD 1 · USERS CARD ══ --}}
            @php $u = $top_cards['users']; @endphp
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] hover:border-cyan-400/40 backdrop-blur-2xl transition-all duration-500 shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden group">
                {{-- Laser Top Accent --}}
                <div class="absolute top-0 left-12 right-12 h-[1.5px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent"></div>

                <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col justify-between h-full">
                    <div>
                        {{-- Sub-Header --}}
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f5ff]"></span>
                                <span class="text-xs font-mono font-bold text-white uppercase tracking-widest">{{ __('Users') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold">
                                <span>TODAY</span>
                                <span class="text-white font-black">+{{ number_format($u['new_users_today']) }}</span>
                            </div>
                        </div>

                        {{-- Metric Counter --}}
                        <div class="mb-6">
                            <div class="text-5xl sm:text-6xl font-black font-mono text-transparent bg-clip-text bg-gradient-to-br from-white via-cyan-100 to-slate-400 tracking-tight leading-none">
                                {{ number_format($u['total']) }}
                            </div>
                            <div class="text-[10px] text-slate-500 uppercase tracking-widest font-mono mt-2">
                                {{ __('Total Registered Users') }}
                            </div>
                        </div>

                        {{-- 3-Col Status Pills --}}
                        <div class="grid grid-cols-3 gap-2.5 mb-3">
                            <div class="bg-white/[0.02] border border-white/[0.06] rounded-2xl p-3 text-center hover:bg-white/[0.04] transition-all">
                                <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block mb-1">{{ __('Active') }}</span>
                                <span class="text-base font-bold font-mono text-emerald-400">{{ number_format($u['active']) }}</span>
                            </div>
                            <div class="bg-white/[0.02] border border-white/[0.06] rounded-2xl p-3 text-center hover:bg-white/[0.04] transition-all">
                                <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block mb-1">{{ __('Banned') }}</span>
                                <span class="text-base font-bold font-mono text-rose-400">{{ number_format($u['banned']) }}</span>
                            </div>
                            <div class="bg-white/[0.02] border border-white/[0.06] rounded-2xl p-3 text-center hover:bg-white/[0.04] transition-all">
                                <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block mb-1">{{ __('Joined Today') }}</span>
                                <span class="text-base font-bold font-mono text-cyan-400">{{ number_format($u['new_users_today']) }}</span>
                            </div>
                        </div>

                        {{-- 2-Col Verification Split --}}
                        <div class="grid grid-cols-2 gap-2.5">
                            <div class="bg-white/[0.02] border border-white/[0.06] rounded-2xl p-3.5 hover:bg-white/[0.04] transition-all">
                                <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block mb-1">{{ __('Email Verified') }}</span>
                                <div class="flex justify-between items-end">
                                    <span class="text-sm font-bold font-mono text-white">{{ number_format($u['email_verified']) }}</span>
                                    <span class="text-[10px] font-mono text-amber-400 font-bold">{{ number_format($u['pending_email_verification']) }} {{ __('pending') }}</span>
                                </div>
                            </div>
                            <div class="bg-white/[0.02] border border-white/[0.06] rounded-2xl p-3.5 hover:bg-white/[0.04] transition-all">
                                <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block mb-1">{{ __('Identity Verified (KYC)') }}</span>
                                @if (!moduleEnabled('kyc_module'))
                                    <span class="text-[10px] font-mono text-slate-500">{{ __('Module Inactive') }}</span>
                                @else
                                    <div class="flex justify-between items-end">
                                        <span class="text-sm font-bold font-mono text-emerald-400">{{ number_format($u['kyc_verified']) }}</span>
                                        <span class="text-[10px] font-mono text-amber-400 font-bold">{{ number_format($u['pending_kyc']) }} {{ __('pending') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Action Button --}}
                    <div class="mt-6 pt-4 border-t border-white/[0.06]">
                        <a href="{{ route('admin.users.index') }}"
                            class="w-full pl-6 pr-2 py-2.5 rounded-full bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/30 hover:border-cyan-400 text-cyan-300 font-bold text-xs uppercase tracking-wider transition-all duration-300 flex items-center justify-between group/btn">
                            <span>{{ __('View All Users') }}</span>
                            <span class="w-8 h-8 rounded-full bg-cyan-400/20 flex items-center justify-center group-hover/btn:translate-x-1 transition-transform">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ══ CARD 2 · PLATFORM BALANCE CARD ══ --}}
            @php $eq = $top_cards['system_equity']; @endphp
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] hover:border-purple-400/40 backdrop-blur-2xl transition-all duration-500 shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden group">
                {{-- Laser Top Accent --}}
                <div class="absolute top-0 left-12 right-12 h-[1.5px] bg-gradient-to-r from-transparent via-purple-400 to-transparent"></div>

                <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col justify-between h-full">
                    <div>
                        {{-- Sub-Header --}}
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-purple-400 shadow-[0_0_8px_#a855f7]"></span>
                                <span class="text-xs font-mono font-bold text-white uppercase tracking-widest">{{ __('Platform Money') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-mono font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>LIVE TOTAL</span>
                            </div>
                        </div>

                        {{-- Metric Counter --}}
                        <div class="mb-6">
                            <div class="text-5xl sm:text-6xl font-black font-mono text-transparent bg-clip-text bg-gradient-to-br from-white via-purple-100 to-slate-400 tracking-tight leading-none">
                                {{ showAmount($eq['total']) }}
                            </div>
                            <div class="text-[10px] text-slate-500 uppercase tracking-widest font-mono mt-2">
                                {{ __('Total money held across all user accounts') }}
                            </div>
                        </div>

                        {{-- Sub-Breakdown Container --}}
                        <div class="space-y-3">
                            <div class="bg-white/[0.02] border border-white/[0.06] rounded-2xl p-4 flex items-center justify-between hover:bg-white/[0.04] transition-all">
                                <div>
                                    <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block">{{ __('User Balances') }}</span>
                                    <span class="text-xs text-slate-500">{{ __('Total available in user wallets') }}</span>
                                </div>
                                <span class="text-lg font-bold font-mono text-white">{{ showAmount($eq['users_balance']) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Button --}}
                    <div class="mt-6 pt-4 border-t border-white/[0.06]">
                        <a href="{{ route('admin.transactions.index') }}"
                            class="w-full pl-6 pr-2 py-2.5 rounded-full bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 hover:border-purple-400 text-purple-300 font-bold text-xs uppercase tracking-wider transition-all duration-300 flex items-center justify-between group/btn">
                            <span>{{ __('View All Transactions') }}</span>
                            <span class="w-8 h-8 rounded-full bg-purple-400/20 flex items-center justify-center group-hover/btn:translate-x-1 transition-transform">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================================================================================== --}}
        {{-- QUICK ACTION SHORTCUTS --}}
        {{-- ==================================================================================== --}}
        @php
            $action_buttons = [
                [
                    'route_name' => 'admin.users.index',
                    'name' => 'Users',
                    'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
                ],
                [
                    'route_name' => 'admin.deposits.index',
                    'name' => 'Deposits',
                    'icon' => '<path d="M12 19V5m0 14l-4-4m4 4l4-4"/>',
                ],
                [
                    'route_name' => 'admin.withdrawals.index',
                    'name' => 'Withdrawals',
                    'icon' => '<path d="M12 5v14m0-14l-4 4m4-4l4 4"/>',
                ],
                [
                    'route_name' => 'admin.deposits.master-wallets',
                    'name' => 'Master Wallets',
                    'icon' => '<rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line>',
                ],
                [
                    'route_name' => 'admin.transactions.index',
                    'name' => 'Transactions',
                    'icon' => '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
                ],
            ];
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 w-full">
            @foreach ($action_buttons as $btn)
                @php
                    $exists = Route::has($btn['route_name']);
                    $href = $exists ? route($btn['route_name']) : '#';
                @endphp
                <a href="{{ $href }}"
                    class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.08] hover:border-cyan-400/40 hover:bg-white/[0.04] transition-all duration-300 flex items-center justify-between group cursor-pointer {{ !$exists ? 'opacity-40 pointer-events-none' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white/[0.04] border border-white/[0.08] group-hover:border-cyan-400/30 text-slate-300 group-hover:text-cyan-400 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                {!! $btn['icon'] !!}
                            </svg>
                        </div>
                        <span class="text-xs font-mono font-bold text-white group-hover:text-cyan-300 transition-colors uppercase tracking-wider">
                            {{ __($btn['name']) }}
                        </span>
                    </div>
                    <span class="text-slate-600 group-hover:text-cyan-400 group-hover:translate-x-0.5 transition-all text-xs font-mono">→</span>
                </a>
            @endforeach
        </div>

        {{-- ==================================================================================== --}}
        {{-- MASTER WALLETS DECK --}}
        {{-- ==================================================================================== --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
            <div class="absolute top-0 left-16 right-16 h-[1.5px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent"></div>

            <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 mb-6 border-b border-white/[0.06]">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0 shadow-[0_0_20px_rgba(0,245,255,0.15)]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect>
                                <line x1="2" y1="10" x2="22" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Platform Master Wallets') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('The blockchain addresses where user deposits arrive.') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.deposits.master-wallets') }}"
                        class="px-5 py-2.5 rounded-full bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/30 hover:border-cyan-400 text-cyan-300 font-bold text-xs uppercase tracking-wider transition-all duration-300 font-mono flex items-center gap-2 self-start sm:self-auto">
                        <span>{{ __('Manage Master Wallets') }}</span>
                        <span>↗</span>
                    </a>
                </div>

                {{-- Wallets List --}}
                <div class="space-y-3">
                    @forelse ($master_wallets ?? [] as $wallet)
                        @php
                            $bcLogo = !empty($wallet->logo)
                                ? asset($wallet->logo)
                                : asset('assets/images/tokens/' . strtolower($wallet->code) . '.png');
                            $qrId = 'admin-qr-panel-' . $wallet->id;
                            $qrTargetId = 'admin-qr-canvas-' . $wallet->id;
                            $address = $wallet->master_wallet_address;
                            $isConfigured = !empty($address);

                            if ($wallet->code === 'solana') {
                                $manageLink = route('admin.solana-master-wallet.index');
                            } elseif ($wallet->isTron()) {
                                $manageLink = route('admin.tron-master-wallet.index');
                            } elseif ($wallet->isBitcoin()) {
                                $manageLink = route('admin.bitcoin-master-wallet.index');
                            } elseif ($wallet->isEvm()) {
                                $manageLink = route('admin.evm-master-wallet.index', ['blockchain' => $wallet->code]);
                            } else {
                                $manageLink = route('admin.deposits.master-wallets');
                            }
                        @endphp

                        <div class="rounded-2xl bg-white/[0.02] border border-white/[0.06] hover:border-white/[0.12] transition-all flex flex-col overflow-hidden">
                            {{-- Main Wallet Row --}}
                            <div class="p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                {{-- Left: Blockchain & Tokens --}}
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-10 h-10 rounded-2xl bg-white/[0.04] border border-white/[0.08] flex items-center justify-center shrink-0 overflow-hidden p-1.5">
                                        <img src="{{ $bcLogo }}"
                                             alt="{{ $wallet->name }}"
                                             class="w-full h-full object-contain rounded-xl"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span style="display:none" class="w-full h-full items-center justify-center text-cyan-400 font-mono text-xs font-black uppercase">
                                            {{ substr($wallet->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-sm font-bold text-white uppercase tracking-wider truncate">{{ $wallet->name }}</h4>
                                            @if ($isConfigured)
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-mono font-bold">
                                                    ● READY
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[9px] font-mono font-bold">
                                                    ● NOT SET UP
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                            <span class="text-[9px] font-mono text-slate-500 uppercase tracking-widest">{{ __('Supported Coins:') }}</span>
                                            @forelse ($wallet->tokens as $token)
                                                @php
                                                    $tokenLogo = !empty($token->logo)
                                                        ? asset($token->logo)
                                                        : asset('assets/images/tokens/' . strtolower($token->symbol) . '.png');
                                                @endphp
                                                <span class="inline-flex items-center gap-1 text-[9px] font-mono font-bold text-slate-300 bg-white/[0.04] border border-white/[0.08] rounded-md px-1.5 py-0.5">
                                                    <img src="{{ $tokenLogo }}" alt="{{ $token->symbol }}" class="w-3 h-3 object-contain rounded-full" onerror="this.style.display='none'">
                                                    <span>{{ $token->symbol }}</span>
                                                </span>
                                            @empty
                                                <span class="text-[9px] font-mono text-slate-400 bg-white/[0.03] border border-white/[0.06] rounded px-1.5 py-0.5">Native Coin Only</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                {{-- Right: Address + Copy + QR + Individual Master Wallet Link --}}
                                <div class="flex items-center gap-2 flex-1 max-w-full lg:max-w-xl justify-end">
                                    @if ($isConfigured)
                                        <div class="flex items-center gap-2 bg-black/40 rounded-xl px-3.5 py-2 border border-white/[0.08] flex-1 min-w-0 justify-between">
                                            <code class="font-mono text-xs text-cyan-300 truncate select-all flex-1 pr-2">{{ $address }}</code>
                                            <div class="flex items-center gap-1 shrink-0">
                                                {{-- Copy --}}
                                                <button onclick="navigator.clipboard.writeText('{{ $address }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Address copied to clipboard') }}', showConfirmButton:false, timer:1500});"
                                                        class="text-slate-400 hover:text-white transition-colors cursor-pointer p-1.5 rounded-lg hover:bg-white/[0.06]" title="{{ __('Copy address') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                                    </svg>
                                                </button>
                                                {{-- QR Code --}}
                                                <button onclick="toggleAdminQr('{{ $qrId }}', '{{ $qrTargetId }}', '{{ addslashes($address) }}')"
                                                        class="text-slate-400 hover:text-cyan-400 transition-colors cursor-pointer p-1.5 rounded-lg hover:bg-white/[0.06]" title="{{ __('Show QR code') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                                                        <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                                                        <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                                                        <rect x="14" y="14" width="3" height="3"></rect>
                                                        <rect x="18" y="14" width="3" height="3"></rect>
                                                        <rect x="14" y="18" width="3" height="3"></rect>
                                                        <rect x="18" y="18" width="3" height="3"></rect>
                                                    </svg>
                                                </button>
                                                {{-- Direct Individual Master Wallet Link --}}
                                                <a href="{{ $manageLink }}" class="text-slate-400 hover:text-cyan-400 transition-colors p-1.5 rounded-lg hover:bg-white/[0.06]" title="{{ __('Manage :name Master Wallet', ['name' => $wallet->name]) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <a href="{{ $manageLink }}"
                                            class="px-4 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-300 font-mono text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-2">
                                            <span>{{ __('Set Up :name Master Wallet', ['name' => $wallet->name]) }}</span>
                                            <span>↗</span>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- QR Panel (hidden by default) --}}
                            <div id="{{ $qrId }}" class="hidden border-t border-white/[0.06] bg-black/40">
                                <div class="flex flex-col items-center gap-3 py-6 px-4">
                                    <div id="{{ $qrTargetId }}" class="p-3.5 bg-white rounded-2xl shadow-2xl shadow-black/80"></div>
                                    <p class="font-mono text-xs text-cyan-300 break-all text-center max-w-md select-all leading-relaxed bg-white/[0.03] p-2.5 rounded-xl border border-white/[0.06]">
                                        {{ $address }}
                                    </p>
                                    <div class="flex items-center gap-3">
                                        <p class="text-[10px] font-mono text-slate-400 uppercase tracking-widest">{{ $wallet->name }} {{ __('Master Wallet Address') }}</p>
                                        <a href="{{ $manageLink }}" class="text-[10px] font-mono text-cyan-400 hover:underline uppercase tracking-wider">{{ __('Manage Master Wallet') }} ↗</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs font-mono text-slate-500">
                            {{ __('No active blockchain networks found.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- CHARTS SECTION --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ── Left: Activity Graph (8 cols) ── --}}
            <div class="lg:col-span-8 p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8">
                    {{-- Header row --}}
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-6 pb-4 border-b border-white/[0.06]">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f5ff]"></span>
                                <h3 id="graph-title" class="text-base font-bold text-white font-mono tracking-wide">
                                    {{ __('Money Activity Over Time') }}
                                </h3>
                            </div>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest font-mono">
                                {{ __('Platform Activity') }} · {{ getSetting('currency', 'USD') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            {{-- Dataset switcher --}}
                            <div class="flex items-center gap-1 bg-white/[0.03] border border-white/[0.08] rounded-xl p-1">
                                @foreach (['transactions' => 'Transactions', 'deposits' => 'Deposits', 'withdrawals' => 'Withdrawals'] as $key => $label)
                                    <button data-dataset="{{ $key }}"
                                        class="graph-dataset-btn cursor-pointer px-3 py-1 rounded-lg text-[10px] font-mono font-bold uppercase tracking-wider transition-all
                                            {{ $key === 'transactions' ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' : 'text-slate-400 hover:text-white' }}">
                                        {{ __($label) }}
                                    </button>
                                @endforeach
                            </div>

                            {{-- Period filter --}}
                            <div class="flex items-center gap-1 bg-white/[0.03] border border-white/[0.08] rounded-xl p-1 font-mono">
                                @foreach (['7d' => '7D', '30d' => '30D', '1y' => '1Y', 'ytd' => 'YTD'] as $key => $label)
                                    <button data-period="{{ $key }}"
                                        class="graph-period-btn cursor-pointer px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase transition-all
                                            {{ $key === '7d' ? 'bg-white/10 text-white' : 'text-slate-500 hover:text-slate-300' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Legend --}}
                    <div id="graph-legend" class="flex items-center gap-4 mb-4 flex-wrap font-mono text-xs"></div>

                    {{-- Chart canvas --}}
                    <div class="relative h-64 sm:h-72 w-full">
                        <canvas id="mainLineChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- ── Right: Balance Breakdown (4 cols) ── --}}
            <div class="lg:col-span-4 p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-purple-400 shadow-[0_0_8px_#a855f7]"></span>
                            <h3 class="text-base font-bold text-white font-mono tracking-wide">{{ __('Balance Breakdown') }}</h3>
                        </div>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest font-mono mb-6">
                            {{ __('Where money is held') }}
                        </p>

                        <div class="relative flex items-center justify-center my-4" style="height: 180px;">
                            <canvas id="equityDonut"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <div class="text-sm font-black font-mono text-white">
                                    {{ showAmount(array_sum(array_values($chart_data))) }}
                                </div>
                                <div class="text-[8px] text-slate-500 uppercase tracking-widest font-mono">
                                    {{ __('Total Balance') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Donut Legend --}}
                    <div class="pt-4 border-t border-white/[0.06] space-y-2 font-mono">
                        @php
                            $donutItems = [
                                'account_balance' => ['label' => 'User Balances', 'color' => '#00f5ff'],
                            ];
                            $donutTotal = array_sum(array_values($chart_data)) ?: 1;
                        @endphp
                        @foreach ($donutItems as $key => $meta)
                            @php
                                $val = $chart_data[$key] ?? 0;
                                $pct = round(($val / $donutTotal) * 100, 1);
                            @endphp
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/[0.02] border border-white/[0.04]">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $meta['color'] }}; box-shadow: 0 0 8px {{ $meta['color'] }}"></span>
                                    <span class="text-xs text-slate-300">{{ __($meta['label']) }}</span>
                                </div>
                                <span class="text-xs font-bold text-white">{{ $pct }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================================================================================== --}}
        {{-- RECENT ACTIVITY TABLE --}}
        {{-- ==================================================================================== --}}
        @php
            $transactionRows = $recent_data['transactions'] ?? collect();
        @endphp

        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
            <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] overflow-hidden">
                {{-- Card Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/[0.06] bg-white/[0.01]">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f5ff]"></span>
                        <span class="text-xs font-mono font-bold text-cyan-400 uppercase tracking-widest">
                            {{ __('Recent Activity') }}
                        </span>
                        <span class="text-[10px] font-mono bg-cyan-500/15 text-cyan-300 border-cyan-500/30 px-2 py-0.5 rounded-full border">
                            {{ $transactionRows->count() }}
                        </span>
                    </div>
                    <a href="{{ route('admin.transactions.index') }}"
                        class="flex items-center gap-1.5 text-xs font-mono font-bold uppercase tracking-wider text-cyan-400 hover:text-white transition-colors cursor-pointer">
                        <span>{{ __('View All') }}</span>
                        <span>↗</span>
                    </a>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-white/[0.06] bg-white/[0.01]">
                                <th class="px-6 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap font-mono">#</th>
                                <th class="px-6 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap font-mono">{{ __('User') }}</th>
                                <th class="px-6 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap font-mono">{{ __('Type') }}</th>
                                <th class="px-6 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap font-mono">{{ __('Amount') }}</th>
                                <th class="px-6 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap font-mono">{{ __('Description') }}</th>
                                <th class="px-6 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap font-mono">{{ __('Time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactionRows as $row)
                                <tr class="border-b border-white/[0.03] hover:bg-white/[0.02] transition-colors">
                                    <td class="px-6 py-3.5 text-xs text-slate-400 whitespace-nowrap font-mono">{{ $row->id }}</td>
                                    <td class="px-6 py-3.5 text-xs text-white font-bold whitespace-nowrap font-mono">{{ $row->user?->username ?? '—' }}</td>
                                    <td class="px-6 py-3.5 text-xs whitespace-nowrap font-mono">
                                        @php
                                            $isCredit = ($row->type === 'credit' || $row->type === 'buy');
                                            $typeBadge = $isCredit ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20';
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $typeBadge }}">{{ $row->type }}</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs font-bold whitespace-nowrap font-mono {{ $isCredit ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $isCredit ? '+' : '-' }}{{ showAmount($row->converted_amount) }}
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-slate-300 whitespace-nowrap font-mono">{{ \Str::limit($row->description, 35) }}</td>
                                    <td class="px-6 py-3.5 text-xs text-slate-400 whitespace-nowrap font-mono">{{ $row->created_at?->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-500 font-mono uppercase tracking-widest">
                                        {{ __('No transactions found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function() {
            'use strict';

            // ── QR Code Support ──────────────────────────────────────────
            (function loadQrLib() {
                if (window.QRCode) return;
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js';
                document.head.appendChild(s);
            })();

            window.toggleAdminQr = function(panelId, canvasId, address) {
                const panel = document.getElementById(panelId);
                const canvas = document.getElementById(canvasId);
                if (!panel || !canvas) return;

                const isHidden = panel.classList.contains('hidden');

                // Close all other open panels
                document.querySelectorAll('[id^="admin-qr-panel-"]').forEach(p => p.classList.add('hidden'));

                if (!isHidden) return; // was open, just close

                panel.classList.remove('hidden');

                if (canvas.dataset.generated) return;
                canvas.dataset.generated = '1';

                function doRender() {
                    new QRCode(canvas, {
                        text: address,
                        width: 160,
                        height: 160,
                        colorDark: '#000000',
                        colorLight: '#ffffff',
                        correctLevel: QRCode.CorrectLevel.M,
                    });
                }

                if (window.QRCode) {
                    doRender();
                } else {
                    const timer = setInterval(() => {
                        if (window.QRCode) {
                            clearInterval(timer);
                            doRender();
                        }
                    }, 50);
                }
            };

            // ── Data from PHP ────────────────────────────────────────────────────────
            const GRAPH_DATA = @json($graph_data);
            const CHART_DATA = @json($chart_data);

            // ── Colour palettes ──────────────────────────────────────────────────────
            const DATASET_META = {
                transactions: {
                    title: 'Transaction History',
                    series: {
                        credit: {
                            label: 'Credit Inflow',
                            color: '#10b981'
                        },
                        debit: {
                            label: 'Debit Outflow',
                            color: '#f43f5e'
                        },
                    },
                },
                deposits: {
                    title: 'Deposit Volume',
                    series: {
                        completed: {
                            label: 'Completed',
                            color: '#00f5ff'
                        },
                        pending: {
                            label: 'Pending',
                            color: '#f59e0b'
                        },
                        failed: {
                            label: 'Failed',
                            color: '#f43f5e'
                        },
                        partial_payment: {
                            label: 'Partial Payment',
                            color: '#a855f7'
                        },
                    },
                },
                withdrawals: {
                    title: 'Withdrawal Volume',
                    series: {
                        completed: {
                            label: 'Completed',
                            color: '#10b981'
                        },
                        pending: {
                            label: 'Pending',
                            color: '#f59e0b'
                        },
                        failed: {
                            label: 'Failed',
                            color: '#f43f5e'
                        },
                        partial_payment: {
                            label: 'Partial Payment',
                            color: '#a855f7'
                        },
                    },
                },
            };

            const DONUT_COLORS = ['#00f5ff'];
            const DONUT_LABELS = ['User Accounts'];
            const DONUT_KEYS = ['account_balance'];

            // ── Shared Chart.js defaults ─────────────────────────────────────────────
            Chart.defaults.color = 'rgba(148,163,184,0.7)';
            Chart.defaults.borderColor = 'rgba(255,255,255,0.05)';
            Chart.defaults.font.family = "'JetBrains Mono', monospace";
            Chart.defaults.font.size = 10;

            // ── Line chart ───────────────────────────────────────────────────────────
            const lineCtx = document.getElementById('mainLineChart').getContext('2d');
            const lineChart = new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(9,12,20,0.95)',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            padding: 12,
                            titleFont: {
                                size: 11,
                                weight: 'bold',
                                family: "'JetBrains Mono', monospace"
                            },
                            bodyFont: {
                                size: 10,
                                family: "'JetBrains Mono', monospace"
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(255,255,255,0.03)'
                            },
                            ticks: {
                                maxTicksLimit: 8,
                                maxRotation: 0
                            },
                        },
                        y: {
                            grid: {
                                color: 'rgba(255,255,255,0.04)'
                            },
                            ticks: {
                                maxTicksLimit: 5
                            },
                            beginAtZero: true,
                        },
                    },
                },
            });

            // ── Donut chart ──────────────────────────────────────────────────────────
            const donutCtx = document.getElementById('equityDonut').getContext('2d');
            const donutChart = new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: DONUT_LABELS,
                    datasets: [{
                        data: DONUT_KEYS.map(k => CHART_DATA[k] || 0),
                        backgroundColor: DONUT_COLORS.map(c => c + 'cc'),
                        borderColor: DONUT_COLORS,
                        borderWidth: 1.5,
                        hoverOffset: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '74%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(9,12,20,0.95)',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            padding: 10,
                        },
                    },
                },
            });

            // ── State ────────────────────────────────────────────────────────────────
            let activeDataset = 'transactions';
            let activePeriod = '7d';

            // ── Render legend ────────────────────────────────────────────────────────
            function renderLegend(meta) {
                const el = document.getElementById('graph-legend');
                el.innerHTML = '';
                Object.entries(meta.series).forEach(([, s]) => {
                    el.insertAdjacentHTML('beforeend',
                        `<span class="flex items-center gap-1.5 text-slate-300">
                            <span style="width:8px;height:8px;border-radius:50%;background:${s.color};box-shadow:0 0 6px ${s.color};display:inline-block;"></span>
                            ${s.label}
                        </span>`
                    );
                });
            }

            // ── Update line chart ────────────────────────────────────────────────────
            function updateLineChart() {
                const meta = DATASET_META[activeDataset];
                const periodData = GRAPH_DATA[activeDataset][activePeriod];

                const firstKey = Object.keys(meta.series)[0];
                const labels = periodData[firstKey]?.labels ?? [];

                lineChart.data.labels = labels;
                lineChart.data.datasets = Object.entries(meta.series).map(([key, s]) => {
                    const raw = periodData[key]?.data ?? [];
                    return {
                        label: s.label,
                        data: raw,
                        borderColor: s.color,
                        backgroundColor: s.color + '15',
                        fill: true,
                        tension: 0.4,
                        pointRadius: labels.length <= 15 ? 3 : 0,
                        pointHoverRadius: 5,
                        borderWidth: 2,
                    };
                });

                document.getElementById('graph-title').textContent = meta.title;
                lineChart.update('active');
                renderLegend(meta);
            }

            // ── Button interaction ───────────────────────────────────────────────────
            document.querySelectorAll('.graph-dataset-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    activeDataset = btn.dataset.dataset;
                    document.querySelectorAll('.graph-dataset-btn').forEach(b => {
                        b.classList.remove('bg-cyan-500/20', 'text-cyan-300', 'border', 'border-cyan-500/30');
                        b.classList.add('text-slate-400');
                    });
                    btn.classList.remove('text-slate-400');
                    btn.classList.add('bg-cyan-500/20', 'text-cyan-300', 'border', 'border-cyan-500/30');
                    updateLineChart();
                });
            });

            document.querySelectorAll('.graph-period-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    activePeriod = btn.dataset.period;
                    document.querySelectorAll('.graph-period-btn').forEach(b => {
                        b.classList.remove('bg-white/10', 'text-white');
                        b.classList.add('text-slate-500');
                    });
                    btn.classList.remove('text-slate-500');
                    btn.classList.add('bg-white/10', 'text-white');
                    updateLineChart();
                });
            });

            // ── Init ─────────────────────────────────────────────────────────────────
            updateLineChart();
        })();
    </script>
@endpush
