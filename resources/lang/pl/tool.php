<?php

return [
    // Upload zone
    'drop_or_click' => 'Przeciągnij plik tutaj lub kliknij, aby wybrać',
    'formats_label' => 'Formaty:',
    'up_to_files' => 'Do :n plików',

    // Processing
    'processing' => 'Przetwarzanie…',
    'js_starting_conversion' => 'Rozpoczynam konwersję…',
    'please_wait' => 'Proszę czekać, przygotowujemy Twój dokument.',
    'loading_step_1' => 'Ładowanie dokumentu.',
    'loading_step_2' => 'Konwersja dokumentu.',
    'loading_step_3' => 'Przygotowanie do pobrania.',
    'loading_converting' => ':tool — przetwarzanie.',
    'loading_signing' => 'Przetwarzanie podpisu.',

    // Fake conversion loading modal (paywall flow)
    'fake_loading_title'  => 'Konwersja w toku, proszę chwilę poczekać',
    'fake_loading_step_1' => 'Przesyłanie dokumentu',
    'fake_loading_step_2' => 'Konwersja dokumentu',
    'fake_loading_step_3' => 'Zabezpieczanie dokumentu',

    // Done / download
    'done' => 'Gotowe!',
    'ready_for_download' => 'Twój plik jest gotowy do pobrania.',
    'download' => 'Pobierz',
    'process_another' => 'Przetwórz kolejny plik',

    // Error
    'error_generic' => 'Wystąpił błąd. Spróbuj ponownie.',
    'try_again' => 'Spróbuj ponownie',

    // How it works
    'how_heading' => 'Jak to działa',
    'step_label' => 'Krok :n',
    'step1_title' => 'Prześlij plik',
    'step1_desc' => 'Przeciągnij plik do obszaru przesyłania lub kliknij, aby wybrać.',
    'step2_title' => 'Automatyczne przetwarzanie',
    'step2_desc' => 'Nasze serwery przetwarzają Twój plik w kilka sekund — bezpiecznie i niezawodnie.',
    'step3_title' => 'Pobierz',
    'step3_desc' => 'Pobierz gotowy plik natychmiast. Bez czekania.',

    // FAQ
    'faq_heading' => 'Często zadawane pytania',
    'faq_secure_q' => 'Czy korzystanie jest bezpieczne?',
    'faq_secure_a' => 'Tak. Wszystkie pliki są przesyłane przez połączenie szyfrowane SSL i automatycznie usuwane po 1 godzinie.',
    'faq_formats_q' => 'Jakie formaty plików są obsługiwane?',
    'faq_formats_a' => 'To narzędzie obsługuje następujące formaty: :formats.',
    'faq_size_q' => 'Czy istnieje maksymalny rozmiar pliku?',
    'faq_size_a' => 'Maksymalny rozmiar pliku to :size MB na plik.',
    'faq_mobile_q' => 'Czy działa na telefonie?',
    'faq_mobile_a' => 'Tak, sofortpdf.com działa na wszystkich urządzeniach — komputer, tablet i smartfon.',

    // Tool-specific params (rendered above the action button)
    'watermark_text_label' => 'Tekst znaku wodnego',
    'watermark_text_placeholder' => 'np. POUFNE',
    'watermark_text_hint' => 'Ten tekst zostanie wydrukowany jako znak wodny na każdej stronie.',
    'param_required_suffix' => '*',
    'param_required_error' => 'Wypełnij wszystkie wymagane pola.',

    'rotate_angle_label' => 'Kąt obrotu',

    'protect_password_label' => 'Hasło',
    'protect_password_placeholder' => 'Wybierz silne hasło',
    'protect_password_hint' => 'To hasło będzie wymagane do otwarcia PDF.',

    'unlock_password_label' => 'Aktualne hasło',
    'unlock_password_placeholder' => 'Wprowadź hasło PDF',
    'unlock_password_hint' => 'Hasło, którym jest aktualnie chroniony PDF.',

    'pages_placeholder' => 'np. 1-3, 5, 7-9',
    'pages_hint' => 'Numery stron lub zakresy oddzielone przecinkami.',
    'pages_remove_label' => 'Strony do usunięcia',
    'pages_extract_label' => 'Strony do wyodrębnienia',

    // Page picker UI
    'picker_remove_heading' => 'Wybierz strony do usunięcia',
    'picker_extract_heading' => 'Wybierz strony do wyodrębnienia',
    'picker_remove_hint' => 'Kliknij strony, które chcesz usunąć.',
    'picker_extract_hint' => 'Kliknij strony, które chcesz zachować.',
    'picker_page_label' => 'Strona :n',
    'picker_loading' => 'Ładowanie stron …',
    'picker_selected_count_remove' => 'Stron oznaczonych do usunięcia: :n',
    'picker_selected_count_extract' => 'Stron oznaczonych do wyodrębnienia: :n',
    'picker_need_selection_remove' => 'Wybierz przynajmniej jedną stronę do usunięcia.',
    'picker_need_selection_extract' => 'Wybierz przynajmniej jedną stronę do wyodrębnienia.',
    'picker_select_all' => 'Zaznacz wszystkie',
    'picker_select_none' => 'Wyczyść zaznaczenie',

    // Rotate-mode picker
    'picker_rotate_heading' => 'Kliknij strony, aby je obrócić',
    'picker_rotate_hint' => 'Każde kliknięcie obraca stronę o 90° zgodnie z ruchem wskazówek zegara.',
    'picker_rotate_count' => 'Stron obróconych: :n',
    'picker_need_rotation' => 'Obróć przynajmniej jedną stronę klikając ją.',
    'picker_reset_rotations' => 'Resetuj obroty',

    // Split-mode picker
    'picker_split_heading' => 'Umieść punkty podziału między stronami',
    'picker_split_hint' => 'Kliknij między dwiema stronami, aby wstawić punkt podziału. Każda powstała grupa stanie się osobnym PDF.',
    'picker_split_count' => 'Grup: :n — :groups',
    'picker_need_split' => 'Umieść przynajmniej jeden punkt podziału.',
    'picker_reset_splits' => 'Resetuj podziały',

    'ocr_language_label' => 'Język',
    'ocr_language_hint' => 'Wybierz język tekstu w dokumencie.',
    'ocr_lang_deu' => 'Niemiecki',
    'ocr_lang_eng' => 'Angielski',
    'ocr_lang_deu_eng' => 'Niemiecki + angielski',
    'ocr_lang_spa' => 'Hiszpański',
    'ocr_lang_fra' => 'Francuski',
    'ocr_lang_ita' => 'Włoski',
    'ocr_lang_por' => 'Portugalski',
    'ocr_lang_nld' => 'Niderlandzki',

    // Related
    'related_heading' => 'Inne narzędzia PDF',

    // Meta / page-title
    'title_suffix' => ' — Natychmiast i online',
    'default_action_label' => 'Konwertuj teraz',
    'upload_button' => 'Prześlij plik',
    'convert_now_button' => 'Konwertuj plik teraz!',
    'maintenance_suffix' => ' — Konserwacja',
    'maintenance_heading' => 'Konserwacja',
    'maintenance_body' => 'To narzędzie jest tymczasowo niedostępne. Spróbuj ponownie później.',

    // Benefits (left column on desktop)
    'benefit_fast_title'   => 'Błyskawiczne przetwarzanie',
    'benefit_fast_desc'    => 'Twoje dokumenty są przetwarzane w kilka sekund. Bez czekania, bez opóźnień.',
    'benefit_secure_title' => 'Maksymalne bezpieczeństwo',
    'benefit_secure_desc'  => 'Twoje pliki są szyfrowane podczas przesyłania i automatycznie usuwane po 1 godzinie.',
    'benefit_quality_title'=> 'Doskonała jakość',
    'benefit_quality_desc' => 'Formatowanie i układ są w pełni zachowane — gwarantowane profesjonalne rezultaty.',
    'benefit_free_title'   => 'Zacznij od razu',
    'benefit_free_desc'    => 'Bez instalacji, bez rejestracji. Działa bezpośrednio w Twojej przeglądarce na każdym urządzeniu.',

    // Trust badges (under upload zone)
    'trust_fast'       => 'Natychmiastowy wynik',
    'trust_secure'     => 'Zgodność z RODO',
    'trust_quality'    => '100% jakości',
    'trust_delete'     => 'Pliki usuwane po 1 godz',

    // Social proof stats
    'stat_docs'        => 'Skonwertowanych dokumentów',
    'stat_users'       => 'Zadowolonych użytkowników',
    'stat_quality'     => 'Gwarancja jakości',

    // JS-side messages (used in inline <script>)
    'js_only_one_file' => 'Dozwolony tylko jeden plik.',
    'js_max_files' => 'Maksymalnie {n} plików jednocześnie.',
    'js_file_too_large' => 'Plik „{name}" jest za duży. Maksymalny rozmiar pliku: {size} MB',
    'js_add_another' => 'Dodaj kolejny plik',
    'js_files_added' => 'Plików: {n}',
    'js_drag_to_reorder' => 'Przeciągnij poniżej, aby zmienić kolejność',
    'js_upload_failed' => 'Przesyłanie nie powiodło się.',
    'js_conversion_failed' => 'Konwersja nie powiodła się.',
];
