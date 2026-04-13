@extends('layouts.app')

@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $pageTitle)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    @if($isEn)
        <h1 class="text-3xl font-bold mb-8">Terms and Conditions</h1>

        <h2 class="text-xl font-semibold mt-8 mb-4">Section 1 &mdash; Scope</h2>
        <p class="mb-4 leading-relaxed">
            These Terms and Conditions (hereinafter "Terms") apply to all contracts concluded between
            Muster GmbH (hereinafter "Provider") and the customer (hereinafter "User") via the website
            sofortpdf.com.
        </p>
        <p class="mb-4 leading-relaxed">
            These Terms apply exclusively. Deviating terms of the User will not be accepted unless the
            Provider expressly agrees to them in writing.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Section 2 &mdash; Subject Matter</h2>
        <p class="mb-4 leading-relaxed">
            The Provider offers online tools for editing PDF documents via the website sofortpdf.com
            (e.g., merging, compressing, converting, editing, signing). Access to these tools is provided
            through a paid subscription.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Section 3 &mdash; Conclusion of Contract</h2>
        <p class="mb-4 leading-relaxed">
            The contract is concluded upon the User's registration on the website and the purchase of a
            subscription. By registering, the User accepts these Terms.
        </p>
        <p class="mb-4 leading-relaxed">
            The Provider will confirm receipt of the order without delay by email. This confirmation
            constitutes acceptance of the contractual offer.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Section 4 &mdash; Prices and Payment</h2>
        <p class="mb-4 leading-relaxed">
            All stated prices include the applicable statutory value-added tax. Payment is processed via
            the payment service provider Stripe. The User authorizes the Provider to collect the agreed
            amounts according to the selected billing period (monthly or annually).
        </p>
        <p class="mb-4 leading-relaxed">
            In the event of payment default, the Provider is entitled to temporarily suspend access to the services.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Section 5 &mdash; Right of Withdrawal</h2>
        <p class="mb-4 leading-relaxed">
            Consumers have the right to withdraw from this contract within fourteen days without giving
            any reason. The withdrawal period is fourteen days from the date of the conclusion of the contract.
        </p>
        <p class="mb-4 leading-relaxed">
            To exercise your right of withdrawal, you must inform us by means of a clear statement (e.g.,
            a letter sent by post or an email) of your decision to withdraw from this contract.
        </p>
        <p class="mb-4 leading-relaxed">
            To meet the withdrawal deadline, it is sufficient for you to send the communication concerning
            your exercise of the right of withdrawal before the withdrawal period has expired.
        </p>

        <h3 class="text-lg font-medium mt-6 mb-3">Consequences of Withdrawal</h3>
        <p class="mb-4 leading-relaxed">
            If you withdraw from this contract, we shall reimburse all payments received from you without
            undue delay and no later than fourteen days from the day on which we receive notification of
            your withdrawal from this contract.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Section 6 &mdash; Term and Cancellation</h2>
        <p class="mb-4 leading-relaxed">
            The subscription automatically renews for the selected billing period unless it is canceled
            before the end of the current term. Cancellation can be made at any time via the user dashboard
            or by email.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Section 7 &mdash; Availability and Warranty</h2>
        <p class="mb-4 leading-relaxed">
            The Provider strives to ensure uninterrupted availability of the services. 100% availability
            cannot be technically guaranteed. Maintenance work will be announced in advance when possible.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Section 8 &mdash; Liability</h2>
        <p class="mb-4 leading-relaxed">
            The Provider is fully liable for intent and gross negligence. In cases of slight negligence,
            the Provider is only liable for breaches of material contractual obligations (cardinal obligations)
            and limited to foreseeable, contract-typical damages.
        </p>
        <p class="mb-4 leading-relaxed">
            The above limitations of liability do not apply to damages resulting from injury to life, body,
            or health.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Section 9 &mdash; Data Protection</h2>
        <p class="mb-4 leading-relaxed">
            Information on the processing of personal data can be found in our
            <a href="{{ route('datenschutz') }}" class="text-blue-600 hover:underline">Privacy Policy</a>.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Section 10 &mdash; Final Provisions</h2>
        <p class="mb-4 leading-relaxed">
            The law of the Federal Republic of Germany shall apply. The place of jurisdiction is, to the
            extent permitted by law, the registered office of the Provider.
        </p>
        <p class="mb-4 leading-relaxed">
            Should individual provisions of these Terms be or become invalid, the validity of the remaining
            provisions shall not be affected.
        </p>
    @else
        <!-- Bitte mit echten Unternehmensdaten erg&auml;nzen -->

        <h1 class="text-3xl font-bold mb-8">Allgemeine Gesch&auml;ftsbedingungen</h1>

        <h2 class="text-xl font-semibold mt-8 mb-4">&sect; 1 Geltungsbereich</h2>
        <p class="mb-4 leading-relaxed">
            Diese Allgemeinen Gesch&auml;ftsbedingungen (nachfolgend &bdquo;AGB&ldquo;) gelten f&uuml;r alle Vertr&auml;ge, die zwischen
            der Muster GmbH (nachfolgend &bdquo;Anbieter&ldquo;) und dem Kunden (nachfolgend &bdquo;Nutzer&ldquo;) &uuml;ber die Website
            sofortpdf.com geschlossen werden.
        </p>
        <p class="mb-4 leading-relaxed">
            Es gelten ausschlie&szlig;lich diese AGB. Abweichende Bedingungen des Nutzers werden nicht anerkannt,
            es sei denn, der Anbieter stimmt ihrer Geltung ausdr&uuml;cklich schriftlich zu.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">&sect; 2 Vertragsgegenstand</h2>
        <p class="mb-4 leading-relaxed">
            Der Anbieter stellt &uuml;ber die Website sofortpdf.com Online-Werkzeuge zur Bearbeitung von
            PDF-Dokumenten bereit (z. B. Zusammenf&uuml;gen, Komprimieren, Konvertieren, Bearbeiten, Unterzeichnen).
            Der Zugang zu diesen Werkzeugen erfolgt im Rahmen eines kostenpflichtigen Abonnements.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">&sect; 3 Vertragsschluss</h2>
        <p class="mb-4 leading-relaxed">
            Der Vertrag kommt durch die Registrierung des Nutzers auf der Website und den Abschluss eines
            Abonnements zustande. Mit der Registrierung erkennt der Nutzer diese AGB an.
        </p>
        <p class="mb-4 leading-relaxed">
            Der Anbieter best&auml;tigt den Eingang der Bestellung unverz&uuml;glich per E-Mail. Diese Best&auml;tigung
            stellt die Annahme des Vertragsangebots dar.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">&sect; 4 Preise und Zahlung</h2>
        <p class="mb-4 leading-relaxed">
            Alle angegebenen Preise verstehen sich inklusive der gesetzlichen Mehrwertsteuer. Die Zahlung
            erfolgt &uuml;ber den Zahlungsdienstleister Stripe. Der Nutzer autorisiert den Anbieter, die
            vereinbarten Betr&auml;ge gem&auml;&szlig; dem gew&auml;hlten Abrechnungszeitraum (monatlich oder j&auml;hrlich)
            einzuziehen.
        </p>
        <p class="mb-4 leading-relaxed">
            Bei Zahlungsverzug ist der Anbieter berechtigt, den Zugang zu den Diensten vor&uuml;bergehend zu sperren.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">&sect; 5 Widerrufsrecht</h2>
        <p class="mb-4 leading-relaxed">
            Verbraucher haben das Recht, binnen vierzehn Tagen ohne Angabe von Gr&uuml;nden diesen Vertrag
            zu widerrufen. Die Widerrufsfrist betr&auml;gt vierzehn Tage ab dem Tag des Vertragsschlusses.
        </p>
        <p class="mb-4 leading-relaxed">
            Um Ihr Widerrufsrecht auszu&uuml;ben, m&uuml;ssen Sie uns mittels einer eindeutigen Erkl&auml;rung (z. B.
            ein mit der Post versandter Brief oder eine E-Mail) &uuml;ber Ihren Entschluss, diesen Vertrag
            zu widerrufen, informieren.
        </p>
        <p class="mb-4 leading-relaxed">
            Zur Wahrung der Widerrufsfrist reicht es aus, dass Sie die Mitteilung &uuml;ber die Aus&uuml;bung des
            Widerrufsrechts vor Ablauf der Widerrufsfrist absenden.
        </p>

        <h3 class="text-lg font-medium mt-6 mb-3">Folgen des Widerrufs</h3>
        <p class="mb-4 leading-relaxed">
            Wenn Sie diesen Vertrag widerrufen, haben wir Ihnen alle Zahlungen, die wir von Ihnen erhalten
            haben, unverz&uuml;glich und sp&auml;testens binnen vierzehn Tagen ab dem Tag zur&uuml;ckzuzahlen, an dem die
            Mitteilung &uuml;ber Ihren Widerruf bei uns eingegangen ist.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">&sect; 6 Laufzeit und K&uuml;ndigung</h2>
        <p class="mb-4 leading-relaxed">
            Das Abonnement verl&auml;ngert sich automatisch um den gew&auml;hlten Abrechnungszeitraum, sofern es
            nicht vor Ablauf der jeweiligen Laufzeit gek&uuml;ndigt wird. Die K&uuml;ndigung kann jederzeit &uuml;ber
            das Nutzer-Dashboard oder per E-Mail erfolgen.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">&sect; 7 Verf&uuml;gbarkeit und Gew&auml;hrleistung</h2>
        <p class="mb-4 leading-relaxed">
            Der Anbieter bem&uuml;ht sich um eine m&ouml;glichst unterbrechungsfreie Verf&uuml;gbarkeit der Dienste.
            Eine Verf&uuml;gbarkeit von 100 % kann technisch nicht garantiert werden. Wartungsarbeiten werden
            nach M&ouml;glichkeit vorab angek&uuml;ndigt.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">&sect; 8 Haftung</h2>
        <p class="mb-4 leading-relaxed">
            Der Anbieter haftet unbeschr&auml;nkt f&uuml;r Vorsatz und grobe Fahrl&auml;ssigkeit. Bei leichter Fahrl&auml;ssigkeit
            haftet der Anbieter nur bei Verletzung wesentlicher Vertragspflichten (Kardinalpflichten) und
            begrenzt auf den vorhersehbaren, vertragstypischen Schaden.
        </p>
        <p class="mb-4 leading-relaxed">
            Die vorstehenden Haftungsbeschr&auml;nkungen gelten nicht f&uuml;r Sch&auml;den aus der Verletzung des Lebens,
            des K&ouml;rpers oder der Gesundheit.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">&sect; 9 Datenschutz</h2>
        <p class="mb-4 leading-relaxed">
            Informationen zur Verarbeitung personenbezogener Daten finden Sie in unserer
            <a href="{{ route('datenschutz') }}" class="text-blue-600 hover:underline">Datenschutzerkl&auml;rung</a>.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">&sect; 10 Schlussbestimmungen</h2>
        <p class="mb-4 leading-relaxed">
            Es gilt das Recht der Bundesrepublik Deutschland. Gerichtsstand ist, soweit gesetzlich zul&auml;ssig,
            der Sitz des Anbieters.
        </p>
        <p class="mb-4 leading-relaxed">
            Sollten einzelne Bestimmungen dieser AGB unwirksam sein oder werden, bleibt die Wirksamkeit
            der &uuml;brigen Bestimmungen unber&uuml;hrt.
        </p>
    @endif
</div>
@endsection
