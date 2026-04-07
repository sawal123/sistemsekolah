@props(['title' => null, 'padding' => '24px'])

<div {{ $attributes->merge(['class' => 'glass-card flex flex-col']) }} style="padding: {{ $padding }};">
    @if($title || isset($header))
        <div class="flex items-center justify-between mb-[18px]">
            @if($title)
                <h3 class="txt-primary font-bold text-[15px]">{{ $title }}</h3>
            @endif
            
            @isset($header)
                <div>{{ $header }}</div>
            @endisset
        </div>
    @endif
    
    <div class="flex-1">
        {{ $slot }}
    </div>
    
    @isset($footer)
        <div class="mt-5 pt-4 border-t border-indigo-500/10 dark:border-white/10">
            {{ $footer }}
        </div>
    @endisset
</div>
