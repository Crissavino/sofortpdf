@extends('layouts.app')

@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $pageTitle)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    @if($isEn)
        <h1 class="text-3xl font-bold mb-8">Privacy Policy</h1>

        <h2 class="text-xl font-semibold mt-8 mb-4">1. Data Controller</h2>
        <p class="mb-4 leading-relaxed">
            The data controller for data processing on this website is:<br><br>
            Muster GmbH<br>
            Musterstrasse 1<br>
            12345 Musterstadt, Germany<br>
            Email: datenschutz@sofortpdf.com
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">2. Data Collection on Our Website</h2>

        <h3 class="text-lg font-medium mt-6 mb-3">a) Server Log Files</h3>
        <p class="mb-4 leading-relaxed">
            The hosting provider of these pages automatically collects and stores information in so-called server log files,
            which your browser automatically transmits to us. These are:
        </p>
        <ul class="list-disc list-inside mb-4 leading-relaxed">
            <li>Browser type and version</li>
            <li>Operating system used</li>
            <li>Referrer URL</li>
            <li>Hostname of the accessing computer</li>
            <li>Time of the server request</li>
            <li>IP address</li>
        </ul>
        <p class="mb-4 leading-relaxed">
            This data is not merged with other data sources.
            The collection is based on Art. 6 (1) (f) GDPR.
        </p>

        <h3 class="text-lg font-medium mt-6 mb-3">b) Registration and User Account</h3>
        <p class="mb-4 leading-relaxed">
            During registration, we collect your name and email address. This data is processed
            for the provision of your user account and for contract fulfillment (Art. 6 (1) (b) GDPR).
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">3. Cookies</h2>
        <p class="mb-4 leading-relaxed">
            Our website uses cookies. These are small text files that your web browser stores on your
            device. Cookies help us make our services more user-friendly and secure.
        </p>
        <p class="mb-4 leading-relaxed">
            Technically necessary cookies are set on the basis of Art. 6 (1) (f) GDPR.
            We have a legitimate interest in storing cookies for the technically error-free and
            optimized provision of our services.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">4. Payment Processing via Stripe</h2>
        <p class="mb-4 leading-relaxed">
            We use the service Stripe (Stripe, Inc., 510 Townsend Street, San Francisco, CA 94103, USA) for
            payment processing. When paying, your payment data is transmitted directly to Stripe.
            Stripe processes this data to carry out the payment.
        </p>
        <p class="mb-4 leading-relaxed">
            Data processing is based on Art. 6 (1) (b) GDPR (contract fulfillment).
            For more information, please refer to
            <a href="https://stripe.com/privacy" target="_blank" rel="noopener" class="text-blue-600 hover:underline">Stripe's Privacy Policy</a>.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">5. Contact Form</h2>
        <p class="mb-4 leading-relaxed">
            If you contact us via the contact form or email, your details will be stored for the purpose
            of processing your inquiry and in case of follow-up questions. We will not share this data
            without your consent. Processing is based on Art. 6 (1) (b) GDPR.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">6. File Processing (PDF Tools)</h2>
        <p class="mb-4 leading-relaxed">
            Uploaded files are processed exclusively for the purpose of carrying out the requested operation
            (e.g., merging, compressing, converting). After completion, the files are automatically deleted
            from our servers within 24 hours.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">7. Your Rights</h2>
        <p class="mb-4 leading-relaxed">
            Under applicable law, you have the right at any time to:
        </p>
        <ul class="list-disc list-inside mb-4 leading-relaxed">
            <li>Free information about your stored personal data (Art. 15 GDPR)</li>
            <li>Rectification of inaccurate data (Art. 16 GDPR)</li>
            <li>Erasure of your stored data (Art. 17 GDPR)</li>
            <li>Restriction of data processing (Art. 18 GDPR)</li>
            <li>Data portability (Art. 20 GDPR)</li>
            <li>Objection to processing (Art. 21 GDPR)</li>
        </ul>
        <p class="mb-4 leading-relaxed">
            You also have the right to lodge a complaint with a data protection supervisory authority regarding
            the processing of your personal data.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">8. Changes to This Privacy Policy</h2>
        <p class="mb-4 leading-relaxed">
            We reserve the right to update this privacy policy to ensure it always complies with current
            legal requirements or to reflect changes to our services in the privacy policy.
        </p>
    @else
        <!-- Bitte mit echten Unternehmensdaten erg&auml;nzen -->

        <h1 class="text-3xl font-bold mb-8">Datenschutzerkl&auml;rung</h1>

        <h2 class="text-xl font-semibold mt-8 mb-4">1. Verantwortlicher</h2>
        <p class="mb-4 leading-relaxed">
            Verantwortlich f&uuml;r die Datenverarbeitung auf dieser Website ist:<br><br>
            Muster GmbH<br>
            Musterstra&szlig;e 1<br>
            12345 Musterstadt<br>
            E-Mail: datenschutz@sofortpdf.com
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">2. Datenerhebung auf unserer Website</h2>

        <h3 class="text-lg font-medium mt-6 mb-3">a) Server-Logfiles</h3>
        <p class="mb-4 leading-relaxed">
            Der Provider der Seiten erhebt und speichert automatisch Informationen in sogenannten Server-Logfiles,
            die Ihr Browser automatisch an uns &uuml;bermittelt. Dies sind:
        </p>
        <ul class="list-disc list-inside mb-4 leading-relaxed">
            <li>Browsertyp und Browserversion</li>
            <li>Verwendetes Betriebssystem</li>
            <li>Referrer URL</li>
            <li>Hostname des zugreifenden Rechners</li>
            <li>Uhrzeit der Serveranfrage</li>
            <li>IP-Adresse</li>
        </ul>
        <p class="mb-4 leading-relaxed">
            Eine Zusammenf&uuml;hrung dieser Daten mit anderen Datenquellen wird nicht vorgenommen.
            Die Erfassung erfolgt auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO.
        </p>

        <h3 class="text-lg font-medium mt-6 mb-3">b) Registrierung und Nutzerkonto</h3>
        <p class="mb-4 leading-relaxed">
            Bei der Registrierung erheben wir Ihren Namen und Ihre E-Mail-Adresse. Diese Daten werden zur
            Bereitstellung Ihres Nutzerkontos und zur Vertragserf&uuml;llung verarbeitet (Art. 6 Abs. 1 lit. b DSGVO).
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">3. Cookies</h2>
        <p class="mb-4 leading-relaxed">
            Unsere Website verwendet Cookies. Dabei handelt es sich um kleine Textdateien, die Ihr Webbrowser auf
            Ihrem Endger&auml;t speichert. Cookies helfen uns dabei, unser Angebot nutzerfreundlicher und sicherer zu machen.
        </p>
        <p class="mb-4 leading-relaxed">
            Technisch notwendige Cookies werden auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO gesetzt.
            Wir haben ein berechtigtes Interesse an der Speicherung von Cookies zur technisch fehlerfreien und
            optimierten Bereitstellung unserer Dienste.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">4. Zahlungsabwicklung &uuml;ber Stripe</h2>
        <p class="mb-4 leading-relaxed">
            F&uuml;r die Zahlungsabwicklung nutzen wir den Dienst Stripe (Stripe, Inc., 510 Townsend Street,
            San Francisco, CA 94103, USA). Bei der Bezahlung werden Ihre Zahlungsdaten direkt an Stripe &uuml;bermittelt.
            Stripe verarbeitet diese Daten zur Durchf&uuml;hrung der Zahlung.
        </p>
        <p class="mb-4 leading-relaxed">
            Die Datenverarbeitung erfolgt auf Grundlage von Art. 6 Abs. 1 lit. b DSGVO (Vertragserf&uuml;llung).
            Weitere Informationen finden Sie in der
            <a href="https://stripe.com/de/privacy" target="_blank" rel="noopener" class="text-blue-600 hover:underline">Datenschutzerkl&auml;rung von Stripe</a>.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">5. Kontaktformular</h2>
        <p class="mb-4 leading-relaxed">
            Wenn Sie uns per Kontaktformular oder E-Mail Anfragen zukommen lassen, werden Ihre Angaben zur
            Bearbeitung der Anfrage und f&uuml;r den Fall von Anschlussfragen bei uns gespeichert. Diese Daten geben
            wir nicht ohne Ihre Einwilligung weiter. Die Verarbeitung erfolgt auf Grundlage von Art. 6 Abs. 1
            lit. b DSGVO.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">6. Dateiverarbeitung (PDF-Werkzeuge)</h2>
        <p class="mb-4 leading-relaxed">
            Hochgeladene Dateien werden ausschlie&szlig;lich zur Durchf&uuml;hrung des gew&uuml;nschten Vorgangs (z. B.
            Zusammenf&uuml;gen, Komprimieren, Konvertieren) verarbeitet. Nach Abschluss des Vorgangs werden
            die Dateien automatisch innerhalb von 24 Stunden von unseren Servern gel&ouml;scht.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">7. Ihre Rechte</h2>
        <p class="mb-4 leading-relaxed">
            Sie haben im Rahmen der geltenden gesetzlichen Bestimmungen jederzeit das Recht auf:
        </p>
        <ul class="list-disc list-inside mb-4 leading-relaxed">
            <li>Unentgeltliche Auskunft &uuml;ber Ihre gespeicherten personenbezogenen Daten (Art. 15 DSGVO)</li>
            <li>Berichtigung unrichtiger Daten (Art. 16 DSGVO)</li>
            <li>L&ouml;schung Ihrer gespeicherten Daten (Art. 17 DSGVO)</li>
            <li>Einschr&auml;nkung der Datenverarbeitung (Art. 18 DSGVO)</li>
            <li>Daten&uuml;bertragbarkeit (Art. 20 DSGVO)</li>
            <li>Widerspruch gegen die Verarbeitung (Art. 21 DSGVO)</li>
        </ul>
        <p class="mb-4 leading-relaxed">
            Sie haben zudem das Recht, sich bei einer Datenschutz-Aufsichtsbeh&ouml;rde &uuml;ber die Verarbeitung
            Ihrer personenbezogenen Daten zu beschweren.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">8. &Auml;nderung dieser Datenschutzerkl&auml;rung</h2>
        <p class="mb-4 leading-relaxed">
            Wir behalten uns vor, diese Datenschutzerkl&auml;rung anzupassen, damit sie stets den aktuellen
            rechtlichen Anforderungen entspricht oder um &Auml;nderungen unserer Leistungen in der
            Datenschutzerkl&auml;rung umzusetzen.
        </p>
    @endif
</div>
@endsection
