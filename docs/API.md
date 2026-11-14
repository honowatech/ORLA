# API mobile Speedex — v1

API REST JSON pour les applications mobiles **coursier** et **client**.
Base : `https://<domaine>/api/v1` · Authentification : jeton Bearer (Laravel Sanctum).

## Conventions

- Envoyer `Accept: application/json` (l'API répond en JSON dans tous les cas).
- Authentification : `Authorization: Bearer <token>` sur toutes les routes sauf `ping` et `auth/login`.
- Dates : ISO 8601 en sortie (`2026-11-14T15:30:00+01:00`) ; en entrée `Y-m-d` et `H:i`.
- Montants : nombres, en XAF.
- Listes paginées : `?page=2&par_page=20` (max 100). Réponse : `data`, `links`, `meta`.
- Limites : 60 requêtes/minute par utilisateur, 6 tentatives de connexion/minute.

### Format des erreurs

```json
{ "message": "Texte lisible", "code": "code_machine", "errors": { "champ": ["…"] } }
```

| HTTP | `code`                                 | Signification                                         |
| ---- | -------------------------------------- | ----------------------------------------------------- |
| 401  | `non_authentifie`                      | Jeton absent, invalide ou révoqué → renvoyer au login |
| 403  | `compte_inactif`                       | Compte désactivé (le jeton est révoqué)               |
| 403  | `role_non_autorise` / `profil_non_lie` | Route réservée à un autre profil                      |
| 402  | `abonnement_expire` / `abonnement_absent` | Abonnement SaaS de la plateforme à renouveler      |
| 503  | `site_inactif` / `aucun_client`        | Plateforme inactive ou non configurée                 |
| 404  | —                                      | Ressource introuvable (ou appartenant à un autre)     |
| 422  | —                                      | Erreurs de validation dans `errors`                   |
| 429  | —                                      | Trop de requêtes                                      |

## Statuts de commande

`attente` → `attribue` → `encours` → `livre` | `annulee` | `echoue`

Chaque commande renvoyée contient `transitions_possibles` : la liste des statuts que
**l'utilisateur connecté** peut appliquer. L'application doit s'en servir pour afficher
les boutons d'action plutôt que de recoder les règles.

- Coursier : `encours`, `livre`, `annulee`, `echoue` (selon le statut courant).
- Client : `annulee`, uniquement tant que la commande est `attente`.

## Endpoints

### Public

| Méthode | Route         | Description                    |
| ------- | ------------- | ------------------------------ |
| GET     | `/ping`       | Santé de l'API                 |
| POST    | `/auth/login` | Connexion → jeton              |

```http
POST /api/v1/auth/login
{ "email": "paul@example.com", "password": "secret123", "device_name": "Pixel 8" }
```

```json
{
  "token": "3|Xg…",
  "token_type": "Bearer",
  "user": {
    "id": 12, "noms": "Paul", "email": "paul@example.com", "telephone": null,
    "role": "coursier", "type_utilisateur": "Coursier", "actif": true,
    "ville": { "id": 1, "libelle": "Douala" },
    "coursier": { "id": 4, "noms": "Mbappe", "prenoms": "Paul", "telephone": "690000001", "disponible": true }
  }
}
```

`role` vaut `coursier`, `client` ou `admin` et détermine l'espace à afficher.

### Compte (authentifié)

| Méthode | Route              | Description                                                              |
| ------- | ------------------ | ------------------------------------------------------------------------ |
| GET     | `/auth/me`         | Profil de l'utilisateur connecté                                         |
| POST    | `/auth/logout`     | Révoque le jeton courant                                                 |
| POST    | `/auth/logout-all` | Révoque tous les jetons de l'utilisateur                                 |
| PUT     | `/auth/password`   | `current_password`, `password`, `password_confirmation` — déconnecte les autres appareils |

### Référentiels (authentifié)

| Méthode | Route                         | Description                                                  |
| ------- | ----------------------------- | ------------------------------------------------------------ |
| GET     | `/villes`                     | Liste des villes                                             |
| GET     | `/villes/{id}/quartiers?q=`   | Quartiers d'une ville (recherche optionnelle)                |
| POST    | `/tarifs/estimation`          | `id_quartier_collecte`, `id_quartier_livraison` → `montant`  |

### Espace coursier (`role = coursier`)

| Méthode | Route                                  | Description                                              |
| ------- | -------------------------------------- | -------------------------------------------------------- |
| GET     | `/coursier/commandes`                  | Ses livraisons. Filtres : `statut=attribue,encours`, `date=Y-m-d`, `q=` |
| GET     | `/coursier/commandes/resume`           | Compteurs par statut + chiffres du jour                  |
| GET     | `/coursier/commandes/{id}`             | Détail                                                   |
| PATCH   | `/coursier/commandes/{id}/statut`      | `{ "statut": "encours" }`                                |

### Espace client (`role = client`)

| Méthode | Route                                  | Description                                  |
| ------- | -------------------------------------- | -------------------------------------------- |
| GET     | `/client/commandes`                    | Ses commandes. Filtre : `statut=`            |
| POST    | `/client/commandes`                    | Nouvelle commande (voir ci-dessous)          |
| GET     | `/client/commandes/{id}`               | Détail (inclut le coursier attribué)         |
| POST    | `/client/commandes/{id}/annuler`       | Annule une commande en attente               |

Création — pour chaque point, fournir `id_quartier` **ou** `quartier` + `id_ville`
(un quartier inconnu est créé). Le montant de livraison est **calculé par le serveur**.

```json
{
  "collecte":  { "contact": "690000010", "id_quartier": 3, "description": "Face pharmacie" },
  "livraison": { "nom": "Jean", "contact": "690000020", "quartier": "Bonapriso", "id_ville": 1 },
  "date_livraison": "2026-11-15",
  "heure_livraison": "15:30",
  "montant_a_recuperer": 5000,
  "mode_de_paiement": "coursier",
  "description": "Un carton de livres"
}
```

Téléphones : 9 chiffres commençant par 6 ou 2. `mode_de_paiement` : `coursier` ou `speedex`.

### Objet commande

```json
{
  "id": 42, "statut": "attribue", "statut_libelle": "Attribuée", "type_commande": "simple",
  "description": "Colis fragile",
  "date_commande": "…", "date_livraison": "…", "date_mise_encours": null, "date_livre": null,
  "montant_livraison": 1500, "montant_a_recuperer": 5000, "mode_de_paiement": "coursier",
  "client":    { "id": 7, "nom": "Ngo Marie", "telephone": "670000002" },
  "collecte":  { "contact": "690000010", "lieu": "Akwa", "description": "Face pharmacie",
                 "quartier": { "id": 3, "libelle": "Akwa", "id_ville": 1, "id_zone": 2, "ville": "Douala" } },
  "livraison": { "nom": "Jean", "contact": "690000020", "lieu": "Bonapriso", "description": null, "quartier": { … } },
  "coursier":  { "id": 4, "noms": "Mbappe", "prenoms": "Paul", "telephone": "690000001" },
  "boutique": null, "produits": [],
  "transitions_possibles": ["encours", "livre", "annulee", "echoue"],
  "cree_le": "…", "modifie_le": "…"
}
```

## Mise en place côté serveur

```bash
php artisan migrate   # crée la table personal_access_tokens
```

Le versionnement (`/v1`) permet de faire évoluer l'API sans casser les
applications déjà publiées sur les stores : toute modification incompatible
passera par un `/v2`.

## Architecture

- `routes/api.php` — déclaration des routes.
- `app/Http/Controllers/Api/V1/` — contrôleurs (Auth, Référentiels, Coursier, Client).
- `app/Http/Requests/Api/V1/` — validation des entrées.
- `app/Http/Resources/V1/` — format JSON des sorties (indépendant des modèles).
- `app/Services/` — règles métier partagées : tarifs, cycle de vie des statuts, création de commande.
- `app/Http/Middleware/Api/` — compte actif, contrôle des rôles.
- `tests/Feature/Api/` — tests (`php artisan test tests/Feature/Api`).
