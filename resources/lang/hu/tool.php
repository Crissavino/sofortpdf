<?php

return [
    // Upload zone
    'drop_or_click' => 'Húzza ide a fájlt, vagy kattintson a kiválasztáshoz',
    'formats_label' => 'Formátumok:',
    'up_to_files' => 'Legfeljebb :n fájl',

    // Processing
    'processing' => 'Feldolgozás…',
    'js_starting_conversion' => 'Konverzió indítása…',
    'please_wait' => 'Kérjük, várjon, készítjük a dokumentumát.',
    'loading_step_1' => 'Dokumentum betöltése.',
    'loading_step_2' => 'Dokumentum konvertálása.',
    'loading_step_3' => 'Letöltés előkészítése.',
    'loading_converting' => ':tool — feldolgozás.',
    'loading_signing' => 'Aláírás feldolgozása.',

    // Fake conversion loading modal (paywall flow)
    'fake_loading_title'  => 'Konverzió folyamatban, kérjük, várjon egy pillanatot',
    'fake_loading_step_1' => 'Dokumentum feltöltése',
    'fake_loading_step_2' => 'Dokumentum konvertálása',
    'fake_loading_step_3' => 'Dokumentum biztosítása',

    // Done / download
    'done' => 'Kész!',
    'ready_for_download' => 'Fájlja letöltésre kész.',
    'download' => 'Letöltés',
    'process_another' => 'Másik fájl feldolgozása',

    // Error
    'error_generic' => 'Hiba történt. Kérjük, próbálja újra.',
    'try_again' => 'Újrapróbálkozás',

    // How it works
    'how_heading' => 'Hogyan működik',
    'step_label' => ':n. lépés',
    'step1_title' => 'Fájl feltöltése',
    'step1_desc' => 'Húzza a fájlját a feltöltési területre, vagy kattintson a kiválasztáshoz.',
    'step2_title' => 'Automatikus feldolgozás',
    'step2_desc' => 'Szervereink másodpercek alatt feldolgozzák a fájlját — biztonságosan és megbízhatóan.',
    'step3_title' => 'Letöltés',
    'step3_desc' => 'Töltse le az elkészült fájlt azonnal. Várakozás nélkül.',

    // FAQ
    'faq_heading' => 'Gyakran ismételt kérdések',
    'faq_secure_q' => 'Biztonságos a használat?',
    'faq_secure_a' => 'Igen. Minden fájl SSL-titkosított kapcsolaton keresztül kerül átvitelre, és 1 óra múlva automatikusan törlődik.',
    'faq_formats_q' => 'Mely fájlformátumok támogatottak?',
    'faq_formats_a' => 'Ez az eszköz a következő formátumokat támogatja: :formats.',
    'faq_size_q' => 'Van maximális fájlméret?',
    'faq_size_a' => 'A maximális fájlméret :size MB fájlonként.',
    'faq_mobile_q' => 'Működik mobilon?',
    'faq_mobile_a' => 'Igen, a sofortpdf.com minden eszközön működik — asztali számítógépen, táblagépen és okostelefonon.',

    // Tool-specific params (rendered above the action button)
    'watermark_text_label' => 'Vízjel szövege',
    'watermark_text_placeholder' => 'pl. BIZALMAS',
    'watermark_text_hint' => 'Ez a szöveg vízjelként minden oldalra rákerül.',
    'param_required_suffix' => '*',
    'param_required_error' => 'Kérjük, töltsön ki minden kötelező mezőt.',

    'rotate_angle_label' => 'Forgatási szög',

    'protect_password_label' => 'Jelszó',
    'protect_password_placeholder' => 'Válasszon erős jelszót',
    'protect_password_hint' => 'Ez a jelszó szükséges lesz a PDF megnyitásához.',

    'unlock_password_label' => 'Jelenlegi jelszó',
    'unlock_password_placeholder' => 'Adja meg a PDF jelszavát',
    'unlock_password_hint' => 'Az a jelszó, amellyel a PDF jelenleg védve van.',

    'pages_placeholder' => 'pl. 1-3, 5, 7-9',
    'pages_hint' => 'Oldalszámok vagy tartományok vesszővel elválasztva.',
    'pages_remove_label' => 'Eltávolítandó oldalak',
    'pages_extract_label' => 'Kinyerendő oldalak',

    // Page picker UI
    'picker_remove_heading' => 'Válassza ki az eltávolítandó oldalakat',
    'picker_extract_heading' => 'Válassza ki a kinyerendő oldalakat',
    'picker_remove_hint' => 'Kattintson azokra az oldalakra, amelyeket el szeretne távolítani.',
    'picker_extract_hint' => 'Kattintson azokra az oldalakra, amelyeket meg szeretne tartani.',
    'picker_page_label' => ':n. oldal',
    'picker_loading' => 'Oldalak betöltése …',
    'picker_selected_count_remove' => ':n oldal eltávolításra megjelölve',
    'picker_selected_count_extract' => ':n oldal kinyerésre megjelölve',
    'picker_need_selection_remove' => 'Válasszon ki legalább egy eltávolítandó oldalt.',
    'picker_need_selection_extract' => 'Válasszon ki legalább egy kinyerendő oldalt.',
    'picker_select_all' => 'Mind kijelölése',
    'picker_select_none' => 'Kijelölés törlése',

    // Rotate-mode picker
    'picker_rotate_heading' => 'Kattintson az oldalakra a forgatáshoz',
    'picker_rotate_hint' => 'Minden kattintás 90°-kal elforgatja az oldalt az óramutató járásával megegyező irányban.',
    'picker_rotate_count' => ':n oldal elforgatva',
    'picker_need_rotation' => 'Forgasson el legalább egy oldalt rákattintással.',
    'picker_reset_rotations' => 'Forgatások visszaállítása',

    // Split-mode picker
    'picker_split_heading' => 'Helyezzen vágópontokat az oldalak közé',
    'picker_split_hint' => 'Kattintson két oldal közé vágópont beillesztéséhez. Minden keletkező csoport külön PDF lesz.',
    'picker_split_count' => ':n csoport — :groups',
    'picker_need_split' => 'Helyezzen el legalább egy vágópontot.',
    'picker_reset_splits' => 'Vágások visszaállítása',

    'ocr_language_label' => 'Nyelv',
    'ocr_language_hint' => 'Válassza ki a dokumentumban lévő szöveg nyelvét.',
    'ocr_lang_deu' => 'Német',
    'ocr_lang_eng' => 'Angol',
    'ocr_lang_deu_eng' => 'Német + angol',
    'ocr_lang_spa' => 'Spanyol',
    'ocr_lang_fra' => 'Francia',
    'ocr_lang_ita' => 'Olasz',
    'ocr_lang_por' => 'Portugál',
    'ocr_lang_nld' => 'Holland',

    // Related
    'related_heading' => 'További PDF eszközök',

    // Meta / page-title
    'title_suffix' => ' — Azonnal és online',
    'default_action_label' => 'Konvertálás most',
    'maintenance_suffix' => ' — Karbantartás',
    'maintenance_heading' => 'Karbantartás',
    'maintenance_body' => 'Ez az eszköz átmenetileg nem érhető el. Kérjük, próbálja újra később.',

    // Benefits (left column on desktop)
    'benefit_fast_title'   => 'Villámgyors feldolgozás',
    'benefit_fast_desc'    => 'Dokumentumai másodpercek alatt feldolgozódnak. Várakozás és késedelem nélkül.',
    'benefit_secure_title' => 'Maximális biztonság',
    'benefit_secure_desc'  => 'A fájlok titkosítva kerülnek átvitelre, és 1 óra múlva automatikusan törlődnek.',
    'benefit_quality_title'=> 'Tökéletes minőség',
    'benefit_quality_desc' => 'A formázás és az elrendezés teljes mértékben megőrzött — profi eredmények garantáltan.',
    'benefit_free_title'   => 'Kezdjen azonnal',
    'benefit_free_desc'    => 'Nincs telepítés, nincs regisztráció. Közvetlenül a böngészőben működik bármilyen eszközön.',

    // Trust badges (under upload zone)
    'trust_fast'       => 'Azonnali eredmény',
    'trust_secure'     => 'GDPR-megfelelő',
    'trust_quality'    => '100% minőség',
    'trust_delete'     => 'Fájlok 1 óra után törölve',

    // Social proof stats
    'stat_docs'        => 'Dokumentum konvertálva',
    'stat_users'       => 'Elégedett felhasználó',
    'stat_quality'     => 'Minőség garantálva',

    // JS-side messages (used in inline <script>)
    'js_only_one_file' => 'Csak egy fájl engedélyezett.',
    'js_max_files' => 'Legfeljebb {n} fájl engedélyezett egyszerre.',
    'js_file_too_large' => 'A(z) „{name}" fájl túl nagy. Maximális fájlméret: {size} MB',
    'js_add_another' => 'További fájl hozzáadása',
    'js_files_added' => 'Fájlok: {n}',
    'js_drag_to_reorder' => 'Húzza alább a sorrend megváltoztatásához',
    'js_upload_failed' => 'Feltöltés sikertelen.',
    'js_conversion_failed' => 'Konverzió sikertelen.',
];
