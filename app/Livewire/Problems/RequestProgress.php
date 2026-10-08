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
        'Your plan will include a short summary, what is likely causing the problem, a few options and first steps you can start this week.',
        'Have a rough budget in mind. It makes it much easier to choose between the options in your plan.',
        'Think about who would be involved in fixing this. Bringing them in early helps changes stick.',
        'Small steps count. Most plans start with one thing you can try in the next few days.',
        'Every plan is read by a real person before you see it, so it fits your organisation and not just the theory.',
        'If you would like a hand putting your plan in place, just ask. That is what we are here for.',
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
