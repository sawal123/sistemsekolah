<?php

namespace App\Exports;

use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\Mapel;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FormatNilaiExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $kelas_id;
    protected $mapel_id;
    protected $tahun_ajaran_id;
    protected $mapelName;
    protected $kelasName;

    public function __construct($kelas_id, $mapel_id, $tahun_ajaran_id)
    {
        $this->kelas_id = $kelas_id;
        $this->mapel_id = $mapel_id;
        $this->tahun_ajaran_id = $tahun_ajaran_id;
        $this->mapelName = Mapel::find($mapel_id)?->nama_mapel ?: 'Mapel';
        $this->kelasName = Kelas::find($kelas_id)?->nama_kelas ?: 'Kelas';
    }

    public function collection()
    {
        return Siswa::with('user')
            ->where('kelas_id', $this->kelas_id)
            ->get()
            ->sortBy('user.name')
            ->map(function ($siswa) {
                // Attach nilai
                $nilai = Nilai::where('siswa_id', $siswa->id)
                              ->where('mapel_id', $this->mapel_id)
                              ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
                              ->first();
                $siswa->nilai = $nilai;
                return $siswa;
            });
    }

    public function map($siswa): array
    {
        $harian = $siswa->nilai->nilai_harian ?? [];
        return [
            $siswa->id,
            $siswa->nisn,
            $siswa->user->name,
            $harian['t1'] ?? '',
            $harian['t2'] ?? '',
            $harian['t3'] ?? '',
            $siswa->nilai->pts ?? '',
            $siswa->nilai->pas ?? '',
            $siswa->nilai->skor_remedial ?? ''
        ];
    }

    public function headings(): array
    {
        return [
            'ID SISWA',
            'NISN',
            'NAMA SISWA',
            'TUGAS 1',
            'TUGAS 2',
            'TUGAS 3',
            'PTS',
            'PAS',
            'REMEDIAL',
        ];
    }

    public function title(): string
    {
        return substr('Nilai ' . $this->kelasName, 0, 31);
    }
}
