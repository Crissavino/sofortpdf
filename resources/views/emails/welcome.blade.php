@extends('emails.layout')

@section('subject', __('email.welcome_subject'))

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">{{ __('email.welcome_heading', ['name' => $user->name]) }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.welcome_intro') }}
    </p>

    @if($password)
    <div style="background-color: #f3f4f6; border-radius: 8px; padding: 16px; margin: 0 0 24px 0;">
        <p style="margin: 0 0 8px 0; font-size: 15px; color: #374151; font-weight: 600;">{{ __('email.welcome_credentials') }}</p>
        <p style="margin: 0 0 4px 0; font-size: 15px; color: #374151;">{{ __('email.welcome_email_label') }} <strong>{{ $user->email }}</strong></p>
        <p style="margin: 0 0 12px 0; font-size: 15px; color: #374151;">{{ __('email.welcome_password_label') }} <strong style="color: #1a56db;">{{ $password }}</strong></p>
        <p style="margin: 0; font-size: 13px; color: #6b7280; font-style: italic;">
            {{ __('email.welcome_password_notice') }}
        </p>
    </div>
    @endif

    <p style="margin: 0 0 8px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.welcome_tools_intro') }}
    </p>

    <ul style="margin: 0 0 24px 0; padding-left: 20px; font-size: 15px; color: #374151; line-height: 1.8;">
        <li>{{ __('email.welcome_tool_merge') }}</li>
        <li>{{ __('email.welcome_tool_compress') }}</li>
        <li>{{ __('email.welcome_tool_convert') }}</li>
        <li>{{ __('email.welcome_tool_more') }}</li>
    </ul>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.welcome_cta_intro') }}
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #1a56db; border-radius: 6px;">
                <a href="{{ url('/' . app()->getLocale() . '/' . config('locales.auth_slugs.' . app()->getLocale() . '.login', 'anmelden')) }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    {{ __('email.welcome_cta') }}
                </a>
            </td>
        </tr>
    </table>
@endsection
