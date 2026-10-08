<?php

namespace App\Livewire\Onboarding;

use App\Models\Business;
use Livewire\Attributes\On;
use Livewire\Component;

class Wizard extends Component
{
    public string $step = 'vertical';

    public const STEPS = ['vertical', 'profile', 'connect'];

    /** The Connect (social channels) step only exists while social features are on. */
    public static function steps(): array
    {
        return config('features.social') ? self::STEPS : ['vertical', 'profile'];
    }

    public ?int $businessId = null;

    public function mount(?string $step = null): void
    {
        // Resume an in-progress business if one exists (unfinished onboarding)
        $business = auth()->user()->businesses()->where('is_active', false)->latest()->first();
        $this->businessId = $business?->id;

        $this->step = in_array($step, self::steps(), true) ? $step : 'vertical';
    }

    #[On('step-completed')]
    public function advance(string $step, ?int $businessId = null): void
    {
        if ($businessId) {
            $this->businessId = $businessId;
        }

        $steps = self::steps();
        $currentIndex = array_search($step, $steps, true);
        $nextStep = $steps[$currentIndex + 1] ?? null;

        if ($nextStep === null) {
            // Final step done — mark business active and go to the real dashboard
            Business::find($this->businessId)?->update(['is_active' => true]);
            $this->redirectRoute('dashboard');
            return;
        }

        $this->step = $nextStep;
        $this->redirectRoute('onboarding', ['step' => $nextStep], navigate: true);
    }

    public function render()
    {
        return view('livewire.onboarding.wizard');
    }
}
