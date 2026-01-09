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
git clone https://github.com/JxstGabi/restauration
cd Restauration
```

### 2. Installer les dépendances

Installez les dépendances PHP et JavaScript :

```bash
composer install
npm install
```

### 3. Configuration de l'environnement

Le projet est configuré pour se connecter à une base de données de test spécifique.
Copiez le fichier `.env.example` vers `.env` (si ce n'est pas déjà fait) et utilisez la configuration suivante :

```bash
cp .env.example .env
```

Modifiez le fichier `.env` avec les identifiants suivants (déjà configurés pour le projet) :

```env
DB_CONNECTION=mysql
DB_HOST=192.168.10.16
DB_PORT=3306
DB_DATABASE=gautret_restauration
DB_USERNAME=gautret
DB_PASSWORD=z2zS5qOm
```

Générez la clé d'application Laravel :

```bash
php artisan key:generate
```

### 4. Base de données (Terminé !)

✅ **Vous êtes connecté à la base de données partagée.**
Comme vous utilisez la base commune (`192.168.10.16`), **vous n'avez rien d'autre à faire.** Les tables et les données (écoles, menus) sont déjà présentes.

**Passez directement à l'étape "Lancement de l'application".**

---

*(Uniquement si vous souhaitez créer votre propre base locale vide :)*

Si vous décidez de ne pas utiliser la base partagée, modifiez le `.env` vers votre base locale, puis :

1. Créez la structure :
   ```bash
   php artisan migrate
   ```
2. Importez les données :
   ```bash
   php artisan sync:ecoles
   php artisan menus:import
   ```
---

## 🏃 Lancement de l'application

Pour lancer l'environnement de développement complet, ouvrez deux terminaux :

**Terminal 1 : Compilation des assets (Vite)**
```bash
npm run dev
```

**Terminal 2 : Serveur Laravel**
```bash
php artisan serve
```

L'application sera accessible sur : [http://127.0.0.1:8000](http://127.0.0.1:8000).

## 🛠️ Fonctionnalités Clés

- **🏠 Tableau de bord Famille** : Gestion centralisée des enfants et accès rapide aux menus via des liens directs sur les écoles.
- **🗺️ Carte Interactive** : 
  - Visualisation de toutes les écoles d'Angers.
  - Bouton de **géolocalisation** pour centrer la carte sur votre position.
  - Informations détaillées au clic (adresse, type).
- **🍽️ Menus Scolaires** : 
  - Affichage clair des repas de la semaine.
  - Gestion des intolérances (affichage des compositions si disponibles).
- **🔗 Partage Social** : 
  - Génération de liens de partage publics pour les grands-parents ou nounous.
  - Boutons de partage rapide (WhatsApp, Email).
- **👤 Gestion de Compte** : 
  - Modification des informations personnelles.
  - Option de suppression de compte sécurisée.
- **⚡ Raccourcis UX** : 
  - Création rapide de fratries (copie de l'école du frère/sœur en un clic).
