<?php

namespace App\Livewire\Student;

use App\Models\PaymentSubmission;
use App\Models\Tagihan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.student')]
#[Title('Pembayaran')]
class StudentPaymentIndex extends Component
{
    use WithFileUploads;

    public ?int $tagihanId = null;
    public float|string $nominal = '';
    public string $tanggalTransfer = '';
    public string $metode = 'Transfer Bank';
    public string $catatanSiswa = '';
    public $bukti;
    public bool $isFormOpen = false;

    public function openForm(int $tagihanId): void
    {
        $tagihan = $this->ownedTagihan($tagihanId);
        abort_if(in_array($tagihan->status, ['Lunas', 'Dibatalkan'], true), 422);
        abort_if(PaymentSubmission::pending()->where('tagihan_id', $tagihan->id)->exists(), 422, 'Tagihan ini sedang menunggu verifikasi.');

        $this->resetForm();
        $this->tagihanId = $tagihan->id;
        $this->nominal = $tagihan->sisa_tagihan;
        $this->tanggalTransfer = today()->toDateString();
        $this->isFormOpen = true;
    }

    public function submit(): void
    {
        $this->validate([
            'tagihanId' => ['required', 'integer'],
            'nominal' => ['required', 'numeric', 'min:1'],
            'tanggalTransfer' => ['required', 'date', 'before_or_equal:today'],
            'metode' => ['required', 'in:Transfer Bank,QRIS'],
            'bukti' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'catatanSiswa' => ['nullable', 'string', 'max:500'],
        ]);

        $tagihan = $this->ownedTagihan($this->tagihanId);
        abort_if($tagihan->status === 'Dibatalkan' || $tagihan->sisa_tagihan <= 0, 422, 'Tagihan tidak dapat diajukan.');
        if ((float) $this->nominal > $tagihan->sisa_tagihan) {
            $this->addError('nominal', 'Nominal tidak boleh melebihi sisa tagihan.');
            return;
        }
        if (PaymentSubmission::pending()->where('tagihan_id', $tagihan->id)->exists()) {
            $this->addError('tagihanId', 'Tagihan ini sedang menunggu verifikasi.');
            return;
        }

        PaymentSubmission::create([
            'tagihan_id' => $tagihan->id,
            'siswa_id' => auth()->user()->siswa->id,
            'submitted_by' => auth()->id(),
            'nominal' => $this->nominal,
            'tanggal_transfer' => $this->tanggalTransfer,
            'metode' => $this->metode,
            'bukti_path' => $this->bukti->store('student-payment-proofs', 'local'),
            'status' => PaymentSubmission::PENDING,
            'catatan_siswa' => $this->catatanSiswa ?: null,
        ]);

        $this->resetForm();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Bukti pembayaran terkirim dan menunggu verifikasi admin.']);
    }

    public function cancel(int $id): void
    {
        $submission = PaymentSubmission::where('id', $id)->where('siswa_id', auth()->user()->siswa->id)->firstOrFail();
        abort_if($submission->status !== PaymentSubmission::PENDING, 422);
        $submission->update(['status' => PaymentSubmission::CANCELLED]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Pengajuan pembayaran dibatalkan.']);
    }

    public function closeForm(): void { $this->resetForm(); }

    private function resetForm(): void
    {
        $this->reset(['tagihanId', 'nominal', 'tanggalTransfer', 'metode', 'catatanSiswa', 'bukti', 'isFormOpen']);
        $this->metode = 'Transfer Bank';
        $this->resetValidation();
    }

    private function ownedTagihan(int $id): Tagihan
    {
        return Tagihan::withSum('pembayarans', 'nominal')
            ->where('id', $id)
            ->where('siswa_id', auth()->user()->siswa->id)
            ->firstOrFail();
    }

    public function render()
    {
        $siswa = auth()->user()->siswa;
        return view('livewire.student.student-payment-index', [
            'tagihans' => Tagihan::where('siswa_id', $siswa->id)->withSum('pembayarans', 'nominal')->latest('jatuh_tempo')->get(),
            'submissions' => PaymentSubmission::with('tagihan')->where('siswa_id', $siswa->id)->latest()->get(),
        ]);
    }
}
