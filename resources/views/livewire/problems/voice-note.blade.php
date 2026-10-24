@php
    // Web Speech API is supported in every mobile browser we actually care
    // about (Chrome, Edge, Safari, Samsung Internet). Firefox for iOS
    // deliberately blocks it, so we degrade to "type it" rather than
    // pretending the button does something.
    $supported = true;
@endphp

<div x-data="voiceNote()"
     x-init="init()"
     class="flex items-center gap-3 rounded-xl bg-surface border border-border p-3 sm:p-4"
     wire:ignore>

    <button type="button"
            x-ref="recordBtn"
            @click="toggle()"
            :disabled="busy || !supported"
            :class="recording
                ? 'bg-accent2-dark text-text-primary'
                : 'bg-surface hover:bg-surface-secondary text-text-secondary'"
            class="shrink-0 w-11 h-11 rounded-full inline-flex items-center justify-center transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :title="recording ? 'Stop recording' : 'Record your problem'">

        <template x-if="!recording">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 1a4 4 0 014 4v7a4 4 0 11-8 0V5a4 4 0 014-4z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-14 0M12 19v3"/>
            </svg>
        </template>
        <template x-if="recording">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M8 5h8v9H8z"/>
            </svg>
        </template>
    </button>

    <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-text-primary" x-text="label()"></p>
        <p class="text-xs text-text-muted mt-0.5" x-text="hint()"></p>

        <template x-if="error">
            <p class="mt-1.5 text-xs text-accent2-dark" x-text="error"></p>
        </template>
        <template x-if="result">
            <p class="mt-1.5 text-xs text-text-secondary">
                <span class="font-semibold text-text-primary">Heard:</span>
                <span x-text="result"></span>
            </p>
        </template>
    </div>
</div>

<script>
function voiceNote() {
    return {
        recording: false,
        busy: false,
        result: '',
        error: '',
        recognition: null,
        // Web Speech API is supported in every mobile browser we actually care
        // about (Chrome, Edge, Safari, Samsung Internet). Firefox for iOS
        // deliberately blocks it, so we degrade to "type it" rather than
        // pretending the button does something.
        supported: !!(window.SpeechRecognition || window.webkitSpeechRecognition),

        init() {
            if (!this.supported) return;

            const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SR();
            this.recognition.continuous = false;
            this.recognition.interimResults = false;
            this.recognition.lang = 'en-GB';

            this.recognition.onresult = (e) => {
                const transcript = Array.from(e.results)
                    .map(r => r[0].transcript)
                    .join(' ')
                    .trim();
                if (transcript) {
                    this.result = transcript;
                    // Push it into the description field of the form around us.
                    this.$wire.set('description',
                        this.$wire.description
                            ? this.$wire.description + ' ' + transcript
                            : transcript
                    );
                    this.$wire.description && this.$wire.call('updated', 'description');
                }
                this.recording = false;
                this.busy = false;
            };

            this.recognition.onerror = (e) => {
                // 'no-speech' happens if you tap and nothing comes out — not an error.
                if (e.error === 'no-speech') {
                    this.error = 'We didn\'t hear anything. Try again.';
                } else if (e.error === 'not-allowed') {
                    this.error = 'Microphone access is blocked. Enable it in your browser settings.';
                } else {
                    this.error = 'That didn\'t work. Type it instead.';
                }
                this.recording = false;
                this.busy = false;
            };

            this.recognition.onend = () => {
                this.recording = false;
                this.busy = false;
            };
        },

        toggle() {
            if (!this.supported) return;
            if (this.busy) return;

            if (this.recording) {
                this.recognition.stop();
                return;
            }

            this.error = '';
            this.result = '';
            this.recording = true;
            this.busy = true;
            try {
                this.recognition.start();
            } catch (e) {
                this.error = 'That didn\'t work. Type it instead.';
                this.recording = false;
                this.busy = false;
            }
        },

        label() {
            if (this.busy && !this.recording) return 'Working…';
            if (this.recording) return 'Listening…';
            return 'Record your problem';
        },

        hint() {
            if (this.recording) return 'Tap to stop';
            if (this.busy) return 'Transcribing';
            return 'Tap to speak instead of typing';
        },
    }
}
</script>