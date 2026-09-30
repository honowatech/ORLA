# Audit qualité : Speedex / ORLA

*Audit réalisé le 29/09/2026 sur la branche `claude/fix-routes` (le code de `main` avec les corrections de routes). Il porte sur le code, l'architecture et la structure. Les références sont données en `fichier:ligne`.*

## 1. Synthèse

L'application fonctionne, mais elle a été construite par copier-coller entre les espaces (admin, client, coursier, multi, super admin). On y trouve peu d'abstractions, aucun test et aucune vérification d'appartenance des données. Il en résulte **des failles de sécurité graves** et un coût de maintenance élevé : chaque correction doit être répétée à 3 ou 4 endroits.

| Axe | Note | Commentaire |
|---|---|---|
| Sécurité | 🔴 2/10 | Prise de compte possible, abonnements gratuits sans authentification, XSS stocké |
| Architecture | 🔴 3/10 | Contrôle d'accès copié 244 fois, logique métier dans les contrôleurs et les helpers |
| Duplication | 🔴 2/10 | Contrôleurs identiques à 85-96 % entre espaces, vues de 600+ lignes dupliquées |
| Données | 🟠 4/10 | Clés étrangères partielles, montants en `float`, 3 modèles pour la table `users` |
| Présentation | 🟠 4/10 | Thème Vuexy de 51 Mo à peine utilisé, JS inline, requêtes dans les layouts |
| Outillage / tests | 🔴 1/10 | 0 test réel, 128 fichiers PHP sur 171 non conformes à Pint, seeders inutilisables |

**Chiffres clés :**
- 14 000 lignes de PHP dans `app/` ;
- 135 vues pour 28 000 lignes ;
- 31 modèles ;
- 190 + 54 + 45 vérifications de rôle faites à la main ;
- 34 méthodes de contrôleur vides ;
- 0 `DB::transaction`.

---

## 2. Sécurité (à traiter en priorité)

### 🔴 Critique

1. **Changement du mot de passe de n'importe quel compte.**
   - `Client/PasswordController.php:163-183` vérifie l'ancien mot de passe sur `Auth::user()`, mais modifie `Users::findOrFail($id)`, où `$id` vient de l'URL.
   - Un client connecté peut donc changer le mot de passe du Super Admin.
   - Même code dans `Coursier/PasswordController.php` (~l.182).
2. **Abonnement gratuit sans authentification.**
   - `routes/web.php:21` expose `Route::resource('Sc-transaction')` hors de tout middleware.
   - `SuperAdmin/TransactionController@store` (l.93-141) n'appelle pas `check_superadmin()`.
   - Avec `methode=application`, elle enregistre une transaction `success` et prolonge l'abonnement du client, sans aucun paiement.
   - `create`, `show` et `checkpay` ne sont pas protégées non plus.
3. **Vérification Monetbil non bloquante.**
   - `app/Helpers/super_admin_helper.php:110` fait `if (!checkSign(...)) { echo 'erreur'; }`, puis le traitement continue.
   - `checkpay` (route GET publique) ne vérifie ni le montant, ni `payment_ref`, ni le lien avec la transaction locale : un paiement réussi peut être rejoué sur une autre transaction.
4. **IDOR (accès aux données d'un autre utilisateur par son identifiant).**
   - `Client/UsersController.php:192` et `Coursier/UsersController.php:192` : modification du profil de n'importe quel utilisateur.
   - `Client/CommandesController.php:562, 601, 647, 689` : lecture, modification et changement de statut de n'importe quelle commande.
   - `Coursier/CommandesController.php:227` et `Coursier/ClientsController.php:222-331` : même problème.

### 🟠 Élevé

5. **XSS stocké (script injecté en base puis exécuté dans le navigateur).**
   - La `description` de commande, saisie par le client, est affichée sans échappement (`{!! !!}`) dans `admin/pages/commandes/info.blade.php:441`, `coursier/pages/home.blade.php:82`, `client/pages/home.blade.php:78`, etc.
   - Les messages flash sont construits en HTML avec des données saisies (`Admin/ClientsController.php:339` : `$client->noms`), puis affichés via `{!! session('message') !!}` dans les 4 layouts.
   - On compte 94 `{!! !!}` au total.
6. **Montant de livraison fourni par le client** : `Client/CommandesController.php:490, 652`, avec pour seule règle `numeric|min:50`.
7. **Contrôle des rôles par liste noire.** Les contrôleurs laissent passer tout rôle qui n'est ni `coursier` ni `client` (par ex. `superviseur_ville`). Un **agent** peut créer un utilisateur Super Admin (`Admin/UsersController.php:138`, où `type_user` est seulement `required`).
8. **Réinitialisation à un mot de passe fixe** : `'11111111'` dans `Admin/PasswordController.php:185`, déclenchée par une route `DELETE`.

### 🟡 Moyen

9. **Super Admin authentifié uniquement par la session** (`check_superadmin()`).
   - Pas de guard Laravel.
   - Pas de `session()->regenerate()` à la connexion (`SuperAdminController.php:50`), ce qui permet la fixation de session.
   - Le modèle complet, hash du mot de passe compris, est stocké en session.
   - Pas de limitation des tentatives de connexion.
10. **Monetbil.**
    - TLS désactivé : `CURLOPT_SSL_VERIFYPEER` et `CURLOPT_SSL_VERIFYHOST` à 0 (`monetbil.php:460, 688`).
    - La signature MD5 est comparée avec `==`.
    - La clé et le secret sont affichés en clair dans `superadmin/pages/parametre/index.blade.php:77, 87`.
11. **Inscription publique active.** `Auth::routes()` expose la page d'inscription, mais `RegisterController::create` écrit une colonne `name` qui n'existe pas.
12. **Route GET qui écrit en base** : `Sc-transaction.checkpay`.
13. **`$visible` expose les secrets.** Il contient `password` (`users/Users.php:12`, `SuperAdmin/User.php:19`) ainsi que `password`, `secret` et `key` (`SuperAdmin/Api.php:17`).

✅ **Points sains :**
- CSRF actif, sans exception.
- Pas de SQL brut (`DB::raw`, `whereRaw`).
- Pas de `create($request->all())`.
- Validation présente dans la plupart des `store` et `update`.
- Pas d'upload de fichiers.

---

## 3. Architecture et code

### 3.1 Contrôle d'accès dupliqué au lieu de middleware ou policies
- Le bloc suivant est répété **190 fois** dans 29 contrôleurs :
  ```php
  $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
  if ($statut == 0) ...; if ($type == 'SUPER ADMIN' || $type == 'AGENT') {} else if ...
  ```
  Chaque occurrence fait une requête SQL, et beaucoup contiennent des branches `if` vides.
- Il existe deux autres variantes : `filter([...])` (54 appels, renvoie la **chaîne** `'true'`) et `check_superadmin()` (45 appels).
- `filter()` redirige vers `home.routeur` et `home.superviseur_ville`, des routes qui n'existent pas, ce qui provoque une erreur 500.
- Trois middlewares globaux sont vides : `Filter`, `Admin_filter` et `Coursier_filter` (`bootstrap/app.php:25-29`).
- `Check_Sa_Client_Error` exécute `Client::get()`, puis une boucle avec lazy loading, **à chaque requête**, sans cache.

**Cible :**
- un middleware `role:admin,agent` sur les groupes de routes ;
- des Policies pour la propriété des ressources (`CommandePolicy`, `UserPolicy`) ;
- un guard `superadmin` ;
- le statut d'abonnement mis en cache.

### 3.2 Duplication massive
| Paire | Similarité |
|---|---|
| `Client/PasswordController` ↔ `Coursier/PasswordController` | ~96 % |
| `Admin/ClientsController` ↔ `Coursier/ClientsController` | ~94 % |
| `Admin/CommandesController` (801 l.) ↔ `Client/CommandesController` (751 l.) | ~85 % |
| `Client/UsersController` ↔ `Coursier/UsersController` | ~85 % |
| `admin/.../commandes/create.blade.php` ↔ `client/...` (670 l.) | ~60 % |
| `commandes/script/bloc_script.blade.php` admin ↔ client (420 l.) | ~95 % |

- Le bloc « créer le quartier Speedex s'il n'existe pas » est copié **16 fois**.
- Il y a 5 classes de base `Controller`.
- **Code mort** :
  - `Admin/ZoneController`, `Admin/CoursiersController` et `Admin/Informations_personnelsController` ne sont référencés par aucune route (947 lignes) ;
  - `Admin/PaiementController` n'est pas routé ;
  - 34 méthodes sont vides, dont une partie est exposée par `Route::resource` et renvoie une page blanche.

### 3.3 Logique métier dans les contrôleurs
- **Machine d'états des commandes cachée dans `destroy()`.** `Admin/CommandesController.php:718-798` s'appuie sur des indices magiques (`$depart = 1/2/3`), et une variable n'y est pas définie pour certains statuts. Le même code est copié dans Client et Coursier.
- **`store()` de commande fait plus de 200 lignes** (`Admin/CommandesController.php:340-548`) : création implicite d'un client par découpage du nom, création de quartiers, commande, puis détails. Tout cela sans transaction.
- **Tarif de livraison** (valeur par défaut `1000`, recherche par zones) : dupliqué 3 fois.
- **Abonnements et paiements dans des fonctions globales.**
  - Fonctions concernées : `Sa_prochaine_date_paie`, `Sa_check_abonnement` (qui renvoie `'true'` ou `'login'`) et `Sa_pay`.
  - L'état passe par `$_SESSION['api']`.
  - `Sa_pay()` est appelé **sans `return`** (`TransactionController.php:146`), si bien que la redirection vers Monetbil est perdue.
- **Bibliothèque Monetbil copiée dans `app/Http/Controllers/SuperAdmin/monetbil/`**, avec ses exemples, son CSS et ses images, et chargée par `require_once`.

**Cible :**
- des enums `CommandeStatut`, `Role` et `Statut` ;
- des services `CommandeService`, `TarifLivraison` et `AbonnementService` ;
- un `MonetbilGateway` dans `app/Services` ;
- `DB::transaction()` sur la création de commande et sur le paiement.

### 3.4 Performance
- `Admin/CommandesController@index` (l.69-88) charge **toutes** les commandes deux fois, les croise par une double boucle PHP (O(n×m)), puis pagine.
- `boutiqueLie` refait un `findOrFail` dans une boucle.
- Chaque layout admin exécute `Users::with(...)->findOrFail(Auth::id())` (`admin/templates/template.blade.php:124`).

### 3.5 Valeurs magiques et nommage
- **Statuts en chaînes dispersées** : `'attente'` (28 fois), `'encours'` (22), `'livre'` (22), `'annulee'` (19), `'attribue'` (18) et `'echoue'` (17). Le tableau `$statuts_norm` est redéclaré 3 fois.
- **Rôles comparés à un libellé en base** via `strtoupper`.
- **Autres valeurs en dur** : `type_client = 2`, `id_ville = 1`, `Monetbil::setUser(12)`, et un logo pointant vers `focus-rent.honowa.com`.
- **Nommage incohérent** :
  - dossiers de modèles en minuscules (`app/Models/users/`) alors que les namespaces sont en PascalCase (`App\Models\Users`) ;
  - classes en snake_case (`Details_zoneController`, `Check_Sa_Client_Error`) ;
  - pluriel et singulier mélangés (`Users`, `Vehicule`) ;
  - français et anglais mélangés ;
  - URLs `/NonAutorisé` et `Coursierusers`.
- **Casse de classe fausse** : la relation `'App\Models\activity'` (`users/Users.php:44`, `User.php:85`) vise une classe qui s'appelle `Activity`. Elle échouera sur Linux.

### 3.6 Réponses et erreurs
- **144 messages HTML construits dans les contrôleurs.** La présentation est couplée au code et crée un risque XSS.
- **58 `redirect()->back()`**.
- Aucune FormRequest ni exception métier.
- Les endpoints AJAX renvoient des entiers ou du HTML au lieu de JSON.

---

## 4. Données

1. **Trois modèles pour la table `users`.**
   - `App\Models\User` sert à l'authentification, mais son `$fillable` contient `name`, une colonne inexistante.
   - `App\Models\users\Users` est utilisé partout ailleurs.
   - `App\Models\SuperAdmin\User` pointe vers une autre table, `super_admin`.
   - Il faut un modèle unique, et un guard dédié pour le Super Admin.
2. **`$visible` mal utilisé.**
   - Aucun modèle n'y inclut `id`, et plusieurs y incluent `password`.
   - Les listes ne correspondent pas au schéma : `Activity.action`, et `nom` ou `salaire` sur les modèles SuperAdmin.
   - Il vaut mieux utiliser `$hidden`.
3. **Relations cassées ou fragiles.**
   - `Stock.php:26` : `id_point_relai` au lieu de `id_point_relais`.
   - `SuperAdmin/User.php:10` : `use App\Models\TypesUser`, classe inexistante.
   - Toutes les relations sont déclarées par des chaînes au lieu de `::class`.
   - Un modèle `Paiement.php` est égaré dans `resources/views/admin/pages/paiement/`.
4. **Clés étrangères.**
   - Celles de 2023 sont bien déclarées.
   - En revanche, les `->integer(...)->constrained()` des migrations de 2024 **ne créent pas de FK** (paiement, activity, users.id_ville, qui vise en plus une table `villes` inexistante).
   - Aucune FK ni index sur :
     - `commandes.id_agent` ;
     - `vehicule.*` ;
     - `super_admin_transaction.*` ;
     - `commandes.statut` et `date_commande`.
   - Les types de clés sont incompatibles (`integer` d'un côté, `bigint` de l'autre).
5. **Types de colonnes.**
   - Montants en `float` : `commandes`, `details_commande`, `paiement`, `montant_livraison`, `stock`. Il faut `decimal(12,2)`.
   - Colonnes en PascalCase (`Prenoms`, `Quantite_en_stock`).
   - Données dénormalisées : `boutiques.quartier` en texte à côté de `id_quartier`.
6. **Migrations.**
   - Le nom `add_columb_...` est répété et mal orthographié.
   - Les `down()` sont vides.
   - Les noms de tables mélangent singulier et pluriel.
7. **Seeders et factories inutilisables.**
   - `DatabaseSeeder` n'a pas de namespace et déclenche une MassAssignmentException.
   - `UserFactory` est celle de Laravel par défaut, avec des colonnes qui n'existent pas.
   - Des mots de passe `11111111` sont en dur.
   - **Il est aujourd'hui impossible de monter une base de développement sans copier la production.**

---

## 5. Présentation (vues et assets)

- **5 layouts différents**, admin, client, coursier, superadmin et `layouts/app`, diffèrent de 220 à 235 lignes chacun. Il faudrait un seul layout avec un menu par rôle.
- **113 blocs `@php`**, des requêtes Eloquent dans les layouts et des relations chargées dans des boucles (N+1).
- **JS et CSS inline.**
  - 106 vues contiennent `<script>`, 11 contiennent `<style>`, et on compte 239 attributs `style=""`.
  - Il y a 82 appels AJAX jQuery écrits à la main, avec un helper `responses_ajax()` qui injecte le HTML de la réponse via `innerHTML`.
- **Vue et le build Vite sont quasi inutilisés.**
  - `@vite` n'est chargé que par `layouts/app` (les pages d'authentification).
  - Vite fournit Bootstrap 5, alors que le thème est en Bootstrap 4.
  - Font Awesome est chargé deux fois.
- **`public/app-assets` : thème Vuexy de 51 Mo et 1 756 fichiers, dont environ 93 chemins sont réellement référencés.**
  - `css-rtl/` (3,6 Mo) n'est jamais utilisé.
  - `flag-icon-css` pèse 9,3 Mo pour un seul drapeau.
  - `images/` pèse 14 Mo, en grande partie inutilisé.
  - Il contient une archive `FacebookToolkit-master.zip` (1,6 Mo) et 14 fichiers `.DS_Store`.
- **Fichiers parasites** : `id_ville`, `libelle` et `save()` à la racine (fichiers vides issus de commandes ratées), ainsi que `.ftpquota`.

---

## 6. Outillage et tests

| Élément | État |
|---|---|
| Tests | Seuls `ExampleTest` (Unit et Feature) existent, avec 0 test métier *(corrigé en phase 1 : 28 tests)* |
| Style (Pint) | **128 fichiers PHP sur 171** non conformes *(corrigé : tout le code est formaté, vérifié en CI)* |
| Analyse statique | Aucune (ni PHPStan ni Larastan) *(corrigé : Larastan niveau 1 avec baseline)* |
| CI | Aucune, en dehors du build `preprod` (PR #1) *(corrigé : Pint, Larastan et PHPUnit sur chaque push)* |
| `.env.example` | Absent *(corrigé)* |
| README | Correct fonctionnellement, sans procédure d'installation *(corrigé)* |

---

## 7. Plan de remédiation

### Suivi (au 30/09/2026)

| Phase | État | Branche |
|---|---|---|
| Phase 0 : sécurité | ✅ Faite, plus 2 failles supplémentaires (agent vers Super Admin, mot de passe fixe) ; 12 tests de non-régression | `claude/security-phase0` |
| Phase 1 : socle | ✅ Faite : middleware de rôle, code mort supprimé, seeder et `.env.example`, Pint, Larastan (niveau 1 + baseline), CI ; 28 tests | `claude/phase1-socle` |
| Phase 2 : structure | À faire. Priorité : la casse des namespaces de modèles (environ 420 des 465 erreurs de la baseline Larastan) | |
| Phase 3 : présentation | À faire | |

Écarts par rapport au plan initial :
- En phase 1, l'appartenance des ressources est vérifiée par des helpers (`commandeDuClient`, `commandeDuCoursier`, `protegerSuperAdmin`) et non par des Policies. Le passage aux Policies se fera avec la fusion des contrôleurs (phase 2).
- Bugs existants repérés par Larastan et laissés dans la baseline, pour la phase 2 :
  - `SuperAdmin\TransactionController@update` utilise `$client`, jamais défini ;
  - `Client\UsersController@update` appelle une classe `Client` inexistante ;
  - plusieurs variables ne sont définies que dans certaines branches (`$depart`, `$produits`, `$reponse`).


### Phase 0 : sécurité immédiate (1 à 2 jours)
1. `PasswordController` Client et Coursier : n'agir que sur `Auth::user()` et ignorer l'`$id` de l'URL.
2. `UsersController` Client et Coursier, `CommandesController` Client et Coursier, `Coursier/ClientsController` : vérifier l'appartenance (`where('id_client', auth()->id())`), ou retourner 403.
3. `Sc-transaction` : protéger `store`, `create`, `show` et `checkpay` ; interdire `methode=application` hors Super Admin ; rendre `checkSign` bloquant ; vérifier montant, référence et statut `waiting`.
4. Remplacer `{!! !!}` par `{{ }}` pour les données utilisateur et les messages flash (messages sans HTML, mise en forme dans la vue).
5. Recalculer le montant de livraison côté serveur.
6. Changer tous les mots de passe réinitialisés à `11111111` et `12345678`.

### Phase 1 : socle (1 à 2 semaines)
- Ajouter `.env.example`, un seeder de développement fonctionnel et des factories.
- Appliquer Pint (dans un commit dédié), ajouter Larastan niveau 1 à 3 et une CI GitHub Actions (Pint, Larastan, tests).
- Écrire les premiers tests Feature : accès par rôle, propriété des commandes, flux d'abonnement.
- Remplacer le contrôle d'accès copié par un middleware de rôle et des Policies, et supprimer les 3 middlewares vides.
- Supprimer le code mort (contrôleurs non routés, méthodes vides, restreindre les `Route::resource` avec `->only()`) et les fichiers parasites.

### Phase 2 : structure (2 à 4 semaines)
- Créer des enums (`CommandeStatut`, `Role`) et une machine d'états des commandes dans un service.
- Créer `CommandeService` et `TarifLivraison`, avec `DB::transaction` ; ajouter les FormRequests.
- Fusionner les contrôleurs dupliqués : un contrôleur par ressource, le rôle étant géré par des policies et des scopes.
- Unifier les modèles : un seul `User`, des namespaces alignés sur les dossiers (PascalCase), des relations en `::class`, `$hidden` et `$casts`.
- Créer une migration corrective : FK manquantes, index (`statut`, dates, `id_*`), montants en `decimal`.
- Déplacer Monetbil dans `app/Services/Payment`, et réactiver la vérification TLS.

### Phase 3 : présentation (en continu)
- Un layout unique avec des composants Blade (formulaire de commande, tableaux, modales).
- Extraire le JS dans `resources/js` et le compiler avec Vite ; faire répondre les endpoints AJAX en JSON.
- Élaguer `public/app-assets` (objectif : moins de 10 Mo) et choisir une seule version de Bootstrap.
