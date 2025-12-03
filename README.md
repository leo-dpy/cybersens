# CyberSens (Version Statique)

Cette version du site "CyberSens" est maintenant **100% HTML / CSS / JavaScript** sans React, sans Node, sans bundler.

## Structure
```
index.html          # Redirection vers pages/index.html
pages/              # Toutes les pages HTML du site
	├─ index.html     # Accueil
	├─ cours.html     # Cours
	├─ quiz.html      # Quiz
	├─ aide.html      # Aide
	├─ leaderboard.html # Classement
	├─ login.html     # Connexion
	└─ signup.html    # Inscription
styles.css          # Styles globaux
main.js             # Interactions (menu mobile + modal bonnes pratiques)
```

## Ouvrir le site
Ouvrez `index.html` à la racine (vous serez redirigé vers `pages/index.html`) :
- Double‑cliquez sur le fichier
- Ou via PowerShell :
```powershell
Start-Process $PWD\index.html
```

Aucun serveur n'est nécessaire.
Astuce: vous pouvez aussi ouvrir directement `pages/index.html`.

## Fonctionnalités conservées
- Design identique (mêmes classes utilitaires)
- Menu mobile (bouton hamburger)
- Boîte modale "Bonnes pratiques" qui s'ouvre après 1 seconde
- Icônes via CDN Lucide (`https://unpkg.com/lucide`)

## Personnalisation
- Pour supprimer l'ouverture automatique du modal, retirez ou commentez `setTimeout(openDialog, 1000);` dans `main.js`.
- Vous pouvez réduire la taille de `styles.css` en ne gardant que les classes réellement utilisées (optimisation manuelle possible si besoin).

## Prochaines améliorations possibles
- Ajout d'un fichier `favicon.ico`
- Minification manuelle du CSS
- Ajout d'une section "Ressources" réelle
- Internationalisation (FR/EN)

## Licence
Ce projet est diffusé sous une licence propriétaire. Toute copie, redistribution, publication ou modification du code est interdite sans autorisation écrite préalable du titulaire des droits.

Usage autorisé sans autorisation préalable : consultation et usage interne pour tests, démonstrations ou maquettes au sein de votre organisation uniquement.

Consultez le fichier `LICENSE` pour les conditions complètes.
