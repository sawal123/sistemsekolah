<?php

namespace App\Livewire\Admin\Website;

use App\Models\Kategori;
use App\Models\Post;
use App\Models\Tag;
use App\Services\OpenAISeoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Editor Artikel Blog')]
class BlogArtikelForm extends Component
{
    use WithFileUploads;

    public ?Post $post = null;

    public string $judul = '';
    public string $slug = '';
    public string $kategori_id = '';
    public string $konten = '';
    public string $status = 'Draft';
    public array $selectedTags = [];

    public string $meta_title = '';
    public string $meta_description = '';
    public string $meta_keywords = '';

    public $thumbnailFile;
    public ?string $existingThumbnail = null;

    public function mount(?Post $post = null): void
    {
        if ($post && $post->exists) {
            $this->post = $post->load('tags');
            $this->judul = $post->judul;
            $this->slug = $post->slug;
            $this->kategori_id = (string) $post->kategori_id;
            $this->konten = $post->konten ?? '';
            $this->status = $post->status;
            $this->meta_title = $post->meta_title ?? '';
            $this->meta_description = $post->meta_description ?? '';
            $this->meta_keywords = $post->meta_keywords ?? '';
            $this->existingThumbnail = $post->thumbnail;
            $this->selectedTags = $post->tags->pluck('id')->map(fn ($id) => (string) $id)->all();
        }
    }

    protected function rules(): array
    {
        return [
            'judul' => 'required|string|max:180',
            'slug' => [
                'required',
                'string',
                'max:200',
                Rule::unique('posts', 'slug')->ignore($this->post?->id),
            ],
            'kategori_id' => 'required|exists:kategoris,id',
            'konten' => 'required|string|min:20',
            'status' => 'required|in:Draft,Published',
            'selectedTags' => 'array',
            'selectedTags.*' => 'exists:tags,id',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:180',
            'meta_keywords' => 'nullable|string|max:255',
            'thumbnailFile' => 'nullable|image|max:3072',
        ];
    }

    public function updatedJudul(): void
    {
        if (! $this->post || blank($this->slug)) {
            $this->slug = Str::slug($this->judul);
        }
    }

    public function generateSeo(OpenAISeoService $seoService): void
    {
        try {
            $result = $seoService->generate($this->konten);

            if (blank($this->judul) && filled($result['title'])) {
                $this->judul = $result['title'];
                $this->slug = Str::slug($this->judul);
            }

            $this->meta_title = $result['meta_title'] ?: $this->meta_title;
            $this->meta_description = $result['meta_description'] ?: $this->meta_description;
            $this->meta_keywords = $result['meta_keywords'] ?: $this->meta_keywords;

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Saran SEO berhasil dibuat AI.']);
        } catch (\Throwable $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function save()
    {
        $this->slug = Str::slug($this->slug ?: $this->judul);
        $validated = $this->validate();

        $thumbnailPath = $this->existingThumbnail;

        if ($this->thumbnailFile) {
            $thumbnailPath = $this->thumbnailFile->store('blog/thumbnails', 'public');

            if ($this->existingThumbnail) {
                Storage::disk('public')->delete($this->existingThumbnail);
            }
        }

        $post = Post::updateOrCreate(
            ['id' => $this->post?->id],
            [
                'user_id' => Auth::id(),
                'kategori_id' => $validated['kategori_id'],
                'judul' => $validated['judul'],
                'slug' => $validated['slug'],
                'konten' => $validated['konten'],
                'thumbnail' => $thumbnailPath,
                'status' => $validated['status'],
                'meta_title' => $validated['meta_title'] ?: null,
                'meta_description' => $validated['meta_description'] ?: null,
                'meta_keywords' => $validated['meta_keywords'] ?: null,
            ]
        );

        $post->tags()->sync($validated['selectedTags'] ?? []);

        session()->flash('notify', [
            'type' => 'success',
            'message' => $this->post ? 'Artikel berhasil diperbarui.' : 'Artikel berhasil dibuat.',
        ]);

        return $this->redirectRoute('admin.website.blog-artikel', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.website.blog-artikel-form', [
            'categories' => Kategori::orderBy('nama_kategori')->get(),
            'tags' => Tag::orderBy('nama_tag')->get(),
        ]);
    }
}
