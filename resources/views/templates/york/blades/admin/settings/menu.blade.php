@php
    function shouldShowLabel($menu_label)
    {
        $menu_label = strtolower($menu_label);
        $menu_label = str_replace(' ', '_', $menu_label);
        $module_key = str_replace('_record', '', $menu_label);
        $module_key = str_replace('_trading', '', $module_key);
        $module_key = str_replace('managed_', '', $module_key);

        $alloed_s = ['bonds', 'futures'];
        if (str_ends_with($module_key, 's') && !in_array($module_key, $alloed_s)) {
            $module_key = substr($module_key, 0, -1);
        }

        $should_show = true;
        switch ($menu_label) {
            case 'capital_instruments':
                if (!moduleEnabled('bonds_module') && !moduleEnabled('stock_module') && !moduleEnabled('etf_module')) {
                    $should_show = false;
                }
                break;

            case 'self_trading':
                if (
                    !moduleEnabled('futures_module') &&
                    !moduleEnabled('forex_module') &&
                    !moduleEnabled('margin_module')
                ) {
                    $should_show = false;
                }
                break;

            default:
                $module_key = $module_key . '_module';
                if (moduleExists($module_key)) {
                    $should_show = moduleEnabled($module_key);
                }
                break;
        }
        return $should_show;
    }
@endphp

@extends('templates.york.blades.admin.layouts.admin')

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush

@section('content')
    <div class="space-y-8 mb-12 font-mono">
        
        {{-- ==================================================================================== --}}
        {{-- TOP HEADER --}}
        {{-- ==================================================================================== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-[0.25em] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    {{ __('NAVIGATION & MENUS') }}
                </div>
                <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                    {{ __('Navigation Menus') }}
                </h1>
                <p class="text-slate-400 text-[10px] md:text-xs mt-0.5 tracking-widest uppercase">
                    {{ __('Customize sidebar navigation, links, and order for admin and client portals') }}
                </p>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick="openCreateModal()"
                    class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase tracking-wider transition-all shadow-[0_0_20px_rgba(0,245,255,0.3)] flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('Add Menu Item') }}</span>
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
                        
                        {{-- Tabs: Admin vs User Sidebar --}}
                        <div class="flex items-center justify-between pb-4 border-b border-white/[0.06]">
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="switchTab('admin')" id="tab-admin"
                                    class="tab-btn px-4 py-2 rounded-xl bg-cyan-400 text-black font-black uppercase text-[10px] tracking-wider transition-all cursor-pointer">
                                    {{ __('Admin Menu') }}
                                </button>
                                <button type="button" onclick="switchTab('user')" id="tab-user"
                                    class="tab-btn px-4 py-2 rounded-xl bg-white/[0.03] hover:bg-white/[0.08] text-slate-400 hover:text-white font-bold uppercase text-[10px] tracking-wider transition-all cursor-pointer">
                                    {{ __('User Menu') }}
                                </button>
                            </div>
                            <span class="text-[9px] text-slate-500 uppercase tracking-widest">{{ __('Drag items to reorder') }}</span>
                        </div>

                        {{-- Menu Containers --}}
                        <div id="menu-container-admin" class="menu-tab-content">
                            @include('templates.' . $template . '.blades.admin.settings.partials.menu_list', [
                                'menus' => $admin_menus,
                            ])
                        </div>

                        <div id="menu-container-user" class="menu-tab-content hidden">
                            @include('templates.' . $template . '.blades.admin.settings.partials.menu_list', [
                                'menus' => $user_menus,
                            ])
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
                    <h3 class="text-sm font-black text-white uppercase tracking-wider">{{ __('Add Menu Item') }}</h3>
                    <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-white cursor-pointer">✕</button>
                </div>

                <form action="{{ route('admin.settings.menu.create') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Menu Label') }}</label>
                            <input type="text" name="label" required placeholder="e.g. Analytics" value="{{ old('label') }}"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Target Menu') }}</label>
                            <select name="type" id="menu_type_select" required onchange="filterParentOptions()"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                <option value="user" {{ old('type') == 'user' ? 'selected' : '' }}>{{ __('User Portal') }}</option>
                                <option value="admin" {{ old('type') == 'admin' ? 'selected' : '' }}>{{ __('Admin Portal') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-white/[0.015] border border-white/[0.04] space-y-3">
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                                <input type="radio" name="nav_mode" value="route" checked onchange="switchNavMode()" class="accent-cyan-400">
                                <span class="text-[10px] font-bold uppercase">{{ __('Named Route') }}</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                                <input type="radio" name="nav_mode" value="url" onchange="switchNavMode()" class="accent-cyan-400">
                                <span class="text-[10px] font-bold uppercase">{{ __('Direct URL') }}</span>
                            </label>
                        </div>

                        <div id="route_input_container">
                            <input type="text" name="route_name" value="{{ old('route_name') }}" placeholder="user.dashboard"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none">
                        </div>

                        <div id="url_input_container" class="hidden">
                            <input type="text" name="url" value="{{ old('url') }}" placeholder="https://external-link.com"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Sort Order') }}</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Parent Menu Item') }}</label>
                            <select name="parent_id" id="parent_id_select"
                                class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl px-4 py-3 text-xs focus:outline-none">
                                <option value="">{{ __('None (Top-Level Item)') }}</option>
                                @foreach ($admin_menus as $menu)
                                    <option value="{{ $menu->id }}" data-type="admin" {{ old('parent_id') == $menu->id ? 'selected' : '' }}>
                                        [Admin] {{ $menu->label }}
                                    </option>
                                @endforeach
                                @foreach ($user_menus as $menu)
                                    <option value="{{ $menu->id }}" data-type="user" {{ old('parent_id') == $menu->id ? 'selected' : '' }}>
                                        [User] {{ $menu->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-slate-400">{{ __('Icon SVG Code (Optional)') }}</label>
                        <textarea name="icon" rows="2" placeholder="<svg ...> ... </svg>"
                            class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none resize-none">{{ old('icon') }}</textarea>
                    </div>

                    <div class="pt-4 border-t border-white/[0.06] flex items-center justify-end gap-3">
                        <button type="button" onclick="closeCreateModal()" class="px-5 py-2.5 rounded-xl bg-white/[0.04] text-slate-300 uppercase text-[10px] font-bold">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black font-black uppercase text-[10px]">
                            {{ __('Add Item') }}
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
        function switchTab(type) {
            $('.tab-btn').removeClass('bg-cyan-400 text-black').addClass('bg-white/[0.03] text-slate-400');
            $(`#tab-${type}`).addClass('bg-cyan-400 text-black').removeClass('bg-white/[0.03] text-slate-400');
            $('.menu-tab-content').addClass('hidden');
            $(`#menu-container-${type}`).removeClass('hidden');
        }

        function openCreateModal() {
            $('#createModal').removeClass('hidden');
            filterParentOptions();
        }

        function closeCreateModal() {
            $('#createModal').addClass('hidden');
        }

        function filterParentOptions() {
            const selectedType = $('#menu_type_select').val();
            const $parentSelect = $('#parent_id_select');

            $parentSelect.find('option').each(function() {
                const optType = $(this).data('type');
                if (!optType || optType === selectedType) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            const currentOption = $parentSelect.find('option:selected');
            if (currentOption.is(':hidden')) {
                $parentSelect.val('');
            }
        }

        function toggleChildren(id) {
            const $container = $(`#children-${id}`);
            const $chevron = $(`#chevron-${id}`);

            if ($container.hasClass('hidden')) {
                $container.removeClass('hidden').addClass('flex');
                $chevron.text('▲');
                initChildSortable(id);
            } else {
                $container.addClass('hidden').removeClass('flex');
                $chevron.text('▼');
            }
        }

        function initParentSortable() {
            $('.menu-list-sortable').each(function() {
                new Sortable(this, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'opacity-40',
                    onEnd: function(evt) {
                        const items = [];
                        $(evt.to).find('> .menu-item-wrapper').each(function() {
                            items.push($(this).data('id'));
                        });
                        saveOrder(items);
                    }
                });
            });
        }

        function initChildSortable(parentId) {
            const container = document.getElementById(`children-${parentId}`);
            if (container && !container.classList.contains('sortable-initialized')) {
                new Sortable(container, {
                    animation: 150,
                    handle: '.drag-handle-child',
                    ghostClass: 'opacity-40',
                    onEnd: function(evt) {
                        const items = [];
                        $(evt.to).find('> .child-item').each(function() {
                            items.push($(this).data('id'));
                        });
                        saveOrder(items);
                    }
                });
                container.classList.add('sortable-initialized');
            }
        }

        function saveOrder(items) {
            $.ajax({
                url: "{{ route('admin.settings.menu.reorder') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    items: items
                },
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message || '{{ __('Order saved.') }}',
                        showConfirmButton: false,
                        timer: 1200,
                        customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                    });
                }
            });
        }

        function switchNavMode() {
            const mode = $('input[name="nav_mode"]:checked').val();
            if (mode === 'route') {
                $('#route_input_container').removeClass('hidden');
                $('#url_input_container').addClass('hidden');
                $('input[name="url"]').val('');
            } else {
                $('#route_input_container').addClass('hidden');
                $('#url_input_container').removeClass('hidden');
                $('input[name="route_name"]').val('');
            }
        }

        function toggleMenuVisibility(id, el) {
            const isActive = el.checked ? 1 : 0;

            $.ajax({
                url: "{{ route('admin.settings.menu.update') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    menu_id: id,
                    is_active: isActive
                },
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message || '{{ __('Visibility updated.') }}',
                        showConfirmButton: false,
                        timer: 1200,
                        customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                    });
                },
                error: function(xhr) {
                    el.checked = !el.checked;
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Error') }}',
                        text: xhr.responseJSON?.message || '{{ __('Failed to update visibility.') }}',
                        customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                    });
                }
            });
        }

        $(document).ready(function() {
            initParentSortable();
        });
    </script>
@endpush
