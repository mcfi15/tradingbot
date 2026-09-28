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
                    {{ __('SEO & SOCIAL LINKS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('SEO & Social Media Settings') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Configure search engine indexing, metadata, social preview cards, and social media links') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="$('#seo-settings-form').submit()"
                    class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </div>

        {{-- ==================================================================================== --}}
        {{-- MAIN LAYOUT: SIDEBAR + CONTENT --}}
        {{-- ==================================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Navigation Deck --}}
            <div class="lg:col-span-4 xl:col-span-3">
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        @include("templates.$template.blades.admin.settings.partials.sidebar")
                    </div>
                </div>
            </div>

            {{-- Right Content Chassis --}}
            <div class="lg:col-span-8 xl:col-span-9 space-y-6">
                <form id="seo-settings-form" action="{{ route('admin.settings.seo.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-10 text-xs">
                            
                            {{-- SECTION 1: SEARCH ENGINE INDEXING POLICY --}}
                            <div class="space-y-6">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            01
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Search Engine Settings') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Control search engine crawling and set default search result titles and descriptions.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex items-center justify-between">
                                    <div class="space-y-0.5">
                                        <span class="text-white font-bold block">{{ __('Allow Search Engine Indexing') }}</span>
                                        <span class="text-[10px] text-slate-400">{{ __('Allow Google, Bing, and other search engines to index public website pages.') }}</span>
                                    </div>
                                    <input type="checkbox" name="search_engine_indexing" value="1" class="accent-cyan-400 w-4 h-4 cursor-pointer"
                                        {{ $seo['search_engine_indexing'] ? 'checked' : '' }}>
                                </div>

                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Meta Description') }}</label>
                                        <textarea name="seo_description" rows="3" placeholder="{{ __('Brief summary of your platform for search engine results...') }}"
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none resize-none">{{ $seo['description'] }}</textarea>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Meta Keywords') }}</label>
                                        <input type="text" name="seo_keywords" value="{{ $seo['keywords'] }}" placeholder="crypto, trading, bot trading, copy trading, quant"
                                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 2: OPENGRAPH SOCIAL GRAPH --}}
                            <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            02
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Social Media Preview Cards (OpenGraph)') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Customize how links appear when shared on Twitter/X, Telegram, Facebook, and messaging apps.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <div class="space-y-2">
                                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Social Title') }}</label>
                                            <input type="text" name="social_title" value="{{ $seo['social_title'] }}" placeholder="Foyana · Automated Trading Platform"
                                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                        </div>

                                        <div class="space-y-2">
                                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Social Description') }}</label>
                                            <textarea name="social_description" rows="4" placeholder="Automated bot trading and professional copy trading platform."
                                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none resize-none">{{ $seo['social_description'] }}</textarea>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Social Preview Image (1200x630)') }}</label>
                                        <div class="h-44 rounded-2xl bg-[#05070d] border border-white/[0.08] p-3 flex items-center justify-center overflow-hidden relative group">
                                            @if ($seo['seo_image'])
                                                <img src="{{ asset('assets/images/' . $seo['seo_image']) }}" id="preview-seo-image" class="max-h-full max-w-full object-cover rounded-xl">
                                            @else
                                                <div id="seo-placeholder" class="text-center text-slate-500 text-xs">
                                                    {{ __('No Preview Image Uploaded') }}
                                                </div>
                                                <img id="preview-seo-image" class="max-h-full max-w-full object-cover rounded-xl hidden">
                                            @endif
                                        </div>
                                        <input type="file" name="seo_image" id="seo_image_input" class="hidden">
                                        <button type="button" onclick="$('#seo_image_input').click()"
                                            class="w-full py-2.5 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white border border-white/[0.08] font-bold uppercase text-[10px] transition-all cursor-pointer">
                                            {{ __('Upload Preview Image') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 3: SOCIAL MEDIA LINKS --}}
                            <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            03
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Social Media Links') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Enter your official social media profile links for platform footers and community pages.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach ($seo['social_media'] as $platform => $url)
                                        <div class="space-y-1">
                                            <label class="text-[9px] uppercase font-bold text-slate-400">{{ ucwords(str_replace('_', ' ', $platform)) }}</label>
                                            <input type="text" name="social_media[{{ $platform }}]" value="{{ $url }}" placeholder="https://{{ $platform }}.com/..."
                                                class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- SUBMIT BUTTON --}}
                            <div class="pt-6 border-t border-white/[0.06] flex items-center justify-end">
                                <button type="submit" id="submit-btn"
                                    class="px-8 py-3.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-xs tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4 submit-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="btn-text">{{ __('Save Changes') }}</span>
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#seo_image_input').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        $('#seo-placeholder').hide();
                        $('#preview-seo-image').attr('src', evt.target.result).removeClass('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#seo-settings-form').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const $btn = $('#submit-btn');
                $btn.prop('disabled', true).addClass('opacity-50');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message || '{{ __('SEO settings updated.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to save SEO settings.') }}',
                            customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).removeClass('opacity-50');
                    }
                });
            });
        });
    </script>
@endpush
