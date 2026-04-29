<?php

return [
    // Upload zone
    'drop_or_click' => 'Přetáhněte sem soubor nebo klikněte pro výběr',
    'formats_label' => 'Formáty:',
    'up_to_files' => 'Až :n souborů',

    // Processing
    'processing' => 'Zpracování…',
    'js_starting_conversion' => 'Spouštím konverzi…',
    'please_wait' => 'Počkejte prosím, připravujeme váš dokument.',
    'loading_step_1' => 'Načítání dokumentu.',
    'loading_step_2' => 'Konverze dokumentu.',
    'loading_step_3' => 'Příprava ke stažení.',
    'loading_converting' => ':tool — zpracování.',
    'loading_signing' => 'Zpracování podpisu.',

    // Fake conversion loading modal (paywall flow)
    'fake_loading_title'  => 'Konverze probíhá, počkejte prosím chvíli',
    'fake_loading_step_1' => 'Nahrávání dokumentu',
    'fake_loading_step_2' => 'Konverze dokumentu',
    'fake_loading_step_3' => 'Zabezpečení dokumentu',

    // Done / download
    'done' => 'Hotovo!',
    'ready_for_download' => 'Váš soubor je připraven ke stažení.',
    'download' => 'Stáhnout',
    'process_another' => 'Zpracovat další soubor',

    // Error
    'error_generic' => 'Došlo k chybě. Zkuste to prosím znovu.',
    'try_again' => 'Zkusit znovu',

    // How it works
    'how_heading' => 'Jak to funguje',
    'step_label' => 'Krok :n',
    'step1_title' => 'Nahrát soubor',
    'step1_desc' => 'Přetáhněte soubor do oblasti pro nahrávání nebo klikněte pro výběr.',
    'step2_title' => 'Automatické zpracování',
    'step2_desc' => 'Naše servery zpracují váš soubor během několika sekund — bezpečně a spolehlivě.',
    'step3_title' => 'Stáhnout',
    'step3_desc' => 'Stáhněte si hotový soubor okamžitě. Bez čekání.',

    // FAQ
    'faq_heading' => 'Často kladené otázky',
    'faq_secure_q' => 'Je použití bezpečné?',
    'faq_secure_a' => 'Ano. Všechny soubory jsou přenášeny prostřednictvím SSL šifrovaného spojení a po 1 hodině automaticky mazány.',
    'faq_formats_q' => 'Jaké formáty souborů jsou podporovány?',
    'faq_formats_a' => 'Tento nástroj podporuje následující formáty: :formats.',
    'faq_size_q' => 'Existuje maximální velikost souboru?',
    'faq_size_a' => 'Maximální velikost souboru je :size MB na soubor.',
    'faq_mobile_q' => 'Funguje to na mobilu?',
    'faq_mobile_a' => 'Ano, sofortpdf.com funguje na všech zařízeních — desktop, tablet i smartphone.',

    // Tool-specific params (rendered above the action button)
    'watermark_text_label' => 'Text vodoznaku',
    'watermark_text_placeholder' => 'např. DŮVĚRNÉ',
    'watermark_text_hint' => 'Tento text se vytiskne jako vodoznak na každou stránku.',
    'param_required_suffix' => '*',
    'param_required_error' => 'Vyplňte prosím všechna povinná pole.',

    'rotate_angle_label' => 'Úhel otočení',

    'protect_password_label' => 'Heslo',
    'protect_password_placeholder' => 'Zvolte si silné heslo',
    'protect_password_hint' => 'Toto heslo bude vyžadováno k otevření PDF.',

    'unlock_password_label' => 'Aktuální heslo',
    'unlock_password_placeholder' => 'Zadejte heslo PDF',
    'unlock_password_hint' => 'Heslo, kterým je PDF aktuálně chráněno.',

    'pages_placeholder' => 'např. 1-3, 5, 7-9',
    'pages_hint' => 'Čísla stránek nebo rozsahy oddělené čárkami.',
    'pages_remove_label' => 'Stránky k odstranění',
    'pages_extract_label' => 'Stránky k extrakci',

    // Page picker UI
    'picker_remove_heading' => 'Vyberte stránky k odstranění',
    'picker_extract_heading' => 'Vyberte stránky k extrakci',
    'picker_remove_hint' => 'Klikněte na stránky, které chcete odstranit.',
    'picker_extract_hint' => 'Klikněte na stránky, které chcete zachovat.',
    'picker_page_label' => 'Strana :n',
    'picker_loading' => 'Načítání stránek …',
    'picker_selected_count_remove' => ':n stránek označeno k odstranění',
    'picker_selected_count_extract' => ':n stránek označeno k extrakci',
    'picker_need_selection_remove' => 'Vyberte alespoň jednu stránku k odstranění.',
    'picker_need_selection_extract' => 'Vyberte alespoň jednu stránku k extrakci.',
    'picker_select_all' => 'Vybrat vše',
    'picker_select_none' => 'Zrušit výběr',

    // Rotate-mode picker
    'picker_rotate_heading' => 'Klikněte na stránky pro otočení',
    'picker_rotate_hint' => 'Každé kliknutí otočí stránku o 90° po směru hodinových ručiček.',
    'picker_rotate_count' => ':n stránek otočeno',
    'picker_need_rotation' => 'Otočte alespoň jednu stránku kliknutím.',
    'picker_reset_rotations' => 'Resetovat otočení',

    // Split-mode picker
    'picker_split_heading' => 'Umístěte body rozdělení mezi stránky',
    'picker_split_hint' => 'Klikněte mezi dvě stránky pro vložení bodu rozdělení. Každá vzniklá skupina se stane samostatným PDF.',
    'picker_split_count' => ':n skupin — :groups',
    'picker_need_split' => 'Umístěte alespoň jeden bod rozdělení.',
    'picker_reset_splits' => 'Resetovat rozdělení',

    'ocr_language_label' => 'Jazyk',
    'ocr_language_hint' => 'Vyberte jazyk textu v dokumentu.',
    'ocr_lang_deu' => 'Němčina',
    'ocr_lang_eng' => 'Angličtina',
    'ocr_lang_deu_eng' => 'Němčina + angličtina',
    'ocr_lang_spa' => 'Španělština',
    'ocr_lang_fra' => 'Francouzština',
    'ocr_lang_ita' => 'Italština',
    'ocr_lang_por' => 'Portugalština',
    'ocr_lang_nld' => 'Nizozemština',

    // Related
    'related_heading' => 'Další PDF nástroje',

    // Meta / page-title
    'title_suffix' => ' — Okamžitě a online',
    'default_action_label' => 'Konvertovat nyní',
    'convert_now_button' => 'Konvertovat soubor nyní!',
    'maintenance_suffix' => ' — Údržba',
    'maintenance_heading' => 'Údržba',
    'maintenance_body' => 'Tento nástroj je dočasně nedostupný. Zkuste to prosím znovu později.',

    // Benefits (left column on desktop)
    'benefit_fast_title'   => 'Bleskové zpracování',
    'benefit_fast_desc'    => 'Vaše dokumenty jsou zpracovány během několika sekund. Bez čekání, bez prodlev.',
    'benefit_secure_title' => 'Maximální bezpečnost',
    'benefit_secure_desc'  => 'Vaše soubory jsou šifrovány při přenosu a automaticky mazány po 1 hodině.',
    'benefit_quality_title'=> 'Dokonalá kvalita',
    'benefit_quality_desc' => 'Formátování a rozvržení jsou plně zachovány — profesionální výsledky zaručeny.',
    'benefit_free_title'   => 'Začněte hned',
    'benefit_free_desc'    => 'Bez instalace, bez registrace. Funguje přímo ve vašem prohlížeči na jakémkoli zařízení.',

    // Trust badges (under upload zone)
    'trust_fast'       => 'Okamžitý výsledek',
    'trust_secure'     => 'Soulad s GDPR',
    'trust_quality'    => '100% kvalita',
    'trust_delete'     => 'Soubory smazány po 1 hod',

    // Social proof stats
    'stat_docs'        => 'Konvertovaných dokumentů',
    'stat_users'       => 'Spokojených uživatelů',
    'stat_quality'     => 'Záruka kvality',

    // JS-side messages (used in inline <script>)
    'js_only_one_file' => 'Povolen pouze jeden soubor.',
    'js_max_files' => 'Maximálně {n} souborů najednou.',
    'js_file_too_large' => 'Soubor „{name}" je příliš velký. Maximální velikost souboru: {size} MB',
    'js_add_another' => 'Přidat další soubor',
    'js_files_added' => 'Soubory: {n}',
    'js_drag_to_reorder' => 'Přetažením níže změníte pořadí',
    'js_upload_failed' => 'Nahrávání selhalo.',
    'js_conversion_failed' => 'Konverze selhala.',
];
