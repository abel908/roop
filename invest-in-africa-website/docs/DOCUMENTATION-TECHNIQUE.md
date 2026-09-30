# Documentation technique

Architecture, installation, déploiement et maintenance — cahier des charges §9, §14.6.

## 1. Architecture

| Couche | Technologie | Emplacement |
|--------|-------------|-------------|
| Back-end | Laravel 13, PHP 8.3+ | `app/`, `routes/`, `config/site.php` |
| Front-end | Blade, Tailwind CSS 4, Alpine.js, Vite | `resources/views`, `resources/css`, `resources/js` |
| Back-office | Filament 5 | `app/Filament`, `app/Providers/Filament/AdminPanelProvider.php` |
| Base de données | MySQL 8 / MariaDB 10.11+ (SQLite en local) | `database/migrations` |
| Cache et files | Redis (production) | `.env` : `CACHE_STORE`, `QUEUE_CONNECTION` |
| Stockage | disque `public` (médias publics), disque `local` privé (dossiers confidentiels) | `storage/app/public`, `storage/app/private` |

### Modules (§9.3)

| Module | Principaux fichiers |
|--------|---------------------|
| Core | `PageController`, `CmsPageController`, `Page`, `MenuItem`, `Setting`, blocs `resources/views/blocks` |
| I18n | `Support/Locales`, `Http/Middleware/SetLocale`, `Support/DatabaseTranslationLoader`, `lang/{en,fr,zh}` |
| Projects | `ProjectController`, `Project`, `Sector`, `projects/*.blade.php` |
| Submissions | `SubmissionController`, `StoreSubmissionRequest`, `Submission`, `SubmissionDocument`, `DocumentScanner` |
| Interest | `InterestController`, `InterestExpression` |
| Partners | `Partner`, `pages/partners.blade.php` |
| Contact | `ContactController`, `ContactMessage` |
| Media | `Media`, `Services/ImageOptimizer`, `Jobs/OptimizeImage`, composant `x-picture` |
| SEO | `Support/Seo`, `SeoController`, `SeoMeta`, `Redirect` |
| Users & Roles | `User`, `Enums/Role`, `Filament/Concerns/HasModulePermission`, `Filament/Auth/Login` |
| Notifications | `Services/Notifier`, `Mail/BrandedMail`, `resources/views/emails` |

### Multilingue

- Routes enregistrées une fois par langue (`routes/web.php`) avec les slugs de `lang/{locale}/routes.php`.
- Contenus traduisibles stockés en JSON `{"en","fr","zh"}` (trait `HasTranslations`) ; repli sur l'anglais à l'affichage, jamais de traduction automatique.
- Textes d'interface dans `lang/*.php`, surchargeables en base (`content_translations`) par le back-office. `php artisan content:sync` enregistre les nouvelles clés.
- `hreflang`, `x-default`, canonical, Open Graph et JSON-LD générés par `Support/Seo` et `layouts/site.blade.php`.

### Sécurité (§11)

- En-têtes : `Http/Middleware/SecurityHeaders` (CSP avec nonce, HSTS en HTTPS, X-Frame-Options, Referrer-Policy, Permissions-Policy).
- Formulaires : CSRF, validation serveur (`app/Http/Requests`), limitation `throttle:forms` (5/min, 40/jour par IP), champ piège + délai minimal + captcha auto-hébergé adaptatif (`ProtectAgainstSpam`, `Support/Captcha`).
- Uploads : liste blanche d'extensions et de types MIME, 5 fichiers × 10 Mo, renommage UUID, disque privé, antivirus ClamAV (`ANTIVIRUS_BINARY`), téléchargement par lien signé temporaire avec journalisation.
- HTML saisi dans le CMS filtré par `Support/Html` (liste blanche).
- Back-office : 2FA TOTP, verrouillage après 5 échecs (15 min), rôles et droits par module, journal d'activité.
- Données personnelles : anonymisation manuelle et automatique (`Services/DataRetention`, `php artisan data:purge`).

## 2. Installation locale

```bash
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
ADMIN_EMAIL=vous@domaine.org ADMIN_PASSWORD='MotDePasse-Solide-2026' php artisan migrate --seed
php artisan storage:link && php artisan content:sync
composer run dev   # ou : php artisan serve + npm run dev + php artisan queue:work
```

Tests : `php artisan test` · Style PSR-12 : `./vendor/bin/pint`.

## 3. Production

### Méthode recommandée : Docker (tout automatique)

`./start.sh mon-domaine.org [email]` construit l'image (`docker/Dockerfile`, FrankenPHP = Caddy + PHP avec HTTPS automatique) et démarre `docker-compose.yml` : `app` (web), `worker` (file d'attente), `scheduler` (tâches planifiées), `mysql`, `redis`, `clamav`. Au démarrage, `docker/entrypoint.sh` génère la clé d'application si besoin, attend la base puis exécute `php artisan site:install` (idempotent). Les volumes `storage`, `mysql` et `caddy_data` conservent fichiers, base et certificats.

### Installation manuelle (sans Docker)

### Serveur

- Linux, Nginx, PHP-FPM 8.3+ (`intl`, `gd` avec WebP/AVIF, `pdo_mysql`, `redis`, `zip`), MySQL/MariaDB, Redis, certificat TLS.
- Racine web : `public/`. Compression Brotli/Gzip, cache HTTP long sur `/build/` et `/storage/`.
- CDN (Cloudflare ou équivalent) avec points de présence en Asie.

### `.env` minimal

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://[domaine]
SESSION_SECURE_COOKIE=true
DB_CONNECTION=mysql …
CACHE_STORE=redis
QUEUE_CONNECTION=redis
MAIL_MAILER=smtp …            # service authentifié SPF / DKIM / DMARC
MAIL_TEAM_ADDRESS=…
BACKUP_PASSWORD=…             # obligatoire
ANTIVIRUS_BINARY=clamdscan
GA4_MEASUREMENT_ID=…
SEED_DEMO=false
```

### Processus

- **File d'attente** (emails, optimisation d'images) : `php artisan queue:work --tries=3` sous Supervisor.
- **Planificateur** (cron) : `* * * * * cd /chemin && php artisan schedule:run` — sauvegarde chiffrée à 02:30, purge RGPD à 03:30, nettoyage hebdomadaire des jobs échoués.

### Déploiement

`./deploy.sh origin/main` : mode maintenance, mise à jour du code, dépendances, build, migrations, `content:sync`, caches, redémarrage des workers.
Retour arrière : `./deploy.sh v1.0.2` (tag précédent). La préproduction (`APP_ENV=staging`) n'est jamais indexée.

## 4. Maintenance

| Opération | Commande |
|-----------|----------|
| Sauvegarde manuelle (archive AES-256, 30 jours glissants) | `php artisan site:backup` |
| Restauration | Déchiffrer l'archive (`7z x backup-….zip`), réimporter `database/*.sql`, recopier `storage/app` |
| Purge des données personnelles | `php artisan data:purge` |
| Régénérer les variantes AVIF/WebP | `php artisan media:optimize [--force]` |
| Enregistrer les nouveaux textes éditables | `php artisan content:sync` |
| Vider les caches | `php artisan optimize:clear` |
| Mise à jour des dépendances | `composer update && npm update`, puis tests en préproduction |

Copier les archives de sauvegarde hors du serveur (stockage objet S3 ou second serveur) et tester une restauration avant la mise en ligne (§11.1).
