<!-- Onboarding Modal Overlay -->
<div id="onboarding-modal"
    class="fixed inset-0 bg-[#050507]/90 backdrop-blur-2xl z-[98] flex items-center justify-center p-2.5 sm:p-6 opacity-0 pointer-events-none transition-all duration-500 overflow-y-auto overflow-x-hidden">

    <!-- Modal Double-Bezel Hardened Container -->
    <div class="relative w-full max-w-3xl rounded-[1.75rem] sm:rounded-[2rem] p-[1px] bg-gradient-to-b from-white/[0.15] via-white/[0.05] to-transparent shadow-[0_25px_80px_rgba(0,0,0,0.95)] overflow-hidden transform scale-95 transition-all duration-500 flex flex-col my-auto max-h-[calc(100dvh-1.25rem)] sm:max-h-[min(88vh,820px)]"
        id="onboarding-content">

        <!-- Internal Hardware Surface -->
        <div class="relative bg-[#090d16]/98 rounded-[calc(1.75rem-1px)] sm:rounded-[calc(2rem-1px)] backdrop-blur-3xl overflow-hidden flex flex-col flex-1 min-h-0">
            
            <!-- Ambient Glow Accents -->
            <div class="absolute -top-24 -right-24 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top HUD Bar (Pinned Top) -->
            <div class="relative flex items-center justify-between gap-3 sm:gap-4 p-4 sm:px-8 sm:pt-7 sm:pb-5 border-b border-white/[0.08] z-10 shrink-0 bg-[#090d16]/98">
                <div class="min-w-0 flex-1 pr-2">
                    <div class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 py-0.5 sm:py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 font-mono text-[9px] sm:text-[10px] font-bold uppercase tracking-widest mb-1 sm:mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse shrink-0"></span>
                        <span>{{ __('BOT SETTINGS') }}</span>
                    </div>
                    <h2 class="text-lg sm:text-2xl font-black text-white tracking-tight leading-snug">
                        {{ __('Choose Your Trading Strategy') }}
                    </h2>
                    <p class="text-[11px] sm:text-sm text-slate-400 mt-0.5 font-body line-clamp-1 sm:line-clamp-none">
                        {{ __('Pick a risk level for your automated bots. You can change this at any time.') }}
                    </p>
                </div>

                <button type="button" id="onboarding-close" aria-label="{{ __('Close') }}"
                    class="w-9 h-9 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] active:scale-95 border border-white/10 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Container -->
            <form id="onboarding-form" action="{{ route('user.onboarding') }}" method="POST" class="relative z-10 flex flex-col flex-1 min-h-0 overflow-hidden">
                @csrf

                <!-- Scrollable Body Container -->
                <div class="flex-1 overflow-y-auto overscroll-contain p-4 sm:px-8 sm:py-6 space-y-4 sm:space-y-6 [scrollbar-width:thin] [scrollbar-color:rgba(255,255,255,0.15)_transparent] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-white/15 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-white/25">

                    <!-- Strategy Selection Matrix -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 sm:gap-4" id="strategy-cards-grid">

                        <!-- 1. Conservative -->
                        <label class="cursor-pointer group relative block">
                            <input type="radio" name="risk_profile" value="conservative" class="strategy-radio peer sr-only">
                            <div class="strategy-card h-full p-4 sm:p-5 rounded-2xl border border-white/[0.08] bg-white/[0.02] hover:bg-white/[0.04] hover:border-emerald-500/40 
                                        transition-all duration-300 flex flex-col justify-between text-left gap-3.5 sm:gap-4 relative overflow-hidden"
                                 data-strategy="conservative">
                                
                                <!-- Header Info -->
                                <div class="flex items-start justify-between w-full">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 shadow-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                                            <path d="m9 12 2 2 4-4" />
                                        </svg>
                                    </div>
                                    <span class="strategy-indicator w-4 h-4 rounded-full border border-white/20 flex items-center justify-center transition-all">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 opacity-0 transition-opacity"></span>
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-[9px] font-mono font-bold uppercase tracking-widest text-emerald-400 block mb-0.5">
                                        {{ __('LOW RISK') }}
                                    </span>
                                    <h4 class="text-sm sm:text-base font-bold text-white group-hover:text-emerald-400 transition-colors">
                                        {{ __('Conservative') }}
                                    </h4>
                                    <p class="text-xs text-slate-400 mt-1 leading-relaxed font-body">
                                        {{ __('Protects your funds. Trades safe setups to deliver small, steady returns.') }}
                                    </p>
                                </div>

                                <!-- Metrics Strip -->
                                <div class="pt-3 border-t border-white/[0.06] grid grid-cols-2 gap-2 text-[10px] font-mono">
                                    <div>
                                        <span class="text-slate-500 block uppercase">{{ __('Est. Return') }}</span>
                                        <span class="text-emerald-400 font-bold">8% - 14%</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 block uppercase">{{ __('Max Loss Risk') }}</span>
                                        <span class="text-white font-bold">&lt; 2.5%</span>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- 2. Balanced (Recommended Default) -->
                        <label class="cursor-pointer group relative block">
                            <input type="radio" name="risk_profile" value="balanced" class="strategy-radio peer sr-only" checked>
                            <div class="strategy-card h-full p-4 sm:p-5 rounded-2xl border border-amber-500/60 bg-amber-500/10 ring-1 ring-amber-500/50 
                                        transition-all duration-300 flex flex-col justify-between text-left gap-3.5 sm:gap-4 relative overflow-hidden"
                                 data-strategy="balanced">
                                
                                <!-- Recommended Tag -->
                                <div class="absolute top-0 right-0 px-2.5 py-0.5 rounded-bl-xl bg-amber-500/20 border-b border-l border-amber-500/30 text-amber-400 font-mono text-[8px] font-bold uppercase tracking-widest">
                                    {{ __('POPULAR') }}
                                </div>

                                <!-- Header Info -->
                                <div class="flex items-start justify-between w-full">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0 shadow-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                                            <polyline points="16 7 22 7 22 13"></polyline>
                                        </svg>
                                    </div>
                                    <span class="strategy-indicator w-4 h-4 rounded-full border border-amber-400 bg-amber-400/20 flex items-center justify-center transition-all mr-14">
                                        <span class="w-2 h-2 rounded-full bg-amber-400 opacity-100 transition-opacity"></span>
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-[9px] font-mono font-bold uppercase tracking-widest text-amber-400 block mb-0.5">
                                        {{ __('MODERATE RISK') }}
                                    </span>
                                    <h4 class="text-sm sm:text-base font-bold text-white group-hover:text-amber-400 transition-colors">
                                        {{ __('Balanced') }}
                                    </h4>
                                    <p class="text-xs text-slate-400 mt-1 leading-relaxed font-body">
                                        {{ __('Trades major market trends while keeping overall risk at a moderate level.') }}
                                    </p>
                                </div>

                                <!-- Metrics Strip -->
                                <div class="pt-3 border-t border-white/[0.06] grid grid-cols-2 gap-2 text-[10px] font-mono">
                                    <div>
                                        <span class="text-slate-500 block uppercase">{{ __('Est. Return') }}</span>
                                        <span class="text-amber-400 font-bold">28% - 52%</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 block uppercase">{{ __('Max Loss Risk') }}</span>
                                        <span class="text-white font-bold">&lt; 8%</span>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- 3. High Growth -->
                        <label class="cursor-pointer group relative block">
                            <input type="radio" name="risk_profile" value="growth" class="strategy-radio peer sr-only">
                            <div class="strategy-card h-full p-4 sm:p-5 rounded-2xl border border-white/[0.08] bg-white/[0.02] hover:bg-white/[0.04] hover:border-rose-500/40 
                                        transition-all duration-300 flex flex-col justify-between text-left gap-3.5 sm:gap-4 relative overflow-hidden"
                                 data-strategy="growth">
                                
                                <!-- Header Info -->
                                <div class="flex items-start justify-between w-full">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 shadow-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                                        </svg>
                                    </div>
                                    <span class="strategy-indicator w-4 h-4 rounded-full border border-white/20 flex items-center justify-center transition-all">
                                        <span class="w-2 h-2 rounded-full bg-rose-400 opacity-0 transition-opacity"></span>
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-[9px] font-mono font-bold uppercase tracking-widest text-rose-400 block mb-0.5">
                                        {{ __('HIGH RISK') }}
                                    </span>
                                    <h4 class="text-sm sm:text-base font-bold text-white group-hover:text-rose-400 transition-colors">
                                        {{ __('High Growth') }}
                                    </h4>
                                    <p class="text-xs text-slate-400 mt-1 leading-relaxed font-body">
                                        {{ __('Targets faster gains with rapid short-term trades. Carries higher price swings.') }}
                                    </p>
                                </div>

                                <!-- Metrics Strip -->
                                <div class="pt-3 border-t border-white/[0.06] grid grid-cols-2 gap-2 text-[10px] font-mono">
                                    <div>
                                        <span class="text-slate-500 block uppercase">{{ __('Est. Return') }}</span>
                                        <span class="text-rose-400 font-bold">68% - 120%+</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 block uppercase">{{ __('Max Loss Risk') }}</span>
                                        <span class="text-white font-bold">&lt; 17%</span>
                                    </div>
                                </div>
                            </div>
                        </label>

                    </div>

                    <!-- Strategy Summary Box -->
                    <div class="rounded-2xl bg-white/[0.02] border border-white/[0.06] p-3.5 sm:p-5">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                {{ __('STRATEGY SUMMARY') }}
                            </span>
                            <span class="text-[10px] font-mono text-amber-400 font-bold" id="telemetry-risk-label">
                                {{ __('Moderate Risk • 60%') }}
                            </span>
                        </div>

                        <!-- Risk Bar -->
                        <div class="h-2 w-full bg-white/[0.05] rounded-full overflow-hidden mb-3.5 sm:mb-4 p-[1px]">
                            <div id="telemetry-risk-bar"
                                 class="h-full rounded-full transition-all duration-500 ease-out bg-amber-400 shadow-[0_0_12px_rgba(245,158,11,0.5)] w-[60%]">
                            </div>
                        </div>

                        <!-- Parameter Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 text-left font-mono">
                            <div class="bg-black/20 p-2.5 rounded-xl border border-white/[0.04]">
                                <span class="text-[9px] text-slate-500 block uppercase">{{ __('Trade Type') }}</span>
                                <span class="text-xs text-white font-bold tracking-tight block truncate" id="telemetry-engine-mode">Grid & Trend</span>
                            </div>
                            <div class="bg-black/20 p-2.5 rounded-xl border border-white/[0.04]">
                                <span class="text-[9px] text-slate-500 block uppercase">{{ __('Protection') }}</span>
                                <span class="text-xs text-white font-bold tracking-tight block truncate" id="telemetry-hedge-ratio">50% Hedged</span>
                            </div>
                            <div class="bg-black/20 p-2.5 rounded-xl border border-white/[0.04]">
                                <span class="text-[9px] text-slate-500 block uppercase">{{ __('Check Rate') }}</span>
                                <span class="text-xs text-white font-bold tracking-tight block truncate" id="telemetry-exec-tick">Every 15 Mins</span>
                            </div>
                            <div class="bg-black/20 p-2.5 rounded-xl border border-white/[0.04]">
                                <span class="text-[9px] text-slate-500 block uppercase">{{ __('Safety Stop') }}</span>
                                <span class="text-xs text-white font-bold tracking-tight block truncate" id="telemetry-stop-loss">4.5% Trailing</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer CTA (Pinned Bottom) -->
                <div class="shrink-0 p-4 sm:px-8 sm:py-4 border-t border-white/[0.08] bg-[#090d16]/98 backdrop-blur-xl flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 z-10">
                    <p class="text-[10px] sm:text-[11px] text-slate-500 flex items-center gap-1.5 font-body text-center sm:text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <span>{{ __('You can change these settings at any time in your bot settings.') }}</span>
                    </p>

                    <button type="submit" id="onboarding-submit-btn"
                        class="group/btn w-full sm:w-auto px-6 sm:px-7 py-3 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-mono text-xs font-black uppercase tracking-wider shadow-[0_0_25px_rgba(245,158,11,0.25)] hover:shadow-[0_0_35px_rgba(245,158,11,0.4)] transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2.5 cursor-pointer shrink-0">
                        <span id="onboarding-submit-label">{{ __('Save Strategy') }}</span>
                        <span class="w-6 h-6 rounded-lg bg-black/15 flex items-center justify-center text-xs group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5 transition-transform">
                            ↗
                        </span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Strategy metadata configuration
            const strategySpecs = {
                conservative: {
                    riskLabel: "{{ __('Low Risk • 25%') }}",
                    riskWidth: "25%",
                    barColor: "bg-emerald-400",
                    barShadow: "shadow-[0_0_12px_rgba(52,211,153,0.5)]",
                    labelColor: "text-emerald-400",
                    engineMode: "{{ __('Hedging & Staking') }}",
                    hedgeRatio: "{{ __('90% Protected') }}",
                    execTick: "{{ __('Hourly Check') }}",
                    stopLoss: "{{ __('1.5% Strict') }}"
                },
                balanced: {
                    riskLabel: "{{ __('Moderate Risk • 60%') }}",
                    riskWidth: "60%",
                    barColor: "bg-amber-400",
                    barShadow: "shadow-[0_0_12px_rgba(245,158,11,0.5)]",
                    labelColor: "text-amber-400",
                    engineMode: "{{ __('Grid & Trend') }}",
                    hedgeRatio: "{{ __('50% Protected') }}",
                    execTick: "{{ __('Every 15 Mins') }}",
                    stopLoss: "{{ __('4.5% Trailing') }}"
                },
                growth: {
                    riskLabel: "{{ __('High Risk • 92%') }}",
                    riskWidth: "92%",
                    barColor: "bg-rose-500",
                    barShadow: "shadow-[0_0_12px_rgba(244,63,94,0.5)]",
                    labelColor: "text-rose-400",
                    engineMode: "{{ __('Fast Scalping') }}",
                    hedgeRatio: "{{ __('Pure Profit Focus') }}",
                    execTick: "{{ __('Instant Trades') }}",
                    stopLoss: "{{ __('9.0% Wide') }}"
                }
            };

            function updateTelemetry(strategy) {
                const spec = strategySpecs[strategy] || strategySpecs.balanced;
                
                // Update Risk Label
                $('#telemetry-risk-label')
                    .text(spec.riskLabel)
                    .removeClass('text-emerald-400 text-amber-400 text-rose-400')
                    .addClass(spec.labelColor);

                // Update Risk Bar
                $('#telemetry-risk-bar')
                    .css('width', spec.riskWidth)
                    .removeClass('bg-emerald-400 bg-amber-400 bg-rose-500 shadow-[0_0_12px_rgba(52,211,153,0.5)] shadow-[0_0_12px_rgba(245,158,11,0.5)] shadow-[0_0_12px_rgba(244,63,94,0.5)]')
                    .addClass(spec.barColor + ' ' + spec.barShadow);

                // Update Parameters
                $('#telemetry-engine-mode').text(spec.engineMode);
                $('#telemetry-hedge-ratio').text(spec.hedgeRatio);
                $('#telemetry-exec-tick').text(spec.execTick);
                $('#telemetry-stop-loss').text(spec.stopLoss);

                // Update Card styling
                $('.strategy-card').each(function() {
                    const cardStrategy = $(this).data('strategy');
                    const indicator = $(this).find('.strategy-indicator');
                    const dot = indicator.find('span');

                    if (cardStrategy === strategy) {
                        if (strategy === 'conservative') {
                            $(this).addClass('border-emerald-500/60 bg-emerald-500/10 ring-1 ring-emerald-500/50')
                                   .removeClass('border-white/[0.08] bg-white/[0.02] border-amber-500/60 bg-amber-500/10 ring-amber-500/50 border-rose-500/60 bg-rose-500/10 ring-rose-500/50');
                            indicator.addClass('border-emerald-400 bg-emerald-400/20').removeClass('border-white/20 border-amber-400 bg-amber-400/20 border-rose-400 bg-rose-400/20');
                            dot.addClass('bg-emerald-400 opacity-100').removeClass('opacity-0 bg-amber-400 bg-rose-400');
                        } else if (strategy === 'balanced') {
                            $(this).addClass('border-amber-500/60 bg-amber-500/10 ring-1 ring-amber-500/50')
                                   .removeClass('border-white/[0.08] bg-white/[0.02] border-emerald-500/60 bg-emerald-500/10 ring-emerald-500/50 border-rose-500/60 bg-rose-500/10 ring-rose-500/50');
                            indicator.addClass('border-amber-400 bg-amber-400/20').removeClass('border-white/20 border-emerald-400 bg-emerald-400/20 border-rose-400 bg-rose-400/20');
                            dot.addClass('bg-amber-400 opacity-100').removeClass('opacity-0 bg-emerald-400 bg-rose-400');
                        } else {
                            $(this).addClass('border-rose-500/60 bg-rose-500/10 ring-1 ring-rose-500/50')
                                   .removeClass('border-white/[0.08] bg-white/[0.02] border-emerald-500/60 bg-emerald-500/10 ring-emerald-500/50 border-amber-500/60 bg-amber-500/10 ring-amber-500/50');
                            indicator.addClass('border-rose-400 bg-rose-400/20').removeClass('border-white/20 border-emerald-400 bg-emerald-400/20 border-amber-400 bg-amber-400/20');
                            dot.addClass('bg-rose-400 opacity-100').removeClass('opacity-0 bg-emerald-400 bg-amber-400');
                        }
                    } else {
                        $(this).removeClass('border-emerald-500/60 bg-emerald-500/10 ring-emerald-500/50 border-amber-500/60 bg-amber-500/10 ring-amber-500/50 border-rose-500/60 bg-rose-500/10 ring-rose-500/50')
                               .addClass('border-white/[0.08] bg-white/[0.02]');
                        indicator.removeClass('border-emerald-400 bg-emerald-400/20 border-amber-400 bg-amber-400/20 border-rose-400 bg-rose-400/20')
                                 .addClass('border-white/20');
                        dot.removeClass('opacity-100').addClass('opacity-0');
                    }
                });
            }

            // Radio change event
            $(document).on('change', 'input[name="risk_profile"]', function() {
                updateTelemetry($(this).val());
            });

            // Auto-open modal on page load (only rendered if user has not completed onboarding)
            setTimeout(function() {
                $('#onboarding-modal').removeClass('pointer-events-none opacity-0');
                $('#onboarding-content').removeClass('scale-95');
            }, 400);

            // Close Modal
            $('#onboarding-close').on('click', function() {
                $('#onboarding-modal').addClass('opacity-0 pointer-events-none');
                $('#onboarding-content').addClass('scale-95');
            });

            // Dismiss modal on ESC
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && !$('#onboarding-modal').hasClass('pointer-events-none')) {
                    $('#onboarding-modal').addClass('opacity-0 pointer-events-none');
                    $('#onboarding-content').addClass('scale-95');
                }
            });

            // Dismiss modal on backdrop click
            $('#onboarding-modal').on('click', function(e) {
                if (e.target === this) {
                    $('#onboarding-modal').addClass('opacity-0 pointer-events-none');
                    $('#onboarding-content').addClass('scale-95');
                }
            });

            // Submit Onboarding via AJAX
            $('#onboarding-form').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = $('#onboarding-submit-btn');
                const submitLabel = $('#onboarding-submit-label');
                const originalText = submitLabel.text();

                submitBtn.prop('disabled', true).addClass('opacity-60 cursor-not-allowed');
                submitLabel.text("{{ __('Saving...') }}");

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        if (res.status === 'success') {

                            // Hide modal smoothly
                            $('#onboarding-modal').addClass('opacity-0 pointer-events-none');
                            $('#onboarding-content').addClass('scale-95');

                            // Toast notification
                            if (typeof toastr !== 'undefined') {
                                toastr.success(res.message || "{{ __('Trading style saved.') }}");
                            }

                            // Synchronize with Bottom Drawer and Notice bar
                            $('#drawer-item-onboarding').slideUp(250, function() {
                                $(this).remove();
                            });

                            const countEl = $('#pending-items-count');
                            if (countEl.length) {
                                let currentCount = parseInt(countEl.text()) || 1;
                                currentCount = Math.max(0, currentCount - 1);
                                countEl.text(currentCount);

                                if (currentCount === 0) {
                                    $('#pending-action-notice').slideUp(300, function() {
                                        $(this).remove();
                                    });
                                }
                            }
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).removeClass('opacity-60 cursor-not-allowed');
                        submitLabel.text(originalText);

                        let errMsg = "{{ __('Could not save your choice. Please try again.') }}";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(errMsg);
                        }
                    }
                });
            });
        });
    </script>
@endpush
