@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div id="referral-content" class="space-y-8 mb-12 font-mono">

        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('REFERRALS & AFFILIATES') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Referral Network') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Track referral networks, commission rates, and affiliate earnings') }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.settings.index') }}"
                    class="px-4 py-2.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/[0.1] text-slate-300 hover:text-white text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ __('Commission Settings') }}</span>
                </a>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- 4-CARD STATS DECK --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Total Commissions Paid') }}</span>
                        <span class="text-xl font-black text-emerald-400">{{ showAmount($total_commissions) }}</span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Total Referrals') }}</span>
                        <span class="text-xl font-black text-white">{{ number_format($total_referrals) }}</span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Levels Active') }}</span>
                        <span class="text-xl font-black text-purple-400">{{ $total_levels }} {{ __('Levels') }}</span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-5 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block mb-1">{{ __('Level 1 Commission') }}</span>
                        <span class="text-xl font-black text-amber-400">{{ $referral_bonus_percentage[0] ?? 0 }}%</span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- LEVEL TIERS & TOP INFLUENCERS --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
            {{-- Commission Tiers Card --}}
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 space-y-4">
                    <div class="pb-3 border-b border-white/[0.06]">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Commission Rates') }}</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ __('Percentage paid out for each referral level') }}</p>
                    </div>

                    <div class="space-y-2.5">
                        @foreach ($referral_bonus_percentage as $idx => $percent)
                            <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                                <span class="font-bold text-white uppercase text-[11px]">{{ __('Level') }} {{ $idx + 1 }}</span>
                                <span class="px-2.5 py-1 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 font-black text-xs">
                                    {{ $percent }}%
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Top Influencers --}}
            <div class="lg:col-span-2 p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Top Referrers') }}</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ __('Top users ranked by referral network size') }}</p>
                        </div>
                        <div class="flex items-center gap-1 text-[10px]">
                            @php $sort = request('sort', 'size'); @endphp
                            <a href="{{ route('admin.referrals.index', ['sort' => 'size']) }}" class="px-2.5 py-1 rounded-lg font-bold {{ $sort === 'size' ? 'bg-cyan-400 text-black' : 'text-slate-400 hover:text-white' }}">{{ __('Size') }}</a>
                            <a href="{{ route('admin.referrals.index', ['sort' => 'deposits']) }}" class="px-2.5 py-1 rounded-lg font-bold {{ $sort === 'deposits' ? 'bg-cyan-400 text-black' : 'text-slate-400 hover:text-white' }}">{{ __('Volume') }}</a>
                            <a href="{{ route('admin.referrals.index', ['sort' => 'payouts']) }}" class="px-2.5 py-1 rounded-lg font-bold {{ $sort === 'payouts' ? 'bg-cyan-400 text-black' : 'text-slate-400 hover:text-white' }}">{{ __('Payouts') }}</a>
                        </div>
                    </div>

                    <div class="divide-y divide-white/[0.04]">
                        @forelse($top_referrers as $leader)
                            <div class="py-3 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center font-bold text-xs">
                                        {{ substr($leader->username, 0, 2) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users.detail', $leader->id) }}" class="text-white hover:text-cyan-300 font-bold block leading-tight">
                                            {{ $leader->first_name }} {{ $leader->last_name }}
                                        </a>
                                        <span class="text-[10px] text-slate-500 block">@<span>{{ $leader->username }}</span></span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-6 text-right">
                                    <div>
                                        <span class="text-[9px] uppercase font-bold text-slate-400 block">{{ __('Network Size') }}</span>
                                        <span class="text-white font-black">{{ number_format($leader->network_size) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] uppercase font-bold text-slate-400 block">{{ __('Total Commissions') }}</span>
                                        <span class="text-emerald-400 font-black">{{ showAmount($leader->network_payouts) }}</span>
                                    </div>
                                    <a href="{{ route('admin.referrals.index', ['user_id' => $leader->id]) }}"
                                        class="px-3 py-1.5 rounded-xl bg-white/[0.04] hover:bg-cyan-400 hover:text-black text-slate-300 font-bold uppercase text-[10px] transition-all cursor-pointer">
                                        {{ __('View Tree') }}
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="p-6 text-center text-slate-500">{{ __('No referral leaders found yet.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- USER TREE INSPECTION (If target user selected) --}}
        {{-- ==================================================================================== --}}
        @if ($target_user)
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-white/[0.06]">
                        <div>
                            <span class="text-[9px] uppercase font-bold text-cyan-400 tracking-widest block">{{ __('REFERRAL TREE') }}</span>
                            <h3 class="text-lg font-black text-white mt-0.5">{{ $target_user->fullname }} (@<span>{{ $target_user->username }}</span>)</h3>
                        </div>
                        <a href="{{ route('admin.referrals.index') }}" class="text-xs text-slate-400 hover:text-white uppercase font-bold cursor-pointer">
                            &times; {{ __('Close Tree') }}
                        </a>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.06] text-xs overflow-x-auto">
                        @if(empty($referral_tree))
                            <p class="text-slate-500 py-4 text-center">{{ __('This user has no direct or downline referrals.') }}</p>
                        @else
                            @foreach ($referral_tree as $node)
                                @include('templates.york.blades.admin.referral-networks.partials.tree-item', ['node' => $node])
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- ==================================================================================== --}}
        {{-- GLOBAL REFERRAL LEDGER --}}
        {{-- ==================================================================================== --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
            <div class="rounded-[2rem] bg-[#090c14]/95 overflow-hidden">
                <div class="p-6 border-b border-white/[0.06] flex items-center justify-between text-xs">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Recent Referral Signups') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-white/[0.02] border-b border-white/[0.06] text-[10px] text-slate-400 uppercase tracking-widest">
                                <th class="p-5">{{ __('User') }}</th>
                                <th class="p-5">{{ __('Referred By') }}</th>
                                <th class="p-5 text-right">{{ __('Signup Date') }}</th>
                                <th class="p-5 text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.04]">
                            @forelse($referrals as $ref)
                                <tr class="hover:bg-white/[0.015] transition-colors group">
                                    {{-- Member --}}
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ substr($ref->username, 0, 2) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.users.detail', $ref->id) }}" class="text-white hover:text-cyan-300 font-bold block leading-tight">
                                                    {{ $ref->fullname ?? $ref->username }}
                                                </a>
                                                <span class="text-[10px] text-slate-500 block">@<span>{{ $ref->username }}</span></span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Sponsor --}}
                                    <td class="p-5">
                                        @if($ref->referrer)
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                    {{ substr($ref->referrer->username, 0, 2) }}
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.users.detail', $ref->referrer_id) }}" class="text-white hover:text-purple-300 font-bold block leading-tight">
                                                        {{ $ref->referrer->fullname ?? $ref->referrer->username }}
                                                    </a>
                                                    <span class="text-[10px] text-slate-500 block">@<span>{{ $ref->referrer->username }}</span></span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-500 italic">{{ __('Direct Signup (No Referrer)') }}</span>
                                        @endif
                                    </td>

                                    {{-- Date --}}
                                    <td class="p-5 text-right text-slate-300 text-[11px]">
                                        {{ $ref->created_at->format('M d, Y H:i') }}
                                    </td>

                                    {{-- Action --}}
                                    <td class="p-5 text-center">
                                        <a href="{{ route('admin.referrals.index', ['user_id' => $ref->id]) }}"
                                            class="px-3.5 py-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/20 font-bold uppercase text-[10px] tracking-wider transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>{{ __('View Tree') }}</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-slate-500">
                                        {{ __('No referral signups found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($referrals->hasPages())
                    <div class="p-6 border-t border-white/[0.06]">
                        {{ $referrals->links('templates.york.blades.partials.pagination') }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function toggleTreeNode(nodeId) {
            const container = document.getElementById(nodeId);
            const button = document.querySelector(`.tree-toggle-btn[data-target="${nodeId}"] svg`);

            if (container) {
                if (container.classList.contains('hidden')) {
                    container.classList.remove('hidden');
                    if (button) button.classList.add('rotate-90');
                } else {
                    container.classList.add('hidden');
                    if (button) button.classList.remove('rotate-90');
                }
            }
        }
    </script>
@endpush
