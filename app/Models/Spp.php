<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    protected $guarded = ['id'];

    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function pembayaranSpps() { return $this->hasMany(PembayaranSpp::class); }
}
