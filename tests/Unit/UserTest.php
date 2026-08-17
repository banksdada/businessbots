<?php

namespace Tests\Unit;

use App\Models\Business;
use App\Models\User;
use Tests\Unit\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_businesses(): void
    {
        $user = User::factory()->create();

        $this->assertCount(0, $user->businesses);
    }

    public function test_user_active_business_returns_first_active(): void
    {
        $user = User::factory()->create();
        Business::factory()->create(['user_id' => $user->id, 'is_active' => false]);
        $active = Business::factory()->active()->create(['user_id' => $user->id]);

        $this->assertEquals($active->id, $user->activeBusiness()->id);
    }

    public function test_user_active_business_returns_null_when_none_active(): void
    {
        $user = User::factory()->create();
        Business::factory()->create(['user_id' => $user->id, 'is_active' => false]);

        $this->assertNull($user->activeBusiness());
    }

    public function test_user_is_authenticatable(): void
    {
        $user = User::factory()->create();

        $this->assertNotEmpty($user->email);
        $this->assertNotEmpty($user->name);
    }
}
