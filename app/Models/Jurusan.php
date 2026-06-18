<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function spps()
    {
        return $this->hasMany(Spp::class);
    }

    public function mapels()
    {
        return $this->hasMany(Mapel::class);
    }

    public function pendaftarans()
    {
        return $this->hasMany(PendaftaranMuridBaru::class);
    }
}
