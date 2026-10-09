<?php

namespace App\Livewire\Problems;

use App\Models\ProblemRequest;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * The "we're working on it" card on a request page. Polls so the client sees
 * the steps move on, and reloads into the finished report once it's approved.
 */
class RequestProgress extends Component
{
    #[Locked]
    public int $problemId;

    public const TIPS = [
        'Your plan will cover the likely cause, the simplest fix, what fixing it could save you and first steps for this week.',
        'You do not need to understand the technology. Like a taxi ride, what matters is getting there, not which car takes you.',
        'We start with the simplest fix that works. No over-complicated systems you will have to babysit.',
        'Have a rough budget in mind. It makes it much easier to choose between the options in your plan.',
        'Think about who would be involved in fixing this. Bringing them in early helps changes stick.',
        'Small steps count. Most plans start with one thing you can try in the next few days.',
        'Every plan is read by a real person before you see it, so it fits your organisation and not just the theory.',
        'Want a hand putting the fix in place? Just ask. We agree what is included and when it is done before anything starts.',
    ];

    public function mount(ProblemRequest $problem): void
    {
        $this->problemId = $problem->id;
    }

    public function render()
    {
        $problem = ProblemRequest::findOrFail($this->problemId);

        if ($problem->isReady()) {
            $this->redirect(route('problems.show', $problem));
        }

        return view('livewire.problems.request-progress', [
            'problem' => $problem,
            'email' => auth()->user()->email,
            'tips' => self::TIPS,
        ]);
    }
}
