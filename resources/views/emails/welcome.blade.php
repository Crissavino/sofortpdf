@extends('emails.layout')

@section('subject', 'Willkommen bei sofortpdf.com')

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">Willkommen bei sofortpdf.com, {{ $user->name }}!</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Vielen Dank fuer Ihre Anmeldung bei sofortpdf.com. Ihr Konto wurde erfolgreich erstellt.
    </p>

    @if($password)
    <div style="background-color: #f3f4f6; border-radius: 8px; padding: 16px; margin: 0 0 24px 0;">
        <p style="margin: 0 0 8px 0; font-size: 15px; color: #374151; font-weight: 600;">Ihre Zugangsdaten:</p>
        <p style="margin: 0 0 4px 0; font-size: 15px; color: #374151;">E-Mail: <strong>{{ $user->email }}</strong></p>
        <p style="margin: 0 0 12px 0; font-size: 15px; color: #374151;">Ihr Passwort lautet: <strong style="color: #1a56db;">{{ $password }}</strong></p>
        <p style="margin: 0; font-size: 13px; color: #6b7280; font-style: italic;">
            Bitte aendern Sie Ihr Passwort nach der ersten Anmeldung.
        </p>
    </div>
    @endif

    <p style="margin: 0 0 8px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Mit sofortpdf.com stehen Ihnen leistungsstarke PDF-Tools zur Verfuegung:
    </p>

    <ul style="margin: 0 0 24px 0; padding-left: 20px; font-size: 15px; color: #374151; line-height: 1.8;">
        <li>PDF-Dateien zusammenfuegen und aufteilen</li>
        <li>PDF-Dateien komprimieren</li>
        <li>PDF in andere Formate umwandeln</li>
        <li>Und vieles mehr!</li>
    </ul>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Starten Sie jetzt und entdecken Sie alle Funktionen.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #1a56db; border-radius: 6px;">
                <a href="{{ url('/anmelden') }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    Jetzt anmelden
                </a>
            </td>
        </tr>
    </table>
@endsection
