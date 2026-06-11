<?php

namespace App\Livewire\Admin\Website;

use App\Models\Kategori;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Blog')]
class BlogArtikelIndex extends Component
{
    use WithPagination;

    public string $tab = 'posts';
    public string $search = '';
    public string $statusFilter = '';
    public string $kategoriFilter = '';

    public bool $isTagModalOpen = false;
    public bool $isCategoryModalOpen = false;
    public ?int $tagEditId = null;
    public ?int $categoryEditId = null;
    public ?int $idBeingDeleted = null;
    public string $deleteType = '';

    public string $tagName = '';
    public string $categoryName = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedKategoriFilter(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $posts = Post::with(['user', 'kategori', 'tags'])
            ->withCount('comments')
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('judul', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->kategoriFilter, fn ($q) => $q->where('kategori_id', $this->kategoriFilter))
            ->latest()
            ->paginate(8);

        $comments = PostComment::with('post')
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('komentar', 'like', '%' . $this->search . '%');
            }))
            ->latest()
            ->paginate(8, ['*'], 'commentsPage');

        $tags = Tag::withCount('posts')->orderBy('nama_tag')->get();
        $categories = Kategori::withCount('posts')->orderBy('nama_kategori')->get();

        return view('livewire.admin.website.blog-artikel-index', compact('posts', 'comments', 'tags', 'categories'));
    }

    public function saveTag(): void
    {
        $this->validate([
            'tagName' => [
                'required',
                'string',
                'max:80',
                Rule::unique('tags', 'nama_tag')->ignore($this->tagEditId),
            ],
        ]);

        Tag::updateOrCreate(
            ['id' => $this->tagEditId],
            ['nama_tag' => $this->tagName, 'slug' => $this->uniqueSlug(Tag::class, $this->tagName, $this->tagEditId)]
        );

        $this->closeModals();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Tag berhasil disimpan.']);
    }

    public function editTag(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $this->tagEditId = $tag->id;
        $this->tagName = $tag->nama_tag;
        $this->isTagModalOpen = true;
        $this->dispatch('open-modal', 'tag-form');
    }

    public function openTagModal(): void
    {
        $this->resetTagForm();
        $this->isTagModalOpen = true;
        $this->dispatch('open-modal', 'tag-form');
    }

    public function saveCategory(): void
    {
        $this->validate([
            'categoryName' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategoris', 'nama_kategori')->ignore($this->categoryEditId),
            ],
        ]);

        Kategori::updateOrCreate(
            ['id' => $this->categoryEditId],
            [
                'nama_kategori' => $this->categoryName,
                'slug' => $this->uniqueSlug(Kategori::class, $this->categoryName, $this->categoryEditId),
            ]
        );

        $this->closeModals();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Kategori berhasil disimpan.']);
    }

    public function editCategory(int $id): void
    {
        $category = Kategori::findOrFail($id);
        $this->categoryEditId = $category->id;
        $this->categoryName = $category->nama_kategori;
        $this->isCategoryModalOpen = true;
        $this->dispatch('open-modal', 'category-form');
    }

    public function openCategoryModal(): void
    {
        $this->resetCategoryForm();
        $this->isCategoryModalOpen = true;
        $this->dispatch('open-modal', 'category-form');
    }

    public function updateCommentStatus(int $id, string $status): void
    {
        abort_unless(in_array($status, ['Pending', 'Approved', 'Rejected'], true), 422);

        PostComment::findOrFail($id)->update(['status' => $status]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Status komentar diperbarui.']);
    }

    public function confirmDelete(string $type, int $id): void
    {
        $this->deleteType = $type;
        $this->idBeingDeleted = $id;
        $this->dispatch('open-modal', 'confirm-delete-modal');
    }

    public function delete(): void
    {
        if (! $this->idBeingDeleted) {
            return;
        }

        if ($this->deleteType === 'category' && Kategori::findOrFail($this->idBeingDeleted)->posts()->exists()) {
            $this->closeModals();
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Kategori masih digunakan artikel dan tidak dapat dihapus.',
            ]);
            return;
        }

        match ($this->deleteType) {
            'post' => $this->deletePost($this->idBeingDeleted),
            'tag' => Tag::findOrFail($this->idBeingDeleted)->delete(),
            'category' => Kategori::findOrFail($this->idBeingDeleted)->delete(),
            'comment' => PostComment::findOrFail($this->idBeingDeleted)->delete(),
            default => null,
        };

        $this->closeModals();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Data berhasil dihapus.']);
    }

    public function closeModals(): void
    {
        $this->isTagModalOpen = false;
        $this->isCategoryModalOpen = false;
        $this->idBeingDeleted = null;
        $this->deleteType = '';
        $this->dispatch('close-modal', 'tag-form');
        $this->dispatch('close-modal', 'category-form');
        $this->dispatch('close-modal', 'confirm-delete-modal');
    }

    public function closeModal(): void
    {
        $this->closeModals();
    }

    private function resetTagForm(): void
    {
        $this->tagEditId = null;
        $this->tagName = '';
        $this->resetValidation();
    }

    private function resetCategoryForm(): void
    {
        $this->categoryEditId = null;
        $this->categoryName = '';
        $this->resetValidation();
    }

    private function deletePost(int $id): void
    {
        $post = Post::findOrFail($id);

        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }

        $post->delete();
    }

    private function uniqueSlug(string $model, string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        $slug = $base;
        $counter = 2;

        while ($model::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
