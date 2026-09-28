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
                    {{ __('MANAGEMENT & TEAM') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Leadership & Team Members') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Manage team member profiles, bios, roles, and photos displayed on the website') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="openCreateModal()"
                    class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('Add Team Member') }}</span>
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
                <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] backdrop-blur-2xl">
                    <div class="rounded-[2rem] bg-[#090c14]/95 p-6 sm:p-10 space-y-6 text-xs">
                        
                        <div class="pb-3 border-b border-white/[0.06] flex items-center justify-between">
                            <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Team Profiles') }}</h3>
                            <span class="text-[10px] text-slate-500 font-bold uppercase">{{ count($teams) }} {{ __('Profiles') }}</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse ($teams as $team)
                                <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/[0.06] flex items-start gap-4 group hover:border-cyan-500/30 transition-all">
                                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-[#05070d] border border-white/[0.08] shrink-0">
                                        <img src="{{ asset('assets/images/team/' . $team->image) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                                    </div>

                                    <div class="flex-1 min-w-0 space-y-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-white font-bold block text-xs truncate">{{ $team->name }}</span>
                                            <span class="px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 text-[9px] font-black uppercase tracking-wider shrink-0">
                                                {{ $team->role }}
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-slate-400 line-clamp-2 leading-relaxed">{{ $team->description }}</p>

                                        <div class="flex items-center gap-2 pt-2">
                                            <button type="button"
                                                onclick="openEditModal({{ $team->id }}, '{{ addslashes($team->name) }}', '{{ $team->role }}', '{{ addslashes($team->description) }}', '{{ route('admin.settings.management-team.update', $team->id) }}', '{{ asset('assets/images/team/' . $team->image) }}')"
                                                class="px-2.5 py-1 rounded-lg bg-white/[0.04] hover:bg-cyan-400 hover:text-black text-slate-300 font-bold uppercase text-[9px] transition-all cursor-pointer">
                                                {{ __('Edit') }}
                                            </button>
                                            <button type="button"
                                                onclick="openDeleteModal({{ $team->id }}, '{{ route('admin.settings.management-team.delete', $team->id) }}')"
                                                class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white font-bold uppercase text-[9px] transition-all cursor-pointer">
                                                {{ __('Delete') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-12 text-center text-slate-500 space-y-2">
                                    <p class="text-xs">{{ __('No team members added yet.') }}</p>
                                    <button type="button" onclick="openCreateModal()" class="text-cyan-400 text-[10px] uppercase font-bold hover:underline">
                                        {{ __('Add First Team Member') }}
                                    </button>
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Create Modal --}}
    <div id="createModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] w-full max-w-lg">
            <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8 space-y-6 text-xs font-mono">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Add Team Member') }}</h3>
                    <button type="button" onclick="closeModal('createModal')" class="text-slate-400 hover:text-white cursor-pointer">✕</button>
                </div>

                <form id="createForm" action="{{ route('admin.settings.management-team.create') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Full Name') }}</label>
                        <input type="text" name="name" required placeholder="Alexander Vance"
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Role / Position') }}</label>
                        <select name="role" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                            <option value="ceo">CEO</option>
                            <option value="cto">CTO</option>
                            <option value="coo">COO</option>
                            <option value="cmo">CMO</option>
                            <option value="cfo">CFO</option>
                            <option value="quant">Quant</option>
                            <option value="others">Others</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Short Bio / Description') }}</label>
                        <textarea name="description" rows="3" required placeholder="Professional trader and quantitative analyst..."
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Profile Photo') }}</label>
                        <input type="file" name="image" required accept="image/*"
                            class="w-full bg-[#05070d] border border-white/[0.08] text-slate-400 rounded-xl px-4 py-2 text-xs">
                    </div>

                    <div class="pt-4 border-t border-white/[0.06] flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal('createModal')" class="px-5 py-2.5 rounded-xl bg-white/[0.04] text-slate-300 uppercase text-[10px] font-bold">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-[10px]">
                            {{ __('Save Member') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.08] w-full max-w-lg">
            <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8 space-y-6 text-xs font-mono">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Edit Team Member') }}</h3>
                    <button type="button" onclick="closeModal('editModal')" class="text-slate-400 hover:text-white cursor-pointer">✕</button>
                </div>

                <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Full Name') }}</label>
                        <input type="text" name="name" id="edit-name" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Role / Position') }}</label>
                        <select name="role" id="edit-role" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                            <option value="ceo">CEO</option>
                            <option value="cto">CTO</option>
                            <option value="coo">COO</option>
                            <option value="cmo">CMO</option>
                            <option value="cfo">CFO</option>
                            <option value="quant">Quant</option>
                            <option value="others">Others</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Short Bio / Description') }}</label>
                        <textarea name="description" id="edit-description" rows="3" required
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('New Photo (Optional)') }}</label>
                        <input type="file" name="image" accept="image/*"
                            class="w-full bg-[#05070d] border border-white/[0.08] text-slate-400 rounded-xl px-4 py-2 text-xs">
                    </div>

                    <div class="pt-4 border-t border-white/[0.06] flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal('editModal')" class="px-5 py-2.5 rounded-xl bg-white/[0.04] text-slate-300 uppercase text-[10px] font-bold">
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

    {{-- Delete Modal --}}
    <div id="deleteModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-rose-500/20 w-full max-w-sm">
            <div class="rounded-[2rem] bg-[#090c14] p-6 sm:p-8 space-y-6 text-xs font-mono text-center">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-400 border border-rose-500/20 flex items-center justify-center mx-auto text-xl font-bold">
                    !
                </div>
                <div>
                    <h3 class="text-sm font-black text-white uppercase">{{ __('Delete Team Member?') }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">{{ __('This team member will be permanently removed.') }}</p>
                </div>

                <form id="deleteForm" method="POST" class="flex flex-col gap-2">
                    @csrf
                    <button type="submit" class="w-full py-3 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-black uppercase text-[10px]">
                        {{ __('Delete Member') }}
                    </button>
                    <button type="button" onclick="closeModal('deleteModal')" class="w-full py-2.5 rounded-xl bg-white/[0.04] text-slate-400 uppercase text-[10px] font-bold">
                        {{ __('Cancel') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openCreateModal() {
            $('#createForm')[0].reset();
            $('#createModal').removeClass('hidden');
        }

        function openEditModal(id, name, role, description, url, image) {
            $('#edit-name').val(name);
            $('#edit-role').val(role);
            $('#edit-description').val(description);
            $('#editForm').attr('action', url);
            $('#editModal').removeClass('hidden');
        }

        function openDeleteModal(id, url) {
            $('#deleteForm').attr('action', url);
            $('#deleteModal').removeClass('hidden');
        }

        function closeModal(id) {
            $('#' + id).addClass('hidden');
        }

        $(document).ready(function() {
            $('#createForm, #editForm, #deleteForm').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const $btn = $form.find('button[type="submit"]');
                $btn.prop('disabled', true).addClass('opacity-50');

                const formData = new FormData(this);

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
                            title: response.message || '{{ __('Team updated.') }}',
                            showConfirmButton: false,
                            timer: 1500,
                            customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                        });
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to process team action.') }}',
                            customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                        });
                        $btn.prop('disabled', false).removeClass('opacity-50');
                    }
                });
            });
        });
    </script>
@endpush
