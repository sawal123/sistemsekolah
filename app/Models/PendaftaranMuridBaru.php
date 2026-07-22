<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranMuridBaru extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'nilai_rapor' => 'array',
    ];

    public function gelombang()
    {
        return $this->belongsTo(PpdbGelombang::class, 'ppdb_gelombang_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function getDokumenLengkapAttribute(): int
    {
        if ($this->document_upload_mode === 'gabungan') {
            return $this->dokumen_gabungan ? 1 : 0;
        }

        return collect([
            $this->ijazah_skl,
            $this->kartu_keluarga,
            $this->akta_kelahiran,
            $this->ktp_ayah,
            $this->ktp_ibu,
            $this->buku_rapor,
            $this->pas_foto,
        ])->filter()->count();
    }

    public function getTotalDokumenAttribute(): int
    {
        return $this->document_upload_mode === 'gabungan' ? 1 : 7;
    }
}
