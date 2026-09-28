@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-12 pb-24">

    {{-- Global Ethereal Ambient Mesh Gradients --}}
    <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-emerald-500/5 via-cyan-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-12">

        {{-- ══ HEADER HERO & LINK VAULT DECK ═══════════════════════════════════ --}}
        <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                 style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">
                
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div class="max-w-2xl space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary shadow-[0_0_15px_rgba(226,177,60,0.2)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                            <span>{{ __('Referral Program') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                            {{ __('Invite Friends & Earn') }}
                        </h1>

                        <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed">
                            {{ __('Invite friends and earn commissions across :levels referral levels. All earnings are credited directly to your wallet.', ['levels' => $total_levels]) }}
                        </p>
                    </div>

                    {{-- Link & Share Actions --}}
                    <div class="w-full lg:w-auto space-y-3">
                        <div class="relative w-full lg:w-[420px]">
                            <input type="text" value="{{ $referral_link }}" id="referralLink" readonly
                                   class="w-full bg-black/60 border border-white/10 rounded-full pl-6 pr-32 py-4 text-xs font-mono text-white outline-none focus:border-accent-primary transition-all">
                            <button onclick="copyReferralLink()"
                                   class="absolute right-2 top-1/2 -translate-y-1/2 px-6 py-2.5 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(226,177,60,0.3)] active:scale-[0.97] cursor-pointer">
                                {{ __('Copy Link') }}
                            </button>
                        </div>

                        {{-- Social Share Buttons --}}
                        <div class="flex items-center gap-3 pt-1">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500">{{ __('Share via:') }}</span>
                            <a href="https://wa.me/?text={{ urlencode(__('Join me on :site_name ! Use my link to get started: :ref_link', ['site_name' => getSetting('name'), 'ref_link' => $referral_link])) }}" target="_blank"
                               class="p-2.5 rounded-full bg-white/5 hover:bg-[#25D366]/20 border border-white/10 text-slate-400 hover:text-[#25D366] transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .004 5.411.002 12.048c0 2.12.54 4.19 1.566 6.055L0 24l6.101-1.599a11.83 11.83 0 005.946 1.59h.005c6.634 0 12.043-5.411 12.046-12.048a11.811 11.811 0 00-3.536-8.509"/></svg>
                            </a>
                            <a href="https://t.me/share/url?url={{ urlencode($referral_link) }}&text={{ urlencode(__('Join me on :site_name !', ['site_name' => getSetting('name')])) }}" target="_blank"
                               class="p-2.5 rounded-full bg-white/5 hover:bg-[#0088cc]/20 border border-white/10 text-slate-400 hover:text-[#0088cc] transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0C5.346 0 0 5.346 0 11.944c0 6.598 5.346 11.944 11.944 11.944 6.598 0 11.944-5.346 11.944-11.944C23.888 5.346 18.542 0 11.944 0zm5.83 8.356c-.17 1.787-.922 6.22-1.305 8.267-.162.866-.481 1.155-.79 1.183-.67.062-1.178-.442-1.828-.868-1.017-.667-1.592-1.082-2.578-1.731-1.14-.75-.401-1.163.249-1.836.17-.176 3.125-2.867 3.181-3.102.007-.03.012-.138-.052-.196-.065-.057-.16-.038-.228-.022-.1-.02-.03-.217 1.62-.68.648-2.316.326-5.01-.225-5.92-.007-.013-.017-.035-.034-.05-.052-.047-.13-.047-.13-.047s-.08-.002-.224.038c-1.343.376-2.583 1.258-3.703 2.05-1.12.793-2.18 1.68-3.141 2.51-.9.778-1.631 1.554-2.193 2.196-.4.457-.75.875-1.05 1.24-.26.316-.32.486-.337.601a.91.91 0 00.176.622c.118.146.337.306.66.44.368.156.883.332 1.487.49 1.233.324 2.82.723 4.102 1.05 1.282.327 2.22.463 2.812.28 1.03-.318 1.649-.699 1.859-.9 2.073-1.986 4.146-3.972 6.219-5.958"/></svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode(__('Start trading with my referral link: :ref_link', ['ref_link' => $referral_link])) }}" target="_blank"
                               class="p-2.5 rounded-full bg-white/5 hover:bg-[#1DA1F2]/20 border border-white/10 text-slate-400 hover:text-[#1DA1F2] transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ NETWORK GROWTH ANALYTICS GRID (4 CARDS) ═════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Referral Earnings --}}
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Commissions') }}</span>
                    <div class="text-3xl font-black text-white font-mono tracking-tight">{{ number_format($referral_earnings, 2) }} <span class="text-xs text-slate-500 font-sans">{{ getSetting('currency') }}</span></div>
                    <div class="text-[10px] text-accent-primary font-bold pt-1">{{ __('Paid Directly to Wallet') }}</div>
                </div>
            </div>

            {{-- Direct Referrals --}}
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Direct Referrals') }}</span>
                    <div class="text-3xl font-black text-purple-400 font-mono tracking-tight">{{ $referrals->total() }} <span class="text-xs text-slate-500 font-sans">{{ __('Users') }}</span></div>
                    <div class="text-[10px] text-purple-400/80 font-medium pt-1">{{ __('Direct Referrals') }}</div>
                </div>
            </div>

            {{-- Total Network Size --}}
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Total Referrals') }}</span>
                    <div class="text-3xl font-black text-blue-400 font-mono tracking-tight">{{ $network_count }} <span class="text-xs text-slate-500 font-sans">{{ __('Users') }}</span></div>
                    <div class="text-[10px] text-blue-400/80 font-medium pt-1">{{ __('All Referral Levels') }}</div>
                </div>
            </div>

            {{-- Active Tiers --}}
            <div class="rounded-[2rem] p-1 bg-white/[0.03] border border-white/8">
                <div class="rounded-[calc(2rem-0.25rem)] p-6 space-y-2" style="background: rgba(8,10,15,0.95);">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-500 block">{{ __('Referral Levels') }}</span>
                    <div class="text-3xl font-black text-emerald-400 font-mono tracking-tight">{{ $total_levels }} <span class="text-xs text-slate-500 font-sans">{{ __('Levels') }}</span></div>
                    <div class="text-[10px] text-emerald-400/80 font-medium pt-1">{{ __('Commission Tiers') }}</div>
                </div>
            </div>
        </div>

        {{-- ══ NETWORK TREE & COMMISSION BREAKDOWN ═══════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Network Tree Column --}}
            <div class="lg:col-span-2 rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl overflow-hidden min-h-[480px]">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-8 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-white/5">
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight">{{ __('Referral Tree') }}</h3>
                            <p class="text-xs text-slate-400 font-medium mt-1">{{ __('View your referral network and generations.') }}</p>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-accent-primary/10 border border-accent-primary/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-accent-primary">{{ __('Network Tree') }}</span>
                        </div>
                    </div>

                    <div class="overflow-y-auto max-h-[500px] pr-2">
                        @if (empty($referral_tree))
                            <div class="py-16 text-center space-y-4">
                                <div class="w-16 h-16 mx-auto rounded-3xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-black text-lg">{{ __('No Referrals Yet') }}</h4>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">{{ __('Share your referral link to start earning commissions when friends join and trade.') }}</p>
                                </div>
                                <button onclick="copyReferralLink()"
                                        class="px-7 py-3 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(226,177,60,0.3)] cursor-pointer">
                                    {{ __('Copy Referral Link') }}
                                </button>
                            </div>
                        @else
                            <div class="referral-tree space-y-3">
                                @foreach ($referral_tree as $node)
                                    @include('templates.york.blades.partials.referral-tree-item', ['node' => $node])
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Commission Breakdown Column --}}
            <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl">
                <div class="rounded-[calc(2.5rem-0.375rem)] p-8 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tight">{{ __('Commission Rates') }}</h3>
                        <p class="text-xs text-slate-400 font-medium mt-1">{{ __('Commission percentage for each referral level.') }}</p>
                    </div>

                    <div class="space-y-3">
                        @foreach ($referral_bonus_percentage as $index => $percent)
                            <div class="p-4 rounded-2xl bg-black/40 border border-white/5 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-accent-primary/10 border border-accent-primary/20 flex items-center justify-center text-accent-primary font-black text-xs font-mono">
                                        L{{ $index + 1 }}
                                    </div>
                                    <div>
                                        <span class="block text-xs font-black text-white">{{ __('Level :num', ['num' => $index + 1]) }}</span>
                                        <span class="text-[9px] font-mono text-slate-500">{{ __('Level :num', ['num' => $index + 1]) }}</span>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <span class="text-lg font-black text-accent-primary font-mono">{{ $percent }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ DIRECT REFERRALS TABLE ═════════════════════════════════════════ --}}
        <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl overflow-hidden">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tight">{{ __('Direct Referrals') }}</h3>
                        <p class="text-xs text-slate-400 font-medium mt-1">{{ __('People who joined using your referral link.') }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-[10px] text-slate-500 uppercase tracking-[0.2em] font-black">
                                <th class="px-4 py-3">{{ __('User') }}</th>
                                <th class="px-4 py-3">{{ __('Referral Code') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Joined Date') }}</th>
                                <th class="px-4 py-3 text-center">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            @forelse ($referrals as $row)
                                <tr class="bg-white/[0.02] border border-white/5 rounded-2xl hover:bg-white/[0.05] transition-all">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white font-black text-xs">
                                                {{ strtoupper(substr($row->first_name, 0, 1)) }}{{ strtoupper(substr($row->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="block text-xs font-black text-white">{{ $row->first_name }} {{ $row->last_name }}</span>
                                                @php
                                                    $emailParts = explode('@', $row->email);
                                                    $maskedEmail = substr($emailParts[0], 0, 2) . '***@' . ($emailParts[1] ?? '');
                                                @endphp
                                                <span class="text-[10px] font-mono text-slate-500">{{ $maskedEmail }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 font-mono font-bold text-accent-primary">
                                        {{ $row->referral_code }}
                                    </td>
                                    <td class="px-4 py-4 text-right text-slate-400 font-medium">
                                        {{ $row->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border bg-emerald-500/10 text-emerald-400 border-emerald-500/20">
                                            {{ __('Active') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-500 text-xs italic font-medium">
                                        {{ __('No referrals found. Share your link to get started.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $referrals->links() }}
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function copyReferralLink() {
    const el = document.getElementById("referralLink");
    if (!el) return;
    navigator.clipboard.writeText(el.value).then(() => {
        if (typeof toastNotification === 'function') {
            toastNotification("{{ __('Referral link copied to clipboard') }}", 'success');
        } else {
            alert("{{ __('Referral link copied to clipboard') }}");
        }
    });
}
</script>
@endpush
