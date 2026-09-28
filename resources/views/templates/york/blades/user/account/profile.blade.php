@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
<div class="min-h-screen relative space-y-12 pb-24">

    {{-- Global Ethereal Ambient Mesh Gradients --}}
    <div class="fixed top-0 right-0 w-[50rem] h-[50rem] bg-gradient-to-br from-accent-primary/10 via-purple-600/5 to-transparent rounded-full blur-[160px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[40rem] h-[40rem] bg-gradient-to-tr from-emerald-500/5 via-cyan-500/5 to-transparent rounded-full blur-[140px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-10">

        {{-- ══ HEADER HERO DECK WITH INTEGRATED TAB SWITCHER ═══════════════════ --}}
        <div class="relative rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.8)] backdrop-blur-2xl">
            <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-12 overflow-hidden relative"
                 style="background: linear-gradient(135deg, rgba(8,9,14,0.98) 0%, rgba(3,4,7,0.99) 100%);">
                
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-accent-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div class="max-w-2xl space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-accent-primary/30 bg-accent-primary/10 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-accent-primary shadow-[0_0_15px_rgba(226,177,60,0.2)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse"></span>
                            <span>{{ __('Account Settings') }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-400 tracking-tight leading-none">
                            {{ __('Profile Settings') }}
                        </h1>

                        <p class="text-slate-400 text-sm sm:text-base font-normal leading-relaxed">
                            {{ __('Manage your personal details, profile picture, and language preferences.') }}
                        </p>
                    </div>

                    {{-- Top Segmented Navigation Bar --}}
                    <div class="shrink-0">
                        @include('templates.york.blades.user.account.partials.sidebar')
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ FULL-WIDTH PROFILE DESK ════════════════════════════════════════ --}}
        <form id="profile-update-form" action="{{ route('user.account.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- Left Column: Avatar & Account Status Badge (4 Cols) --}}
                <div class="lg:col-span-4 space-y-8">
                    {{-- Avatar Upload Card --}}
                    <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl overflow-hidden">
                        <div class="rounded-[calc(2.5rem-0.375rem)] p-8 text-center space-y-6" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 block">{{ __('Profile Photo') }}</span>
                            
                            <div class="relative w-36 h-36 mx-auto rounded-[2rem] overflow-hidden border-2 border-accent-primary/40 p-1 bg-black/60 shadow-[0_0_30px_rgba(226,177,60,0.2)] group">
                                @if ($user->photo)
                                    <img id="image-preview" src="{{ asset('storage/profile/' . $user->photo) }}" class="w-full h-full object-cover rounded-[1.75rem]">
                                @else
                                    <div id="image-placeholder" class="w-full h-full bg-gradient-to-br from-accent-primary/30 to-purple-600/20 rounded-[1.75rem] flex items-center justify-center text-accent-primary font-black text-4xl">
                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                    </div>
                                    <img id="image-preview" class="w-full h-full object-cover rounded-[1.75rem] hidden">
                                @endif

                                <label for="image-upload" class="absolute inset-0 bg-black/75 backdrop-blur-sm flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all cursor-pointer">
                                    <svg class="w-8 h-8 text-accent-primary mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="text-[9px] font-black uppercase text-white tracking-widest">{{ __('Change Photo') }}</span>
                                </label>
                            </div>

                            <div class="space-y-2">
                                <h4 class="text-white font-black text-base">{{ $user->first_name }} {{ $user->last_name }}</h4>
                                <p class="text-xs text-slate-400 font-mono">{{ $user->email }}</p>
                                <input type="file" id="image-upload" name="photo" class="hidden" accept="image/*" onchange="previewImage(this)">
                                <label for="image-upload" class="inline-block px-5 py-2 rounded-full bg-white/5 hover:bg-white/10 text-white font-bold text-xs uppercase tracking-wider border border-white/10 transition-all cursor-pointer">
                                    {{ __('Upload Photo') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Account Status Overview Card --}}
                    <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl overflow-hidden">
                        <div class="rounded-[calc(2.5rem-0.375rem)] p-6 space-y-4" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 block">{{ __('Account Status') }}</span>
                            
                            <div class="space-y-3 text-xs">
                                <div class="flex justify-between items-center py-2 border-b border-white/5">
                                    <span class="text-slate-400 font-medium">{{ __('Email Status') }}</span>
                                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-black text-[9px] uppercase tracking-widest">{{ __('Verified') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-white/5">
                                    <span class="text-slate-400 font-medium">{{ __('Member Since') }}</span>
                                    <span class="text-white font-bold font-mono">{{ $user->created_at ? $user->created_at->format('M Y') : 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-slate-400 font-medium">{{ __('Account Role') }}</span>
                                    <span class="text-accent-primary font-bold font-mono uppercase">{{ __('Investor') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Personal Information Form (8 Cols) --}}
                <div class="lg:col-span-8">
                    <div class="rounded-[2.5rem] p-1.5 bg-white/[0.03] border border-white/8 shadow-2xl overflow-hidden">
                        <div class="rounded-[calc(2.5rem-0.375rem)] p-8 sm:p-10 space-y-8" style="background: linear-gradient(145deg, rgba(10,12,18,0.96) 0%, rgba(5,6,10,0.99) 100%);">
                            
                            <div class="pb-4 border-b border-white/5">
                                <h3 class="text-xl font-black text-white tracking-tight">{{ __('Personal Information') }}</h3>
                                <p class="text-xs text-slate-400 font-medium mt-1">{{ __('Update your profile and language settings.') }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @php
                                    $hasUsername = !empty($user->getRawOriginal('username'));
                                @endphp

                                {{-- Username --}}
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 block">{{ __('Username') }}</label>
                                    <input type="text" name="username" value="{{ $user->getRawOriginal('username') }}"
                                           class="w-full bg-black/50 border border-white/10 rounded-full px-6 py-3.5 text-xs text-white outline-none focus:border-accent-primary transition-all font-mono {{ $hasUsername ? 'opacity-60 cursor-not-allowed' : '' }}"
                                           placeholder="{{ __('Set your username') }}" {{ $hasUsername ? 'readonly' : '' }}>
                                    @if($hasUsername)
                                        <p class="text-[10px] text-slate-500 font-mono italic pl-2">{{ __('Username cannot be changed.') }}</p>
                                    @endif
                                </div>

                                {{-- First Name --}}
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 block">{{ __('First Name') }}</label>
                                    <input type="text" value="{{ $user->getRawOriginal('first_name') }}" readonly
                                           class="w-full bg-black/50 border border-white/10 rounded-full px-6 py-3.5 text-xs text-white opacity-60 cursor-not-allowed outline-none">
                                </div>

                                {{-- Last Name --}}
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 block">{{ __('Last Name') }}</label>
                                    <input type="text" value="{{ $user->getRawOriginal('last_name') }}" readonly
                                           class="w-full bg-black/50 border border-white/10 rounded-full px-6 py-3.5 text-xs text-white opacity-60 cursor-not-allowed outline-none">
                                </div>

                                {{-- Email Address --}}
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 block">{{ __('Email Address') }}</label>
                                    <input type="email" value="{{ $user->getRawOriginal('email') }}" readonly
                                           class="w-full bg-black/50 border border-white/10 rounded-full px-6 py-3.5 text-xs text-white opacity-60 cursor-not-allowed outline-none font-mono">
                                </div>

                                {{-- Preferred Language --}}
                                <div class="space-y-2 md:col-span-2">
                                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 block">{{ __('Language') }}</label>
                                    <select name="lang" class="w-full bg-black/50 border border-white/10 rounded-full px-6 py-3.5 text-xs text-white outline-none focus:border-accent-primary transition-all">
                                        @foreach (config('languages') as $code => $lang)
                                            <option value="{{ $code }}" {{ $user->lang == $code ? 'selected' : '' }}>
                                                {{ $lang['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Save CTA --}}
                            <div class="pt-6 border-t border-white/5 flex justify-end">
                                <button type="submit" class="px-8 py-3.5 rounded-full bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(226,177,60,0.3)] active:scale-[0.98] cursor-pointer">
                                    {{ __('Save Changes') }}
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('image-placeholder');
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
