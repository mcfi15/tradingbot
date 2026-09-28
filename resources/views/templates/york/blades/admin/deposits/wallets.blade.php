@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12">

        {{-- Top Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                    {{ __('USER WALLETS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('User Deposit Wallets') }}
                </h1>
                <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Deposit addresses generated for users to receive cryptocurrency payments') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.deposits.index') }}"
                    class="px-5 py-2.5 rounded-full bg-white/[0.03] hover:bg-white/[0.06] border border-white/[0.1] text-slate-300 hover:text-white font-mono font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                    <span>← {{ __('All Deposits') }}</span>
                </a>
            </div>
        </div>

        {{-- Wallets Table Section (Double-Bezel) --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
            <div class="rounded-[2rem] bg-[#090c14]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] overflow-hidden">
                
                {{-- Table Header & Filters --}}
                <div class="p-6 border-b border-white/[0.06] flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white/[0.01]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect>
                                <line x1="2" y1="10" x2="22" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white font-mono uppercase tracking-wide flex items-center gap-2">
                                <span>{{ __('User Deposit Addresses') }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 text-[10px]">
                                    {{ $wallets->total() }}
                                </span>
                            </h3>
                            <p class="text-xs text-slate-400">{{ __('Blockchain addresses generated for user deposits') }}</p>
                        </div>
                    </div>

                    {{-- Search Form --}}
                    <form action="{{ route('admin.deposits.wallets') }}" method="GET" class="flex flex-wrap gap-2.5 items-center font-mono">
                        <div class="relative min-w-[240px]">
                            <input type="text" name="search" placeholder="{{ __('Search user, wallet address...') }}"
                                value="{{ request('search') }}"
                                class="w-full bg-white/[0.03] border border-white/[0.1] focus:border-cyan-400/60 rounded-xl px-4 py-2 text-white text-xs placeholder-slate-500 focus:outline-none transition-all">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-cyan-400 hover:bg-cyan-300 text-[#050507] rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer shadow-[0_0_15px_rgba(0,245,255,0.25)]">
                            {{ __('Search') }}
                        </button>
                        @if (request('search'))
                            <a href="{{ route('admin.deposits.wallets') }}" class="px-4 py-2 bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 rounded-xl text-xs font-bold uppercase tracking-wider transition-all border border-white/[0.08]">
                                {{ __('Clear') }}
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[900px]">
                        <thead>
                            <tr class="border-b border-white/[0.06] bg-white/[0.01]">
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('User') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Network') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Deposit Address') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Private Key') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono">{{ __('Explorer') }}</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest font-mono text-right">{{ __('Date Created') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.03]">
                            @forelse ($wallets as $wallet)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    {{-- User --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-xs font-bold font-mono text-cyan-400 shrink-0">
                                                {{ strtoupper(substr($wallet->user->username ?? ($wallet->user->name ?? 'U'), 0, 2)) }}
                                            </div>
                                            <div>
                                                @if ($wallet->user)
                                                    <a href="{{ route('admin.users.detail', $wallet->user_id) }}" class="text-xs font-bold font-mono text-white hover:text-cyan-400 transition-colors block">
                                                        {{ $wallet->user->username ?? $wallet->user->name }}
                                                    </a>
                                                    <span class="text-[10px] text-slate-400 font-mono block mt-0.5">{{ $wallet->user->email ?? '' }}</span>
                                                @else
                                                    <span class="text-xs font-mono text-slate-400">{{ __('Deleted Account') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Blockchain --}}
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-300 text-xs font-mono font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                                            <span>{{ $wallet->blockchain->name ?? __('Unknown') }}</span>
                                        </span>
                                    </td>

                                    {{-- Wallet Address --}}
                                    <td class="px-6 py-4">
                                        <div class="inline-flex items-center gap-2 bg-black/40 px-3 py-1.5 rounded-xl border border-white/[0.06] max-w-md">
                                            <code class="font-mono text-xs text-cyan-300 truncate select-all">{{ $wallet->address }}</code>
                                            <button type="button" onclick="copyToClipboard('{{ $wallet->address }}', '{{ __('Address copied to clipboard') }}')"
                                                    class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/[0.08] transition-all cursor-pointer shrink-0" title="{{ __('Copy address') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                                </svg>
                                            </button>
                                            <button type="button" onclick="showMasterWalletQrModal('{{ addslashes($wallet->blockchain->name ?? 'User') }}', '{{ addslashes($wallet->address) }}', '{{ __('Deposit Wallet') }}')"
                                                    class="p-1 rounded-lg text-slate-400 hover:text-cyan-400 hover:bg-white/[0.08] transition-all cursor-pointer shrink-0" title="{{ __('Show QR Code') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                                                    <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                                                    <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                                                    <rect x="14" y="14" width="3" height="3"></rect>
                                                    <rect x="18" y="14" width="3" height="3"></rect>
                                                    <rect x="14" y="18" width="3" height="3"></rect>
                                                    <rect x="18" y="18" width="3" height="3"></rect>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Private Key --}}
                                    <td class="px-6 py-4">
                                        <div class="inline-flex items-center gap-2 bg-black/40 px-3 py-1.5 rounded-xl border border-white/[0.06] max-w-xs">
                                            <span class="font-mono text-xs text-slate-500 tracking-wider truncate" id="priv-key-display-{{ $wallet->id }}">
                                                ••••••••••••••••
                                            </span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                {{-- Reveal Button --}}
                                                <button type="button" id="btn-reveal-{{ $wallet->id }}"
                                                        onclick="promptRevealUserKey({{ $wallet->id }}, '{{ addslashes($wallet->user->username ?? $wallet->user->name ?? 'User') }}', '{{ addslashes($wallet->blockchain->name ?? 'Wallet') }}')"
                                                        class="p-1 rounded-lg text-amber-400 hover:text-amber-300 hover:bg-amber-400/10 transition-all cursor-pointer"
                                                        title="{{ __('Reveal Private Key') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                </button>
                                                {{-- Copy Private Key Button --}}
                                                <button type="button" id="btn-copy-priv-{{ $wallet->id }}" style="display:none;"
                                                        onclick="copyUserPrivKey({{ $wallet->id }})"
                                                        class="p-1 rounded-lg text-emerald-400 hover:text-emerald-300 hover:bg-emerald-400/10 transition-all cursor-pointer"
                                                        title="{{ __('Copy Private Key') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                                    </svg>
                                                </button>
                                                {{-- Hide Private Key Button --}}
                                                <button type="button" id="btn-hide-priv-{{ $wallet->id }}" style="display:none;"
                                                        onclick="hideUserPrivKey({{ $wallet->id }})"
                                                        class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-all cursor-pointer"
                                                        title="{{ __('Hide Private Key') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                                        <line x1="1" y1="1" x2="23" y2="23"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Explorer Link --}}
                                    <td class="px-6 py-4">
                                        <a href="{{ $wallet->explorerUrl() }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/[0.03] border border-white/[0.06] hover:border-cyan-400/40 text-slate-300 hover:text-cyan-300 text-xs font-mono transition-all">
                                            <span>Explorer</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </td>

                                    {{-- Date --}}
                                    <td class="px-6 py-4 text-right font-mono">
                                        <span class="text-xs text-slate-200 block">
                                            {{ $wallet->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="text-[10px] text-slate-500 block mt-0.5">
                                            {{ $wallet->created_at->format('H:i') }} UTC
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center text-slate-500 font-mono text-xs uppercase tracking-widest">
                                        {{ __('No user deposit wallets found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                @if ($wallets->hasPages())
                    <div class="px-6 py-4 border-t border-white/[0.06] bg-white/[0.01]">
                        {{ $wallets->links('templates.york.blades.partials.pagination') }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const revealedUserKeys = {};

        function promptRevealUserKey(walletId, username, network) {
            Swal.fire({
                title: '{{ __('Reveal Private Key') }}',
                text: '{{ __('Please enter your administrator password to decrypt and reveal this wallet private key:') }}',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                showCancelButton: true,
                confirmButtonText: '{{ __('Confirm') }}',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#1e293b',
                background: '#12131a',
                color: '#fff',
                showLoaderOnConfirm: true,
                customClass: {
                    popup: 'border border-white/10 rounded-2xl shadow-2xl font-mono'
                },
                preConfirm: (password) => {
                    const url = '{{ route("admin.deposits.wallets.reveal", ":id") }}'.replace(':id', walletId);
                    return $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            password: password
                        }
                    }).then(response => {
                        if (response.status !== 'success') {
                            throw new Error(response.message || '{{ __('Failed to decrypt') }}');
                        }
                        return response;
                    }).catch(error => {
                        let msg = error.responseJSON?.message || error.message || '{{ __('Incorrect password') }}';
                        Swal.showValidationMessage(msg);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const privKey = result.value.private_key;
                    revealedUserKeys[walletId] = privKey;

                    // Update inline row
                    $(`#priv-key-display-${walletId}`).html(`<span class="text-amber-400 font-mono text-xs font-bold select-all break-all">${privKey}</span>`);
                    $(`#btn-reveal-${walletId}`).hide();
                    $(`#btn-copy-priv-${walletId}`).show();
                    $(`#btn-hide-priv-${walletId}`).show();

                    // Detailed secure popup
                    Swal.fire({
                        title: `<span class="text-white text-base font-bold font-mono uppercase tracking-wide">${username}'s ${network} {{ __('Private Key') }}</span>`,
                        html: `
                            <div class="space-y-3 py-2 text-left font-mono">
                                <div>
                                    <div class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mb-1">{{ __('Deposit Address') }}</div>
                                    <div class="bg-black/50 p-2.5 rounded-xl border border-white/10 text-xs text-cyan-300 break-all select-all">
                                        ${result.value.address}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[10px] text-amber-400 uppercase font-bold tracking-wider mb-1">{{ __('Decrypted Private Key') }}</div>
                                    <div class="bg-amber-500/10 p-3 rounded-xl border border-amber-500/30 text-xs text-amber-300 font-bold break-all select-all selection:bg-amber-400/40">
                                        ${privKey}
                                    </div>
                                </div>
                                <p class="text-[11px] text-slate-400">
                                    {{ __('Keep this private key confidential. It grants full custody and transfer authority over funds in this address.') }}
                                </p>
                            </div>
                        `,
                        background: '#090c14',
                        color: '#fff',
                        confirmButtonText: '{{ __('Copy Private Key') }}',
                        showCancelButton: true,
                        cancelButtonText: '{{ __('Close') }}',
                        customClass: {
                            popup: 'border border-amber-500/30 rounded-3xl shadow-2xl font-mono',
                            confirmButton: 'px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-black font-black uppercase text-xs tracking-wider transition-all cursor-pointer mr-2',
                            cancelButton: 'px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold uppercase text-xs tracking-wider transition-all cursor-pointer'
                        }
                    }).then((res) => {
                        if (res.isConfirmed) {
                            copyToClipboard(privKey, '{{ __('Private key copied to clipboard') }}');
                        }
                    });
                }
            });
        }

        function copyUserPrivKey(walletId) {
            if (revealedUserKeys[walletId]) {
                copyToClipboard(revealedUserKeys[walletId], '{{ __('Private key copied to clipboard') }}');
            }
        }

        function hideUserPrivKey(walletId) {
            delete revealedUserKeys[walletId];
            $(`#priv-key-display-${walletId}`).html('••••••••••••••••');
            $(`#btn-reveal-${walletId}`).show();
            $(`#btn-copy-priv-${walletId}`).hide();
            $(`#btn-hide-priv-${walletId}`).hide();
        }
    </script>
@endpush
