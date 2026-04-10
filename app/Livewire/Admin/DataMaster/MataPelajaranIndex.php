<?php

namespace App\Livewire\Admin\DataMaster;

use App\Exports\MapelExport;
use App\Imports\MapelImport;
use App\Models\Mapel;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.admin')]
#[Title('Mata Pelajaran')]
class MataPelajaranIndex extends Component
{
    use WithFileUploads, WithPagination;

    // Search & Filter
    public $search = '';
    public $perPage = 10;

    public $filterJenjang = '';

    public $filterKelompok = '';

    // Modal State
    public $isModalOpen = false;

    public $editId = null;

    public $idBeingDeleted = null;

    // Form Fields
    public $kode_mapel;

    public $nama_mapel;

    public $kelompok = 'Nasional';

    public $jenjang = 'Umum';

    // Import
    public $importFile;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterJenjang' => ['except' => ''],
        'filterKelompok' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterJenjang()
    {
        $this->resetPage();
    }

    public function updatingFilterKelompok()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Mapel::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_mapel', 'like', '%'.$this->search.'%')
                    ->orWhere('nama_mapel', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filterJenjang) {
            $query->where('jenjang', $this->filterJenjang);
        }

        if ($this->filterKelompok) {
            $query->where('kelompok', $this->filterKelompok);
        }

        return view('livewire.admin.data-master.mata-pelajaran-index', [
            'mapels' => $query->latest()->paginate($this->perPage),
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'mapel-form');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->idBeingDeleted = null;
        $this->dispatch('close-modal', 'mapel-form');
        $this->dispatch('close-modal', 'confirm-delete-modal');
    }

    public function resetForm()
    {
        $this->kode_mapel = '';
        $this->nama_mapel = '';
        $this->kelompok = 'Nasional';
        $this->jenjang = 'Umum';
        $this->editId = null;
        $this->idBeingDeleted = null;
        $this->resetErrorBag();
    }

    public function edit($id)
    {
        $this->resetForm();
        $item = Mapel::findOrFail($id);
        $this->editId = $id;
        $this->kode_mapel = $item->kode_mapel;
        $this->nama_mapel = $item->nama_mapel;
        $this->kelompok = $item->kelompok;
        $this->jenjang = $item->jenjang;

        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'mapel-form');
    }

    public function save()
    {
        $this->validate([
            'kode_mapel' => 'required|string|max:50|unique:mapels,kode_mapel,'.$this->editId,
            'nama_mapel' => 'required|string|max:255',
            'kelompok' => 'required|in:Nasional,Kewilayahan,Peminatan,Mulok',
            'jenjang' => 'required|in:SMP,SMA,Umum',
        ]);

        if ($this->editId) {
            Mapel::find($this->editId)->update([
                'kode_mapel' => $this->kode_mapel,
                'nama_mapel' => $this->nama_mapel,
                'kelompok' => $this->kelompok,
                'jenjang' => $this->jenjang,
            ]);
            $msg = 'Mata Pelajaran berhasil diperbarui!';
        } else {
            Mapel::create([
                'kode_mapel' => $this->kode_mapel,
                'nama_mapel' => $this->nama_mapel,
                'kelompok' => $this->kelompok,
                'jenjang' => $this->jenjang,
            ]);
            $msg = 'Mata Pelajaran berhasil ditambahkan!';
        }

        $this->closeModal();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $msg,
        ]);
    }

    public function confirmDelete($id)
    {
        $this->idBeingDeleted = $id;
        $this->dispatch('open-modal', 'confirm-delete-modal');
    }

    public function delete()
    {
        if ($this->idBeingDeleted) {
            Mapel::find($this->idBeingDeleted)->delete();
            $this->closeModal();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Mata Pelajaran berhasil dihapus!',
            ]);
        }
    }

    public function exportExcel()
    {
        return Excel::download(new MapelExport, 'daftar-mata-pelajaran.xlsx');
    }

    public function exportPdf()
    {
        $mapels = Mapel::all();
        $pdf = Pdf::loadView('exports.mapel-pdf', compact('mapels'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'daftar-mata-pelajaran.pdf');
    }

    public function importExcel()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new MapelImport, $this->importFile->getRealPath());

            $this->closeModal();
            $this->importFile = null;

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Data Mata Pelajaran berhasil di-import!',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Gagal meng-import data: '.$e->getMessage(),
            ]);
        }
    }

    public function openImportModal()
    {
        $this->importFile = null;
        $this->dispatch('open-modal', 'import-modal');
    }
}
