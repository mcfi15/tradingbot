@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
    <div class="min-h-screen relative space-y-12 pb-24">
        <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
        <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-emerald-500/5 via-cyan-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

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
                                <span>{{ __('Order Entry') }}</span>
                            </div>

                            <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                                {{ __('Place a Trade') }}
                            </h1>

                            <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                                {{ __('Orders go straight to your exchange. Your risk limits are checked before anything is sent.') }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl shrink-0">
                            <a href="{{ route('user.trading.orders.index') }}"
                               class="px-5 py-2.5 rounded-full bg-accent-primary text-black font-black text-xs uppercase tracking-wider transition-all">
                                {{ __('Ticket') }}
                            </a>
                            <a href="{{ route('user.trading.signals.index') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Signals') }}
                            </a>
                            <a href="{{ route('user.trading.orders.logs') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Logs') }}
                            </a>
                            <a href="{{ route('user.trading.exchanges.index') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Exchanges') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if ($connections->isEmpty())
                {{-- A ticket with no credential behind it can only ever fail, so
                     the form is replaced by the one thing that can unblock it. --}}
                <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 max-w-lg mx-auto text-center">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-12">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-500/10 border border-amber-500/25 text-amber-400 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 9v4m0 4h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">{{ __('Connect an Exchange First') }}</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                            {{ __('An order needs a live exchange credential. Add your API key to start trading.') }}
                        </p>
                        <a href="{{ route('user.trading.exchanges.index') }}"
                           class="inline-block mt-6 px-6 py-3 rounded-xl bg-accent-primary text-black font-black text-[10px] uppercase tracking-widest hover:bg-accent-primary/90 transition-all">
                            {{ __('Connect Exchange') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

                    {{-- ══ ORDER TICKET ═══════════════════════════════════════════ --}}
                    <div class="xl:col-span-5">
                        <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 xl:sticky xl:top-6">
                            <div class="rounded-[calc(2.5rem-0.375rem)] p-7 sm:p-8 relative overflow-hidden"
                                 style="background: linear-gradient(135deg, rgba(15,17,21,0.98) 0%, rgba(8,9,14,0.99) 100%);">

                                <div class="flex items-center gap-3 mb-7">
                                    <div class="w-11 h-11 rounded-2xl bg-accent-primary/10 border border-accent-primary/25 text-accent-primary flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13 7h8m0 0v8m0-8L11 17l-4-4-6 6" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-black text-white tracking-tight">{{ __('Order Ticket') }}</h2>
                                        <p class="text-[10px] text-slate-500">
                                            {{ __('Spot and futures on your connected accounts.') }}
                                        </p>
                                    </div>
                                </div>

                                <form id="order-form" class="space-y-5" autocomplete="off">
                                    <input type="hidden" id="order-signal" name="signal_id" value="{{ request('signal_id') }}">

                                    {{-- Exchange account --}}
                                    <div>
                                        <label for="order-connection" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                            {{ __('Exchange Account') }}
                                        </label>
                                        <select id="order-connection" name="connection_id" required
                                            class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-bold text-white focus:border-accent-primary focus:outline-none">
                                            <option value="">{{ __('Select an account…') }}</option>
                                            @foreach ($connections as $connection)
                                                <option value="{{ $connection['id'] }}" data-market="{{ $connection['market_type'] }}"
                                                    @selected((int) request('connection_id') === $connection['id'])>
                                                    {{ strtoupper($connection['exchange']) }} — {{ $connection['market_type'] }}
                                                    @if ($connection['label']) ({{ $connection['label'] }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Pair + market --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="order-pair" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                                {{ __('Pair') }}
                                            </label>
                                            <input type="text" id="order-pair" name="pair" required maxlength="20"
                                                value="{{ request('pair') }}" placeholder="BTCUSDT"
                                                class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-mono font-bold text-white placeholder:text-slate-600 focus:border-accent-primary focus:outline-none uppercase">
                                        </div>

                                        <div>
                                            <label for="order-market" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                                {{ __('Market') }}
                                            </label>
                                            <select id="order-market" name="market_type" required
                                                class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-bold text-white focus:border-accent-primary focus:outline-none">
                                                <option value="spot" @selected(request('market_type') === 'spot')>{{ __('Spot') }}</option>
                                                <option value="futures" @selected(request('market_type') === 'futures')>{{ __('Futures / Perp') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Side --}}
                                    <div>
                                        <span class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                            {{ __('Side') }}
                                        </span>
                                        <div class="grid grid-cols-2 gap-3 p-1.5 rounded-2xl bg-white/[0.03] border border-white/10">
                                            @foreach (['buy' => __('Buy / Long'), 'sell' => __('Sell / Short')] as $value => $label)
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="side" value="{{ $value }}" class="peer sr-only"
                                                        @checked(request('side', 'buy') === $value)>
                                                    <span class="block text-center px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                                                        {{ $value === 'buy'
                                                            ? 'bg-emerald-500/10 text-emerald-300 peer-checked:bg-emerald-500 peer-checked:text-black'
                                                            : 'bg-rose-500/10 text-rose-300 peer-checked:bg-rose-500 peer-checked:text-black' }}">
                                                        {{ $label }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Order type --}}
                                    <div>
                                        <span class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                            {{ __('Order Type') }}
                                        </span>
                                        <div class="grid grid-cols-2 gap-3 p-1.5 rounded-2xl bg-white/[0.03] border border-white/10">
                                            @foreach (['market' => __('Market'), 'limit' => __('Limit')] as $value => $label)
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="order_type" value="{{ $value }}" class="peer sr-only"
                                                        @checked(request('order_type', 'market') === $value)>
                                                    <span class="block text-center px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-400 peer-checked:bg-accent-primary peer-checked:text-black transition-all">
                                                        {{ $label }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Price (limit only) + quantity --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div id="order-price-field">
                                            <label for="order-price" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                                {{ __('Limit Price') }}
                                            </label>
                                            <input type="number" id="order-price" name="price" step="any" min="0"
                                                value="{{ request('price') }}" placeholder="0.00"
                                                class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-mono font-bold text-white placeholder:text-slate-600 focus:border-accent-primary focus:outline-none">
                                        </div>

                                        <div>
                                            <label for="order-quantity" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                                {{ __('Quantity') }}
                                            </label>
                                            <input type="number" id="order-quantity" name="quantity" step="any" min="0"
                                                value="{{ request('quantity') }}" placeholder="0.00"
                                                class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-mono font-bold text-white placeholder:text-slate-600 focus:border-accent-primary focus:outline-none">
                                        </div>
                                    </div>

                                    {{-- Leaving quantity blank is deliberate: RiskManager
                                         then sizes the order from the risk percentage
                                         and the max trade size, which is what a user
                                         who does not know the exact size wants. --}}
                                    <p class="text-[10px] text-slate-600 leading-relaxed -mt-2">
                                        {{ __('Leave the quantity blank to size it automatically:') }}
                                        <span class="text-slate-400 font-bold">{{ number_format((float) $preferences->risk_percentage, 2) }}%</span>
                                        {{ __('risk, capped at') }}
                                        <span class="text-slate-400 font-bold">{{ number_format((float) $preferences->max_trade_size, 2) }} {{ $preferences->default_market_type === 'futures' ? 'USDT' : '' }}</span>.
                                    </p>

                                    {{-- Leverage (futures only) --}}
                                    <div id="order-leverage-field">
                                        <div class="flex items-center justify-between mb-2">
                                            <label for="order-leverage" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500">
                                                {{ __('Leverage') }}
                                            </label>
                                            <span class="text-[10px] font-black text-accent-primary font-mono" id="leverage-value">
                                                {{ request('leverage', 10) }}x
                                            </span>
                                        </div>
                                        <input type="range" id="order-leverage" name="leverage" min="1" max="125" step="1"
                                            value="{{ request('leverage', 10) }}"
                                            class="w-full accent-accent-primary">
                                    </div>

                                    {{-- Bracket --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="order-tp" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                                {{ __('Take Profit') }}
                                            </label>
                                            <input type="number" id="order-tp" name="take_profit_price" step="any" min="0"
                                                value="{{ request('take_profit') }}" placeholder="0.00"
                                                class="w-full px-4 py-3 rounded-xl bg-emerald-500/[0.06] border border-emerald-500/20 text-sm font-mono font-bold text-white placeholder:text-slate-600 focus:border-emerald-400 focus:outline-none">
                                        </div>

                                        <div>
                                            <label for="order-sl" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                                {{ __('Stop Loss') }}
                                            </label>
                                            <input type="number" id="order-sl" name="stop_loss_price" step="any" min="0"
                                                value="{{ request('stop_loss') }}" placeholder="0.00"
                                                class="w-full px-4 py-3 rounded-xl bg-rose-500/[0.06] border border-rose-500/20 text-sm font-mono font-bold text-white placeholder:text-slate-600 focus:border-rose-400 focus:outline-none">
                                        </div>
                                    </div>

                                    {{-- Live notional preview --}}
                                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/8 flex items-center justify-between">
                                        <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500">
                                            {{ __('Order Value') }}
                                        </span>
                                        <span class="text-sm font-black text-white font-mono" id="order-notional">
                                            —
                                        </span>
                                    </div>

                                    <button type="submit" id="order-submit"
                                        class="w-full px-6 py-4 rounded-xl bg-accent-primary text-black font-black text-[10px] uppercase tracking-widest hover:bg-accent-primary/90 active:scale-[0.98] transition-all shadow-[0_0_25px_rgba(226,177,60,0.25)] flex items-center justify-center gap-2">
                                        {{ __('Place Order') }}
                                    </button>

                                    {{-- The exchange is the authority, so the button
                                         states which way it will go rather than
                                         asking the user to remember the radio. --}}
                                    <p class="text-center text-[9px] text-slate-600 font-mono" id="order-direction-hint"></p>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- ══ OPEN POSITIONS ═══════════════════════════════════════════ --}}
                    <div class="xl:col-span-7">
                        <div class="space-y-6">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <h2 class="text-xl font-black text-white tracking-tight">
                                    {{ __('Open Positions') }}
                                    <span class="ml-2 px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-black text-slate-400 font-mono">
                                        {{ $orders->total() }}
                                    </span>
                                </h2>

                                <a href="{{ route('user.trading.orders.logs') }}"
                                    class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-white/10 hover:text-white transition-all">
                                    {{ __('Trade History') }}
                                </a>
                            </div>

                            <div id="orders-list" class="space-y-4">
                                @forelse ($orders as $order)
                                    @php
                                        $isLong = in_array($order->direction, ['buy', 'long'], true);
                                        $tone = $isLong
                                            ? ['text-emerald-400', 'bg-emerald-500/10', 'border-emerald-500/20']
                                            : ['text-rose-400', 'bg-rose-500/10', 'border-rose-500/20'];
                                    @endphp

                                    <div class="order-row relative rounded-[2rem] p-1.5 bg-white/[0.03] border border-white/10 transition-all duration-300 hover:border-accent-primary/40"
                                        data-order-id="{{ $order->id }}">

                                        <div class="rounded-[calc(2rem-0.375rem)] p-6 relative overflow-hidden"
                                            style="background: linear-gradient(135deg, rgba(15,17,21,0.98) 0%, rgba(8,9,14,0.99) 100%);">

                                            <div class="flex flex-wrap items-start justify-between gap-4">
                                                <div class="min-w-0">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="text-base font-black text-white font-mono">
                                                            {{ $order->pair }}
                                                        </span>
                                                        <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border {{ $tone[1] }} {{ $tone[0] }} {{ $tone[2] }}">
                                                            {{ strtoupper($order->direction) }}
                                                        </span>
                                                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-white/5 text-slate-400 border border-white/10">
                                                            {{ $order->status }}
                                                        </span>
                                                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-white/5 text-slate-500 border border-white/10">
                                                            {{ $order->origin }}
                                                        </span>
                                                        @if ($order->leverage > 1)
                                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-purple-400/10 text-purple-400 border border-purple-400/20 font-mono">
                                                                {{ $order->leverage }}x
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="flex flex-wrap items-center gap-3 mt-1.5 text-[9px] text-slate-500 font-mono uppercase tracking-widest">
                                                        <span>{{ strtoupper($order->exchange) }}</span>
                                                        <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                                                        <span>{{ $order->market_type }}</span>
                                                        <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                                                        <span>{{ strtoupper($order->order_type) }}</span>
                                                        @if ($order->submitted_at)
                                                            <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                                                            <span>{{ \Carbon\Carbon::createFromTimestamp((int) $order->submitted_at)->diffForHumans() }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    @if ($order->status === 'open' && $order->exchange_order_id)
                                                        <button type="button" class="order-cancel px-3.5 py-2 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-white/10 hover:text-white transition-all"
                                                            data-id="{{ $order->id }}">
                                                            {{ __('Cancel') }}
                                                        </button>
                                                    @endif

                                                    <button type="button" class="order-close px-3.5 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-[10px] font-black uppercase tracking-widest text-rose-300 hover:bg-rose-500/20 transition-all"
                                                        data-id="{{ $order->id }}"
                                                        data-pair="{{ $order->pair }}"
                                                        data-direction="{{ $order->direction }}">
                                                        {{ __('Close') }}
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/8">
                                                    <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">
                                                        {{ __('Quantity') }}
                                                    </div>
                                                    <div class="text-[11px] font-black text-white font-mono mt-0.5">
                                                        {{ number_format((float) $order->quantity, 8) }}
                                                    </div>
                                                </div>

                                                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/8">
                                                    <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">
                                                        {{ $order->filled_price ? __('Entry') : __('Limit') }}
                                                    </div>
                                                    <div class="text-[11px] font-black text-white font-mono mt-0.5">
                                                        {{ number_format((float) ($order->filled_price ?: $order->limit_price), 8) }}
                                                    </div>
                                                </div>

                                                <div class="p-3.5 rounded-2xl bg-emerald-500/[0.06] border border-emerald-500/15">
                                                    <div class="text-[9px] font-black uppercase tracking-widest text-emerald-400/80">
                                                        {{ __('Take Profit') }}
                                                    </div>
                                                    <div class="text-[11px] font-black text-white font-mono mt-0.5">
                                                        {{ $order->take_profit_price ? number_format((float) $order->take_profit_price, 8) : '—' }}
                                                    </div>
                                                </div>

                                                <div class="p-3.5 rounded-2xl bg-rose-500/[0.06] border border-rose-500/15">
                                                    <div class="text-[9px] font-black uppercase tracking-widest text-rose-400/80">
                                                        {{ __('Stop Loss') }}
                                                    </div>
                                                    <div class="text-[11px] font-black text-white font-mono mt-0.5">
                                                        {{ $order->stop_loss_price ? number_format((float) $order->stop_loss_price, 8) : '—' }}
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($order->error_message)
                                                <p class="mt-4 p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-[10px] font-bold text-rose-300 font-mono break-words">
                                                    {{ $order->error_message }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-[2rem] p-1.5 bg-white/[0.03] border border-white/10 text-center">
                                        <div class="rounded-[calc(2rem-0.375rem)] p-12">
                                            <div class="w-16 h-16 mx-auto rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 mb-4">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M9 17V7m4 10v-5m4 5V9" />
                                                </svg>
                                            </div>
                                            <h3 class="text-base font-bold text-white mb-2">{{ __('No Open Positions') }}</h3>
                                            <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                                                {{ __('Your open trades will appear here once an order is placed.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            @if ($orders->hasPages())
                                <div class="flex justify-center">
                                    {{ $orders->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const TOKEN = "{{ csrf_token() }}";

            const ENDPOINTS = {
                store: "{{ route('user.trading.orders.store') }}",
            };

            // Close and cancel both live at /{id}/close and /{id}/cancel, so the
            // id sits in the middle of the path where a regex hunting for
            // trailing digits never finds it. A named token is substituted
            // instead: unambiguous, and it cannot be mistaken for the digits of
            // a port or a query string the way a bare number can.
            const ID_TOKEN = '__ID__';

            const closeUrl = "{{ route('user.trading.orders.close', ['id' => '__ID__']) }}";
            const cancelUrl = "{{ route('user.trading.orders.cancel', ['id' => '__ID__']) }}";

            const withId = (template, id) => template.replace(ID_TOKEN, encodeURIComponent(id));

            function notify(message, type) {
                if (typeof toastNotification === 'function') {
                    toastNotification(message, type || 'success');
                }
            }

            function errorMessage(xhr, fallback) {
                const body = xhr.responseJSON;

                if (body && body.errors) {
                    const first = Object.values(body.errors)[0];
                    if (first && first.length) return first[0];
                }

                return (body && body.message) || fallback || "{{ __('Something went wrong.') }}";
            }

            function busy($button, on) {
                if (on) {
                    $button.data('label', $button.html())
                        .prop('disabled', true)
                        .html('<span class="inline-block w-4 h-4 border-2 border-current border-r-transparent rounded-full animate-spin"></span>');
                } else {
                    $button.prop('disabled', false).html($button.data('label') || '');
                }
            }

            // ── Ticket behaviour ──────────────────────────────────────────────
            function orderType() {
                return $('input[name="order_type"]:checked').val() || 'market';
            }

            function marketType() {
                return $('#order-market').val();
            }

            function side() {
                return $('input[name="side"]:checked').val() || 'buy';
            }

            // A limit order without a price cannot be sent, so the field is
            // required only while it is actually relevant.
            function syncFields() {
                const isLimit = orderType() === 'limit';
                const isFutures = marketType() === 'futures';

                $('#order-price-field').toggleClass('opacity-40', !isLimit);
                $('#order-price').prop('required', isLimit).prop('disabled', !isLimit);

                $('#order-leverage-field').toggleClass('hidden', !isFutures);
                $('#order-leverage').prop('disabled', !isFutures);

                // The market is a property of the credential, not a free choice:
                // a futures key cannot trade spot. Forcing them into agreement
                // beats a 422 from the server after the user fills the form.
                const $connection = $('#order-connection');
                const selected = $connection.find('option:selected');
                const connectionMarket = selected.data('market');

                if (connectionMarket && connectionMarket !== marketType()) {
                    $('#order-market').val(connectionMarket);
                }

                const isLong = side() === 'buy';
                const label = marketType() === 'futures'
                    ? (isLong ? "{{ __('LONG') }}" : "{{ __('SHORT') }}")
                    : (isLong ? "{{ __('BUY') }}" : "{{ __('SELL') }}");

                $('#order-direction-hint').text(
                    marketType() === 'futures'
                        ? "{{ __('This will open a') }} " + label + " {{ __('position at your chosen leverage.') }}"
                        : "{{ __('This will') }} " + label + " " + "{{ __('in the spot market.') }}"
                );

                preview();
            }

            // The preview is an estimate, not a quote: with a market order the
            // fill price is unknown until the exchange matches it.
            function preview() {
                const quantity = parseFloat($('#order-quantity').val()) || 0;
                const price = parseFloat($('#order-price').val()) || 0;
                const leverage = parseInt($('#order-leverage').val(), 10) || 1;
                const isLimit = orderType() === 'limit';

                if (quantity <= 0) {
                    $('#order-notional').text('{{ __('Auto-sized by risk rules') }}');
                    return;
                }

                if (!isLimit && price <= 0) {
                    $('#order-notional').text(
                        quantity.toLocaleString(undefined, { maximumFractionDigits: 8 }) + ' ' + (marketType() === 'futures' ? '{{ __('contracts') }}' : '')
                    );
                    return;
                }

                const notional = quantity * price * (marketType() === 'futures' ? leverage : 1);

                $('#order-notional').text(
                    notional.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USDT'
                );
            }

            $('input[name="order_type"], input[name="side"]').on('change', syncFields);
            $('#order-market, #order-connection').on('change', syncFields);

            $('#order-price, #order-quantity, #order-leverage').on('input', function() {
                if (this.id === 'order-leverage') {
                    $('#leverage-value').text($(this).val() + 'x');
                }

                preview();
            });

            syncFields();

            // ── Submit ────────────────────────────────────────────────────────
            $('#order-form').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $submit = $('#order-submit');

                const payload = {
                    connection_id: $('#order-connection').val(),
                    signal_id: $('#order-signal').val() || null,
                    pair: $('#order-pair').val(),
                    market_type: marketType(),
                    side: side(),
                    order_type: orderType(),
                    quantity: $('#order-quantity').val() || null,
                    price: orderType() === 'limit' ? $('#order-price').val() : null,
                    leverage: marketType() === 'futures' ? parseInt($('#order-leverage').val(), 10) : null,
                    take_profit_price: $('#order-tp').val() || null,
                    stop_loss_price: $('#order-sl').val() || null,
                };

                const isLong = payload.side === 'buy';
                const directionWord = payload.market_type === 'futures'
                    ? (isLong ? '{{ __('LONG') }}' : '{{ __('SHORT') }}')
                    : (isLong ? '{{ __('BUY') }}' : '{{ __('SELL') }}');

                Swal.fire({
                    title: "{{ __('Confirm this order?') }}",
                    html: `
                        <div class="text-left mt-3 space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-slate-500">${"{{ __('Pair') }}"}</span><span class="text-white font-bold font-mono">${payload.pair.toUpperCase()}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">${"{{ __('Side') }}"}</span><span class="text-white font-bold">${directionWord}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">${"{{ __('Type') }}"}</span><span class="text-white font-bold uppercase">${payload.order_type}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">${"{{ __('Quantity') }}"}</span><span class="text-white font-bold font-mono">${payload.quantity || "{{ __('Auto') }}"}</span></div>
                            ${payload.price ? `<div class="flex justify-between"><span class="text-slate-500">${"{{ __('Price') }}"}</span><span class="text-white font-bold font-mono">${payload.price}</span></div>` : ''}
                            ${payload.leverage ? `<div class="flex justify-between"><span class="text-slate-500">${"{{ __('Leverage') }}"}</span><span class="text-white font-bold font-mono">${payload.leverage}x</span></div>` : ''}
                            ${payload.take_profit_price ? `<div class="flex justify-between"><span class="text-slate-500">${"{{ __('Take Profit') }}"}</span><span class="text-emerald-400 font-bold font-mono">${payload.take_profit_price}</span></div>` : ''}
                            ${payload.stop_loss_price ? `<div class="flex justify-between"><span class="text-slate-500">${"{{ __('Stop Loss') }}"}</span><span class="text-rose-400 font-bold font-mono">${payload.stop_loss_price}</span></div>` : ''}
                        </div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: "{{ __('Place Order') }}",
                    cancelButtonText: "{{ __('Cancel') }}",
                    background: 'rgba(31, 31, 34, 0.95)',
                    customClass: {
                        popup: 'backdrop-blur-xl border border-white/10 rounded-2xl',
                        confirmButton: 'rounded-xl font-black text-xs uppercase tracking-widest',
                        cancelButton: 'rounded-xl font-black text-xs uppercase tracking-widest',
                    }
                }).then(function(result) {
                    if (!result.isConfirmed) return;

                    busy($submit, true);

                    axios.post(ENDPOINTS.store, payload, {
                        headers: { 'X-CSRF-TOKEN': TOKEN }
                    }).then(function(res) {
                        notify(res.data.message, 'success');

                        setTimeout(function() {
                            window.location.reload();
                        }, 1200);
                    }).catch(function(xhr) {
                        busy($submit, false);
                        notify(errorMessage(xhr), 'error');
                    });
                });
            });

            // ── Close a position ──────────────────────────────────────────────
            $(document).on('click', '.order-close', function() {
                const $button = $(this);
                const id = $button.data('id');

                Swal.fire({
                    title: "{{ __('Close this position at market?') }}",
                    text: "{{ __('A market order will be sent immediately. You cannot cancel it once matched.') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{ __('Close Position') }}",
                    cancelButtonText: "{{ __('Cancel') }}",
                    confirmButtonColor: '#ef4444',
                    background: 'rgba(31, 31, 34, 0.95)',
                    customClass: {
                        popup: 'backdrop-blur-xl border border-white/10 rounded-2xl',
                        confirmButton: 'rounded-xl font-black text-xs uppercase tracking-widest',
                        cancelButton: 'rounded-xl font-black text-xs uppercase tracking-widest',
                    }
                }).then(function(result) {
                    if (!result.isConfirmed) return;

                    busy($button, true);

                    axios.post(withId(closeUrl, id), {}, {
                        headers: { 'X-CSRF-TOKEN': TOKEN }
                    }).then(function(res) {
                        notify(res.data.message, 'success');

                        setTimeout(function() {
                            window.location.reload();
                        }, 1200);
                    }).catch(function(xhr) {
                        busy($button, false);
                        notify(errorMessage(xhr), 'error');
                    });
                });
            });

            // ── Cancel a resting order ────────────────────────────────────────
            $(document).on('click', '.order-cancel', function() {
                const $button = $(this);
                const id = $button.data('id');

                busy($button, true);

                axios.post(withId(cancelUrl, id), {}, {
                    headers: { 'X-CSRF-TOKEN': TOKEN }
                }).then(function(res) {
                    notify(res.data.message, 'success');

                    setTimeout(function() {
                        window.location.reload();
                    }, 900);
                }).catch(function(xhr) {
                    busy($button, false);
                    notify(errorMessage(xhr), 'error');
                });
            });
        });
    </script>
@endsection
