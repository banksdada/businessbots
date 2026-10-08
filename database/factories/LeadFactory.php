<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'phone' => fake()->numerify('+44##########'),
            'name' => fake()->name(),
            'status' => 'new',
            'intent' => 'inquiry',
            'message' => fake()->sentence(),
            'ai_reply_sent' => true,
        ];
    }

    /** A complaint — needsHumanAttention() flags it for manual follow-up. */
    public function escalated(): static
    {
        return $this->state(fn (array $attributes) => [
            'intent' => 'complaint',
        ]);
    }
}
