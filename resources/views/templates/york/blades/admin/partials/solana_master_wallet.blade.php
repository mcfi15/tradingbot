@php
    $blockchain = \App\Models\Blockchain::where('code', 'solana')->first();
    $masterAddress = $blockchain?->master_wallet_address;
    $masterPrivateKeyEncrypted = $blockchain?->master_private_key;
    $isWalletValid = false;
    $decryptError = false;

    if ($masterAddress) {
        if ($masterPrivateKeyEncrypted) {
            try {
                \Illuminate\Support\Facades\Crypt::decryptString($masterPrivateKeyEncrypted);
                $isWalletValid = true;
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                $decryptError = true;
            }
        } else {
            $decryptError = true;
        }
    }
    $instructions = $blockchain?->instructions ?? __('To enable auto-sweeping of user deposits, make sure this Master Wallet address is funded with some SOL (for transaction fees). You must also send a tiny amount of USDC and USDT to this master wallet address to initialize and activate their respective token accounts on the Solana network.');
@endphp

<div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors mt-6">
    {{-- Decorative Background Blurs --}}
    <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-48 h-48 bg-purple-600/15 rounded-full blur-3xl pointer-events-none z-0"></div>
    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-36 h-36 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none z-0"></div>

    <div class="relative z-10 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                {{-- Solana Stylized SVG Icon --}}
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-purple-600 to-emerald-500 p-0.5 flex items-center justify-center shadow-lg shadow-purple-500/10">
                    <div class="w-full h-full bg-secondary-dark rounded-[10px] flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-400" viewBox="0 0 397 311" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M64.6 237.9c-2.4-2.4-5.7-3.8-9.2-3.8H4c-2.9 0-4.3 3.5-2.2 5.6l62.8 62.8c2.4 2.4 5.7 3.8 9.2 3.8h51.4c2.9 0 4.3-3.5 2.2-5.6L64.6 237.9z" fill="url(#solana-gradient)" />
                            <path d="M332.4 73.1c2.4 2.4 5.7 3.8 9.2 3.8h51.4c2.9 0 4.3-3.5 2.2-5.6L332.4 8.5c-2.4-2.4-5.7-3.8-9.2-3.8h-51.4c-2.9 0-4.3 3.5-2.2 5.6l62.8 62.8z" fill="url(#solana-gradient)" />
                            <path d="M64.6 8.5L1.8 71.3c-2.1 2.1-.7 5.6 2.2 5.6h51.4c3.5 0 6.8-1.4 9.2-3.8l62.8-62.8c2.1-2.1.7-5.6-2.2-5.6H73.8c-3.5 0-6.8 1.4-9.2 3.8z" fill="url(#solana-gradient)" />
                            <path d="M332.4 237.9l-62.8 62.8c-2.1 2.1-.7 5.6 2.2 5.6h51.4c3.5 0 6.8-1.4 9.2-3.8l62.8-62.8c2.1-2.1.7-5.6-2.2-5.6h-51.4c-3.5 0-6.8 1.4-9.2 3.8z" fill="url(#solana-gradient)" />
                            <path d="M198.5 123.2l-62.8 62.8c-2.1 2.1-.7 5.6 2.2 5.6h51.4c3.5 0 6.8-1.4 9.2-3.8l62.8-62.8c2.1-2.1.7-5.6-2.2-5.6h-51.4c-3.5 0-6.8 1.4-9.2 3.8z" fill="url(#solana-gradient)" />
                            <path d="M198.5 187.8c-2.4-2.4-5.7-3.8-9.2-3.8h-51.4c-2.9 0-4.3 3.5-2.2 5.6l62.8 62.8c2.4 2.4 5.7 3.8 9.2 3.8h51.4c-2.9 0-4.3-3.5-2.2-5.6l-62.8-62.8z" fill="url(#solana-gradient)" />
                            <defs>
                                <linearGradient id="solana-gradient" x1="0" y1="0" x2="397" y2="311" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#9945FF" />
                                    <stop offset="100%" stop-color="#14F195" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white tracking-tight">{{ __('Solana Master Wallet') }}</h3>
                    <p class="text-xs text-slate-500 font-mono tracking-wider uppercase">
                        {{ __('Custodial wallet for deposits and payouts') }}
                    </p>
                </div>
            </div>

            @if ($masterAddress && $isWalletValid)
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full">
                        {{ __('Active') }}
                    </span>
                </div>
            @elseif ($masterAddress && $decryptError)
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-red-400 uppercase tracking-widest bg-red-500/10 border border-red-500/20 px-2 py-0.5 rounded-full">
                        {{ __('Invalid Key') }}
                    </span>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span class="text-[10px] font-bold text-amber-400 uppercase tracking-widest bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-full">
                        {{ __('Not Setup') }}
                    </span>
                </div>
            @endif
        </div>

        @if ($masterAddress && $isWalletValid)
            {{-- Wallet Configured State --}}
            <div class="space-y-4">
                {{-- Master Activation Tip Alert --}}
                <div class="p-4 bg-purple-500/5 border border-purple-500/10 rounded-xl flex items-start gap-3">
                    <div class="p-2 rounded-lg bg-purple-500/10 text-purple-400 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-purple-300 uppercase tracking-wider mb-1">
                            {{ __('Activation & Sweeping Requirements') }}
                        </h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            {{ $instructions }}
                        </p>
                    </div>
                </div>

                {{-- 1. Private Key Encryption Security Notice (Cyan) --}}
                <div class="p-4 bg-cyan-500/[0.04] border border-cyan-500/20 rounded-xl flex items-start gap-3.5 font-mono text-xs">
                    <div class="p-2 rounded-lg bg-cyan-500/10 text-cyan-400 shrink-0 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div class="space-y-0.5 min-w-0">
                        <h4 class="text-[11px] font-bold text-white uppercase tracking-wider">
                            {{ __('Cryptographic Key Security & Encryption') }}
                        </h4>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            {{ __('Private keys are encrypted at rest and can only be decrypted using the application key (APP_KEY) and your administrator password.') }}
                        </p>
                    </div>
                </div>

                {{-- 2. External Wallet Import Notice (Amber) --}}
                <div class="p-4 bg-amber-500/[0.04] border border-amber-500/20 rounded-xl flex items-start gap-3.5 font-mono text-xs">
                    <div class="p-2 rounded-lg bg-amber-500/10 text-amber-400 shrink-0 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <div class="space-y-0.5 min-w-0">
                        <h4 class="text-[11px] font-bold text-white uppercase tracking-wider">
                            {{ __('External Wallet Import (Phantom / Solflare / Trust Wallet)') }}
                        </h4>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            {{ __('You can reveal and export this Solana master wallet private key to import it into Phantom, Solflare, Trust Wallet, or hardware wallets for external management.') }}
                        </p>
                    </div>
                </div>

                {{-- 3. Blockchain Settings & RPC Notice (Indigo) --}}
                <div class="p-4 bg-indigo-500/[0.04] border border-indigo-500/20 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 font-mono text-xs">
                    <div class="flex items-start gap-3.5">
                        <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-400 shrink-0 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="space-y-0.5 min-w-0">
                            <h4 class="text-[11px] font-bold text-white uppercase tracking-wider">
                                {{ __('Blockchain Settings & RPC Node Configuration') }}
                            </h4>
                            <p class="text-[11px] text-slate-300 leading-relaxed">
                                {{ __('General blockchain settings, custom/self-hosted RPC node endpoints, block explorers, and supported tokens can be configured in Blockchain Settings.') }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.settings.blockchain.index') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/25 text-indigo-300 hover:text-white border border-indigo-500/30 text-[9px] font-bold uppercase tracking-wider transition-all shrink-0 self-end sm:self-auto cursor-pointer">
                        <span>{{ __('Blockchain Settings') }}</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    {{-- Master Wallet Address --}}
                    <div class="p-4 bg-white/[0.02] border border-white/[0.05] rounded-xl hover:bg-white/[0.04] transition-colors">
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Master Wallet Address') }}</div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <span class="font-mono text-xs md:text-sm text-white tracking-wide break-all select-all selection:bg-purple-500/30" id="solana-address-text">
                                {{ $masterAddress }}
                            </span>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" onclick="copyToClipboard('{{ $masterAddress }}', '{{ __('Address copied to clipboard') }}')"
                                        class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-[10px] font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:text-white transition-all active:scale-95 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                    </svg>
                                    <span>{{ __('Copy') }}</span>
                                </button>
                                <button type="button" onclick="toggleWalletQr('solana-qr-panel', 'solana-qr-canvas', '{{ addslashes($masterAddress) }}')"
                                        class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-[10px] font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:text-cyan-400 hover:border-cyan-500/30 transition-all active:scale-95 cursor-pointer"
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
                                    <span>{{ __('QR Code') }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- QR Code Panel --}}
                        <div id="solana-qr-panel" class="hidden mt-4 pt-4 border-t border-white/[0.06]">
                            <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-xl bg-black/40 border border-white/[0.06]">
                                <div class="p-2.5 bg-white rounded-xl shadow-2xl shrink-0 flex items-center justify-center">
                                    <div id="solana-qr-canvas">{!! QrCode::size(140)->margin(0)->generate($masterAddress) !!}</div>
                                </div>
                                <div class="space-y-1.5 text-center sm:text-left min-w-0 flex-1">
                                    <div class="flex items-center justify-center sm:justify-between gap-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400 flex items-center gap-1.5 font-mono">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                                            {{ __('Solana Master Address') }}
                                        </span>
                                        <button type="button" onclick="toggleWalletQr('solana-qr-panel')" class="text-slate-500 hover:text-white p-1 text-xs cursor-pointer" title="{{ __('Close') }}">✕</button>
                                    </div>
                                    <p class="text-[11px] text-slate-400 leading-relaxed font-mono">
                                        {{ __('Scan with Phantom, Solflare, or any mobile wallet to deposit funds or send transaction fee reserves.') }}
                                    </p>
                                    <div class="font-mono text-[10px] text-slate-300 break-all select-all bg-white/[0.03] p-2 rounded-lg border border-white/[0.04]">
                                        {{ $masterAddress }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Private Key --}}
                    <div class="p-4 bg-white/[0.02] border border-white/[0.05] rounded-xl hover:bg-white/[0.04] transition-colors">
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Private Key') }}</div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex-1 font-mono text-sm text-slate-400 break-all select-all selection:bg-purple-500/30" id="solana-private-key-container">
                                <span class="text-slate-600 tracking-widest font-sans">••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" id="btn-reveal-key" onclick="promptRevealKey()"
                                        class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-500/10 border border-purple-500/20 text-[10px] font-bold text-purple-300 uppercase tracking-wider hover:bg-purple-500/20 hover:text-purple-200 transition-all active:scale-95 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    {{ __('Reveal') }}
                                </button>
                                <button type="button" id="btn-copy-private" style="display:none;" onclick="copyPrivateWalletKey()"
                                        class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-[10px] font-bold text-emerald-300 uppercase tracking-wider hover:bg-emerald-500/20 hover:text-emerald-200 transition-all active:scale-95 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                    </svg>
                                    {{ __('Copy') }}
                                </button>
                                <button type="button" id="btn-hide-key" style="display:none;" onclick="hidePrivateKey()"
                                        class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-[10px] font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:text-white transition-all active:scale-95 cursor-pointer">
                                    {{ __('Hide') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Wallet Balances --}}
                    <div class="p-4 bg-white/[0.02] border border-white/[0.05] rounded-xl hover:bg-white/[0.04] transition-colors flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">{{ __('Wallet Balances') }}</span>
                            <button type="button" onclick="fetchSolanaBalance()" class="text-[9px] font-bold text-purple-400 hover:text-purple-300 transition-colors uppercase tracking-wider flex items-center gap-1 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m-3-3v12" />
                                </svg>
                                {{ __('Refresh') }}
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            {{-- Native Token --}}
                            <div class="p-2.5 bg-black/25 rounded-xl border border-white/5 flex items-center gap-3 min-h-[52px]">
                                <img src="{{ $blockchain->logo ? asset($blockchain->logo) : asset('assets/images/tokens/' . $blockchain->code . '.png') }}" class="w-6 h-6 rounded-lg object-contain shrink-0">
                                <div class="flex-1 min-w-0 flex flex-col justify-between h-full">
                                    <div class="text-[8px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">{{ __('SOL') }}</div>
                                    <span class="font-mono text-xs font-bold text-white tracking-tight animate-pulse truncate token-balance-display" 
                                          id="token-bal-sol">
                                        {{ __('Loading...') }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- SPL Tokens --}}
                            @php
                                $tokens = $blockchain->tokens()->where('status', 'enabled')->get();
                            @endphp
                            @foreach ($tokens as $token)
                                @if ($token->mint_address)
                                    <div class="p-2.5 bg-black/25 rounded-xl border border-white/5 flex items-center gap-3 min-h-[52px]">
                                        <img src="{{ $token->logo ? asset($token->logo) : asset('assets/images/tokens/' . strtolower($token->symbol) . '.png') }}" class="w-6 h-6 rounded-lg object-contain shrink-0">
                                        <div class="flex-1 min-w-0 flex flex-col justify-between h-full">
                                            <div class="text-[8px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">{{ strtoupper($token->symbol) }}</div>
                                            <span class="font-mono text-xs font-bold text-slate-300 tracking-tight animate-pulse truncate token-balance-display" 
                                                  id="token-bal-{{ strtolower($token->symbol) }}">
                                                {{ __('Loading...') }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <a href="{{ route('admin.solana-master-wallet.history') }}"
                       class="flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-300 text-[10px] font-bold uppercase tracking-widest hover:bg-purple-500/20 hover:text-purple-200 transition-all active:scale-95 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                            <path d="M12 7v5l4 2"/>
                        </svg>
                        {{ __('View Wallet Details & History') }}
                    </a>
                </div>
            </div>
        @elseif ($masterAddress && $decryptError)
            {{-- Decryption Error State --}}
            <div class="space-y-4">
                <div class="p-5 bg-red-500/5 border border-red-500/10 rounded-xl">
                    <div class="flex items-start gap-4">
                        <div class="p-2 rounded-lg bg-red-500/10 text-red-400 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <h4 class="text-sm font-bold text-red-400 uppercase tracking-wider mb-1">
                                    {{ __('Wallet Private Key Decryption Failed') }}
                                </h4>
                                <p class="text-xs text-red-200/60 leading-relaxed">
                                    {{ __('The private key for the master wallet could not be decrypted. This typically indicates that your APP_KEY in the .env file has changed, or the database setting has been corrupted. The current address is locked and cannot sign transactions.') }}
                                </p>
                            </div>
                            <div class="p-3 bg-black/25 rounded-lg border border-white/5 font-mono text-xs text-red-200/60 break-all select-all selection:bg-red-500/30">
                                <span class="font-bold text-red-400/80 mr-1">{{ __('Wallet Address:') }}</span>{{ $masterAddress }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="generateMasterWallet(true)"
                            class="px-5 py-2.5 rounded-xl bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 text-red-300 text-[10px] font-bold uppercase tracking-widest active:scale-95 transition-all cursor-pointer">
                        {{ __('Regenerate Master Wallet') }}
                    </button>
                </div>
            </div>
        @else
            {{-- Wallet Empty / Generate State --}}
            <div class="flex flex-col items-center justify-center text-center p-6 bg-white/[0.01] border border-dashed border-white/10 rounded-2xl">
                <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-1">
                    {{ __('No Master Wallet Created') }}
                </h4>
                <p class="text-xs text-slate-500 max-w-md mb-6 leading-relaxed">
                    {{ __('A Solana Master Wallet has not been generated yet. This wallet is responsible for holding the swept deposits of all users and authorizing operational payout transactions.') }}
                </p>
                <button type="button" onclick="generateMasterWallet(false)"
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-emerald-500 hover:from-purple-500 hover:to-emerald-400 text-white text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-purple-500/20 active:scale-95 transition-all cursor-pointer">
                    {{ __('Generate Solana Master Wallet') }}
                </button>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    let revealedPrivateKey = '';

    // Only show loader on the initial load. Subsequent polling updates run silently.
    let __solanaBalancesInitialized = false;

    $(document).ready(function() {
        @if ($masterAddress && $isWalletValid)
            // initial load: show loading state
            fetchSolanaBalance(true);
            // background refresh every 5 seconds without showing the loading placeholder
            setInterval(function() { fetchSolanaBalance(false); }, 5000);
        @endif
    });

    function fetchSolanaBalance(showLoading = false) {
        if (showLoading && !__solanaBalancesInitialized) {
            $('.token-balance-display').text('{{ __('Loading...') }}').addClass('animate-pulse');
        }

        $.ajax({
            url: '{{ route("admin.solana-master-wallet.balance") }}',
            type: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    __solanaBalancesInitialized = true;

                    // Update SOL balance
                    $('#token-bal-sol').text(parseFloat(response.balance).toFixed(4) + ' SOL').removeClass('animate-pulse');
                    
                    // Update SPL token balances dynamically
                    if (response.token_balances) {
                        for (let sym in response.token_balances) {
                            let lowerSym = sym.toLowerCase();
                            let balanceVal = response.token_balances[sym];
                            
                            let $el = $('#token-bal-' + lowerSym);
                            if ($el.length) {
                                let formatted = balanceVal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 6 });
                                if (lowerSym.startsWith('usdc') || lowerSym === 'usdt' || lowerSym === 'dai') {
                                    formatted = '$' + formatted;
                                } else {
                                    formatted = formatted + ' ' + sym;
                                }
                                $el.text(formatted).removeClass('animate-pulse');
                            }
                        }
                    }

                    // Clear animate-pulse for any tokens that are still showing loading
                    $('.token-balance-display.animate-pulse').filter(function() {
                        return $(this).text().trim() === '{{ __('Loading...') }}' || $(this).text().trim() === '';
                    }).text('0.00').removeClass('animate-pulse');
                } else {
                    if (!__solanaBalancesInitialized && showLoading) {
                        $('.token-balance-display').text('{{ __('Error') }}').removeClass('animate-pulse');
                    }
                }
            },
            error: function() {
                if (!__solanaBalancesInitialized && showLoading) {
                    $('.token-balance-display').text('{{ __('Error') }}').removeClass('animate-pulse');
                }
            }
        });
    }


    function generateMasterWallet(isForce = false) {
        let title = '{{ __('Create Solana Master Wallet?') }}';
        let text = '{{ __('This will generate a brand new Ed25519 Solana Keypair as the platform’s master wallet. Ensure you keep the encryption key (APP_KEY) secure.') }}';
        
        if (isForce) {
            title = '{{ __('Regenerate Solana Master Wallet?') }}';
            text = '{{ __('WARNING: This will overwrite the existing master wallet configuration. The current address will be replaced, and any active sweep addresses pointing to it will become orphaned. This action cannot be undone.') }}';
        }

        Swal.fire({
            title: title,
            text: text,
            icon: isForce ? 'error' : 'warning',
            showCancelButton: true,
            confirmButtonColor: isForce ? '#ef4444' : '#9945ff',
            cancelButtonColor: '#1e293b',
            confirmButtonText: isForce ? '{{ __('Regenerate & Overwrite') }}' : '{{ __('Generate Now') }}',
            background: '#12131a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                $.ajax({
                    url: '{{ route("admin.solana-master-wallet.generate") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        force: isForce ? 1 : 0
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: isForce ? '{{ __('Regenerated!') }}' : '{{ __('Generated!') }}',
                            text: response.message,
                            background: '#12131a',
                            color: '#fff'
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        let msg = '{{ __('Failed to generate wallet.') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
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

    function promptRevealKey() {
        Swal.fire({
            title: '{{ __('Reveal Private Key') }}',
            text: '{{ __('Please enter your administrator password to decrypt and reveal the master wallet private key:') }}',
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: '{{ __('Confirm') }}',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#1e293b',
            background: '#12131a',
            color: '#fff',
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                return $.ajax({
                    url: '{{ route("admin.solana-master-wallet.reveal") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        password: password
                    }
                }).then(response => {
                    if (response.status === 'success') {
                        return response;
                    }
                    throw new Error(response.message || '{{ __('Failed to reveal key.') }}');
                }).catch(error => {
                    let msg = '{{ __('Incorrect password or decryption error.') }}';
                    if (error.responseJSON && error.responseJSON.message) {
                        msg = error.responseJSON.message;
                    } else if (error.message) {
                        msg = error.message;
                    }
                    Swal.showValidationMessage(msg);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                revealedPrivateKey = result.value.private_key;
                
                // Update UI elements
                $('#solana-private-key-container').html('<span class="text-amber-400 break-all select-all select-text font-bold">' + revealedPrivateKey + '</span>');
                $('#btn-reveal-key').hide();
                $('#btn-copy-private').show();
                $('#btn-hide-key').show();
            }
        });
    }

    function copyPrivateWalletKey() {
        if (revealedPrivateKey) {
            copyToClipboard(revealedPrivateKey, 'Private Key copied to clipboard');
        }
    }

    function hidePrivateKey() {
        revealedPrivateKey = '';
        $('#solana-private-key-container').html('<span class="text-slate-600 tracking-widest font-sans">••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••</span>');
        $('#btn-reveal-key').show();
        $('#btn-copy-private').hide();
        $('#btn-hide-key').hide();
    }
</script>
@endpush
