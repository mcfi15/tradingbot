@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div id="kyc-content" class="space-y-8 mb-12">

        {{-- Disabled Warning --}}
        @if (!moduleEnabled('kyc_module'))
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-8 text-center font-mono">
                    <h3 class="text-lg font-bold text-white uppercase">{{ __('KYC Verification is Disabled') }}</h3>
                    <p class="text-xs text-slate-400 mt-2 mb-4">{{ __('Turn on the KYC module in settings to review documents.') }}</p>
                    <a href="{{ route('admin.settings.modules.index') }}" class="px-6 py-2 rounded-full bg-cyan-400 text-black font-black text-xs uppercase">{{ __('Settings') }}</a>
                </div>
            </div>
        @else

            {{-- ==================================================================================== --}}
            {{-- TOP HEADER --}}
            {{-- ==================================================================================== --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        {{ __('IDENTITY VERIFICATION') }}
                    </div>
                    <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-amber-100 to-amber-400/70 tracking-tight leading-tight">
                        {{ __('KYC Documents') }}
                    </h1>
                    <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                        {{ __('Review and manage user identity verification requests') }}
                    </p>
                </div>

                {{-- Fast Filter Ribbon Tabs --}}
                <div class="flex flex-wrap items-center bg-[#090c14] border border-white/[0.08] p-1.5 rounded-2xl gap-1.5 font-mono text-xs shadow-xl">
                    @php $currentStatus = request('status', 'all'); @endphp
                    <a href="{{ route('admin.kyc.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
                        class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentStatus === 'all' ? 'bg-amber-400 text-black shadow-[0_0_15px_rgba(245,158,11,0.3)]' : 'text-slate-400 hover:text-white' }}">
                        {{ __('All Documents') }}
                    </a>
                    <a href="{{ route('admin.kyc.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}"
                        class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentStatus === 'pending' ? 'bg-amber-400 text-black shadow-[0_0_15px_rgba(245,158,11,0.3)]' : 'text-slate-400 hover:text-white' }}">
                        {{ __('Pending') }} ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('admin.kyc.index', array_merge(request()->except('status', 'page'), ['status' => 'approved'])) }}"
                        class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentStatus === 'approved' ? 'bg-amber-400 text-black shadow-[0_0_15px_rgba(245,158,11,0.3)]' : 'text-slate-400 hover:text-white' }}">
                        {{ __('Approved') }} ({{ $approvedCount }})
                    </a>
                    <a href="{{ route('admin.kyc.index', array_merge(request()->except('status', 'page'), ['status' => 'rejected'])) }}"
                        class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentStatus === 'rejected' ? 'bg-amber-400 text-black shadow-[0_0_15px_rgba(245,158,11,0.3)]' : 'text-slate-400 hover:text-white' }}">
                        {{ __('Rejected') }} ({{ $rejectedCount }})
                    </a>
                </div>
            </div>

            {{-- ==================================================================================== --}}
            {{-- 4-CARD STATS DECK --}}
            {{-- ==================================================================================== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 font-mono">
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Total Documents') }}</span>
                            <span class="text-xl font-black text-white">{{ number_format($totalSubmissions) }}</span>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Pending Review') }}</span>
                            <span class="text-xl font-black text-amber-400">{{ number_format($pendingCount) }}</span>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Approved') }}</span>
                            <span class="text-xl font-black text-emerald-400">{{ number_format($approvedCount) }}</span>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Rejected') }}</span>
                            <span class="text-xl font-black text-rose-400">{{ number_format($rejectedCount) }}</span>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================================================================================== --}}
            {{-- DATA TABLE CHASSIS --}}
            {{-- ==================================================================================== --}}
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 overflow-hidden font-mono">

                    {{-- Search Toolbar --}}
                    <div class="p-6 border-b border-white/[0.06] flex flex-col lg:flex-row justify-between items-center gap-4 text-xs">
                        <form action="{{ route('admin.kyc.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by user or document...') }}" 
                                class="bg-[#05070d] border border-white/[0.1] focus:border-amber-400 text-white rounded-xl px-4 py-2 text-xs focus:outline-none transition-all w-full sm:w-72">

                            <button type="submit" class="p-2 rounded-xl bg-amber-400 text-black hover:bg-amber-300 font-bold transition-all cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-white/[0.02] border-b border-white/[0.06] text-[10px] text-slate-400 uppercase tracking-widest">
                                    <th class="p-5">{{ __('User') }}</th>
                                    <th class="p-5">{{ __('Document Type') }}</th>
                                    <th class="p-5">{{ __('Country') }}</th>
                                    <th class="p-5 text-center">{{ __('Status') }}</th>
                                    <th class="p-5 text-right">{{ __('Submitted Date') }}</th>
                                    <th class="p-5 text-right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.04]">
                                @forelse($kycs as $kyc)
                                    <tr class="hover:bg-white/[0.015] transition-colors group">
                                        {{-- User --}}
                                        <td class="p-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                    {{ substr($kyc->user?->username ?? 'U', 0, 2) }}
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.users.detail', $kyc->user_id) }}" class="text-white hover:text-amber-300 font-bold block leading-tight">
                                                        {{ $kyc->user?->fullname ?? $kyc->user?->username ?? 'N/A' }}
                                                    </a>
                                                    <span class="text-[10px] text-slate-500 block">@<span>{{ $kyc->user?->username ?? 'user' }}</span></span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Document Type --}}
                                        <td class="p-5">
                                            <span class="text-white font-bold block uppercase">{{ $kyc->document_type ?? __('National ID') }}</span>
                                            <span class="text-[10px] text-slate-500 font-normal">#{{ $kyc->document_number ?? 'N/A' }}</span>
                                        </td>

                                        {{-- Country --}}
                                        <td class="p-5 text-slate-300">
                                            {{ $kyc->country ?? $kyc->user?->country ?? 'N/A' }}
                                        </td>

                                        {{-- Status --}}
                                        <td class="p-5 text-center">
                                            @if ($kyc->status === 'pending')
                                                <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[9px] font-bold uppercase tracking-wider">
                                                    ● {{ __('Pending') }}
                                                </span>
                                            @elseif($kyc->status === 'approved')
                                                <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-bold uppercase tracking-wider">
                                                    ● {{ __('Approved') }}
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[9px] font-bold uppercase tracking-wider">
                                                    ● {{ __('Rejected') }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Date --}}
                                        <td class="p-5 text-right text-slate-300 text-[11px]">
                                            {{ $kyc->created_at->format('M d, Y') }}
                                            <span class="text-[10px] text-slate-500 block">{{ $kyc->created_at->format('H:i') }} UTC</span>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="p-5 text-right">
                                            <a href="{{ route('admin.kyc.view', $kyc->id) }}"
                                                class="px-3.5 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/20 font-bold uppercase text-[11px] tracking-wider transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>{{ __('View Details') }}</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-12 text-center text-slate-500 font-mono">
                                            {{ __('No KYC documents found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($kycs->hasPages())
                        <div class="p-6 border-t border-white/[0.06]">
                            {{ $kycs->links('templates.york.blades.partials.pagination') }}
                        </div>
                    @endif

                </div>
            </div>

        @endif

    </div>
@endsection
