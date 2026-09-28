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
                                <span>{{ __('Live Trading') }}</span>
                            </div>

                            <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                                {{ __('Exchange Connections') }}
                            </h1>

                            <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                                {{ __('Connect Binance, Bybit or MEXC to trade with your own funds. Keys are encrypted before they are written and are never displayed again.') }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl shrink-0">
                            <a href="{{ route('user.trading.exchanges.index') }}"
                               class="px-5 py-2.5 rounded-full bg-accent-primary text-black font-black text-xs uppercase tracking-wider transition-all">
                                {{ __('Connections') }}
                            </a>
                            <a href="{{ route('user.trading.exchanges.portfolio') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Wallet') }}
                            </a>
                            <a href="{{ route('user.trading.orders.index') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Trade') }}
                            </a>
                            <a href="{{ route('user.trading.preferences.index') }}"
                               class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all">
                                {{ __('Risk') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ WALLET WIDGET ═════════════════════════════════════════════════ --}}
            @include('templates.' . config('site.template') . '.blades.user.trading.exchange.partials.wallet_widget', [
                'portfolio' => $portfolio,
            ])

            {{-- ══ CONNECTED ACCOUNTS ════════════════════════════════════════════ --}}
            <div class="space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h2 class="text-xl font-black text-white tracking-tight">
                        {{ __('Your Connections') }}
                        <span class="ml-2 px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-black text-slate-400 font-mono">
                            {{ count($connections) }}
                        </span>
                    </h2>
                </div>

                @forelse ($connections as $connection)
                    <div class="connection-row group relative rounded-[2rem] p-1.5 bg-white/[0.03] border border-white/10 transition-all duration-300 hover:border-accent-primary/40"
                         data-connection-id="{{ $connection['id'] }}">

                        <div class="rounded-[calc(2rem-0.375rem)] p-6 sm:p-7 relative overflow-hidden"
                             style="background: linear-gradient(135deg, rgba(15,17,21,0.98) 0%, rgba(8,9,14,0.99) 100%);">

                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">

                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0">
                                        <img src="{{ asset('assets/images/exchanges/' . strtolower($connection['exchange']) . '.svg') }}"
                                             class="w-7 h-7"
                                             onerror="this.style.display='none'" alt="{{ $connection['exchange'] }}">
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-base font-black text-white uppercase tracking-wider">
                                                {{ $connection['exchange'] }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest {{ $connection['market_type'] === 'futures' ? 'bg-purple-400/10 text-purple-400 border border-purple-400/20' : 'bg-blue-400/10 text-blue-400 border border-blue-400/20' }}">
                                                {{ $connection['market_type'] }}
                                            </span>
                                            @if (! $connection['is_active'])
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                                    {{ __('Disabled') }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-[10px] text-slate-500 font-mono mt-1">
                                            {{ $connection['api_key_hint'] }}
                                            @if ($connection['label'])
                                                <span class="text-slate-600">·</span> {{ $connection['label'] }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-6">
                                    <div class="text-left lg:text-right">
                                        <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">
                                            {{ __('Balance') }}
                                        </span>
                                        <div class="text-lg font-black text-white font-mono">
                                            {{ number_format((float) $connection['last_total_balance'], 2) }}
                                        </div>
                                        <div class="text-[9px] text-slate-500">
                                            {{ $connection['last_synced_at_human'] ?? __('Never synced') }}
                                        </div>
                                    </div>

                                    {{--
                                        The last failure, in the exchange's own words.
                                        "Never synced" next to a zero balance is
                                        indistinguishable from a rejected key, and
                                        the two are fixed in completely different
                                        places. The Test button writes this.
                                    --}}
                                    @if (!empty($connection['last_error']))
                                        <div class="basis-full lg:basis-auto lg:max-w-md text-left lg:text-right">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-rose-400">
                                                {{ __('Last failure') }}
                                            </span>
                                            <p class="text-[10px] text-rose-300/70 font-mono leading-snug mt-1"
                                               title="{{ $connection['last_error'] }}">
                                                {{ $connection['last_error'] }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-2">
                                        {{--
                                            Every action URL is resolved here, on the server, for
                                            this specific connection. The client used to
                                            reconstruct them by regex-swapping a placeholder id,
                                            which silently no-oped on the nested routes
                                            (/{id}/test, /{id}/sync) because the id is not the
                                            last path segment. A row must never depend on
                                            client-side URL surgery.
                                        --}}
                                        <button type="button" class="connection-test px-3.5 py-2 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-white/10 hover:text-white transition-all"
                                            data-id="{{ $connection['id'] }}"
                                            data-url="{{ route('user.trading.exchanges.test', ['id' => $connection['id']]) }}">
                                            {{ __('Test') }}
                                        </button>

                                        <button type="button" class="connection-sync px-3.5 py-2 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-white/10 hover:text-white transition-all"
                                            data-id="{{ $connection['id'] }}"
                                            data-url="{{ route('user.trading.exchanges.sync', ['id' => $connection['id']]) }}">
                                            {{ __('Sync') }}
                                        </button>

                                        <button type="button" class="connection-edit px-3.5 py-2 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-white/10 hover:text-white transition-all"
                                            data-id="{{ $connection['id'] }}"
                                            data-url="{{ route('user.trading.exchanges.update', ['id' => $connection['id']]) }}"
                                            data-label="{{ $connection['label'] ?? '' }}">
                                            {{ __('Keys') }}
                                        </button>

                                        <button type="button" class="connection-delete px-3.5 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-[10px] font-black uppercase tracking-widest text-rose-300 hover:bg-rose-500/20 transition-all"
                                            data-id="{{ $connection['id'] }}"
                                            data-url="{{ route('user.trading.exchanges.destroy', ['id' => $connection['id']]) }}"
                                            data-label="{{ strtoupper($connection['exchange']) }} {{ $connection['market_type'] }}">
                                            {{ __('Remove') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="connection-status mt-4 hidden"></div>
                        </div>
                    </div>
                @empty
                    <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 max-w-lg mx-auto text-center">
                        <div class="rounded-[calc(2.5rem-0.375rem)] p-12">
                            <div class="w-16 h-16 mx-auto rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">{{ __('No Exchanges Connected') }}</h3>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                                {{ __('Add an API key below to start trading your own funds.') }}
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- ══ ADD / EDIT CONNECTION ══════════════════════════════════════════ --}}
            <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-10 relative overflow-hidden"
                     style="background: linear-gradient(135deg, rgba(15,17,21,0.98) 0%, rgba(8,9,14,0.99) 100%);">

                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-11 h-11 rounded-2xl bg-accent-primary/10 border border-accent-primary/25 text-accent-primary flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-white tracking-tight" id="connection-form-title">
                                {{ __('Add a Connection') }}
                            </h2>
                            <p class="text-[11px] text-slate-500">
                                {{ __('Enable trading permission on the key. Withdrawal permission should stay off.') }}
                            </p>
                        </div>
                    </div>

                    <form id="connection-form" class="space-y-6" autocomplete="off">
                        <input type="hidden" id="connection-id" value="">
                        {{-- Resolved by the server for the connection being edited, so the
                             submit handler posts to a real URL instead of rebuilding one. --}}
                        <input type="hidden" id="connection-update-url" value="">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            {{-- Exchange --}}
                            <div>
                                <label for="connection-exchange" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                    {{ __('Exchange') }}
                                </label>
                                <select id="connection-exchange" name="exchange"
                                    class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-bold text-white focus:border-accent-primary focus:outline-none">
                                    @foreach (config('exchange.supported', []) as $slug)
                                        <option value="{{ $slug }}" @selected($slug === 'binance')>{{ strtoupper($slug) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Market type --}}
                            <div>
                                <label for="connection-market" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                    {{ __('Market') }}
                                </label>
                                <select id="connection-market" name="market_type"
                                    class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-bold text-white focus:border-accent-primary focus:outline-none">
                                    <option value="spot">{{ __('Spot') }}</option>
                                    <option value="futures">{{ __('Futures / Perp') }}</option>
                                </select>
                            </div>

                            {{-- Label --}}
                            <div>
                                <label for="connection-label" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                    {{ __('Label') }} <span class="normal-case tracking-normal text-slate-600">{{ __('(optional)') }}</span>
                                </label>
                                <input type="text" id="connection-label" name="label" maxlength="191"
                                    placeholder="{{ __('Main account') }}"
                                    class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-bold text-white placeholder:text-slate-600 focus:border-accent-primary focus:outline-none">
                            </div>
                        </div>

                        {{-- Credentials: the exchange and market pickers lock when
                             editing, because a connection is identified by that
                             pair. Changing it here would create a duplicate. --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="connection-key" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                    {{ __('API Key') }}
                                </label>
                                <input type="text" id="connection-key" name="api_key" required minlength="8"
                                    autocomplete="off" spellcheck="false"
                                    class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-mono text-white placeholder:text-slate-600 focus:border-accent-primary focus:outline-none">
                            </div>

                            <div>
                                <label for="connection-secret" class="block text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">
                                    {{ __('API Secret') }}
                                </label>
                                <input type="password" id="connection-secret" name="api_secret" required minlength="8"
                                    autocomplete="new-password"
                                    class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/10 text-sm font-mono text-white placeholder:text-slate-600 focus:border-accent-primary focus:outline-none">
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
                            <p class="text-[10px] text-slate-600 max-w-md leading-relaxed">
                                {{ __('Credentials are sealed with the application key before they are written. They cannot be read back through this page.') }}
                            </p>

                            <div class="flex items-center gap-3">
                                <button type="button" id="connection-cancel"
                                    class="hidden px-5 py-3 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:bg-white/10 transition-all">
                                    {{ __('Cancel') }}
                                </button>

                                <button type="submit" id="connection-submit"
                                    class="px-7 py-3 rounded-xl bg-accent-primary text-black font-black text-[10px] uppercase tracking-widest hover:bg-accent-primary/90 active:scale-95 transition-all shadow-[0_0_20px_rgba(226,177,60,0.25)]">
                                    {{ __('Save Connection') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══ RECENT ACTIVITY ══════════════════════════════════════════════ --}}
            <div class="space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h2 class="text-xl font-black text-white tracking-tight">{{ __('Recent Activity') }}</h2>
                    <a href="{{ route('user.trading.orders.logs') }}"
                       class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-white/10 hover:text-white transition-all">
                        {{ __('All Logs') }}
                    </a>
                </div>

                <div class="relative rounded-[2rem] p-1.5 bg-white/[0.03] border border-white/10">
                    <div class="rounded-[calc(2rem-0.375rem)] p-6 sm:p-8 overflow-hidden">
                        @forelse ($recent_logs as $log)
                            @php
                                $tone = match ($log->level) {
                                    'critical' => ['rose', 'text-rose-400', 'bg-rose-500/10', 'border-rose-500/20'],
                                    'error' => ['rose', 'text-rose-400', 'bg-rose-500/10', 'border-rose-500/20'],
                                    'warning' => ['amber', 'text-amber-400', 'bg-amber-500/10', 'border-amber-500/20'],
                                    default => ['slate', 'text-slate-400', 'bg-white/5', 'border-white/10'],
                                };
                            @endphp

                            <div class="flex items-start gap-4 p-4 rounded-2xl bg-white/[0.02] border border-white/8 mb-3">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border {{ $tone[2] }} {{ $tone[1] }} shrink-0 mt-0.5">
                                    {{ $log->level }}
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-bold text-slate-200 leading-relaxed break-words">
                                        {{ $log->message }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1.5 text-[9px] text-slate-500 font-mono">
                                        @if ($log->pair)
                                            <span>{{ $log->pair }}</span>
                                            <span class="text-slate-700">·</span>
                                        @endif
                                        @if ($log->exchange)
                                            <span>{{ strtoupper($log->exchange) }}</span>
                                            <span class="text-slate-700">·</span>
                                        @endif
                                        <span>{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-[11px] font-bold text-slate-500 text-center py-8">
                                {{ __('No activity yet.') }}
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const TOKEN = "{{ csrf_token() }}";

            // Only id-less endpoints live here. Anything that needs a connection
            // id carries its own server-resolved URL in the row's data-url, so
            // the client never has to build one.
            const ENDPOINTS = {
                store: "{{ route('user.trading.exchanges.store') }}",
                portfolio: "{{ route('user.trading.exchanges.portfolio') }}",
            };

            function actionUrl($el) {
                const url = $el.data('url');

                if (!url) {
                    throw new Error('Action is missing its data-url attribute.');
                }

                return url;
            }

            function notify(message, type) {
                if (typeof toastNotification === 'function') {
                    toastNotification(message, type || 'success');
                }
            }

            // Validation failures come back as a 422 with a field map, which is
            // far more useful than a flat "invalid input" string.
            function errorMessage(xhr, fallback) {
                const body = xhr.responseJSON;

                if (body && body.errors) {
                    const first = Object.values(body.errors)[0];

                    if (first && first.length) return first[0];
                }

                return (body && body.message) || fallback || "{{ __('Something went wrong.') }}";
            }

            function busy($button, on, label) {
                if (on) {
                    $button.data('label', $button.text()).prop('disabled', true)
                        .html('<span class="inline-block w-3 h-3 border-2 border-current border-r-transparent rounded-full animate-spin"></span>');
                } else {
                    $button.prop('disabled', false).text($button.data('label') || label || '');
                }
            }

            // ── Inline status line under a connection row ────────────────────
            function status($row, message, tone) {
                const palette = {
                    success: 'text-emerald-300 border-emerald-500/20 bg-emerald-500/10',
                    error: 'text-rose-300 border-rose-500/20 bg-rose-500/10',
                    info: 'text-slate-300 border-white/10 bg-white/5',
                };

                $row.find('.connection-status')
                    .removeClass('hidden')
                    .attr('class', 'connection-status mt-4 p-3.5 rounded-2xl border text-[11px] font-bold ' + (palette[tone] || palette.info))
                    .text(message);
            }

            // ── Form mode: add vs. edit ───────────────────────────────────────
            function resetForm() {
                $('#connection-id').val('');
                $('#connection-update-url').val('');
                $('#connection-form-title').text("{{ __('Add a Connection') }}");
                $('#connection-submit').text("{{ __('Save Connection') }}");
                $('#connection-cancel').addClass('hidden');
                $('#connection-exchange, #connection-market').prop('disabled', false);
                $('#connection-form')[0].reset();
            }

            $(document).on('click', '.connection-edit', function() {
                const id = $(this).data('id');

                // The stored key is never sent to the browser, so the field is
                // left blank and the placeholder says a re-paste is required.
                $('#connection-id').val(id);
                $('#connection-update-url').val(actionUrl($(this)));
                $('#connection-form-title').text("{{ __('Replace Credentials') }}");
                $('#connection-submit').text("{{ __('Update Credentials') }}");
                $('#connection-cancel').removeClass('hidden');
                $('#connection-exchange, #connection-market').prop('disabled', true);
                $('#connection-label').val($(this).data('label') || '');
                $('#connection-key').val('');
                $('#connection-secret').val('');
                $('html, body').animate({
                    scrollTop: $('#connection-form').offset().top - 120
                }, 400);
                $('#connection-key').trigger('focus');
            });

            $('#connection-cancel').on('click', resetForm);

            // ── Save ──────────────────────────────────────────────────────────
            $('#connection-form').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $submit = $('#connection-submit');
                const id = $('#connection-id').val();

                const payload = {
                    label: $('#connection-label').val() || null,
                    api_key: $('#connection-key').val(),
                    api_secret: $('#connection-secret').val(),
                };

                if (!id) {
                    payload.exchange = $('#connection-exchange').val();
                    payload.market_type = $('#connection-market').val();
                }

                busy($submit, true);

                // The update target comes from the Keys button that opened this
                // form, so it is a real resolved URL rather than a rebuilt one.
                const updateUrl = $('#connection-update-url').val();

                axios.post(updateUrl || ENDPOINTS.store, payload, {
                    headers: { 'X-CSRF-TOKEN': TOKEN }
                }).then(function(res) {
                    notify(res.data.message, 'success');

                    // The API key is a one-way write: it must never be echoed
                    // back, so the field is cleared before the reload.
                    $('#connection-secret').val('');
                    $('#connection-key').val('');

                    setTimeout(function() {
                        window.location.reload();
                    }, 900);
                }).catch(function(xhr) {
                    busy($submit, false, id ? "{{ __('Update Credentials') }}" : "{{ __('Save Connection') }}");
                    notify(errorMessage(xhr), 'error');
                });
            });

            // ── Test ──────────────────────────────────────────────────────────
            $(document).on('click', '.connection-test', function() {
                const $button = $(this);
                const $row = $button.closest('.connection-row');

                busy($button, true);
                status($row, "{{ __('Verifying with the exchange…') }}", 'info');

                axios.post(actionUrl($button), {}, {
                    headers: { 'X-CSRF-TOKEN': TOKEN }
                }).then(function(res) {
                    busy($button, false);
                    status($row, res.data.message, 'success');
                }).catch(function(xhr) {
                    busy($button, false);
                    status($row, errorMessage(xhr), 'error');
                });
            });

            // ── Sync ──────────────────────────────────────────────────────────
            $(document).on('click', '.connection-sync', function() {
                const $button = $(this);
                const $row = $button.closest('.connection-row');

                busy($button, true);
                status($row, "{{ __('Balance sync queued.') }}", 'info');

                axios.post(actionUrl($button), {}, {
                    headers: { 'X-CSRF-TOKEN': TOKEN }
                }).then(function() {
                    busy($button, false);

                    setTimeout(function() {
                        window.location.reload();
                    }, 1400);
                }).catch(function(xhr) {
                    busy($button, false);
                    status($row, errorMessage(xhr), 'error');
                });
            });

            // ── Remove ────────────────────────────────────────────────────────
            $(document).on('click', '.connection-delete', function() {
                const $button = $(this);
                const label = $button.data('label');

                Swal.fire({
                    title: "{{ __('Remove this connection?') }}",
                    html: "<p class='text-slate-400 text-sm'>" +
                        "{{ __('Your API credentials will be deleted. Trade history is kept. Open positions must be closed first.') }}" +
                        "</p><p class='text-white font-bold mt-3'>" + label + "</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{ __('Remove') }}",
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

                    const $row = $button.closest('.connection-row');

                    busy($button, true);
                    status($row, "{{ __('Removing…') }}", 'info');

                    axios.delete(actionUrl($button), {
                        headers: { 'X-CSRF-TOKEN': TOKEN }
                    }).then(function(res) {
                        $row.fadeOut(220, function() {
                            $(this).remove();
                        });
                        notify(res.data.message, 'success');
                    }).catch(function(xhr) {
                        busy($button, false);
                        status($row, errorMessage(xhr), 'error');
                    });
                });
            });

            // ── Wallet refresh ────────────────────────────────────────────────
            $('#wallet-refresh').on('click', function() {
                const $button = $(this);

                busy($button, true);

                axios.get(ENDPOINTS.portfolio, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(function(res) {
                    busy($button, false);

                    const total = parseFloat((res.data.data || {}).total) || 0;
                    const quote = (res.data.data || {}).quote_asset || 'USDT';

                    $('#wallet-total').text(total.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $('#wallet-total').data('total', total);

                    notify("{{ __('Wallet refreshed.') }}", 'success');
                }).catch(function() {
                    busy($button, false);
                    notify("{{ __('Could not refresh the wallet.') }}", 'error');
                });
            });
        });
    </script>
@endsection
