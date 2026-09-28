@if ($last_error_message)
    <div class="rounded-2xl p-4 mb-6 border border-red-500/30 bg-red-500/10 text-red-400 text-xs font-medium flex items-center gap-3">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $last_error_message }}</span>
    </div>
@endif

<div class="relative z-10 max-w-7xl mx-auto space-y-8">

    {{-- ══ CONTROL DECK HEADER ══════════════════════════════════════════════ --}}
    <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
        <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-10 overflow-hidden relative"
             style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">
            
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div class="max-w-2xl space-y-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary shadow-[0_0_15px_rgba(226,177,60,0.2)]">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                        <span>{{ __('Copy Trading') }}</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                        {{ __('Copy Trading') }}
                    </h1>

                    <p class="text-slate-400 text-xs sm:text-sm font-normal leading-relaxed max-w-xl">
                        {{ __('Copy expert trades in real time. Enter a bot code to start copy trading.') }}
                    </p>
                </div>

                {{-- Floating Island Navigation Sub-Pills --}}
                <div class="flex flex-wrap items-center gap-2.5 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl shrink-0">
                    <a href="{{ route('user.copy-trading.index') }}"
                       class="px-5 py-2.5 rounded-full bg-accent-primary text-black font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(226,177,60,0.3)] transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <span>{{ __('Terminal') }}</span>
                    </a>

                    <a href="{{ route('user.copy-trading.history') }}"
                       class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>{{ __('History') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ TOP ASSET STATS BAR ══════════════════════════════════════════════ --}}
    @if (!$last_error_message || !empty($current_ticker_info))
        <div id="topPanelStats" class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8 relative z-40">
            <div class="rounded-[calc(2rem-0.25rem)] p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4" style="background: rgba(8,10,15,0.95);">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <button id="pairDropdownBtn"
                                class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold text-sm transition-all">
                            <span>{{ $current_ticker }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div id="pairDropdownMenu"
                            class="absolute top-full left-0 mt-2 w-64 rounded-2xl border border-white/10 shadow-2xl overflow-hidden hidden z-50 bg-[#08090e]">
                            <div class="p-3 border-b border-white/10">
                                <input type="text" placeholder="{{ __('Search Ticker...') }}" id="pairSearch"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-accent-primary transition-all">
                            </div>
                            <div class="max-h-60 overflow-y-auto">
                                @foreach ($all_crypto_tickers as $asset)
                                    <a href="{{ route('user.copy-trading.index', ['ticker' => $asset['ticker']]) }}"
                                        class="pair-item px-4 py-2.5 text-xs text-slate-300 hover:bg-white/10 hover:text-white transition-all flex items-center justify-between">
                                        <span class="font-bold">{{ $asset['ticker'] }}</span>
                                        @if ($asset['ticker'] == $current_ticker)
                                            <span class="w-2 h-2 rounded-full bg-accent-primary"></span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pl-3 border-l border-white/10">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">{{ __('Price:') }}</span>
                        <span class="text-base font-black text-white font-mono" id="lastPrice">
                            @php $price = $current_ticker_info['current_price'] ?? 0; @endphp
                            {{ number_format($price, $price < 1 ? 4 : 2) }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/[0.03] border border-white/5">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">{{ __('1D Change:') }}</span>
                        <span class="text-xs font-black font-mono {{ ($current_ticker_info['change_1d_percentage'] ?? 0) < 0 ? 'text-red-400' : 'text-emerald-400' }}">
                            {{ ($current_ticker_info['change_1d_percentage'] ?? 0) > 0 ? '+' : '' }}{{ $current_ticker_info['change_1d_percentage'] ?? '0.00' }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══ MAIN GRID: CHART & ACTIVATION FORM ══════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Left: Live TradingView Chart --}}
        <div class="lg:col-span-8 rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-6 space-y-4" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400">{{ __('Live Chart') }}</h3>
                    <div id="chartTime" class="text-[10px] font-mono text-slate-500">--:--:--</div>
                </div>

                <div class="relative h-[320px] sm:h-[420px] rounded-2xl border border-white/10 bg-[#08090e] overflow-hidden">
                    <div id="chartLoader" class="absolute inset-0 z-20 flex items-center justify-center bg-[#08090e]/80 backdrop-blur-sm">
                        <div class="w-8 h-8 border-2 border-accent-primary border-t-transparent rounded-full animate-spin"></div>
                    </div>
                    <div id="chartContainer" class="absolute inset-0 z-10 w-full h-full"></div>
                </div>
            </div>
        </div>

        {{-- Right: Copy Strategy Mirror Activation --}}
        <div class="lg:col-span-4 rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                
                <div class="space-y-1">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-accent-primary">{{ __('Copy Trading') }}</span>
                    <h3 class="text-xl font-black text-white tracking-tight">{{ __('Copy Trader') }}</h3>
                </div>

                {{-- Demo Codes Pill Notice --}}
                @if (config('app.env') === 'sandbox' && count($active_sandbox_codes) > 0)
                    <div class="p-3.5 rounded-2xl bg-accent-primary/5 border border-accent-primary/15 space-y-2">
                        <span class="text-[9px] font-bold text-accent-primary uppercase tracking-widest block">{{ __('Sample Codes:') }}</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($active_sandbox_codes->take(5) as $code)
                                <button type="button"
                                        onclick="document.getElementById('copyTradingCode').value='{{ $code->code }}'; checkTradingCode('{{ $code->code }}');"
                                        class="px-2.5 py-1 rounded-xl bg-white/5 hover:bg-accent-primary/20 border border-white/10 text-[10px] font-bold text-white transition-all cursor-pointer">
                                    {{ $code->code }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Code Input --}}
                <div class="space-y-2">
                    <label class="text-[10px] text-slate-400 uppercase font-black tracking-widest">{{ __('Enter Bot Code') }}</label>
                    <div class="relative">
                        <input type="text" id="copyTradingCode"
                               class="w-full bg-black/50 border border-white/10 rounded-2xl py-3.5 px-4 text-white text-base font-black tracking-widest focus:border-accent-primary outline-none transition-all placeholder:text-slate-600 uppercase"
                               placeholder="e.g. BTC-ALPHA">
                    </div>
                </div>

                {{-- Validated Strategy Card --}}
                <div id="tradeDetails" class="hidden space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                            <span class="block text-[9px] text-slate-500 font-bold uppercase tracking-widest mb-1">{{ __('Trading Pair') }}</span>
                            <span id="tradePair" class="text-sm font-black text-white">--</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                            <span class="block text-[9px] text-slate-500 font-bold uppercase tracking-widest mb-1">{{ __('Target Return') }}</span>
                            <span id="tradeRoi" class="text-sm font-black text-emerald-400">--</span>
                        </div>
                    </div>

                    <div id="amountInputContainer" class="space-y-2">
                        <div class="flex justify-between items-center text-[10px]">
                            <label class="text-slate-400 font-black uppercase tracking-widest">{{ __('Investment Amount') }}</label>
                            <span class="text-slate-500 font-bold">{{ __('Available:') }} <strong id="availableBalanceValue" class="text-white font-black">{{ number_format($add_available, 2) }}</strong> {{ getSetting('currency') }}</span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-accent-primary font-black text-sm">{{ getSetting('currency_symbol', '$') }}</span>
                            </div>
                            <input type="number" id="inputAmount" step="any"
                                   class="w-full bg-black/50 border border-white/10 rounded-2xl py-3.5 pl-10 pr-4 text-white text-lg font-bold focus:border-accent-primary outline-none transition-all placeholder:text-slate-600"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <div id="percentageInfoContainer" class="hidden p-4 rounded-2xl bg-accent-primary/10 border border-accent-primary/20">
                        <h4 class="text-white font-bold text-xs">{{ __('Percentage Mode') }}</h4>
                        <p class="text-slate-300 text-[11px] mt-1 leading-relaxed">
                            {{ __('This trade will automatically allocate') }} <span id="displayPercentage" class="text-accent-primary font-black">--</span>% {{ __('of your balance.') }}
                        </p>
                    </div>
                </div>

                {{-- Action Button --}}
                <button id="btnActivate" disabled
                        class="w-full py-4 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_4px_20px_rgba(226,177,60,0.3)] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    {{ __('Start Copy Trade') }}
                </button>

            </div>
        </div>

    </div>

    {{-- ══ RECENT COPY ACTIVATIONS TABLE ════════════════════════════════════ --}}
    <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl overflow-hidden">
        <div class="rounded-[calc(2.5rem-0.375rem)] p-8 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
            
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-white tracking-tight">{{ __('Recent Copy Trades') }}</h3>
                    <p class="text-xs text-slate-400 font-medium mt-1">{{ __('Your most recent copy trading activity.') }}</p>
                </div>
            </div>

            <div class="overflow-x-auto" id="activationsTable">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead>
                        <tr class="text-[10px] text-slate-500 uppercase tracking-[0.2em] font-black">
                            <th class="px-4 py-3">{{ __('Bot Code') }}</th>
                            <th class="px-4 py-3">{{ __('Pair') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Invested') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Profit') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('ROI') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Activated At') }}</th>
                            <th class="px-4 py-3 text-center">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        @forelse($activations as $activation)
                            <tr class="bg-white/[0.02] border border-white/5 rounded-2xl hover:bg-white/[0.05] transition-all">
                                <td class="px-4 py-4 font-black text-accent-primary font-mono">{{ $activation->copy_code }}</td>
                                <td class="px-4 py-4 font-bold text-white">{{ $activation->pair }}</td>
                                <td class="px-4 py-4 text-right font-mono font-bold text-white">{{ showAmount($activation->amount) }}</td>
                                <td class="px-4 py-4 text-right font-mono font-bold text-emerald-400">
                                    {{ $activation->status === 'active' ? '--' : showAmount($activation->profit) }}
                                </td>
                                <td class="px-4 py-4 text-right font-bold {{ $activation->roi < 0 ? 'text-red-400' : 'text-emerald-400' }}">
                                    {{ $activation->roi > 0 ? '+' : '' }}{{ $activation->roi }}%
                                </td>
                                <td class="px-4 py-4 text-right text-slate-400 font-medium">
                                    {{ $activation->created_at ? date('M d, Y H:i', strtotime($activation->created_at)) : '--' }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $activation->status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-white/5 text-slate-400 border border-white/10' }}">
                                        {{ ucfirst($activation->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500 text-xs italic font-medium">
                                    {{ __('No copy trade activations found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
