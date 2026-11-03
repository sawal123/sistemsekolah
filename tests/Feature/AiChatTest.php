<?php

namespace Tests\Feature;

use App\Livewire\Assistant\HelpChat;
use App\Models\AiChatConversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AiChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'guru']);
        config()->set('ai-chat.enabled', true);
        config()->set('services.openai.api_key', 'test-api-key');
        config()->set('ai-chat.model', 'test-model');
    }

    public function test_authenticated_admin_can_open_help_and_api_key_is_not_rendered(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get(route('assistant.help'))
            ->assertOk()
            ->assertSee('Bantuan AI')
            ->assertDontSee('test-api-key');
    }

    public function test_guest_cannot_open_ai_help(): void
    {
        $this->get(route('assistant.help'))->assertRedirect(route('login'));
    }

    public function test_chat_stores_owned_messages_and_uses_responses_api_from_backend(): void
    {
        $user = User::factory()->create(['name' => 'Admin Sekolah']);
        $user->assignRole('admin');
        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'id' => 'resp_test_1',
                'model' => 'test-model',
                'output' => [[
                    'type' => 'message',
                    'content' => [['type' => 'output_text', 'text' => 'Buka menu Data Master, lalu pilih Data Kelas.']],
                ]],
                'usage' => ['input_tokens' => 20, 'output_tokens' => 12],
            ]),
        ]);

        $this->actingAs($user);
        Livewire::test(HelpChat::class)
            ->set('message', 'Bagaimana cara menambah kelas?')
            ->call('send')
            ->assertSet('chatError', null)
            ->assertSee('Buka menu Data Master, lalu pilih Data Kelas.');

        $conversation = AiChatConversation::firstOrFail();
        $this->assertSame($user->id, $conversation->user_id);
        $this->assertDatabaseHas('ai_chat_messages', ['conversation_id' => $conversation->id, 'role' => 'user', 'content' => 'Bagaimana cara menambah kelas?']);
        $this->assertDatabaseHas('ai_chat_messages', ['conversation_id' => $conversation->id, 'role' => 'assistant', 'openai_response_id' => 'resp_test_1']);
        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.openai.com/v1/responses'
            && $request['model'] === 'test-model'
            && $request['store'] === false);
    }

    public function test_user_cannot_open_or_delete_another_users_conversation(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('admin');
        $other = User::factory()->create();
        $other->assignRole('admin');
        $conversation = AiChatConversation::create(['user_id' => $owner->id, 'title' => 'Privat']);

        $this->actingAs($other);
        $this->get(route('assistant.help'))->assertOk()->assertDontSee('Privat');
        try {
            Livewire::test(HelpChat::class)->call('deleteConversation', $conversation->id);
            $this->fail('Percakapan pengguna lain tidak boleh dapat dihapus.');
        } catch (ModelNotFoundException) {
            // Query kepemilikan sengaja tidak menemukan percakapan pengguna lain.
        }
        $this->assertDatabaseHas('ai_chat_conversations', ['id' => $conversation->id, 'deleted_at' => null]);
    }

    public function test_rate_limit_prevents_excess_chat_messages(): void
    {
        $user = User::factory()->create();
        $user->assignRole('guru');
        config()->set('ai-chat.rate_limit_max_attempts', 1);
        config()->set('ai-chat.rate_limit_decay_seconds', 60);
        Http::fake(['https://api.openai.com/v1/responses' => Http::response(['output_text' => 'Jawaban bantuan.'])]);

        $this->actingAs($user);
        $key = 'ai-chat:'.$user->id.':127.0.0.1';
        RateLimiter::clear($key);
        $component = Livewire::test(HelpChat::class)->set('message', 'Pertanyaan pertama')->call('send');
        $component->set('message', 'Pertanyaan kedua')->call('send')->assertSee('Batas pesan tercapai');
        Http::assertSentCount(1);
    }
}
