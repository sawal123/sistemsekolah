<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    protected $guarded = ['id'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class)->withTrashed();
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function anggotaRombels()
    {
        return $this->hasMany(AnggotaRombel::class);
    }

    public function siswas()
    {
        return $this->belongsToMany(Siswa::class, 'anggota_rombels')
            ->withPivot(['status', 'tanggal_masuk', 'tanggal_keluar'])
            ->withTimestamps();
    }
}
