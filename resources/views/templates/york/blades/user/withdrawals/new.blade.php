@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-12 pb-24">

    {{-- Global Ethereal Ambient Mesh Gradients --}}
    <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-emerald-500/5 via-cyan-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-12">

        {{-- ══ CONTROL DECK HEADER & BALANCE HUD ═══════════════════════════════ --}}
        <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                 style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">
                
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div class="max-w-xl space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary shadow-[0_0_15px_rgba(226,177,60,0.2)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                            <span>{{ __('Withdrawal Portal') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                            {{ __('Withdraw Funds') }}
                        </h1>

                        <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed">
                            {{ __('Select a token and network to withdraw funds to your external wallet.') }}
                        </p>
                    </div>

                    {{-- Available Balance Card --}}
                    <div class="p-6 rounded-3xl bg-accent-primary/10 border border-accent-primary/20 space-y-1 shrink-0 min-w-[260px] shadow-[0_0_30px_rgba(226,177,60,0.15)]">
                        <span class="text-[9px] font-bold uppercase tracking-widest text-accent-primary block">{{ __('Available Account Balance') }}</span>
                        <div class="text-3xl font-black text-white font-mono tracking-tight">
                            {{ showAmount(auth()->user()->balance) }}
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium block pt-1">{{ __('Available to withdraw') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ WITHDRAWAL LIMITS & PARAMETER METRICS ═══════════════════════════ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-1" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Min Withdrawal Limit') }}</span>
                    <div class="text-2xl font-black text-white font-mono tracking-tight">{{ showAmount(getSetting('min_withdrawal')) }}</div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-1" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Max Withdrawal Limit') }}</span>
                    <div class="text-2xl font-black text-white font-mono tracking-tight">{{ showAmount(getSetting('max_withdrawal')) }}</div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-1" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Processing Fee') }}</span>
                    <div class="text-2xl font-black text-accent-primary font-mono tracking-tight">{{ getSetting('withdrawal_fee') }}%</div>
                </div>
            </div>

            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-1" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Processing Speed') }}</span>
                    <div class="text-2xl font-black text-emerald-400 tracking-tight flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>{{ __('Instant') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SUPPORTED TOKENS SEARCH & SELECTION ═════════════════════════════ --}}
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-2">
                <div>
                    <h3 class="text-2xl font-black text-white tracking-tight">{{ __('Supported Withdrawal Tokens') }}</h3>
                    <p class="text-xs text-slate-400 font-medium mt-1">{{ __('Select a token to enter your withdrawal amount and receiving wallet address.') }}</p>
                </div>

                {{-- Token Search Input --}}
                <div class="relative w-full sm:w-80">
                    <input id="withdrawal-token-search" type="text" placeholder="{{ __('Search supported tokens...') }}"
                           class="w-full bg-black/50 border border-white/10 rounded-full px-6 py-3 text-xs text-white outline-none focus:border-accent-primary transition-all placeholder:text-slate-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($token_blockchain_map as $index => $tokenGroup)
                    @php $token = $tokenGroup['token']; @endphp
                    <div class="withdrawal-token-card group relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 transition-all duration-300 hover:border-accent-primary/40 hover:shadow-[0_20px_40px_rgba(0,0,0,0.8)] cursor-pointer flex flex-col justify-between"
                         data-search="{{ strtolower($token['symbol']) }} {{ strtolower($token['name']) }} {{ collect($tokenGroup['blockchains'])->pluck('blockchain_code')->map(fn($code) => strtolower($code))->implode(' ') }}"
                         onclick="openWithdrawalModal({{ $tokenGroup['blockchains'][0]['blockchain_id'] ?? 0 }}, '{{ addslashes($tokenGroup['blockchains'][0]['blockchain_name'] ?? '') }}', {{ json_encode($tokenGroup['blockchains']) }}, {{ $token['id'] ?? 0 }}, '{{ addslashes($token['symbol']) }}')">
                        
                        <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                            
                            <div class="flex items-start justify-between gap-4">
                                <div class="relative shrink-0">
                                    <div class="absolute -inset-1 bg-gradient-to-tr from-accent-primary to-purple-500 rounded-2xl blur opacity-20 group-hover:opacity-60 transition-opacity"></div>
                                    @php
                                        $tokenLogo = !empty($token['logo']) ? asset($token['logo']) : asset('assets/images/tokens/' . strtolower($token['symbol']) . '.png');
                                    @endphp
                                    <img src="{{ $tokenLogo }}"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                         alt="{{ $token['symbol'] }}"
                                         class="relative w-14 h-14 rounded-2xl object-contain border border-white/10 p-2 bg-white/5">
                                    <span class="hidden items-center justify-center relative w-14 h-14 rounded-2xl border border-white/10 bg-white/5 font-black text-xs text-white">{{ $token['symbol'] }}</span>
                                </div>

                                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[9px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-accent-primary group-hover:border-accent-primary/30 transition-all">
                                    {{ count($tokenGroup['blockchains']) }} {{ __('Routes') }}
                                </span>
                            </div>

                            <div>
                                <h3 class="text-xl font-black text-white group-hover:text-accent-primary transition-colors tracking-tight">
                                    {{ $token['symbol'] }}
                                </h3>
                                <p class="text-xs text-slate-400 font-medium mt-1 truncate">
                                    {{ $token['name'] }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-white/5 flex items-center justify-between">
                                <span class="text-[10px] font-mono text-accent-primary font-bold">{{ count($tokenGroup['blockchains']) }} {{ __('Networks') }}</span>
                                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider group-hover:text-white flex items-center gap-1">
                                    <span>{{ __('Withdraw') }}</span>
                                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </span>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- ══ WITHDRAWAL VAULT MODAL ═══════════════════════════════════════════════ --}}
<div id="withdrawalModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md p-4 overflow-y-auto animate-in fade-in duration-200">
    <div class="relative w-full max-w-lg rounded-[2.5rem] p-1.5 bg-white/10 border border-white/15 shadow-2xl my-auto max-h-[90vh] flex flex-col">
        <div class="rounded-[calc(2.5rem-0.375rem)] p-6 sm:p-7 space-y-5 overflow-y-auto max-h-[calc(90vh-1rem)]" style="background: linear-gradient(145deg, rgba(12,14,20,0.98) 0%, rgba(6,7,12,0.99) 100%);">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-4 border-b border-white/5 sticky top-0 bg-[#0c0e14] z-10">
                <div>
                    <span class="text-[9px] font-bold uppercase tracking-widest text-accent-primary">{{ __('Withdrawal') }}</span>
                    <h3 class="text-xl font-black text-white tracking-tight" id="modalTokenTitle">{{ __('Withdraw Token') }}</h3>
                </div>
                <button type="button" onclick="closeWithdrawalModal()" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('user.withdrawals.new-validate') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token_id" id="modalTokenId">
                <input type="hidden" name="blockchain_id" id="modalBlockchainId">

                {{-- Network Selector --}}
                <div class="space-y-2">
                    <label class="text-[10px] text-slate-400 uppercase font-black tracking-widest">{{ __('Select Blockchain Network') }}</label>
                    <div id="modalNetworkList" class="grid grid-cols-2 gap-2 max-h-44 overflow-y-auto p-1"></div>
                </div>

                {{-- Wallet Address --}}
                <div class="space-y-2">
                    <label class="text-[10px] text-slate-400 uppercase font-black tracking-widest">{{ __('Destination Wallet Address') }}</label>
                    <input type="text" name="wallet_address" required
                           class="w-full bg-black/50 border border-white/10 rounded-2xl py-3.5 px-4 text-white font-mono text-xs focus:border-accent-primary outline-none transition-all placeholder:text-slate-600"
                           placeholder="{{ __('Paste your receiving wallet address...') }}">
                </div>

                {{-- Amount Input --}}
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-[10px]">
                        <label class="text-slate-400 font-black uppercase tracking-widest">{{ __('Withdrawal Amount') }}</label>
                        <span class="text-slate-500 font-bold">{{ __('Available:') }} <strong class="text-white font-black">{{ showAmount(auth()->user()->balance) }}</strong></span>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-accent-primary font-black text-base">{{ getSetting('currency_symbol', '$') }}</span>
                        </div>
                        <input type="number" name="amount" step="any" required
                               class="w-full bg-black/50 border border-white/10 rounded-2xl py-3.5 pl-10 pr-4 text-white text-lg font-bold focus:border-accent-primary outline-none transition-all placeholder:text-slate-600"
                               placeholder="0.00">
                    </div>
                </div>

                {{-- Notice --}}
                <div class="p-4 bg-accent-primary/5 rounded-2xl border border-accent-primary/10">
                    <p class="text-[11px] text-slate-300 leading-relaxed font-medium">
                        {{ __('Please ensure your receiving wallet supports the selected blockchain network. Transactions on blockchain networks are irreversible.') }}
                    </p>
                </div>

                <button type="submit"
                        class="w-full py-4 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_4px_20px_rgba(226,177,60,0.3)] active:scale-[0.98] cursor-pointer">
                    {{ __('Confirm Withdrawal') }}
                </button>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentBlockchains = [];

function openWithdrawalModal(blockchainId, blockchainName, blockchains, tokenId, tokenSymbol) {
    document.getElementById('modalTokenId').value = tokenId;
    document.getElementById('modalTokenTitle').innerText = 'Withdraw ' + tokenSymbol;
    currentBlockchains = blockchains;

    const list = document.getElementById('modalNetworkList');
    list.innerHTML = '';

    blockchains.forEach((net, idx) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'p-3 rounded-2xl border text-center flex items-center justify-center transition-all cursor-pointer ' +
            (idx === 0 ? 'bg-accent-primary/10 border-accent-primary text-white' : 'bg-white/[0.02] border-white/5 text-slate-400 hover:bg-white/5');
        
        const netName = net.blockchain_network || net.blockchain_name || '';
        const netCode = net.blockchain_code || '';
        const networkLabel = netCode ? (netName + ' (' + netCode + ')') : netName;
        
        btn.innerHTML = '<span class="block text-xs font-black font-mono text-white tracking-wider">' + networkLabel + '</span>';
        
        btn.onclick = function() {
            document.querySelectorAll('#modalNetworkList button').forEach(b => {
                b.className = 'p-3 rounded-2xl border text-center flex items-center justify-center transition-all cursor-pointer bg-white/[0.02] border-white/5 text-slate-400 hover:bg-white/5';
            });
            btn.className = 'p-3 rounded-2xl border text-center flex items-center justify-center transition-all cursor-pointer bg-accent-primary/10 border-accent-primary text-white';
            document.getElementById('modalBlockchainId').value = net.blockchain_id;
        };

        list.appendChild(btn);
    });

    if (blockchains.length > 0) {
        document.getElementById('modalBlockchainId').value = blockchains[0].blockchain_id;
    }

    document.getElementById('withdrawalModal').classList.remove('hidden');
}

function closeWithdrawalModal() {
    document.getElementById('withdrawalModal').classList.add('hidden');
}

// Token Search Filter
document.getElementById('withdrawal-token-search').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.withdrawal-token-card').forEach(card => {
        const text = card.getAttribute('data-search') || '';
        if (text.includes(q)) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
});
</script>
@endpush
