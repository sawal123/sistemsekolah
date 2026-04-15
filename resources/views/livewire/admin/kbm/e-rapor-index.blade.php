<div>
    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-6">
        <div>
            <h1 class="txt-primary text-[22px] font-extrabold flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Manajemen e-Rapor
            </h1>
            <p class="txt-muted text-[13px] mt-1">Sistem rekapitulasi data akademik & non-akademik akhir semester.</p>
        </div>
    </div>

    <x-ui.card class="mb-6 relative z-50 border border-indigo-500/10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-ui.label value="Tahun Ajaran Aktif" />
                @php
                    $optTA = ['' => 'Pilih Tahun Ajaran'];
                    foreach($listTahunAjaran as $ta) { $optTA[$ta->id] = $ta->tahun . ' - ' . ucfirst($ta->semester); }
                @endphp
                <x-ui.select wire:model.live="filterTahunAjaran" :options="$optTA" />
            </div>
            <div>
                <x-ui.label value="Kelas Perwalian" />
                @php
                    $optK = ['' => 'Pilih Opsi'];
                    foreach($listKelas as $k) { $optK[$k->id] = $k->nama_kelas; }
                @endphp
                <x-ui.select wire:model.live="filterKelas" :options="$optK" />
            </div>
        </div>
    </x-ui.card>

    @if($filterKelas && count($siswas) > 0)
        <div class="glass-card shadow-2xl rounded-2xl overflow-hidden border border-white/20 dark:border-white/10">
            <div class="overflow-x-auto w-full custom-scrollbar">
                <table class="w-full text-left border-collapse bg-white/50 dark:bg-[#0c0e1a]/50">
                    <thead>
                        <tr class="bg-gray-100/80 dark:bg-slate-900/90 text-xs font-bold txt-secondary uppercase tracking-wider backdrop-blur-md">
                            <th class="py-3 px-4 border-b border-gray-200 dark:border-white/5 w-10 text-center">No</th>
                            <th class="py-3 px-4 border-b border-gray-200 dark:border-white/5">Siswa</th>
                            <th class="py-3 px-4 border-b border-gray-200 dark:border-white/5 text-center">Rata² Nilai</th>
                            <th class="py-3 px-4 border-b border-gray-200 dark:border-white/5 text-center">Peringkat</th>
                            <th class="py-3 px-4 border-b border-gray-200 dark:border-white/5 text-center">Status Data</th>
                            <th class="py-3 px-4 border-b border-gray-200 dark:border-white/5 text-right">Aksi Dokumen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5 text-sm">
                        @foreach($siswas as $idx => $s)
                            @php 
                                $rp = $raporsMap[$s->id] ?? null; 
                                $avg = $rp ? $rp->rata_rata_nilai : 0;
                            @endphp
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="py-3 px-4 text-center txt-muted font-mono">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4">
                                    <p class="txt-primary font-bold">{{ $s->user->name }}</p>
                                    <p class="text-xs txt-muted font-mono">{{ $s->nisn }}</p>
                                </td>
                                <td class="py-3 px-4 text-center font-extrabold text-indigo-600 dark:text-indigo-400">
                                    {{ $avg > 0 ? $avg : '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if(isset($rankings[$s->id]) && $avg > 0)
                                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100/50 dark:bg-amber-500/10 text-amber-600 font-black border border-amber-500/20 shadow-inner">
                                            {{ $rankings[$s->id] }}
                                        </div>
                                    @else
                                        <span class="txt-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($rp)
                                        @if($rp->is_locked)
                                            <x-ui.badge variant="danger" class="!px-2 !py-0.5"><svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> Terkunci</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="success" class="!px-2 !py-0.5"><svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg> Disunting</x-ui.badge>
                                        @endif
                                    @else
                                        <span class="text-xs txt-muted italic">Kosong</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="openEditModal({{ $s->id }})" class="p-1.5 text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded transition" title="Lengkapi Ekstrakurikuler dll">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button wire:click="toggleLock({{ $s->id }})" class="p-1.5 {{ $rp && $rp->is_locked ? 'text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-500/10' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5' }} rounded transition" title="Kunci/Buka Rapor">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        </button>
                                        <a href="{{ route('admin.kbm.e-rapor.cetak', ['siswa_id' => $s->id, 'ta' => $filterTahunAjaran]) }}" target="_blank" class="p-1.5 text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded transition" title="Cetak PDF Resmi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($filterKelas)
        <div class="py-12 text-center txt-muted italic">Tidak ada siswa yang terdaftar di kelas ini.</div>
    @endif

    {{-- Modal Kelengkapan e-Rapor --}}
    <x-ui.modal name="edit-rapor-modal" maxWidth="2xl">
        <form wire:submit="simpanRapor" class="p-6">
            <h3 class="text-xl font-bold txt-primary mb-1 border-b border-gray-200 dark:border-white/10 pb-4">Kelengkapan e-Rapor Siswa</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                {{-- Kiri: Absensi & Catatan --}}
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <x-ui.label value="Ketidakhadiran (Per Semester)" class="!mb-0" />
                            <button type="button" wire:click="syncAttendance" wire:loading.attr="disabled" class="text-[11px] text-indigo-500 font-extrabold hover:underline flex items-center gap-1">
                                <svg wire:loading.class="animate-spin" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Refresh dari Data Harian
                            </button>
                        </div>
                        <div class="grid grid-cols-3 gap-2 p-3 bg-gray-50 dark:bg-black/20 rounded-xl border border-gray-200 dark:border-white/5">
                            <div class="text-center">
                                <label class="text-[10px] uppercase font-bold text-gray-500">Sakit</label>
                                <input type="number" wire:model="formSakit" class="w-full text-center mt-1 p-1 rounded border-gray-300 dark:border-white/20 bg-white dark:bg-black/40 txt-primary">
                            </div>
                            <div class="text-center">
                                <label class="text-[10px] uppercase font-bold text-gray-500">Izin</label>
                                <input type="number" wire:model="formIzin" class="w-full text-center mt-1 p-1 rounded border-gray-300 dark:border-white/20 bg-white dark:bg-black/40 txt-primary">
                            </div>
                            <div class="text-center">
                                <label class="text-[10px] uppercase font-bold text-gray-500">Alpa</label>
                                <input type="number" wire:model="formAlpa" class="w-full text-center mt-1 p-1 rounded border-gray-300 dark:border-white/20 bg-white dark:bg-black/40 txt-primary">
                            </div>
                        </div>
                    </div>

                    <div>
                        <x-ui.label value="Keputusan Final" />
                        <x-ui.select wire:model="formKeputusan" class="!py-2">
                            <option value="">- Pilih Status -</option>
                            <option value="Naik ke Kelas Berikutnya">Naik ke Kelas Berikutnya</option>
                            <option value="Tinggal di Kelas Saat Ini">Tinggal di Kelas Saat Ini</option>
                            <option value="Lulus">Lulus Menengah Atas</option>
                        </x-ui.select>
                    </div>

                    <div>
                        <x-ui.label value="Catatan Eksekutif Wali Kelas" />
                        <textarea wire:model="formCatatan" rows="3" class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-[#0c0e1a]/50 p-3 txt-primary text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Pertahankan prestasimu nak..."></textarea>
                    </div>
                </div>

                {{-- Kanan: Ekskul & Prestasi --}}
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <x-ui.label value="Ekstrakurikuler" class="!mb-0" />
                            <button type="button" wire:click="addEkskul" class="text-xs text-indigo-500 font-bold hover:underline">+ Tambah</button>
                        </div>
                        <div class="space-y-2">
                            @foreach($formEkskul as $index => $ekskul)
                                <div class="flex gap-2 items-start relative border border-gray-200 dark:border-white/10 p-2 rounded-lg bg-gray-50 dark:bg-black/20">
                                    <div class="flex-1 space-y-1">
                                        <input type="text" wire:model="formEkskul.{{ $index }}.nama" placeholder="Pramuka..." class="w-full text-xs p-1.5 rounded border border-gray-300 dark:border-white/10 bg-white dark:bg-black/40 txt-primary">
                                        <div class="flex gap-1">
                                            <div class="w-24">
                                                @php
                                                    $predOptions = ['' => 'Pred.', 'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'];
                                                @endphp
                                                <x-ui.select wire:model="formEkskul.{{ $index }}.predikat" :options="$predOptions" />
                                            </div>
                                            <input type="text" wire:model="formEkskul.{{ $index }}.keterangan" placeholder="Keterangan..." class="flex-1 text-xs p-1.5 rounded border border-gray-300 dark:border-white/10 bg-white dark:bg-black/40 txt-primary">
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removeEkskul({{ $index }})" class="text-red-500 hover:bg-red-50 p-1 rounded absolute top-1 right-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                </div>
                            @endforeach
                            @if(empty($formEkskul)) <p class="text-xs txt-muted italic">Belum ada ekskul dicatat.</p> @endif
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <x-ui.label value="Prestasi" class="!mb-0" />
                            <button type="button" wire:click="addPrestasi" class="text-xs text-indigo-500 font-bold hover:underline">+ Tambah</button>
                        </div>
                        <div class="space-y-2">
                            @foreach($formPrestasi as $index => $prestasi)
                                <div class="flex gap-2 items-center relative border border-gray-200 dark:border-white/10 p-2 rounded-lg bg-gray-50 dark:bg-black/20 pr-6">
                                    <input type="text" wire:model="formPrestasi.{{ $index }}.jenis" placeholder="Juara 1..." class="w-1/3 text-xs p-1.5 rounded border border-gray-300 dark:border-white/10 bg-white dark:bg-black/40 txt-primary">
                                    <input type="text" wire:model="formPrestasi.{{ $index }}.keterangan" placeholder="Tingkat Nasional..." class="flex-1 text-xs p-1.5 rounded border border-gray-300 dark:border-white/10 bg-white dark:bg-black/40 txt-primary">
                                    <button type="button" wire:click="removePrestasi({{ $index }})" class="text-red-500 hover:bg-red-50 p-1 rounded absolute right-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                </div>
                            @endforeach
                            @if(empty($formPrestasi)) <p class="text-xs txt-muted italic">Belum ada prestasi dicatat.</p> @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-white/10">
                <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'edit-rapor-modal')">Tutup</x-ui.button>
                <x-ui.button type="submit" variant="primary">Simpan Rapor</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

</div>
