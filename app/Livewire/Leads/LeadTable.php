<?php

namespace App\Livewire\Leads;

use App\Models\Lead;
use Livewire\Component;
use Livewire\WithPagination;

class LeadTable extends Component
{
    use WithPagination;

    public string $intentFilter = 'all';
    public string $sortBy = 'newest';

    public const INTENT_FILTERS = ['all', 'inquiry', 'schedule', 'complaint'];
    public const SORT_OPTIONS = ['newest' => 'Newest first', 'oldest' => 'Oldest first'];

    protected $queryString = ['intentFilter', 'sortBy'];

    public function updatingIntentFilter(): void
    {
        $this->resetPage();
    }

    public function markClosed(int $leadId): void
    {
        try {
            $lead = Lead::where('business_id', $this->currentBusinessId())->findOrFail($leadId);
            $lead->update(['status' => 'closed']);
        } catch (\Exception $e) {
            \Log::error('[Leads\\LeadTable] markClosed failed', ['lead_id' => $leadId, 'error' => $e->getMessage()]);
            $this->addError('leads', 'Could not update that lead. Please try again.');
        }
    }

    public function render()
    {
        $query = Lead::where('business_id', $this->currentBusinessId());

        if ($this->intentFilter !== 'all') {
            $query->where('intent', $this->intentFilter);
        }

        $query->orderBy('created_at', $this->sortBy === 'newest' ? 'desc' : 'asc');

        return view('livewire.leads.lead-table', [
            'leads' => $query->paginate(20),
        ]);
    }

    private function currentBusinessId(): int
    {
        return auth()->user()->activeBusiness()->id;
    }
}
