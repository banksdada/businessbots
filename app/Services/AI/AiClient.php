<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class AiClient
{
    /**
     * Deliberately plain Http calls, not the openai-php/client SDK — that
     * package assumes OpenAI's own base URL in places, which fights against
     * being provider-agnostic. A REST call against config('ai.base_url')
     * works identically whether that's OpenAI, Azure OpenAI, or a
     * self-hosted OpenAI-compatible gateway.
     */
    public function chat(string $useCase, array $messages, float $temperature = 0.7, int $maxTokens = 300): string
    {
        $model = config("ai.models.{$useCase}") ?? config('ai.models.reply_generation');

        $response = Http::withToken(config('ai.api_key'))
            ->timeout(config('ai.timeout_seconds'))
            ->post(rtrim(config('ai.base_url'), '/') . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException("AI request failed ({$model}): {$response->body()}");
        }

        return trim($response->json('choices.0.message.content') ?? '');
    }
}
