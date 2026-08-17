<div class="min-h-screen bg-background flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-[520px] bg-surface border border-border rounded-2xl p-8">

        <x-onboarding.step-indicator :steps="\App\Livewire\Onboarding\Wizard::STEPS" :current="$step" />

        @switch($step)
            @case('vertical')
                @livewire('onboarding.vertical-step', ['businessId' => $businessId], key('step-vertical'))
                @break
            @case('profile')
                @livewire('onboarding.profile-step', ['businessId' => $businessId], key('step-profile'))
                @break
            @case('connect')
                @livewire('onboarding.connect-step', ['businessId' => $businessId], key('step-connect'))
                @break
        @endswitch

    </div>
</div>
