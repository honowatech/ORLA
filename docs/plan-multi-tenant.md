# Plan de transformation d'ORLA en plateforme SaaS multi-entreprises de gestion de livraison

## 0. Contexte

**Ce qu'est le projet aujourd'hui.** ORLA (marque historique « Speedex ») est une application Laravel 12 / PHP 8.2 (Laravel UI, thème Vuexy jQuery sous `public/app-assets`, MySQL, déploiement FTP sur hébergement mutualisé cPanel) qui gère l'activité d'une entreprise de livraison : agents/dispatch, coursiers, véhicules, villes/zones/quartiers et grille tarifaire par paire de zones, clients (particuliers et entreprises), boutiques, produits/stock, commandes avec cycle de vie `attente → attribue → encours → livre/annulee/echoue`, encaissements, journal d'activité.

**Pourquoi le changement.** Le README annonce un SaaS multi-tenant, mais le code est **mono-entreprise par déploiement** :

- La table `super_admin_client` n'est pas une table d'entreprises clientes mais une **licence d'instance** : le middleware `app/Http/Middleware/Check_Sa_Client_Error.php` charge toutes les lignes et laisse passer si *au moins une* est active avec un abonnement valide, sans aucun lien avec l'utilisateur connecté.
- Aucune des 20 tables opérationnelles (`users`, `commandes`, `coursiers`, `clients`, `ville`, `zone`, `quartier`, `boutiques`, `produits`, `stock`, `paiement`, `vehicule`, `activity`, …) ne porte de colonne d'appartenance à une entreprise. Toutes les listes sont globales (`Coursiers::where('statut',1)->get()`, `Commandes::where('statut','livre')->get()`, `Ville::get()`…).
- Le « Super Admin » plateforme (table `super_admin`) se connecte par une session ad hoc (`session('SuperAdmin_infos')`, `check_superadmin()` copié dans chaque méthode) sans guard Laravel ; le « Super Admin » de `type_utilisateur` (id 1) est en réalité l'administrateur de l'espace de travail.
- Le flux de paiement Monetbil est public (`Sc-transaction.*` sans authentification), suppose un seul client (`Client::where('statut',1)->first()`), passe les identifiants par `$_SESSION`, continue après une signature invalide et permet une activation gratuite via `methode=application`.
- « Speedex » est **une donnée** : quartier et boutique sentinelles créés à la volée (`Admin/CommandesController.php:50-55`, `Admin/ClientsController.php:185-194`), valeurs d'ENUM `commandes.mode_de_paiement` et `paiement.qui_paie`, libellés dans les menus.
- Nombreuses failles d'autorisation intra-instance (IDOR) : `Client/CommandesController.php:562,601,647,689`, `Coursier/CommandesController.php:227`, `Admin/PasswordController.php:184` (réinitialise n'importe quel mot de passe à `11111111`), `Admin/UsersController.php:147-167` (un agent peut créer un administrateur). Route publique `/teston` (`routes/web.php:157-161`) réinitialisant le mot de passe du super admin.
- Bogues structurels : `RegisterController` écrit une colonne `name` inexistante ; route `Clientclients` vers un contrôleur absent ; modèle `users/Users.php` doublon de `User` ; dossiers de modèles en minuscules avec namespaces capitalisés ; migrations framework absentes (`password_reset_tokens`, `sessions`, `cache`, `jobs`) ; `->constrained()` chaîné sur `integer()` (aucune FK créée) ; pas de `.env.example` ; tests d'exemple uniquement.

**Résultat attendu.** Une plateforme unique (une base, un déploiement) où :

1. un **site vitrine** public optimisé SEO présente l'offre et sert d'atterrissage avant connexion / inscription ;
2. toute entreprise de livraison **crée son espace en libre-service**, bénéficie d'un **essai gratuit**, puis paie un **forfait** (Monetbil Mobile Money) pour continuer ;
3. chaque espace est **strictement cloisonné** (données, utilisateurs, paramètres, marque) ;
4. le **Super Admin** (Honowa) administre les entreprises, les forfaits d'abonnement (CRUD), les transactions, le référentiel des villes et les réglages de paiement.

## 1. Décisions structurantes (validées avec vous)

| Sujet | Décision | Conséquence |
|---|---|---|
| Identification du tenant | **Par l'utilisateur connecté** (`users.entreprise_id`). Une seule URL. Un `slug` par entreprise est réservé pour une future page publique de suivi. | Pas de DNS wildcard ni de vhost. Middleware `SetCurrentEntreprise` après `auth`. |
| Unicité de l'email | **Unique sur toute la plateforme** (`users.email` reste `UNIQUE`). | Formulaire de connexion inchangé. Un même email ne peut pas appartenir à deux entreprises. |
| Activation d'un espace | **Essai gratuit** (durée configurable `SAAS_ESSAI_JOURS`, 14 j par défaut) puis blocage jusqu'à paiement d'un forfait. Le Super Admin peut suspendre, prolonger ou activer manuellement. | État d'abonnement **calculé à chaque requête** (pas de cron sur mutualisé). |
| Forfaits | Le Super Admin gère les forfaits en **CRUD** (titre, description, prix, période, grâce, mise en avant, statut). Les forfaits actifs alimentent la page `/tarifs` du site vitrine et la page `/abonnement` des entreprises. | Table `super_admin_abonnement` conservée et enrichie. |
| Référentiel géographique | **Villes = référentiel plateforme partagé**, géré par le Super Admin ; chaque entreprise choisit les villes qu'elle dessert et ajuste ses propres aspects (tarif par défaut par ville, dépôt par ville, activation), peut **proposer** une ville manquante (utilisable immédiatement par elle, validée ensuite par le Super Admin). **Zones, quartiers, grille tarifaire, véhicules restent propres à chaque entreprise.** | Table `ville` sans `entreprise_id` ; pivot `entreprise_villes`. |
| Site vitrine | Site public SEO (`/`, `/fonctionnalites`, `/tarifs`, `/faq`, `/contact`, pages légales, `sitemap.xml`, `robots.txt`), layout léger indépendant de Vuexy, contenu rédigé à partir des fonctionnalités (voir §12). | Le tableau de bord client quitte `/` pour `/client`. |
| Tenancy | Base unique, schéma partagé, colonne `entreprise_id` + **scope global maison** (pas de package `stancl/tenancy` ni `spatie/laravel-multitenancy`). | Couche `App\Tenancy` + trait `BelongsToEntreprise` + policies. |
| Rôles | `type_utilisateur` reste un référentiel global (1 Administrateur, 2 Agent, 3 Coursier, 4 Client) ; enum PHP `Role` ; middleware `role:`. Le Super Admin plateforme a son propre **guard** `superadmin`. | Suppression des blocs de contrôle copiés-collés (≈250 occurrences). |
| Marque plateforme | « ORLA » (`APP_NAME`), « Speedex » devient simplement le nom de l'entreprise legacy (id 1). | Nom/logo de l'entreprise affichés dans les espaces de travail. |

## 2. Architecture cible

### 2.1 Modèle de données (vue d'ensemble)

```
PLATEFORME (globale, gérée par le Super Admin)
  super_admin (opérateurs)      super_admin_abonnement (forfaits)     super_admin_api (Monetbil, chiffré)
  super_admin_contact           ville (référentiel : libelle, code, pays, statut validee|proposee)
  type_utilisateur (rôles)      type_client (Entreprise|Simple)

ENTREPRISES (tenants)
  entreprises  ← renommage de super_admin_client
     id, name, slug, email, telephone, adresse, logo_path, pays, devise, prefixe_telephone,
     statut (active|suspendue), essai_fin, id_abonnement, date_fin, date_dernier_paiement,
     id_quartier_siege, tarif_defaut, parametres json, timestamps, deleted_at
  entreprise_villes (pivot) : entreprise_id, id_ville, actif, tarif_defaut, id_quartier_depot, parametres json
  abonnement_transactions ← renommage de super_admin_transaction (+ entreprise_id, reference, payment_id, payload)

DONNÉES MÉTIER (toutes avec entreprise_id, scope global strict)
  users (non strict), agents, coursiers, clients, informations_personnels,
  zone (→ ville globale), quartier (→ ville globale), details_zone, montant_livraison,
  type_vehicule, vehicule (→ ville globale), boutiques (+ est_siege), point_relais, produits, stock,
  commandes, details_commande, paiement, activity
```

### 2.2 Couche tenancy (`app/Tenancy`, `app/Models/Concerns`, `app/Models/Scopes`)

- `App\Tenancy\CurrentEntreprise` : singleton (`AppServiceProvider::register`) — `set()`, `get()`, `id()`, `has()`, `forget()`, `bypass(bool)`, `runAs(Entreprise, Closure)`, `runWithoutTenancy(Closure)` (restaure l'état dans `finally`).
- `App\Models\Scopes\EntrepriseScope` : si bypass → rien ; si aucun tenant courant et modèle strict → lève `TenantNonResoluException` ; sinon `where(qualifyColumn('entreprise_id'), id)`.
- `App\Models\Concerns\BelongsToEntreprise` : enregistre le scope, remplit `entreprise_id` dans `creating`, relation `entreprise()`, `scopeWithoutTenancy()`, propriété `static $tenancyStrict` (à `false` uniquement sur `User`, car `EloquentUserProvider::retrieveById()/retrieveByCredentials()` interrogent `users` **avant** que le tenant soit résolu : login, cookie remember-me, reset de mot de passe).
- Helpers `entreprise()` / `entreprise_id()` dans `app/Helpers/tenancy_helper.php` (ajouté à `composer.json` → `autoload.files`, cohérent avec les helpers existants).

### 2.3 Middlewares et groupes de routes (`bootstrap/app.php`, `routes/web.php`)

```php
// Site vitrine (public, sans session tenant)
Route::name('site.')->group(...);                       // /, /fonctionnalites, /tarifs, /faq, /contact, légales, sitemap.xml

// Auth Laravel UI (register désactivé) + inscription self-service
Auth::routes(['register' => false, 'verify' => false]);
Route::middleware('guest')->group(...);                  // GET/POST /inscription

// Espaces de travail
Route::middleware(['auth', 'entreprise', 'actif'])->group(function () {
    Route::prefix('abonnement')->middleware('role:admin')->group(...);   // accessible même bloqué
    Route::prefix('entreprise')->middleware('role:admin')->group(...);   // paramètres, villes desservies
    Route::middleware('entreprise.active')->group(function () {
        Route::get('/home', ...)->name('home');
        Route::prefix('admin')->middleware('role:admin,agent')->group(...);
        Route::prefix('multi')->middleware('role:admin,agent')->group(...);
        Route::prefix('coursier')->middleware('role:coursier')->group(...);
        Route::prefix('client')->middleware('role:client')->group(...);  // ancien préfixe ''
    });
});

// Console plateforme
Route::prefix('superadmin')->name('superadmin.')->middleware(['auth:superadmin', 'tenancy.bypass'])->group(...);

// Webhook Monetbil (hors auth, hors CSRF)
Route::post('/webhooks/monetbil', ...)->name('webhooks.monetbil');
```

Alias : `entreprise` (`SetCurrentEntreprise`), `actif` (`EnsureUserIsActive`), `entreprise.active` (`EnsureEntrepriseActive`), `role` (`EnsureUserHasRole`), `tenancy.bypass`. Les **noms** de routes existants (`commandes.index`, `Clientcommandes.*`, …) sont conservés pour ne pas réécrire toutes les vues.

### 2.4 État d'abonnement (calculé, `App\Services\Abonnement\AbonnementService::etat()`)

| État | Condition | Effet |
|---|---|---|
| `suspendue` | `entreprises.statut = suspendue` (décision Super Admin) | Tous les rôles → page `abonnement.suspendue` |
| `essai` | `date_fin` null et `essai_fin ≥ now` | Accès complet, bandeau « Essai : J-n » |
| `active` | `date_fin ≥ now` | Accès complet |
| `grace` | `date_fin < now ≤ date_fin + periode_grace` | Accès complet, bandeau d'alerte |
| `expiree` | sinon | Admin → `/abonnement` (payer) ; autres rôles → page `abonnement.expire` |

### 2.5 Rôles et guards

- `App\Enums\Role: int { Admin = 1; Agent = 2; Coursier = 3; Client = 4 }` aligné sur `type_utilisateur`. `User::role()`, `hasRole()`, `isAdmin()`… `dashboardRoute()` remplace `HomeController::index` et `filter()`.
- Guard `superadmin` (`config/auth.php`, provider `super_admins` → `App\Models\SuperAdmin\SuperAdminUser`), `redirectGuestsTo` conditionnel sur `superadmin*`.
- Policies (`app/Policies`) enregistrées via `Gate::policy()` dans `AppServiceProvider::boot` : cross-tenant = 404 (scope), même tenant / mauvais propriétaire = 403.

## 3. Ordre de livraison et estimation

| Release | Phase | Contenu | Impact sur la prod actuelle | Estimation |
|---|---|---|---|---|
| R0 | 0 | Stabilisation, nettoyage, socle de tests, `.env.example` | Aucun visible | 3–4 j |
| R1 | 1 | Entité `entreprises`, `entreprise_id` + backfill, couche tenancy, middlewares | Aucun (tout = entreprise 1) | 4–5 j |
| R1 | 2 | Enum `Role`, middleware `role:`, guard `superadmin` | Nouvelle URL de login super admin | 3–4 j |
| R1 | 8 | Site vitrine SEO (indépendant, démarre en parallèle) | `/` devient la page d'accueil publique | 5–7 j |
| R2 | 3 | Refactor des contrôleurs : policies, validation scoppée, suppression des hardcodes Speedex, grille tarifaire | Aucun fonctionnel | 10–12 j |
| R2 | 4 | Référentiel de villes partagé + villes desservies | Migration des villes existantes | 3–4 j |
| R3 | 5 | Inscription self-service + provisioning + onboarding | Nouveau | 3–4 j |
| R3 | 6 | Facturation par entreprise, `MonetbilGateway`, webhook | Remplace `Sc-transaction.*` | 5–6 j |
| R3 | 7 | Console Super Admin sur le guard (entreprises, forfaits CRUD, villes, transactions, Monetbil) | Remplace les écrans actuels | 4–5 j |
| R3 | 9 | Branding entreprise, paramètres, pages abonnement | Menus | 3–4 j |
| R4 | 10 | Durcissement du schéma : NOT NULL, FK, index et uniques composites, nettoyage | Après audit | 2 j |
| transversal | 11 | Tests (répartis dans chaque phase) | — | ≈6 j |
| **Total** | | | | **≈ 52–63 j/dev** |

R0 et R1 sont déployables sur la production actuelle sans changement visible pour l'entreprise existante. Les contraintes NOT NULL / uniques composites ne sont posées qu'en R4, après audit des données.

## 4. Phase 0 — Stabilisation (R0)

Objectif : rendre le projet installable, testable et sain avant toute refonte.

### 4.1 Autoload et modèles
- Renommer les dossiers `app/Models/{agents,boutiques,clients,commandes,coursiers,details_commande,details_zone,informations_personnels,montant_livraison,paiement,point_relais,produits,quartier,stock,typeClient,typeUtilisateur,ville,zone}` pour correspondre aux namespaces déclarés (`App\Models\Clients`, …), en deux `git mv` (vers un nom temporaire puis final) pour les systèmes de fichiers insensibles à la casse ; remplacer mécaniquement les références (`App\Models\clients\Clients` → `App\Models\Clients\Clients`) dans `app/`, `database/`, `resources/views/admin/templates/template.blade.php:2`. Passer les relations en chaînes (`'App\Models\clients\Clients'`) en `Clients::class`.
- Supprimer `app/Models/users/Users.php` (doublon) et utiliser `App\Models\User` partout. Corriger `User::$fillable` (`noms, email, password, telephone, id_type_utilisateur, id_agent, id_coursier, id_client, id_ville, statut` ; jamais `entreprise_id`). Le cast `password => hashed` rend les `Hash::make()` manuels redondants (à retirer au refactor).
- Corriger les relations cassées relevées : `Ville::vehicules` (`App\Models\Vehicules` inexistant), `Stock::point_relai` (clé `id_point_relai`), `User::activities` (`App\Models\activity`), `SuperAdmin\Client::$fillable` (`nom`, `date_debut_contrat` inexistants).
- Supprimer `resources/views/admin/pages/paiement/Paiement.php` et les fichiers vides à la racine `id_ville`, `libelle`, `save()`.

### 4.2 Bootstrap Laravel 12
- Les providers **sont** enregistrés via `config/app.php` (`ServiceProvider::defaultProviders()->merge([...])`) : pas de blocage. Modernisation optionnelle : créer `bootstrap/providers.php` et fusionner `AuthServiceProvider` (policies) dans `AppServiceProvider`.
- `bootstrap/app.php` : retirer les trois middlewares no-op appendés (`Filter`, `Coursier_filter`, `Admin_filter`) et leurs fichiers ; supprimer les middlewares legacy non référencés (`Authenticate`, `RedirectIfAuthenticated`, `TrustHosts`, `ValidateSignature`, `VerifyCsrfToken`, `EncryptCookies`).

### 4.3 Routes et contrôleurs cassés ou dangereux
- Supprimer `/teston` (`routes/web.php:157-161`).
- `Auth::routes(['register' => false, 'verify' => false])` ; supprimer `RegisterController` et `auth/register.blade.php` (remplacés par `/inscription` en Phase 5).
- Supprimer la route `Clientclients` (contrôleur absent), les routes `Sa-api.index_ajax` / `Sa-api.recap_create` (méthodes absentes), la resource `admin/details_zone` (doublon de nom avec `multi/details_zone`).
- Supprimer les contrôleurs morts `Admin/{CoursiersController, ZoneController, Informations_personnelsController, PaiementController}` et les vues associées `admin/pages/{coursiers,zones}/*`, ainsi que `Admin/{TypeClientController, TypeUtilisateurController}` (stubs sur référentiels globaux).
- Retirer de `public/app-assets` les fichiers dangereux ou inutiles : `images/logo/FacebookToolkit-master.zip`, scripts PHP de démo sous `data/`, `.DS_Store` ; ajouter `public/error_log`, `.ftpquota`, `.DS_Store` au `.gitignore`.

### 4.4 Migrations framework, configuration, seeders
- `database/migrations/2025_10_01_000001_create_framework_tables.php` : `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `personal_access_tokens`.
- `2025_10_01_000002_add_email_verified_at_and_doit_changer_mdp_to_users.php`.
- `.env.example` complet (`APP_NAME=ORLA`, `APP_URL`, `DB_*`, `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`, `MAIL_*`, `SAAS_ESSAI_JOURS=14`, `SAAS_TENANCY_STRICT=true`, `SAAS_ENTREPRISE_LEGACY_NOM=Speedex`, `SITE_CONTACT_EMAIL`, `SITE_CONTACT_TEL`, `APP_DEPLOY_TOKEN=`).
- `config/saas.php` : `essai_jours`, `tenancy_strict`, `entreprise_legacy` (nom, slug), `tarif_defaut` (1000), `devise` (XAF), `pays` (CM), `prefixe_telephone` (+237), `telephone_regex` par pays. `config/site.php` : nom commercial, baseline, contacts, réseaux sociaux, URL canonique, image OG par défaut.
- `database/seeders/DatabaseSeeder.php` : ajouter `namespace Database\Seeders;` ; appeler `ReferentielSeeder` (type_utilisateur 1–4 — le libellé `Super Admin` de l'id 1 est **conservé en phase 0** car ~250 comparaisons de chaînes en dépendent ; il devient `Administrateur` en phase 1 — type_client 1 Entreprise / 2 Simple, villes de base Douala et Yaoundé ; la liste complète du référentiel arrive en phase 4), `SuperAdminSeeder` (renommage de `SuperAdmin.php`), `DemoSeeder` (local uniquement : admin, agent, coursier, client de démonstration ; deux entreprises à partir de la phase 5).
- Corrections de bogues évidentes et sans risque dans `app/Helpers/system_helper.php` : `Dossier()` mappe `AGENT` vers `admin` (le dossier `routeur/templates` n'existe pas, un agent qui ouvre une page `multi/*` obtient une erreur) ; `filter()` redirige `routeur`/`superviseur_ville` vers `home.admin` (routes `home.routeur`/`home.superviseur_ville` inexistantes). Ces helpers disparaissent en phase 2.
- `resources/lang/fr/` : publier les traductions françaises de validation/auth (`validation.php`, `auth.php`, `passwords.php`) car `locale=fr` sans fichiers affiche des clés brutes.

### 4.5 Socle de tests
- `tests/TestCase.php` : `RefreshDatabase`, helpers `creerEntreprise()`, `actingAsAdmin(Entreprise)`, `actingAsAgent`, `actingAsCoursier`, `actingAsClient`, `runAs(Entreprise, Closure)` ; `setUp()` réinitialise `CurrentEntreprise`.
- Remplacer `tests/Feature/ExampleTest.php` par des tests de fumée (voir §20.7) : en phase 0, `/` reste derrière `Check_Sa_Client_Error` et `auth` (redirection) ; la page d'accueil publique n'arrive qu'en phase 8.
- Vérifier que toute la chaîne de migrations passe sur sqlite `:memory:` (les `Schema::table()->foreign()` y sont ignorés ; les `dropForeign` doivent être protégés par `DB::getDriverName() !== 'sqlite'`).

**Critère de sortie R0 :** `composer install`, `php artisan migrate:fresh --seed`, `php artisan test` verts ; `php artisan route:list` sans erreur ; l'application fonctionne comme avant pour l'entreprise existante.

## 5. Phase 1 — Entité `entreprises` et couche tenancy (R1)

### 5.1 Migrations (dans cet ordre ; backfills uniquement en `DB::table`, jamais en Eloquent)

1. **`2025_10_02_000001_create_entreprises_from_super_admin_client.php`** — `Schema::rename('super_admin_client','entreprises')` (ou `create` si absente). Ajouts : `slug string(80) nullable`, `email string nullable`, `logo_path string nullable`, `pays string(2) default 'CM'`, `devise string(3) default 'XAF'`, `prefixe_telephone string(6) default '+237'`, `essai_fin datetime nullable`, `id_quartier_siege unsignedInteger nullable` (type de `quartier.id`), `tarif_defaut decimal(12,2) default 1000`, `parametres json nullable`, `id_abonnement` → `unsignedBigInteger nullable` (type de `super_admin_abonnement.id`). `statut boolean` → `string(20) default 'active'` (`change()` doit ré-énoncer tous les modificateurs) puis `UPDATE … SET statut = CASE WHEN statut IN ('1') THEN 'active' ELSE 'suspendue' END`. Backfill `slug = Str::slug(name)` avec suffixe en cas de collision.
2. **`…000002_create_abonnement_transactions_from_super_admin_transaction.php`** — `rename` ; ajouts `entreprise_id unsignedBigInteger nullable` (backfill `= id_client`), `reference string(64) nullable` (backfill uuid), `payment_id string nullable`, `payload json nullable`, index `(entreprise_id, statut)`. `id_client` conservé jusqu'en R4.
3. **`…000003_add_entreprise_id_to_tenant_tables.php`** — pour les **19 tables tenant** `users, agents, coursiers, clients, informations_personnels, zone, quartier, details_zone, montant_livraison, type_vehicule, vehicule, boutiques, point_relais, produits, stock, commandes, details_commande, paiement, activity` : `unsignedBigInteger('entreprise_id')->nullable()->after('id')` + `index('entreprise_id')`. (`ville` **exclue** : référentiel global, voir Phase 4.)
4. **`…000004_backfill_entreprise_id.php`** — `$id = première entreprise` ; si aucune et données présentes → insère l'entreprise legacy (`config('saas.entreprise_legacy')`, `statut active`, `date_fin` null : l'admin devra souscrire, ou le Super Admin fixe une date). `UPDATE <table> SET entreprise_id = :id WHERE entreprise_id IS NULL` sur les 19 tables. `boutiques.est_siege boolean default 0` + `UPDATE … SET est_siege = 1 WHERE UPPER(libelle)='SPEEDEX'`. `entreprises.id_quartier_siege` = quartier `UPPER(libelle)='SPEEDEX'` (créé s'il manque), renommé « Siège <nom> ». `type_utilisateur` id 1 → `Administrateur` ; `type_client` 1/2 insérés si absents.
5. **`…000005_add_indexes_on_entreprises_and_transactions.php`** — `entreprises.slug unique`, `abonnement_transactions.reference unique`, FK `abonnement_transactions.entreprise_id → entreprises`, FK `entreprises.id_quartier_siege → quartier (nullOnDelete)`, FK `entreprises.id_abonnement → super_admin_abonnement`.
6. **`…000006_convert_speedex_enums_to_strings.php`** — `commandes.mode_de_paiement` et `paiement.qui_paie` : ENUM → `string(20)` (portable sqlite/MySQL, pas d'altération d'ENUM) puis `speedex` → `entreprise`. `agents.telephone text` → `string(30)` (prérequis de l'unique composite R4).
7. **`…000007_encrypt_super_admin_api_credentials.php`** — `key`, `secret`, `password` → `text`, réencryption idempotente (`try decryptString catch encryptString`). Documenter : rotation d'`APP_KEY` interdite.

### 5.2 Classes
- `app/Tenancy/CurrentEntreprise.php`, `app/Tenancy/Exceptions/TenantNonResoluException.php`, `app/Models/Scopes/EntrepriseScope.php`, `app/Models/Concerns/BelongsToEntreprise.php`, `app/Helpers/tenancy_helper.php` (cf. §2.2).
- `app/Models/Entreprise.php` (table `entreprises`, `SoftDeletes`, casts `parametres array`, dates, `tarif_defaut decimal:2` ; relations `abonnement()`, `transactions()`, `utilisateurs()`, `quartierSiege()`, `villes()` (pivot, Phase 4) ; fillable **sans** `statut`).
- `app/Models/AbonnementTransaction.php`, `app/Models/Abonnement.php` (forfait, table `super_admin_abonnement`), `app/Models/SuperAdmin/{SuperAdminUser, ApiCredential (casts encrypted), Contact, InfoTransaction}.php`.
- Appliquer `BelongsToEntreprise` + `HasFactory` aux 19 modèles tenant (`User` avec `$tenancyStrict = false`).

### 5.3 Middlewares
- `app/Http/Middleware/SetCurrentEntreprise.php` : sans utilisateur → `forget()` ; `entreprise_id` null ou entreprise supprimée → `Auth::logout()` + redirection login avec message ; sinon `set()`.
- `app/Http/Middleware/EnsureUserIsActive.php` : `statut == 0` → `home.error` (remplace les blocs `if ($statut == 0)` de chaque méthode).
- `app/Http/Middleware/EnsureEntrepriseActive.php` : applique le tableau §2.4 ; routes exemptées `abonnement.*`, `entreprise.*`, `logout`.
- `LoginController::logout` override → `CurrentEntreprise::forget()`.
- Suppression de `Check_Sa_Client_Error`, `SuperAdmin/ErrorController`, helpers `Sa_check_abonnement`, `Sa_statut`, `Sa_site_*`, routes `SuperAdmin.no_abonnement|empty_abonnement|empty_client|site_inactif`. Les vues `superadmin/errors/*` sont recyclées en `resources/views/abonnement/{expire,suspendue}.blade.php`.

### 5.4 Routes
- Appliquer la structure §2.3 dès R1 (préfixe `client` pour l'ancien groupe `''`, `/` libéré pour le site vitrine). `HomeController::index` → `redirect()->route($user->dashboardRoute())`.

### 5.5 Contexte console et audit
- Seeders : toute création tenant dans `app(CurrentEntreprise::class)->runAs($entreprise, fn () => …)`.
- Commande `app/Console/Commands/TenancyAudit.php` (`tenancy:audit`) : lignes à `entreprise_id NULL` par table, doublons qui bloqueraient les uniques composites, incohérences parent/enfant (`commandes.entreprise_id ≠ clients.entreprise_id`, quartier d'une commande hors entreprise…). Pré-vol obligatoire de R4.

### 5.6 Adaptation temporaire
- `SuperAdmin/{ClientController, TransactionController}` : `Client` → `Entreprise`, `id_client` → `entreprise_id`. Le flux legacy de paiement reste fonctionnel pour l'entreprise 1 jusqu'à R3.

**Critère de sortie R1 :** `SELECT COUNT(*) … WHERE entreprise_id IS NULL` = 0 sur 19 tables ; l'entreprise 1 a `slug`, `id_quartier_siege`, `statut active` ; connexion et création de commande inchangées ; tests d'isolation de base verts (deux entreprises en sqlite).

### 5.7 Bilan d'exécution (phase 1 réalisée)

Réalisé conformément aux §5.1–5.6, avec les écarts suivants, choisis pour ne rien casser avant le refactor des contrôleurs :

- **`entreprises.statut` reste booléen** (1 actif / 0 suspendu, cast `boolean`) au lieu d'une chaîne : c'est la seule décision du Super Admin, les états essai/actif/grâce/expiré sont calculés par `AbonnementService` ; les vues et contrôleurs Super Admin qui comparent `statut == 1` restent valides.
- **Libellé `type_utilisateur` id 1 non renommé** (toujours « Super Admin ») : ~250 comparaisons de chaînes en dépendent jusqu'au refactor (phases 2–3).
- **Valeurs `speedex` non remappées** dans `commandes.mode_de_paiement` et `paiement.qui_paie` : seules les colonnes passent d'ENUM à `string(20)` (migration 000006) ; le remplacement par `entreprise` accompagne la suppression des hardcodes (phase 3). Le quartier « Speedex » garde aussi son libellé (les contrôleurs le recherchent ainsi) ; il est simplement référencé par `entreprises.id_quartier_siege`.
- **Modèles Super Admin** : seuls `SuperAdmin\Client` → `App\Models\Entreprise` et `SuperAdmin\Transaction` → `App\Models\AbonnementTransaction` sont remplacés ; `Abonnement`, `Api` (casts `encrypted`), `Contact`, `Info_transaction`, `SuperAdmin\User` gardent leur nom jusqu'à la réécriture de la console (phase 7).
- **Flux de paiement legacy conservé et sécurisé** : `Sc-transaction.create/store` passent sous `auth` + `entreprise` (souscription pour l'entreprise courante uniquement), `methode=application` exige la session Super Admin, `show`/`checkpay` restent publics (retour Monetbil) avec `tenancy.bypass`. Le remplacement complet arrive en phase 6.
- **Pages de blocage** : `abonnement.expire` et `abonnement.suspendue` (contrôleur `Facturation\AbonnementStatutController`) remplacent les quatre pages d'erreur de licence ; l'administrateur d'une entreprise expirée est redirigé vers `Sc-transaction.create`.
- **Routes** : le groupe client passe de `/` à `/client` ; `/` redirige vers la connexion ou le tableau de bord (le site vitrine prendra la racine en phase 8). Les middlewares `role:` arrivent en phase 2 ; les contrôles de rôle restent dans les contrôleurs.
- **Tests** : 64 tests verts (scope, isolation entre deux entreprises à travers les contrôleurs existants, connexion/déconnexion, gating par état, audit, seeders, fumée). sqlite applique les clés étrangères : les factories créent leurs parents.

## 6. Phase 2 — Rôles et guard Super Admin (R1)

- `app/Enums/Role.php`, méthodes sur `User` (`role()`, `hasRole()`, `isAdmin()`, `isAgent()`, `isCoursier()`, `isClient()`, `dashboardRoute()`).
- `app/Http/Middleware/EnsureUserHasRole.php` (alias `role`) : `role:admin,agent` → redirection vers `dashboardRoute()` (comportement actuel) ou 403 pour les requêtes AJAX.
- Supprimer `filter()` et `Dossier()` de `app/Helpers/system_helper.php` (routes `home.routeur` / `home.superviseur_ville` inexistantes) ; les vues `multi/*` choisissent leur layout via `auth()->user()->role()->layout()`.
- Vues : remplacer les comparaisons `type_utilisateur->libelle == 'Super Admin'` (`admin/menu/admin.blade.php:123`, `coursier/pages/home.blade.php:113,246`, `client/pages/home.blade.php:109`, `admin/templates/template.blade.php:179`) par `isAdmin()` / `hasRole()`.
- `config/auth.php` : guard `superadmin` + provider `super_admins` ; `bootstrap/app.php` : `redirectGuestsTo` / `redirectUsersTo` conditionnels.
- `app/Http/Controllers/SuperAdmin/AuthController.php` (`login`, `authenticate` avec throttle, `logout`) ; suppression de `check_superadmin()`, de `SuperAdmin_infos` (qui stockait le hash du mot de passe en session), de `dernier_url` (→ `redirect()->intended()`), du `password_verify` manuel. `superadmin/layout/template.blade.php:95` → `auth('superadmin')->user()->name`.
- Middleware `tenancy.bypass` (`CurrentEntreprise::bypass(true)`) sur le groupe `superadmin` ; les écrans qui inspectent une entreprise précise utilisent `runAs()`.

## 7. Phase 3 — Refactor des contrôleurs (R2)

### 7.1 Pattern (décrit une fois, appliqué à `app/Http/Controllers/{Admin,Client,Coursier,Multi}/*`)
1. Retirer le bloc de contrôle de rôle/statut en tête de chaque méthode (≈250 occurrences) : assuré par `role:` et `actif`.
2. `Users` → `User` ; retirer `Hash::make` (cast) ; envelopper les créations multi-tables (`clients + users`, `coursiers + users + informations_personnels`) dans `DB::transaction()`.
3. **Lectures** : ne rien ajouter, le scope filtre. **Interdire** tout `->where('entreprise_id', …)` manuel : les chaînes `orWhere` des recherches (`Admin/CommandesController::index`, `Multi/ZoneController::index`, `Admin/UsersController::index`) casseraient l'isolation, alors que le scope global est parenthésé par Laravel.
4. **Propriété (IDOR)** : `$this->authorize()` via policies (§7.2).
5. **Validation** : Form Requests dans `app/Http/Requests/{Admin,Client,Coursier,Multi}/…` avec `App\Support\Regles::unique('quartier','libelle', ignore: $id)` (= `Rule::unique()->where('entreprise_id', entreprise_id())`) et `Regles::exists('quartier','id')` scoppé pour **tous** les ids postés (`id_client`, `id_boutique`, `id_quartier_*`, `id_coursier`, `id_agent`, `id_zone`, `produit[]`, `id_compte_associe`, `id_montant`), `Regles::villeDesservie()` pour `id_ville` (Phase 4). Remplacer les 48 règles `unique:table` globales ; `users.email` reste `unique:users`, `users.telephone` devient scoppé.
6. **Hardcodes** : quartier `'Speedex'` (12 emplacements) → `entreprise()->quartierSiege` ; boutique `'Speedex'` → `Boutiques::create(['libelle' => 'Dépôt '.entreprise()->name, 'est_siege' => true, …])` ; `['coursier','speedex']` → `App\Enums\ModePaiement` (`Coursier` « Payé au coursier », `Entreprise` « Réglé à l'entreprise ») ; `qui_paie` → `App\Enums\QuiPaie` ; `1000` (`Admin/CommandesController.php:246-251`, `Montant_livraisonController.php:60,68`, `Multi/ZoneController.php:134,142`) → tarif par défaut de la ville desservie, sinon `entreprise()->tarif_defaut` ; `type_client = 1|2` → constantes `TypeClient::ENTREPRISE|SIMPLE` ; `id_type_utilisateur = 2|3|4` → `Role::…->value` ; `id_ville = 1` disparaît.
7. Retirer les créations « à la volée » dans les `index()`/`create()` (`Informations_personnels`, `TypeClient`, quartier Speedex) : faites au provisioning ou à la création de l'entité.

### 7.2 Policies (`app/Policies`)
- `CommandePolicy` : `viewAny/create` (admin, agent, client) ; `view` : admin ∨ agent affecté (`commande.id_agent`, logique de `Admin/CommandesController::show`) ∨ client propriétaire ∨ coursier affecté ; `update` : admin, agent affecté, client propriétaire si `attente` ; `changerStatut` : admin/agent ; coursier affecté (transitions `encours|livre|annulee|echoue`) ; `attribuerCoursier`, `attribuerAgent`.
- `UserPolicy` : `create` : admin ; agent uniquement pour `Coursier|Client` ; `update/resetPassword` : admin ou soi-même ; `toggle` : admin.
- `ClientPolicy`, `CoursierPolicy`, `AgentPolicy`, `BoutiquePolicy`, `PaiementPolicy`, `VehiculePolicy`, `ZonePolicy` (zones/grille : admin uniquement, conformément au menu).
- Toutes vérifient en défense en profondeur `$model->entreprise_id === $user->entreprise_id`.

### 7.3 Contrôleurs représentatifs et points spécifiques
- `Admin/CommandesController.php` (801 l., contrôleur de référence) : `store()` crée clients/quartiers à la volée (garder, scoppé, en transaction) ; `montant()` recherche `Montant_livraison` par `whereIn` sur les deux colonnes (garder) ; `attribuate_agent` avec `Regles::exists('agents')`.
- `Client/CommandesController.php` : `show/edit/update/destroy` (l. 562-689) → policy ; `index` filtre `id_client = auth()->user()->id_client` (l. 69-82 aujourd'hui globales).
- `Coursier/CommandesController.php:227` → policy coursier affecté.
- `{Client,Coursier}/PasswordController.php:177-183`, `UsersController.php:192` : ignorer `$id`, utiliser `auth()->id()`.
- `Admin/PasswordController.php:184` : mot de passe temporaire aléatoire affiché une fois, `users.doit_changer_mdp = 1`, entrée `Activity` ; middleware `ForcerChangementMotDePasse`.
- `Admin/UsersController.php` : `show($email)`/`edit($email)` → par id ; blocage agent → admin par `UserPolicy`.
- `Admin/Montant_livraisonController.php`, `Multi/ZoneController.php::store` (l. 120-142) : extraire `app/Services/Tarification/GrilleTarifaire.php::synchroniser()` : `Zone::pluck('id')` scoppé, paires existantes en ensemble `"$a-$b"`, insertion des paires manquantes avec `entreprise_id` **explicite** (`insert()` groupé contourne le hook `creating`), montant = tarif par défaut.
- `Admin/Details_commandeController.php` : `'Speedex'` (l. 198), `$dette_speedex` → enums / `$dette_entreprise`.
- `HomeController.php:76-80` (tableau de bord) : requêtes scoppées automatiquement ; corriger `coursier()` qui filtre `Activity` par id coursier au lieu de l'id user.
- `Multi/*` : conserver le filtrage secondaire `users.id_ville` (intra-entreprise).

## 8. Phase 4 — Référentiel de villes partagé et villes desservies (R2)

### 8.1 Schéma
- **`ville`** (globale, sans `entreprise_id`) : ajouter `pays string(2) default 'CM'`, `region string nullable`, `statut string(20) default 'validee'` (`validee|proposee|rejetee`), `proposee_par_entreprise_id unsignedBigInteger nullable`, `actif boolean default 1`, `slug`, unique `(pays, slug)`.
- **`entreprise_villes`** (pivot) : `entreprise_id`, `id_ville unsignedInteger`, `actif boolean default 1`, `tarif_defaut decimal(12,2) nullable` (surcharge du tarif entreprise pour cette ville), `id_quartier_depot unsignedInteger nullable`, `parametres json nullable`, unique `(entreprise_id, id_ville)`.
- Migration de données : dédoublonner les `ville` existantes sur `UPPER(libelle)` (remapper `zone.id_ville`, `quartier.id_ville`, `vehicule.id_ville`, `users.id_ville` vers l'id conservé), marquer `validee`, créer les lignes pivot `actif = 1` pour l'entreprise 1.
- `zone`, `quartier`, `vehicule`, `users` continuent de référencer `ville.id` mais sont scoppés par `entreprise_id` ; uniques composites en R4 : `zone (entreprise_id, id_ville, libelle)`, `quartier (entreprise_id, id_ville, libelle)`.

### 8.2 Modèles et règles
- `Ville` : **pas** de `BelongsToEntreprise` ; scope `visiblesPour(Entreprise)` = `statut validee` ∨ `proposee_par_entreprise_id = id` ; relation `entreprises()` (pivot).
- `Entreprise::villes()` (`belongsToMany` avec pivot `actif`, `tarif_defaut`, `id_quartier_depot`) ; `villesDesservies()` = pivot `actif = 1`.
- `Regles::villeDesservie()` : `id_ville` ∈ villes desservies de l'entreprise courante ; utilisé par zones, quartiers, véhicules, utilisateurs, commandes.
- `App\Services\Geographie\VilleService` : `proposer(Entreprise, libelle, region)` (crée `proposee`, active immédiatement dans le pivot), `valider(Ville)`, `rejeter(Ville)`, `fusionner(Ville $doublon, Ville $cible)` (remappe toutes les références de l'entreprise proposante puis supprime le doublon).

### 8.3 Écrans
- Espace entreprise (`role:admin`) : `entreprise/villes` remplace `Admin/VilleController` : liste du référentiel avec bascule « desservie », tarif par défaut par ville, dépôt par ville, formulaire « Proposer une ville ». Les listes déroulantes de villes de tous les formulaires (`Ville::orderBy…` dans ~20 contrôleurs) passent par `entreprise()->villesDesservies()`.
- Console Super Admin : `superadmin/villes` (CRUD, file des propositions à valider / fusionner / rejeter, activation).

## 9. Phase 5 — Inscription self-service et onboarding (R3)

- Routes `guest` : `GET /inscription` → `Inscription\InscriptionController@create`, `POST /inscription` → `@store` (`throttle:5,1`).
- `app/Http/Requests/InscriptionRequest.php` : `entreprise_nom` (required, max 100), `entreprise_telephone` (regex pays), `id_ville` (existe, `validee`), `admin_noms`, `email` (`unique:users` et `unique:entreprises`), `telephone`, `password` (`confirmed`, `Password::defaults()`), `cgu` (`accepted`).
- `app/Services/Tenancy/EntrepriseProvisioner.php::provisionner(array): Entreprise` en `DB::transaction` : `Entreprise::create` (`statut active`, `essai_fin = now + essai_jours`, `slug`, `tarif_defaut`, `pays`, `devise`) ; `runAs` : ligne pivot `entreprise_villes` (ville choisie, actif), quartier « Siège » → `id_quartier_siege`, `User` admin (`Role::Admin`, `statut 1`, `entreprise_id` explicite, sans ligne `agents` — l'admin ne doit pas apparaître dans les listes de dispatch), `Activity` « Espace créé ». Réutilisé par la console Super Admin et `DemoSeeder`.
- Après `store` : `Auth::login`, `event(new Registered($user))` (vérification email activable plus tard), redirection `home.admin` avec composant d'onboarding `resources/views/components/onboarding.blade.php` (a une zone ? un coursier ? un client ? un abonnement ?).
- Vue `resources/views/inscription/create.blade.php` (thème `app-assets`, même style que `auth/login.blade.php`) ; `auth/login.blade.php` : « Speedex » → `config('app.name')`, lien « Créer l'espace de mon entreprise ».

## 10. Phase 6 — Facturation par entreprise et Monetbil (R3)

- `app/Enums/EtatAbonnement.php` (`Essai, Active, Grace, Expiree, Suspendue`) ; `app/Services/Abonnement/AbonnementService.php` : `etat()`, `dateFinEffective()`, `demarrerTransaction(Entreprise, Abonnement, Methode)` (`waiting`, `reference` uuid, `date_debut = max(now, date_fin)`, `date_fin` via `app/Support/Periode.php` qui reprend `Sa_prochaine_date_paie` **en ajoutant le cas `heure`**), `confirmer()` (idempotent), `echouer()`, `annuler()`, `activerManuellement(Entreprise, Abonnement, SuperAdminUser)`, `prolongerEssai()`.
- `app/Services/Monetbil/MonetbilGateway.php` (+ `MonetbilConfig` depuis `ApiCredential`) : **remplace** le SDK vendored (`require_once config.php` lisant `$_SESSION`, `header()+exit`, SSL désactivé) par ~100 lignes sur `Http` : `creerPaiement(AbonnementTransaction): string` (POST `https://www.monetbil.com/widget/v2.1/{service_key}` avec `amount`, `currency = entreprise.devise`, `country`, `locale fr`, `payment_ref = reference`, `item_ref = entreprise.id`, `user = admin.telephone`, `email`, `return_url = route('abonnement.retour', $reference)` **sans query string** (elle entrerait dans la signature), `notify_url = route('webhooks.monetbil')`, `logo`) ; `verifierSignature(array)` (retirer `sign`, `ksort`, `md5(secret . implode)`) ; `verifierPaiement(paymentId)` (POST `checkPayment`, mapping succès/annulé/échec, `testmode`). Testable par `Http::fake()`. Le dossier `app/Http/Controllers/SuperAdmin/monetbil/` est supprimé.
- `app/Http/Controllers/Facturation/AbonnementController.php` (`abonnement.index|payer|retour|show`, `role:admin`, hors `entreprise.active`) : `index` (forfaits actifs, état, historique), `payer` (`PayerAbonnementRequest`), redirection vers `payment_url`, `retour($reference)` : signature invalide → 403 + log ; `verifierPaiement()` avant tout changement ; `confirmer/annuler/echouer` ; détails dans `super_admin_info_transaction`.
- `app/Http/Controllers/Facturation/MonetbilWebhookController.php` : `POST /webhooks/monetbil` (`validateCsrfTokens(except: ['webhooks/monetbil'])`), même logique (source de vérité si l'utilisateur ferme la fenêtre).
- Suppression des routes publiques `Sc-transaction.*`, `Sc-transaction.checkpay`, des helpers `Sa_pay`, `Sa_checkpay`, de `$_SESSION`.
- Vues : `resources/views/abonnement/{index,expire,suspendue,retour}.blade.php` ; bandeau essai/grâce dans `admin/templates/template.blade.php`.

## 11. Phase 7 — Console Super Admin (R3)

Toutes les routes sous `auth:superadmin` + `tenancy.bypass`, contrôleurs `app/Http/Controllers/SuperAdmin/` :

| Contrôleur | Écrans | Notes |
|---|---|---|
| `DashboardController` | Compteurs : entreprises (essai/actives/expirées/suspendues), MRR, transactions du mois, inscriptions récentes | remplace `pages/home.blade.php` (démo statique) |
| `EntrepriseController` | `index` (+ DataTables ajax), `show` (`runAs` : utilisateurs, commandes du mois, dernière activité, historique d'abonnement), `create/store` (via `EntrepriseProvisioner`), `edit/update`, `suspendre/activer`, `prolongerEssai`, `abonner` (activation manuelle), `impersonner` (optionnel : bandeau + retour) | vues à partir de `superadmin/pages/client/*` |
| `PlanController` (**CRUD forfaits**) | `index/create/store/edit/update/toggle` : `titre`, `description`, `montant`, `accumulateur`, `type_periode`, `periode_grace`, `statut`, `mis_en_avant`, `ordre`, `limites json` (optionnel : max coursiers / commandes par mois, appliqué plus tard) | migration ajoutant `description`, `mis_en_avant`, `ordre`, `limites` à `super_admin_abonnement` ; `abonnement/info.blade.php` cassé → réécrit |
| `TransactionController` | liste filtrable par entreprise/statut, détail | vues actuelles sont des copies de la liste clients → réécrites |
| `VilleController` | CRUD référentiel, propositions à valider/fusionner/rejeter | Phase 4 |
| `MonetbilSettingsController` | édition clé/secret (chiffrés, masqués), mode test | remplace `ApiController`/`ParametreController` (stubs) |
| `ContactController` | téléphones/email affichés sur le site vitrine et les pages de blocage | table `super_admin_contact` |
| `MaintenanceController` | bouton « Exécuter les migrations » (`Artisan::call('migrate', ['--force'=>true])`) et « Audit tenancy » | hébergement sans SSH (§14) |

Routes renommées `superadmin.entreprises.*`, `superadmin.plans.*`, `superadmin.transactions.*`, `superadmin.villes.*`, `superadmin.monetbil.*`, `superadmin.contacts.*` ; menu `superadmin/menu/menu.blade.php` complété (Transactions, Villes, Paiement, Contacts, Maintenance).

## 12. Phase 8 — Site vitrine optimisé SEO (démarre en R1, livré avec R3)

### 12.1 Structure technique
- Routes `routes/web.php` (groupe `site.`) : `/` accueil, `/fonctionnalites`, `/tarifs`, `/faq`, `/contact` (GET + POST vers `super_admin_contact` email, throttle, honeypot), `/a-propos`, `/mentions-legales`, `/confidentialite`, `/cgu`, `/sitemap.xml`, `/robots.txt` (généré : `Disallow: /admin /superadmin /coursier /client /home /abonnement /entreprise /multi /login /inscription`, `Sitemap:` absolu).
- `app/Http/Controllers/Site/{PageController, ContactController, SitemapController}.php` ; les forfaits de `/tarifs` viennent de `Abonnement::where('statut',1)->orderBy('ordre')` (cache 10 min).
- Layout **léger et indépendant de Vuexy** : `resources/views/site/layouts/site.blade.php`, composants `resources/views/components/site/{seo,header,footer,cta,feature-card,plan-card,faq-item}.blade.php`. Entrées Vite dédiées `resources/css/site.css` (Bootstrap 5 partiel via Sass, ou CSS maison ≤ 30 ko) et `resources/js/site.js` (sans jQuery) ajoutées à `vite.config.js` ; `public/build` construit localement et téléversé (gitignoré).
- Composant `<x-site.seo>` : `<title>` (≤ 60 car.), `meta description` (≤ 155 car.), `canonical`, `robots`, `lang="fr"`, `hreflang="fr"`, Open Graph + Twitter Card (`public/images/site/og-cover.jpg` 1200×630), JSON-LD : `Organization`, `WebSite` (+ `SearchAction` non), `SoftwareApplication` avec `Offer` par forfait sur `/tarifs`, `FAQPage` sur `/faq`, `BreadcrumbList` sur les pages internes, `ContactPoint`.
- Performance : une seule feuille CSS, polices système (ou une police auto-hébergée `font-display: swap`), images WebP avec `width/height` et `loading="lazy"`, `preload` du visuel LCP, pas de script tiers bloquant ; `.htaccess` : `mod_deflate` + `mod_expires` pour `/build`, `/images`. Cibles Core Web Vitals : LCP < 2,5 s, CLS < 0,1, INP < 200 ms. Accessibilité : contrastes AA, `alt`, focus visible, un seul `<h1>` par page.
- Mesure : `config('site.analytics')` optionnel (Plausible/GA4, chargé `defer`), Search Console à configurer après mise en ligne.
- Mots-clés cibles (FR, Cameroun/Afrique centrale) : « logiciel de gestion de livraison », « plateforme de gestion de coursiers », « application de suivi de livraison », « dispatch coursiers », « gestion de flotte de livreurs », « paiement à la livraison », « livraison Douala », « livraison Yaoundé », « SaaS logistique urbaine ».

### 12.2 Contenu rédactionnel (à intégrer dans les vues ; marque `ORLA` = `config('site.nom')`)

**Accueil `/`** — title : « ORLA – Logiciel de gestion de livraison pour entreprises de coursiers » ; description : « Pilotez vos coursiers, vos zones et vos commandes depuis une seule plateforme. Essai gratuit, sans engagement, paiement Mobile Money. »
- H1 : *Gérez toutes vos livraisons depuis une seule plateforme.*
- Sous-titre : *ORLA équipe les entreprises de livraison et de coursiers : commandes, dispatch, coursiers, zones tarifaires, encaissements et suivi client, en temps réel et sans installation.*
- CTA : « Créer mon espace gratuitement » (→ `/inscription`) · « Voir les fonctionnalités ».
- Bandeau preuves : *Essai gratuit de 14 jours · Paiement par Mobile Money et Orange Money · Données cloisonnées par entreprise · Accessible sur mobile*.
- Section « Un espace pour chaque acteur de la livraison » (4 cartes) : **Administrateur** (paramètre l'entreprise, les villes desservies, les zones et la grille tarifaire, gère les équipes et suit l'activité) ; **Agent de dispatch** (reçoit les commandes, calcule le tarif, attribue le bon coursier, suit chaque statut) ; **Coursier** (voit ses courses du jour classées par quartier, marque livré ou annulé, enregistre son activité) ; **Client** (passe ses commandes en ligne, lie ses boutiques, suit l'état de chaque livraison).
- Section « Comment ça marche » (3 étapes) : 1. *Créez votre espace en deux minutes* — nom de l'entreprise, ville, compte administrateur. 2. *Configurez vos zones et vos tarifs* — quartiers, zones, prix par paire de zones, coursiers et véhicules. 3. *Livrez et encaissez* — commandes, attribution, statuts, paiement au coursier ou à l'entreprise, rapports.
- Section « Pensé pour l'Afrique centrale » : *Tarifs en FCFA, numéros au format local, abonnement réglé par MTN Mobile Money ou Orange Money via Monetbil, fonctionne sur les connexions mobiles.*
- Section forfaits (aperçu dynamique des forfaits mis en avant) + FAQ courte (3 questions) + CTA final.

**Fonctionnalités `/fonctionnalites`** — title : « Fonctionnalités – Dispatch, coursiers, zones tarifaires, suivi | ORLA » ; description : « Découvrez comment ORLA gère commandes, coursiers, véhicules, zones, quartiers, tarifs, stock et encaissements pour votre entreprise de livraison. »
- H1 : *Tout ce qu'il faut pour faire tourner une entreprise de livraison.*
- Blocs (H2) : **Commandes et dispatch** (création par un agent ou par le client, commandes simples ou entreprise, adresse et quartier de collecte et de livraison, tarif calculé automatiquement selon les zones, attribution d'un agent puis d'un coursier, cycle de vie attente → attribuée → en cours → livrée / annulée / échouée, montant à récupérer, mode de paiement) ; **Coursiers et flotte** (fiches coursiers avec informations personnelles et pièce d'identité, affectation par zones, types de véhicules et véhicules immatriculés, disponibilité) ; **Villes, zones et quartiers** (référentiel de villes, zones propres à votre entreprise, quartiers rattachés, grille tarifaire par paire de zones générée automatiquement et modifiable) ; **Clients, boutiques et points relais** (clients particuliers ou entreprises, comptes clients avec accès en ligne, boutiques liées, points relais tenus par vos coursiers, produits et mouvements de stock) ; **Encaissements et rapports** (paiement au coursier ou réglé à l'entreprise, enregistrement des versements Mobile Money / Orange Money, rapport de livraison par client et par période, solde dû) ; **Sécurité et cloisonnement** (chaque entreprise dispose de son espace, de ses utilisateurs et de ses rôles ; personne n'accède aux données d'une autre entreprise) ; **Abonnement simple** (essai gratuit, forfaits mensuels ou annuels, paiement Mobile Money, période de grâce).

**Tarifs `/tarifs`** — title : « Tarifs – Forfaits d'abonnement ORLA, essai gratuit 14 jours » ; description : « Choisissez le forfait adapté à votre volume de livraisons. Essai gratuit, sans carte bancaire, paiement Mobile Money. »
- H1 : *Des forfaits simples, payables en Mobile Money.* Cartes générées depuis les forfaits actifs (titre, prix FCFA, période, description, grâce, badge « Recommandé » si `mis_en_avant`), bouton « Commencer l'essai gratuit ». Paragraphe : *Tous les forfaits incluent l'ensemble des fonctionnalités, un nombre illimité de commandes et l'accès pour vos agents, coursiers et clients.* Note : *Les prix sont indiqués en FCFA, toutes taxes comprises. Le renouvellement se fait en un clic depuis votre espace.* + mini-FAQ tarifaire.

**FAQ `/faq`** (balisage `FAQPage`) — questions/réponses : *ORLA est-il gratuit ?* (essai 14 jours puis forfait) ; *Faut-il installer un logiciel ?* (non, navigateur, mobile compatible) ; *Comment payer mon abonnement ?* (MTN MoMo / Orange Money via Monetbil, activation immédiate) ; *Que se passe-t-il à la fin de l'abonnement ?* (période de grâce puis accès en lecture bloqué jusqu'au renouvellement, données conservées) ; *Mes coursiers ont-ils besoin d'un compte ?* (oui, compte coursier avec accès mobile) ; *Mes clients peuvent-ils passer commande eux-mêmes ?* (oui, compte client) ; *Comment sont calculés les frais de livraison ?* (grille par paire de zones, tarif par défaut par ville) ; *Puis-je gérer plusieurs villes ?* (oui, villes desservies, proposition de nouvelle ville) ; *Mes données sont-elles isolées ?* (oui, cloisonnement par entreprise) ; *Comment obtenir de l'aide ?* (contacts).

**Contact `/contact`** — formulaire (nom, entreprise, email, téléphone, message), coordonnées issues de `super_admin_contact`, mention Honowa Technologies (éditeur). **À propos `/a-propos`** : éditeur, mission (*outiller les entreprises de livraison d'Afrique centrale*), historique (issu de l'expérience Speedex). **Pages légales** : mentions légales (éditeur, hébergeur), politique de confidentialité (données collectées, finalités, durée, droits), CGU (compte, abonnement, essai, résiliation, disponibilité, responsabilité).

## 13. Phase 9 — Branding entreprise, paramètres et UI des espaces (R3)

- `resources/views/components/marque-entreprise.blade.php` (nom + logo) remplaçant « Speedex » dans `admin/menu/{admin,agent}.blade.php:8`, `client/menu/menu.blade.php:8`, `coursier/menu/menu.blade.php:8`, `admin/pages/home.blade.php:5` ; « Enregistrée par Speedex » → `entreprise()->name` ; `admin/pages/details_commande/result.blade.php:91,145,185-186,254` → enums + nom ; repli `'Speedex'` des boutiques (`boutiqueLie.blade.php`, `clients/info.blade.php`) → `$boutique->est_siege ? 'Dépôt '.entreprise()->name : …`. Titres `<title>` : `entreprise()->name . ' – ' . config('app.name')`.
- Page `entreprise/parametres` (`Entreprise\ParametresController`, `role:admin`) : nom, logo (upload `storage/app/public/logos/{id}.webp`, servi par une route `entreprise.logo` pour éviter le lien symbolique `public/storage` en FTP), téléphone, adresse, devise, pays, tarif par défaut, quartier siège, préférences (`parametres` json).
- Menu admin : « Villes desservies », « Abonnement », « Paramètres de l'entreprise » ; bandeaux essai/grâce ; correction des fautes récurrentes (« Acceuil » → « Accueil »).
- Vues `superadmin/*` : `Sa_logo()` pointe vers un fichier absent → logo plateforme `public/images/site/logo.svg`.

## 14. Phase 10 — Durcissement du schéma et nettoyage (R4)

Prérequis : `php artisan tenancy:audit` sans anomalie (depuis la console Super Admin ou le terminal cPanel).

1. `2025_11_01_000001_make_entreprise_id_not_null_and_foreign.php` : 19 tables → `nullable(false)->change()` + FK `→ entreprises (restrictOnDelete)` (InnoDB obligatoire, types BIGINT UNSIGNED identiques).
2. `…000002_add_composite_indexes.php` : `commandes (entreprise_id, statut)`, `(entreprise_id, id_client)`, `(entreprise_id, id_coursier)`, `(entreprise_id, id_agent)`, `(entreprise_id, date_livraison)` ; `clients/coursiers/agents/users (entreprise_id, statut)` ; `quartier (entreprise_id, id_ville)` ; `activity (entreprise_id, id_user, created_at)`.
3. `…000003_add_composite_uniques.php` : `zone (entreprise_id, id_ville, libelle)`, `quartier (entreprise_id, id_ville, libelle)`, `type_vehicule (entreprise_id, libelle)`, `vehicule (entreprise_id, immatriculation)`, `produits (entreprise_id, noms)`, `boutiques (entreprise_id, id_client, libelle)`, `coursiers/agents/clients/users (entreprise_id, telephone)`, `montant_livraison (entreprise_id, id_zone_colis, id_zone_livraison)`.
4. `…000004_cleanup_legacy.php` : drop `abonnement_transactions.id_client`, `entreprises.cni` ; suppression des vues/contrôleurs legacy restants.

Dette signalée hors périmètre : `activity.id_user` (BIGINT signé) et `paiement.id_client` (INT signé) ne peuvent recevoir de FK sans changement de type.

## 15. Tests (sqlite `:memory:`, `RefreshDatabase`)

Factories : `EntrepriseFactory`, `UserFactory` réécrite (`noms`, `id_type_utilisateur`, `entreprise_id`, états `admin()/agent()/coursier()/client()`), `Clients`, `Coursiers`, `Agents`, `Ville`, `Zone`, `Quartier`, `Boutiques`, `Commandes`, `Abonnement`, `AbonnementTransaction`, `SuperAdminUser`. Les factories tenant fixent `entreprise_id` explicitement.

| Fichier | Vérifie |
|---|---|
| `tests/Unit/Tenancy/EntrepriseScopeTest.php` | strict sans tenant → exception ; `User` non strict ; `withoutTenancy` ; `runAs` restaure ; `creating` remplit `entreprise_id` |
| `tests/Feature/Tenancy/IsolationCommandesTest.php` | pour chaque rôle de A : `index` ne liste que A ; `show/edit/update/destroy` d'une commande de B → 404 ; `attribuate_agent` avec agent de B → 422 |
| `tests/Feature/Tenancy/IsolationReferentielsTest.php` | zones, quartiers, produits, boutiques, coursiers, agents, clients, users, véhicules croisés → 404 ; eager-load `Zone::with('quartiers')` ne remonte que A |
| `tests/Feature/Tenancy/OwnershipPolicyTest.php` | client sur commande d'un autre client de A → 403 ; coursier non affecté → 403 ; mot de passe uniquement soi ; agent ne crée pas d'admin |
| `tests/Feature/Geographie/VillesDesserviesTest.php` | ville proposée visible par A seulement ; validation la rend globale ; fusion remappe zones/quartiers ; `id_ville` non desservie → 422 |
| `tests/Feature/Auth/LoginTest.php` | login résout le tenant ; entreprise suspendue → page dédiée ; `statut 0` → `home.error` ; logout vide `CurrentEntreprise` |
| `tests/Feature/Inscription/InscriptionTest.php` | crée entreprise + admin + pivot ville + quartier siège en transaction ; slug unique ; email pris → 422 ; connecté ; `essai_fin` = now + N ; `SAAS_ESSAI_JOURS=0` → `/abonnement` |
| `tests/Feature/Abonnement/GatingTest.php` | essai/active/grâce passent (bandeau), expirée bloque selon le rôle ; `/abonnement` et `/entreprise/parametres` accessibles bloqué ; superadmin non concerné |
| `tests/Feature/Abonnement/PaiementMonetbilTest.php` | `Http::fake()` : `payer` crée `waiting` + redirige ; signature invalide → 403 ; succès → `date_fin` prolongée depuis `max(now, date_fin)` ; rejeu idempotent ; annulé ; webhook sans CSRF |
| `tests/Feature/SuperAdmin/{GuardTest,PlansCrudTest,EntreprisesTest}.php` | guard, CRUD forfaits (validation, toggle, ordre), suspension/activation/activation manuelle/prolongation |
| `tests/Feature/Validation/UniqueParEntrepriseTest.php` | même libellé dans deux entreprises → OK ; doublon même entreprise → 422 ; `exists` scoppé rejette un id de B |
| `tests/Feature/Tarification/GrilleTarifaireTest.php` | création d'une zone → `n(n-1)/2` paires pour A seulement, montant = tarif ville ou entreprise |
| `tests/Feature/Site/SitePagesTest.php` | 200 sur chaque page, `<title>`/`description`/`canonical` présents, JSON-LD valide, `sitemap.xml` liste les pages, `robots.txt` bloque les espaces privés, `/tarifs` reflète les forfaits actifs |
| `tests/Unit/Support/PeriodeTest.php`, `tests/Feature/Console/TenancyAuditTest.php` | périodes (heure/jour/semaine/mois/année) ; audit détecte NULL et doublons |

## 16. Runbook de déploiement (FTP, mutualisé cPanel, sans SSH ni cron)

**Avant chaque release** : export MySQL complet (phpMyAdmin) + copie des fichiers ; vérifier `SHOW TABLE STATUS` (InnoDB partout, MySQL ≥ 5.7.8 / MariaDB ≥ 10.2 pour JSON, PHP 8.2) ; build local avec la même version PHP : `composer install --no-dev --optimize-autoloader`, `npm run build` ; téléverser `vendor/`, `app/`, `bootstrap/`, `config/`, `database/`, `resources/`, `routes/`, `public/` (dont `public/build`) ; **supprimer** `bootstrap/cache/{config,routes-v7,services,packages}.php` distants ; compléter `.env` (nouvelles clés) sans toucher `APP_KEY` ; mode maintenance (`storage/framework/down`).

**Exécution des migrations sans SSH** : terminal cPanel (`php artisan migrate --force`) si disponible ; sinon, dès R0, route `POST /ops/migrate` protégée par `APP_DEPLOY_TOKEN` (`hash_equals`) + allowlist IP, qui devient en R3 le bouton « Maintenance » de la console Super Admin (`auth:superadmin`) et la route à token est retirée.

**Document root** : le `error_log` montre un déploiement sous `public_html/subdomaines/speedex/` ; pour le site vitrine, pointer le domaine principal sur `public/` (ou `.htaccess` racine redirigeant vers `public/`), HTTPS forcé (redirection 301), `www` → apex.

**R1** : après migration, vérifier en phpMyAdmin `entreprise_id IS NULL` = 0 sur 19 tables, `entreprises` (1 ligne `active`, `slug`, `id_quartier_siege`), `type_utilisateur.1 = Administrateur` ; connexion admin existant ; création d'une commande ; `/superadmin/login`.
**R2** : vérifier le dédoublonnage des villes et les lignes `entreprise_villes`. **R3** : configurer les clés Monetbil (mode test), payer un forfait de test, vérifier `notify_url` joignable ; soumettre `sitemap.xml` à Search Console. **R4** : `tenancy:audit` en pré-vol, migration NOT NULL/FK seule à faible trafic.
**Rollback** : restauration du dump + des fichiers précédents (les `down()` existent mais ne sont pas le chemin de secours retenu).

## 17. Risques et pièges spécifiques à ce code

1. **`User` en scope strict = login impossible** (provider Eloquent interroge `users` avant résolution du tenant) → non strict sur `User` uniquement.
2. **`Model::insert()` contourne `creating`** → `entreprise_id` NULL puis violation NOT NULL en R4 ; imposer `entreprise_id` explicite (grille tarifaire).
3. **`withoutGlobalScope` ne se propage pas aux relations / eager loads** : côté Super Admin, toujours `runAs($entreprise)` ; `withoutTenancy()` réservé aux listes plates.
4. **Filtres manuels + `orWhere`** proscrits (les recherches actuelles en sont pleines).
5. **Ids venant de la requête HTTP** (`table_produit`, `id_quartier_associe[]`, `zones[]`) : `Regles::exists` scoppé, sinon 404 en plein `foreach` après écritures partielles → transactions obligatoires.
6. **`findOrFail` cross-tenant = 404** (souhaité) : tests en 404, vues avec `?->` sur relations potentiellement nulles.
7. **Types de clés** : `entreprises.id` BIGINT UNSIGNED, PK legacy INT UNSIGNED ; `entreprise_id` = `unsignedBigInteger`, `id_quartier_siege`/`id_ville` = `unsignedInteger` ; MySQL refuse une FK entre types différents.
8. **ENUM MySQL vs sqlite** : conversion en `string` + enum PHP ; `change()` efface les modificateurs non ré-énoncés.
9. **sqlite en tests** : constaté en phase 0, sqlite **applique** les clés étrangères déclarées par `Schema::table()->foreign()` (une factory `User` sans ligne `type_utilisateur` échoue) ; `dropForeign` lève, `after()` est ignoré, `json` = `text`. Les factories doivent donc créer leurs parents ; l'isolation reste garantie par scope + policies.
10. **`super_admin_api` chiffré** : migration idempotente, `APP_KEY` figé.
11. **Signature Monetbil** calculée sur tous les paramètres de l'URL de retour → référence en segment de chemin, journaliser les échecs.
12. **Contexte console/seeders/tinker** : `runAs`/`runWithoutTenancy` obligatoires ; migrations en `DB::table` uniquement.
13. **Singleton `CurrentEntreprise` en tests** : `SetCurrentEntreprise` doit `forget()` sans utilisateur ; `TestCase::setUp` réinitialise.
14. **Sessions** : `SESSION_DRIVER=file` sur mutualisé ; les deux guards partagent le cookie (clés `login_web_*` / `login_superadmin_*`, compatible).
15. **Villes partagées** : une fusion de doublons remappe des références dans plusieurs tables → transaction + journal ; une ville rejetée reste utilisable par l'entreprise proposante tant qu'elle n'est pas fusionnée.
16. **Données legacy et uniques composites** : quartiers créés à la volée par libellé et produits peuvent contenir des doublons de casse → `tenancy:audit` avant R4.
17. **Déploiement FTP** : `vendor/` et `public/build` construits localement avec la version PHP/Node cible ; caches `bootstrap/cache` périmés = erreurs silencieuses.
18. **Pas de queue ni de cron** : mails synchrones, états d'abonnement calculés, webhook Monetbil comme filet de sécurité.
19. **Ancien flux public `Sc-transaction.*`** actif entre R1 et R3 (adapté à l'entreprise 1) ; le retirer en R3 avec ses liens dans `superadmin/pages/transaction/*`.
20. **Site vitrine et thème Vuexy** : ne jamais charger `app-assets` (51 Mo) sur les pages publiques, sous peine de ruiner les Core Web Vitals.

## 18. Fichiers critiques

- `bootstrap/app.php`, `routes/web.php` — middlewares, alias, restructuration complète des groupes, retrait de `/teston` et `Sc-transaction.*`.
- `app/Tenancy/CurrentEntreprise.php`, `app/Models/Scopes/EntrepriseScope.php`, `app/Models/Concerns/BelongsToEntreprise.php` — cœur de l'isolation.
- `database/migrations/2025_10_02_000004_backfill_entreprise_id.php` — entreprise legacy, backfill des 19 tables, quartier siège, `est_siege`, référentiels.
- `app/Http/Middleware/{SetCurrentEntreprise,EnsureEntrepriseActive,EnsureUserHasRole}.php`, `app/Services/Abonnement/AbonnementService.php` — gating par entreprise.
- `app/Http/Controllers/Admin/CommandesController.php` — contrôleur de référence du pattern de refactor.
- `app/Services/Monetbil/MonetbilGateway.php`, `app/Http/Controllers/Facturation/*` — paiement par entreprise.
- `app/Services/Tenancy/EntrepriseProvisioner.php`, `app/Http/Controllers/Inscription/InscriptionController.php` — inscription.
- `app/Services/Geographie/VilleService.php`, `app/Models/Ville.php`, pivot `entreprise_villes` — référentiel partagé.
- `app/Http/Controllers/SuperAdmin/{EntrepriseController,PlanController,VilleController}.php` — console.
- `resources/views/site/**`, `app/Http/Controllers/Site/*`, `resources/css/site.css` — site vitrine.

Réutilisation de l'existant : `Sa_prochaine_date_paie` → `App\Support\Periode` ; `Sa_montant`, `Sa_Ladate`, `text_helper.php` conservés ; vues `superadmin/errors/*` → pages abonnement ; layouts Vuexy des espaces conservés ; `super_admin_contact` → contacts du site ; DataTables ajax des listes Super Admin conservés.

## 19. Vérification de bout en bout

1. **Local** : `composer install && cp .env.example .env && php artisan key:generate && php artisan migrate:fresh --seed && npm ci && npm run build && php artisan test` — tout vert ; `php artisan route:list` sans erreur.
2. **Scénario multi-entreprises (navigateur)** : visiter `/` (site vitrine, Lighthouse SEO ≥ 95, performance ≥ 90 mobile) → `/tarifs` affiche les forfaits seedés → `/inscription` crée l'entreprise A (admin connecté, bandeau essai) → paramétrer villes desservies, une zone, deux quartiers, un coursier, un client → créer une commande, l'attribuer, la livrer depuis le compte coursier → créer l'entreprise B dans un autre navigateur → vérifier que B ne voit aucune donnée de A (listes vides, URL d'une commande de A → 404) → même libellé de zone dans A et B accepté.
3. **Abonnement** : passer `SAAS_ESSAI_JOURS=0`, se reconnecter en A → redirection `/abonnement` ; agent de A → page « expiré » ; payer un forfait en mode test Monetbil (`Http::fake()` en test, sandbox en préprod) → `date_fin` prolongée, accès rétabli ; suspendre A depuis `/superadmin` → page « suspendu ».
4. **Super Admin** : `/superadmin/login` avec le guard ; CRUD d'un forfait (apparition sur `/tarifs` et `/abonnement`) ; validation d'une ville proposée par A et fusion avec un doublon ; activation manuelle d'un abonnement ; audit tenancy vide.
5. **Migration de la production (copie)** : restaurer un dump de prod en local, dérouler R0→R4, vérifier compteurs (`commandes`, `clients`, `coursiers` identiques), connexion des utilisateurs existants, quartier « Siège Speedex », boutiques `est_siege`, paiements `qui_paie = entreprise`, `tenancy:audit` vide.
6. **Sécurité** : `/teston` → 404 ; `POST /superadmin/Sa-transaction` sans guard → 302 login ; signature Monetbil altérée → 403 ; `robots.txt` interdit les espaces privés ; secrets Monetbil chiffrés en base.

---

## 20. Exécution de la phase 0 — déroulé détaillé (demande : « commence la phase 0 puis arrête-toi »)

Périmètre strict : stabilisation sans changement fonctionnel visible. Rien de la phase 1 (pas de colonne `entreprise_id`, pas de renommage de `Super Admin`). Branche : `claude/vigilant-knuth-5xhg8j`. Environnement local vérifié : PHP 8.4, Composer, Node 22, Packagist joignable (`composer install` possible), `vendor/` absent.

### 20.1 Installation locale et état de référence
1. `composer install` (plugins désactivés dans cette session : sans incidence), `cp .env.example .env` une fois le fichier créé (§20.5), `php artisan key:generate`, `.env` local sur sqlite (`DB_CONNECTION=sqlite`, `database/database.sqlite`).
2. Relevé de l'état initial : `php artisan route:list` (échec attendu : contrôleur `Client\ClientsController` absent), `php artisan test` (échec attendu), `composer dump-autoload -o` (avertissements PSR-4 attendus sur les 19 dossiers de modèles).

### 20.2 Autoload et modèles (commit « refactor: aligne les namespaces des modèles »)
1. Renommer en deux temps (`git mv x tmp && git mv tmp X`) : `agents→Agents`, `boutiques→Boutiques`, `clients→Clients`, `commandes→Commandes`, `coursiers→Coursiers`, `details_commande→Details_commande`, `details_zone→Details_zone`, `informations_personnels→Informations_personnels`, `montant_livraison→Montant_livraison`, `paiement→Paiement`, `point_relais→Point_relais`, `produits→Produits`, `quartier→Quartier`, `stock→Stock`, `typeClient→TypeClient`, `typeUtilisateur→TypeUtilisateur`, `ville→Ville`, `zone→Zone`.
2. Script de remplacement (sed, sur `app/`, `database/`, `resources/`, `routes/`, `tests/`) de chaque `App\Models\<minuscule>\X` vers la casse déclarée, **y compris dans les chaînes de relations** (sinon PSR-4 ne trouve plus le fichier sur Linux). Volumes : 42 `TypeUtilisateur`, 30 `Coursiers`, 29 `Commandes`, 28 `Ville`, 28 `Clients`, 25 `Agents`, 20 `Informations_personnels`, 19 `Quartier`, 13 `Zone`, 13 `Boutiques`, 10 `TypeClient`, 9 `Montant_livraison`, 8 `Produits`, 8 `Details_zone`, 8 `Details_commande`, 6 `Stock`, 5 `Paiement`, 4 `Point_relais`.
3. Supprimer `app/Models/users/Users.php` : dans les 30 fichiers qui l'importent (29 `use`, 38 `Users::`, plus `resources/views/admin/templates/template.blade.php:2` et `database/seeders/DatabaseSeeder.php:34`), `use App\Models\users\Users;` → `use App\Models\User;` et `\bUsers\b` → `User` (vérifié : aucun de ces fichiers n'importe aussi `App\Models\SuperAdmin\User`, pas de conflit d'alias).
4. `app/Models/User.php` : `$fillable` = `noms, email, password, telephone, id_type_utilisateur, id_agent, id_coursier, id_client, id_ville, statut` ; relation `activities()` → `Activity::class`. Corriger `Activity::user()` → `User::class`, `Ville::vehicules()` → `\App\Models\Vehicule::class`, `Stock::point_relai()` → clé `id_point_relais`, `SuperAdmin\User` (retirer `use App\Models\TypesUser;`), `SuperAdmin\Client::$fillable` (`name`, `date_fin`), `SuperAdmin\Abonnement::$fillable` (`montant`, `periode_grace`, `statut`).
5. Supprimer `resources/views/admin/pages/paiement/Paiement.php` et les fichiers vides `id_ville`, `libelle`, `save()`.
6. Contrôle : `composer dump-autoload -o` sans avertissement ; `grep -rn "Models\\\\[a-z]" app resources database routes` vide (hors `App\Models\SuperAdmin`).

### 20.3 Bootstrap et middlewares (commit « chore: nettoie la pile de middlewares »)
1. `bootstrap/app.php` : supprimer le bloc `prepend([...])` (doublon de la pile globale par défaut de Laravel 12 : `TrustProxies` sans proxy, `TrimStrings` avec les mêmes exceptions, `PreventRequestsDuringMaintenance` sans exception) et le bloc `append([Filter, Coursier_filter, Admin_filter])` (no-op) ; conserver uniquement l'alias `Check_Sa_Client_Error`.
2. `config/sanctum.php:63-64` → `Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class` et `Illuminate\Cookie\Middleware\EncryptCookies::class`.
3. Supprimer `app/Http/Middleware/{Filter,Coursier_filter,Admin_filter,Authenticate,RedirectIfAuthenticated,TrustHosts,ValidateSignature,EncryptCookies,VerifyCsrfToken,TrustProxies,TrimStrings,PreventRequestsDuringMaintenance}.php` (plus aucune référence après 1 et 2 ; `grep` de contrôle avant suppression).
4. `bootstrap/providers.php` : **non créé** en phase 0 (les providers sont déjà chargés via `config/app.php` ; le double enregistrement serait un risque sans bénéfice).

### 20.4 Routes, contrôleurs morts, fichiers dangereux (commit « fix: retire les routes cassées et dangereuses »)
1. `routes/web.php` : supprimer `/teston` (l. 157-161) ; `Auth::routes(['register' => false])` ; supprimer `Route::resource('Clientclients', …)` (contrôleur absent), `Sa-api-ajax` et `Sa-api.recap_create` (méthodes absentes), `Route::resource('details_zone', Admin\Details_zoneController)` du groupe `admin` (doublon de nom ; la version `multi` reste, utilisée par `multi/pages/coursiers/info.blade.php`), `typeclient` et `typeutilisateur` (stubs, aucune vue ne les référence).
2. Supprimer : `app/Http/Controllers/Auth/{RegisterController,VerificationController}.php`, `resources/views/auth/{register,verify}.blade.php`, `resources/views/{welcome,home}.blade.php` (démo, jamais rendus) ; contrôleurs sans route `app/Http/Controllers/Admin/{CoursiersController,ZoneController,Informations_personnelsController,PaiementController,Details_zoneController,TypeClientController,TypeUtilisateurController}.php` et leurs vues `resources/views/admin/pages/{coursiers,zones}/*` (vérifié : référencées uniquement par ces contrôleurs). `layouts/app.blade.php` garde son `@if (Route::has('register'))`, donc reste valide.
3. `app/Helpers/system_helper.php` : `Dossier()` → `'AGENT' => 'admin'` ; `filter()` → `'routeur' => 'home.admin'`, `'superviseur_ville' => 'home.admin'`.
4. `public/` : supprimer `app-assets/images/logo/FacebookToolkit-master.zip`, `app-assets/data/ajax.php`, `app-assets/data/fullcalendar/php/*.php`, les 14 `.DS_Store`, `public/error_log` ; `git rm --cached .ftpquota` ; `.gitignore` += `public/error_log`, `.ftpquota`, `.DS_Store`, `/database/database.sqlite`.
5. `public/index.php` et `artisan` : passage au squelette Laravel 12 (`$app->handleRequest(Request::capture())`, `$app->handleCommand(new ArgvInput)`) — équivalent fonctionnel, supprime la dépendance au Kernel HTTP legacy.

### 20.5 Migrations framework, configuration, traductions (commit « feat: migrations framework, configuration et traductions fr »)
1. `database/migrations/2025_10_01_000001_create_framework_tables.php` : `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `personal_access_tokens`, chacune sous `if (! Schema::hasTable(...))` (idempotent sur une prod partielle).
2. `2025_10_01_000002_add_email_verified_at_and_doit_changer_mdp_to_users_table.php` : `email_verified_at timestamp nullable`, `doit_changer_mdp boolean default false`, `down()` réel.
3. `.env.example` : `APP_NAME=ORLA`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`, `APP_TIMEZONE=Africa/Douala` (lu par `config/app.php` à la place du `UTC` figé : échéances d'abonnement en heure locale), `APP_LOCALE=fr`, `LOG_*`, `DB_*` (mysql par défaut, bloc sqlite commenté), `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`, `MAIL_*`, `SAAS_ESSAI_JOURS=14`, `SAAS_TENANCY_STRICT=true`, `SAAS_ENTREPRISE_LEGACY_NOM=Speedex`, `SAAS_TARIF_DEFAUT=1000`, `SITE_CONTACT_EMAIL`, `SITE_CONTACT_TEL`, `APP_DEPLOY_TOKEN=`.
4. `config/saas.php` (`essai_jours`, `tenancy_strict`, `entreprise_legacy` nom/slug, `tarif_defaut`, `devise`, `pays`, `prefixe_telephone`, `telephone_regex` par pays) et `config/site.php` (nom, baseline, contacts, réseaux, url canonique, image OG). Lus dès la phase 0 par rien d'autre que les tests de configuration ; ils fixent le contrat des phases suivantes.
5. `lang/fr/{auth,pagination,passwords,validation}.php` (traductions françaises complètes ; sans elles, `locale=fr` affiche des clés brutes comme `validation.required`).
6. Vérification sqlite : `php artisan migrate:fresh` sur sqlite doit passer de bout en bout (FK ignorées, `after()` ignoré) ; toute migration legacy qui casse est corrigée a minima (jamais en changeant le schéma MySQL résultant).

### 20.6 Seeders et factories (commit « feat: seeders idempotents et factories »)
1. `database/seeders/DatabaseSeeder.php` (namespace `Database\Seeders`) → `ReferentielSeeder`, `SuperAdminSeeder`, puis `DemoSeeder` seulement si `app()->environment('local', 'testing')`.
2. `ReferentielSeeder` (`firstOrCreate`) : type_utilisateur `1 Super Admin, 2 Agent, 3 Coursier, 4 Client` (libellés inchangés), type_client `1 Entreprise, 2 Simple`, villes `Douala/Dla`, `Yaoundé/Yde`.
3. `SuperAdminSeeder` (renommage de `SuperAdmin.php`, idempotent) : opérateur `superadmin@example.com`, ligne `super_admin_api` Monetbill, contacts.
4. `DemoSeeder` : admin `test@example.com / 11111111` (déplacé depuis l'ancien `DatabaseSeeder`), un agent, un coursier, un client « Simple » avec leurs utilisateurs liés, et une licence `super_admin_client` active avec abonnement valide (sans elle `Check_Sa_Client_Error` bloque tout en local).
5. `database/factories/UserFactory.php` réécrite (`noms`, `email`, `telephone`, `password`, `id_type_utilisateur = 1`, `statut = 1` ; états `agent()`, `coursier()`, `client()`), plus `Database\Factories\SuperAdmin\{ClientFactory,AbonnementFactory}` pour les tests de licence.

### 20.7 Socle de tests (commit « test: socle de tests de fumée »)
1. `tests/TestCase.php` façon Laravel 12 (`abstract class TestCase extends BaseTestCase {}`) ; supprimer `tests/CreatesApplication.php` ; `tests/Unit/ExampleTest.php` conservé.
2. `tests/Feature/BootTest.php` : `GET /login` → 200 ; `GET /superadmin/Sa-login` → 200 ; `GET /teston` → 404 ; `GET /register` → 404 ; `GET /` sans licence → redirection vers `SuperAdmin.empty_client`.
3. `tests/Feature/SchemaTest.php` (`RefreshDatabase`) : tables `users`, `password_reset_tokens`, `sessions`, `cache`, `jobs`, colonne `users.email_verified_at`.
4. `tests/Feature/SeedersTest.php` : `$this->seed()` → 4 types utilisateur, 2 types client, opérateur super admin, admin de démo.
5. `tests/Feature/AdminSmokeTest.php` : licence active (factories) + admin connecté → `GET /home` redirige vers `/admin` ; `GET` 200 sur `admin`, `admin/users`, `admin/clients`, `admin/agents`, `admin/commandes`, `admin/commandes/create`, `admin/produits`, `admin/quartier`, `admin/ville`, `multi/coursiers`, `multi/zone`, `multi/vehicule`, `multi/type_vehicule`. Ce test exerce les namespaces corrigés et les relations des modèles sur toute la couche admin ; un échec dû à un bogue préexistant est corrigé s'il est trivial, sinon consigné dans le rapport final.
6. `tests/Feature/CoursierClientSmokeTest.php` : coursier et client de démo connectés → tableaux de bord 200.

### 20.8 Documentation et livraison
1. `README.md` : stack `Laravel 12 / PHP 8.2`, étapes d'installation (`.env.example` réel, `--seed` → `DemoSeeder` en local), identifiants de démonstration ; `docs/plan-multi-tenant.md` synchronisé avec cette section.
2. Vérifications finales : `composer dump-autoload -o` silencieux, `php artisan route:list` OK, `php artisan migrate:fresh --seed` OK (sqlite), `php artisan test` vert, `php artisan config:clear` ; `vendor/bin/pint --test` uniquement sur les fichiers créés ou modifiés (pas de reformatage global).
3. Commits par lot (§20.2 → §20.7), `git push -u origin claude/vigilant-knuth-5xhg8j`, pas de pull request (non demandée). Rapport final : ce qui a été fait, résultats des tests avec leur sortie, écarts éventuels. **Arrêt après la phase 0** : aucune tâche de la phase 1 n'est entamée.
