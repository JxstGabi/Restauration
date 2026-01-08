# Installation et lancement du projet

## Prérequis

- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL
- Serveur local (Laragon, XAMPP, WAMP…)

---

## Installation

### 1. Cloner le projet
```bash
git clone https://github.com/ton-utilisateur/nom-du-repo.git
cd nom-du-repo
2. Installer les dépendances PHP
bash
Copier le code
composer install
3. Installer les dépendances front-end
bash
Copier le code
npm install
Configuration
4. Créer le fichier .env
bash
Copier le code
cp .env.example .env
Configurer la base de données dans le fichier .env :

env
Copier le code
DB_DATABASE=nom_de_la_base
DB_USERNAME=utilisateur
DB_PASSWORD=mot_de_passe
5. Générer la clé de l’application
bash
Copier le code
php artisan key:generate
Base de données
Lancer les migrations :

bash
Copier le code
php artisan migrate
Lancer le projet
Serveur Laravel
bash
Copier le code
php artisan serve
Accès au projet :

cpp
Copier le code
http://127.0.0.1:8000
Assets front-end
bash
Copier le code
npm run dev