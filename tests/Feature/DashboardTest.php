<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_user_without_a_business_is_sent_to_onboarding(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('onboarding', ['step' => 'vertical']));
    }

    public function test_onboarding_page_loads_when_billing_is_required(): void
    {
        // The navbar's billing badge renders nothing for users without a
        // subscription; it must still output a root element for Livewire.
        config(['billing.required' => true]);
        $user = User::factory()->create();

        $this->actingAs($user)->get('/onboarding')->assertOk();
    }

    public function test_onboarded_user_can_access_dashboard(): void
    {
        config(['billing.required' => false]);
        $business = Business::factory()->active()->create();

        $this->actingAs($business->owner)->get('/dashboard')
            ->assertOk()
            ->assertSee('Your requests')
            ->assertSee('No requests yet');
    }

    public function test_subscription_is_required_when_billing_is_on(): void
    {
        config(['billing.required' => true]);
        $business = Business::factory()->active()->create();

        $this->actingAs($business->owner)->get('/dashboard')->assertRedirect(route('marketing.pricing'));
    }

    public function test_home_page_is_displayed(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_pricing_page_is_displayed(): void
    {
        $response = $this->get('/pricing');

        $response->assertStatus(200);
    }
}
