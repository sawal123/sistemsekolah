<div class="flex flex-col gap-6 pb-20">
    <x-ui.toast />

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Pendaftaran Murid Baru</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Kelola daftar calon siswa, pilihan sekolah, dan kelengkapan berkas PPDB.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <x-ui.button wire:click="openGelombangModal" variant="secondary" class="w-full md:w-auto justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Kelola Gelombang
            </x-ui.button>
            <x-ui.button wire:click="openModal" variant="primary" class="shadow-lg w-full md:w-auto justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pendaftaran
            </x-ui.button>
        </div>
    </div>

    <x-ui.card padding="0">
        <div class="p-4 border-b border-indigo-500/10 dark:border-white/10 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="txt-primary text-sm font-bold">Gelombang Pendaftaran</h2>
                <p class="txt-muted text-xs mt-1">Atur periode buka/tutup PPDB dan lihat histori perubahan status.</p>
            </div>
            <button type="button" wire:click="openGelombangModal" class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-indigo-500/10 text-indigo-500 text-xs font-bold border border-indigo-500/20 hover:bg-indigo-500/20">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Gelombang
            </button>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_360px] gap-4 p-4">
            <div class="rounded-xl overflow-hidden border border-indigo-500/10 dark:border-white/10">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-indigo-500/5 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider txt-muted">Tahun</th>
                                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider txt-muted">Gelombang</th>
                                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider txt-muted">Periode</th>
                                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider txt-muted">Pendaftar</th>
                                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider txt-muted">Status</th>
                                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider txt-muted text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                            @forelse($gelombangs as $gelombang)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-semibold txt-primary">{{ $gelombang->tahun_pendaftaran }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-bold txt-primary">{{ $gelombang->nama_gelombang }}</div>
                                        @if($gelombang->deskripsi)
                                            <div class="text-[11px] txt-muted line-clamp-1">{{ $gelombang->deskripsi }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs txt-muted">
                                        {{ $gelombang->tanggal_mulai?->format('d/m/Y') ?? '-' }} - {{ $gelombang->tanggal_selesai?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-bold txt-primary">{{ $gelombang->pendaftarans_count }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $gelombangStatusClass = [
                                                'Draft' => 'bg-slate-500/10 text-slate-500',
                                                'Dibuka' => 'bg-emerald-500/10 text-emerald-500',
                                                'Ditutup' => 'bg-red-500/10 text-red-500',
                                            ][$gelombang->status] ?? 'bg-slate-500/10 text-slate-500';
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $gelombangStatusClass }}">{{ $gelombang->status }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" wire:click="editGelombang({{ $gelombang->id }})" class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500" title="Edit Gelombang">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a2.5 2.5 0 10-3.536-3.536L4 16.928V20z" />
                                                </svg>
                                            </button>
                                            @if($gelombang->status !== 'Dibuka')
                                                <button type="button" wire:click="openGelombang({{ $gelombang->id }})" class="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500" title="Buka Pendaftaran">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-9 4h10a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6a2 2 0 012-2z" />
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="button" wire:click="closeGelombang({{ $gelombang->id }})" class="p-2 rounded-lg hover:bg-red-500/10 text-red-500" title="Tutup Pendaftaran">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center txt-muted text-sm">Belum ada gelombang PPDB.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="rounded-xl border border-indigo-500/10 dark:border-white/10 p-4">
                <h3 class="txt-primary text-sm font-bold">Riwayat Pembukaan</h3>
                <div class="mt-3 space-y-3">
                    @forelse($riwayatGelombangs as $riwayat)
                        <div class="border-l-2 border-indigo-500/30 pl-3">
                            <p class="text-xs font-bold txt-primary">{{ $riwayat->aksi }} - {{ $riwayat->gelombang?->nama_gelombang ?? '-' }}</p>
                            <p class="text-[11px] txt-muted">{{ $riwayat->gelombang?->tahun_pendaftaran ?? '-' }} | {{ $riwayat->status_sebelum ?: '-' }} -> {{ $riwayat->status_sesudah ?: '-' }}</p>
                            <p class="text-[11px] txt-muted">{{ $riwayat->terjadi_pada?->format('d/m/Y H:i') }} oleh {{ $riwayat->user?->name ?? 'Sistem' }}</p>
                        </div>
                    @empty
                        <p class="text-xs txt-muted">Belum ada riwayat pembukaan pendaftaran.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </x-ui.card>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.card class="bg-indigo-500/5 border-indigo-500/10">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-500/10 rounded-xl text-indigo-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.5L19 8.5V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs txt-muted font-medium">Total Pendaftar</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card class="bg-amber-500/5 border-amber-500/10">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-amber-500/10 rounded-xl text-amber-500 text-xs font-bold">BARU</div>
                <div>
                    <p class="text-xs txt-muted font-medium">Belum Diperiksa</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['baru'] }}</h3>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card class="bg-emerald-500/5 border-emerald-500/10">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-500/10 rounded-xl text-emerald-500 text-xs font-bold">OK</div>
                <div>
                    <p class="text-xs txt-muted font-medium">Berkas Lengkap</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['lengkap'] }}</h3>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card class="bg-blue-500/5 border-blue-500/10">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-500/10 rounded-xl text-blue-500 text-xs font-bold">IN</div>
                <div>
                    <p class="text-xs txt-muted font-medium">Diterima</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['diterima'] }}</h3>
                </div>
            </div>
        </x-ui.card>
    </div>

    <x-ui.card padding="0">
        <div class="flex flex-wrap items-center gap-3 p-3">
            <div class="w-20 flex-shrink-0">
                <x-ui.select wire:model.live="perPage" :options="['10' => '10', '20' => '20', '50' => '50']" />
            </div>

            <div class="h-7 w-px bg-indigo-500/20 flex-shrink-0 hidden sm:block"></div>

            <div class="flex-1 min-w-[180px]">
                <x-ui.search model="search" placeholder="Cari nama, NISN, NIK, atau sekolah asal..." />
            </div>

            <div class="w-44 flex-shrink-0">
                <x-ui.select wire:model.live="filterStatus" :options="['' => 'Semua Status'] + $statusOptions" />
            </div>

            <div class="w-44 flex-shrink-0">
                <x-ui.select wire:model.live="filterTahunPendaftaran" :options="['' => 'Semua Tahun'] + $tahunOptions" />
            </div>

            <div class="w-56 flex-shrink-0">
                <x-ui.select wire:model.live="filterGelombangId" :options="['' => 'Semua Gelombang'] + $filteredGelombangOptions" />
            </div>
        </div>

        <div class="rounded-xl overflow-hidden border border-indigo-500/10 dark:border-white/10 m-3 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr class="bg-indigo-500/5 dark:bg-white/5 border-b border-indigo-500/10 dark:border-white/10">
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted w-[70px]">No</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Calon Siswa</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Asal Sekolah</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Gelombang</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Pilihan</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Dokumen</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                        @forelse($pendaftarans as $item)
                            @php
                                $statusClasses = [
                                    'Baru' => 'bg-amber-500/10 text-amber-500',
                                    'Diperiksa' => 'bg-indigo-500/10 text-indigo-500',
                                    'Lengkap' => 'bg-emerald-500/10 text-emerald-500',
                                    'Diterima' => 'bg-blue-500/10 text-blue-500',
                                    'Ditolak' => 'bg-red-500/10 text-red-500',
                                    'Daftar Ulang' => 'bg-purple-500/10 text-purple-500',
                                    'Aktif' => 'bg-green-500/10 text-green-500',
                                ];
                                $files = [
                                    'Ijazah/SKL' => $item->ijazah_skl,
                                    'KK' => $item->kartu_keluarga,
                                    'Akta' => $item->akta_kelahiran,
                                    'KTP Ayah' => $item->ktp_ayah,
                                    'KTP Ibu' => $item->ktp_ibu,
                                    'Rapor' => $item->buku_rapor,
                                    'Pas Foto' => $item->pas_foto,
                                ];
                            @endphp
                            <tr class="hover:bg-indigo-500/[0.02] dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4 text-sm txt-primary font-medium">
                                    {{ ($pendaftarans->currentPage() - 1) * $pendaftarans->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold txt-primary">{{ $item->gelombang?->nama_gelombang ?? '-' }}</span>
                                        <span class="text-[11px] txt-muted">{{ $item->gelombang?->tahun_pendaftaran ?? 'Tanpa tahun' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold txt-primary leading-tight">{{ $item->nama_lengkap }}</span>
                                        <span class="text-[11px] txt-muted">NISN: {{ $item->nisn ?: '-' }} | NIK: {{ $item->nik ?: '-' }}</span>
                                        <span class="text-[11px] txt-muted">{{ $item->no_hp ?: '-' }}{{ $item->email ? ' | ' . $item->email : '' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold txt-primary">{{ $item->sekolah_asal ?: '-' }}</span>
                                        <span class="text-[11px] txt-muted">NPSN: {{ $item->npsn_sekolah_asal ?: '-' }} | Lulus: {{ $item->tahun_lulus ?: '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm txt-primary">{{ $item->sekolah_pilihan_1 ?: '-' }}</span>
                                        <span class="text-[11px] txt-muted">{{ $item->jurusan_pilihan_1 ?: 'Jurusan belum dipilih' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2">
                                        <div class="text-xs font-bold txt-primary">
                                            {{ $item->dokumen_lengkap }}/{{ $item->total_dokumen }} berkas
                                            <span class="txt-muted font-medium">(per dokumen)</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($files as $label => $path)
                                                @if($path)
                                                    <a href="{{ Storage::url($path) }}" target="_blank" class="px-2 py-1 rounded-lg bg-indigo-500/10 text-indigo-500 text-[10px] font-bold hover:bg-indigo-500/20">
                                                        {{ $label }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClasses[$item->status] ?? 'bg-slate-500/10' }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($item->status === 'Diterima')
                                            <button wire:click="konversiKeSiswa({{ $item->id }})" 
                                                wire:confirm="Konversi {{ $item->nama_lengkap }} menjadi siswa aktif? Pastikan data sudah lengkap dan benar."
                                                class="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-all cursor-pointer" 
                                                title="Konversi ke Siswa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        @endif
                                        <button wire:click="edit({{ $item->id }})" class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500 transition-all cursor-pointer" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $item->id }})" class="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-all cursor-pointer" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-40">
                                        <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.5L19 8.5V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm font-medium">Belum ada data pendaftaran murid baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <x-ui.pagination :links="$pendaftarans" />
    </x-ui.card>

    <x-ui.modal name="pendaftaran-murid-baru-form" :show="$isModalOpen" maxWidth="5xl">
        <div x-data="{ tab: 'berkas' }" class="py-2">
            <h2 class="text-xl font-bold txt-primary mb-1">{{ $editId ? 'Edit Pendaftaran Murid Baru' : 'Tambah Pendaftaran Murid Baru' }}</h2>
            <p class="text-sm txt-muted mb-6">Lengkapi data calon siswa dan unggah berkas sesuai format yang tersedia.</p>

            <div class="grid grid-cols-2 md:grid-cols-6 gap-2 mb-6 bg-indigo-500/[0.03] p-1 rounded-xl">
                @foreach([
                    'berkas' => 'Berkas',
                    'biodata' => 'Biodata',
                    'alamat' => 'Alamat',
                    'asal' => 'Asal',
                    'nilai' => 'Nilai',
                    'ortu' => 'Ortu',
                ] as $key => $label)
                    <button type="button" @click="tab = '{{ $key }}'" class="py-2.5 px-3 rounded-lg text-xs font-bold transition-all" :class="tab === '{{ $key }}' ? 'bg-indigo-500 text-white shadow-lg' : 'txt-muted hover:bg-white/5'">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <form wire:submit.prevent="save">
                <div class="mb-6 rounded-xl border border-indigo-500/10 bg-indigo-500/5 p-4">
                    <x-ui.select label="Tahun & Gelombang Pendaftaran" wire:model="ppdb_gelombang_id" :options="['' => 'Pilih Gelombang'] + $gelombangOptions" />
                    <p class="text-[11px] txt-muted mt-2">Pilih gelombang agar pendaftar bisa difilter berdasarkan periode. Landing page nanti hanya menampilkan gelombang berstatus Dibuka.</p>
                    @error('ppdb_gelombang_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div x-show="tab === 'biodata'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <x-ui.label for="nama_lengkap" value="Nama Lengkap *" class="mb-2" />
                        <x-ui.input wire:model="nama_lengkap" id="nama_lengkap" placeholder="Sesuai Akta Kelahiran/Ijazah" />
                        @error('nama_lengkap') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-ui.label for="nisn" value="NISN" class="mb-2" />
                        <x-ui.input wire:model="nisn" id="nisn" placeholder="Nomor Induk Siswa Nasional" />
                        @error('nisn') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-ui.label for="nik" value="NIK" class="mb-2" />
                        <x-ui.input wire:model="nik" id="nik" placeholder="Nomor Induk Kependudukan" />
                        @error('nik') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-ui.label for="tempat_lahir" value="Tempat Lahir" class="mb-2" />
                        <x-ui.input wire:model="tempat_lahir" id="tempat_lahir" />
                    </div>
                    <div>
                        <x-ui.label for="tanggal_lahir" value="Tanggal Lahir" class="mb-2" />
                        <x-ui.input wire:model="tanggal_lahir" id="tanggal_lahir" type="date" />
                    </div>
                    <div>
                        <x-ui.select label="Jenis Kelamin" wire:model="jenis_kelamin" :options="['' => 'Pilih Jenis Kelamin', 'Laki-Laki' => 'Laki-Laki', 'Perempuan' => 'Perempuan']" />
                    </div>
                    <div>
                        <x-ui.select label="Agama" wire:model="agama" :options="['' => 'Pilih Agama', 'Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu', 'Lainnya' => 'Lainnya']" />
                    </div>
                    <div>
                        <x-ui.label for="no_hp" value="Nomor HP Aktif" class="mb-2" />
                        <x-ui.input wire:model="no_hp" id="no_hp" placeholder="Nomor siswa/orang tua" />
                    </div>
                    <div>
                        <x-ui.label for="email" value="Alamat Email" class="mb-2" />
                        <x-ui.input wire:model="email" id="email" type="email" placeholder="Email siswa/orang tua" />
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div x-show="tab === 'alamat'" x-transition class="grid grid-cols-1 md:grid-cols-4 gap-5">
                    <div class="md:col-span-4">
                        <x-ui.label for="alamat" value="Alamat Lengkap" class="mb-2" />
                        <textarea wire:model="alamat" id="alamat" rows="3" placeholder="Nama jalan, nomor rumah, patokan, dan detail domisili" class="w-full px-4 py-3 glass border-2 border-transparent rounded-xl text-sm txt-primary outline-none focus:border-indigo-500/50 transition-all"></textarea>
                    </div>
                    <div><x-ui.label for="rt" value="RT" class="mb-2" /><x-ui.input wire:model="rt" id="rt" /></div>
                    <div><x-ui.label for="rw" value="RW" class="mb-2" /><x-ui.input wire:model="rw" id="rw" /></div>
                    <div><x-ui.label for="dusun" value="Dusun" class="mb-2" /><x-ui.input wire:model="dusun" id="dusun" /></div>
                    <div><x-ui.label for="kelurahan" value="Kelurahan/Desa" class="mb-2" /><x-ui.input wire:model="kelurahan" id="kelurahan" /></div>
                    <div><x-ui.label for="kecamatan" value="Kecamatan" class="mb-2" /><x-ui.input wire:model="kecamatan" id="kecamatan" /></div>
                    <div><x-ui.label for="kota_kabupaten" value="Kota/Kabupaten" class="mb-2" /><x-ui.input wire:model="kota_kabupaten" id="kota_kabupaten" /></div>
                    <div><x-ui.label for="provinsi" value="Provinsi" class="mb-2" /><x-ui.input wire:model="provinsi" id="provinsi" /></div>
                    <div><x-ui.label for="no_kk" value="Nomor Kartu Keluarga" class="mb-2" /><x-ui.input wire:model="no_kk" id="no_kk" /></div>
                    <div><x-ui.label for="tanggal_terbit_kk" value="Tanggal Terbit KK" class="mb-2" /><x-ui.input wire:model="tanggal_terbit_kk" id="tanggal_terbit_kk" type="date" /></div>
                    <div class="md:col-span-3"><x-ui.label for="koordinat_rumah" value="Titik Koordinat Rumah" class="mb-2" /><x-ui.input wire:model="koordinat_rumah" id="koordinat_rumah" placeholder="-6.200000, 106.816666 atau link Google Maps" /></div>
                </div>

                <div x-show="tab === 'asal'" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div><x-ui.label for="sekolah_asal" value="Nama Sekolah Asal" class="mb-2" /><x-ui.input wire:model="sekolah_asal" id="sekolah_asal" placeholder="SMP/MTs sederajat" /></div>
                    <div><x-ui.label for="npsn_sekolah_asal" value="NPSN Sekolah Asal" class="mb-2" /><x-ui.input wire:model="npsn_sekolah_asal" id="npsn_sekolah_asal" /></div>
                    <div><x-ui.label for="tahun_lulus" value="Tahun Lulus" class="mb-2" /><x-ui.input wire:model="tahun_lulus" id="tahun_lulus" type="number" min="2000" max="{{ now()->year + 1 }}" /></div>
                    <div class="md:col-span-3 pt-3 border-t border-indigo-500/10"></div>
                    <div><x-ui.label for="sekolah_pilihan_1" value="Sekolah Tujuan Pilihan 1" class="mb-2" /><x-ui.input wire:model="sekolah_pilihan_1" id="sekolah_pilihan_1" /></div>
                    <div><x-ui.select label="Jenjang Tujuan" wire:model.live="jenjang_pilihan" :options="['' => 'Pilih Jenjang', 'SMP' => 'SMP', 'SMA' => 'SMA', 'SMK' => 'SMK']" /></div>
                    @if($jenjang_pilihan === 'SMK')
                        <div><x-ui.select label="Jurusan SMK" wire:model="jurusan_id" :options="['' => 'Pilih Jurusan'] + $jurusans->mapWithKeys(fn($j) => [$j->id => $j->kode . ' - ' . $j->nama])->toArray()" /></div>
                    @else
                        <div><x-ui.label for="jurusan_pilihan_1" value="Program/Peminatan" class="mb-2" /><x-ui.input wire:model="jurusan_pilihan_1" id="jurusan_pilihan_1" placeholder="IPA/IPS/Umum" /></div>
                    @endif
                    <div><x-ui.select label="Status Berkas" wire:model="status" :options="$statusOptions" /></div>
                    <div><x-ui.label for="sekolah_pilihan_2" value="Sekolah Tujuan Pilihan 2" class="mb-2" /><x-ui.input wire:model="sekolah_pilihan_2" id="sekolah_pilihan_2" /></div>
                    <div><x-ui.label for="jurusan_pilihan_2" value="Jurusan Pilihan 2" class="mb-2" /><x-ui.input wire:model="jurusan_pilihan_2" id="jurusan_pilihan_2" /></div>
                </div>

                <div x-show="tab === 'nilai'" x-transition class="grid grid-cols-1 md:grid-cols-5 gap-5">
                    @foreach([1, 2, 3, 4, 5] as $semester)
                        <div>
                            <x-ui.label for="nilai_semester_{{ $semester }}" value="Rata-rata Semester {{ $semester }}" class="mb-2" />
                            <x-ui.input wire:model="nilai_semester_{{ $semester }}" id="nilai_semester_{{ $semester }}" type="number" step="0.01" min="0" max="100" />
                            @error('nilai_semester_' . $semester) <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                    <div class="md:col-span-5 pt-3 border-t border-indigo-500/10"></div>
                    <div class="md:col-span-2"><x-ui.label for="nama_prestasi" value="Nama Kejuaraan/Prestasi" class="mb-2" /><x-ui.input wire:model="nama_prestasi" id="nama_prestasi" /></div>
                    <div><x-ui.label for="tingkat_prestasi" value="Tingkat Prestasi" class="mb-2" /><x-ui.input wire:model="tingkat_prestasi" id="tingkat_prestasi" placeholder="Kota/Provinsi/Nasional" /></div>
                    <div><x-ui.label for="tahun_prestasi" value="Tahun Prestasi" class="mb-2" /><x-ui.input wire:model="tahun_prestasi" id="tahun_prestasi" type="number" min="2000" max="{{ now()->year + 1 }}" /></div>
                    <div><x-ui.label for="penyelenggara_prestasi" value="Penyelenggara" class="mb-2" /><x-ui.input wire:model="penyelenggara_prestasi" id="penyelenggara_prestasi" /></div>
                </div>

                <div x-show="tab === 'ortu'" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div><x-ui.label for="nama_ayah" value="Nama Ayah" class="mb-2" /><x-ui.input wire:model="nama_ayah" id="nama_ayah" /></div>
                    <div><x-ui.label for="nik_ayah" value="NIK Ayah" class="mb-2" /><x-ui.input wire:model="nik_ayah" id="nik_ayah" /></div>
                    <div><x-ui.label for="pekerjaan_ayah" value="Pekerjaan Ayah" class="mb-2" /><x-ui.input wire:model="pekerjaan_ayah" id="pekerjaan_ayah" /></div>
                    <div><x-ui.label for="nama_ibu" value="Nama Ibu" class="mb-2" /><x-ui.input wire:model="nama_ibu" id="nama_ibu" /></div>
                    <div><x-ui.label for="nik_ibu" value="NIK Ibu" class="mb-2" /><x-ui.input wire:model="nik_ibu" id="nik_ibu" /></div>
                    <div><x-ui.label for="pekerjaan_ibu" value="Pekerjaan Ibu" class="mb-2" /><x-ui.input wire:model="pekerjaan_ibu" id="pekerjaan_ibu" /></div>
                    <div><x-ui.label for="nama_wali" value="Nama Wali" class="mb-2" /><x-ui.input wire:model="nama_wali" id="nama_wali" /></div>
                    <div><x-ui.label for="nik_wali" value="NIK Wali" class="mb-2" /><x-ui.input wire:model="nik_wali" id="nik_wali" /></div>
                    <div><x-ui.label for="pekerjaan_wali" value="Pekerjaan Wali" class="mb-2" /><x-ui.input wire:model="pekerjaan_wali" id="pekerjaan_wali" /></div>
                    <div><x-ui.label for="penghasilan_ortu" value="Penghasilan Bulanan" class="mb-2" /><x-ui.input wire:model="penghasilan_ortu" id="penghasilan_ortu" placeholder="Contoh: Rp 3.000.000 - Rp 5.000.000" /></div>
                    <div class="md:col-span-2"><x-ui.label for="no_telp_ortu" value="Nomor Telepon Orang Tua/Wali" class="mb-2" /><x-ui.input wire:model="no_telp_ortu" id="no_telp_ortu" /></div>
                </div>

                <div x-show="tab === 'berkas'" x-transition class="space-y-6">
                    @if($aiLastDocument)
                        <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3">
                            <p class="text-sm font-bold text-emerald-600">AI terakhir membaca: {{ $aiLastDocument }}</p>
                            <p class="text-xs txt-muted mt-1">Field yang berhasil dikenali otomatis sudah diisi pada tab terkait. Tetap cek ulang data sebelum menyimpan.</p>
                        </div>
                    @endif

                    <div class="rounded-xl border border-indigo-500/10 bg-indigo-500/5 px-4 py-3">
                        <p class="text-sm font-bold txt-primary">Upload dokumen satu per satu</p>
                        <p class="text-xs txt-muted mt-1">Mode PDF gabungan sementara dinonaktifkan. Format yang diterima: JPG/JPEG atau PDF. Tidak ada minimum ukuran khusus, tetapi pastikan file jelas terbaca.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach([
                            'ijazah_skl' => ['label' => 'Ijazah / Surat Keterangan Lulus (SKL)', 'hint' => 'JPG/JPEG atau PDF, maksimal 5MB. Disarankan untuk AI mengisi nama calon siswa dan NISN.'],
                            'kartu_keluarga' => ['label' => 'Kartu Keluarga (KK)', 'hint' => 'JPG/JPEG atau PDF, maksimal 5MB. Jalankan AI Ijazah/SKL lebih dulu agar baris calon siswa di KK bisa dikenali, lalu KK dipakai untuk alamat, orang tua, dan koordinat.'],
                            'akta_kelahiran' => ['label' => 'Akta Kelahiran', 'hint' => 'JPG/JPEG atau PDF, maksimal 5MB.'],
                            'ktp_ayah' => ['label' => 'KTP Ayah', 'hint' => 'JPG/JPEG atau PDF, maksimal 5MB. Jika tidak tersedia, kosongkan dan beri catatan.'],
                            'ktp_ibu' => ['label' => 'KTP Ibu', 'hint' => 'JPG/JPEG atau PDF, maksimal 5MB. Jika tidak tersedia, kosongkan dan beri catatan.'],
                            'buku_rapor' => ['label' => 'Buku Rapor Semester 1-5', 'hint' => 'JPG/JPEG atau PDF, maksimal 10MB.'],
                            'pas_foto' => ['label' => 'Pas Foto 3x4 atau 4x6', 'hint' => 'JPG/JPEG atau PDF, maksimal 2MB.'],
                        ] as $field => $meta)
                            <div>
                                <x-ui.label for="{{ $field }}" value="{{ $meta['label'] }}" class="mb-2" />
                                <input type="file" wire:model="{{ $field }}" id="{{ $field }}" class="block w-full text-sm txt-primary glass rounded-xl border-2 border-transparent px-3 py-2 focus:border-indigo-500/50" accept=".pdf,.jpg,.jpeg">
                                <p class="text-[11px] txt-muted mt-1">{{ $meta['hint'] }}</p>
                                @if(! empty($existingFiles[$field]))
                                    <a href="{{ Storage::url($existingFiles[$field]) }}" target="_blank" class="text-xs text-indigo-500 font-semibold mt-2 inline-block">Lihat berkas tersimpan</a>
                                @endif
                                @if(in_array($field, ['kartu_keluarga', 'ijazah_skl'], true))
                                    <button type="button" wire:click="extractFromDocument('{{ $field }}')" wire:loading.attr="disabled" wire:target="extractFromDocument('{{ $field }}')"
                                        class="mt-2 inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-500/10 text-emerald-600 text-xs font-bold border border-emerald-500/20 hover:bg-emerald-500/20 disabled:opacity-50">
                                        <svg wire:loading.remove wire:target="extractFromDocument('{{ $field }}')" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.456-2.456L14.25 6l1.035-.259a3.375 3.375 0 002.456-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z" />
                                        </svg>
                                        <svg wire:loading wire:target="extractFromDocument('{{ $field }}')" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="extractFromDocument('{{ $field }}')">Isi otomatis dari AI</span>
                                        <span wire:loading wire:target="extractFromDocument('{{ $field }}')">Membaca dokumen...</span>
                                    </button>
                                @endif
                                <div wire:loading wire:target="{{ $field }}" class="text-xs text-indigo-500 mt-2">Mengunggah...</div>
                                <div wire:loading wire:target="extractFromDocument('{{ $field }}')" class="text-xs text-emerald-600 mt-2">AI sedang membaca dokumen...</div>
                                @error($field) <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <x-ui.label for="catatan" value="Catatan Admin" class="mb-2" />
                        <textarea wire:model="catatan" id="catatan" rows="3" placeholder="Catatan pemeriksaan berkas, kekurangan dokumen, atau informasi tambahan" class="w-full px-4 py-3 glass border-2 border-transparent rounded-xl text-sm txt-primary outline-none focus:border-indigo-500/50 transition-all"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex flex-col md:flex-row md:items-center md:justify-between gap-3 pt-5 border-t border-indigo-500/10">
                    <p class="text-[11px] txt-muted font-semibold">Kolom bertanda * wajib diisi. Dokumen bisa dilengkapi bertahap oleh admin.</p>
                    <div class="flex justify-end gap-3">
                        <x-ui.button wire:click="closeModal" variant="secondary" type="button">Batal</x-ui.button>
                        <x-ui.button variant="primary" type="submit" class="shadow-lg shadow-indigo-500/20">
                            {{ $editId ? 'Simpan Perubahan' : 'Simpan Pendaftaran' }}
                        </x-ui.button>
                    </div>
                </div>
            </form>
        </div>
    </x-ui.modal>

    <x-ui.modal name="gelombang-ppdb-form" :show="$isGelombangModalOpen" maxWidth="2xl">
        <div class="py-2">
            <h2 class="text-xl font-bold txt-primary mb-1">{{ $gelombangEditId ? 'Edit Gelombang PPDB' : 'Tambah Gelombang PPDB' }}</h2>
            <p class="text-sm txt-muted mb-6">Gelombang berstatus Dibuka nantinya bisa ditampilkan di landing page pendaftaran.</p>

            <form wire:submit.prevent="saveGelombang">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <x-ui.label for="gelombang_tahun_pendaftaran" value="Tahun Pendaftaran *" class="mb-2" />
                        <x-ui.input wire:model="gelombang_tahun_pendaftaran" id="gelombang_tahun_pendaftaran" type="number" min="2000" max="{{ now()->year + 5 }}" />
                        @error('gelombang_tahun_pendaftaran') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-ui.label for="gelombang_nama" value="Nama Gelombang *" class="mb-2" />
                        <x-ui.input wire:model="gelombang_nama" id="gelombang_nama" placeholder="Gelombang 1" />
                        @error('gelombang_nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-ui.label for="gelombang_tanggal_mulai" value="Tanggal Mulai" class="mb-2" />
                        <x-ui.input wire:model="gelombang_tanggal_mulai" id="gelombang_tanggal_mulai" type="date" />
                        @error('gelombang_tanggal_mulai') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-ui.label for="gelombang_tanggal_selesai" value="Tanggal Selesai" class="mb-2" />
                        <x-ui.input wire:model="gelombang_tanggal_selesai" id="gelombang_tanggal_selesai" type="date" />
                        @error('gelombang_tanggal_selesai') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <x-ui.select label="Status" wire:model="gelombang_status" :options="['Draft' => 'Draft', 'Dibuka' => 'Dibuka', 'Ditutup' => 'Ditutup']" />
                        @error('gelombang_status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <x-ui.label for="gelombang_deskripsi" value="Deskripsi / Catatan" class="mb-2" />
                        <textarea wire:model="gelombang_deskripsi" id="gelombang_deskripsi" rows="3" placeholder="Contoh: Pendaftaran jalur reguler tahap pertama" class="w-full px-4 py-3 glass border-2 border-transparent rounded-xl text-sm txt-primary outline-none focus:border-indigo-500/50 transition-all"></textarea>
                        @error('gelombang_deskripsi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-5 border-t border-indigo-500/10">
                    <x-ui.button wire:click="closeModal" variant="secondary" type="button">Batal</x-ui.button>
                    <x-ui.button variant="primary" type="submit">
                        {{ $gelombangEditId ? 'Simpan Perubahan' : 'Simpan Gelombang' }}
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>

    <x-ui.confirm-modal
        name="confirm-delete-pendaftaran-modal"
        title="Hapus Data Pendaftaran"
        message="Apakah Anda yakin ingin menghapus data pendaftaran ini? Berkas yang sudah diunggah juga akan dihapus."
        onConfirm="delete"
    />
</div>
