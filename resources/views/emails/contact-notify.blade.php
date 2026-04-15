@extends('emails.layout')

@section('subject', 'Neue Kontaktanfrage — sofortpdf.com')

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">Neue Kontaktanfrage</h1>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 20px 0; font-size: 14px; color: #374151;">
        <tr>
            <td style="padding: 6px 0; color: #6b7280; width: 120px;">Name:</td>
            <td style="padding: 6px 0; font-weight: 600;">{{ $contactName }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; color: #6b7280;">E-Mail:</td>
            <td style="padding: 6px 0;"><a href="mailto:{{ $contactEmail }}" style="color: #1a56db;">{{ $contactEmail }}</a></td>
        </tr>
        <tr>
            <td style="padding: 6px 0; color: #6b7280;">Sprache:</td>
            <td style="padding: 6px 0;">{{ strtoupper($contactLocale) }}</td>
        </tr>
        @if($contactIp)
        <tr>
            <td style="padding: 6px 0; color: #6b7280;">IP:</td>
            <td style="padding: 6px 0; font-family: monospace; font-size: 13px;">{{ $contactIp }}</td>
        </tr>
        @endif
        @if($contactUserAgent)
        <tr>
            <td style="padding: 6px 0; color: #6b7280; vertical-align: top;">User-Agent:</td>
            <td style="padding: 6px 0; font-size: 12px; color: #6b7280; word-break: break-all;">{{ $contactUserAgent }}</td>
        </tr>
        @endif
    </table>

    <p style="margin: 0 0 8px 0; font-size: 14px; color: #6b7280; font-weight: 600;">Nachricht:</p>

    <div style="background-color: #f3f4f6; border-left: 3px solid #1a56db; border-radius: 4px; padding: 14px 16px; margin: 0 0 24px 0;">
        <p style="margin: 0; font-size: 14px; color: #111827; line-height: 1.6; white-space: pre-wrap;">{{ $contactMessage }}</p>
    </div>

    <p style="margin: 0; font-size: 13px; color: #6b7280;">
        Klicken Sie auf „Antworten", um direkt <strong>{{ $contactEmail }}</strong> zu antworten.
    </p>
@endsection
