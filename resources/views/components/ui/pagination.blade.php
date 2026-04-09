@props(['links'])

@if($links->hasPages())
    <div {{ $attributes->merge(['class' => 'px-6 py-4 border-t border-indigo-500/10 dark:border-white/10 bg-indigo-500/[0.01]']) }}>
        {{ $links->links('vendor.pagination.glass') }}
    </div>
@endif