# Romanian locale (ro) — sofortpdf

**Date:** 2026-07-01  
**Status:** Approved

## Goal

Add Romanian as the 6th supported locale on sofortpdf.com. Romanian visitors (`RO` country code) get redirected to `/ro/` instead of `/en/`. All pages, tools, legal, auth, and email flows render in Romanian.

## Scope

### Files to create

| File | What |
|---|---|
| `config/tools_ro.php` | 27 tools × 5 fields (h1, h2, description, meta_description, action_label) in Romanian. Mirrors `tools_en.php`. |
| `resources/lang/ro/*.php` | 20 translation files (same keys as `lang/hu/`). |

### Files to modify

| File | Change |
|---|---|
| `config/locales.php` | Add `ro` to: `supported`, `tool_slugs` (same as EN), `tool_titles` (Romanian), `aliases` (empty), `auth_slugs` (same as EN), `legal_slugs` (same as EN), `contact_slugs` (`contact`), `cancellation_slugs` (`cancel`). |
| `routes/web.php` | Regex `de\|en\|hu\|cs\|pl` → `de\|en\|hu\|cs\|pl\|ro`; add `'RO' => 'ro'` to geo-redirect map; add `ro` to sitemap with priority `1.0`. |
| `resources/views/partials/seo.blade.php` | Add `'ro' => 'ro_RO'` to `$ogLocaleMap`; add Romanian org description to `$orgDescriptionMap`; add `'ro' => 'ro'` to `$hreflangTags`. |
| `resources/views/layouts/app.blade.php` | Add `'ro' => ['flag' => '🇷🇴', 'label' => 'Română']` to language switcher `$localeMeta`. |

### Explicitly out of scope

- No new blade views (legal/auth reuse EN slugs, same pattern as HU/CS/PL)
- No CSS rebuild (no new dynamic Tailwind classes)
- No payment routing changes (VAD/Stripe unchanged)

## Slug strategy

Tool slugs for RO are identical to EN slugs (e.g. `pdf-to-word`, `merge-pdf`). Same decision as HU, CS, PL — avoids translation overhead on URLs.

Auth slugs: `login`, `logout`, `password-reset` (same as EN).  
Legal slugs: `imprint`, `privacy`, `terms`, `cookie-policy` (same as EN).  
Contact slug: `contact`. Cancellation slug: `cancel`.

## How the locale is wired

1. Visitor from Romania hits `/` → geo-redirect sends to `/ro`
2. `SetLocale` middleware validates `ro` against `config('locales.supported')`, sets `App::setLocale('ro')`
3. Laravel loads `resources/lang/ro/*.php`
4. `config('tools')` is loaded from `config/tools_ro.php` by the `ToolController` (already locale-aware)
5. hreflang and og:locale populated from `seo.blade.php` maps

## Romanian tool titles (locales.php tool_titles.ro)

| key | title |
|---|---|
| merge | Îmbinare PDF |
| compress | Comprimă PDF |
| image-to-pdf | Imagine la PDF |
| jpg-to-pdf | JPG la PDF |
| pdf-to-word | PDF la Word |
| word-to-pdf | Word la PDF |
| pdf-to-jpg | PDF la JPG |
| split | Desparte PDF |
| edit | Editează PDF |
| sign | Semnează PDF |
| pdf-to-excel | PDF la Excel |
| excel-to-pdf | Excel la PDF |
| rotate | Rotește PDF |
| protect | Protejează PDF |
| unlock | Deblochează PDF |
| watermark | Adaugă filigran |
| page-numbers | Adaugă numere de pagini |
| pdf-to-ppt | PDF la PowerPoint |
| ppt-to-pdf | PowerPoint la PDF |
| pdf-to-png | PDF la PNG |
| png-to-pdf | PNG la PDF |
| ocr | Recunoaștere text (OCR) |
| remove-pages | Eliminare pagini |
| extract-pages | Extragere pagini |
| html-to-pdf | HTML la PDF |
| optimize | Optimizare PDF |

## Success criteria

- `curl -s -o/dev/null -w%{http_code} https://sofortpdf.com/ro/pdf-to-word` → 200
- All 5 smoke-check locales still 200 post-deploy
- `/ro` appears in sitemap.xml
- Language switcher shows 🇷🇴 Română and links to `/ro/{current-slug}`
- hreflang includes `<link rel="alternate" hreflang="ro" href="...">`
- Romanian IP geo-redirects to `/ro`
