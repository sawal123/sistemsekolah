<?php

namespace App\Imports;

use App\Models\Mapel;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class MapelImport implements ToModel, WithHeadingRow, WithUpserts, WithValidation
{
    /**
     * @return Model|null
     */
    public function model(array $row)
    {
        return new Mapel([
            'kode_mapel' => $row['kode_mapel'],
            'nama_mapel' => $row['nama_mata_pelajaran'],
            'kelompok' => $row['kelompok'] ?? 'Nasional',
            'jenjang' => $row['jenjang'] ?? 'Umum',
        ]);
    }

    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'kode_mapel';
    }

    public function rules(): array
    {
        return [
            'kode_mapel' => 'required|string',
            'nama_mata_pelajaran' => 'required|string',
            'kelompok' => 'nullable|in:Nasional,Kewilayahan,Peminatan,Mulok',
            'jenjang' => 'nullable|in:SMP,SMA,Umum',
        ];
    }
}
