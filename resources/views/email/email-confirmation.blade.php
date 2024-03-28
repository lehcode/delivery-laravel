@component('mail::message')
# E-mail address confirmation

Please click <i>Confirm e-mail</i> link to activate your account:

@component('mail::button', ['url' => route('confirm-email.link', ['uuid' => $uuid, 'key' => $key])])
Confirm e-mail
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
