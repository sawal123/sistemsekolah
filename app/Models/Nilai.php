<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $guarded = ['id'];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function mapel() { return $this->belongsTo(Mapel::class); }
    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
}
