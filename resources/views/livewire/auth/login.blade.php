<div class="flex-1 grid grid-cols-1 lg:grid-cols-12 min-h-screen">
  <!-- Left Visual & Brand Column (Desktop Split) -->
  <div class="hidden lg:flex lg:col-span-6 relative bg-[#0F172A] text-white p-12 xl:p-16 flex-col justify-between overflow-hidden">
    <img src="{{ asset('assets/images/hero-campus.jpg') }}" alt="Kampus Nama Sekolah" class="absolute inset-0 w-full h-full object-cover opacity-25" />
    <div class="absolute inset-0 bg-gradient-to-br from-[#0F172A] via-[#0F172A]/90 to-[#1E3A8A]/80"></div>

    <div class="relative z-10 flex items-center justify-between">
      <a href="{{ route('home') }}" class="flex items-center gap-3.5 group">
        <img src="{{ asset('assets/images/logo-sekolah.svg') }}" alt="Logo Nama Sekolah" class="w-11 h-11 rounded-xl" />
        <div>
          <span class="block font-heading font-bold text-xl text-white">Nama Sekolah</span>
          <span class="block text-xs text-slate-300">Portal Sistem Informasi Sekolah</span>
        </div>
      </a>
      <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/20 border border-white/15 transition-colors">
        &larr; Kembali ke Beranda
      </a>
    </div>

    <div class="relative z-10 max-w-lg space-y-5 my-auto">
      <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-[#0EA5E9] border border-sky-400/30">
        Ekosistem Akademik Digital Terintegrasi
      </span>
      <h1 class="font-heading text-3xl xl:text-4xl font-extrabold text-white leading-tight">
        Satu Akses untuk Pembelajaran, E-Rapor, Presensi, dan Layanan Akademik.
      </h1>
      <p class="text-sm text-slate-300 leading-relaxed">
        Portal ini dirancang untuk mempermudah kolaborasi antara pendidik, peserta didik, orang tua/wali, serta tenaga administrasi sekolah secara aman dan terstruktur.
      </p>
      <div class="grid grid-cols-3 gap-3 pt-2 text-xs">
        <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
          <strong class="block text-white font-semibold">E-Learning</strong>
          <span class="text-slate-400">Materi &amp; tugas kelas</span>
        </div>
        <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
          <strong class="block text-white font-semibold">E-Rapor</strong>
          <span class="text-slate-400">Capaian hasil belajar</span>
        </div>
        <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
          <strong class="block text-white font-semibold">SIAKAD</strong>
          <span class="text-slate-400">Jadwal &amp; presensi</span>
        </div>
      </div>
    </div>

    <div class="relative z-10 text-xs text-slate-400 flex items-center justify-between">
      <span>&copy; {{ date('Y') }} Nama Sekolah</span>
      <span>Sistem Informasi Manajemen Sekolah</span>
    </div>
  </div>

  <!-- Right Form Column -->
  <div class="lg:col-span-6 flex flex-col justify-center p-6 sm:p-12 xl:p-16 bg-white">
    <div class="max-w-md w-full mx-auto space-y-7">
      <!-- Mobile Top Bar -->
      <div class="flex lg:hidden items-center justify-between pb-4 border-b border-slate-200">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
          <img src="{{ asset('assets/images/logo-sekolah.svg') }}" alt="Logo Nama Sekolah" class="w-9 h-9 rounded-lg" />
          <span class="font-heading font-bold text-base text-[#0F172A]">Nama Sekolah</span>
        </a>
        <a href="{{ route('home') }}" class="text-xs font-semibold text-[#2563EB] hover:underline">&larr; Beranda Publik</a>
      </div>

      <div>
        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#2563EB] mb-2">
          Autentikasi Sivitas
        </span>
        <h2 class="font-heading text-2xl sm:text-3xl font-bold text-[#0F172A]">Masuk ke Portal Sekolah</h2>
        <p class="text-xs sm:text-sm text-[#64748B] mt-1.5">
          Masukkan alamat email institusi beserta kata sandi Anda.
        </p>
      </div>

      <!-- Alerts -->
      @if (session('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 flex items-center gap-2">
          <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      <!-- Login Form -->
      <form wire:submit="authenticate" class="space-y-5" novalidate x-data="{
          fillDemo(type) {
              if (type === 'admin') {
                  $wire.set('email', 'admin@sekolah.com');
                  $wire.set('password', 'password');
              } else if (type === 'guru') {
                  $wire.set('email', 'guru@sekolah.com');
                  $wire.set('password', 'password');
              }
          }
      }">
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label for="login-identity" class="block text-xs font-semibold text-[#0F172A]">Email Institusi</label>
            <div class="flex items-center gap-2 text-xs">
              <span class="text-slate-400">Demo:</span>
              <button type="button" @click="fillDemo('admin')" class="font-semibold text-[#2563EB] hover:underline">
                Admin
              </button>
              <span class="text-slate-300">|</span>
              <button type="button" @click="fillDemo('guru')" class="font-semibold text-[#2563EB] hover:underline">
                Guru
              </button>
            </div>
          </div>
          <input
            id="login-identity"
            type="email"
            wire:model="email"
            required
            autocomplete="username"
            placeholder="admin@sekolah.com"
            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-red-400 bg-red-50/30' : 'border-slate-300' }} text-sm focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-blue-100 transition"
          />
          @error('email')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              <span>{{ $message }}</span>
            </p>
          @enderror
        </div>

        <div x-data="{ showPass: false }">
          <div class="flex items-center justify-between mb-1.5">
            <label for="login-password" class="block text-xs font-semibold text-[#0F172A]">Kata Sandi</label>
            <span class="text-xs text-slate-400">Minimal 6 karakter</span>
          </div>
          <div class="relative">
            <input
              id="login-password"
              :type="showPass ? 'text' : 'password'"
              wire:model="password"
              required
              autocomplete="current-password"
              placeholder="Masukkan kata sandi"
              class="w-full pl-4 pr-24 py-3 rounded-xl border {{ $errors->has('password') ? 'border-red-400 bg-red-50/30' : 'border-slate-300' }} text-sm focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-blue-100 transition"
            />
            <button
              type="button"
              @click="showPass = !showPass"
              aria-label="Tampilkan kata sandi"
              class="absolute right-2.5 top-1/2 -translate-y-1/2 px-2.5 py-1 rounded-lg text-xs font-semibold text-[#64748B] hover:text-[#0F172A] bg-slate-100 transition"
            >
              <span x-text="showPass ? 'Sembunyikan' : 'Tampilkan'"></span>
            </button>
          </div>
          @error('password')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              <span>{{ $message }}</span>
            </p>
          @enderror
        </div>

        <div class="flex items-center justify-between">
          <label class="inline-flex items-center gap-2 text-xs text-[#64748B] cursor-pointer">
            <input type="checkbox" wire:model="remember" class="rounded border-slate-300 text-[#2563EB] focus:ring-[#2563EB]" />
            <span>Ingat perangkat ini</span>
          </label>
          <span class="text-xs text-slate-400">Koneksi Terenkripsi</span>
        </div>

        <button
          id="login-submit-btn"
          type="submit"
          wire:loading.attr="disabled"
          class="w-full py-3.5 px-5 rounded-xl text-sm font-semibold text-white bg-[#0F172A] hover:bg-[#2563EB] transition-colors shadow-sm flex items-center justify-center gap-2 disabled:opacity-75"
        >
          <span wire:loading.remove wire:target="authenticate" class="inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            <span>Masuk ke Portal Akademik</span>
          </span>
          <span wire:loading wire:target="authenticate" class="inline-flex items-center gap-2">
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor" opacity="0.4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4a8 8 0 018 8"></path></svg>
            <span>Memproses Autentikasi...</span>
          </span>
        </button>
      </form>

      <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#64748B]">
        <span>Calon peserta didik baru? <a href="{{ Route::has('landing.ppdb') ? route('landing.ppdb') : '#' }}" class="font-semibold text-[#2563EB] hover:underline">Daftar PPDB Online</a></span>
        <a href="{{ Route::has('landing.contact') ? route('landing.contact') : '#' }}" class="hover:text-[#0F172A]">Bantuan Teknis IT</a>
      </div>
    </div>
  </div>
</div>