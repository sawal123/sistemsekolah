<?php

namespace App\Imports;

use App\Models\Nilai;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FormatNilaiImport implements ToCollection, WithHeadingRow
{
    protected $mapel_id;
    protected $tahun_ajaran_id;

    public function __construct($mapel_id, $tahun_ajaran_id)
    {
        $this->mapel_id = $mapel_id;
        $this->tahun_ajaran_id = $tahun_ajaran_id;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Kolom diubah otomatis oleh paket menjadi snake_case
            $siswaId = $row['id_siswa'] ?? null;
            if (!$siswaId) continue;

            $n = Nilai::firstOrNew([
                'siswa_id' => $siswaId,
                'mapel_id' => $this->mapel_id,
                'tahun_ajaran_id' => $this->tahun_ajaran_id,
            ]);

            $harian = $n->nilai_harian ?? [];
            
            if (isset($row['tugas_1']) && $row['tugas_1'] !== '') $harian['t1'] = (float) $row['tugas_1'];
            if (isset($row['tugas_2']) && $row['tugas_2'] !== '') $harian['t2'] = (float) $row['tugas_2'];
            if (isset($row['tugas_3']) && $row['tugas_3'] !== '') $harian['t3'] = (float) $row['tugas_3'];

            $n->nilai_harian = $harian;
            
            if (isset($row['pts']) && $row['pts'] !== '') $n->pts = (float) $row['pts'];
            if (isset($row['pas']) && $row['pas'] !== '') $n->pas = (float) $row['pas'];
            if (isset($row['remedial']) && $row['remedial'] !== '') $n->skor_remedial = (float) $row['remedial'];

            $n->save();
        }
    }
}
