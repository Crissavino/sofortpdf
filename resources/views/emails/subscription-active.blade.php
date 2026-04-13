@extends('emails.layout')

@section('subject', 'Ihr Abonnement ist aktiv')

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">Ihr Abonnement ist jetzt aktiv, {{ $user->name }}!</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Vielen Dank für Ihr Vertrauen! Ihre erste Zahlung wurde erfolgreich verarbeitet und Ihr sofortpdf.com-Abonnement ist ab sofort aktiv.
    </p>

    <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #065f46;">
            <strong>Abonnement-Details:</strong><br>
            Status: Aktiv<br>
            Abrechnung: Monatlich
        </p>
    </div>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Sie haben jetzt uneingeschränkten Zugriff auf alle Premium-PDF-Tools. Nutzen Sie alle Funktionen von sofortpdf.com ohne Limits.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #1a56db; border-radius: 6px;">
                <a href="{{ url('/tools') }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    Zu Ihren PDF-Tools
                </a>
            </td>
        </tr>
    </table>
@endsection
