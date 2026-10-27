<?php

// config/ai.php
// Any provider exposing an OpenAI-compatible /chat/completions endpoint works
// here by changing only .env — OpenAI itself, Azure OpenAI, or a local/self-hosted
// model gateway. This does NOT change which tool you use to write code (that's
// a separate choice, e.g. OpenCode) — this is what the running app calls at
// request time to generate content and power AI-assisted workflows.

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

    // Voice notes — "speak your problem" on the advice form.
    //
    // Default is the browser's Web Speech API: it runs on the client, costs
    // nothing, and needs no API key. That's the whole point for a product
    // with no AI budget.
    //
    // Set VOICE_PROVIDER=openai + VOICE_MODEL=whisper-1 and an AI_API_KEY to
    // route through Whisper instead — useful if the client's browser doesn't
    // support speech recognition (e.g. Firefox iOS). The Laravel controller
    // at /voice-notes/transcribe handles that path; the default path needs
    // no server code at all.
    'voice' => [
        'provider' => env('VOICE_PROVIDER', 'web_speech'),
        'model' => env('VOICE_MODEL', 'whisper-1'),
    ],

];
