@extends('emails.layout')

@section('subject', __('email.contact_autoreply_subject'))

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">{{ __('email.contact_autoreply_heading', ['name' => $contactName]) }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.contact_autoreply_intro') }}
    </p>

    <p style="margin: 0 0 8px 0; font-size: 14px; color: #6b7280; font-weight: 600;">
        {{ __('email.contact_autoreply_your_message') }}
    </p>

    <div style="background-color: #f3f4f6; border-left: 3px solid #1a56db; border-radius: 4px; padding: 14px 16px; margin: 0 0 24px 0;">
        <p style="margin: 0; font-size: 14px; color: #374151; line-height: 1.6; white-space: pre-wrap;">{{ $contactMessage }}</p>
    </div>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.contact_autoreply_body') }}
    </p>

    <p style="margin: 0; font-size: 14px; color: #6b7280; line-height: 1.6;">
        {{ __('email.contact_autoreply_signature_line1') }}<br>
        {{ __('email.contact_autoreply_signature_line2') }}
    </p>
@endsection
