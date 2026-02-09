# Metrix

Metrix est un tableau de suivi cyber avec des domaines, clauses, metriques, observations et preuves.

## Authentification JWT

Un systeme de connexion simple a ete ajoute:

- `login.php` pour se connecter
- JWT stocke en cookie `HttpOnly`
- protection des pages (`index.php`, `admin/*`) et des API (`api/*`)
- `logout.php` pour se deconnecter

## Table users (Adminer)

Script SQL pret a l'emploi: `sql/users.sql`

Compte admin de demo:

- email: `admin@metrix.local`
- mot de passe: `admin123`

## Formulaires admin

Le endpoint `api/create_entity.php` gere maintenant:

- `domaine`
- `clause`
- `user`

La creation d'utilisateur est disponible via `admin/ajouter-user.php` (acces admin uniquement).

Suppression des utilisateurs (acces admin):

- page: `admin/liste-users.php`
- endpoint: `api/delete_user.php`

Guide complet:

- `docs/gestion-comptes.md`

## Secret JWT

Definir la variable d'environnement `JWT_SECRET` en production.
Sans variable, une valeur par defaut est utilisee (a changer).
