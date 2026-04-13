@extends('layouts.app')

@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $pageTitle)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    @if($isEn)
        <h1 class="text-3xl font-bold mb-8">Legal Notice</h1>

        <h2 class="text-xl font-semibold mt-8 mb-4">Information pursuant to Sect. 5 German Telemedia Act (TMG)</h2>
        <p class="mb-4 leading-relaxed">
            Muster GmbH<br>
            Musterstrasse 1<br>
            12345 Musterstadt, Germany
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Represented by</h2>
        <p class="mb-4 leading-relaxed">
            Max Mustermann, Managing Director
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Contact</h2>
        <p class="mb-4 leading-relaxed">
            Phone: +49 (0) 123 456789<br>
            Email: info@sofortpdf.com
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Commercial Register</h2>
        <p class="mb-4 leading-relaxed">
            Registered in the commercial register.<br>
            Court of registration: Amtsgericht Musterstadt<br>
            Registration number: HRB 12345
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">VAT ID</h2>
        <p class="mb-4 leading-relaxed">
            VAT identification number pursuant to Sect. 27a of the German Value Added Tax Act:<br>
            DE 123456789
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Person responsible for editorial content</h2>
        <p class="mb-4 leading-relaxed">
            Max Mustermann<br>
            Musterstrasse 1<br>
            12345 Musterstadt, Germany
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">EU Dispute Resolution</h2>
        <p class="mb-4 leading-relaxed">
            The European Commission provides a platform for online dispute resolution (ODR):
            <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener" class="text-blue-600 hover:underline">https://ec.europa.eu/consumers/odr</a>.<br>
            Our email address can be found above in the legal notice.
        </p>
        <p class="mb-4 leading-relaxed">
            We are not willing or obliged to participate in dispute resolution proceedings before a consumer arbitration board.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Liability for Contents</h2>
        <p class="mb-4 leading-relaxed">
            As a service provider, we are responsible for our own content on these pages in accordance with general legislation pursuant to Sect. 7 (1) German Telemedia Act (TMG). However, pursuant to Sects. 8 to 10 TMG, we are not obligated to monitor transmitted or stored third-party information, or to investigate circumstances that indicate illegal activity.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Liability for Links</h2>
        <p class="mb-4 leading-relaxed">
            Our website contains links to external third-party websites over whose content we have no influence. Therefore, we cannot assume any liability for such external content. The respective provider or operator of the linked pages is always responsible for their content.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Copyright</h2>
        <p class="mb-4 leading-relaxed">
            The content and works created by the site operators on these pages are subject to German copyright law. Duplication, processing, distribution, and any form of commercialization beyond the scope of copyright law require the written consent of the respective author or creator.
        </p>
    @else
        <!-- Bitte mit echten Unternehmensdaten ergänzen -->

        <h1 class="text-3xl font-bold mb-8">Impressum</h1>

        <h2 class="text-xl font-semibold mt-8 mb-4">Angaben gem&auml;&szlig; &sect; 5 TMG</h2>
        <p class="mb-4 leading-relaxed">
            Muster GmbH<br>
            Musterstra&szlig;e 1<br>
            12345 Musterstadt
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Vertreten durch</h2>
        <p class="mb-4 leading-relaxed">
            Max Mustermann, Gesch&auml;ftsf&uuml;hrer
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Kontakt</h2>
        <p class="mb-4 leading-relaxed">
            Telefon: +49 (0) 123 456789<br>
            E-Mail: info@sofortpdf.com
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Registereintrag</h2>
        <p class="mb-4 leading-relaxed">
            Eintragung im Handelsregister.<br>
            Registergericht: Amtsgericht Musterstadt<br>
            Registernummer: HRB 12345
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Umsatzsteuer-ID</h2>
        <p class="mb-4 leading-relaxed">
            Umsatzsteuer-Identifikationsnummer gem&auml;&szlig; &sect; 27a Umsatzsteuergesetz:<br>
            DE 123456789
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Verantwortlich f&uuml;r den Inhalt nach &sect; 55 Abs. 2 RStV</h2>
        <p class="mb-4 leading-relaxed">
            Max Mustermann<br>
            Musterstra&szlig;e 1<br>
            12345 Musterstadt
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Streitschlichtung</h2>
        <p class="mb-4 leading-relaxed">
            Die Europ&auml;ische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit:
            <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener" class="text-blue-600 hover:underline">https://ec.europa.eu/consumers/odr</a>.<br>
            Unsere E-Mail-Adresse finden Sie oben im Impressum.
        </p>
        <p class="mb-4 leading-relaxed">
            Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer
            Verbraucherschlichtungsstelle teilzunehmen.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Haftung f&uuml;r Inhalte</h2>
        <p class="mb-4 leading-relaxed">
            Als Diensteanbieter sind wir gem&auml;&szlig; &sect; 7 Abs.1 TMG f&uuml;r eigene Inhalte auf diesen Seiten nach den
            allgemeinen Gesetzen verantwortlich. Nach &sect;&sect; 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht
            verpflichtet, &uuml;bermittelte oder gespeicherte fremde Informationen zu &uuml;berwachen oder nach Umst&auml;nden zu
            forschen, die auf eine rechtswidrige T&auml;tigkeit hinweisen.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Haftung f&uuml;r Links</h2>
        <p class="mb-4 leading-relaxed">
            Unser Angebot enth&auml;lt Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben.
            Deshalb k&ouml;nnen wir f&uuml;r diese fremden Inhalte auch keine Gew&auml;hr &uuml;bernehmen. F&uuml;r die Inhalte der
            verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich.
        </p>

        <h2 class="text-xl font-semibold mt-8 mb-4">Urheberrecht</h2>
        <p class="mb-4 leading-relaxed">
            Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen
            Urheberrecht. Die Vervielf&auml;ltigung, Bearbeitung, Verbreitung und jede Art der Verwertung au&szlig;erhalb der
            Grenzen des Urheberrechtes bed&uuml;rfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers.
        </p>
    @endif
</div>
@endsection
