<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSubmission extends Model
{
    public const PENDING = 'Menunggu Verifikasi';
    public const APPROVED = 'Disetujui';
    public const REJECTED = 'Ditolak';
    public const CANCELLED = 'Dibatalkan';

    protected $guarded = ['id'];

    protected $casts = [
        'nominal' => 'float',
        'tanggal_transfer' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function tagihan(): BelongsTo { return $this->belongsTo(Tagihan::class); }
    public function siswa(): BelongsTo { return $this->belongsTo(Siswa::class)->withTrashed(); }
    public function submitter(): BelongsTo { return $this->belongsTo(User::class, 'submitted_by'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function pembayaran(): BelongsTo { return $this->belongsTo(Pembayaran::class); }

    public function scopePending($query)
    {
        return $query->where('status', self::PENDING);
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === self::APPROVED;
    }
}
