@php
    $user = auth()->user();

    // 1. KYC Identity Verification
    $latestKyc = $user->kyc()->latest()->first();
    $shouldShowKycPrompt = moduleEnabled('kyc_module') && (!$latestKyc || $latestKyc->status == 'rejected' || config('app.env') == 'sandbox');
    $isKycRejected = $latestKyc && $latestKyc->status == 'rejected';

    // 2. Trading Strategy Onboarding
    $shouldShowOnboardingPrompt = !$user->onboarding;

    // 3. First Deposit
    $hasCompletedDeposit = $user->deposits()->whereIn('status', ['completed', 'approved'])->exists();
    $shouldShowDepositPrompt = !$hasCompletedDeposit;

    // 4. First Bot Trading
    $hasBotActivation = $user->tradingBotActivations()->exists();
    $shouldShowBotPrompt = moduleEnabled('trading_bot_module') && !$hasBotActivation;

    // 5. First Copy Trading
    $hasCopyTrade = $user->copyTradingHistories()->exists();
    $shouldShowCopyPrompt = moduleEnabled('copy_trading_module') && !$hasCopyTrade;

    $pendingCount = ($shouldShowKycPrompt ? 1 : 0)
        + ($shouldShowOnboardingPrompt ? 1 : 0)
        + ($shouldShowDepositPrompt ? 1 : 0)
        + ($shouldShowBotPrompt ? 1 : 0)
        + ($shouldShowCopyPrompt ? 1 : 0);

    $hasPendingActions = $pendingCount > 0;
@endphp

@if ($hasPendingActions)
    {{-- ==================================================================================== --}}
    {{-- PENDING ACTION NOTICE STRIP --}}
    {{-- ==================================================================================== --}}
    <div id="pending-action-notice"
        class="mb-8 group relative rounded-2xl p-[1px] bg-gradient-to-r from-red-500/30 via-white/[0.06] to-red-500/20 shadow-[0_10px_35px_rgba(239,68,68,0.08)] hover:shadow-[0_10px_40px_rgba(239,68,68,0.15)] transition-all duration-300">
        
        <div class="relative bg-[#090d16]/95 hover:bg-[#0b101c]/95 rounded-[calc(1rem-1px)] px-4 py-3.5 sm:px-5 sm:py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 backdrop-blur-2xl transition-colors duration-200 cursor-pointer"
             id="pending-notice-trigger">
            
            {{-- Ambient Glow --}}
            <div class="absolute -top-12 left-12 w-36 h-36 bg-red-500/10 rounded-full blur-2xl pointer-events-none"></div>

            {{-- Left Content: Red Attention Icon & Typography --}}
            <div class="flex items-center gap-3.5 relative z-10 w-full sm:w-auto">
                {{-- Attention Signal --}}
                <div class="relative flex items-center justify-center shrink-0 w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/25 text-red-400 shadow-[0_0_20px_rgba(239,68,68,0.25)]">
                    <span class="absolute -top-0.5 -right-0.5 flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-[2.2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 9v4"/>
                        <path d="M12 17h.01"/>
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                    </svg>
                </div>

                <div>
                    <div class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 font-mono text-[9px] font-bold uppercase tracking-widest mb-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span>
                        <span>{{ __('ACTION NEEDED') }} • <span id="pending-items-count">{{ $pendingCount }}</span> {{ __('LEFT') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-white text-sm tracking-tight">
                            {{ __('Finish Account Setup') }}
                        </h3>
                        <span class="hidden md:inline text-slate-400 text-xs font-normal">
                            : {{ __('Complete these steps to trade without limits.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Right Content: Review Trigger Button & Dismiss --}}
            <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end relative z-10 shrink-0">
                <button type="button" id="trigger-pending-drawer-btn"
                    class="group/btn whitespace-nowrap px-4 py-2 bg-white/[0.04] hover:bg-white/[0.08] active:bg-white/[0.12] border border-white/10 hover:border-white/20 text-white font-mono text-xs font-bold uppercase tracking-wider rounded-xl transition-all duration-200 flex items-center gap-2 cursor-pointer active:scale-95 shadow-sm">
                    <span>{{ __('View Tasks') }}</span>
                    <span class="w-5 h-5 rounded-lg bg-red-500/20 text-red-400 flex items-center justify-center text-[10px] group-hover/btn:-translate-y-0.5 transition-transform">
                        ↑
                    </span>
                </button>
                <button type="button" id="dismiss-pending-notice"
                    class="p-2 text-slate-400 hover:text-white bg-white/[0.02] hover:bg-white/[0.06] rounded-xl transition-colors border border-white/5 cursor-pointer shrink-0"
                    title="{{ __('Dismiss notice') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ==================================================================================== --}}
    {{-- BOTTOM DRAWER MODAL --}}
    {{-- ==================================================================================== --}}
    <div id="pending-drawer-backdrop"
        class="fixed inset-0 bg-black/80 backdrop-blur-md z-[85] opacity-0 pointer-events-none transition-opacity duration-300"></div>

    <div id="pending-drawer"
        class="fixed bottom-0 inset-x-0 z-[90] max-w-2xl mx-auto w-full rounded-t-[2.5rem] bg-[#090d16] border-t border-x border-white/10 shadow-[0_-25px_60px_rgba(0,0,0,0.95)] p-6 sm:p-8 translate-y-full transition-transform duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] flex flex-col max-h-[85vh] overflow-y-auto scrollbar-thin scrollbar-thumb-white/10">
        
        {{-- Grab Handle --}}
        <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-6 shrink-0"></div>

        {{-- Drawer Header --}}
        <div class="flex items-start justify-between gap-4 pb-5 border-b border-white/[0.08] shrink-0">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 font-mono text-[9px] font-bold uppercase tracking-widest mb-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span>
                    <span>{{ __('REQUIRED TASKS') }}</span>
                </div>
                <h3 class="text-xl font-black text-white tracking-tight">{{ __('Next Steps for Your Account') }}</h3>
                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                    {{ __('Complete these items to deposit, trade, and withdraw without limits.') }}
                </p>
            </div>

            <button type="button" id="close-pending-drawer"
                class="w-9 h-9 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] border border-white/10 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 stroke-[2.5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        {{-- Action Items List --}}
        <div class="space-y-4 py-5" id="pending-actions-list">
            
            {{-- Action 1: KYC Identity Verification --}}
            @if ($shouldShowKycPrompt)
                <div id="drawer-item-kyc"
                    class="group relative rounded-2xl p-[1px] bg-gradient-to-r {{ $isKycRejected ? 'from-rose-500/40 via-white/[0.06] to-amber-500/20' : 'from-red-500/30 via-white/[0.06] to-amber-500/20' }} transition-all duration-300">
                    <div class="relative bg-[#0c101c] rounded-[calc(1rem-1px)] p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-11 h-11 rounded-2xl {{ $isKycRejected ? 'bg-rose-500/10 border-rose-500/25 text-rose-400' : 'bg-red-500/10 border-red-500/25 text-red-400' }} border flex items-center justify-center shrink-0 shadow-inner mt-0.5 sm:mt-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-[2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    @if ($isKycRejected)
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    @else
                                        <path d="m9 12 2 2 4-4"/>
                                    @endif
                                </svg>
                            </div>
                            <div>
                                <div class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full {{ $isKycRejected ? 'bg-rose-500/10 border-rose-500/20 text-rose-400' : 'bg-red-500/10 border-red-500/20 text-red-400' }} font-mono text-[9px] font-bold uppercase tracking-widest mb-1">
                                    <span>{{ $isKycRejected ? __('NEEDS RESUBMISSION') : __('ID CHECK') }}</span>
                                </div>
                                <h4 class="font-bold text-white text-sm tracking-tight">
                                    {{ $isKycRejected ? __('ID Check Failed') : __('Verify Your Identity') }}
                                </h4>
                                <p class="text-xs text-slate-400 mt-1 leading-relaxed max-w-md">
                                    {{ $isKycRejected
                                        ? __('Your previous documents were not accepted. Upload a clear photo of your valid ID.')
                                        : __('Upload your government ID to unlock higher withdrawal limits and all deposit methods.') }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('user.kyc') }}"
                            class="group/btn w-full sm:w-auto whitespace-nowrap px-4 py-2.5 rounded-xl bg-white/[0.06] hover:bg-white/[0.12] border border-white/10 hover:border-white/20 text-white font-mono text-xs font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 active:scale-95 shrink-0">
                            <span>{{ $isKycRejected ? __('Upload ID Again') : __('Verify ID') }}</span>
                            <span class="w-5 h-5 rounded-lg bg-white/10 flex items-center justify-center text-[10px] group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5 transition-transform">↗</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Action 2: Trading Strategy Onboarding --}}
            @if ($shouldShowOnboardingPrompt)
                <div id="drawer-item-onboarding"
                    class="group relative rounded-2xl p-[1px] bg-gradient-to-r from-amber-500/30 via-white/[0.06] to-emerald-500/20 transition-all duration-300">
                    <div class="relative bg-[#0c101c] rounded-[calc(1rem-1px)] p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-amber-500/10 border border-amber-500/25 text-amber-400 border flex items-center justify-center shrink-0 shadow-inner mt-0.5 sm:mt-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-[2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2v20"/>
                                    <path d="m17 5-5-3-5 3"/>
                                    <path d="m17 19-5 3-5-3"/>
                                </svg>
                            </div>
                            <div>
                                <div class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 font-mono text-[9px] font-bold uppercase tracking-widest mb-1">
                                    <span>{{ __('TRADING PREFERENCE') }}</span>
                                </div>
                                <h4 class="font-bold text-white text-sm tracking-tight">
                                    {{ __('Choose Your Trading Style') }}
                                </h4>
                                <p class="text-xs text-slate-400 mt-1 leading-relaxed max-w-md">
                                    {{ __('Pick how much risk your automated bots take when trading on your behalf.') }}
                                </p>
                            </div>
                        </div>

                        <button type="button" id="drawer-trigger-onboarding"
                            class="group/btn w-full sm:w-auto whitespace-nowrap px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-mono text-xs font-black uppercase tracking-wider shadow-[0_0_20px_rgba(245,158,11,0.2)] transition-all flex items-center justify-center gap-2 active:scale-95 cursor-pointer shrink-0">
                            <span>{{ __('Set Style') }}</span>
                            <span class="w-5 h-5 rounded-lg bg-black/15 flex items-center justify-center text-[10px] group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5 transition-transform">↗</span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Action 3: First Deposit --}}
            @if ($shouldShowDepositPrompt)
                <div id="drawer-item-deposit"
                    class="group relative rounded-2xl p-[1px] bg-gradient-to-r from-emerald-500/30 via-white/[0.06] to-teal-500/20 transition-all duration-300">
                    <div class="relative bg-[#0c101c] rounded-[calc(1rem-1px)] p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 border flex items-center justify-center shrink-0 shadow-inner mt-0.5 sm:mt-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-[2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/>
                                    <path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/>
                                    <path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-mono text-[9px] font-bold uppercase tracking-widest mb-1">
                                    <span>{{ __('FIRST DEPOSIT') }}</span>
                                </div>
                                <h4 class="font-bold text-white text-sm tracking-tight">
                                    {{ __('Deposit Funds') }}
                                </h4>
                                <p class="text-xs text-slate-400 mt-1 leading-relaxed max-w-md">
                                    {{ __('Add crypto or cash to your wallet so your bots can trade.') }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('user.deposits.new') }}"
                            class="group/btn w-full sm:w-auto whitespace-nowrap px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-400 to-emerald-500 hover:from-emerald-300 hover:to-emerald-400 text-slate-950 font-mono text-xs font-black uppercase tracking-wider shadow-[0_0_20px_rgba(16,185,129,0.2)] transition-all flex items-center justify-center gap-2 active:scale-95 shrink-0">
                            <span>{{ __('Deposit') }}</span>
                            <span class="w-5 h-5 rounded-lg bg-black/15 flex items-center justify-center text-[10px] group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5 transition-transform">↗</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Action 4: First Bot Trading --}}
            @if ($shouldShowBotPrompt)
                <div id="drawer-item-bot"
                    class="group relative rounded-2xl p-[1px] bg-gradient-to-r from-cyan-500/30 via-white/[0.06] to-blue-500/20 transition-all duration-300">
                    <div class="relative bg-[#0c101c] rounded-[calc(1rem-1px)] p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-cyan-500/10 border border-cyan-500/25 text-cyan-400 border flex items-center justify-center shrink-0 shadow-inner mt-0.5 sm:mt-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-[2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 8V4H8"/>
                                    <rect width="16" height="12" x="4" y="8" rx="2"/>
                                    <path d="M2 14h2"/>
                                    <path d="M20 14h2"/>
                                    <path d="M15 13v2"/>
                                    <path d="M9 13v2"/>
                                </svg>
                            </div>
                            <div>
                                <div class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[9px] font-bold uppercase tracking-widest mb-1">
                                    <span>{{ __('BOT TRADING') }}</span>
                                </div>
                                <h4 class="font-bold text-white text-sm tracking-tight">
                                    {{ __('Start a Trading Bot') }}
                                </h4>
                                <p class="text-xs text-slate-400 mt-1 leading-relaxed max-w-md">
                                    {{ __('Run an automated bot to trade pairs for you around the clock.') }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('user.trading-bots.index') }}"
                            class="group/btn w-full sm:w-auto whitespace-nowrap px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-300 hover:to-blue-400 text-slate-950 font-mono text-xs font-black uppercase tracking-wider shadow-[0_0_20px_rgba(6,182,212,0.2)] transition-all flex items-center justify-center gap-2 active:scale-95 shrink-0">
                            <span>{{ __('Start Bot') }}</span>
                            <span class="w-5 h-5 rounded-lg bg-black/15 flex items-center justify-center text-[10px] group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5 transition-transform">↗</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Action 5: First Copy Trading --}}
            @if ($shouldShowCopyPrompt)
                <div id="drawer-item-copy"
                    class="group relative rounded-2xl p-[1px] bg-gradient-to-r from-indigo-500/30 via-white/[0.06] to-purple-500/20 transition-all duration-300">
                    <div class="relative bg-[#0c101c] rounded-[calc(1rem-1px)] p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-indigo-500/10 border border-indigo-500/25 text-indigo-400 border flex items-center justify-center shrink-0 shadow-inner mt-0.5 sm:mt-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-[2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                                    <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                                </svg>
                            </div>
                            <div>
                                <div class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 font-mono text-[9px] font-bold uppercase tracking-widest mb-1">
                                    <span>{{ __('COPY TRADING') }}</span>
                                </div>
                                <h4 class="font-bold text-white text-sm tracking-tight">
                                    {{ __('Start Copy Trading') }}
                                </h4>
                                <p class="text-xs text-slate-400 mt-1 leading-relaxed max-w-md">
                                    {{ __('Follow top traders and mirror their moves in real time.') }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('user.copy-trading.index') }}"
                            class="group/btn w-full sm:w-auto whitespace-nowrap px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-400 to-purple-500 hover:from-indigo-300 hover:to-purple-400 text-white font-mono text-xs font-black uppercase tracking-wider shadow-[0_0_20px_rgba(99,102,241,0.2)] transition-all flex items-center justify-center gap-2 active:scale-95 shrink-0">
                            <span>{{ __('Copy Trades') }}</span>
                            <span class="w-5 h-5 rounded-lg bg-black/20 flex items-center justify-center text-[10px] group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5 transition-transform">↗</span>
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endif

@push('scripts')
    <script>
        $(document).ready(function() {
            function openPendingDrawer() {
                $('#pending-drawer-backdrop').removeClass('pointer-events-none opacity-0');
                $('#pending-drawer').removeClass('translate-y-full');
            }

            function closePendingDrawer() {
                $('#pending-drawer-backdrop').addClass('pointer-events-none opacity-0');
                $('#pending-drawer').addClass('translate-y-full');
            }

            // Click triggers
            $('#pending-notice-trigger').on('click', function(e) {
                if ($(e.target).closest('#dismiss-pending-notice').length) return;
                openPendingDrawer();
            });

            $('#trigger-pending-drawer-btn').on('click', function(e) {
                e.stopPropagation();
                openPendingDrawer();
            });

            $('#close-pending-drawer, #pending-drawer-backdrop').on('click', function() {
                closePendingDrawer();
            });

            // ESC key to close
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    closePendingDrawer();
                }
            });

            // Dismiss notice banner
            $('#dismiss-pending-notice').on('click', function(e) {
                e.stopPropagation();
                $('#pending-action-notice').fadeOut(250, function() {
                    $(this).remove();
                });
            });

            // Trigger Onboarding Modal from drawer
            $('#drawer-trigger-onboarding').on('click', function() {
                closePendingDrawer();
                setTimeout(function() {
                    $('#onboarding-modal').removeClass('opacity-0 pointer-events-none');
                    $('#onboarding-content').removeClass('scale-95');
                }, 250);
            });
        });
    </script>
@endpush
