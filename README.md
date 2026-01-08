# 🍽️ Restauration Scolaire - Angers

Application de consultation des menus de la restauration scolaire de la ville d'Angers, avec gestion des profils enfants, carte interactive et fonctionnalités de partage.

## 📋 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **PHP** >= 8.2
- **Composer** (Gestionnaire de dépendances PHP)
- **Node.js** & **NPM** (pour la compilation des assets)
- **MySQL** ou un autre serveur de base de données compatible

## 🚀 Installation

### 1. Cloner le projet

```bash
git clone <votre-repo-url>
cd Restauration
```

### 2. Installer les dépendances

Installez les dépendances PHP et JavaScript :

```bash
composer install
npm install
```

### 3. Configuration de l'environnement

Dupliquez le fichier d'exemple pour créer votre configuration locale :

```bash
cp .env.example .env
```

Ouvrez le fichier `.env` et configurez vos accès à la base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=votre_nom_de_db
DB_USERNAME=votre_user
DB_PASSWORD=votre_password
```

Générez la clé d'application Laravel :

```bash
php artisan key:generate
```

### 4. Base de données

Créez la base de données spécifiée dans votre `.env`, puis exécutez les migrations :

```bash
php artisan migrate
```

## 📦 Initialisation des données (Open Data)

L'application s'appuie sur les données ouvertes de la ville d'Angers. Vous devez exécuter les commandes personnalisées suivantes pour peupler la base de données.

**Important :** Exécutez ces commandes dans l'ordre suivant.

1. **Synchroniser la liste des écoles :**
   Cette commande récupère les écoles depuis l'API OpenData d'Angers.
   ```bash
   php artisan sync:ecoles
   ```

2. **Importer les menus :**
   Cette commande récupère les menus associés aux écoles.
   ```bash
   php artisan menus:import
   ```

*(Optionnel) Créer un utilisateur de test :*
```bash
php artisan db:seed
```

## 🏃 Lancement de l'application

Vous aurez besoin de deux terminaux pour lancer l'application en mode développement.

**Terminal 1 : Compilation des assets (Vite)**
```bash
npm run dev
```

**Terminal 2 : Serveur Laravel**
```bash
php artisan serve
```

L'application sera accessible à l'adresse : [http://127.0.0.1:8000](http://127.0.0.1:8000).

## 🛠️ Fonctionnalités

- **Carte interactive** : Visualisation des écoles sur une carte (OpenStreetMap / Leaflet).
- **Menus détaillés** : Consultation des menus de la semaine pour chaque école.
- **Gestion famille** : Ajout d'enfants et association à leur école.
- **Partage** : Possibilité de partager le menu d'un enfant via un lien unique sécurisé.
