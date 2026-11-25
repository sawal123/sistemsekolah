<div class="flex flex-col gap-6 pb-16">
    <x-ui.toast />

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="txt-primary text-2xl font-extrabold tracking-tight">Master Unit Sekolah</h1>
            <p class="txt-muted text-sm mt-1">Kelola tingkatan sekolah (SD, SMP, SMA, SMK) beserta identitas cetak rapor & kuitansi masing-masing unit.</p>
        </div>

        <x-ui.button wire:click="openModal" variant="primary" class="shadow-lg">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Unit Sekolah
        </x-ui.button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @forelse($units as $unit)
            <x-ui.card class="relative flex flex-col justify-between gap-4 border {{ $unit->is_active ? 'border-indigo-500/15' : 'border-slate-300/30 opacity-75' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        @if($unit->logo)
                            <img src="{{ Storage::url($unit->logo) }}" alt="{{ $unit->nama }}" class="w-11 h-11 rounded-xl object-contain bg-white p-1 border border-indigo-500/15" />
                        @else
                            <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-500 font-extrabold text-sm flex items-center justify-center">
                                {{ $unit->kode }}
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-base font-extrabold txt-primary">{{ $unit->kode }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $unit->is_active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-500/10 text-slate-400' }}">
                                    {{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <p class="text-xs txt-muted line-clamp-1">{{ $unit->nama }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center py-2.5 px-2 rounded-xl bg-indigo-500/5 border border-indigo-500/10">
                    <div>
                        <p class="text-[10px] txt-muted font-semibold uppercase">Siswa</p>
                        <p class="text-sm font-bold txt-primary">{{ $unit->siswas_count }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] txt-muted font-semibold uppercase">Guru</p>
                        <p class="text-sm font-bold txt-primary">{{ $unit->gurus_count }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] txt-muted font-semibold uppercase">Kelas</p>
                        <p class="text-sm font-bold txt-primary">{{ $unit->kelas_count }}</p>
                    </div>
                </div>

                <div class="text-xs space-y-1 txt-muted">
                    <p><span class="font-semibold txt-primary">Tingkat:</span> Kelas {{ $unit->tingkat_awal }} – {{ $unit->tingkat_akhir }}</p>
                    <p><span class="font-semibold txt-primary">NPSN:</span> {{ $unit->npsn ?: 'Belum diisi' }}</p>
                    <p class="truncate"><span class="font-semibold txt-primary">Kepsek:</span> {{ $unit->kepala_sekolah ?: 'Belum diisi' }}</p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-indigo-500/10">
                    <button wire:click="toggleActive({{ $unit->id }})" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $unit->is_active ? 'bg-amber-500/10 text-amber-600 hover:bg-amber-500/20' : 'bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20' }} transition cursor-pointer">
                        {{ $unit->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                    <button wire:click="edit({{ $unit->id }})" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-500/10 text-indigo-500 hover:bg-indigo-500/20 transition cursor-pointer">
                        Edit Identitas
                    </button>
                </div>
            </x-ui.card>
        @empty
            <div class="col-span-full text-center py-12 txt-muted text-sm">Belum ada unit sekolah.</div>
        @endforelse
    </div>

    <x-ui.modal name="unit-form" :show="$isModalOpen" maxWidth="2xl" title="{{ $editId ? 'Edit Unit Sekolah' : 'Tambah Unit Sekolah' }}">
        <form wire:submit.prevent="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-ui.label for="unit-kode" value="Kode Unit (misal: SD, SMP)" />
                    <x-ui.input wire:model="kode" id="unit-kode" placeholder="SD" class="mt-1 w-full uppercase" />
                    @error('kode') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <x-ui.label for="unit-nama" value="Nama Resmi Unit Sekolah" />
                    <x-ui.input wire:model="nama" id="unit-nama" placeholder="SD Nusantara" class="mt-1 w-full" />
                    @error('nama') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <x-ui.label for="unit-npsn" value="NPSN" />
                    <x-ui.input wire:model="npsn" id="unit-npsn" placeholder="2020xxxx" class="mt-1 w-full" />
                </div>
                <div>
                    <x-ui.label for="unit-awal" value="Tingkat Awal" />
                    <x-ui.input wire:model="tingkat_awal" id="unit-awal" type="number" min="1" max="12" class="mt-1 w-full" />
                    @error('tingkat_awal') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-ui.label for="unit-akhir" value="Tingkat Akhir (Lulus)" />
                    <x-ui.input wire:model="tingkat_akhir" id="unit-akhir" type="number" min="1" max="13" class="mt-1 w-full" />
                    @error('tingkat_akhir') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-ui.label for="unit-urutan" value="Urutan Tampil" />
                    <x-ui.input wire:model="urutan" id="unit-urutan" type="number" min="0" class="mt-1 w-full" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-ui.label for="unit-kepsek" value="Nama Kepala Sekolah (Kop Rapor)" />
                    <x-ui.input wire:model="kepala_sekolah" id="unit-kepsek" placeholder="Drs. H. Ahmad, M.Pd" class="mt-1 w-full" />
                </div>
                <div>
                    <x-ui.label for="unit-nip-kepsek" value="NIP Kepala Sekolah" />
                    <x-ui.input wire:model="nip_kepala_sekolah" id="unit-nip-kepsek" placeholder="19700101..." class="mt-1 w-full" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-ui.label for="unit-telp" value="Telepon Unit" />
                    <x-ui.input wire:model="telepon" id="unit-telp" placeholder="(021) xxxxxxx" class="mt-1 w-full" />
                </div>
                <div>
                    <x-ui.label for="unit-email" value="Email Unit" />
                    <x-ui.input wire:model="email" id="unit-email" type="email" placeholder="sd@sekolah.sch.id" class="mt-1 w-full" />
                </div>
            </div>

            <div>
                <x-ui.label for="unit-alamat" value="Alamat Unit (Kop Surat / Kuitansi)" />
                <textarea wire:model="alamat" id="unit-alamat" rows="2" class="mt-1 w-full rounded-xl border border-indigo-500/20 bg-white/60 dark:bg-white/5 px-3 py-2 text-sm txt-primary outline-none focus:border-indigo-500"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                <div>
                    <x-ui.label for="unit-logo" value="Logo Unit (Opsional, Max 2MB)" />
                    <input type="file" wire:model="logo" id="unit-logo" class="mt-1 text-xs txt-muted" />
                    @error('logo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center gap-6 pt-3">
                    <label class="inline-flex items-center gap-2 text-xs font-semibold txt-primary cursor-pointer">
                        <input type="checkbox" wire:model="punya_jurusan" class="rounded border-indigo-500/30 text-indigo-600" />
                        <span>Memiliki Jurusan (SMK)</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-xs font-semibold txt-primary cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="rounded border-indigo-500/30 text-indigo-600" />
                        <span>Unit Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-indigo-500/10">
                <x-ui.button type="button" variant="secondary" wire:click="closeModal">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary">Simpan Unit</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
