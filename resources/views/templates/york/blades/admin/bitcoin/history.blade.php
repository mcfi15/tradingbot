@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.bitcoin-master-wallet.index') }}"
                       class="p-2 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-slate-300 hover:text-white transition-all active:scale-95 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-orange-200 to-amber-400 tracking-tight leading-tight">
                            {{ __('Bitcoin Master Wallet Details') }}
                        </h2>
                        <p class="text-amber-200/60 font-mono text-[9px] md:text-xs mt-0.5 tracking-widest uppercase">
                            {{ __('Wallet telemetry and recent platform activity') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 text-[10px] {{ $rpcStatus === __('Connected') ? 'text-emerald-400/80 bg-emerald-500/5 border-emerald-500/10' : 'text-amber-400/80 bg-amber-500/5 border-amber-500/10' }} font-mono border px-3 py-1.5 rounded-xl">
                <span class="w-1.5 h-1.5 rounded-full {{ $rpcStatus === __('Connected') ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                {{ __('BITCOIN RPC: :status', ['status' => strtoupper($rpcStatus)]) }}
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors p-6">
                <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-32 h-32 bg-amber-600/10 rounded-full blur-2xl pointer-events-none z-0"></div>

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
                                <button type="button" onclick="showMasterWalletQrModal('Bitcoin', '{{ addslashes($address) }}')"
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
                        <div class="p-3 bg-white/2 border border-white/5 rounded-xl">
                            <div class="text-[8px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Network Type') }}</div>
                            <div class="text-xs font-bold text-white uppercase">{{ __('Bitcoin Mainnet') }}</div>
                        </div>
                        <div class="p-3 bg-white/2 border border-white/5 rounded-xl">
                            <div class="text-[8px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Blockchain') }}</div>
                            <div class="text-xs font-bold text-amber-400 uppercase">{{ __('BTC (Bech32)') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors p-6">
                <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-32 h-32 bg-orange-500/10 rounded-full blur-2xl pointer-events-none z-0"></div>

                <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Wallet Balance') }}</span>
                    </div>

                    <div class="my-auto py-2">
                        <div class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-orange-100 to-amber-300 tracking-tight leading-none font-mono">
                            {{ number_format($balance, 8) }} <span class="text-sm font-black text-slate-400 font-sans">BTC</span>
                        </div>
                        <div class="text-xs text-emerald-400 font-bold tracking-wide mt-2">
                            {{ $formattedBalance }}
                        </div>
                    </div>

                    <div class="text-[10px] text-slate-500 italic flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ $rpcStatus === __('Connected') ? 'bg-orange-500 animate-ping' : 'bg-amber-500' }}"></span>
                        {{ __('Native BTC balance queried from the configured Bitcoin Esplora API.') }}
                    </div>
                </div>
            </div>

            <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors p-6">
                <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none z-0"></div>

                <div class="relative z-10 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Explorer & RPC') }}</span>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('Active Node URL') }}</div>
                        <div class="font-mono text-[10px] text-slate-300 bg-black/20 border border-white/5 p-3 rounded-xl break-all">
                            {{ $blockchain->rpc_url ?: 'https://blockstream.info/api' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Quick Links') }}</div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ str_replace('{address}', $address, $blockchain->explorer_url_live ?: 'https://blockstream.info/address/{address}') }}" target="_blank"
                               class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[9px] font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>
                                </svg>
                                {{ __('Blockstream.info') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-secondary border border-white/5 rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>

            <div class="relative z-10 space-y-4">
                <div class="border-b border-white/5 pb-4">
                    <h3 class="text-lg font-bold text-white">{{ __('On-Chain Transaction History') }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('Direct ledger state fetched from the Bitcoin public explorer nodes.') }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-white/5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-4">{{ __('Transaction Hash') }}</th>
                                <th class="py-3 px-4">{{ __('Direction') }}</th>
                                <th class="py-3 px-4">{{ __('Amount') }}</th>
                                <th class="py-3 px-4">{{ __('Fee') }}</th>
                                <th class="py-3 px-4">{{ __('Block Height') }}</th>
                                <th class="py-3 px-4">{{ __('Timestamp') }}</th>
                                <th class="py-3 px-4">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-xs text-slate-300">
                            @forelse ($onChainTransactions as $tx)
                                <tr class="hover:bg-white/[0.01] transition-colors">
                                    <td class="py-3 px-4 font-mono">
                                        <a href="{{ $tx['explorer_url'] }}" target="_blank" class="text-amber-400 hover:underline">
                                            {{ substr($tx['hash'], 0, 10) }}...{{ substr($tx['hash'], -10) }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if ($tx['direction'] === 'in')
                                            <span class="text-[9px] font-bold text-emerald-400 uppercase tracking-wider bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/20">{{ __('Deposit') }}</span>
                                        @else
                                            <span class="text-[9px] font-bold text-red-400 uppercase tracking-wider bg-red-500/10 px-2 py-0.5 rounded-md border border-red-500/20">{{ __('Payout') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-mono font-bold text-white">
                                        {{ number_format($tx['amount'], 8) }} BTC
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-500">
                                        {{ number_format($tx['fee'], 8) }} BTC
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-400">
                                        {{ $tx['block'] ?? __('Mempool') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        {{ $tx['timestamp']->diffForHumans() }}
                                        <span class="block text-[10px] text-slate-500">{{ $tx['timestamp']->format('Y-m-d H:i:s') }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $tx['status'] === __('Confirmed') ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse' }}"></span>
                                            {{ $tx['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-500 italic">
                                        {{ __('No transactions found for this master wallet.') }}
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
