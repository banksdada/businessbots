<?php

namespace Tests\Unit;

use App\Models\Business;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

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

    public function test_new_lead_needs_human_attention(): void
    {
        $lead = Lead::factory()->create();

        $this->assertTrue($lead->needsHumanAttention());
    }

    public function test_closed_lead_does_not_need_human_attention(): void
    {
        $lead = Lead::factory()->create(['status' => 'closed']);

        $this->assertFalse($lead->needsHumanAttention());
    }

    public function test_complaint_needs_human_attention(): void
    {
        $lead = Lead::factory()->escalated()->create();

        $this->assertTrue($lead->needsHumanAttention());
    }

    public function test_ai_reply_fields_are_removed(): void
    {
        $this->assertFalse(Schema::hasColumn('leads', 'ai_reply_sent'));
        $this->assertFalse(Schema::hasColumn('leads', 'ai_reply_text'));
        $this->assertFalse(Schema::hasColumn('leads', 'reply_time_seconds'));
    }
}
