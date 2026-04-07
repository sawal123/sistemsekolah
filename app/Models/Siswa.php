<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $guarded = ['id'];

    public function user() { return $this->belongsTo(User::class); }
    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function absensis() { return $this->hasMany(Absensi::class); }
    public function nilais() { return $this->hasMany(Nilai::class); }
    public function rapors() { return $this->hasMany(Rapor::class); }
    public function pembayaranSpps() { return $this->hasMany(PembayaranSpp::class); }
    public function kegiatanAlumnis() { return $this->hasMany(KegiatanAlumni::class); }
}
