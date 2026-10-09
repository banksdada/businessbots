<?php

namespace Tests\Feature;

use App\Livewire\Onboarding\ProfileStep;
use App\Livewire\Onboarding\VerticalStep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_is_optional(): void
    {
        $user = User::factory()->create();
        $business = $user->businesses()->create(['is_active' => false]);

        Livewire::actingAs($user)
            ->test(ProfileStep::class, ['businessId' => $business->id])
            ->set('name', 'Bright Co Ltd')
            ->set('location', '')
            ->call('continue')
            ->assertHasNoErrors()
            ->assertDispatched('step-completed');

        $this->assertSame('Bright Co Ltd', $business->fresh()->name);
    }

    public function test_name_is_still_required(): void
    {
        $user = User::factory()->create();
        $business = $user->businesses()->create(['is_active' => false]);

        Livewire::actingAs($user)
            ->test(ProfileStep::class, ['businessId' => $business->id])
            ->set('name', '')
            ->call('continue')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_new_business_types_can_be_picked(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(VerticalStep::class)
            ->call('selectVertical', 'hospitality')
            ->call('continue')
            ->assertHasNoErrors();

        $this->assertSame('hospitality', $user->businesses()->first()->verticalType());
        $this->assertSame('Hospitality & food', VerticalStep::labelFor('hospitality'));
    }
}
