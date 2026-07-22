@props([
    'eyebrow' => 'SMA Negeri 1 Antigravity',
    'title',
    'description' => null,
])

<section class="relative overflow-hidden text-white" style="background:linear-gradient(135deg,#172554 0%,#1e40af 52%,#3b82f6 100%);">
    <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-blue-300/20 blur-3xl"></div>
    <div class="absolute -bottom-36 left-1/4 h-72 w-72 rounded-full bg-cyan-300/15 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-24">
        <p class="mb-3 text-sm font-bold uppercase tracking-[0.2em] text-blue-200">{{ $eyebrow }}</p>
        <h1 class="max-w-4xl font-display text-4xl font-bold leading-tight md:text-5xl">{{ $title }}</h1>
        @if($description)
            <p class="mt-5 max-w-2xl whitespace-normal text-base leading-8 text-blue-100 md:text-lg" style="overflow-wrap:anywhere;">{{ $description }}</p>
        @endif
    </div>
</section>
