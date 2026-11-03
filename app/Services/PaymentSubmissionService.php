<?php

namespace App\Services;

use App\Models\PaymentSubmission;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentSubmissionService
{
    public function approve(int $submissionId, User $reviewer, ?string $note = null): PaymentSubmission
    {
        return DB::transaction(function () use ($submissionId, $reviewer, $note) {
            $submission = PaymentSubmission::lockForUpdate()->findOrFail($submissionId);

            if ($submission->status !== PaymentSubmission::PENDING || $submission->pembayaran_id) {
                throw ValidationException::withMessages(['submission' => 'Pengajuan ini sudah diproses.']);
            }

            $tagihan = Tagihan::lockForUpdate()->findOrFail($submission->tagihan_id);
            if ($tagihan->siswa_id !== $submission->siswa_id || $tagihan->status === 'Dibatalkan') {
                throw ValidationException::withMessages(['submission' => 'Tagihan tidak valid untuk pengajuan ini.']);
            }

            $tagihan->unsetRelation('pembayarans');
            if ($submission->nominal > $tagihan->sisa_tagihan) {
                throw ValidationException::withMessages(['submission' => 'Nominal pengajuan melebihi sisa tagihan terbaru.']);
            }

            $payment = $tagihan->bayar(
                $submission->nominal,
                $submission->metode,
                $reviewer->id,
                'Disetujui dari bukti pembayaran #' . $submission->id,
                $submission->tanggal_transfer,
            );

            $submission->update([
                'status' => PaymentSubmission::APPROVED,
                'catatan_admin' => $note ?: null,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'pembayaran_id' => $payment->id,
            ]);

            return $submission->fresh(['tagihan', 'pembayaran', 'reviewer']);
        });
    }

    public function reject(int $submissionId, User $reviewer, string $note): PaymentSubmission
    {
        return DB::transaction(function () use ($submissionId, $reviewer, $note) {
            $submission = PaymentSubmission::lockForUpdate()->findOrFail($submissionId);

            if ($submission->status !== PaymentSubmission::PENDING) {
                throw ValidationException::withMessages(['submission' => 'Pengajuan ini sudah diproses.']);
            }

            $submission->update([
                'status' => PaymentSubmission::REJECTED,
                'catatan_admin' => $note,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            return $submission->fresh();
        });
    }
}
