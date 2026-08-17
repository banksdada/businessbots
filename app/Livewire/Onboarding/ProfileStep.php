<?php

namespace App\Livewire\Onboarding;

use App\Models\Business;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ProfileStep extends Component
{
    public int $businessId;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255')]
    public string $location = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    public function mount(int $businessId): void
    {
        $this->businessId = $businessId;

        $business = Business::find($businessId);
        $this->name = $business?->name ?? '';
        $this->location = $business?->location ?? '';
        $this->description = $business?->description ?? '';
    }

    public function continue(): void
    {
        $this->validate();

        try {
            Business::findOrFail($this->businessId)->update([
                'name' => $this->name,
                'location' => $this->location,
                'description' => $this->description,
            ]);

            $this->dispatch('step-completed', step: 'profile', businessId: $this->businessId);
        } catch (\Exception $e) {
            \Log::error('[Onboarding\\ProfileStep] continue failed', ['error' => $e->getMessage()]);
            $this->addError('name', 'Something went wrong. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.onboarding.profile-step');
    }
}
