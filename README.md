Installation et lancement du projet Laravel
Prérequis

Avant de commencer, assurez-vous d’avoir installé sur votre machine :

PHP (version recommandée : 8.1 ou supérieure)

Composer

MySQL (ou un autre SGBD compatible)

Node.js & npm (optionnel, mais recommandé)

Un serveur local (Laragon, XAMPP, WAMP, ou équivalent)

Installation du projet

Cloner le dépôt

git clone <url-du-depot>
cd <nom-du-projet>


Installer les dépendances PHP

composer install


Créer le fichier d’environnement

cp .env.example .env


Configurer l’environnement

Ouvrir le fichier .env

Renseigner les informations de la base de données :

DB_DATABASE=nom_de_la_base
DB_USERNAME=utilisateur
DB_PASSWORD=mot_de_passe


Générer la clé de l’application

php artisan key:generate


Exécuter les migrations (si nécessaire)

php artisan migrate

Installation des dépendances front-end (optionnel)

Si le projet utilise des assets front-end :

npm install
npm run build


ou en mode développement :

npm run dev

Lancer le projet

Démarrer le serveur de développement Laravel :

php artisan serve


L’application sera accessible à l’adresse suivante :

http://127.0.0.1:8000

Arrêt du serveur

Pour arrêter le serveur, utilisez :

CTRL + C
