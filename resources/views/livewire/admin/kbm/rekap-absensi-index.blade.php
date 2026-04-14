<div>
    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-6">
        <div>
            <h1 class="txt-primary text-[22px] font-extrabold flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Matriks Rekap Absensi
            </h1>
            <p class="txt-muted text-[13px] mt-1">Sistem deteksi cerdas Hari Efektif berbasis Kalender Akademik.</p>
        </div>
        
        @if($filterKelas && count($siswas) > 0)
        <!-- Kumpulan Tombol Alat -->
        <div class="flex gap-3">
            <a href="{{ route('admin.kbm.rekap-absensi.cetak-template', ['kelas' => $filterKelas, 'bulan' => $filterBulan, 'tahun' => $filterTahun]) }}" target="_blank" class="cursor-pointer group relative inline-flex items-center gap-2 justify-center px-4 py-2.5 font-bold text-indigo-600 bg-white border border-indigo-200 hover:border-indigo-500 hover:text-indigo-700 hover:bg-slate-50 rounded-xl shadow-sm transition-all focus:ring focus:ring-indigo-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Unduh Format Kertas</span>
            </a>

            <!-- Tombol Pindai AI Canggih -->
            <button x-on:click="$dispatch('open-modal', 'modal-scanner-ai')" class="cursor-pointer group relative inline-flex items-center gap-2 justify-center px-6 py-2.5 font-extrabold text-white bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 hover:from-indigo-500 hover:to-purple-500 rounded-xl shadow-lg hover:shadow-indigo-500/30 ring-1 ring-white/20 transition-all overflow-hidden bg-[length:200%_auto] hover:bg-[position:right_center]">
                <div class="absolute inset-0 w-full h-full bg-white/20 blur-xl group-hover:bg-transparent transition-colors"></div>
                <svg class="w-5 h-5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="relative z-10">Pindai Kertas (A.I)</span>
            </button>
        </div>
        @endif
    </div>

    <x-ui.card class="mb-6 relative z-50 border border-indigo-500/10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <x-ui.label value="Kelas Perwalian" />
                @php
                    $optK = ['' => 'Pilih Kelas'];
                    foreach($listKelas as $k) { $optK[$k->id] = $k->nama_kelas; }
                @endphp
                <x-ui.select wire:model.live="filterKelas" :options="$optK" />
            </div>
            <div>
                <x-ui.label value="Pilih Bulan" />
                @php
                    $optBulan = [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                @endphp
                <x-ui.select wire:model.live="filterBulan" :options="$optBulan" />
            </div>
            <div>
                <x-ui.label value="Tahun" />
                @php
                    $y = date('Y');
                    $optTahun = [
                        ($y-1) => ($y-1),
                        $y => $y,
                        ($y+1) => ($y+1)
                    ];
                @endphp
                <x-ui.select wire:model.live="filterTahun" :options="$optTahun" />
            </div>
        </div>
    </x-ui.card>

    @if($filterKelas && count($siswas) > 0)
        <!-- Statistik Kecil -->
        <div class="mb-4 flex flex-wrap gap-4">
            <div class="px-4 py-2 border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-slate-900 shadow-sm txt-primary">
                Total Hari Efektif: <span class="font-extrabold text-indigo-600">{{ $hariEfektif }} Hari</span>
            </div>
            <div class="px-4 py-2 border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-slate-900 shadow-sm txt-primary">
                Total Hari Libur: <span class="font-extrabold text-rose-500">{{ count($kalender) - $hariEfektif }} Hari</span>
            </div>
        </div>

        <div class="glass-card shadow-2xl rounded-2xl overflow-hidden border border-white/20 dark:border-white/10">
            <div class="overflow-x-auto w-full custom-scrollbar pb-6">
                <!-- Memaksa minimum width agar matriks cukup menampung 31 kolom -->
                <table class="w-max min-w-full text-left border-collapse bg-white/50 dark:bg-[#0c0e1a]/50 table-fixed">
                    <thead>
                        <tr class="bg-gray-100/80 dark:bg-slate-900/90 text-[10px] font-bold txt-secondary uppercase tracking-wider backdrop-blur-md">
                            <th class="py-3 px-3 border-b border-gray-200 dark:border-white/5 w-8 text-center sticky left-0 bg-gray-100 dark:bg-slate-900 z-20">No</th>
                            <th class="py-3 px-3 border-b border-gray-200 dark:border-white/5 w-48 sticky left-8 bg-gray-100 dark:bg-slate-900 z-20">Siswa</th>
                            
                            @foreach($kalender as $tgl => $data)
                                <th class="py-2 px-1 border-b border-gray-200 dark:border-white/5 w-10 text-center 
                                    {{ $data['is_libur'] ? 'bg-rose-50/80 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400' : '' }}"
                                    title="{{ $data['is_libur'] ? 'Hari Libur / Minggu' : 'Hari Efektif' }}">
                                    {{ $tgl }}<br>
                                    <span class="text-[8px] font-normal opacity-70">{{ $data['is_minggu'] ? 'Min' : date('D', strtotime($data['tanggal'])) }}</span>
                                </th>
                            @endforeach
                            
                            <!-- Rekap Box -->
                            <th class="py-3 px-2 border-b border-gray-200 dark:border-white/5 w-10 text-center bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600">H</th>
                            <th class="py-3 px-2 border-b border-gray-200 dark:border-white/5 w-10 text-center bg-blue-50 dark:bg-blue-900/20 text-blue-600">S</th>
                            <th class="py-3 px-2 border-b border-gray-200 dark:border-white/5 w-10 text-center bg-amber-50 dark:bg-amber-900/20 text-amber-600">I</th>
                            <th class="py-3 px-2 border-b border-gray-200 dark:border-white/5 w-10 text-center bg-rose-50 dark:bg-rose-900/20 text-rose-600">A</th>
                            <th class="py-3 px-2 border-b border-gray-200 dark:border-white/5 w-16 text-center bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5 text-[11px]">
                        @foreach($siswas as $idx => $s)
                            @php
                                $totalH = 0; $totalS = 0; $totalI = 0; $totalA = 0;
                            @endphp
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="py-2 px-3 text-center txt-muted sticky left-0 bg-white/90 dark:bg-[#0c0e1a]/90 backdrop-blur z-10">{{ $idx + 1 }}</td>
                                <td class="py-2 px-3 sticky left-8 bg-white/90 dark:bg-[#0c0e1a]/90 backdrop-blur z-10">
                                    <p class="txt-primary font-bold truncate w-44" title="{{ $s->user->name }}">{{ $s->user->name }}</p>
                                </td>
                                
                                @foreach($kalender as $tgl => $data)
                                    @php
                                        $status = $absensiData[$s->id][$data['tanggal']] ?? '';
                                        if($status == 'hadir') $totalH++;
                                        elseif($status == 'sakit') $totalS++;
                                        elseif($status == 'izin') $totalI++;
                                        elseif($status == 'alpa') $totalA++;
                                    @endphp
                                    <td class="p-1 text-center border-x border-gray-100 dark:border-white/5 {{ $data['is_libur'] ? 'bg-rose-50/40 dark:bg-rose-900/10' : '' }}">
                                        @if($data['is_libur'])
                                            <span class="text-rose-400 font-bold opacity-50 cursor-not-allowed cursor-help" title="Libur">L</span>
                                        @else
                                            <select 
                                                x-data
                                                x-on:change="$wire.updateAbsensi({{ $s->id }}, '{{ $data['tanggal'] }}', $event.target.value)"
                                                class="w-full h-7 text-center appearance-none bg-transparent border-none rounded hover:bg-gray-100 dark:hover:bg-slate-800 focus:ring-2 focus:ring-indigo-500 cursor-pointer font-bold
                                                {{ $status == 'hadir' ? 'text-emerald-500' : ($status == 'sakit' ? 'text-blue-500' : ($status == 'izin' ? 'text-amber-500' : ($status == 'alpa' ? 'text-rose-500' : 'text-gray-400'))) }}"
                                            >
                                                <option value="" @if($status == '') selected @endif>-</option>
                                                <option value="hadir" class="text-emerald-600" @if($status == 'hadir') selected @endif>H</option>
                                                <option value="sakit" class="text-blue-600" @if($status == 'sakit') selected @endif>S</option>
                                                <option value="izin" class="text-amber-600" @if($status == 'izin') selected @endif>I</option>
                                                <option value="alpa" class="text-rose-600" @if($status == 'alpa') selected @endif>A</option>
                                            </select>
                                        @endif
                                    </td>
                                @endforeach

                                @php
                                    // Persentase
                                    // % Kehadiran = (Total Hadir / Hari Efektif) * 100
                                    $persentase = $hariEfektif > 0 ? round(($totalH / $hariEfektif) * 100, 1) : 0;
                                @endphp

                                <td class="py-2 px-2 text-center font-bold text-emerald-600 bg-emerald-50/50 dark:bg-emerald-900/10">{{ $totalH }}</td>
                                <td class="py-2 px-2 text-center font-bold text-blue-600 bg-blue-50/50 dark:bg-blue-900/10">{{ $totalS }}</td>
                                <td class="py-2 px-2 text-center font-bold text-amber-600 bg-amber-50/50 dark:bg-amber-900/10">{{ $totalI }}</td>
                                <td class="py-2 px-2 text-center font-bold text-rose-600 bg-rose-50/50 dark:bg-rose-900/10">{{ $totalA }}</td>
                                <td class="py-2 px-2 text-center font-extrabold text-indigo-600 bg-indigo-50/50 dark:bg-indigo-900/10">
                                    {{ $persentase }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4 flex gap-4 text-xs txt-muted font-mono italic">
            <span>* H = Hadir</span>
            <span>* S = Sakit</span>
            <span>* I = Izin</span>
            <span>* A = Alpa/Tanpa Keterangan</span>
            <span class="text-rose-500">* L = Libur (Kunci Otomatis)</span>
        </div>

    @elseif($filterKelas)
        <div class="py-12 text-center txt-muted italic">Tidak ada siswa terdaftar.</div>
    @else
        <div class="py-12 text-center txt-muted">Silahkan pilih kelas untuk memulai rekap absensi.</div>
    @endif

    <!-- MODAL AI SCANNER -->
    <x-ui.modal name="modal-scanner-ai" title="AI Vision Scanner Matriks Absensi" maxWidth="2xl">
        <div x-data="{ 
            scanning: false, 
            seconds: 0, 
            timerInterval: null,
            startTimer() {
                this.scanning = true;
                this.seconds = 0;
                this.timerInterval = setInterval(() => { this.seconds++ }, 1000);
            },
            stopTimer() {
                this.scanning = false;
                if(this.timerInterval) clearInterval(this.timerInterval);
            },
            formatTime(s) {
                const mins = Math.floor(s / 60);
                const secs = s % 60;
                return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
        }" 
        x-on:close-modal.window="if($event.detail == 'modal-scanner-ai') stopTimer()"
        class="p-6">
            <form wire:submit="scanAbsensi" x-on:submit="startTimer()">
                <div class="mb-6 text-center">
                    <div class="w-20 h-20 mx-auto bg-indigo-50 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                    </div>
                    <h3 class="text-lg font-extrabold txt-primary">Pindai Buku Absen Kertas</h3>
                    <p class="txt-muted text-sm mt-2">Sistem AI akan membaca format rentang absensi bulan <strong>{{ isset($optBulan[$filterBulan]) ? $optBulan[$filterBulan] : '' }} {{ $filterTahun }}</strong> untuk kelas terpilih secara otomatis.</p>
                </div>

                <div class="mb-6">
                    <!-- Dropzone Area -->
                    <label for="fotoKertas" class="relative ring-1 ring-gray-200 dark:ring-white/10 hover:ring-indigo-500 cursor-pointer overflow-hidden rounded-2xl bg-gray-50/50 dark:bg-[#0c0e1a]/50 p-8 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 dark:border-white/20 transition-all hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        <div class="text-center" wire:loading.remove wire:target="fotoKertas">
                            @if($fotoKertas)
                                <div class="font-bold text-emerald-500 mb-2">📸 Gambar Siap Discan!</div>
                                <img src="{{ $fotoKertas->temporaryUrl() }}" class="h-32 mb-4 mx-auto rounded shadow-lg object-contain">
                                <span class="text-xs text-emerald-600 block bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded">Ketuk untuk Ganti Foto</span>
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="text-sm font-semibold txt-secondary mb-1">Unggah foto matriks absensi</p>
                                <p class="text-xs txt-muted">Bilah kolom nama harus terekam jelas.</p>
                            @endif
                        </div>
                        <div wire:loading wire:target="fotoKertas" class="text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500 mx-auto mb-3"></div>
                            <p class="text-sm font-bold text-indigo-500">Mempersiapkan Foto...</p>
                        </div>
                        <input type="file" id="fotoKertas" wire:model="fotoKertas" class="sr-only" accept="image/*">
                    </label>
                    @error('fotoKertas') <span class="text-rose-500 text-xs font-bold mt-1 block text-center">{{ $message }}</span> @enderror
                </div>

                <!-- TIMER DAN PROGRESS -->
                <div x-show="scanning" x-cloak class="mb-6 p-4 bg-indigo-50/50 dark:bg-indigo-900/10 rounded-xl border border-indigo-100 dark:border-indigo-500/20">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Proses Pemindaian AI</span>
                        <span x-text="formatTime(seconds)" class="text-sm font-mono font-bold text-indigo-600 dark:text-indigo-400">00:00</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-white/10 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-indigo-500 h-full animate-progress-indeterminate"></div>
                    </div>
                    <p class="text-[10px] text-center txt-muted mt-2 italic">*Mohon jangan menutup jendela ini hingga proses selesai.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-white/5">
                    <button type="button" x-on:click="$dispatch('close-modal', 'modal-scanner-ai'); stopTimer()" class="cursor-pointer group relative inline-flex items-center gap-2 justify-center px-4 py-2.5 font-extrabold text-white bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 hover:from-indigo-500 hover:to-purple-500 rounded-xl shadow-lg hover:shadow-indigo-500/30 ring-1 ring-white/20 transition-all overflow-hidden bg-[length:200%_auto] hover:bg-[position:right_center] px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all dark:bg-transparent dark:text-gray-300 dark:border-white/10 dark:hover:bg-white/5">
                        Batal
                    </button>
                    
                    <button type="submit" 
                        wire:loading.attr="disabled" 
                        class="cursor-pointer group relative inline-flex items-center gap-2 justify-center px-6 py-2.5 font-extrabold text-white bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 hover:from-indigo-500 hover:to-purple-500 rounded-xl shadow-lg hover:shadow-indigo-500/30 ring-1 ring-white/20 transition-all overflow-hidden bg-[length:200%_auto] hover:bg-[position:right_center] px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none transition-all flex items-center gap-2"
                        @if(!$fotoKertas) disabled @endif>
                        <span wire:loading.remove wire:target="scanAbsensi">Mulai Pemindaian AI</span>
                        <span wire:loading wire:target="scanAbsensi" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menganalisa...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </x-ui.modal>
    
    <style>
        @keyframes progress-indeterminate {
            0% { width: 0; left: 0; }
            50% { width: 70%; left: 15%; }
            100% { width: 100%; left: 0; }
        }
        .animate-progress-indeterminate {
            position: relative;
            animation: progress-indeterminate 2s infinite ease-in-out;
        }
    </style>
</div>
