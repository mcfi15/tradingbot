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
                            {{ __(':blockchain Master Wallet Details', ['blockchain' => $blockchain->name]) }}
                        </h2>
                        <p class="text-indigo-200/60 font-mono text-[9px] md:text-xs mt-0.5 tracking-widest uppercase">
                            {{ __('Real-time On-chain Wallet Telemetry') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 text-[10px] text-indigo-400/80 font-mono bg-indigo-500/5 border border-indigo-500/10 px-3 py-1.5 rounded-xl">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                {{ __('CONNECTED TO :blockchain NETWORK', ['blockchain' => strtoupper($blockchain->name)]) }}
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
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>{{ __('Active') }}
                        </span>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('Wallet Address') }}</div>
                        <div class="font-mono text-xs text-white bg-black/20 border border-white/5 p-3 rounded-xl break-all select-all flex items-start justify-between gap-2">
                            <span>{{ $address }}</span>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" onclick="copyToClipboard('{{ $address }}', '{{ __('Address copied to clipboard') }}')"
                                        class="text-slate-400 hover:text-white p-1 hover:bg-white/5 rounded transition-colors cursor-pointer" title="{{ __('Copy Address') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                    </svg>
                                </button>
                                <button type="button" onclick="showMasterWalletQrModal('{{ addslashes($blockchain->name) }}', '{{ addslashes($address) }}')"
                                        class="text-slate-400 hover:text-cyan-400 p-1 hover:bg-white/5 rounded transition-colors cursor-pointer" title="{{ __('Show QR Code') }}">
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
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div class="p-3 bg-white/[0.02] border border-white/[0.05] rounded-xl">
                            <div class="text-[8px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Network Type') }}</div>
                            <div class="text-xs font-bold text-white uppercase">{{ $blockchain->name }}</div>
                        </div>
                        <div class="p-3 bg-white/[0.02] border border-white/[0.05] rounded-xl">
                            <div class="text-[8px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Blockchain') }}</div>
                            <div class="text-xs font-bold text-indigo-400 uppercase">{{ __('EVM (Secp256k1)') }}</div>
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
                            <span id="evm-balance-text">{{ number_format($balance, 4) }}</span> <span class="text-sm font-black text-slate-400 font-sans">{{ $nativeSymbol }}</span>
                        </div>
                        <div class="text-xs text-emerald-400 font-bold tracking-wide mt-2">
                            ≈ <span id="evm-fiat-text">{{ $fiatBalance }}</span> <span class="text-[9px] text-slate-500 font-normal uppercase">({{ getSetting('currency') }})</span>
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
                            {{ $blockchain->rpc_url }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Quick Links') }}</div>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $liveExplorer = str_replace('{address}', $address, $blockchain->explorer_url_live);
                            @endphp
                            <a href="{{ $liveExplorer }}" target="_blank"
                               class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[9px] font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>
                                </svg>
                                {{ __('Block Explorer') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOKEN HOLDINGS SECTION --}}
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-white tracking-tight">{{ __('Token Holdings (ERC-20)') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach ($tokenBalances as $symbol => $bal)
                    <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors p-5 flex items-center justify-between gap-4">
                        <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none z-0"></div>
                        
                        <div class="relative z-10 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center font-bold text-xs text-indigo-400">
                                {{ $symbol }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white leading-tight">{{ $symbol }} {{ __('Balance') }}</h4>
                                <p class="text-[10px] text-slate-500 font-mono mt-0.5">{{ $symbol }} {{ __('Token') }}</p>
                            </div>
                        </div>
                        <div class="relative z-10 text-right">
                            <div class="text-xl font-black text-white font-mono tracking-tight leading-none" id="evm-{{ strtolower($symbol) }}-holdings">
                                {{ number_format($bal, 2) }} <span class="text-[10px] text-slate-400 font-normal font-sans">{{ $symbol }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- TRANSACTION HISTORY SECTION --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white tracking-tight">{{ __('Recent Transactions (On-chain)') }}</h3>
                <span class="text-[10px] text-slate-500 font-mono uppercase bg-white/5 border border-white/10 px-2.5 py-1 rounded-lg">
                    {{ __('Showing last 30 normal/transfer events') }}
                </span>
            </div>

            <div class="bg-secondary border border-white/5 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-white/5 bg-black/25 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                <th class="p-4 pl-6">{{ __('Transaction Hash') }}</th>
                                <th class="p-4">{{ __('Type') }}</th>
                                <th class="p-4 text-right">{{ __('Amount') }}</th>
                                <th class="p-4">{{ __('Memo / Detail') }}</th>
                                <th class="p-4">{{ __('Timestamp') }}</th>
                                <th class="p-4 pr-6 text-right">{{ __('Network Fee') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-xs text-slate-300 font-medium">
                            @forelse ($transactions as $tx)
                                <tr class="hover:bg-white/[0.01] transition-colors">
                                    <td class="p-4 pl-6 font-mono text-[11px] tracking-tight">
                                        <div class="flex items-center gap-2">
                                            @php
                                                $txExplorer = str_replace('address/{address}', 'tx/' . $tx['signature'], $blockchain->explorer_url_live);
                                            @endphp
                                            <a href="{{ $txExplorer }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 transition-colors hover:underline">
                                                {{ substr($tx['signature'], 0, 10) }}...{{ substr($tx['signature'], -10) }}
                                            </a>
                                            <button type="button" onclick="copyToClipboard('{{ $tx['signature'] }}', 'Transaction hash copied')" class="text-slate-500 hover:text-slate-300 p-0.5 rounded transition-colors cursor-pointer">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        @if ($tx['type'] === 'Incoming')
                                            <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 rounded-full">
                                                {{ __('Incoming') }}
                                            </span>
                                        @else
                                            <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider bg-amber-500/10 border border-amber-500/20 px-2.5 py-0.5 rounded-full">
                                                {{ __('Outgoing') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right font-mono font-bold text-white">
                                        @if ($tx['type'] === 'Incoming')
                                            <span class="text-emerald-400">+{{ number_format($tx['amount'], 4) }}</span>
                                        @else
                                            <span class="text-amber-400">-{{ number_format($tx['amount'], 4) }}</span>
                                        @endif
                                        <span class="text-[9px] text-slate-400 font-normal font-sans pl-0.5">{{ $tx['token_symbol'] }}</span>
                                    </td>
                                    <td class="p-4 text-slate-400">
                                        {{ $tx['memo'] }}
                                    </td>
                                    <td class="p-4 text-slate-500 font-mono text-[11px]">
                                        {{ date('Y-m-d H:i:s', $tx['block_time']) }}
                                    </td>
                                    <td class="p-4 pr-6 text-right font-mono text-[11px] text-slate-400">
                                        @if ($tx['fee'] > 0)
                                            {{ number_format($tx['fee'], 6) }} {{ $nativeSymbol }}
                                        @else
                                            <span class="text-slate-600">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-slate-500 italic">
                                        {{ __('No transactions found for this master wallet on-chain.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transactions->hasPages())
                    <div class="p-4 border-t border-white/5 bg-black/10">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function triggerManualRefresh() {
        $('#btn-balance-refresh').addClass('pointer-events-none opacity-50');
        $('#refresh-button-text').text('{{ __('Refreshing...') }}');
        
        $.ajax({
            url: '{{ route("admin.evm-master-wallet.balance", ["blockchain" => $blockchain->code]) }}',
            method: 'GET',
            success: function(res) {
                if (res.status === 'success') {
                    $('#evm-balance-text').text(parseFloat(res.balance).toFixed(4));
                    $('#evm-fiat-text').text(res.formatted_balance.split(' ≈ ')[1] || '$0.00');
                    
                    // Update dynamic token holdings values in the DOM
                    if (res.token_balances) {
                        for (let sym in res.token_balances) {
                            let el = $('#evm-' + sym.toLowerCase() + '-holdings');
                            if (el.length) {
                                el.html(parseFloat(res.token_balances[sym]).toFixed(2) + ' <span class="text-[10px] text-slate-400 font-normal font-sans">' + sym + '</span>');
                            }
                        }
                    }

                    Notify('success', '{{ __('Balances updated successfully.') }}');
                }
                resetRefreshButton();
            },
            error: function() {
                Notify('error', '{{ __('Failed to refresh balances.') }}');
                resetRefreshButton();
            }
        });
    }

    function resetRefreshButton() {
        $('#btn-balance-refresh').removeClass('pointer-events-none opacity-50');
        $('#refresh-button-text').text('{{ __('Refresh') }}');
    }

    // Auto-refresh balances every 10 seconds
    setInterval(function() {
        $.ajax({
            url: '{{ route("admin.evm-master-wallet.balance", ["blockchain" => $blockchain->code]) }}',
            method: 'GET',
            success: function(res) {
                if (res.status === 'success') {
                    $('#evm-balance-text').text(parseFloat(res.balance).toFixed(4));
                    $('#evm-fiat-text').text(res.formatted_balance.split(' ≈ ')[1] || '$0.00');
                    if (res.token_balances) {
                        for (let sym in res.token_balances) {
                            let el = $('#evm-' + sym.toLowerCase() + '-holdings');
                            if (el.length) {
                                el.html(parseFloat(res.token_balances[sym]).toFixed(2) + ' <span class="text-[10px] text-slate-400 font-normal font-sans">' + sym + '</span>');
                            }
                        }
                    }
                }
            }
        });
    }, 10000);
</script>
@endpush
