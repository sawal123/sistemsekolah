<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function wali_kelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function rapors()
    {
        return $this->hasMany(Rapor::class);
    }
}
