@if (getSetting('preloader', 'enabled') === 'enabled')
    <div id="dashboard-preloader"
        class="fixed inset-0 z-[99999] bg-[#050507]/95 backdrop-blur-2xl flex flex-col items-center justify-center font-mono select-none overflow-hidden transition-all duration-500 ease-out">
        
        {{-- Ambient Glow Orbs --}}
        <div class="absolute w-96 h-96 bg-cyan-500/15 rounded-full blur-[140px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 4s;"></div>
        <div class="absolute w-80 h-80 bg-purple-600/10 rounded-full blur-[120px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 6s;"></div>

        {{-- Center Emblem Container --}}
        <div class="relative flex flex-col items-center justify-center z-10">
            
            {{-- Orbital SVG Spinner & Center Medallion --}}
            <div class="relative w-28 h-28 flex items-center justify-center mb-6">
                
                {{-- Background Track Ring --}}
                <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="44" stroke="currentColor" stroke-width="2" class="text-white/[0.06]" fill="none" />
                </svg>

                {{-- Animated Spinning Neon Ring --}}
                <svg class="absolute inset-0 w-full h-full -rotate-90 animate-spin" style="animation-duration: 2s; animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);" viewBox="0 0 100 100">
                    <defs>
                        <linearGradient id="preloader-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#22d3ee" stop-opacity="1" />
                            <stop offset="50%" stop-color="#06b6d4" stop-opacity="0.8" />
                            <stop offset="100%" stop-color="#3b82f6" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                    <circle cx="50" cy="50" r="44" stroke="url(#preloader-grad)" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="140 140" fill="none" />
                </svg>

                {{-- Center Glowing Medallion --}}
                <div class="relative w-20 h-20 rounded-2xl bg-[#090c14] border border-white/[0.12] p-3 flex items-center justify-center shadow-[0_0_35px_rgba(6,182,212,0.25)] ring-1 ring-cyan-400/20 backdrop-blur-xl group">
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-400/10 via-transparent to-purple-500/10 rounded-2xl"></div>
                    @php
                        $favicon = getSetting('favicon');
                        $logo = getSetting('logo_rectangle');
                    @endphp
                    @if ($favicon)
                        <img src="{{ asset('assets/images/' . $favicon) }}" alt="{{ getSetting('name') }}" class="w-10 h-10 object-contain relative z-10 drop-shadow-[0_0_12px_rgba(6,182,212,0.6)]">
                    @elseif ($logo)
                        <img src="{{ asset('assets/images/' . $logo) }}" alt="{{ getSetting('name') }}" class="w-10 h-10 object-contain relative z-10 drop-shadow-[0_0_12px_rgba(6,182,212,0.6)]">
                    @else
                        <span class="text-xl font-bold font-mono text-cyan-400 relative z-10 drop-shadow-[0_0_10px_rgba(6,182,212,0.8)]">
                            {{ substr(getSetting('name', 'F'), 0, 1) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Progress Counter & Telemetry --}}
            <div class="flex flex-col items-center text-center space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span id="preloader-percent" class="text-xs font-mono font-bold text-white tracking-wider">0%</span>
                </div>

                {{-- Precision Horizontal Micro Progress Bar --}}
                <div class="w-44 h-[2px] bg-white/[0.08] rounded-full overflow-hidden relative">
                    <div id="preloader-progress-bar" class="h-full bg-gradient-to-r from-cyan-400 via-cyan-500 to-blue-600 w-0 rounded-full transition-all duration-300 ease-out shadow-[0_0_10px_rgba(6,182,212,0.8)]"></div>
                </div>

                <div class="flex items-center gap-1.5 text-[10px] font-mono text-slate-400 pt-1 tracking-widest uppercase">
                    <span>{{ getSetting('name') }}</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-cyan-400/90">{{ __('Encrypted Session') }}</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const preloader = document.getElementById('dashboard-preloader');
            const percentText = document.getElementById('preloader-percent');
            const progressBar = document.getElementById('preloader-progress-bar');
            
            if (!preloader) return;

            let currentPercent = 0;
            let isLoaded = false;

            // Smooth incremental counter
            const interval = setInterval(() => {
                if (currentPercent < (isLoaded ? 100 : 88)) {
                    currentPercent += Math.floor(Math.random() * 12) + 6;
                    if (currentPercent > (isLoaded ? 100 : 88)) {
                        currentPercent = isLoaded ? 100 : 88;
                    }
                    if (percentText) percentText.textContent = currentPercent + '%';
                    if (progressBar) progressBar.style.width = currentPercent + '%';
                }

                if (currentPercent >= 100) {
                    clearInterval(interval);
                    dismissPreloader();
                }
            }, 60);

            function completeAndDismiss() {
                isLoaded = true;
                currentPercent = 100;
                if (percentText) percentText.textContent = '100%';
                if (progressBar) progressBar.style.width = '100%';
                setTimeout(dismissPreloader, 180);
            }

            function dismissPreloader() {
                preloader.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 500);
            }

            // Trigger complete on window load
            if (document.readyState === 'complete') {
                completeAndDismiss();
            } else {
                window.addEventListener('load', completeAndDismiss);
            }

            // Safety timeout (max 1.2s total so it never blocks the user)
            setTimeout(completeAndDismiss, 1200);
        })();
    </script>
@endif
