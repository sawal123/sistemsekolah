<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Login Portal Sivitas Akademika' }} — Nama Sekolah</title>
    <meta name="description" content="Halaman masuk Portal Sistem Informasi Akademik terpadu untuk guru, siswa, orang tua, dan staf administrasi Nama Sekolah." />
    <meta name="theme-color" content="#0F172A" />

    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logo-sekolah.svg') }}" />

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS v4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Custom Design System Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />

    @livewireStyles
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] antialiased min-h-screen flex flex-col">
    {{ $slot }}

    <script src="{{ asset('assets/js/main.js') }}"></script>
    @livewireScripts
</body>
</html>