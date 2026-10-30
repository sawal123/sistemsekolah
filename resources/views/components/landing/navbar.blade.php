<!-- COMPONENT: TopInfoBar -->
<div class="bg-[#0F172A] text-slate-300 text-xs border-b border-slate-800">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-10 flex items-center justify-between">
    <div class="flex items-center gap-4 sm:gap-6 truncate">
      <span class="inline-flex items-center gap-1.5">
        <span class="h-2 w-2 rounded-full bg-[#F59E0B]"></span>
        <span class="font-medium text-white">PPDB Tahun Ajaran 2026/2027 Gelombang I Dibuka</span>
      </span>
      <span class="hidden md:inline-flex items-center gap-1.5 text-slate-400">
        <svg class="w-3.5 h-3.5 text-[#0EA5E9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        info@namasekolah.sch.id
      </span>
    </div>
    <div class="flex items-center gap-4 shrink-0">
      <a href="#pengumuman" class="hover:text-white transition-colors hidden sm:inline">Pusat Pengumuman</a>
      <span class="text-slate-700 hidden sm:inline">|</span>
      <a href="#agenda" class="hover:text-white transition-colors">Kalender Akademik</a>
    </div>
  </div>
</div>
<!-- /COMPONENT: TopInfoBar -->

<!-- COMPONENT: Navbar -->
<header id="site-header" class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition-all duration-200">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-20">
      <!-- Brand Identity -->
      <a href="{{ route('home') }}" class="flex items-center gap-3.5 group focus:outline-none">
        <img src="{{ asset('assets/images/logo-sekolah.svg') }}" alt="Logo Nama Sekolah" class="w-11 h-11 rounded-xl shadow-sm group-hover:scale-105 transition-transform duration-200" />
        <div>
          <span class="block font-heading font-bold text-lg sm:text-xl text-[#0F172A] tracking-tight leading-none">Nama Sekolah</span>
          <span class="block text-xs font-medium text-[#64748B] mt-1">Institusi Pendidikan Menengah Modern</span>
        </div>
      </a>

      <!-- Desktop Navigation -->
      <nav class="hidden xl:flex items-center gap-1" aria-label="Navigasi Utama">
        <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('home') ? 'text-[#2563EB] font-semibold' : 'text-[#0F172A] hover:text-[#2563EB] font-medium' }}">Beranda</a>

        <!-- Dropdown: Profil Sekolah -->
        <div class="relative nav-dropdown">
          <button type="button" data-dropdown-toggle aria-expanded="false" class="px-3 py-2 rounded-lg text-sm inline-flex items-center gap-1.5 transition-colors text-[#0F172A] hover:text-[#2563EB] font-medium">
            <span>Profil Sekolah</span>
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div class="nav-dropdown-menu absolute left-0 mt-1 w-64 bg-white rounded-2xl shadow-xl border border-slate-200/90 p-2 z-50">
            <a href="{{ Route::has('landing.about') ? route('landing.about') : '#' }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
              <div class="w-9 h-9 rounded-lg bg-blue-50 text-[#2563EB] flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
              </div>
              <div>
                <span class="block text-sm font-semibold text-[#0F172A]">Tentang &amp; Sejarah</span>
                <span class="block text-xs text-[#64748B]">Visi, misi, budaya &amp; identitas</span>
              </div>
            </a>
            <a href="{{ Route::has('landing.staffing') ? route('landing.staffing', 'guru') : '#' }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
              <div class="w-9 h-9 rounded-lg bg-sky-50 text-[#0EA5E9] flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </div>
              <div>
                <span class="block text-sm font-semibold text-[#0F172A]">Guru &amp; Kepegawaian</span>
                <span class="block text-xs text-[#64748B]">Direktori pendidik &amp; struktur</span>
              </div>
            </a>
          </div>
        </div>

        <!-- Dropdown: Akademik -->
        <div class="relative nav-dropdown">
          <button type="button" data-dropdown-toggle aria-expanded="false" class="px-3 py-2 rounded-lg text-sm inline-flex items-center gap-1.5 transition-colors text-[#0F172A] hover:text-[#2563EB] font-medium">
            <span>Akademik</span>
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div class="nav-dropdown-menu absolute left-0 mt-1 w-64 bg-white rounded-2xl shadow-xl border border-slate-200/90 p-2 z-50">
            <a href="{{ Route::has('landing.academic') ? route('landing.academic', 'kurikulum') : '#' }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
              <div class="w-9 h-9 rounded-lg bg-blue-50 text-[#2563EB] flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
              </div>
              <div>
                <span class="block text-sm font-semibold text-[#0F172A]">Kurikulum &amp; Program</span>
                <span class="block text-xs text-[#64748B]">Kurikulum Nasional, IPA, IPS, Bahasa</span>
              </div>
            </a>
            <a href="#agenda" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
              <div class="w-9 h-9 rounded-lg bg-amber-50 text-[#F59E0B] flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
              <div>
                <span class="block text-sm font-semibold text-[#0F172A]">Agenda Akademik</span>
                <span class="block text-xs text-[#64748B]">Jadwal kegiatan &amp; ujian sekolah</span>
              </div>
            </a>
          </div>
        </div>

        <!-- Dropdown: Kesiswaan -->
        <div class="relative nav-dropdown">
          <button type="button" data-dropdown-toggle aria-expanded="false" class="px-3 py-2 rounded-lg text-sm inline-flex items-center gap-1.5 transition-colors text-[#0F172A] hover:text-[#2563EB] font-medium">
            <span>Kesiswaan</span>
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div class="nav-dropdown-menu absolute left-0 mt-1 w-64 bg-white rounded-2xl shadow-xl border border-slate-200/90 p-2 z-50">
            <a href="#prestasi" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
              <div class="w-9 h-9 rounded-lg bg-amber-50 text-[#F59E0B] flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
              </div>
              <div>
                <span class="block text-sm font-semibold text-[#0F172A]">Prestasi Sekolah</span>
                <span class="block text-xs text-[#64748B]">Capaian akademik &amp; non-akademik</span>
              </div>
            </a>
            <a href="#galeri" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
              <div class="w-9 h-9 rounded-lg bg-blue-50 text-[#2563EB] flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
              <div>
                <span class="block text-sm font-semibold text-[#0F172A]">Galeri Kegiatan</span>
                <span class="block text-xs text-[#64748B]">Dokumentasi aktivitas siswa</span>
              </div>
            </a>
            <a href="#alumni" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
              <div class="w-9 h-9 rounded-lg bg-sky-50 text-[#0EA5E9] flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
              </div>
              <div>
                <span class="block text-sm font-semibold text-[#0F172A]">Jejaring Alumni</span>
                <span class="block text-xs text-[#64748B]">Kisah inspiratif &amp; ikatan alumni</span>
              </div>
            </a>
          </div>
        </div>

        <a href="{{ Route::has('landing.facilities') ? route('landing.facilities') : '#' }}" class="px-3 py-2 rounded-lg text-sm transition-colors text-[#0F172A] hover:text-[#2563EB] font-medium">Fasilitas</a>

        <!-- Dropdown: Informasi -->
        <div class="relative nav-dropdown">
          <button type="button" data-dropdown-toggle aria-expanded="false" class="px-3 py-2 rounded-lg text-sm inline-flex items-center gap-1.5 transition-colors text-[#0F172A] hover:text-[#2563EB] font-medium">
            <span>Informasi</span>
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div class="nav-dropdown-menu absolute left-0 mt-1 w-64 bg-white rounded-2xl shadow-xl border border-slate-200/90 p-2 z-50">
            <a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
              <div class="w-9 h-9 rounded-lg bg-blue-50 text-[#2563EB] flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
              </div>
              <div>
                <span class="block text-sm font-semibold text-[#0F172A]">Berita &amp; Artikel</span>
                <span class="block text-xs text-[#64748B]">Kabar terbaru &amp; opini pendidikan</span>
              </div>
            </a>
            <a href="#pengumuman" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
              <div class="w-9 h-9 rounded-lg bg-amber-50 text-[#F59E0B] flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
              </div>
              <div>
                <span class="block text-sm font-semibold text-[#0F172A]">Pengumuman Resmi</span>
                <span class="block text-xs text-[#64748B]">Edaran &amp; dokumen unduhan</span>
              </div>
            </a>
            <a href="#agenda" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
              <div class="w-9 h-9 rounded-lg bg-sky-50 text-[#0EA5E9] flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
              <div>
                <span class="block text-sm font-semibold text-[#0F172A]">Agenda Sekolah</span>
                <span class="block text-xs text-[#64748B]">Jadwal acara &amp; kegiatan</span>
              </div>
            </a>
          </div>
        </div>

        <a href="{{ Route::has('landing.ppdb') ? route('landing.ppdb') : '#' }}" class="px-3 py-2 rounded-lg text-sm transition-colors text-[#0F172A] hover:text-[#2563EB] font-medium">PPDB</a>
        <a href="{{ Route::has('landing.contact') ? route('landing.contact') : '#' }}" class="px-3 py-2 rounded-lg text-sm transition-colors text-[#0F172A] hover:text-[#2563EB] font-medium">Kontak</a>
      </nav>

      <!-- Desktop Action Buttons -->
      <div class="hidden xl:flex items-center gap-3">
        <a href="{{ Route::has('landing.ppdb') ? route('landing.ppdb') : '#' }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-[#2563EB] bg-blue-50 hover:bg-blue-100 transition-colors">
          Info PPDB
        </a>
        <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#0F172A] hover:bg-[#2563EB] transition-colors inline-flex items-center gap-2 shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
          <span>Login Portal</span>
        </a>
      </div>

      <!-- Mobile Hamburger Button -->
      <div class="flex xl:hidden items-center gap-2">
        <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-[#0F172A] hover:bg-[#2563EB] transition-colors">
          Portal
        </a>
        <button id="mobile-menu-open" type="button" aria-expanded="false" aria-controls="mobile-menu-drawer" aria-label="Buka menu navigasi" class="p-2.5 rounded-xl text-[#0F172A] hover:bg-slate-100 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
      </div>
    </div>
  </div>
</header>

<!-- Mobile Navigation Drawer & Backdrop -->
<div id="mobile-menu-backdrop" class="fixed inset-0 bg-[#0F172A]/60 backdrop-blur-xs z-50 opacity-0 pointer-events-none transition-opacity duration-300 xl:hidden" aria-hidden="true"></div>
<aside id="mobile-menu-drawer" class="fixed top-0 right-0 bottom-0 w-full max-w-sm bg-white z-50 shadow-2xl translate-x-full transition-transform duration-300 ease-in-out xl:hidden flex flex-col" aria-label="Menu Mobile">
  <div class="p-5 border-b border-slate-200 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <img src="{{ asset('assets/images/logo-sekolah.svg') }}" alt="Logo Nama Sekolah" class="w-9 h-9 rounded-lg" />
      <span class="font-heading font-bold text-base text-[#0F172A]">Nama Sekolah</span>
    </div>
    <button id="mobile-menu-close" type="button" aria-label="Tutup menu navigasi" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>

  <div class="flex-1 overflow-y-auto p-5 space-y-1.5">
    <a href="{{ route('home') }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('home') ? 'text-[#2563EB] bg-blue-50/50' : 'text-[#0F172A] hover:bg-slate-100' }}">Beranda</a>

    <!-- Mobile Accordion: Profil -->
    <div>
      <button type="button" data-mobile-accordion="mob-acc-profil" aria-expanded="false" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] hover:bg-slate-100">
        <span>Profil Sekolah</span>
        <svg data-accordion-icon class="w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <div id="mob-acc-profil" class="hidden pl-4 pr-2 py-1 space-y-1">
        <a href="{{ Route::has('landing.about') ? route('landing.about') : '#' }}" class="block px-4 py-2 rounded-lg text-sm text-[#64748B] hover:text-[#2563EB] hover:bg-blue-50/50">Tentang &amp; Sejarah Sekolah</a>
        <a href="{{ Route::has('landing.staffing') ? route('landing.staffing', 'guru') : '#' }}" class="block px-4 py-2 rounded-lg text-sm text-[#64748B] hover:text-[#2563EB] hover:bg-blue-50/50">Guru &amp; Kepegawaian</a>
      </div>
    </div>

    <!-- Mobile Accordion: Akademik -->
    <div>
      <button type="button" data-mobile-accordion="mob-acc-akademik" aria-expanded="false" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] hover:bg-slate-100">
        <span>Akademik</span>
        <svg data-accordion-icon class="w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <div id="mob-acc-akademik" class="hidden pl-4 pr-2 py-1 space-y-1">
        <a href="{{ Route::has('landing.academic') ? route('landing.academic', 'kurikulum') : '#' }}" class="block px-4 py-2 rounded-lg text-sm text-[#64748B] hover:text-[#2563EB] hover:bg-blue-50/50">Kurikulum &amp; Program Studi</a>
        <a href="#agenda" class="block px-4 py-2 rounded-lg text-sm text-[#64748B] hover:text-[#2563EB] hover:bg-blue-50/50">Kalender &amp; Agenda Akademik</a>
      </div>
    </div>

    <!-- Mobile Accordion: Kesiswaan -->
    <div>
      <button type="button" data-mobile-accordion="mob-acc-kesiswaan" aria-expanded="false" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] hover:bg-slate-100">
        <span>Kesiswaan</span>
        <svg data-accordion-icon class="w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <div id="mob-acc-kesiswaan" class="hidden pl-4 pr-2 py-1 space-y-1">
        <a href="#prestasi" class="block px-4 py-2 rounded-lg text-sm text-[#64748B] hover:text-[#2563EB] hover:bg-blue-50/50">Prestasi Siswa &amp; Sekolah</a>
        <a href="#galeri" class="block px-4 py-2 rounded-lg text-sm text-[#64748B] hover:text-[#2563EB] hover:bg-blue-50/50">Galeri Kegiatan</a>
        <a href="#alumni" class="block px-4 py-2 rounded-lg text-sm text-[#64748B] hover:text-[#2563EB] hover:bg-blue-50/50">Jejaring Alumni</a>
      </div>
    </div>

    <a href="{{ Route::has('landing.facilities') ? route('landing.facilities') : '#' }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] hover:bg-slate-100">Fasilitas</a>

    <!-- Mobile Accordion: Informasi -->
    <div>
      <button type="button" data-mobile-accordion="mob-acc-informasi" aria-expanded="false" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] hover:bg-slate-100">
        <span>Informasi</span>
        <svg data-accordion-icon class="w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <div id="mob-acc-informasi" class="hidden pl-4 pr-2 py-1 space-y-1">
        <a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}" class="block px-4 py-2 rounded-lg text-sm text-[#64748B] hover:text-[#2563EB] hover:bg-blue-50/50">Berita &amp; Artikel</a>
        <a href="#pengumuman" class="block px-4 py-2 rounded-lg text-sm text-[#64748B] hover:text-[#2563EB] hover:bg-blue-50/50">Pengumuman Resmi</a>
        <a href="#agenda" class="block px-4 py-2 rounded-lg text-sm text-[#64748B] hover:text-[#2563EB] hover:bg-blue-50/50">Agenda Kegiatan</a>
      </div>
    </div>

    <a href="{{ Route::has('landing.ppdb') ? route('landing.ppdb') : '#' }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] hover:bg-slate-100">PPDB Online</a>
    <a href="{{ Route::has('landing.contact') ? route('landing.contact') : '#' }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] hover:bg-slate-100">Kontak Kami</a>
  </div>

  <div class="p-5 border-t border-slate-200 space-y-2.5 bg-slate-50">
    <a href="{{ Route::has('landing.ppdb') ? route('landing.ppdb') : '#' }}" class="w-full py-3 px-4 rounded-xl text-sm font-semibold text-white bg-[#2563EB] hover:bg-blue-700 flex items-center justify-center gap-2 shadow-sm">
      <span>Daftar PPDB Sekarang</span>
    </a>
    <a href="{{ route('login') }}" class="w-full py-3 px-4 rounded-xl text-sm font-semibold text-[#0F172A] bg-white border border-slate-300 hover:bg-slate-100 flex items-center justify-center gap-2">
      <span>Masuk Portal Sivitas</span>
    </a>
  </div>
</aside>
<!-- /COMPONENT: Navbar -->
