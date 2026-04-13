@extends('emails.layout')

@section('subject', __('email.trial_ending_subject'))

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">{{ __('email.trial_ending_heading', ['name' => $user->name]) }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {!! __('email.trial_ending_intro') !!}
    </p>

    <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #92400e;">
            {!! __('email.trial_ending_notice') !!}
        </p>
    </div>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.trial_ending_body') }}
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #1a56db; border-radius: 6px;">
                <a href="{{ url('/tools') }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    {{ __('email.trial_ending_cta') }}
                </a>
            </td>
        </tr>
    </table>
@endsection
