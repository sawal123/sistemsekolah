@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-2'])

@php
switch ($align) {
    case 'left':
        $alignmentClasses = 'origin-top-left left-0';
        break;
    case 'top':
        $alignmentClasses = 'origin-top';
        break;
    case 'right':
    default:
        $alignmentClasses = 'origin-top-right right-0';
        break;
}

switch ($width) {
    case '48':
        $widthClasses = 'w-48';
        break;
    case '64':
        $widthClasses = 'w-64';
        break;
    default:
        $widthClasses = $width;
}
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-2 {{ $widthClasses }} rounded-xl shadow-lg {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        
        <!-- Extends the global .glass-card for theme consistency -->
        <div class="rounded-xl glass-card border border-indigo-500/10 dark:border-white/10 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
