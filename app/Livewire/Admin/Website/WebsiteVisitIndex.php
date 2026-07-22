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

        $today = now()->toDateString();

        return view('livewire.admin.website.website-visit-index', [
            'visits' => $query->latest('visited_at')->paginate($this->perPage),
            'stats' => [
                'today' => WebsiteVisit::whereDate('visited_at', $today)->count(),
                'unique_today' => WebsiteVisit::whereDate('visited_at', $today)->distinct('ip_address')->count('ip_address'),
                'danger_today' => WebsiteVisit::whereDate('visited_at', $today)->where('is_suspicious', true)->count(),
                'total_danger' => WebsiteVisit::where('is_suspicious', true)->count(),
            ],
            'dangerLogs' => WebsiteVisit::suspicious()->latest('visited_at')->limit(8)->get(),
        ]);
    }
}
