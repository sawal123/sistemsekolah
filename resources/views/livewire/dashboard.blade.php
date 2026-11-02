<div class="flex flex-col gap-6 pb-20">
    @if($isGuru)
        {{-- ════════════════════════ DASHBOARD GURU ════════════════════════ --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Dashboard Guru</h1>
                <p class="txt-muted" style="font-size:13px;margin-top:4px;">
                    Selamat datang, {{ auth()->user()->name }} — Ringkasan KBM & perwalian hari ini, {{ now()->translatedFormat('l, d F Y') }}.
                </p>
            </div>

            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                <span>Tahun Ajaran: {{ $taAktif ? $taAktif->tahun . ' (' . ucfirst($taAktif->semester) . ')' : 'Tidak Ada TA Aktif' }}</span>
            </div>
        </div>

        {{-- Kartu Metrik Guru --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <x-ui.card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs txt-muted font-semibold">Kelas Perwalian</p>
                        <h2 class="text-2xl font-black txt-primary mt-2">
                            {{ $kelasWali ? $kelasWali->nama_kelas : 'Bukan Wali' }}
                        </h2>
                        <p class="text-xs txt-muted mt-1">
                            {{ $kelasWali ? $totalSiswaWali . ' Siswa aktif di kelas' : 'Fokus mengajar mapel' }}
                        </p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs txt-muted font-semibold">Kelas Diajar</p>
                        <h2 class="text-2xl font-black txt-primary mt-2">{{ $totalKelasDiajar }} Kelas</h2>
                        <p class="text-xs txt-muted mt-1">{{ $totalSesiMinggu }} Sesi KBM per minggu</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs txt-muted font-semibold">Mata Pelajaran</p>
                        <h2 class="text-2xl font-black txt-primary mt-2">{{ $totalMapelDiajar }} Mapel</h2>
                        <p class="text-xs txt-muted mt-1">Mata pelajaran diampu</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs txt-muted font-semibold">Jadwal Hari Ini ({{ $hariIni }})</p>
                        <h2 class="text-2xl font-black txt-primary mt-2">{{ $jadwalHariIni->count() }} Sesi</h2>
                        <p class="text-xs txt-muted mt-1">Tatap muka hari {{ $hariIni }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Konten Utama Guru --}}
        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_380px] gap-5">
            {{-- Kolom Kiri: Jadwal Hari Ini & Akses Cepat --}}
            <div class="space-y-5">
                <x-ui.card>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="txt-primary text-base font-bold">Jadwal Mengajar Hari Ini</h2>
                            <p class="txt-muted text-xs mt-0.5">Hari {{ $hariIni }}, sesi tatap muka aktif.</p>
                        </div>
                        <a href="{{ route('admin.kbm.jadwal-pelajaran') }}" wire:navigate class="text-xs font-bold text-indigo-500 hover:underline">
                            Lihat Semua Jadwal &rarr;
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse($jadwalHariIni as $jdw)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl border border-indigo-500/10 bg-indigo-500/5 hover:bg-indigo-500/10 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="px-2.5 py-1.5 rounded-lg bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 font-mono text-xs font-bold">
                                        {{ \Carbon\Carbon::parse($jdw->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jdw->jam_selesai)->format('H:i') }}
                                    </div>
                                    <div>
                                        <p class="txt-primary font-bold text-sm">{{ $jdw->mapel->nama_mapel }}</p>
                                        <p class="txt-muted text-xs mt-0.5">Kelas: <span class="font-semibold text-indigo-500">{{ $jdw->kelas->nama_kelas }}</span></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 text-xs">
                                    @if($jdw->ruangan)
                                        <span class="px-2.5 py-1 rounded-md bg-amber-500/10 text-amber-500 font-semibold">
                                            {{ $jdw->ruangan->nama_ruangan }}
                                        </span>
                                    @endif
                                    <a href="{{ route('admin.kbm.manajemen-nilai', ['filterKelas' => $jdw->kelas_id, 'filterMapel' => $jdw->mapel_id, 'filterTahunAjaran' => $taAktif?->id]) }}" wire:navigate class="px-2.5 py-1 rounded-md bg-indigo-500/15 text-indigo-500 font-bold hover:bg-indigo-500/25 transition">
                                        Input Nilai
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 dark:border-white/10 p-8 text-center">
                                <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="txt-primary font-bold text-sm">Tidak ada jadwal mengajar pada hari {{ $hariIni }}.</p>
                                <p class="txt-muted text-xs mt-1">Gunakan waktu luang untuk evaluasi nilai atau menyusun materi ajar.</p>
                            </div>
                        @endforelse
                    </div>
                </x-ui.card>

                {{-- Akses Cepat KBM --}}
                <x-ui.card>
                    <h2 class="txt-primary text-base font-bold mb-3">Akses Cepat Modul KBM</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <a href="{{ route('admin.kbm.jadwal-pelajaran') }}" wire:navigate class="p-4 rounded-xl border border-indigo-500/10 bg-white/50 dark:bg-white/[0.02] hover:border-indigo-500/30 hover:bg-indigo-500/5 transition text-center group">
                            <div class="w-10 h-10 mx-auto rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="txt-primary font-bold text-xs mt-2.5">Jadwal Pelajaran</p>
                        </a>
                        <a href="{{ route('admin.kbm.manajemen-nilai') }}" wire:navigate class="p-4 rounded-xl border border-indigo-500/10 bg-white/50 dark:bg-white/[0.02] hover:border-indigo-500/30 hover:bg-indigo-500/5 transition text-center group">
                            <div class="w-10 h-10 mx-auto rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="txt-primary font-bold text-xs mt-2.5">Manajemen Nilai</p>
                        </a>
                        <a href="{{ route('admin.kbm.rekap-absensi') }}" wire:navigate class="p-4 rounded-xl border border-indigo-500/10 bg-white/50 dark:bg-white/[0.02] hover:border-indigo-500/30 hover:bg-indigo-500/5 transition text-center group">
                            <div class="w-10 h-10 mx-auto rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <p class="txt-primary font-bold text-xs mt-2.5">Rekap Absensi</p>
                        </a>
                        <a href="{{ route('admin.kbm.e-rapor') }}" wire:navigate class="p-4 rounded-xl border border-indigo-500/10 bg-white/50 dark:bg-white/[0.02] hover:border-indigo-500/30 hover:bg-indigo-500/5 transition text-center group">
                            <div class="w-10 h-10 mx-auto rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <p class="txt-primary font-bold text-xs mt-2.5">e-Rapor</p>
                        </a>
                    </div>
                </x-ui.card>
            </div>

            {{-- Kolom Kanan: Status Perwalian & Agenda Terdekat --}}
            <div class="space-y-5">
                @if($kelasWali)
                    <x-ui.card>
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h2 class="txt-primary text-base font-bold">Status Perwalian</h2>
                                <p class="text-xs text-indigo-500 font-bold mt-0.5">{{ $kelasWali->nama_kelas }} ({{ $totalSiswaWali }} Siswa)</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase">
                                Wali Kelas
                            </span>
                        </div>

                        {{-- Kehadiran Hari Ini --}}
                        <div class="mt-4">
                            <p class="text-xs font-semibold txt-muted mb-2">Kehadiran Hari Ini</p>
                            <div class="grid grid-cols-4 gap-2 text-center">
                                <div class="p-2 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                                    <p class="text-base font-black text-emerald-500">{{ $rekapAbsen['hadir'] }}</p>
                                    <p class="text-[10px] txt-muted font-bold">Hadir</p>
                                </div>
                                <div class="p-2 rounded-lg bg-blue-500/10 border border-blue-500/20">
                                    <p class="text-base font-black text-blue-500">{{ $rekapAbsen['sakit'] }}</p>
                                    <p class="text-[10px] txt-muted font-bold">Sakit</p>
                                </div>
                                <div class="p-2 rounded-lg bg-amber-500/10 border border-amber-500/20">
                                    <p class="text-base font-black text-amber-500">{{ $rekapAbsen['izin'] }}</p>
                                    <p class="text-[10px] txt-muted font-bold">Izin</p>
                                </div>
                                <div class="p-2 rounded-lg bg-red-500/10 border border-red-500/20">
                                    <p class="text-base font-black text-red-500">{{ $rekapAbsen['alpa'] }}</p>
                                    <p class="text-[10px] txt-muted font-bold">Alpa</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.kbm.rekap-absensi', ['filterKelas' => $kelasWali->id]) }}" wire:navigate class="mt-3 inline-flex w-full items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-indigo-500/10 text-indigo-500 text-xs font-bold hover:bg-indigo-500/20 transition">
                                Buka Rekap Absensi Kelas
                            </a>
                        </div>

                        {{-- Progress Rapor --}}
                        <div class="mt-5 pt-4 border-t border-indigo-500/10">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="txt-muted font-semibold">Progres e-Rapor Terkunci</span>
                                <span class="txt-primary font-bold">{{ $raporStats->locked ?? 0 }} / {{ $totalSiswaWali }}</span>
                            </div>
                            @php
                                $pct = $totalSiswaWali > 0 ? round((($raporStats->locked ?? 0) / $totalSiswaWali) * 100) : 0;
                            @endphp
                            <div class="w-full h-2 rounded-full bg-gray-200 dark:bg-white/10 overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full transition-all" style="width: {{ $pct }}%;"></div>
                            </div>
                            <a href="{{ route('admin.kbm.e-rapor', ['filterKelas' => $kelasWali->id, 'filterTahunAjaran' => $taAktif?->id]) }}" wire:navigate class="mt-3 inline-flex w-full items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-purple-500/10 text-purple-500 text-xs font-bold hover:bg-purple-500/20 transition">
                                Kelola e-Rapor Siswa
                            </a>
                        </div>
                    </x-ui.card>
                @else
                    <x-ui.card>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-500/10 text-gray-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="txt-primary text-sm font-bold">Status Tugas Guru</h3>
                                <p class="txt-muted text-xs mt-0.5">Guru Pengampu Mata Pelajaran</p>
                            </div>
                        </div>
                        <p class="text-xs txt-muted mt-3 leading-relaxed">
                            Anda saat ini tidak bertugas sebagai Wali Kelas. Aktivitas Anda berfokus pada pengisian nilai harian, PTS, dan PAS sesuai jadwal kelas yang diampu.
                        </p>
                    </x-ui.card>
                @endif

                {{-- Agenda & Kalender Terdekat --}}
                <x-ui.card>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="txt-primary text-base font-bold">Agenda Akademik Terdekat</h2>
                        <a href="{{ route('admin.kbm.kalender-akademik') }}" wire:navigate class="text-xs font-bold text-indigo-500 hover:underline">
                            Kalender
                        </a>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($agendaTerdekat as $event)
                            <div class="flex items-start gap-2.5 p-2.5 rounded-xl border border-gray-200 dark:border-white/5 bg-white/40 dark:bg-white/[0.01]">
                                <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ $event->jenis_libur === 'nasional' ? 'bg-red-500' : ($event->jenis_libur === 'sekolah' ? 'bg-amber-500' : 'bg-indigo-500') }}"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="txt-primary font-bold text-xs truncate">{{ $event->keterangan }}</p>
                                    <p class="txt-muted text-[10px] mt-0.5">{{ $event->tanggal->translatedFormat('d F Y') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs txt-muted italic">Tidak ada agenda libur terdekat.</p>
                        @endforelse
                    </div>
                </x-ui.card>
            </div>
        </div>

    @else
        {{-- ════════════════════════ DASHBOARD ADMIN ════════════════════════ --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Dashboard</h1>
                <p class="txt-muted" style="font-size:13px;margin-top:4px;">Ringkasan penting hari ini, {{ now()->translatedFormat('l, d F Y') }}.</p>
            </div>

            @hasrole('admin')
                <a href="{{ route('admin.website.pengunjung') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2 font-semibold text-sm rounded-xl transition-all duration-200 bg-indigo-500 text-white shadow-md shadow-indigo-500/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14m-6 1a4 4 0 100-8 4 4 0 000 8z" />
                    </svg>
                    Detail Pengunjung Website
                </a>
            @endhasrole
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach([
                ['Siswa Aktif', $totalSiswa, 'Data siswa aktif saat ini', 'bg-indigo-500/10 text-indigo-500'],
                ['Guru', $totalGuru, 'Total guru terdata', 'bg-emerald-500/10 text-emerald-500'],
                ['Rombel', $totalKelas, 'Kelas/rombel aktif', 'bg-amber-500/10 text-amber-500'],
                ['Visit Hari Ini', $visitToday, $uniqueVisitToday . ' pengunjung unik', 'bg-blue-500/10 text-blue-500'],
            ] as [$label, $value, $caption, $class])
                <x-ui.card>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs txt-muted font-semibold">{{ $label }}</p>
                            <h2 class="text-3xl font-black txt-primary mt-2">{{ $value }}</h2>
                            <p class="text-xs txt-muted mt-1">{{ $caption }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-xl {{ $class }} flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_380px] gap-5">
            <x-ui.card>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
                    <div>
                        <h2 class="txt-primary text-base font-bold">Grafik Visit Website</h2>
                        <p class="txt-muted text-xs mt-1">Kunjungan landing page selama 7 hari terakhir.</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs txt-muted">
                        <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-indigo-500"></span>Total</span>
                        <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-red-500"></span>Bahaya</span>
                    </div>
                </div>

                <div class="h-72 flex items-end gap-3 border-b border-indigo-500/10 pb-4">
                    @foreach($visitChart as $point)
                        @php
                            $height = max(8, ($point['total'] / $maxVisit) * 100);
                            $dangerHeight = $point['total'] > 0 ? max(4, ($point['danger'] / max($point['total'], 1)) * $height) : 0;
                        @endphp
                        <div class="flex-1 h-full flex flex-col items-center justify-end gap-2">
                            <div class="w-full h-full flex items-end justify-center gap-1">
                                <div class="w-7 rounded-t-lg bg-indigo-500/80" style="height: {{ $height }}%;" title="{{ $point['total'] }} visit"></div>
                                <div class="w-3 rounded-t bg-red-500/80" style="height: {{ $dangerHeight }}%;" title="{{ $point['danger'] }} log bahaya"></div>
                            </div>
                            <div class="text-center">
                                <p class="text-[11px] font-bold txt-primary">{{ $point['label'] }}</p>
                                <p class="text-[10px] txt-muted">{{ $point['date'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            <div class="space-y-5">
                <x-ui.card>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="txt-primary text-base font-bold">Keamanan Website</h2>
                            <p class="txt-muted text-xs mt-1">Request mencurigakan yang perlu dipantau.</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full {{ $dangerToday > 0 ? 'bg-red-500/10 text-red-500' : 'bg-emerald-500/10 text-emerald-500' }} text-[10px] font-bold uppercase">
                            {{ $dangerToday }} hari ini
                        </span>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse($dangerLogs as $log)
                            <div class="rounded-xl border border-red-500/10 bg-red-500/5 p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs font-black text-red-500">{{ $log->threat_type }}</p>
                                    <p class="text-[10px] txt-muted">{{ $log->visited_at?->format('H:i') }}</p>
                                </div>
                                <p class="text-xs txt-primary font-semibold mt-1">{{ $log->ip_address ?: '-' }}</p>
                                <p class="text-[11px] txt-muted mt-1 break-all">{{ $log->path }}</p>
                            </div>
                        @empty
                            <div class="rounded-xl border border-emerald-500/10 bg-emerald-500/5 p-4">
                                <p class="text-sm font-bold text-emerald-500">Belum ada log berbahaya.</p>
                                <p class="text-xs txt-muted mt-1">Pantauan hari ini terlihat aman.</p>
                            </div>
                        @endforelse
                    </div>
                </x-ui.card>

                @hasrole('admin')
                    <x-ui.card>
                        <h2 class="txt-primary text-base font-bold">PPDB</h2>
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-indigo-500/10 p-4">
                                <p class="text-2xl font-black text-indigo-500">{{ $ppdbOpen }}</p>
                                <p class="text-xs txt-muted mt-1">Gelombang dibuka</p>
                            </div>
                            <div class="rounded-xl bg-amber-500/10 p-4">
                                <p class="text-2xl font-black text-amber-500">{{ $ppdbNeedReview }}</p>
                                <p class="text-xs txt-muted mt-1">Perlu dicek</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.ppdb.pendaftaran-murid-baru') }}" wire:navigate class="mt-4 inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded-xl bg-indigo-500/10 text-indigo-500 text-sm font-bold">
                            Buka Data PPDB
                        </a>
                    </x-ui.card>
                @endhasrole
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <x-ui.card class="lg:col-span-2">
                <h2 class="txt-primary text-base font-bold">Fokus Hari Ini</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="rounded-xl border border-indigo-500/10 p-4">
                        <p class="text-xs txt-muted font-semibold">PPDB Baru</p>
                        <p class="text-2xl font-black txt-primary mt-2">{{ $ppdbNew }}</p>
                        <p class="text-xs txt-muted mt-1">Pendaftar belum diperiksa.</p>
                    </div>
                    <div class="rounded-xl border border-indigo-500/10 p-4">
                        <p class="text-xs txt-muted font-semibold">Unique Visitor</p>
                        <p class="text-2xl font-black txt-primary mt-2">{{ $uniqueVisitToday }}</p>
                        <p class="text-xs txt-muted mt-1">Pengunjung berbeda hari ini.</p>
                    </div>
                    <div class="rounded-xl border border-indigo-500/10 p-4">
                        <p class="text-xs txt-muted font-semibold">Log Bahaya</p>
                        <p class="text-2xl font-black {{ $dangerToday > 0 ? 'text-red-500' : 'text-emerald-500' }} mt-2">{{ $dangerToday }}</p>
                        <p class="text-xs txt-muted mt-1">Request mencurigakan hari ini.</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <h2 class="txt-primary text-base font-bold">Waktu Sistem</h2>
                <p class="text-3xl font-black txt-primary mt-3">{{ now()->format('H:i') }}</p>
                <p class="text-xs txt-muted mt-1">Asia/Jakarta</p>
                <p class="text-xs txt-muted mt-5">Diperbarui saat halaman dimuat.</p>
            </x-ui.card>
        </div>
    @endif
</div>
