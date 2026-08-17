<?php

namespace Tests\Unit;

use App\Models\Business;
use App\Models\Lead;
use Tests\Unit\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_belongs_to_business(): void
    {
        $business = Business::factory()->create();
        $lead = Lead::factory()->create(['business_id' => $business->id]);

        $this->assertInstanceOf(Business::class, $lead->business);
        $this->assertEquals($business->id, $lead->business->id);
    }

    public function test_lead_defaults_to_new_status(): void
    {
        $lead = Lead::factory()->create();

        $this->assertEquals('new', $lead->status);
    }

    public function test_lead_is_not_escalated_by_default(): void
    {
        $lead = Lead::factory()->create();

        $this->assertFalse($lead->escalated);
    }

    public function test_lead_can_be_escalated(): void
    {
        $lead = Lead::factory()->escalated()->create();

        $this->assertTrue($lead->escalated);
    }
}
