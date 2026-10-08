<div class="flex justify-center px-4 sm:px-6 py-10 sm:py-16">
    <div class="w-full max-w-[560px] card p-6 sm:p-8">

        <div class="flex items-center gap-3 mb-6">
            <x-person name="manager-hijab" bg="bg-peach" class="w-12 h-12 shrink-0" />
            <p class="text-sm text-text-secondary"><span class="font-semibold text-text-primary">Let's get to know you.</span><br>A few quick questions so your advice fits your organisation.</p>
        </div>

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
