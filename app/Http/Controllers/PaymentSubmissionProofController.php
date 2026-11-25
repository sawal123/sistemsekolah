<?php

namespace App\Http\Controllers;

use App\Models\PaymentSubmission;
use Illuminate\Support\Facades\Storage;

class PaymentSubmissionProofController extends Controller
{
    public function show(PaymentSubmission $submission)
    {
        $user = request()->user();
        $isOwner = $user && $submission->submitted_by === $user->id;
        $isAuthorizedAdmin = $user && $user->isAnyAdmin()
            && \App\Support\UnitContext::canAccessUnit($user, $submission->siswa?->unit_id);

        abort_unless($isOwner || $isAuthorizedAdmin, 403);
        abort_unless(Storage::disk('local')->exists($submission->bukti_path), 404);

        return Storage::disk('local')->response($submission->bukti_path, basename($submission->bukti_path), [
            'Content-Disposition' => 'inline; filename="bukti-pembayaran"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
