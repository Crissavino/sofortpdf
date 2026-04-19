@extends('emails.layout')

@section('subject', __('email.payment_failed_subject'))

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">{{ __('email.payment_failed_heading', ['name' => $user->name]) }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.payment_failed_intro') }}
    </p>

    <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #991b1b;">
            {!! __('email.payment_failed_notice') !!}
        </p>
    </div>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.payment_failed_body') }}
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #ef4444; border-radius: 6px;">
                <a href="{{ url('/' . app()->getLocale() . '/dashboard/billing') }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    {{ __('email.payment_failed_cta') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin: 24px 0 0 0; font-size: 13px; color: #6b7280; line-height: 1.6;">
        {{ __('email.payment_failed_help') }}
    </p>
@endsection
