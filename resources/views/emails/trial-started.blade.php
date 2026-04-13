@extends('emails.layout')

@section('subject', 'Ihr Testzeitraum hat begonnen')

@section('content')
    <h1 style="margin: 0 0 16px 0; font-size: 22px; color: #111827;">Ihr Testzeitraum hat begonnen, {{ $user->name }}!</h1>

    <p style="margin: 0 0 16px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Ihr kostenloser Testzeitraum bei sofortpdf.com ist jetzt aktiv. Sie haben <strong>{{ config('services.stripe.trial_days', 2) }} Tage</strong> lang vollen Zugriff auf alle Premium-Funktionen.
    </p>

    <p style="margin: 0 0 8px 0; font-size: 15px; color: #374151; line-height: 1.6;">
        Was Sie jetzt tun können:
    </p>

    <ul style="margin: 0 0 24px 0; padding-left: 20px; font-size: 15px; color: #374151; line-height: 1.8;">
        <li>Unbegrenzt PDF-Dateien verarbeiten</li>
        <li>Alle Premium-Tools nutzen</li>
        <li>Dateien ohne Einschränkungen konvertieren</li>
    </ul>

    <div style="background-color: #eff6ff; border-left: 4px solid #1a56db; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #1e40af;">
            <strong>Hinweis:</strong> Ihr Testzeitraum endet am <strong>{{ $user->subscriptions()->where('stripe_price_id', 'like', '%sofortpdf_%')->where('status', 'trialing')->first()?->trial_ends_at?->format('d.m.Y') }}</strong>.
            Danach wird Ihr Abonnement automatisch aktiviert.
        </p>
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #1a56db; border-radius: 6px;">
                <a href="{{ url('/tools') }}" style="display: inline-block; padding: 12px 28px; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none;">
                    PDF-Tools entdecken
                </a>
            </td>
        </tr>
    </table>
@endsection
