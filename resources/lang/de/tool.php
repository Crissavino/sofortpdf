<?php

return [
    // Upload zone
    'drop_or_click' => 'Datei hier ablegen oder klicken zum Auswählen',
    'formats_label' => 'Formate:',
    'up_to_files' => 'Bis zu :n Dateien',

    // Processing
    'processing' => 'Wird verarbeitet…',
    'please_wait' => 'Bitte warten Sie einen Moment.',

    // Done / download
    'done' => 'Fertig!',
    'ready_for_download' => 'Ihre Datei ist bereit zum Herunterladen.',
    'download' => 'Herunterladen',
    'process_another' => 'Weitere Datei verarbeiten',

    // Error
    'error_generic' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.',
    'try_again' => 'Erneut versuchen',

    // How it works
    'how_heading' => 'So funktioniert es',
    'step_label' => 'Schritt :n',
    'step1_title' => 'Datei hochladen',
    'step1_desc' => 'Ziehen Sie Ihre Datei in den Upload-Bereich oder klicken Sie zum Auswählen.',
    'step2_title' => 'Automatisch verarbeiten',
    'step2_desc' => 'Unsere Server verarbeiten Ihre Datei in Sekunden — sicher und zuverlässig.',
    'step3_title' => 'Herunterladen',
    'step3_desc' => 'Laden Sie Ihre fertige Datei sofort herunter. Keine Wartezeit.',

    // FAQ
    'faq_heading' => 'Häufig gestellte Fragen',
    'faq_secure_q' => 'Ist die Nutzung sicher?',
    'faq_secure_a' => 'Ja. Alle Dateien werden über eine SSL-verschlüsselte Verbindung übertragen und nach 1 Stunde automatisch gelöscht.',
    'faq_formats_q' => 'Welche Dateiformate werden unterstützt?',
    'faq_formats_a' => 'Dieses Tool unterstützt folgende Formate: :formats.',
    'faq_size_q' => 'Gibt es eine maximale Dateigröße?',
    'faq_size_a' => 'Die maximale Dateigröße beträgt :size MB pro Datei.',
    'faq_mobile_q' => 'Funktioniert es auf dem Handy?',
    'faq_mobile_a' => 'Ja, sofortpdf.com funktioniert auf allen Geräten — Desktop, Tablet und Smartphone.',

    // Tool-specific params (rendered above the action button)
    'watermark_text_label' => 'Wasserzeichen-Text',
    'watermark_text_placeholder' => 'z. B. VERTRAULICH',
    'watermark_text_hint' => 'Dieser Text wird als Wasserzeichen auf jede Seite gedruckt.',
    'param_required_suffix' => '*',
    'param_required_error' => 'Bitte füllen Sie alle Pflichtfelder aus.',

    'rotate_angle_label' => 'Drehwinkel',

    'protect_password_label' => 'Passwort',
    'protect_password_placeholder' => 'Starkes Passwort wählen',
    'protect_password_hint' => 'Dieses Passwort wird beim Öffnen der PDF verlangt.',

    'unlock_password_label' => 'Aktuelles Passwort',
    'unlock_password_placeholder' => 'Passwort der PDF eingeben',
    'unlock_password_hint' => 'Das Passwort, mit dem die PDF aktuell geschützt ist.',

    'pages_placeholder' => 'z. B. 1-3, 5, 7-9',
    'pages_hint' => 'Seitennummern oder Bereiche getrennt mit Kommas.',
    'pages_remove_label' => 'Zu entfernende Seiten',
    'pages_extract_label' => 'Zu extrahierende Seiten',

    'ocr_language_label' => 'Sprache',
    'ocr_language_hint' => 'Wählen Sie die Sprache des Textes im Dokument.',
    'ocr_lang_deu' => 'Deutsch',
    'ocr_lang_eng' => 'Englisch',
    'ocr_lang_deu_eng' => 'Deutsch + Englisch',
    'ocr_lang_spa' => 'Spanisch',
    'ocr_lang_fra' => 'Französisch',
    'ocr_lang_ita' => 'Italienisch',
    'ocr_lang_por' => 'Portugiesisch',
    'ocr_lang_nld' => 'Niederländisch',

    // Related
    'related_heading' => 'Weitere PDF-Tools',

    // Meta / page-title
    'title_suffix' => ' — Sofort & Online',
    'default_action_label' => 'Jetzt konvertieren',
    'maintenance_suffix' => ' — Wartung',
    'maintenance_heading' => 'Wartungsarbeiten',
    'maintenance_body' => 'Dieses Werkzeug steht vorübergehend nicht zur Verfügung. Bitte versuchen Sie es später erneut.',

    // JS-side messages (used in inline <script>)
    'js_only_one_file' => 'Nur eine Datei erlaubt.',
    'js_max_files' => 'Maximal {n} Dateien gleichzeitig erlaubt.',
    'js_file_too_large' => 'Die Datei „{name}" ist zu groß. Maximale Dateigröße: {size} MB',
    'js_add_another' => 'Weitere Datei hinzufügen',
    'js_upload_failed' => 'Upload fehlgeschlagen.',
    'js_conversion_failed' => 'Konvertierung fehlgeschlagen.',
];
