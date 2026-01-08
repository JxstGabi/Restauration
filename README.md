Guide d'installation et de lancement d'un projet Laravel
Prérequis
Avant de commencer, assurez-vous d'avoir installé sur votre machine :

PHP (version 8.1 ou supérieure)
Composer (gestionnaire de dépendances PHP)
Un serveur de base de données (MySQL, PostgreSQL, SQLite, etc.)
Node.js et NPM (pour la compilation des assets front-end)

Installation d'un nouveau projet Laravel
Méthode 1 : Via Composer
bashcomposer create-project laravel/laravel nom-du-projet
cd nom-du-projet
Méthode 2 : Via l'installeur Laravel
bashcomposer global require laravel/installer
laravel new nom-du-projet
cd nom-du-projet
Configuration du projet
1. Configuration de l'environnement
Copiez le fichier .env.example en .env :
bashcp .env.example .env
Générez la clé d'application :
bashphp artisan key:generate
2. Configuration de la base de données
Éditez le fichier .env et configurez vos paramètres de base de données :
envDB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nom_de_votre_base
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
Créez la base de données (via phpMyAdmin, MySQL Workbench ou en ligne de commande) :
bashmysql -u root -p
CREATE DATABASE nom_de_votre_base;
3. Exécution des migrations
bashphp artisan migrate
4. Installation des dépendances front-end
bashnpm install
Lancement du projet
Démarrage du serveur de développement
bashphp artisan serve
Par défaut, l'application sera accessible à l'adresse : http://localhost:8000
Pour spécifier un port différent :
bashphp artisan serve --port=8080
Compilation des assets (optionnel)
Pour compiler les assets une seule fois :
bashnpm run build
Pour compiler en mode développement avec rechargement automatique :
bashnpm run dev
Commandes utiles
Nettoyage du cache
bashphp artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
Génération de données de test
bashphp artisan db:seed
Mode maintenance
Activer :
bashphp artisan down
Désactiver :
bashphp artisan up
Cloner un projet Laravel existant
Si vous clonez un projet existant depuis Git :
bashgit clone url-du-repository
cd nom-du-projet
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
Dépannage
Erreur de permissions : Sur Linux/Mac, vous devrez peut-être ajuster les permissions des dossiers storage et bootstrap/cache :
bashchmod -R 775 storage bootstrap/cache
