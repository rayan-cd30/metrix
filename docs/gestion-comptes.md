# Gestion des comptes

Ce document explique comment ajouter ou supprimer un compte utilisateur dans Metrix.

## Prerequis

- Etre connecte avec un compte `admin`.
- La table `users` doit exister (voir `sql/users.sql`).

## Ajouter un compte

1. Ouvrir `admin/ajouter-user.php`.
2. Renseigner:
   - `email`
   - `mot de passe` (minimum 8 caracteres)
   - `role` (`user` ou `admin`)
3. Cliquer sur **Ajouter l'utilisateur**.

Notes:

- L'email est valide cote serveur (format email).
- L'email doit etre unique.
- Le mot de passe est hashé avec `password_hash` avant insertion.

## Supprimer un compte

1. Ouvrir `admin/liste-users.php`.
2. Cliquer sur **Supprimer** sur la ligne de l'utilisateur cible.
3. Confirmer la suppression.

Regles de securite:

- Impossible de supprimer son propre compte connecte.
- Impossible de supprimer le dernier compte `admin`.

## Endpoints concernes

- Creation: `api/create_entity.php` avec `entity=user`
- Suppression: `api/delete_user.php`

Ces endpoints sont proteges et accessibles uniquement pour les roles `admin`.
