# sofortpdf — operating context

PDF conversion SaaS targeting EU (HU, CZ, PL, DE primary; EN global).
Stack: Laravel 8.75 (PHP 8), MySQL, Tailwind CSS, vanilla JS in the
modal. Deployed to a single AWS-region nginx + php-fpm box.

---

## Working rules (read first, every time)

→ **think before coding**
state your assumptions, ask when unsure. never guess.

→ **simplicity first**
write the minimum code that solves the problem. no abstractions
nobody asked for.

→ **surgical changes**
don't touch code unrelated to the request. every changed line must
trace back to what was asked.

→ **goal-driven execution**
turn vague instructions into verifiable success criteria before
writing a single line.

---

## Deploy workflow (NEVER edit on the server)

1. Edit locally.
2. `git add` only the files relevant to the change. Never `git add .`
   (untracked CSVs and ad assets live in the repo root).
3. `git commit` + `git push origin main`.
4. Pull on server + clear caches:
   ```
   ssh sofortpdf 'cd /var/www/sofortpdf && sudo git pull \
     && sudo php artisan view:clear \
     && sudo php artisan config:clear'
   ```
5. Add `route:clear && route:cache` only when routes/web.php changed.
6. Smoke check all 5 locales:
   ```
   for u in https://sofortpdf.com/{hu,cs,pl,en}/pdf-to-word https://sofortpdf.com/de/pdf-zu-word; do
     echo "$(curl -so/dev/null -w%{http_code} $u)  $u"
   done
   ```

**`.env` is NOT in git.** To change it, ssh in and edit directly,
then `config:clear`. Confirm the change with `grep ^KEY .env`.

---

## CSS build

Mix is configured (`webpack.mix.js`) but **never used** — laravel-mix 6
breaks on Node 22 with a ProgressPlugin schema mismatch. The actual
build runs Tailwind CLI directly:

```
npx tailwindcss -i ./resources/css/app.css -o ./public/css/app.css --minify
# or: npm run build:css
```

`public/css/app.css` IS committed (~49KB minified). No npm step on
the server — the deploy pulls the prebuilt file. Cache-busted via
`filemtime()` query in `layouts/app.blade.php`.

If a new dynamic class appears in a blade that wasn't scanned
before, run `npm run build:css` locally, commit `public/css/app.css`,
deploy. The `content` array in `tailwind.config.js` already scans
`resources/views/**`, `resources/js/**`, `app/**`, `config/tools*.php`.

---

## Where things live

| File | What |
|---|---|
| `app/Http/Controllers/Api/PaymentController.php` | 3-step modal flow (create-customer → pay-trial → create-subscription). Has the email-already-subscribed pre-flight and idempotency guards on steps 2 + 3. |
| `app/Http/Middleware/ResolveVad.php` | Per-request VAD resolution. Tier 1 looks up `bo_payment_routes` by IP, Tier 2 looks up `customers` + their payments, Tier 3 calls `VadRouter`. Sets `cameFromAds`, country code, shares pricing/company with views. |
| `app/Services/Payment/StripeService.php` | Resolves Stripe account, products, pricing from the VAD route. Has lazy-VAD fallback + active-rule check for stale session VADs. |
| `app/Services/Payment/VadRouter.php` | Weighted, country-segmented selection from `bo_vad_rules`. |
| `app/Services/Payment/Gateways/StripeGateway.php` | Forwards each payment step to the BO. Has retry-once on Stripe `lock_timeout`. |
| `app/Http/Controllers/WebhookController.php` | Proxies Stripe webhooks to the BO. Returns 503 on BO failure so Stripe re-delivers. |
| `app/Models/Customer.php` | `hasSofortpdfSubscription()` = any payment with `payment_status_id=2`. |
| `app/Services/PaywallBypass.php` | Env-based bypass (`PAYMENT_BYPASS`, `PAYMENT_BYPASS_IPS`). Off in prod. |
| `resources/views/partials/payment-modal.blade.php` | The whole modal: HTML + CSS + JS in one ~1240-line file. Stripe Elements, local-currency price display, "buried" T&C copy, `localizeError()` mapping `card_declined` and `already_subscribed` codes. |
| `resources/views/layouts/app.blade.php` | Header, footer, language switcher, GTM, Cookiebot, lucide icons. |
| `routes/web.php` | Root geo-redirect (IP → locale), `{locale}` prefix group (`de\|en\|hu\|cs\|pl`), API endpoints. |
| `config/locales.php` | Per-locale tool slugs, titles, aliases, auth slugs, legal slugs. |
| `config/tools_{en,de,hu,cs,pl}.php` | Per-tool h1/h2/description/action_label per locale. |
| `config/company.php` | Company profiles (AVOCODE=1, KIWIKODE=2, JACKCODE=3). |

---

## Payment architecture (the complicated part)

Sofortpdf does **not** talk to Stripe directly. All Stripe calls go
through a shared back-office at `https://avocode-bo.online`:

```
JS modal → /api/payment/create-customer    → StripeGateway → BO → Stripe
        → /api/payment/pay-trial           → ...
        → /api/payment/create-subscription → ...
```

Webhooks: `POST /stripe/webhook` (AVOCODE account) and
`POST /stripe/webhook-jack` (JACKCODE account). The controller is a
fine proxy — BO does signature verification, customer resolution,
subscription updates, lifecycle emails.

Each visitor gets routed to a **VAD** (Avocode SRL legal entity vs
JackCode FZCO vs others) based on `bo_vad_rules` filtered by country
segment + weight. The VAD points at a `bo_stripe_account`, which has
the actual Stripe API keys. Multiple Stripe accounts per website are
supported — that's why `getStripeAccount()` must NOT fall back to
"first by PK", which silently selects JACKCODE when AVOCODE was
intended.

**Current VAD state for sofortpdf (website_id=6):**
- Rule 21 (`bo_vad_id=30`, AVOCODE Romania) — **active** ✅
- Rule 20 (`bo_vad_id=29`, JACKCODE Dubai) — inactive since 2026-05-07
- Existing JACKCODE subscriptions stay on that account forever
  (Stripe doesn't move customers). Only NEW charges go to AVOCODE.

**Idempotency contract (post-Lenka incident on 2026-05-15):**
- `createCustomer`: pre-flight 409 if email already has active sub
  (returns `error.code = already_subscribed`).
- `payTrial`: returns cached `{success: true, paymentIntent: null,
  duplicate: true}` if same customer has a status=2 payment within
  the last 30 minutes.
- `createSubscription`: same idea but additionally requires
  `last_four_digit` populated (= step 3 already ran for this trial).

---

## Database & external services

**Shared MySQL `avocode` DB** (`13.49.227.149`, user `sofortpdf`,
password in server `.env` as `DB_PASSWORD`):
- `customers`, `payments`, `subscriptions` — shared across all
  brands. Scoped by `website_id` (= 6 for sofortpdf).
- `bo_websites` — sofortpdf has TWO rows (id=18 JACKCODE, id=19
  AVOCODE), both `bo_websites.website_id=6`.
- `bo_vads`, `bo_vad_rules`, `bo_vad_products`, `bo_stripe_accounts`,
  `bo_stripe_products`, `bo_stripe_customers`, `bo_payment_routes`.

**Editable projects:** only `saas/sofortpdf` and
`saas/conversion-service`. Everything else under `saas/*`
(`conversie-pdf`, `convierte-pdf`, `contract-kit`, `device-finder`,
`avocode-bo`, …) is read-only reference — useful for pattern lookups
but never edit.

**SSH aliases** (`~/.ssh/config`):
- `sofortpdf` — prod app server (nginx, php-fpm, /var/www/sofortpdf)
- `conversion-service` — Docker host (`pdf-service-app` container)
- `avocode-db` — DB host (rarely needed; prefer mysql client through
  the app server)
- `avocode-bo` — BO server (read-only)

**Stripe accounts:** AVOCODE = `avocode.srl@gmail.com`, JACKCODE =
`jackcode.fzco@gmail.com`. Logging in to the dashboards requires
those mailboxes.

---

## Logging conventions

- `.env` has `LOG_LEVEL=error` — `Log::info()` and `Log::warning()`
  do NOT reach `laravel.log`. Don't trust laravel.log for routine
  signals; it surfaces errors only.
- Real funnel telemetry lives in `storage/logs/activity-YYYY-MM-DD.log`
  via the `activity` channel (`Log::channel('activity')->info(...)`).
- `client_event` entries there: `file_uploaded`,
  `payment_modal_opened`, `payment_attempted`, `webhook_received`.
- nginx access log: `/var/log/nginx/access.log` (need `sudo`).
  Useful response sizes: `pay-trial 1505` = success, `357` = error.

To temporarily bump visibility, set `LOG_LEVEL=warning` in `.env` —
catches lock_timeout retries and idempotency guard fires.

---

## Common pitfalls

1. **MySQL shell expansion.** When inserting a bcrypt hash via
   `mysql -e "..."`, bash eats `$2y$10$` as variable refs and
   silently truncates the hash. Always use stdin/heredoc:
   ```
   ssh sofortpdf "mysql -h ... avocode" <<SQL
     UPDATE customers SET password = '\$2y\$10\$...' WHERE ...;
   SQL
   ```

2. **Session-cached VAD survives DB changes.** A user's session
   cookie carries `bo_payment_route_id` from a previous visit. If
   you flip a `bo_vad_rules.active`, returning visitors keep paying
   the old VAD until session expires. `getStripeAccount()` already
   validates the cached VAD against `active=1` and forgets stale
   ones — but `bo_payment_routes` rows for an IP also need cleaning
   to fully reset a specific IP (`DELETE FROM bo_payment_routes
   WHERE ip = '…' AND bo_website_id IN (18,19)`).

3. **PRESERVE session VAD on login.** `Auth::login()` regenerates
   the session and would wipe `vad.*` keys without the snapshot/
   restore pattern in `createCustomer`. Don't refactor that away.

4. **No TrustProxies configured.** Sofortpdf serves nginx directly,
   no Cloudflare in front. If a CDN is added later, both
   `TrustProxies::$proxies` AND the `filter_var` private-IP check
   in `ResolveVad::detectCountry()` need attention.

5. **The BO is not idempotent.** Every call to pay-trial creates a
   new Stripe charge; every call to create-subscription creates a
   new Stripe subscription. That's why the server-side guards in
   `PaymentController` are non-negotiable.

6. **`payment_status_id` is the source of truth.** `=2` means
   "active/trialing", `=3` means "terminated", `=4` means
   "pending/failed". Anything else = inactive. Webhooks may take
   minutes to land — be patient.

7. **localStorage / browser cache of the HTML landing.** Returning
   visitors hit the API without re-rendering the landing first
   (`ResolveVad` middleware never runs). Lazy VAD resolution inside
   `getStripeAccount()` covers this case — don't remove it.

8. **Stripe webhook IPs.** `54.187.*` and `3.18.*` ranges are
   Stripe — don't filter them out when grepping for "real" traffic.

---

## Useful queries / one-liners

**Daily funnel snapshot:**
```
ssh sofortpdf 'cd /var/www/sofortpdf/storage/logs && for d in $(date -u -d "6 days ago" +%Y-%m-%d) ... ; do
  u=$(grep -hc file_uploaded activity-$d.log 2>/dev/null)
  m=$(grep -hc payment_modal_opened activity-$d.log 2>/dev/null)
  a=$(grep -hc payment_attempted activity-$d.log 2>/dev/null)
  echo "$d  u=$u m=$m a=$a"
done'
```

**Recent conversions:**
```sql
SELECT p.id, c.email, c.ip, v.name AS vad, p.bo_website_id, p.create_time
FROM payments p
LEFT JOIN customers c ON c.id = p.customer_id
LEFT JOIN bo_vads v ON v.id = p.bo_vad_id
WHERE p.bo_website_id IN (18, 19)
  AND p.create_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)
  AND p.payment_status_id = 2
ORDER BY p.create_time DESC;
```

**Active VAD rules for sofortpdf:**
```sql
SELECT r.id, r.bo_website_id, r.segment, r.currency, v.name AS vad, r.weight, r.priority, r.active
FROM bo_vad_rules r LEFT JOIN bo_vads v ON v.id = r.bo_vad_id
WHERE r.website_id = 6 ORDER BY r.active DESC, r.priority DESC;
```

**Guard fires (post-Lenka):**
```
ssh sofortpdf 'grep -hc "duplicate detected" /var/www/sofortpdf/storage/logs/laravel.log;
              grep -hc "email already has active subscription" /var/www/sofortpdf/storage/logs/laravel.log'
```

**Webhook AVOCODE vs JACKCODE today:**
```
ssh sofortpdf 'grep webhook_received /var/www/sofortpdf/storage/logs/activity-$(date -u +%Y-%m-%d).log | grep -oE "\"jack\":[a-z]+" | sort | uniq -c'
```

---

## Live testing

`agent-browser` is installed and can drive a real browser session:
```
npx agent-browser --session foo open https://sofortpdf.com/hu/pdf-to-word
npx agent-browser --session foo snapshot -i
npx agent-browser --session foo eval 'window.SofortpdfPaymentModal.open({file: new File(["x"],"t.pdf"), filename:"t.pdf"})'
npx agent-browser --session foo screenshot /tmp/out.png
npx agent-browser --session foo close
```

For unit smoke / sanity, the local dev server can't reach the prod
DB — `php artisan view:cache` is enough to catch blade syntax errors.

---

## Memory of recent incidents (don't repeat them)

- **2026-05-07 AVOCODE cutover.** Switched all new payments from
  JACKCODE to AVOCODE. Required a `getStripeAccount()` rewrite that
  no longer trusts `->first()` ordering, plus a stale-session-VAD
  invalidation. Pre-existing JACKCODE subs stay forever.
- **2026-05-15 Lenka triple-pay.** A real user paid the trial 3
  times in 4 minutes (re-opened the modal, no UX signal of success).
  Triggered the idempotency guards + the email-already-subscribed
  pre-flight. Manual cleanup needed in Stripe (cancel extras,
  refund duplicates).
- **2026-05-21 BO webhook timeout.** A single cURL timeout to the
  BO silently dropped a Stripe event because we used to always
  return 200. Now we return 503 on BO failure → Stripe retries.

---

## Test login

Internal test account with paywall bypass via a fake active
payment row (no real Stripe customer behind it, `is_test=1`):
- Email: `cris@sofortpdf.com`
- Password: stored in your password manager (last regenerated
  2026-05-15). If lost, regenerate via the SQL helper used at the
  time (see git log for `password_hash` insert pattern).

Use this account for any visual / paywall test instead of issuing
real €0.69 charges.
