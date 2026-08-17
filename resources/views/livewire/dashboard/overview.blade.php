<div wire:poll.30s="refreshStats" class="mx-auto max-w-[1000px] px-6 py-8">

    <div class="flex items-center gap-2 mb-1">
        <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
        <span class="text-[11px] font-semibold text-success tracking-wide">LIVE</span>
        <span class="text-[11px] text-text-muted">Updates every 30 seconds</span>
    </div>
    <h1 class="text-xl font-bold mb-6">Good morning, {{ $business->name }}</h1>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <x-stat-card label="Leads this week" :value="$leadsThisWeek" />
        <x-stat-card label="Avg reply time" :value="$avgReplyTime" />
        <x-stat-card label="Posts this week" :value="$postsThisWeek" />
        <x-stat-card label="Days without staff" :value="$daysWithoutStaff" />
    </div>

    {{-- Savings callout --}}
    <div class="bg-success-muted border border-success/20 rounded-xl p-4 mb-6">
        <div class="text-[10px] text-success tracking-wide mb-2">SAVED THIS MONTH — VS STAFF &amp; AGENCIES</div>
        <div class="text-2xl font-bold text-text-primary">£{{ number_format($moneySavedThisMonth) }}</div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        {{-- Recent leads --}}
        <div class="bg-surface border border-border rounded-xl p-5">
            <h2 class="text-sm font-semibold mb-3">Recent leads</h2>
            @forelse ($recentLeads as $lead)
                <div class="flex justify-between items-center py-2.5 border-b border-border last:border-0">
                    <div>
                        <div class="text-sm font-medium text-text-primary">{{ $lead['name'] }}</div>
                        <div class="text-xs text-text-muted">{{ $lead['message'] }}</div>
                    </div>
                    <x-badge :variant="$lead['status'] === 'scheduled' ? 'success' : 'accent'">
                        {{ ucfirst($lead['intent']) }}
                    </x-badge>
                </div>
            @empty
                <p class="text-sm text-text-muted py-4 text-center">No leads yet — they'll show up here as WhatsApp messages come in.</p>
            @endforelse
        </div>

        {{-- Recent AI activity --}}
        <div class="bg-surface border border-border rounded-xl p-5">
            <h2 class="text-sm font-semibold mb-3">AI activity</h2>
            @forelse ($recentActivity as $activity)
                <div class="flex gap-2.5 mb-3 last:mb-0">
                    <span class="w-2 h-2 rounded-full bg-accent mt-1.5 flex-shrink-0"></span>
                    <div>
                        <div class="text-xs text-text-primary">{{ $activity['label'] }}</div>
                        <div class="text-[11px] text-text-muted">{{ $activity['time'] }}</div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-text-muted py-4 text-center">Activity will appear here once your AI starts working.</p>
            @endforelse
        </div>
    </div>
</div>
