<?php

namespace App\Livewire\Problems;

use Livewire\Component;

/**
 * A "speak your problem" button that records on the client's device and
 * transcribes it straight into the description field of the form around it.
 *
 * The audio never reaches Laravel as a stored file: the browser records a
 * blob, uploads it to /voice-notes/transcribe, and we send back the text.
 * The component itself holds no state — it just offers the button and
 * relays the result to its parent via a Livewire event.
 */
class VoiceNote extends Component
{
    public bool $supported = true;

    public function render()
    {
        return view('livewire.problems.voice-note');
    }
}