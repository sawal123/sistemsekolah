@props(['placeholder' => 'Cari...', 'model' => 'search'])

<div x-data="{ 
    search: @entangle($model)
}" class="relative group w-full">
    {{-- Search Icon (Left) --}}
    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors duration-200 z-10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>

    {{-- Input Box --}}
    <input 
        wire:model.live.debounce.300ms="{{ $model }}"
        type="text" 
        placeholder="{{ $placeholder }}"
        class="block w-full rounded-xl border-2 glass focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 txt-primary py-2.5 pl-12 pr-10 transition-all duration-200 placeholder-gray-400 dark:placeholder-gray-500 outline-none shadow-sm"
    >

    {{-- Clear Button (Right) --}}
    <button 
        x-show="search && search.length > 0" 
        wire:click="$set('{{ $model }}', '')"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        type="button"
        class="absolute inset-y-0 right-3 flex items-center px-1 text-slate-400 hover:text-red-500 transition-colors z-10"
        title="Bersihkan Pencarian"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
