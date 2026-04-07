@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white/50 dark:bg-black/20 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm txt-primary py-2 px-3 transition-colors duration-200 placeholder-gray-400 dark:placeholder-gray-500 outline-none']) !!}>
