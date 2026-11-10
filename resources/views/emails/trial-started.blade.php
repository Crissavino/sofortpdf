@extends('emails.layout')

@php
    $trialDays = (int) config('services.stripe.trial_days', 2);
    // La tabla `subscriptions` es compartida entre marcas y su esquema es
    // agnostico de Stripe (ver el docblock de App\Models\Subscription): no
    // existen `stripe_price_id` ni `status`. `status` es un accessor, asi que
    // tampoco sirve en un where. Las dos clausulas anteriores consultaban
    // columnas inexistentes y el render tiraba `Unknown column`, que
    // EmailService::sendTrialStarted captura y loguea: el mail no le llegaba a
    // nadie, en silencio.
    //
    // La marca se filtra por `website_id` (el idiom del repo:
    // Customer::resolveCancellation, DashboardController) y "trialing" es
    // exactamente `is_trial_active` = true, porque la rama 'canceled' del
    // accessor exige !is_trial_active.
    $trialEndsAt = $user->subscriptions()
        ->where('website_id', config('services.bo.website_id'))
        ->where('is_trial_active', true)
        ->orderBy('id', 'desc')
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
