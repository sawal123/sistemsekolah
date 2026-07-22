<div class="flex flex-col gap-6 pb-20">
    <x-ui.toast />

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="txt-primary text-2xl font-extrabold tracking-tight">Master Jurusan SMK</h1>
            <p class="mt-1 text-sm txt-muted">Kelola kompetensi keahlian untuk kelas, siswa, PPDB, mata pelajaran, dan tarif SPP SMK.</p>
        </div>
        <x-ui.button wire:click="openModal" variant="primary">
            <span class="mr-2 text-lg">+</span> Tambah Jurusan
        </x-ui.button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['Total Jurusan', $stats['total'], 'primary'],
            ['Jurusan Aktif', $stats['aktif'], 'success'],
            ['Kelas SMK', $stats['kelas'], 'info'],
            ['Siswa SMK', $stats['siswa'], 'warning'],
        ] as [$label, $value, $variant])
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider txt-muted">{{ $label }}</p>
                <p class="mt-2 text-2xl font-extrabold txt-primary">{{ $value }}</p>
            </x-ui.card>
        @endforeach
    </div>

    <x-ui.card padding="16px">
        <div class="grid gap-3 md:grid-cols-[1fr_220px]">
            <x-ui.search model="search" placeholder="Cari kode atau nama jurusan..." />
            <x-ui.select wire:model.live="filterStatus" :options="[
                '' => 'Semua Status',
                'aktif' => 'Aktif',
                'nonaktif' => 'Nonaktif',
            ]" />
        </div>
    </x-ui.card>

    <x-ui.card padding="0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-indigo-500/10 bg-indigo-500/5">
                    <tr>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider txt-muted">Kode</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider txt-muted">Nama Jurusan</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider txt-muted text-center">Kelas</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider txt-muted text-center">Siswa</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider txt-muted text-center">Tarif</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider txt-muted text-center">Status</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider txt-muted text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-500/10">
                    @forelse($jurusans as $jurusan)
                        <tr class="transition-colors hover:bg-indigo-500/[0.03]">
                            <td class="px-5 py-4"><x-ui.badge variant="primary">{{ $jurusan->kode }}</x-ui.badge></td>
                            <td class="px-5 py-4">
                                <p class="font-semibold txt-primary">{{ $jurusan->nama }}</p>
                                @if($jurusan->deskripsi)
                                    <p class="mt-1 max-w-xl text-xs txt-muted">{{ Str::limit($jurusan->deskripsi, 100) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center txt-primary">{{ $jurusan->kelas_count }}</td>
                            <td class="px-5 py-4 text-center txt-primary">{{ $jurusan->siswas_count }}</td>
                            <td class="px-5 py-4 text-center txt-primary">{{ $jurusan->spps_count }}</td>
                            <td class="px-5 py-4 text-center">
                                <button wire:click="toggleActive({{ $jurusan->id }})" class="cursor-pointer">
                                    <x-ui.badge :variant="$jurusan->is_active ? 'success' : 'secondary'">
                                        {{ $jurusan->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </x-ui.badge>
                                </button>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button wire:click="edit({{ $jurusan->id }})" class="rounded-lg p-2 text-indigo-400 hover:bg-indigo-500/10" title="Edit">Edit</button>
                                <button wire:click="confirmDelete({{ $jurusan->id }})" class="rounded-lg p-2 text-red-400 hover:bg-red-500/10" title="Hapus">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center txt-muted">Belum ada master jurusan SMK.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-ui.pagination :links="$jurusans" />
    </x-ui.card>

    <x-ui.modal name="jurusan-form" :show="$isModalOpen" maxWidth="lg">
        <h2 class="text-xl font-bold txt-primary">{{ $editId ? 'Edit Jurusan' : 'Tambah Jurusan SMK' }}</h2>
        <p class="mb-6 mt-1 text-sm txt-muted">Gunakan kode singkat seperti RPL, TKJ, AKL, atau TKR.</p>

        <form wire:submit="save" class="space-y-5">
            <div class="grid gap-4 sm:grid-cols-[140px_1fr]">
                <div>
                    <x-ui.input label="Kode Jurusan" wire:model="kode" placeholder="RPL" />
                    @error('kode') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-ui.input label="Nama Jurusan" wire:model="nama" placeholder="Rekayasa Perangkat Lunak" />
                    @error('nama') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <x-ui.label value="Deskripsi" class="mb-2" />
                <textarea wire:model="deskripsi" rows="4" class="w-full rounded-xl border border-indigo-500/20 bg-white/5 px-4 py-3 text-sm txt-primary" placeholder="Deskripsi singkat kompetensi keahlian..."></textarea>
                @error('deskripsi') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-indigo-500/10 bg-indigo-500/[0.03] p-4">
                <input wire:model="is_active" type="checkbox" class="h-5 w-5 rounded border-indigo-500/30 text-indigo-600">
                <span>
                    <span class="block text-sm font-semibold txt-primary">Jurusan aktif</span>
                    <span class="block text-xs txt-muted">Jurusan aktif dapat dipilih pada kelas, siswa, PPDB, dan tarif.</span>
                </span>
            </label>

            <div class="flex justify-end gap-3 border-t border-indigo-500/10 pt-5">
                <x-ui.button wire:click="closeModal" type="button" variant="secondary">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary">Simpan Jurusan</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.confirm-modal
        name="confirm-delete-jurusan"
        title="Hapus Jurusan"
        message="Jurusan hanya dapat dihapus jika belum digunakan oleh kelas, siswa, atau tarif."
        onConfirm="delete"
    />
</div>
