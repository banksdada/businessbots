<?php

namespace Tests\Unit;

use App\Models\Business;
use App\Models\User;
use Tests\Unit\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BusinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $business->owner);
        $this->assertEquals($user->id, $business->owner->id);
    }

    public function test_business_has_one_business_vertical(): void
    {
        $business = Business::factory()->create();

        $this->assertNull($business->businessVertical);
    }

    public function test_business_has_many_leads(): void
    {
        $business = Business::factory()->create();

        $this->assertCount(0, $business->leads);
    }

    public function test_business_scope_active(): void
    {
        Business::factory()->active()->create();
        Business::factory()->create(['is_active' => false]);

        $active = Business::active()->get();

        $this->assertCount(1, $active);
    }

    public function test_business_get_filament_name(): void
    {
        $business = Business::factory()->create(['name' => 'Test Cleaning Co']);

        $this->assertEquals('Test Cleaning Co', $business->getFilamentName());
    }

    public function test_business_vertical_type_returns_null_when_no_vertical(): void
    {
        $business = Business::factory()->create();

        $this->assertNull($business->verticalType());
    }
}
