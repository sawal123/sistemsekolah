<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Collection;

class StudentPortalService
{
    public function activeAcademicYear(): ?TahunAjaran
    {
        return TahunAjaran::where('is_active', true)->first();
    }

    public function activeRombel(Siswa $siswa, ?TahunAjaran $tahunAjaran): mixed
    {
        if (! $tahunAjaran) {
            return null;
        }

        return $siswa->anggotaRombels()
            ->where('status', 'Aktif')
            ->whereNull('tanggal_keluar')
            ->whereHas('rombel', fn ($query) => $query->where('tahun_ajaran_id', $tahunAjaran->id))
            ->with(['rombel.kelas.wali_kelas.user', 'rombel.kelas.jurusan'])
            ->latest('id')
            ->first()
            ?->rombel;
    }

    public function schedules(?int $kelasId, ?int $tahunAjaranId): Collection
    {
        if (! $kelasId || ! $tahunAjaranId) {
            return collect();
        }

        return Jadwal::with(['mapel', 'guru.user', 'ruangan'])
            ->where('kelas_id', $kelasId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 ELSE 7 END")
            ->orderBy('jam_mulai')
            ->get();
    }
}
