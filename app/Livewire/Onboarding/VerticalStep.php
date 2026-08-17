<?php

namespace App\Livewire\Onboarding;

use App\Models\Business;
use App\Models\BusinessVertical;
use Livewire\Component;

class VerticalStep extends Component
{
    public ?int $businessId = null;

    public string $selectedVertical = '';

    public const VERTICALS = [
        ['slug' => 'care', 'label' => 'Care & support', 'description' => 'Home care, elderly support'],
        ['slug' => 'cleaning', 'label' => 'Cleaning', 'description' => 'Homes, offices, decluttering'],
        ['slug' => 'real_estate', 'label' => 'Real estate', 'description' => 'Sales, lettings, valuations'],
        ['slug' => 'fitness', 'label' => 'Fitness', 'description' => 'Studios, personal training'],
        ['slug' => 'trades', 'label' => 'Trades', 'description' => 'Plumbing, electrical, handyman'],
        ['slug' => 'beauty', 'label' => 'Beauty & salon', 'description' => 'Hair, nails, beauty services'],
        ['slug' => 'legal', 'label' => 'Legal', 'description' => 'Law firms, legal advice'],
        ['slug' => 'automotive', 'label' => 'Automotive', 'description' => 'Car repair, maintenance, sales'],
    ];

    public function mount(?int $businessId = null): void
    {
        $this->businessId = $businessId;

        if ($businessId) {
            $this->selectedVertical = Business::find($businessId)?->verticalType() ?? '';
        }
    }

    public function selectVertical(string $slug): void
    {
        $this->selectedVertical = $slug;
    }

    public function continue(): void
    {
        if (empty($this->selectedVertical)) {
            $this->addError('selectedVertical', 'Choose a business type to continue.');
            return;
        }

        try {
            $business = $this->businessId
                ? Business::findOrFail($this->businessId)
                : auth()->user()->businesses()->create(['is_active' => false]);

            $business->businessVertical()->updateOrCreate(
                ['business_id' => $business->id],
                ['vertical_type' => $this->selectedVertical]
            );

            $this->dispatch('step-completed', step: 'vertical', businessId: $business->id);
        } catch (\Exception $e) {
            \Log::error('[Onboarding\\VerticalStep] continue failed', ['error' => $e->getMessage()]);
            $this->addError('selectedVertical', 'Something went wrong. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.onboarding.vertical-step', ['verticals' => self::VERTICALS]);
    }
}
