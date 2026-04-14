<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>e-Rapor Siswa</title>
    <style>
        @page { margin: 1.5in 1in 1in 1in; }
        body { font-family: "Helvetica", "Arial", sans-serif; font-size: 11pt; color: #333; line-height: 1.4; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        .text-sm { font-size: 9pt; }
        .text-2xl { font-size: 16pt; font-weight: bold; }
        .w-full { width: 100%; }
        .mt-4 { margin-top: 1rem; }
        .mb-4 { margin-bottom: 1rem; }
        
        table.t-data { border-collapse: collapse; width: 100%; margin-top: 10pt; }
        table.t-data th, table.t-data td { border: 1px solid #555; padding: 6px; text-align: left; }
        table.t-data th { background-color: #f1f5f9; text-align: center; }
        table.t-data .c { text-align: center; }
        table.t-data .r { text-align: right; }

        table.header-table { width: 100%; border: none; font-size: 10pt; margin-bottom: 10pt; }
        table.header-table td { padding: 2px 0; border: none; }
        
        .signature-table { width: 100%; margin-top: 40pt; border: none; page-break-inside: avoid; }
        .signature-table td { text-align: center; border: none; vertical-align: bottom; }
        .qr-code { width: 80px; height: 80px; margin: 10px auto; }
        
        h3.section-title { font-size: 11pt; border-bottom: 2px solid #555; padding-bottom: 2px; margin-top: 20px; }
        .pagenum:before { content: counter(page); }
        .footer { position: fixed; bottom: -30pt; left: 0px; right: 0px; font-size: 8pt; text-align: right; color: #666; }
    </style>
</head>
<body>

    <div class="footer">
        Dihasilkan oleh Sistem Rapor Pintar | Halaman <span class="pagenum"></span>
    </div>

    <!-- HEADER / IDENTITAS -->
    <div class="text-center mb-4">
        <div class="text-2xl">LAPORAN HASIL BELAJAR SISWA</div>
        <div><strong>SEKOLAH MENENGAH ATAS</strong></div>
    </div>

    <table class="header-table">
        <tr>
            <td width="20%">Nama Peserta Didik</td>
            <td width="3%">:</td>
            <td width="42%"><strong>{{ $siswa->user->name }}</strong></td>
            <td width="15%">Kelas</td>
            <td width="3%">:</td>
            <td width="17%">{{ $siswa->kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <td>NIS / NISN</td>
            <td>:</td>
            <td>{{ $siswa->nisn }}</td>
            <td>Fase/Semester</td>
            <td>:</td>
            <td>{{ strtoupper($ta->semester) }}</td>
        </tr>
        <tr>
            <td>Nama Sekolah</td>
            <td>:</td>
            <td>SMA Pintar Nusantara</td>
            <td>Tahun Ajaran</td>
            <td>:</td>
            <td>{{ $ta->tahun }}</td>
        </tr>
    </table>

    <!-- A. NILAI AKADEMIK -->
    <h3 class="section-title">A. NILAI AKADEMIK</h3>
    <table class="t-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Mata Pelajaran</th>
                <th width="10%">KKM</th>
                <th width="10%">Nilai Akhir</th>
                <th width="10%">Predikat</th>
                <th width="30%">Deskripsi Capaian</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilais as $idx => $n)
            <tr>
                <td class="c">{{ $idx + 1 }}</td>
                <td>{{ $n->mapel->nama_mapel }}</td>
                <td class="c">{{ $n->mapel->kkm }}</td>
                <td class="c font-bold">{{ $n->nilai_akhir }}</td>
                <td class="c font-bold">{{ $n->predikat }}</td>
                <td class="text-sm">{{ $n->deskripsi }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="c">Belum ada data nilai.</td>
            </tr>
            @endforelse
            <tr>
                <td colspan="2" class="r font-bold">Rata-Rata Seluruh Mata Pelajaran :</td>
                <td colspan="4" class="c font-bold">{{ $rapor ? $rapor->rata_rata_nilai : '-' }} (Peringkat: {{ $peringkat }})</td>
            </tr>
        </tbody>
    </table>

    <!-- B. EKSTRAKURIKULER -->
    <h3 class="section-title">B. EKSTRAKURIKULER</h3>
    <table class="t-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="40%">Kegiatan Ekstrakurikuler</th>
                <th width="15%">Predikat</th>
                <th width="40%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @if($rapor && $rapor->ekskul && count($rapor->ekskul) > 0)
                @foreach($rapor->ekskul as $idx => $eks)
                <tr>
                    <td class="c">{{ $idx + 1 }}</td>
                    <td>{{ $eks['nama'] }}</td>
                    <td class="c">{{ $eks['predikat'] }}</td>
                    <td>{{ $eks['keterangan'] }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="4" class="c">Tidak ada catatan ekstrakurikuler.</td></tr>
            @endif
        </tbody>
    </table>

    <!-- C. PRESTASI -->
    <h3 class="section-title">C. PRESTASI</h3>
    <table class="t-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Jenis Prestasi</th>
                <th width="50%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @if($rapor && $rapor->prestasi && count($rapor->prestasi) > 0)
                @foreach($rapor->prestasi as $idx => $prs)
                <tr>
                    <td class="c">{{ $idx + 1 }}</td>
                    <td>{{ $prs['jenis'] }}</td>
                    <td>{{ $prs['keterangan'] }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="3" class="c">Belum ada catatan prestasi.</td></tr>
            @endif
        </tbody>
    </table>

    <!-- D. KETIDAKHADIRAN -->
    <table style="width: 100%; border: none; margin-top: 20pt; page-break-inside: avoid;">
        <tr>
            <td width="40%" style="vertical-align: top;">
                <table class="t-data" style="margin-top:0;">
                    <thead>
                        <tr><th colspan="2">D. KETIDAKHADIRAN</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="60%">Sakit</td>
                            <td class="c">{{ $rapor->total_sakit ?? 0 }} hari</td>
                        </tr>
                        <tr>
                            <td>Izin</td>
                            <td class="c">{{ $rapor->total_izin ?? 0 }} hari</td>
                        </tr>
                        <tr>
                            <td>Tanpa Keterangan</td>
                            <td class="c">{{ $rapor->total_alpa ?? 0 }} hari</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="5%"></td>
            <td width="55%" style="vertical-align: top;">
                <table class="t-data" style="margin-top:0; height: 100%;">
                    <thead>
                        <tr><th>E. CATATAN WALI KELAS</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="height: 50pt; vertical-align: top; font-style: italic;">
                                "{{ $rapor->catatan_wali_kelas ?? 'Belum ada catatan dari Wali Kelas.' }}"
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- KEPUTUSAN -->
    @if($ta->semester === 'genap')
    <div style="margin-top: 15px; padding: 10px; border: 1px solid #555; font-weight: bold;">
        Keputusan: <br/>
        Berdasarkan hasil pencapaian kompetensi pada semester ganjil dan genap, peserta didik ditetapkan:<br/>
        <span style="font-size: 14pt; color: #b91c1c; text-decoration: underline;">
            {{ $rapor->keputusan ?? '.........................................' }}
        </span>
    </div>
    @endif

    <!-- TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td width="33%">
                Mengetahui,<br/>
                Orang Tua / Wali<br/><br/><br/><br/><br/>
                _______________________
            </td>
            <td width="34%">
                <br/>
                Kepala Sekolah<br/>
                <!-- Generate SVG/Base64 dari QR Code -->
                <div>
                    <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" class="qr-code">
                </div>
                <strong>DR. H. Ahmad Daris, M.Pd</strong><br/>
                NIP. 19780512 201001 1 002
            </td>
            <td width="33%">
                Jakarta, {{ date('d F Y') }}<br/>
                Wali Kelas<br/><br/><br/><br/><br/>
                <strong>{{ $siswa->kelas->wali_kelas->user->name ?? '.................' }}</strong><br/>
                NIP. {{ $siswa->kelas->wali_kelas->nip ?? '.................' }}
            </td>
        </tr>
    </table>

</body>
</html>
