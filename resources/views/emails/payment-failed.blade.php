@extends('emails.layout')

@section('subject', 'Zahlung fehlgeschlagen — Aktion erforderlich')

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">Zahlung fehlgeschlagen, {{ $user->name }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Leider konnte Ihre letzte Zahlung für Ihr sofortpdf.com-Abonnement nicht verarbeitet werden.
    </p>

    <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #991b1b;">
            <strong>Wichtig:</strong> Bitte aktualisieren Sie Ihre Zahlungsinformationen so schnell wie möglich, um eine Unterbrechung Ihres Zugangs zu vermeiden.
        </p>
    </div>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Sie können Ihre Kreditkarte oder Zahlungsmethode ganz einfach über unser Abrechnungsportal aktualisieren. Klicken Sie dazu auf den Button unten.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #ef4444; border-radius: 6px;">
                <a href="{{ url('/billing') }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    Zahlungsmethode aktualisieren
                </a>
            </td>
        </tr>
    </table>

    <p style="margin: 24px 0 0 0; font-size: 13px; color: #6b7280; line-height: 1.6;">
        Falls Sie Fragen haben, antworten Sie einfach auf diese E-Mail. Wir helfen Ihnen gerne weiter.
    </p>
@endsection
