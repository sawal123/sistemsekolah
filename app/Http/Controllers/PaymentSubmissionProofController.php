<?php

namespace App\Http\Controllers;

use App\Models\PaymentSubmission;
use Illuminate\Support\Facades\Storage;

class PaymentSubmissionProofController extends Controller
{
    public function show(PaymentSubmission $submission)
    {
        $user = request()->user();
        abort_unless($user && ($user->hasRole('admin') || $submission->submitted_by === $user->id), 403);
        abort_unless(Storage::disk('local')->exists($submission->bukti_path), 404);

        return Storage::disk('local')->response($submission->bukti_path, basename($submission->bukti_path), [
            'Content-Disposition' => 'inline; filename="bukti-pembayaran"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
