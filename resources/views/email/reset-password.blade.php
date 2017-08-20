@component('mail::message')
# Pssword reset

Your Barq password was reset to: $password:

Thanks,<br>
{{ config('app.name') }}
@endcomponent
