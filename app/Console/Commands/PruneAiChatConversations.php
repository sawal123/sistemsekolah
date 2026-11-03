<?php

namespace App\Console\Commands;

use App\Models\AiChatConversation;
use Illuminate\Console\Command;

class PruneAiChatConversations extends Command
{
    protected $signature = 'ai-chat:prune';

    protected $description = 'Remove expired and deleted AI help conversations';

    public function handle(): int
    {
        $threshold = now()->subDays(config('ai-chat.retention_days'));
        $expired = AiChatConversation::where('last_message_at', '<', $threshold)->delete();
        $purged = AiChatConversation::onlyTrashed()->where('deleted_at', '<', $threshold)->forceDelete();

        $this->info("{$expired} percakapan diarsipkan, {$purged} percakapan dihapus permanen.");

        return self::SUCCESS;
    }
}
