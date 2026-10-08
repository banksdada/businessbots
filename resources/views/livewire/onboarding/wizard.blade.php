<div class="flex justify-center px-4 sm:px-6 py-10 sm:py-16">
    <div class="w-full max-w-[560px] card p-6 sm:p-8">

        <x-onboarding.step-indicator :steps="\App\Livewire\Onboarding\Wizard::steps()" :current="$step" />

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
