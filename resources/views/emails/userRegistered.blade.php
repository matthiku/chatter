@component('mail::message')

A new user has registerd

@component('mail::table')
| Tag           | Value              |
| ------------- |:------------------:|
| Name          | {{ $user->name }}  |
| Email         | {{ $user->email }} |
| UserName      | {{ $user->username }} |
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent