<?php

namespace App\Livewire\Assistant;

use App\Models\AiChatConversation;
use App\Services\AiChatService;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Bantuan AI')]
class HelpChat extends Component
{
    public ?int $conversationId = null;

    public string $message = '';

    public ?string $chatError = null;

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
    }

    public function newConversation(): void
    {
        $this->conversationId = null;
        $this->reset(['message', 'chatError']);
        $this->resetValidation();
    }

    public function selectConversation(int $conversationId): void
    {
        $this->ownedConversation($conversationId);
        $this->conversationId = $conversationId;
        $this->reset(['message', 'chatError']);
        $this->resetValidation();
    }

    public function deleteConversation(int $conversationId): void
    {
        $conversation = $this->ownedConversation($conversationId);
        $conversation->delete();

        if ($this->conversationId === $conversationId) {
            $this->newConversation();
        }
    }

    public function send(AiChatService $chatService): void
    {
        $this->chatError = null;
        $this->validate([
            'message' => ['required', 'string', 'max:'.config('ai-chat.max_message_length')],
        ], [
            'message.required' => 'Tulis pertanyaan terlebih dahulu.',
            'message.max' => 'Pesan bantuan terlalu panjang.',
        ]);

        $key = 'ai-chat:'.auth()->id().':'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, config('ai-chat.rate_limit_max_attempts'))) {
            $seconds = RateLimiter::availableIn($key);
            $this->chatError = "Batas pesan tercapai. Coba lagi dalam {$seconds} detik.";

            return;
        }
        RateLimiter::hit($key, config('ai-chat.rate_limit_decay_seconds'));

        $conversation = $this->conversationId
            ? $this->ownedConversation($this->conversationId)
            : AiChatConversation::create([
                'user_id' => auth()->id(),
                'title' => 'Percakapan bantuan baru',
            ]);

        try {
            $chatService->respond(auth()->user(), $conversation, $this->message);
            $this->conversationId = $conversation->id;
            $this->reset('message');
        } catch (\RuntimeException $exception) {
            report($exception);
            $this->chatError = $exception->getMessage();
        }
    }

    public function render()
    {
        $conversations = AiChatConversation::where('user_id', auth()->id())
            ->latest('last_message_at')
            ->latest('id')
            ->get();
        $conversation = $this->conversationId
            ? $conversations->firstWhere('id', $this->conversationId)
            : null;

        return view('livewire.assistant.help-chat', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
            'messages' => $conversation
                ? $conversation->messages()->oldest('id')->get()
                : collect(),
        ]);
    }

    private function ownedConversation(int $conversationId): AiChatConversation
    {
        return AiChatConversation::where('user_id', auth()->id())->findOrFail($conversationId);
    }
}
