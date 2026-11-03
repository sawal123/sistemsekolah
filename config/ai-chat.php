<?php

return [
    'enabled' => env('AI_CHAT_ENABLED', true),
    'model' => env('AI_CHAT_MODEL', env('OPENAI_MODEL', 'gpt-4o-mini')),
    'timeout' => (int) env('AI_CHAT_TIMEOUT', 30),
    'max_message_length' => (int) env('AI_CHAT_MAX_MESSAGE_LENGTH', 2000),
    'history_limit' => (int) env('AI_CHAT_HISTORY_LIMIT', 10),
    'rate_limit_max_attempts' => (int) env('AI_CHAT_RATE_LIMIT_MAX_ATTEMPTS', 10),
    'rate_limit_decay_seconds' => (int) env('AI_CHAT_RATE_LIMIT_DECAY_SECONDS', 600),
    'retention_days' => (int) env('AI_CHAT_RETENTION_DAYS', 90),
];
