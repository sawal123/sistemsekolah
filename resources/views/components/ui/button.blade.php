@props(['variant' => 'primary', 'type' => 'button'])

@php
    $baseClass = "inline-flex items-center justify-center gap-2 px-4 py-2 font-semibold text-sm rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer";

    $variants = [
        'primary' => 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white   hover:from-indigo-600 hover:to-purple-600 focus:ring-indigo-500 shadow-md shadow-indigo-500/30',
        'secondary' => 'glass txt-secondary  hover:bg-white/40 dark:hover:bg-white/10 focus:ring-gray-300',
        'danger' => 'bg-gradient-to-r from-red-500 to-rose-500 text-white hover:from-red-600 hover:to-rose-600 focus:ring-red-500 shadow-md shadow-red-500/30',
    ];

    $classes = $baseClass . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>