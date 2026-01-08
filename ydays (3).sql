-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 07 jan. 2026 à 09:09
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ydays`
--

-- --------------------------------------------------------

--
-- Structure de la table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text,
  `difficulty` enum('Facile','Intermédiaire','Difficile') DEFAULT 'Facile',
  `content` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `difficulty`, `content`) VALUES
(1, 'Introduction à l\'Ingénierie Sociale', 'Niveau 1 : Apprenez les bases de la manipulation humaine.', 'Facile', '<h3>Qu\'est-ce que l\'ingénierie sociale ?</h3><p>C\'est l\'art de manipuler les gens pour qu\'ils divulguent des informations confidentielles.</p><h4>Les vecteurs d\'attaque :</h4><ul><li>Phishing (Email)</li><li>Vishing (Téléphone)</li><li>Smishing (SMS)</li></ul>'),
(2, 'Attaques Réseaux & WiFi', 'Niveau 2 : Comprendre les attaques DDoS et Man in the Middle.', 'Intermédiaire', '<h3>Le danger des WiFi publics</h3><p>Sur un réseau non sécurisé, un attaquant peut intercepter vos données via une attaque <strong>Man in the Middle</strong>.</p><p>Une attaque <strong>DDoS</strong> vise quant à elle à saturer un serveur pour le rendre inaccessible.</p>'),
(3, 'Failles Web (SQL & XSS)', 'Niveau 3 : Injections et scripts malveillants.', 'Difficile', '<h3>Les failles Web</h3><p><strong>Injection SQL :</strong> L\'attaquant manipule la base de données via un formulaire.</p><p><strong>XSS (Cross-Site Scripting) :</strong> L\'attaquant injecte du code JavaScript pour voler les cookies des visiteurs.</p>'),
(4, 'test', 'test', 'Facile', '???? Module 5: Guide Niveau Avancé (Pro/Associatif)\nPrincipe du Moindre Privilège\nDéfinition : Règle consistant à donner à un utilisateur uniquement les accès strictement nécessaires à son travail, et rien de plus.\nExemple : Un stagiaire en communication doit accéder au dossier \"Photos\", mais pas au dossier \"Comptabilité/Salaires\". Si son compte est compromis, le pirate sera bloqué à l\'entrée de la comptabilité.\nBYOD (Bring Your Own Device)\nDéfinition : Pratique autorisant les collaborateurs à utiliser leur matériel personnel (PC, smartphone) pour le travail.\nRisque : L\'ordinateur personnel est souvent moins sécurisé (jeux vidéo piratés, pas d\'antivirus). S\'il se connecte au réseau de l\'entreprise, il peut servir de \"cheval de Troie\" pour infecter tout le réseau professionnel.\nShadow IT (Informatique de l\'ombre)\nDéfinition : L\'utilisation par les employés de logiciels non approuvés par le service informatique pour contourner des restrictions.\nExemple : L\'entreprise sécurise les échanges via un serveur interne. Trouvant cela trop lent, les employés s\'envoient des fichiers confidentiels via WeTransfer ou leur Gmail personnel. L\'entreprise perd alors tout contrôle sur la sécurité de ces documents.\nSurface d\'attaque\nDéfinition : L\'ensemble des points d\'entrée qu\'un attaquant peut utiliser pour pénétrer dans un système. L\'objectif de la sécurité est de réduire cette surface.\nAnalogie : Une forteresse avec 50 portes et 100 fenêtres a une \"grande surface d\'attaque\" (difficile à surveiller). Si vous murez 40 portes et mettez des barreaux aux fenêtres, vous \"réduisez la surface d\'attaque\". En informatique, cela signifie désactiver les logiciels et ports inutiles.\nZero Trust (Zéro Confiance)\nDéfinition : Modèle de sécurité moderne qui part du principe qu\'aucun utilisateur ni appareil ne doit être approuvé par défaut, même s\'il est déjà à l\'intérieur du réseau.\nDevise : \"Ne jamais faire confiance, toujours vérifier.\"\nExemple : Dans un réseau classique (type château fort), une fois le pont-levis passé, on est libre. Dans un modèle Zero Trust, il y a un garde de sécurité devant chaque porte à l\'intérieur du château qui revérifie votre badge à chaque fois.\n\n'),
(5, 'Module 5', 'Guide Niveau Avancé', 'Intermédiaire', '<h1 class=\"ql-align-center\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">???? Module 5: Guide Niveau Avancé (Pro/Associatif)</span></h1><h3><br></h3><h3><em style=\"background-color: transparent; color: rgb(0, 0, 0);\">Principe du Moindre Privilège</em></h3><h3><br></h3><p><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Définition : Règle consistant à donner à un utilisateur </span><em style=\"background-color: transparent; color: rgb(0, 0, 0);\">uniquement</em><span style=\"background-color: transparent; color: rgb(0, 0, 0);\"> les accès strictement nécessaires à son travail, et rien de plus.</span></p><h4><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Exemple : Un stagiaire en communication doit accéder au dossier \"Photos\", mais pas au dossier \"Comptabilité/Salaires\". Si son compte est compromis, le pirate sera bloqué à l\'entrée de la comptabilité.</span></h4><p><br></p><h3><em style=\"color: rgb(0, 0, 0);\">BYOD (Bring Your Own Device)</em></h3><p><br></p><p><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Définition : Pratique autorisant les collaborateurs à utiliser leur matériel personnel (PC, smartphone) pour le travail.</span></p><p><span style=\"background-color: transparent; color: rgb(0, 0, 0);\"><span class=\"ql-cursor\">﻿</span></span></p><p><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Risque : L\'ordinateur personnel est souvent moins sécurisé (jeux vidéo piratés, pas d\'antivirus). S\'il se connecte au réseau de l\'entreprise, il peut servir de \"cheval de Troie\" pour infecter tout le réseau professionnel.</span></p><p><br></p><h3><em style=\"background-color: transparent; color: rgb(0, 0, 0);\">Shadow IT (Informatique de l\'ombre)</em></h3><p><br></p><p><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Définition : L\'utilisation par les employés de logiciels non approuvés par le service informatique pour contourner des restrictions.</span></p><p><br></p><p><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Exemple : L\'entreprise sécurise les échanges via un serveur interne. Trouvant cela trop lent, les employés s\'envoient des fichiers confidentiels via </span><em style=\"background-color: transparent; color: rgb(0, 0, 0);\">WeTransfer</em><span style=\"background-color: transparent; color: rgb(0, 0, 0);\"> ou leur </span><em style=\"background-color: transparent; color: rgb(0, 0, 0);\">Gmail personnel</em><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">. L\'entreprise perd alors tout contrôle sur la sécurité de ces documents.</span></p><h3><em style=\"background-color: transparent; color: rgb(0, 0, 0);\">Surface d\'attaque</em></h3><p><br></p><p><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Définition : L\'ensemble des points d\'entrée qu\'un attaquant peut utiliser pour pénétrer dans un système. L\'objectif de la sécurité est de réduire cette surface.</span></p><p><br></p><p><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Analogie : Une forteresse avec 50 portes et 100 fenêtres a une \"grande surface d\'attaque\" (difficile à surveiller). Si vous murez 40 portes et mettez des barreaux aux fenêtres, vous \"réduisez la surface d\'attaque\". En informatique, cela signifie désactiver les logiciels et ports inutiles.</span></p><h3><br></h3><h3><em style=\"background-color: transparent; color: rgb(0, 0, 0);\">Zero Trust (Zéro Confiance)</em></h3><p><br></p><p><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Définition : Modèle de sécurité moderne qui part du principe qu\'aucun utilisateur ni appareil ne doit être approuvé par défaut, même s\'il est déjà à l\'intérieur du réseau.</span></p><p><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Devise : \"Ne jamais faire confiance, toujours vérifier.\"</span></p><p><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Exemple : Dans un réseau classique (type château fort), une fois le pont-levis passé, on est libre. Dans un modèle Zero Trust, il y a un garde de sécurité devant </span><em style=\"background-color: transparent; color: rgb(0, 0, 0);\">chaque</em><span style=\"background-color: transparent; color: rgb(0, 0, 0);\"> porte à l\'intérieur du château qui revérifie votre badge à chaque fois.</span></p><h1 class=\"ql-align-center\"><br></h1><p><br></p>');

-- --------------------------------------------------------

--
-- Structure de la table `progression`
--

DROP TABLE IF EXISTS `progression`;
CREATE TABLE IF NOT EXISTS `progression` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `course_id` int NOT NULL,
  `completed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `course_id` (`course_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `progression`
--

INSERT INTO `progression` (`id`, `user_id`, `course_id`, `completed`) VALUES
(1, 1, 1, 1),
(2, 1, 3, 1),
(3, 1, 2, 1),
(4, 2, 3, 1),
(5, 3, 4, 1),
(6, 3, 1, 1),
(7, 3, 5, 1),
(8, 3, 2, 1),
(9, 3, 3, 1),
(10, 2, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `correct_option` char(1) NOT NULL,
  `explanation` text,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id`, `course_id`, `question_text`, `option_a`, `option_b`, `option_c`, `correct_option`, `explanation`) VALUES
(1, 1, 'L\'ingénierie sociale cible principalement :', 'Les failles humaines', 'Les failles logicielles', 'Les câbles réseaux', 'a', 'Le but est de tromper la personne, pas la machine.'),
(2, 1, 'Que faire face à un email suspect ?', 'Cliquer pour vérifier', 'Signaler et supprimer', 'Répondre à l\'expéditeur', 'b', 'Ne jamais interagir avec un contenu suspect.'),
(3, 2, 'Que signifie DDoS ?', 'Données Directes Sécurisées', 'Déni de Service Distribué', 'Disque Dur ou Système', 'b', 'Distributed Denial of Service : saturation d\'un service.'),
(4, 2, 'Quelle protection utiliser sur un WiFi public ?', 'Un VPN', 'Un bon écran de veille', 'Le mode avion', 'a', 'Le VPN chiffre vos données pour empêcher l\'écoute (Man in the Middle).'),
(5, 3, 'Une injection SQL permet de :', 'Voler ou modifier la base de données', 'Changer la couleur du site', 'Installer un virus sur le PC du client', 'a', 'Elle passe des commandes directement au moteur de base de données.'),
(6, 3, 'Une faille XSS s\'exécute sur :', 'Le serveur', 'Le navigateur de la victime', 'Le routeur Wifi', 'b', 'C\'est du code (souvent JavaScript) qui s\'exécute chez le client.'),
(8, 5, 'blabla', 'aaa', 'bb', 'ccc', 'b', 'oui');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `is_admin`) VALUES
(1, 'tom', 'tom@gmail.com', '$2y$10$PTvbjwIfyQ1So7r9UinW7eCdZMZY4yn2yT8/1serEU59tXtIe3LvG', '2025-12-03 10:52:51', 0),
(2, 'aaa', 'aaa@gmail.com', '$2y$10$ncfV0qofYTQpGm79C0dB6eTsVaGu8nIlo0xa6N.dFpn5m8XkfNjse', '2025-12-03 11:09:46', 0),
(3, 'Admin', 'admin@cybersens.com', '$2y$10$mZ0MrwEwGZxtNmb5gdQyg./YEgo1umTLEt5JUZoUl6N0GzT/hAIlq', '2025-12-03 13:25:57', 1),
(4, 'jules', 'hervieu@gmail.com', '$2y$10$MQPm7Rtu2ZUK6g/p6Qca4O0lX4yF7VIHyHk/ClBRpiOxwgR71wByC', '2026-01-07 08:22:25', 0),
(5, 'skozy', 'skozy@cybersens.com', '$2y$10$AQs9OkePyvWDOVQXOEG7XucxieXomtEMnBeBntkDIcmfBpui.yObq', '2026-01-07 08:58:09', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
