@extends('emails.layout')

@section('subject', __('email.order_subject'))

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">{{ __('email.order_heading', ['name' => $user->name]) }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.order_intro') }}
    </p>

    <div style="background-color: #f0fdf4; border: 1px solid #dcfce7; border-radius: 8px; padding: 16px; margin: 0 0 24px 0;">
        <p style="margin: 0 0 8px 0; font-size: 15px; color: #374151; font-weight: 600;">{{ __('email.order_details_title') }}</p>
        <p style="margin: 0 0 4px 0; font-size: 15px; color: #374151;">{{ __('email.order_plan') }}</p>
        <p style="margin: 0 0 4px 0; font-size: 15px; color: #374151;">{{ __('email.order_amount', ['amount' => $amount]) }}</p>
        <p style="margin: 0; font-size: 13px; color: #6b7280; font-style: italic;">
            {{ __('email.order_cancel_notice') }}
        </p>
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #1a56db; border-radius: 6px;">
                <a href="{{ url('/' . app()->getLocale()) }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    {{ __('email.order_cta') }}
                </a>
            </td>
        </tr>
    </table>
@endsection
