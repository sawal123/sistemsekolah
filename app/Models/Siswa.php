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

    public function keanggotaanKelas()
    {
        return $this->hasMany(KelasSiswa::class);
    }

    public function kelasPeriodik()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_siswa')
            ->withPivot(['tahun_ajaran_id', 'status', 'tanggal_mulai', 'tanggal_selesai'])
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

        return $query->whereHas('keanggotaanKelas', function ($q) use ($kelasId, $tahunAjaranId) {
            $q->where('kelas_id', $kelasId)
                ->where('tahun_ajaran_id', $tahunAjaranId);
        });
    }

    public function kelasPadaTahunAjaran($tahunAjaranId): ?Kelas
    {
        if (! $tahunAjaranId) {
            return $this->kelas;
        }

        return $this->keanggotaanKelas()
            ->with('kelas')
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->latest('id')
            ->first()
            ?->kelas;
    }
}
