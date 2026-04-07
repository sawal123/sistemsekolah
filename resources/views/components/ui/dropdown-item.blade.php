<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-indigo-500/10 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none focus:bg-indigo-500/20 transition duration-150 ease-in-out cursor-pointer']) }}>
    {{ $slot }}
</a>
