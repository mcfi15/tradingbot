@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-12 pb-24">

    {{-- Global Ethereal Ambient Mesh Gradients --}}
    <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-emerald-500/5 via-cyan-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-12">

        {{-- ══ MACRO HERO DECK & FLOATING ISLAND NAV ════════════════════════════ --}}
        <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                 style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">
                
                {{-- Radial Ambient Glow Node --}}
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div class="max-w-2xl space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary shadow-[0_0_15px_rgba(226,177,60,0.2)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                            <span>{{ __('Automated Trading') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                            {{ __('Trading Bots') }}
                        </h1>

                        <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed max-w-xl">
                            {{ __('Automated trading bots for yield generation. Choose and activate bots tailored to your risk level and budget.') }}
                        </p>
                    </div>

                    {{-- Floating Island Navigation Sub-Pills --}}
                    <div class="flex flex-wrap items-center gap-2.5 p-2 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-xl shrink-0">
                        <a href="{{ route('user.trading-bots.index') }}"
                           class="px-5 py-2.5 rounded-full bg-accent-primary text-black font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(226,177,60,0.3)] transition-all flex items-center gap-2">
                            <span>{{ __('Marketplace') }}</span>
                        </a>

                        <a href="{{ route('user.trading-bots.activations') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                            <span>{{ __('Active Bots') }}</span>
                            <span class="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 text-[10px] font-black">{{ $total_activations }}</span>
                        </a>

                        <a href="{{ route('user.trading-bots.daily-summary') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                            <span>{{ __('Daily Summary') }}</span>
                        </a>

                        <a href="{{ route('user.trading-bots.logs') }}"
                           class="px-5 py-2.5 rounded-full hover:bg-white/10 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                            <span>{{ __('Logs') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ FEATURED SPOTLIGHT + ASYMMETRICAL GRID ═════════════════════════ --}}
        @if($bots->isEmpty())
            <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 max-w-lg mx-auto text-center p-12">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">{{ __('No Bots Active') }}</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                    {{ __('Trading bots are undergoing automated updates. Check back shortly.') }}
                </p>
            </div>
        @else
            @php
                $featuredBot = $bots->first();
                $remainingBots = $bots->slice(1);
            @endphp

            {{-- Featured Hero Strategy Node (Col-Span-12 Spotlight) --}}
            @if ($featuredBot)
                <div class="group relative rounded-[2.5rem] p-1.5 bg-gradient-to-b from-white/15 via-white/5 to-white/0 border border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.9)] transition-all duration-500 hover:border-accent-primary/50">
                    <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative flex flex-col lg:flex-row lg:items-center justify-between gap-10"
                         style="background: radial-gradient(circle at 80% 20%, rgba(226,177,60,0.08) 0%, rgba(6,8,12,0.98) 60%);">
                        
                        <div class="space-y-6 max-w-xl">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full bg-accent-primary/20 border border-accent-primary/30 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary shadow-[0_0_15px_rgba(226,177,60,0.2)]">
                                    {{ __('Featured Trading Bot') }}
                                </span>
                                <span class="text-xs font-mono font-bold text-slate-400 uppercase">
                                    {{ strtoupper($featuredBot->type) }}
                                </span>
                            </div>

                            <div class="flex items-center gap-5">
                                <div class="relative shrink-0">
                                    <div class="absolute -inset-2 bg-gradient-to-tr from-accent-primary to-purple-600 rounded-3xl blur-md opacity-40 group-hover:opacity-80 transition-opacity"></div>
                                    <img src="{{ asset('assets/images/bots/' . $featuredBot->logo) }}"
                                         alt="{{ $featuredBot->name }}"
                                         class="relative w-20 h-20 rounded-3xl object-cover border border-white/20 shadow-2xl">
                                </div>

                                <div>
                                    <h2 class="text-2xl sm:text-4xl font-black text-white group-hover:text-accent-primary transition-colors tracking-tight">
                                         {{ $featuredBot->name }}
                                     </h2>
                                     <p class="text-xs text-slate-400 mt-1 font-medium">
                                         {{ $featuredBot->duration }} {{ __($featuredBot->duration_type) }}{{ $featuredBot->duration > 1 ? 's' : '' }} {{ __('Duration') }}
                                     </p>
                                 </div>
                             </div>

                             {{-- Quantitative Metrics Row --}}
                             <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-2">
                                 <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/8 space-y-1">
                                     <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500">{{ __('Estimated Daily Return') }}</span>
                                     <div class="text-xl font-black text-emerald-400 font-mono">+{{ $featuredBot->daily_return_min }}% - {{ $featuredBot->daily_return_max }}%</div>
                                 </div>

                                 <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/8 space-y-1">
                                     <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500">{{ __('Min Investment') }}</span>
                                     <div class="text-xl font-black text-white font-mono">{{ showAmount($featuredBot->min_amount) }}</div>
                                 </div>

                                 <div class="col-span-2 sm:col-span-1 p-4 rounded-2xl bg-white/[0.03] border border-white/8 space-y-1">
                                     <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500">{{ __('Max Investment') }}</span>
                                     <div class="text-xl font-black text-accent-primary font-mono">{{ showAmount($featuredBot->max_amount) }}</div>
                                 </div>
                             </div>
                         </div>

                         {{-- Action Card & Button-in-Button CTA --}}
                         <div class="flex flex-col justify-between items-start lg:items-end gap-6 border-t lg:border-t-0 lg:border-l border-white/10 pt-6 lg:pt-0 lg:pl-10 shrink-0">
                             <div class="space-y-2 text-left lg:text-right">
                                 <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500 block">{{ __('Trading Days') }}</span>
                                 <div class="flex flex-wrap lg:justify-end gap-1.5">
                                     @php
                                         $allDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                         $botDays = array_map(fn($day) => substr($day, 0, 3), $featuredBot->trading_days ?? []);
                                     @endphp
                                     @foreach ($allDays as $day)
                                         <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ in_array($day, $botDays) ? 'bg-accent-primary/20 text-accent-primary border border-accent-primary/30' : 'bg-white/[0.02] text-slate-600 border border-white/5' }}">
                                             {{ $day }}
                                         </span>
                                     @endforeach
                                 </div>
                             </div>

                             <button type="button"
                                     onclick="openActivationModal({{ $featuredBot->id }}, '{{ addslashes($featuredBot->name) }}', {{ $featuredBot->min_amount }}, {{ $featuredBot->max_amount }}, '{{ $featuredBot->daily_return_min }}% - {{ $featuredBot->daily_return_max }}%', '{{ $featuredBot->duration }} {{ __($featuredBot->duration_type) }}', '{{ implode(', ', array_slice($featuredBot->traded_pairs ?? [], 0, 5)) }}')"
                                     class="group/btn relative inline-flex items-center gap-4 px-8 py-4 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_0_30px_rgba(226,177,60,0.35)] active:scale-[0.97] cursor-pointer">
                                 <span>{{ __('Start Bot') }}</span>
                                 <div class="w-8 h-8 rounded-full bg-black/10 flex items-center justify-center group-hover/btn:translate-x-1 group-hover/btn:-translate-y-[1px] transition-transform">
                                     <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                 </div>
                             </button>
                         </div>

                     </div>
                 </div>
             @endif

             {{-- Remaining Strategy Nodes (Grid Layout) --}}
             @if ($remainingBots->isNotEmpty())
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                     @foreach ($remainingBots as $bot)
                         <div class="group relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 transition-all duration-300 hover:border-white/20 hover:shadow-[0_20px_40px_rgba(0,0,0,0.8)] flex flex-col justify-between">
                             <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-6"
                                  style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                                 
                                 <div class="flex items-start justify-between gap-4">
                                     <div class="flex items-center gap-4 min-w-0">
                                         <div class="relative shrink-0">
                                             <div class="absolute -inset-1 bg-gradient-to-tr from-accent-primary to-purple-500 rounded-2xl blur opacity-20 group-hover:opacity-60 transition-opacity"></div>
                                             <img src="{{ asset('assets/images/bots/' . $bot->logo) }}"
                                                  alt="{{ $bot->name }}"
                                                  class="relative w-14 h-14 rounded-2xl object-cover border border-white/10 shadow-lg">
                                         </div>
                                         <div class="min-w-0">
                                             <h3 class="text-lg font-black text-white group-hover:text-accent-primary transition-colors tracking-tight truncate">
                                                 {{ $bot->name }}
                                             </h3>
                                             <div class="flex items-center gap-2 mt-1">
                                                 <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">
                                                     {{ strtoupper($bot->type) }}
                                                 </span>
                                                 <span class="text-[9px] font-bold text-slate-500 font-mono">• {{ $bot->duration }} {{ __($bot->duration_type) }}{{ $bot->duration > 1 ? 's' : '' }}</span>
                                             </div>
                                         </div>
                                     </div>

                                     <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-xs font-black text-emerald-400 shrink-0">
                                         +{{ $bot->daily_return_min }}% - {{ $bot->daily_return_max }}%
                                     </span>
                                 </div>

                                 <div class="grid grid-cols-2 gap-3 p-4 rounded-2xl bg-black/40 border border-white/5">
                                     <div>
                                         <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block mb-0.5">{{ __('Min Investment') }}</span>
                                         <span class="text-sm font-black text-white font-mono">{{ showAmount($bot->min_amount) }}</span>
                                     </div>
                                     <div>
                                         <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block mb-0.5">{{ __('Max Investment') }}</span>
                                         <span class="text-sm font-black text-accent-primary font-mono">{{ showAmount($bot->max_amount) }}</span>
                                     </div>
                                 </div>

                                 <div class="flex items-center justify-between gap-4 pt-2">
                                     <div class="flex items-center gap-1">
                                         @php
                                             $allDays = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
                                             $botDays = array_map(fn($day) => substr($day, 0, 3), $bot->trading_days ?? []);
                                             $dayKeys = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                         @endphp
                                         @foreach ($dayKeys as $idx => $dayKey)
                                             <span class="w-6 h-6 rounded-md flex items-center justify-center text-[9px] font-bold {{ in_array($dayKey, $botDays) ? 'bg-accent-primary/20 text-accent-primary border border-accent-primary/30' : 'bg-white/[0.02] text-slate-700' }}">
                                                 {{ $allDays[$idx] }}
                                             </span>
                                         @endforeach
                                     </div>

                                     <button type="button"
                                             onclick="openActivationModal({{ $bot->id }}, '{{ addslashes($bot->name) }}', {{ $bot->min_amount }}, {{ $bot->max_amount }}, '{{ $bot->daily_return_min }}% - {{ $bot->daily_return_max }}%', '{{ $bot->duration }} {{ __($bot->duration_type) }}', '{{ implode(', ', array_slice($bot->traded_pairs ?? [], 0, 5)) }}')"
                                             class="px-5 py-2.5 rounded-full bg-white/5 hover:bg-accent-primary text-white hover:text-black font-black text-xs uppercase tracking-wider transition-all border border-white/10 hover:border-accent-primary cursor-pointer active:scale-95">
                                         {{ __('Start') }}
                                     </button>
                                 </div>

                             </div>
                         </div>
                     @endforeach
                 </div>
             @endif

             {{-- Pagination --}}
             <div class="mt-8">
                 {{ $bots->links() }}
             </div>
         @endif

     </div>
 </div>

 {{-- ══ DEPLOYMENT MODAL ═════════════════════════════════════════════════════ --}}
 <div id="activationModal"
      class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md p-4 animate-in fade-in duration-200">
     <div class="relative w-full max-w-md rounded-[2.5rem] p-1.5 bg-white/10 border border-white/15 shadow-2xl overflow-hidden">
         <div class="rounded-[calc(2.5rem-0.375rem)] p-7 space-y-6"
              style="background: linear-gradient(145deg, rgba(12,14,20,0.98) 0%, rgba(6,7,12,0.99) 100%);">
             
             {{-- Modal Header --}}
             <div class="flex items-center justify-between pb-4 border-b border-white/5">
                 <div>
                     <span class="text-[9px] font-bold uppercase tracking-widest text-accent-primary">{{ __('Start Trading Bot') }}</span>
                     <h3 class="text-xl font-black text-white tracking-tight" id="modalBotName"></h3>
                 </div>
                 <button type="button" onclick="closeActivationModal()" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white flex items-center justify-center transition-all">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                 </button>
             </div>

             {{-- Summary Badges --}}
             <div class="grid grid-cols-2 gap-3">
                 <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5">
                     <span class="block text-[9px] text-slate-500 font-bold uppercase tracking-widest mb-1">{{ __('Estimated Daily Return') }}</span>
                     <span id="modalReturns" class="text-sm font-black text-emerald-400">--</span>
                 </div>
                 <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5">
                     <span class="block text-[9px] text-slate-500 font-bold uppercase tracking-widest mb-1">{{ __('Duration') }}</span>
                     <span id="modalDuration" class="text-sm font-black text-white">--</span>
                 </div>
                 <div class="col-span-2 p-3.5 rounded-2xl bg-white/[0.02] border border-white/5">
                     <span class="block text-[9px] text-slate-500 font-bold uppercase tracking-widest mb-1">{{ __('Trading Pairs') }}</span>
                     <span id="modalPairs" class="text-xs font-mono font-medium text-cyan-400 block truncate">--</span>
                 </div>
             </div>

             {{-- Allocation Form --}}
             <form id="activationForm" class="space-y-5">
                 @csrf
                 <input type="hidden" name="bot_id" id="modalBotId">

                 <div class="space-y-2">
                     <div class="flex items-center justify-between">
                         <label class="text-[10px] text-slate-400 uppercase font-black tracking-widest">{{ __('Investment Amount') }}</label>
                         <div class="flex items-center gap-1.5 bg-accent-primary/10 border border-accent-primary/20 px-3 py-1 rounded-full">
                             <span class="text-[9px] text-accent-primary font-bold uppercase tracking-tighter">{{ __('Available Balance:') }}</span>
                             <span class="text-xs text-white font-black">{{ showAmount(auth()->user()->balance) }}</span>
                         </div>
                     </div>

                     <div class="relative">
                         <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                             <span class="text-accent-primary font-black text-base">{{ getSetting('currency_symbol', '$') }}</span>
                         </div>
                         <input type="number" name="amount" id="modalAmount" step="any" required
                                class="w-full bg-black/50 border border-white/10 rounded-2xl py-3.5 pl-10 pr-4 text-white text-lg font-bold focus:border-accent-primary outline-none transition-all placeholder:text-slate-600"
                                placeholder="0.00">
                     </div>

                     <div class="flex justify-between text-[10px] text-slate-500 font-bold uppercase pt-1">
                         <span>{{ __('Min:') }} <strong id="modalMinAmount" class="text-slate-300"></strong></span>
                         <span>{{ __('Max:') }} <strong id="modalMaxAmount" class="text-slate-300"></strong></span>
                     </div>
                 </div>

                 <div class="p-4 bg-accent-primary/5 rounded-2xl border border-accent-primary/10">
                     <p class="text-[11px] text-slate-300 leading-relaxed font-medium">
                         {{ __('Your funds will be allocated to this bot for the chosen duration. Daily profits are automatically added to your account balance.') }}
                     </p>
                 </div>

                 <button type="submit" id="activationSubmit"
                         class="w-full py-4 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_4px_20px_rgba(226,177,60,0.3)] active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer">
                     <span id="submitSpan">{{ __('Start Bot') }}</span>
                     <div id="submitSpinner" class="hidden w-4 h-4 border-2 border-black/30 border-t-black rounded-full animate-spin"></div>
                 </button>
             </form>

         </div>
     </div>
 </div>
@endsection

@push('scripts')
<script>
function openActivationModal(id, name, min, max, returns, duration, pairs) {
    document.getElementById('modalBotId').value = id;
    document.getElementById('modalBotName').innerText = name;
    document.getElementById('modalMinAmount').innerText = min.toLocaleString();
    document.getElementById('modalMaxAmount').innerText = max.toLocaleString();
    document.getElementById('modalAmount').min = min;
    document.getElementById('modalAmount').max = max;
    document.getElementById('modalAmount').value = min;

    document.getElementById('modalReturns').innerText = returns;
    document.getElementById('modalDuration').innerText = duration;
    document.getElementById('modalPairs').innerText = pairs;

    const modal = document.getElementById('activationModal');
    modal.classList.remove('hidden');
}

function closeActivationModal() {
    const modal = document.getElementById('activationModal');
    modal.classList.add('hidden');
}

document.getElementById('activationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('activationSubmit');
    const span = document.getElementById('submitSpan');
    const spinner = document.getElementById('submitSpinner');

    btn.disabled = true;
    span.innerText = "{{ __('Deploying Bot...') }}";
    spinner.classList.remove('hidden');

    const formData = new FormData(this);

    fetch("{{ route('user.trading-bots.activate') }}", {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '{{ __('Bot Deployed!') }}',
                text: data.message,
                background: '#08090e',
                color: '#fff'
            }).then(() => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '{{ __('Notice') }}',
                text: data.message,
                background: '#08090e',
                color: '#fff'
            });
            btn.disabled = false;
            span.innerText = "{{ __('Confirm Allocation') }}";
            spinner.classList.add('hidden');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: '{{ __('Error') }}',
            text: '{{ __('Something went wrong. Please try again.') }}',
            background: '#08090e',
            color: '#fff'
        });
        btn.disabled = false;
        span.innerText = "{{ __('Confirm Allocation') }}";
        spinner.classList.add('hidden');
    });
});
</script>
@endpush
