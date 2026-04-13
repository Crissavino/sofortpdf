@extends('emails.layout')

@section('subject', 'Ihr Abonnement wurde gekündigt')

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">Ihr Abonnement wurde gekündigt, {{ $user->name }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Wir bestätigen, dass Ihr sofortpdf.com-Abonnement gekündigt wurde.
    </p>

    <div style="background-color: #eff6ff; border-left: 4px solid #1a56db; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #1e40af;">
            <strong>Hinweis:</strong> Sie haben weiterhin Zugriff auf alle Premium-Funktionen bis zum Ende Ihres aktuellen Abrechnungszeitraums. Danach wird Ihr Konto auf den kostenlosen Plan zurückgesetzt.
        </p>
    </div>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Es tut uns leid, Sie gehen zu sehen. Falls Sie Ihre Meinung ändern, können Sie jederzeit Ihr Abonnement erneut aktivieren.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #1a56db; border-radius: 6px;">
                <a href="{{ url('/pricing') }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    Erneut abonnieren
                </a>
            </td>
        </tr>
    </table>

    <p style="margin: 24px 0 0 0; font-size: 13px; color: #6b7280; line-height: 1.6;">
        Falls Sie Feedback für uns haben, antworten Sie einfach auf diese E-Mail. Wir würden gerne erfahren, wie wir uns verbessern können.
    </p>
@endsection
