<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'tingkat_awal' => 'integer',
            'tingkat_akhir' => 'integer',
            'punya_jurusan' => 'boolean',
            'is_active' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('urutan')->orderBy('nama');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function gurus()
    {
        return $this->hasMany(Guru::class);
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function mapels()
    {
        return $this->hasMany(Mapel::class);
    }

    public function spps()
    {
        return $this->hasMany(Spp::class);
    }

    public function ruangans()
    {
        return $this->hasMany(Ruangan::class);
    }

    public function ppdbGelombangs()
    {
        return $this->hasMany(PpdbGelombang::class);
    }

    public function pendaftarans()
    {
        return $this->hasMany(PendaftaranMuridBaru::class);
    }

    public function kalenderAkademiks()
    {
        return $this->hasMany(KalenderAkademik::class);
    }
}
