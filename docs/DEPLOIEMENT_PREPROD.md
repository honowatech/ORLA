# Déploiement en préproduction (hébergement mutualisé)

Sur l'hébergement mutualisé, on ne peut pas lancer `composer install` ni
`npm run build` : le serveur ne fait qu'un `git pull`. La branche **`preprod`**
contient donc, en plus du code de `main` :

- `vendor/` — dépendances PHP de production (`composer install --no-dev --optimize-autoloader`) ;
- `public/build/` — assets Vite compilés (`npm run build`).

## Mise à jour automatique

Le workflow `.github/workflows/preprod.yml` reconstruit `preprod` à chaque push
sur `main` (ou manuellement via *Actions → Build preprod → Run workflow*).
Chaque build est ajouté comme un nouveau commit au-dessus de l'historique de
`preprod` : côté serveur, `git pull` reste un simple fast-forward.

> Ne pas développer directement sur `preprod` : ses commits sont générés.
> Toute modification passe par `main`.

## Première installation sur le serveur

```bash
git clone -b preprod https://github.com/honowatech/ORLA.git .
# créer le fichier .env et renseigner APP_KEY, APP_URL, DB_*, APP_ENV=preprod, APP_DEBUG=false
```

- La racine web (document root) doit pointer vers le dossier `public/`.
- `storage/` et `bootstrap/cache/` doivent être accessibles en écriture.
- Si `php artisan` est disponible en SSH : `php artisan key:generate`,
  `php artisan migrate --force`, `php artisan storage:link`.

## Mises à jour

```bash
git pull origin preprod
# si php artisan est disponible :
php artisan migrate --force
php artisan optimize:clear
```

## Version de PHP

Le `composer.lock` actuel impose **PHP ≥ 8.4.1** (paquets Symfony 8.x).
Le serveur doit donc tourner en PHP 8.4, sinon `vendor/composer/platform_check.php`
bloquera l'application.
