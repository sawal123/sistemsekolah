<main>
  <!-- COMPONENT: HeroSlider (Section 1 — Hero Banner) -->
  <section data-hero-slider class="relative bg-[#0F172A] text-white overflow-hidden min-h-[560px] lg:min-h-[640px] flex items-center">
    <!-- Slide 1 -->
    <div class="hero-slide active absolute inset-0 flex items-center" aria-hidden="false">
      <img src="{{ asset('assets/images/hero-campus.jpg') }}" alt="Lingkungan Kampus Nama Sekolah" class="absolute inset-0 w-full h-full object-cover object-center" />
      <div class="absolute inset-0 bg-gradient-to-r from-[#0F172A]/95 via-[#0F172A]/80 to-[#0F172A]/35"></div>
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 w-full">
        <div class="max-w-2xl space-y-6">
          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/10 text-[#0EA5E9] border border-white/15 backdrop-blur-xs">
            <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
            Tahun Ajaran 2026/2027 • Pendidikan Berbasis Karakter &amp; Riset
          </span>
          <h1 class="font-heading text-3xl sm:text-5xl lg:text-[52px] font-extrabold tracking-tight text-white leading-[1.15]">
            Membentuk Generasi Pembelajar yang <span class="text-[#0EA5E9]">Unggul, Berintegritas,</span> &amp; Berdaya Saing.
          </h1>
          <p class="text-base sm:text-lg text-slate-200 leading-relaxed">
            Nama Sekolah menghadirkan ekosistem pembelajaran modern yang memadukan kurikulum nasional terkini, laboratorium terpadu, serta pembinaan karakter secara menyeluruh.
          </p>
          <div class="flex flex-wrap items-center gap-3.5 pt-2">
            <a href="{{ Route::has('landing.ppdb') ? route('landing.ppdb') : '#' }}" class="px-6 py-3.5 rounded-xl text-sm font-semibold text-white bg-[#2563EB] hover:bg-blue-500 transition-all shadow-lg shadow-blue-900/30 inline-flex items-center gap-2">
              <span>Daftar Sekarang</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <a href="{{ Route::has('landing.about') ? route('landing.about') : '#' }}" class="px-6 py-3.5 rounded-xl text-sm font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-xs transition-all">
              Jelajahi Sekolah
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Slide 2 -->
    <div class="hero-slide absolute inset-0 flex items-center" aria-hidden="true">
      <img src="{{ asset('assets/images/hero-lab.jpg') }}" alt="Kegiatan Praktikum Sains dan Robotika Siswa" class="absolute inset-0 w-full h-full object-cover object-center" />
      <div class="absolute inset-0 bg-gradient-to-r from-[#0F172A]/95 via-[#0F172A]/80 to-[#0F172A]/40"></div>
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 w-full">
        <div class="max-w-2xl space-y-6">
          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/10 text-[#F59E0B] border border-white/15 backdrop-blur-xs">
            <span class="w-2 h-2 rounded-full bg-[#0EA5E9]"></span>
            Eksplorasi Sains, Teknologi &amp; Humaniora
          </span>
          <h2 class="font-heading text-3xl sm:text-5xl lg:text-[52px] font-extrabold tracking-tight text-white leading-[1.15]">
            Laboratorium Modern untuk <span class="text-[#F59E0B]">Inovasi dan Eksperimen</span> Siswa.
          </h2>
          <p class="text-base sm:text-lg text-slate-200 leading-relaxed">
            Didukung fasilitas praktikum sains, komputasi digital, dan perpustakaan kolaboratif untuk menumbuhkan daya pikir kritis serta pemecahan masalah nyata.
          </p>
          <div class="flex flex-wrap items-center gap-3.5 pt-2">
            <a href="{{ Route::has('landing.ppdb') ? route('landing.ppdb') : '#' }}" class="px-6 py-3.5 rounded-xl text-sm font-semibold text-white bg-[#2563EB] hover:bg-blue-500 transition-all shadow-lg inline-flex items-center gap-2">
              <span>Daftar Sekarang</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <a href="{{ Route::has('landing.facilities') ? route('landing.facilities') : '#' }}" class="px-6 py-3.5 rounded-xl text-sm font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-xs transition-all">
              Jelajahi Sekolah
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Slide 3 -->
    <div class="hero-slide absolute inset-0 flex items-center" aria-hidden="true">
      <img src="{{ asset('assets/images/achievement-olympiad.jpg') }}" alt="Prestasi Siswa di Kompetisi Sains dan Seni" class="absolute inset-0 w-full h-full object-cover object-center" />
      <div class="absolute inset-0 bg-gradient-to-r from-[#0F172A]/95 via-[#0F172A]/80 to-[#0F172A]/40"></div>
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 w-full">
        <div class="max-w-2xl space-y-6">
          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/10 text-[#0EA5E9] border border-white/15 backdrop-blur-xs">
            <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
            Pengembangan Bakat &amp; Kepemimpinan Siswa
          </span>
          <h2 class="font-heading text-3xl sm:text-5xl lg:text-[52px] font-extrabold tracking-tight text-white leading-[1.15]">
            Mendampingi Setiap Potensi Menuju <span class="text-[#0EA5E9]">Prestasi Terbaik</span>.
          </h2>
          <p class="text-base sm:text-lg text-slate-200 leading-relaxed">
            Melalui bimbingan intensif pendidik berpengalaman dan klub ekstrakurikuler aktif, siswa didorong berprestasi di bidang akademik, olahraga, maupun seni budaya.
          </p>
          <div class="flex flex-wrap items-center gap-3.5 pt-2">
            <a href="{{ Route::has('landing.ppdb') ? route('landing.ppdb') : '#' }}" class="px-6 py-3.5 rounded-xl text-sm font-semibold text-white bg-[#2563EB] hover:bg-blue-500 transition-all shadow-lg inline-flex items-center gap-2">
              <span>Daftar Sekarang</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <a href="#prestasi" class="px-6 py-3.5 rounded-xl text-sm font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-xs transition-all">
              Jelajahi Sekolah
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Slider Controls & Indicators -->
    <div class="absolute bottom-6 left-0 right-0 z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
      <div class="flex items-center gap-2" role="tablist" aria-label="Indikator Slide Hero">
        <button type="button" data-hero-dot aria-label="Slide 1" aria-current="true" class="h-2.5 w-8 rounded-full bg-[#F59E0B] transition-all duration-300"></button>
        <button type="button" data-hero-dot aria-label="Slide 2" class="h-2.5 w-2.5 rounded-full bg-white/50 hover:bg-white transition-all duration-300"></button>
        <button type="button" data-hero-dot aria-label="Slide 3" class="h-2.5 w-2.5 rounded-full bg-white/50 hover:bg-white transition-all duration-300"></button>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" data-hero-prev aria-label="Slide Sebelumnya" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-[#2563EB] text-white border border-white/20 flex items-center justify-center transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button type="button" data-hero-next aria-label="Slide Selanjutnya" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-[#2563EB] text-white border border-white/20 flex items-center justify-center transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>
    </div>
  </section>
  <!-- /COMPONENT: HeroSlider -->

  <!-- COMPONENT: SchoolStats (Section 2 — Statistik Sekolah) -->
  <section class="relative z-20 -mt-10 pb-12" aria-label="Statistik Utama Sekolah">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-6 shadow-md border border-slate-200/90 flex items-center gap-4 card-elevated">
          <div class="w-13 h-13 rounded-2xl bg-blue-50 text-[#2563EB] flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
          </div>
          <div>
            <span class="block font-heading text-2xl sm:text-3xl font-extrabold text-[#0F172A]">960+</span>
            <span class="block text-xs font-medium text-[#64748B]">Total Siswa Aktif</span>
          </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-md border border-slate-200/90 flex items-center gap-4 card-elevated">
          <div class="w-13 h-13 rounded-2xl bg-sky-50 text-[#0EA5E9] flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          </div>
          <div>
            <span class="block font-heading text-2xl sm:text-3xl font-extrabold text-[#0F172A]">68</span>
            <span class="block text-xs font-medium text-[#64748B]">Pendidik &amp; Tenaga Kependidikan</span>
          </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-md border border-slate-200/90 flex items-center gap-4 card-elevated">
          <div class="w-13 h-13 rounded-2xl bg-amber-50 text-[#F59E0B] flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          </div>
          <div>
            <span class="block font-heading text-2xl sm:text-3xl font-extrabold text-[#0F172A]">3 Program</span>
            <span class="block text-xs font-medium text-[#64748B]">Peminatan Akademik Utama</span>
          </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-md border border-slate-200/90 flex items-center gap-4 card-elevated">
          <div class="w-13 h-13 rounded-2xl bg-indigo-50 text-[#2563EB] flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
          </div>
          <div>
            <span class="block font-heading text-2xl sm:text-3xl font-extrabold text-[#0F172A]">4.200+</span>
            <span class="block text-xs font-medium text-[#64748B]">Jejaring Alumni Terdaftar</span>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /COMPONENT: SchoolStats -->

  <!-- COMPONENT: PrincipalWelcome (Section 3 — Sambutan Kepala Sekolah) -->
  <section class="py-16 lg:py-24 bg-white border-y border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
        <!-- Principal Photo Card -->
        <div class="lg:col-span-5">
          <div class="relative mx-auto max-w-md">
            <div class="aspect-4/4 rounded-3xl overflow-hidden shadow-xl border border-slate-200 bg-slate-100">
              <img src="{{ asset('assets/images/principal.jpg') }}" alt="Foto Kepala Sekolah Nama Sekolah" class="w-full h-full object-cover" loading="lazy" />
            </div>
            <div class="mt-4 sm:mt-0 sm:absolute sm:-bottom-5 sm:left-5 sm:right-5 bg-[#0F172A] text-white p-4 sm:p-5 rounded-2xl shadow-lg border border-slate-800">
              <p class="font-heading font-bold text-base sm:text-lg text-white">Drs. H. Ahmad Ridwan, M.Pd.</p>
              <p class="text-xs text-[#0EA5E9] font-medium mt-0.5">Kepala Sekolah • Nama Sekolah</p>
            </div>
          </div>
        </div>

        <!-- Principal Message -->
        <div class="lg:col-span-7 space-y-6">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#2563EB]">
            Sambutan Pimpinan Sekolah
          </div>
          <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-[#0F172A] leading-tight">
            Mewujudkan Lingkungan Belajar yang Inklusif, Inspiratif, dan Berorientasi Masa Depan
          </h2>
          <blockquote class="p-5 rounded-2xl bg-[#F8FAFC] border-l-4 border-[#2563EB] text-slate-700 text-sm sm:text-base italic leading-relaxed">
            "Pendidikan bukan sekadar proses transfer pengetahuan di ruang kelas, melainkan perjalanan menumbuhkan integritas, rasa ingin tahu, dan kepekaan sosial pada diri setiap peserta didik."
          </blockquote>
          <p class="text-sm sm:text-base text-[#64748B] leading-relaxed">
            Selamat datang di portal resmi <strong class="text-[#0F172A] font-semibold">Nama Sekolah</strong>. Kami berkomitmen menghadirkan tata kelola pendidikan yang transparan dan kolaboratif bersama orang tua serta masyarakat. Melalui kurikulum yang adaptif dan fasilitas pembelajaran terpadu, kami mendampingi setiap siswa mengenali potensi terbaiknya.
          </p>
          <div class="pt-2 flex flex-wrap items-center gap-4">
            <a href="{{ Route::has('landing.about') ? route('landing.about') . '#sambutan' : '#sambutan' }}" class="px-5 py-3 rounded-xl text-sm font-semibold text-white bg-[#2563EB] hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
              <span>Baca Selengkapnya</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="{{ Route::has('landing.staffing') ? route('landing.staffing', 'guru') : '#' }}" class="px-5 py-3 rounded-xl text-sm font-semibold text-[#0F172A] bg-slate-100 hover:bg-slate-200 transition-colors">
              Lihat Direktori Pendidik
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /COMPONENT: PrincipalWelcome -->

  <!-- COMPONENT: AboutSummary (Section 4 — Tentang Sekolah) -->
  <section class="py-16 lg:py-24 bg-[#F8FAFC]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-6 space-y-6">
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#2563EB]">
            Tentang Institusi Kami
          </span>
          <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-[#0F172A] leading-tight">
            Tradisi Akademik Kuat dengan Pendekatan Pembelajaran Abad ke-21
          </h2>
          <p class="text-sm sm:text-base text-[#64748B] leading-relaxed">
            Nama Sekolah dirancang sebagai institusi pendidikan menengah yang menyeimbangkan penguasaan kompetensi sains, literasi humaniora, kecakapan digital, dan kematangan karakter.
          </p>

          <!-- Vision Card -->
          <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2">
            <div class="flex items-center gap-2.5">
              <span class="w-2.5 h-2.5 rounded-full bg-[#F59E0B]"></span>
              <h3 class="font-heading text-sm font-bold uppercase tracking-wider text-[#0F172A]">Visi Pendidikan Sekolah</h3>
            </div>
            <p class="text-sm text-[#64748B] leading-relaxed">
              Menjadi komunitas pembelajar yang unggul dalam prestasi akademik, berakhlak mulia, berwawasan lingkungan, dan siap menghadapi dinamika global secara bertanggung jawab.
            </p>
          </div>

          <!-- 3 Key Pillars -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
            <div class="bg-white p-4 rounded-xl border border-slate-200/90">
              <span class="block font-heading font-bold text-sm text-[#0F172A]">Kurikulum Adaptif</span>
              <span class="block text-xs text-[#64748B] mt-1">Pembelajaran berbasis proyek &amp; riset kontekstual.</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200/90">
              <span class="block font-heading font-bold text-sm text-[#0F172A]">Pendidik Profesional</span>
              <span class="block text-xs text-[#64748B] mt-1">Guru tersertifikasi dan aktif melakukan pengembangan diri.</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200/90">
              <span class="block font-heading font-bold text-sm text-[#0F172A]">Budaya Positif</span>
              <span class="block text-xs text-[#64748B] mt-1">Lingkungan aman, inklusif, dan bebas perundungan.</span>
            </div>
          </div>
        </div>

        <div class="lg:col-span-6">
          <div class="grid grid-cols-2 gap-4 sm:gap-5">
            <div class="space-y-4 sm:space-y-5">
              <img src="{{ asset('assets/images/facility-library.jpg') }}" alt="Perpustakaan Digital Nama Sekolah" class="rounded-2xl shadow-md border border-slate-200 w-full aspect-4/3 object-cover" loading="lazy" />
              <div class="bg-[#0F172A] text-white p-5 rounded-2xl">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#0EA5E9]">Lingkungan Hijau</span>
                <p class="font-heading font-bold text-base mt-1">Ruang Terbuka &amp; Area Diskusi Siswa</p>
                <p class="text-xs text-slate-300 mt-1">Mendukung kolaborasi lintas kelas yang sehat dan produktif.</p>
              </div>
            </div>
            <div class="space-y-4 sm:space-y-5 pt-6">
              <div class="bg-[#2563EB] text-white p-5 rounded-2xl">
                <span class="text-xs font-semibold uppercase tracking-wider text-blue-100">Integrasi Digital</span>
                <p class="font-heading font-bold text-base mt-1">Smart Classroom &amp; E-Learning</p>
                <p class="text-xs text-blue-100 mt-1">Setiap kelas dilengkapi perangkat multimedia interaktif.</p>
              </div>
              <img src="{{ asset('assets/images/facility-computer.jpg') }}" alt="Laboratorium Komputer Sekolah" class="rounded-2xl shadow-md border border-slate-200 w-full aspect-4/3 object-cover" loading="lazy" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /COMPONENT: AboutSummary -->

  <!-- COMPONENT: AcademicPrograms (Section 5 — Program Akademik) -->
  <section class="py-16 lg:py-24 bg-white border-t border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- COMPONENT: SectionHeading -->
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
        <div class="max-w-2xl">
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#2563EB]">
            Program Pendidikan
          </span>
          <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-[#0F172A] mt-3">
            Peminatan Akademik yang Terarah Sesuai Minat &amp; Rencana Karier
          </h2>
        </div>
        <a href="{{ Route::has('landing.academic') ? route('landing.academic', 'kurikulum') : '#' }}" class="text-sm font-semibold text-[#2563EB] hover:text-[#0F172A] inline-flex items-center gap-1.5 shrink-0">
          <span>Pelajari Struktur Kurikulum</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
      </div>
      <!-- /COMPONENT: SectionHeading -->

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
        <!-- Card 1: IPA -->
        <article class="bg-[#F8FAFC] rounded-2xl p-7 border border-slate-200/90 flex flex-col justify-between card-elevated">
          <div>
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-[#2563EB] flex items-center justify-center mb-5">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <span class="text-xs font-semibold uppercase tracking-wider text-[#2563EB]">Rumpun MIPA &amp; Teknologi</span>
            <h3 class="font-heading text-xl font-bold text-[#0F172A] mt-1">Program Ilmu Pengetahuan Alam (IPA)</h3>
            <p class="text-sm text-[#64748B] mt-3 leading-relaxed">
              Berfokus pada penguatan metode ilmiah, eksperimen laboratorium fisika, kimia, biologi, matematika lanjut, serta informatika dan robotika.
            </p>
            <ul class="mt-4 space-y-2 text-xs text-slate-600">
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>Praktikum laboratorium terstruktur setiap pekan</li>
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>Persiapan studi kedokteran, teknik &amp; sains murni</li>
            </ul>
          </div>
          <div class="pt-6 mt-6 border-t border-slate-200/80">
            <a href="{{ Route::has('landing.academic') ? route('landing.academic', 'ipa') : '#' }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#2563EB] hover:text-[#0F172A]">
              <span>Detail Program IPA</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </article>

        <!-- Card 2: IPS -->
        <article class="bg-[#F8FAFC] rounded-2xl p-7 border border-slate-200/90 flex flex-col justify-between card-elevated">
          <div>
            <div class="w-12 h-12 rounded-xl bg-sky-100 text-[#0EA5E9] flex items-center justify-center mb-5">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs font-semibold uppercase tracking-wider text-[#0EA5E9]">Rumpun Sosial &amp; Ekonomi</span>
            <h3 class="font-heading text-xl font-bold text-[#0F172A] mt-1">Program Ilmu Pengetahuan Sosial (IPS)</h3>
            <p class="text-sm text-[#64748B] mt-3 leading-relaxed">
              Mengembangkan kemampuan analisis sosial, literasi ekonomi dan kewirausahaan, sosiologi, sejarah kritis, serta pemetaan geografi modern.
            </p>
            <ul class="mt-4 space-y-2 text-xs text-slate-600">
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#0EA5E9]"></span>Riset sosial lapangan &amp; inkubasi bisnis siswa</li>
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#0EA5E9]"></span>Persiapan studi hukum, ekonomi, manajemen &amp; HI</li>
            </ul>
          </div>
          <div class="pt-6 mt-6 border-t border-slate-200/80">
            <a href="{{ Route::has('landing.academic') ? route('landing.academic', 'ips') : '#' }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#2563EB] hover:text-[#0F172A]">
              <span>Detail Program IPS</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </article>

        <!-- Card 3: Bahasa -->
        <article class="bg-[#F8FAFC] rounded-2xl p-7 border border-slate-200/90 flex flex-col justify-between card-elevated">
          <div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-[#F59E0B] flex items-center justify-center mb-5">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
            </div>
            <span class="text-xs font-semibold uppercase tracking-wider text-[#F59E0B]">Rumpun Linguistik &amp; Budaya</span>
            <h3 class="font-heading text-xl font-bold text-[#0F172A] mt-1">Program Bahasa dan Budaya</h3>
            <p class="text-sm text-[#64748B] mt-3 leading-relaxed">
              Memperdalam penguasaan komunikasi lintas budaya, sastra Indonesia dan Inggris, bahasa asing pilihan, serta kajian antropologi budaya.
            </p>
            <ul class="mt-4 space-y-2 text-xs text-slate-600">
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span>Klub debat internasional, jurnalistik &amp; diplomasi</li>
              <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span>Persiapan studi komunikasi, sastra &amp; budaya global</li>
            </ul>
          </div>
          <div class="pt-6 mt-6 border-t border-slate-200/80">
            <a href="{{ Route::has('landing.academic') ? route('landing.academic', 'bahasa') : '#' }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#2563EB] hover:text-[#0F172A]">
              <span>Detail Program Bahasa</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </article>
      </div>
    </div>
  </section>
  <!-- /COMPONENT: AcademicPrograms -->

  <!-- COMPONENT: SchoolAchievements (Section 6 — Prestasi Sekolah) -->
  <section id="prestasi" class="py-16 lg:py-24 bg-[#0F172A] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
        <div class="max-w-2xl">
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-[#F59E0B] border border-amber-400/25">
            Apresiasi &amp; Capaian
          </span>
          <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-white mt-3">
            Rekam Jejak Prestasi Siswa &amp; Pendidik
          </h2>
        </div>
        <a href="#prestasi" class="text-sm font-semibold text-[#0EA5E9] hover:text-white inline-flex items-center gap-1.5 shrink-0">
          <span>Lihat Seluruh Prestasi</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
        <!-- Achievement Card 1 -->
        <article class="bg-slate-900/90 rounded-2xl overflow-hidden border border-slate-800 flex flex-col">
          <div class="relative aspect-16/10 overflow-hidden">
            <img src="{{ asset('assets/images/achievement-olympiad.jpg') }}" alt="Penghargaan Festival Sains Nasional" class="w-full h-full object-cover" loading="lazy" />
            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-semibold bg-[#F59E0B] text-[#0F172A]">
              Tingkat Nasional • Siswa
            </span>
          </div>
          <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
            <div>
              <span class="text-xs text-slate-400">September 2026 • Sains &amp; Teknologi</span>
              <h3 class="font-heading text-lg font-bold text-white mt-1">Medali Emas Kategori Inovasi Robotika Terapan Pelajar</h3>
              <p class="text-sm text-slate-300 mt-2">
                Tim riset siswa merancang prototipe pemantau kualitas air otomatis berbasis sensor hemat energi.
              </p>
            </div>
            <a href="#prestasi" class="text-xs font-semibold text-[#0EA5E9] hover:underline inline-flex items-center gap-1">
              <span>Lihat Detail Capaian</span>
              <span>&rarr;</span>
            </a>
          </div>
        </article>

        <!-- Achievement Card 2 -->
        <article class="bg-slate-900/90 rounded-2xl overflow-hidden border border-slate-800 flex flex-col">
          <div class="relative aspect-16/10 overflow-hidden">
            <img src="{{ asset('assets/images/hero-lab.jpg') }}" alt="Penghargaan Guru Inovatif" class="w-full h-full object-cover" loading="lazy" />
            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-semibold bg-[#0EA5E9] text-[#0F172A]">
              Tingkat Provinsi • Guru
            </span>
          </div>
          <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
            <div>
              <span class="text-xs text-slate-400">Agustus 2026 • Inovasi Pembelajaran</span>
              <h3 class="font-heading text-lg font-bold text-white mt-1">Penghargaan Karya Modul Pembelajaran STEM Interaktif Terbaik</h3>
              <p class="text-sm text-slate-300 mt-2">
                Apresiasi atas pengembangan metode praktikum kolaboratif yang mudah diakses oleh seluruh siswa.
              </p>
            </div>
            <a href="#prestasi" class="text-xs font-semibold text-[#0EA5E9] hover:underline inline-flex items-center gap-1">
              <span>Lihat Detail Capaian</span>
              <span>&rarr;</span>
            </a>
          </div>
        </article>

        <!-- Achievement Card 3 -->
        <article class="bg-slate-900/90 rounded-2xl overflow-hidden border border-slate-800 flex flex-col">
          <div class="relative aspect-16/10 overflow-hidden">
            <img src="{{ asset('assets/images/activity-arts.jpg') }}" alt="Festival Musik dan Budaya" class="w-full h-full object-cover" loading="lazy" />
            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-semibold bg-white text-[#0F172A]">
              Tingkat Daerah • Ekstrakurikuler
            </span>
          </div>
          <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
            <div>
              <span class="text-xs text-slate-400">Juli 2026 • Seni &amp; Budaya</span>
              <h3 class="font-heading text-lg font-bold text-white mt-1">Penyaji Terbaik Festival Orkestra &amp; Karawitan Pelajar</h3>
              <p class="text-sm text-slate-300 mt-2">
                Kolaborasi ansambel musik modern dan instrumen tradisional Nusantara dalam gelar karya pelajar.
              </p>
            </div>
            <a href="#prestasi" class="text-xs font-semibold text-[#0EA5E9] hover:underline inline-flex items-center gap-1">
              <span>Lihat Detail Capaian</span>
              <span>&rarr;</span>
            </a>
          </div>
        </article>
      </div>
    </div>
  </section>
  <!-- /COMPONENT: SchoolAchievements -->

  <!-- COMPONENT: FeaturedFacilities (Section 7 — Fasilitas Unggulan) -->
  <section class="py-16 lg:py-24 bg-[#F8FAFC]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
        <div class="max-w-2xl">
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#2563EB]">
            Sarana &amp; Prasarana Kampus
          </span>
          <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-[#0F172A] mt-3">
            Fasilitas Unggulan Pendukung Eksplorasi Siswa
          </h2>
        </div>
        <a href="{{ Route::has('landing.facilities') ? route('landing.facilities') : '#' }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] bg-white border border-slate-200 hover:border-[#2563EB] hover:text-[#2563EB] transition-colors shrink-0">
          Lihat Semua Fasilitas
        </a>
      </div>

      <!-- COMPONENT: FacilityCard Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <article class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-xs card-elevated">
          <div class="aspect-4/3 overflow-hidden bg-slate-100">
            <img src="{{ asset('assets/images/facility-library.jpg') }}" alt="Perpustakaan & Co-Working Space" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" loading="lazy" />
          </div>
          <div class="p-5">
            <span class="text-xs font-semibold text-[#2563EB]">Literasi &amp; Riset</span>
            <h3 class="font-heading text-base font-bold text-[#0F172A] mt-1">Perpustakaan &amp; Pod Diskusi</h3>
            <p class="text-xs text-[#64748B] mt-2 leading-relaxed">Koleksi ribuan buku cetak, katalog e-book, dan bilik belajar hening ber-AC.</p>
          </div>
        </article>

        <article class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-xs card-elevated">
          <div class="aspect-4/3 overflow-hidden bg-slate-100">
            <img src="{{ asset('assets/images/hero-lab.jpg') }}" alt="Laboratorium Sains Terpadu" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" loading="lazy" />
          </div>
          <div class="p-5">
            <span class="text-xs font-semibold text-[#0EA5E9]">Eksperimen MIPA</span>
            <h3 class="font-heading text-base font-bold text-[#0F172A] mt-1">Laboratorium Sains Terpadu</h3>
            <p class="text-xs text-[#64748B] mt-2 leading-relaxed">Ruang praktikum fisika, kimia, dan biologi dengan standar keselamatan modern.</p>
          </div>
        </article>

        <article class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-xs card-elevated">
          <div class="aspect-4/3 overflow-hidden bg-slate-100">
            <img src="{{ asset('assets/images/facility-computer.jpg') }}" alt="Laboratorium Komputer & Multimedia" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" loading="lazy" />
          </div>
          <div class="p-5">
            <span class="text-xs font-semibold text-[#2563EB]">Teknologi Informasi</span>
            <h3 class="font-heading text-base font-bold text-[#0F172A] mt-1">Lab Komputasi &amp; Bahasa</h3>
            <p class="text-xs text-[#64748B] mt-2 leading-relaxed">Dilengkapi perangkat komputer terbaru, jaringan serat optik, dan smartboard.</p>
          </div>
        </article>

        <article class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-xs card-elevated">
          <div class="aspect-4/3 overflow-hidden bg-slate-100">
            <img src="{{ asset('assets/images/facility-sports.jpg') }}" alt="Gelanggang Olahraga Semi-Indoor" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" loading="lazy" />
          </div>
          <div class="p-5">
            <span class="text-xs font-semibold text-[#F59E0B]">Kebugaran &amp; Olahraga</span>
            <h3 class="font-heading text-base font-bold text-[#0F172A] mt-1">Arena Olahraga Semi-Indoor</h3>
            <p class="text-xs text-[#64748B] mt-2 leading-relaxed">Lapangan basket, futsal, dan voli beratap kanopi modern yang teduh dan aman.</p>
          </div>
        </article>
      </div>
      <!-- /COMPONENT: FacilityCard Grid -->
    </div>
  </section>
  <!-- /COMPONENT: FeaturedFacilities -->

  <!-- COMPONENT: LatestNews (Section 8 — Berita Terbaru) -->
  <section class="py-16 lg:py-24 bg-white border-t border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
        <div class="max-w-2xl">
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#2563EB]">
            Kabar &amp; Publikasi Sekolah
          </span>
          <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-[#0F172A] mt-3">
            Berita &amp; Artikel Pendidikan Terbaru
          </h2>
        </div>
        <a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}" class="text-sm font-semibold text-[#2563EB] hover:text-[#0F172A] inline-flex items-center gap-1.5 shrink-0">
          <span>Jelajahi Semua Artikel</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
        <!-- COMPONENT: NewsCard 1 -->
        <article class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-xs flex flex-col justify-between card-elevated">
          <div>
            <a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}" class="block aspect-16/10 overflow-hidden bg-slate-100">
              <img src="{{ asset('assets/images/hero-lab.jpg') }}" alt="Pameran Karya Riset Siswa" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" loading="lazy" />
            </a>
            <div class="p-6">
              <div class="flex items-center gap-3 text-xs text-[#64748B] mb-3">
                <span class="px-2.5 py-0.5 rounded-md bg-blue-50 text-[#2563EB] font-semibold">Akademik</span>
                <span>18 Oktober 2026</span>
              </div>
              <h3 class="font-heading text-lg font-bold text-[#0F172A] leading-snug hover:text-[#2563EB] transition-colors">
                <a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}">Pekan Eksplorasi Sains 2026: Menampilkan 24 Proyek Inovasi Lingkungan Karya Siswa</a>
              </h3>
              <p class="text-sm text-[#64748B] mt-2.5 leading-relaxed">
                Kegiatan tahunan yang mewadahi kolaborasi lintas jurusan dalam merancang solusi ramah lingkungan di lingkungan sekolah.
              </p>
            </div>
          </div>
          <div class="px-6 pb-6">
            <a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}" class="text-sm font-semibold text-[#2563EB] hover:text-[#0F172A] inline-flex items-center gap-1.5">
              <span>Baca Selengkapnya</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </article>

        <!-- COMPONENT: NewsCard 2 -->
        <article class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-xs flex flex-col justify-between card-elevated">
          <div>
            <a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}" class="block aspect-16/10 overflow-hidden bg-slate-100">
              <img src="{{ asset('assets/images/facility-library.jpg') }}" alt="Gerakan Literasi Pagi" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" loading="lazy" />
            </a>
            <div class="p-6">
              <div class="flex items-center gap-3 text-xs text-[#64748B] mb-3">
                <span class="px-2.5 py-0.5 rounded-md bg-sky-50 text-[#0EA5E9] font-semibold">Literasi</span>
                <span>10 Oktober 2026</span>
              </div>
              <h3 class="font-heading text-lg font-bold text-[#0F172A] leading-snug hover:text-[#2563EB] transition-colors">
                <a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}">Transformasi Perpustakaan Sekolah Menjadi Ruang Kolaborasi &amp; Diskusi Terbuka</a>
              </h3>
              <p class="text-sm text-[#64748B] mt-2.5 leading-relaxed">
                Hadirnya pod belajar mandiri dan katalog literasi digital meningkatkan minat baca serta diskusi kelompok siswa secara signifikan.
              </p>
            </div>
          </div>
          <div class="px-6 pb-6">
            <a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}" class="text-sm font-semibold text-[#2563EB] hover:text-[#0F172A] inline-flex items-center gap-1.5">
              <span>Baca Selengkapnya</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </article>

        <!-- COMPONENT: NewsCard 3 -->
        <article class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-xs flex flex-col justify-between card-elevated">
          <div>
            <a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}" class="block aspect-16/10 overflow-hidden bg-slate-100">
              <img src="{{ asset('assets/images/activity-arts.jpg') }}" alt="Pentas Budaya Tahunan" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" loading="lazy" />
            </a>
            <div class="p-6">
              <div class="flex items-center gap-3 text-xs text-[#64748B] mb-3">
                <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-[#F59E0B] font-semibold">Kesiswaan</span>
                <span>02 Oktober 2026</span>
              </div>
              <h3 class="font-heading text-lg font-bold text-[#0F172A] leading-snug hover:text-[#2563EB] transition-colors">
                <a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}">Harmoni Nusantara: Apresiasi Seni Musik dan Karawitan Siswa Nama Sekolah</a>
              </h3>
              <p class="text-sm text-[#64748B] mt-2.5 leading-relaxed">
                Melatih kepekaan estetika, kepemimpinan acara, dan kecintaan terhadap warisan budaya bangsa melalui panggung ekspresi siswa.
              </p>
            </div>
          </div>
          <div class="px-6 pb-6">
            <a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}" class="text-sm font-semibold text-[#2563EB] hover:text-[#0F172A] inline-flex items-center gap-1.5">
              <span>Baca Selengkapnya</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </article>
      </div>
    </div>
  </section>
  <!-- /COMPONENT: LatestNews -->

  <!-- COMPONENT: ActivityGallery (Section 9 — Galeri Kegiatan) -->
  <section id="galeri" class="py-16 lg:py-24 bg-[#F8FAFC] border-t border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
        <div class="max-w-2xl">
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#2563EB]">
            Dokumentasi Visual
          </span>
          <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-[#0F172A] mt-3">
            Galeri Aktivitas &amp; Kehidupan Kampus
          </h2>
        </div>
        <a href="#galeri" class="text-sm font-semibold text-[#2563EB] hover:text-[#0F172A] inline-flex items-center gap-1.5 shrink-0">
          <span>Buka Galeri Lengkap</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <button type="button" data-lightbox-src="{{ asset('assets/images/hero-campus.jpg') }}" data-lightbox-title="Lingkungan Utama Kampus" data-lightbox-category="Fasilitas Sekolah" data-lightbox-desc="Area terbuka hijau yang menjadi pusat interaksi dan belajar kolaboratif siswa." class="group relative rounded-2xl overflow-hidden aspect-4/3 bg-slate-200 text-left focus:outline-none">
          <img src="{{ asset('assets/images/hero-campus.jpg') }}" alt="Lingkungan Utama Kampus" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
          <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A]/85 via-[#0F172A]/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col justify-end p-5">
            <span class="text-xs font-semibold text-[#0EA5E9]">Fasilitas Sekolah</span>
            <span class="font-heading font-bold text-white text-base">Lingkungan Utama Kampus</span>
          </div>
        </button>

        <button type="button" data-lightbox-src="{{ asset('assets/images/hero-lab.jpg') }}" data-lightbox-title="Praktikum Robotika & Sains" data-lightbox-category="Kegiatan Akademik" data-lightbox-desc="Siswa melakukan pengujian sensor mikroprosesor didampingi guru pembimbing." class="group relative rounded-2xl overflow-hidden aspect-4/3 bg-slate-200 text-left focus:outline-none">
          <img src="{{ asset('assets/images/hero-lab.jpg') }}" alt="Praktikum Robotika dan Sains" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
          <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A]/85 via-[#0F172A]/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col justify-end p-5">
            <span class="text-xs font-semibold text-[#0EA5E9]">Kegiatan Akademik</span>
            <span class="font-heading font-bold text-white text-base">Praktikum Robotika &amp; Sains</span>
          </div>
        </button>

        <button type="button" data-lightbox-src="{{ asset('assets/images/facility-library.jpg') }}" data-lightbox-title="Diskusi Kelompok di Perpustakaan" data-lightbox-category="Literasi Siswa" data-lightbox-desc="Fasilitas perpustakaan modern dengan study pod individual dan area diskusi." class="group relative rounded-2xl overflow-hidden aspect-4/3 bg-slate-200 text-left focus:outline-none">
          <img src="{{ asset('assets/images/facility-library.jpg') }}" alt="Diskusi Kelompok di Perpustakaan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
          <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A]/85 via-[#0F172A]/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col justify-end p-5">
            <span class="text-xs font-semibold text-[#0EA5E9]">Literasi Siswa</span>
            <span class="font-heading font-bold text-white text-base">Diskusi Kelompok di Perpustakaan</span>
          </div>
        </button>

        <button type="button" data-lightbox-src="{{ asset('assets/images/facility-computer.jpg') }}" data-lightbox-title="Kelas Pemrograman & Multimedia" data-lightbox-category="Teknologi Digital" data-lightbox-desc="Pembelajaran algoritma dan literasi digital menggunakan perangkat interaktif." class="group relative rounded-2xl overflow-hidden aspect-4/3 bg-slate-200 text-left focus:outline-none">
          <img src="{{ asset('assets/images/facility-computer.jpg') }}" alt="Kelas Pemrograman dan Multimedia" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
          <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A]/85 via-[#0F172A]/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col justify-end p-5">
            <span class="text-xs font-semibold text-[#0EA5E9]">Teknologi Digital</span>
            <span class="font-heading font-bold text-white text-base">Kelas Pemrograman &amp; Multimedia</span>
          </div>
        </button>

        <button type="button" data-lightbox-src="{{ asset('assets/images/facility-sports.jpg') }}" data-lightbox-title="Olahraga & Pembinaan Fisik" data-lightbox-category="Kesiswaan" data-lightbox-desc="Aktivitas pendidikan jasmani di gelanggang olahraga semi-indoor." class="group relative rounded-2xl overflow-hidden aspect-4/3 bg-slate-200 text-left focus:outline-none">
          <img src="{{ asset('assets/images/facility-sports.jpg') }}" alt="Olahraga dan Pembinaan Fisik" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
          <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A]/85 via-[#0F172A]/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col justify-end p-5">
            <span class="text-xs font-semibold text-[#0EA5E9]">Kesiswaan</span>
            <span class="font-heading font-bold text-white text-base">Olahraga &amp; Pembinaan Fisik</span>
          </div>
        </button>

        <button type="button" data-lightbox-src="{{ asset('assets/images/activity-arts.jpg') }}" data-lightbox-title="Gelar Karya Orkestra & Karawitan" data-lightbox-category="Ekstrakurikuler" data-lightbox-desc="Penampilan kolaborasi seni musik siswa pada acara tahunan sekolah." class="group relative rounded-2xl overflow-hidden aspect-4/3 bg-slate-200 text-left focus:outline-none">
          <img src="{{ asset('assets/images/activity-arts.jpg') }}" alt="Gelar Karya Orkestra dan Karawitan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
          <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A]/85 via-[#0F172A]/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col justify-end p-5">
            <span class="text-xs font-semibold text-[#0EA5E9]">Ekstrakurikuler</span>
            <span class="font-heading font-bold text-white text-base">Gelar Karya Orkestra &amp; Karawitan</span>
          </div>
        </button>
      </div>
    </div>
  </section>
  <!-- /COMPONENT: ActivityGallery -->

  <!-- COMPONENT: PpdbCtaBanner (Section 10 — PPDB Call to Action) -->
  <section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="relative rounded-3xl bg-[#0F172A] text-white p-8 sm:p-12 lg:p-16 overflow-hidden shadow-xl border border-slate-800">
        <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-[#2563EB]/20 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          <div class="lg:col-span-8 space-y-4">
            <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-semibold bg-[#F59E0B]/20 text-[#F59E0B] border border-[#F59E0B]/30">
              Penerimaan Peserta Didik Baru • Tahun Ajaran 2026/2027
            </span>
            <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-white leading-tight">
              Siap Bergabung Menjadi Bagian dari Sivitas Akademika Nama Sekolah?
            </h2>
            <p class="text-sm sm:text-base text-slate-300 max-w-2xl leading-relaxed">
              Pendaftaran daring Gelombang I dibuka untuk jalur Reguler, Prestasi Akademik/Non-Akademik, serta Afirmasi. Pelajari syarat dokumen dan tahapan seleksinya.
            </p>
          </div>
          <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-3.5 justify-end">
            <a href="{{ Route::has('landing.ppdb') ? route('landing.ppdb') . '#form-ppdb' : '#form-ppdb' }}" class="px-6 py-4 rounded-xl text-sm font-semibold text-white bg-[#2563EB] hover:bg-blue-500 text-center transition-colors shadow-lg">
              Daftar Online Sekarang
            </a>
            <a href="{{ Route::has('landing.ppdb') ? route('landing.ppdb') . '#persyaratan' : '#persyaratan' }}" class="px-6 py-4 rounded-xl text-sm font-semibold text-white bg-white/10 hover:bg-white/15 border border-white/20 text-center transition-colors">
              Lihat Persyaratan &amp; Jadwal
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /COMPONENT: PpdbCtaBanner -->
</main>
