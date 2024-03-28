@component('mail::message')
# Account activation

Please click <i>Confirm registration</i> link to activate your account:

@component('mail::button', ['url' => route('activate.link', ['user' => $user_id, 'key' => $key])])
Confirm registration
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
