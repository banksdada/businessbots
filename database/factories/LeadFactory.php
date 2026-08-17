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
            'intent' => null,
            'last_message' => fake()->sentence(),
            'escalated' => false,
        ];
    }

    public function escalated(): static
    {
        return $this->state(fn (array $attributes) => [
            'escalated' => true,
        ]);
    }
}
