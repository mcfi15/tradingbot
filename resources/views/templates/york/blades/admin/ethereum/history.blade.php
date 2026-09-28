@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12">
        {{-- Header & Back Button --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.deposits.master-wallets') }}" 
                       class="p-2 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-slate-300 hover:text-white transition-all active:scale-95 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-indigo-200 to-indigo-400 tracking-tight leading-tight">
                            {{ __('Ethereum Master Wallet Details') }}
                        </h2>
                        <p class="text-indigo-200/60 font-mono text-[9px] md:text-xs mt-0.5 tracking-widest uppercase">
                            {{ __('Real-time On-chain Wallet Telemetry') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 text-[10px] text-emerald-400/80 font-mono bg-emerald-500/5 border border-emerald-500/10 px-3 py-1.5 rounded-xl">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ __('CONNECTED TO ETHEREUM NETWORK') }}
            </div>
        </div>

        {{-- WALLET DETAILS CARD GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- CARD 1: WALLET METADATA --}}
            <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors p-6">
                <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-32 h-32 bg-indigo-600/10 rounded-full blur-2xl pointer-events-none z-0"></div>
                
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
                                <button type="button" onclick="showMasterWalletQrModal('Ethereum', '{{ addslashes($address) }}')"
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
                            <div class="text-xs font-bold text-white uppercase">{{ __('Ethereum') }}</div>
                        </div>
                        <div class="p-3 bg-white/[0.02] border border-white/[0.05] rounded-xl">
                            <div class="text-[8px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Blockchain') }}</div>
                            <div class="text-xs font-bold text-indigo-400 uppercase">{{ __('ETH (Secp256k1)') }}</div>
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
                                class="flex items-center gap-1 text-[9px] font-bold text-indigo-400 hover:text-indigo-300 transition-colors uppercase tracking-wider cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m-3-3v12" />
                            </svg>
                            <span id="refresh-button-text">{{ __('Refresh') }}</span>
                        </button>
                    </div>

                    <div class="my-auto py-2">
                        <div class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-indigo-100 to-indigo-300 tracking-tight leading-none font-mono">
                            <span id="eth-balance-text">{{ number_format($balance, 4) }}</span> <span class="text-sm font-black text-slate-400 font-sans">ETH</span>
                        </div>
                        <div class="text-xs text-emerald-400 font-bold tracking-wide mt-2">
                            ≈ <span id="eth-fiat-text">{{ $fiatBalance }}</span> <span class="text-[9px] text-slate-500 font-normal uppercase">({{ getSetting('currency') }})</span>
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
                            @php
                                $blockchain = \App\Models\Blockchain::where('code', 'ethereum')->first();
                                $rpcUrl = $blockchain?->rpc_url ?? 'https://cloudflare-eth.com';
                            @endphp
                            {{ $rpcUrl }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Quick Links') }}</div>
                        <div class="flex flex-wrap gap-2">
                            <a href="https://etherscan.io/address/{{ $address }}" target="_blank"
                               class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[9px] font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>
                                </svg>
                                {{ __('Etherscan') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOKEN HOLDINGS SECTION --}}
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-white tracking-tight">{{ __('Token Holdings (ERC-20)') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- USDC Card --}}
                <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors p-5 flex items-center justify-between gap-4">
                    <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl pointer-events-none z-0"></div>
                    
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 font-bold text-sm shadow-[0_0_15px_rgba(59,130,246,0.15)]">
                            USDC
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-0.5">{{ __('USD Coin') }}</h4>
                            <div class="text-2xl font-black text-white font-mono">
                                <span id="usdc-balance-text">{{ number_format($tokenBalances['USDC'] ?? 0, 2) }}</span> <span class="text-xs font-bold text-slate-500 font-sans">USDC</span>
                            </div>
                            <div class="text-[10px] text-emerald-400 font-bold mt-0.5">
                                ≈ <span id="usdc-fiat-text">{{ $fiatUsdc }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 text-right space-y-1">
                        <span class="text-[8px] font-mono text-slate-600 bg-white/5 border border-white/5 px-2 py-0.5 rounded-full select-all" title="USDC Contract Address">
                            {{ __('ERC-20 Contract') }}
                        </span>
                    </div>
                </div>

                {{-- USDT Card --}}
                <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors p-5 flex items-center justify-between gap-4">
                    <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none z-0"></div>

                    <div class="relative z-10 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold text-sm shadow-[0_0_15px_rgba(16,185,129,0.15)]">
                            USDT
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-0.5">{{ __('Tether USD') }}</h4>
                            <div class="text-2xl font-black text-white font-mono">
                                <span id="usdt-balance-text">{{ number_format($tokenBalances['USDT'] ?? 0, 2) }}</span> <span class="text-xs font-bold text-slate-500 font-sans">USDT</span>
                            </div>
                            <div class="text-[10px] text-emerald-400 font-bold mt-0.5">
                                ≈ <span id="usdt-fiat-text">{{ $fiatUsdt }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 text-right space-y-1">
                        <span class="text-[8px] font-mono text-slate-600 bg-white/5 border border-white/5 px-2 py-0.5 rounded-full select-all" title="USDT Contract Address">
                            {{ __('ERC-20 Contract') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- TRANSACTION HISTORY SECTION --}}
        <div class="bg-secondary border border-white/5 rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                <div class="flex items-center justify-between border-b border-white/5 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ __('Recent Swept Deposits') }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ __('Displaying recent processed and swept user deposits on Ethereum.') }}</p>
                    </div>
                    <div class="text-[10px] font-bold text-slate-500 uppercase font-mono flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-ping" id="auto-refresh-ping"></span>
                        {{ __('Telemetry Active') }}
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
                                {{ __('No local completed deposits were found for Ethereum.') }}
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-white/5 text-[10px] uppercase tracking-wider text-slate-500">
                                        <th class="py-3 px-4">{{ __('Date / Time') }}</th>
                                        <th class="py-3 px-4">{{ __('Transaction Hash') }}</th>
                                        <th class="py-3 px-4 text-center">{{ __('Transfer Type') }}</th>
                                        <th class="py-3 px-4 text-right">{{ __('Amount') }}</th>
                                        <th class="py-3 px-4 text-center">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5 text-xs text-slate-300 font-medium" id="transactions-table-body">
                                    @foreach ($transactions as $tx)
                                        <tr class="hover:bg-white/[0.01] transition-colors group">
                                            <td class="py-4 px-4 font-mono text-slate-400 whitespace-nowrap">
                                                {{ showDateTime($tx['block_time'] * 1000, 'Y-m-d H:i:s') }}
                                            </td>
                                            <td class="py-4 px-4 font-mono">
                                                <div class="flex items-center gap-2">
                                                    <a href="https://etherscan.io/tx/{{ $tx['signature'] }}" target="_blank"
                                                       class="text-indigo-400 hover:text-indigo-300 hover:underline break-all truncate max-w-[150px] sm:max-w-[250px]">
                                                        {{ $tx['signature'] }}
                                                    </a>
                                                    <button type="button" onclick="copyToClipboard('{{ $tx['signature'] }}', 'Hash copied to clipboard')"
                                                            class="opacity-0 group-hover:opacity-100 shrink-0 text-slate-500 hover:text-white p-0.5 hover:bg-white/5 rounded transition-all cursor-pointer">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                @if ($tx['memo'])
                                                    <div class="text-[10px] text-slate-500 italic mt-0.5 max-w-sm truncate" title="{{ $tx['memo'] }}">
                                                        <span class="font-bold text-slate-600">Ref:</span> {{ $tx['memo'] }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                @if (($tx['type'] ?? 'Incoming') === 'Outgoing')
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold text-red-400 bg-red-500/10 border border-red-500/20 uppercase tracking-wide">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                        </svg>
                                                        {{ __('Outgoing') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 uppercase tracking-wide">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                        </svg>
                                                        {{ __('Incoming') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 text-right font-mono font-bold whitespace-nowrap">
                                                @php
                                                    $symbol = $tx['token_symbol'];
                                                    $decimals = ($symbol === 'ETH') ? 6 : 2;
                                                    $isOutgoing = ($tx['type'] ?? 'Incoming') === 'Outgoing';
                                                @endphp
                                                <span class="{{ $isOutgoing ? 'text-red-400' : 'text-emerald-400' }}">
                                                    {{ $isOutgoing ? '-' : '+' }}{{ number_format($tx['amount'], $decimals) }}
                                                    <span class="text-[9px] text-slate-500">{{ $symbol }}</span>
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 uppercase tracking-wide">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ __('Success') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Controls --}}
                        <div class="flex items-center justify-between border-t border-white/5 pt-4 mt-4" id="pagination-controls-wrapper">
                            <div class="text-xs text-slate-500">
                                {{ __('Showing page') }} <span class="font-bold text-slate-300" id="current-page-num">{{ $page }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                {{-- Previous Button --}}
                                @if ($hasPrev)
                                    <a href="{{ route('admin.ethereum-master-wallet.history', ['page' => $page - 1]) }}"
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
                                    <a href="{{ route('admin.ethereum-master-wallet.history', ['page' => $page + 1]) }}"
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
                    $('#eth-balance-text').text(response.balance.toLocaleString(undefined, { minimumFractionDigits: 4, maximumFractionDigits: 4 }));
                    $('#eth-fiat-text').text(response.fiat_balance);

                    $('#usdc-balance-text').text(response.token_balances.USDC.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#usdc-fiat-text').text(response.fiat_usdc);

                    $('#usdt-balance-text').text(response.token_balances.USDT.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#usdt-fiat-text').text(response.fiat_usdt);

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
                        ${'{{ __('No local completed deposits were found for Ethereum.') }}'}
                    </p>
                </div>
            `);
            return;
        }

        let tableRows = '';
        transactions.forEach(tx => {
            // Date / Time
            const date = new Date(tx.block_time * 1000);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            const dateVal = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

            // Ref
            let memoHtml = '';
            if (tx.memo) {
                memoHtml = `<div class="text-[10px] text-slate-500 italic mt-0.5 max-w-sm truncate" title="${escapeHtml(tx.memo)}">
                    <span class="font-bold text-slate-600">Ref:</span> ${escapeHtml(tx.memo)}
                </div>`;
            }

            // Amount Column
            const sym = tx.token_symbol || 'ETH';
            const dec = (sym === 'ETH') ? 6 : 2;
            const isOutgoing = tx.type === 'Outgoing';
            const amountClass = isOutgoing ? 'text-red-400' : 'text-emerald-400';
            const amountSign = isOutgoing ? '-' : '+';
            const amountHtml = `<span class="${amountClass}">${amountSign}${tx.amount.toLocaleString(undefined, { minimumFractionDigits: dec, maximumFractionDigits: dec })} <span class="text-[9px] text-slate-500">${sym}</span></span>`;

            // Type Column
            let typeHtml = '';
            if (isOutgoing) {
                typeHtml = `
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold text-red-400 bg-red-500/10 border border-red-500/20 uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        ${'{{ __('Outgoing') }}'}
                    </span>
                `;
            } else {
                typeHtml = `
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        ${'{{ __('Incoming') }}'}
                    </span>
                `;
            }

            tableRows += `
                <tr class="hover:bg-white/[0.01] transition-colors group">
                    <td class="py-4 px-4 font-mono text-slate-400 whitespace-nowrap">${dateVal}</td>
                    <td class="py-4 px-4 font-mono">
                        <div class="flex items-center gap-2">
                            <a href="https://etherscan.io/tx/${tx.signature}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 hover:underline break-all truncate max-w-[150px] sm:max-w-[250px]">
                                ${tx.signature}
                            </a>
                            <button type="button" onclick="copyToClipboard('${tx.signature}', 'Hash copied to clipboard')"
                                    class="opacity-0 group-hover:opacity-100 shrink-0 text-slate-500 hover:text-white p-0.5 hover:bg-white/5 rounded transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                </svg>
                            </button>
                        </div>
                        ${memoHtml}
                    </td>
                    <td class="py-4 px-4 text-center">
                        ${typeHtml}
                    </td>
                    <td class="py-4 px-4 text-right font-mono font-bold whitespace-nowrap">${amountHtml}</td>
                    <td class="py-4 px-4 text-center">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 uppercase tracking-wide">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            ${'{{ __('Success') }}'}
                        </span>
                    </td>
                </tr>
            `;
        });

        container.html(`
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 text-[10px] uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">${'{{ __('Date / Time') }}'}</th>
                            <th class="py-3 px-4">${'{{ __('Transaction Hash') }}'}</th>
                            <th class="py-3 px-4 text-center">${'{{ __('Transfer Type') }}'}</th>
                            <th class="py-3 px-4 text-right">${'{{ __('Amount') }}'}</th>
                            <th class="py-3 px-4 text-center">${'{{ __('Status') }}'}</th>
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
        const pageNum = res.current_page;
        wrapper.find('#current-page-num').text(pageNum);

        // Previous button
        let prevBtnHtml = '';
        if (res.has_prev) {
            const prevUrl = `{{ route("admin.ethereum-master-wallet.history") }}?page=${pageNum - 1}`;
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
            const nextUrl = `{{ route("admin.ethereum-master-wallet.history") }}?page=${pageNum + 1}`;
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
