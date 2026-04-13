@extends('emails.layout')

@section('subject', 'Ihr Testzeitraum endet morgen')

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">Ihr Testzeitraum endet morgen, {{ $user->name }}</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Wir möchten Sie daran erinnern, dass Ihr kostenloser Testzeitraum bei sofortpdf.com <strong>morgen endet</strong>.
    </p>

    <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #92400e;">
            <strong>Was passiert danach?</strong> Nach Ablauf des Testzeitraums wird Ihr Abonnement automatisch aktiviert und die erste reguläre Zahlung wird eingezogen.
        </p>
    </div>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Nutzen Sie die verbleibende Zeit und testen Sie alle Premium-Funktionen von sofortpdf.com. So stellen Sie sicher, dass unsere Tools genau das Richtige für Sie sind.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #1a56db; border-radius: 6px;">
                <a href="{{ url('/tools') }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    Jetzt PDF-Tools nutzen
                </a>
            </td>
        </tr>
    </table>
@endsection
