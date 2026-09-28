@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div id="withdrawal-view-content" class="space-y-8 mb-12">

        {{-- Top Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-white/[0.06]">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.withdrawals.index') }}"
                    class="w-11 h-11 rounded-2xl bg-white/[0.03] border border-white/[0.08] hover:border-cyan-400/40 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-mono font-bold uppercase tracking-[0.2em] mb-1">
                        {{ __('WITHDRAWAL DETAILS') }}
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                        <span>{{ __('Withdrawal Details') }}</span>
                    </h1>
                    <p class="text-slate-400 font-mono text-xs mt-0.5 uppercase tracking-wider">
                        {{ __('Transaction ID') }}: <span class="text-cyan-300">#{{ $withdrawal->transaction_reference }}</span>
                    </p>
                </div>
            </div>

            {{-- Status & Actions --}}
            <div class="flex items-center gap-3 font-mono">
                @php
                    $statusClasses = [
                        'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                        'completed' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                        'failed' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                    ];
                    $class = $statusClasses[$withdrawal->status] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                @endphp
                <span class="px-4 py-2 rounded-full border {{ $class }} font-bold uppercase tracking-wider text-xs">
                    ● {{ __($withdrawal->status) }}
                </span>

                <button type="button"
                    class="btn-edit-status px-5 py-2 rounded-full bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(0,245,255,0.3)] transition-all cursor-pointer"
                    data-id="{{ $withdrawal->id }}" data-status="{{ $withdrawal->status }}">
                    {{ __('Update Status') }}
                </button>
            </div>
        </div>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left: Main Details (2 cols) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- User Information Card --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-7 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            @if ($withdrawal->user?->photo)
                                <div class="w-14 h-14 rounded-2xl border border-white/10 shadow-lg overflow-hidden shrink-0">
                                    <img src="{{ asset('storage/profile/' . $withdrawal->user->photo) }}"
                                        alt="{{ $withdrawal->user->username }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 font-mono font-black text-lg shrink-0">
                                    {{ strtoupper(substr($withdrawal->user?->username ?? 'NA', 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <span class="text-[10px] text-slate-500 font-mono uppercase tracking-widest font-bold block mb-1">
                                    {{ __('Recipient') }}
                                </span>
                                @if ($withdrawal->user)
                                    <a href="{{ route('admin.users.detail', $withdrawal->user_id) }}"
                                        class="text-lg font-bold text-white hover:text-cyan-400 transition-colors font-mono block">
                                        {{ $withdrawal->user->fullname }}
                                        <span class="text-xs text-slate-400">({{ $withdrawal->user->username }})</span>
                                    </a>
                                    <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $withdrawal->user->email }}</p>
                                @else
                                    <p class="text-lg font-bold text-slate-400 font-mono">{{ __('Deleted Account') }}</p>
                                @endif
                            </div>
                        </div>
                        @if ($withdrawal->user)
                            <a href="{{ route('admin.users.detail', $withdrawal->user_id) }}"
                                class="w-11 h-11 bg-white/[0.04] hover:bg-cyan-500/10 border border-white/[0.08] hover:border-cyan-500/30 text-slate-300 hover:text-cyan-400 rounded-2xl flex items-center justify-center transition-all shadow-md"
                                title="{{ __('View Profile') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Financial Summary Card --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8">
                        <div class="flex items-center gap-2 mb-6 pb-4 border-b border-white/[0.06]">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f5ff]"></span>
                            <h3 class="text-base font-bold text-white font-mono uppercase tracking-wide">{{ __('Payout Summary') }}</h3>
                        </div>

                        <div class="space-y-4 font-mono">
                            <div class="flex justify-between items-center py-3 border-b border-white/[0.04]">
                                <span class="text-slate-400 text-xs">{{ __('Withdrawal Amount') }}</span>
                                <span class="text-white font-bold text-sm">{{ showAmount($withdrawal->amount) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-white/[0.04]">
                                <span class="text-slate-400 text-xs flex items-center gap-1.5">
                                    <span>{{ __('Fee') }}</span>
                                    <span class="text-[10px] text-slate-500">({{ number_format($withdrawal->fee_percent, 2) }}%)</span>
                                </span>
                                <span class="text-rose-400 font-bold text-sm">- {{ showAmount($withdrawal->fee_amount) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-4 px-5 rounded-2xl bg-white/[0.02] border border-white/[0.06]">
                                <span class="text-white font-bold text-xs uppercase tracking-wider">{{ __('Net Amount Sent to User') }}</span>
                                <span class="text-2xl font-black text-emerald-400 drop-shadow-[0_0_12px_rgba(16,185,129,0.3)]">
                                    {{ showAmount($withdrawal->amount_payable) }}
                                </span>
                            </div>

                            @if ($withdrawal->exchange_rate != 1)
                                <div class="bg-black/40 rounded-2xl p-5 border border-white/[0.06] mt-4 space-y-3">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">{{ __('Exchange Rate') }}</span>
                                        <span class="text-white font-mono">1 {{ getSetting('currency', 'USD') }} = {{ number_format($withdrawal->exchange_rate, 8) }} {{ $withdrawal->currency }}</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-3 border-t border-white/[0.06]">
                                        <span class="text-slate-300 font-bold text-xs">{{ __('Converted Amount') }}</span>
                                        <span class="text-xl font-black text-cyan-300">
                                            {{ number_format($withdrawal->converted_amount, 8) }} {{ $withdrawal->currency }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Blockchain Details Card --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8">
                        <div class="flex items-center gap-2 mb-6 pb-4 border-b border-white/[0.06]">
                            <span class="w-2 h-2 rounded-full bg-purple-400 shadow-[0_0_8px_#a855f7]"></span>
                            <h3 class="text-base font-bold text-white font-mono uppercase tracking-wide">{{ __('Blockchain Details') }}</h3>
                        </div>

                        {{-- Core Network & Asset Badges --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4">
                            <div class="bg-white/[0.02] border border-white/[0.06] p-4 rounded-2xl">
                                <span class="text-[10px] text-slate-500 uppercase tracking-widest font-bold font-mono block mb-1">
                                    {{ __('Network') }}
                                </span>
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                    <span class="text-white font-bold font-mono text-sm">{{ $withdrawal->gatewayName() }}</span>
                                </div>
                            </div>

                            <div class="bg-white/[0.02] border border-white/[0.06] p-4 rounded-2xl">
                                <span class="text-[10px] text-slate-500 uppercase tracking-widest font-bold font-mono block mb-1">
                                    {{ __('Currency') }}
                                </span>
                                <span class="px-2.5 py-1 rounded-md bg-purple-500/10 text-purple-300 border border-purple-500/20 font-mono font-bold text-xs">
                                    {{ $withdrawal->currency }}
                                </span>
                            </div>

                            @if ($withdrawal->transaction_hash)
                                @php $withdrawalTxUrl = $withdrawal->explorerTxUrl(); @endphp
                                <div class="bg-white/[0.02] border border-white/[0.06] p-4 rounded-2xl md:col-span-2">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[10px] text-slate-500 uppercase tracking-widest font-bold font-mono">
                                            {{ __('Transaction Hash (TXID)') }}
                                        </span>
                                        @if ($withdrawalTxUrl)
                                            <a href="{{ $withdrawalTxUrl }}" target="_blank"
                                                class="inline-flex items-center gap-1 text-[10px] font-mono font-bold text-cyan-400 hover:text-cyan-300 transition-colors uppercase tracking-wider">
                                                <span>{{ __('View on Explorer') }}</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between gap-3 bg-black/40 p-2.5 rounded-xl border border-white/[0.04]">
                                        @if ($withdrawalTxUrl)
                                            <a href="{{ $withdrawalTxUrl }}" target="_blank"
                                                class="text-cyan-300 hover:text-cyan-200 font-mono text-xs break-all select-all flex-1 underline decoration-cyan-500/30 underline-offset-2 hover:decoration-cyan-400 transition-colors">
                                                {{ $withdrawal->transaction_hash }}
                                            </a>
                                        @else
                                            <code class="text-cyan-300 font-mono text-xs break-all select-all flex-1">
                                                {{ $withdrawal->transaction_hash }}
                                            </code>
                                        @endif
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            @if ($withdrawalTxUrl)
                                                <a href="{{ $withdrawalTxUrl }}" target="_blank"
                                                    class="p-1.5 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-lg transition-colors"
                                                    title="{{ __('Open in Explorer') }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>
                                            @endif
                                            <button type="button" onclick="navigator.clipboard.writeText('{{ $withdrawal->transaction_hash }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Hash Copied') }}', showConfirmButton:false, timer:1500});"
                                                class="p-1.5 bg-white/[0.05] hover:bg-white/[0.1] rounded-lg text-slate-300 hover:text-white transition-colors cursor-pointer" title="{{ __('Copy Hash') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Structured User Data (Formatted Hierarchically with Human Readable Timestamps) --}}
                        @php
                            $raw_structure = $withdrawal->getAttributes()['structured_data'] ?? null;
                            $details = is_array($raw_structure) ? $raw_structure : (is_string($raw_structure) ? json_decode($raw_structure, true) : null);

                            // Formatter helper for values & timestamps
                            $formatValue = function ($key, $val) {
                                if (is_null($val)) return ['display' => '—', 'is_date' => false, 'copyable' => false];
                                if (is_array($val)) return ['display' => json_encode($val), 'is_date' => false, 'copyable' => false];

                                $keyLower = strtolower($key);

                                // Check if timestamp or time
                                if (str_contains($keyLower, 'timestamp') || str_contains($keyLower, 'time') || str_contains($keyLower, 'date')) {
                                    if (is_numeric($val)) {
                                        $num = (float) $val;
                                        $ts = $num > 10000000000 ? (int) round($num / 1000) : (int) $num;
                                        if ($ts > 0 && $ts < 4102444800) {
                                            $carbonDate = \Carbon\Carbon::createFromTimestamp($ts);
                                            return [
                                                'display' => $carbonDate->format('M d, Y H:i:s') . ' UTC',
                                                'sub' => $carbonDate->diffForHumans(),
                                                'is_date' => true,
                                                'copyable' => true,
                                                'raw' => (string) $val
                                            ];
                                        }
                                    }
                                }

                                return [
                                    'display' => (string) $val,
                                    'is_date' => false,
                                    'copyable' => (strlen((string) $val) > 6),
                                    'raw' => (string) $val
                                ];
                            };
                        @endphp

                        @if ($details && is_array($details))
                            <div class="pt-6 border-t border-white/[0.06] space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-mono font-bold text-slate-300 uppercase tracking-widest flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                        <span>{{ __('Raw Payout Data') }}</span>
                                    </h4>
                                    <span class="text-[10px] font-mono text-cyan-400/70 uppercase tracking-wider">{{ __('Verified Event') }}</span>
                                </div>

                                <div class="space-y-4 font-mono">
                                    @foreach ($details as $key => $value)
                                        @continue($key === 'transaction_hash' || is_null($value))
                                        
                                        @if (is_array($value))
                                            {{-- Nested Category Group (e.g. crypto) --}}
                                            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/[0.08] space-y-3">
                                                <div class="flex items-center gap-2 pb-2 border-b border-white/[0.04]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                                                    <span class="text-xs font-bold text-white uppercase tracking-wider">
                                                        {{ ucwords(str_replace('_', ' ', $key)) }}
                                                    </span>
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    @foreach ($value as $subKey => $subVal)
                                                        @continue(is_null($subVal))
                                                        @php $formatted = $formatValue($subKey, $subVal); @endphp
                                                        <div class="p-3.5 bg-black/40 rounded-xl border border-white/[0.04] space-y-1">
                                                            <span class="text-[10px] text-slate-400 uppercase tracking-widest block font-bold">
                                                                {{ ucwords(str_replace('_', ' ', $subKey)) }}
                                                            </span>
                                                            <div class="flex items-center justify-between gap-2">
                                                                <div>
                                                                    <span class="text-white text-xs font-semibold break-all select-all {{ $formatted['is_date'] ? 'text-cyan-300' : '' }}">
                                                                        {{ $formatted['display'] }}
                                                                    </span>
                                                                    @if (!empty($formatted['sub']))
                                                                        <span class="text-[10px] text-slate-500 block">({{ $formatted['sub'] }})</span>
                                                                    @endif
                                                                </div>
                                                                @if ($formatted['copyable'])
                                                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $formatted['raw'] ?? $formatted['display'] }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Copied') }}', showConfirmButton:false, timer:1500});"
                                                                        class="p-1 text-slate-400 hover:text-cyan-300 transition-colors cursor-pointer shrink-0" title="{{ __('Copy value') }}">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            {{-- Flat Key-Value Item --}}
                                            @php $formatted = $formatValue($key, $value); @endphp
                                            <div class="p-3.5 bg-white/[0.02] rounded-xl border border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                <div>
                                                    <span class="text-[10px] text-slate-400 uppercase tracking-widest font-bold block mb-0.5">
                                                        {{ ucwords(str_replace('_', ' ', $key)) }}
                                                    </span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-white text-xs font-semibold break-all select-all {{ $formatted['is_date'] ? 'text-cyan-300 font-bold' : '' }}">
                                                            {{ $formatted['display'] }}
                                                        </span>
                                                        @if (!empty($formatted['sub']))
                                                            <span class="text-[10px] text-slate-500">({{ $formatted['sub'] }})</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if ($formatted['copyable'])
                                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $formatted['raw'] ?? $formatted['display'] }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'{{ __('Copied to clipboard') }}', showConfirmButton:false, timer:1500});"
                                                        class="self-start sm:self-center px-3 py-1 bg-white/[0.04] hover:bg-cyan-500/10 text-slate-300 hover:text-cyan-300 rounded-lg text-[10px] uppercase font-bold border border-white/[0.08] transition-all cursor-pointer flex items-center gap-1.5">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                                        <span>{{ __('Copy') }}</span>
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Right: Timeline & Actions (1 col) --}}
            <div class="space-y-6">

                {{-- Timeline --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] relative overflow-hidden">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6">
                        <h3 class="text-xs font-bold text-white font-mono uppercase tracking-wide mb-6 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                            <span>{{ __('Timeline') }}</span>
                        </h3>

                        <div class="relative pl-6 border-l border-white/10 ml-2 space-y-6 font-mono">
                            {{-- Step 1: Requested --}}
                            <div class="relative">
                                <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f5ff]"></div>
                                <span class="text-[10px] text-slate-500 uppercase tracking-widest font-bold block">{{ __('Withdrawal Requested') }}</span>
                                <p class="text-white font-bold text-xs mt-1">{{ $withdrawal->created_at->format('M d, Y') }}</p>
                                <p class="text-slate-400 text-[10px]">{{ $withdrawal->created_at->format('H:i:s') }} UTC</p>
                            </div>

                            {{-- Step 2: Current Status / Processed --}}
                            <div class="relative">
                                @php
                                    $dotColor = $withdrawal->status === 'completed' 
                                        ? 'bg-emerald-400 shadow-[0_0_8px_#10b981]' 
                                        : ($withdrawal->status === 'failed' ? 'bg-rose-400 shadow-[0_0_8px_#f43f5e]' : 'bg-amber-400 shadow-[0_0_8px_#f59e0b] animate-pulse');
                                @endphp
                                <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full {{ $dotColor }}"></div>
                                <span class="text-[10px] text-slate-500 uppercase tracking-widest font-bold block">{{ __('Current Status') }}</span>
                                <p class="text-white font-bold text-xs mt-1">{{ $withdrawal->updated_at->format('M d, Y') }}</p>
                                <p class="text-slate-400 text-[10px]">{{ $withdrawal->updated_at->format('H:i:s') }} UTC</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Danger Zone: Delete Record --}}
                <div class="p-2 rounded-[2.5rem] bg-rose-500/5 border border-rose-500/20 backdrop-blur-xl relative overflow-hidden group">
                    <div class="rounded-[2rem] bg-[#090c14]/90 p-5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-400 border border-rose-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-white font-mono font-bold text-xs uppercase tracking-wide">{{ __('Delete Withdrawal') }}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ __('Permanently delete this withdrawal record') }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-delete-withdrawal px-4 py-2 rounded-full bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white font-mono font-bold text-xs uppercase tracking-wider transition-all cursor-pointer"
                            data-id="{{ $withdrawal->id }}">
                            {{ __('Delete') }}
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- 1. Edit / Process Status Modal --}}
    <div id="edit-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-[#05070d]/80 backdrop-blur-xl transition-opacity modal-close"></div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.9)] relative w-full max-w-md transform transition-all text-left">
                <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8 relative">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/[0.06]">
                        <div>
                            <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide">{{ __('Update Withdrawal Status') }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('Change the status of this withdrawal.') }}</p>
                        </div>
                        <button type="button" class="w-8 h-8 rounded-full bg-white/[0.04] text-slate-400 hover:text-white transition-colors modal-close flex items-center justify-center cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <form id="edit-status-form" class="space-y-6">
                        @csrf
                        <input type="hidden" name="withdrawal_id" id="edit-withdrawal-id">

                        <div>
                            <label class="block text-xs font-mono font-bold text-slate-400 uppercase tracking-widest mb-2">
                                {{ __('New Status') }}
                            </label>
                            <div class="relative custom-dropdown" id="edit-status-dropdown">
                                <button type="button"
                                    class="dropdown-btn w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl bg-white/[0.03] border border-white/[0.1] text-white text-xs font-mono focus:border-cyan-400 transition-all cursor-pointer">
                                    <span class="selected-label font-bold text-cyan-300">{{ __('Select Status') }}</span>
                                    <svg class="w-4 h-4 text-slate-500 dropdown-icon transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <input type="hidden" name="status" id="edit-status-value">
                                <div class="dropdown-menu absolute z-[120] mt-2 w-full bg-[#0b0e17] border border-white/[0.1] rounded-2xl shadow-2xl py-2 hidden animate-in fade-in slide-in-from-top-2 duration-200 font-mono text-xs">
                                    <div class="dropdown-option px-4 py-3 text-emerald-400 hover:bg-emerald-500/10 transition-colors cursor-pointer font-bold flex items-center gap-2" data-value="completed">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        <span>{{ __('Completed (Approve Payout)') }}</span>
                                    </div>
                                    <div class="dropdown-option px-4 py-3 text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer font-bold flex items-center gap-2" data-value="failed">
                                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                        <span>{{ __('Failed (Reject & Refund)') }}</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-500 font-mono mt-2 leading-relaxed">
                                {{ __('Selecting Completed will execute automated blockchain dispatch from the network master wallet.') }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-white/[0.06] flex gap-3 font-mono">
                            <button type="button"
                                class="flex-1 px-4 py-2.5 rounded-full border border-white/[0.1] text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-white/[0.05] transition-all modal-close cursor-pointer">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-full bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(0,245,255,0.3)] transition-all cursor-pointer">
                                {{ __('Update Status') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Delete Confirmation Modal --}}
    <div id="delete-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="fixed inset-0 bg-[#05070d]/80 backdrop-blur-xl transition-opacity modal-close"></div>

            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-rose-500/20 backdrop-blur-2xl shadow-[0_20px_60px_rgba(0,0,0,0.9)] relative w-full max-w-md transform transition-all text-center">
                <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8">
                    <div class="w-14 h-14 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>

                    <h3 class="text-lg font-bold text-white font-mono uppercase tracking-wide mb-2">{{ __('Delete Withdrawal') }}</h3>
                    <p class="text-xs text-slate-400 mb-6 leading-relaxed">
                        {{ __('Are you sure you want to delete this withdrawal record? This action cannot be undone.') }}
                    </p>

                    <div class="flex gap-3 font-mono">
                        <input type="hidden" id="delete-withdrawal-id">
                        <button type="button"
                            class="flex-1 px-4 py-2.5 rounded-full border border-white/[0.1] text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-white/[0.05] transition-all modal-close cursor-pointer">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" id="confirm-delete"
                            class="flex-1 px-4 py-2.5 rounded-full bg-rose-500 hover:bg-rose-400 text-white font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(244,63,94,0.3)] transition-all cursor-pointer">
                            {{ __('Confirm Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Dropdowns
            $(document).on('click', '.dropdown-btn', function(e) {
                e.stopPropagation();
                $('.dropdown-menu').not($(this).siblings('.dropdown-menu')).addClass('hidden');
                $('.dropdown-icon').not($(this).find('.dropdown-icon')).removeClass('rotate-180');

                const menu = $(this).siblings('.dropdown-menu');
                const icon = $(this).find('.dropdown-icon');

                menu.toggleClass('hidden');
                icon.toggleClass('rotate-180');
            });

            $(document).on('click', function() {
                $('.dropdown-menu').addClass('hidden');
                $('.dropdown-icon').removeClass('rotate-180');
            });

            // Modals
            function openModal(id) {
                const $modal = $(`#${id}`);
                $modal.removeClass('hidden');
                setTimeout(() => {
                    $modal.find('.transform').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
                }, 10);
            }

            function closeModal(id) {
                const $modal = $(`#${id}`);
                $modal.find('.transform').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
                setTimeout(() => {
                    $modal.addClass('hidden');
                }, 200);
            }

            $('.modal-close').on('click', function() {
                const modalId = $(this).closest('.fixed.inset-0.z-\\[100\\]').attr('id');
                closeModal(modalId);
            });

            // Status Update
            $(document).on('click', '.btn-edit-status', function() {
                const id = $(this).data('id');
                const status = $(this).data('status');

                $('#edit-withdrawal-id').val(id);
                $('#edit-status-value').val(status);

                let statusLabel = status.charAt(0).toUpperCase() + status.slice(1);
                $('#edit-status-dropdown .selected-label').text(statusLabel);

                openModal('edit-modal');
            });

            $('#edit-status-dropdown .dropdown-option').on('click', function() {
                const val = $(this).data('value');
                const label = $(this).text().trim();

                $('#edit-status-value').val(val);
                $('#edit-status-dropdown .selected-label').text(label);
            });

            $('#edit-status-form').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit-withdrawal-id').val();
                const status = $('#edit-status-value').val();

                if (!status) {
                    Swal.fire({toast:true, position:'top-end', icon:'error', title:'{{ __('Please select a target status') }}', showConfirmButton:false, timer:2000});
                    return;
                }

                const $submitBtn = $(this).find('button[type="submit"]');
                const originalText = $submitBtn.text();

                $submitBtn.text('{{ __('Processing...') }}').prop('disabled', true);

                $.ajax({
                    url: `{{ url('admin/withdrawals/edit') }}/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(res) {
                        if (res.success || res.status === 'success') {
                            Swal.fire({toast:true, position:'top-end', icon:'success', title: res.message || 'Withdrawal processed successfully', showConfirmButton:false, timer:2500});
                            closeModal('edit-modal');
                            $submitBtn.text(originalText).prop('disabled', false);

                            $.ajax({
                                url: window.location.href,
                                type: 'GET',
                                success: function(html) {
                                    $('#withdrawal-view-content').html($(html).find('#withdrawal-view-content').html());
                                }
                            });
                        } else {
                            Swal.fire({toast:true, position:'top-end', icon:'error', title: res.message || 'An error occurred', showConfirmButton:false, timer:2500});
                            $submitBtn.text(originalText).prop('disabled', false);
                        }
                    },
                    error: function(err) {
                        let error = err.responseJSON?.message || 'An error occurred';
                        Swal.fire({toast:true, position:'top-end', icon:'error', title: error, showConfirmButton:false, timer:3000});
                        $submitBtn.text(originalText).prop('disabled', false);
                    }
                });
            });

            // Delete
            $(document).on('click', '.btn-delete-withdrawal', function() {
                const id = $(this).data('id');
                $('#delete-withdrawal-id').val(id);
                openModal('delete-modal');
            });

            $('#confirm-delete').on('click', function() {
                const id = $('#delete-withdrawal-id').val();
                const $btn = $(this);
                const originalText = $btn.text();

                $btn.text('{{ __('Deleting...') }}').prop('disabled', true);

                $.ajax({
                    url: `{{ url('admin/withdrawals/delete') }}/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            window.location.href = "{{ route('admin.withdrawals.index') }}";
                        } else {
                            Swal.fire({toast:true, position:'top-end', icon:'error', title: res.message || 'An error occurred', showConfirmButton:false, timer:2000});
                            $btn.text(originalText).prop('disabled', false);
                        }
                    },
                    error: function(err) {
                        let error = err.responseJSON?.message || 'An error occurred';
                        Swal.fire({toast:true, position:'top-end', icon:'error', title: error, showConfirmButton:false, timer:2000});
                        $btn.text(originalText).prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
