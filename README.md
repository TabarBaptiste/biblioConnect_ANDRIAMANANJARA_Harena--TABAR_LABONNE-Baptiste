# BiblioConnect - Gestion de Bibliothèque en Ligne

BiblioConnect est une plateforme web de gestion de bibliothèque en ligne, permettant aux utilisateurs de consulter un catalogue de livres, de réserver des livres et de gérer leur compte. La plateforme est dotée de rôles différents permettant une gestion flexible des utilisateurs, des réservations et du catalogue. Elle est construite avec Symfony et utilise MySQL pour la base de données.

## Table des matières

- [Technologies utilisées](#technologies-utilisées)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Fonctionnalités](#fonctionnalités)
- [Rôles et permissions](#rôles-et-permissions)
- [Auteurs](#auteurs)

## Technologies utilisées

- **Backend** : Symfony 7.2.x
- **Base de données** : MySQL 8.0+
- **Frontend** : Twig, HTML5, CSS3
- **Gestion des rôles** : Symfony Security
- **Gestion des formulaires** : Symfony Forms
- **Gestion des entités** : Doctrine ORM

## Prérequis

Avant de commencer, assurez-vous d'avoir installé les outils suivants :

- PHP 8.2 ou supérieur
- Composer (pour gérer les dépendances PHP)
- MySQL
- Symfony CLI

## Installation

### Étape 1 : Clonez le repository

```bash
git clone https://github.com/votre-utilisateur/biblioconnect.git

cd biblioconnect
```

### Étape 2 : Installez les dépendances

Assurez-vous d'avoir PHP et Composer installés, puis exécutez les commandes suivantes :

```bash
composer install
```

Cela installera toutes les dépendances PHP nécessaires.

### Étape 3 : Configurez la base de données

Configurez votre `.env.dev` avec les bonnes informations de connexion à votre base de données :

```bash
DATABASE_URL="mysql://root:password@127.0.0.1:3306/biblioconnect"
```

Ensuite, créez une base de données dans MySQL :

```bash
php bin/console doctrine:database:create
```

### Étape 4 : Appliquez les migrations

```bash
php bin/console make:migration
```

```bash
php bin/console doctrine:migrations:migrate
```

Cela appliquera les migrations de base de données et créera les tables nécessaires.

### Étape 5 : Lancez le serveur

Vous pouvez maintenant démarrer le serveur de développement Symfony avec la commande suivante :

```bash
php bin/console server:start
```

Le site sera accessible à l'adresse : [http://http://127.0.0.1/:8000](http://http://127.0.0.1/:8000).

## Fonctionnalités

- **Catalogue de livres** : Consulter la liste complète des livres disponibles dans la bibliothèque.
- **Réservations de livres** : Les utilisateurs peuvent réserver des livres et voir l'historique de leurs réservations.
- **Gestion des utilisateurs** : Les administrateurs peuvent ajouter, supprimer et modifier les utilisateurs.
- **Gestion des rôles** : Les administrateurs peuvent attribuer des rôles comme **Utilisateur**, **Bibliothécaire** ou **Administrateur**.
- **Connexion et sécurité** : Authentification avec des rôles et permissions définis, permettant un contrôle d'accès précis aux différentes sections de la plateforme.

## Rôles et permissions

L'application utilise un système de rôles basé sur Symfony Security pour contrôler l'accès des utilisateurs.

- **ROLE_USER** : Utilisateur standard, peut consulter le catalogue et réserver des livres.
- **ROLE_LIBRARIAN** : Bibliothécaire, peut gérer les livres et consulter les réservations.
- **ROLE_ADMIN** : Administrateur, peut gérer les utilisateurs, les rôles et les livres.

Les administrateurs ont la possibilité de modifier les rôles des autres utilisateurs (mais pas le leur).

## Auteurs

- **Baptiste TABAR LABONNE** - [https://github.com/TabarBaptiste](https://github.com/TabarBaptiste)
- **Harena ANDRIAMANANJARA MANDIMBY**
