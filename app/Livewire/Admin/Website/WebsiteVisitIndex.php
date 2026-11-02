<?php

namespace App\Livewire\Admin\Website;

use App\Models\WebsiteVisit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Pengunjung Website')]
class WebsiteVisitIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRisk = '';
    public string $filterDate = '';
    public int $perPage = 15;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRisk(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDate(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = WebsiteVisit::query()
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('ip_address', 'like', '%' . $this->search . '%')
                        ->orWhere('path', 'like', '%' . $this->search . '%')
                        ->orWhere('threat_type', 'like', '%' . $this->search . '%')
                        ->orWhere('user_agent', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterRisk === 'danger', fn ($q) => $q->where('is_suspicious', true))
            ->when($this->filterRisk === 'safe', fn ($q) => $q->where('is_suspicious', false))
            ->when($this->filterDate !== '', fn ($q) => $q->whereDate('visited_at', $this->filterDate));

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $todayStats = WebsiteVisit::whereBetween('visited_at', [$todayStart, $todayEnd])
            ->selectRaw('COUNT(*) as today, COUNT(DISTINCT ip_address) as unique_today, SUM(CASE WHEN is_suspicious = 1 THEN 1 ELSE 0 END) as danger_today')
            ->first();

        return view('livewire.admin.website.website-visit-index', [
            'visits' => $query->latest('visited_at')->paginate($this->perPage),
            'stats' => [
                'today' => (int) ($todayStats->today ?? 0),
                'unique_today' => (int) ($todayStats->unique_today ?? 0),
                'danger_today' => (int) ($todayStats->danger_today ?? 0),
                'total_danger' => WebsiteVisit::where('is_suspicious', true)->count(),
            ],
            'dangerLogs' => WebsiteVisit::suspicious()->latest('visited_at')->limit(8)->get(),
        ]);
    }
}
