<x-layouts.app title="Settings — BusinessBots">
    <div class="mx-auto max-w-[700px] px-6 py-8 space-y-5">
        <h1 class="text-lg font-semibold">Settings</h1>

        @livewire('settings.business-settings', ['business' => $business])
        @livewire('settings.billing-panel')
    </div>
</x-layouts.app>
