<?php

namespace App\Livewire\Landing;

use App\Models\Post;
use App\Models\PostComment;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.landing')]
class BlogDetailPage extends Component
{
    public Post $post;
    public string $name = '';
    public string $email = '';
    public string $comment = '';

    public function mount(string $slug): void
    {
        $this->post = Post::with(['kategori', 'user', 'tags'])
            ->where('status', 'Published')
            ->where('slug', $slug)
            ->firstOrFail();

        $this->post->increment('views');
    }

    public function submitComment(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:120',
            'comment' => 'required|string|min:5|max:1500',
        ]);

        PostComment::create([
            'post_id' => $this->post->id,
            'nama' => $validated['name'],
            'email' => $validated['email'] ?: null,
            'komentar' => $validated['comment'],
            'status' => 'Pending',
        ]);

        $this->reset('name', 'email', 'comment');
        session()->flash('comment_success', 'Komentar berhasil dikirim dan menunggu moderasi.');
    }

    public function render()
    {
        return view('livewire.landing.blog-detail-page', [
            'comments' => $this->post->approvedComments()->latest()->get(),
            'relatedPosts' => Post::where('status', 'Published')
                ->where('id', '!=', $this->post->id)
                ->where('kategori_id', $this->post->kategori_id)
                ->latest()
                ->take(3)
                ->get(),
        ])->title($this->post->meta_title ?: $this->post->judul);
    }
}
