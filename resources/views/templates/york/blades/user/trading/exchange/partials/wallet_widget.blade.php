{{--
    Wallet widget: available balance, asset allocation and total wallet value.

    Expects:
        $portfolio  array from ExchangeBalanceService::portfolioFor()

    Rendered server-side from cache. The refresh button only re-reads the
    cached portfolio, so it never blocks on an exchange round trip.
--}}
@php
    $accounts = $portfolio['accounts'] ?? [];
    $quote = $portfolio['quote_asset'] ?? 'USDT';
    $total = (float) ($portfolio['total'] ?? 0);

    // Allocation is computed across every account, because a user with a spot
    // wallet on one venue and a futures wallet on another still has one
    // portfolio. Each asset is summed before the percentage is derived, so an
    // asset held in two accounts is not counted twice as two bars.
    $allocation = [];

    foreach ($accounts as $account) {
        foreach ($account['assets'] ?? [] as $asset) {
            $name = $asset['asset'];

            if (!isset($allocation[$name])) {
                $allocation[$name] = ['total' => 0.0, 'usd_value' => 0.0];
            }

            $allocation[$name]['total'] += (float) $asset['total'];
            $allocation[$name]['usd_value'] += (float) $asset['usd_value'];
        }
    }

    arsort($allocation);

    // Cap the bar chart at six rows: past that the slice is noise, and the full
    // breakdown lives one click away on the wallet page.
    $top = array_slice($allocation, 0, 6, true);
@endphp

<div id="wallet-widget" class="relative rounded-[2rem] p-1.5 bg-white/[0.03] border border-white/10 backdrop-blur-2xl">
    <div class="rounded-[calc(2rem-0.375rem)] p-6 sm:p-8 relative overflow-hidden"
         style="background: radial-gradient(circle at 90% 0%, rgba(226,177,60,0.07) 0%, rgba(6,8,12,0.99) 65%);">

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-1">
                <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 block">
                    {{ __('Total Wallet Value') }}
                </span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl sm:text-4xl font-black text-white font-mono tracking-tight"
                          id="wallet-total"
                          data-total="{{ $total }}">
                        {{ number_format($total, 2) }}
                    </span>
                    <span class="text-sm font-black text-slate-500">{{ $quote }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('user.trading.exchanges.portfolio') }}"
                   class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-white/10 hover:text-white transition-all">
                    {{ __('Full Wallet') }}
                </a>

                <button type="button" id="wallet-refresh"
                    class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-white/10 hover:text-white transition-all flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 4v6h6M20 20v-6h-6M5.5 15a7 7 0 0011.9 2M18.5 9A7 7 0 006.1 7" />
                    </svg>
                    {{ __('Refresh') }}
                </button>
            </div>
        </div>

        @if ($portfolio['has_failures'] ?? false)
            <div class="mt-5 flex items-start gap-2.5 p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20">
                <svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v4m0 4h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z" />
                </svg>
                <p class="text-[11px] font-bold text-amber-200 leading-relaxed">
                    {{ __('One or more exchanges could not be reached. The totals below exclude those accounts.') }}
                </p>
            </div>
        @endif

        <div class="mt-7 grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Available balance per account --}}
            <div class="space-y-3">
                <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 block">
                    {{ __('Available Balance') }}
                </span>

                @forelse ($accounts as $account)
                    <div class="flex items-center justify-between gap-3 p-3.5 rounded-2xl bg-white/[0.02] border border-white/8">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset('assets/images/exchanges/' . strtolower($account['exchange']) . '.svg') }}"
                                class="w-5 h-5 rounded-lg grayscale opacity-80 shrink-0"
                                onerror="this.style.display='none'" alt="{{ $account['exchange'] }}">
                            <div class="min-w-0">
                                <div class="text-[11px] font-black text-white uppercase tracking-wider truncate">
                                    {{ $account['exchange'] }}
                                    <span class="text-slate-600">/</span>
                                    <span class="text-slate-400">{{ $account['market_type'] }}</span>
                                </div>
                                <div class="text-[9px] text-slate-500 font-mono truncate">
                                    {{ $account['api_key_hint'] ?? '' }}
                                </div>
                            </div>
                        </div>

                        <div class="text-right shrink-0 max-w-[16rem]">
                            @if (!empty($account['error']))
                                {{--
                                    The exchange's own words, not just "Unavailable".
                                    A refused credential is otherwise
                                    indistinguishable from a network fault, and
                                    those two need completely different fixes. The
                                    full message sits in the title for the ones
                                    that get clipped.
                                --}}
                                <span class="text-[9px] font-black uppercase tracking-widest text-rose-400">
                                    {{ __('Unavailable') }}
                                </span>
                                <p class="text-[10px] text-rose-300/70 font-mono leading-snug mt-1 line-clamp-3"
                                   title="{{ $account['error'] }}">
                                    {{ $account['error'] }}
                                </p>
                            @else
                                <div class="text-xs font-black text-white font-mono">
                                    {{ number_format((float) $account['free_quote'], 2) }}
                                    <span class="text-slate-500">{{ $account['quote_asset'] ?? $quote }}</span>
                                </div>
                                <div class="text-[9px] text-slate-500 font-mono">
                                    {{ number_format((float) $account['total'], 2) }} {{ __('total') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-6 rounded-2xl bg-white/[0.02] border border-dashed border-white/10 text-center">
                        <p class="text-[11px] font-bold text-slate-500 leading-relaxed">
                            {{ __('No exchange connected yet. Add your API credentials to see balances here.') }}
                        </p>
                        <a href="{{ route('user.trading.exchanges.index') }}"
                           class="inline-block mt-3 px-4 py-2 rounded-xl bg-accent-primary text-black font-black text-[10px] uppercase tracking-widest hover:bg-accent-primary/90 transition-all">
                            {{ __('Connect Exchange') }}
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Asset allocation --}}
            <div class="space-y-3">
                <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 block">
                    {{ __('Asset Allocation') }}
                </span>

                @if ($top === [])
                    <div class="p-6 rounded-2xl bg-white/[0.02] border border-dashed border-white/10 text-center">
                        <p class="text-[11px] font-bold text-slate-500">
                            {{ __('Nothing to allocate yet.') }}
                        </p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($top as $name => $slice)
                            @php
                                // A holding priced at zero (the exchange did not
                                // return a ticker for it) must not be rendered as
                                // a 100% slice, so the bar falls back to its share
                                // of units.
                                $pct = $total > 0 ? ($slice['usd_value'] / $total) * 100 : 0;
                                $barWidth = $pct > 0 ? max(2, min(100, $pct)) : 4;
                            @endphp

                            <div>
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <span class="text-[11px] font-black text-white font-mono">{{ $name }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 font-mono">
                                        {{ number_format($slice['total'], 8) }}
                                        @if ($pct > 0)
                                            <span class="text-slate-600">·</span>
                                            <span class="text-accent-primary">{{ number_format($pct, 1) }}%</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="h-1.5 rounded-full bg-white/5 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-accent-primary to-purple-500 transition-all duration-500"
                                         style="width: {{ $barWidth }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
