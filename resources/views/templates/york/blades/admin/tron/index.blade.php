@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    @php
        $masterAddress = $blockchain?->master_wallet_address;
        $masterPrivateKeyEncrypted = $blockchain?->master_private_key;
        $isConfigured = !empty($masterAddress) && !empty($masterPrivateKeyEncrypted);
        $nativeToken = $blockchain?->tokens?->whereNull('mint_address')->first();
        $nativeSymbol = $nativeToken?->symbol ? strtoupper($nativeToken->symbol) : 'TRX';
        $instructions = $blockchain?->instructions ?? __('To enable auto-sweeping of user deposits, make sure this Tron Master Wallet is funded with some TRX for bandwidth, energy, and transaction fees. You must also send a small amount of TRX to activate resource availability for TRC20 sweeps.');
        $supportedTokens = $supportedTokens ?? collect();
        $supportedTokenBalances = $supportedTokenBalances ?? [];
        $nativeBalance = $nativeBalance ?? 0;
        $rpcStatus = $rpcStatus ?? __('Unavailable');
    @endphp

    <div class="space-y-8">
        <div>
            <h2 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-orange-200 to-red-400 tracking-tight leading-tight">
                {{ __('Tron Master Wallet') }}
            </h2>
            <p class="text-orange-200/60 font-mono text-[9px] md:text-xs mt-0.5 tracking-widest uppercase">
                {{ __('Dedicated TRON custodial wallet for TRX and TRC20 deposit sweeps') }}
            </p>
        </div>

        <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors mt-6">
            <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-48 h-48 bg-red-600/15 rounded-full blur-3xl pointer-events-none z-0"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-36 h-36 bg-orange-500/10 rounded-full blur-2xl pointer-events-none z-0"></div>

            <div class="relative z-10 p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-red-600 to-orange-500 p-0.5 flex items-center justify-center shadow-lg shadow-red-500/10 overflow-hidden">
                            <div class="w-full h-full bg-secondary-dark rounded-[10px] flex items-center justify-center">
                                <img src="{{ $blockchain?->logo ? asset($blockchain->logo) : asset('assets/images/tokens/trx.png') }}"
                                     alt="{{ $blockchain?->name ?? __('Tron') }}"
                                     class="w-7 h-7 object-contain">
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white tracking-tight">{{ __('Tron Master Wallet') }}</h3>
                            <p class="text-xs text-slate-500 font-mono tracking-wider uppercase">
                                {{ __('Custodial wallet for deposits and payouts') }}
                            </p>
                        </div>
                    </div>

                    @if ($masterAddress && $isConfigured)
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full">
                                {{ __('Active') }}
                            </span>
                        </div>
                    @elseif ($masterAddress)
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

                @if ($masterAddress && $isConfigured)
                    <div class="space-y-4">
                        <div class="p-4 bg-orange-500/5 border border-orange-500/10 rounded-xl flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-orange-500/10 text-orange-400 shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-orange-300 uppercase tracking-wider mb-1">
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
                                    {{ __('External Wallet Import (TronLink / Trust Wallet / Ledger)') }}
                                </h4>
                                <p class="text-[11px] text-slate-300 leading-relaxed">
                                    {{ __('You can reveal and export this Tron master wallet private key to import it directly into TronLink, Trust Wallet, or hardware wallets for external management.') }}
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
                            <div class="p-4 bg-white/[0.02] border border-white/[0.05] rounded-xl hover:bg-white/[0.04] transition-colors">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">{{ __('Native Balance') }}</div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full border {{ $rpcStatus === __('Connected') ? 'text-emerald-300 bg-emerald-500/10 border-emerald-500/20' : 'text-amber-300 bg-amber-500/10 border-amber-500/20' }} uppercase tracking-widest font-bold">{{ $rpcStatus }}</span>
                                </div>
                                <div class="font-mono text-lg font-black text-white">
                                    {{ number_format((float) $nativeBalance, 6) }} <span class="text-xs text-slate-400 font-bold">{{ $nativeSymbol }}</span>
                                </div>
                            </div>

                            <div class="p-4 bg-white/[0.02] border border-white/[0.05] rounded-xl hover:bg-white/[0.04] transition-colors">
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Master Wallet Address') }}</div>
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <span class="font-mono text-xs md:text-sm text-white tracking-wide break-all select-all selection:bg-orange-500/30" id="tron-address-text">
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
                                        <button type="button" onclick="toggleWalletQr('tron-qr-panel', 'tron-qr-canvas', '{{ addslashes($masterAddress) }}')"
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
                                <div id="tron-qr-panel" class="hidden mt-4 pt-4 border-t border-white/[0.06]">
                                    <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-xl bg-black/40 border border-white/[0.06]">
                                        <div class="p-2.5 bg-white rounded-xl shadow-2xl shrink-0 flex items-center justify-center">
                                            <div id="tron-qr-canvas">{!! QrCode::size(140)->margin(0)->generate($masterAddress) !!}</div>
                                        </div>
                                        <div class="space-y-1.5 text-center sm:text-left min-w-0 flex-1">
                                            <div class="flex items-center justify-center sm:justify-between gap-2">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400 flex items-center gap-1.5 font-mono">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                                                    {{ __('Tron Master Address') }}
                                                </span>
                                                <button type="button" onclick="toggleWalletQr('tron-qr-panel')" class="text-slate-500 hover:text-white p-1 text-xs cursor-pointer" title="{{ __('Close') }}">✕</button>
                                            </div>
                                            <p class="text-[11px] text-slate-400 leading-relaxed font-mono">
                                                {{ __('Scan with TronLink, Trust Wallet, or any TRC20 wallet to deposit TRX or energy/bandwidth fees.') }}
                                            </p>
                                            <div class="font-mono text-[10px] text-slate-300 break-all select-all bg-white/[0.03] p-2 rounded-lg border border-white/[0.04]">
                                                {{ $masterAddress }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-white/[0.02] border border-white/[0.05] rounded-xl hover:bg-white/[0.04] transition-colors">
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Wallet Status') }}</div>
                                <div class="space-y-3">
                                    <div class="text-xs text-slate-300">
                                        {{ __('Private key is encrypted and stored securely. Use the generate action only if you intend to replace the current Tron master wallet.') }}
                                    </div>
                                    <div id="tron-private-key-container" class="text-[11px] bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-slate-600 tracking-widest font-sans break-all select-none">
                                        •••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <button type="button" id="btn-reveal-tron-key" onclick="revealTronPrivateKey()"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600/10 border border-emerald-500/20 text-[10px] font-bold text-emerald-300 uppercase tracking-wider hover:bg-emerald-600/20 hover:text-emerald-200 transition-all active:scale-95 cursor-pointer">
                                            {{ __('Reveal Private Key') }}
                                        </button>
                                        <button type="button" id="btn-copy-tron-key" onclick="copyTronPrivateKey()"
                                                class="hidden items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-[10px] font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:text-white transition-all active:scale-95 cursor-pointer">
                                            {{ __('Copy Key') }}
                                        </button>
                                        <button type="button" id="btn-hide-tron-key" onclick="hideTronPrivateKey()"
                                                class="hidden items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600/10 border border-red-500/20 text-[10px] font-bold text-red-300 uppercase tracking-wider hover:bg-red-600/20 hover:text-red-200 transition-all active:scale-95 cursor-pointer">
                                            {{ __('Hide Key') }}
                                        </button>
                                        <button type="button" onclick="generateTronWallet(true)"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-500/10 border border-orange-500/20 text-[10px] font-bold text-orange-300 uppercase tracking-wider hover:bg-orange-500/20 hover:text-orange-200 transition-all active:scale-95 cursor-pointer">
                                            {{ __('Regenerate') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-white/[0.02] border border-white/[0.05] rounded-xl hover:bg-white/[0.04] transition-colors flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">{{ __('Supported Tokens') }}</span>
                                    <span class="text-[10px] font-bold text-orange-300 uppercase tracking-wider">{{ $supportedTokens->count() }} {{ __('enabled') }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach ($supportedTokens as $token)
                                        <div class="p-2.5 bg-black/25 rounded-xl border border-white/5 flex items-center gap-3 min-h-[52px]">
                                            <img src="{{ $token->logo ? asset($token->logo) : asset('assets/images/tokens/' . strtolower($token->symbol) . '.png') }}" class="w-6 h-6 rounded-lg object-contain shrink-0" alt="{{ $token->symbol }}">
                                            <div class="flex-1 min-w-0 flex flex-col justify-between h-full">
                                                <div class="text-[8px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">{{ strtoupper($token->symbol) }}</div>
                                                <span class="font-mono text-[10px] font-bold text-slate-300 tracking-tight truncate">
                                                    {{ $token->mint_address ? $token->name : __('Native Coin') }}
                                                </span>
                                                @php
                                                    $tokenDecimals = (int) ($token->decimals ?? 6);
                                                    $displayDecimals = min(max($tokenDecimals, 2), 8);
                                                    $tokenBalance = (float) ($supportedTokenBalances[$token->id] ?? 0);
                                                @endphp
                                                <span class="font-mono text-[10px] font-black text-emerald-300 tracking-tight truncate">
                                                    {{ number_format($tokenBalance, $displayDecimals) }} {{ strtoupper($token->symbol) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <a href="{{ route('admin.tron-master-wallet.history') }}"
                               class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white text-[10px] font-bold uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(249,115,22,0.2)] hover:shadow-[0_0_25px_rgba(249,115,22,0.35)] active:scale-[0.98] cursor-pointer">
                                {{ __('Telemetry & On-chain History') }}
                            </a>

                            <button type="button" onclick="generateTronWallet(true)"
                                    class="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-slate-300 text-[10px] font-bold uppercase tracking-widest hover:bg-white/10 hover:text-white transition-all active:scale-[0.98] cursor-pointer">
                                {{ __('Regenerate Wallet') }}
                            </button>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-center max-w-md mx-auto space-y-4">
                        <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 shadow-inner overflow-hidden">
                            <img src="{{ $blockchain?->logo ? asset($blockchain->logo) : asset('assets/images/tokens/trx.png') }}" alt="{{ __('Tron') }}" class="w-10 h-10 object-contain">
                        </div>
                        <div class="space-y-1.5">
                            <h4 class="text-base font-bold text-white tracking-tight">
                                {{ __(':blockchain Master Wallet Not Configured', ['blockchain' => $blockchain?->name ?? 'Tron']) }}
                            </h4>
                            <p class="text-xs text-slate-500 leading-relaxed font-light">
                                {{ __('Generate the Tron master wallet to start receiving deposits and sweeping TRX/TRC20 assets automatically.') }}
                            </p>
                        </div>
                        <button type="button" onclick="generateTronWallet()"
                                class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(99,102,241,0.25)] hover:shadow-[0_0_25px_rgba(99,102,241,0.45)] active:scale-[0.97] cursor-pointer">
                            {{ __('Generate Tron Master Wallet') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let revealedTronPrivateKey = '';

    function generateTronWallet(force = false) {
        let title = '{{ __('Generate Tron Master Wallet?') }}';
        let text = '{{ __('This will generate a secure Tron master address. Keep the private key safe and make sure the wallet is funded with TRX for bandwidth and transaction fees.') }}';

        if (force) {
            title = '{{ __('Regenerate Wallet?') }}';
            text = '{{ __('WARNING: Regenerating the wallet will replace the active Tron master address. Ensure you backup any private key information before proceeding.') }}';
        }

        Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f97316',
            cancelButtonColor: '#1e293b',
            confirmButtonText: force ? '{{ __('Regenerate') }}' : '{{ __('Generate Address') }}',
            background: '#12131a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                $.ajax({
                    url: '{{ route("admin.tron-master-wallet.generate") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        force: force ? 1 : 0
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
                        }
                    },
                    error: function(xhr) {
                        let msg = '{{ __('Failed to generate Tron master wallet.') }}';
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

    function revealTronPrivateKey() {
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
                    url: '{{ route("admin.tron-master-wallet.reveal") }}',
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
                revealedTronPrivateKey = result.value.private_key;

                $('#tron-private-key-container')
                    .html('<span class="text-emerald-400 break-all select-all select-text font-bold">' + revealedTronPrivateKey + '</span>')
                    .removeClass('text-slate-600 tracking-widest font-sans select-none');
                $('#btn-reveal-tron-key').hide();
                $('#btn-copy-tron-key').removeClass('hidden').addClass('inline-flex');
                $('#btn-hide-tron-key').removeClass('hidden').addClass('inline-flex');
            }
        });
    }

    function copyTronPrivateKey() {
        if (revealedTronPrivateKey) {
            copyToClipboard(revealedTronPrivateKey, '{{ __('Private key copied to clipboard') }}');
        }
    }

    function hideTronPrivateKey() {
        revealedTronPrivateKey = '';
        $('#tron-private-key-container')
            .html('•••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••')
            .addClass('text-slate-600 tracking-widest font-sans select-none');
        $('#btn-reveal-tron-key').show();
        $('#btn-copy-tron-key').addClass('hidden').removeClass('inline-flex');
        $('#btn-hide-tron-key').addClass('hidden').removeClass('inline-flex');
    }
</script>
@endpush
