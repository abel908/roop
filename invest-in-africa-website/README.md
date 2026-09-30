# The Invest In Africa Initiative — Site institutionnel premium

Site trilingue **English · Français · 中文** réalisé selon le cahier des charges
`CDC-WEB-2026-01` (v1.0, septembre 2026).

- Front : Laravel 13 · Blade · Tailwind CSS 4 · Alpine.js · Vite — design entièrement sur mesure, sans thème ni kit UI.
- Back-office : Filament 5 (`/admin`) — rôles, 2FA, journal d'activité, contenus EN/FR/中文 côte à côte.
- Base de données : MySQL 8 / MariaDB 10.11+ en production, SQLite en local.

---

## Mise en ligne : une seule commande

Sur un serveur Linux avec Docker, le nom de domaine pointant vers ce serveur :

```bash
git clone <dépôt> && cd <dépôt>/invest-in-africa-website
./start.sh invest-in-africa.org contact@invest-in-africa.org
```

Tout est automatique : secrets générés (`.env.docker`), certificat HTTPS Let's Encrypt obtenu et renouvelé,
base MySQL, Redis, file d'attente (emails, images), planificateur (sauvegardes chiffrées, conservation des données,
IndexNow), antivirus ClamAV, migrations, données de référence, premier Super Admin (identifiants affichés à la fin),
textes éditables, variantes AVIF/WebP, caches. Relancer `./start.sh` met le site à jour sans perdre aucun contenu.
Avec les secrets `DEPLOY_*` du dépôt GitHub, chaque push sur `main` validé par les tests est déployé automatiquement
(`.github/workflows/website-deploy.yml`).

Réglages facultatifs dans `.env.docker` : compte SMTP d'envoi des emails (`MAIL_*`, sinon ils sont journalisés),
`ANTHROPIC_API_KEY` (traduction automatique des langues manquantes), `GA4_MEASUREMENT_ID`.

## Développement local

```bash
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate && touch database/database.sqlite
php artisan site:install      # base, données, Super Admin (identifiants dans storage/app/private/first-admin.txt)
php artisan serve             # http://localhost:8000  —  back-office : /admin
```

---

## Correspondance avec le cahier des charges

| § | Exigence | Mise en œuvre |
|---|----------|---------------|
| 3.1 | Arborescence officielle | `routes/web.php` — URLs `/en/`, `/fr/`, `/zh/`, slugs FR traduits, slugs ZH latins (`lang/*/routes.php`) |
| 3.2 | En-tête fixe, mega-menu, menu mobile plein écran | `resources/views/partials/header.blade.php` |
| 3.3–3.4 | Pied de page, pages légales, confirmations, 404 trilingue | `partials/footer`, `pages/legal`, `pages/confirmation`, `errors/404` |
| 4.2–4.3 | Palette officielle, contrastes | `resources/css/app.css` (`--color-brand-*`, vert fonctionnel `#08703A` pour le petit texte) |
| 4.4 | Logo officiel uniquement | `public/images/logo-invest-in-africa.png`, jamais retouché ; plaque blanche sur fond noir |
| 4.5 | Montserrat / Inter / Noto Sans SC auto-hébergées | `@fontsource/*` compilées par Vite ; Noto Sans SC chargée uniquement en chinois, découpée par plages Unicode |
| 4.6 | Pictogrammes sur mesure, motif des rayons | `components/domain-icon.blade.php`, `components/rays.blade.php`, filet tricolore |
| 4.7 | Apparitions 200–400 ms, compteurs, transitions, « réduire les animations » | `app.css` (`.reveal`, `@view-transition`, `prefers-reduced-motion`), `app.js` |
| 5.1 | Accueil en 10 sections | `pages/home.blade.php` (carte interactive de l'Afrique incluse) |
| 5.2–5.6 | About, Mission, What We Do + 6 pages domaine, Partners, Contact | `pages/*.blade.php` ; domaines administrables (`DomainSeeder`) |
| 6.2 | Catalogue, filtres combinables dans l'URL, fiche projet, intérêt pré-rempli | `ProjectController`, `projects/*.blade.php`, barre d'action mobile |
| 6.3 | Formulaire en 3 étapes, récapitulatif, upload sécurisé | `submissions/create.blade.php`, `resources/js/submission-form.js`, `StoreSubmissionRequest` |
| 6.4 | Workflow des dossiers | `SubmissionStatus` + back-office (statut, attribution, notes, conversion en fiche projet) |
| 6.5 | Emails transactionnels dans la langue du visiteur | `Mail/BrandedMail`, `Services/Notifier`, gabarit `emails/branded.blade.php`, textes éditables |
| 7 | Multilingue natif, hreflang, x-default, sitemap par langue, sélecteur sans drapeaux, choix mémorisé, aucune redirection IP | `Support/Locales`, `Support/Seo`, `SetLocale`, `SeoController` |
| 7.6 | Aucune dépendance Google pour fonctionner | polices locales, carte OSM/Leaflet, anti-spam sans reCAPTCHA, GA4 non bloquant, avatars admin locaux |
| 8.1 | Pages par blocs validés, prévisualisation, brouillons, publication programmée | `Filament/Support/PageBlocks`, `CmsPageController`, `resources/views/blocks/*` |
| 8.1 | Historique des versions et restauration | trait `HasRevisions`, action *Historique* (pages, projets, domaines, partenaires, textes) |
| 7.2 / 8.1 | Menus administrables par langue | `MenuItem`, ressource *Menus* |
| 8.1 / 10.4 | Médiathèque, textes alternatifs par langue, conversion AVIF/WebP, `srcset` | `Media`, `Services/ImageOptimizer`, composant `x-picture`, `php artisan media:optimize` |
| 8 | CMS : modules, rôles paramétrables, 2FA, exports CSV/Excel, notifications, journal | `app/Filament/**`, `Enums/Role` + page *Rôles et droits* |
| 10 | Titles/metas éditables, canonical, OG, JSON-LD, robots, 301 | `Support/Seo`, ressources *Métadonnées* et *Redirections* |
| 10.5 | Événements GA4 après consentement | `resources/js/analytics.js`, attributs `data-track` ; conversions enregistrées sur la page de confirmation |
| 11.1 | CSRF, CSP, HSTS, en-têtes, rate limiting, uploads privés, liens signés, antivirus | `SecurityHeaders`, `ProtectAgainstSpam`, `DocumentScanner`, `DocumentDownloadController`, `Support/Html` |
| 6.3 / 11.1 | Captcha compatible Chine (auto-hébergé, adaptatif), verrouillage après échecs | `Support/Captcha`, `Filament/Auth/Login` |
| 11.1 | Sauvegardes quotidiennes chiffrées AES-256, 30 jours | `Services/Backup`, `php artisan site:backup` (planifiée) |
| 11.2 | Durées de conservation, anonymisation (droit à l'effacement) | `Services/DataRetention`, `php artisan data:purge` (planifiée), action *Anonymiser* |
| 5.1–5.3 | Récits d'impact, études de cas, témoignages | *Contenus institutionnels*, `partials/stories` |
| 12 | Mobile-first, zones tactiles 44 px, WCAG 2.2 AA | lien d'évitement, focus visible, labels et erreurs associés, `lang` par version |

### Back-office (`/admin`)

| Menu | Contenu |
|------|---------|
| Demandes | Dossiers de projets (pièces jointes via liens signés 15 min, chaque accès journalisé), manifestations d'intérêt, messages |
| Opportunités | Projets (EN/FR/中文 côte à côte, statut de traduction, mise en avant, publication programmée), secteurs |
| Contenus | Contenus institutionnels (chiffres clés, récits d'impact, frise, dirigeants), **Pages par blocs**, **Menus**, domaines, **Contenus et traductions** (tous les textes du site et des emails), partenaires, documents, **Médiathèque** |
| Référencement | Métadonnées par page et par langue, redirections 301 |
| Administration | Utilisateurs, **rôles et droits**, journal d'activité, paramètres (coordonnées, réseaux, destinataires par sujet, conservation des données, vidéo du hero) |

Interface en français ou en anglais selon l'utilisateur ; double authentification par application (TOTP) avec codes de secours.

---

## Éléments « À valider par The Invest In Africa Initiative »

Aucun contenu institutionnel n'a été inventé. Les éléments suivants sont vides ou fournis comme
**propositions éditoriales** à valider, et se remplissent depuis le back-office :

- Coordonnées officielles, réseaux sociaux, localisation (carte affichée dès que latitude/longitude sont saisies).
- Historique, dirigeants, chiffres d'impact (les chiffres affichés par défaut — 54 pays, 6 domaines, 2 parcours, 3 langues — sont tirés du cahier des charges).
- Liste et logos des partenaires (la section d'accueil invite à devenir partenaire tant qu'elle est vide).
- Textes juridiques (mentions légales, confidentialité, cookies, avertissement) : trames structurées marquées « à valider ».
- Accroche du hero, vision, mission, valeurs, descriptions des 6 domaines, intitulés FR et 中文 : propositions rédigées à partir du cahier des charges. **Les intitulés anglais des rubriques et des domaines sont repris à l'identique.**
- Traduction chinoise : à faire relire par un traducteur natif spécialisé (§7.5).
- Logo vectoriel (SVG/AI/PDF) et éventuelle version pour fond sombre (§4.4) — le fichier PNG 298 × 214 transmis est utilisé tel quel.
- Photographies et vidéo institutionnelles : sans visuel, le hero affiche le motif graphique des rayons du logo.

---

## Automatisations

| Besoin | Automatisme |
|--------|-------------|
| Langues manquantes | Traduction par Claude après chaque enregistrement (projets, pages, domaines, partenaires, textes, paramètres) ; rien n'est écrasé ; journalisé pour relecture ; désactivable dans *Paramètres* |
| Référencement technique | Sitemaps par langue avec hreflang, robots.txt, balises de vérification (Google, Bing, Baidu, Yandex) saisies dans *Paramètres*, notification IndexNow à chaque publication et chaque semaine |
| Images | Conversion AVIF / WebP en plusieurs largeurs à l'import |
| Sauvegardes | Archive chiffrée chaque nuit, 30 jours (mot de passe généré ; `php artisan site:backup --show-password`) |
| Données personnelles | Anonymisation automatique après les durées de conservation |
| Pages légales | Textes complets, alimentés par les paramètres (coordonnées, hébergeur, durées de conservation) |
| Déploiement | `./start.sh` ou déploiement GitHub automatique ; migrations et mises à jour non destructives |

## Documentation livrée (§14.6)

- [Guide d'utilisation du back-office (FR / EN)](docs/GUIDE-BACK-OFFICE.md)
- [Documentation technique : architecture, installation, déploiement, maintenance](docs/DOCUMENTATION-TECHNIQUE.md)
- Script de déploiement réversible : `./deploy.sh origin/main` (retour arrière : `./deploy.sh <tag>`)

## Mise en production (§9.4–9.5)

- PHP 8.3+ (extensions `intl`, `gd`, `pdo_mysql`, `redis`), Nginx + PHP-FPM, HTTPS, MySQL/MariaDB, Redis (`CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`).
- `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, `SEED_DEMO=false` ; la préproduction reste non indexée automatiquement (`X-Robots-Tag` + `robots.txt`).
- File d'attente (emails, images) : `php artisan queue:work` (Supervisor) ; service d'envoi authentifié SPF/DKIM/DMARC.
- Planificateur : cron `* * * * * php artisan schedule:run` (sauvegarde chiffrée 02:30, purge RGPD 03:30) ; définir `BACKUP_PASSWORD`.
- Optimisation : `php artisan optimize` (config, routes, vues) après chaque déploiement ; `php artisan content:sync` pour enregistrer les nouveaux textes.
- Antivirus : installer ClamAV et définir `ANTIVIRUS_BINARY=clamdscan`.
- CDN (Cloudflare ou équivalent) avec points de présence en Asie ; compression Brotli/Gzip côté serveur.
- Sauvegardes quotidiennes chiffrées de la base et de `storage/app` (documents privés), conservées 30 jours.
