# FourData - Backend
# Démarrage du Projet

## Prérequis

- PHP >= 7.4
- Composer
- Docker et Docker Compose
- Node.js et npm (si nécessaire)

## Installation

1. Clonez le dépôt :

    ```sh
    git clone <url-du-repo>
    cd <nom-du-repo>
    ```

2. Installez les dépendances PHP :

    ```sh
    composer install
    ```

3. Copiez le fichier `.env` et configurez les variables d'environnement :

    ```sh
    cp .env .env.local
    ```

4. Générez les clés JWT :

    ```sh
    openssl genrsa -out config/jwt/private.pem -aes256 4096
    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
    ```

5. Configurez Docker et démarrez les services :

    ```sh
    docker-compose up -d
    ```

6. Créez la base de données :

    ```sh
    php bin/console doctrine:database:create
    ```

7. Appliquez les migrations :

    ```sh
    php bin/console doctrine:migrations:migrate
    ```

8. Chargez les fixtures (si nécessaire) :

    ```sh
    php bin/console doctrine:fixtures:load
    ```

## Exécution des Tests

Pour exécuter les tests unitaires et fonctionnels via PHPUnit :

```sh
php bin/phpunit