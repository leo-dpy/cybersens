# CyberSens

Plateforme web de sensibilisation à la cybersécurité, avec gestion d’utilisateurs, quiz, progression, badges, ressources, et interface d’administration.

## Structure du projet
```
index.html           # Page d’accueil
main.js              # JS principal
styles.css           # Styles globaux
install.php          # Script d’installation
logout.php           # Déconnexion

admin/               # Interface d’administration (PHP)
backend/             # API backend (PHP)
database/            # Fichiers SQL (structure et données)
templates/           # Templates HTML (pages principales)
```

## Prérequis
- PHP 7.4+
- MySQL/MariaDB
- Serveur web (Apache recommandé)

## Installation
1. Clonez le dépôt :
	```sh
	git clone https://github.com/leo-dpy/cybersens.git
	```
2. Importez le fichier `database/cybersens.sql` dans votre base de données MySQL.
3. Configurez la connexion à la base dans `backend/db.php` (hôte, utilisateur, mot de passe, nom de la base).
4. Placez le dossier sur votre serveur web (ex : `htdocs` sous XAMPP).
5. Accédez à `index.html` ou `admin/index.php` via votre navigateur.

## Fonctionnalités principales
- Authentification et gestion des utilisateurs
- Quiz interactifs et progression
- Attribution de badges et certificats
- Tableau de bord administrateur (ajout/édition de cours, questions, utilisateurs)
- Classement (leaderboard)
- Notifications et ressources pédagogiques

## Arborescence simplifiée
```
admin/         # Pages et scripts d’administration
backend/       # API et logique serveur (PHP)
database/      # Dump SQL
templates/     # Templates HTML pour le rendu dynamique
```

## Personnalisation
- Modifiez les templates HTML dans `templates/` pour adapter le design.
- Ajoutez des cours/questions via l’interface admin ou directement en base.

## Licence
Ce projet est diffusé sous une licence propriétaire. Toute copie, redistribution, publication ou modification du code est interdite sans autorisation écrite préalable du titulaire des droits.

Usage autorisé sans autorisation préalable : consultation et usage interne pour tests, démonstrations ou maquettes au sein de votre organisation uniquement.

Consultez le fichier `LICENSE` pour les conditions complètes


