# sofortpdf.com

Deutschsprachige SaaS-Webanwendung fuer Online-PDF-Werkzeuge (zusammenfuegen, komprimieren, konvertieren, bearbeiten, unterzeichnen). Gebaut mit Laravel 8, Tailwind CSS und Stripe fuer die Zahlungsabwicklung.

## Voraussetzungen

- PHP >= 7.4 (empfohlen 8.0+)
- Composer
- Node.js >= 14 und npm
- Apache 2.4+ mit `mod_rewrite`
- MySQL / MariaDB (gemeinsame Datenbank, siehe Hinweis unten)
- Stripe-Konto fuer die Zahlungsabwicklung

## Installation

```bash
# 1. Repository klonen
git clone git@github.com:DEIN-ORG/sofortpdf.git
cd sofortpdf

# 2. PHP-Abhaengigkeiten installieren
composer install --no-dev --optimize-autoloader

# 3. Umgebungsdatei erstellen
cp .env.example .env

# 4. Anwendungsschluessel generieren
php artisan key:generate

# 5. Frontend-Assets bauen
npm install
npm run production
```

## Umgebungsvariablen (.env)

Wichtige Eintraege, die angepasst werden muessen:

```
APP_NAME=sofortpdf
APP_URL=https://sofortpdf.com

DB_HOST=127.0.0.1
DB_DATABASE=sofortpdf
DB_USERNAME=xxx
DB_PASSWORD=xxx

STRIPE_KEY=pk_live_xxx
STRIPE_SECRET=sk_live_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
```

## Datenbank -- WICHTIGER HINWEIS

Dieses Projekt verwendet eine **gemeinsame Datenbank** (Shared Database). Es werden **KEINE Migrationen** ausgefuehrt. Die Datenbankstruktur wird ausserhalb dieses Projekts verwaltet.

```bash
# NICHT ausfuehren:
# php artisan migrate
```

## Apache Virtual-Host Konfiguration

```apache
<VirtualHost *:443>
    ServerName sofortpdf.com
    DocumentRoot /var/www/sofortpdf/public

    <Directory /var/www/sofortpdf/public>
        AllowOverride All
        Require all granted
    </Directory>

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/sofortpdf.com.crt
    SSLCertificateKeyFile /etc/ssl/private/sofortpdf.com.key
</VirtualHost>
```

Sicherstellen, dass `mod_rewrite` aktiviert ist:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## Queue Worker

Die Anwendung nutzt Queues fuer Hintergrundaufgaben (z. B. Dateikonvertierung, E-Mail-Versand). Der Queue Worker muss dauerhaft laufen:

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

Fuer den Produktivbetrieb empfiehlt sich Supervisor:

```ini
[program:sofortpdf-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sofortpdf/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/sofortpdf/storage/logs/worker.log
stopwaitsecs=3600
```

## Geplante Aufgaben (Crontab)

Laravel benoetigt einen einzelnen Cron-Eintrag fuer den Task Scheduler:

```
* * * * * cd /var/www/sofortpdf && php artisan schedule:run >> /dev/null 2>&1
```

## Feature Flags

Die Anwendung unterstuetzt Feature Flags zur schrittweisen Aktivierung neuer Funktionen. Feature Flags werden ueber die Datenbank oder Umgebungsvariablen gesteuert.

| Flag | Beschreibung |
|------|-------------|
| `FEATURE_PDF_SIGN` | PDF-Unterzeichnung aktivieren/deaktivieren |
| `FEATURE_PDF_EDIT` | PDF-Bearbeitung aktivieren/deaktivieren |
| `FEATURE_EXCEL_CONVERT` | Excel-Konvertierung aktivieren/deaktivieren |

Feature Flags koennen in der `.env`-Datei gesetzt werden:

```
FEATURE_PDF_SIGN=true
FEATURE_PDF_EDIT=true
FEATURE_EXCEL_CONVERT=false
```

In Blade-Views pruefen:

```blade
@if(config('features.pdf_sign'))
    {{-- PDF-Unterzeichnung anzeigen --}}
@endif
```

## Projektstruktur (wichtige Verzeichnisse)

```
app/Http/Controllers/     -- Controller fuer alle Routen
app/Http/Middleware/       -- Middleware (z. B. Abo-Pruefung)
resources/views/           -- Blade-Templates
resources/views/legal/     -- Impressum, Datenschutz, AGB
routes/web.php             -- Alle Web-Routen
```

## Verfuegbare Werkzeuge

| Slug | Werkzeug |
|------|----------|
| /pdf-zusammenfuegen | PDF zusammenfuegen |
| /pdf-komprimieren | PDF komprimieren |
| /jpg-zu-pdf | JPG zu PDF |
| /pdf-zu-word | PDF zu Word |
| /word-zu-pdf | Word zu PDF |
| /pdf-zu-jpg | PDF zu JPG |
| /pdf-verkleinern | PDF verkleinern (Alias) |
| /pdf-bearbeiten | PDF bearbeiten |
| /pdf-trennen | PDF trennen |
| /pdf-in-excel | PDF in Excel |
| /excel-zu-pdf | Excel zu PDF |
| /pdf-unterzeichnen | PDF unterzeichnen |

## Lizenz

Proprietaer. Alle Rechte vorbehalten.
