@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12 font-mono">
        
        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('ACCOUNT SETTINGS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Admin Profile') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Manage your administrator profile details, username, email, and language preferences') }}
                </p>
            </div>

            {{-- Quick ID Pill --}}
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 rounded-2xl bg-white/[0.02] border border-white/[0.08] text-right">
                    <span class="text-[9px] uppercase font-bold text-slate-500 block">{{ __('Logged In As') }}</span>
                    <span class="text-xs font-black text-white">@<span>{{ $admin->username }}</span></span>
                </div>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- MAIN GRID LAYOUT --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Navigation Deck --}}
            <div class="lg:col-span-4 xl:col-span-3">
                @include("templates.$template.blades.admin.account.partials.sidebar")

                {{-- Account Metadata Card --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl mt-6">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 space-y-4 text-xs">
                        <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                            <span class="text-slate-500 uppercase text-[10px] font-bold">{{ __('Account Role') }}</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase">
                                {{ __('Administrator') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                            <span class="text-slate-500 uppercase text-[10px] font-bold">{{ __('Account ID') }}</span>
                            <span class="text-white font-bold">#{{ $admin->id }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 uppercase text-[10px] font-bold">{{ __('Member Since') }}</span>
                            <span class="text-slate-400">{{ $admin->created_at ? $admin->created_at->format('M d, Y') : 'Genesis' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Main Form Panel --}}
            <div class="lg:col-span-8 xl:col-span-9">
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-8">
                        
                        <form id="profile-update-form" action="{{ route('admin.account.profile.update') }}" method="POST"
                            enctype="multipart/form-data" class="space-y-8">
                            @csrf

                            {{-- Avatar Section --}}
                            <div class="pb-8 border-b border-white/[0.06]">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-4">
                                    {{ __('Profile Photo') }}
                                </label>
                                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                                    <div class="relative group cursor-pointer" onclick="$('#image-upload').click()">
                                        <div class="w-24 h-24 rounded-3xl overflow-hidden border-2 border-white/10 group-hover:border-cyan-400 transition-all bg-[#05070d] flex items-center justify-center shadow-[0_0_20px_rgba(0,0,0,0.5)]">
                                            @if ($admin->image)
                                                <img id="image-preview" src="{{ asset('storage/profile/' . $admin->image) }}" class="w-full h-full object-cover">
                                            @else
                                                <div id="image-placeholder" class="w-full h-full bg-gradient-to-br from-cyan-600/30 to-purple-600/30 flex items-center justify-center text-white text-3xl font-black">
                                                    {{ substr($admin->name, 0, 1) }}
                                                </div>
                                                <img id="image-preview" class="w-full h-full object-cover hidden">
                                            @endif
                                            
                                            {{-- Hover Overlay --}}
                                            <div class="absolute inset-0 bg-[#05070d]/80 backdrop-blur-xs flex flex-col items-center justify-center text-cyan-400 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="text-[8px] font-bold uppercase tracking-wider mt-1">{{ __('Change') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5 text-center sm:text-left">
                                        <h4 class="text-xs font-black text-white uppercase tracking-wider">{{ __('Profile Photo') }}</h4>
                                        <p class="text-[11px] text-slate-400">{{ __('Supported formats: JPG, PNG, WebP. Recommended size: 512x512px.') }}</p>
                                        <button type="button" onclick="$('#image-upload').click()"
                                            class="mt-2 px-3 py-1.5 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white border border-white/[0.08] text-[10px] font-bold uppercase tracking-wider transition-all cursor-pointer">
                                            {{ __('Upload Photo') }}
                                        </button>
                                        <input type="file" id="image-upload" name="image" class="hidden" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            {{-- Input Fields --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ __('Full Name') }} <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" name="name" value="{{ $admin->name }}" required
                                        class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-colors"
                                        placeholder="{{ __('Enter your full name') }}">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ __('Username') }} <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" name="username" value="{{ $admin->username }}" required
                                        class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-colors"
                                        placeholder="{{ __('Enter username') }}">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ __('Email Address') }} <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="email" name="email" value="{{ $admin->email }}" required
                                        class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-colors"
                                        placeholder="{{ __('admin@example.com') }}">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ __('Language') }} <span class="text-rose-400">*</span>
                                    </label>
                                    <select name="lang" required
                                        class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none transition-colors">
                                        @foreach (config('languages') as $code => $lang)
                                            <option value="{{ $code }}" {{ $admin->lang == $code ? 'selected' : '' }} class="bg-[#090c14] text-white">
                                                {{ $lang['name'] }} ({{ strtoupper($code) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="pt-6 border-t border-white/[0.06] flex items-center justify-end">
                                <button type="submit" id="submit-btn"
                                    class="px-8 py-3.5 bg-cyan-400 hover:bg-cyan-300 text-[#050507] text-xs font-black uppercase tracking-wider rounded-xl shadow-[0_0_25px_rgba(0,245,255,0.3)] hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-3 cursor-pointer">
                                    <svg class="w-4 h-4 submit-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg class="w-4 h-4 hidden loading-icon animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="btn-text">{{ __('Save Changes') }}</span>
                                </button>
                            </div>

                        </form>
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
            // Instant Image Preview
            $('#image-upload').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#image-preview').attr('src', e.target.result).removeClass('hidden');
                        $('#image-placeholder').addClass('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // AJAX Profile Update
            $('#profile-update-form').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $btn = $('#submit-btn');
                const $btnText = $btn.find('.btn-text');
                const $submitIcon = $btn.find('.submit-icon');
                const $loadingIcon = $btn.find('.loading-icon');
                const originalText = $btnText.text();

                const formData = new FormData(this);

                $btn.prop('disabled', true).addClass('opacity-50');
                $btnText.text('{{ __('Saving...') }}');
                $submitIcon.addClass('hidden');
                $loadingIcon.removeClass('hidden');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message || '{{ __('Profile updated successfully.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = '{{ __('Something went wrong.') }}';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Validation Error') }}',
                            html: errorMessage,
                            customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).removeClass('opacity-50');
                        $btnText.text(originalText);
                        $submitIcon.removeClass('hidden');
                        $loadingIcon.addClass('hidden');
                    }
                });
            });
        });
    </script>
@endpush
