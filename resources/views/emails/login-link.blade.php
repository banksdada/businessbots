<x-mail::message>
# Sign in to BusinessBots

Click the button below to sign in. This link is valid for 15 minutes and can only be used once.

<x-mail::button :url="$url">
    Sign in to BusinessBots
</x-mail::button>

If you did not request this link, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>