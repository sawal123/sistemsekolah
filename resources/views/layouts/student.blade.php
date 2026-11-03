<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $title ?? 'Portal Siswa' }} — SMA Nusantara</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="mx-auto min-h-screen max-w-5xl bg-slate-50 pb-24 sm:pb-8">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur sm:px-6">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('siswa.dashboard') }}" wire:navigate class="flex min-w-0 items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-sm font-extrabold text-white">SN</div>
                    <div class="min-w-0"><p class="truncate text-sm font-extrabold">Portal Siswa</p><p class="truncate text-xs text-slate-500">SMA Nusantara</p></div>
                </a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="min-h-11 rounded-xl px-3 text-xs font-bold text-slate-600 hover:bg-slate-100">Keluar</button></form>
            </div>
        </header>
        <main class="px-4 py-5 sm:px-6">{{ $slot }}</main>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 px-2 pb-[max(0.5rem,env(safe-area-inset-bottom))] pt-2 backdrop-blur sm:sticky sm:bottom-0 sm:mx-auto sm:max-w-5xl sm:rounded-t-2xl">
        <div class="mx-auto flex max-w-lg items-stretch justify-around">
            @foreach ([
                ['route' => 'siswa.dashboard', 'label' => 'Beranda', 'icon' => '⌂'],
                ['route' => 'siswa.payments', 'label' => 'Bayar', 'icon' => 'Rp'],
                ['route' => 'siswa.academic', 'label' => 'Akademik', 'icon' => '▤'],
                ['route' => 'siswa.profile', 'label' => 'Profil', 'icon' => '◉'],
            ] as $item)
                <a href="{{ route($item['route']) }}" wire:navigate class="flex min-h-14 min-w-16 flex-col items-center justify-center rounded-xl px-3 text-[11px] font-bold {{ request()->routeIs($item['route']) ? 'bg-blue-50 text-blue-700' : 'text-slate-500 hover:bg-slate-50' }}"><span class="mb-1 text-base">{{ $item['icon'] }}</span>{{ $item['label'] }}</a>
            @endforeach
        </div>
    </nav>
    <x-ui.toast />
    @livewireScripts
</body>
</html>
