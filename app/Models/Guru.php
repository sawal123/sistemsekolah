<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $guarded = ['id'];

    public function user() { return $this->belongsTo(User::class); }
    public function kelas() { return $this->hasOne(Kelas::class, 'wali_kelas_id'); }
    public function jadwals() { return $this->hasMany(Jadwal::class); }
}
