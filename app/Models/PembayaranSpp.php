<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranSpp extends Model
{
    protected $guarded = ['id'];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function spp() { return $this->belongsTo(Spp::class); }
}
