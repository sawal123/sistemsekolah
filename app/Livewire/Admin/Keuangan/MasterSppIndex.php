<?php

namespace App\Livewire\Admin\Keuangan;

use App\Models\Spp;
use App\Models\TahunAjaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Master Data SPP')]
class MasterSppIndex extends Component
{
    use WithPagination;

    // ── Filter ────────────────────────────────────────────────
    public string $search           = '';
    public string $filterJenjang    = '';
    public string $filterTahunAjaran = '';

    // ── Modal state ───────────────────────────────────────────
    public bool $isModalOpen = false;
    public ?int $editId      = null;
    public ?int $idBeingDeleted = null;

    // ── Form fields ───────────────────────────────────────────
    public string $tahun_ajaran_id = '';
    public string $jenjang         = 'Semua';
    public string $kategori        = 'SPP Bulanan';
    public string $nominal         = '';
    public string $keterangan      = '';

    protected function rules(): array
    {
        return [
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'jenjang'         => 'required|in:SMP,SMA,Semua',
            'kategori'        => 'required|string|max:100',
            'nominal'         => 'required|numeric|min:1',
            'keterangan'      => 'nullable|string|max:500',
        ];
    }

    protected function messages(): array
    {
        return [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'nominal.required'         => 'Nominal wajib diisi.',
            'nominal.numeric'          => 'Nominal harus berupa angka.',
            'nominal.min'              => 'Nominal harus lebih dari 0.',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $spps = Spp::with('tahunAjaran')
            ->when($this->search, fn ($q) => $q
                ->where('kategori', 'like', '%' . $this->search . '%')
                ->orWhere('keterangan', 'like', '%' . $this->search . '%'))
            ->when($this->filterJenjang, fn ($q) => $q->where('jenjang', $this->filterJenjang))
            ->when($this->filterTahunAjaran, fn ($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaran))
            ->orderByDesc('created_at')
            ->paginate(10);

        $tahunAjarans = TahunAjaran::orderByDesc('tahun')->get();

        return view('livewire.admin.keuangan.master-spp-index', compact('spps', 'tahunAjarans'));
    }

    // ── CRUD Actions ──────────────────────────────────────────

    public function openModal(): void
    {
        $this->resetForm();
        $aktif = TahunAjaran::where('is_active', true)->first();
        if ($aktif) {
            $this->tahun_ajaran_id = (string) $aktif->id;
        }
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'spp-form');
    }

    public function edit(int $id): void
    {
        $this->resetForm();
        $item = Spp::findOrFail($id);

        $this->editId          = $id;
        $this->tahun_ajaran_id = (string) $item->tahun_ajaran_id;
        $this->jenjang         = $item->jenjang;
        $this->kategori        = $item->kategori;
        $this->nominal         = (string) $item->nominal;
        $this->keterangan      = $item->keterangan ?? '';

        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'spp-form');
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
            'jenjang'         => $this->jenjang,
            'kategori'        => $this->kategori,
            'nominal'         => $this->nominal,
            'keterangan'      => $this->keterangan ?: null,
        ];

        if ($this->editId) {
            Spp::findOrFail($this->editId)->update($data);
            $msg = 'Tarif SPP berhasil diperbarui.';
        } else {
            Spp::create($data);
            $msg = 'Tarif SPP berhasil ditambahkan.';
        }

        $this->closeModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
    }

    public function confirmDelete(int $id): void
    {
        $this->idBeingDeleted = $id;
        $this->dispatch('open-modal', 'confirm-delete-modal');
    }

    public function delete(): void
    {
        if ($this->idBeingDeleted) {
            $spp = Spp::findOrFail($this->idBeingDeleted);
            if ($spp->pembayaranSpps()->exists()) {
                $this->dispatch('notify', [
                    'type'    => 'error',
                    'message' => 'Tarif tidak dapat dihapus karena sudah memiliki data pembayaran.',
                ]);
                $this->closeModal();
                return;
            }
            $spp->delete();
            $this->closeModal();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Tarif SPP berhasil dihapus.']);
        }
    }

    public function closeModal(): void
    {
        $this->isModalOpen    = false;
        $this->idBeingDeleted = null;
        $this->dispatch('close-modal', 'spp-form');
        $this->dispatch('close-modal', 'confirm-delete-modal');
    }

    public function resetForm(): void
    {
        $this->editId          = null;
        $this->tahun_ajaran_id = '';
        $this->jenjang         = 'Semua';
        $this->kategori        = 'SPP Bulanan';
        $this->nominal         = '';
        $this->keterangan      = '';
        $this->idBeingDeleted  = null;
        $this->resetValidation();
    }
}
