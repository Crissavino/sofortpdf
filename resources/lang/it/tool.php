<?php

return [
    // Upload zone
    'drop_or_click' => 'Trascina qui il file oppure clicca per selezionarlo',
    'formats_label' => 'Formati:',
    'up_to_files' => 'Fino a :n file',

    // Processing
    'processing' => 'Elaborazione…',
    'js_starting_conversion' => 'Avvio della conversione…',
    'please_wait' => 'Attendi, stiamo preparando il tuo documento.',
    'loading_step_1' => 'Caricamento del documento.',
    'loading_step_2' => 'Conversione del documento.',
    'loading_step_3' => 'Preparazione del download.',
    'loading_converting' => ':tool — elaborazione in corso.',
    'loading_signing' => 'Elaborazione della tua firma.',

    // Fake conversion loading modal (paywall flow)
    'fake_loading_title'  => 'Conversione in corso, attendi un istante',
    'fake_loading_step_1' => 'Caricamento del documento',
    'fake_loading_step_2' => 'Conversione del documento',
    'fake_loading_step_3' => 'Messa in sicurezza del documento',

    // Done / download
    'done' => 'Pronto!',
    'ready_for_download' => 'Il tuo file è pronto per il download.',
    'download' => 'Scarica',
    'process_another' => 'Elabora un altro file',

    // Error
    'error_generic' => 'Si è verificato un errore. Riprova.',
    'try_again' => 'Riprova',

    // How it works
    'how_heading' => 'Come funziona',
    'step_label' => 'Passaggio :n',
    'step1_title' => 'Carica il file',
    'step1_desc' => 'Trascina il file nell\'area di caricamento oppure clicca per selezionarlo.',
    'step2_title' => 'Elaborazione automatica',
    'step2_desc' => 'I nostri server elaborano il tuo file in pochi secondi, in modo sicuro e affidabile.',
    'step3_title' => 'Scarica',
    'step3_desc' => 'Scarica subito il file finito. Senza attese.',

    // FAQ
    'faq_heading' => 'Domande frequenti',
    'faq_secure_q' => 'È sicuro da usare?',
    'faq_secure_a' => 'Sì. Tutti i file vengono trasferiti su una connessione cifrata SSL ed eliminati automaticamente dopo 1 ora.',
    'faq_formats_q' => 'Quali formati di file sono supportati?',
    'faq_formats_a' => 'Questo strumento supporta i seguenti formati: :formats.',
    'faq_size_q' => 'C\'è un limite alla dimensione del file?',
    'faq_size_a' => 'La dimensione massima è di :size MB per file.',
    'faq_mobile_q' => 'Funziona da telefono?',
    'faq_mobile_a' => 'Sì, sofortpdf.com funziona su tutti i dispositivi: computer, tablet e smartphone.',

    // Tool-specific params (rendered above the action button)
    'watermark_text_label' => 'Testo della filigrana',
    'watermark_text_placeholder' => 'ad es. RISERVATO',
    'watermark_text_hint' => 'Questo testo verrà stampato come filigrana su ogni pagina.',
    'param_required_suffix' => '*',
    'param_required_error' => 'Compila tutti i campi obbligatori.',

    'rotate_angle_label' => 'Angolo di rotazione',

    'protect_password_label' => 'Password',
    'protect_password_placeholder' => 'Scegli una password sicura',
    'protect_password_hint' => 'Questa password servirà per aprire il PDF.',

    'unlock_password_label' => 'Password attuale',
    'unlock_password_placeholder' => 'Inserisci la password del PDF',
    'unlock_password_hint' => 'La password con cui il PDF è protetto adesso.',

    'pages_placeholder' => 'ad es. 1-3, 5, 7-9',
    'pages_hint' => 'Numeri di pagina o intervalli separati da virgole.',
    'pages_remove_label' => 'Pagine da rimuovere',
    'pages_extract_label' => 'Pagine da estrarre',

    // Page picker UI
    'picker_remove_heading' => 'Scegli le pagine da rimuovere',
    'picker_extract_heading' => 'Scegli le pagine da estrarre',
    'picker_remove_hint' => 'Clicca sulle pagine che vuoi rimuovere.',
    'picker_extract_hint' => 'Clicca sulle pagine che vuoi tenere.',
    'picker_page_label' => 'Pagina :n',
    'picker_loading' => 'Caricamento delle pagine …',
    'picker_selected_count_remove' => ':n pagina/e da rimuovere',
    'picker_selected_count_extract' => ':n pagina/e da estrarre',
    'picker_need_selection_remove' => 'Scegli almeno una pagina da rimuovere.',
    'picker_need_selection_extract' => 'Scegli almeno una pagina da estrarre.',
    'picker_select_all' => 'Seleziona tutte',
    'picker_select_none' => 'Annulla la selezione',

    // Rotate-mode picker
    'picker_rotate_heading' => 'Clicca sulle pagine da ruotare',
    'picker_rotate_hint' => 'Ogni clic ruota la pagina di 90° in senso orario.',
    'picker_rotate_count' => ':n pagina/e ruotate',
    'picker_need_rotation' => 'Ruota almeno una pagina cliccandoci sopra.',
    'picker_reset_rotations' => 'Azzera le rotazioni',

    // Split-mode picker
    'picker_split_heading' => 'Inserisci i punti di taglio tra le pagine',
    'picker_split_hint' => 'Clicca tra due pagine per inserire un punto di taglio. Ogni gruppo che ne risulta diventa un PDF a sé.',
    'picker_split_count' => ':n gruppo/i — :groups',
    'picker_need_split' => 'Inserisci almeno un punto di taglio.',
    'picker_reset_splits' => 'Azzera i tagli',

    'ocr_language_label' => 'Lingua',
    'ocr_language_hint' => 'Scegli la lingua del testo contenuto nel documento.',
    'ocr_lang_deu' => 'Tedesco',
    'ocr_lang_eng' => 'Inglese',
    'ocr_lang_deu_eng' => 'Tedesco + inglese',
    'ocr_lang_spa' => 'Spagnolo',
    'ocr_lang_fra' => 'Francese',
    'ocr_lang_ita' => 'Italiano',
    'ocr_lang_por' => 'Portoghese',
    'ocr_lang_nld' => 'Olandese',

    // Related
    'related_heading' => 'Altri strumenti PDF',

    // Meta / page-title
    'title_suffix' => ' — subito e online',
    'default_action_label' => 'Converti ora',
    'upload_button' => 'Carica il tuo file',
    'convert_now_button' => 'Converti il file ora!',
    'maintenance_suffix' => ' — Manutenzione',
    'maintenance_heading' => 'Manutenzione',
    'maintenance_body' => 'Questo strumento non è momentaneamente disponibile. Riprova più tardi.',

    // Benefits (left column on desktop)
    'benefit_fast_title'   => 'Elaborazione fulminea',
    'benefit_fast_desc'    => 'I tuoi documenti vengono elaborati in pochi secondi. Nessuna attesa, nessun ritardo.',
    'benefit_secure_title' => 'Massima sicurezza',
    'benefit_secure_desc'  => 'I file sono cifrati durante il trasferimento ed eliminati automaticamente dopo 1 ora.',
    'benefit_quality_title'=> 'Qualità perfetta',
    'benefit_quality_desc' => 'Formattazione e layout vengono conservati integralmente: risultati professionali garantiti.',
    'benefit_free_title'   => 'Inizia subito',
    'benefit_free_desc'    => 'Nessuna installazione, nessuna registrazione. Funziona direttamente nel browser, su qualsiasi dispositivo.',

    // Trust badges (under upload zone)
    'trust_fast'       => 'Risultati immediati',
    'trust_secure'     => 'Conforme al GDPR',
    'trust_quality'    => 'Qualità al 100%',
    'trust_delete'     => 'File eliminati dopo 1 h',

    // Social proof stats
    'stat_docs'        => 'Documenti convertiti',
    'stat_users'       => 'Utenti soddisfatti',
    'stat_quality'     => 'Qualità garantita',

    // JS-side messages (used in inline <script>)
    'js_only_one_file' => 'È consentito un solo file.',
    'js_max_files' => 'Puoi caricare al massimo {n} file per volta.',
    'js_file_too_large' => 'Il file "{name}" è troppo grande. Dimensione massima: {size} MB',
    'js_add_another' => 'Aggiungi un altro file',
    'js_files_added' => 'File: {n}',
    'js_drag_to_reorder' => 'Trascina qui sotto per riordinare',
    'js_upload_failed' => 'Caricamento non riuscito.',
    'js_conversion_failed' => 'Conversione non riuscita.',
];
