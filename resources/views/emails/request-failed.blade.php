<x-mail::message>
# An advice request failed

**{{ $request->business->name }}** asked: {{ $request->title }}

No draft was produced. The error was:

> {{ $request->error_message }}

You can retry it from the review screen once the problem is fixed.

<x-mail::button :url="url('/ops/problem-requests/' . $request->id . '/edit')">
Open the request
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
