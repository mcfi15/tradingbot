<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-mono">
    {{-- Left Column: Core Details (2 Cols) --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Basic Info Card --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
            <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-white/[0.06]">
                    <div>
                        <h3 class="text-base font-bold text-white uppercase tracking-wide flex items-center gap-2">
                            <span>{{ __('Bot Details') }}</span>
                        </h3>
                        <p class="text-xs text-slate-400 font-sans mt-0.5">{{ __('Basic bot information and trading market') }}</p>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label for="name" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                            {{ __('Bot Name') }} <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" id="name" name="name" required value="{{ old('name', $bot->name ?? '') }}"
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-all placeholder:text-slate-600"
                            placeholder="{{ __('e.g., Perpetual Scalper Prime') }}">
                    </div>

                    <div>
                        <label for="logo" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                            {{ __('Bot Icon / Image') }} @if(!isset($bot)) <span class="text-rose-400">*</span> @endif
                        </label>
                        <div class="flex items-center gap-3">
                            @if(isset($bot))
                                <div class="shrink-0 w-11 h-11 rounded-xl bg-white/[0.04] border border-white/10 flex items-center justify-center overflow-hidden">
                                    <img id="logo-preview" 
                                        src="{{ str_starts_with($bot->logo, 'bot-') ? asset('assets/images/bots/' . $bot->logo) : asset('assets/images/bots/' . $bot->logo) }}" 
                                        alt="{{ $bot->name }}" 
                                        class="w-8 h-8 object-contain">
                                </div>
                            @else
                                <div class="shrink-0 w-11 h-11 rounded-xl bg-white/[0.04] border border-white/10 hidden items-center justify-center overflow-hidden" id="logo-preview-container">
                                    <img id="logo-preview" src="" alt="Preview" class="w-8 h-8 object-contain">
                                </div>
                            @endif
                            <input type="file" id="logo" name="logo" @if(!isset($bot)) required @endif onchange="previewImage(this)"
                                class="flex-1 bg-[#05070d] border border-white/[0.1] rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-cyan-400 transition-all file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-cyan-500/20 file:text-cyan-300 hover:file:bg-cyan-500/30">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <label for="type" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                            {{ __('Trading Market') }} <span class="text-rose-400">*</span>
                        </label>
                        <select id="type" name="type" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-all cursor-pointer">
                            <option value="crypto" {{ old('type', $bot->type ?? '') == 'crypto' ? 'selected' : '' }}>{{ __('Crypto') }}</option>
                            <option value="forex" {{ old('type', $bot->type ?? '') == 'forex' ? 'selected' : '' }}>{{ __('Forex') }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="is_active" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                            {{ __('Status') }} <span class="text-rose-400">*</span>
                        </label>
                        <select id="is_active" name="is_active" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-all cursor-pointer">
                            <option value="1" {{ old('is_active', $bot->is_active ?? '') == '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="0" {{ old('is_active', $bot->is_active ?? '') == '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="is_capital_returned" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                            {{ __('Return Capital') }} <span class="text-rose-400">*</span>
                        </label>
                        <select id="is_capital_returned" name="is_capital_returned" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-all cursor-pointer">
                            <option value="1" {{ old('is_capital_returned', (isset($bot) ? $bot->is_capital_returned : 1)) == 1 ? 'selected' : '' }}>{{ __('Return Capital (100%)') }}</option>
                            <option value="0" {{ old('is_capital_returned', (isset($bot) ? $bot->is_capital_returned : 1)) == 0 ? 'selected' : '' }}>{{ __('Do Not Return (Profit Only)') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Trading Schedule Days --}}
                <div class="pt-2">
                    <label class="block font-bold text-slate-400 uppercase tracking-widest mb-2.5 text-xs">
                        {{ __('Trading Days') }}
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
                        @php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            $selectedDays = old('trading_days', $bot->trading_days ?? $days);
                        @endphp
                        @foreach($days as $day)
                            <label class="cursor-pointer group">
                                <input type="checkbox" name="trading_days[]" value="{{ $day }}" 
                                    {{ in_array($day, $selectedDays) ? 'checked' : '' }}
                                    class="hidden peer">
                                <div class="px-3 py-2 rounded-xl bg-white/[0.02] border border-white/[0.08] text-slate-400 text-[10px] font-bold text-center transition-all peer-checked:bg-cyan-500/20 peer-checked:border-cyan-400 peer-checked:text-cyan-300 group-hover:border-white/20 whitespace-nowrap">
                                    {{ substr($day, 0, 3) }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Pairs & Exchanges Card --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
            <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-white/[0.06]">
                    <div>
                        <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Exchanges & Trading Pairs') }}</h3>
                        <p class="text-xs text-slate-400 font-sans mt-0.5">{{ __('Select supported exchanges and trading pairs for this bot') }}</p>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                </div>

                {{-- Exchanges --}}
                <div id="exchanges-container" class="{{ old('type', $bot->type ?? 'crypto') == 'forex' ? 'hidden' : '' }}">
                    <label class="block font-bold text-slate-400 uppercase tracking-widest mb-2.5 text-xs">{{ __('Exchanges') }}</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-56 overflow-y-auto pr-1">
                        @foreach($data['exchanges'] as $exchange)
                            @php
                                $exchangeLogo = str_replace('.', '', strtolower($exchange)) . '.svg';
                            @endphp
                            <label class="flex items-center gap-3 cursor-pointer group p-3 bg-white/[0.02] border border-white/[0.08] rounded-xl hover:border-cyan-400/40 transition-all">
                                <input type="checkbox" name="exchanges[]" value="{{ $exchange }}" 
                                    {{ in_array($exchange, old('exchanges', $bot->exchanges ?? [])) ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-white/20 bg-white/5 text-cyan-400 focus:ring-cyan-400 accent-cyan-400 cursor-pointer">
                                <span class="text-xs font-bold text-slate-300 group-hover:text-white truncate">{{ $exchange }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Pairs --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block font-bold text-slate-400 uppercase tracking-widest text-xs">{{ __('Trading Pairs') }}</label>
                        <input type="text" id="pair-search" placeholder="{{ __('Search pairs...') }}" 
                            class="w-48 bg-[#05070d] border border-white/[0.1] rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-cyan-400 transition-all">
                    </div>

                    <div id="crypto-pairs" class="grid grid-cols-2 sm:grid-cols-4 gap-2 max-h-60 overflow-y-auto pr-1 p-3 bg-[#05070d] border border-white/[0.08] rounded-2xl {{ old('type', $bot->type ?? 'crypto') == 'forex' ? 'hidden' : '' }}">
                        @php
                            $selectedPairs = old('traded_pairs', $bot->traded_pairs ?? []);
                            $cryptoPairs = $data['pairs']['crypto'] ?? [];
                        @endphp
                        @foreach($cryptoPairs as $pair)
                            <label class="pair-item flex items-center gap-2 cursor-pointer group p-1.5 rounded-lg hover:bg-white/[0.04]" data-pair-name="{{ strtolower($pair) }}">
                                <input type="checkbox" name="traded_pairs_crypto[]" value="{{ $pair }}" 
                                    {{ in_array($pair, $selectedPairs) ? 'checked' : '' }}
                                    class="w-3.5 h-3.5 rounded border-white/20 bg-white/5 text-cyan-400 focus:ring-cyan-400 accent-cyan-400 cursor-pointer">
                                <span class="text-[10px] text-slate-400 group-hover:text-white transition-colors">{{ $pair }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div id="forex-pairs" class="grid grid-cols-2 sm:grid-cols-4 gap-2 max-h-60 overflow-y-auto pr-1 p-3 bg-[#05070d] border border-white/[0.08] rounded-2xl {{ old('type', $bot->type ?? 'crypto') == 'crypto' ? 'hidden' : '' }}">
                        @php
                            $forexPairs = $data['pairs']['forex'] ?? [];
                        @endphp
                        @foreach($forexPairs as $pair)
                            <label class="pair-item flex items-center gap-2 cursor-pointer group p-1.5 rounded-lg hover:bg-white/[0.04]" data-pair-name="{{ strtolower($pair) }}">
                                <input type="checkbox" name="traded_pairs_forex[]" value="{{ $pair }}" 
                                    {{ in_array($pair, $selectedPairs) ? 'checked' : '' }}
                                    class="w-3.5 h-3.5 rounded border-white/20 bg-white/5 text-cyan-400 focus:ring-cyan-400 accent-cyan-400 cursor-pointer">
                                <span class="text-[10px] text-slate-400 group-hover:text-white transition-colors">{{ $pair }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Limits & Durations (1 Col) --}}
    <div class="space-y-6">
        {{-- Limits & Returns --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
            <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-4">
                <div class="pb-3 border-b border-white/[0.06]">
                    <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Investment & Profit Limits') }}</h3>
                    <p class="text-xs text-slate-400 font-sans mt-0.5">{{ __('Set investment amount range and daily profit targets') }}</p>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label for="min_amount" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                            {{ __('Min Investment') }} ({{ getSetting('currency') }})
                        </label>
                        <input type="number" id="min_amount" name="min_amount" step="any" required value="{{ old('min_amount', $bot->min_amount ?? '') }}"
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all"
                            placeholder="100">
                    </div>

                    <div>
                        <label for="max_amount" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                            {{ __('Max Investment') }} ({{ getSetting('currency') }})
                        </label>
                        <input type="number" id="max_amount" name="max_amount" step="any" required value="{{ old('max_amount', $bot->max_amount ?? '') }}"
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all"
                            placeholder="10000">
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div>
                            <label for="daily_return_min" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                                {{ __('Min Daily Profit (%)') }}
                            </label>
                            <input type="number" id="daily_return_min" name="daily_return_min" step="any" required value="{{ old('daily_return_min', $bot->daily_return_min ?? '') }}"
                                class="w-full bg-[#05070d] border border-emerald-500/30 text-emerald-400 font-bold rounded-xl px-3 py-2.5 text-xs focus:outline-none focus:border-emerald-400 transition-all"
                                placeholder="1.2">
                        </div>
                        <div>
                            <label for="daily_return_max" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                                {{ __('Max Daily Profit (%)') }}
                            </label>
                            <input type="number" id="daily_return_max" name="daily_return_max" step="any" required value="{{ old('daily_return_max', $bot->daily_return_max ?? '') }}"
                                class="w-full bg-[#05070d] border border-emerald-500/30 text-emerald-400 font-bold rounded-xl px-3 py-2.5 text-xs focus:outline-none focus:border-emerald-400 transition-all"
                                placeholder="4.5">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Duration Card --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
            <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-4">
                <div class="pb-3 border-b border-white/[0.06]">
                    <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Duration') }}</h3>
                    <p class="text-xs text-slate-400 font-sans mt-0.5">{{ __('How long the bot runs before completing') }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <label for="duration" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Duration') }}</label>
                        <input type="number" id="duration" name="duration" min="1" required value="{{ old('duration', $bot->duration ?? '') }}"
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all"
                            placeholder="30">
                    </div>
                    <div>
                        <label for="duration_type" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Time Unit') }}</label>
                        <select id="duration_type" name="duration_type" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all cursor-pointer">
                            @foreach(['hour', 'day', 'week', 'month', 'year'] as $type)
                                <option value="{{ $type }}" {{ old('duration_type', $bot->duration_type ?? '') == $type ? 'selected' : '' }}>{{ __(ucfirst($type)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
