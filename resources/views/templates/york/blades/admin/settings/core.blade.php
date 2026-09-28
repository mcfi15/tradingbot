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
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            right: 10px !important;
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
        .select2-results__option[aria-selected=true] {
            background: rgba(0, 245, 255, 0.25) !important;
            color: #fff !important;
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
                    {{ __('GENERAL SETTINGS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('General Settings') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Platform name, contact email, logos, base currency, and office locations') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="$('#core-settings-form').submit()"
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

            {{-- Right Main Form Chassis --}}
            <div class="lg:col-span-8 xl:col-span-9 space-y-6">
                <form id="core-settings-form" action="{{ route('admin.settings.core.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-10 text-xs">
                            
                            {{-- SECTION 1: IDENTITY & LOCALE --}}
                            <div class="space-y-6">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            01
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Basic Information') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Set your website name, support email, and primary timezone.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ __('Site Name') }}</label>
                                        <input type="text" name="site_name" value="{{ getSetting('name') }}" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-all">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ __('Support Email') }}</label>
                                        <input type="email" name="support_email" value="{{ getSetting('email') }}" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-all">
                                    </div>

                                    <div class="space-y-2 md:col-span-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ __('Timezone') }}</label>
                                        <div class="relative">
                                            <select name="app_timezone" id="timezone-select" class="w-full">
                                                @foreach ($timezones as $tz)
                                                    <option value="{{ $tz['tzCode'] }}" data-utc="{{ $tz['utc'] }}" data-name="{{ $tz['name'] }}"
                                                        {{ getSetting('app_timezone', config('app.timezone', 'UTC')) == $tz['tzCode'] ? 'selected' : '' }}>
                                                        {{ $tz['label'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 2: BRANDING ASSETS --}}
                            <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            02
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Logos & Favicon') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Upload your square mark, main horizontal logo, and browser favicon.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    {{-- Square Logo --}}
                                    <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex flex-col justify-between space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">{{ __('Square Logo') }}</span>
                                            <span class="text-[9px] text-slate-500">512x512</span>
                                        </div>
                                        <div class="h-24 rounded-xl bg-[#05070d] border border-white/[0.06] flex items-center justify-center p-3 overflow-hidden">
                                            <img src="{{ asset('assets/images/' . getSetting('logo_square')) }}" id="preview-logo-square" class="max-h-full object-contain">
                                        </div>
                                        <input type="file" name="logo_square" class="hidden image-input" data-preview="#preview-logo-square">
                                        <button type="button" class="trigger-input w-full py-2.5 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white border border-white/[0.08] font-bold uppercase text-[10px] transition-all cursor-pointer">
                                            {{ __('Choose Square Logo') }}
                                        </button>
                                    </div>

                                    {{-- Full Logo --}}
                                    <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex flex-col justify-between space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">{{ __('Full Logo') }}</span>
                                            <span class="text-[9px] text-slate-500">{{ __('Horizontal') }}</span>
                                        </div>
                                        <div class="h-24 rounded-xl bg-[#05070d] border border-white/[0.06] flex items-center justify-center p-3 overflow-hidden">
                                            <img src="{{ asset('assets/images/' . getSetting('logo_rectangle')) }}" id="preview-logo-rectangle" class="max-h-full object-contain">
                                        </div>
                                        <input type="file" name="logo_rectangle" class="hidden image-input" data-preview="#preview-logo-rectangle">
                                        <button type="button" class="trigger-input w-full py-2.5 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white border border-white/[0.08] font-bold uppercase text-[10px] transition-all cursor-pointer">
                                            {{ __('Choose Full Logo') }}
                                        </button>
                                    </div>

                                    {{-- Favicon --}}
                                    <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex flex-col justify-between space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">{{ __('Favicon') }}</span>
                                            <span class="text-[9px] text-slate-500">64x64 .ico/.png</span>
                                        </div>
                                        <div class="h-24 rounded-xl bg-[#05070d] border border-white/[0.06] flex items-center justify-center p-3 overflow-hidden">
                                            <img src="{{ asset('assets/images/' . getSetting('favicon')) }}" id="preview-favicon" class="w-10 h-10 object-contain">
                                        </div>
                                        <input type="file" name="favicon" class="hidden image-input" data-preview="#preview-favicon">
                                        <button type="button" class="trigger-input w-full py-2.5 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white border border-white/[0.08] font-bold uppercase text-[10px] transition-all cursor-pointer">
                                            {{ __('Choose Favicon') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 3: FINANCIAL & CURRENCY MATRIX --}}
                            <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            03
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Currency & Formatting') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Configure base currency, symbol placement, and decimal places.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-2 md:col-span-3">
                                        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ __('Currency') }}</label>
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
                                        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ __('Symbol Placement') }}</label>
                                        <select name="currency_position"
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-all">
                                            <option value="before" {{ getSetting('currency_symbol_position') == 'before' ? 'selected' : '' }}>{{ __('Before amount ($100.00)') }}</option>
                                            <option value="after" {{ getSetting('currency_symbol_position') == 'after' ? 'selected' : '' }}>{{ __('After amount (100.00$)') }}</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ __('Decimal Places') }}</label>
                                        <input type="number" name="decimal_places" value="{{ getSetting('decimal_places', 2) }}" min="0" max="8" required
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-all">
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 4: OFFICE LOCATIONS REPEATER --}}
                            <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            04
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Office Locations') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Office contact details shown on your website\'s contact and about pages.') }}</p>
                                        </div>
                                    </div>
                                    <button type="button" id="add-office-btn"
                                        class="px-4 py-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/20 font-bold uppercase text-[10px] tracking-wider transition-all flex items-center gap-2 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>{{ __('Add Office Location') }}</span>
                                    </button>
                                </div>

                                <div id="offices-container" class="space-y-4">
                                    @php
                                        $offices = json_decode(getSetting('offices', '[]'), true) ?: [];
                                    @endphp

                                    @forelse($offices as $index => $office)
                                        <div class="office-item p-6 rounded-2xl bg-white/[0.02] border border-white/[0.06] space-y-4 relative group">
                                            <button type="button" class="remove-office-btn absolute top-4 right-4 text-slate-500 hover:text-rose-400 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="space-y-1">
                                                    <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Office Name') }}</label>
                                                    <input type="text" name="offices[{{ $index }}][name]" value="{{ $office['name'] ?? '' }}" placeholder="Global HQ" required
                                                        class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-3 py-2 text-xs focus:outline-none">
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Email') }}</label>
                                                    <input type="email" name="offices[{{ $index }}][email]" value="{{ $office['email'] ?? '' }}" placeholder="hq@domain.com"
                                                        class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-3 py-2 text-xs focus:outline-none">
                                                </div>
                                                <div class="space-y-1 md:col-span-2">
                                                    <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Address') }}</label>
                                                    <textarea name="offices[{{ $index }}][address]" rows="2" placeholder="Street, City, Country" required
                                                        class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-3 py-2 text-xs focus:outline-none resize-none">{{ $office['address'] ?? '' }}</textarea>
                                                </div>
                                                <div class="space-y-1 md:col-span-2">
                                                    <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Phone Number') }}</label>
                                                    <input type="text" name="offices[{{ $index }}][phone]" value="{{ $office['phone'] ?? '' }}" placeholder="+1 234 567 890"
                                                        class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-3 py-2 text-xs focus:outline-none">
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div id="empty-offices-msg" class="p-8 rounded-2xl bg-white/[0.01] border border-dashed border-white/[0.06] text-center text-slate-500 text-xs">
                                            {{ __('No office locations added yet. Click "Add Office Location" above.') }}
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Bottom Submit Cluster --}}
                            <div class="pt-6 border-t border-white/[0.06] flex items-center justify-end">
                                <button type="submit" id="submit-btn"
                                    class="px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4 submit-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="btn-text">{{ __('Save Settings') }}</span>
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

            function formatTz(tz) {
                if (!tz.id) return tz.text;
                const $el = $(tz.element);
                return $(`
                    <div class="flex items-center justify-between gap-4 py-0.5">
                        <span class="text-xs font-bold text-white">${tz.text}</span>
                        <span class="text-[9px] bg-cyan-500/10 text-cyan-400 px-2 py-0.5 rounded font-bold border border-cyan-500/20">GMT ${$el.data('utc')}</span>
                    </div>
                `);
            }

            $('#timezone-select').select2({
                templateResult: formatTz,
                templateSelection: formatTz,
                matcher: customMatcher,
                width: '100%'
            });

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
                $('#hidden-currency-name').val($opt.data('code'));
                $('#hidden-currency-symbol').val($opt.data('symbol'));
            });

            let officeIndex = {{ count($offices) }};
            $('#add-office-btn').on('click', function() {
                $('#empty-offices-msg').hide();
                const html = `
                    <div class="office-item p-6 rounded-2xl bg-white/[0.02] border border-white/[0.06] space-y-4 relative group animate__animated animate__fadeIn">
                        <button type="button" class="remove-office-btn absolute top-4 right-4 text-slate-500 hover:text-rose-400 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Office Label') }}</label>
                                <input type="text" name="offices[${officeIndex}][name]" placeholder="Branch Office" required
                                    class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-3 py-2 text-xs focus:outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Email') }}</label>
                                <input type="email" name="offices[${officeIndex}][email]" placeholder="branch@domain.com"
                                    class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-3 py-2 text-xs focus:outline-none">
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Address') }}</label>
                                <textarea name="offices[${officeIndex}][address]" rows="2" placeholder="Street, City, Country" required
                                    class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-3 py-2 text-xs focus:outline-none resize-none"></textarea>
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Telephone') }}</label>
                                <input type="text" name="offices[${officeIndex}][phone]" placeholder="+1 234 567 890"
                                    class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-3 py-2 text-xs focus:outline-none">
                            </div>
                        </div>
                    </div>
                `;
                $('#offices-container').append(html);
                officeIndex++;
            });

            $(document).on('click', '.remove-office-btn', function() {
                $(this).closest('.office-item').remove();
                if ($('#offices-container').find('.office-item').length === 0) {
                    $('#empty-offices-msg').show();
                }
            });

            $('.trigger-input').click(function() {
                $(this).siblings('input[type="file"]').click();
            });

            $('.image-input').change(function() {
                const input = this;
                const preview = $(this).data('preview');
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $(preview).attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            });

            $('#core-settings-form').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const $btn = $('#submit-btn');

                $btn.prop('disabled', true).addClass('opacity-50');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message || '{{ __('Settings saved successfully.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to save settings.') }}',
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
