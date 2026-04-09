<?php

namespace App\Exports;

use App\Models\Mapel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MapelExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Mapel::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kode Mapel',
            'Nama Mata Pelajaran',
            'Kelompok',
            'Jenjang',
        ];
    }

    public function map($mapel): array
    {
        return [
            $mapel->id,
            $mapel->kode_mapel,
            $mapel->nama_mapel,
            $mapel->kelompok,
            $mapel->jenjang,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
