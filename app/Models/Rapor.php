<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rapor extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'ekskul' => 'array',
        'prestasi' => 'array',
        'karakter' => 'array',
        'is_locked' => 'boolean',
    ];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function kelas() { return $this->belongsTo(Kelas::class); }

    // Mutator: Kalkulasi Rata-Rata on the fly (Siswa.nilais harus di-eager load)
    public function getRataRataNilaiAttribute()
    {
        if (!$this->siswa || !$this->siswa->relationLoaded('nilais')) {
            return 0; // Menghindari N+1 Query jika relasi nilais belum ditarik
        }

        $nilaisSiswa = $this->siswa->nilais->where('tahun_ajaran_id', $this->tahun_ajaran_id);
        
        if ($nilaisSiswa->isEmpty()) {
            return 0;
        }

        // Kalkulasi sum(nilai_akhir) / total_mapel
        $total = $nilaisSiswa->sum(function($nilai) {
            return $nilai->nilai_akhir;
        });

        return round($total / $nilaisSiswa->count(), 2);
    }
}
