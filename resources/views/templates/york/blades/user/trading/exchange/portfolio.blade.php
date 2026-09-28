@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
    <div class="min-h-screen relative space-y-12 pb-24">
        <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>

        <div class="relative z-10 max-w-7xl mx-auto space-y-12">

            {{-- ══ HERO ══════════════════════════════════════════════════════════ --}}
            <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                     style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">

                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                        <div class="max-w-2xl space-y-4">
                            <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary">
                                <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                                <span>{{ __('Live Balances') }}</span>
                            </div>

                            <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                                {{ __('Wallet') }}
                            </h1>

                            <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                                {{ __('Every connected account, valued in your quote asset. Balances refresh once a minute in the background.') }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl shrink-0">
                            <a href="{{ route('user.trading.exchanges.index') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Connections') }}
                            </a>
                            <a href="{{ route('user.trading.orders.index') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Trade') }}
                            </a>
                            <a href="{{ route('user.trading.orders.logs') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Logs') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @include('templates.' . config('site.template') . '.blades.user.trading.exchange.partials.wallet_widget', [
                'portfolio' => $portfolio,
            ])

            {{-- ══ PER-ACCOUNT HOLDINGS ═══════════════════════════════════════════ --}}
            @php
                $accounts = $portfolio['accounts'] ?? [];
            @endphp

            @forelse ($accounts as $key => $account)
                <div class="space-y-6">

                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('assets/images/exchanges/' . strtolower($account['exchange']) . '.svg') }}"
                                class="w-6 h-6 rounded-lg grayscale opacity-80"
                                onerror="this.style.display='none'" alt="{{ $account['exchange'] }}">
                            <h2 class="text-xl font-black text-white tracking-tight uppercase">
                                {{ $account['exchange'] }}
                                <span class="text-slate-600">/</span>
                                <span class="text-slate-400">{{ $account['market_type'] }}</span>
                            </h2>
                        </div>

                        <div class="text-right">
                            <div class="text-2xl font-black text-white font-mono">
                                {{ number_format((float) $account['total'], 2) }}
                                <span class="text-sm text-slate-500">{{ $account['quote_asset'] ?? $portfolio['quote_asset'] }}</span>
                            </div>
                            @if (!empty($account['fetched_at']))
                                <div class="text-[9px] text-slate-500 uppercase tracking-widest font-bold">
                                    {{ \Carbon\Carbon::createFromTimestamp($account['fetched_at'])->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    </div>

                    @if (!empty($account['error']))
                        <div class="p-5 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-start gap-3">
                            <svg class="w-4 h-4 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v4m0 4h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z" />
                            </svg>
                            <div>
                                <p class="text-[11px] font-black text-rose-200">{{ __('Exchange unreachable') }}</p>
                                <p class="text-[11px] text-rose-300/80 font-mono mt-1 break-words">{{ $account['error'] }}</p>
                            </div>
                        </div>
                    @else
                        <div class="relative rounded-[2rem] p-1.5 bg-white/[0.03] border border-white/10">
                            <div class="rounded-[calc(2rem-0.375rem)] p-6 sm:p-8 overflow-x-auto">
                                <table class="w-full text-left border-separate border-spacing-y-3">
                                    <thead>
                                        <tr class="text-[9px] text-slate-500 uppercase tracking-[0.2em] font-black">
                                            <th class="px-4 py-2 text-left">{{ __('Asset') }}</th>
                                            <th class="px-4 py-2 text-right">{{ __('Free') }}</th>
                                            <th class="px-4 py-2 text-right">{{ __('Locked') }}</th>
                                            <th class="px-4 py-2 text-right">{{ __('Total') }}</th>
                                            <th class="px-4 py-2 text-right">{{ __('Value') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($account['assets'] as $asset)
                                            <tr class="group bg-white/[0.02] hover:bg-white/[0.04] border border-white/5 transition-all">
                                                <td class="px-4 py-4 first:rounded-l-xl border-y border-l border-white/5">
                                                    <span class="text-xs font-black text-white font-mono">{{ $asset['asset'] }}</span>
                                                </td>
                                                <td class="px-4 py-4 border-y border-white/5 text-right font-mono text-[11px] text-slate-300">
                                                    {{ number_format((float) $asset['free'], 8) }}
                                                </td>
                                                <td class="px-4 py-4 border-y border-white/5 text-right font-mono text-[11px] text-slate-500">
                                                    {{ number_format((float) $asset['locked'], 8) }}
                                                </td>
                                                <td class="px-4 py-4 border-y border-white/5 text-right font-mono text-[11px] font-bold text-white">
                                                    {{ number_format((float) $asset['total'], 8) }}
                                                </td>
                                                <td class="px-4 py-4 last:rounded-r-xl border-y border-r border-white/5 text-right font-mono text-[11px] font-black text-accent-primary">
                                                    {{ number_format((float) $asset['usd_value'], 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-10 text-center text-[11px] text-slate-500 font-bold">
                                                    {{ __('This account holds no balance.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 max-w-lg mx-auto text-center">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-12">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 7h18M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M3 7l2-3h14l2 3" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">{{ __('No Wallet Yet') }}</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                            {{ __('Connect an exchange to see your balances here.') }}
                        </p>
                        <a href="{{ route('user.trading.exchanges.index') }}"
                           class="inline-block mt-6 px-6 py-3 rounded-xl bg-accent-primary text-black font-black text-[10px] uppercase tracking-widest hover:bg-accent-primary/90 transition-all">
                            {{ __('Connect Exchange') }}
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
