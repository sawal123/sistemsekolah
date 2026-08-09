<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class)->withTrashed();
    }

    public function anggotaRombels()
    {
        return $this->hasMany(AnggotaRombel::class);
    }

    public function rombels()
    {
        return $this->belongsToMany(Rombel::class, 'anggota_rombels')
            ->withPivot(['status', 'tanggal_masuk', 'tanggal_keluar'])
            ->withTimestamps();
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }

    public function rapors()
    {
        return $this->hasMany(Rapor::class);
    }

    public function pembayaranSpps()
    {
        return $this->hasMany(PembayaranSpp::class);
    }

    /**
     * Tagihan (invoice) siswa — sistem keuangan jangka panjang.
     */
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function kegiatanAlumnis()
    {
        return $this->hasMany(KegiatanAlumni::class);
    }

    public function scopeInKelasPadaTahunAjaran($query, $kelasId, $tahunAjaranId)
    {
        if (! $kelasId) {
            return $query;
        }

        if (! $tahunAjaranId) {
            return $query->where('kelas_id', $kelasId);
        }

        return $query->whereHas('anggotaRombels.rombel', function ($q) use ($kelasId, $tahunAjaranId) {
            $q->where('kelas_id', $kelasId)
                ->where('tahun_ajaran_id', $tahunAjaranId);
        });
    }

    public function kelasPadaTahunAjaran($tahunAjaranId): ?Kelas
    {
        if (! $tahunAjaranId) {
            return $this->kelas;
        }

        return $this->rombelPadaTahunAjaran($tahunAjaranId)?->kelas;
    }

    public function rombelPadaTahunAjaran($tahunAjaranId): ?Rombel
    {
        if (! $tahunAjaranId) {
            return null;
        }

        return $this->anggotaRombels()
            ->with('rombel.kelas')
            ->whereHas('rombel', fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->latest('id')
            ->first()
            ?->rombel;
    }
}
