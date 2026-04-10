<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            line-height: 1.5;
            padding: 36px;
            background: #ffffff;
            font-size: 12px;
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 28px;
            background: #fff;
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #0f172a;
            padding-bottom: 18px;
            margin-bottom: 22px;
        }

        .logo-wrap,
        .school-wrap {
            display: table-cell;
            vertical-align: middle;
        }

        .logo-wrap {
            width: 90px;
        }

        .logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }

        .school-name {
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin: 0;
            line-height: 1.2;
        }

        .school-address,
        .school-contact {
            margin: 4px 0 0;
            color: #475569;
            font-size: 11px;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin: 10px 0 26px;
            color: #0f172a;
        }

        .profile-section {
            display: table;
            width: 100%;
        }

        .photo-col,
        .content-col {
            display: table-cell;
            vertical-align: top;
        }

        .photo-col {
            width: 150px;
            padding-right: 22px;
        }

        .photo-box {
            width: 130px;
            height: 170px;
            border: 2px solid #cbd5e1;
            border-radius: 10px;
            overflow: hidden;
            background: #f8fafc;
            text-align: center;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #94a3b8;
            font-weight: bold;
        }

        .section {
            margin-bottom: 18px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .section-title {
            background: #f8fafc;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f172a;
            border-left: 4px solid #0f172a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        td {
            padding: 8px 12px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .label {
            width: 180px;
            color: #475569;
            font-weight: 600;
        }

        .value {
            color: #0f172a;
            font-weight: 700;
        }

        .footer {
            margin-top: 36px;
            text-align: right;
        }

        .signature-box {
            display: inline-block;
            width: 250px;
            text-align: center;
        }

        .signature-role {
            margin-bottom: 60px;
            font-weight: 700;
        }

        .signature-name {
            font-weight: 800;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="header">
            <div class="logo-wrap">
                @if($logo && file_exists($logo))
                    <img src="data:image/{{ pathinfo($logo, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($logo)) }}"
                        class="logo">
                @endif
            </div>
            <div class="school-wrap">
                <h1 class="school-name">{{ $settings['school_name'] ?? 'SEKOLAH ANDA' }}</h1>
                <p class="school-address">{{ $settings['school_address'] ?? 'Alamat Belum Diatur' }}</p>
                <p class="school-contact">Telp: {{ $settings['school_phone'] ?? '-' }} | Web:
                    {{ $settings['school_website'] ?? '-' }}
                </p>
            </div>
        </div>

        <h2 class="title">Biodata Peserta Didik</h2>

        <div class="profile-section">
            <div class="photo-col">
                <div class="photo-box">
                    @if($siswa->foto && file_exists(storage_path('app/public/' . $siswa->foto)))
                        <img
                            src="data:image/{{ pathinfo($siswa->foto, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents(storage_path('app/public/' . $siswa->foto))) }}">
                    @else
                        <div class="photo-placeholder">FOTO 3x4</div>
                    @endif
                </div>
            </div>

            <div class="content-col">
                <div class="section">
                    <div class="section-title">A. Informasi Akademik</div>
                    <table>
                        <tr>
                            <td class="label">Nama Lengkap</td>
                            <td class="value">: {{ $siswa->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="label">NIS</td>
                            <td class="value">: {{ $siswa->nis }}</td>
                        </tr>
                        <tr>
                            <td class="label">NISN</td>
                            <td class="value">: {{ $siswa->nisn }}</td>
                        </tr>
                        <tr>
                            <td class="label">Jenjang / Kelas</td>
                            <td class="value">: {{ $siswa->jenjang }} / {{ $siswa->kelas?->nama_kelas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Status</td>
                            <td class="value">: {{ $siswa->status }}</td>
                        </tr>
                    </table>
                </div>

                <div class="section">
                    <div class="section-title">B. Keterangan Pribadi</div>
                    <table>
                        <tr>
                            <td class="label">Tempat, Tgl Lahir</td>
                            <td class="value">: {{ $siswa->tempat_lahir ?? '-' }},
                                {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d F Y') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Agama</td>
                            <td class="value">: {{ $siswa->agama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Alamat</td>
                            <td class="value">: {{ $siswa->alamat ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="section">
                    <div class="section-title">C. Data Orang Tua</div>
                    <table>
                        <tr>
                            <td class="label">Nama Ayah</td>
                            <td class="value">: {{ $siswa->nama_ayah ?? '-' }} ({{ $siswa->pekerjaan_ayah ?? '-' }})
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Nama Ibu</td>
                            <td class="value">: {{ $siswa->nama_ibu ?? '-' }} ({{ $siswa->pekerjaan_ibu ?? '-' }})</td>
                        </tr>
                        <tr>
                            <td class="label">No. Telp / WA Ortu</td>
                            <td class="value">: {{ $siswa->no_telp_ortu ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        @if(($settings['show_signature_on_print'] ?? '1') == '1')
            <div class="footer">
                <div class="signature-box">
                    <p>Dicetak Pada, {{ date('d F Y') }}</p>
                    <p class="signature-role">{{ $settings['admin_signature_role'] ?? 'Admin Sekolah' }}</p>
                    <p class="signature-name">(
                        {{ $settings['admin_signature_name'] ?? '........................................' }} )
                    </p>
                </div>
            </div>
        @endif
    </div>
</body>

</html>