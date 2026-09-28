@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.tron-master-wallet.index') }}"
                       class="p-2 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-slate-300 hover:text-white transition-all active:scale-95 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-linear-to-r from-white via-orange-200 to-red-400 tracking-tight leading-tight">
                            {{ __('Tron Master Wallet Details') }}
                        </h2>
                        <p class="text-orange-200/60 font-mono text-[9px] md:text-xs mt-0.5 tracking-widest uppercase">
                            {{ __('Wallet telemetry and recent platform activity') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 text-[10px] {{ $rpcStatus === __('Connected') ? 'text-emerald-400/80 bg-emerald-500/5 border-emerald-500/10' : 'text-amber-400/80 bg-amber-500/5 border-amber-500/10' }} font-mono border px-3 py-1.5 rounded-xl">
                <span class="w-1.5 h-1.5 rounded-full {{ $rpcStatus === __('Connected') ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                {{ __('TRON RPC: :status', ['status' => strtoupper($rpcStatus)]) }}
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="relative bg-secondary border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-colors p-6">
                <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-32 h-32 bg-red-600/10 rounded-full blur-2xl pointer-events-none z-0"></div>

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
                                <button type="button" onclick="showMasterWalletQrModal('Tron', '{{ addslashes($address) }}')"
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
                            <div class="text-xs font-bold text-white uppercase">{{ __('Tron Mainnet') }}</div>
                        </div>
                        <div class="p-3 bg-white/2 border border-white/5 rounded-xl">
                            <div class="text-[8px] font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Blockchain') }}</div>
                            <div class="text-xs font-bold text-orange-400 uppercase">{{ __('TRX (TRC20)') }}</div>
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
                        <div class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-linear-to-r from-white via-orange-100 to-red-300 tracking-tight leading-none font-mono">
                            {{ number_format($balance, 4) }} <span class="text-sm font-black text-slate-400 font-sans">TRX</span>
                        </div>
                        <div class="text-xs text-emerald-400 font-bold tracking-wide mt-2">
                            {{ $formattedBalance }}
                        </div>
                    </div>

                    <div class="text-[10px] text-slate-500 italic flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ $rpcStatus === __('Connected') ? 'bg-orange-500 animate-ping' : 'bg-amber-500' }}"></span>
                        {{ __('Native TRX balance queried from the configured Tron RPC node.') }}
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
                            {{ $blockchain->rpc_url ?: getSetting('tron_rpc_url', 'https://api.trongrid.io') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Quick Links') }}</div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ str_replace('{address}', $address, $blockchain->explorer_url_live ?: 'https://tronscan.org/#/address/{address}') }}" target="_blank"
                               class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[9px] font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>
                                </svg>
                                {{ __('Tronscan') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-secondary border border-white/5 rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>

            <div class="relative z-10 space-y-5">
                <div class="flex items-center justify-between border-b border-white/5 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ __('TRC20 Token Balances') }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ __('Showing balances for enabled Tron tokens configured under this blockchain.') }}</p>
                    </div>
                    <div class="text-[10px] font-bold text-slate-500 uppercase font-mono flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        {{ __('Token Holdings') }}
                    </div>
                </div>

                @if (empty($tokenBalances))
                    <div class="text-xs text-slate-500 italic">{{ __('No enabled TRC20 token configuration found for this wallet.') }}</div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach ($tokenBalances as $tokenBalance)
                            @php
                                $symbol = strtoupper($tokenBalance['symbol']);
                                $logo = !empty($tokenBalance['logo']) ? asset($tokenBalance['logo']) : asset('assets/images/tokens/' . strtolower($symbol) . '.png');
                            @endphp
                            <div class="rounded-xl bg-black/20 border border-white/10 p-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $logo }}" alt="{{ $symbol }}" class="w-6 h-6 rounded-full object-cover bg-white/5 border border-white/10"
                                         onerror="this.onerror=null;this.src='{{ asset('assets/images/tokens/default.png') }}';">
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ $symbol }}</div>
                                </div>
                                <div class="mt-2 text-xl font-black text-white font-mono">{{ number_format((float) ($tokenBalance['balance'] ?? 0), 8) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-secondary border border-white/5 rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('{{ asset('/assets/images/noise.svg') }}')] opacity-10 pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                <div class="flex items-center justify-between border-b border-white/5 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ __('Recent Transfers & Interactions') }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ __('Displaying recent on-chain activity with local platform records as fallback.') }}</p>
                    </div>
                    <div class="text-[10px] font-bold text-slate-500 uppercase font-mono flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                        {{ __('Recent Activity') }}
                    </div>
                </div>

                @if ($transactions->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-center space-y-3">
                        <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-white uppercase tracking-wider">{{ __('No Transactions Found') }}</h4>
                        <p class="text-xs text-slate-500 max-w-sm leading-relaxed">
                            {{ __('No recent on-chain transactions were detected for this Tron wallet yet.') }}
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-white/5 text-[10px] uppercase tracking-wider text-slate-500">
                                    <th class="py-3 px-4">{{ __('Date / Time') }}</th>
                                    <th class="py-3 px-4">{{ __('Reference / Hash') }}</th>
                                    <th class="py-3 px-4 text-center">{{ __('Direction') }}</th>
                                    <th class="py-3 px-4 text-right">{{ __('Amount') }}</th>
                                    <th class="py-3 px-4 text-center">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-xs text-slate-300 font-medium">
                                @foreach ($transactions as $tx)
                                    <tr class="hover:bg-white/1 transition-colors group">
                                        <td class="py-4 px-4 font-mono text-slate-400 whitespace-nowrap">
                                            {{ showDateTime($tx['created_at']) }}
                                        </td>
                                        <td class="py-4 px-4 font-mono text-slate-300">
                                            <div class="flex items-center gap-2">
                                                @if (!empty($tx['tx_explorer_url']))
                                                    <a href="{{ $tx['tx_explorer_url'] }}" target="_blank" class="truncate max-w-55 md:max-w-95 text-orange-300 hover:text-orange-200 hover:underline">{{ $tx['hash'] }}</a>
                                                @else
                                                    <span class="truncate max-w-55 md:max-w-95">{{ $tx['hash'] }}</span>
                                                @endif
                                                @if (($tx['source'] ?? 'db') === 'onchain')
                                                    <span class="px-1.5 py-0.5 rounded border border-emerald-500/20 bg-emerald-500/10 text-[9px] uppercase tracking-wider text-emerald-300 font-bold">{{ __('On-chain') }}</span>
                                                @endif
                                                <button type="button" onclick="copyToClipboard('{{ $tx['hash'] }}', '{{ __('Reference copied to clipboard') }}')" class="text-slate-500 hover:text-slate-300 p-0.5 rounded transition-colors cursor-pointer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @php
                                                $directionClass = match ($tx['direction'] ?? 'unknown') {
                                                    'incoming' => 'text-emerald-300 bg-emerald-500/10 border-emerald-500/20',
                                                    'outgoing' => 'text-amber-300 bg-amber-500/10 border-amber-500/20',
                                                    default => 'text-slate-400 bg-white/5 border-white/10',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[10px] font-bold uppercase tracking-widest {{ $directionClass }}">
                                                <span class="w-1 h-1 rounded-full {{ str_contains($directionClass, 'emerald') ? 'bg-emerald-400' : (str_contains($directionClass, 'amber') ? 'bg-amber-400' : 'bg-slate-400') }}"></span>
                                                {{ ucfirst($tx['direction'] ?? 'unknown') }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right font-mono text-white">
                                            {{ number_format((float) $tx['amount'], 8) }} {{ $tx['currency'] }}
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @php
                                                $statusClass = match ($tx['status']) {
                                                    'completed' => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                                                    'pending' => 'text-amber-400 bg-amber-500/10 border-amber-500/20',
                                                    'failed' => 'text-red-400 bg-red-500/10 border-red-500/20',
                                                    default => 'text-slate-400 bg-white/5 border-white/10',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[10px] font-bold uppercase tracking-widest {{ $statusClass }}">
                                                <span class="w-1 h-1 rounded-full {{ str_contains($statusClass, 'emerald') ? 'bg-emerald-400' : (str_contains($statusClass, 'amber') ? 'bg-amber-400' : (str_contains($statusClass, 'red') ? 'bg-red-400' : 'bg-slate-400')) }}"></span>
                                                {{ ucfirst($tx['status']) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection