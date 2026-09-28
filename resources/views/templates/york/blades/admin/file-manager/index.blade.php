@extends('templates.york.blades.admin.layouts.admin')

@section('content')
    <div class="space-y-8 mb-12 font-mono">

        {{-- Disabled Warning --}}
        @if (config('app.env') === 'sandbox')
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                <div class="rounded-[2rem] bg-[#090c14]/95 p-8 text-center">
                    <h3 class="text-lg font-bold text-amber-400 uppercase">{{ __('Sandbox Mode Active') }}</h3>
                    <p class="text-xs text-slate-400 mt-2 mb-4">{{ __('System modifications in sandbox environment are restricted.') }}</p>
                </div>
            </div>
        @else

            {{-- ==================================================================================== --}}
            {{-- TOP COMMAND HUD HEADER --}}
            {{-- ==================================================================================== --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-2 border-b border-white/[0.06]">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-[0.25em] mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        {{ __('STORAGE_SYSTEM // AJAX_BROWSER') }}
                    </div>
                    <h1 class="text-2xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-cyan-400/70 tracking-tight leading-tight">
                        {{ __('File Manager') }}
                    </h1>
                    
                    {{-- Breadcrumbs (AJAX target) --}}
                    <nav id="file-manager-breadcrumbs" class="flex items-center gap-2 mt-2 text-xs flex-wrap">
                        @foreach ($breadcrumbs as $index => $breadcrumb)
                            <div class="flex items-center gap-2">
                                @if ($index > 0)
                                    <span class="text-slate-600">/</span>
                                @endif
                                <a href="javascript:void(0)" onclick='loadFolder({{ json_encode($breadcrumb['path']) }})'
                                    class="uppercase font-bold transition-all {{ $loop->last ? 'text-cyan-400' : 'text-slate-400 hover:text-white' }}">
                                    {{ $breadcrumb['name'] }}
                                </a>
                            </div>
                        @endforeach
                    </nav>
                </div>

                {{-- Action Cluster --}}
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <button type="button" onclick="openCreateModal('folder')"
                        class="px-4 py-2.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/[0.1] text-slate-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <span>{{ __('New Folder') }}</span>
                    </button>

                    <button type="button" onclick="openCreateModal('file')"
                        class="px-4 py-2.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/[0.1] text-slate-300 hover:text-white font-bold uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ __('New File') }}</span>
                    </button>

                    <button type="button" onclick="$('#file-upload-input').click()"
                        class="px-5 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-[#050507] font-black uppercase tracking-wider transition-all flex items-center gap-2 shadow-[0_0_20px_rgba(0,245,255,0.3)] cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>{{ __('Upload') }}</span>
                    </button>
                    <input type="file" id="file-upload-input" class="hidden" onchange="handleFileUpload(this)">
                </div>
            </div>

            {{-- ==================================================================================== --}}
            {{-- DATA TABLE CHASSIS (Double-Bezel, AJAX swapped) --}}
            {{-- ==================================================================================== --}}
            <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl">
                <div id="file-manager-browser" class="rounded-[2rem] bg-[#090c14]/95 overflow-hidden">
                    <input type="hidden" id="current-path-hidden" value="{{ $current_path }}">
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-white/[0.02] border-b border-white/[0.06] text-[10px] text-slate-400 uppercase tracking-widest">
                                    <th class="p-5 w-12 text-center">
                                        <input type="checkbox" id="select-all" class="rounded border-white/20 bg-white/5 text-cyan-400 accent-cyan-400 cursor-pointer">
                                    </th>
                                    <th class="p-5">{{ __('Name') }}</th>
                                    <th class="p-5 hidden md:table-cell">{{ __('Size') }}</th>
                                    <th class="p-5 hidden lg:table-cell">{{ __('Modified') }}</th>
                                    <th class="p-5 hidden xl:table-cell">{{ __('Permissions') }}</th>
                                    <th class="p-5 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.04]" id="file-manager-items-body">
                                @forelse ($items as $item)
                                    <tr class="hover:bg-white/[0.015] transition-colors group file-row cursor-pointer"
                                        data-path="{{ $item['path'] }}" data-name="{{ $item['name'] }}">
                                        {{-- Checkbox --}}
                                        <td class="p-5 text-center">
                                            <input type="checkbox" class="file-checkbox rounded border-white/20 bg-white/5 text-cyan-400 accent-cyan-400 cursor-pointer" data-path="{{ $item['path'] }}">
                                        </td>

                                        {{-- Name --}}
                                        <td class="p-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl {{ $item['is_dir'] ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' }} flex items-center justify-center shrink-0">
                                                    @if ($item['is_dir'])
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if ($item['is_dir'])
                                                        <a href="javascript:void(0)" onclick='loadFolder({{ json_encode($item['path']) }})'
                                                            class="text-white hover:text-cyan-300 font-bold block leading-tight">
                                                            {{ $item['name'] }}
                                                        </a>
                                                    @else
                                                        <span class="text-white font-bold block leading-tight">
                                                            {{ $item['name'] }}
                                                        </span>
                                                    @endif
                                                    <span class="text-[9px] text-slate-500 uppercase">{{ $item['extension'] }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Size --}}
                                        <td class="p-5 hidden md:table-cell text-slate-400">
                                            {{ $item['size'] }}
                                        </td>

                                        {{-- Modified --}}
                                        <td class="p-5 hidden lg:table-cell text-slate-400 text-[11px]">
                                            {{ $item['last_modified'] }}
                                        </td>

                                        {{-- Permissions --}}
                                        <td class="p-5 hidden xl:table-cell">
                                            <code class="px-2 py-0.5 rounded-md bg-white/[0.04] text-cyan-300 border border-white/[0.08] text-[10px]">
                                                {{ $item['permissions'] }}
                                            </code>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="p-5 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                @if (!$item['is_dir'])
                                                    <a href="{{ route('admin.file-manager.code-editor', ['path' => $item['path']]) }}" target="_blank"
                                                        class="p-2 rounded-xl bg-white/[0.04] hover:bg-cyan-400 hover:text-black text-slate-300 transition-all cursor-pointer" title="{{ __('Edit') }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('admin.file-manager.download', ['path' => $item['path']]) }}" target="_blank"
                                                        class="p-2 rounded-xl bg-white/[0.04] hover:bg-emerald-400 hover:text-black text-slate-300 transition-all cursor-pointer" title="{{ __('Download') }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                <button onclick='openRenameModal({{ json_encode($item['path']) }}, {{ json_encode($item['name']) }})'
                                                    class="p-2 rounded-xl bg-white/[0.04] hover:bg-amber-400 hover:text-black text-slate-300 transition-all cursor-pointer" title="{{ __('Rename') }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>

                                                <button onclick='openPermissionModal({{ json_encode($item['path']) }}, {{ json_encode($item['permissions']) }})'
                                                    class="p-2 rounded-xl bg-white/[0.04] hover:bg-purple-400 hover:text-black text-slate-300 transition-all cursor-pointer" title="{{ __('Permissions') }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </button>

                                                <button onclick='deleteItem({{ json_encode($item['path']) }})'
                                                    class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-all cursor-pointer" title="{{ __('Delete') }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-12 text-center text-slate-500">
                                            {{ __('Empty directory.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Floating Bulk Actions Bar --}}
            <div id="bulk-toolbar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-[#090c14] border border-white/20 p-2.5 rounded-2xl shadow-2xl items-center gap-3 text-xs">
                <span class="px-3 text-slate-300 font-bold"><span id="selected-count">0</span> {{ __('Selected') }}</span>
                <button type="button" onclick="openBulkMoveModal('copy')" class="px-3 py-1.5 rounded-xl bg-white/[0.06] hover:bg-white/10 text-white font-bold uppercase cursor-pointer">{{ __('Copy') }}</button>
                <button type="button" onclick="openBulkMoveModal('move')" class="px-3 py-1.5 rounded-xl bg-white/[0.06] hover:bg-white/10 text-white font-bold uppercase cursor-pointer">{{ __('Move') }}</button>
                <button type="button" onclick="openZipModal()" class="px-3 py-1.5 rounded-xl bg-white/[0.06] hover:bg-white/10 text-white font-bold uppercase cursor-pointer">{{ __('Zip') }}</button>
                <button type="button" onclick="bulkDelete()" class="px-3 py-1.5 rounded-xl bg-rose-500 text-white font-bold uppercase cursor-pointer">{{ __('Delete') }}</button>
                <button type="button" onclick="closeBulkToolbar()" class="px-2 text-slate-400 hover:text-white cursor-pointer">&times;</button>
            </div>

        @endif

    </div>

    {{-- Create Modal --}}
    <div id="create-modal" class="hidden fixed inset-0 bg-[#05070d]/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.05] border border-white/[0.1] w-full max-w-md font-mono">
            <div class="rounded-[2rem] bg-[#090c14] p-8 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 id="create-modal-title" class="text-base font-bold text-white uppercase">{{ __('Create Item') }}</h3>
                    <button type="button" onclick="closeModal('create-modal')" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>
                <input type="text" id="create-item-name" placeholder="{{ __('Item Name...') }}" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none">
                <button type="button" onclick="confirmCreate()" class="w-full py-3 rounded-xl bg-cyan-400 text-black font-black text-xs uppercase tracking-wider cursor-pointer">{{ __('Create') }}</button>
            </div>
        </div>
    </div>

    {{-- Rename Modal --}}
    <div id="rename-modal" class="hidden fixed inset-0 bg-[#05070d]/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.05] border border-white/[0.1] w-full max-w-md font-mono">
            <div class="rounded-[2rem] bg-[#090c14] p-8 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 class="text-base font-bold text-white uppercase">{{ __('Rename Item') }}</h3>
                    <button type="button" onclick="closeModal('rename-modal')" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>
                <input type="text" id="rename-item-name" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-amber-400 text-white rounded-xl p-3 text-xs focus:outline-none">
                <button type="button" onclick="confirmRename()" class="w-full py-3 rounded-xl bg-amber-400 text-black font-black text-xs uppercase tracking-wider cursor-pointer">{{ __('Save Rename') }}</button>
            </div>
        </div>
    </div>

    {{-- Permission Modal --}}
    <div id="permission-modal" class="hidden fixed inset-0 bg-[#05070d]/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.05] border border-white/[0.1] w-full max-w-md font-mono">
            <div class="rounded-[2rem] bg-[#090c14] p-8 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 class="text-base font-bold text-white uppercase">{{ __('Change Permissions') }}</h3>
                    <button type="button" onclick="closeModal('permission-modal')" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>
                <input type="text" id="permission-value" placeholder="0755" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-purple-400 text-white rounded-xl p-3 text-xs focus:outline-none">
                <button type="button" onclick="confirmPermission()" class="w-full py-3 rounded-xl bg-purple-500 text-white font-black text-xs uppercase tracking-wider cursor-pointer">{{ __('Update Permissions') }}</button>
            </div>
        </div>
    </div>

    {{-- Bulk Move / Copy Modal --}}
    <div id="bulk-move-modal" class="hidden fixed inset-0 bg-[#05070d]/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.05] border border-white/[0.1] w-full max-w-md font-mono">
            <div class="rounded-[2rem] bg-[#090c14] p-8 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 id="bulk-move-title" class="text-base font-bold text-white uppercase">{{ __('Move Selected Items') }}</h3>
                    <button type="button" onclick="closeModal('bulk-move-modal')" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>
                <input type="text" id="bulk-move-dest" placeholder="{{ __('Destination Path...') }}" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none">
                <button type="button" id="bulk-move-btn" onclick="confirmBulkMove()" class="w-full py-3 rounded-xl bg-cyan-400 text-black font-black text-xs uppercase tracking-wider cursor-pointer">{{ __('Execute') }}</button>
            </div>
        </div>
    </div>

    {{-- Zip Modal --}}
    <div id="zip-modal" class="hidden fixed inset-0 bg-[#05070d]/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4">
        <div class="p-2 rounded-[2.5rem] bg-white/[0.05] border border-white/[0.1] w-full max-w-md font-mono">
            <div class="rounded-[2rem] bg-[#090c14] p-8 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06]">
                    <h3 class="text-base font-bold text-white uppercase">{{ __('Archive Selected Items') }}</h3>
                    <button type="button" onclick="closeModal('zip-modal')" class="text-slate-400 hover:text-white text-lg cursor-pointer">&times;</button>
                </div>
                <input type="text" id="archive-name" value="archive.zip" placeholder="{{ __('archive.zip') }}" class="w-full bg-[#05070d] border border-white/[0.1] focus:border-cyan-400 text-white rounded-xl p-3 text-xs focus:outline-none">
                <button type="button" onclick="confirmZip()" class="w-full py-3 rounded-xl bg-cyan-400 text-black font-black text-xs uppercase tracking-wider cursor-pointer">{{ __('Generate Archive') }}</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentTargetPath = $('#current-path-hidden').val() || '';
        let createItemType = 'folder';
        let renameItemPath = '';
        let permissionItemPath = '';
        let bulkOperationMode = 'move';

        // ==========================================
        // PURE AJAX FOLDER NAVIGATION & POPSTATE
        // ==========================================
        function loadFolder(path) {
            currentTargetPath = path;

            // Show lightweight loading indicator on the table
            $('#file-manager-browser').css('opacity', '0.5');

            $.get("{{ route('admin.file-manager.index') }}", { path: path }, function(response) {
                const $html = $('<div>').append($.parseHTML(response));

                $('#file-manager-breadcrumbs').html($html.find('#file-manager-breadcrumbs').html());
                $('#file-manager-browser').html($html.find('#file-manager-browser').html());

                currentTargetPath = $('#current-path-hidden').val();

                // Update Browser URL seamlessly
                const url = new URL(window.location);
                url.searchParams.set('path', path);
                window.history.pushState({ path: path }, '', url);

                closeBulkToolbar();
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('Navigation Error') }}',
                    text: '{{ __('Failed to load directory.') }}',
                    customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                });
            }).always(function() {
                $('#file-manager-browser').css('opacity', '1');
            });
        }

        window.onpopstate = function(event) {
            const path = new URLSearchParams(window.location.search).get('path') || @json($base_path);
            $('#file-manager-browser').css('opacity', '0.5');
            $.get("{{ route('admin.file-manager.index') }}", { path: path }, function(response) {
                const $html = $('<div>').append($.parseHTML(response));
                $('#file-manager-breadcrumbs').html($html.find('#file-manager-breadcrumbs').html());
                $('#file-manager-browser').html($html.find('#file-manager-browser').html());
                currentTargetPath = $('#current-path-hidden').val();
                closeBulkToolbar();
            }).always(function() {
                $('#file-manager-browser').css('opacity', '1');
            });
        };

        // ==========================================
        // AJAX ACTIONS: UPLOAD, CREATE, RENAME, ETC.
        // ==========================================
        function handleFileUpload(input) {
            if (!input.files || !input.files[0]) return;
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('file', input.files[0]);
            formData.append('path', currentTargetPath);

            Swal.fire({
                title: '{{ __('Uploading...') }}',
                didOpen: () => { Swal.showLoading(); },
                customClass: { popup: 'bg-[#090c14] border border-white/10 text-white font-mono' }
            });

            $.ajax({
                url: "{{ route('admin.file-manager.upload') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message || '{{ __('File uploaded successfully.') }}',
                        showConfirmButton: false,
                        timer: 2000,
                        customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                    });
                    $('#file-upload-input').val('');
                    loadFolder(currentTargetPath);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Upload Error') }}',
                        text: xhr.responseJSON?.message || '{{ __('Upload failed.') }}',
                        customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                    });
                }
            });
        }

        function openCreateModal(type) {
            createItemType = type;
            $('#create-modal-title').text(type === 'folder' ? '{{ __('Create New Folder') }}' : '{{ __('Create New File') }}');
            $('#create-item-name').val('').focus();
            $('#create-modal').removeClass('hidden');
        }

        function confirmCreate() {
            const name = $('#create-item-name').val();
            if (!name) return;

            $.ajax({
                url: "{{ route('admin.file-manager.create') }}",
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', name: name, type: createItemType, path: currentTargetPath },
                success: function(response) {
                    closeModal('create-modal');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message || '{{ __('Item created successfully.') }}',
                        showConfirmButton: false,
                        timer: 2000,
                        customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                    });
                    loadFolder(currentTargetPath);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Creation Error') }}',
                        text: xhr.responseJSON?.message || '{{ __('Failed to create item.') }}',
                        customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                    });
                }
            });
        }

        function openRenameModal(path, name) {
            renameItemPath = path;
            $('#rename-item-name').val(name).focus();
            $('#rename-modal').removeClass('hidden');
        }

        function confirmRename() {
            const newName = $('#rename-item-name').val();
            if (!newName) return;

            $.ajax({
                url: "{{ route('admin.file-manager.rename') }}",
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', old_path: renameItemPath, new_name: newName },
                success: function(response) {
                    closeModal('rename-modal');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message || '{{ __('Renamed successfully.') }}',
                        showConfirmButton: false,
                        timer: 2000,
                        customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                    });
                    loadFolder(currentTargetPath);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Rename Error') }}',
                        text: xhr.responseJSON?.message || '{{ __('Failed to rename.') }}',
                        customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                    });
                }
            });
        }

        function openPermissionModal(path, currentPerm) {
            permissionItemPath = path;
            $('#permission-value').val(currentPerm || '0755').focus();
            $('#permission-modal').removeClass('hidden');
        }

        function confirmPermission() {
            const perm = $('#permission-value').val();
            if (!perm) return;

            $.ajax({
                url: "{{ route('admin.file-manager.permission') }}",
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', path: permissionItemPath, permissions: perm },
                success: function(response) {
                    closeModal('permission-modal');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message || '{{ __('Permissions updated.') }}',
                        showConfirmButton: false,
                        timer: 2000,
                        customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                    });
                    loadFolder(currentTargetPath);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Permission Error') }}',
                        text: xhr.responseJSON?.message || '{{ __('Failed to update permissions.') }}',
                        customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                    });
                }
            });
        }

        function deleteItem(path) {
            Swal.fire({
                title: '{{ __('Delete Item?') }}',
                text: '{{ __('This file or folder will be permanently removed.') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#334155',
                confirmButtonText: '{{ __('Yes, Delete') }}',
                customClass: { popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono' }
            }).then((res) => {
                if (res.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.file-manager.delete') }}",
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', path: path },
                        success: function(response) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: response.message || '{{ __('Item deleted.') }}',
                                showConfirmButton: false,
                                timer: 2000,
                                customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                            });
                            loadFolder(currentTargetPath);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('Delete Error') }}',
                                text: xhr.responseJSON?.message || '{{ __('Failed to delete.') }}',
                                customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                            });
                        }
                    });
                }
            });
        }

        function closeModal(id) { $('#' + id).addClass('hidden'); }

        // ==========================================
        // SELECTION & BULK ACTIONS
        // ==========================================
        $(document).on('change', '#select-all', function() {
            $('.file-checkbox').prop('checked', $(this).prop('checked'));
            updateBulkToolbar();
        });

        $(document).on('change', '.file-checkbox', function() {
            updateBulkToolbar();
        });

        function updateBulkToolbar() {
            const count = $('.file-checkbox:checked').length;
            $('#selected-count').text(count);
            if (count > 0) {
                $('#bulk-toolbar').removeClass('hidden').addClass('flex');
            } else {
                $('#bulk-toolbar').addClass('hidden').removeClass('flex');
            }
        }

        function closeBulkToolbar() {
            $('.file-checkbox, #select-all').prop('checked', false);
            updateBulkToolbar();
        }

        function getSelectedPaths() {
            return $('.file-checkbox:checked').map(function() { return $(this).data('path'); }).get();
        }

        function bulkDelete() {
            const paths = getSelectedPaths();
            if (!paths.length) return;
            Swal.fire({
                title: '{{ __('Purge Selected Items?') }}',
                text: `{{ __('Are you sure you want to delete ') }}${paths.length}{{ __(' items?') }}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#334155',
                confirmButtonText: '{{ __('Yes, Purge') }}',
                customClass: { popup: 'bg-[#090c14] border border-white/10 text-white rounded-3xl font-mono' }
            }).then((res) => {
                if (res.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.file-manager.bulk-delete') }}",
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', paths: paths },
                        success: function(response) {
                            closeBulkToolbar();
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: response.message || '{{ __('Items deleted.') }}',
                                showConfirmButton: false,
                                timer: 2000,
                                customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                            });
                            loadFolder(currentTargetPath);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('Bulk Delete Error') }}',
                                text: xhr.responseJSON?.message || '{{ __('Failed to bulk delete.') }}',
                                customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                            });
                        }
                    });
                }
            });
        }

        function openBulkMoveModal(mode) {
            bulkOperationMode = mode;
            $('#bulk-move-title').text(mode === 'move' ? "{{ __('Move Selected Items') }}" : "{{ __('Copy Selected Items') }}");
            $('#bulk-move-btn').text(mode === 'move' ? "{{ __('Move Items') }}" : "{{ __('Copy Items') }}");
            $('#bulk-move-dest').val(currentTargetPath).focus();
            $('#bulk-move-modal').removeClass('hidden');
        }

        function confirmBulkMove() {
            const dest = $('#bulk-move-dest').val();
            const paths = getSelectedPaths();
            if (!dest || !paths.length) return;

            const url = bulkOperationMode === 'move' ?
                "{{ route('admin.file-manager.bulk-move') }}" :
                "{{ route('admin.file-manager.bulk-copy') }}";

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    paths: paths,
                    new_dir: dest
                },
                success: function(response) {
                    closeModal('bulk-move-modal');
                    closeBulkToolbar();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message || '{{ __('Operation completed.') }}',
                        showConfirmButton: false,
                        timer: 2000,
                        customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                    });
                    loadFolder(currentTargetPath);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Operation Error') }}',
                        text: xhr.responseJSON?.message || "{{ __('Bulk operation failed.') }}",
                        customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                    });
                }
            });
        }

        function openZipModal() {
            $('#archive-name').val('archive.zip').focus();
            $('#zip-modal').removeClass('hidden');
        }

        function confirmZip() {
            const name = $('#archive-name').val();
            const paths = getSelectedPaths();
            if (!name || !paths.length) return;

            $.ajax({
                url: "{{ route('admin.file-manager.bulk-zip') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    paths: paths,
                    name: name,
                    dest_dir: currentTargetPath
                },
                success: function(response) {
                    closeModal('zip-modal');
                    closeBulkToolbar();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message || '{{ __('Archive generated.') }}',
                        showConfirmButton: false,
                        timer: 2000,
                        customClass: { popup: 'bg-[#090c14] border border-cyan-500/20 text-white font-mono text-xs' }
                    });
                    loadFolder(currentTargetPath);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Zip Error') }}',
                        text: xhr.responseJSON?.message || "{{ __('Zip failed.') }}",
                        customClass: { popup: 'bg-[#090c14] border border-rose-500/20 text-white font-mono' }
                    });
                }
            });
        }
    </script>
@endpush
