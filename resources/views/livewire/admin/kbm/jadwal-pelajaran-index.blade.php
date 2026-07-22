<div class="flex flex-col gap-6 h-full p-4 md:p-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 fu d1">
        <div>
            <h1 class="txt-primary text-2xl font-extrabold flex items-center gap-2">
                <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Jadwal Pelajaran
            </h1>
            <p class="txt-muted text-sm mt-1">Sistem Pendeteksi Bentrok (Smart Scheduling) untuk Kelas & Guru.</p>
        </div>

        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('operator'))
            <x-ui.button wire:click="openModalForCreate" class="shadow-indigo-500/30">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Jadwal Baru
            </x-ui.button>
        @endif
    </div>

    {{-- Notifikasi sekarang dialihkan ke sistem Toast (diurus oleh admin layout + Livewire Dispatch) --}}

    {{-- Filter Panel --}}
    <x-ui.card class="fu d2 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <x-ui.label value="Kaca Pembesar Kelas" class="text-xs" />
                @php
                    $optKelas = ['' => 'Menampilkan Semua Kelas'];
                    foreach ($dataKelas as $k) {
                        $optKelas[$k->id] = $k->nama_kelas;
                    }
                @endphp
                <x-ui.select wire:model.live="filterKelas" :options="$optKelas"
                    :disabled="auth()->user()->hasRole('siswa')" />
            </div>
            <div>
                <x-ui.label value="Lacak Guru" class="text-xs" />
                @php
                    $optGuru = ['' => 'Semua Guru'];
                    foreach ($dataGuru as $g) {
                        $optGuru[$g->id] = $g->user->name;
                    }
                @endphp
                <x-ui.select wire:model.live="filterGuru" :options="$optGuru"
                    :disabled="auth()->user()->hasRole('guru')" />
            </div>
            <div>
                <x-ui.label value="Pemakaian Ruangan" class="text-xs" />
                @php
                    $optR = ['' => 'Semua Ruangan'];
                    foreach ($dataRuangan as $r) {
                        $optR[$r->id] = $r->nama_ruangan;
                    }
                @endphp
                <x-ui.select wire:model.live="filterRuangan" :options="$optR" />
            </div>
            <div class="flex justify-end h-full mt-2 md:mt-0">
                <div class="glass flex items-center p-1 rounded-xl">
                    <button wire:click="$set('activeTab', 'grid')"
                        class="cursor-pointer px-4 py-2 text-xs font-semibold rounded-lg transition-all {{ $activeTab == 'grid' ? 'bg-indigo-500/20 text-indigo-500' : 'text-gray-400 hover:text-indigo-400' }}">Grid
                        Mingguan</button>
                    <button wire:click="$set('activeTab', 'list')"
                        class="cursor-pointer px-4 py-2 text-xs font-semibold rounded-lg transition-all {{ $activeTab == 'list' ? 'bg-indigo-500/20 text-indigo-500' : 'text-gray-400 hover:text-indigo-400' }}">List
                        Data</button>
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- Smart Matrix Display (2 Baris = 3 Kolom per Baris) --}}
    @if($activeTab == 'grid')
        <div class="fu d3 pb-8 relative z-10 w-full">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                @foreach($hariMinggu as $hari)
                    <div class="flex flex-col min-h-[500px]">
                        {{-- Header Kolom --}}
                        <div
                            class="bg-gradient-to-r from-indigo-500/10 to-indigo-500/5 border border-indigo-500/20 backdrop-blur-md rounded-t-xl py-3 px-4 shadow-sm">
                            <h3
                                class="text-center font-bold text-indigo-600 dark:text-indigo-400 tracking-wider uppercase text-sm">
                                {{ $hari }}
                            </h3>
                        </div>

                        {{-- Isi Card Kolom --}}
                        <div class="flex-1 glass-card border-t-0 rounded-t-none pb-2 h-full flex flex-col items-center">
                            <div class="max-h-[500px] overflow-y-auto custom-scrollbar p-3 space-y-3 w-full h-full flex flex-col items-center">
                            @php
                                $jadwalsHariIni = $matrix[$hari];
                                $colors = ['emerald', 'blue', 'orange', 'rose', 'cyan', 'fuchsia'];
                            @endphp

                            @forelse($jadwalsHariIni as $jdwl)
                                @php
                                    $c = $colors[$jdwl->mapel_id % count($colors)];
                                @endphp
                                <div
                                    class="w-full relative group p-3 rounded-xl border border-{{$c}}-500/20 bg-{{$c}}-500/5 hover:bg-{{$c}}-500/10 transition-colors">
                                    <div class="flex justify-between items-start mb-1">
                                        <span
                                            class="text-[11px] font-bold py-0.5 px-2 bg-{{$c}}-500/20 txt-secondary dark:text-{{$c}}-400 rounded-full">
                                            {{ \Carbon\Carbon::parse($jdwl->jam_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($jdwl->jam_selesai)->format('H:i') }}
                                        </span>
                                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('operator'))
                                            <button wire:click="confirmHapus({{ $jdwl->id }})"
                                                class="cursor-pointer text-gray-400 hover:text-red-500 transition-colors p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                    <h4 class="font-bold txt-primary text-sm mt-2 leading-tight">{{ $jdwl->mapel->nama_mapel }}</h4>

                                    <div class="mt-2 text-xs txt-secondary space-y-1">
                                        <div class="flex items-center gap-1.5 line-clamp-1">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ $jdwl->guru->user->name }}
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            {{ $jdwl->kelas->nama_kelas }}
                                            @if($jdwl->ruangan)
                                                <span class="text-amber-500 truncate ml-1">({{ $jdwl->ruangan->nama_ruangan }})</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="w-full h-full flex flex-col items-center justify-center opacity-30">
                                    <svg class="w-8 h-8 mb-2 txt-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-xs txt-primary font-medium uppercase tracking-widest">Kosong</span>
                                </div>
                            @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- List Mode Table --}}
        <x-ui.card>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/5 txt-muted text-xs uppercase tracking-wider">
                            <th class="p-3">Hari & Slot Waktu</th>
                            <th class="p-3">Mata Pelajaran</th>
                            <th class="p-3">Guru & Ruangan</th>
                            <th class="p-3 text-right">#</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5 txt-primary text-sm">
                        @forelse($listJadwals as $j)
                            <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                                <td class="p-3">
                                    <span class="font-bold">{{ $j->hari }}</span> <br>
                                    <span
                                        class="text-xs text-indigo-500">{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}
                                        - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}</span>
                                </td>
                                <td class="p-3 font-semibold">{{ $j->mapel->nama_mapel }} <br> <span
                                        class="text-xs font-normal opacity-70">Kelas: {{ $j->kelas->nama_kelas }}</span></td>
                                <td class="p-3">{{ $j->guru->user->name }} <br> <span
                                        class="text-xs text-amber-500">{{ $j->ruangan->nama_ruangan ?? 'Belum ada ruangan' }}</span>
                                </td>
                                <td class="p-3 text-right">
                                    @if(auth()->user()->hasRole('admin'))
                                        <button wire:click="confirmHapus({{ $j->id }})"
                                            class="text-red-500 hover:text-red-700 p-1"><svg class="w-4 h-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg></button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-5 text-center txt-muted italic text-sm">Tidak ada jadwal ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    @endif

    {{-- Form Smart Modal --}}
    <x-ui.modal name="jadwal-modal" maxWidth="xl">
        <div class="px-6 py-4">
            <h3 class="text-xl font-bold txt-primary flex items-center gap-2 mb-6">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Penjadwalan Cerdas
            </h3>

            @if(count($conflictErrors) > 0)
                <div class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-600">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h4 class="font-bold text-sm">Sistem Menolak! Bentrok Terdeteksi:</h4>
                            <ul class="list-disc ml-5 mt-1 text-xs space-y-1">
                                @foreach($conflictErrors as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form wire:submit="simpanJadwal" class="space-y-4">
                @php
                    $optK = ['' => 'Pilih Kelas'];
                    foreach ($dataKelas as $k) { $optK[$k->id] = $k->nama_kelas . ' (Jenjang: ' . $k->jenjang . ')'; }
                    
                    $optM = ['' => 'Pilih Mata Pelajaran'];
                    foreach ($dataMapel as $m) { $optM[$m->id] = $m->nama_mapel . ' - ' . $m->kelompok; }
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Row 1: Target Kelas (Input Utama) --}}
                    <div class="col-span-full">
                        <x-ui.label value="Target Kelas (Pilih Pertama)" required />
                        <x-ui.select wire:model.live="kelas_id" :options="$optK" />
                        @error('kelas_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Row 2: Mapel --}}
                    <div class="col-span-full">
                        <x-ui.label value="Mata Pelajaran" required />
                        <x-ui.select wire:model="mapel_id" :options="$optM" />
                        @error('mapel_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Row 3 --}}
                    <div>
                        <x-ui.label value="Hari Aktif" required />
                        <x-ui.select wire:model.live="hari" :options="['' => 'Pilih Hari', 'Senin' => 'Senin', 'Selasa' => 'Selasa', 'Rabu' => 'Rabu', 'Kamis' => 'Kamis', 'Jumat' => 'Jumat', 'Sabtu' => 'Sabtu']" />
                        @error('hari') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-ui.label value="Saran Jam Pelajaran (Otomatis)" />
                        <x-ui.select wire:model.live="jamKe" :options="$optSaranJam" />
                        <span class="text-[10px] txt-secondary italic mt-0.5 block">*Hanya menampilkan jam tersedia.</span>
                        @error('jamKe') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Row 4 (Waktu Custom) --}}
                    <div>
                        <x-ui.label value="Waktu Mulai" required />
                        <x-ui.input wire:model.live.debounce.400ms="jam_mulai" type="time" />
                        @error('jam_mulai') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-ui.label value="Waktu Selesai" required />
                        <x-ui.input wire:model.live.debounce.400ms="jam_selesai" type="time" />
                        @error('jam_selesai') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Row 5 (Dinamis dari Livewire) --}}
                    <div>
                        <x-ui.label value="Staff Pengajar" required />
                        <x-ui.select wire:model="guru_id" :options="$optGuruModal" />
                        <span class="text-[10px] text-emerald-500 italic mt-0.5 block">Hanya menampilkan guru kosong (tak bentrok).</span>
                        @error('guru_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-ui.label value="Lokasi Ruangan (Opsi)" />
                        <x-ui.select wire:model="ruangan_id" :options="$optRuanganModal" />
                        <span class="text-[10px] text-emerald-500 italic mt-0.5 block">Hanya ruangan kosong yang tampil.</span>
                        @error('ruangan_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                </div>

                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-white/10">
                    <x-ui.button type="button" variant="secondary" wire:click="closeModal">Batalkan</x-ui.button>
                    <x-ui.button type="submit" variant="primary">Validasi & Simpan</x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>

    {{-- Delete Modal --}}
    <x-ui.modal name="delete-modal" maxWidth="sm">
        <div class="text-center pb-2">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-500/20 mb-4">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-bold txt-primary mb-2">Hapus Jadwal?</h3>
            <p class="text-xs txt-muted mb-6">
                Tindakan ini akan menghapus jadwal secara permanen dari sistem. Lanjutkan?
            </p>
            <div class="flex justify-center gap-3">
                <x-ui.button variant="secondary" x-on:click="$dispatch('close-modal', 'delete-modal')">Batal</x-ui.button>
                <x-ui.button variant="danger" wire:click="executeHapus">Ya, Hapus</x-ui.button>
            </div>
        </div>
    </x-ui.modal>

</div>