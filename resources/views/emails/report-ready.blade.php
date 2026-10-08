<x-mail::message>
# Your report is ready

Hi {{ $request->user->name }},

Your report for "{{ $request->title }}" has been reviewed and is ready to read.

<x-mail::button :url="route('problems.show', $request)">
Read your report
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
