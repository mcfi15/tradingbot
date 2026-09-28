<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-mono text-xs">
    {{-- Left Column: Core Details (2 cols) --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
            <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-white/[0.06]">
                    <div>
                        <h3 class="text-base font-bold text-white uppercase tracking-wide flex items-center gap-2">
                            <span>{{ __('Trade Details') }}</span>
                        </h3>
                        <p class="text-xs text-slate-400 font-sans mt-0.5">{{ __('Set up trading pair, expected profit, and investment type') }}</p>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                        </svg>
                    </div>
                </div>

                @if(isset($tickerError) && $tickerError)
                    <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-xl text-xs flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-bold">{{ __('Market Feed Notice') }}</p>
                            <p class="opacity-80">{{ $tickerError }}</p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="code" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Trade Code') }}</label>
                        <input type="text" id="code" name="code" readonly value="{{ $strategy->code ?? __('AUTO-GENERATED') }}"
                            class="w-full bg-[#05070d] border border-white/[0.08] rounded-xl px-4 py-3 text-slate-500 text-xs focus:outline-none cursor-not-allowed uppercase font-black"
                            placeholder="{{ __('e.g., XDC-ALPHA-01') }}">
                    </div>

                    <div>
                        <label for="pair" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Trading Pair') }} <span class="text-rose-400">*</span></label>
                        <select id="pair" name="pair" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-emerald-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-all cursor-pointer">
                            <option value="">{{ __('Select Trading Pair') }}</option>
                            @foreach($tickers as $ticker)
                                <option value="{{ $ticker['ticker'] }}" {{ (old('pair', $strategy->pair ?? '') == $ticker['ticker']) ? 'selected' : '' }}>
                                    {{ $ticker['ticker'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="roi" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Expected Profit (%)') }} <span class="text-rose-400">*</span></label>
                        <input type="number" id="roi" name="roi" step="0.01" required value="{{ old('roi', $strategy->roi ?? '') }}"
                            class="w-full bg-[#05070d] border border-emerald-500/30 text-emerald-400 font-bold rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-emerald-400 transition-all"
                            placeholder="18.50">
                    </div>

                    <div>
                        <label for="amount_type" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Investment Type') }} <span class="text-rose-400">*</span></label>
                        <select id="amount_type" name="amount_type" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-emerald-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-all cursor-pointer">
                            <option value="manual" {{ (old('amount_type', $strategy->amount_type ?? 'manual') == 'manual') ? 'selected' : '' }}>
                                {{ __('Custom Amount (User decides)') }}
                            </option>
                            <option value="percentage" {{ (old('amount_type', $strategy->amount_type ?? 'manual') == 'percentage') ? 'selected' : '' }}>
                                {{ __('Percentage of Balance') }}
                            </option>
                        </select>
                    </div>
                </div>

                <div id="percentage_container" class="{{ (old('amount_type', $strategy->amount_type ?? 'manual') == 'percentage') ? '' : 'hidden' }}">
                    <label for="percentage" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Percentage of Balance (%)') }} <span class="text-rose-400">*</span></label>
                    <input type="number" id="percentage" name="percentage" step="0.01" value="{{ old('percentage', $strategy->percentage ?? '') }}"
                        class="w-full bg-[#05070d] border border-white/[0.1] focus:border-emerald-400 text-white font-bold rounded-xl px-4 py-3 text-xs focus:outline-none transition-all"
                        placeholder="15.00">
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Expiration & Notes (1 col) --}}
    <div class="space-y-6">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
            <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-4">
                <div class="pb-3 border-b border-white/[0.06]">
                    <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Expiration') }}</h3>
                    <p class="text-xs text-slate-400 font-sans mt-0.5">{{ __('Set an expiration date and time for this trade') }}</p>
                </div>

                <div>
                    <label for="expires_at" class="block font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ __('Expiration Date & Time') }}</label>
                    <input type="datetime-local" id="expires_at" name="expires_at" 
                        value="{{ isset($strategy->expires_at) ? date('Y-m-d\TH:i', $strategy->expires_at) : old('expires_at') }}"
                        min="{{ date('Y-m-d\TH:i') }}"
                        class="w-full bg-[#05070d] border border-white/[0.1] focus:border-emerald-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none transition-all [color-scheme:dark]">
                    <p class="text-[10px] text-slate-500 mt-2 italic">
                        {{ __('Leave blank if this trade should never expire.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
