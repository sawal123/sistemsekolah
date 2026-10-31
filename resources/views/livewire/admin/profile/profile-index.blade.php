<div class="flex flex-col gap-6 pb-20 p-4 md:p-8">
    {{-- Header Halaman --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 fu d1">
        <div>
            <h1 class="txt-primary text-2xl font-extrabold flex items-center gap-2">
                <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profil Saya
            </h1>
            <p class="txt-muted text-sm mt-1">Kelola informasi pribadi, data kepegawaian, dan identitas akun Anda.</p>
        </div>

        <a href="{{ route('admin.settings') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl border border-indigo-500/20 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500/20 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <circle cx="12" cy="12" r="3" stroke-width="2" />
            </svg>
            Buka Pengaturan Akun &rarr;
        </a>
    </div>

    {{-- Kartu Ringkasan Akun --}}
    <x-ui.card class="fu d2 border border-indigo-500/10">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-indigo-500/25 shrink-0">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl font-black txt-primary">{{ $user->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold uppercase {{ $user->hasRole('admin') ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' : 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' }}">
                            {{ $user->hasRole('admin') ? 'Administrator' : ($user->hasRole('guru') ? 'Guru Pengajar' : 'Pengguna') }}
                        </span>
                    </div>
                    <p class="text-xs txt-muted mt-1">{{ $user->email }}</p>
                    <div class="flex items-center gap-4 text-xs txt-muted mt-2">
                        <span>Bergabung: <strong class="txt-primary">{{ $user->created_at?->translatedFormat('d F Y') }}</strong></span>
                        @if($user->last_login_at)
                            <span>● Terakhir Login: <strong class="txt-primary">{{ $user->last_login_at->diffForHumans() }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>

            @if($isGuru)
                <div class="grid grid-cols-2 gap-3 w-full md:w-auto">
                    <div class="p-3 rounded-xl bg-indigo-500/5 border border-indigo-500/10 text-center">
                        <p class="text-[10px] txt-muted font-bold uppercase">Perwalian</p>
                        <p class="text-sm font-extrabold text-indigo-500 mt-0.5">
                            {{ $kelasWali ? $kelasWali->nama_kelas : 'Bukan Wali' }}
                        </p>
                    </div>
                    <div class="p-3 rounded-xl bg-indigo-500/5 border border-indigo-500/10 text-center">
                        <p class="text-[10px] txt-muted font-bold uppercase">Jadwal Aktif</p>
                        <p class="text-sm font-extrabold text-indigo-500 mt-0.5">
                            {{ $totalJadwal }} Sesi KBM
                        </p>
                    </div>
                </div>
            @elseif($staff_position)
                <div class="p-3 px-4 rounded-xl bg-indigo-500/5 border border-indigo-500/10">
                    <p class="text-[10px] txt-muted font-bold uppercase">Posisi Pegawai</p>
                    <p class="text-sm font-extrabold text-indigo-500 mt-0.5">{{ $staff_position }}</p>
                </div>
            @endif
        </div>
    </x-ui.card>

    {{-- Form Edit Data Diri --}}
    <form wire:submit="updateProfile" class="space-y-6">
        <x-ui.card class="fu d3 border border-indigo-500/10">
            <h3 class="text-base font-bold txt-primary mb-4 pb-3 border-b border-gray-100 dark:border-white/5 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Informasi Akun
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <x-ui.label value="Nama Lengkap" required />
                    <x-ui.input wire:model="name" type="text" placeholder="Masukkan nama lengkap Anda..." />
                    <x-ui.input-error for="name" />
                </div>
                <div>
                    <x-ui.label value="Alamat Email (Akun Login)" required />
                    <x-ui.input wire:model="email" type="email" placeholder="nama@sekolah.com" />
                    <x-ui.input-error for="email" />
                </div>
            </div>
        </x-ui.card>

        @if($isGuru)
            <x-ui.card class="fu d3 border border-indigo-500/10">
                <h3 class="text-base font-bold txt-primary mb-4 pb-3 border-b border-gray-100 dark:border-white/5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Biodata Tenaga Pendidik
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <x-ui.label value="Nomor Induk Pegawai (NIP)" />
                        <x-ui.input wire:model="nip" type="text" disabled class="opacity-70 bg-gray-50 dark:bg-black/20 cursor-not-allowed" title="NIP dikelola oleh Administrator" />
                        <p class="text-[10px] txt-muted mt-1">Perubahan NIP diatur oleh Administrator Sekolah.</p>
                    </div>
                    <div>
                        <x-ui.label value="Nomor Telepon / WhatsApp" />
                        <x-ui.input wire:model="no_telp" type="text" placeholder="Contoh: 081234567890" />
                        <x-ui.input-error for="no_telp" />
                    </div>
                    <div>
                        <x-ui.label value="Agama" />
                        <x-ui.select wire:model="agama" :options="[
                            '' => 'Pilih Agama',
                            'Islam' => 'Islam',
                            'Kristen' => 'Kristen',
                            'Katolik' => 'Katolik',
                            'Hindu' => 'Hindu',
                            'Buddha' => 'Buddha',
                            'Konghucu' => 'Konghucu',
                            'Lainnya' => 'Lainnya',
                        ]" />
                        <x-ui.input-error for="agama" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <div>
                        <x-ui.label value="Tempat Lahir" />
                        <x-ui.input wire:model="tempat_lahir" type="text" placeholder="Kota / Kabupaten kelahiran..." />
                        <x-ui.input-error for="tempat_lahir" />
                    </div>
                    <div>
                        <x-ui.label value="Tanggal Lahir" />
                        <x-ui.input wire:model="tanggal_lahir" type="date" />
                        <x-ui.input-error for="tanggal_lahir" />
                    </div>
                </div>

                <div class="mt-5">
                    <x-ui.label value="Alamat Domisili" />
                    <textarea wire:model="alamat" rows="3" class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-[#0c0e1a]/50 p-3 txt-primary text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Alamat tempat tinggal lengkap saat ini..."></textarea>
                    <x-ui.input-error for="alamat" />
                </div>
            </x-ui.card>
        @endif

        <div class="flex items-center justify-end gap-3 pt-2">
            <x-ui.button type="submit" variant="primary" class="px-6 py-2.5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Perubahan Profil
            </x-ui.button>
        </div>
    </form>
</div>

