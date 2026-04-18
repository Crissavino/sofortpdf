@extends('emails.layout')

@section('subject', __('email.download_ready_subject'))

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">{{ __('email.download_ready_heading', ['name' => $user->name]) }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        {{ __('email.download_ready_service_' . str_replace('-', '_', $tool), [], __('email.download_ready_intro')) }}
    </p>

    <div style="background-color: #f3f4f6; border-radius: 8px; padding: 16px; margin: 0 0 24px 0;">
        <p style="margin: 0; font-size: 15px; color: #374151;">
            <strong>{{ __('email.download_ready_file_label') }}</strong> {{ $filename }}
        </p>
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #059669; border-radius: 6px;">
                <a href="{{ $downloadUrl }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    {{ __('email.download_ready_cta') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin: 24px 0 0 0; font-size: 13px; color: #6b7280; line-height: 1.6;">
        {{ __('email.download_ready_expiry_notice') }}
    </p>
@endsection
