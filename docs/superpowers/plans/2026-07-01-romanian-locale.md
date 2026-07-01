# Romanian Locale (ro) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add Romanian as the 6th supported locale on sofortpdf.com.

**Architecture:** `ro` wired identically to `hu`/`cs`/`pl` — English URL slugs, locale regex extended, 20 new `lang/ro/` files, new `config/tools_ro.php`. No new blade views; legal/auth reuse `{locale}` prefix routing.

**Tech Stack:** Laravel 8.75, PHP 8, config-based locale system.

## Global Constraints

- Tool URL slugs for `ro` identical to `en` (e.g. `pdf-to-word`, `merge-pdf`).
- `config/tools_ro.php` contains only content overrides (h1/h2/description/meta_description/action_label); `enabled` lives in `config/tools.php`.
- No new blade views; no CSS rebuild needed.
- After all tasks: full 5-locale smoke check before deploying.

---

### Task 1: Wire `ro` into config and routes

**Files:**
- Modify: `config/locales.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Add `ro` to `config/locales.php`**

Add `'ro'` to `supported` array:
```php
'supported' => ['de', 'en', 'hu', 'cs', 'pl', 'ro'],
```

Add `'ro'` slug block inside `tool_slugs` (after `'pl'` block):
```php
'ro' => [
    'merge'        => 'merge-pdf',
    'compress'     => 'compress-pdf',
    'image-to-pdf' => 'image-to-pdf',
    'jpg-to-pdf'   => 'jpg-to-pdf',
    'pdf-to-word'  => 'pdf-to-word',
    'word-to-pdf'  => 'word-to-pdf',
    'pdf-to-jpg'   => 'pdf-to-jpg',
    'split'        => 'split-pdf',
    'edit'         => 'edit-pdf',
    'sign'         => 'sign-pdf',
    'pdf-to-excel' => 'pdf-to-excel',
    'excel-to-pdf' => 'excel-to-pdf',
    'rotate'       => 'rotate-pdf',
    'protect'      => 'protect-pdf',
    'unlock'       => 'unlock-pdf',
    'watermark'    => 'add-watermark',
    'page-numbers' => 'add-page-numbers',
    'pdf-to-ppt'   => 'pdf-to-powerpoint',
    'ppt-to-pdf'   => 'powerpoint-to-pdf',
    'pdf-to-png'   => 'pdf-to-png',
    'png-to-pdf'   => 'png-to-pdf',
    'ocr'          => 'ocr-pdf',
    'remove-pages' => 'remove-pages',
    'extract-pages'=> 'extract-pages',
    'html-to-pdf'  => 'html-to-pdf',
    'optimize'     => 'optimize-pdf',
],
```

Add `'ro'` block inside `tool_titles` (after `'pl'` block):
```php
'ro' => [
    'merge'        => 'Îmbinare PDF',
    'compress'     => 'Comprimă PDF',
    'image-to-pdf' => 'Imagine la PDF',
    'jpg-to-pdf'   => 'JPG la PDF',
    'pdf-to-word'  => 'PDF la Word',
    'word-to-pdf'  => 'Word la PDF',
    'pdf-to-jpg'   => 'PDF la JPG',
    'split'        => 'Desparte PDF',
    'edit'         => 'Editează PDF',
    'sign'         => 'Semnează PDF',
    'pdf-to-excel' => 'PDF la Excel',
    'excel-to-pdf' => 'Excel la PDF',
    'rotate'       => 'Rotește PDF',
    'protect'      => 'Protejează PDF',
    'unlock'       => 'Deblochează PDF',
    'watermark'    => 'Adaugă filigran',
    'page-numbers' => 'Adaugă numere de pagini',
    'pdf-to-ppt'   => 'PDF la PowerPoint',
    'ppt-to-pdf'   => 'PowerPoint la PDF',
    'pdf-to-png'   => 'PDF la PNG',
    'png-to-pdf'   => 'PNG la PDF',
    'ocr'          => 'Recunoaștere text (OCR)',
    'remove-pages' => 'Eliminare pagini',
    'extract-pages'=> 'Extragere pagini',
    'html-to-pdf'  => 'HTML la PDF',
    'optimize'     => 'Optimizare PDF',
],
```

Add `'ro'` to `aliases`, `auth_slugs`, `legal_slugs`, `contact_slugs`, `cancellation_slugs`:
```php
// aliases
'ro' => [],

// auth_slugs
'ro' => ['login' => 'login', 'logout' => 'logout', 'password_reset' => 'password-reset'],

// legal_slugs
'ro' => ['imprint' => 'imprint', 'privacy' => 'privacy', 'terms' => 'terms', 'cookies' => 'cookie-policy'],

// contact_slugs
'ro' => 'contact',

// cancellation_slugs
'ro' => 'cancel',
```

- [ ] **Step 2: Update `routes/web.php` — three changes**

Change 1 — locale regex (line ~244):
```php
->where(['locale' => 'de|en|hu|cs|pl|ro'])
```

Change 2 — geo-redirect country map (add after `'PL' => 'pl',`):
```php
'RO' => 'ro',
```

Change 3 — sitemap homepage priority (add after `'pl' => '1.0',`):
```php
'ro' => '1.0',
```

And update sitemap tool-page priority condition (find the line with `in_array($locale, ['hu', 'cs', 'pl']`):
```php
$priority = in_array($locale, ['hu', 'cs', 'pl', 'ro'], true) ? '0.9' : '0.6';
```

- [ ] **Step 3: Verify**
```bash
php artisan config:clear && php artisan route:list | grep "ro/" | head -5
```
Expected: several `/ro/...` routes listed.

- [ ] **Step 4: Commit**
```bash
git add config/locales.php routes/web.php
git commit -m "feat(ro): wire Romanian locale into config and routes"
```

---

### Task 2: Wire `ro` into views

**Files:**
- Modify: `resources/views/partials/seo.blade.php`
- Modify: `resources/views/layouts/app.blade.php`

- [ ] **Step 1: Update `seo.blade.php` — three additions**

In `$ogLocaleMap`, add after `'cs' => 'cs_CZ',`:
```php
'ro' => 'ro_RO',
```

In `$orgDescriptionMap`, add after the `'cs'` entry:
```php
'ro' => 'Instrumente PDF online — îmbinare, comprimare, conversie și altele.',
```

In `$hreflangTags`, add after `'cs' => 'cs',`:
```php
'ro' => 'ro',
```

- [ ] **Step 2: Update `app.blade.php` — language switcher**

Find the `$localeMeta` array (around line 111). Add after `'pl' => ['flag' => '🇵🇱', 'label' => 'Polski'],`:
```php
'ro' => ['flag' => '🇷🇴', 'label' => 'Română'],
```

- [ ] **Step 3: Verify**
```bash
php artisan view:clear && echo "Blade syntax OK"
```

- [ ] **Step 4: Commit**
```bash
git add resources/views/partials/seo.blade.php resources/views/layouts/app.blade.php
git commit -m "feat(ro): add hreflang, og:locale and language switcher for Romanian"
```

---

### Task 3: Create `config/tools_ro.php`

**Files:**
- Create: `config/tools_ro.php`

- [ ] **Step 1: Create the file**

```php
<?php

return [
    'merge' => [
        'h1'               => 'Îmbinare PDF',
        'h2'               => 'Combină PDF, Word, Excel, PowerPoint, JPG și PNG într-un singur document',
        'description'      => 'Combină PDF, Word, Excel, PowerPoint și imagini într-un singur PDF',
        'meta_description' => 'Îmbinare PDF — combină PDF, Word, Excel, PowerPoint și imagini într-un document instant online. Rapid, sigur și fără instalare.',
        'action_label'     => 'Îmbinare fișiere',
    ],
    'compress' => [
        'h1'               => 'Comprimă PDF',
        'h2'               => 'Reduce dimensiunea fișierului PDF online — fără pierdere de calitate',
        'description'      => 'Reduce dimensiunea fișierului PDF online fără pierdere de calitate',
        'meta_description' => 'Comprimă PDF — reduce dimensiunea fișierelor PDF online. Rapid și fără pierdere de calitate.',
        'action_label'     => 'Comprimă',
    ],
    'image-to-pdf' => [
        'h1'               => 'Imagine la PDF',
        'h2'               => 'Convertește orice imagine la PDF instant — JPG, PNG, GIF, BMP, WebP și altele',
        'description'      => 'Convertește orice format de imagine la PDF — JPG, PNG, GIF, BMP, WebP, TIFF, SVG',
        'meta_description' => 'Imagine la PDF — convertește orice imagine (JPG, PNG, GIF, BMP, WebP, TIFF, SVG) la un document PDF instant. Rapid, sigur și online.',
        'action_label'     => 'Convertește la PDF',
    ],
    'jpg-to-pdf' => [
        'h1'               => 'JPG la PDF',
        'h2'               => 'Convertește imagini la PDF instant — JPG, PNG și altele',
        'description'      => 'Convertește imagini la documente PDF instant',
        'meta_description' => 'JPG la PDF — convertește imagini la documente PDF instant. Suportă JPG, PNG și alte formate.',
        'action_label'     => 'Creează PDF',
    ],
    'pdf-to-word' => [
        'h1'               => 'PDF la Word',
        'h2'               => 'Convertește PDF la document Word — editabil și formatat',
        'description'      => 'Convertește PDF la documente Word editabile',
        'meta_description' => 'PDF la Word — convertește fișiere PDF la documente Word editabile. Formatare păstrată.',
        'action_label'     => 'Convertește la Word',
    ],
    'word-to-pdf' => [
        'h1'               => 'Word la PDF',
        'h2'               => 'Salvează documente Word ca PDF instant',
        'description'      => 'Convertește documente Word la PDF instant',
        'meta_description' => 'Word la PDF — convertește documente Word la fișiere PDF instant. Rapid și ușor online.',
        'action_label'     => 'Convertește la PDF',
    ],
    'pdf-to-jpg' => [
        'h1'               => 'PDF la JPG',
        'h2'               => 'Exportă paginile PDF ca imagini — calitate înaltă',
        'description'      => 'Exportă paginile PDF ca imagini JPG de calitate înaltă',
        'meta_description' => 'PDF la JPG — exportă paginile PDF ca imagini JPG de calitate înaltă. Rapid și online.',
        'action_label'     => 'Convertește la JPG',
    ],
    'split' => [
        'h1'               => 'Desparte PDF',
        'h2'               => 'Desparte PDF — extrage și separă pagini',
        'description'      => 'Desparte fișiere PDF și extrage pagini individuale',
        'meta_description' => 'Desparte PDF — desparte fișiere PDF și extrage pagini individuale. Rapid și online.',
        'action_label'     => 'Desparte PDF',
    ],
    'edit' => [
        'h1'               => 'Editează PDF',
        'h2'               => 'Editează PDF online — adaugă text, imagini și adnotări',
        'description'      => 'Editează fișiere PDF online cu instrumente simple',
        'meta_description' => 'Editează PDF — editează fișiere PDF online. Adaugă text, imagini, adnotări.',
        'action_label'     => 'Editează PDF',
    ],
    'sign' => [
        'h1'               => 'Semnează PDF',
        'h2'               => 'Adaugă semnătura digitală la PDF instant',
        'description'      => 'Semnează documente PDF cu semnătura ta digitală',
        'meta_description' => 'Semnează PDF — adaugă semnătura digitală la documente PDF instant. Rapid și sigur.',
        'action_label'     => 'Semnează PDF',
    ],
    'pdf-to-excel' => [
        'h1'               => 'PDF la Excel',
        'h2'               => 'Convertește PDF la foi de calcul Excel',
        'description'      => 'Convertește PDF la fișiere Excel editabile',
        'meta_description' => 'PDF la Excel — convertește fișiere PDF la foi de calcul Excel editabile. Rapid și online.',
        'action_label'     => 'Convertește la Excel',
    ],
    'excel-to-pdf' => [
        'h1'               => 'Excel la PDF',
        'h2'               => 'Salvează foi de calcul Excel ca PDF',
        'description'      => 'Convertește fișiere Excel la documente PDF',
        'meta_description' => 'Excel la PDF — convertește fișiere Excel la documente PDF instant.',
        'action_label'     => 'Convertește la PDF',
    ],
    'rotate' => [
        'h1'               => 'Rotește PDF',
        'h2'               => 'Rotește paginile PDF online — 90°, 180° sau 270°',
        'description'      => 'Rotește paginile din fișierele PDF online',
        'meta_description' => 'Rotește PDF — rotește paginile PDF online. Alege 90°, 180° sau 270°.',
        'action_label'     => 'Rotește',
    ],
    'protect' => [
        'h1'               => 'Protejează PDF',
        'h2'               => 'Adaugă parolă la fișierul tău PDF',
        'description'      => 'Protejează fișierele PDF cu o parolă',
        'meta_description' => 'Protejează PDF cu parolă — adaugă protecție prin parolă la fișierele PDF online.',
        'action_label'     => 'Adaugă parolă',
    ],
    'unlock' => [
        'h1'               => 'Deblochează PDF',
        'h2'               => 'Elimină protecția prin parolă din PDF',
        'description'      => 'Elimină parola și restricțiile din fișierele PDF',
        'meta_description' => 'Deblochează PDF — elimină protecția prin parolă din fișierele PDF online.',
        'action_label'     => 'Deblochează',
    ],
    'watermark' => [
        'h1'               => 'Adaugă filigran',
        'h2'               => 'Adaugă un filigran text sau imagine la PDF',
        'description'      => 'Adaugă filigran la fișierele tale PDF',
        'meta_description' => 'Filigran PDF — adaugă filigran text sau imagine la documente PDF online.',
        'action_label'     => 'Adaugă filigran',
    ],
    'page-numbers' => [
        'h1'               => 'Adaugă numere de pagini',
        'h2'               => 'Numerotează paginile PDF online',
        'description'      => 'Adaugă numere de pagini la fișierele PDF',
        'meta_description' => 'Numere de pagini PDF — adaugă numere de pagini la documentele PDF online.',
        'action_label'     => 'Adaugă numere',
    ],
    'pdf-to-ppt' => [
        'h1'               => 'PDF la PowerPoint',
        'h2'               => 'Convertește PDF la prezentare PowerPoint editabilă',
        'description'      => 'Convertește PDF la fișiere PowerPoint editabile',
        'meta_description' => 'PDF la PowerPoint — convertește fișiere PDF la prezentări PowerPoint editabile. Rapid și online.',
        'action_label'     => 'Convertește la PowerPoint',
    ],
    'ppt-to-pdf' => [
        'h1'               => 'PowerPoint la PDF',
        'h2'               => 'Convertește prezentări PowerPoint la PDF',
        'description'      => 'Salvează prezentările PowerPoint ca documente PDF',
        'meta_description' => 'PowerPoint la PDF — convertește prezentări PowerPoint la documente PDF instant.',
        'action_label'     => 'Convertește la PDF',
    ],
    'pdf-to-png' => [
        'h1'               => 'PDF la PNG',
        'h2'               => 'Exportă paginile PDF ca imagini PNG',
        'description'      => 'Exportă paginile PDF ca imagini PNG de calitate înaltă',
        'meta_description' => 'PDF la PNG — exportă paginile PDF ca imagini PNG de calitate înaltă. Rapid și online.',
        'action_label'     => 'Convertește la PNG',
    ],
    'png-to-pdf' => [
        'h1'               => 'PNG la PDF',
        'h2'               => 'Convertește imagini PNG la PDF',
        'description'      => 'Convertește imagini PNG la documente PDF',
        'meta_description' => 'PNG la PDF — convertește imagini PNG la documente PDF instant.',
        'action_label'     => 'Creează PDF',
    ],
    'ocr' => [
        'h1'               => 'Recunoaștere text (OCR)',
        'h2'               => 'Extrage text din PDF scanate sau imagini',
        'description'      => 'Recunoaștere optică a caracterelor din PDF și imagini',
        'meta_description' => 'OCR PDF — extrage și recunoaște text din PDF scanate și imagini. Rapid și precis online.',
        'action_label'     => 'Recunoaște text',
    ],
    'remove-pages' => [
        'h1'               => 'Eliminare pagini',
        'h2'               => 'Șterge pagini din documentul PDF',
        'description'      => 'Elimină paginile nedorite din fișierele PDF',
        'meta_description' => 'Eliminare pagini PDF — șterge pagini individuale din documente PDF online.',
        'action_label'     => 'Elimină pagini',
    ],
    'extract-pages' => [
        'h1'               => 'Extragere pagini',
        'h2'               => 'Extrage pagini specifice din PDF',
        'description'      => 'Extrage paginile selectate din fișierele PDF',
        'meta_description' => 'Extragere pagini PDF — extrage pagini specifice din documentele PDF online.',
        'action_label'     => 'Extrage pagini',
    ],
    'html-to-pdf' => [
        'h1'               => 'HTML la PDF',
        'h2'               => 'Convertește pagini web sau HTML la PDF',
        'description'      => 'Convertește fișiere HTML sau URL-uri web la documente PDF',
        'meta_description' => 'HTML la PDF — convertește pagini web și fișiere HTML la documente PDF instant.',
        'action_label'     => 'Convertește la PDF',
    ],
    'optimize' => [
        'h1'               => 'Optimizare PDF',
        'h2'               => 'Optimizează fișierele PDF pentru web sau arhivare',
        'description'      => 'Optimizează fișierele PDF pentru dimensiune și compatibilitate',
        'meta_description' => 'Optimizare PDF — optimizează documentele PDF pentru web, email sau arhivare. Rapid și online.',
        'action_label'     => 'Optimizează',
    ],
];
```

- [ ] **Step 2: Verify**
```bash
php artisan config:clear && php artisan tinker --execute="app()->setLocale('ro'); echo config('tools_ro.pdf-to-word.h1');"
```
Expected: `PDF la Word`

- [ ] **Step 3: Commit**
```bash
git add config/tools_ro.php
git commit -m "feat(ro): add Romanian tool content config"
```

---

### Task 4: Create `resources/lang/ro/` — small files (12 files)

**Files:** Create all 12 files below in `resources/lang/ro/`.

- [ ] **Step 1: Create `resources/lang/ro/auth.php`**
```php
<?php
return [
    'failed'   => 'Datele introduse nu corespund înregistrărilor noastre.',
    'password' => 'Parola introdusă este incorectă.',
    'throttle' => 'Prea multe încercări de autentificare. Vă rugăm să încercați din nou în :seconds secunde.',
];
```

- [ ] **Step 2: Create `resources/lang/ro/auth_ui.php`**
```php
<?php
return [
    'login_title_suffix'  => 'Autentificare - SofortPDF',
    'login_heading'       => 'Autentificare',
    'email'               => 'Adresă de email',
    'password'            => 'Parolă',
    'remember_me'         => 'Ține-mă minte',
    'forgot_password'     => 'Ai uitat parola?',
    'login_submit'        => 'Autentificare',
    'no_account'          => 'Nu ai cont încă?',
    'register_free'       => 'Înregistrează-te gratuit',

    'register_title_suffix' => 'Înregistrare - SofortPDF',
    'register_heading'      => 'Creează cont',
    'name'                  => 'Nume',
    'password_confirm'      => 'Confirmă parola',
    'already_registered'    => 'Ai deja cont?',
    'login_now'             => 'Autentifică-te acum',

    'reset_title_suffix'   => 'Resetare parolă - SofortPDF',
    'reset_heading'        => 'Resetare parolă',
    'reset_instructions'   => 'Introdu adresa de email și îți vom trimite un link pentru resetarea parolei.',
    'reset_submit'         => 'Trimite link de resetare',
    'back_to_login'        => 'Înapoi la autentificare',

    'reset_err_email_required' => 'Te rugăm să introduci adresa de email.',
    'reset_err_email_invalid'  => 'Te rugăm să introduci o adresă de email validă.',
    'reset_err_user_not_found' => 'Nu am găsit un utilizator cu această adresă de email.',
    'reset_status_sent'        => 'Ți-am trimis un link de resetare a parolei pe email.',

    'reset_confirm_title_suffix'  => 'Alege o nouă parolă - SofortPDF',
    'reset_confirm_heading'       => 'Alege o parolă nouă',
    'reset_confirm_instructions'  => 'Introdu o parolă nouă pentru contul tău.',
    'reset_new_password'          => 'Parolă nouă',
    'reset_confirm_password'      => 'Confirmă parola',
    'reset_confirm_submit'        => 'Salvează parola',
    'reset_err_password_required' => 'Te rugăm să introduci o parolă nouă.',
    'reset_err_password_confirmed'=> 'Parolele nu se potrivesc.',
    'reset_err_password_min'      => 'Parola trebuie să aibă cel puțin :min caractere.',
    'reset_err_token_invalid'     => 'Acest link de resetare este invalid sau a expirat. Te rugăm să soliciți unul nou.',
    'reset_status_updated'        => 'Parola a fost actualizată cu succes. Te poți autentifica acum.',
];
```

- [ ] **Step 3: Create `resources/lang/ro/cancellation.php`**
```php
<?php
return [
    'title'                  => 'Anulare abonament',
    'subtitle'               => 'Introdu adresa de email cu care te-ai înregistrat.',
    'email_label'            => 'Adresă de email',
    'email_placeholder'      => 'email@tau.com',
    'warning'                => 'După anulare vei pierde imediat accesul la toate instrumentele PDF premium. Această acțiune nu poate fi anulată.',
    'cancel_button'          => 'Anulează abonamentul',
    'confirm_title'          => 'Ești sigur?',
    'confirm_body'           => 'Abonamentul tău va fi anulat imediat și vei pierde accesul la toate funcțiile premium.',
    'keep_subscription'      => 'Păstrează abonamentul',
    'confirm_cancel_button'  => 'Da, anulează',
    'customer_not_found'     => 'Nu am găsit un utilizator cu această adresă de email.',
    'no_active_subscription' => 'Nu există un abonament activ pentru această adresă de email.',
    'cancel_failed'          => 'Anularea nu a putut fi procesată. Te rugăm să încerci din nou sau să contactezi suportul.',
    'success'                => 'Abonamentul tău a fost anulat cu succes.',
    'help_text'              => 'Probleme cu anularea?',
    'contact_support'        => 'Contactează suportul',
];
```

- [ ] **Step 4: Create `resources/lang/ro/checkout.php`**
```php
<?php
return [
    'payment_heading'    => 'Deblochează acum',
    'payment_subheading' => ':days zile de probă pentru doar :price €',
    'plan_name'          => 'sofortpdf Pro',
    'plan_tagline'       => 'Toate instrumentele PDF — nelimitat',
    'then_per_month'     => 'apoi :price €/lună',
    'email_label'        => 'Adresă de email',
    'email_placeholder'  => 'email@tau.com',
    'full_name_label'    => 'Nume complet',
    'full_name_placeholder' => 'Ion Popescu',
    'cardholder_label'   => 'Titular card',
    'cardholder_placeholder' => 'Ion Popescu',
    'card_number_label'  => 'Număr card',
    'expiry_label'       => 'Dată expirare',
    'submit_try_now'     => 'Încearcă acum pentru :price €',
    'terms_disclaimer_html' => 'Apăsând butonul ești de acord cu <a href=":terms_url" class="underline hover:text-slate-600" target="_blank">Termenii și Condițiile</a>. Perioada de probă se încheie după :days zile. Ulterior se va percepe :price €/lună. Poți anula oricând.',

    'js_processing'      => 'Se procesează…',
    'js_submit_try_now'  => 'Încearcă acum pentru :price €',
    'js_err_cardholder'  => 'Te rugăm să introduci numele titularului cardului.',
    'js_err_email'       => 'Te rugăm să introduci adresa de email.',
    'js_err_name'        => 'Te rugăm să introduci numele complet.',
    'js_err_generic'     => 'A apărut o eroare. Te rugăm să încerci din nou.',

    'success_title'        => 'Mulțumim - sofortpdf',
    'success_heading'      => 'Mulțumim! Perioada ta de probă a început.',
    'success_trial_info'   => 'Ai :days zile să testezi toate instrumentele gratuit.',
    'success_renewal_info' => 'După perioada de probă, abonamentul tău se va reînnoi automat la :price EUR pe lună. Poți anula oricând.',
    'success_cta'          => 'Începe acum',
];
```

- [ ] **Step 5: Create `resources/lang/ro/common.php`**
```php
<?php
return [
    'login'        => 'Autentificare',
    'logout'       => 'Deconectare',
    'register'     => 'Înregistrare',
    'dashboard'    => 'Cont',
    'imprint'      => 'Imprint',
    'privacy'      => 'Confidențialitate',
    'terms'        => 'Termeni',
    'back'         => 'Înapoi la pagina principală',
    'save'         => 'Salvează',
    'cancel'       => 'Anulează',
    'confirm'      => 'Confirmă',
    'continue'     => 'Continuă',
    'close'        => 'Închide',
    'users'        => 'Utilizatori',
    'gdpr_compliant'  => 'Conform GDPR',
    'ssl_encryption'  => 'Criptare SSL',
    'servers_in_eu'   => 'Servere în UE',
    'auto_deletion'   => 'Ștergere automată',
];
```

- [ ] **Step 6: Create `resources/lang/ro/confirmation.php`**
```php
<?php
return [
    'meta_title'        => 'Gata — Fișierul tău este pregătit',
    'meta_description'  => 'Fișierul tău a fost procesat și este gata de descărcare.',
    'heading'           => 'Gata!',
    'subheading'        => 'Fișierul tău a fost procesat cu succes și este gata de descărcare.',
    'subheading_paid'   => 'Mulțumim pentru plată. Fișierul tău a fost procesat cu succes și este gata de descărcare.',
    'auto_download_note'=> 'Descărcarea începe automat. Dacă nu, apasă butonul de mai jos.',
    'filename_label'    => 'Nume fișier',
    'download_button'   => 'Descarcă fișierul',
    'redownload_button' => 'Descarcă din nou',
    'dashboard_button'  => 'Mergi la contul meu',
    'home_button'       => 'Înapoi la pagina principală',
    'no_token_heading'  => 'Nicio descărcare disponibilă',
    'no_token_message'  => 'Nu am găsit un fișier pentru acest link. Este posibil să fi expirat.',
    'working_heading'   => 'Pregătim fișierul tău',
    'working_message'   => 'Procesarea rulează în fundal. Poți lăsa pagina deschisă — se va actualiza automat când fișierul tău este gata.',
    'working_dashboard_hint' => 'Poți și să închizi pagina: rezultatul va apărea în spațiul tău personal și îți vom trimite un email când este gata.',
    'working_elapsed'   => 'Se procesează de :seconds sec.',
    'failed_heading'    => 'Procesarea a eșuat',
    'failed_message_fallback' => 'Fișierul tău nu a putut fi procesat. Te rugăm să încerci din nou.',
    'retry_button'      => 'Încearcă din nou',
];
```

- [ ] **Step 7: Create `resources/lang/ro/contact_ui.php`**
```php
<?php
return [
    'title_suffix'      => 'Contact - SofortPDF',
    'heading'           => 'Contact',
    'sub'               => 'Ai o întrebare sau o problemă? Trimite-ne un mesaj — de obicei răspundem în cel mult o zi lucrătoare.',
    'label_name'        => 'Nume',
    'label_email'       => 'Adresă de email',
    'label_message'     => 'Mesajul tău',
    'privacy_notice'    => 'Prin trimiterea acestui formular ești de acord ca noi să procesăm informațiile tale pentru a răspunde solicitării tale. Vezi politica noastră de confidențialitate pentru detalii.',
    'submit'            => 'Trimite mesaj',
    'success'           => 'Mulțumim! Am primit mesajul tău și îți vom răspunde în curând.',
    'err_name_required'    => 'Te rugăm să ne spui numele tău.',
    'err_email_required'   => 'Te rugăm să introduci o adresă de email.',
    'err_email_invalid'    => 'Te rugăm să introduci o adresă de email validă.',
    'err_message_required' => 'Te rugăm să ne scrii un mesaj.',
    'err_message_min'      => 'Mesajul tău este prea scurt (cel puțin 10 caractere).',
    'err_send_failed'      => 'Mesajul tău nu a putut fi trimis. Te rugăm să încerci din nou mai târziu.',
    'err_rate_limited'     => 'Prea multe solicitări de la IP-ul tău. Te rugăm să încerci din nou mai târziu.',
    'err_generic'          => 'Solicitarea ta nu a putut fi procesată.',
    'footer_link'          => 'Contact',
];
```

- [ ] **Step 8: Create `resources/lang/ro/errors.php`**
```php
<?php
return [
    'service_unavailable' => 'Serviciul de conversie este temporar indisponibil. Te rugăm să încerci din nou mai târziu.',
    'conversion_failed'   => 'Conversia a eșuat. Te rugăm să încerci din nou.',
    'timeout'             => 'Conversia a durat prea mult. Te rugăm să încerci cu un fișier mai mic.',
    'file_too_large'      => 'Fișierul este prea mare. Dimensiunea maximă: :size MB',
    'unsupported_input'   => 'Formatul de intrare nu este suportat de acest instrument.',
];
```

- [ ] **Step 9: Create `resources/lang/ro/pagination.php`**
```php
<?php
return [
    'previous' => '&laquo; Anterior',
    'next'     => 'Următor &raquo;',
];
```

- [ ] **Step 10: Create `resources/lang/ro/passwords.php`**
```php
<?php
return [
    'reset'     => 'Parola ta a fost resetată!',
    'sent'      => 'Ți-am trimis linkul de resetare a parolei pe email!',
    'throttled' => 'Te rugăm să aștepți înainte de a reîncerca.',
    'token'     => 'Acest token de resetare a parolei este invalid.',
    'user'      => 'Nu găsim un utilizator cu această adresă de email.',
];
```

- [ ] **Step 11: Create `resources/lang/ro/sign.php`**
```php
<?php
return [
    'drop_or_click'      => 'Trage PDF-ul aici sau apasă pentru a selecta',
    'format_hint'        => 'Format: PDF · Max. :size MB',
    'create_signature'   => 'Creează semnătură',
    'reset'              => 'Resetează',
    'page_indicator'     => 'Pagina :current din :total',
    'placement_hint'     => 'Apasă pe document pentru a plasa semnătura',
    'submit'             => 'Semnează',
    'submitting'         => 'Se semnează PDF-ul…',
    'processing_heading' => 'Se semnează PDF-ul…',
    'processing_note'    => 'Te rugăm să aștepți un moment.',
    'download_ready'     => 'Gata! PDF-ul semnat este pregătit pentru descărcare.',
    'download'           => 'Descarcă',
    'error_generic'      => 'A apărut o eroare. Te rugăm să încerci din nou.',
    'try_again'          => 'Încearcă din nou',
    'modal_heading'      => 'Desenează semnătura ta',
    'modal_hint'         => 'Desenează semnătura cu mouse-ul sau degetul',
    'clear'              => 'Șterge',
    'cancel'             => 'Anulează',
    'apply'              => 'Aplică',
    'err_file_too_large' => 'Fișierul este prea mare. Dimensiunea maximă: :size MB',
    'err_not_pdf'        => 'Te rugăm să încarci un fișier PDF.',
    'err_sign_failed'    => 'Semnarea a eșuat.',
];
```

- [ ] **Step 12: Create `resources/lang/ro/trust.php`**
```php
<?php
return [
    'ssl_encrypted'    => 'Criptat SSL',
    'eu_servers'       => 'Servere în Europa',
    'auto_deletion_1h' => 'Ștergere automată după 1h',
    'no_watermark'     => 'Fără filigran',
];
```

- [ ] **Step 13: Verify**
```bash
php artisan config:clear && php artisan tinker --execute="app()->setLocale('ro'); echo __('trust.ssl_encrypted');"
```
Expected: `Criptat SSL`

- [ ] **Step 14: Commit**
```bash
git add resources/lang/ro/
git commit -m "feat(ro): add Romanian translations — small UI files"
```

---

### Task 5: Create `resources/lang/ro/` — large files (8 files)

- [ ] **Step 1: Create `resources/lang/ro/layout.php`**
```php
<?php
return [
    'nav_all_tools' => 'Toate instrumentele',
    'nav_convert'   => 'Conversie',
    'nav_legal'     => 'Legal',
    'nav_tools'     => 'Instrumente PDF',
    'nav_dashboard' => 'Cont',
    'nav_login'     => 'Autentificare',
    'nav_logout'    => 'Deconectare',
    'footer_brand_tagline' => 'Instrumentele tale online pentru PDF. Rapid, sigur și fără instalare.',
    'footer_imprint'   => 'Imprint',
    'footer_privacy'   => 'Confidențialitate',
    'footer_terms'     => 'Termeni',
    'footer_copyright' => '© :year sofortpdf.com — Toate drepturile rezervate.',
    'footer_ssl'       => 'Criptat SSL',
    'footer_eu_servers'=> 'Servere în Europa',
    'footer_cookies'   => 'Cookie-uri',
    'footer_cancel'    => 'Anulare',
    'breadcrumb_home'  => 'Acasă',
];
```

- [ ] **Step 2: Create `resources/lang/ro/home.php`**
```php
<?php
return [
    'hero_badge'           => 'Toate instrumentele PDF într-un singur loc',
    'hero_title_line1'     => 'Editează fișiere PDF—',
    'hero_title_highlight' => 'instant și în siguranță',
    'hero_subtitle'        => 'Instrumente PDF profesionale direct în browser — îmbinare, comprimare, conversie și altele.',
    'hero_cta_primary'     => 'Începe acum',
    'hero_cta_secondary'   => 'Vezi toate instrumentele',

    'social_users'    => 'Utilizatori',
    'social_gdpr'     => 'Conform GDPR',
    'social_eu_servers' => 'Servere în UE',

    'tools_heading'    => 'Toate instrumentele PDF',
    'tools_subheading' => 'Alege instrumentul potrivit și începe imediat.',

    'how_heading'      => 'Cum funcționează',
    'how_subheading'   => 'Obține rezultate în trei pași simpli.',
    'how_step1_title'  => 'Încarcă fișierul',
    'how_step1_desc'   => 'Selectează fișierul sau trage-l în zona de încărcare.',
    'how_step2_title'  => 'Procesare automată',
    'how_step2_desc'   => 'Sistemul nostru convertește fișierul în câteva secunde — sigur și fiabil.',
    'how_step3_title'  => 'Descarcă rezultatul',
    'how_step3_desc'   => 'Descarcă fișierul finalizat. Rapid, sigur și fără așteptare.',

    'testimonials_heading'    => 'Ce spun utilizatorii noștri',
    'testimonials_subheading' => 'De încredere pentru mii de profesioniști în fiecare zi.',
    'testimonial_1_quote' => 'Folosesc sofortpdf zilnic pentru a îmbina PDF-uri pentru clienții mei. Simplu, rapid și fiabil.',
    'testimonial_1_name'  => 'Thomas M.',
    'testimonial_1_role'  => 'Consultant fiscal',
    'testimonial_1_tool'  => 'Îmbinare PDF',
    'testimonial_2_quote' => 'Compresia PDF este perfectă — documentele mele pentru aplicații au devenit instant suficient de mici pentru email.',
    'testimonial_2_name'  => 'Lisa K.',
    'testimonial_2_role'  => 'Studentă',
    'testimonial_2_tool'  => 'Comprimă PDF',
    'testimonial_3_quote' => 'În sfârșit un instrument care convertește curat facturile PDF în Excel. Îmi economisește ore întregi.',
    'testimonial_3_name'  => 'Markus W.',
    'testimonial_3_role'  => 'Contabil',
    'testimonial_3_tool'  => 'PDF la Excel',
    'testimonial_4_quote' => 'Ca avocat, trebuie adesea să semnez PDF-uri. sofortpdf face acest lucru sigur și simplu.',
    'testimonial_4_name'  => 'Dr. Anna S.',
    'testimonial_4_role'  => 'Avocat',
    'testimonial_4_tool'  => 'Semnează PDF',
    'testimonial_5_quote' => 'Folosim sofortpdf în întregul birou. Conformitatea GDPR a fost deosebit de importantă pentru noi.',
    'testimonial_5_name'  => 'Stefan R.',
    'testimonial_5_role'  => 'Director general',
    'testimonial_5_tool'  => 'PDF la Word',
    'testimonial_6_quote' => 'Rapid, sigur și fără instalare — exact ce căutam.',
    'testimonial_6_name'  => 'Maria H.',
    'testimonial_6_role'  => 'Freelancer',
    'testimonial_6_tool'  => 'Desparte PDF',

    'trust_heading'        => 'Datele tale sunt în siguranță',
    'trust_subheading'     => 'Confidențialitatea și securitatea sunt prioritatea noastră.',
    'trust_ssl_title'      => 'Criptare SSL',
    'trust_ssl_desc'       => 'Toate transferurile de date sunt criptate cu SSL/TLS pe 256 de biți — la fel ca internetul banking.',
    'trust_gdpr_title'     => 'Conform GDPR',
    'trust_gdpr_desc'      => 'Procesăm datele tale în deplină conformitate cu GDPR. Fără partajare cu terți.',
    'trust_deletion_title' => 'Ștergere automată',
    'trust_deletion_desc'  => 'Fișierele încărcate sunt șterse automat și permanent după o oră.',
    'trust_servers_title'  => 'Servere în Europa',
    'trust_servers_desc'   => 'Serverele noastre se află în Germania și UE. Datele tale nu părăsesc niciodată Europa.',

    'faq_heading'    => 'Întrebări frecvente',
    'faq_subheading' => 'Tot ce trebuie să știi despre sofortpdf.',
    'faq_1_q' => 'Este sofortpdf gratuit?',
    'faq_1_a' => 'sofortpdf oferă o perioadă de probă pentru :trial_price :currency. Ulterior, abonamentul costă :sub_price :currency/lună. Poți anula oricând.',
    'faq_2_q' => 'Sunt datele mele în siguranță?',
    'faq_2_a' => 'Da. Toate fișierele sunt criptate prin SSL, procesate pe servere din UE și șterse automat după 1 oră.',
    'faq_3_q' => 'Ce formate de fișiere sunt suportate?',
    'faq_3_a' => 'PDF, Word (DOCX), Excel (XLSX), PowerPoint (PPTX), JPG, PNG și multe alte formate.',
    'faq_4_q' => 'Trebuie să instalez vreun software?',
    'faq_4_a' => 'Nu. sofortpdf funcționează complet în browser — pe desktop, tabletă și smartphone.',
    'faq_5_q' => 'Pot folosi sofortpdf pe telefon?',
    'faq_5_a' => 'Da, sofortpdf este complet optimizat pentru mobil și funcționează pe toate dispozitivele.',
    'faq_6_q' => 'Cum anulez abonamentul?',
    'faq_6_a' => 'Poți anula oricând din tabloul de bord. Accesul rămâne activ până la sfârșitul perioadei de facturare.',

    'cta_title'         => 'Toate instrumentele PDF. Un singur preț.',
    'cta_subtitle'      => 'Acces nelimitat la toate instrumentele — fără costuri ascunse.',
    'cta_try_now'       => 'Încearcă acum',
    'cta_trial_days'    => 'pentru :days zile de probă',
    'cta_unlock'        => 'Deblochează acum',
    'cta_no_auto_renewal' => 'Anulează oricând. Fără angajament.',

    'meta_title'       => 'Instrumente PDF Online — Rapid și Sigur',
    'meta_description' => 'sofortpdf.com — Instrumentele tale online pentru PDF. Îmbinare, comprimare, conversie PDF și altele. Rapid, sigur și fără instalare.',
];
```

- [ ] **Step 3: Create `resources/lang/ro/tool.php`**
```php
<?php
return [
    'drop_or_click'    => 'Trage fișierul aici sau apasă pentru a selecta',
    'formats_label'    => 'Formate:',
    'up_to_files'      => 'Până la :n fișiere',

    'processing'           => 'Se procesează…',
    'js_starting_conversion' => 'Se pornește conversia…',
    'please_wait'          => 'Te rugăm să aștepți, pregătim documentul tău.',
    'loading_step_1'       => 'Se încarcă documentul tău.',
    'loading_step_2'       => 'Se convertește documentul tău.',
    'loading_step_3'       => 'Se pregătește descărcarea.',
    'loading_converting'   => ':tool — se procesează.',
    'loading_signing'      => 'Se procesează semnătura ta.',

    'fake_loading_title'   => 'Conversia este în curs, te rugăm să aștepți un moment',
    'fake_loading_step_1'  => 'Se încarcă documentul tău',
    'fake_loading_step_2'  => 'Se convertește documentul tău',
    'fake_loading_step_3'  => 'Se securizează documentul tău',

    'done'              => 'Gata!',
    'ready_for_download'=> 'Fișierul tău este pregătit pentru descărcare.',
    'download'          => 'Descarcă',
    'process_another'   => 'Procesează alt fișier',

    'error_generic'     => 'A apărut o eroare. Te rugăm să încerci din nou.',
    'try_again'         => 'Încearcă din nou',

    'how_heading'       => 'Cum funcționează',
    'step_label'        => 'Pasul :n',
    'step1_title'       => 'Încarcă fișierul',
    'step1_desc'        => 'Trage fișierul în zona de încărcare sau apasă pentru a selecta.',
    'step2_title'       => 'Procesare automată',
    'step2_desc'        => 'Serverele noastre procesează fișierul în câteva secunde — sigur și fiabil.',
    'step3_title'       => 'Descarcă',
    'step3_desc'        => 'Descarcă fișierul finalizat instant. Fără așteptare.',

    'faq_heading'       => 'Întrebări frecvente',
    'faq_secure_q'      => 'Este sigur de utilizat?',
    'faq_secure_a'      => 'Da. Toate fișierele sunt transferate printr-o conexiune criptată SSL și șterse automat după 1 oră.',
    'faq_formats_q'     => 'Ce formate de fișiere sunt suportate?',
    'faq_formats_a'     => 'Acest instrument suportă următoarele formate: :formats.',
    'faq_size_q'        => 'Există o dimensiune maximă a fișierului?',
    'faq_size_a'        => 'Dimensiunea maximă a fișierului este de :size MB per fișier.',
    'faq_mobile_q'      => 'Funcționează pe mobil?',
    'faq_mobile_a'      => 'Da, sofortpdf.com funcționează pe toate dispozitivele — desktop, tabletă și smartphone.',

    'watermark_text_label'       => 'Text filigran',
    'watermark_text_placeholder' => 'ex. CONFIDENȚIAL',
    'watermark_text_hint'        => 'Acest text va fi tipărit ca filigran pe fiecare pagină.',
    'param_required_suffix'      => '*',
    'param_required_error'       => 'Te rugăm să completezi toate câmpurile obligatorii.',

    'rotate_angle_label'         => 'Unghi de rotație',

    'protect_password_label'       => 'Parolă',
    'protect_password_placeholder' => 'Alege o parolă puternică',
    'protect_password_hint'        => 'Această parolă va fi necesară pentru a deschide PDF-ul.',

    'unlock_password_label'       => 'Parola curentă',
    'unlock_password_placeholder' => 'Introdu parola PDF-ului',
    'unlock_password_hint'        => 'Parola cu care este protejat în prezent PDF-ul.',

    'pages_placeholder'      => 'ex. 1-3, 5, 7-9',
    'pages_hint'             => 'Numere de pagini sau intervale separate prin virgulă.',
    'pages_remove_label'     => 'Pagini de eliminat',
    'pages_extract_label'    => 'Pagini de extras',

    'picker_remove_heading'  => 'Selectează paginile de eliminat',
    'picker_extract_heading' => 'Selectează paginile de extras',
    'picker_remove_hint'     => 'Apasă pe paginile pe care vrei să le elimini.',
    'picker_extract_hint'    => 'Apasă pe paginile pe care vrei să le păstrezi.',
    'picker_page_label'      => 'Pagina :n',
    'picker_loading'         => 'Se încarcă paginile…',
    'picker_selected_count_remove'  => ':n pagină(i) marcată(e) pentru eliminare',
    'picker_selected_count_extract' => ':n pagină(i) marcată(e) pentru extragere',
    'picker_need_selection_remove'  => 'Selectează cel puțin o pagină de eliminat.',
    'picker_need_selection_extract' => 'Selectează cel puțin o pagină de extras.',
    'picker_select_all'      => 'Selectează toate',
    'picker_select_none'     => 'Deselectează toate',

    'picker_rotate_heading'  => 'Apasă paginile pentru rotire',
    'picker_rotate_hint'     => 'Fiecare apăsare rotește pagina cu 90° în sensul acelor de ceasornic.',
    'picker_rotate_count'    => ':n pagină(i) rotită(e)',
    'picker_need_rotation'   => 'Rotește cel puțin o pagină apăsând pe ea.',
    'picker_reset_rotations' => 'Resetează rotițiile',

    'picker_split_heading'   => 'Plasează puncte de tăiere între pagini',
    'picker_split_hint'      => 'Apasă între două pagini pentru a insera un punct de tăiere. Fiecare grup rezultat devine propriul PDF.',
    'picker_split_count'     => ':n grup(uri) — :groups',
    'picker_need_split'      => 'Plasează cel puțin un punct de tăiere.',
    'picker_reset_splits'    => 'Resetează tăieturile',

    'ocr_language_label'     => 'Limbă',
    'ocr_language_hint'      => 'Selectează limba textului din document.',
    'ocr_lang_deu'           => 'Germană',
    'ocr_lang_eng'           => 'Engleză',
    'ocr_lang_deu_eng'       => 'Germană + Engleză',
    'ocr_lang_spa'           => 'Spaniolă',
    'ocr_lang_fra'           => 'Franceză',
    'ocr_lang_ita'           => 'Italiană',
    'ocr_lang_por'           => 'Portugheză',
    'ocr_lang_nld'           => 'Olandeză',

    'related_heading'        => 'Mai multe instrumente PDF',

    'title_suffix'           => ' — Instant și Online',
    'default_action_label'   => 'Convertește acum',
    'upload_button'          => 'Încarcă fișierul tău',
    'convert_now_button'     => 'Convertește fișierul acum!',
    'maintenance_suffix'     => ' — Mentenanță',
    'maintenance_heading'    => 'Mentenanță',
    'maintenance_body'       => 'Acest instrument este temporar indisponibil. Te rugăm să încerci din nou mai târziu.',

    'benefit_fast_title'    => 'Procesare fulger',
    'benefit_fast_desc'     => 'Documentele tale sunt procesate în câteva secunde. Fără așteptare, fără întârzieri.',
    'benefit_secure_title'  => 'Securitate maximă',
    'benefit_secure_desc'   => 'Fișierele sunt criptate în tranzit și șterse automat după 1 oră.',
    'benefit_quality_title' => 'Calitate perfectă',
    'benefit_quality_desc'  => 'Formatarea și aspectul sunt complet păstrate — rezultate profesionale garantate.',
    'benefit_free_title'    => 'Începe instant',
    'benefit_free_desc'     => 'Fără instalare, fără înregistrare necesară. Funcționează direct în browser pe orice dispozitiv.',

    'trust_fast'    => 'Rezultate instant',
    'trust_secure'  => 'Conform GDPR',
    'trust_quality' => '100% calitate',
    'trust_delete'  => 'Fișiere șterse după 1h',

    'stat_docs'     => 'Documente convertite',
    'stat_users'    => 'Utilizatori mulțumiți',
    'stat_quality'  => 'Calitate garantată',

    'js_only_one_file'    => 'Este permis un singur fișier.',
    'js_max_files'        => 'Maximum {n} fișiere permise simultan.',
    'js_file_too_large'   => 'Fișierul "{name}" este prea mare. Dimensiunea maximă: {size} MB',
    'js_add_another'      => 'Adaugă alt fișier',
    'js_files_added'      => 'Fișiere: {n}',
    'js_drag_to_reorder'  => 'Trage mai jos pentru a reordona',
    'js_upload_failed'    => 'Încărcarea a eșuat.',
    'js_conversion_failed'=> 'Conversia a eșuat.',
];
```

- [ ] **Step 4: Create `resources/lang/ro/payment.php`**
```php
<?php
return [
    'step_1' => 'Creează cont',
    'step_2' => 'Plată securizată',
    'step_3' => 'Descărcare instant',

    'heading'    => 'Procesează document',
    'subheading' => 'Deblochează toate instrumentele PDF pentru :days zile — doar :price',

    'summary_title'       => 'Fișierul tău',
    'summary_description' => 'După plată, fișierul tău este procesat și descărcat instant.',
    'total_label'         => 'Prețul de probă',
    'full_price_label'    => 'apoi :price/lună',
    'after_trial_label'   => 'Apoi :price/lună · Anulezi oricând',
    'promo_label'         => 'Preț promoțional',
    'discount_label'      => '-:percent%',

    'included_title'  => 'Ce este inclus',
    'included_item_1' => 'Descărcare instant a fișierului tău',
    'included_item_2' => 'Toate instrumentele PDF deblocate',
    'included_item_3' => ':days zile de utilizare nelimitată',

    'secure_payment'  => 'Plată securizată',
    'ssl_badge'       => 'Criptat SSL',
    'guarantee_badge' => 'Garanție de returnare 14 zile',
    'gdpr_badge'      => 'Conform GDPR',
    'delete_badge'    => 'Fișiere șterse după 1h',

    'form_full_name'    => 'Numele titularului cardului',
    'form_email'        => 'Email pentru documentul tău',
    'form_card_details' => 'Detalii card',
    'form_encrypted'    => 'Criptat',

    'pay_button'  => 'Obține documentul acum!',
    'preview_link'=> 'Previzualizare',
    'processing'  => 'Se procesează…',

    'tc_label'        => 'Declar că am citit și acceptat Termenii și Condițiile.',
    'tc_details_label'=> 'Vezi mai mult',
    'tc_text'         => 'Sunt de acord cu Termenii Abonamentului și confirm comanda mea pentru un abonament lunar nelimitat la prețul de :fullPrice. Înțeleg că sunt eligibil pentru o ofertă promoțională de probă de :days zile la costul de doar :trialPrice. După perioada de probă, abonamentul meu se va reînnoi automat în fiecare lună și taxa de abonament va fi percepută pe același mijloc de plată folosit pentru oferta de probă. Pentru a anula abonamentul, apasă <a href=":cancelUrl">aici</a>. Autorizez sofortpdf.com să deducă taxa de abonament din mijlocul meu de plată în data aniversară lunară a abonamentului.',
    'tc_required'     => 'Trebuie să accepți termenii pentru a continua.',

    'bank_statement'  => ':name va apărea pe extrasul tău bancar.',
    'close_button'    => 'Închide',
    'files_count'     => '{n} fișiere în total',
    'show_preview'    => 'Arată previzualizarea',
    'hide_preview'    => 'Ascunde previzualizarea',
    'back_to_form'    => 'Înapoi la formular',
    'preview_title'   => 'Fișierul tău',

    'bottom_bar_text'  => 'Documentul tău este gata!',
    'bottom_bar_button'=> 'Descarcă acum',

    'err_card'               => 'Cardul tău a fost refuzat. Te rugăm să încerci alt card.',
    'err_already_subscribed' => 'Ai deja un abonament activ cu acest email. Te rugăm să te autentifici cu datele tale.',
    'err_generic'            => 'Ceva nu a mers bine. Te rugăm să încerci din nou.',
    'err_name'               => 'Te rugăm să introduci numele titularului cardului.',
    'err_email'              => 'Te rugăm să introduci o adresă de email validă.',

    'trial_label_short' => 'probă 2 zile',
];
```

- [ ] **Step 5: Create `resources/lang/ro/dashboard.php`**
```php
<?php
return [
    'nav_overview'    => 'Prezentare generală',
    'nav_downloads'   => 'Descărcări',
    'nav_subscription'=> 'Abonament',
    'nav_profile'     => 'Profil',

    'index_heading'    => 'Prezentare generală',
    'welcome_morning'  => 'Bună dimineața, :name',
    'welcome_afternoon'=> 'Bună ziua, :name',
    'welcome_evening'  => 'Bună seara, :name',
    'welcome_sub'      => 'Bine ai revenit în spațiul tău personal.',

    'stat_this_month' => 'Conversii în această lună',
    'stat_total'      => 'Total conversii',
    'stat_top_tool'   => 'Cel mai folosit instrument',
    'stat_none'       => 'Nicio activitate încă',

    'quick_access_heading' => 'Acces rapid',
    'quick_access_sub'     => 'Treci direct la un instrument.',

    'flash_no_active_subscription' => 'Nu s-a găsit niciun abonament activ.',
    'flash_subscription_canceled'  => 'Abonamentul tău a fost anulat. Vei păstra accesul până la sfârșitul perioadei de facturare curente.',
    'flash_profile_saved'          => 'Modificările tale au fost salvate.',

    'profile_val_name_required'     => 'Te rugăm să introduci numele tău.',
    'profile_val_email_required'    => 'Te rugăm să introduci adresa de email.',
    'profile_val_email_invalid'     => 'Te rugăm să introduci o adresă de email validă.',
    'profile_val_email_unique'      => 'Această adresă de email este deja folosită.',
    'profile_val_current_pw_required' => 'Te rugăm să introduci parola curentă.',
    'profile_val_current_pw_wrong'  => 'Parola ta curentă este incorectă.',
    'profile_val_password_min'      => 'Noua parolă trebuie să aibă cel puțin 8 caractere.',
    'profile_val_password_confirmed'=> 'Confirmarea parolei nu se potrivește.',

    'subscription_status' => 'Status abonament',
    'status_active'    => 'Activ',
    'status_trial'     => 'Probă',
    'status_canceled'  => 'Anulat',
    'status_none'      => 'Fără abonament',
    'next_payment_on'  => 'Următoarea plată pe :date',
    'trial_ends_on'    => 'Perioada de probă se încheie pe :date',
    'access_until'     => 'Acces până la :date',
    'subscribe_now'    => 'Abonează-te acum',
    'recent_conversions'=> 'Conversii recente',
    'view_all'         => 'Vezi toate',
    'col_date'         => 'Dată',
    'col_filename'     => 'Nume fișier',
    'conv_done'        => 'Gata',
    'conv_failed'      => 'Eșuat',
    'conv_processing'  => 'Se procesează',
    'no_conversions'   => 'Nicio conversie încă.',
    'try_tool_now'     => 'Încearcă un instrument acum',
    'date_format'      => 'd/m/Y H:i',
    'date_format_short'=> 'd/m/Y',

    'billing_heading'        => 'Abonament',
    'current_plan'           => 'Plan curent',
    'plan_active_desc'       => 'Abonamentul tău este activ.',
    'next_payment_amount'    => 'Următoarea plată pe :date: ',
    'plan_trial_label'       => 'Probă',
    'plan_trial_desc'        => 'Testezi sofortpdf Pro gratuit.',
    'your_trial_ends_on'     => 'Perioada ta de probă se încheie pe :date',
    'plan_canceled_label'    => 'Anulat',
    'plan_canceled_desc'     => 'Abonamentul tău a fost anulat.',
    'plan_none_title'        => 'Fără abonament',
    'plan_none_desc'         => 'Nu ai un abonament activ.',
    'management'             => 'Gestionare',
    'change_payment_method'  => 'Schimbă metoda de plată',
    'cancel_subscription'    => 'Anulează abonamentul',
    'cancel_confirm_title'   => 'Anulezi abonamentul?',
    'cancel_confirm_body'    => 'Ești sigur că vrei să anulezi abonamentul? Vei păstra accesul până la sfârșitul perioadei de facturare curente.',
    'go_back'                => 'Înapoi',
    'yes_cancel_subscription'=> 'Da, anulează abonamentul',

    'downloads_col_status'  => 'Status',
    'downloads_col_link'    => 'Link de descărcare',
    'downloads_download'    => 'Descarcă',
    'downloads_empty_title' => 'Nicio descărcare disponibilă',
    'downloads_empty_desc'  => 'Nu ai convertit niciun fișier încă.',

    'profile_heading'       => 'Profil',
    'personal_info'         => 'Informații personale',
    'label_name'            => 'Nume',
    'label_email'           => 'Adresă de email',
    'change_password'       => 'Schimbă parola',
    'change_password_hint'  => 'Lasă câmpurile goale dacă nu vrei să schimbi parola.',
    'current_password'      => 'Parola curentă',
    'new_password'          => 'Parolă nouă',
    'confirm_password'      => 'Confirmă parola',
    'save_changes'          => 'Salvează modificările',
];
```

- [ ] **Step 6: Create `resources/lang/ro/email.php`**
```php
<?php
return [
    'footer_tagline' => 'sofortpdf.com — instrumentele tale online pentru PDF',
    'footer_reason'  => 'Primești acest email deoarece ai un cont pe sofortpdf.com.',
    'footer_visit'   => 'Vizitează sofortpdf.com',

    'welcome_subject'               => 'Bun venit la sofortpdf.com',
    'trial_started_subject'         => 'Perioada ta de probă a început',
    'trial_ending_subject'          => 'Perioada ta de probă se încheie mâine',
    'subscription_active_subject'   => 'Abonamentul tău este activ',
    'subscription_canceled_subject' => 'Abonamentul tău a fost anulat',
    'payment_failed_subject'        => 'Plata a eșuat — acțiune necesară',
    'reset_subject'                 => 'Resetează parola — sofortpdf.com',

    'reset_heading'          => 'Bună :name,',
    'reset_intro'            => 'Primești acest email deoarece am primit o solicitare de resetare a parolei pentru contul tău de pe sofortpdf.com.',
    'reset_cta_intro'        => 'Apasă butonul de mai jos pentru a alege o nouă parolă:',
    'reset_cta'              => 'Resetează parola',
    'reset_expiry_notice'    => 'Acest link este valabil :minutes minute.',
    'reset_ignore_notice'    => 'Dacă nu ai solicitat resetarea parolei, poți ignora acest email. Parola ta va rămâne neschimbată.',
    'reset_plain_url_label'  => 'Dacă butonul nu funcționează, copiază acest link în browser:',

    'contact_autoreply_subject'        => 'Am primit mesajul tău — sofortpdf.com',
    'contact_autoreply_heading'        => 'Mulțumim, :name',
    'contact_autoreply_intro'          => 'Am primit mesajul tău și îți vom răspunde cât mai curând posibil — de obicei în cel mult o zi lucrătoare.',
    'contact_autoreply_your_message'   => 'Mesajul tău:',
    'contact_autoreply_body'           => 'Nu trebuie să răspunzi — te vom contacta la adresa de email de la care ai scris.',
    'contact_autoreply_signature_line1'=> 'Cu stimă,',
    'contact_autoreply_signature_line2'=> 'Echipa sofortpdf.com',

    'welcome_heading'        => 'Bun venit la sofortpdf.com, :name!',
    'welcome_intro'          => 'Mulțumim că te-ai înregistrat la sofortpdf.com. Contul tău a fost creat cu succes.',
    'welcome_credentials'    => 'Datele tale de autentificare:',
    'welcome_email_label'    => 'Email:',
    'welcome_password_label' => 'Parola ta este:',
    'welcome_password_notice'=> 'Te rugăm să îți schimbi parola după prima autentificare.',
    'welcome_tools_intro'    => 'Cu sofortpdf.com ai la dispoziție instrumente PDF puternice:',
    'welcome_tool_merge'     => 'Îmbinare și separare fișiere PDF',
    'welcome_tool_compress'  => 'Comprimare fișiere PDF',
    'welcome_tool_convert'   => 'Conversie PDF în alte formate',
    'welcome_tool_more'      => 'Și multe altele!',
    'welcome_cta_intro'      => 'Începe acum și descoperă toate funcționalitățile.',
    'welcome_cta'            => 'Autentifică-te acum',

    'trial_started_heading'          => 'Perioada ta de probă a început, :name!',
    'trial_started_intro'            => 'Perioada ta de probă gratuită la sofortpdf.com este acum activă. Ai <strong>:days zile</strong> de acces complet la toate funcțiile premium.',
    'trial_started_features_intro'   => 'Ce poți face chiar acum:',
    'trial_started_feature_1'        => 'Procesează fișiere PDF nelimitat',
    'trial_started_feature_2'        => 'Folosești toate instrumentele premium',
    'trial_started_feature_3'        => 'Convertești fișiere fără restricții',
    'trial_started_notice'           => '<strong>Notă:</strong> Perioada ta de probă se încheie pe <strong>:date</strong>. Ulterior, abonamentul tău va fi activat automat.',
    'trial_started_notice_no_date'   => '<strong>Notă:</strong> După încheierea perioadei de probă, abonamentul tău va fi activat automat.',
    'trial_started_cta'              => 'Descoperă instrumentele PDF',

    'trial_ending_heading' => 'Perioada ta de probă se încheie mâine, :name',
    'trial_ending_intro'   => 'Vrem să te anunțăm că perioada ta de probă gratuită la sofortpdf.com <strong>se încheie mâine</strong>.',
    'trial_ending_notice'  => '<strong>Ce urmează?</strong> După încheierea perioadei de probă, abonamentul tău va fi activat automat și va fi percepută prima plată regulată.',
    'trial_ending_body'    => 'Folosește timpul rămas pentru a testa toate funcțiile premium ale sofortpdf.com. Astfel te poți asigura că instrumentele noastre sunt exact ce ai nevoie.',
    'trial_ending_cta'     => 'Folosește instrumentele PDF acum',

    'subscription_active_heading'        => 'Abonamentul tău este acum activ, :name!',
    'subscription_active_intro'          => 'Mulțumim pentru încredere! Prima ta plată a fost procesată cu succes și abonamentul tău la sofortpdf.com este activ de acum.',
    'subscription_active_details_title'  => 'Detalii abonament:',
    'subscription_active_status'         => 'Status: Activ',
    'subscription_active_billing'        => 'Facturare: Lunar',
    'subscription_active_body'           => 'Ai acum acces nelimitat la toate instrumentele PDF premium. Bucură-te de toate funcțiile sofortpdf.com fără limite.',
    'subscription_active_cta'            => 'Mergi la instrumentele tale PDF',

    'subscription_canceled_heading'  => 'Abonamentul tău a fost anulat, :name',
    'subscription_canceled_intro'    => 'Confirmăm că abonamentul tău la sofortpdf.com a fost anulat.',
    'subscription_canceled_notice'   => '<strong>Notă:</strong> Vei continua să ai acces la toate funcțiile premium până la sfârșitul perioadei de facturare curente. După aceea, contul tău va reveni la planul gratuit.',
    'subscription_canceled_body'     => 'Ne pare rău că pleci. Dacă te răzgândești, îți poți reactiva abonamentul oricând.',
    'subscription_canceled_cta'      => 'Abonează-te din nou',
    'subscription_canceled_feedback' => 'Dacă ai feedback pentru noi, răspunde pur și simplu la acest email. Am dori să aflăm cum ne putem îmbunătăți.',

    'payment_failed_heading' => 'Plata a eșuat, :name',
    'payment_failed_intro'   => 'Din păcate, ultima ta plată pentru abonamentul la sofortpdf.com nu a putut fi procesată.',
    'payment_failed_notice'  => '<strong>Important:</strong> Te rugăm să îți actualizezi informațiile de plată cât mai curând pentru a evita o întrerupere a accesului.',
    'payment_failed_body'    => 'Poți actualiza ușor cardul de credit sau metoda de plată prin portalul nostru de facturare. Apasă butonul de mai jos.',
    'payment_failed_cta'     => 'Actualizează metoda de plată',
    'payment_failed_help'    => 'Dacă ai întrebări, răspunde la acest email. Vom fi bucuroși să te ajutăm.',

    'order_subject'       => 'Comanda ta la sofortpdf.com',
    'order_heading'       => 'Mulțumim pentru comanda ta, :name!',
    'order_intro'         => 'Am primit cu succes plata ta. Accesul tău la toate instrumentele sofortpdf este acum activ.',
    'order_details_title' => 'Detalii comandă:',
    'order_plan'          => 'sofortpdf Pro — Toate instrumentele PDF',
    'order_amount'        => 'Sumă: :amount',
    'order_number'        => 'Număr comandă: :number',
    'order_cancel_notice' => 'Poți anula abonamentul oricând din spațiul tău personal.',
    'order_cta'           => 'Mergi la instrumentele mele PDF',

    'download_ready_subject'               => 'Documentul tău este gata — sofortpdf.com',
    'download_ready_heading'               => 'Documentul tău este gata, :name!',
    'download_ready_intro'                 => 'Fișierul tău a fost procesat cu succes și este gata pentru descărcare.',
    'download_ready_service_pdf_to_word'   => 'Vrem să te anunțăm că fișierul tău a fost convertit cu succes! Poți descărca fișierul apăsând butonul de mai jos:',
    'download_ready_service_word_to_pdf'   => 'Vrem să te anunțăm că fișierul tău a fost convertit cu succes! Poți descărca fișierul apăsând butonul de mai jos:',
    'download_ready_service_pdf_to_excel'  => 'Vrem să te anunțăm că fișierul tău a fost convertit cu succes! Poți descărca fișierul apăsând butonul de mai jos:',
    'download_ready_service_excel_to_pdf'  => 'Vrem să te anunțăm că fișierul tău a fost convertit cu succes! Poți descărca fișierul apăsând butonul de mai jos:',
    'download_ready_service_pdf_to_jpg'    => 'Vrem să te anunțăm că fișierul tău a fost convertit cu succes! Poți descărca fișierul apăsând butonul de mai jos:',
    'download_ready_service_pdf_to_png'    => 'Vrem să te anunțăm că fișierul tău a fost convertit cu succes! Poți descărca fișierul apăsând butonul de mai jos:',
    'download_ready_service_compress'      => 'Vrem să te anunțăm că fișierul tău a fost comprimat cu succes! Poți descărca fișierul apăsând butonul de mai jos:',
    'download_ready_service_merge'         => 'Vrem să te anunțăm că fișierele tale au fost îmbinate cu succes! Poți descărca fișierul apăsând butonul de mai jos:',
    'download_ready_service_split'         => 'Vrem să te anunțăm că fișierul tău a fost separat cu succes! Poți descărca fișierul apăsând butonul de mai jos:',
    'download_ready_service_sign'          => 'Vrem să te anunțăm că documentul tău a fost semnat cu succes! Poți descărca documentul apăsând butonul de mai jos:',
    'download_ready_service_rotate'        => 'Vrem să te anunțăm că fișierul tău a fost rotat cu succes! Poți descărca fișierul apăsând butonul de mai jos:',
    'download_ready_service_convert'       => 'Vrem să te anunțăm că fișierul tău a fost convertit cu succes! Poți descărca fișierul apăsând butonul de mai jos:',
    'download_ready_file_label'    => 'Fișier:',
    'download_ready_cta'           => 'Descarcă acum',
    'download_ready_expiry_notice' => 'Acest link de descărcare este disponibil permanent în spațiul tău personal.',
];
```

- [ ] **Step 7: Create `resources/lang/ro/legal.php`**
```php
<?php
return [
    'agb_heading' => 'Termeni și Condiții',

    'agb_section_1_title' => 'Secțiunea 1 — Domeniu de aplicare',
    'agb_section_1_p1' => 'Acești Termeni și Condiții (denumiți în continuare „Termeni") se aplică tuturor contractelor încheiate între :company (denumit în continuare „Furnizor") și client (denumit în continuare „Utilizator") prin intermediul site-ului sofortpdf.com.',
    'agb_section_1_p2' => 'Acești Termeni se aplică exclusiv. Termenii divergenți ai Utilizatorului nu vor fi acceptați dacă Furnizorul nu îi agreează în mod expres în scris.',

    'agb_section_2_title' => 'Secțiunea 2 — Obiect',
    'agb_section_2_p1' => 'Furnizorul oferă instrumente online pentru editarea documentelor PDF prin intermediul site-ului sofortpdf.com (de exemplu, îmbinare, comprimare, conversie, editare, semnare). Accesul la aceste instrumente se face printr-un abonament plătit.',

    'agb_section_3_title' => 'Secțiunea 3 — Încheierea contractului',
    'agb_section_3_p1' => 'Contractul este încheiat la înregistrarea Utilizatorului pe site și achiziționarea unui abonament. Prin înregistrare, Utilizatorul acceptă acești Termeni.',
    'agb_section_3_p2' => 'Furnizorul va confirma primirea comenzii fără întârziere prin email. Această confirmare constituie acceptarea ofertei contractuale.',

    'agb_section_4_title' => 'Secțiunea 4 — Prețuri și plată',
    'agb_section_4_p1' => 'Toate prețurile afișate includ taxa pe valoarea adăugată aplicabilă. Plata se procesează prin furnizorul de servicii de plată Stripe. Utilizatorul autorizează Furnizorul să colecteze sumele convenite conform perioadei de facturare selectate (lunar sau anual).',
    'agb_section_4_p2' => 'În cazul neplății, Furnizorul are dreptul să suspende temporar accesul la servicii.',
    'agb_section_4_p3' => 'La :website oferim acces bazat pe abonament la toate instrumentele PDF. Abonamentul este în mod normal evaluat la :trial_marketing, dar în prezent oferim o perioadă promoțională de probă de :trial_days zile la prețul redus de :trial_price. După perioada de probă, abonamentul se reînnoiește automat lunar la :subscription_price pe lună. Plasând comanda, ești de acord că această sumă va fi dedusă automat din metoda ta de plată la sfârșitul perioadei de probă, cu excepția cazului în care anulezi în prealabil. Dacă nu dorești să continui, trebuie să anulezi înainte de sfârșitul perioadei de probă pentru a evita taxa recurentă. Plata pentru abonamentul tău va fi dedusă automat în data lunară aniversară și va apărea ca „:website" pe extrasul tău bancar.',
    'agb_section_4_p4' => 'Abonamentul are o durată de o lună și se reînnoiește automat la sfârșitul fiecărei perioade de facturare. Poți anula oricând prin pagina de anulare sau prin email — anulările intră în vigoare imediat și previn orice taxă ulterioară. Rambursările sunt disponibile dacă sunt solicitate în termen de 14 zile de la tranzacție; după această perioadă, nu se vor emite rambursări, cu excepția cazurilor prevăzute de lege.',

    'agb_section_5_title' => 'Secțiunea 5 — Dreptul de retragere',
    'agb_section_5_p1' => 'Consumatorii au dreptul de a se retrage din acest contract în termen de paisprezece zile fără a da nicio justificare. Perioada de retragere este de paisprezece zile de la data încheierii contractului.',
    'agb_section_5_p2' => 'Pentru a-ți exercita dreptul de retragere, trebuie să ne informezi (:company, :address) printr-o declarație clară (de exemplu, o scrisoare trimisă prin poștă sau un email la :email) cu privire la decizia ta de a te retrage din acest contract.',
    'agb_section_5_p3' => 'Pentru a respecta termenul de retragere, este suficient să trimiți comunicarea privind exercitarea dreptului de retragere înainte de expirarea perioadei de retragere.',
    'agb_section_5_consequences_title' => 'Consecințele retragerii',
    'agb_section_5_consequences_p1' => 'Dacă te retragi din acest contract, vom rambursa toate plățile primite de la tine fără întârziere nejustificată și cel târziu în termen de paisprezece zile de la data la care primim notificarea retragerii tale din acest contract.',

    'agb_section_5b_title' => 'Secțiunea 5a — Rambursări după perioada de retragere',
    'agb_section_5b_p1' => 'După expirarea perioadei de retragere de 14 zile, rambursările nu sunt în general disponibile. În circumstanțe excepționale justificate (de exemplu, erori tehnice care fac imposibilă utilizarea serviciului), Furnizorul poate, la discreția sa exclusivă, să acorde o rambursare totală sau parțială.',
    'agb_section_5b_p2' => 'Solicitările de rambursare pot fi trimise prin email la :email. Furnizorul va analiza fiecare solicitare individual și va comunica o decizie în termen de 14 zile lucrătoare.',
    'agb_section_5b_p3' => 'Anularea abonamentului este posibilă oricând și se poate face prin pagina de anulare sau prin email.',

    'agb_section_6_title' => 'Secțiunea 6 — Durată și anulare',
    'agb_section_6_p1' => 'Abonamentul se reînnoiește automat pentru perioada de facturare selectată, cu excepția cazului în care este anulat înainte de sfârșitul perioadei curente. Anularea se poate face oricând prin tabloul de bord al utilizatorului, pagina de anulare sau prin email.',

    'agb_section_7_title' => 'Secțiunea 7 — Disponibilitate și garanție',
    'agb_section_7_p1' => 'Furnizorul se străduiește să asigure disponibilitatea neîntreruptă a serviciilor. O disponibilitate de 100% nu poate fi garantată tehnic. Lucrările de întreținere vor fi anunțate în prealabil, când este posibil.',

    'agb_section_8_title' => 'Secțiunea 8 — Răspundere',
    'agb_section_8_p1' => 'Furnizorul este pe deplin răspunzător pentru intenție și neglijență gravă. În cazurile de neglijență ușoară, Furnizorul este răspunzător numai pentru încălcarea obligațiilor contractuale esențiale și limitat la daunele previzibile, tipice contractului.',
    'agb_section_8_p2' => 'Limitările de răspundere de mai sus nu se aplică daunelor rezultate din vătămarea vieții, corpului sau sănătății.',

    'agb_section_9_title' => 'Secțiunea 9 — Protecția datelor',
    'agb_section_9_p1_html' => 'Informații privind prelucrarea datelor cu caracter personal pot fi găsite în <a href=":url" class="text-blue-600 hover:underline">Politica noastră de confidențialitate</a>.',

    'agb_section_10_title' => 'Secțiunea 10 — Dispoziții finale',
    'agb_section_10_p1' => 'Acest contract este guvernat de :governing_law. În măsura permisă de lege, locul de jurisdicție va fi :jurisdiction.',
    'agb_section_10_p2' => 'Dacă anumite prevederi ale acestor Termeni sunt sau devin invalide, valabilitatea celorlalte prevederi nu va fi afectată.',

    'impressum_heading'               => 'Mențiuni legale',
    'impressum_tmg_title'             => 'Operatorul acestui site web',
    'impressum_contact_title'         => 'Contact',
    'impressum_contact_email_label'   => 'Email:',
    'impressum_contact_website_label' => 'Site web:',
    'impressum_vat_title'             => 'TVA / Cod fiscal',
    'impressum_registration_title'    => 'Număr de înregistrare în registrul comerțului',
    'impressum_dispute_title'         => 'Soluționarea litigiilor UE',
    'impressum_dispute_p1_html'       => 'Comisia Europeană oferă o platformă pentru soluționarea online a litigiilor (SOL): <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener" class="text-blue-600 hover:underline">https://ec.europa.eu/consumers/odr</a>.<br>Adresa noastră de email se găsește mai sus în mențiunile legale.',
    'impressum_dispute_p2'            => 'Nu suntem dispuși și nici obligați să participăm la proceduri de soluționare a litigiilor în fața unui consiliu de arbitraj pentru consumatori.',
    'impressum_liability_content_title'=> 'Răspundere pentru conținut',
    'impressum_liability_content_p1'  => 'Ca furnizor de servicii, suntem responsabili pentru propriul nostru conținut de pe aceste pagini în conformitate cu legislația generală. Cu toate acestea, nu suntem obligați să monitorizăm informațiile transmise sau stocate de terți sau să investigăm circumstanțe care indică activitate ilegală.',
    'impressum_liability_links_title' => 'Răspundere pentru linkuri',
    'impressum_liability_links_p1'    => 'Site-ul nostru conține linkuri către site-uri externe ale terților asupra conținutului cărora nu avem nicio influență. Prin urmare, nu putem prelua nicio responsabilitate pentru conținutul extern. Furnizorul sau operatorul respectiv al paginilor linkuite este întotdeauna responsabil pentru conținutul acestora.',
    'impressum_copyright_title'       => 'Drepturi de autor',
    'impressum_copyright_p1'          => 'Conținutul și lucrările create de :company pe aceste pagini sunt supuse legilor aplicabile privind drepturile de autor. Duplicarea, procesarea, distribuirea și orice formă de comercializare care depășesc domeniul de aplicare al legii drepturilor de autor necesită consimțământul scris al autorului sau creatorului respectiv.',

    'datenschutz_heading' => 'Politica de confidențialitate',

    'datenschutz_section_1_title' => '1. Responsabilul cu prelucrarea datelor',
    'datenschutz_section_1_intro' => 'Responsabilul cu prelucrarea datelor pe acest site web este:',
    'datenschutz_section_1_email_label' => 'Email:',

    'datenschutz_section_2_title'    => '2. Colectarea datelor pe site-ul nostru',
    'datenschutz_section_2a_title'   => 'a) Fișiere jurnal ale serverului',
    'datenschutz_section_2a_p1'      => 'Furnizorul de găzduire al acestor pagini colectează și stochează automat informații în fișiere jurnal ale serverului, pe care browser-ul tău le transmite automat. Acestea sunt:',
    'datenschutz_section_2a_li1'     => 'Tipul și versiunea browser-ului',
    'datenschutz_section_2a_li2'     => 'Sistemul de operare utilizat',
    'datenschutz_section_2a_li3'     => 'URL-ul referrer',
    'datenschutz_section_2a_li4'     => 'Numele de gazdă al computerului care accesează',
    'datenschutz_section_2a_li5'     => 'Ora solicitării serverului',
    'datenschutz_section_2a_li6'     => 'Adresa IP',
    'datenschutz_section_2a_p2'      => 'Aceste date nu sunt combinate cu alte surse de date. Colectarea se bazează pe Art. 6 alin. (1) lit. (f) GDPR.',
    'datenschutz_section_2b_title'   => 'b) Înregistrare și cont de utilizator',
    'datenschutz_section_2b_p1'      => 'La înregistrare, colectăm numele și adresa ta de email. Aceste date sunt prelucrate pentru furnizarea contului tău de utilizator și executarea contractului (Art. 6 alin. (1) lit. (b) GDPR).',

    'datenschutz_section_3_title' => '3. Cookie-uri',
    'datenschutz_section_3_p1'    => 'Site-ul nostru folosește cookie-uri. Acestea sunt fișiere text mici pe care browser-ul tău le stochează pe dispozitivul tău. Cookie-urile ne ajută să facem serviciile noastre mai ușor de utilizat și mai sigure.',
    'datenschutz_section_3_p2'    => 'Cookie-urile strict necesare sunt setate în baza Art. 6 alin. (1) lit. (f) GDPR. Avem un interes legitim în stocarea cookie-urilor pentru furnizarea tehnică fără erori și optimizată a serviciilor noastre.',

    'datenschutz_section_4_title' => '4. Procesarea plăților prin Stripe',
    'datenschutz_section_4_p1'    => 'Folosim serviciul Stripe (Stripe, Inc., 510 Townsend Street, San Francisco, CA 94103, SUA) pentru procesarea plăților. La plată, datele tale de plată sunt transmise direct către Stripe. Stripe prelucrează aceste date pentru a efectua plata.',
    'datenschutz_section_4_p2_html' => 'Prelucrarea datelor se bazează pe Art. 6 alin. (1) lit. (b) GDPR (executarea contractului). Pentru mai multe informații, consultați <a href="https://stripe.com/privacy" target="_blank" rel="noopener" class="text-blue-600 hover:underline">Politica de confidențialitate a Stripe</a>.',

    'datenschutz_section_5_title' => '5. Formularul de contact',
    'datenschutz_section_5_p1'    => 'Dacă ne contactezi prin formularul de contact sau email, datele tale vor fi stocate în scopul procesării solicitării tale și în caz de întrebări ulterioare. Nu vom partaja aceste date fără consimțământul tău. Prelucrarea se bazează pe Art. 6 alin. (1) lit. (b) GDPR.',

    'datenschutz_section_6_title' => '6. Procesarea fișierelor (instrumente PDF)',
    'datenschutz_section_6_p1'    => 'Fișierele încărcate sunt procesate exclusiv în scopul efectuării operațiunii solicitate (de exemplu, îmbinare, comprimare, conversie). După finalizare, fișierele sunt șterse automat de pe serverele noastre în termen de 24 de ore.',

    'datenschutz_section_7_title' => '7. Drepturile tale',
    'datenschutz_section_7_p1'    => 'Conform legii aplicabile, ai dreptul oricând la:',
    'datenschutz_section_7_li1'   => 'Informații gratuite despre datele personale stocate (Art. 15 GDPR)',
    'datenschutz_section_7_li2'   => 'Rectificarea datelor inexacte (Art. 16 GDPR)',
    'datenschutz_section_7_li3'   => 'Ștergerea datelor tale stocate (Art. 17 GDPR)',
    'datenschutz_section_7_li4'   => 'Restricționarea prelucrării datelor (Art. 18 GDPR)',
    'datenschutz_section_7_li5'   => 'Portabilitatea datelor (Art. 20 GDPR)',
    'datenschutz_section_7_li6'   => 'Obiecția la prelucrare (Art. 21 GDPR)',
    'datenschutz_section_7_p2'    => 'Ai de asemenea dreptul de a depune o plângere la o autoritate de supraveghere a protecției datelor cu privire la prelucrarea datelor tale personale.',

    'datenschutz_section_8_title' => '8. Modificări ale acestei politici de confidențialitate',
    'datenschutz_section_8_p1'    => 'Ne rezervăm dreptul de a actualiza această politică de confidențialitate pentru a asigura conformitatea cu cerințele legale actuale sau pentru a reflecta modificările serviciilor noastre.',

    'cookies_heading'          => 'Politica privind cookie-urile',
    'cookies_what_title'       => 'Ce sunt cookie-urile?',
    'cookies_what_p1'          => 'Cookie-urile sunt fișiere text mici stocate pe dispozitivul tău de site-uri web pentru a îmbunătăți experiența ta de navigare. Această politică explică ce cookie-uri folosim și de ce.',
    'cookies_types_title'      => 'Tipuri de cookie-uri',
    'cookies_necessary_title'  => 'Cookie-uri strict necesare',
    'cookies_necessary_p1'     => 'Aceste cookie-uri sunt esențiale pentru funcțiile de bază ale site-ului (de exemplu, autentificare, gestionarea sesiunilor, protecție CSRF). Nu pot fi dezactivate.',
    'cookies_analytics_title'  => 'Cookie-uri de analiză',
    'cookies_analytics_p1'     => 'Aceste cookie-uri ne ajută să înțelegem cum vizitatorii folosesc site-ul nostru. Datele sunt colectate anonim și sunt folosite exclusiv pentru îmbunătățirea serviciului nostru. Sunt setate numai cu consimțământul tău.',
    'cookies_marketing_title'  => 'Cookie-uri de marketing',
    'cookies_marketing_p1'     => 'Cookie-urile de marketing sunt folosite pentru a-ți afișa publicitate relevantă. Aceste cookie-uri sunt setate numai cu consimțământul tău explicit.',
    'cookies_tools_title'      => 'Servicii pe care le folosim',
    'cookies_ga4_p1'           => 'Folosim Google Analytics 4 (GA4) pentru a analiza comportamentul utilizatorilor. GA4 prelucrează datele anonim și le transferă pe serverele Google. Pentru mai multe informații, consultați politica de confidențialitate a Google.',
    'cookies_gtm_p1'           => 'Google Tag Manager (GTM) este utilizat pentru a gestiona etichetele noastre de urmărire. GTM în sine nu setează cookie-uri, dar permite încărcarea altor servicii care pot seta cookie-uri.',
    'cookies_stripe_p1'        => 'Stripe setează cookie-uri strict necesare pentru procesarea securizată a plăților. Aceste cookie-uri sunt necesare pentru funcționarea procesului de plată.',
    'cookies_cookiebot_p1'     => 'Cookiebot gestionează consimțământul tău privind cookie-urile. Stochează preferințele tale de cookie-uri astfel încât să nu fii întrebat din nou la fiecare vizită.',
    'cookies_manage_title'     => 'Gestionarea cookie-urilor',
    'cookies_manage_p1'        => 'Poți modifica setările de cookie-uri oricând prin bannerul de cookie-uri sau setările browser-ului. Apasă <a href="javascript:Cookiebot.renew()" class="text-blue-600 hover:underline">modifică setările de cookie-uri</a> pentru a-ți ajusta preferințele.',
    'cookies_changes_title'    => 'Modificări ale acestei politici',
    'cookies_changes_p1'       => 'Ne rezervăm dreptul de a actualiza această politică privind cookie-urile după cum este necesar. Versiunea actuală este disponibilă întotdeauna pe această pagină.',
];
```

- [ ] **Step 8: Create `resources/lang/ro/validation.php`**

Copy `resources/lang/en/validation.php` as the base and replace all English attribute labels in the `attributes` array at the bottom with Romanian equivalents:
```php
<?php
return [
    'accepted'             => 'Câmpul :attribute trebuie acceptat.',
    'accepted_if'          => 'Câmpul :attribute trebuie acceptat când :other este :value.',
    'active_url'           => 'Câmpul :attribute nu este un URL valid.',
    'after'                => 'Câmpul :attribute trebuie să fie o dată după :date.',
    'after_or_equal'       => 'Câmpul :attribute trebuie să fie o dată după sau egală cu :date.',
    'alpha'                => 'Câmpul :attribute poate conține doar litere.',
    'alpha_dash'           => 'Câmpul :attribute poate conține doar litere, cifre, liniuțe și underscore.',
    'alpha_num'            => 'Câmpul :attribute poate conține doar litere și cifre.',
    'array'                => 'Câmpul :attribute trebuie să fie un array.',
    'before'               => 'Câmpul :attribute trebuie să fie o dată înainte de :date.',
    'before_or_equal'      => 'Câmpul :attribute trebuie să fie o dată înainte de sau egală cu :date.',
    'between'              => [
        'numeric' => 'Câmpul :attribute trebuie să fie între :min și :max.',
        'file'    => 'Câmpul :attribute trebuie să fie între :min și :max kilobyți.',
        'string'  => 'Câmpul :attribute trebuie să fie între :min și :max caractere.',
        'array'   => 'Câmpul :attribute trebuie să aibă între :min și :max elemente.',
    ],
    'boolean'              => 'Câmpul :attribute trebuie să fie adevărat sau fals.',
    'confirmed'            => 'Confirmarea câmpului :attribute nu se potrivește.',
    'current_password'     => 'Parola este incorectă.',
    'date'                 => 'Câmpul :attribute nu este o dată validă.',
    'date_equals'          => 'Câmpul :attribute trebuie să fie o dată egală cu :date.',
    'date_format'          => 'Câmpul :attribute nu corespunde formatului :format.',
    'declined'             => 'Câmpul :attribute trebuie refuzat.',
    'declined_if'          => 'Câmpul :attribute trebuie refuzat când :other este :value.',
    'different'            => 'Câmpurile :attribute și :other trebuie să fie diferite.',
    'digits'               => 'Câmpul :attribute trebuie să aibă :digits cifre.',
    'digits_between'       => 'Câmpul :attribute trebuie să aibă între :min și :max cifre.',
    'dimensions'           => 'Câmpul :attribute are dimensiuni de imagine invalide.',
    'distinct'             => 'Câmpul :attribute are o valoare duplicată.',
    'email'                => 'Câmpul :attribute trebuie să fie o adresă de email validă.',
    'ends_with'            => 'Câmpul :attribute trebuie să se termine cu una din: :values.',
    'enum'                 => ':attribute selectat este invalid.',
    'exists'               => ':attribute selectat este invalid.',
    'file'                 => 'Câmpul :attribute trebuie să fie un fișier.',
    'filled'               => 'Câmpul :attribute trebuie să aibă o valoare.',
    'gt'                   => [
        'numeric' => 'Câmpul :attribute trebuie să fie mai mare decât :value.',
        'file'    => 'Câmpul :attribute trebuie să fie mai mare decât :value kilobyți.',
        'string'  => 'Câmpul :attribute trebuie să fie mai mare decât :value caractere.',
        'array'   => 'Câmpul :attribute trebuie să aibă mai mult de :value elemente.',
    ],
    'gte'                  => [
        'numeric' => 'Câmpul :attribute trebuie să fie mai mare sau egal cu :value.',
        'file'    => 'Câmpul :attribute trebuie să fie mai mare sau egal cu :value kilobyți.',
        'string'  => 'Câmpul :attribute trebuie să fie mai mare sau egal cu :value caractere.',
        'array'   => 'Câmpul :attribute trebuie să aibă :value elemente sau mai multe.',
    ],
    'image'                => 'Câmpul :attribute trebuie să fie o imagine.',
    'in'                   => ':attribute selectat este invalid.',
    'in_array'             => 'Câmpul :attribute nu există în :other.',
    'integer'              => 'Câmpul :attribute trebuie să fie un număr întreg.',
    'ip'                   => 'Câmpul :attribute trebuie să fie o adresă IP validă.',
    'ipv4'                 => 'Câmpul :attribute trebuie să fie o adresă IPv4 validă.',
    'ipv6'                 => 'Câmpul :attribute trebuie să fie o adresă IPv6 validă.',
    'json'                 => 'Câmpul :attribute trebuie să fie un șir JSON valid.',
    'lt'                   => [
        'numeric' => 'Câmpul :attribute trebuie să fie mai mic decât :value.',
        'file'    => 'Câmpul :attribute trebuie să fie mai mic decât :value kilobyți.',
        'string'  => 'Câmpul :attribute trebuie să fie mai mic decât :value caractere.',
        'array'   => 'Câmpul :attribute trebuie să aibă mai puțin de :value elemente.',
    ],
    'lte'                  => [
        'numeric' => 'Câmpul :attribute trebuie să fie mai mic sau egal cu :value.',
        'file'    => 'Câmpul :attribute trebuie să fie mai mic sau egal cu :value kilobyți.',
        'string'  => 'Câmpul :attribute trebuie să fie mai mic sau egal cu :value caractere.',
        'array'   => 'Câmpul :attribute trebuie să aibă cel mult :value elemente.',
    ],
    'mac_address'          => 'Câmpul :attribute trebuie să fie o adresă MAC validă.',
    'max'                  => [
        'numeric' => 'Câmpul :attribute nu poate fi mai mare decât :max.',
        'file'    => 'Câmpul :attribute nu poate fi mai mare decât :max kilobyți.',
        'string'  => 'Câmpul :attribute nu poate fi mai mare decât :max caractere.',
        'array'   => 'Câmpul :attribute nu poate avea mai mult de :max elemente.',
    ],
    'mimes'                => 'Câmpul :attribute trebuie să fie un fișier de tip: :values.',
    'mimetypes'            => 'Câmpul :attribute trebuie să fie un fișier de tip: :values.',
    'min'                  => [
        'numeric' => 'Câmpul :attribute trebuie să fie cel puțin :min.',
        'file'    => 'Câmpul :attribute trebuie să fie cel puțin :min kilobyți.',
        'string'  => 'Câmpul :attribute trebuie să fie cel puțin :min caractere.',
        'array'   => 'Câmpul :attribute trebuie să aibă cel puțin :min elemente.',
    ],
    'multiple_of'          => 'Câmpul :attribute trebuie să fie un multiplu de :value.',
    'not_in'               => ':attribute selectat este invalid.',
    'not_regex'            => 'Formatul câmpului :attribute este invalid.',
    'numeric'              => 'Câmpul :attribute trebuie să fie un număr.',
    'password'             => [
        'letters'       => 'Câmpul :attribute trebuie să conțină cel puțin o literă.',
        'mixed'         => 'Câmpul :attribute trebuie să conțină cel puțin o majusculă și o literă mică.',
        'numbers'       => 'Câmpul :attribute trebuie să conțină cel puțin un număr.',
        'symbols'       => 'Câmpul :attribute trebuie să conțină cel puțin un simbol.',
        'uncompromised' => 'Câmpul :attribute a apărut într-o scurgere de date. Te rugăm să alegi un alt :attribute.',
    ],
    'present'              => 'Câmpul :attribute trebuie să fie prezent.',
    'prohibited'           => 'Câmpul :attribute este interzis.',
    'prohibited_if'        => 'Câmpul :attribute este interzis când :other este :value.',
    'prohibited_unless'    => 'Câmpul :attribute este interzis dacă :other nu este în :values.',
    'prohibits'            => 'Câmpul :attribute interzice prezența :other.',
    'regex'                => 'Formatul câmpului :attribute este invalid.',
    'required'             => 'Câmpul :attribute este obligatoriu.',
    'required_array_keys'  => 'Câmpul :attribute trebuie să conțină intrări pentru: :values.',
    'required_if'          => 'Câmpul :attribute este obligatoriu când :other este :value.',
    'required_unless'      => 'Câmpul :attribute este obligatoriu dacă :other nu este în :values.',
    'required_with'        => 'Câmpul :attribute este obligatoriu când :values este prezent.',
    'required_with_all'    => 'Câmpul :attribute este obligatoriu când :values sunt prezente.',
    'required_without'     => 'Câmpul :attribute este obligatoriu când :values nu este prezent.',
    'required_without_all' => 'Câmpul :attribute este obligatoriu când niciuna din :values nu este prezentă.',
    'same'                 => 'Câmpurile :attribute și :other trebuie să se potrivească.',
    'size'                 => [
        'numeric' => 'Câmpul :attribute trebuie să fie :size.',
        'file'    => 'Câmpul :attribute trebuie să fie :size kilobyți.',
        'string'  => 'Câmpul :attribute trebuie să fie :size caractere.',
        'array'   => 'Câmpul :attribute trebuie să conțină :size elemente.',
    ],
    'starts_with'          => 'Câmpul :attribute trebuie să înceapă cu una din: :values.',
    'string'               => 'Câmpul :attribute trebuie să fie un șir de caractere.',
    'timezone'             => 'Câmpul :attribute trebuie să fie un fus orar valid.',
    'unique'               => 'Câmpul :attribute a fost deja luat.',
    'uploaded'             => 'Câmpul :attribute nu a putut fi încărcat.',
    'url'                  => 'Câmpul :attribute trebuie să fie un URL valid.',
    'uuid'                 => 'Câmpul :attribute trebuie să fie un UUID valid.',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'mesaj-personalizat',
        ],
    ],

    'attributes' => [],
];
```

- [ ] **Step 9: Verify all translations load**
```bash
php artisan config:clear && php artisan tinker --execute="app()->setLocale('ro'); echo __('home.meta_title') . ' | ' . __('tool.done') . ' | ' . __('legal.agb_heading');"
```
Expected: `Instrumente PDF Online — Rapid și Sigur | Gata! | Termeni și Condiții`

- [ ] **Step 10: Full smoke check**
```bash
php artisan view:clear && php artisan config:cache
for u in https://sofortpdf.com/ro/pdf-to-word https://sofortpdf.com/ro/merge-pdf https://sofortpdf.com/ro https://sofortpdf.com/hu/pdf-to-word https://sofortpdf.com/cs/pdf-to-word https://sofortpdf.com/pl/pdf-to-word https://sofortpdf.com/de/pdf-zu-word https://sofortpdf.com/en/pdf-to-word; do
  echo "$(curl -so/dev/null -w%{http_code} $u)  $u"
done
```
Expected: all 200.

- [ ] **Step 11: Commit**
```bash
git add resources/lang/ro/
git commit -m "feat(ro): add Romanian translations — all 20 language files"
```

---

### Final: Deploy

- [ ] Deploy to production:
```bash
ssh sofortpdf 'cd /var/www/sofortpdf && sudo git pull && sudo php artisan view:clear && sudo php artisan config:clear && sudo php artisan config:cache'
```

- [ ] Smoke check all locales including `/ro`:
```bash
for u in https://sofortpdf.com/ro/pdf-to-word https://sofortpdf.com/hu/pdf-to-word https://sofortpdf.com/cs/pdf-to-word https://sofortpdf.com/pl/pdf-to-word https://sofortpdf.com/de/pdf-zu-word https://sofortpdf.com/en/pdf-to-word; do
  echo "$(curl -so/dev/null -w%{http_code} $u)  $u"
done
```

- [ ] Verify `/ro` appears in sitemap: `curl -s https://sofortpdf.com/sitemap.xml | grep -c '/ro/'`
- [ ] Verify language switcher shows 🇷🇴 Română: check `https://sofortpdf.com/ro` in browser.
- [ ] Save deploy memory entry.
