<?php

namespace App\Livewire\Admin\Akademik;

use App\Models\KalenderAkademik;
use App\Services\HolidayService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

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

    public $deleteId;

    public $tanggal;

    public $jenis_libur = 'nasional';

    public $keterangan;

    public $is_libur = true;

    // Bulk Generator State
    public $bulkStartDate;

    public $bulkEndDate;

    public $hariAktif = [1, 2, 3, 4, 5]; // ISO: 1=Sen, 2=Sel, 3=Rab, 4=Kam, 5=Jum, 6=Sab, 7=Min

    public $bulkKeterangan = 'Libur Akhir Pekan';

    public $bulkJenis = 'sekolah';

    protected $rules = [
        'tanggal' => 'required|date',
        'jenis_libur' => 'required|in:nasional,sekolah,darurat',
        'keterangan' => 'required|string|max:255',
        'is_libur' => 'boolean',
        'bulkStartDate' => 'required|date',
        'bulkEndDate' => 'required|date|after_or_equal:bulkStartDate',
        'bulkKeterangan' => 'required|string|max:255',
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
                        'is_libur' => true,
                    ]
                );
                $count++;
            }

            // 2. Ambil dari API sebagai cadangan/pelengkap (Opsional: Hanya jika data lokal kosong)
            if (empty($localHolidays)) {
                $response = Http::get('https://date.nager.at/api/v3/PublicHolidays/'.$this->viewYear.'/ID');
                if ($response->successful()) {
                    foreach ($response->json() as $h) {
                        KalenderAkademik::updateOrCreate(
                            ['tanggal' => $h['date']],
                            [
                                'jenis_libur' => 'nasional',
                                'keterangan' => $h['localName'],
                                'is_libur' => true,
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
        $existing = KalenderAkademik::where('tanggal', $dateStr)->first();

        if ($existing) {
            $this->openEditModal($existing->id);
        } else {
            $this->reset(['editId', 'jenis_libur', 'keterangan', 'is_libur']);
            $this->tanggal = $dateStr;
            $this->dispatch('open-modal', 'calendar-modal');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

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

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('open-modal', 'delete-confirm-modal');
    }

    public function delete($id = null)
    {
        $targetId = $id ?? $this->deleteId;
        KalenderAkademik::destroy($targetId);
        $this->deleteId = null;
        $this->dispatch('close-modal', 'delete-confirm-modal');
        $this->dispatch('close-modal', 'calendar-modal');
        $this->dispatch('notify', title: 'Dihapus', message: 'Data kalender telah dihapus.', type: 'success');
    }

    public function toggleHariAktif($day)
    {
        if (in_array($day, $this->hariAktif)) {
            $this->hariAktif = array_values(array_filter($this->hariAktif, fn ($d) => $d !== $day));
        } else {
            $this->hariAktif[] = $day;
            sort($this->hariAktif);
        }
    }

    public function generateBulkHolidays()
    {
        $this->validateOnly('bulkStartDate');
        $this->validateOnly('bulkEndDate');
        $this->validateOnly('bulkKeterangan');

        $start = Carbon::parse($this->bulkStartDate);
        $end = Carbon::parse($this->bulkEndDate);
        $count = 0;
        $skip = 0;

        $current = $start->copy();
        while ($current->lte($end)) {
            $dayIso = $current->dayOfWeekIso; // 1=Mon ... 7=Sun

            // Jika hari ini BUKAN hari aktif (hari libur)
            if (! in_array($dayIso, $this->hariAktif)) {
                $dateStr = $current->format('Y-m-d');

                // Jangan timpa data yang sudah ada (misal: libur nasional)
                $exists = KalenderAkademik::where('tanggal', $dateStr)->exists();
                if (! $exists) {
                    KalenderAkademik::create([
                        'tanggal' => $dateStr,
                        'jenis_libur' => $this->bulkJenis,
                        'keterangan' => $this->bulkKeterangan,
                        'is_libur' => true,
                    ]);
                    $count++;
                } else {
                    $skip++;
                }
            }

            $current->addDay();
        }

        $this->dispatch('close-modal', 'bulk-generator-modal');
        $this->dispatch('notify',
            title: 'Generate Berhasil',
            message: "$count hari libur berhasil ditambahkan. ($skip tanggal dilewati karena sudah ada data.)",
            type: 'success'
        );
    }

    public function openBulkModal()
    {
        $this->bulkStartDate = Carbon::create($this->viewYear, $this->viewMonth, 1)->format('Y-m-d');
        $this->bulkEndDate = Carbon::create($this->viewYear, $this->viewMonth, 1)->endOfMonth()->format('Y-m-d');
        $this->bulkKeterangan = 'Libur Akhir Pekan';
        $this->bulkJenis = 'sekolah';
        $this->hariAktif = [1, 2, 3, 4, 5];
        $this->dispatch('open-modal', 'bulk-generator-modal');
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
            ->keyBy(fn ($e) => $e->tanggal->format('Y-m-d'));

        $totalLiburBulanIni = $monthlyEvents->where('is_libur', true)->count();

        // Data untuk Tabel Daftar (Pencarian & Pagination)
        $query = KalenderAkademik::query();

        if ($this->search) {
            $query->where('keterangan', 'like', '%'.$this->search.'%');
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
            'totalLiburBulanIni' => $totalLiburBulanIni,
        ]);
    }
}
