@props([
    'label' => null,
    'options' => [],
    'placeholder' => 'Pilih Opsi'
])

<div x-data="{ 
    open: false,
    selected: @entangle($attributes->wire('model')),
    options: @js($options),
    get selectedLabel() {
        if (this.selected === null || this.selected === undefined || this.selected === '') {
            return this.options[''] || '{{ $placeholder }}';
        }
        // Handle numeric keys that might be sent as integers or strings
        return this.options[this.selected] || this.options[String(this.selected)] || this.selected;
    }
}" class="relative w-full">
    @if($label)
        <x-ui.label :value="$label" class="mb-2" />
    @endif
    
    <button type="button" @click="open = !open" @click.away="open = false" draggable="false"
        class="w-full flex items-center justify-between px-4 py-2.5 glass border-2 border-transparent rounded-xl text-sm font-medium focus:border-indigo-500/50 transition-all txt-primary focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
        <span x-text="selectedLabel"></span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" 
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 w-full mt-2 glass rounded-2xl border border-white/10 shadow-xl overflow-hidden backdrop-blur-xl divide-y divide-white/5"
        style="display: none;">
        
        <div class="max-h-60 overflow-y-auto custom-scrollbar">
            @foreach($options as $key => $value)
                @php
                    $optionValue = $key;
                    $optionLabel = $value;
                @endphp
                <button type="button" 
                    @click="selected = '{{ $optionValue }}'; open = false"
                    class="w-full text-left px-4 py-3 text-sm hover:bg-indigo-500/10 transition-colors txt-primary"
                    :class="{ 'bg-indigo-500/5 font-bold': selected == '{{ $optionValue }}' }">
                    <div class="flex items-center justify-between">
                        <span>{{ $optionLabel }}</span>
                        <template x-if="selected == '{{ $optionValue }}'">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                    </div>
                </button>
            @endforeach
        </div>
    </div>
</div>
