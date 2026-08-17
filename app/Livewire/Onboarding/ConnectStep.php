<?php

namespace App\Livewire\Onboarding;

use App\Models\Business;
use App\Models\ChannelSetting;
use Livewire\Component;

class ConnectStep extends Component
{
    public int $businessId;

    /** @var array<string, bool> */
    public array $connected = [
        'whatsapp' => false,
        'instagram' => false,
        'linkedin' => false,
        'gbp' => false,
    ];

    public const CHANNELS = [
        ['key' => 'whatsapp', 'label' => 'WhatsApp Business', 'required' => true],
        ['key' => 'instagram', 'label' => 'Instagram', 'required' => false],
        ['key' => 'linkedin', 'label' => 'LinkedIn', 'required' => false],
        ['key' => 'gbp', 'label' => 'Google Business Profile', 'required' => false],
    ];

    public function mount(int $businessId): void
    {
        $this->businessId = $businessId;
        $this->refreshConnectionStatus();
    }

    /**
     * Called after returning from an OAuth redirect, and on mount.
     * Cheap enough to also drive via wire:poll if OAuth runs in a popup.
     */
    public function refreshConnectionStatus(): void
    {
        $settings = ChannelSetting::where('business_id', $this->businessId)
            ->pluck('is_connected', 'platform');

        foreach (array_keys($this->connected) as $platform) {
            $this->connected[$platform] = (bool) ($settings[$platform] ?? false);
        }
    }

    public function connect(string $platform): void
    {
        // Redirects out to the OAuth provider; ChannelSettingsController handles the callback
        // and writes to channel_settings, then redirects back here.
        $this->redirect(route('channels.connect', $platform), navigate: false);
    }

    public function finish(): void
    {
        if (! $this->connected['whatsapp']) {
            $this->addError('whatsapp', 'Connect WhatsApp to finish setup — it\'s how leads reach you.');
            return;
        }

        $this->dispatch('step-completed', step: 'connect', businessId: $this->businessId);
    }

    public function render()
    {
        return view('livewire.onboarding.connect-step', ['channels' => self::CHANNELS]);
    }
}
