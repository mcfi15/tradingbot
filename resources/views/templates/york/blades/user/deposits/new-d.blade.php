@extends('templates.york.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-8 pb-16">

    {{-- Global ambient background blur --}}
    <div class="fixed top-0 right-0 w-[40rem] h-[40rem] bg-accent-primary/5 rounded-full blur-[120px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[30rem] h-[30rem] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-5xl mx-auto space-y-8">

        {{-- ══ HEADER DECK ══════════════════════════════════════════════════════ --}}
        <div class="relative rounded-2xl overflow-hidden p-6 md:p-8"
             style="background: linear-gradient(145deg, rgba(8,9,14,0.97) 0%, rgba(5,6,10,0.99) 100%); border: 1px solid rgba(255,255,255,0.06);">
            
            <div class="absolute top-0 right-0 w-80 h-80 bg-accent-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse shadow-[0_0_8px_rgba(226,177,60,0.7)]"></span>
                        <span class="text-[9px] font-bold uppercase tracking-[0.25em] text-slate-500">{{ __('Deposit Portal') }}</span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-slate-500 tracking-tight leading-tight">
                        {{ __('Deposit Crypto') }}
                    </h1>
                    <p class="text-slate-500 text-sm mt-2 font-medium max-w-xl">
                        {{ __('Select a token and network to view or generate your deposit address.') }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 shrink-0">
                    <div class="inline-flex items-center gap-2 rounded-xl border border-white/8 bg-white/[0.02] px-4 py-2.5 text-xs font-bold text-slate-300">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>{{ __(':count Tokens Active', ['count' => count($token_blockchain_map)]) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($blockchains->isEmpty())
            <div class="relative rounded-2xl overflow-hidden p-12 text-center max-w-lg mx-auto"
                 style="background: linear-gradient(145deg, rgba(8,9,14,0.97) 0%, rgba(5,6,10,0.99) 100%); border: 1px solid rgba(255,255,255,0.06);">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">{{ __('No Blockchains Supported') }}</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                    {{ __('No deposit blockchains are currently configured or active. Please check back later.') }}
                </p>
            </div>
        @else
            {{-- ══ SEARCH & FILTER BAR ══════════════════════════════════════════════ --}}
            <div class="relative rounded-2xl overflow-hidden p-4"
                 style="background: linear-gradient(145deg, rgba(8,9,14,0.95) 0%, rgba(4,5,8,0.98) 100%); border: 1px solid rgba(255,255,255,0.05);">
                <div class="relative">
                    <input id="deposit-token-search"
                           type="text"
                           placeholder="{{ __('Search by token name, symbol, or blockchain network (e.g. USDT, Solana, TRX)...') }}"
                           class="w-full bg-white/[0.03] border border-white/8 rounded-xl pl-11 pr-4 py-3.5 text-sm text-white placeholder:text-slate-600 focus:outline-none focus:border-accent-primary/60 transition-all font-medium">
                    <svg class="w-4 h-4 text-slate-600 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            {{-- ══ TOKEN CARDS LIST ═════════════════════════════════════════════════ --}}
            <div class="space-y-4">
                @foreach ($token_blockchain_map as $index => $tokenGroup)
                    @php $token = $tokenGroup['token']; @endphp
                    
                    <div class="deposit-token-card relative rounded-2xl overflow-hidden transition-all duration-300 hover:border-white/10"
                         style="background: linear-gradient(145deg, rgba(8,9,14,0.95) 0%, rgba(4,5,8,0.98) 100%); border: 1px solid rgba(255,255,255,0.05);"
                         data-search="{{ strtolower($token['symbol']) }} {{ strtolower($token['name']) }} {{ collect($tokenGroup['blockchains'])->pluck('blockchain_code')->map(fn($code) => strtolower($code))->implode(' ') }}">
                        
                        {{-- Token Main Row --}}
                        <div class="flex items-center justify-between gap-4 p-5">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-12 h-12 rounded-2xl bg-accent-primary/10 border border-accent-primary/20 flex items-center justify-center text-accent-primary font-black text-xs shrink-0 overflow-hidden relative shadow-[0_0_15px_rgba(226,177,60,0.1)]">
                                    @php
                                        $tokenLogo = !empty($token['logo']) ? asset($token['logo']) : asset('assets/images/tokens/' . strtolower($token['symbol']) . '.png');
                                    @endphp
                                    <img src="{{ $tokenLogo }}"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                         alt="{{ $token['symbol'] }}" class="w-full h-full object-contain p-2">
                                    <span class="hidden items-center justify-center w-full h-full font-black text-xs">{{ $token['symbol'] }}</span>
                                </div>
                                
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-black text-white tracking-tight">{{ $token['symbol'] }}</h3>
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest bg-white/5 border border-white/5 px-2 py-0.5 rounded-md">
                                            {{ $token['name'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5 font-medium">
                                        {{ count($tokenGroup['blockchains']) }} {{ count($tokenGroup['blockchains']) == 1 ? __('Supported Network') : __('Supported Networks') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <button type="button"
                                        class="px-5 py-2.5 rounded-xl bg-accent-primary/10 hover:bg-accent-primary text-accent-primary hover:text-black font-black text-xs uppercase tracking-wider transition-all border border-accent-primary/20 hover:border-accent-primary shadow-[0_0_15px_rgba(226,177,60,0.15)] active:scale-95 cursor-pointer flex items-center gap-2"
                                        onclick="openNetworkSelectionModal({{ json_encode($tokenGroup) }})">
                                    <span>{{ __('Select Network') }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>

{{-- ══ STEP 1: NETWORK SELECTION MODAL ════════════════════════════════════════ --}}
<div id="networkSelectionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 animate-in fade-in duration-200">
    <div class="relative w-full max-w-lg rounded-2xl overflow-hidden shadow-2xl"
         style="background: linear-gradient(145deg, rgba(12,14,20,0.98) 0%, rgba(6,7,12,0.99) 100%); border: 1px solid rgba(255,255,255,0.08);">
        
        {{-- Header --}}
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div id="modalTokenLogoContainer" class="w-10 h-10 rounded-xl bg-accent-primary/10 border border-accent-primary/20 flex items-center justify-center text-accent-primary font-black text-xs overflow-hidden shrink-0">
                    <img id="modalTokenLogo" src="" alt="" class="w-full h-full object-contain p-1.5">
                    <span id="modalTokenSymbolFallback" class="hidden font-black text-xs"></span>
                </div>
                <div>
                    <h3 id="modalTokenTitle" class="text-base font-black text-white tracking-tight"></h3>
                    <p class="text-xs text-slate-500 font-medium">{{ __('Choose a blockchain network for deposit') }}</p>
                </div>
            </div>

            <button type="button" onclick="closeNetworkSelectionModal()" class="w-8 h-8 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white flex items-center justify-center transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Network List Body --}}
        <div id="modalNetworkList" class="p-6 space-y-3 max-h-[60vh] overflow-y-auto">
            {{-- Dynamically populated --}}
        </div>
    </div>
</div>

{{-- ══ STEP 2: DEPOSIT VAULT & QR CODE MODAL ══════════════════════════════════ --}}
<div id="depositVaultModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 animate-in fade-in duration-200">
    <div class="relative w-full max-w-md rounded-2xl overflow-hidden shadow-2xl"
         style="background: linear-gradient(145deg, rgba(12,14,20,0.98) 0%, rgba(6,7,12,0.99) 100%); border: 1px solid rgba(255,255,255,0.08);">
        
        {{-- Vault Header --}}
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <button type="button" onclick="backToNetworkSelection()" class="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white transition-all mr-1" title="{{ __('Back to networks') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div>
                    <h3 id="vaultModalHeaderTitle" class="text-base font-black text-white tracking-tight"></h3>
                    <p id="vaultModalHeaderSub" class="text-xs text-slate-500 font-medium"></p>
                </div>
            </div>

            <button type="button" onclick="closeDepositVaultModal()" class="w-8 h-8 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white flex items-center justify-center transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Vault Body --}}
        <div class="p-6 space-y-5 text-center">
            
            {{-- QR Code Box --}}
            <div id="vaultQrContainer" class="flex flex-col items-center justify-center">
                <div class="p-3 bg-white rounded-2xl shadow-xl border border-white/10">
                    <img id="vaultQrImage" src="" alt="QR Code" class="w-36 h-36">
                </div>
                <p class="text-[10px] font-mono text-slate-500 mt-2">{{ __('Scan address with your mobile wallet app') }}</p>
            </div>

            {{-- Address Block or Generate CTA --}}
            <div id="vaultAddressContainer" class="space-y-3">
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 text-left">{{ __('Personal Deposit Address') }}</div>
                
                <div class="p-3.5 rounded-xl bg-black/50 border border-white/8 font-mono text-xs text-white break-all text-left flex items-center justify-between gap-2 shadow-inner">
                    <span id="vaultAddressText" class="select-all"></span>
                </div>

                <button type="button" id="vaultCopyBtn" onclick="copyVaultAddress()"
                        class="w-full py-3 rounded-xl bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_4px_20px_rgba(226,177,60,0.3)] active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span>{{ __('Copy Deposit Address') }}</span>
                </button>
            </div>

            <div id="vaultGenerateContainer" class="hidden space-y-3 py-4">
                <p class="text-xs text-slate-400">{{ __('No personal vault address has been generated for this network yet.') }}</p>
                <button type="button" id="vaultGenerateBtn"
                        class="w-full py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_4px_20px_rgba(16,185,129,0.3)] active:scale-95 cursor-pointer">
                    {{ __('Generate Personal Address') }}
                </button>
            </div>

            {{-- Security Warning Card --}}
            <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-left space-y-1.5">
                <div class="flex items-center gap-2 text-amber-400 font-bold text-xs">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ __('Important Deposit Notice') }}</span>
                </div>
                <p id="vaultWarningText" class="text-[11px] text-slate-300 leading-relaxed font-medium">
                    {{ __('Send only the specified asset over this exact network. Transferring any other asset or using an incompatible network will result in loss of funds.') }}
                </p>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentActiveTokenGroup = null;
let currentActiveNetwork = null;

function openNetworkSelectionModal(tokenGroup) {
    currentActiveTokenGroup = tokenGroup;
    const token = tokenGroup.token;
    
    // Set Header Info
    $('#modalTokenTitle').text(token.symbol + ' - ' + token.name);
    
    const logoUrl = token.logo ? ('/' + token.logo.replace(/^\//, '')) : ('/assets/images/tokens/' + token.symbol.toLowerCase() + '.png');
    $('#modalTokenLogo').attr('src', logoUrl).show();
    $('#modalTokenSymbolFallback').text(token.symbol).hide();

    // Populate Network List
    let html = '';
    tokenGroup.blockchains.forEach(function(net) {
        const netLogo = net.blockchain_logo ? ('/' + net.blockchain_logo.replace(/^\//, '')) : ('/assets/images/tokens/' + net.blockchain_code.toLowerCase() + '.png');
        const statusBadge = net.wallet_exists 
            ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>Address Active</span>'
            : '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>Address Pending</span>';

        html += `
            <div onclick="selectNetworkAndOpenVault(${JSON.stringify(net).replace(/"/g, '&quot;')})"
                 class="group p-4 rounded-xl bg-white/[0.02] hover:bg-white/[0.05] border border-white/5 hover:border-accent-primary/40 transition-all cursor-pointer flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0 group-hover:border-accent-primary/40 transition-all">
                        <img src="${netLogo}" onerror="this.style.display='none';" class="w-6 h-6 object-contain">
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white group-hover:text-accent-primary transition-colors flex items-center gap-2">
                            <span>${net.blockchain_network || net.blockchain_name}</span>
                            <span class="text-[10px] font-mono text-slate-500">(${net.blockchain_code.toUpperCase()})</span>
                        </div>
                        <div class="mt-1">${statusBadge}</div>
                    </div>
                </div>

                <div class="shrink-0 text-slate-400 group-hover:text-accent-primary transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
        `;
    });

    $('#modalNetworkList').html(html);
    $('#networkSelectionModal').removeClass('hidden');
}

function closeNetworkSelectionModal() {
    $('#networkSelectionModal').addClass('hidden');
}

function selectNetworkAndOpenVault(network) {
    currentActiveNetwork = network;
    closeNetworkSelectionModal();

    const token = currentActiveTokenGroup.token;

    // Set Vault Header
    $('#vaultModalHeaderTitle').text(token.symbol + ' (' + network.blockchain_name + ')');
    $('#vaultModalHeaderSub').text('{{ __('Deposit via ') }}' + network.blockchain_name + ' (' + network.blockchain_code.toUpperCase() + ')');

    // Warning Text
    $('#vaultWarningText').html('Send only <strong>' + token.symbol + '</strong> via the <strong>' + network.blockchain_name + ' (' + network.blockchain_code.toUpperCase() + ')</strong> network to this address. Transferred funds will settle automatically.');

    if (network.wallet_exists && network.wallet_address) {
        $('#vaultAddressText').text(network.wallet_address);
        $('#vaultQrImage').attr('src', 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' + encodeURIComponent(network.wallet_address));
        $('#vaultQrContainer').removeClass('hidden');
        $('#vaultAddressContainer').removeClass('hidden');
        $('#vaultGenerateContainer').addClass('hidden');
    } else {
        $('#vaultQrContainer').addClass('hidden');
        $('#vaultAddressContainer').addClass('hidden');
        $('#vaultGenerateContainer').removeClass('hidden');
        
        $('#vaultGenerateBtn').off('click').on('click', function() {
            generatePersonalWallet(network.blockchain_id, network.blockchain_name);
        });
    }

    $('#depositVaultModal').removeClass('hidden');
}

function backToNetworkSelection() {
    closeDepositVaultModal();
    if (currentActiveTokenGroup) {
        openNetworkSelectionModal(currentActiveTokenGroup);
    }
}

function closeDepositVaultModal() {
    $('#depositVaultModal').addClass('hidden');
}

function copyVaultAddress() {
    const address = $('#vaultAddressText').text();
    if (!address) return;

    navigator.clipboard.writeText(address).then(function() {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ __('Deposit address copied to clipboard!') }}',
            showConfirmButton: false,
            timer: 2200,
            background: '#08090e',
            color: '#fff'
        });
    });
}

$(function () {
    const $searchInput = $('#deposit-token-search');
    const $cards = $('.deposit-token-card');

    $searchInput.on('input', function () {
        const query = $(this).val().toLowerCase().trim();

        $cards.each(function () {
            const text = $(this).data('search') || '';
            const matches = text.includes(query);
            $(this).toggle(matches);
        });
    });
});

function generatePersonalWallet(blockchainId, blockchainName) {
    Swal.fire({
        title: '{{ __('Generate Address?') }}',
        text: '{{ __('Create a personal deposit address for ') }}' + blockchainName + '{{ __('.') }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#e2b13c',
        cancelButtonColor: '#1e293b',
        confirmButtonText: '{{ __('Generate Now') }}',
        background: '#08090e',
        color: '#fff',
        customClass: {
            popup: 'border border-white/10 rounded-2xl'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            $.ajax({
                url: '{{ route("user.deposits.generate-wallet") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    blockchain_id: blockchainId
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('Address Created!') }}',
                        text: response.message,
                        background: '#08090e',
                        color: '#fff'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    let msg = '{{ __('Failed to generate deposit address.') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Notice') }}',
                        text: msg,
                        background: '#08090e',
                        color: '#fff'
                    });
                }
            });
        }
    });
}
</script>
@endsection
