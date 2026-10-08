<x-mail::message>
# A draft is ready to review

**{{ $request->business->name }}** asked: {{ $request->title }}

The AI has written a draft. Read it, edit it if needed, and approve it to send it to the client.

<x-mail::button :url="url('/ops/problem-requests/' . $request->id . '/edit')">
Review the draft
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
