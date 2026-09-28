@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <a href="{{ route('admin.copy-trading.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-mono text-emerald-400 hover:text-emerald-300 transition-colors mb-2 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>{{ __('Back to Copy Trading') }}</span>
                </a>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-emerald-100 to-emerald-400/70 tracking-tight leading-tight">
                    {{ __('Edit Copy Trade') }}: {{ $strategy->code }}
                </h1>
                <p class="text-slate-400 font-mono text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Update target profit, trading pair, and expiration') }}
                </p>
            </div>

            <button type="button"
                class="btn-save-strategy px-6 py-2.5 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-[#050507] font-black font-mono text-xs uppercase tracking-wider transition-all flex items-center gap-2 shadow-[0_0_20px_rgba(16,185,129,0.3)] cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ __('Save Changes') }}</span>
            </button>
        </div>

        <form id="strategyForm" action="{{ route('admin.copy-trading.update', $strategy->id) }}" method="POST">
            @csrf
            @include('templates.york.blades.admin.trading.copy.partials.form')

            {{-- Mobile Submit Button --}}
            <div class="mt-8 flex md:hidden">
                <button type="button"
                    class="btn-save-strategy w-full flex justify-center items-center gap-2 bg-emerald-400 text-[#050507] px-6 py-3.5 rounded-xl font-black font-mono text-xs uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(16,185,129,0.3)] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#amount_type').on('change', function() {
                if ($(this).val() === 'percentage') {
                    $('#percentage_container').removeClass('hidden');
                } else {
                    $('#percentage_container').addClass('hidden');
                }
            });

            $('.btn-save-strategy').on('click', function() {
                const $form = $('#strategyForm');
                const $btn = $(this);
                const originalText = $btn.html();

                $btn.prop('disabled', true).html('<svg class="w-4 h-4 animate-spin mx-auto text-[#050507]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message || '{{ __('Strategy updated successfully.') }}',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        setTimeout(() => {
                            window.location.href = "{{ route('admin.copy-trading.index') }}";
                        }, 1200);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalText);
                        let msg = '{{ __('An error occurred.') }}';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Update Error') }}',
                            html: msg,
                            customClass: {
                                popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono text-xs',
                                title: 'text-white font-mono',
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
