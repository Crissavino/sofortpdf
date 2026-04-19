@extends('emails.layout')

@php
    $trialDays = (int) config('services.stripe.trial_days', 2);
    $trialEndsAt = $user->subscriptions()
        ->where('stripe_price_id', 'like', '%sofortpdf_%')
        ->where('status', 'trialing')
        ->first()?->trial_ends_at;
    $trialEndsFormatted = $trialEndsAt
        ? (app()->getLocale() === 'en' ? $trialEndsAt->format('Y-m-d') : $trialEndsAt->format('d.m.Y'))
        : null;
@endphp

@section('subject', __('email.trial_started_subject'))

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">{{ __('email.trial_started_heading', ['name' => $user->name]) }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {!! __('email.trial_started_intro', ['days' => $trialDays]) !!}
    </p>

    <p style="margin: 0 0 8px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.trial_started_features_intro') }}
    </p>

    <ul style="margin: 0 0 24px 0; padding-left: 20px; font-size: 15px; color: #374151; line-height: 1.8;">
        <li>{{ __('email.trial_started_feature_1') }}</li>
        <li>{{ __('email.trial_started_feature_2') }}</li>
        <li>{{ __('email.trial_started_feature_3') }}</li>
    </ul>

    <div style="background-color: #eff6ff; border-left: 4px solid #1a56db; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #1e40af;">
            @if($trialEndsFormatted)
                {!! __('email.trial_started_notice', ['date' => $trialEndsFormatted]) !!}
            @else
                {!! __('email.trial_started_notice_no_date') !!}
            @endif
        </p>
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #1a56db; border-radius: 6px;">
                <a href="{{ url('/' . app()->getLocale()) }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    {{ __('email.trial_started_cta') }}
                </a>
            </td>
        </tr>
    </table>
@endsection
