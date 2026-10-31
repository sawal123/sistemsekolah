<div class="flex flex-col gap-6 pb-20 p-4 md:p-8">
    {{-- Header Halaman --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 fu d1">
        <div>
            <h1 class="txt-primary text-2xl font-extrabold flex items-center gap-2">
                <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <circle cx="12" cy="12" r="3" stroke-width="2" />
                </svg>
                Pengaturan
            </h1>
            <p class="txt-muted text-sm mt-1">Konfigurasi keamanan akun, kata sandi, dan preferensi operasional sistem.</p>
        </div>

        <a href="{{ route('admin.profile') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl border border-indigo-500/20 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500/20 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            &larr; Lihat Profil Saya
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Sisi Kiri: Keamanan Akun & Ubah Kata Sandi --}}
        <div class="space-y-6">
            <x-ui.card class="fu d2 border border-indigo-500/10">
                <h3 class="text-base font-bold txt-primary mb-1 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Ubah Kata Sandi
                </h3>
                <p class="text-xs txt-muted mb-5">Pastikan kata sandi baru Anda kuat dan tidak mudah ditebak.</p>

                <form wire:submit="updatePassword" class="space-y-4">
                    <div>
                        <x-ui.label value="Kata Sandi Saat Ini" required />
                        <x-ui.input wire:model="current_password" type="password" placeholder="Masukkan kata sandi saat ini..." autocomplete="current-password" />
                        <x-ui.input-error for="current_password" />
                    </div>

                    <div>
                        <x-ui.label value="Kata Sandi Baru" required />
                        <x-ui.input wire:model="new_password" type="password" placeholder="Minimal 8 karakter..." autocomplete="new-password" />
                        <x-ui.input-error for="new_password" />
                    </div>

                    <div>
                        <x-ui.label value="Konfirmasi Kata Sandi Baru" required />
                        <x-ui.input wire:model="new_password_confirmation" type="password" placeholder="Ulangi kata sandi baru..." autocomplete="new-password" />
                    </div>

                    <div class="pt-2 flex justify-end">
                        <x-ui.button type="submit" variant="primary">
                            Perbarui Kata Sandi
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            {{-- Preferensi Tampilan --}}
            <x-ui.card class="fu d3 border border-indigo-500/10">
                <h3 class="text-base font-bold txt-primary mb-1 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                    Preferensi Tampilan
                </h3>
                <p class="text-xs txt-muted mb-4">Pilih mode tampilan tema antarmuka sesuai kenyamanan Anda.</p>

                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/5">
                    <div>
                        <p class="text-sm font-bold txt-primary">Mode Gelap / Terang</p>
                        <p class="text-xs txt-muted mt-0.5">Beralih antara nuansa visual Light atau Dark.</p>
                    </div>
                    <button type="button" @click="$store.theme.toggle()" class="cursor-pointer inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-xs font-bold hover:bg-indigo-500/20 transition">
                        <svg x-show="!$store.theme.dark" class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                        </svg>
                        <svg x-show="$store.theme.dark" x-cloak class="w-4 h-4 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                        <span x-text="$store.theme.dark ? 'Tema Gelap' : 'Tema Terang'"></span>
                    </button>
                </div>
            </x-ui.card>
        </div>

        {{-- Sisi Kanan: Pengaturan Khusus Admin atau Panduan --}}
        <div class="space-y-6">
            @if($isAdmin)
                {{-- Standar Operasional KBM --}}
                <x-ui.card class="fu d2 border border-indigo-500/10">
                    <h3 class="text-base font-bold txt-primary mb-1 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Standar Waktu KBM & Penjadwalan
                    </h3>
                    <p class="text-xs txt-muted mb-5">Konfigurasi generator waktu otomatis pada modul Jadwal Pelajaran.</p>

                    <form wire:submit="updateKbmSettings" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-ui.label value="Jam Mulai Pelajaran (Jam Ke-1)" required />
                                <x-ui.input wire:model="jam_mulai_pelajaran" type="time" />
                                <x-ui.input-error for="jam_mulai_pelajaran" />
                                <p class="text-[10px] txt-muted mt-1">Default: 07:15 WIB</p>
                            </div>
                            <div>
                                <x-ui.label value="Durasi per Jam (Menit)" required />
                                <x-ui.input wire:model="durasi_jam_pelajaran" type="number" min="20" max="120" />
                                <x-ui.input-error for="durasi_jam_pelajaran" />
                                <p class="text-[10px] txt-muted mt-1">Default: 45 Menit</p>
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <x-ui.button type="submit" variant="primary">
                                Simpan Waktu KBM
                            </x-ui.button>
                        </div>
                    </form>
                </x-ui.card>

                {{-- Tautan Cepat Pengaturan Sistem Lainnya --}}
                <x-ui.card class="fu d3 border border-indigo-500/10">
                    <h3 class="text-base font-bold txt-primary mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Pengaturan Sekolah & Website
                    </h3>
                    <p class="text-xs txt-muted mb-4">Pengaturan identitas sekolah, logo, media sosial, dan kontak publik.</p>

                    <div class="space-y-2.5">
                        <a href="{{ route('admin.website.pengaturan-umum') }}" wire:navigate class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.01] hover:bg-indigo-500/5 hover:border-indigo-500/20 transition group">
                            <div>
                                <p class="text-xs font-bold txt-primary group-hover:text-indigo-500 transition-colors">Identitas Sekolah, Logo & Kontak</p>
                                <p class="text-[10px] txt-muted mt-0.5">Nama sekolah, alamat, telepon, email resmi, dan media sosial</p>
                            </div>
                            <span class="text-xs text-indigo-500 font-bold">&rarr;</span>
                        </a>

                        <a href="{{ route('admin.data-master.tahun-ajaran') }}" wire:navigate class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.01] hover:bg-indigo-500/5 hover:border-indigo-500/20 transition group">
                            <div>
                                <p class="text-xs font-bold txt-primary group-hover:text-indigo-500 transition-colors">Tahun Ajaran & Semester Aktif</p>
                                <p class="text-[10px] txt-muted mt-0.5">Kelola status aktif/tutup periode akademik dan kenaikan kelas</p>
                            </div>
                            <span class="text-xs text-indigo-500 font-bold">&rarr;</span>
                        </a>
                    </div>
                </x-ui.card>
            @else
                {{-- Informasi Hak Akses Guru --}}
                <x-ui.card class="fu d2 border border-indigo-500/10">
                    <h3 class="text-base font-bold txt-primary mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Hak Akses Akun
                    </h3>
                    <p class="text-xs txt-muted leading-relaxed">
                        Akun Anda terdaftar dengan peran <strong>Guru Pengajar</strong>. Hak akses Anda mencakup pengelolaan nilai mata pelajaran yang diampu, rekap absensi dan pengisian rapor untuk kelas perwalian Anda.
                    </p>
                    <div class="mt-4 p-3.5 rounded-xl bg-indigo-500/5 border border-indigo-500/10 text-xs text-indigo-600 dark:text-indigo-400">
                        <p class="font-bold">Butuh Bantuan Akun?</p>
                        <p class="mt-1 text-[11px] txt-muted">Jika terdapat ketidaksesuaian penugasan mata pelajaran atau NIP, silakan hubungi Administrator atau Bagian Tata Usaha.</p>
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>
</div>

