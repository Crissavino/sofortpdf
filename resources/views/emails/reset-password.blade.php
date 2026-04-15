@extends('emails.layout')

@section('subject', __('email.reset_subject'))

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">{{ __('email.reset_heading', ['name' => $user->name ?: $user->email]) }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.reset_intro') }}
    </p>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.reset_cta_intro') }}
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 0 24px 0;">
        <tr>
            <td style="background-color: #1a56db; border-radius: 6px;">
                <a href="{{ $resetUrl }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    {{ __('email.reset_cta') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 8px 0; font-size: 13px; color: #6b7280; line-height: 1.6;">
        {{ __('email.reset_expiry_notice', ['minutes' => config('auth.passwords.users.expire', 60)]) }}
    </p>

    <p style="margin: 0 0 16px 0; font-size: 13px; color: #6b7280; line-height: 1.6;">
        {{ __('email.reset_ignore_notice') }}
    </p>

    <p style="margin: 24px 0 4px 0; font-size: 13px; color: #9ca3af;">
        {{ __('email.reset_plain_url_label') }}
    </p>
    <p style="margin: 0; font-size: 12px; color: #6b7280; word-break: break-all;">
        <a href="{{ $resetUrl }}" style="color: #1a56db;">{{ $resetUrl }}</a>
    </p>
@endsection
