@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        {{-- Mobile View --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span
                    class="relative inline-flex items-center px-4 py-2 text-xs font-medium txt-muted glass border border-white/10 rounded-xl cursor-default opacity-50">
                    Previous
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="relative inline-flex items-center px-4 py-2 text-xs font-medium txt-primary glass border border-white/10 rounded-xl hover:bg-indigo-500/10 transition-all active:scale-95">
                    Previous
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="relative inline-flex items-center px-4 py-2 text-xs font-medium txt-primary glass border border-white/10 rounded-xl hover:bg-indigo-500/10 transition-all active:scale-95">
                    Next
                </button>
            @else
                <span
                    class="relative inline-flex items-center px-4 py-2 text-xs font-medium txt-muted glass border border-white/10 rounded-xl cursor-default opacity-50">
                    Next
                </span>
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between px-2">
            <div>
                <p class="text-xs txt-muted">
                    Menampilkan
                    <span class="font-bold txt-primary">{{ $paginator->firstItem() }}</span>
                    -
                    <span class="font-bold txt-primary">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-bold txt-primary">{{ $paginator->total() }}</span>
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex  gap-1.5">
                    {{-- Previous Page Button --}}
                    @if ($paginator->onFirstPage())
                        <span
                            class="relative cursor-pointer inline-flex items-center p-2 glass border border-white/10 rounded-xl txt-muted cursor-default opacity-30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </span>
                    @else
                        <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev"
                            class="cursor-pointer relative inline-flex items-center p-2 glass border border-white/10 rounded-xl txt-primary hover:bg-indigo-500/10 transition-all active:scale-90 hover:border-indigo-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span
                                class="cursor-pointer relative inline-flex items-center px-3 py-2 text-xs font-medium txt-muted glass border border-white/10 rounded-xl cursor-default">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span
                                        class="cursor-pointer relative inline-flex items-center px-4 py-2 text-[13px] font-bold text-white bg-indigo-500 rounded-xl shadow-lg shadow-indigo-500/30 z-10 border border-indigo-400/50">
                                        {{ $page }}
                                    </span>
                                @else
                                    <button wire:click="gotoPage({{ $page }})"
                                        class="cursor-pointer relative inline-flex items-center px-4 py-2 text-[13px] font-medium txt-primary glass border border-white/10 rounded-xl hover:bg-indigo-500/10 transition-all active:scale-90 hover:border-indigo-500/30">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Button --}}
                    @if ($paginator->hasMorePages())
                        <button wire:click="nextPage" wire:loading.attr="disabled" rel="next"
                            class="cursor-pointer relative inline-flex items-center p-2 glass border border-white/10 rounded-xl txt-primary hover:bg-indigo-500/10 transition-all active:scale-90 hover:border-indigo-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @else
                        <span
                            class="cursor-pointer relative inline-flex items-center p-2 glass border border-white/10 rounded-xl txt-muted cursor-default opacity-30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif