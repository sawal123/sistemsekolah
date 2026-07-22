<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Template Absensi - Kelas {{ $kelas->nama_kelas }}</title>
    <style>
        @page {
            size: legal landscape;
            margin: 0.8cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
        }

        /* ── HEADER ── */
        .header-bar {
            width: 100%;
            margin-bottom: 4px;
            border-collapse: collapse;
            font-size: 9px;
        }

        .header-bar td {
            padding: 1px 0;
        }

        .title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        /* ── MAIN TABLE ── */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .grid-table th,
        .grid-table td {
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            height: 14px;
            padding: 0;
            overflow: hidden;
        }

        .grid-table th {
            font-size: 7px;
            font-weight: bold;
            background-color: #f3f4f6;
        }

        .grid-table td {
            font-size: 8px;
        }

        /* ── COLUMN WIDTHS ──
           Legal Landscape lebar efektif ±33cm setelah margin 1.6cm.
           Kolom: No(1%) + Nama(14%) + Induk(4%) + 31 tgl(% masing) + S+I+A(1.2% each) + Ket(9%)
           31 × 2.1% = 65.1%  →  total = 1 + 14 + 4 + 65.1 + 3.6 + 9 = 96.7% ✓
        */
        .col-no {
            width: 2%;
        }

        .col-nama {
            width: 14%;
            text-align: left !important;
            padding-left: 3px !important;
            white-space: normal !important;
            word-break: break-word;
        }

        .col-induk {
            width: 4%;
            font-size: 7px;
        }

        .col-tgl {
            width: 2.2%;
            font-size: 7px;
        }

        .col-s {
            width: 1.5%;
        }

        .col-i {
            width: 1.5%;
        }

        .col-a {
            width: 1.5%;
        }

        .col-ket {
            width: 8%;
            text-align: left !important;
            padding-left: 3px !important;
        }

        /* Hari libur / Minggu */
        .col-libur {
            background-color: #d1d5db;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 15px;
            font-size: 9px;
            width: 100%;
        }

        .footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer td {
            padding: 2px;
            vertical-align: top;
        }

        .sign-area {
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <table class="header-bar">
        <tr>
            <td width="40%">
                BULAN : <strong>{{ strtoupper($namaBulan) }} {{ $tahun }}</strong>
                <span style="display:inline-block; width: 120px; border-bottom: 1px solid #000;"></span>
            </td>
            <td width="20%" class="title">BUKU ABSENSI SISWA</td>
            <td width="40%" style="text-align:right;">
                KELAS : <strong>{{ strtoupper($kelas->nama_kelas) }}</strong>
                &nbsp;&nbsp;&nbsp; Wali Kelas : <strong>{{ $kelas->wali_kelas->user->name ?? '-' }}</strong>
            </td>
        </tr>
    </table>

    <!-- MAIN ATTENDANCE TABLE -->
    <table class="grid-table">
        <thead>
            <tr>
                <th rowspan="2" class="col-no">No.<br>Urut</th>
                <th rowspan="2" class="col-nama" style="text-align:center !important;">NAMA MURID</th>
                <th rowspan="2" class="col-induk">No.<br>Daftar<br>Induk</th>
                <th colspan="{{ $daysInMonth }}">T A N G G A L</th>
                <th rowspan="2" class="col-s">S</th>
                <th rowspan="2" class="col-i">I</th>
                <th rowspan="2" class="col-a">A</th>
                <th rowspan="2" class="col-ket">KETERANGAN</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= $daysInMonth; $i++)
                    @php $isLibur = isset($kalender[$i]) && $kalender[$i]['is_libur']; @endphp
                    <th class="col-tgl {{ $isLibur ? 'col-libur' : '' }}" title="{{ $isLibur ? 'Libur' : 'Efektif' }}">
                        {{ $i }}
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @for ($idx = 0; $idx < 36; $idx++)
                @php $s = $siswas[$idx] ?? null; @endphp
                <tr>
                    <td class="col-no">{{ $idx + 1 }}</td>
                    <td class="col-nama">{{ $s ? strtoupper($s->user->name) : '' }}</td>
                    <td class="col-induk">{{ $s ? ($s->nis ?? '') : '' }}</td>

                    @for ($i = 1; $i <= $daysInMonth; $i++)
                        @php $isLibur = isset($kalender[$i]) && $kalender[$i]['is_libur']; @endphp
                        <td class="col-tgl {{ $isLibur ? 'col-libur' : '' }}"></td>
                    @endfor

                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="col-ket"></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <table>
            <tr>
                <td width="45%">
                    <strong>Absensi :</strong><br>
                    <table style="margin-top:4px; line-height:1.7;">
                        <tr>
                            <td width="55">Sakit</td>
                            <td width="20">(S)</td>
                            <td>.................. × 100 = ......... %</td>
                        </tr>
                        <tr>
                            <td>Izin</td>
                            <td>(I)</td>
                            <td>.................. × 100 = ......... %</td>
                        </tr>
                        <tr>
                            <td>Tidak Sah</td>
                            <td>(A)</td>
                            <td>.................. × 100 = ......... %</td>
                        </tr>
                    </table>
                </td>
                <td width="25%" class="sign-area">
                    <br>
                    Guru Kelas<br><br><br><br><br>
                    <strong>{{ $kelas->wali_kelas->user->name ?? '________________________' }}</strong><br>
                    NIP. {{ $kelas->wali_kelas->nip ?? '________________________' }}
                </td>
                <td width="25%" class="sign-area">
                    .........., ......... {{ $namaBulan }} {{ $tahun }}<br>
                    Kepala Sekolah<br><br><br><br><br>
                    <strong>________________________</strong><br>
                    NIP. ________________________
                </td>
            </tr>
        </table>
    </div>

</body>

</html>