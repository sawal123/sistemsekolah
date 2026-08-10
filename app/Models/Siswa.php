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

    /**
     * Scope: siswa yang PERNAH berada di kelas pada tahun ajaran tertentu (histori).
     * Cocok untuk: laporan historis, transkrip nilai lama.
     */
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

    /**
     * Scope: siswa yang SAAT INI AKTIF di kelas pada tahun ajaran tertentu.
     * Filter: anggota_rombels.status = Aktif, tanggal_keluar IS NULL, siswa.status = Aktif.
     * Cocok untuk: Manajemen Nilai, e-Rapor, roster aktif.
     */
    public function scopeAktifDiKelasPadaTahunAjaran($query, $kelasId, $tahunAjaranId)
    {
        if (! $kelasId || ! $tahunAjaranId) {
            return $query->where('kelas_id', $kelasId);
        }

        return $query->whereHas('anggotaRombels', function ($q) use ($kelasId, $tahunAjaranId) {
            $q->where('status', 'Aktif')
                ->whereNull('tanggal_keluar')
                ->whereHas('rombel', function ($r) use ($kelasId, $tahunAjaranId) {
                    $r->where('kelas_id', $kelasId)
                        ->where('tahun_ajaran_id', $tahunAjaranId);
                });
        });
    }

    /**
     * Scope: siswa yang berada di kelas pada TANGGAL tertentu.
     * Berdasarkan: tanggal_masuk <= tanggal AND (tanggal_keluar IS NULL OR tanggal_keluar >= tanggal).
     * Cocok untuk: rekap absensi harian, laporan per tanggal.
     */
    public function scopeInKelasPadaTanggal($query, $kelasId, $tanggal)
    {
        if (! $kelasId || ! $tanggal) {
            return $query;
        }

        $tanggalStr = \Carbon\Carbon::parse($tanggal)->toDateString();

        return $query->whereHas('anggotaRombels', function ($q) use ($kelasId, $tanggalStr) {
            $q->where('tanggal_masuk', '<=', $tanggalStr)
                ->where(function ($sub) use ($tanggalStr) {
                    $sub->whereNull('tanggal_keluar')
                        ->orWhere('tanggal_keluar', '>=', $tanggalStr);
                })
                ->whereHas('rombel', function ($r) use ($kelasId) {
                    $r->where('kelas_id', $kelasId);
                });
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
            ->whereHas('rombel', fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->latest('id')
            ->first()
            ?->rombel;
    }
}
