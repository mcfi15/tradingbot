@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12 font-mono">

        {{-- Top Header --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-[0.2em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('System Update') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Installing Update') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-wider uppercase">
                    {{ __('Keep this browser window open until the update finishes.') }}
                </p>
            </div>

            {{-- Live Status Badge --}}
            <div class="flex items-center gap-2.5 text-xs shrink-0">
                <div id="status-badge" class="px-3.5 py-2 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-[0_0_15px_rgba(0,245,255,0.15)]">
                    <div class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></div>
                    <span id="status-text">{{ __('In Progress') }}</span>
                </div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="p-5 rounded-2xl bg-[#090c14] border border-white/[0.08] shadow-2xl space-y-3">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 text-xs">
                <div class="flex items-center gap-2">
                    <span class="text-[9px] uppercase font-bold text-slate-500 tracking-wider">{{ __('Status:') }}</span>
                    <span id="progress-text" class="font-bold text-cyan-400 text-xs">
                        {{ __('Preparing update...') }}
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <span id="progress-elapsed" class="text-[10px] text-slate-500 font-mono">00:00</span>
                    <span id="progress-percent" class="font-black text-white text-sm font-mono">0%</span>
                </div>
            </div>

            <div class="h-2 w-full bg-white/[0.04] rounded-full overflow-hidden border border-white/[0.08] p-0.5">
                <div id="progress-bar" style="width: 0%" class="h-full bg-gradient-to-r from-cyan-500 to-cyan-300 rounded-full transition-all duration-500 shadow-[0_0_15px_rgba(0,245,255,0.6)]"></div>
            </div>
        </div>

        {{-- Step Cards (Current: Spinner, Pending: Grayed Out) --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3" id="step-cards">
            @php
                $stages_meta = [
                    ['id' => 'init-cleanup', 'name' => __('Clean Cache'),      'desc' => __('Clears temporary files')],
                    ['id' => 'download',     'name' => __('Download Update'),  'desc' => __('Downloads update package')],
                    ['id' => 'extract',      'name' => __('Extract Files'),    'desc' => __('Unpacks update files')],
                    ['id' => 'sanitize',     'name' => __('Protect Settings'), 'desc' => __('Keeps your .env and uploads safe')],
                    ['id' => 'replace',      'name' => __('Replace Files'),    'desc' => __('Updates application files')],
                    ['id' => 'cleanup',      'name' => __('Update Database'),  'desc' => __('Runs database updates & clears cache')]
                ];
            @endphp

            @foreach ($stages_meta as $idx => $stg)
                <div class="stage-card p-3.5 rounded-xl border transition-all flex flex-col justify-between h-full space-y-2 opacity-35 bg-white/[0.01] border-white/[0.04]" id="stage-card-{{ $stg['id'] }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-bold font-mono text-slate-500">{{ $idx + 1 }}</span>
                        <span class="stage-badge px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider bg-white/[0.04] text-slate-500 border border-white/[0.06]">
                            {{ __('Waiting') }}
                        </span>
                    </div>
                    <div>
                        <h4 class="stage-title text-xs font-bold text-slate-400 tracking-tight leading-snug">{{ $stg['name'] }}</h4>
                        <p class="text-[9px] text-slate-500 mt-0.5 line-clamp-1 leading-tight">{{ $stg['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Current Step Message Panel (Replaces old terminal console) --}}
        <div class="p-6 rounded-2xl bg-[#090c14] border border-white/[0.08] shadow-2xl space-y-4" id="current-step-panel">
            <div class="flex items-center justify-between pb-3 border-b border-white/[0.06] text-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('Current Step') }}</span>
                <span id="active-step-counter" class="text-xs font-bold text-cyan-400 font-mono">{{ __('Step 1 of 6') }}</span>
            </div>

            <div class="flex items-start gap-4">
                <div id="step-icon-container" class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center shrink-0 mt-0.5 shadow-[0_0_15px_rgba(0,245,255,0.15)]">
                    <div class="w-4 h-4 border-2 border-cyan-400/30 border-t-cyan-400 rounded-full animate-spin"></div>
                </div>
                <div class="space-y-1">
                    <h3 id="active-step-title" class="text-sm font-bold text-white tracking-wide">
                        {{ __('Preparing server...') }}
                    </h3>
                    <p id="active-step-message" class="text-xs text-slate-400 leading-relaxed">
                        {{ __('Starting update process and verifying server environment.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Retry Box (Shown only if a step fails) --}}
        <div id="retry-container" class="hidden p-6 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-rose-400 uppercase tracking-wide">{{ __('Update Paused') }}</h3>
                    <p id="error-description" class="text-slate-300 mt-1 leading-relaxed">{{ __('The current step could not complete.') }}</p>
                </div>
            </div>
            <button type="button" id="btn-retry" class="px-5 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-[10px] tracking-wider transition-all shrink-0 cursor-pointer shadow-[0_0_15px_rgba(0,245,255,0.3)]">
                {{ __('Retry Step') }}
            </button>
        </div>

        {{-- Completion Box --}}
        <div id="completion-container" class="hidden p-6 sm:p-8 rounded-2xl bg-gradient-to-r from-emerald-950/30 via-[#090c14] to-[#090c14] border border-emerald-500/30 shadow-[0_0_30px_rgba(16,185,129,0.1)] flex flex-col md:flex-row md:items-center justify-between gap-6 text-xs">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center font-bold shrink-0 shadow-[0_0_20px_rgba(16,185,129,0.3)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-black text-white uppercase tracking-wide">{{ __('Update Complete') }}</h3>
                        <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-bold uppercase tracking-wider">{{ __('Success') }}</span>
                    </div>
                    <p class="text-slate-400 mt-1 max-w-xl text-[11px] leading-relaxed">
                        {{ __('All update files and database changes were installed successfully. Your system is ready.') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.update.index') }}" class="px-4 py-2.5 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-black font-black uppercase text-[10px] tracking-wider transition-all shadow-[0_0_15px_rgba(16,185,129,0.3)] cursor-pointer">
                    {{ __('Back to System Update') }}
                </a>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const stages = [
            { 
                id: 'init-cleanup', 
                name: "{{ __('Clean Cache') }}", 
                runningMsg: "{{ __('Clearing temporary update files and preparing your server...') }}",
                doneMsg: "{{ __('Temporary files cleared.') }}",
                progress: 15 
            },
            { 
                id: 'download',     
                name: "{{ __('Download Update') }}", 
                runningMsg: "{{ __('Downloading update files from the server...') }}",
                doneMsg: "{{ __('Update files downloaded.') }}",
                progress: 35 
            },
            { 
                id: 'extract',      
                name: "{{ __('Extract Files') }}", 
                runningMsg: "{{ __('Extracting files into the staging folder...') }}",
                doneMsg: "{{ __('Files extracted.') }}",
                progress: 55 
            },
            { 
                id: 'sanitize',     
                name: "{{ __('Protect Settings') }}", 
                runningMsg: "{{ __('Preserving your settings, database credentials, and uploads...') }}",
                doneMsg: "{{ __('Settings protected.') }}",
                progress: 70 
            },
            { 
                id: 'replace',      
                name: "{{ __('Replace Files') }}", 
                runningMsg: "{{ __('Replacing application files on your server...') }}",
                doneMsg: "{{ __('Application files updated.') }}",
                progress: 85 
            },
            { 
                id: 'cleanup',      
                name: "{{ __('Update Database') }}", 
                runningMsg: "{{ __('Running database updates and rebuilding cache...') }}",
                doneMsg: "{{ __('Database updated and cache rebuilt.') }}",
                progress: 100 
            }
        ];

        let currentStageIndex = 0;
        let startTime = Date.now();
        let timerInterval = null;

        $(document).ready(function() {
            startTimer();
            setTimeout(processNextStage, 1000);

            $('#btn-retry').on('click', function() {
                $('#retry-container').addClass('hidden');
                $('#status-badge').removeClass('bg-rose-500/10 text-rose-400 border-rose-500/30')
                    .addClass('bg-cyan-500/10 text-cyan-400 border-cyan-500/30');
                $('#status-text').text("{{ __('In Progress') }}");
                processNextStage();
            });
        });

        function startTimer() {
            timerInterval = setInterval(() => {
                const elapsedSec = Math.floor((Date.now() - startTime) / 1000);
                const mins = String(Math.floor(elapsedSec / 60)).padStart(2, '0');
                const secs = String(elapsedSec % 60).padStart(2, '0');
                $('#progress-elapsed').text(`${mins}:${secs}`);
            }, 1000);
        }

        function updateProgressBar(percent, text) {
            $('#progress-bar').css('width', percent + '%');
            $('#progress-percent').text(percent + '%');
            if (text) $('#progress-text').text(text);
        }

        function updateStageCardState(stageId, state) {
            const card = $(`#stage-card-${stageId}`);
            const badge = card.find('.stage-badge');
            const title = card.find('.stage-title');

            if (state === 'running') {
                card.removeClass('opacity-35 bg-white/[0.01] border-white/[0.04]')
                    .addClass('opacity-100 bg-cyan-950/20 border-cyan-400 shadow-[0_0_15px_rgba(0,245,255,0.15)]');
                badge.removeClass('bg-white/[0.04] text-slate-500 border-white/[0.06]')
                    .addClass('bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 flex items-center gap-1.5')
                    .html('<span class="w-2 h-2 border-2 border-cyan-400/40 border-t-cyan-400 rounded-full animate-spin inline-block"></span> {{ __('Running') }}');
                title.removeClass('text-slate-400').addClass('text-white');
            } else if (state === 'completed') {
                card.removeClass('border-cyan-400 bg-cyan-950/20 shadow-[0_0_15px_rgba(0,245,255,0.15)]')
                    .addClass('opacity-100 bg-[#090c14] border-emerald-500/30');
                badge.removeClass('bg-cyan-500/20 text-cyan-300 border-cyan-500/30 flex items-center gap-1.5')
                    .addClass('bg-emerald-500/10 text-emerald-400 border border-emerald-500/20')
                    .html('✓ {{ __('Done') }}');
                title.removeClass('text-slate-400').addClass('text-white');
            } else if (state === 'failed') {
                card.removeClass('border-cyan-400 bg-cyan-950/20')
                    .addClass('opacity-100 bg-rose-950/20 border-rose-500/40');
                badge.removeClass('bg-cyan-500/20 text-cyan-300 border-cyan-500/30 flex items-center gap-1.5')
                    .addClass('bg-rose-500/20 text-rose-400 border border-rose-500/40')
                    .html('✗ {{ __('Failed') }}');
            }
        }

        function processNextStage() {
            if (currentStageIndex >= stages.length) {
                handleSuccess();
                return;
            }

            const stage = stages[currentStageIndex];
            updateProgressBar(stage.progress, stage.name);
            updateStageCardState(stage.id, 'running');

            // Update step message box
            $('#active-step-counter').text(`Step ${currentStageIndex + 1} of ${stages.length}`);
            $('#active-step-title').text(stage.name);
            $('#active-step-message').text(stage.runningMsg);
            $('#step-icon-container').html('<div class="w-4 h-4 border-2 border-cyan-400/30 border-t-cyan-400 rounded-full animate-spin"></div>')
                .removeClass('bg-emerald-500/10 border-emerald-500/30 text-emerald-400')
                .addClass('bg-cyan-500/10 border-cyan-500/30 text-cyan-400');

            $.ajax({
                url: "{{ route('admin.update.process.index') }}/" + stage.id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    uid: "{{ $uid }}"
                },
                success: function(res) {
                    updateStageCardState(stage.id, 'completed');
                    $('#active-step-message').text(res.message || stage.doneMsg);

                    currentStageIndex++;
                    setTimeout(processNextStage, 700);
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || "{{ __('An error occurred during this step.') }}";
                    updateStageCardState(stage.id, 'failed');
                    $('#error-description').text(errorMsg);
                    $('#retry-container').removeClass('hidden');
                    $('#status-badge').removeClass('bg-cyan-500/10 text-cyan-400 border-cyan-500/30')
                        .addClass('bg-rose-500/10 text-rose-400 border-rose-500/30');
                    $('#status-text').text("{{ __('Failed') }}");
                }
            });
        }

        function handleSuccess() {
            if (timerInterval) clearInterval(timerInterval);
            updateProgressBar(100, "{{ __('Update completed successfully') }}");
            $('#status-badge').removeClass('bg-cyan-500/10 text-cyan-400 border-cyan-500/30')
                .addClass('bg-emerald-500/10 text-emerald-400 border-emerald-500/30')
                .html(`<div class="w-2 h-2 rounded-full bg-emerald-400"></div> {{ __('Completed') }}`);
            $('#current-step-panel').addClass('hidden');
            $('#completion-container').removeClass('hidden');
        }
    </script>
@endpush
