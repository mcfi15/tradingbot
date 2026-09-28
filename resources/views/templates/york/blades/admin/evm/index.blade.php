@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8">
        {{-- Header --}}
        <div>
            <h2 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-indigo-200 to-indigo-400 tracking-tight leading-tight">
                {{ __(':blockchain Master Wallet', ['blockchain' => $blockchain->name]) }}
            </h2>
            <p class="text-indigo-200/60 font-mono text-[9px] md:text-xs mt-0.5 tracking-widest uppercase">
                {{ __('Configure and monitor the platform\'s primary sweep destination') }}
            </p>
        </div>

        {{-- Dynamic EVM Master Wallet Setup Panel --}}
        @include('templates.york.blades.admin.partials.evm_master_wallet')
    </div>
@endsection
