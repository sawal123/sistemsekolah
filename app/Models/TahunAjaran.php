<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $guarded = ['id'];

    public function nilais() { return $this->hasMany(Nilai::class); }
    public function rapors() { return $this->hasMany(Rapor::class); }
    public function spps() { return $this->hasMany(Spp::class); }
}
