<?php

namespace App\Livewire\Settings;

use App\Models\Business;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BusinessSettings extends Component
{
    public int $businessId;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255')]
    public string $location = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    public string $verticalLabel = '';

    public function mount(Business $business): void
    {
        $this->businessId = $business->id;
        $this->name = $business->name ?? '';
        $this->location = $business->location ?? '';
        $this->description = $business->description ?? '';
        $this->verticalLabel = $business->businessVertical?->vertical_type ?? 'Not set';
    }

    public function save(): void
    {
        $this->validate();

        try {
            Business::findOrFail($this->businessId)->update([
                'name' => $this->name,
                'location' => $this->location,
                'description' => $this->description,
            ]);

            session()->flash('notice', 'Business details saved.');
        } catch (\Exception $e) {
            \Log::error('[Settings\\BusinessSettings] save failed', [
                'business_id' => $this->businessId,
                'error' => $e->getMessage(),
            ]);
            $this->addError('name', 'Could not save. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.settings.business-settings');
    }
}
