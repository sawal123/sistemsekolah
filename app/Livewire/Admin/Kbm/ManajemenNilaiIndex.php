<?php

namespace App\Livewire\Admin\Kbm;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\Jadwal;
use App\Models\TahunAjaran;
use Livewire\WithFileUploads;
use Livewire\Attributes\Url;

#[Layout('layouts.admin')]
#[Title('Manajemen Nilai')]
class ManajemenNilaiIndex extends Component
{
    use WithFileUploads;

    // Filter State
    #[Url]
    public $filterTahunAjaran;
    
    #[Url]
    public $filterKelas;
    
    #[Url]
    public $filterMapel;

    // Excel AutoSave State
    public $n_harian = [];
    public $n_pts = [];
    public $n_pas = [];
    public $n_remedial = [];
    
    // Upload State
    public $fileExcel;

    // Tabs
    public $tab = 'spreadsheet'; // spreadsheet, pengaturan
    
    // Setting Mapping
    public $mapel_kkm = 75;
    public $mapel_bobot_harian = 40;
    public $mapel_bobot_pts = 30;
    public $mapel_bobot_pas = 30;

    public function mount()
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        // Hanya override jika filter di URL masih kosong
        if ($ta && !$this->filterTahunAjaran) {
            $this->filterTahunAjaran = $ta->id;
        }

        // Set default filter if Guru dan URL kosong
        if(auth()->user()->hasRole('guru') && auth()->user()->guru) {
            $j = Jadwal::where('guru_id', auth()->user()->guru->id)->first();
            if ($j && !$this->filterKelas && !$this->filterMapel) {
                $this->filterKelas = $j->kelas_id;
                $this->filterMapel = $j->mapel_id;
            }
        }

        // Inisialisasi State Input Nilai saat Page / URL Reload
        if ($this->filterKelas && $this->filterMapel) {
            $m = Mapel::find($this->filterMapel);
            if ($m) {
                $this->mapel_kkm = $m->kkm;
                $this->mapel_bobot_harian = $m->bobot_harian;
                $this->mapel_bobot_pts = $m->bobot_pts;
                $this->mapel_bobot_pas = $m->bobot_pas;
            }
            $this->loadData();
        }
    }

    public function updatedFilterKelas()
    {
        $this->filterMapel = '';
        $this->loadData();
    }

    public function updatedFilterMapel()
    {
        if ($this->filterMapel) {
            $m = Mapel::find($this->filterMapel);
            if ($m) {
                $this->mapel_kkm = $m->kkm;
                $this->mapel_bobot_harian = $m->bobot_harian;
                $this->mapel_bobot_pts = $m->bobot_pts;
                $this->mapel_bobot_pas = $m->bobot_pas;
            }
        }
        $this->loadData();
    }

    public function simpanPengaturan()
    {
        if ($this->filterMapel) {
            $m = Mapel::find($this->filterMapel);
            $m->update([
                'kkm' => $this->mapel_kkm,
                'bobot_harian' => $this->mapel_bobot_harian,
                'bobot_pts' => $this->mapel_bobot_pts,
                'bobot_pas' => $this->mapel_bobot_pas,
            ]);
            $this->dispatch('notify', title: 'Berhasil', message: 'Pengaturan KKM & Bobot Angka berhasil disimpan.', type: 'success');
        }
    }

    public function loadData()
    {
        $this->n_harian = [];
        $this->n_pts = [];
        $this->n_pas = [];
        $this->n_remedial = [];

        if ($this->filterKelas && $this->filterMapel && $this->filterTahunAjaran) {
            $siswas = Siswa::where('kelas_id', $this->filterKelas)->pluck('id');
            $nilais = Nilai::where('mapel_id', $this->filterMapel)
                           ->where('tahun_ajaran_id', $this->filterTahunAjaran)
                           ->whereIn('siswa_id', $siswas)
                           ->get()
                           ->keyBy('siswa_id');
            
            foreach ($siswas as $siswaId) {
                $n = $nilais->get($siswaId);
                $this->n_harian[$siswaId] = $n ? ($n->nilai_harian ?? []) : [];
                $this->n_pts[$siswaId] = $n ? $n->pts : null;
                $this->n_pas[$siswaId] = $n ? $n->pas : null;
                $this->n_remedial[$siswaId] = $n ? $n->skor_remedial : null;
            }
        }
    }

    private function saveScore($siswaId, $field, $value, $harianKey = null)
    {
        if (!$this->filterKelas || !$this->filterMapel || !$this->filterTahunAjaran) return;
        
        $nilai = Nilai::firstOrCreate(
            [
                'siswa_id' => $siswaId,
                'mapel_id' => $this->filterMapel,
                'tahun_ajaran_id' => $this->filterTahunAjaran,
            ]
        );

        if ($field === 'harian') {
            $json = $nilai->nilai_harian ?? [];
            $json[$harianKey] = ($value !== '' && $value !== null) ? (float)$value : null;
            $nilai->nilai_harian = $json;
        } else {
            $nilai->{$field} = ($value !== '' && $value !== null) ? (float)$value : null;
        }
        
        $nilai->save();
        $this->dispatch('notify', title: 'Tersimpan', message: 'Perubahan nilai berhasil disimpan.', type: 'success');
    }

    public function updateHarian($siswaId, $tugasKey, $value)
    {
        $this->saveScore($siswaId, 'harian', $value, $tugasKey);
        $this->loadData();
    }

    public function updatePts($siswaId, $value)
    {
        $this->saveScore($siswaId, 'pts', $value);
        $this->loadData();
    }

    public function updatePas($siswaId, $value)
    {
        $this->saveScore($siswaId, 'pas', $value);
        $this->loadData();
    }

    public function updateRemedial($siswaId, $value)
    {
        $this->saveScore($siswaId, 'skor_remedial', $value);
        $this->loadData();
    }

    public function downloadExcel()
    {
        if (!$this->filterKelas || !$this->filterMapel || !$this->filterTahunAjaran) {
            $this->dispatch('notify', title: 'Gagal', message: 'Harap pilih Kelas dan Mata Pelajaran terlebih dahulu.', type: 'danger');
            return;
        }
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\FormatNilaiExport($this->filterKelas, $this->filterMapel, $this->filterTahunAjaran), 
            'Format_Nilai_' . date('Ymd_His') . '.xlsx'
        );
    }

    public function importExcel()
    {
        $this->validate([
            'fileExcel' => 'required|mimes:xlsx,xls|max:5120', // maks 5MB
        ]);
        
        \Maatwebsite\Excel\Facades\Excel::import(
            new \App\Imports\FormatNilaiImport($this->filterMapel, $this->filterTahunAjaran), 
            $this->fileExcel->getRealPath()
        );
        
        $this->fileExcel = null;
        $this->dispatch('close-modal', 'upload-modal');
        $this->dispatch('notify', title: 'Berhasil', message: 'Data nilai telah diimpor massal ke database.', type: 'success');
        
        $this->loadData();
    }

    public function render()
    {
        $user = auth()->user();
        
        // Authorization Logic
        if ($user->hasRole('guru') && $user->guru) {
            $jadwals = Jadwal::where('guru_id', $user->guru->id)->with(['kelas', 'mapel'])->get();
            $listKelas = $jadwals->pluck('kelas')->unique('id');
            // Menampilkan mapel yang diajar oleh guru TSB di KELAS tsb.
            $listMapel = $jadwals->where('kelas_id', $this->filterKelas)->pluck('mapel')->unique('id');
        } else {
            $listKelas = Kelas::orderBy('jenjang')->orderBy('nama_kelas')->get();
            $listMapel = Mapel::orderBy('nama_mapel')->get();
        }
        
        $listTahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->orderBy('semester')->get();

        $siswas = [];
        $nilaisRender = collect();
        $mapelSetting = null;

        if ($this->filterKelas) {
            $siswas = Siswa::with('user')->where('kelas_id', $this->filterKelas)
                           ->get()
                           ->sortBy('user.name')
                           ->values(); // Reset array index to 0, 1, 2...
            
            if ($this->filterMapel) {
                $nilaisRender = Nilai::with('mapel')->where('mapel_id', $this->filterMapel)
                                   ->where('tahun_ajaran_id', $this->filterTahunAjaran)
                                   ->whereIn('siswa_id', $siswas->pluck('id'))
                                   ->get()
                                   ->keyBy('siswa_id');
                                   
                $mapelSetting = Mapel::find($this->filterMapel);
            }
        }

        return view('livewire.admin.kbm.manajemen-nilai-index', compact('listKelas', 'listMapel', 'listTahunAjaran', 'siswas', 'nilaisRender', 'mapelSetting'));
    }
}
