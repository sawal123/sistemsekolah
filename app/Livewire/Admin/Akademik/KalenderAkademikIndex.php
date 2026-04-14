<?php

namespace App\Livewire\Admin\Akademik;

use App\Models\KalenderAkademik;
use App\Services\HolidayService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

#[Layout('layouts.admin')]
#[Title('Kalender Akademik')]
class KalenderAkademikIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterJenis = '';

    // Calendar View State
    public $viewMonth;
    public $viewYear;
    
    // Sync state
    public $isSyncing = false;
    
    // Form Modal State
    public $editId;
    public $tanggal;
    public $jenis_libur = 'nasional';
    public $keterangan;
    public $is_libur = true;

    protected $rules = [
        'tanggal' => 'required|date',
        'jenis_libur' => 'required|in:nasional,sekolah,darurat',
        'keterangan' => 'required|string|max:255',
        'is_libur' => 'boolean',
    ];

    public function mount()
    {
        $this->viewMonth = now()->month;
        $this->viewYear = now()->year;
    }

    public function syncNationalHolidays()
    {
        $this->isSyncing = true;

        try {
            $count = 0;

            // 1. Ambil dari Database Lokal Internal (Akurasi 100% untuk Indonesia 2026)
            $localHolidays = HolidayService::getHolidays($this->viewYear);
            
            foreach ($localHolidays as $h) {
                KalenderAkademik::updateOrCreate(
                    ['tanggal' => $h['date']],
                    [
                        'jenis_libur' => $h['type'],
                        'keterangan' => $h['name'],
                        'is_libur' => true
                    ]
                );
                $count++;
            }

            // 2. Ambil dari API sebagai cadangan/pelengkap (Opsional: Hanya jika data lokal kosong)
            if (empty($localHolidays)) {
                $response = Http::get("https://date.nager.at/api/v3/PublicHolidays/" . $this->viewYear . "/ID");
                if ($response->successful()) {
                    foreach ($response->json() as $h) {
                        KalenderAkademik::updateOrCreate(
                            ['tanggal' => $h['date']],
                            [
                                'jenis_libur' => 'nasional',
                                'keterangan' => $h['localName'],
                                'is_libur' => true
                            ]
                        );
                        $count++;
                    }
                }
            }

            $this->dispatch('notify', title: 'Sinkronisasi Berhasil', message: "Berhasil menarik $count hari libur nasional & cuti bersama.", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Gagal', message: 'Terjadi kesalahan saat sinkronisasi data.', type: 'danger');
        }

        $this->isSyncing = false;
    }

    public function prevMonth()
    {
        $current = Carbon::create($this->viewYear, $this->viewMonth, 1)->subMonth();
        $this->viewMonth = $current->month;
        $this->viewYear = $current->year;
    }

    public function nextMonth()
    {
        $current = Carbon::create($this->viewYear, $this->viewMonth, 1)->addMonth();
        $this->viewMonth = $current->month;
        $this->viewYear = $current->year;
    }

    public function selectDate($dateStr)
    {
        $this->reset(['editId', 'jenis_libur', 'keterangan', 'is_libur']);
        $this->tanggal = $dateStr;
        $this->dispatch('open-modal', 'calendar-modal');
    }

    public function updatingSearch() { $this->resetPage(); }

    public function openAddModal()
    {
        $this->reset(['editId', 'tanggal', 'jenis_libur', 'keterangan', 'is_libur']);
        $this->tanggal = now()->format('Y-m-d');
        $this->dispatch('open-modal', 'calendar-modal');
    }

    public function openEditModal($id)
    {
        $event = KalenderAkademik::findOrFail($id);
        $this->editId = $id;
        $this->tanggal = $event->tanggal->format('Y-m-d');
        $this->jenis_libur = $event->jenis_libur;
        $this->keterangan = $event->keterangan;
        $this->is_libur = $event->is_libur;

        $this->dispatch('open-modal', 'calendar-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'tanggal' => $this->tanggal,
            'jenis_libur' => $this->jenis_libur,
            'keterangan' => $this->keterangan,
            'is_libur' => $this->is_libur,
        ];

        if ($this->editId) {
            KalenderAkademik::find($this->editId)->update($data);
            $msg = 'Data kalender berhasil diperbarui.';
        } else {
            // Cek apakah tanggal sudah ada
            if (KalenderAkademik::where('tanggal', $this->tanggal)->exists()) {
                $this->addError('tanggal', 'Tanggal ini sudah terdaftar di kalender.');
                return;
            }
            KalenderAkademik::create($data);
            $msg = 'Hari libur/event baru berhasil ditambahkan.';
        }

        $this->dispatch('close-modal', 'calendar-modal');
        $this->dispatch('notify', title: 'Berhasil', message: $msg, type: 'success');
    }

    public function delete($id)
    {
        KalenderAkademik::destroy($id);
        $this->dispatch('notify', title: 'Dihapus', message: 'Data kalender telah dihapus.', type: 'success');
    }

    public function render()
    {
        // Data untuk Visual Calendar
        $firstDayOfMonth = Carbon::create($this->viewYear, $this->viewMonth, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        
        // Ambil event untuk bulan ini
        $monthlyEvents = KalenderAkademik::whereYear('tanggal', $this->viewYear)
            ->whereMonth('tanggal', $this->viewMonth)
            ->get()
            ->keyBy(fn($e) => $e->tanggal->format('Y-m-d'));

        $totalLiburBulanIni = $monthlyEvents->where('is_libur', true)->count();

        // Data untuk Tabel Daftar (Pencarian & Pagination)
        $query = KalenderAkademik::query();

        if ($this->search) {
            $query->where('keterangan', 'like', '%' . $this->search . '%');
        }

        if ($this->filterJenis) {
            $query->where('jenis_libur', $this->filterJenis);
        }

        $events = $query->orderBy('tanggal', 'desc')->paginate(5);

        return view('livewire.admin.akademik.kalender-akademik-index', [
            'events' => $events,
            'monthlyEvents' => $monthlyEvents,
            'firstDayOfMonth' => $firstDayOfMonth,
            'daysInMonth' => $daysInMonth,
            'monthName' => $firstDayOfMonth->translatedFormat('F Y'),
            'totalLiburBulanIni' => $totalLiburBulanIni
        ]);
    }
}
