<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Mata Pelajaran</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin-bottom: 5px;">DAFTAR MATA PELAJARAN</h1>
        <p style="margin-top: 5px;">SMA Nusantara — Sistem Manajemen Sekolah</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Kode Mapel</th>
                <th>Nama Mata Pelajaran</th>
                <th>Kelompok</th>
                <th>Jenjang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mapels as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->kode_mapel }}</td>
                    <td>{{ $item->nama_mapel }}</td>
                    <td>{{ $item->kelompok }}</td>
                    <td>{{ $item->jenjang }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
