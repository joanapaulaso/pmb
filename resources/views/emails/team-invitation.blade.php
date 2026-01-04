@component('mail::message')

{{ __('Você foi convidado(a) para fazer parte do laboratório :team!', ['team' => $invitation->team->name]) }}

@if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::registration()))
{{ __('Se ainda não tiver uma conta, crie uma clicando no botão abaixo. Depois de criar a conta, clique no botão de aceitar convite para entrar no laboratório:') }}

@component('mail::button', ['url' => route('register')])
{{ __('Criar conta') }}
@endcomponent

{{ __('Se já possui conta, basta aceitar o convite no botão abaixo:') }}
@else
{{ __('Aceite o convite clicando no botão abaixo:') }}
@endif

@component('mail::button', ['url' => $acceptUrl])
{{ __('Aceitar convite') }}
@endcomponent

{{ __('Se você não esperava receber este convite, pode simplesmente ignorar este e-mail.') }}
@endcomponent
