@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div id="kyc-view-content" class="space-y-8 mb-12">

        {{-- Disabled Warning --}}
        @if (!moduleEnabled('kyc_module'))
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-8 text-center font-mono">
                    <h3 class="text-lg font-bold text-white uppercase">{{ __('KYC Verification is Disabled') }}</h3>
                    <p class="text-xs text-slate-400 mt-2 mb-4">{{ __('Turn on the KYC module in settings to review documents.') }}</p>
                    <a href="{{ route('admin.settings.modules.index') }}" class="px-6 py-2 rounded-full bg-amber-400 text-black font-black text-xs uppercase">{{ __('Settings') }}</a>
                </div>
            </div>
        @else

            {{-- ==================================================================================== --}}
            {{-- TOP HEADER --}}
            {{-- ==================================================================================== --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
                <div>
                    <a href="{{ route('admin.kyc.index') }}"
                        class="inline-flex items-center gap-2 text-xs font-mono text-amber-400 hover:text-amber-300 transition-colors mb-2 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>{{ __('Back to KYC Documents') }}</span>
                    </a>
                    <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-amber-100 to-amber-400/70 tracking-tight leading-tight">
                        {{ __('KYC Details') }}: {{ $kyc->user->username }}
                    </h1>
                    <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                        {{ __('Review submitted identity documents and user details') }}
                    </p>
                </div>

                {{-- Status Badge --}}
                <div class="flex items-center gap-3 font-mono text-xs">
                    @if ($kyc->status === 'pending')
                        <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 font-bold uppercase tracking-wider shadow-[0_0_15px_rgba(245,158,11,0.15)]">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                            <span>{{ __('Pending Review') }}</span>
                        </div>
                    @elseif($kyc->status === 'approved')
                        <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold uppercase tracking-wider shadow-[0_0_15px_rgba(16,185,129,0.15)]">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>{{ __('Approved') }}</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 font-bold uppercase tracking-wider shadow-[0_0_15px_rgba(244,63,94,0.15)]">
                            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                            <span>{{ __('Rejected') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ==================================================================================== --}}
            {{-- MAIN LAYOUT --}}
            {{-- ==================================================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-mono">
                
                {{-- Left: User & Documents (2 cols) --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- User Identity Box --}}
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center font-black text-xl shadow-lg shrink-0">
                                    {{ substr($kyc->user->username, 0, 2) }}
                                </div>
                                <div>
                                    <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-0.5">{{ __('User Profile') }}</span>
                                    <h3 class="text-xl font-bold text-white leading-tight">
                                        {{ $kyc->user->fullname ?? $kyc->user->username }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-slate-400 text-xs mt-1">
                                        <span>@<span>{{ $kyc->user->username }}</span></span>
                                        <span>·</span>
                                        <span class="text-slate-300">{{ $kyc->user->email }}</span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('admin.users.detail', $kyc->user_id) }}"
                                class="px-4 py-2.5 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white border border-white/[0.08] text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer">
                                <span>{{ __('View Profile') }}</span>
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Personal Information Grid --}}
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-4">
                            <div class="pb-3 border-b border-white/[0.06]">
                                <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Personal Details') }}</h3>
                                <p class="text-xs text-slate-400 font-sans mt-0.5">{{ __('Information submitted by the user') }}</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/[0.06]">
                                    <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Date of Birth') }}</span>
                                    <span class="text-white font-bold">{{ $kyc->date_of_birth ? $kyc->date_of_birth->format('M d, Y') : __('Not Provided') }}</span>
                                </div>

                                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/[0.06]">
                                    <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Phone Number') }}</span>
                                    <span class="text-white font-bold">{{ $kyc->phone_code }} {{ $kyc->phone ?? __('N/A') }}</span>
                                </div>

                                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/[0.06] sm:col-span-2">
                                    <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Address') }}</span>
                                    <span class="text-white font-bold block">{{ $kyc->address_line_1 ?? __('Not Provided') }}</span>
                                    <span class="text-slate-400 text-[11px] block mt-0.5">{{ $kyc->city ?? '' }}, {{ $kyc->zip ?? '' }}, {{ $kyc->country ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Uploaded Identity Document Gallery --}}
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-6">
                            <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                                <div>
                                    <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Uploaded Documents') }}</h3>
                                    <p class="text-xs text-slate-400 font-sans mt-0.5">{{ __('Government ID and address documents') }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-xl bg-amber-500/10 text-amber-300 border border-amber-500/20 text-xs font-bold uppercase tracking-wider">
                                    {{ __(str_replace('_', ' ', $kyc->document_type)) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Front Document --}}
                                @if ($kyc->document_front)
                                    <div class="group relative rounded-2xl overflow-hidden border border-white/10 aspect-video bg-black/50">
                                        <img src="{{ asset('storage/' . $kyc->document_front) }}" alt="Document Front" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                            <a href="{{ asset('storage/' . $kyc->document_front) }}" target="_blank"
                                                class="px-4 py-2 bg-amber-400 hover:bg-amber-300 text-black font-black text-xs uppercase tracking-wider rounded-xl transition-all">
                                                {{ __('View Full Size') }}
                                            </a>
                                        </div>
                                        <div class="absolute top-3 left-3 px-2.5 py-1 bg-black/80 text-white text-[9px] font-bold uppercase tracking-widest rounded-lg border border-white/10">
                                            {{ __('Front Side') }}
                                        </div>
                                    </div>
                                @endif

                                {{-- Back Document --}}
                                @if ($kyc->document_back)
                                    <div class="group relative rounded-2xl overflow-hidden border border-white/10 aspect-video bg-black/50">
                                        <img src="{{ asset('storage/' . $kyc->document_back) }}" alt="Document Back" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                            <a href="{{ asset('storage/' . $kyc->document_back) }}" target="_blank"
                                                class="px-4 py-2 bg-amber-400 hover:bg-amber-300 text-black font-black text-xs uppercase tracking-wider rounded-xl transition-all">
                                                {{ __('View Full Size') }}
                                            </a>
                                        </div>
                                        <div class="absolute top-3 left-3 px-2.5 py-1 bg-black/80 text-white text-[9px] font-bold uppercase tracking-widest rounded-lg border border-white/10">
                                            {{ __('Back Side') }}
                                        </div>
                                    </div>
                                @endif

                                {{-- Selfie Photo --}}
                                @if ($kyc->selfie)
                                    <div class="group relative rounded-2xl overflow-hidden border border-white/10 aspect-[3/4] bg-black/50">
                                        <img src="{{ asset('storage/' . $kyc->selfie) }}" alt="Selfie" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                            <a href="{{ asset('storage/' . $kyc->selfie) }}" target="_blank"
                                                class="px-4 py-2 bg-amber-400 hover:bg-amber-300 text-black font-black text-xs uppercase tracking-wider rounded-xl transition-all">
                                                {{ __('View Full Size') }}
                                            </a>
                                        </div>
                                        <div class="absolute top-3 left-3 px-2.5 py-1 bg-black/80 text-white text-[9px] font-bold uppercase tracking-widest rounded-lg border border-white/10">
                                            {{ __('Selfie Photo') }}
                                        </div>
                                    </div>
                                @endif

                                {{-- Proof of Address --}}
                                @if ($kyc->proof_address)
                                    <div class="group relative rounded-2xl overflow-hidden border border-white/10 aspect-[3/4] bg-black/50">
                                        <img src="{{ asset('storage/' . $kyc->proof_address) }}" alt="Proof of Address" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                            <a href="{{ asset('storage/' . $kyc->proof_address) }}" target="_blank"
                                                class="px-4 py-2 bg-amber-400 hover:bg-amber-300 text-black font-black text-xs uppercase tracking-wider rounded-xl transition-all">
                                                {{ __('View Full Size') }}
                                            </a>
                                        </div>
                                        <div class="absolute top-3 left-3 px-2.5 py-1 bg-black/80 text-white text-[9px] font-bold uppercase tracking-widest rounded-lg border border-white/10">
                                            {{ __('Proof of Address') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right: Decision Sidebar (1 col) --}}
                <div class="space-y-6">

                    {{-- Review Decision Card --}}
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-5">
                            <div class="pb-3 border-b border-white/[0.06]">
                                <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Review Decision') }}</h3>
                                <p class="text-xs text-slate-400 font-sans mt-0.5">{{ __('Approve or reject this verification request') }}</p>
                            </div>

                            <div class="space-y-3">
                                <button type="button" onclick="openApproveModal()"
                                    class="w-full py-3.5 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-[#050507] font-black text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 shadow-[0_0_20px_rgba(16,185,129,0.3)] cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{ __('Approve KYC') }}</span>
                                </button>

                                <button type="button" onclick="openRejectModal()"
                                    class="w-full py-3.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>{{ __('Reject KYC') }}</span>
                                </button>
                            </div>

                            @if ($kyc->status === 'rejected' && $kyc->rejection_reason)
                                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs">
                                    <span class="text-[9px] uppercase font-bold text-rose-400 tracking-wider block mb-1">{{ __('Rejection Reason') }}</span>
                                    <p class="italic">"{{ $kyc->rejection_reason }}"</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Timeline Card --}}
                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-4">
                            <div class="pb-3 border-b border-white/[0.06]">
                                <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Timeline') }}</h3>
                            </div>

                            <div class="space-y-4 text-xs">
                                <div class="flex items-start gap-3">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400 mt-1"></span>
                                    <div>
                                        <span class="text-white font-bold block">{{ __('Submitted by User') }}</span>
                                        <span class="text-[10px] text-slate-500">{{ $kyc->created_at->format('M d, Y H:i') }} UTC</span>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    @if ($kyc->status === 'approved')
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 mt-1"></span>
                                        <div>
                                            <span class="text-emerald-400 font-bold block">{{ __('Approved by Admin') }}</span>
                                            <span class="text-[10px] text-slate-500">{{ $kyc->updated_at->format('M d, Y H:i') }} UTC</span>
                                        </div>
                                    @elseif($kyc->status === 'rejected')
                                        <span class="w-2 h-2 rounded-full bg-rose-400 mt-1"></span>
                                        <div>
                                            <span class="text-rose-400 font-bold block">{{ __('Rejected by Admin') }}</span>
                                            <span class="text-[10px] text-slate-500">{{ $kyc->updated_at->format('M d, Y H:i') }} UTC</span>
                                        </div>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-amber-400 mt-1 animate-pulse"></span>
                                        <div>
                                            <span class="text-amber-400 font-bold block">{{ __('Pending Review') }}</span>
                                            <span class="text-[10px] text-slate-500">{{ __('Waiting for admin review') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        @endif

    </div>

    {{-- Approve Confirmation Modal --}}
    <div id="approveModal" class="hidden fixed inset-0 bg-[#05070d]/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.05] border border-white/[0.1] w-full max-w-md font-mono">
            <div class="rounded-[2rem] bg-[#090c14] p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-white/[0.06]">
                    <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Approve KYC') }}</h3>
                    <button type="button" onclick="closeApproveModal()" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    {{ __('Are you sure you want to approve this KYC request? The user will be marked as verified.') }}
                </p>
                <form action="{{ route('admin.kyc.update', $kyc->id) }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <button type="button" onclick="closeApproveModal()" class="flex-1 py-3 rounded-xl bg-white/[0.04] text-slate-300 text-xs font-bold uppercase cursor-pointer">{{ __('Cancel') }}</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl bg-emerald-400 text-black font-black text-xs uppercase tracking-wider cursor-pointer">{{ __('Approve') }}</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="hidden fixed inset-0 bg-[#05070d]/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.05] border border-white/[0.1] w-full max-w-md font-mono">
            <div class="rounded-[2rem] bg-[#090c14] p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-white/[0.06]">
                    <h3 class="text-base font-bold text-white uppercase tracking-wide">{{ __('Reject KYC') }}</h3>
                    <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('admin.kyc.update', $kyc->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <div>
                        <label for="rejection_reason" class="block font-bold text-slate-400 uppercase tracking-widest text-xs mb-1.5">{{ __('Rejection Reason') }}</label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-rose-400 text-white rounded-xl p-3 text-xs focus:outline-none transition-all placeholder:text-slate-600"
                            placeholder="{{ __('Explain why this submission was rejected (e.g., photo is blurry or ID has expired)...') }}"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeRejectModal()" class="flex-1 py-3 rounded-xl bg-white/[0.04] text-slate-300 text-xs font-bold uppercase cursor-pointer">{{ __('Cancel') }}</button>
                        <button type="submit" class="flex-1 py-3 rounded-xl bg-rose-500 text-white font-black text-xs uppercase tracking-wider cursor-pointer">{{ __('Reject') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openApproveModal() { $('#approveModal').removeClass('hidden'); }
        function closeApproveModal() { $('#approveModal').addClass('hidden'); }
        function openRejectModal() { $('#rejectModal').removeClass('hidden'); }
        function closeRejectModal() { $('#rejectModal').addClass('hidden'); }
    </script>
@endpush
