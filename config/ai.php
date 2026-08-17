<?php

// config/ai.php
// Any provider exposing an OpenAI-compatible /chat/completions endpoint works
// here by changing only .env — OpenAI itself, Azure OpenAI, or a local/self-hosted
// model gateway. This does NOT change which tool you use to write code (that's
// a separate choice, e.g. OpenCode) — this is what the running app calls at
// request time to classify WhatsApp intent and generate replies/content.

return [

    'base_url' => env('AI_BASE_URL', 'https://api.openai.com/v1'),
    'api_key' => env('AI_API_KEY', env('OPENAI_API_KEY')), // falls back to the existing var so nothing breaks on upgrade

    'models' => [
        // Separate model per use case — classification wants fast/cheap,
        // reply generation wants a bit more quality. Both configurable
        // independently since not every provider prices/names them the same.
        'classification' => env('AI_MODEL_CLASSIFICATION', 'gpt-4o-mini'),
        'reply_generation' => env('AI_MODEL_REPLY', 'gpt-4o-mini'),
    ],

    'timeout_seconds' => (int) env('AI_TIMEOUT_SECONDS', 15),

];
