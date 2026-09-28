<div class="menu-list-sortable space-y-3 font-mono">
    @foreach ($menus as $menu)
        @php
            $visible = shouldShowLabel($menu->label);
        @endphp
        <div class="menu-item-wrapper relative rounded-2xl bg-white/[0.015] border border-white/[0.06] overflow-hidden transition-all hover:border-cyan-500/30 @if (!$visible) opacity-50 grayscale @endif"
            data-id="{{ $menu->id }}">
            
            @if (!$visible)
                <div class="absolute inset-0 z-20 flex items-center justify-center bg-black/75 backdrop-blur-[2px]">
                    <a href="{{ route('admin.settings.modules.index') }}"
                        class="px-4 py-2 bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/30 rounded-xl text-cyan-400 font-bold uppercase text-[10px] tracking-wider transition-all flex items-center gap-2">
                        <span>{{ __('Module Disabled · Click to Enable') }}</span>
                    </a>
                </div>
            @endif

            <div class="p-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="drag-handle cursor-grab active:cursor-grabbing text-slate-500 hover:text-cyan-400 p-1 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                        </svg>
                    </div>

                    <div class="w-9 h-9 rounded-xl bg-[#05070d] border border-white/[0.08] flex items-center justify-center text-cyan-400 shrink-0">
                        {!! $menu->icon ?? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>' !!}
                    </div>

                    <div class="min-w-0">
                        <span class="text-white font-bold block text-xs truncate">{{ $menu->label }}</span>
                        <span class="text-[10px] text-slate-500 font-mono block truncate">{{ $menu->route_name ?? $menu->url }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-4 shrink-0">
                    {{-- Toggle Switch --}}
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" onchange="toggleMenuVisibility({{ $menu->id }}, this)"
                            {{ $menu->is_active ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-9 h-5 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-cyan-400"></div>
                    </label>

                    {{-- Children dropdown toggle --}}
                    @if ($menu->children->count() > 0)
                        <button type="button" onclick="toggleChildren({{ $menu->id }})"
                            class="px-2.5 py-1 rounded-lg bg-white/[0.04] hover:bg-white/[0.08] text-slate-400 hover:text-white transition-all flex items-center gap-1 text-[10px] font-bold cursor-pointer">
                            <span id="chevron-{{ $menu->id }}" class="inline-block transition-transform duration-300">▼</span>
                            <span>{{ $menu->children->count() }}</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Children Items Sub-Deck --}}
            @if ($menu->children->count() > 0)
                <div id="children-{{ $menu->id }}" class="children-sortable hidden flex-col border-t border-white/[0.04] bg-[#05070d]/60 divide-y divide-white/[0.02]">
                    @foreach ($menu->children as $child)
                        @php
                            $childVisible = shouldShowLabel($child->label);
                        @endphp
                        <div class="child-item p-3 pl-12 flex items-center justify-between gap-4 relative @if (!$childVisible) opacity-50 grayscale @endif"
                            data-id="{{ $child->id }}">
                            
                            @if (!$childVisible)
                                <div class="absolute inset-0 z-20 flex items-center justify-center bg-black/60 backdrop-blur-[1px]">
                                    <a href="{{ route('admin.settings.modules.index') }}"
                                        class="px-3 py-1 bg-cyan-500/10 border border-cyan-500/30 rounded-lg text-cyan-400 font-bold uppercase text-[8px]">
                                        {{ __('Enable Module') }}
                                    </a>
                                </div>
                            @endif

                            <div class="flex items-center gap-3 min-w-0">
                                <div class="drag-handle-child cursor-grab active:cursor-grabbing text-slate-600 hover:text-cyan-400 p-1 shrink-0">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-white font-bold block text-xs truncate">{{ $child->label }}</span>
                                    <span class="text-[9px] text-slate-500 font-mono block truncate">{{ $child->route_name ?? $child->url }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" onchange="toggleMenuVisibility({{ $child->id }}, this)"
                                        {{ $child->is_active ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-7 h-4 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-cyan-400"></div>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    @endforeach
</div>
