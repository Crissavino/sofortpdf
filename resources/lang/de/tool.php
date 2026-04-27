<?php

return [
    // Upload zone
    'drop_or_click' => 'Datei hier ablegen oder klicken zum Auswählen',
    'formats_label' => 'Formate:',
    'up_to_files' => 'Bis zu :n Dateien',

    // Processing
    'processing' => 'Wird verarbeitet…',
    'js_starting_conversion' => 'Konvertierung wird gestartet…',
    'please_wait' => 'Bitte warten Sie, wir bereiten Ihr Dokument vor.',
    'loading_step_1' => 'Ihr Dokument wird geladen.',
    'loading_step_2' => 'Ihr Dokument wird konvertiert.',
    'loading_step_3' => 'Ihr Dokument wird vorbereitet.',
    'loading_converting' => ':tool — wird verarbeitet.',
    'loading_signing' => 'Ihre Unterschrift wird verarbeitet.',

    // Fake conversion loading modal (paywall flow)
    'fake_loading_title'  => 'Konvertierung läuft, bitte einen Moment Geduld',
    'fake_loading_step_1' => 'Dokument wird hochgeladen',
    'fake_loading_step_2' => 'Dokument wird konvertiert',
    'fake_loading_step_3' => 'Dokument wird gesichert',

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

    // Page picker UI
    'picker_remove_heading' => 'Seiten zum Entfernen auswählen',
    'picker_extract_heading' => 'Seiten zum Extrahieren auswählen',
    'picker_remove_hint' => 'Klicken Sie auf die Seiten, die entfernt werden sollen.',
    'picker_extract_hint' => 'Klicken Sie auf die Seiten, die behalten werden sollen.',
    'picker_page_label' => 'Seite :n',
    'picker_loading' => 'Seiten werden geladen …',
    'picker_selected_count_remove' => ':n Seite(n) zum Entfernen markiert',
    'picker_selected_count_extract' => ':n Seite(n) zum Extrahieren markiert',
    'picker_need_selection_remove' => 'Wählen Sie mindestens eine Seite zum Entfernen aus.',
    'picker_need_selection_extract' => 'Wählen Sie mindestens eine Seite zum Extrahieren aus.',
    'picker_select_all' => 'Alle auswählen',
    'picker_select_none' => 'Auswahl aufheben',

    // Rotate-mode picker
    'picker_rotate_heading' => 'Seiten zum Drehen anklicken',
    'picker_rotate_hint' => 'Jeder Klick dreht die Seite um 90° im Uhrzeigersinn.',
    'picker_rotate_count' => ':n Seite(n) gedreht',
    'picker_need_rotation' => 'Drehen Sie mindestens eine Seite, indem Sie sie anklicken.',
    'picker_reset_rotations' => 'Drehungen zurücksetzen',

    // Split-mode picker
    'picker_split_heading' => 'Schnittpunkte zwischen Seiten setzen',
    'picker_split_hint' => 'Klicken Sie zwischen zwei Seiten, um einen Schnittpunkt einzufügen. Jede entstehende Gruppe wird zu einer separaten PDF.',
    'picker_split_count' => ':n Gruppe(n) — :groups',
    'picker_need_split' => 'Setzen Sie mindestens einen Schnittpunkt.',
    'picker_reset_splits' => 'Schnitte zurücksetzen',

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

    // Benefits (left column on desktop)
    'benefit_fast_title'   => 'Blitzschnelle Verarbeitung',
    'benefit_fast_desc'    => 'Ihre Dokumente werden in wenigen Sekunden verarbeitet. Kein Warten, keine Verzögerung.',
    'benefit_secure_title' => 'Höchste Sicherheit',
    'benefit_secure_desc'  => 'Ihre Dateien werden verschlüsselt übertragen und nach 1 Stunde automatisch gelöscht.',
    'benefit_quality_title'=> 'Perfekte Qualität',
    'benefit_quality_desc' => 'Formatierung und Layout bleiben vollständig erhalten — professionelle Ergebnisse garantiert.',
    'benefit_free_title'   => 'Sofort loslegen',
    'benefit_free_desc'    => 'Keine Installation, keine Registrierung. Direkt im Browser auf jedem Gerät nutzbar.',

    // Trust badges (under upload zone)
    'trust_fast'       => 'Sofortergebnis',
    'trust_secure'     => 'DSGVO-konform',
    'trust_quality'    => '100% Qualität',
    'trust_delete'     => 'Dateien nach 1h gelöscht',

    // Social proof stats
    'stat_docs'        => 'Dokumente konvertiert',
    'stat_users'       => 'Zufriedene Nutzer',
    'stat_quality'     => 'Qualität garantiert',

    // JS-side messages (used in inline <script>)
    'js_only_one_file' => 'Nur eine Datei erlaubt.',
    'js_max_files' => 'Maximal {n} Dateien gleichzeitig erlaubt.',
    'js_file_too_large' => 'Die Datei „{name}" ist zu groß. Maximale Dateigröße: {size} MB',
    'js_add_another' => 'Weitere Datei hinzufügen',
    'js_upload_failed' => 'Upload fehlgeschlagen.',
    'js_conversion_failed' => 'Konvertierung fehlgeschlagen.',
];
