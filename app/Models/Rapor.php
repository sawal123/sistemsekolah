<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rapor extends Model
{
    protected $guarded = ['id'];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function kelas() { return $this->belongsTo(Kelas::class); }
}
