# Projet Symfony – Plateforme BiblioConnect
@auteur TABAR LABONNE Baptiste
@auteur ANDRIAMANANJARA Harena

## Créer la class utilisateur 
```bash
php bin/console make:user
```
Ajout des propriétées `nom`, `prénom`, `dateDeCreation`

## Configurer le fichier `security.yaml`
```yaml
    main:
        lazy: true
        provider: app_user_provider
        form_login:
            login_path: login
            check_path: login
        logout:
            path: logout
            target: /

access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
    - { path: ^/librarian, roles: ROLE_LIBRARIAN }
    - { path: ^/user, roles: ROLE_USER }
    - { path: ^/login, roles: PUBLIC_ACCESS }
```