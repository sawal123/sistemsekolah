<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $guarded = ['id'];

    public function jadwal() { return $this->belongsTo(Jadwal::class); }
    public function siswa() { return $this->belongsTo(Siswa::class); }
}
