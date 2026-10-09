<x-mail::message>
# New message from the website

**From:** {{ $data['name'] }} ({{ $data['email'] }})<br>
@if (! empty($data['organisation']))
**Organisation:** {{ $data['organisation'] }}<br>
@endif

{{ $data['message'] }}

Press Reply to answer them directly.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
