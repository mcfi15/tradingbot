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
                    {{ __('LEGAL & CERTIFICATES') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Certificates & Regulatory Bodies') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Manage regulatory bodies and upload legal certificates or compliance documents') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="$('#certificate-settings-form').submit()"
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
                <form id="certificate-settings-form" action="{{ route('admin.settings.certificate.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                        <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-10 text-xs">
                            
                            {{-- SECTION 1: REGULATORS --}}
                            <div class="space-y-6">
                                <div class="pb-3 border-b border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            01
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Regulatory Authorities') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('List financial authorities or regulatory badges displayed on your website.') }}</p>
                                        </div>
                                    </div>
                                    <button type="button" id="add-regulator-btn"
                                        class="px-4 py-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/20 font-bold uppercase text-[10px] tracking-wider transition-all flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>{{ __('Add Authority') }}</span>
                                    </button>
                                </div>

                                <div id="regulators-container" class="space-y-3">
                                    @foreach ($compliance['regulators'] as $regulator)
                                        <div class="regulator-item flex items-center gap-3">
                                            <input type="text" name="regulators[]" value="{{ $regulator }}" placeholder="{{ __('e.g. SEC, FCA, CySEC...') }}"
                                                class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                            <button type="button" class="remove-regulator-btn p-3 rounded-xl bg-white/[0.02] hover:bg-rose-500/20 text-slate-500 hover:text-rose-400 border border-white/[0.06] transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                    @if (empty($compliance['regulators']))
                                        <div class="regulator-item flex items-center gap-3">
                                            <input type="text" name="regulators[]" placeholder="{{ __('e.g. SEC, FCA, CySEC...') }}"
                                                class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- SECTION 2: PDF CERTIFICATES --}}
                            <div class="space-y-6 pt-6 border-t border-white/[0.06]">
                                <div class="pb-3 border-b border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold">
                                            02
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Legal Documents (PDF)') }}</h3>
                                            <p class="text-[10px] text-slate-400">{{ __('Upload certificates of incorporation, licenses, or compliance documents for public download.') }}</p>
                                        </div>
                                    </div>
                                    <button type="button" id="add-pdf-btn"
                                        class="px-4 py-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/20 font-bold uppercase text-[10px] tracking-wider transition-all flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>{{ __('Add Document') }}</span>
                                    </button>
                                </div>

                                <div id="pdf-container" class="space-y-4">
                                    @foreach ($compliance['pdf_certificates'] as $index => $cert)
                                        <div class="pdf-item p-5 rounded-2xl bg-white/[0.02] border border-white/[0.06] space-y-4 relative group">
                                            <button type="button" class="remove-pdf-btn absolute top-4 right-4 text-slate-500 hover:text-rose-400 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="space-y-1">
                                                    <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Document Name') }}</label>
                                                    <input type="text" name="pdf_names[{{ $index }}]" value="{{ $cert['name'] }}" placeholder="{{ __('Certificate of Incorporation') }}"
                                                        class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                                </div>

                                                <div class="space-y-1">
                                                    <div class="flex items-center justify-between">
                                                        <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Upload PDF File') }}</label>
                                                        @if ($cert['file'])
                                                            <a href="{{ asset('assets/pdf/' . $cert['file']) }}" target="_blank" class="text-cyan-400 text-[9px] font-bold hover:underline">
                                                                {{ __('View Current PDF') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <input type="file" name="pdf_files[{{ $index }}]" class="w-full bg-[#05070d] border border-white/[0.08] text-slate-400 rounded-xl px-4 py-2.5 text-xs">
                                                </div>
                                            </div>
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
            $('#add-regulator-btn').on('click', function() {
                const html = `
                    <div class="regulator-item flex items-center gap-3 animate__animated animate__fadeIn">
                        <input type="text" name="regulators[]" placeholder="{{ __('e.g. SEC, FCA, CySEC...') }}"
                            class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                        <button type="button" class="remove-regulator-btn p-3 rounded-xl bg-white/[0.02] hover:bg-rose-500/20 text-slate-500 hover:text-rose-400 border border-white/[0.06] transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                `;
                $('#regulators-container').append(html);
            });

            $(document).on('click', '.remove-regulator-btn', function() {
                $(this).closest('.regulator-item').remove();
            });

            let pdfCount = {{ count($compliance['pdf_certificates']) }};
            $('#add-pdf-btn').on('click', function() {
                const html = `
                    <div class="pdf-item p-5 rounded-2xl bg-white/[0.02] border border-white/[0.06] space-y-4 relative group animate__animated animate__fadeIn">
                        <button type="button" class="remove-pdf-btn absolute top-4 right-4 text-slate-500 hover:text-rose-400 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Document Name') }}</label>
                                <input type="text" name="pdf_names[${pdfCount}]" placeholder="{{ __('Certificate of Incorporation') }}"
                                    class="w-full bg-[#05070d] border border-white/[0.08] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] uppercase font-bold text-slate-400">{{ __('Upload PDF File') }}</label>
                                <input type="file" name="pdf_files[${pdfCount}]" class="w-full bg-[#05070d] border border-white/[0.08] text-slate-400 rounded-xl px-4 py-2.5 text-xs">
                            </div>
                        </div>
                    </div>
                `;
                $('#pdf-container').append(html);
                pdfCount++;
            });

            $(document).on('click', '.remove-pdf-btn', function() {
                $(this).closest('.pdf-item').remove();
            });

            $('#certificate-settings-form').on('submit', function(e) {
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
                            title: response.message || '{{ __('Certificates updated.') }}',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to save certificates.') }}',
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
