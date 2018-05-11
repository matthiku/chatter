@component('mail::message')
# There's a new message in your chat room "{{ $room->name }}"

Dear {{ $user->username }},

You have opted to receive notifications when there are new messages in your chat.

This was the most recent message:

"{{ $message->message }}"


@component('mail::button', ['url' => env('APP_URL')])
Open chatroom
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
