@extends('templates.' . config('site.template') . '.blades.layouts.front')

@section('title', $page_title . ' - ' . getSetting('name'))
@section('page_title', $page_title)

@section('content')
    <div class="relative bg-[#050507] py-16 sm:py-24 overflow-hidden isolate">
        
        {{-- Ambient Background Glows --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-[radial-gradient(circle_at_50%_0%,rgba(0,245,255,0.08),transparent_70%)] pointer-events-none -z-10"></div>
        <div class="fixed inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.03] pointer-events-none -z-20"></div>

        <div class="max-w-7xl mx-auto px-4 lg:px-8 relative z-10">
            
            {{-- Header Hero --}}
            <div class="max-w-4xl mx-auto text-center mb-16 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 font-mono text-[10px] font-bold uppercase tracking-widest mb-6 shadow-[0_0_20px_rgba(0,245,255,0.15)]">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('24/7 CUSTOMER SUPPORT') }}
                </div>
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight leading-[1.08] mb-6">
                    {{ __('Get in Touch with') }} <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#00f5ff] via-white to-emerald-400">
                        {{ __('Our Support Team.') }}
                    </span>
                </h1>
                <p class="text-base sm:text-xl text-slate-400 font-body leading-relaxed max-w-2xl mx-auto">
                    {{ $page_description }}
                </p>
            </div>

            {{-- Contact Split Console --}}
            <div class="max-w-6xl mx-auto rounded-3xl bg-[#090c14]/95 border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.9)] overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                    
                    {{-- Form Side (7 Cols) --}}
                    <div class="lg:col-span-7 p-8 sm:p-12 lg:p-14">
                        <div class="flex items-center justify-between pb-4 border-b border-white/[0.08] mb-8">
                            <span class="text-xs font-mono font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                                {{ __('SEND US A MESSAGE') }}
                            </span>
                            <span class="text-[10px] font-mono text-emerald-400 font-bold">24/7 ACTIVE</span>
                        </div>

                        <form action="{{ route('contact.send') }}" method="POST" class="ajax-form space-y-6" data-action="reset">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-300">{{ __('Full Name') }}</label>
                                    <input type="text" name="name" placeholder="John Doe" required
                                        class="w-full bg-black/60 border border-white/10 focus:border-[#00f5ff] rounded-xl px-4 py-3.5 text-sm text-white font-mono placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-[#00f5ff] transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-300">{{ __('Email Address') }}</label>
                                    <input type="email" name="email" placeholder="john@example.com" required
                                        class="w-full bg-black/60 border border-white/10 focus:border-[#00f5ff] rounded-xl px-4 py-3.5 text-sm text-white font-mono placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-[#00f5ff] transition-all">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-300">{{ __('Subject') }}</label>
                                <input type="text" name="subject" placeholder="{{ __('How can we help you?') }}" required
                                    class="w-full bg-black/60 border border-white/10 focus:border-[#00f5ff] rounded-xl px-4 py-3.5 text-sm text-white font-mono placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-[#00f5ff] transition-all">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-300">{{ __('Message') }}</label>
                                <textarea name="message" rows="5" placeholder="{{ __('Describe your question or issue in detail...') }}" required
                                    class="w-full bg-black/60 border border-white/10 focus:border-[#00f5ff] rounded-xl px-4 py-3.5 text-sm text-white font-mono placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-[#00f5ff] transition-all resize-none"></textarea>
                            </div>

                            @if (getSetting('google_recaptcha') == 'enabled')
                                <div class="pt-2">
                                    {!! NoCaptcha::display(['data-theme' => 'dark']) !!}
                                    @error('g-recaptcha-response')
                                        <span class="text-red-400 font-mono text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <button type="submit"
                                class="w-full py-4 px-6 bg-[#00f5ff] hover:bg-cyan-300 text-[#050507] font-black uppercase tracking-widest text-xs rounded-xl transition-all shadow-[0_0_25px_rgba(0,245,255,0.4)] hover:shadow-[0_0_40px_rgba(0,245,255,0.6)] cursor-pointer flex items-center justify-center gap-2">
                                <span>{{ __('Send Message') }}</span>
                                <svg class="w-4 h-4 text-[#050507]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </form>
                    </div>

                    {{-- Info Side (5 Cols) --}}
                    <div class="lg:col-span-5 bg-black/60 p-8 sm:p-12 border-t lg:border-t-0 lg:border-l border-white/[0.08] flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-48 h-48 bg-cyan-500/10 blur-3xl rounded-full pointer-events-none"></div>

                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-6">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="text-[10px] font-mono font-bold text-emerald-400 uppercase tracking-widest">{{ __('Online 24/7') }}</span>
                            </div>

                            <h3 class="text-2xl font-black text-white mb-6 leading-tight">
                                {{ __('We Are Here to Help') }}
                            </h3>

                            <div class="space-y-6">
                                @php
                                    $raw_offices = getSetting('offices');
                                    $offices = is_string($raw_offices) ? json_decode($raw_offices, true) : $raw_offices;
                                    if (!is_array($offices)) {
                                        $offices = [];
                                    }
                                @endphp

                                @foreach ($offices as $office)
                                    <div class="flex items-start gap-4 group">
                                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 shrink-0 shadow-inner">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <div>
                                            <h4 class="text-white font-bold text-sm group-hover:text-cyan-300 transition-colors">{{ $office['name'] ?? 'Global Headquarters' }}</h4>
                                            @if (!empty($office['address']))
                                                <p class="text-slate-400 text-xs leading-relaxed font-body mt-0.5">{{ $office['address'] }}</p>
                                            @endif
                                            @if (!empty($office['email']))
                                                <a href="mailto:{{ $office['email'] }}" class="text-xs font-mono text-cyan-400 hover:underline block mt-1">{{ $office['email'] }}</a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @if (empty($offices))
                                    <div class="p-4 rounded-xl border border-white/5 bg-white/[0.02] text-xs font-mono text-slate-400">
                                        {{ __('Support Email:') }} <strong class="text-white">support@foyana.com</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Advisory Quote Box --}}
                        <div class="mt-12 p-6 rounded-2xl bg-white/[0.02] border border-white/[0.08] relative group">
                            <p class="text-xs text-slate-300 font-body italic leading-relaxed">
                                {{ __('":site_name is dedicated to providing fast, friendly, and reliable support whenever you need assistance."', ['site_name' => getSetting('name')]) }}
                            </p>
                            <div class="mt-3 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-cyan-400"></div>
                                <span class="text-[10px] font-mono font-bold text-cyan-400 uppercase tracking-widest">{{ __('Customer Support Team') }}</span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (getSetting('google_recaptcha') == 'enabled')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush
