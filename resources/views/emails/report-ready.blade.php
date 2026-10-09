<x-mail::message>
# Your plan is ready

Hi {{ $request->user->name }},

Your plan to fix "{{ $request->title }}" has been checked by a real person and is ready to read. It covers the simplest fix, what it could save you and the first steps to take.

<x-mail::button :url="route('problems.show', $request)">
Read your report
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
