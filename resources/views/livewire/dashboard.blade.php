<div style="display:flex;flex-direction:column;gap:20px;height:100%;">
    {{-- Welcome --}}
    <div class="fu d1">
        <h1 class="txt-primary" style="font-size:22px;font-weight:800;">Selamat Pagi, {{ $userName }} 👋</h1>
        <p class="txt-muted" style="font-size:13px;margin-top:3px;">{{ now()->translatedFormat('l, d F Y') }} &mdash;
            Semester Ganjil 2025/2026</p>
    </div>

    {{-- ──── ROW 1: STAT CARDS ──── --}}
    <div class="fu d2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Siswa --}}
        <div class="glass-card" style="padding:22px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                <div
                    style="width:44px;height:44px;border-radius:14px;background:rgba(99,102,241,0.12);display:flex;align-items:center;justify-content:center;">
                    <svg class="w-5 h-5" style="color:#6366f1;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="badge"
                    style="background:rgba(16,185,129,0.12);color:#059669;font-size:11px;">+12</span>
            </div>
            <p class="txt-primary" style="font-size:28px;font-weight:800;line-height:1;">{{ $totalSiswa }}</p>
            <p class="txt-muted" style="font-size:12px;margin-top:4px;">Total Siswa Aktif</p>
            <div class="prog-track" style="margin-top:14px;">
                <div class="prog-bar" style="width:72%;background:linear-gradient(90deg,#6366f1,#8b5cf6);"></div>
            </div>
        </div>

        {{-- Guru --}}
        <div class="glass-card" style="padding:22px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                <div
                    style="width:44px;height:44px;border-radius:14px;background:rgba(139,92,246,0.12);display:flex;align-items:center;justify-content:center;">
                    <svg class="w-5 h-5" style="color:#8b5cf6;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <span class="badge"
                    style="background:rgba(139,92,246,0.12);color:#7c3aed;font-size:11px;">Aktif</span>
            </div>
            <p class="txt-primary" style="font-size:28px;font-weight:800;line-height:1;">{{ $totalGuru }}</p>
            <p class="txt-muted" style="font-size:12px;margin-top:4px;">Total Guru</p>
            <div class="prog-track" style="margin-top:14px;">
                <div class="prog-bar" style="width:58%;background:linear-gradient(90deg,#8b5cf6,#a78bfa);"></div>
            </div>
        </div>

        {{-- Rombel --}}
        <div class="glass-card" style="padding:22px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                <div
                    style="width:44px;height:44px;border-radius:14px;background:rgba(245,158,11,0.12);display:flex;align-items:center;justify-content:center;">
                    <svg class="w-5 h-5" style="color:#f59e0b;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                        <rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                        <rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                        <rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                    </svg>
                </div>
                <span class="badge"
                    style="background:rgba(245,158,11,0.12);color:#d97706;font-size:11px;">Aktif</span>
            </div>
            <p class="txt-primary" style="font-size:28px;font-weight:800;line-height:1;">{{ $totalKelas }}</p>
            <p class="txt-muted" style="font-size:12px;margin-top:4px;">Total Rombel / Kelas</p>
            <div class="prog-track" style="margin-top:14px;">
                <div class="prog-bar" style="width:100%;background:linear-gradient(90deg,#f59e0b,#fbbf24);"></div>
            </div>
        </div>

        {{-- SPP — solid gradient card --}}
        <div class="glass-card"
            style="padding:22px;background:linear-gradient(135deg,rgba(99,102,241,0.85),rgba(139,92,246,0.85));border:1px solid rgba(255,255,255,0.20);">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                <div
                    style="width:44px;height:44px;border-radius:14px;background:rgba(255,255,255,0.20);display:flex;align-items:center;justify-content:center;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="badge"
                    style="background:rgba(255,255,255,0.22);color:white;font-size:11px;">+8.2%</span>
            </div>
            <p style="font-size:22px;font-weight:800;color:white;line-height:1;">Rp 15 Jt</p>
            <p style="font-size:12px;color:rgba(255,255,255,0.65);margin-top:4px;">Pendapatan SPP Bulan Ini</p>
            <div
                style="margin-top:14px;height:6px;border-radius:999px;background:rgba(255,255,255,0.20);overflow:hidden;">
                <div style="height:100%;border-radius:999px;width:62%;background:rgba(255,255,255,0.7);"></div>
            </div>
        </div>
    </div>

    {{-- ──── ROW 2: KEHADIRAN + PENGUMUMAN ──── --}}
    <div class="fu d3 grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Kehadiran --}}
        <div class="glass-card lg:col-span-2" style="padding:24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;">
                <div>
                    <h3 class="txt-primary" style="font-size:15px;font-weight:700;">Kehadiran Siswa Hari Ini</h3>
                    <p class="txt-muted" style="font-size:12px;margin-top:2px;">Total {{ $totalSiswa }} siswa
                        terdaftar</p>
                </div>
                <div
                    style="display:flex;align-items:center;gap:6px;background:rgba(16,185,129,0.10);border:1px solid rgba(16,185,129,0.20);border-radius:999px;padding:5px 12px;">
                    <span class="pulse-dot"
                        style="width:6px;height:6px;border-radius:50%;background:#10b981;"></span>
                    <span style="font-size:11px;font-weight:600;color:#10b981;">Live</span>
                </div>
            </div>

            {{-- Donut + Legend --}}
            <div style="display:flex;align-items:center;gap:28px;">
                <div style="position:relative;flex-shrink:0;">
                    <div class="donut"></div>
                    <div
                        style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;z-index:2;">
                        <span class="txt-primary" style="font-size:22px;font-weight:800;line-height:1;">72%</span>
                        <span class="txt-muted" style="font-size:10px;">Hadir</span>
                    </div>
                </div>

                <div style="flex:1;display:flex;flex-direction:column;gap:12px;">
                    @foreach ([
                        ['Hadir', 324, 72, '#6366f1', '#818cf8'],
                        ['Absen', 45, 10, '#ef4444', '#f87171'],
                        ['Sakit', 36, 8, '#f59e0b', '#fbbf24'],
                        ['Izin', 45, 10, '#10b981', '#34d399'],
                    ] as [$label, $count, $pct, $color1, $color2])
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                                <span
                                    style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:500;"
                                    class="txt-secondary">
                                    <span
                                        style="width:10px;height:10px;border-radius:3px;background:{{ $color1 }};flex-shrink:0;"></span>{{ $label }}</span>
                                <span class="txt-primary" style="font-size:13px;font-weight:700;">{{ $count }}
                                    <span class="txt-muted"
                                        style="font-weight:400;font-size:11px;">({{ $pct }}%)</span></span>
                            </div>
                            <div class="prog-track">
                                <div class="prog-bar"
                                    style="width:{{ $pct }}%;background:linear-gradient(90deg,{{ $color1 }},{{ $color2 }});">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 7-day bar chart --}}
            <div style="margin-top:24px;padding-top:20px;border-top:1px solid rgba(99,102,241,0.10);">
                <p class="txt-muted"
                    style="font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:12px;">
                    7 Hari Terakhir</p>
                <div style="display:flex;align-items:flex-end;gap:8px;height:60px;">
                    @foreach ([
                        ['Sen', 65, true],
                        ['Sel', 80, true],
                        ['Rab', 72, true],
                        ['Kam', 88, true],
                        ['Jum', 58, false],
                        ['Sab', 8, false],
                        ['Min', 8, false],
                    ] as [$day, $height, $active])
                        <div
                            style="flex:1;display:flex;flex-direction:column;align-items:center;gap:5px;height:100%;">
                            <div style="flex:1;width:100%;display:flex;align-items:flex-end;">
                                <div class="bar"
                                    style="width:100%;height:{{ $height }}%;background:{{ $active ? 'linear-gradient(180deg,#818cf8,#6366f1)' : ($height > 50 ? 'linear-gradient(180deg,#fbbf24,#f59e0b)' : 'rgba(150,150,150,0.3)') }};">
                                </div>
                            </div>
                            <span class="txt-muted" style="font-size:10px;">{{ $day }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Pengumuman --}}
        <div class="glass-card lg:col-span-1" style="padding:22px;display:flex;flex-direction:column;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                <h3 class="txt-primary" style="font-size:15px;font-weight:700;">Pengumuman Internal</h3>
                <button
                    style="font-size:12px;color:#6366f1;font-weight:600;background:none;border:none;cursor:pointer;">+
                    Tambah</button>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;flex:1;">
                @foreach ([
                    ['Penting', '2 jam lalu', 'Rapat Koordinasi Wali Kelas', 'Seluruh wali kelas hadir di aula pukul 13.00 WIB.', 'rgba(245,158,11,', '#d97706'],
                    ['Info', '1 hari lalu', 'Input Nilai UTS Dibuka', 'Deadline input nilai UTS: 15 Juli 2025.', 'rgba(99,102,241,', '#6366f1'],
                    ['Sistem', '3 hari lalu', 'Backup Otomatis Selesai', 'Backup data berhasil pukul 02.00 WIB.', 'rgba(139,92,246,', '#7c3aed'],
                ] as [$type, $time, $title, $desc, $rgba, $color])
                    <div
                        style="padding:13px 14px;border-radius:13px;background:{{ $rgba }}0.10);border:1px solid {{ $rgba }}0.22);">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                            <span class="badge"
                                style="background:{{ $rgba }}0.20);color:{{ $color }};font-size:10px;">{{ $type }}</span>
                            <span class="txt-muted" style="font-size:10px;">{{ $time }}</span>
                        </div>
                        <p class="txt-primary" style="font-size:12px;font-weight:600;margin-bottom:3px;">
                            {{ $title }}</p>
                        <p class="txt-muted" style="font-size:11px;line-height:1.5;">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
            <button
                style="margin-top:14px;width:100%;padding:9px;border-radius:11px;border:1.5px dashed rgba(99,102,241,0.25);background:none;cursor:pointer;font-size:12px;color:#6366f1;font-weight:500;transition:all 0.15s;"
                onmouseover="this.style.background='rgba(99,102,241,0.06)'"
                onmouseout="this.style.background='none'">Lihat Semua</button>
        </div>
    </div>

    {{-- ──── ROW 3: SPP TABLE + ULTAH ──── --}}
    <div class="fu d4 grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- SPP Table --}}
        <div class="glass-card lg:col-span-2" style="padding:24px;overflow-x:auto;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                <div>
                    <h3 class="txt-primary" style="font-size:15px;font-weight:700;">Transaksi SPP Terbaru</h3>
                    <p class="txt-muted" style="font-size:12px;margin-top:2px;">5 transaksi terakhir hari ini</p>
                </div>
                <a href="#"
                    style="font-size:12px;color:#6366f1;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:4px;">
                    Lihat Semua <svg style="width:12px;height:12px;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <table class="tbl" style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th class="txt-muted" style="text-align:left;">Siswa</th>
                        <th class="txt-muted" style="text-align:left;">Kelas</th>
                        <th class="txt-muted" style="text-align:left;">Bulan</th>
                        <th class="txt-muted" style="text-align:right;">Nominal</th>
                        <th class="txt-muted" style="text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ([
                        ['AR', 'Anisa Rahmawati', 'XII IPA 1', 'Juli 2025', 'Rp 350.000', 'Lunas', 'rgba(99,102,241,0.15)', '#6366f1', 'rgba(16,185,129,0.12)', '#059669'],
                        ['BP', 'Budi Pratama', 'XI IPS 2', 'Juli 2025', 'Rp 350.000', 'Lunas', 'rgba(139,92,246,0.15)', '#7c3aed', 'rgba(16,185,129,0.12)', '#059669'],
                        ['CL', 'Citra Lestari', 'X IPA 3', 'Juni 2025', 'Rp 350.000', 'Sebagian', 'rgba(245,158,11,0.15)', '#d97706', 'rgba(245,158,11,0.12)', '#d97706'],
                        ['DH', 'Dimas Haryanto', 'XII IPS 1', 'Juli 2025', 'Rp 350.000', 'Lunas', 'rgba(16,185,129,0.15)', '#059669', 'rgba(16,185,129,0.12)', '#059669'],
                        ['EN', 'Eka Nugroho', 'XI IPA 2', 'Mei 2025', 'Rp 350.000', 'Tunggak', 'rgba(239,68,68,0.15)', '#dc2626', 'rgba(239,68,68,0.12)', '#dc2626'],
                    ] as [$initials, $name, $kelas, $bulan, $nominal, $status, $avBg, $avColor, $badgeBg, $badgeColor])
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="av"
                                        style="background:{{ $avBg }};color:{{ $avColor }};">
                                        {{ $initials }}</div>
                                    <span class="txt-primary"
                                        style="font-size:13px;font-weight:500;">{{ $name }}</span>
                                </div>
                            </td>
                            <td class="txt-secondary" style="font-size:13px;">{{ $kelas }}</td>
                            <td class="txt-secondary" style="font-size:13px;">{{ $bulan }}</td>
                            <td class="txt-primary"
                                style="font-size:13px;font-weight:600;text-align:right;">{{ $nominal }}</td>
                            <td style="text-align:center;"><span class="badge"
                                    style="background:{{ $badgeBg }};color:{{ $badgeColor }};">{{ $status }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Ulang Tahun --}}
        <div class="glass-card lg:col-span-1" style="padding:22px;display:flex;flex-direction:column;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                <div>
                    <h3 class="txt-primary" style="font-size:15px;font-weight:700;">Ulang Tahun</h3>
                    <p class="txt-muted" style="font-size:12px;margin-top:2px;">{{ now()->format('d F Y') }}</p>
                </div>
                <span style="font-size:22px;">🎂</span>
            </div>

            <div style="display:flex;flex-direction:column;gap:10px;flex:1;">
                @foreach ([
                    ['FN', 'Farah Nabila', 'XI IPA 1 — 17 tahun', 'rgba(236,72,153,', '#be185d'],
                    ['GS', 'Gilang Santoso', 'XII IPS 2 — 18 tahun', 'rgba(139,92,246,', '#7c3aed'],
                    ['HW', 'Hana Wijayanti', 'X IPA 2 — 16 tahun', 'rgba(59,130,246,', '#1d4ed8'],
                ] as [$initials, $name, $info, $rgba, $color])
                    <div
                        style="display:flex;align-items:center;gap:11px;padding:11px 13px;border-radius:13px;background:{{ $rgba }}0.09);border:1px solid {{ $rgba }}0.18);">
                        <div class="av" style="background:{{ $rgba }}0.20);color:{{ $color }};">
                            {{ $initials }}</div>
                        <div style="flex:1;min-width:0;">
                            <p class="txt-primary" style="font-size:12px;font-weight:600;">{{ $name }}</p>
                            <p class="txt-muted" style="font-size:11px;">{{ $info }}</p>
                        </div>
                        <button
                            style="font-size:10px;background:{{ $rgba }}0.18);color:{{ $color }};border:none;padding:5px 10px;border-radius:999px;cursor:pointer;font-weight:600;white-space:nowrap;">Ucapkan</button>
                    </div>
                @endforeach
            </div>

            <div
                style="margin-top:14px;padding:13px;border-radius:13px;background:linear-gradient(135deg,rgba(99,102,241,0.12),rgba(139,92,246,0.12));border:1px solid rgba(99,102,241,0.18);text-align:center;">
                <p style="font-size:13px;font-weight:700;color:#6366f1;">3 siswa berulang tahun hari ini 🎉</p>
                <p class="txt-muted" style="font-size:11px;margin-top:3px;">Semoga selalu semangat belajar!</p>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div style="padding:12px 0;display:flex;align-items:center;justify-content:space-between;">
        <p class="txt-muted" style="font-size:11px;">SMA Nusantara &copy; {{ date('Y') }} — Admin Panel v1.0</p>
        <p class="txt-muted" style="font-size:11px;">Diperbarui: {{ now()->format('d M Y, H:i') }} WIB</p>
    </div>
</div>
