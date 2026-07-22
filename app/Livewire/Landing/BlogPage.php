<?php

namespace App\Livewire\Landing;

use App\Models\Kategori;
use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.landing')]
#[Title('Blog & Berita')]
class BlogPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $category = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.landing.blog-page', [
            'posts' => Post::with(['kategori', 'user', 'tags'])
                ->where('status', 'Published')
                ->when($this->search, fn ($query) => $query->where('judul', 'like', "%{$this->search}%"))
                ->when($this->category, fn ($query) => $query->whereHas('kategori', fn ($category) => $category->where('slug', $this->category)))
                ->latest()
                ->paginate(9),
            'categories' => Kategori::whereHas('posts', fn ($query) => $query->where('status', 'Published'))
                ->withCount(['posts' => fn ($query) => $query->where('status', 'Published')])
                ->orderBy('nama_kategori')
                ->get(),
        ]);
    }
}
