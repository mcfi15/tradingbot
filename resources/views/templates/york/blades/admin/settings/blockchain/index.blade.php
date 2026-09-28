@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12 font-mono">
        
        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('BLOCKCHAIN SETTINGS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Blockchain Settings') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Configure supported blockchain networks, RPC node connections, and tokens') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="openAddBlockchainModal()"
                    class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('Add Blockchain') }}</span>
                </button>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- MAIN LAYOUT: SIDEBAR + CONTENT --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Navigation Deck --}}
            <div class="lg:col-span-4 xl:col-span-3">
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        @include("templates.$template.blades.admin.settings.partials.sidebar")
                    </div>
                </div>
            </div>

            {{-- Right Content Chassis --}}
            <div class="lg:col-span-8 xl:col-span-9 space-y-6">
                
                {{-- MASTER WALLETS NOTICE BANNER --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-gradient-to-r from-[#090c14] via-[#0c1424] to-[#090c14] p-6 sm:p-8 border border-cyan-500/20 shadow-[0_0_30px_rgba(0,245,255,0.05)] flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 shrink-0 shadow-[0_0_20px_rgba(0,245,255,0.2)]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Master Wallets & Private Keys') }}</h3>
                                    <span class="px-2 py-0.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 text-[9px] font-bold uppercase tracking-wider">{{ __('Admin Hub') }}</span>
                                </div>
                                <p class="text-[11px] text-slate-300 leading-relaxed max-w-2xl">
                                    {{ __('Master receiving addresses, private key generation/reveals, sweeping schedules, and on-chain balances are managed in the Master Wallets Dashboard. Use the button below or click each network\'s direct link.') }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('admin.deposits.master-wallets') }}"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-[10px] tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.25)] shrink-0 active:scale-[0.98] cursor-pointer">
                            <span>{{ __('Go to Master Wallets') }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- BLOCKCHAIN NETWORKS & RPC ROSTER --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-6 text-xs">
                        <div class="pb-3 border-b border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Supported Blockchains & Tokens') }}</h3>
                                <p class="text-[10px] text-slate-400">{{ __('Drag chains to adjust display priority order. Click any network card to expand its settings.') }}</p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <button type="button" onclick="toggleAllBlockchainCards()" id="toggle-all-btn"
                                    class="px-3 py-1.5 rounded-lg bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 font-bold uppercase text-[9px] transition-all cursor-pointer">
                                    {{ __('Expand All') }}
                                </button>
                                <span class="px-2.5 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold">
                                    {{ count($blockchains) }} {{ __('Chains') }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-2.5">
                            {{-- Minimal Self-Hosted / Custom RPC Notice --}}
                            <div class="px-4 py-3 rounded-2xl bg-orange-500/10 border border-orange-500/25 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-[11px] text-orange-200/90">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-orange-400 shrink-0 animate-pulse"></span>
                                    <span>
                                        <strong class="text-orange-300 font-bold">{{ __('Custom & Dedicated RPCs:') }}</strong>
                                        {{ __('You can host your own private nodes or use premium RPC providers (Helius, QuickNode, Alchemy) by pasting your endpoint URL directly into any blockchain network.') }}
                                        <a href="https://foyana.com/contact" target="_blank" rel="noopener noreferrer" class="text-orange-300 hover:text-orange-100 underline font-semibold">{{ __('Contact us') }}</a>
                                        {{ __('if you need help setting up dedicated private nodes.') }}
                                    </span>
                                </div>
                                <a href="https://foyana.com/contact" target="_blank" rel="noopener noreferrer" class="text-orange-400 font-bold hover:text-orange-300 hover:underline shrink-0 text-[10px] uppercase tracking-wider inline-flex items-center gap-1">
                                    <span>{{ __('Get Node Assistance') }}</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>

                            {{-- External Wallet Import Notice --}}
                            <div class="px-4 py-3 rounded-2xl bg-cyan-500/10 border border-cyan-500/25 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-[11px] text-cyan-200/90">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400 shrink-0 animate-pulse"></span>
                                    <span>
                                        <strong class="text-cyan-300 font-bold">{{ __('External Wallet Import (Trust Wallet / MetaMask / Phantom / TronLink):') }}</strong>
                                        {{ __('Admins can reveal and export master wallet private keys to import them directly into Trust Wallet, MetaMask, Phantom, TronLink, or hardware wallets for direct manual control and monitoring.') }}
                                    </span>
                                </div>
                                <a href="{{ route('admin.deposits.master-wallets') }}" class="text-cyan-400 font-bold hover:text-cyan-300 hover:underline shrink-0 text-[10px] uppercase tracking-wider inline-flex items-center gap-1">
                                    <span>{{ __('View Master Wallets') }}</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>

                        {{-- DRAGGABLE BLOCKCHAINS LIST (COLLAPSED BY DEFAULT) --}}
                        <div class="space-y-4" id="blockchain-sortable-list">
                            @foreach ($blockchains as $index => $bc)
                                @php
                                    if ($bc->code === 'solana') {
                                        $masterWalletLink = route('admin.solana-master-wallet.index');
                                    } elseif ($bc->code === 'tron') {
                                        $masterWalletLink = route('admin.tron-master-wallet.index');
                                    } elseif ($bc->code === 'bitcoin' || $bc->code === 'btc') {
                                        $masterWalletLink = route('admin.bitcoin-master-wallet.index');
                                    } else {
                                        $masterWalletLink = route('admin.evm-master-wallet.index', ['blockchain' => $bc->code]);
                                    }
                                @endphp
                                <div class="blockchain-card rounded-2xl bg-white/[0.015] border border-white/[0.06] hover:border-white/[0.12] transition-all overflow-hidden" 
                                     id="blockchain-panel-{{ $bc->id }}" 
                                     data-id="{{ $bc->id }}">
                                    
                                    {{-- Collapsed Card Header --}}
                                    <div class="p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div class="flex items-center gap-3 min-w-0">
                                            {{-- Drag Handle --}}
                                            <div class="drag-handle p-2 text-slate-500 hover:text-cyan-400 cursor-grab active:cursor-grabbing shrink-0" title="{{ __('Drag to reorder priority') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                                </svg>
                                            </div>

                                            {{-- Priority Rank Tag --}}
                                            <span class="rank-badge px-2 py-0.5 rounded-md bg-white/[0.04] border border-white/[0.08] text-[9px] font-bold text-slate-400 shrink-0 font-mono">
                                                #<span class="rank-num">{{ $index + 1 }}</span>
                                            </span>

                                            {{-- Blockchain Logo --}}
                                            <div class="w-9 h-9 rounded-xl bg-[#05070d] border border-white/[0.08] flex items-center justify-center text-cyan-400 font-bold shrink-0 overflow-hidden">
                                                @php $blockchainLogo = !empty($bc->logo) ? asset($bc->logo) : asset('assets/images/tokens/' . strtolower($bc->code) . '.png'); @endphp
                                                <img src="{{ $blockchainLogo }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="{{ $bc->name }}" class="w-full h-full object-contain p-1">
                                                <span class="hidden items-center justify-center font-black text-[10px]">{{ strtoupper(substr($bc->code, 0, 3)) }}</span>
                                            </div>

                                            {{-- Title & Info --}}
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h4 class="text-sm font-bold text-white truncate">{{ $bc->name }}</h4>
                                                    <span class="px-2 py-0.5 rounded bg-white/[0.04] text-slate-400 font-mono text-[9px]">{{ $bc->code }}</span>
                                                    <span class="px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 font-mono text-[9px]">
                                                        {{ $bc->tokens->count() }} {{ __('Tokens') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Actions & Toggle --}}
                                        <div class="flex flex-wrap items-center gap-2.5 sm:gap-3 shrink-0 self-end md:self-auto">
                                            {{-- Master Wallet Link --}}
                                            <a href="{{ $masterWalletLink }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-400 hover:text-black text-cyan-400 font-bold uppercase text-[9px] transition-all cursor-pointer border border-cyan-500/20 shadow-[0_0_10px_rgba(0,245,255,0.08)]">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                </svg>
                                                <span>{{ __('Master Wallet & Keys') }}</span>
                                                <svg class="w-2.5 h-2.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                            </a>

                                            {{-- Status Toggle --}}
                                            <label class="relative inline-flex items-center cursor-pointer" title="{{ __('Toggle network active state') }}">
                                                <input type="checkbox" class="blockchain-status-toggle sr-only peer" data-id="{{ $bc->id }}" {{ $bc->status === 'enabled' ? 'checked' : '' }}>
                                                <div class="w-9 h-5 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-cyan-400"></div>
                                            </label>

                                            {{-- Expand / Collapse Button --}}
                                            <button type="button" onclick="toggleBlockchainCard({{ $bc->id }})"
                                                class="card-toggle-btn px-3 py-1.5 rounded-lg bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 font-bold uppercase text-[9px] transition-all flex items-center gap-1.5 cursor-pointer">
                                                <span class="toggle-text">{{ __('Details') }}</span>
                                                <svg class="w-3 h-3 chevron-icon transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Collapsible Body (Hidden by default) --}}
                                    <div class="blockchain-body hidden p-5 pt-0 border-t border-white/[0.04] space-y-5 bg-black/20" id="blockchain-body-{{ $bc->id }}">
                                        
                                        {{-- RPC Node & Controls --}}
                                        <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="p-3 bg-[#05070d] rounded-xl border border-white/[0.06] flex-1 min-w-0">
                                                <span class="text-[8px] uppercase font-bold text-slate-500 block mb-0.5">{{ __('Active RPC Node URL') }}</span>
                                                <code class="text-cyan-300 font-mono text-[10px] truncate block select-all">{{ $bc->rpc_url }}</code>
                                            </div>

                                            <button type="button" onclick="openEditBlockchainModal({{ json_encode($bc) }})"
                                                class="px-4 py-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 font-bold uppercase text-[9px] transition-all shrink-0 flex items-center justify-center gap-1.5 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                <span>{{ __('Edit Network') }}</span>
                                            </button>
                                        </div>

                                        @if (!empty($bc->instructions))
                                            <div class="p-3 bg-white/[0.01] rounded-xl border border-white/[0.04]">
                                                <span class="text-[8px] uppercase font-bold text-slate-500 block mb-0.5">{{ __('Deposit Instructions') }}</span>
                                                <p class="text-slate-300 text-[10px] leading-relaxed">{{ $bc->instructions }}</p>
                                            </div>
                                        @endif

                                        {{-- Tokens Sub-Deck --}}
                                        <div class="space-y-3 pt-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[9px] uppercase font-black text-slate-400 tracking-wider">{{ __('Supported Tokens') }} ({{ $bc->tokens->count() }})</span>
                                                <button type="button" onclick="openAddTokenModal({{ $bc->id }}, '{{ $bc->name }}')"
                                                    class="px-2.5 py-1 rounded-md bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 text-[9px] font-bold uppercase tracking-wider transition-all cursor-pointer">
                                                    + {{ __('Add Token') }}
                                                </button>
                                            </div>

                                            @if ($bc->tokens->count() === 0)
                                                <div class="p-4 rounded-xl bg-white/[0.01] border border-white/[0.04] text-center text-slate-500 text-[10px]">
                                                    {{ __('No tokens configured for this network yet. Click "+ Add Token" above.') }}
                                                </div>
                                            @else
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    @foreach ($bc->tokens as $token)
                                                        <div class="p-3 bg-[#05070d] rounded-xl border border-white/[0.06] flex items-center justify-between gap-3" id="token-row-{{ $token->id }}">
                                                            <div class="flex items-center gap-2.5 min-w-0">
                                                                <div class="w-7 h-7 rounded-lg bg-white/[0.03] flex items-center justify-center text-cyan-400 text-xs font-black shrink-0 overflow-hidden">
                                                                    @php $tokenLogo = !empty($token->logo) ? asset($token->logo) : asset('assets/images/tokens/' . strtolower($token->symbol) . '.png'); @endphp
                                                                    <img src="{{ $tokenLogo }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="{{ $token->symbol }}" class="w-full h-full object-contain p-0.5">
                                                                    <span class="hidden font-bold text-[8px]">{{ $token->symbol }}</span>
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <span class="text-white font-bold block text-[11px] truncate">{{ $token->name }} ({{ $token->symbol }})</span>
                                                                    <span class="text-[8px] text-slate-500 font-mono block truncate">{{ $token->mint_address ? substr($token->mint_address, 0, 10) . '...' : __('Native Gas') }}</span>
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center gap-2 shrink-0">
                                                                <label class="relative inline-flex items-center cursor-pointer">
                                                                    <input type="checkbox" class="token-status-toggle sr-only peer" data-id="{{ $token->id }}" {{ $token->status === 'enabled' ? 'checked' : '' }}>
                                                                    <div class="w-7 h-4 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-cyan-400"></div>
                                                                </label>

                                                                <button type="button" onclick="openEditTokenModal({{ json_encode($token) }})"
                                                                    class="p-1 text-slate-400 hover:text-white cursor-pointer" title="{{ __('Edit') }}">
                                                                    ✎
                                                                </button>

                                                                @if ($token->symbol !== 'SOL' && $token->symbol !== 'BTC' && $token->symbol !== 'ETH')
                                                                    <button type="button" onclick="deleteToken({{ $token->id }})"
                                                                        class="p-1 text-rose-400 hover:text-rose-300 cursor-pointer" title="{{ __('Delete') }}">
                                                                        ✕
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                    </div>

                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Add/Edit Blockchain Modal -->
    <div id="blockchainModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-md flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] w-full max-w-lg">
            <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8 space-y-5 text-xs font-mono">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider" id="blockchainModalTitle">{{ __('Configure Blockchain') }}</h3>
                    <button type="button" onclick="closeModal('blockchainModal')" class="text-slate-400 hover:text-white cursor-pointer">✕</button>
                </div>

                <form id="blockchainModalForm" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Network Name') }}</label>
                            <input type="text" name="name" id="blockchain_name" required placeholder="Solana Mainnet"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Chain Code') }}</label>
                            <input type="text" name="code" id="blockchain_code" required placeholder="solana"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('RPC Node URL') }}</label>
                            <span class="text-[9px] text-slate-500">{{ __('Supports self-hosted & custom providers') }}</span>
                        </div>
                        <input type="url" name="rpc_url" id="blockchain_rpc_url" required placeholder="https://mainnet.helius-rpc.com/?api-key=..."
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none font-mono">
                        <p class="text-[9px] text-slate-500">
                            {{ __('Paste your private node or provider endpoint URL (QuickNode, Helius, Alchemy, etc.). Contact support for help configuring dedicated nodes.') }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Deposit Instructions') }}</label>
                        <textarea name="instructions" id="blockchain_instructions" rows="2" placeholder="Deposit network notes or instructions for users..."
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Live Explorer Base URL') }}</label>
                            <input type="text" name="explorer_url_live" id="blockchain_explorer_url_live" placeholder="https://solscan.io/tx/{tx}"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Devnet Explorer Base URL') }}</label>
                            <input type="text" name="explorer_url_devnet" id="blockchain_explorer_url_devnet" placeholder="https://solscan.io/tx/{tx}?cluster=devnet"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                        </div>
                    </div>

                    <div class="space-y-2" id="blockchainStatusGroup">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Network Status') }}</label>
                        <select name="status" id="blockchain_status" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                            <option value="enabled">{{ __('Enabled') }}</option>
                            <option value="disabled">{{ __('Disabled') }}</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-white/[0.06] flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal('blockchainModal')" class="px-5 py-2.5 rounded-xl bg-white/[0.04] text-slate-400 font-bold uppercase text-[10px] tracking-wider cursor-pointer">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-[10px] tracking-wider transition-all cursor-pointer">{{ __('Save Network') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add/Edit Token Modal -->
    <div id="tokenModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-md flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] w-full max-w-md">
            <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8 space-y-5 text-xs font-mono">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider" id="tokenModalTitle">{{ __('Configure Token') }}</h3>
                    <button type="button" onclick="closeModal('tokenModal')" class="text-slate-400 hover:text-white cursor-pointer">✕</button>
                </div>

                <form id="tokenModalForm" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="blockchain_id" id="token_blockchain_id">

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Symbol') }}</label>
                            <input type="text" name="symbol" id="token_symbol" required placeholder="USDT"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Token Name') }}</label>
                            <input type="text" name="name" id="token_name" required placeholder="Tether USD"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Contract / Mint Address (Leave empty for Native Token)') }}</label>
                        <input type="text" name="mint_address" id="token_mint_address" placeholder="Es9vMFrzaCERmJfrF4H2FYD4KCoNkY11McCe8BenwNYB"
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none font-mono">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Decimals') }}</label>
                            <input type="number" name="decimals" id="token_decimals" required value="6" min="0" max="18"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                        </div>

                        <div class="space-y-2" id="tokenStatusGroup">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Status') }}</label>
                            <select name="status" id="token_status" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                <option value="enabled">{{ __('Enabled') }}</option>
                                <option value="disabled">{{ __('Disabled') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/[0.06] flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal('tokenModal')" class="px-5 py-2.5 rounded-xl bg-white/[0.04] text-slate-400 font-bold uppercase text-[10px] tracking-wider cursor-pointer">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-[10px] tracking-wider transition-all cursor-pointer">{{ __('Save Token') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function closeModal(id) {
            $('#' + id).addClass('hidden');
        }

        function toggleBlockchainCard(id) {
            const $body = $(`#blockchain-body-${id}`);
            const $card = $(`#blockchain-panel-${id}`);
            const $chevron = $card.find('.chevron-icon');
            const $toggleText = $card.find('.toggle-text');

            if ($body.hasClass('hidden')) {
                $body.removeClass('hidden');
                $chevron.addClass('rotate-180');
                $toggleText.text('{{ __('Close') }}');
            } else {
                $body.addClass('hidden');
                $chevron.removeClass('rotate-180');
                $toggleText.text('{{ __('Details') }}');
            }
        }

        let allExpanded = false;
        function toggleAllBlockchainCards() {
            allExpanded = !allExpanded;
            if (allExpanded) {
                $('.blockchain-body').removeClass('hidden');
                $('.chevron-icon').addClass('rotate-180');
                $('.toggle-text').text('{{ __('Close') }}');
                $('#toggle-all-btn').text('{{ __('Collapse All') }}');
            } else {
                $('.blockchain-body').addClass('hidden');
                $('.chevron-icon').removeClass('rotate-180');
                $('.toggle-text').text('{{ __('Details') }}');
                $('#toggle-all-btn').text('{{ __('Expand All') }}');
            }
        }

        function updateRankNumbers() {
            $('#blockchain-sortable-list > .blockchain-card').each(function(index) {
                $(this).find('.rank-num').text(index + 1);
            });
        }

        $(document).ready(function() {
            // Initialize SortableJS for Draggable Priority
            const sortableContainer = document.getElementById('blockchain-sortable-list');
            if (sortableContainer) {
                new Sortable(sortableContainer, {
                    animation: 200,
                    handle: '.drag-handle',
                    ghostClass: 'opacity-40',
                    chosenClass: 'scale-[1.01]',
                    dragClass: 'shadow-[0_0_30px_rgba(0,245,255,0.2)]',
                    onEnd: function() {
                        const items = [];
                        $('#blockchain-sortable-list > .blockchain-card').each(function() {
                            items.push($(this).data('id'));
                        });

                        updateRankNumbers();

                        $.ajax({
                            url: "{{ route('admin.settings.blockchain.blockchains.reorder') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                items: items
                            },
                            success: function(response) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: response.message || '{{ __('Priority order updated.') }}',
                                    showConfirmButton: false,
                                    timer: 1200,
                                    customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: '{{ __('Failed to save priority order.') }}',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono text-xs' }
                                });
                            }
                        });
                    }
                });
            }

            // Blockchain status toggle
            $(document).on('change', '.blockchain-status-toggle', function() {
                const $toggle = $(this);
                const id = $toggle.data('id');
                $.post(`{{ url('admin/settings/blockchain/blockchains/toggle-status') }}/${id}`, { _token: '{{ csrf_token() }}' })
                    .done(function(res) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1200, customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' } });
                    })
                    .fail(function() {
                        $toggle.prop('checked', !$toggle.is(':checked'));
                    });
            });

            // Token status toggle
            $(document).on('change', '.token-status-toggle', function() {
                const $toggle = $(this);
                const id = $toggle.data('id');
                $.post(`{{ url('admin/settings/blockchain/tokens/toggle-status') }}/${id}`, { _token: '{{ csrf_token() }}' })
                    .done(function(res) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1200, customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' } });
                    })
                    .fail(function() {
                        $toggle.prop('checked', !$toggle.is(':checked'));
                    });
            });
        });

        function openAddBlockchainModal() {
            $('#blockchainModalTitle').text('{{ __('Add Blockchain Network') }}');
            $('#blockchainModalForm').attr('action', '{{ route('admin.settings.blockchain.blockchains.store') }}');
            $('#blockchain_name').val('');
            $('#blockchain_code').val('').prop('readonly', false);
            $('#blockchain_rpc_url').val('');
            $('#blockchain_instructions').val('');
            $('#blockchain_explorer_url_live').val('');
            $('#blockchain_explorer_url_devnet').val('');
            $('#blockchainStatusGroup').show();
            $('#blockchainModal').removeClass('hidden');
        }

        function openEditBlockchainModal(bc) {
            $('#blockchainModalTitle').text('{{ __('Edit Blockchain Network') }}');
            $('#blockchainModalForm').attr('action', `{{ url('admin/settings/blockchain/blockchains/update') }}/${bc.id}`);
            $('#blockchain_name').val(bc.name);
            $('#blockchain_code').val(bc.code).prop('readonly', true);
            $('#blockchain_rpc_url').val(bc.rpc_url);
            $('#blockchain_instructions').val(bc.instructions || '');
            $('#blockchain_explorer_url_live').val(bc.explorer_url_live || '');
            $('#blockchain_explorer_url_devnet').val(bc.explorer_url_devnet || '');
            $('#blockchainStatusGroup').hide();
            $('#blockchainModal').removeClass('hidden');
        }

        function openAddTokenModal(blockchainId, blockchainName) {
            $('#tokenModalTitle').text('{{ __('Add Token for') }} ' + blockchainName);
            $('#tokenModalForm').attr('action', '{{ route('admin.settings.blockchain.tokens.store') }}');
            $('#token_blockchain_id').val(blockchainId);
            $('#token_symbol').val('');
            $('#token_name').val('');
            $('#token_mint_address').val('');
            $('#token_decimals').val('6');
            $('#tokenStatusGroup').show();
            $('#tokenModal').removeClass('hidden');
        }

        function openEditTokenModal(token) {
            $('#tokenModalTitle').text('{{ __('Edit Token') }}');
            $('#tokenModalForm').attr('action', `{{ url('admin/settings/blockchain/tokens/update') }}/${token.id}`);
            $('#token_blockchain_id').val(token.blockchain_id);
            $('#token_symbol').val(token.symbol);
            $('#token_name').val(token.name);
            $('#token_mint_address').val(token.mint_address || '');
            $('#token_decimals').val(token.decimals);
            $('#tokenStatusGroup').hide();
            $('#tokenModal').removeClass('hidden');
        }

        function deleteToken(id) {
            Swal.fire({
                title: '{{ __('Delete Token?') }}',
                text: '{{ __('This token will be removed from the blockchain roster.') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#1e293b',
                confirmButtonText: '{{ __('Delete') }}',
                customClass: { popup: 'bg-[#090c14] border border-white/10 text-white font-mono' }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`{{ url('admin/settings/blockchain/tokens/delete') }}/${id}`, { _token: '{{ csrf_token() }}' })
                        .done(function() {
                            $(`#token-row-${id}`).fadeOut(300, function() { $(this).remove(); });
                        });
                }
            });
        }
    </script>
@endpush
