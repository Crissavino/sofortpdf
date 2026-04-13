@extends('emails.layout')

@section('subject', __('email.subscription_canceled_subject'))

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">{{ __('email.subscription_canceled_heading', ['name' => $user->name]) }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.subscription_canceled_intro') }}
    </p>

    <div style="background-color: #eff6ff; border-left: 4px solid #1a56db; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #1e40af;">
            {!! __('email.subscription_canceled_notice') !!}
        </p>
    </div>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.subscription_canceled_body') }}
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #1a56db; border-radius: 6px;">
                <a href="{{ url('/pricing') }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    {{ __('email.subscription_canceled_cta') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin: 24px 0 0 0; font-size: 13px; color: #6b7280; line-height: 1.6;">
        {{ __('email.subscription_canceled_feedback') }}
    </p>
@endsection
