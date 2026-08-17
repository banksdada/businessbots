<?php

namespace App\Jobs;

use App\Models\Business;
use App\Models\Lead;
use App\Models\WhatsAppConversation;
use App\Services\WhatsApp\IntentClassifier;
use App\Services\WhatsApp\RateLimiter;
use App\Services\WhatsApp\ReplyGenerator;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WhatsAppHandlerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        private readonly int $businessId,
        private readonly string $fromPhone,
        private readonly string $messageText,
        private readonly ?string $senderName = null,
    ) {}

    public function handle(
        IntentClassifier $classifier,
        ReplyGenerator $replyGenerator,
        WhatsAppService $whatsapp,
        RateLimiter $rateLimiter,
    ): void {
        $startedAt = now();

        try {
            $business = Business::findOrFail($this->businessId);
            $intent = $classifier->classify($this->messageText);

            // Opted-out numbers are suppressed for everything EXCEPT an explicit
            // re-opt-in (START) — the gate must not block the one message type
            // whose entire purpose is to lift the gate.
            $isOptedOut = Lead::hasOptedOut($this->businessId, $this->fromPhone);

            if ($isOptedOut && $intent !== 'opt_in') {
                $this->recordInboundOnly($business->id, 'opted_out — no reply sent');
                return;
            }

            $lead = Lead::create([
                'business_id' => $business->id,
                'phone' => $this->fromPhone,
                'name' => $this->senderName,
                'message' => $this->messageText,
                'intent' => in_array($intent, ['opt_out', 'opt_in'], true) ? 'other' : $intent,
                'status' => 'new',
            ]);

            $this->logConversation($lead->id, $this->messageText, 'human');

            if ($intent === 'opt_out') {
                $this->handleOptOut($lead, $whatsapp);
                return;
            }

            if ($intent === 'opt_in') {
                $this->handleOptIn($lead, $whatsapp);
                return;
            }

            // Rate limits protect the business's WhatsApp account standing with Meta,
            // not just our own infrastructure — exceeding them risks a platform ban.
            if ($rateLimiter->tooManyForNumber($business->id, $this->fromPhone)
                || $rateLimiter->tooManyForBusiness($business->id)) {
                \Log::warning('[WhatsAppHandlerJob] rate limited', ['business_id' => $business->id]);
                return;
            }

            $replyText = $replyGenerator->generate($business, $intent, $this->messageText);
            $replyText = $this->appendConsentNoticeIfFirstContact($lead, $business->id, $replyText);

            $whatsapp->sendMessage($business->id, $this->fromPhone, $replyText);

            $lead->update([
                'ai_reply_sent' => true,
                'ai_reply_text' => $replyText,
                'reply_time_seconds' => now()->diffInSeconds($startedAt),
            ]);

            $this->logConversation($lead->id, $replyText, 'ai');
        } catch (\Exception $e) {
            \Log::error('[WhatsAppHandlerJob] failed', [
                'business_id' => $this->businessId,
                'phone' => $this->fromPhone,
                'error' => $e->getMessage(),
            ]);
            // Let Laravel's retry/backoff handle transient failures (API timeouts etc.)
            // rather than swallowing the exception — but don't crash the whole queue worker.
            $this->fail($e);
        }
    }

    /**
     * STOP/UNSUBSCRIBE is handled as a hard compliance action, not a normal AI reply:
     * fixed confirmation text, permanent flag, no AI generation involved.
     */
    /**
     * START reverses a prior opt-out. Like handleOptOut, this is fixed text and
     * a direct database flag — not AI-generated — for the same reliability reason.
     */
    private function handleOptIn(Lead $lead, WhatsAppService $whatsapp): void
    {
        Lead::where('business_id', $lead->business_id)
            ->where('phone', $lead->phone)
            ->update(['opted_out' => false, 'opted_out_at' => null]);

        $confirmation = "You're resubscribed and will receive messages again. Reply STOP anytime to opt out.";

        try {
            $whatsapp->sendMessage($lead->business_id, $lead->phone, $confirmation);
            $lead->update(['ai_reply_sent' => true, 'ai_reply_text' => $confirmation]);
            $this->logConversation($lead->id, $confirmation, 'ai');
        } catch (\Exception $e) {
            // Same principle as opt-out: the flag change is what matters and is
            // already committed above — a failed confirmation send doesn't undo it.
            \Log::error('[WhatsAppHandlerJob] opt-in confirmation failed to send', ['error' => $e->getMessage()]);
        }
    }

    private function handleOptOut(Lead $lead, WhatsAppService $whatsapp): void
    {
        Lead::where('business_id', $lead->business_id)
            ->where('phone', $lead->phone)
            ->update(['opted_out' => true, 'opted_out_at' => now()]);

        $confirmation = "You've been unsubscribed and won't receive further messages. Reply START to opt back in.";

        try {
            $whatsapp->sendMessage($lead->business_id, $lead->phone, $confirmation);
            $lead->update(['ai_reply_sent' => true, 'ai_reply_text' => $confirmation]);
            $this->logConversation($lead->id, $confirmation, 'ai');
        } catch (\Exception $e) {
            // Opt-out is recorded regardless of whether the confirmation send succeeds —
            // the suppression must never depend on a successful outbound message.
            \Log::error('[WhatsAppHandlerJob] opt-out confirmation failed to send', ['error' => $e->getMessage()]);
        }
    }

    /**
     * First message from a new number gets a short consent/identity notice appended,
     * once — satisfies WhatsApp Business Messaging Policy transparency expectations
     * without turning every reply into a wall of legal text.
     */
    private function appendConsentNoticeIfFirstContact(Lead $lead, int $businessId, string $replyText): string
    {
        $isFirstContact = Lead::where('business_id', $businessId)
            ->where('phone', $lead->phone)
            ->where('id', '!=', $lead->id)
            ->doesntExist();

        if (! $isFirstContact) {
            return $replyText;
        }

        $lead->update(['consent_notice_sent_at' => now()]);

        return $replyText . "\n\n(Automated reply. Reply STOP anytime to opt out.)";
    }

    private function logConversation(int $leadId, string $message, string $sender): void
    {
        WhatsAppConversation::create([
            'lead_id' => $leadId,
            'message' => $message,
            'sender' => $sender,
            'sent_at' => now(),
        ]);
    }

    private function recordInboundOnly(int $businessId, string $note): void
    {
        \Log::info('[WhatsAppHandlerJob] inbound suppressed', [
            'business_id' => $businessId,
            'phone' => $this->fromPhone,
            'note' => $note,
        ]);
    }
}
