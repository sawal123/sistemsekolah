@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => '
 block w-full rounded-xl border-2  glass
 focus:border-indigo-500 focus:ring-indigo-500
//  txt-primary
   focus:outline-none
                                    focus:ring-4 focus:ring-indigo-500/15
 txt-primary py-2 px-3 transition-colors duration-200 placeholder-gray-400 dark:placeholder-gray-500 outline-none'
]) !!}>