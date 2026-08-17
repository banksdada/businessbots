<?php

namespace App\Services\WhatsApp;

use App\Models\Business;
use App\Services\AI\AiClient;

class ReplyGenerator
{
    public function __construct(private readonly AiClient $ai) {}
    /**
     * Generate a reply using the business's vertical config (tone, lead questions)
     * from vertical_configs + business_verticals.industry_config overrides —
     * this is the mechanism that makes one AI work for 8 different industries.
     */
    public function generate(Business $business, string $intent, string $message): string
    {
        $config = $business->businessVertical?->resolvedConfig() ?? [];
        $tone = $config['default_tone'] ?? 'friendly, professional';
        $leadQuestions = implode(', ', $config['lead_questions'] ?? []);

        try {
            $reply = $this->ai->chat(
                useCase: 'reply_generation',
                messages: [
                    [
                        'role' => 'system',
                        'content' => "You are replying on WhatsApp for {$business->name}, a "
                            . ($config['label'] ?? 'small') . " business in {$business->location}. "
                            . "Tone: {$tone}. Keep replies under 300 characters, warm, and specific. "
                            . "If useful, ask one of these qualifying questions: {$leadQuestions}. "
                            . "Never invent prices, availability, or promises you can't verify.",
                    ],
                    ['role' => 'user', 'content' => $message],
                ],
                temperature: 0.6,
                maxTokens: 150,
            );

            return $reply ?: $this->fallbackReply($intent);
        } catch (\Exception $e) {
            \Log::error('[ReplyGenerator] generation failed', [
                'business_id' => $business->id,
                'error' => $e->getMessage(),
            ]);
            return $this->fallbackReply($intent);
        }
    }

    /** Never leave a customer with silence just because the AI call failed. */
    private function fallbackReply(string $intent): string
    {
        return match ($intent) {
            'complaint' => "Thanks for letting us know — someone from our team will follow up with you shortly.",
            default => "Thanks for reaching out! We'll get back to you shortly.",
        };
    }
}
