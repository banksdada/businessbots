<?php

namespace App\Livewire\Problems;

use App\Models\ProblemRequest;
use App\Services\Advice\AdviceRequestService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Tell us a problem')]
class SubmitProblem extends Component
{
    public string $title = '';
    public string $description = '';
    public array $impacts = [];
    public ?float $hours_per_week = null;
    public ?int $hourly_cost = null;
    public string $desired_outcome = '';
    public string $already_tried = '';
    public string $current_tools = '';
    public ?int $staff_count = null;
    public string $urgency = 'medium';
    public string $help_wanted = 'advice';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'min:30', 'max:5000'],
            'impacts' => ['array'],
            'impacts.*' => [Rule::in(array_keys(ProblemRequest::IMPACTS))],
            'hours_per_week' => ['nullable', 'numeric', 'min:0.5', 'max:1000'],
            'hourly_cost' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'desired_outcome' => ['nullable', 'string', 'max:2000'],
            'already_tried' => ['nullable', 'string', 'max:3000'],
            'current_tools' => ['nullable', 'string', 'max:1000'],
            'staff_count' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'urgency' => ['required', Rule::in(array_keys(ProblemRequest::URGENCIES))],
            'help_wanted' => ['required', Rule::in(array_keys(ProblemRequest::HELP_OPTIONS))],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Give your problem a short title.',
            'description.required' => 'Describe the problem so we can help.',
            'description.min' => 'Please add a bit more detail (at least 30 characters). The more we know, the better the advice.',
            'hours_per_week.min' => 'Enter at least half an hour, or leave it blank.',
            'hours_per_week.numeric' => 'Enter a number of hours, like 5 or 2.5.',
            'hourly_cost.integer' => 'Enter a whole number of pounds, like 15.',
        ];
    }

    /** Clear a field's error as soon as the client fixes it. */
    public function updated(string $property): void
    {
        if ($this->getErrorBag()->has($property)) {
            $this->validateOnly($property);
        }
    }

    public function submit(AdviceRequestService $advice)
    {
        $validated = $this->validate();

        $user = auth()->user();
        $business = $user->activeBusiness();

        $request = $advice->submit($user, $business, $validated);

        session()->flash('status', 'Thanks! We\'re preparing your report and will email you when it\'s ready.');

        return $this->redirectRoute('problems.show', $request);
    }

    public function render()
    {
        return view('livewire.problems.submit-problem', [
            'urgencies' => ProblemRequest::URGENCIES,
            'helpOptions' => ProblemRequest::HELP_OPTIONS,
            'impactOptions' => ProblemRequest::IMPACTS,
            // Shown live as the client types, from their own numbers only.
            'yearlyCost' => ProblemRequest::yearlyCostText($this->hours_per_week, $this->hourly_cost),
        ]);
    }
}
