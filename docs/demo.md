# Démo sans mot de passe

En production, un visiteur choisit un profil (Administrateur, Agent routeur, Coursier, Client entreprise) et entre dans **sa propre agence de livraison**, remplie de deux mois d'activité, sans saisir de mot de passe. Il peut tout faire : saisir une commande, l'attribuer, la livrer. Rien de ce qu'il fait ne touche la production.

Ce document applique à ORLA (Speedex) l'approche générique « Accès démo sans mot de passe » : un bac à sable par visiteur, un générateur qui rejoue l'activité par les actions métier, une réserve de bacs prêts et dix exigences de sécurité. Il décrit ce qui est en place et où.

**Statut :** implémenté le 2026-11-14, désactivé par défaut (`DEMO_ENABLED=false`).

---

## 1. L'écart avec l'approche générique : une base par bac

L'approche suppose que l'application cloisonne déjà ses locataires, et fait du bac un locataire comme un autre. **ORLA ne cloisonne pas** :

- aucune table métier (`commandes`, `clients`, `coursiers`, `zone`, `users`…) ne porte d'identifiant d'entreprise ;
- les contrôleurs lisent toute la table (`Commandes::all()`, `Clients::findOrFail($id)`…) ;
- `super_admin_client` ne contient que des fiches d'abonnement, et `Check_Sa_Client_Error` laisse passer dès qu'un client quelconque est actif.

Une installation correspond donc à une seule entreprise. L'approche prévoit ce cas (§7) : ajouter un identifiant de locataire partout, prendre une base par bac, ou se contenter d'une démo en lecture seule. **On a retenu une base par bac**, sous la forme d'un fichier SQLite :

| | Base SQLite par bac (retenu) | Identifiant de locataire partout |
|---|---|---|
| Isolation | Physique : un bac ne peut pas lire une autre base | Dépend de chaque requête des ~30 contrôleurs |
| Changements dans le métier | Aucun contrôleur modifié (hors actions extraites, §4) | Colonne sur ~20 tables, scope global, reprise des données |
| Purge | Supprimer un fichier | Supprimer dans le bon ordre, table par table |
| Oracle d'unicité (e-mail…) | Inexistant : l'unicité ne porte que sur le bac | À fermer champ par champ |
| Prérequis | `pdo_sqlite`, un dossier en écriture | — |

Le code métier n'utilise que l'ORM Eloquent, sans SQL propre à MySQL. Il tourne donc tel quel sur SQLite.

## 2. Fonctionnement

```
Visiteur ──POST /demo (slug)──► DemoController::enter
                                  │ SandboxPool::claim()  (registre demo_sandboxes, base principale)
                                  │ Demo::enter($bac)      connexion par défaut → storage/demo/<nom>.sqlite
                                  │ Auth::login(utilisateur du profil, dans le bac)
                                  ▼
Requêtes suivantes ──► middleware DemoSandbox (groupe web, avant Authenticate)
                         bac ouvert ?  oui → bascule sur son fichier, prolonge l'inactivité
                                       non → session détruite SANS lire l'utilisateur, retour à /demo
```

- La base principale ne contient que le **registre** des bacs (`demo_sandboxes`). Aucune ligne métier de démo n'y entre.
- La session et le cache restent sur la base principale : `Demo::useFile()` les épingle avant la bascule.
- **Le point sensible.** L'identifiant d'utilisateur gardé en session désigne une ligne **du bac**. Résolu dans la base principale, il ouvrirait le compte réel de même identifiant. C'est pourquoi le garde est placé avant `Authenticate` dans l'ordre de priorité (`bootstrap/app.php`). Si le bac a disparu ou a expiré, il détruit la session sans appeler `Auth::logout()`, qui lirait l'utilisateur. Test : `test_an_expired_session_never_falls_back_to_the_real_account_with_the_same_id`.

## 3. Ce qui est en place

| Élément | Où | Remarque |
|---|---|---|
| Réglages | `config/demo.php` (`DEMO_*`) | Voir §6. |
| Registre | migration `2026_11_14_000000_create_demo_sandboxes_table`, modèle `App\Demo\Sandbox` | Toujours lu sur la connexion principale, même pendant une requête démo. |
| Bascule de connexion | `App\Demo\Demo` | `enter()`, `useFile()`, `leave()`, `current()`. |
| Garde | `App\Http\Middleware\DemoSandbox` | Dans le groupe `web` : aucune route ne peut l'oublier. Il vérifie l'expiration (inactivité et durée maximale), le fichier et l'activation, et interdit en démo `superadmin/*`, `Sc-*` (Monetbil), `register`, `password/*` et `login`. Prolongation au plus une fois par minute. En-tête `X-Robots-Tag: noindex`. |
| Parcours | `App\Http\Controllers\DemoController`, routes `demo.*` dans `routes/web.php` | `GET /demo`, `POST /demo` (`guest`, `throttle:demo`), `POST /demo/profile` et `POST /demo/end` (`auth`, et uniquement pour le bac de la session, 403 sinon). |
| Page des profils | `resources/views/demo/index.blade.php` | Un formulaire POST avec CSRF ; chaque carte est un bouton qui n'envoie qu'un slug. `noindex`. |
| Bandeau | `resources/views/demo/banner.blade.php`, inclus par les trois gabarits (admin, coursier, client) | Heure d'effacement, changement de profil (`<details>`, sans script), « Terminer », « Créer mon compte » si `DEMO_SIGNUP_URL` est défini. |
| Connexion | `LoginController::attemptLogin` | Adresse du domaine réservé refusée, même avec le bon mot de passe, avec la réponse d'un mauvais mot de passe. |
| Réinitialisation | `ForgotPasswordController::sendResetLinkEmail` | Ni jeton ni e-mail ; même réponse qu'une adresse inconnue. |
| Envois | `App\Providers\DemoServiceProvider` (écouteur `MessageSending`) | Annule tout e-mail envoyé depuis un bac ou vers une adresse `.invalid`. |
| Réserve | `App\Demo\SandboxPool` | Frais = construit aujourd'hui (fuseau `Africa/Douala`) et depuis moins de 6 h. Attribution par comparer-et-poser (`UPDATE … WHERE id = ? AND claimed_at IS NULL`, puis relecture par jeton) : portable sur MySQL, MariaDB et SQLite. Un bac sans fichier n'est jamais servi. Plafond de bacs ouverts. |
| Construction | `App\Demo\SandboxBuilder` | Copie d'un modèle de schéma (migré une fois, renommé quand les migrations changent), remplissage dans une transaction, contrôle, puis renommage du fichier : le bac existe entier et cohérent, ou pas du tout. |
| Générateur | `App\Demo\Generator\DemoAgency`, `ActivitySimulator`, `DemoCatalogue`, horloge `App\Demo\DemoClock` | Voir §4. |
| Contrôle | `App\Demo\Generator\SandboxCheck` | Profils présents et actifs, tous les utilisateurs sur `@demo.invalid`, abonnement de l'agence valide, aucune date après « maintenant », dates de chaque commande dans l'ordre, coursier présent dès l'attribution, tarif de zone cohérent, commandes confiées à l'agent, aucune action refusée pendant le rejeu. |
| Commandes | `routes/console.php` | `demo:build` (`--seed`, `--days`, `--ahead`), `demo:refill` (toutes les 5 min, sans chevauchement, un seul serveur, `storage/logs/demo.log`), `demo:prune` (toutes les 15 min). |
| Accueil | `resources/views/auth/login.blade.php` | « Essayer la démo, sans compte » quand la démo est active. |
| Tests | `tests/Feature/Demo/` | 27 tests sur le parcours et la sécurité (`DemoEntryTest`), 6 sur le générateur (`DemoBuilderTest`). |

## 4. Le générateur

- **Mise en place** (`DemoAgency`), datée deux semaines avant l'historique : Douala (XAF), 6 zones et 23 quartiers réels, un tarif par paire de zones (1 500 à 3 500 XAF), 8 coursiers répartis sur les zones, un agent routeur, un administrateur, 6 boutiques fictives avec produits et stock, 45 particuliers, un point relais, et la fiche d'abonnement de l'agence que lit `Check_Sa_Client_Error`.
- **Activité** (`ActivitySimulator`) sur 60 jours (`DEMO_HISTORY_DAYS`), environ 14 commandes par jour, modulées par le jour de la semaine (creux le dimanche, pic le samedi), la fin de mois et une croissance sur la période. Chaque commande suit son cycle : saisie (par l'agent, ou par la boutique du profil Client), confiée à l'agent, attribuée à un coursier de la zone, mise en cours, puis livrée (88 %), annulée ou échouée. Environ 2 % sont annulées avant attribution, et certaines livraisons sont planifiées jusqu'à 7 jours à l'avance.
- **Rejeu.** Tout est planifié d'abord, avec tous les tirages aléatoires (graine fixe, `Random\Randomizer` Mt19937). Les événements sont ensuite rejoués dans l'ordre chronologique, l'horloge réglée sur chacun. Ceux qui restent à venir ne sont pas joués : la journée en cours a ses commandes en attente, attribuées, en cours. L'horloge est rétablie même après une erreur.
- **Par les actions métier.** Elles ont été extraites des contrôleurs, qui les utilisent désormais :

  | Action | Extraite de |
  |---|---|
  | `App\Actions\Commandes\EnregistrerCommande` | `store` des contrôleurs Admin et Client (identiques) : tarif de zone, enregistrement, détail des produits |
  | `App\Actions\Commandes\ChangerStatutCommande` | `destroy` des contrôleurs Admin, Client et Coursier : transitions permises, attribution du coursier, dates de mise en cours et de livraison |
  | `App\Actions\Commandes\NoterActiviteLivraison` | `destroy` du contrôleur Coursier : journal d'activité |
  | `App\Actions\Commandes\ConfierCommandeAgent` | `Admin\CommandesController::attribuate_agent` |

  Deux corrections de comportement en passant :
  - un changement de statut sur une commande déjà terminée (livrée, annulée…) donnait une erreur 500 (`$depart` indéfini). Il répond désormais « opération impossible » ;
  - le passage d'un coursier à « échouée » plantait aussi (`$reponse` indéfini).

**Mesuré** (PHP 8.3, conteneur Linux) : un bac de 60 jours, environ 830 commandes, se construit en 1 à 3 s. La construction à la volée, quand la réserve est vide, reste donc acceptable.

## 5. Exigences de sécurité

| § de l'approche | Mise en œuvre | Test |
|---|---|---|
| 5.1 POST + CSRF, débit, plafond | Formulaire POST, `throttle:demo` (3 / 10 min par IP), `max_active` | `test_entry_is_rate_limited`, `test_capacity_and_freshness_are_enforced` |
| 5.2 Visiteur déconnecté | `guest`, refus d'une session Super Admin, session invalidée à l'entrée, pas de « se souvenir de moi » | `test_a_logged_in_real_user_cannot_enter`, `test_a_super_admin_session_cannot_enter` |
| 5.3 Aucun mot de passe utilisable | Mot de passe aléatoire non affiché, connexion et réinitialisation refusées | `test_password_login_refuses_…`, `test_reset_link_…` |
| 5.4 Aucun message sortant | Domaine `demo.invalid`, écouteur `MessageSending`, Monetbil interdit | `test_mail_to_a_reserved_domain_…`, `test_platform_and_payment_routes_are_closed_in_a_demo` |
| 5.5 Aucune fuite sur les vrais comptes | Sans objet par construction : l'unicité des e-mails ne porte que sur le fichier du bac | `test_a_sandbox_is_a_separate_file_…` |
| 5.6 Profils en liste fermée | Slug validé contre `demo.profiles`, utilisateur cherché dans le bac de la session | `test_an_unknown_profile_…`, `test_switching_profile_…` |
| 5.7 Garde sur les routes métier | Middleware `DemoSandbox` dans le groupe `web` | `test_an_expired_session_…`, `test_a_deleted_sandbox_file_…`, `test_disabling_the_demo_…` |
| 5.8 La plateforme ne voit pas les démos | Par construction : les bacs ne sont pas dans `super_admin_client` de la base principale | `test_a_sandbox_is_a_separate_file_…` |
| 5.9 Purge réelle | `demo:prune` supprime le fichier et la ligne, ainsi que les fichiers orphelins | `test_ending_logs_out_and_the_sandbox_is_then_deleted`, `test_prune_removes_…` |
| 5.10 Garde-fous | 404 tant que `DEMO_ENABLED=false` (routes toujours enregistrées), `noindex`, journal minimal (`demo.enter`, `demo.profile`, `demo.signup`, sans IP) | `test_pages_answer_404_while_disabled`, `test_profile_page_lists_profiles_and_is_not_indexed` |

## 6. Paramètres

| Variable | Défaut | Remarque |
|---|---|---|
| `DEMO_ENABLED` | `false` | Pages en 404 tant qu'elle est fausse ; les sessions ouvertes sont fermées. |
| `DEMO_PATH` | `storage/demo` | Dossier des bacs, en écriture pour PHP. |
| `DEMO_IDLE_MINUTES` | 120 | Prolongée à chaque requête. |
| `DEMO_MAX_HOURS` | 8 | Une journée de travail. |
| `DEMO_POOL_SIZE` | 3 | Bacs prêts. |
| `DEMO_POOL_MAX_AGE_HOURS` | 6 | Au-delà, un bac non servi est jeté. |
| `DEMO_MAX_ACTIVE` | 100 | Bacs ouverts en même temps. |
| `DEMO_BUILD_ON_DEMAND` | `true` | Réserve vide : construire pendant la requête (1 à 3 s). |
| `DEMO_THROTTLE_ATTEMPTS` / `DEMO_THROTTLE_MINUTES` | 3 / 10 | Entrées par IP. |
| `DEMO_HISTORY_DAYS` / `DEMO_AHEAD_DAYS` | 60 / 7 | |
| `DEMO_TIMEZONE` | `Africa/Douala` | Décide du « jour » d'un bac. |
| `DEMO_SIGNUP_URL` | vide | Cible de « Créer mon compte » ; vide, le bouton est masqué. |

Chaque bac occupe environ 1 Mo sur disque. Au plafond de 100, cela fait environ 100 Mo.

## 7. Mise en production

1. Vérifier que `pdo_sqlite` est actif (`php -m | grep -i sqlite`) et que `storage/demo` est accessible en écriture.
2. `php artisan migrate --force` : crée la table `demo_sandboxes`.
3. Vérifier que le planificateur tourne (`* * * * * php artisan schedule:run`).
4. Dans `.env` : `DEMO_ENABLED=true`, et `DEMO_SIGNUP_URL` si l'on veut le bouton d'inscription.
5. `php artisan config:cache`, puis `php artisan demo:refill` pour amorcer la réserve.
6. Ouvrir `/demo`.

Pour couper la démo, repasser `DEMO_ENABLED=false` : les sessions démo ouvertes sont fermées à leur requête suivante, et `demo:prune` efface les bacs.

## 8. Limites connues

- Un bac s'arrête à l'heure de sa construction. Servi jusqu'à 6 h plus tard, il montre des livraisons de l'après-midi encore « attribuées ». C'est le prix de la réserve ; `DEMO_POOL_MAX_AGE_HOURS` règle le compromis.
- Les heures affichées par l'application sont en UTC (convention existante, `config/app.php`). Seul le bandeau affiche l'heure de Douala.
- Une nouvelle migration doit tourner sur SQLite. Le modèle de schéma est régénéré automatiquement, et le premier `demo:build` qui suit révèle tout écart.
- La console Super Admin de la plateforme ne liste pas les bacs. Pour suivre l'usage, on peut lire `demo_sandboxes` et `storage/logs/demo.log`.

## 9. Questions ouvertes

- Cible de « Créer mon compte » : il n'y a pas d'inscription en libre-service (le Super Admin crée les entreprises). Formulaire de contact, WhatsApp, page commerciale ?
- Profils affichés : les quatre retenus conviennent-ils ?
- Repli au plafond : message d'attente (en place) ou démo en lecture seule ?
- Mesure de la conversion : quel événement compte ?
