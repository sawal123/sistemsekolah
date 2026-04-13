<div>
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-6 fu d1">
        <div>
            <h1 class="txt-primary text-[22px] font-extrabold flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Manajemen Nilai Pintar
            </h1>
            <p class="txt-muted text-[13px] mt-1">Spreadsheet interaktif terintegrasi Auto-save & sistem bobot otomatis.</p>
        </div>
        
        <div class="glass flex items-center p-1 rounded-xl w-max">
            <button wire:click="$set('tab', 'spreadsheet')"
                class="cursor-pointer px-4 py-2 text-[13px] font-bold rounded-lg transition-all {{ $tab === 'spreadsheet' ? 'bg-indigo-500/20 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 hover:text-indigo-400' }}">
                Spreadsheet Nilai
            </button>
            <button wire:click="$set('tab', 'pengaturan')"
                class="cursor-pointer px-4 py-2 text-[13px] font-bold rounded-lg transition-all {{ $tab === 'pengaturan' ? 'bg-indigo-500/20 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 hover:text-indigo-400' }}">
                Pengaturan Mapel
            </button>
        </div>
    </div>

    {{-- Filter Card --}}
    <x-ui.card class="mb-6 fu d2 relative z-10 border border-indigo-500/10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <x-ui.label value="Tahun Ajaran Aktif" />
                @php
                    $optTA = ['' => 'Pilih Tahun Ajaran'];
                    foreach($listTahunAjaran as $ta) { $optTA[$ta->id] = $ta->tahun . ' - ' . ucfirst($ta->semester); }
                @endphp
                <x-ui.select wire:model.live="filterTahunAjaran" :options="$optTA" />
            </div>
            <div>
                <x-ui.label value="Pilih Kelas" />
                @php
                    $optK = ['' => 'Pilih Kelas'];
                    foreach($listKelas as $k) { $optK[$k->id] = $k->nama_kelas; }
                @endphp
                <x-ui.select wire:model.live="filterKelas" :options="$optK" />
            </div>
            <div>
                <x-ui.label value="Pilih Mata Pelajaran" />
                @php
                    $optM = ['' => 'Pilih Mata Pelajaran'];
                    foreach($listMapel as $m) { $optM[$m->id] = $m->nama_mapel; }
                @endphp
                <x-ui.select wire:model.live="filterMapel" :options="$optM" />
            </div>
        </div>
    </x-ui.card>

    @if(!$filterMapel || !$filterKelas)
        <div class="py-16 text-center glass-card border border-indigo-500/10 rounded-2xl flex flex-col items-center fu d3">
            <div class="h-20 w-20 rounded-full bg-indigo-500/10 flex items-center justify-center mb-4">
                <svg class="h-10 w-10 text-indigo-500/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            </div>
            <h3 class="txt-primary text-xl font-extrabold mb-1">Siap Mengolah Angka?</h3>
            <p class="txt-muted text-sm max-w-sm">Tentukan Kelas dan Mata Pelajaran di panel atas untuk memulai sesi pengisian skor rapor interaktif ini.</p>
        </div>
    @else

        @if($tab === 'pengaturan')
            {{-- Pengaturan Form KKM dan Bobot --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 fu d3" x-data x-transition>
                <x-ui.card title="Konfigurasi Mapel & KKM">
                    <form wire:submit="simpanPengaturan" class="space-y-5">
                        <div class="p-4 rounded-xl bg-orange-500/10 border border-orange-500/20 text-orange-600 dark:text-orange-400 text-sm flex items-start gap-3">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p>Perhatian: Mengubah bobot akan membuat seluruh daftar nilai Akhir tersesuaikan massal ketika dihitung ulang!</p>
                        </div>
                        
                        <div>
                            <x-ui.label value="Kriteria Ketuntasan Minimal (KKM)" required />
                            <x-ui.input wire:model="mapel_kkm" type="number" min="0" max="100" class="text-xl font-bold" />
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3 pt-4 border-t border-gray-200 dark:border-white/10">
                            <div>
                                <x-ui.label value="Bobot Harian (%)" required />
                                <x-ui.input wire:model="mapel_bobot_harian" type="number" min="0" max="100" />
                            </div>
                            <div>
                                <x-ui.label value="Bobot PTS (%)" required />
                                <x-ui.input wire:model="mapel_bobot_pts" type="number" min="0" max="100" />
                            </div>
                            <div>
                                <x-ui.label value="Bobot PAS (%)" required />
                                <x-ui.input wire:model="mapel_bobot_pas" type="number" min="0" max="100" />
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <x-ui.button type="submit" variant="primary">Simpan Pengaturan</x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
                
                <x-ui.card title="Simulasi Kalkulasi">
                    <div class="space-y-4">
                        <p class="txt-muted text-sm border-b border-gray-200 dark:border-white/10 pb-2">Nilai Akhir dirumuskan berdasarkan rasio proporsi persentase yang Anda bagikan.</p>
                        <div class="flex flex-col gap-2 font-mono text-sm txt-secondary bg-gray-50 dark:bg-black/30 p-4 rounded-xl border border-gray-200 dark:border-white/5">
                            <div class="flex justify-between"><span>Nilai Harian</span> <span>x {{ $mapel_bobot_harian ?? 40 }}%</span></div>
                            <div class="flex justify-between"><span>Nilai PTS</span> <span>x {{ $mapel_bobot_pts ?? 30 }}%</span></div>
                            <div class="flex justify-between"><span>Nilai PAS</span> <span>x {{ $mapel_bobot_pas ?? 30 }}%</span></div>
                            <div class="flex justify-between font-bold txt-primary border-t border-gray-300 dark:border-white/20 pt-2 mt-1"><span>Total</span> <span>{{ ($mapel_bobot_harian??0) + ($mapel_bobot_pts??0) + ($mapel_bobot_pas??0) }}%</span></div>
                        </div>
                    </div>
                </x-ui.card>
            </div>
        @elseif($tab === 'spreadsheet')
            {{-- Mode Spreadsheet --}}
            <div class="fu d3" x-data x-transition>
                <div class="flex justify-between items-center mb-3">
                    <p class="text-sm font-semibold txt-secondary">
                        <span class="text-indigo-500 mr-2">●</span> Total Siswa: {{ count($siswas) }} | KKM: <span class="font-bold txt-primary bg-indigo-500/10 px-2 py-0.5 rounded">{{ $mapelSetting->kkm ?? 75 }}</span>
                    </p>
                    <div class="flex gap-2">
                        <x-ui.button wire:click="downloadExcel" variant="secondary" class="cursor-pointer !px-3 !py-1.5 text-xs text-emerald-600 border-emerald-500/20 bg-emerald-500/10 hover:bg-emerald-500/20">
                            Download Blank Excel
                        </x-ui.button>
                        <x-ui.button x-on:click="$dispatch('open-modal', 'upload-modal')" variant="primary" class="cursor-pointer !px-3 !py-1.5 text-xs">
                            Unggah Excel
                        </x-ui.button>
                    </div>
                </div>

                <style>
                    .ss-input {
                        width: 4rem; height: 2rem; border-radius: 4px; border: 1px solid transparent; background: transparent;
                        text-align: center; font-weight: 600; color: inherit; transition: all 0.2s; outline: none; padding: 0 4px;
                    }
                    .ss-input:-webkit-autofill, .ss-input:-webkit-autofill:hover, .ss-input:-webkit-autofill:focus, .ss-input:-webkit-autofill:active{
                        -webkit-box-shadow: 0 0 0 30px transparent inset !important;
                    }
                    .ss-input:hover { border-color: rgba(99, 102, 241, 0.4); }
                    .ss-input:focus { border-color: #6366f1; background: rgba(255,255,255,0.05); box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2); }
                    .dark .ss-input:focus { background: rgba(0,0,0,0.3); }
                    /* Input spinner hidden */
                    .ss-input::-webkit-outer-spin-button, .ss-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
                </style>

                <div class="glass-card shadow-2xl rounded-2xl overflow-hidden border border-white/20 dark:border-white/10">
                    <div class="overflow-x-auto w-full custom-scrollbar">
                        <table class="w-full text-left min-w-[1000px] border-collapse bg-white/50 dark:bg-[#0c0e1a]/50">
                            <thead>
                                <tr class="bg-gray-100/80 dark:bg-slate-900/90 text-xs font-bold txt-secondary uppercase tracking-wider backdrop-blur-md">
                                    <th class="py-3 px-4 border-b border-r border-gray-200 dark:border-white/5 w-10 text-center sticky left-0 z-20 bg-gray-100/90 dark:bg-slate-900/90 backdrop-blur-md">No</th>
                                    <th class="py-3 px-4 border-b border-r border-gray-200 dark:border-white/5 w-64 sticky left-10 z-20 bg-gray-100/90 dark:bg-slate-900/90 backdrop-blur-md shadow-[4px_0_10px_rgba(0,0,0,0.05)] shadow-black/20">Nama Siswa</th>
                                    
                                    {{-- Kolom Harian Dinamis --}}
                                    <th class="py-3 px-2 border-b border-r border-indigo-200 dark:border-indigo-500/20 text-center bg-indigo-50/50 dark:bg-indigo-500/[0.02]">Tgs 1</th>
                                    <th class="py-3 px-2 border-b border-r border-indigo-200 dark:border-indigo-500/20 text-center bg-indigo-50/50 dark:bg-indigo-500/[0.02]">Tgs 2</th>
                                    <th class="py-3 px-2 border-b border-r border-indigo-200 dark:border-indigo-500/20 text-center bg-indigo-50/50 dark:bg-indigo-500/[0.02]">Tgs 3</th>
                                    <th class="py-3 px-2 border-b border-r border-indigo-200 dark:border-indigo-500/20 text-center bg-indigo-50/50 dark:bg-indigo-500/[0.02] text-indigo-600 dark:text-indigo-400">Rataan</th>
                                    
                                    <th class="py-3 px-2 border-b border-r border-gray-200 dark:border-white/5 text-center text-rose-600 dark:text-rose-400">PTS</th>
                                    <th class="py-3 px-2 border-b border-r border-gray-200 dark:border-white/5 text-center text-amber-600 dark:text-amber-400">PAS</th>
                                    
                                    <th class="py-3 px-3 border-b border-r border-gray-200 dark:border-white/5 text-center text-emerald-600 dark:text-emerald-400">Akhir</th>
                                    <th class="py-3 px-2 border-b border-r border-gray-200 dark:border-white/5 text-center">Pred.</th>
                                    <th class="py-3 px-3 border-b border-gray-200 dark:border-white/5 text-center">Remedial</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                @forelse($siswas as $idx => $s)
                                    @php 
                                        $n = $nilaisRender[$s->id] ?? null;
                                        $na = $n ? $n->nilai_akhir : 0;
                                        $kkm = $mapelSetting->kkm ?? 75;
                                        $isRemedial = ($na < $kkm && $n !== null);
                                    @endphp
                                    <tr class="hover:bg-indigo-50/30 dark:hover:bg-white/[0.02] transition-colors {{ $isRemedial ? 'bg-red-50/40 dark:bg-red-500/5' : '' }}">
                                        {{-- Sticky Cols --}}
                                        <td class="py-2 px-4 border-r border-gray-200 dark:border-white/5 text-center text-xs txt-muted font-mono sticky left-0 z-10 bg-white dark:bg-[#0c0e1a]">
                                            {{ $idx + 1 }}
                                        </td>
                                        <td class="py-2 px-4 border-r border-gray-200 dark:border-white/5 sticky left-10 z-10 bg-white dark:bg-[#0c0e1a] shadow-[4px_0_10px_rgba(0,0,0,0.05)] shadow-black/20">
                                            <div class="flex flex-col">
                                                <span class="txt-primary text-sm font-bold truncate max-w-[220px]">{{ $s->user->name }}</span>
                                                <span class="text-[10px] txt-muted font-mono">{{ $s->nisn }}</span>
                                            </div>
                                        </td>

                                        {{-- Editable Harian --}}
                                        <td class="px-1 border-r border-indigo-100 dark:border-white/5 bg-indigo-50/10 dark:bg-indigo-500/[0.01]">
                                            <div class="flex justify-center"><input type="number" x-on:change="$wire.updateHarian({{ $s->id }}, 't1', $el.value)" value="{{ $n_harian[$s->id]['t1'] ?? '' }}" class="ss-input text-gray-700 dark:text-gray-300"></div>
                                        </td>
                                        <td class="px-1 border-r border-indigo-100 dark:border-white/5 bg-indigo-50/10 dark:bg-indigo-500/[0.01]">
                                            <div class="flex justify-center"><input type="number" x-on:change="$wire.updateHarian({{ $s->id }}, 't2', $el.value)" value="{{ $n_harian[$s->id]['t2'] ?? '' }}" class="ss-input text-gray-700 dark:text-gray-300"></div>
                                        </td>
                                        <td class="px-1 border-r border-indigo-200 dark:border-indigo-500/20 bg-indigo-50/10 dark:bg-indigo-500/[0.01]">
                                            <div class="flex justify-center"><input type="number" x-on:change="$wire.updateHarian({{ $s->id }}, 't3', $el.value)" value="{{ $n_harian[$s->id]['t3'] ?? '' }}" class="ss-input text-gray-700 dark:text-gray-300"></div>
                                        </td>
                                        
                                        {{-- Read-only Rataan --}}
                                        <td class="px-2 border-r border-gray-200 dark:border-white/5 text-center font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50/40 dark:bg-indigo-500/[0.03]">
                                            {{ $n ? $n->rata_harian : '-' }}
                                        </td>

                                        {{-- Editable PTS & PAS --}}
                                        <td class="px-1 border-r border-gray-200 dark:border-white/5">
                                            <div class="flex justify-center"><input type="number" x-on:change="$wire.updatePts({{ $s->id }}, $el.value)" value="{{ $n_pts[$s->id] ?? '' }}" class="ss-input text-rose-600 dark:text-rose-400"></div>
                                        </td>
                                        <td class="px-1 border-r border-gray-200 dark:border-white/5">
                                            <div class="flex justify-center"><input type="number" x-on:change="$wire.updatePas({{ $s->id }}, $el.value)" value="{{ $n_pas[$s->id] ?? '' }}" class="ss-input text-amber-600 dark:text-amber-400"></div>
                                        </td>

                                        {{-- Kalkulasi Akhir & Predikat --}}
                                        <td class="px-3 border-r border-gray-200 dark:border-white/5 text-center">
                                            <span class="font-extrabold text-[15px] {{ $isRemedial ? 'text-red-500' : 'text-emerald-500' }}">
                                                {{ $n ? $n->nilai_akhir : '-' }}
                                            </span>
                                            @if($isRemedial)
                                                <div class="text-[9px] text-red-500 uppercase tracking-widest font-bold mt-0.5">Remedial</div>
                                            @endif
                                        </td>
                                        <td class="px-2 border-r border-gray-200 dark:border-white/5 text-center">
                                            @if($n)
                                                <x-ui.badge variant="{{ $n->predikat === 'A' ? 'success' : ($n->predikat === 'D' ? 'danger' : 'info') }}" class="font-black text-xs px-2 py-0.5">
                                                    {{ $n->predikat }}
                                                </x-ui.badge>
                                            @else
                                                <span class="text-xs txt-muted">-</span>
                                            @endif
                                        </td>

                                        {{-- Field Remedial --}}
                                        <td class="px-3 text-center">
                                            @if($isRemedial || ($n && $n->skor_remedial))
                                                <div class="flex items-center gap-1 justify-center relative">
                                                    <div class="absolute left-1" title="Input nilai perbaikan"><svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg></div>
                                                    <input type="number" x-on:change="$wire.updateRemedial({{ $s->id }}, $el.value)" value="{{ $n_remedial[$s->id] ?? '' }}" class="ss-input pl-5 bg-red-50/50 dark:bg-white/5 text-red-500 font-bold border-red-500/20" placeholder="Perbaikan">
                                                </div>
                                            @else
                                                <span class="text-[10px] text-emerald-500 italic block mt-1"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Tuntas</span>
                                            @endif
                                        </td>
                                    </tr>
                                    
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-8 text-center txt-muted italic text-sm">Tidak ada siswa di kelas ini.</td>
                                    </tr>
                                @endforelse
                                
                                {{-- Bottom border to cover overflow visual artifact --}}
                                <tr><td colspan="10" class="h-1 bg-white/50 dark:bg-black/20"></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 p-4 rounded-xl border border-blue-500/20 bg-blue-500/5 text-blue-800 dark:text-blue-300 text-xs flex gap-3 shadow-inner">
                   <svg class="h-6 w-6 shrink-0 mt-0.5 shadow-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                   <div>
                       <p class="font-bold mb-1">Spreadsheet Interaktif</p>
                       <p>Klik kotak nilai manapun dan ketik angkanya. Nilai otomatis tersimpan (auto-save) ke server dan dikalkulasi dua detik setelah kursor Anda berpindah kotak (tekan tombol <b>Tab</b>). Tidak butuh tombol "Simpan"!</p>
                   </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Modal Import Excel --}}
    <x-ui.modal name="upload-modal" maxWidth="md">
        <form wire:submit="importExcel" class="p-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-10 w-10 flex border border-emerald-500/20 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold txt-primary">Impor Data Nilai Excel</h3>
                    <p class="text-xs txt-muted">Otomatis sinkronisasi baris demi baris ke sistem.</p>
                </div>
            </div>

            <div class="mt-4 mb-6">
                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-white/10 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik untuk memilih fle</span> atau tarik masuk Excel Anda</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pastikan format kolom tidak diubah (xlsx, xls)</p>
                    </div>
                    <input type="file" wire:model="fileExcel" class="hidden" accept=".xlsx, .xls" />
                </label>
                
                <div wire:loading wire:target="fileExcel" class="text-xs text-emerald-500 font-bold mt-2 text-center w-full">
                    Membaca dokumen...
                </div>
                
                @error('fileExcel') 
                    <span class="text-xs text-red-500 font-semibold mt-2 block text-center">{{ $message }}</span> 
                @enderror
                
                @if ($fileExcel)
                    <div class="mt-3 p-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 rounded-lg text-xs font-semibold flex justify-between items-center px-4">
                        <span class="truncate block w-48">{{ $fileExcel->getClientOriginalName() }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                @endif
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'upload-modal')" wire:loading.attr="disabled">Batal</x-ui.button>
                <div wire:loading.remove wire:target="importExcel">
                    <x-ui.button type="submit" variant="primary">Proses & Unggah</x-ui.button>
                </div>
                <div wire:loading wire:target="importExcel">
                    <x-ui.button type="button" variant="primary" disabled>Menyinkronkan...</x-ui.button>
                </div>
            </div>
        </form>
    </x-ui.modal>

</div>
