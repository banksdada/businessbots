<x-layouts.app :title="'Contact us — ' . config('app.name')">

    <section class="mx-auto max-w-[560px] px-4 sm:px-6 py-12 sm:py-20">
        <div class="text-center mb-8">
            <x-person name="owner-explain" bg="bg-sun" class="w-24 h-24 mx-auto mb-5" />
            <h1 class="text-3xl font-bold">Talk to us</h1>
            <p class="mt-2 text-text-secondary">Questions about pricing, or want a hand putting a fix in place? Send us a message and a real person will reply by email.</p>
        </div>

        <form method="POST" action="{{ route('contact.send') }}" class="card p-6 sm:p-8 space-y-5" novalidate>
            @csrf

            {{-- Spam trap: hidden from people, bots fill it in. --}}
            <div class="hidden" aria-hidden="true">
                <label for="website">Leave this empty</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label for="name" class="field-label">Your name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $name) }}" required maxlength="100" class="field-input" autocomplete="name">
                @error('name') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="field-label">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required class="field-input" autocomplete="email" placeholder="you@example.com">
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="organisation" class="field-label">Organisation <span class="font-normal text-text-muted">(optional)</span></label>
                <input type="text" id="organisation" name="organisation" value="{{ old('organisation') }}" maxlength="150" class="field-input" autocomplete="organization">
                @error('organisation') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="message" class="field-label">Message</label>
                <textarea id="message" name="message" rows="5" required class="field-input"
                    placeholder="How can we help?">{{ old('message', $about ? "I'd like help putting the fix in place for: {$about}" : '') }}</textarea>
                @error('message') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary w-full py-3">Send message</button>
        </form>
    </section>

</x-layouts.app>
