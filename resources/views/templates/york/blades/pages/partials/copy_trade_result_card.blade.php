<div class="group relative rounded-3xl p-[1px] bg-gradient-to-b from-white/[0.12] via-white/[0.04] to-transparent hover:from-emerald-500/40 hover:via-emerald-500/10 hover:to-transparent transition-all duration-500 transform hover:-translate-y-1.5 hover:shadow-[0_20px_50px_rgba(16,185,129,0.1)] flex flex-col h-full overflow-hidden">
    
    {{-- Internal Double-Bezel Hardened Surface --}}
    <div class="relative bg-[#090c14]/95 rounded-[calc(1.5rem-1px)] p-6 sm:p-7 h-full flex flex-col justify-between overflow-hidden backdrop-blur-2xl">
        
        {{-- Ambient Corner Glow --}}
        <div class="absolute -top-16 -right-16 w-36 h-36 bg-emerald-500/10 group-hover:bg-emerald-500/25 rounded-full blur-2xl pointer-events-none transition-all duration-700"></div>

        <div>
            {{-- Header: Pair & Win Percentage Badge --}}
            <div class="flex items-center justify-between gap-3 mb-5 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-white/10 to-white/[0.02] p-[1px] shrink-0 group-hover:from-emerald-500/50 group-hover:to-teal-500/50 transition-all duration-500">
                        <div class="w-full h-full rounded-[15px] bg-[#050507] flex items-center justify-center font-mono font-black text-xs text-white">
                            {{ substr($trade->pair, 0, 3) }}
                        </div>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-white group-hover:text-emerald-300 transition-colors uppercase tracking-tight">
                            {{ $trade->pair }}
                        </h4>
                        <span class="text-[9px] text-slate-500 font-mono uppercase tracking-widest block">
                            {{ __('Trade ID:') }} #{{ substr($trade->copy_code, 0, 8) }}
                        </span>
                    </div>
                </div>

                <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-mono text-xs font-black shadow-sm">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    +{{ number_format($trade->roi, 2) }}%
                </div>
            </div>

            {{-- Profit Display Card --}}
            <div class="mb-5 p-4 rounded-2xl bg-black/40 border border-white/[0.06] group-hover:border-emerald-500/20 transition-all relative overflow-hidden">
                <div class="flex justify-between items-center text-[10px] font-mono text-slate-400 mb-1 uppercase tracking-wider">
                    <span>{{ __('Net Profit Earned') }}</span>
                    <span class="text-emerald-400 font-bold">{{ __('COMPLETED') }}</span>
                </div>
                <div class="text-2xl sm:text-3xl font-black font-mono text-white tracking-tight flex items-baseline gap-1.5">
                    <span class="text-emerald-400">+</span>{{ showAmount($trade->profit) }}
                </div>
            </div>

            {{-- Metrics Grid --}}
            <div class="grid grid-cols-2 gap-2.5 font-mono text-xs mb-5">
                <div class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.06]">
                    <span class="text-[9px] uppercase tracking-wider text-slate-500 block mb-1">{{ __('Amount Invested') }}</span>
                    <span class="text-xs font-bold text-white">{{ showAmount($trade->amount) }}</span>
                </div>
                <div class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.06] text-right">
                    <span class="text-[9px] uppercase tracking-wider text-slate-500 block mb-1">{{ __('Trade Duration') }}</span>
                    <span class="text-xs font-bold text-slate-300">{{ $trade->completed_at->diffForHumans($trade->activated_at, true) }}</span>
                </div>
            </div>
        </div>

        {{-- Verification Footer --}}
        <div class="pt-4 border-t border-white/[0.06] flex items-center justify-between font-mono text-[10px] relative z-10">
            <div class="flex items-center gap-1.5 text-emerald-400 font-bold">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_8px_#10b981]"></span>
                <span>{{ __('Verified Result') }}</span>
            </div>
            <span class="text-slate-500">{{ $trade->completed_at->format('M d, H:i') }}</span>
        </div>
    </div>
</div>
