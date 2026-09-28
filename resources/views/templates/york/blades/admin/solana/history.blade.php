@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12">
        {{-- Header & Back Button --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="p-2 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-slate-300 hover:text-white transition-all active:scale-95 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-indigo-200 to-indigo-400 tracking-tight leading-tight">
                            {{ __('Solana Master Wallet Details') }}
                        </h2>
                        <p class="text-indigo-200/60 font-mono text-[9px] md:text-xs mt-0.5 tracking-widest uppercase">
                            {{ __('Real-time On-chain Wallet Telemetry') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 text-[10px] text-emerald-400/80 font-mono bg-emerald-500/5 border border-emerald-500/10 px-3 py-1.5 rounded-xl">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ __('CONNECTED TO SOLANA NETWORK') }}
            </div>
        </div>

        {{-- WALLET DETAILS CARD GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- CARD 1: WALLET METADATA --}}
            <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors p-6">
                <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-32 h-32 bg-purple-600/10 rounded-full blur-2xl pointer-events-none z-0"></div>
                
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Wallet Metadata') }}</span>
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full flex items-center gap-1.5">
                            <span class="w-1 h-1 rounded-full bg-emerald-400"></span>{{ __('Active') }}
                        </span>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('Wallet Address') }}</div>
                        <div class="font-mono text-xs text-white bg-black/20 border border-white/5 p-3 rounded-xl break-all select-all flex items-start justify-between gap-2">
                            <span>{{ $address }}</span>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" onclick="copyToClipboard('{{ $address }}', '{{ __('Address copied to clipboard') }}')"
                                        class="text-slate-400 hover:text-white p-1 hover:bg-white/5 rounded transition-colors cursor-pointer" title="{{ __('Copy Address') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                    </svg>
                                </button>
                                <button type="button" onclick="showMasterWalletQrModal('Solana', '{{ addslashes($address) }}')"
                                        class="text-slate-400 hover:text-cyan-400 p-1 hover:bg-white/5 rounded transition-colors cursor-pointer" title="{{ __('Show QR Code') }}">
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
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div class="p-3 bg-white/[0.02] border border-white/[0.05] rounded-xl">
                            <div class="text-[8px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Network Type') }}</div>
                            <div class="text-xs font-bold text-white uppercase">{{ __('Solana Mainnet') }}</div>
                        </div>
                        <div class="p-3 bg-white/[0.02] border border-white/[0.05] rounded-xl">
                            <div class="text-[8px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Blockchain') }}</div>
                            <div class="text-xs font-bold text-purple-400 uppercase">{{ __('SOL (Ed25519)') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: CUSTODIAL BALANCE --}}
            <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors p-6">
                <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none z-0"></div>
                
                <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Wallet Balance') }}</span>
                        <button type="button" onclick="triggerManualRefresh()" id="btn-balance-refresh"
                                class="flex items-center gap-1 text-[9px] font-bold text-purple-400 hover:text-purple-300 transition-colors uppercase tracking-wider cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m-3-3v12" />
                            </svg>
                            <span id="refresh-button-text">{{ __('Refresh') }}</span>
                        </button>
                    </div>

                    <div class="my-auto py-2">
                        <div class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-indigo-100 to-purple-300 tracking-tight leading-none font-mono">
                            <span id="sol-balance-text">{{ number_format($balance, 4) }}</span> <span class="text-sm font-black text-slate-400 font-sans">SOL</span>
                        </div>
                        <div class="text-xs text-emerald-400 font-bold tracking-wide mt-2">
                            ≈ <span id="sol-fiat-text">{{ $fiatBalance }}</span> <span class="text-[9px] text-slate-500 font-normal uppercase">({{ getSetting('currency') }})</span>
                        </div>
                    </div>

                    <div class="text-[10px] text-slate-500 italic flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-ping"></span>
                        {{ __('Updating automatically every 10 seconds...') }}
                    </div>
                </div>
            </div>

            {{-- CARD 3: NODE & CONFIGURATION --}}
            <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors p-6">
                <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none z-0"></div>
                
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('RPC Endpoint') }}</span>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('Active Node URL') }}</div>
                        <div class="font-mono text-[10px] text-slate-300 bg-black/20 border border-white/5 p-3 rounded-xl break-all">
                            {{ getSetting('solana_rpc_url', 'https://api.mainnet-beta.solana.com') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Quick Links') }}</div>
                        <div class="flex flex-wrap gap-2">
                            <a href="https://solscan.io/account/{{ $address }}" target="_blank"
                               class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[9px] font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>
                                </svg>
                                {{ __('Solscan') }}
                            </a>
                            <a href="https://explorer.solana.com/address/{{ $address }}" target="_blank"
                               class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[9px] font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>
                                </svg>
                                {{ __('Solana Explorer') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOKEN HOLDINGS SECTION --}}
        @php
            $splTokens = $blockchain->tokens()->whereNotNull('mint_address')->where('status', 'enabled')->get();
            
            if (!function_exists('getTokenColors')) {
                function getTokenColors($symbol) {
                    $symbol = strtoupper($symbol);
                    if (str_starts_with($symbol, 'USDC')) {
                        return ['bg' => 'bg-blue-500/10', 'border' => 'border-blue-500/20', 'text' => 'text-blue-400', 'blur' => 'bg-blue-500/10'];
                    } elseif ($symbol === 'USDT') {
                        return ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-400', 'blur' => 'bg-emerald-500/10'];
                    } elseif ($symbol === 'DAI') {
                        return ['bg' => 'bg-amber-500/10', 'border' => 'border-amber-500/20', 'text' => 'text-amber-400', 'blur' => 'bg-amber-500/10'];
                    } else {
                        return ['bg' => 'bg-purple-500/10', 'border' => 'border-purple-500/20', 'text' => 'text-purple-400', 'blur' => 'bg-purple-500/10'];
                    }
                }
            }
        @endphp

        <div class="space-y-4">
            <h3 class="text-lg font-bold text-white tracking-tight">{{ __('Token Holdings (SPL)') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($splTokens as $token)
                    @php
                        $colors = getTokenColors($token->symbol);
                        $symUpper = strtoupper($token->symbol);
                        $tokenVal = $tokenBalances[$symUpper] ?? 0.0;
                        
                        $siteCurrency = getSetting('currency', 'USD');
                        $tokenConverted = rateConverter($tokenVal, $symUpper, $siteCurrency, 'master');
                        $fiatTokenVal = !empty($tokenConverted) && isset($tokenConverted['converted_amount'])
                            ? showAmount($tokenConverted['converted_amount'])
                            : showAmount($tokenVal);
                    @endphp
                    <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors p-5 flex items-center justify-between gap-4">
                        <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-24 h-24 {{ $colors['blur'] }} rounded-full blur-2xl pointer-events-none z-0"></div>
                        
                        <div class="relative z-10 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl {{ $colors['bg'] }} {{ $colors['border'] }} flex items-center justify-center {{ $colors['text'] }} font-bold text-sm shadow-lg shrink-0">
                                {{ $symUpper }}
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-0.5">{{ $token->name }}</h4>
                                <div class="text-xl font-black text-white font-mono">
                                    <span class="sol-token-balance-val text-2xl font-black text-white font-mono" id="{{ strtolower($token->symbol) }}-balance-text">{{ number_format($tokenVal, 2) }}</span> <span class="text-xs font-bold text-slate-500 font-sans">{{ $symUpper }}</span>
                                </div>
                                <div class="text-[10px] text-emerald-400 font-bold mt-0.5">
                                    ≈ <span class="sol-token-fiat-val" id="{{ strtolower($token->symbol) }}-fiat-text">{{ $fiatTokenVal }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 text-right space-y-1">
                            <span class="text-[8px] font-mono text-slate-600 bg-white/5 border border-white/5 px-2 py-0.5 rounded-full select-all" title="{{ $token->symbol }} Mint Address">
                                {{ __('Mint: ') . substr($token->mint_address, 0, 6) . '...' . substr($token->mint_address, -6) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- TRANSACTION HISTORY SECTION --}}
        <div class="bg-secondary border border-white/5 rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                <div class="flex items-center justify-between border-b border-white/5 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ __('Recent Transfers & Interactions') }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ __('Displaying the 20 most recent transactions on the blockchain.') }}</p>
                    </div>
                    <div class="text-[10px] font-bold text-slate-500 uppercase font-mono flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-ping" id="auto-refresh-ping"></span>
                        {{ __('Parsed Balance Changes') }}
                    </div>
                </div>

                <div id="transactions-container">
                    @if (empty($transactions))
                        <div class="flex flex-col items-center justify-center py-16 text-center space-y-3">
                            <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-white uppercase tracking-wider">{{ __('No Transactions Found') }}</h4>
                            <p class="text-xs text-slate-500 max-w-sm leading-relaxed">
                                {{ __('No on-chain transactions were detected for this wallet. Newly generated wallets will display history once they interact with the network.') }}
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-white/5 text-[10px] uppercase tracking-wider text-slate-500">
                                        <th class="py-3 px-4">{{ __('Date / Time') }}</th>
                                        <th class="py-3 px-4">{{ __('Transaction Signature') }}</th>
                                        <th class="py-3 px-4 text-center">{{ __('Slot') }}</th>
                                        <th class="py-3 px-4 text-center">{{ __('Transfer Type') }}</th>
                                        <th class="py-3 px-4 text-right">{{ __('Change Amount') }}</th>
                                        <th class="py-3 px-4 text-center">{{ __('On-Chain Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5 text-xs text-slate-300 font-medium" id="transactions-table-body">
                                    @foreach ($transactions as $tx)
                                        <tr class="hover:bg-white/[0.01] transition-colors group">
                                            <td class="py-4 px-4 font-mono text-slate-400 whitespace-nowrap">
                                                @if ($tx['block_time'])
                                                    {{ showDateTime($tx['block_time'] * 1000, 'Y-m-d H:i:s') }}
                                                @else
                                                    <span class="text-slate-600 italic">{{ __('Pending/Unknown') }}</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 font-mono">
                                                <div class="flex items-center gap-2">
                                                    <a href="https://solscan.io/tx/{{ $tx['signature'] }}" target="_blank"
                                                       class="text-indigo-400 hover:text-indigo-300 hover:underline break-all truncate max-w-[150px] sm:max-w-[250px]">
                                                        {{ $tx['signature'] }}
                                                    </a>
                                                    <button type="button" onclick="copyToClipboard('{{ $tx['signature'] }}', 'Signature copied to clipboard')"
                                                            class="opacity-0 group-hover:opacity-100 shrink-0 text-slate-500 hover:text-white p-0.5 hover:bg-white/5 rounded transition-all cursor-pointer">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                @if ($tx['memo'])
                                                    <div class="text-[10px] text-slate-500 italic mt-0.5 max-w-sm truncate" title="{{ $tx['memo'] }}">
                                                        <span class="font-bold text-slate-600">Memo:</span> {{ $tx['memo'] }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 text-center font-mono text-slate-400">
                                                {{ number_format($tx['slot']) }}
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                @if ($tx['type'] === 'Incoming')
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 uppercase tracking-wide">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                        </svg>
                                                        {{ __('Incoming') }}
                                                    </span>
                                                @elseif ($tx['type'] === 'Outgoing')
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 uppercase tracking-wide">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                        </svg>
                                                        {{ __('Outgoing') }}
                                                    </span>
                                                @elseif ($tx['type'] === 'Contract Interaction')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-cyan-400 bg-cyan-500/10 border border-cyan-500/20 uppercase tracking-wide">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                                        </svg>
                                                        {{ __('Interaction') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold text-slate-400 bg-white/5 border border-white/5 uppercase tracking-wide">
                                                        {{ __($tx['type']) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 text-right font-mono font-bold whitespace-nowrap">
                                                @php
                                                    $symbol = $tx['token_symbol'] ?? 'SOL';
                                                    $decimals = ($symbol === 'SOL') ? 6 : 2;
                                                @endphp
                                                @if ($tx['type'] === 'Incoming')
                                                    <span class="text-emerald-400">+{{ number_format($tx['amount'], $decimals) }} <span class="text-[9px] text-slate-500">{{ $symbol }}</span></span>
                                                @elseif ($tx['type'] === 'Outgoing')
                                                    <span class="text-rose-400">-{{ number_format($tx['amount'], $decimals) }} <span class="text-[9px] text-slate-500">{{ $symbol }}</span></span>
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                @if ($tx['error'])
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 uppercase tracking-wide" title="{{ json_encode($tx['error']) }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                        </svg>
                                                        {{ __('Failed') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 uppercase tracking-wide">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                        {{ __('Success') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Stateless Pagination Controls --}}
                        <div class="flex items-center justify-between border-t border-white/5 pt-4 mt-4" id="pagination-controls-wrapper">
                            <div class="text-xs text-slate-500">
                                {{ __('Showing page') }} <span class="font-bold text-slate-300">{{ count(explode(',', $nextStackStr ?? '')) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                {{-- Previous Button --}}
                                @if ($hasPrev)
                                    <a href="{{ route('admin.solana-master-wallet.history', $prevBefore ? ['before' => $prevBefore, 'stack' => $prevStackStr] : []) }}"
                                       class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-xs font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:text-white transition-all cursor-pointer"
                                       id="btn-prev-page">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        {{ __('Previous') }}
                                    </a>
                                @else
                                    <span class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/[0.02] border border-white/5 text-xs font-bold text-slate-600 uppercase tracking-wider cursor-not-allowed"
                                          id="btn-prev-page">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        {{ __('Previous') }}
                                    </span>
                                @endif

                                {{-- Next Button --}}
                                @if ($hasMore)
                                    <a href="{{ route('admin.solana-master-wallet.history', ['before' => $nextBefore, 'stack' => $nextStackStr]) }}"
                                       class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 border border-indigo-500/20 text-xs font-bold text-white uppercase tracking-wider hover:bg-indigo-500 hover:shadow-[0_0_15px_rgba(99,102,241,0.4)] transition-all cursor-pointer"
                                       id="btn-next-page">
                                        {{ __('Next') }}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/[0.02] border border-white/5 text-xs font-bold text-slate-600 uppercase tracking-wider cursor-not-allowed"
                                          id="btn-next-page">
                                        {{ __('Next') }}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let isRefreshing = false;

    $(document).ready(function() {
        // Run telemetry auto refresh every 10 seconds
        setInterval(fetchTelemetryData, 10000);
    });

    function triggerManualRefresh() {
        if (isRefreshing) return;
        
        $('#refresh-button-text').text('{{ __('Refreshing...') }}');
        $('#btn-balance-refresh svg').addClass('animate-spin');
        
        fetchTelemetryData(true);
    }

    function fetchTelemetryData(isManual = false) {
        isRefreshing = true;
        
        // Visual indicator on auto-refresh ping
        $('#auto-refresh-ping').removeClass('bg-purple-500').addClass('bg-indigo-400 animate-ping');

        $.ajax({
            url: window.location.href,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                isRefreshing = false;
                $('#refresh-button-text').text('{{ __('Refresh') }}');
                $('#btn-balance-refresh svg').removeClass('animate-spin');
                $('#auto-refresh-ping').removeClass('bg-indigo-400 animate-ping').addClass('bg-purple-500');

                if (response.status === 'success') {
                    // Update Balances
                    $('#sol-balance-text').text(response.balance.toLocaleString(undefined, { minimumFractionDigits: 4, maximumFractionDigits: 4 }));
                    $('#sol-fiat-text').text(response.fiat_balance);

                    // Update SPL token balances dynamically
                    if (response.token_balances && response.fiat_token_balances) {
                        for (let sym in response.token_balances) {
                            let lowerSym = sym.toLowerCase();
                            let balanceVal = response.token_balances[sym];
                            let fiatVal = response.fiat_token_balances[sym];

                            let $elVal = $('#' + lowerSym + '-balance-text');
                            let $elFiat = $('#' + lowerSym + '-fiat-text');

                            if ($elVal.length) {
                                $elVal.text(balanceVal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                            }
                            if ($elFiat.length) {
                                $elFiat.text(fiatVal);
                            }
                        }
                    }

                    // Re-render transactions
                    renderTransactions(response.transactions);
                    updatePaginationControls(response);
                }
            },
            error: function(xhr) {
                isRefreshing = false;
                $('#refresh-button-text').text('{{ __('Refresh') }}');
                $('#btn-balance-refresh svg').removeClass('animate-spin');
                $('#auto-refresh-ping').removeClass('bg-indigo-400 animate-ping').addClass('bg-purple-500');
                console.error('Failed to load wallet telemetry: ', xhr);
            }
        });
    }

    function renderTransactions(transactions) {
        const container = $('#transactions-container');
        if (!container.length) return;

        if (!transactions || transactions.length === 0) {
            container.html(`
                <div class="flex flex-col items-center justify-center py-16 text-center space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider">${'{{ __('No Transactions Found') }}'}</h4>
                    <p class="text-xs text-slate-500 max-w-sm leading-relaxed">
                        ${'{{ __('No on-chain transactions were detected for this wallet. Newly generated wallets will display history once they interact with the network.') }}'}
                    </p>
                </div>
            `);
            return;
        }

        let tableRows = '';
        transactions.forEach(tx => {
            // Date / Time
            let dateVal = '<span class="text-slate-600 italic">' + '{{ __('Pending/Unknown') }}' + '</span>';
            if (tx.block_time) {
                const date = new Date(tx.block_time * 1000);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const seconds = String(date.getSeconds()).padStart(2, '0');
                dateVal = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
            }

            // Memo
            let memoHtml = '';
            if (tx.memo) {
                memoHtml = `<div class="text-[10px] text-slate-500 italic mt-0.5 max-w-sm truncate" title="${escapeHtml(tx.memo)}">
                    <span class="font-bold text-slate-600">Memo:</span> ${escapeHtml(tx.memo)}
                </div>`;
            }

            // Transfer Type Badge
            let typeBadge = '';
            if (tx.type === 'Incoming') {
                typeBadge = `
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        ${'{{ __('Incoming') }}'}
                    </span>
                `;
            } else if (tx.type === 'Outgoing') {
                typeBadge = `
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        ${'{{ __('Outgoing') }}'}
                    </span>
                `;
            } else if (tx.type === 'Contract Interaction') {
                typeBadge = `
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-cyan-400 bg-cyan-500/10 border border-cyan-500/20 uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                        </svg>
                        ${'{{ __('Interaction') }}'}
                    </span>
                `;
            } else {
                typeBadge = `
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold text-slate-400 bg-white/5 border border-white/5 uppercase tracking-wide">
                        ${tx.type}
                    </span>
                `;
            }

            // Amount Column
            let amountHtml = '-';
            const sym = tx.token_symbol || 'SOL';
            const dec = (sym === 'SOL') ? 6 : 2;
            if (tx.type === 'Incoming') {
                amountHtml = `<span class="text-emerald-400">+${tx.amount.toLocaleString(undefined, { minimumFractionDigits: dec, maximumFractionDigits: dec })} <span class="text-[9px] text-slate-500">${sym}</span></span>`;
            } else if (tx.type === 'Outgoing') {
                amountHtml = `<span class="text-rose-400">-${tx.amount.toLocaleString(undefined, { minimumFractionDigits: dec, maximumFractionDigits: dec })} <span class="text-[9px] text-slate-500">${sym}</span></span>`;
            }

            // Status Badge
            let statusBadge = '';
            if (tx.error) {
                statusBadge = `
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 uppercase tracking-wide" title="${escapeHtml(JSON.stringify(tx.error))}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        ${'{{ __('Failed') }}'}
                    </span>
                `;
            } else {
                statusBadge = `
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        ${'{{ __('Success') }}'}
                    </span>
                `;
            }

            tableRows += `
                <tr class="hover:bg-white/[0.01] transition-colors group">
                    <td class="py-4 px-4 font-mono text-slate-400 whitespace-nowrap">${dateVal}</td>
                    <td class="py-4 px-4 font-mono">
                        <div class="flex items-center gap-2">
                            <a href="https://solscan.io/tx/${tx.signature}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 hover:underline break-all truncate max-w-[150px] sm:max-w-[250px]">
                                ${tx.signature}
                            </a>
                            <button type="button" onclick="copyToClipboard('${tx.signature}', 'Signature copied to clipboard')"
                                    class="opacity-0 group-hover:opacity-100 shrink-0 text-slate-500 hover:text-white p-0.5 hover:bg-white/5 rounded transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                </svg>
                            </button>
                        </div>
                        ${memoHtml}
                    </td>
                    <td class="py-4 px-4 text-center font-mono text-slate-400">${tx.slot.toLocaleString()}</td>
                    <td class="py-4 px-4 text-center">${typeBadge}</td>
                    <td class="py-4 px-4 text-right font-mono font-bold whitespace-nowrap">${amountHtml}</td>
                    <td class="py-4 px-4 text-center">${statusBadge}</td>
                </tr>
            `;
        });

        container.html(`
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 text-[10px] uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">${'{{ __('Date / Time') }}'}</th>
                            <th class="py-3 px-4">${'{{ __('Transaction Signature') }}'}</th>
                            <th class="py-3 px-4 text-center">${'{{ __('Slot') }}'}</th>
                            <th class="py-3 px-4 text-center">${'{{ __('Transfer Type') }}'}</th>
                            <th class="py-3 px-4 text-right">${'{{ __('Change Amount') }}'}</th>
                            <th class="py-3 px-4 text-center">${'{{ __('On-Chain Status') }}'}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-xs text-slate-300 font-medium" id="transactions-table-body">
                        ${tableRows}
                    </tbody>
                </table>
            </div>
        `);
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    function updatePaginationControls(res) {
        const wrapper = $('#pagination-controls-wrapper');
        if (!wrapper.length) return;

        // Current page number
        const pageNum = res.next_stack ? res.next_stack.split(',').length : 1;
        wrapper.find('.text-slate-300').text(pageNum);

        // Previous button
        let prevBtnHtml = '';
        if (res.has_prev) {
            let prevUrl = '{{ route("admin.solana-master-wallet.history") }}';
            if (res.prev_before) {
                prevUrl += `?before=${res.prev_before}&stack=${res.prev_stack}`;
            }
            prevBtnHtml = `
                <a href="${prevUrl}"
                   class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-xs font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:text-white transition-all cursor-pointer"
                   id="btn-prev-page">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    ${'{{ __('Previous') }}'}
                </a>
            `;
        } else {
            prevBtnHtml = `
                <span class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/[0.02] border border-white/5 text-xs font-bold text-slate-600 uppercase tracking-wider cursor-not-allowed"
                      id="btn-prev-page">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    ${'{{ __('Previous') }}'}
                </span>
            `;
        }
        $('#btn-prev-page').replaceWith(prevBtnHtml);

        // Next button
        let nextBtnHtml = '';
        if (res.has_more) {
            const nextUrl = `{{ route("admin.solana-master-wallet.history") }}?before=${res.next_before}&stack=${res.next_stack}`;
            nextBtnHtml = `
                <a href="${nextUrl}"
                   class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 border border-indigo-500/20 text-xs font-bold text-white uppercase tracking-wider hover:bg-indigo-500 hover:shadow-[0_0_15px_rgba(99,102,241,0.4)] transition-all cursor-pointer"
                   id="btn-next-page">
                    ${'{{ __('Next') }}'}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            `;
        } else {
            nextBtnHtml = `
                <span class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/[0.02] border border-white/5 text-xs font-bold text-slate-600 uppercase tracking-wider cursor-not-allowed"
                      id="btn-next-page">
                    ${'{{ __('Next') }}'}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            `;
        }
        $('#btn-next-page').replaceWith(nextBtnHtml);
    }
</script>
@endpush
