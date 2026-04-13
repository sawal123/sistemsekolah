<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $guarded = ['id'];

    // Casting JSON secara otomatis ke array
    protected $casts = [
        'nilai_harian' => 'array',
        'pts' => 'decimal:2',
        'pas' => 'decimal:2',
        'skor_remedial' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // ----------------------------------------------------
    // ACCESORS & MUTATORS (Software Architecture Standard)
    // ----------------------------------------------------

    /**
     * Hitung Rata-rata dari nilai_harian JSON
     */
    public function getRataHarianAttribute()
    {
        $harian = $this->nilai_harian ?? [];
        if (empty($harian)) return 0;
        
        $total = 0; $count = 0;
        foreach ($harian as $val) {
            if (is_numeric($val)) {
                $total += $val;
                $count++;
            }
        }
        return $count > 0 ? round($total / $count, 2) : 0;
    }

    /**
     * Hitung Nilai Akhir Berdasarkan Bobot Mapel
     */
    public function getNilaiAkhirAttribute()
    {
        $mapel = $this->mapel;
        if (!$mapel) return 0;

        $bobotHarian = $mapel->bobot_harian ?? 40;
        $bobotPts = $mapel->bobot_pts ?? 30;
        $bobotPas = $mapel->bobot_pas ?? 30;

        $rataHarian = $this->rata_harian;
        $pts = $this->pts ?? 0;
        $pas = $this->pas ?? 0;

        $nilai = ($rataHarian * ($bobotHarian / 100)) + 
                 ($pts * ($bobotPts / 100)) + 
                 ($pas * ($bobotPas / 100));

        // Logika Remedial Pintar
        // Jika siswa mengikuti remedial dan nilainya lebih baik, maka ambil remedial.
        // Tapi perlakuan remedial lazimnya hanya mentok sampai KKM (tidak melebihi).
        if ($this->skor_remedial && $this->skor_remedial > $nilai) {
            $nilai = min($this->skor_remedial, $mapel->kkm ?? 75);
        }

        return round($nilai, 2);
    }

    /**
     * Ambil Predikat A/B/C/D
     */
    public function getPredikatAttribute()
    {
        $na = $this->nilai_akhir;
        $kkm = $this->mapel->kkm ?? 75;

        if ($na < $kkm) return 'D';
        
        $interval = (100 - $kkm) / 3;
        if ($na >= $kkm && $na < $kkm + $interval) return 'C';
        if ($na >= $kkm + $interval && $na < $kkm + ($interval * 2)) return 'B';
        return 'A';
    }

    /**
     * Ambil Capaian Deskripsi Rapors Otomatis
     */
    public function getDeskripsiAttribute()
    {
        $predikat = $this->predikat;
        $nama = $this->siswa->nama_lengkap ?? 'Siswa';
        $mapel = $this->mapel->nama_mapel ?? 'Mata Pelajaran';

        switch($predikat) {
            case 'A': return "Ananda $nama sangat menonjol dalam memahami dan menguasai seluruh kompetensi dasar pada mata pelajaran $mapel.";
            case 'B': return "Ananda $nama baik dalam menguasai kompetensi dasar $mapel, terus pertahankan dan tingkatkan cara belajarnya.";
            case 'C': return "Ananda $nama cukup menguasai kompetensi dasar $mapel, namun perlu bimbingan lebih lanjut pada beberapa materi.";
            case 'D': return "Ananda $nama belum mencapai batas kriteria ketuntasan $mapel, membutuhkan remedial dan bimbingan intensif.";
            default: return "-";
        }
    }
}
