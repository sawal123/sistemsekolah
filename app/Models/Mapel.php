<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $guarded = ['id'];

    public function jadwals() { return $this->hasMany(Jadwal::class); }
    public function nilais() { return $this->hasMany(Nilai::class); }
}
