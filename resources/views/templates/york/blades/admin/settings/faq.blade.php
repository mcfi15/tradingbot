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
                    {{ __('FAQS & HELP') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('FAQs & Help Center') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Create, edit, and organize frequently asked questions and help content') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="openModal('add-faq-modal')"
                    class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('Add FAQ') }}</span>
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
                
                {{-- Supported Placeholders --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-8 space-y-4 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider">{{ __('Available Variables') }}</h3>
                        </div>
                        <p class="text-slate-400 text-[11px] leading-relaxed">
                            {{ __('You can use these variables in your questions and answers. They will be replaced automatically.') }}
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="p-3 rounded-xl bg-white/[0.015] border border-white/[0.04]">
                                <code class="text-cyan-300 font-bold text-[11px]">:name</code>
                                <span class="text-[9px] text-slate-500 block mt-1">{{ getSetting('name') ?? 'Foyana' }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/[0.015] border border-white/[0.04]">
                                <code class="text-cyan-300 font-bold text-[11px]">:currency</code>
                                <span class="text-[9px] text-slate-500 block mt-1">{{ getSetting('currency_symbol', '$') }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/[0.015] border border-white/[0.04]">
                                <code class="text-cyan-300 font-bold text-[11px]">:email</code>
                                <span class="text-[9px] text-slate-500 block mt-1">{{ getSetting('contact_email', 'support@domain.com') }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/[0.015] border border-white/[0.04]">
                                <code class="text-cyan-300 font-bold text-[11px]">:url</code>
                                <span class="text-[9px] text-slate-500 block mt-1">{{ config('app.url') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FAQs List --}}
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-6 text-xs">
                        <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Frequently Asked Questions') }}</h3>
                            <span class="text-[10px] text-slate-500 font-bold uppercase">{{ count($faqs) }} {{ __('Entries') }}</span>
                        </div>

                        <div class="space-y-4">
                            @forelse ($faqs as $faq)
                                <div class="p-5 rounded-2xl bg-white/[0.015] border border-white/[0.04] space-y-3 hover:border-cyan-500/30 transition-all">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 text-[9px] font-black uppercase">
                                                {{ $faq->category }}
                                            </span>
                                            <span class="text-slate-500 text-[10px]">{{ __('Order:') }} {{ $faq->sort_order }}</span>
                                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase {{ $faq->status ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                                {{ $faq->status ? __('Active') : __('Draft') }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="editFaq({{ json_encode($faq) }})"
                                                class="px-2.5 py-1 rounded-lg bg-white/[0.04] hover:bg-cyan-400 hover:text-black text-slate-300 font-bold uppercase text-[9px] transition-all cursor-pointer">
                                                {{ __('Edit') }}
                                            </button>
                                            <button type="button" onclick="deleteFaq({{ $faq->id }})"
                                                class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white font-bold uppercase text-[9px] transition-all cursor-pointer">
                                                {{ __('Delete') }}
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="text-white font-bold text-xs">{{ $faq->question }}</h4>
                                        <p class="text-slate-400 text-[11px] leading-relaxed mt-1">{{ $faq->answer }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="py-12 text-center text-slate-500 space-y-2">
                                    <p class="text-xs">{{ __('No FAQs created yet.') }}</p>
                                    <button type="button" onclick="openModal('add-faq-modal')" class="text-cyan-400 text-[10px] uppercase font-bold hover:underline">
                                        {{ __('Create First FAQ') }}
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- Add FAQ Modal --}}
    <div id="add-faq-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] w-full max-w-lg">
            <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8 space-y-6 text-xs font-mono">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Add FAQ') }}</h3>
                    <button type="button" onclick="closeModal('add-faq-modal')" class="text-slate-400 hover:text-white cursor-pointer">✕</button>
                </div>

                <form id="add-faq-form" onsubmit="submitAddFaq(event)" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Category') }}</label>
                        <input type="text" name="category" value="AI Bots" placeholder="Trading, Security, General..."
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Question') }}</label>
                        <input type="text" name="question" required placeholder="e.g. How does bot trading work on :name?"
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Answer') }}</label>
                        <textarea name="answer" rows="4" required placeholder="e.g. :name uses automated trading bots to execute trades..."
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Sort Order') }}</label>
                            <input type="number" name="sort_order" value="0"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="status" value="1" checked class="accent-cyan-400 w-4 h-4">
                                <span class="text-white font-bold text-xs">{{ __('Active') }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/[0.06] flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal('add-faq-modal')" class="px-5 py-2.5 rounded-xl bg-white/[0.04] text-slate-300 uppercase text-[10px] font-bold">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-[10px]">
                            {{ __('Save FAQ') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit FAQ Modal --}}
    <div id="edit-faq-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] w-full max-w-lg">
            <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8 space-y-6 text-xs font-mono">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Edit FAQ') }}</h3>
                    <button type="button" onclick="closeModal('edit-faq-modal')" class="text-slate-400 hover:text-white cursor-pointer">✕</button>
                </div>

                <form id="edit-faq-form" onsubmit="submitEditFaq(event)" class="space-y-4">
                    @csrf
                    <input type="hidden" id="edit-faq-id">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Category') }}</label>
                        <input type="text" id="edit-category" name="category"
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Question') }}</label>
                        <input type="text" id="edit-question" name="question" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Answer') }}</label>
                        <textarea id="edit-answer" name="answer" rows="4" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Sort Order') }}</label>
                            <input type="number" id="edit-sort_order" name="sort_order"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" id="edit-status" name="status" value="1" class="accent-cyan-400 w-4 h-4">
                                <span class="text-white font-bold text-xs">{{ __('Active') }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/[0.06] flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal('edit-faq-modal')" class="px-5 py-2.5 rounded-xl bg-white/[0.04] text-slate-300 uppercase text-[10px] font-bold">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-[10px]">
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openModal(id) {
            $('#' + id).removeClass('hidden');
        }

        function closeModal(id) {
            $('#' + id).addClass('hidden');
        }

        function submitAddFaq(e) {
            e.preventDefault();
            const form = document.getElementById('add-faq-form');
            const formData = new FormData(form);

            fetch("{{ route('admin.settings.faq.store') }}", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500,
                    customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                });
                setTimeout(() => window.location.reload(), 1000);
            });
        }

        function editFaq(faq) {
            document.getElementById('edit-faq-id').value = faq.id;
            document.getElementById('edit-category').value = faq.category;
            document.getElementById('edit-question').value = faq.question;
            document.getElementById('edit-answer').value = faq.answer;
            document.getElementById('edit-sort_order').value = faq.sort_order;
            document.getElementById('edit-status').checked = (faq.status == 1);

            document.getElementById('edit-faq-form').action = "{{ route('admin.settings.faq.index') }}/update/" + faq.id;
            openModal('edit-faq-modal');
        }

        function submitEditFaq(e) {
            e.preventDefault();
            const form = document.getElementById('edit-faq-form');
            const formData = new FormData(form);

            fetch(form.action, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500,
                    customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                });
                setTimeout(() => window.location.reload(), 1000);
            });
        }

        function deleteFaq(id) {
            Swal.fire({
                title: '{{ __('Delete FAQ?') }}',
                text: '{{ __('This FAQ will be permanently deleted.') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#1e293b',
                confirmButtonText: '{{ __('Delete') }}',
                customClass: { popup: 'bg-[#090c14] border border-white/10 text-white font-mono' }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('admin.settings.faq.index') }}/delete/" + id, {
                        method: "POST",
                        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                    })
                    .then(res => res.json())
                    .then(data => {
                        window.location.reload();
                    });
                }
            });
        }
    </script>
@endpush
