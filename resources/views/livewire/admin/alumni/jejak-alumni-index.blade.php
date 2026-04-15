<div class="space-y-6" x-data="{ 
    initCharts() {
        const stats = @js($stats['sebaran']);
        const ctx = document.getElementById('sebaranChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(stats),
                datasets: [{
                    data: Object.values(stats),
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(107, 114, 128, 0.8)'
                    ],
                    borderColor: 'transparent',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 11 } } }
                },
                cutout: '70%'
            }
        });
    }
}" x-init="setTimeout(() => initCharts(), 100)">

    <x-ui.toast />

    {{-- ── Header ────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold txt-primary tracking-tight">Tracer Study Alumni</h1>
            <p class="text-sm txt-muted mt-1">Pantau keterserapan lulusan untuk akreditasi dan promosi sekolah.</p>
        </div>
        <div class="flex items-center gap-3">
            <x-ui.button variant="secondary" wire:click="$dispatch('export-alumni')" class="shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Ekspor Excel
            </x-ui.button>
            <x-ui.button variant="primary" wire:click="openModal" class="shadow-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Data Jejak
            </x-ui.button>
        </div>
    </div>

    {{-- ── Dashboard Stats ───────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Chart Sebaran --}}
        <x-ui.card class="lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold uppercase tracking-wider txt-muted">Sebaran Kegiatan</h3>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400">PIE CHART</span>
            </div>
            <div class="relative h-60">
                <canvas id="sebaranChart"></canvas>
            </div>
        </x-ui.card>

        {{-- Top Instansi --}}
        <x-ui.card class="lg:col-span-1">
            <h3 class="text-sm font-bold uppercase tracking-wider txt-muted mb-4">Top Instansi / Kampus</h3>
            <div class="space-y-4">
                @forelse($stats['top_instansi'] as $instansi)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-xs font-bold">
                                {{ $loop->iteration }}
                            </div>
                            <span class="text-sm txt-primary font-medium">{{ $instansi->nama_instansi }}</span>
                        </div>
                        <span class="text-xs font-bold txt-muted">{{ $instansi->total }} Alumni</span>
                    </div>
                @empty
                    <div class="py-10 text-center opacity-40">
                        <p class="text-xs txt-muted italic">Data instansi belum tersedia</p>
                    </div>
                @endforelse
            </div>
        </x-ui.card>

        {{-- Summary Cards --}}
        <div class="lg:col-span-1 space-y-6">
            <x-ui.card class="bg-indigo-600/5 border-indigo-500/20">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-indigo-400 tracking-wider">Total Lulusan Terlacak</p>
                        <p class="text-3xl font-black txt-primary">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="bg-amber-600/5 border-amber-500/20">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-amber-500 tracking-wider">Menunggu Verifikasi</p>
                        <p class="text-3xl font-black txt-primary">{{ $stats['verifikasi_pending'] }}</p>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- ── Filters & List ────────────────────────────────── --}}
    <x-ui.card padding="0" class="overflow-hidden">
        <div class="p-4 border-b border-indigo-500/10 flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari alumni atau instansi..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm bg-white/5 border border-indigo-500/20 txt-primary focus:outline-none focus:ring-2 focus:ring-indigo-500/30" />
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 txt-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <div class="flex gap-2">
                @php
                    $jenisOptions = [
                        '' => 'Semua Kegiatan',
                        'Kuliah' => 'Kuliah',
                        'Kerja' => 'Kerja',
                        'Wirausaha' => 'Wirausaha',
                        'Mencari_Kerja' => 'Mencari Kerja'
                    ];
                @endphp
                <div class="w-48">
                    <x-ui.select wire:model.live="filterJenis" :options="$jenisOptions" />
                </div>

                @php
                    $tahunOptions = ['' => 'Semua Angkatan'];
                    foreach($availableYears as $year) {
                        $tahunOptions[$year] = "Lulusan $year";
                    }
                @endphp
                <div class="w-48">
                    <x-ui.select wire:model.live="filterTahun" :options="$tahunOptions" />
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-indigo-500/5 border-b border-indigo-500/10">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider txt-muted">Alumni</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider txt-muted">Kegiatan</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider txt-muted">Instansi / Kampus</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider txt-muted">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-wider txt-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-500/5">
                    @forelse($kegiatans as $item)
                        <tr class="hover:bg-indigo-500/[0.02] transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs uppercase">
                                        {{ substr($item->siswa->user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold txt-primary">{{ $item->siswa->user->name }}</p>
                                        <p class="text-[10px] txt-muted">Lulus {{ $item->siswa->tahun_lulus }} — {{ $item->siswa->kelas->nama_kelas ?? 'Tanpa Kelas' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm txt-primary font-medium">
                                <span @class([
                                    'inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-tight border',
                                    'bg-blue-500/10 text-blue-400 border-blue-500/20' => $item->jenis_kegiatan === 'Kuliah',
                                    'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' => $item->jenis_kegiatan === 'Kerja',
                                    'bg-amber-500/10 text-amber-400 border-amber-500/20' => $item->jenis_kegiatan === 'Wirausaha',
                                    'bg-rose-500/10 text-rose-400 border-rose-500/20' => $item->jenis_kegiatan === 'Mencari_Kerja',
                                    'bg-slate-500/10 text-slate-400 border-slate-500/20' => $item->jenis_kegiatan === 'Lainnya',
                                ])>
                                    {{ str_replace('_', ' ', $item->jenis_kegiatan) }}
                                </span>
                                <p class="text-[10px] txt-muted mt-0.5">{{ $item->posisi_jurusan }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm txt-primary font-semibold">{{ $item->nama_instansi ?: '-' }}</p>
                                <p class="text-[10px] txt-muted mt-0.5">Mulai: {{ $item->tahun_mulai }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->status_verifikasi === 'Verified')
                                    <span class="inline-flex items-center gap-1.5 text-xs text-emerald-500 font-bold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        Terverifikasi
                                    </span>
                                @elseif($item->status_verifikasi === 'Pending')
                                    <span class="inline-flex items-center gap-1.5 text-xs text-amber-500 font-bold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                        Menunggu
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs text-rose-500 font-bold">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="edit({{ $item->id }})" class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-400 transition-all cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.5 2.5 0 113.536 3.536L12 20.122l-4 1 1-4L19.586 3z"/></svg>
                                    </button>
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus data jejak alumni ini?" class="p-2 rounded-lg hover:bg-rose-500/10 text-rose-400 transition-all cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center opacity-30">
                                    <svg class="w-16 h-16 mb-4 txt-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm font-bold tracking-wide">Belum Ada Data Jejak Alumni</p>
                                    <p class="text-[10px] mt-1">Cari alumni di formulir "Tambah Data" untuk melengkapi modul ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kegiatans->hasPages())
            <div class="px-6 py-4 border-t border-indigo-500/10">
                {{ $kegiatans->links() }}
            </div>
        @endif
    </x-ui.card>

    {{-- ── Form Modal ────────────────────────────────────── --}}
    <x-ui.modal name="alumni-form" :show="$isModalOpen" maxWidth="xl">
        <form wire:submit.prevent="save" class="p-1">
            <h2 class="text-xl font-extrabold txt-primary mb-1">
                {{ $editId ? 'Edit Jejak Alumni' : 'Tambah Jejak Alumni' }}
            </h2>
            <p class="text-xs txt-muted mb-6">Lengkapi informasi kegiatan alumni untuk database sekolah.</p>

            <div class="space-y-4">
                {{-- Pilih Alumni --}}
                @if(!$editId)
                <div>
                    <x-ui.label value="Pilih Alumni (Status Lulus)" class="mb-2" />
                    @php
                        $alumniMap = ['' => '-- Pilih Alumni --'];
                        foreach($alumniOptions as $alumni) {
                            $alumniMap[$alumni->id] = $alumni->user->name . " (Lulus " . $alumni->tahun_lulus . ")";
                        }
                    @endphp
                    <x-ui.select wire:model="siswa_id" :options="$alumniMap" />
                    @error('siswa_id') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Jenis Kegiatan --}}
                    <div>
                        <x-ui.label value="Kegiatan Saat Ini" class="mb-2" />
                        @php
                            $actOptions = [
                                'Kuliah' => 'Kuliah',
                                'Kerja' => 'Kerja',
                                'Wirausaha' => 'Wirausaha',
                                'Mencari_Kerja' => 'Mencari Kerja',
                                'Lainnya' => 'Lainnya'
                            ];
                        @endphp
                        <x-ui.select wire:model="jenis_kegiatan" :options="$actOptions" />
                    </div>

                    {{-- Tahun Mulai --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider txt-muted mb-2">Tahun Mulai</label>
                        <input wire:model="tahun_mulai" type="number" placeholder="Contoh: 2025"
                            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white/5 border border-indigo-500/20 txt-primary focus:outline-none focus:ring-2 focus:ring-indigo-500/30" />
                        @error('tahun_mulai') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Nama Instansi --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider txt-muted mb-2">Nama Instansi / Kampus</label>
                        <input wire:model="nama_instansi" type="text" placeholder="Contoh: Universitas Gadjah Mada"
                            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white/5 border border-indigo-500/20 txt-primary focus:outline-none focus:ring-2 focus:ring-indigo-500/30" />
                    </div>

                    {{-- Posisi / Jurusan --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider txt-muted mb-2">Posisi / Jurusan</label>
                        <input wire:model="posisi_jurusan" type="text" placeholder="Contoh: Teknik Informatika"
                            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white/5 border border-indigo-500/20 txt-primary focus:outline-none focus:ring-2 focus:ring-indigo-500/30" />
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider txt-muted mb-2">Deskripsi Tambahan</label>
                    <textarea wire:model="deskripsi" rows="3" placeholder="Keterangan tambahan mengenai kegiatan..."
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-white/5 border border-indigo-500/20 txt-primary focus:outline-none focus:ring-2 focus:ring-indigo-500/30 resize-none"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-5 border-t border-indigo-500/10">
                <x-ui.button type="button" variant="secondary" wire:click="closeModal">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" class="px-8 shadow-lg shadow-indigo-500/20">Simpan Jejak</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    {{-- Script untuk Chart.js (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</div>
