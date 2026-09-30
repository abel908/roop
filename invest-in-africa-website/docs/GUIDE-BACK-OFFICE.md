# Guide d'utilisation du back-office — User guide

The Invest In Africa Initiative · cahier des charges §14.6 « Guide d'utilisation — manuel du back-office en français et en anglais »

- [Français](#français)
- [English](#english)

---

## Français

### 1. Connexion et sécurité

- Adresse : `https://[domaine]/admin`.
- Mot de passe : 12 caractères minimum, avec majuscules, minuscules, chiffres et symboles.
- **Double authentification** : menu en haut à droite › *Profil* › activer l'authentification par application (Google Authenticator, Microsoft Authenticator, 1Password…). Conservez les **codes de secours** affichés.
- Après 5 tentatives échouées, le compte est verrouillé 15 minutes.
- La langue de l'interface (français ou anglais) se règle dans la fiche de chaque utilisateur.

### 2. Tableau de bord

Il affiche les nouveaux dossiers, manifestations d'intérêt et messages, les projets publiés et les projets dont une traduction manque. Chaque carte est cliquable. La cloche en haut de l'écran signale chaque nouvelle demande.

### 3. Traiter les demandes (menu *Demandes*)

**Dossiers de projets** (formulaire *Submit a Project*)
1. Ouvrez le dossier : coordonnées du porteur, description, montant, documents.
2. Les documents s'ouvrent par un **lien temporaire de 15 minutes** ; chaque consultation est enregistrée dans le journal.
3. Bouton **Traiter** : changez le statut (Reçu → En cours d'examen → Complément demandé → Retenu → Publié / Non retenu → Archivé), attribuez un chargé de dossier, ajoutez des notes internes. Cochez *Informer le porteur par email* pour lui envoyer automatiquement la mise à jour dans sa langue.
4. Bouton **Convertir en fiche projet** : crée une fiche projet en brouillon à partir du dossier ; complétez les traductions puis publiez-la.

**Manifestations d'intérêt** et **Messages de contact** : même principe (statut Nouveau → En cours → Traité, notes internes).

**Exports** : bouton *Exporter CSV / Excel* (tous les résultats filtrés) ou sélection de lignes › *Exporter*.

**Droit à l'effacement** : bouton *Anonymiser* sur la fiche d'une demande (irréversible). Les demandes traitées sont aussi anonymisées automatiquement après les durées définies dans *Paramètres › Conservation des données*.

### 4. Projets (menu *Opportunités*)

- Onglet **Contenu EN · FR · 中文** : les trois langues côte à côte. L'anglais est obligatoire ; une mention *Traduction manquante* signale les langues à compléter. Une fiche non traduite n'est pas affichée dans la langue concernée (le visiteur est renvoyé vers le catalogue avec un message).
- Onglet **Investissement** : pays, secteur, domaine, stade, montant, devise, type de financement. La référence (P-AAAA-NNN) est générée automatiquement.
- Onglet **Visuels et documents** : image principale et galerie (converties automatiquement en AVIF/WebP), synthèse publique en PDF, dossier complet confidentiel (jamais publié).
- Onglet **Publication** : statut (Ouvert, En cours de financement, Financé, Clos), *Publié*, *Mis en avant* (accueil, 3 à 6 projets), date de publication (une date future programme la publication).
- **Secteurs** : liste administrable utilisée par les filtres et le formulaire.
- Bouton **Historique** : restaurer une version antérieure.

### 5. Contenus (menu *Contenus*)

- **Contenus institutionnels** : chiffres clés animés, récits d'impact et témoignages, frise historique, dirigeants.
- **Pages** : créez des pages à partir de **blocs validés** (en-tête, texte, image et texte, chiffres clés, cartes, citation, vidéo, questions-réponses, documents, domaines, opportunités, appel à l'action). Statut *Brouillon* / *Publiée*, date de publication programmée, bouton **Voir** pour prévisualiser chaque langue avant publication, **Historique** pour restaurer une version.
- **Menus** : navigation de l'en-tête et du pied de page — rubriques du site, mega-menus, pages créées ou liens externes, libellés par langue, ordre par glisser-déposer.
- **What We Do — domaines** : textes des six domaines et URL (slug) par langue.
- **Contenus et traductions** : **tous les textes** du site, des formulaires et des emails, en trois colonnes. Un champ vide conserve le texte par défaut (affiché en gris) ; un champ rempli le remplace immédiatement. Filtre par page et recherche par mot.
- **Partenaires** : logo (SVG ou PNG HD, couleurs d'origine), description trilingue, catégorie, lien, *Mis en avant sur l'accueil*, ordre par glisser-déposer.
- **Documents** : guides et brochures téléchargeables, rattachés à un domaine.
- **Médiathèque** : images, vidéos et documents avec texte alternatif par langue.

### 6. Référencement

- **Métadonnées** : title (50–60 caractères), meta description (150–160 caractères) et image de partage par page et par langue.
- **Redirections** : ancienne URL → nouvelle URL (301).

### 7. Administration (Super Admin / Administrateur)

- **Utilisateurs** : création des comptes, rôle, langue d'interface, activation. Seul un Super Admin peut créer un Super Admin.
- **Rôles et droits** (Super Admin) : modules accessibles à chaque rôle.
- **Journal d'activité** : connexions, échecs, modifications, consultations de documents, exports.
- **Paramètres** (Super Admin) : coordonnées officielles, carte (latitude/longitude), réseaux sociaux, destinataires des notifications **par type de demande**, conservation des données, vidéo et image du hero.

---

## English

### 1. Sign-in and security

- Address: `https://[domain]/admin`.
- Passwords: at least 12 characters with upper and lower case letters, digits and symbols.
- **Two-factor authentication**: top-right menu › *Profile* › enable app authentication; keep the **recovery codes**.
- After 5 failed attempts the account is locked for 15 minutes.
- The interface language (English or French) is set on each user's record.

### 2. Dashboard

New project files, expressions of interest and messages, published projects and projects missing a translation. The bell icon notifies every new request.

### 3. Handling requests (*Requests* menu)

- **Project files**: open the file; documents open through **15-minute signed links**, every access is logged. *Process*: change the status, assign an officer, add internal notes, optionally email the holder. *Convert to project sheet* creates a draft project.
- **Expressions of interest** and **Contact messages**: status New → In progress → Closed, internal notes.
- **Exports**: *Export CSV / Excel* (filtered results) or bulk selection.
- **Right to erasure**: *Anonymise* on a request (irreversible); processed requests are also anonymised automatically after the periods set in *Settings › Data retention*.

### 4. Projects (*Opportunities* menu)

Content in EN / FR / 中文 side by side (English required), investment data, visuals (converted automatically to AVIF/WebP), public PDF summary, confidential full file, status, *Published*, *Featured* on the home page, scheduled publication date, **History** to restore a previous version.

### 5. Content

- **Institutional content**: key figures, impact stories, timeline, leadership.
- **Pages**: build pages from **validated blocks**; draft / published, scheduled date, **Preview** in each language, **History**.
- **Menus**: header and footer navigation per language.
- **What We Do — areas**, **Contents & translations** (every text of the site, forms and emails; empty = default text), **Partners**, **Documents**, **Media library** (alternative texts per language).

### 6. SEO

**Metadata** per page and per language; **Redirects** (301).

### 7. Administration

**Users**, **Roles and permissions** (Super Admin), **Activity log**, **Settings** (official contact details, map, social networks, notification recipients per request type, data retention, hero video and image).
