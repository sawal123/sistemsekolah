@props(['for'])

@error($for)
    <p {{ $attributes->merge(['class' => 'text-[11px] text-rose-600 dark:text-rose-400 font-semibold mt-1']) }}>
        {{ $message }}
    </p>
@enderror
