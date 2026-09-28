@extends('templates.york.blades.admin.layouts.admin')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            background: #05070d !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 0.75rem !important;
            height: 46px !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #00f5ff !important;
            box-shadow: 0 0 10px rgba(0, 245, 255, 0.2) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #fff !important;
            padding-left: 14px !important;
            padding-right: 35px !important;
            font-size: 12px !important;
        }
        .select2-dropdown {
            background: #090c14 !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            border-radius: 0.75rem !important;
            color: #fff !important;
            z-index: 9999 !important;
        }
        .select2-search--dropdown .select2-search__field {
            background: #05070d !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 0.5rem !important;
            color: #fff !important;
            font-size: 11px !important;
            padding: 6px 10px !important;
        }
        .select2-results__option {
            font-size: 11px !important;
            padding: 8px 12px !important;
        }
        .select2-results__option--highlighted[aria-selected] {
            background: rgba(0, 245, 255, 0.15) !important;
            color: #00f5ff !important;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-8 mb-12 font-mono">
        
        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('FINANCIAL SETTINGS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Financial Settings & Limits') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Configure currency preferences, deposit rules, and withdrawal limits') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="$('#financial-settings-form').submit()"
                    class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- MAIN LAYOUT: SIDEBAR + CONTENT --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Navigation Deck --}}
            <div class="lg:col-span-4 xl:col-span-3">
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        @include("templates.$template.blades.admin.settings.partials.sidebar")
                    </div>
                </div>
            </div>

            {{-- Right Content Chassis --}}
            <div class="lg:col-span-8 xl:col-span-9 space-y-6">
                <form id="financial-settings-form" action="{{ route('admin.settings.financial.update') }}" method="POST">
                    @csrf

                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-10 text-xs">
                            
                            {{-- SECTION 1: CURRENCY PREFERENCES --}}
                            <div class="space-y-6">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            01
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Currency Settings') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Select the default currency and formatting for platform balances.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-2 md:col-span-3">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Default Platform Currency') }}</label>
                                        <select id="currency-select" class="w-full">
                                            @foreach ($currencies as $code => $curr)
                                                <option value="{{ $code }}" data-symbol="{{ $curr['symbol'] }}" data-name="{{ $curr['name'] }}" data-code="{{ $code }}"
                                                    @if ($code == getSetting('currency')) selected @endif>
                                                    {{ $code }} - {{ $curr['name'] }} ({{ $curr['symbol'] }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="currency_name" id="hidden-currency-name" value="{{ getSetting('currency') }}">
                                        <input type="hidden" name="currency_symbol" id="hidden-currency-symbol" value="{{ getSetting('currency_symbol') }}">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Symbol Position') }}</label>
                                        <select name="currency_position" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                            <option value="before" {{ getSetting('currency_symbol_position') == 'before' ? 'selected' : '' }}>{{ __('Prefix: $100.00') }}</option>
                                            <option value="after" {{ getSetting('currency_symbol_position') == 'after' ? 'selected' : '' }}>{{ __('Suffix: 100.00$') }}</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Decimal Places') }}</label>
                                        <input type="number" name="decimal_places" value="{{ getSetting('decimal_places', 2) }}" min="0" max="8" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 2: DEPOSITS RULES --}}
                            <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            02
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Deposit Limits & Fees') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Set minimum and maximum deposit amounts, processing fees, and invoice expiration.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Minimum Deposit') }}</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dynamic-currency-symbol font-bold">{{ getSetting('currency_symbol') }}</span>
                                            <input type="number" name="min_deposit" value="{{ getSetting('min_deposit', 1) }}" step="0.01" min="0" required
                                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl pl-10 pr-4 py-3 text-xs focus:outline-none">
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Maximum Deposit') }}</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dynamic-currency-symbol font-bold">{{ getSetting('currency_symbol') }}</span>
                                            <input type="number" name="max_deposit" value="{{ getSetting('max_deposit', 60000) }}" step="0.01" min="0" required
                                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl pl-10 pr-4 py-3 text-xs focus:outline-none">
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Deposit Fee (%)') }}</label>
                                        <input type="number" name="deposit_fee" value="{{ getSetting('deposit_fee', 0) }}" step="0.01" min="0" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Deposit Expiry (Hours)') }}</label>
                                        <input type="number" name="deposit_expires_at" value="{{ getSetting('deposit_expires_at', 24) }}" step="1" min="1" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 3: WITHDRAWALS RULES --}}
                            <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            03
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Withdrawal Limits & Fees') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Set minimum and maximum withdrawal amounts and processing fees.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Minimum Withdrawal') }}</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dynamic-currency-symbol font-bold">{{ getSetting('currency_symbol') }}</span>
                                            <input type="number" name="min_withdrawal" value="{{ getSetting('min_withdrawal', 10) }}" step="0.01" min="0" required
                                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl pl-10 pr-4 py-3 text-xs focus:outline-none">
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Maximum Withdrawal') }}</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dynamic-currency-symbol font-bold">{{ getSetting('currency_symbol') }}</span>
                                            <input type="number" name="max_withdrawal" value="{{ getSetting('max_withdrawal', 50000) }}" step="0.01" min="0" required
                                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl pl-10 pr-4 py-3 text-xs focus:outline-none">
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Withdrawal Fee (%)') }}</label>
                                        <input type="number" name="withdrawal_fee" value="{{ getSetting('withdrawal_fee', 0) }}" step="0.01" min="0" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>
                                </div>
                            </div>

                            {{-- SUBMIT BUTTON --}}
                            <div class="pt-6 border-t border-white/[0.06] flex items-center justify-end">
                                <button type="submit" id="submit-btn"
                                    class="px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4 submit-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="btn-text">{{ __('Save Changes') }}</span>
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            function customMatcher(params, data) {
                if ($.trim(params.term) === '') return data;
                if (typeof data.text === 'undefined') return null;
                const term = params.term.toLowerCase();
                const $el = $(data.element);
                if (data.text.toLowerCase().indexOf(term) > -1) return data;
                for (const key in $el.data()) {
                    const value = $el.data(key);
                    if (typeof value === 'string' && value.toLowerCase().indexOf(term) > -1) return data;
                }
                return null;
            }

            function formatCurrency(curr) {
                if (!curr.id) return curr.text;
                const $el = $(curr.element);
                return $(`
                    <div class="flex items-center justify-between gap-4 py-0.5">
                        <span class="text-xs font-bold text-white">${curr.text}</span>
                        <span class="text-[10px] text-cyan-400 font-bold bg-white/5 px-2 py-0.5 rounded">${$el.data('symbol')}</span>
                    </div>
                `);
            }

            $('#currency-select').select2({
                templateResult: formatCurrency,
                templateSelection: formatCurrency,
                matcher: customMatcher,
                width: '100%'
            }).on('change', function() {
                const $opt = $(this).find(':selected');
                const newCode = $opt.data('code');
                const newSymbol = $opt.data('symbol');
                $('#hidden-currency-name').val(newCode);
                $('#hidden-currency-symbol').val(newSymbol);
                $('.dynamic-currency-symbol').text(newSymbol);
            });

            $('#financial-settings-form').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#submit-btn');
                $btn.prop('disabled', true).addClass('opacity-50');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message || '{{ __('Financial rules updated.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to save financial settings.') }}',
                            customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).removeClass('opacity-50');
                    }
                });
            });
        });
    </script>
@endpush
