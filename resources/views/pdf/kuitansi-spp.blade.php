<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kuitansi Pembayaran SPP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            padding: 20px;
        }

        /* ── KOP SURAT ── */
        .kop {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-bottom: 12px;
            border-bottom: 3px solid #4f46e5;
            margin-bottom: 8px;
        }
        .kop-logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        .kop-logo-text {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            font-weight: 900;
        }
        .kop-info { flex: 1; }
        .kop-info h1 {
            font-size: 15px;
            font-weight: 900;
            color: #1e293b;
            letter-spacing: -0.3px;
        }
        .kop-info p {
            font-size: 9.5px;
            color: #64748b;
            margin-top: 2px;
            line-height: 1.5;
        }
        .kop-nomor {
            text-align: right;
        }
        .kop-nomor .badge {
            display: inline-block;
            background: #4f46e5;
            color: white;
            font-size: 9px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .kop-nomor .no {
            font-size: 9px;
            color: #64748b;
            margin-top: 4px;
        }

        /* ── JUDUL ── */
        .title-section {
            text-align: center;
            margin: 14px 0 12px;
        }
        .title-section h2 {
            font-size: 14px;
            font-weight: 900;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .title-section .underline {
            width: 60px;
            height: 3px;
            background: #4f46e5;
            margin: 5px auto 0;
            border-radius: 2px;
        }

        /* ── INFO SISWA ── */
        .info-box {
            background: #f8faff;
            border: 1px solid #e0e7ff;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 14px;
        }
        .info-row {
            display: flex;
            gap: 0;
            margin-bottom: 5px;
        }
        .info-row:last-child { margin-bottom: 0; }
        .info-label {
            width: 100px;
            font-size: 10px;
            color: #64748b;
            font-weight: 600;
            flex-shrink: 0;
        }
        .info-sep {
            width: 14px;
            color: #94a3b8;
            flex-shrink: 0;
            text-align: center;
        }
        .info-value {
            font-size: 10px;
            color: #1e293b;
            font-weight: 700;
            flex: 1;
        }

        /* ── TABEL PEMBAYARAN ── */
        table.detail {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        table.detail thead tr {
            background: #4f46e5;
            color: white;
        }
        table.detail thead th {
            padding: 8px 10px;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }
        table.detail thead th.right { text-align: right; }
        table.detail tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }
        table.detail tbody tr:nth-child(even) {
            background: #f8faff;
        }
        table.detail tbody td {
            padding: 7px 10px;
            font-size: 10px;
            color: #334155;
        }
        table.detail tbody td.right {
            text-align: right;
            font-weight: 700;
            color: #4f46e5;
        }

        /* ── TOTAL ── */
        .total-section {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }
        .total-section .label {
            font-size: 10px;
            font-weight: 700;
            opacity: 0.85;
        }
        .total-section .amount {
            font-size: 16px;
            font-weight: 900;
            letter-spacing: -0.5px;
        }

        /* ── STATUS LUNAS WATERMARK ── */
        .lunas-badge {
            text-align: center;
            margin-bottom: 12px;
        }
        .lunas-badge span {
            display: inline-block;
            border: 2.5px solid #10b981;
            color: #059669;
            font-size: 18px;
            font-weight: 900;
            padding: 4px 20px;
            border-radius: 6px;
            transform: rotate(-5deg);
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        /* ── TANDA TANGAN ── */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 12px;
        }
        .sig-box {
            text-align: center;
            width: 45%;
        }
        .sig-box .sig-title {
            font-size: 9.5px;
            color: #64748b;
            margin-bottom: 2px;
        }
        .sig-box .sig-name {
            margin-top: 36px;
            font-size: 10px;
            font-weight: 700;
            color: #1e293b;
            border-top: 1px solid #334155;
            padding-top: 4px;
        }

        /* ── FOOTER ── */
        .footer-note {
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            margin-top: 12px;
            border-top: 1px solid #f1f5f9;
            padding-top: 8px;
        }

        /* ── PEMISAH ANTAR KUITANSI ── */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@foreach($pembayarans->groupBy(fn($p) => $p->siswa_id . '_' . $p->tahun) as $groupKey => $group)
@php
    $bulanNamesArr = [
        1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
        5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
        9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
    ];
    $firstItem  = $group->first();
    $siswa      = $firstItem->siswa;
    $totalNetto = $group->sum('netto_bayar');
    $isLast     = $loop->last;
    $tglBayarStr = $firstItem->tanggal_bayar
        ? $firstItem->tanggal_bayar->format('d') . ' ' .
          ($bulanNamesArr[(int)$firstItem->tanggal_bayar->format('m')] ?? '') . ' ' .
          $firstItem->tanggal_bayar->format('Y')
        : '-';
@endphp

<div class="{{ !$isLast ? 'page-break' : '' }}">

    {{-- ── KOP SURAT ── --}}
    <div class="kop">
        <div class="kop-logo">
            @if($logoPath)
                <img src="{{ $logoPath }}" alt="Logo Sekolah">
            @else
                <div class="kop-logo-text">
                    {{ strtoupper(substr($namaSekolah, 0, 1)) }}
                </div>
            @endif
        </div>
        <div class="kop-info">
            <h1>{{ $namaSekolah }}</h1>
            @if($alamat)
                <p>{{ $alamat }}</p>
            @endif
            @if($telepon)
                <p>Telp: {{ $telepon }}</p>
            @endif
        </div>
        <div class="kop-nomor">
            <div class="badge">Kuitansi Resmi</div>
            <div class="no">No: {{ $nomorKuitansi }}</div>
            <div class="no">{{ $tanggalCetak }}</div>
        </div>
    </div>

    {{-- ── JUDUL ── --}}
    <div class="title-section">
        <h2>Tanda Terima Pembayaran SPP</h2>
        <div class="underline"></div>
    </div>

    {{-- ── INFO SISWA ── --}}
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Nama Siswa</span>
            <span class="info-sep">:</span>
            <span class="info-value">{{ $siswa?->user?->name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">NISN / NIS</span>
            <span class="info-sep">:</span>
            <span class="info-value">{{ $siswa?->nisn ?? '-' }} / {{ $siswa?->nis ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Kelas</span>
            <span class="info-sep">:</span>
            <span class="info-value">{{ $siswa?->kelas?->nama_kelas ?? 'Belum Ada Kelas' }} ({{ $siswa?->jenjang ?? '-' }})</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tahun Tagihan</span>
            <span class="info-sep">:</span>
            <span class="info-value">{{ $firstItem->tahun }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Bayar</span>
            <span class="info-sep">:</span>
            <span class="info-value">{{ $tglBayarStr }}</span>
        </div>
    </div>

    {{-- ── DETAIL PEMBAYARAN ── --}}
    <table class="detail">
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Kategori Biaya</th>
                <th>Keterangan</th>
                <th class="right" style="width:100px">Nominal</th>
                <th class="right" style="width:100px">Diskon</th>
                <th class="right" style="width:110px">Dibayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($group as $i => $p)
            @php
                $keterangan = $p->bulan
                    ? ($bulanNamesArr[$p->bulan] ?? '-') . ' ' . $p->tahun
                    : 'Sekali Bayar';
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->spp?->kategori ?? '-' }}</td>
                <td>{{ $keterangan }}</td>
                <td class="right">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                <td class="right">
                    @if($p->potongan > 0)
                        Rp {{ number_format($p->potongan, 0, ',', '.') }}
                    @else
                        <span style="color:#94a3b8">—</span>
                    @endif
                </td>
                <td class="right">Rp {{ number_format($p->netto_bayar, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── TOTAL ── --}}
    <div class="total-section">
        <span class="label">TOTAL YANG DITERIMA</span>
        <span class="amount">Rp {{ number_format($totalNetto, 0, ',', '.') }}</span>
    </div>

    {{-- ── STEMPEL LUNAS ── --}}
    <div class="lunas-badge">
        <span>✓ LUNAS</span>
    </div>

    {{-- ── TANDA TANGAN ── --}}
    <div class="signature-section">
        <div class="sig-box">
            <div class="sig-title">Wali Murid / Siswa,</div>
            <div class="sig-name">( {{ $siswa?->user?->name ?? '________________________' }} )</div>
        </div>
        <div class="sig-box">
            <div class="sig-title">Petugas Tata Usaha,</div>
            <div class="sig-name">( {{ $firstItem->user?->name ?? $petugas }} )</div>
        </div>
    </div>

    {{-- ── FOOTER NOTE ── --}}
    <div class="footer-note">
        Kuitansi ini dicetak secara otomatis oleh sistem pada {{ now()->format('d/m/Y H:i:s') }}.
        Simpan dokumen ini sebagai bukti pembayaran yang sah.
    </div>

</div>
@endforeach

</body>
</html>
