<?php

namespace Tests\Feature;

use App\Livewire\Onboarding\Wizard;
use App\Models\Business;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SocialFeatureSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_pages_are_hidden_when_switched_off(): void
    {
        config(['billing.required' => false]);
        $business = Business::factory()->active()->create();

        $this->get('/live-proof')->assertNotFound();
        $this->actingAs($business->owner)->get('/leads')->assertNotFound();
    }

    public function test_onboarding_skips_the_connect_step_when_social_is_off(): void
    {
        $this->assertSame(['vertical', 'profile'], Wizard::steps());

        config(['features.social' => true]);
        $this->assertSame(['vertical', 'profile', 'connect'], Wizard::steps());
    }

    public function test_home_page_shows_the_advice_offer(): void
    {
        $this->get('/')->assertOk()->assertSee("Tell us what's getting in the way.", false)->assertDontSee('WhatsApp');
    }

    public function test_home_page_uses_non_reply_metrics(): void
    {
        config(['features.social' => true]);

        $this->get('/')->assertOk()
            ->assertSee('Leads captured')
            ->assertDontSee('Avg response time');
    }

    public function test_live_proof_uses_non_reply_metrics(): void
    {
        config(['features.social' => true]);

        $this->get('/live-proof')->assertOk()
            ->assertSee('Leads this month')
            ->assertSee('Active businesses')
            ->assertSee('Open leads')
            ->assertDontSee('AI replies sent')
            ->assertDontSee('Avg response time');
    }
}
