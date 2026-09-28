@extends('templates.york.blades.admin.layouts.admin')

@push('css')
    <style>
        /* Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
            flex-shrink: 0;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 12px;
            width: 12px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        input:checked+.slider {
            background-color: #10b981;
            border-color: #10b981;
        }

        input:checked+.slider:before {
            transform: translateX(20px);
            background-color: white;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
            <div>
                <h2 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-indigo-200 to-indigo-400 tracking-tight leading-tight">
                    {{ __('Master Wallets') }}
                </h2>
                <p class="text-indigo-200/60 font-mono text-[9px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Central destination wallets where incoming deposits are received and stored') }}
                </p>
            </div>
            
            {{-- Search & Actions Group --}}
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto shrink-0">
                {{-- Reset Button --}}
                <button type="button" id="reset-wallets-button" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-rose-600/90 hover:bg-rose-500 text-xs font-bold text-white flex items-center justify-center gap-2 transition-all duration-300 shadow-[0_0_15px_rgba(244,63,94,0.2)] active:scale-[0.98] cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>{{ __('Reset Wallets') }}</span>
                </button>

                {{-- Sync Button --}}
                <button type="button" id="sync-button" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white flex items-center justify-center gap-2 transition-all duration-300 shadow-[0_0_15px_rgba(99,102,241,0.2)] active:scale-[0.98] cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" id="sync-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h1m15 6a8 8 0 1 1-13.5-3.5M20 20v-5h-1m-15-6a8 8 0 1 0 13.5 3.5" />
                    </svg>
                    <span>{{ __('Sync Wallets') }}</span>
                </button>
                
                {{-- Search Bar --}}
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="search-input" placeholder="{{ __('Search blockchain or address...') }}" 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-black/20 border border-white/5 focus:border-indigo-500/50 focus:bg-black/40 text-xs text-white placeholder-slate-500 outline-none transition-all duration-300">
                </div>
            </div>
        </div>

        {{-- Information & Security Notices Stack --}}
        <div class="space-y-3 font-mono text-xs">
            {{-- 1. Private Key Encryption Security Notice (Cyan) --}}
            <div class="px-5 py-4 rounded-2xl bg-cyan-500/[0.04] border border-cyan-500/20 flex items-start gap-3.5 shadow-[0_0_20px_rgba(0,245,255,0.03)]">
                <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-white font-bold block text-xs tracking-wide">{{ __('Cryptographic Key Security & Encryption') }}</h4>
                    <p class="text-[11px] text-slate-300 leading-relaxed mt-0.5">
                        {{ __('All master wallet private keys are strictly encrypted at rest and can only be decrypted using the application key (APP_KEY) and authenticated administrator credentials.') }}
                    </p>
                </div>
            </div>

            {{-- 2. External Wallet Import Notice (Amber) --}}
            <div class="px-5 py-4 rounded-2xl bg-amber-500/[0.04] border border-amber-500/20 flex items-start gap-3.5 shadow-[0_0_20px_rgba(245,158,11,0.03)]">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-white font-bold block text-xs tracking-wide">{{ __('External Wallet Import (Trust Wallet / MetaMask / Phantom / TronLink)') }}</h4>
                    <p class="text-[11px] text-slate-300 leading-relaxed mt-0.5">
                        {{ __('Admins can reveal and export master wallet private keys to import them directly into Trust Wallet, MetaMask, Phantom, TronLink, or hardware wallets for direct manual control and monitoring.') }}
                    </p>
                </div>
            </div>

            {{-- 3. Blockchain Settings & RPC Notice (Indigo) --}}
            <div class="px-5 py-4 rounded-2xl bg-indigo-500/[0.04] border border-indigo-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-[0_0_20px_rgba(99,102,241,0.03)]">
                <div class="flex items-start gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-400 shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-white font-bold block text-xs tracking-wide">{{ __('Blockchain Settings & RPC Node Configuration') }}</h4>
                        <p class="text-[11px] text-slate-300 leading-relaxed mt-0.5">
                            {{ __('General blockchain network parameters, custom/self-hosted RPC node endpoints, block explorers, and supported deposit tokens can be configured in Blockchain Settings.') }}
                        </p>
                    </div>
                </div>
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.settings.blockchain.index') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-500/10 hover:bg-indigo-500/25 text-indigo-300 hover:text-white border border-indigo-500/30 text-[10px] font-bold uppercase tracking-wider transition-all cursor-pointer">
                        <span>{{ __('Blockchain Settings') }}</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Master Wallets List Table --}}
        <div class="bg-secondary border border-white/5 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 bg-black/25 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <th class="p-4 pl-6 w-12 text-center">#</th>
                            <th class="p-4">{{ __('Blockchain') }}</th>
                            <th class="p-4">{{ __('Master Address') }}</th>
                            <th class="p-4 pr-6 text-right w-44">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-xs text-slate-300 font-medium" id="wallets-tbody">
                        @foreach ($blockchains as $index => $bc)
                            @php
                                $address = $bc->master_wallet_address;
                                $isConfigured = !empty($address);
                                $explorerUrl = $isConfigured && $bc->explorer_url_live ? str_replace('{address}', $address, $bc->explorer_url_live) : null;
                                
                                if ($bc->code === 'solana') {
                                    $manageLink = route('admin.solana-master-wallet.index');
                                    $generateLink = route('admin.solana-master-wallet.generate');
                                } elseif ($bc->isTron()) {
                                    $manageLink = route('admin.tron-master-wallet.index');
                                    $generateLink = route('admin.tron-master-wallet.generate');
                                } elseif ($bc->isBitcoin()) {
                                    $manageLink = route('admin.bitcoin-master-wallet.index');
                                    $generateLink = route('admin.bitcoin-master-wallet.generate');
                                } elseif ($bc->isEvm()) {
                                    $manageLink = route('admin.evm-master-wallet.index', ['blockchain' => $bc->code]);
                                    $generateLink = route('admin.evm-master-wallet.generate', ['blockchain' => $bc->code]);
                                } else {
                                    $manageLink = null;
                                    $generateLink = null;
                                }

                                $logoUrl = $bc->logo ? asset($bc->logo) : asset('assets/images/tokens/eth.png');
                            @endphp
                            <tr class="hover:bg-white/[0.01] transition-colors group wallet-row" data-search="{{ strtolower($bc->name) }} {{ strtolower($bc->code) }} {{ strtolower($address) }}">
                                <td class="p-4 pl-6 text-center font-mono text-slate-500 text-[11px] row-index">{{ $index + 1 }}</td>
                                 <td class="p-4">
                                     <div class="flex items-center justify-between gap-4">
                                         <div class="flex items-center gap-3">
                                             {{-- Dynamic Logo --}}
                                             <div class="w-8 h-8 rounded-lg overflow-hidden border border-white/10 flex items-center justify-center bg-indigo-500/10 text-indigo-400 font-bold shrink-0">
                                                 <img src="{{ $logoUrl }}" 
                                                      alt="{{ $bc->name }}" 
                                                      class="w-full h-full object-cover" 
                                                      onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                 <span class="hidden w-full h-full items-center justify-center font-black text-[10px] uppercase">
                                                     {{ substr($bc->name, 0, 2) }}
                                                 </span>
                                             </div>
                                             <div>
                                                 <div class="font-bold text-white leading-tight group-hover:text-indigo-300 transition-colors">
                                                     {{ $bc->name }}
                                                 </div>
                                                 <div class="text-[9px] text-slate-500 font-mono tracking-wider uppercase mt-0.5">
                                                     {{ $bc->code }}
                                                 </div>
                                                 <div class="text-[9px] text-indigo-400/80 font-bold uppercase tracking-wider mt-1 flex items-center gap-1">
                                                     <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                         <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                     </svg>
                                                     {{ __(':count Tokens', ['count' => $bc->tokens()->where('status', 'enabled')->count()]) }}
                                                 </div>
                                             </div>
                                         </div>
                                         {{-- Status Toggle switch --}}
                                         <label class="switch shrink-0 mr-4" title="{{ __('Toggle Blockchain Status') }}">
                                             <input type="checkbox" class="blockchain-status-toggle"
                                                 data-id="{{ $bc->id }}"
                                                 {{ $bc->status === 'enabled' ? 'checked' : '' }}>
                                             <span class="slider"></span>
                                         </label>
                                     </div>
                                 </td>
                                <td class="p-4 font-mono text-[11px] tracking-tight">
                                    @if ($isConfigured)
                                        <div class="flex flex-col gap-2 max-w-sm lg:max-w-md xl:max-w-xl">
                                            <span class="truncate text-slate-300 select-all" id="addr-{{ $bc->code }}">{{ $address }}</span>
                                            
                                            {{-- Supported Tokens Tiny Logos --}}
                                            <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                                @php
                                                    $tokens = $bc->tokens()->where('status', 'enabled')->get();
                                                @endphp
                                                @foreach ($tokens as $token)
                                                    @php
                                                        $tokenLogo = $token->logo ? asset($token->logo) : asset('assets/images/tokens/' . strtolower($token->symbol) . '.png');
                                                    @endphp
                                                    <div class="w-5.5 h-5.5 rounded-full overflow-hidden border border-white/10 bg-black/25 flex items-center justify-center shrink-0" title="{{ strtoupper($token->symbol) }} ({{ $token->name }})">
                                                        <img src="{{ $tokenLogo }}" alt="{{ $token->symbol }}" class="w-4 h-4 object-contain rounded-full" onerror="this.src='{{ asset('assets/images/tokens/eth.png') }}'">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-bold text-amber-400 uppercase tracking-widest bg-amber-500/5 border border-amber-500/10 px-2 py-0.5 rounded-full inline-flex items-center gap-1.5">
                                            <span class="w-1 h-1 rounded-full bg-amber-400 animate-pulse"></span>{{ __('Not Set Up') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 pr-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Copy Button --}}
                                        @if ($isConfigured)
                                            <button type="button" onclick="copyToClipboard('{{ $address }}', '{{ __(':blockchain address copied', ['blockchain' => $bc->name]) }}')" 
                                                    class="p-2 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-slate-400 hover:text-white transition-all active:scale-95 cursor-pointer"
                                                    title="{{ __('Copy Address') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                                </svg>
                                            </button>

                                            {{-- QR Code Button --}}
                                            <button type="button" onclick="showMasterWalletQrModal('{{ addslashes($bc->name) }}', '{{ addslashes($address) }}')" 
                                                    class="p-2 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 hover:border-cyan-500/30 text-slate-400 hover:text-cyan-400 transition-all active:scale-95 cursor-pointer"
                                                    title="{{ __('Show QR Code') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                                                    <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                                                    <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                                                    <rect x="14" y="14" width="3" height="3"></rect>
                                                    <rect x="18" y="14" width="3" height="3"></rect>
                                                    <rect x="14" y="18" width="3" height="3"></rect>
                                                    <rect x="18" y="18" width="3" height="3"></rect>
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Explorer Button --}}
                                        @if ($isConfigured && $explorerUrl)
                                            <a href="{{ $explorerUrl }}" target="_blank" 
                                               class="p-2 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-slate-400 hover:text-white transition-all active:scale-95 cursor-pointer"
                                               title="{{ __('View Explorer') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>
                                                </svg>
                                            </a>
                                        @endif

                                        {{-- Settings / Configure / Generate Button --}}
                                        @if ($isConfigured)
                                            @if ($manageLink)
                                                <a href="{{ $manageLink }}" 
                                                   class="p-2 rounded-lg bg-indigo-600/10 border border-indigo-500/20 hover:bg-indigo-600 text-indigo-400 hover:text-white transition-all active:scale-95 cursor-pointer"
                                                   title="{{ __('Manage Wallet') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        @else
                                            @if ($generateLink)
                                                <button type="button" onclick="inlineGenerateWallet('{{ $bc->name }}', '{{ $generateLink }}')"
                                                        class="p-2 rounded-lg bg-emerald-600/10 border border-emerald-500/20 hover:bg-emerald-600 text-emerald-400 hover:text-white transition-all active:scale-95 cursor-pointer"
                                                        title="{{ __('Generate Master Wallet') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M12 5v14M5 12h14"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="button" disabled 
                                                        class="p-2 rounded-lg bg-white/5 border border-white/5 text-slate-600 cursor-not-allowed"
                                                        title="{{ __('Integration Coming Soon') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="no-results-row" class="hidden">
                            <td colspan="4" class="p-12 text-center text-slate-500 italic">
                                {{ __('No blockchains match your search.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showNotify(type, message) {
        if (typeof Swal !== 'undefined' && Swal.fire) {
            const icon = type === 'success' ? 'success' : (type === 'error' ? 'error' : 'info');
            Swal.fire({
                icon: icon,
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                background: '#12131a',
                color: '#fff'
            });
            return;
        }

        console.log(type + ': ' + message);
    }

    window.notify = function(type, message) {
        showNotify(type, message);
    };

    $(document).ready(function() {
        // Javascript Search Filter
        $('#search-input').on('input', function() {
            let query = $(this).val().toLowerCase().trim();
            let visibleCount = 0;
            
            $('.wallet-row').each(function() {
                let searchData = $(this).attr('data-search') || '';
                if (searchData.indexOf(query) !== -1) {
                    $(this).removeClass('hidden');
                    visibleCount++;
                } else {
                    $(this).addClass('hidden');
                }
            });
            
            // Recalculate index numbers consecutively for visible items
            let idx = 1;
            $('.wallet-row:not(.hidden)').each(function() {
                $(this).find('.row-index').text(idx++);
            });
            
            if (visibleCount === 0) {
                $('#no-results-row').removeClass('hidden');
            } else {
                $('#no-results-row').addClass('hidden');
            }
        });

        $('#reset-wallets-button').on('click', function() {
            Swal.fire({
                title: '{{ __('Reset all master wallets?') }}',
                text: '{{ __('This will clear the configured address and private key for every master wallet record. This action cannot be undone.') }}',
                icon: 'warning',
                input: 'password',
                inputLabel: '{{ __('Admin Password') }}',
                inputPlaceholder: '{{ __('Enter your admin password') }}',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocomplete: 'current-password'
                },
                showCancelButton: true,
                confirmButtonText: '{{ __('Reset All Wallets') }}',
                cancelButtonText: '{{ __('Cancel') }}',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#475569',
                background: '#12131a',
                color: '#fff',
                preConfirm: (password) => {
                    if (!password || !password.trim()) {
                        Swal.showValidationMessage('{{ __('Admin password is required.') }}');
                        return false;
                    }

                    return password;
                }
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                let $btn = $(this);
                if ($btn.hasClass('opacity-50')) return;

                $btn.addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
                notify('info', 'Resetting master wallets...');

                $.ajax({
                    url: "{{ route('admin.deposits.master-wallets.reset') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        password: result.value
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            notify('success', res.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            notify('error', res.message || 'Reset failed');
                            $btn.removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Reset failed';
                        notify('error', msg);
                        $btn.removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
                    }
                });
            });
        });

        // Sync button AJAX post
        $('#sync-button').on('click', function() {
            let $btn = $(this);
            let $icon = $('#sync-icon');
            
            if ($btn.hasClass('opacity-50')) return;
            
            $btn.addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
            $icon.addClass('animate-spin');
            
            notify('info', 'Synchronizing master wallets...');
            
            $.ajax({
                url: "{{ route('admin.evm-master-wallet.sync') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status === 'success') {
                        notify('success', res.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        notify('error', res.message || 'Sync failed');
                        $btn.removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
                        $icon.removeClass('animate-spin');
                    }
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Synchronization failed';
                    notify('error', msg);
                    $btn.removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
                    $icon.removeClass('animate-spin');
                }
            });
        });

        // Toggle blockchain status AJAX
        $(document).on('change', '.blockchain-status-toggle', function() {
            const $toggle = $(this);
            const id = $toggle.data('id');
            const isChecked = $toggle.is(':checked');

            $.ajax({
                url: `{{ url('admin/settings/deposit/blockchains/toggle-status') }}/${id}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    notify('success', response.message);
                },
                error: function(xhr) {
                    $toggle.prop('checked', !isChecked);
                    let errorMessage = xhr.responseJSON?.message || '{{ __('Failed to update blockchain status.') }}';
                    notify('error', errorMessage);
                }
            });
        });
    });

    window.inlineGenerateWallet = function(blockchainName, generateUrl) {
        Swal.fire({
            title: '{{ __('Generate Master Wallet') }}',
            text: '{{ __('Are you sure you want to generate a new master wallet for :blockchain?') }}'.replace(':blockchain', blockchainName),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '{{ __('Generate') }}',
            cancelButtonText: '{{ __('Cancel') }}',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#475569',
            background: '#12131a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: '{{ __('Processing...') }}',
                    text: '{{ __('Generating master wallet...') }}',
                    allowOutsideClick: false,
                    background: '#12131a',
                    color: '#fff',
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: generateUrl,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('Success!') }}',
                                text: res.message,
                                background: '#12131a',
                                color: '#fff'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('Error') }}',
                                text: res.message || 'Generation failed',
                                background: '#12131a',
                                color: '#fff'
                            });
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Generation failed';
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: msg,
                            background: '#12131a',
                            color: '#fff'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
