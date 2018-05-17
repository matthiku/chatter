@component('mail::message')
# There's a new message in your chat room "{{ $room->name }}"

Dear {{ $user->username }},

You have opted to receive notifications when there are new messages in your chat.

The latest message was from {{ $author->username }}:

@component('mail::panel')
  @if ($message->filename && $message->filetype === 'image')
    <img src=" {{ url('/images') . '/' . $message->filename }}" width="350">
  @else
    "{{ $message->message }}"
  @endif

@endcomponent


@component('mail::button', ['url' => url('/').'?room='.$room->id])
Open chatroom
@endcomponent

<small>To unsubscribe from these emails, open the chat room
and click on the settings icon in the top left corner.</small>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
