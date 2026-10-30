<!-- COMPONENT: LightboxModal -->
<div id="lightbox-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-6" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="lightbox-title">
  <div id="lightbox-backdrop" class="fixed inset-0 bg-[#0F172A]/85 backdrop-blur-sm"></div>
  <div class="relative z-10 max-w-4xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl border border-slate-200">
    <div class="relative bg-slate-950 aspect-16/10 flex items-center justify-center">
      <img id="lightbox-image" src="{{ asset('assets/images/hero-campus.jpg') }}" alt="Preview Dokumentasi" class="max-h-[70vh] w-auto object-contain" />
      <button id="lightbox-close" type="button" aria-label="Tutup pratinjau foto" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-[#0F172A]/80 text-white hover:bg-[#2563EB] flex items-center justify-center transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="p-5 sm:p-6 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <span id="lightbox-category" class="inline-block text-xs font-semibold uppercase tracking-wider text-[#2563EB] mb-1">Galeri Kegiatan</span>
        <h3 id="lightbox-title" class="font-heading text-lg font-bold text-[#0F172A]">Dokumentasi Kegiatan Sekolah</h3>
        <p id="lightbox-desc" class="text-sm text-[#64748B] mt-1">Suasana pembelajaran aktif dan kolaboratif di lingkungan Nama Sekolah.</p>
      </div>
    </div>
  </div>
</div>
<!-- /COMPONENT: LightboxModal -->

<!-- COMPONENT: Footer -->
<footer class="bg-[#0F172A] text-slate-300 border-t border-slate-800">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 pb-12 border-b border-slate-800/90">
      <!-- Col 1: Brand & Description (4 cols) -->
      <div class="lg:col-span-4 space-y-5">
        <a href="{{ route('home') }}" class="flex items-center gap-3.5">
          <img src="{{ asset('assets/images/logo-sekolah.svg') }}" alt="Logo Nama Sekolah" class="w-11 h-11 rounded-xl" />
          <div>
            <span class="block font-heading font-bold text-xl text-white tracking-tight">Nama Sekolah</span>
            <span class="block text-xs text-slate-400">Institusi Pendidikan Menengah Modern</span>
          </div>
        </a>
        <p class="text-sm text-slate-400 leading-relaxed">
          Membangun generasi pembelajar yang berkarakter tangguh, unggul dalam literasi sains serta teknologi, dan siap berkontribusi positif bagi masyarakat luas.
        </p>
        <!-- Social Media Icons -->
        <div class="flex items-center gap-3 pt-1">
          <a href="{{ Route::has('landing.contact') ? route('landing.contact') : '#' }}" aria-label="Instagram Sekolah" class="w-9 h-9 rounded-lg bg-slate-800/90 hover:bg-[#2563EB] text-slate-300 hover:text-white flex items-center justify-center transition-colors">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
          </a>
          <a href="{{ Route::has('landing.contact') ? route('landing.contact') : '#' }}" aria-label="YouTube Sekolah" class="w-9 h-9 rounded-lg bg-slate-800/90 hover:bg-[#2563EB] text-slate-300 hover:text-white flex items-center justify-center transition-colors">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
          </a>
          <a href="{{ Route::has('landing.contact') ? route('landing.contact') : '#' }}" aria-label="LinkedIn Sekolah" class="w-9 h-9 rounded-lg bg-slate-800/90 hover:bg-[#2563EB] text-slate-300 hover:text-white flex items-center justify-center transition-colors">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
          </a>
        </div>
      </div>

      <!-- Col 2: Tautan Cepat (2 cols) -->
      <div class="lg:col-span-2 space-y-3">
        <h3 class="font-heading text-sm font-bold text-white uppercase tracking-wider">Tautan Cepat</h3>
        <ul class="space-y-2.5 text-sm text-slate-400">
          <li><a href="{{ Route::has('landing.about') ? route('landing.about') : '#' }}" class="hover:text-white transition-colors">Profil Sekolah</a></li>
          <li><a href="{{ Route::has('landing.staffing') ? route('landing.staffing', 'guru') : '#' }}" class="hover:text-white transition-colors">Direktori Guru</a></li>
          <li><a href="{{ Route::has('landing.facilities') ? route('landing.facilities') : '#' }}" class="hover:text-white transition-colors">Fasilitas Kampus</a></li>
          <li><a href="#prestasi" class="hover:text-white transition-colors">Prestasi Siswa</a></li>
          <li><a href="#galeri" class="hover:text-white transition-colors">Galeri Kegiatan</a></li>
          <li><a href="#alumni" class="hover:text-white transition-colors">Jejaring Alumni</a></li>
        </ul>
      </div>

      <!-- Col 3: Informasi Akademik (3 cols) -->
      <div class="lg:col-span-3 space-y-3">
        <h3 class="font-heading text-sm font-bold text-white uppercase tracking-wider">Layanan &amp; Akademik</h3>
        <ul class="space-y-2.5 text-sm text-slate-400">
          <li><a href="{{ Route::has('landing.academic') ? route('landing.academic', 'kurikulum') : '#' }}" class="hover:text-white transition-colors">Kurikulum &amp; Program Peminatan</a></li>
          <li><a href="{{ Route::has('landing.ppdb') ? route('landing.ppdb') : '#' }}" class="hover:text-white transition-colors">Informasi PPDB Online</a></li>
          <li><a href="#pengumuman" class="hover:text-white transition-colors">Pusat Pengumuman Resmi</a></li>
          <li><a href="#agenda" class="hover:text-white transition-colors">Agenda &amp; Kalender Akademik</a></li>
          <li><a href="{{ Route::has('landing.blog') ? route('landing.blog') : '#' }}" class="hover:text-white transition-colors">Berita &amp; Artikel Pendidikan</a></li>
          <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Login Portal Sivitas Akademika</a></li>
        </ul>
      </div>

      <!-- Col 4: Kontak & Alamat (3 cols) -->
      <div class="lg:col-span-3 space-y-3">
        <h3 class="font-heading text-sm font-bold text-white uppercase tracking-wider">Kontak &amp; Alamat</h3>
        <ul class="space-y-3 text-sm text-slate-400">
          <li class="flex items-start gap-3">
            <svg class="w-5 h-5 text-[#0EA5E9] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Jl. Pendidikan Nasional No. 10, Kawasan Akademika, Kota Contoh, Indonesia</span>
          </li>
          <li class="flex items-center gap-3">
            <svg class="w-5 h-5 text-[#0EA5E9] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <span>(021) 555-0192 / 0812-0000-0000</span>
          </li>
          <li class="flex items-center gap-3">
            <svg class="w-5 h-5 text-[#0EA5E9] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span>info@namasekolah.sch.id</span>
          </li>
          <li class="flex items-center gap-3">
            <svg class="w-5 h-5 text-[#F59E0B] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Senin – Jumat: 07.00 – 16.00 WIB</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Bottom Copyright -->
    <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
      <p>&copy; 2026 <strong class="text-white font-semibold">Nama Sekolah</strong>. Seluruh hak cipta dilindungi.</p>
      <div class="flex items-center gap-6">
        <a href="{{ Route::has('landing.about') ? route('landing.about') : '#' }}" class="hover:text-white transition-colors">Kebijakan Privasi</a>
        <a href="#pengumuman" class="hover:text-white transition-colors">Standar Pelayanan</a>
        <a href="{{ Route::has('landing.contact') ? route('landing.contact') : '#' }}" class="hover:text-white transition-colors">Peta Situs</a>
      </div>
    </div>
  </div>
</footer>
<!-- /COMPONENT: Footer -->

<!-- COMPONENT: FloatingUtilities -->
<div id="action-toast" class="fixed bottom-6 left-6 z-50 bg-[#0F172A] text-white px-4 py-3 rounded-xl shadow-xl border border-slate-700 text-sm font-medium flex items-center gap-2.5 opacity-0 pointer-events-none translate-y-4 transition-all duration-300" role="status" aria-live="polite">
  <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
  <span id="action-toast-text">Tautan berhasil disalin!</span>
</div>

<button id="scroll-to-top" type="button" aria-label="Kembali ke atas halaman" class="fixed bottom-6 right-6 z-40 w-12 h-12 rounded-2xl bg-[#2563EB] hover:bg-[#0F172A] text-white shadow-lg flex items-center justify-center opacity-0 pointer-events-none translate-y-4 transition-all duration-300">
  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
</button>
<!-- /COMPONENT: FloatingUtilities -->
