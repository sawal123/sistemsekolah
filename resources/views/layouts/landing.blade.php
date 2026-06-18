@php
    $landingSettings = \App\Models\Setting::pluck('value', 'key');
    $schoolName = $landingSettings->get('school_name') ?: 'SMA Negeri 1 Cendekia';
    $siteTitle = $landingSettings->get('site_title') ?: 'Website Resmi Sekolah';
    $tagline = $landingSettings->get('site_tagline') ?: 'Mendidik dengan hati, membangun masa depan dengan ilmu dan karakter.';
    $schoolInitials = collect(explode(' ', $schoolName))
        ->filter()
        ->take(2)
        ->map(fn ($word) => strtoupper(mb_substr($word, 0, 1)))
        ->implode('') ?: 'SC';
    $templateSource = file_get_contents(resource_path('views/template.html'));
    preg_match_all('/<style\b[^>]*>.*?<\/style>/s', $templateSource, $templateStyles);
    $templateStyles = array_values(array_filter(
        $templateStyles[0] ?? [],
        fn ($style) => ! str_contains($style, 'id="tailwind-compiled"')
    ));
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? $siteTitle }} - {{ $schoolName }}</title>
    <meta name="description" content="{{ $landingSettings->get('seo_description') ?: 'Website resmi sekolah, informasi akademik, PPDB, fasilitas, dan berita terbaru.' }}">
    <meta name="keywords" content="{{ $landingSettings->get('seo_keywords') ?: 'sekolah, sma, ppdb, pendidikan, akademik' }}">

    @if($landingSettings->get('favicon'))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $landingSettings->get('favicon')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    {!! implode("\n", $templateStyles) !!}
    @livewireStyles
</head>
<body class="bg-white text-slate-800">
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md shadow-sm">
        <nav class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between h-20">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center text-white font-display font-bold text-lg overflow-hidden">
                    @if($landingSettings->get('logo'))
                        <img src="{{ asset('storage/' . $landingSettings->get('logo')) }}" alt="Logo {{ $schoolName }}" class="w-full h-full object-contain">
                    @else
                        {{ $schoolInitials }}
                    @endif
                </div>
                <div>
                    <p class="font-display font-bold text-blue-900 leading-tight">{{ $schoolName }}</p>
                    <p class="text-xs text-blue-500 leading-tight tracking-wide">{{ strtoupper($landingSettings->get('school_short_name') ?: 'CENDEKIA') }}</p>
                </div>
            </a>

            <ul class="hidden lg:flex items-center gap-5 text-sm font-medium text-slate-600">
                <li><a href="{{ route('home') }}" class="hover:text-blue-600 transition {{ request()->routeIs('home') ? 'text-blue-600' : '' }}">Beranda</a></li>
                <li><a href="{{ route('landing.about') }}" class="hover:text-blue-600 transition {{ request()->routeIs('landing.about') ? 'text-blue-600' : '' }}">Tentang</a></li>
                <li class="relative group">
                    <a href="{{ route('landing.staffing', 'guru') }}" class="flex items-center gap-1 hover:text-blue-600 transition {{ request()->routeIs('landing.staffing') ? 'text-blue-600' : '' }}">
                        Kepegawaian
                        <svg class="w-4 h-4 mt-0.5 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                    <div class="absolute left-0 top-full pt-3 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 translate-y-1 group-hover:translate-y-0">
                        <div class="bg-white rounded-xl shadow-xl border border-blue-50 p-2">
                            <a href="{{ route('landing.staffing', 'guru') }}" class="block px-4 py-2.5 rounded-lg text-sm hover:bg-blue-50 hover:text-blue-600 transition">Guru</a>
                            <a href="{{ route('landing.staffing', 'tata-usaha') }}" class="block px-4 py-2.5 rounded-lg text-sm hover:bg-blue-50 hover:text-blue-600 transition">Tata Usaha</a>
                            <a href="{{ route('landing.staffing', 'struktur-organisasi') }}" class="block px-4 py-2.5 rounded-lg text-sm hover:bg-blue-50 hover:text-blue-600 transition">Struktur Organisasi</a>
                        </div>
                    </div>
                </li>
                <li class="relative group">
                    <a href="{{ route('landing.academic', 'kurikulum') }}" class="flex items-center gap-1 hover:text-blue-600 transition {{ request()->routeIs('landing.academic') ? 'text-blue-600' : '' }}">
                        Akademik
                        <svg class="w-4 h-4 mt-0.5 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                    <div class="absolute left-0 top-full pt-3 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 translate-y-1 group-hover:translate-y-0">
                        <div class="bg-white rounded-xl shadow-xl border border-blue-50 p-2">
                            <a href="{{ route('landing.academic', 'ipa') }}" class="block px-4 py-2.5 rounded-lg text-sm hover:bg-blue-50 hover:text-blue-600 transition">Program IPA</a>
                            <a href="{{ route('landing.academic', 'ips') }}" class="block px-4 py-2.5 rounded-lg text-sm hover:bg-blue-50 hover:text-blue-600 transition">Program IPS</a>
                            <a href="{{ route('landing.academic', 'bahasa') }}" class="block px-4 py-2.5 rounded-lg text-sm hover:bg-blue-50 hover:text-blue-600 transition">Program Bahasa</a>
                            <a href="{{ route('landing.academic', 'kurikulum') }}" class="block px-4 py-2.5 rounded-lg text-sm hover:bg-blue-50 hover:text-blue-600 transition">Kurikulum</a>
                        </div>
                    </div>
                </li>
                <li><a href="{{ route('landing.facilities') }}" class="hover:text-blue-600 transition {{ request()->routeIs('landing.facilities') ? 'text-blue-600' : '' }}">Fasilitas</a></li>
                <li><a href="{{ route('landing.blog') }}" class="hover:text-blue-600 transition {{ request()->routeIs('landing.blog*') ? 'text-blue-600' : '' }}">Blog</a></li>
                <li class="relative group">
                    <a href="{{ route('landing.ppdb') }}" class="flex items-center gap-1 hover:text-blue-600 transition {{ request()->routeIs('landing.ppdb*') ? 'text-blue-600' : '' }}">
                        PPDB
                        <svg class="w-4 h-4 mt-0.5 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                    <div class="absolute left-0 top-full pt-3 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 translate-y-1 group-hover:translate-y-0">
                        <div class="bg-white rounded-xl shadow-xl border border-blue-50 p-2">
                            <a href="{{ route('landing.ppdb.section', 'syarat') }}#syarat" class="block px-4 py-2.5 rounded-lg text-sm hover:bg-blue-50 hover:text-blue-600 transition">Syarat Pendaftaran</a>
                            <a href="{{ route('landing.ppdb.section', 'jalur') }}#jalur" class="block px-4 py-2.5 rounded-lg text-sm hover:bg-blue-50 hover:text-blue-600 transition">Jalur Masuk</a>
                            <a href="{{ route('landing.ppdb.section', 'biaya') }}#biaya" class="block px-4 py-2.5 rounded-lg text-sm hover:bg-blue-50 hover:text-blue-600 transition">Biaya Sekolah</a>
                        </div>
                    </div>
                </li>
                <li><a href="{{ route('landing.contact') }}" class="hover:text-blue-600 transition {{ request()->routeIs('landing.contact') ? 'text-blue-600' : '' }}">Kontak</a></li>
            </ul>

            <div class="hidden lg:flex items-center gap-3">
                {{-- @auth
                    <a href="{{ route('dashboard') }}" class="text-blue-700 font-semibold text-sm hover:text-blue-900 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-blue-700 font-semibold text-sm hover:text-blue-900 transition">Masuk</a>
                @endauth --}}
                <a href="{{ route('landing.ppdb.section', 'daftar') }}#daftar" class="inline-flex bg-gradient-to-r from-blue-600 to-blue-500 text-white px-5 py-2.5 rounded-full font-semibold text-sm shadow-md hover:shadow-lg hover:from-blue-700 hover:to-blue-600 transition">
                    Daftar Sekarang
                </a>
            </div>

            <button id="menuBtn" type="button" class="lg:hidden text-blue-700">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </nav>

        <div id="mobileMenu" class="hidden lg:hidden border-t border-blue-100 bg-white">
            <div class="px-6 py-4 flex flex-col gap-1 font-medium text-slate-600">
                <a href="{{ route('home') }}" class="py-2.5 hover:text-blue-600">Beranda</a>
                <a href="{{ route('landing.about') }}" class="py-2.5 hover:text-blue-600">Tentang</a>
                <details class="group">
                    <summary class="py-2.5 cursor-pointer list-none flex justify-between items-center hover:text-blue-600">
                        Kepegawaian
                        <svg class="w-4 h-4 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div class="pl-4 pb-2 flex flex-col gap-1 text-sm">
                        <a href="{{ route('landing.staffing', 'guru') }}" class="py-2 hover:text-blue-600">Guru</a>
                        <a href="{{ route('landing.staffing', 'tata-usaha') }}" class="py-2 hover:text-blue-600">Tata Usaha</a>
                        <a href="{{ route('landing.staffing', 'struktur-organisasi') }}" class="py-2 hover:text-blue-600">Struktur Organisasi</a>
                    </div>
                </details>
                <details class="group">
                    <summary class="py-2.5 cursor-pointer list-none flex justify-between items-center hover:text-blue-600">
                        Akademik
                        <svg class="w-4 h-4 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div class="pl-4 pb-2 flex flex-col gap-1 text-sm">
                        <a href="{{ route('landing.academic', 'ipa') }}" class="py-2 hover:text-blue-600">Program IPA</a>
                        <a href="{{ route('landing.academic', 'ips') }}" class="py-2 hover:text-blue-600">Program IPS</a>
                        <a href="{{ route('landing.academic', 'bahasa') }}" class="py-2 hover:text-blue-600">Program Bahasa</a>
                        <a href="{{ route('landing.academic', 'kurikulum') }}" class="py-2 hover:text-blue-600">Kurikulum</a>
                    </div>
                </details>
                <a href="{{ route('landing.facilities') }}" class="py-2.5 hover:text-blue-600">Fasilitas</a>
                <a href="{{ route('landing.blog') }}" class="py-2.5 hover:text-blue-600">Blog</a>
                <details class="group">
                    <summary class="py-2.5 cursor-pointer list-none flex justify-between items-center hover:text-blue-600">
                        PPDB
                        <svg class="w-4 h-4 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div class="pl-4 pb-2 flex flex-col gap-1 text-sm">
                        <a href="{{ route('landing.ppdb.section', 'syarat') }}#syarat" class="py-2 hover:text-blue-600">Syarat Pendaftaran</a>
                        <a href="{{ route('landing.ppdb.section', 'jalur') }}#jalur" class="py-2 hover:text-blue-600">Jalur Masuk</a>
                        <a href="{{ route('landing.ppdb.section', 'biaya') }}#biaya" class="py-2 hover:text-blue-600">Biaya Sekolah</a>
                    </div>
                </details>
                @auth
                    <a href="{{ route('dashboard') }}" class="py-2.5 hover:text-blue-600">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="py-2.5 hover:text-blue-600">Masuk</a>
                @endauth
                <a href="{{ route('landing.contact') }}" class="py-2.5 hover:text-blue-600">Kontak</a>
                <a href="{{ route('landing.ppdb.section', 'daftar') }}#daftar" class="mt-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white text-center px-5 py-3 rounded-full font-semibold text-sm">Daftar Sekarang</a>
            </div>
        </div>
    </header>

    {{ $slot }}

    <footer id="kontak" class="bg-blue-900 text-blue-100 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-10">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-400 to-blue-200 flex items-center justify-center text-blue-900 font-display font-bold text-lg overflow-hidden">
                        @if($landingSettings->get('logo'))
                            <img src="{{ asset('storage/' . $landingSettings->get('logo')) }}" alt="Logo {{ $schoolName }}" class="w-full h-full object-contain">
                        @else
                            {{ $schoolInitials }}
                        @endif
                    </div>
                    <p class="font-display font-bold text-white">{{ $schoolName }}</p>
                </div>
                <p class="text-sm text-blue-200">{{ $tagline }}</p>
            </div>
            <div>
                <h4 class="font-display font-bold text-white mb-4">Tautan</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('landing.about') }}" class="hover:text-white transition">Tentang Sekolah</a></li>
                    <li><a href="{{ route('landing.staffing', 'guru') }}" class="hover:text-white transition">Kepegawaian</a></li>
                    <li><a href="{{ route('landing.facilities') }}" class="hover:text-white transition">Fasilitas</a></li>
                    <li><a href="{{ route('landing.blog') }}" class="hover:text-white transition">Blog</a></li>
                    <li><a href="{{ route('landing.ppdb.section', 'daftar') }}#daftar" class="hover:text-white transition">PPDB Online</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display font-bold text-white mb-4">Jurusan</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('landing.academic', 'ipa') }}" class="hover:text-white transition">IPA</a></li>
                    <li><a href="{{ route('landing.academic', 'ips') }}" class="hover:text-white transition">IPS</a></li>
                    <li><a href="{{ route('landing.academic', 'bahasa') }}" class="hover:text-white transition">Bahasa</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display font-bold text-white mb-4">Kontak</h4>
                <ul class="space-y-2 text-sm text-blue-200">
                    <li>{{ $landingSettings->get('school_address') ?: 'Jl. Pendidikan No. 17, Medan, Sumatera Utara' }}</li>
                    <li>{{ $landingSettings->get('school_phone') ?: '(061) 1234-5678' }}</li>
                    <li>{{ $landingSettings->get('school_email') ?: 'info@sman1cendekia.sch.id' }}</li>
                    @if($landingSettings->get('whatsapp'))
                        <li>WhatsApp: {{ $landingSettings->get('whatsapp') }}</li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 border-t border-blue-800 pt-6 text-center text-sm text-blue-300">
            &copy; {{ date('Y') }} {{ $schoolName }}. Seluruh hak cipta dilindungi.
        </div>
    </footer>

    @livewireScripts
    <script>
        function setupLandingTemplate() {
            const menuBtn = document.getElementById('menuBtn');
            const mobileMenu = document.getElementById('mobileMenu');

            if (menuBtn && mobileMenu && !menuBtn.dataset.bound) {
                menuBtn.dataset.bound = 'true';
                menuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('[data-dot]');
            const contents = document.querySelectorAll('[data-slide-content]');
            let current = Array.from(slides).findIndex((slide) => slide.classList.contains('active'));
            current = current >= 0 ? current : 0;

            if (window.landingSliderTimer) {
                clearInterval(window.landingSliderTimer);
            }

            window.changeSlide = function (i) {
                if (!slides.length || !dots.length || !slides[i]) {
                    return;
                }

                slides[current]?.classList.remove('active');
                contents[current]?.classList.add('hidden');
                dots[current]?.classList.remove('bg-white');
                dots[current]?.classList.add('bg-white/40');
                dots[current]?.setAttribute('aria-current', 'false');

                current = i;
                slides[current].classList.add('active');
                contents[current]?.classList.remove('hidden');
                dots[current]?.classList.remove('bg-white/40');
                dots[current]?.classList.add('bg-white');
                dots[current]?.setAttribute('aria-current', 'true');
            };

            window.nextSlide = function () {
                if (slides.length > 1) {
                    window.changeSlide((current + 1) % slides.length);
                }
            };

            window.prevSlide = function () {
                if (slides.length > 1) {
                    window.changeSlide((current - 1 + slides.length) % slides.length);
                }
            };

            if (slides.length > 1) {
                window.landingSliderTimer = setInterval(() => {
                    window.nextSlide();
                }, 5000);
            }
        }

        document.addEventListener('DOMContentLoaded', setupLandingTemplate);
        document.addEventListener('livewire:navigated', setupLandingTemplate);
    </script>
</body>
</html>
