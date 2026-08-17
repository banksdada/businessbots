<?php

namespace App\Services\WhatsApp;

use App\Services\AI\AiClient;

class IntentClassifier
{
    public function __construct(private readonly AiClient $ai) {}

    public const INTENTS = ['inquiry', 'schedule', 'complaint', 'opt_out', 'opt_in', 'other'];

    /** Keywords checked before ever calling the AI — cheaper, faster, and 100% reliable for compliance. */
    private const OPT_OUT_KEYWORDS = ['stop', 'unsubscribe', 'opt out', 'optout', 'cancel'];
    private const OPT_IN_KEYWORDS = ['start', 'unstop', 'subscribe', 'opt in', 'optin'];

    public function classify(string $message): string
    {
        $normalized = strtolower(trim($message));

        if (in_array($normalized, self::OPT_OUT_KEYWORDS, true)) {
            return 'opt_out';
        }

        if (in_array($normalized, self::OPT_IN_KEYWORDS, true)) {
            return 'opt_in';
        }

        try {
            $intent = strtolower($this->ai->chat(
                useCase: 'classification',
                messages: [
                    [
                        'role' => 'system',
                        'content' => 'Classify the customer message intent. Respond with exactly one word: '
                            . 'inquiry, schedule, complaint, or other.',
                    ],
                    ['role' => 'user', 'content' => $message],
                ],
                temperature: 0,
                maxTokens: 5,
            ));

            return in_array($intent, self::INTENTS, true) ? $intent : 'other';
        } catch (\Exception $e) {
            \Log::error('[IntentClassifier] classification failed', ['error' => $e->getMessage()]);
            return 'other'; // never block the reply pipeline on a classification failure
        }
    }
}
