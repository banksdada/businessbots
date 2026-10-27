<?php

namespace Tests\Feature;

use App\Livewire\Onboarding\ConnectStep;
use App\Models\Business;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WhatsAppRemovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_whatsapp_webhook_is_not_registered(): void
    {
        $this->get('/webhooks/whatsapp')->assertNotFound();
        $this->post('/webhooks/whatsapp')->assertNotFound();

        config(['features.social' => true]);

        $this->get('/webhooks/whatsapp')->assertNotFound();
        $this->post('/webhooks/whatsapp')->assertNotFound();
    }

    public function test_whatsapp_is_not_an_available_channel(): void
    {
        $this->assertNull(config('channels.whatsapp'));
        $this->assertNull(config('services.meta.whatsapp_token'));
        $this->assertNull(config('services.meta.whatsapp_phone_number_id'));
        $this->assertNull(config('services.meta.webhook_verify_token'));

        $this->assertNotContains('whatsapp', array_column(ConnectStep::CHANNELS, 'key'));
    }

    public function test_onboarding_can_finish_without_connecting_a_channel(): void
    {
        $business = Business::factory()->create(['is_active' => false]);

        Livewire::test(ConnectStep::class, ['businessId' => $business->id])
            ->call('finish')
            ->assertHasNoErrors()
            ->assertDispatched('step-completed');
    }

    public function test_public_copy_no_longer_advertises_whatsapp(): void
    {
        config(['features.social' => false]);

        $this->get('/')->assertOk()->assertDontSee('WhatsApp');
        $this->get('/terms')->assertOk()->assertDontSee('WhatsApp');
        $this->get('/privacy')->assertOk()->assertDontSee('WhatsApp');
    }
}
