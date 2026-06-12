<!DOCTYPE html>
<html lang="id" class="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $settings->get('site_title') ?? 'Website Resmi Sekolah' }} — {{ $settings->get('school_name') ?? 'SMA Nusantara' }}</title>
    <meta name="description" content="{{ $settings->get('seo_description') ?? 'Website Resmi Sekolah — Informasi Akademik, PPDB Online, dan Berita Terbaru.' }}" />
    <meta name="keywords" content="{{ $settings->get('seo_keywords') ?? 'sekolah, sma, ppdb, e-rapor' }}" />
    
    @if($settings->get('favicon'))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings->get('favicon')) }}" />
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    @endif

    {{-- Fonts & CSS --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe',
                            300: '#c084fc', 400: '#a855f7', 500: '#6366f1',
                            600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81'
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        html {
            scroll-behavior: smooth;
        }
        body {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(ellipse 80% 60% at 10% 15%, rgba(99, 102, 241, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 85% 85%, rgba(168, 85, 247, 0.06) 0%, transparent 55%);
            background-attachment: fixed;
            transition: background-color 0.3s, color 0.3s;
        }
        .dark body {
            background-color: #0c0e1a;
            background-image: 
                radial-gradient(ellipse 80% 60% at 10% 15%, rgba(99, 102, 241, 0.15) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 85% 85%, rgba(168, 85, 247, 0.10) 0%, transparent 55%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 45px -12px rgba(99, 102, 241, 0.15);
        }
        .dark .glass-card:hover {
            box-shadow: 0 20px 45px -12px rgba(0, 0, 0, 0.5);
        }
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="font-sans antialiased text-slate-800 dark:text-slate-200" 
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          },
          init() {
              if (this.darkMode || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                  this.darkMode = true;
                  document.documentElement.classList.add('dark');
              } else {
                  this.darkMode = false;
                  document.documentElement.classList.remove('dark');
              }
          }
      }" x-init="init()">

    {{-- NAVIGATION BAR --}}
    <nav class="sticky top-0 z-[1000] w-full glass transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            {{-- Brand Logo & Name --}}
            <a href="#" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center overflow-hidden" 
                     style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                    @if($settings->get('logo'))
                        <img src="{{ asset('storage/' . $settings->get('logo')) }}" class="w-full h-full object-contain" alt="Logo">
                    @else
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    @endif
                </div>
                <div>
                    <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-indigo-500 to-purple-500 bg-clip-text text-transparent block leading-none">
                        {{ $settings->get('school_name') ?? 'SMA Nusantara' }}
                    </span>
                    @if($settings->get('site_tagline'))
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 tracking-wide font-medium block mt-0.5">
                            {{ $settings->get('site_tagline') }}
                        </span>
                    @endif
                </div>
            </a>

            {{-- Nav Menu Links --}}
            <div class="hidden lg:flex items-center gap-8 font-semibold text-sm">
                <a href="#beranda" class="text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors">Beranda</a>
                <a href="#sambutan" class="text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors">Sambutan</a>
                <a href="#visimisi" class="text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors">Visi & Misi</a>
                <a href="#fasilitas" class="text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors">Fasilitas</a>
                <a href="#berita" class="text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors">Berita</a>
                <a href="#kontak" class="text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors">Hubungi Kami</a>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-4">
                {{-- Dark Mode Toggle --}}
                <button @click="toggleTheme()" 
                        class="p-2.5 rounded-2xl bg-indigo-500/5 border border-indigo-500/10 hover:bg-indigo-500/10 text-indigo-500 transition-all cursor-pointer">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.243 17.657l.707-.707M6.343 6.343l.707-.707M14.25 12a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    <svg x-show="darkMode" x-cloak class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="hidden sm:inline-flex items-center justify-center px-6 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm tracking-wide shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/35 transition-all">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="hidden sm:inline-flex items-center justify-center px-6 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm tracking-wide shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/35 transition-all">
                        Masuk
                    </a>
                @endauth
                
                {{-- PPDB CTA --}}
                <a href="{{ route('admin.ppdb.pendaftaran-murid-baru') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm tracking-wide shadow-lg shadow-purple-600/20 hover:shadow-purple-600/35 transition-all">
                    PPDB Online
                </a>
            </div>
        </div>
    </nav>

    {{-- HERO BANNER SLIDER --}}
    <section id="beranda" class="relative overflow-hidden w-full bg-slate-900" style="height: 560px;" 
             x-data="{ 
                 activeSlide: 0,
                 slideCount: {{ count($sliders) }},
                 next() { this.activeSlide = (this.activeSlide + 1) % this.slideCount },
                 prev() { this.activeSlide = (this.activeSlide - 1 + this.slideCount) % this.slideCount },
                 autoPlay() { setInterval(() => this.next(), 6000) }
             }" x-init="autoPlay()">
        
        @if(count($sliders) > 0)
            @foreach($sliders as $index => $slider)
                <div class="absolute inset-0 w-full h-full transition-all duration-1000 transform"
                     x-show="activeSlide === {{ $index }}"
                     x-transition:enter="transition ease-out duration-1000"
                     x-transition:enter-start="opacity-0 scale-105"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-1000"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     style="display: none;">
                    
                    {{-- Slide Image --}}
                    @if($slider->foto)
                        <img src="{{ asset('storage/' . $slider->foto) }}" class="absolute inset-0 w-full h-full object-cover filter brightness-50" alt="{{ $slider->judul }}">
                    @else
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-slate-900 to-indigo-950 flex items-center justify-center"></div>
                    @endif

                    {{-- Slide Content Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                    
                    <div class="absolute inset-0 flex items-center justify-center text-center p-6 z-10">
                        <div class="max-w-4xl mx-auto space-y-6">
                            <h2 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight">
                                {{ $slider->judul }}
                            </h2>
                            @if($slider->deskripsi)
                                <p class="text-slate-200 text-base sm:text-xl max-w-2xl mx-auto font-medium leading-relaxed">
                                    {{ $slider->deskripsi }}
                                </p>
                            @endif
                            
                            <div class="pt-4 flex items-center justify-center gap-4">
                                <a href="#sambutan" class="px-7 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold tracking-wide shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 transition-all text-sm">
                                    Tentang Kami
                                </a>
                                <a href="#visimisi" class="px-7 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold tracking-wide backdrop-blur-md transition-all text-sm">
                                    Visi & Misi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            {{-- Navigation Arrows --}}
            @if(count($sliders) > 1)
                <button @click="prev()" class="absolute left-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/10 text-white flex items-center justify-center backdrop-blur-md transition-colors cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="next()" class="absolute right-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/10 text-white flex items-center justify-center backdrop-blur-md transition-colors cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                {{-- Indicators --}}
                <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex gap-2.5">
                    @foreach($sliders as $index => $slider)
                        <button @click="activeSlide = {{ $index }}" 
                                class="w-3.5 h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                                :class="activeSlide === {{ $index }} ? 'w-8 bg-indigo-500' : 'bg-white/40'"></button>
                    @endforeach
                </div>
            @endif
        @else
            {{-- Fallback Banner --}}
            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-slate-900 to-indigo-950 flex items-center justify-center text-center p-6">
                <div class="max-w-3xl mx-auto space-y-6">
                    <h2 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight">
                        Selamat Datang di {{ $settings->get('school_name') ?? 'SMA Nusantara' }}
                    </h2>
                    <p class="text-slate-300 text-lg sm:text-xl max-w-2xl mx-auto font-medium">
                        {{ $settings->get('site_tagline') ?? 'Membina Prestasi, Mengukir Karakter, Mengembangkan Potensi Global.' }}
                    </p>
                    <div class="pt-4">
                        <a href="{{ route('admin.ppdb.pendaftaran-murid-baru') }}" class="px-7 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold tracking-wide shadow-lg shadow-indigo-600/30 transition-all">
                            Pendaftaran PPDB Online
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </section>

    {{-- SAMBUTAN KEPALA SEKOLAH --}}
    @if($settings->get('principal_greeting'))
        <section id="sambutan" class="py-24 max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-[380px_1fr] gap-12 lg:gap-16 items-center">
                {{-- Principal Photo & Info Card --}}
                <div class="flex flex-col items-center text-center lg:items-start lg:text-left">
                    <div class="relative w-64 h-80 sm:w-72 sm:h-96 rounded-[32px] overflow-hidden shadow-2xl border-4 border-white dark:border-slate-800 bg-slate-100 dark:bg-slate-800 transform rotate-1 hover:rotate-0 transition-transform duration-500">
                        @if($settings->get('principal_image'))
                            <img src="{{ asset('storage/' . $settings->get('principal_image')) }}" class="w-full h-full object-cover" alt="Foto Kepala Sekolah">
                        @else
                            {{-- Fallback avatar --}}
                            <div class="w-full h-full bg-gradient-to-br from-indigo-500/10 to-purple-500/10 flex items-center justify-center">
                                <svg class="w-24 h-24 text-indigo-500/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="text-xl font-extrabold text-slate-900 dark:text-white leading-tight">
                            {{ $settings->get('principal_name') ?? 'Nama Kepala Sekolah' }}
                        </h4>
                        <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 mt-1">
                            {{ $settings->get('principal_title') ?? 'Kepala Sekolah' }}
                        </p>
                        @if($settings->get('principal_nip'))
                            <p class="text-xs text-slate-400 mt-0.5">
                                NIP. {{ $settings->get('principal_nip') }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Greeting speech content --}}
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 text-xs font-bold uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Sambutan Hangat
                    </div>
                    
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Sambutan Kepala Sekolah
                    </h3>
                    
                    <div class="h-1 w-20 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full"></div>
                    
                    <div class="text-slate-600 dark:text-slate-300 text-base sm:text-lg leading-relaxed font-medium space-y-4 whitespace-pre-line">
                        {!! e($settings->get('principal_greeting')) !!}
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- VISION AND MISSION SECTION --}}
    @if($settings->get('school_vision') || $settings->get('school_mission'))
        <section id="visimisi" class="py-24 bg-indigo-500/[0.02] dark:bg-indigo-500/[0.01] border-y border-indigo-500/5">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-500 dark:text-purple-400 text-xs font-bold uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Komitmen Bersama
                    </div>
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Visi & Misi Sekolah
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm sm:text-base max-w-xl mx-auto">
                        Fokus utama kami dalam membimbing generasi penerus bangsa yang unggul, cerdas, berkarakter, dan berdaya saing global.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-stretch">
                    {{-- Vision card --}}
                    @if($settings->get('school_vision'))
                        <div class="glass-card p-8 sm:p-10 flex flex-col justify-between" style="border-left: 6px solid #6366f1;">
                            <div class="space-y-6">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 dark:text-indigo-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                                <h4 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Visi Sekolah</h4>
                                <p class="text-slate-600 dark:text-slate-300 text-lg leading-relaxed font-medium italic">
                                    "{{ $settings->get('school_vision') }}"
                                </p>
                            </div>
                            
                            <div class="mt-8 text-xs text-slate-400 dark:text-slate-500 font-semibold tracking-wide">
                                DIUMUMKAN OLEH: {{ $settings->get('school_name') ?? 'SMA Nusantara' }}
                            </div>
                        </div>
                    @endif

                    {{-- Mission card --}}
                    @if($settings->get('school_mission'))
                        <div class="glass-card p-8 sm:p-10 flex flex-col justify-between" style="border-left: 6px solid #a855f7;">
                            <div class="space-y-6">
                                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-500 dark:text-purple-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <h4 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Misi Sekolah</h4>
                                
                                <ul class="space-y-4">
                                    @php
                                        // Parse mission from newline string into list elements
                                        $missions = array_filter(array_map('trim', explode("\n", $settings->get('school_mission'))));
                                    @endphp
                                    @foreach($missions as $mission)
                                        <li class="flex items-start gap-3.5">
                                            <span class="w-6 h-6 rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                                                {{ $loop->iteration }}
                                            </span>
                                            <span class="text-slate-600 dark:text-slate-300 text-sm sm:text-base font-semibold leading-relaxed">
                                                {{ $mission }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            <div class="mt-8 text-xs text-slate-400 dark:text-slate-500 font-semibold tracking-wide">
                                UPDATED: TAHUN AJARAN 2025/2026
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- FACILITIES SECTION --}}
    @if(count($facilities) > 0)
        <section id="fasilitas" class="py-24 max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sarana Prasarana
                    </div>
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Fasilitas Unggulan Sekolah
                    </h3>
                </div>
                <a href="{{ route('admin.fasilitas') }}" 
                   class="inline-flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold hover:gap-3 transition-all">
                    Lihat Semua Fasilitas
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($facilities as $item)
                    <div class="glass-card overflow-hidden flex flex-col h-full">
                        <div class="h-52 w-full overflow-hidden bg-slate-100 dark:bg-slate-800 relative">
                            @if($item->foto)
                                <img src="{{ asset('storage/' . $item->foto) }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" alt="{{ $item->nama_fasilitas }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400 dark:text-slate-600">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            @endif
                            <span class="absolute top-4 right-4 bg-slate-900/60 backdrop-blur-md text-white text-[11px] font-bold px-3 py-1 rounded-full border border-white/10">
                                {{ $item->kategori }}
                            </span>
                        </div>
                        
                        <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <h4 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $item->nama_fasilitas }}</h4>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 font-semibold">Lokasi: {{ $item->lokasi ?? 'Area Sekolah' }}</p>
                                <p class="text-slate-500 dark:text-slate-400 text-sm mt-3 leading-relaxed line-clamp-3">
                                    {{ $item->deskripsi ?? 'Fasilitas pembelajaran sekolah guna mendukung kegiatan belajar mengajar secara optimal.' }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800 text-xs font-semibold">
                                <span class="text-slate-500">Jumlah: {{ $item->jumlah }} Unit</span>
                                <span class="px-2.5 py-0.5 rounded-full {{ $item->kondisi === 'Baik' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500' }}">
                                    {{ $item->kondisi }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- LATEST NEWS SECTION --}}
    @if(count($posts) > 0)
        <section id="berita" class="py-24 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-900">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                    <div class="space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 text-xs font-bold uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Informasi Terkini
                        </div>
                        <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Artikel & Berita Sekolah
                        </h3>
                    </div>
                    <a href="{{ route('admin.website.blog-artikel') }}" 
                       class="inline-flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold hover:gap-3 transition-all">
                        Semua Artikel & Berita
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        <div class="glass-card overflow-hidden flex flex-col h-full bg-white dark:bg-slate-900">
                            <div class="h-48 w-full overflow-hidden bg-slate-100 dark:bg-slate-850 relative">
                                @if($post->thumbnail)
                                    <img src="{{ asset('storage/' . $post->thumbnail) }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" alt="{{ $post->judul }}">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-500/5 to-purple-500/5 flex items-center justify-center text-slate-300 dark:text-slate-700">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h9l5 5v9a2 2 0 01-2 2zM14 4v5h5M8 13h8M8 17h5" />
                                        </svg>
                                    </div>
                                @endif
                                <span class="absolute top-4 left-4 bg-indigo-600 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                    {{ $post->kategori->nama ?? 'Umum' }}
                                </span>
                            </div>

                            <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                <div>
                                    <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 font-semibold mb-2">
                                        <span>Oleh: {{ $post->user->name ?? 'Admin' }}</span>
                                        <span>•</span>
                                        <span>{{ $post->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h4 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight leading-snug line-clamp-2 hover:text-indigo-600 transition-colors">
                                        <a href="#">{{ $post->judul }}</a>
                                    </h4>
                                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-3 leading-relaxed line-clamp-3">
                                        {{ Str::limit(strip_tags($post->konten), 120) }}
                                    </p>
                                </div>
                                
                                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold">
                                    <span class="text-slate-400">Pembaca: {{ $post->views ?? 0 }} kali</span>
                                    <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:underline">Baca Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA ENROLL BANNER --}}
    <section class="py-20 relative overflow-hidden bg-slate-900 text-white flex items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-900/80 to-purple-900/80 z-10"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,rgba(99,102,241,0.2),transparent_50%)]"></div>
        
        <div class="relative z-20 max-w-4xl mx-auto px-6 text-center space-y-8">
            <h3 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                Pendaftaran Murid Baru Telah Dibuka!
            </h3>
            <p class="text-slate-200 text-lg max-w-2xl mx-auto font-medium leading-relaxed">
                Bergabunglah bersama kami di {{ $settings->get('school_name') ?? 'SMA Nusantara' }} dan dapatkan pendidikan berkualitas unggul dengan fasilitas lengkap bersertifikasi nasional.
            </p>
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('admin.ppdb.pendaftaran-murid-baru') }}"
                   class="px-8 py-4 rounded-2xl bg-white text-indigo-900 font-extrabold shadow-2xl shadow-white/10 hover:bg-slate-100 hover:shadow-white/20 transition-all text-sm sm:text-base tracking-wide w-full sm:w-auto">
                    Daftar Sekarang Online
                </a>
                <a href="https://wa.me/{{ $settings->get('whatsapp') }}" target="_blank"
                   class="px-8 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white border border-indigo-500 font-extrabold shadow-lg shadow-indigo-600/20 transition-all text-sm sm:text-base tracking-wide flex items-center justify-center gap-2 w-full sm:w-auto">
                    Hubungi via WhatsApp
                </a>
            </div>
        </div>
    </section>

    {{-- FOOTER SECTION --}}
    <footer id="kontak" class="bg-slate-950 text-slate-400 border-t border-slate-900 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-16">
            {{-- Column 1: School Identity --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center overflow-hidden" 
                         style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        @if($settings->get('logo'))
                            <img src="{{ asset('storage/' . $settings->get('logo')) }}" class="w-full h-full object-contain" alt="Logo">
                        @else
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z" />
                            </svg>
                        @endif
                    </div>
                    <span class="font-extrabold text-base tracking-tight text-white leading-none">
                        {{ $settings->get('school_name') ?? 'SMA Nusantara' }}
                    </span>
                </div>
                <p class="text-sm font-medium leading-relaxed">
                    {{ $settings->get('site_tagline') ?? 'Membina Prestasi, Mengukir Karakter, Mengembangkan Potensi Global.' }}
                </p>
                <div class="flex items-center gap-3 pt-3">
                    @if($settings->get('facebook_url'))
                        <a href="{{ $settings->get('facebook_url') }}" class="w-9 h-9 rounded-xl bg-slate-900 hover:bg-indigo-600 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                            Facebook
                        </a>
                    @endif
                    @if($settings->get('instagram_url'))
                        <a href="{{ $settings->get('instagram_url') }}" class="w-9 h-9 rounded-xl bg-slate-900 hover:bg-indigo-600 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                            Instagram
                        </a>
                    @endif
                    @if($settings->get('youtube_url'))
                        <a href="{{ $settings->get('youtube_url') }}" class="w-9 h-9 rounded-xl bg-slate-900 hover:bg-indigo-600 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                            YouTube
                        </a>
                    @endif
                    @if($settings->get('tiktok_url'))
                        <a href="{{ $settings->get('tiktok_url') }}" class="w-9 h-9 rounded-xl bg-slate-900 hover:bg-indigo-600 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                            TikTok
                        </a>
                    @endif
                </div>
            </div>

            {{-- Column 2: Navigation Links --}}
            <div class="space-y-4">
                <h4 class="font-bold text-white tracking-wide text-sm uppercase">Tautan Cepat</h4>
                <div class="flex flex-col gap-2.5 font-semibold text-sm">
                    <a href="#sambutan" class="hover:text-white transition-colors">Sambutan Sekolah</a>
                    <a href="#visimisi" class="hover:text-white transition-colors">Visi & Misi</a>
                    <a href="#fasilitas" class="hover:text-white transition-colors">Fasilitas Unggulan</a>
                    <a href="#berita" class="hover:text-white transition-colors">Berita & Pengumuman</a>
                    <a href="{{ route('admin.ppdb.pendaftaran-murid-baru') }}" class="hover:text-white transition-colors">Pendaftaran PPDB</a>
                </div>
            </div>

            {{-- Column 3: Contact Info --}}
            <div class="space-y-4">
                <h4 class="font-bold text-white tracking-wide text-sm uppercase">Kontak Sekolah</h4>
                <div class="space-y-3 text-sm font-semibold">
                    @if($settings->get('school_address'))
                        <p class="leading-relaxed">
                            <span class="text-white block font-bold">Alamat:</span>
                            {{ $settings->get('school_address') }}
                        </p>
                    @endif
                    @if($settings->get('school_phone'))
                        <p><span class="text-white font-bold">Telp:</span> {{ $settings->get('school_phone') }}</p>
                    @endif
                    @if($settings->get('school_email'))
                        <p><span class="text-white font-bold">Email:</span> {{ $settings->get('school_email') }}</p>
                    @endif
                    @if($settings->get('whatsapp'))
                        <p><span class="text-white font-bold">WhatsApp:</span> +{{ $settings->get('whatsapp') }}</p>
                    @endif
                </div>
            </div>

            {{-- Column 4: Location Map/Footer CTA --}}
            <div class="space-y-4">
                <h4 class="font-bold text-white tracking-wide text-sm uppercase">Hubungi PPDB</h4>
                <p class="text-sm leading-relaxed">
                    Penerimaan Peserta Didik Baru (PPDB) dibuka setiap tahunnya. Silakan mendaftar secara mandiri melalui website portal PPDB resmi.
                </p>
                <a href="{{ route('admin.ppdb.pendaftaran-murid-baru') }}"
                   class="inline-flex items-center justify-center w-full px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-colors">
                    Portal PPDB Online
                </a>
            </div>
        </div>

        {{-- Bottom Footer --}}
        <div class="max-w-7xl mx-auto px-6 mt-16 pt-8 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-semibold">
            <p>
                &copy; {{ date('Y') }} {{ $settings->get('school_name') ?? 'SMA Nusantara' }}. Semua Hak Dilindungi.
            </p>
            <div class="flex gap-4">
                <a href="{{ route('login') }}" class="hover:text-white transition-colors">Sistem Informasi Akademik</a>
                <span>•</span>
                <p>Website Resmi Sekolah v2.0</p>
            </div>
        </div>
    </footer>

</body>

</html>
