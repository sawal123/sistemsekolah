<div style="display:flex;flex-direction:column;gap:24px;height:100%;">
    <x-ui.toast />
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="fu d1">
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Data Tahun Ajaran &
                Semester</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Kelola periode akademik aktif dan riwayat semester
                sekolah.</p>
        </div>

        <x-ui.button wire:click="openModal" variant="primary" class="shadow-lg">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Data
        </x-ui.button>
    </div>

    {{-- Main Table Section --}}
    <x-ui.card padding="0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left" style="border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr class="bg-indigo-500/5 dark:bg-white/5 border-b border-indigo-500/10 dark:border-white/10">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted w-[80px]">No</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Tahun Ajaran</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Semester</th>
                        <th
                            class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-center w-[150px]">
                            Status</th>
                        <th
                            class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-right w-[250px]">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                    @forelse($tahunAjarans as $index => $item)
                        <tr class="hover:bg-indigo-500/[0.02] dark:hover:bg-white/[0.02] transition-colors group">
                            <td class="px-6 py-4 text-sm txt-primary font-medium">
                                {{ ($tahunAjarans->currentPage() - 1) * $tahunAjarans->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 text-sm txt-primary font-semibold">
                                {{ $item->tahun }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="txt-primary">{{ $item->semester }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'Draft' => 'secondary',
                                        'Aktif' => 'success',
                                        'Ditutup' => 'warning',
                                        'Diarsipkan' => 'secondary',
                                    ];
                                @endphp
                                <x-ui.badge variant="{{ $statusColors[$item->status] ?? 'secondary' }}">
                                    {{ $item->status }}
                                </x-ui.badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($item->status !== 'Aktif')
                                        <button wire:click="toggleStatus({{ $item->id }})"
                                            class="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-all cursor-pointer"
                                            title="Set Aktif">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        {{-- Kenaikan Kelas: untuk tahun ajaran berbeda tahun --}}
                                        @php
                                            $taAktif = $tahunAjarans->firstWhere('is_active', true);
                                            $tahunIni = (int) explode('/', $item->tahun)[0];
                                            $tahunAktif = $taAktif ? (int) explode('/', $taAktif->tahun)[0] : null;
                                            $bedaTahun = $tahunAktif === null || $tahunIni !== $tahunAktif;
                                        @endphp
                                        @if ($bedaTahun && $item->status === 'Draft')
                                            <button wire:click="previewKenaikanKelas({{ $item->id }})"
                                                class="p-2 rounded-lg hover:bg-purple-500/10 text-purple-500 transition-all cursor-pointer"
                                                title="Kenaikan Kelas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endif

                                    <button wire:click="edit({{ $item->id }})"
                                        class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500 transition-all cursor-pointer"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>

                                    @if ($item->status === 'Draft')
                                        <button wire:click="confirmDelete({{ $item->id }})"
                                            class="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-all cursor-pointer"
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center opacity-40">
                                    <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-sm">Belum ada data tahun ajaran.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-ui.pagination :links="$tahunAjarans" />
    </x-ui.card>

    {{-- Form Modal --}}
    <x-ui.modal name="tahun-ajaran-form" :show="$isModalOpen" maxWidth="lg">
        <div class="py-2">
            <h2 class="text-xl font-bold txt-primary mb-1">
                {{ $editId ? 'Edit Data Tahun Ajaran' : 'Tambah Tahun Ajaran' }}
            </h2>
            <p class="text-sm txt-muted mb-6">Lengkapi informasi periode akademik di bawah ini.</p>

            <form wire:submit.prevent="save">
                <div class="mb-6">
                    <x-ui.label for="tahun" value="Tahun Ajaran" class="mb-2 txt-secondary" />
                    <x-ui.input wire:model="tahun" id="tahun" type="text" placeholder="Contoh: 2025/2026"
                        class="w-full" />
                </div>

                <div class="mb-6">
                    <x-ui.select label="Semester" wire:model="semester" :options="['Ganjil', 'Genap']"
                        placeholder="Pilih Semester" />
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <x-ui.label for="tanggal_mulai" value="Tanggal Mulai" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="tanggal_mulai" id="tanggal_mulai" type="date" class="w-full" />
                    </div>
                    <div>
                        <x-ui.label for="tanggal_selesai" value="Tanggal Selesai" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="tanggal_selesai" id="tanggal_selesai" type="date"
                            class="w-full" />
                    </div>
                </div>

                <div class="mb-6">
                    @php
                        $statusOptions = $editId && $status === 'Aktif'
                            ? ['Aktif', 'Ditutup', 'Diarsipkan']
                            : ['Draft', 'Ditutup', 'Diarsipkan'];
                    @endphp
                    <x-ui.select label="Status" wire:model="status" :options="$statusOptions" placeholder="Pilih Status" />
                </div>

                <div
                    class="flex items-center gap-3 bg-indigo-500/[0.03] dark:bg-indigo-500/5 p-4 rounded-xl border border-indigo-500/10 mb-8">
                    <p class="text-xs txt-muted">Pilih <strong>Aktif</strong> pada dropdown Status di atas untuk
                        menjadikan periode ini sebagai semester aktif. Periode lain akan otomatis dinonaktifkan.</p>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-indigo-500/10">
                    <x-ui.button wire:click="closeModal" variant="secondary" type="button">
                        Batal
                    </x-ui.button>
                    <x-ui.button variant="primary" type="submit">
                        {{ $editId ? 'Simpan Perubahan' : 'Tambah Data' }}
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>

    {{-- Confirm Delete Modal --}}
    <x-ui.confirm-modal name="confirm-delete-modal" title="Hapus Tahun Ajaran"
        message="Apakah Anda yakin ingin menghapus tahun ajaran ini? Hanya tahun ajaran dengan status 'Draft' yang dapat dihapus. Data yang sudah memiliki transaksi nilai, rapor, atau SPP tidak akan terpengaruh karena sudah dicegah oleh sistem."
        onConfirm="delete" />

    {{-- Cannot Delete Modal --}}
    <x-ui.modal name="cannot-delete-modal" maxWidth="md">
        <div class="py-2">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold txt-primary">Tidak Dapat Dihapus</h3>
            </div>
            <p class="text-sm txt-muted mb-6">{{ $deleteErrorMessage }}</p>
            <div class="flex justify-end">
                <x-ui.button wire:click="closeModal" variant="secondary">
                    Mengerti
                </x-ui.button>
            </div>
        </div>
    </x-ui.modal>

    {{-- Salin Rombel Modal --}}
    <x-ui.modal name="salin-rombel-modal" maxWidth="md">
        <div class="py-2">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold txt-primary">Salin Rombel</h3>
            </div>
            <p class="text-sm txt-muted mb-6">{{ $salinRombelMessage }}</p>
            <div class="flex justify-end gap-3">
                <x-ui.button wire:click="tolakSalinRombel" variant="secondary">
                    Nanti Saja
                </x-ui.button>
                <x-ui.button wire:click="salinRombelDariSemesterSebelumnya" variant="primary">
                    Ya, Salin Rombel
                </x-ui.button>
            </div>
        </div>
    </x-ui.modal>

    {{-- Kenaikan Kelas Modal --}}
    <x-ui.modal name="kenaikan-kelas-modal" maxWidth="2xl">
        <div class="py-2">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-purple-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold txt-primary">Pratinjau Kenaikan Kelas</h3>
                    <p class="text-xs txt-muted">Siswa akan dipindahkan ke kelas setingkat di atasnya. Kelas akhir akan
                        berstatus Lulus.</p>
                </div>
            </div>

            @if (!empty($kenaikanPreview))
                <div class="overflow-x-auto max-h-96 mb-4">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-indigo-500/5">
                            <tr>
                                <th class="px-3 py-2 text-xs font-bold txt-muted">Kelas Asal</th>
                                <th class="px-3 py-2 text-xs font-bold txt-muted text-center">→</th>
                                <th class="px-3 py-2 text-xs font-bold txt-muted">Kelas Tujuan</th>
                                <th class="px-3 py-2 text-xs font-bold txt-muted text-center">Siswa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-500/10">
                            @foreach ($kenaikanPreview as $row)
                                <tr @class(['opacity-50' => $row['is_lulus']])>
                                    <td class="px-3 py-2 font-semibold txt-primary">{{ $row['kelas_asal'] }}</td>
                                    <td class="px-3 py-2 text-center txt-muted">→</td>
                                    <td class="px-3 py-2 font-semibold txt-primary">
                                        {{ $row['is_lulus'] ? '🎓 Lulus' : $row['kelas_tujuan'] }}
                                    </td>
                                    <td class="px-3 py-2 text-center txt-primary font-bold">{{ $row['jumlah_siswa'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm txt-muted text-center py-8">Tidak ada data rombel untuk dipratinjau.</p>
            @endif

            <div class="flex justify-end gap-3 border-t border-indigo-500/10 pt-4">
                <x-ui.button wire:click="batalKenaikanKelas" variant="secondary">
                    Batal
                </x-ui.button>
                <x-ui.button wire:click="executeKenaikanKelas" variant="primary"
                    {{ empty($kenaikanPreview) ? 'disabled' : '' }}>
                    Jalankan Kenaikan Kelas
                </x-ui.button>
            </div>
        </div>
    </x-ui.modal>
</div>
