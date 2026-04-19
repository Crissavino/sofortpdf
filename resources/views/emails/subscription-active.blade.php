@extends('emails.layout')

@section('subject', __('email.subscription_active_subject'))

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">{{ __('email.subscription_active_heading', ['name' => $user->name]) }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.subscription_active_intro') }}
    </p>

    <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #065f46;">
            <strong>{{ __('email.subscription_active_details_title') }}</strong><br>
            {{ __('email.subscription_active_status') }}<br>
            {{ __('email.subscription_active_billing') }}
        </p>
    </div>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.subscription_active_body') }}
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #1a56db; border-radius: 6px;">
                <a href="{{ url('/' . app()->getLocale()) }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    {{ __('email.subscription_active_cta') }}
                </a>
            </td>
        </tr>
    </table>
@endsection
