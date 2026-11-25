<?php

namespace App\Livewire\Admin\Keuangan;

use App\Models\PaymentSubmission;
use App\Services\PaymentSubmissionService;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Verifikasi Pembayaran')]
class PaymentSubmissionReviewIndex extends Component
{
    use WithPagination;

    public string $filter = 'Menunggu Verifikasi';
    public ?int $rejectingId = null;
    public string $adminNote = '';

    public function approve(int $id, PaymentSubmissionService $service): void
    {
        $submission = PaymentSubmission::with('siswa')->findOrFail($id);
        \App\Support\UnitContext::authorizeUnit(auth()->user(), $submission->siswa?->unit_id);

        try {
            $service->approve($id, auth()->user());
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Bukti disetujui dan transaksi telah dicatat.']);
        } catch (ValidationException $exception) {
            $this->dispatch('notify', ['type' => 'error', 'message' => collect($exception->errors())->flatten()->first()]);
        }
    }

    public function openReject(int $id): void
    {
        $submission = PaymentSubmission::with('siswa')->findOrFail($id);
        \App\Support\UnitContext::authorizeUnit(auth()->user(), $submission->siswa?->unit_id);

        $this->rejectingId = $id;
        $this->adminNote = '';
        $this->dispatch('open-modal', 'reject-payment-submission');
    }

    public function reject(PaymentSubmissionService $service): void
    {
        $this->validate(['adminNote' => ['required', 'string', 'min:5', 'max:500']]);
        $submission = PaymentSubmission::with('siswa')->findOrFail((int) $this->rejectingId);
        \App\Support\UnitContext::authorizeUnit(auth()->user(), $submission->siswa?->unit_id);

        try {
            $service->reject((int) $this->rejectingId, auth()->user(), $this->adminNote);
            $this->closeReject();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Pengajuan ditolak; alasannya dapat dilihat siswa.']);
        } catch (ValidationException $exception) {
            $this->addError('adminNote', collect($exception->errors())->flatten()->first());
        }
    }

    public function closeReject(): void
    {
        $this->reset('rejectingId', 'adminNote');
        $this->resetValidation();
        $this->dispatch('close-modal', 'reject-payment-submission');
    }

    public function render()
    {
        $submissions = PaymentSubmission::with(['siswa.user', 'tagihan', 'reviewer'])
            ->whereHas('siswa', fn ($q) => $q->forCurrentUser())
            ->when($this->filter !== 'Semua', fn ($query) => $query->where('status', $this->filter))
            ->latest()->paginate(15);

        return view('livewire.admin.keuangan.payment-submission-review-index', compact('submissions'));
    }
}
