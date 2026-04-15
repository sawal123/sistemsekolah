<?php

namespace App\Exports;

use App\Models\KegiatanAlumni;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AlumniExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return KegiatanAlumni::with('siswa.user')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Alumni',
            'NISN',
            'Tahun Lulus',
            'Jenis Kegiatan',
            'Nama Instansi / Kampus',
            'Posisi / Jurusan',
            'Tahun Mulai',
            'Deskripsi',
            'Status Verifikasi'
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->siswa->user->name,
            $row->siswa->nisn,
            $row->siswa->tahun_lulus,
            str_replace('_', ' ', $row->jenis_kegiatan),
            $row->nama_instansi,
            $row->posisi_jurusan,
            $row->tahun_mulai,
            $row->deskripsi,
            $row->status_verifikasi
        ];
    }
}
