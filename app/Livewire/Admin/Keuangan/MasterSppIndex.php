<?php

namespace App\Livewire\Admin\Keuangan;

use App\Models\Jurusan;
use App\Models\Spp;
use App\Models\TahunAjaran;
use Illuminate\Validation\Rule;
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
    public string $search = '';

    public string $filterJenjang = '';

    public string $filterTahunAjaran = '';

    // ── Modal state ───────────────────────────────────────────
    public bool $isModalOpen = false;

    public ?int $editId = null;

    public ?int $idBeingDeleted = null;

    // ── Form fields ───────────────────────────────────────────
    public string $tahun_ajaran_id = '';

    public string $jenjang = 'Semua';

    public string $kategori = 'SPP Bulanan';

    public string $nominal = '';

    public string $keterangan = '';

    public bool $is_active = true;

    public string $jurusan_id = '';

    protected function rules(): array
    {
        return [
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'jenjang' => 'required|in:SMP,SMA,SMK,Semua',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'kategori' => [
                'required',
                'string',
                'max:100',
                Rule::unique('spps', 'kategori')
                    ->where(fn ($query) => $query
                        ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
                        ->where('jenjang', $this->jenjang)
                        ->when(
                            $this->jurusan_id !== '',
                            fn ($q) => $q->where('jurusan_id', $this->jurusan_id),
                            fn ($q) => $q->whereNull('jurusan_id')
                        ))
                    ->ignore($this->editId),
            ],
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal harus lebih dari 0.',
            'kategori.unique' => 'Tarif untuk kategori, jenjang, dan tahun ajaran tersebut sudah ada.',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedJenjang(string $value): void
    {
        if ($value !== 'SMK') {
            $this->jurusan_id = '';
        }
    }

    public function render()
    {
        $spps = Spp::with(['tahunAjaran', 'jurusan'])
            ->when($this->search, fn ($q) => $q->where(fn ($searchQuery) => $searchQuery
                ->where('kategori', 'like', '%'.$this->search.'%')
                ->orWhere('keterangan', 'like', '%'.$this->search.'%')))
            ->when($this->filterJenjang, fn ($q) => $q->where('jenjang', $this->filterJenjang))
            ->when($this->filterTahunAjaran, fn ($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaran))
            ->orderByDesc('created_at')
            ->paginate(10);

        $tahunAjarans = TahunAjaran::orderByDesc('tahun')->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('nama')->get();

        return view('livewire.admin.keuangan.master-spp-index', compact('spps', 'tahunAjarans', 'jurusans'));
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

        $this->editId = $id;
        $this->tahun_ajaran_id = (string) $item->tahun_ajaran_id;
        $this->jenjang = $item->jenjang;
        $this->kategori = $item->kategori;
        $this->nominal = (string) $item->nominal;
        $this->keterangan = $item->keterangan ?? '';
        $this->is_active = $item->is_active;
        $this->jurusan_id = (string) ($item->jurusan_id ?? '');

        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'spp-form');
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
            'jenjang' => $this->jenjang,
            'jurusan_id' => $this->jenjang === 'SMK' && $this->jurusan_id !== '' ? $this->jurusan_id : null,
            'kategori' => $this->kategori,
            'nominal' => $this->nominal,
            'keterangan' => $this->keterangan ?: null,
            'is_active' => $this->is_active,
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

    public function toggleActive(int $id): void
    {
        $spp = Spp::findOrFail($id);
        $spp->update(['is_active' => ! $spp->is_active]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $spp->is_active
                ? 'Biaya berhasil diaktifkan dan tampil di rincian biaya.'
                : 'Biaya dinonaktifkan dari rincian biaya.',
        ]);
    }

    public function delete(): void
    {
        if ($this->idBeingDeleted) {
            $spp = Spp::findOrFail($this->idBeingDeleted);
            if ($spp->pembayaranSpps()->exists()) {
                $this->dispatch('notify', [
                    'type' => 'error',
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
        $this->isModalOpen = false;
        $this->idBeingDeleted = null;
        $this->dispatch('close-modal', 'spp-form');
        $this->dispatch('close-modal', 'confirm-delete-modal');
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->tahun_ajaran_id = '';
        $this->jenjang = 'Semua';
        $this->jurusan_id = '';
        $this->kategori = 'SPP Bulanan';
        $this->nominal = '';
        $this->keterangan = '';
        $this->is_active = true;
        $this->idBeingDeleted = null;
        $this->resetValidation();
    }
}
