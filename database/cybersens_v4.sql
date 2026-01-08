-- ============================================
-- CYBERSENS DATABASE v4.0
-- Base de données complète pour la plateforme
-- Date: 7 janvier 2026
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================
-- SUPPRESSION DES TABLES EXISTANTES
-- ============================================
DROP TABLE IF EXISTS user_notifications;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS user_badges;
DROP TABLE IF EXISTS badges;
DROP TABLE IF EXISTS phishing_results;
DROP TABLE IF EXISTS phishing_scenarios;
DROP TABLE IF EXISTS quiz_results;
DROP TABLE IF EXISTS progression;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS users;

-- ============================================
-- TABLE: users
-- Utilisateurs de la plateforme
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    xp INT DEFAULT 0,
    level INT DEFAULT 1,
    avatar VARCHAR(255) DEFAULT NULL,
    last_login DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_xp (xp DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: courses
-- Cours de cybersécurité
-- ============================================
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content LONGTEXT,
    difficulty ENUM('Facile', 'Intermédiaire', 'Difficile') DEFAULT 'Facile',
    icon VARCHAR(50) DEFAULT 'shield',
    theme VARCHAR(20) DEFAULT 'blue',
    xp_reward INT DEFAULT 25,
    estimated_time INT DEFAULT 15,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_difficulty (difficulty),
    INDEX idx_published (is_published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: questions
-- Questions de quiz pour les cours
-- ============================================
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    question TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_answer CHAR(1) NOT NULL,
    explanation TEXT,
    points INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_course (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: progression
-- Suivi de progression des utilisateurs
-- ============================================
CREATE TABLE progression (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    is_completed TINYINT(1) DEFAULT 0,
    score INT DEFAULT 0,
    best_score INT DEFAULT 0,
    attempts INT DEFAULT 0,
    time_spent INT DEFAULT 0,
    completed_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_course (user_id, course_id),
    INDEX idx_user (user_id),
    INDEX idx_completed (is_completed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: quiz_results
-- Historique détaillé des quiz
-- ============================================
CREATE TABLE quiz_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    correct_answers INT NOT NULL,
    time_taken INT DEFAULT 0,
    xp_earned INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_user_quiz (user_id),
    INDEX idx_course_quiz (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: badges
-- Badges et récompenses
-- ============================================
CREATE TABLE badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'award',
    color VARCHAR(20) DEFAULT '#6366f1',
    category ENUM('progression', 'quiz', 'phishing', 'special', 'streak') DEFAULT 'progression',
    requirement_type VARCHAR(50),
    requirement_value INT DEFAULT 0,
    xp_bonus INT DEFAULT 0,
    is_secret TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: user_badges
-- Badges obtenus par les utilisateurs
-- ============================================
CREATE TABLE user_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    badge_id INT NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_badge (user_id, badge_id),
    INDEX idx_user_badges (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: phishing_scenarios
-- Scénarios de simulation de phishing
-- ============================================
CREATE TABLE phishing_scenarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type ENUM('email', 'sms', 'website') DEFAULT 'email',
    sender VARCHAR(255),
    subject VARCHAR(255),
    content TEXT NOT NULL,
    is_phishing TINYINT(1) NOT NULL DEFAULT 1,
    difficulty ENUM('facile', 'moyen', 'difficile') DEFAULT 'facile',
    indicators TEXT,
    explanation TEXT,
    xp_reward INT DEFAULT 15,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_difficulty (difficulty)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: phishing_results
-- Résultats des exercices de phishing
-- ============================================
CREATE TABLE phishing_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    scenario_id INT NOT NULL,
    user_answer TINYINT(1) NOT NULL,
    is_correct TINYINT(1) NOT NULL,
    time_taken INT DEFAULT 0,
    xp_earned INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (scenario_id) REFERENCES phishing_scenarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_scenario (user_id, scenario_id),
    INDEX idx_user_phishing (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: resources
-- Ressources et documentation
-- ============================================
CREATE TABLE resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('article', 'video', 'tool', 'documentation', 'external') DEFAULT 'article',
    url VARCHAR(500),
    content LONGTEXT,
    icon VARCHAR(50) DEFAULT 'file-text',
    difficulty ENUM('debutant', 'intermediaire', 'avance') DEFAULT 'debutant',
    views INT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_difficulty (difficulty)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: notifications
-- Notifications système
-- ============================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    type ENUM('info', 'success', 'warning', 'achievement') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_notif (user_id),
    INDEX idx_unread (user_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DONNÉES: Utilisateurs par défaut
-- ============================================
INSERT INTO users (username, email, password, role, xp, level) VALUES
('admin', 'admin@cybersens.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 500, 5),
('demo', 'demo@cybersens.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 150, 2),
('test', 'test@cybersens.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 0, 1);
-- Mot de passe pour tous: password

-- ============================================
-- DONNÉES: Cours
-- ============================================
INSERT INTO courses (title, description, content, difficulty, icon, theme, xp_reward, estimated_time) VALUES
(
    'Introduction à la Cybersécurité',
    'Découvrez les bases de la sécurité informatique et les menaces courantes.',
    '<h2>Qu''est-ce que la cybersécurité ?</h2>
<p>La <strong>cybersécurité</strong> est l''ensemble des moyens techniques, organisationnels et humains mis en place pour protéger les systèmes informatiques, les réseaux et les données contre les attaques malveillantes.</p>

<h3>Les trois piliers de la sécurité</h3>
<ul>
<li><strong>Confidentialité</strong> : seules les personnes autorisées peuvent accéder aux informations</li>
<li><strong>Intégrité</strong> : les données ne peuvent pas être modifiées sans autorisation</li>
<li><strong>Disponibilité</strong> : les systèmes et données sont accessibles quand on en a besoin</li>
</ul>

<h3>Les principales menaces</h3>
<p>Les cyberattaques peuvent prendre de nombreuses formes :</p>
<ul>
<li><strong>Malwares</strong> : virus, ransomwares, chevaux de Troie</li>
<li><strong>Phishing</strong> : tentatives d''hameçonnage par email ou SMS</li>
<li><strong>Attaques par force brute</strong> : tentatives de deviner les mots de passe</li>
<li><strong>Ingénierie sociale</strong> : manipulation psychologique</li>
</ul>

<h3>Pourquoi c''est important ?</h3>
<p>En 2025, les cyberattaques coûtent des milliards d''euros aux entreprises et particuliers. Chaque individu est une cible potentielle.</p>',
    'Facile',
    'shield',
    'blue',
    25,
    15
),
(
    'Créer des mots de passe sécurisés',
    'Apprenez à créer et gérer des mots de passe robustes pour protéger vos comptes.',
    '<h2>L''importance des mots de passe</h2>
<p>Le mot de passe est souvent la <strong>première ligne de défense</strong> contre les intrusions. Un mot de passe faible, c''est comme laisser la porte de sa maison grande ouverte.</p>

<h3>Les caractéristiques d''un bon mot de passe</h3>
<ul>
<li><strong>Longueur</strong> : minimum 12 caractères (idéalement 16+)</li>
<li><strong>Complexité</strong> : mélange de majuscules, minuscules, chiffres et symboles</li>
<li><strong>Unicité</strong> : un mot de passe différent pour chaque compte</li>
<li><strong>Imprévisibilité</strong> : éviter les informations personnelles</li>
</ul>

<h3>La méthode de la phrase secrète</h3>
<p>Une technique efficace est de créer une phrase et de la transformer :</p>
<p><code>"J''aime le café le matin à 7h"</code> devient <code>J@1m3L3C@f3L3M@t1n@7h!</code></p>

<h3>Les gestionnaires de mots de passe</h3>
<p>Utilisez un gestionnaire comme <strong>Bitwarden</strong>, <strong>1Password</strong> ou <strong>Dashlane</strong> pour :</p>
<ul>
<li>Générer des mots de passe complexes</li>
<li>Les stocker de façon sécurisée</li>
<li>Les remplir automatiquement</li>
</ul>

<h3>L''authentification à deux facteurs (2FA)</h3>
<p>Activez toujours le 2FA quand c''est possible ! Même si votre mot de passe est compromis, le pirate ne pourra pas accéder à votre compte.</p>',
    'Facile',
    'key',
    'green',
    30,
    20
),
(
    'Reconnaître le Phishing',
    'Identifiez les tentatives d''hameçonnage et protégez-vous des arnaques en ligne.',
    '<h2>Qu''est-ce que le phishing ?</h2>
<p>Le <strong>phishing</strong> (ou hameçonnage) est une technique utilisée par les cybercriminels pour voler vos informations personnelles en se faisant passer pour une entité de confiance.</p>

<h3>Les différents types de phishing</h3>
<ul>
<li><strong>Email phishing</strong> : faux emails imitant des entreprises légitimes</li>
<li><strong>Smishing</strong> : phishing par SMS</li>
<li><strong>Vishing</strong> : phishing par téléphone</li>
<li><strong>Spear phishing</strong> : attaques ciblées et personnalisées</li>
</ul>

<h3>Comment reconnaître un email de phishing ?</h3>
<p><strong>🚩 Signaux d''alerte :</strong></p>
<ul>
<li>Adresse email suspecte (ex: support@amaz0n-secure.com)</li>
<li>Urgence excessive ("Votre compte sera bloqué dans 24h")</li>
<li>Fautes d''orthographe et de grammaire</li>
<li>Liens suspects (survolez sans cliquer !)</li>
<li>Demande d''informations sensibles</li>
<li>Pièces jointes inattendues</li>
</ul>

<h3>Que faire en cas de doute ?</h3>
<ol>
<li>Ne cliquez sur aucun lien</li>
<li>Ne téléchargez aucune pièce jointe</li>
<li>Contactez l''entreprise via son site officiel</li>
<li>Signalez l''email comme phishing</li>
</ol>',
    'Intermédiaire',
    'mail',
    'red',
    35,
    25
),
(
    'Sécuriser son réseau Wi-Fi',
    'Protégez votre réseau domestique contre les intrusions et les attaques.',
    '<h2>Pourquoi sécuriser son Wi-Fi ?</h2>
<p>Un réseau Wi-Fi non sécurisé est une porte ouverte pour les pirates. Ils peuvent intercepter vos données, utiliser votre connexion pour des activités illégales, ou accéder à vos appareils.</p>

<h3>Les bases de la sécurité Wi-Fi</h3>

<h4>1. Choisir le bon protocole de sécurité</h4>
<ul>
<li><strong>WPA3</strong> : le plus récent et sécurisé (recommandé)</li>
<li><strong>WPA2</strong> : encore acceptable si WPA3 non disponible</li>
<li><strong>WEP</strong> : obsolète et vulnérable, à éviter absolument !</li>
</ul>

<h4>2. Créer un mot de passe Wi-Fi solide</h4>
<p>Utilisez au moins 20 caractères avec un mélange de lettres, chiffres et symboles.</p>

<h4>3. Changer le nom du réseau (SSID)</h4>
<p>Évitez les noms par défaut qui révèlent le modèle de votre box (ex: "Livebox-A1B2").</p>

<h4>4. Désactiver le WPS</h4>
<p>Le WPS est pratique mais vulnérable aux attaques par force brute.</p>

<h3>Mesures avancées</h3>
<ul>
<li>Créer un réseau invité séparé</li>
<li>Filtrer les adresses MAC</li>
<li>Mettre à jour régulièrement le firmware de la box</li>
<li>Réduire la portée du signal si possible</li>
</ul>',
    'Intermédiaire',
    'wifi',
    'cyan',
    40,
    30
),
(
    'Protection contre les Malwares',
    'Comprenez les différents types de logiciels malveillants et comment vous en protéger.',
    '<h2>Qu''est-ce qu''un malware ?</h2>
<p>Un <strong>malware</strong> (logiciel malveillant) est tout programme conçu pour endommager, perturber ou prendre le contrôle d''un système informatique.</p>

<h3>Types de malwares</h3>

<h4>🦠 Virus</h4>
<p>Se propage en s''attachant à des fichiers légitimes. Nécessite une action de l''utilisateur pour s''activer.</p>

<h4>🐛 Ver (Worm)</h4>
<p>Se propage automatiquement sur le réseau sans intervention humaine.</p>

<h4>🐴 Cheval de Troie (Trojan)</h4>
<p>Se fait passer pour un logiciel légitime mais cache des fonctionnalités malveillantes.</p>

<h4>💰 Ransomware</h4>
<p>Chiffre vos fichiers et exige une rançon pour les récupérer. <strong>Ne payez jamais !</strong></p>

<h4>👀 Spyware</h4>
<p>Espionne votre activité : frappes clavier, historique de navigation, mots de passe...</p>

<h3>Comment se protéger ?</h3>
<ul>
<li><strong>Antivirus à jour</strong> : Windows Defender est suffisant pour la plupart des utilisateurs</li>
<li><strong>Mises à jour système</strong> : corrigent les failles de sécurité</li>
<li><strong>Téléchargements officiels</strong> : uniquement depuis les sources de confiance</li>
<li><strong>Sauvegardes régulières</strong> : la meilleure protection contre les ransomwares</li>
<li><strong>Vigilance</strong> : ne cliquez pas sur n''importe quoi !</li>
</ul>',
    'Difficile',
    'bug',
    'purple',
    50,
    35
);

-- ============================================
-- DONNÉES: Questions de quiz
-- ============================================
INSERT INTO questions (course_id, question, option_a, option_b, option_c, option_d, correct_answer, explanation, points) VALUES
-- Cours 1: Introduction à la Cybersécurité
(1, 'Quels sont les trois piliers de la sécurité informatique ?', 'Vitesse, Stockage, Réseau', 'Confidentialité, Intégrité, Disponibilité', 'Hardware, Software, Network', 'Antivirus, Firewall, VPN', 'B', 'La triade CIA (Confidentiality, Integrity, Availability) représente les trois objectifs fondamentaux de la sécurité informatique.', 10),
(1, 'Qu''est-ce qu''un ransomware ?', 'Un logiciel antivirus', 'Un type de firewall', 'Un malware qui chiffre les fichiers et demande une rançon', 'Un protocole de sécurité réseau', 'C', 'Un ransomware chiffre vos données et exige un paiement pour les récupérer. Ne payez jamais !', 10),
(1, 'Qu''est-ce que l''ingénierie sociale ?', 'La création de réseaux sociaux', 'La manipulation psychologique pour obtenir des informations', 'Un type de programmation', 'La maintenance des serveurs', 'B', 'L''ingénierie sociale exploite la psychologie humaine plutôt que les failles techniques.', 10),

-- Cours 2: Mots de passe
(2, 'Quelle est la longueur minimale recommandée pour un mot de passe sécurisé ?', '6 caractères', '8 caractères', '12 caractères', '4 caractères', 'C', 'Un mot de passe de 12 caractères minimum offre une bien meilleure protection contre les attaques par force brute.', 10),
(2, 'Qu''est-ce que l''authentification à deux facteurs (2FA) ?', 'Utiliser deux mots de passe différents', 'Une double vérification avec un code temporaire', 'Se connecter sur deux appareils', 'Changer son mot de passe deux fois', 'B', 'Le 2FA ajoute une couche de sécurité en demandant un code temporaire en plus du mot de passe.', 10),
(2, 'Quel est le problème avec la réutilisation des mots de passe ?', 'C''est plus difficile à retenir', 'Si un compte est compromis, tous les autres le sont aussi', 'Ça ralentit la connexion', 'Il n''y a pas de problème', 'B', 'Une fuite de données sur un site compromet tous vos comptes utilisant le même mot de passe.', 10),

-- Cours 3: Phishing
(3, 'Quel est le premier réflexe à avoir face à un email suspect ?', 'Cliquer sur les liens pour vérifier', 'Répondre pour demander des explications', 'Ne pas cliquer et vérifier l''expéditeur', 'Transférer à un ami', 'C', 'Ne jamais cliquer sur les liens d''un email suspect. Vérifiez toujours l''adresse de l''expéditeur.', 10),
(3, 'Comment reconnaître une URL suspecte ?', 'Elle contient des chiffres', 'Elle est trop longue', 'Elle imite un site connu avec des variations (amaz0n, g00gle)', 'Elle commence par https', 'C', 'Les URLs de phishing imitent souvent les sites légitimes avec de légères modifications.', 10),
(3, 'Qu''est-ce que le spear phishing ?', 'Un phishing via les réseaux sociaux', 'Un phishing ciblé et personnalisé', 'Un phishing par téléphone', 'Un logiciel anti-phishing', 'B', 'Le spear phishing cible des individus spécifiques avec des informations personnalisées pour être plus crédible.', 10),

-- Cours 4: Sécurité Wi-Fi
(4, 'Quel protocole de sécurité Wi-Fi est le plus sécurisé ?', 'WEP', 'WPA', 'WPA2', 'WPA3', 'D', 'WPA3 est le protocole le plus récent et le plus sécurisé pour les réseaux Wi-Fi.', 10),
(4, 'Pourquoi faut-il désactiver le WPS ?', 'Il consomme trop de batterie', 'Il est vulnérable aux attaques par force brute', 'Il ralentit la connexion', 'Il n''est pas compatible avec tous les appareils', 'B', 'Le WPS peut être cracké en quelques heures avec des outils spécialisés.', 10),
(4, 'Qu''est-ce qu''un réseau invité ?', 'Un réseau pour les employés', 'Un réseau séparé pour les visiteurs sans accès au réseau principal', 'Un réseau sans mot de passe', 'Un réseau temporaire', 'B', 'Un réseau invité isole les appareils des visiteurs de votre réseau principal et de vos données.', 10),

-- Cours 5: Malwares
(5, 'Quelle est la différence entre un virus et un ver ?', 'Un virus est plus dangereux', 'Un ver se propage automatiquement sans intervention humaine', 'Un ver est un ancien virus', 'Il n''y a pas de différence', 'B', 'Contrairement aux virus, les vers n''ont pas besoin d''action utilisateur pour se propager sur le réseau.', 10),
(5, 'Que faire en cas d''infection par un ransomware ?', 'Payer la rançon immédiatement', 'Éteindre l''ordinateur et ne plus l''utiliser', 'Déconnecter du réseau, ne pas payer, restaurer depuis une sauvegarde', 'Ignorer le message', 'C', 'Ne payez jamais ! Déconnectez-vous du réseau pour éviter la propagation et restaurez vos données depuis une sauvegarde.', 10),
(5, 'Quelle est la meilleure protection contre les ransomwares ?', 'Un antivirus payant', 'Des sauvegardes régulières sur support externe', 'Un VPN', 'Un pare-feu matériel', 'B', 'Les sauvegardes régulières permettent de récupérer vos données sans payer la rançon.', 10);

-- ============================================
-- DONNÉES: Badges
-- ============================================
INSERT INTO badges (name, description, icon, color, category, requirement_type, requirement_value, xp_bonus) VALUES
-- Badges de progression
('Premier Pas', 'Terminez votre premier cours', 'footprints', '#10b981', 'progression', 'courses_completed', 1, 10),
('Apprenti', 'Terminez 3 cours', 'book-open', '#3b82f6', 'progression', 'courses_completed', 3, 25),
('Expert', 'Terminez tous les cours', 'graduation-cap', '#8b5cf6', 'progression', 'courses_completed', 5, 50),
('Étudiant Assidu', 'Atteignez le niveau 5', 'trending-up', '#f59e0b', 'progression', 'level', 5, 30),
('Maître Cyber', 'Atteignez le niveau 10', 'crown', '#eab308', 'progression', 'level', 10, 100),

-- Badges de quiz
('Premier Quiz', 'Réussissez votre premier quiz', 'check-circle', '#22c55e', 'quiz', 'quiz_completed', 1, 10),
('Sans Faute', 'Obtenez 100% à un quiz', 'star', '#fbbf24', 'quiz', 'perfect_quiz', 1, 25),
('Génie', 'Obtenez 100% à 5 quiz différents', 'brain', '#ec4899', 'quiz', 'perfect_quiz', 5, 75),

-- Badges de phishing
('Œil de Lynx', 'Identifiez correctement 5 tentatives de phishing', 'eye', '#06b6d4', 'phishing', 'phishing_detected', 5, 20),
('Détective', 'Identifiez correctement 10 tentatives de phishing', 'search', '#6366f1', 'phishing', 'phishing_detected', 10, 40),
('Incorruptible', 'Ne tombez dans aucun piège de phishing (10 scénarios)', 'shield-check', '#dc2626', 'phishing', 'phishing_perfect', 10, 60),

-- Badges spéciaux
('Bienvenue', 'Créez votre compte CyberSens', 'user-plus', '#8b5cf6', 'special', 'account_created', 1, 5);

-- ============================================
-- DONNÉES: Scénarios de phishing
-- ============================================
INSERT INTO phishing_scenarios (title, type, sender, subject, content, is_phishing, difficulty, indicators, explanation, xp_reward) VALUES
(
    'Email bancaire urgent',
    'email',
    'securite@bnp-paribas-secure.com',
    'URGENT: Votre compte sera bloqué',
    'Cher client,

Nous avons détecté une activité suspecte sur votre compte. Pour éviter le blocage, veuillez confirmer vos informations en cliquant sur le lien ci-dessous dans les 24h.

[Vérifier mon compte]

Cordialement,
Service Sécurité BNP Paribas',
    1,
    'facile',
    'Adresse email suspecte (bnp-paribas-secure.com au lieu de bnpparibas.com), Urgence excessive, Demande de cliquer sur un lien, Pas de personnalisation',
    'C''est du PHISHING ! L''adresse email n''est pas celle de BNP Paribas (bnpparibas.net). Une vraie banque ne vous demandera jamais de confirmer vos informations par email avec un lien.',
    15
),
(
    'Notification Amazon',
    'email',
    'ship-confirm@amazon.fr',
    'Votre commande #402-8756321 a été expédiée',
    'Bonjour,

Bonne nouvelle ! Votre commande a été expédiée et arrivera le 15 janvier.

Numéro de suivi : 1Z999AA10123456784

Détails de la commande :
- Echo Dot (4ème génération) - 49,99€

Suivre ma livraison : https://amazon.fr/track/1Z999AA10123456784

L''équipe Amazon',
    0,
    'facile',
    'Adresse email légitime (@amazon.fr), Informations spécifiques et cohérentes, Lien vers le domaine officiel amazon.fr, Pas de demande d''informations sensibles',
    'Cet email est LÉGITIME. L''adresse provient bien d''amazon.fr, le contenu est informatif sans urgence ni menace, et le lien pointe vers le domaine officiel.',
    15
),
(
    'Remboursement impôts',
    'email',
    'ne-pas-repondre@impots-gouv-remboursement.fr',
    'Vous avez un remboursement de 847,50€ en attente',
    'Madame, Monsieur,

Suite à votre déclaration de revenus, vous bénéficiez d''un remboursement de 847,50€.

Pour recevoir votre virement sous 48h, veuillez mettre à jour vos coordonnées bancaires :

[Mettre à jour mes informations]

Direction Générale des Finances Publiques',
    1,
    'moyen',
    'Domaine suspect (impots-gouv-remboursement.fr), Les impôts n''envoient jamais ce type d''email, Demande de coordonnées bancaires, Montant précis pour appâter',
    'C''est du PHISHING ! Le site officiel des impôts est impots.gouv.fr. L''administration fiscale ne demande jamais vos coordonnées bancaires par email.',
    20
),
(
    'SMS colis en attente',
    'sms',
    '+33644582147',
    'La Poste',
    'La Poste: Votre colis est en attente de livraison. Payez les frais de port (1,99€) pour le recevoir: https://laposte-livraison.info/tracking',
    1,
    'moyen',
    'Numéro de téléphone inconnu, URL suspecte (laposte-livraison.info), Demande de paiement inattendue, La Poste ne demande pas de paiement par SMS',
    'C''est du SMISHING ! La Poste n''envoie pas de SMS demandant un paiement. L''URL n''est pas le site officiel (laposte.fr).',
    20
),
(
    'Mise à jour LinkedIn',
    'email',
    'messages-noreply@linkedin.com',
    'Vous avez 3 nouvelles invitations',
    'Bonjour Jean,

Vous avez 3 nouvelles invitations de connexion :

- Marie Dupont, Directrice Marketing chez TechCorp
- Pierre Martin, Développeur Senior
- Sophie Bernard, RH Manager

Voir mes invitations : https://www.linkedin.com/mynetwork/invitation-manager/

Cordialement,
L''équipe LinkedIn',
    0,
    'facile',
    'Adresse email officielle LinkedIn, Lien vers linkedin.com (vérifiable au survol), Contenu cohérent avec les fonctionnalités LinkedIn, Pas de demande urgente',
    'Cet email est LÉGITIME. Il provient d''une adresse officielle LinkedIn et le lien pointe vers le vrai site.',
    15
),
(
    'Support Microsoft',
    'email',
    'support@microsoft-account-verification.com',
    'Action requise: Votre compte Microsoft expire',
    'Attention,

Votre compte Microsoft Office 365 expire dans 24 heures. Pour éviter la perte de vos données, renouvelez immédiatement :

[Renouveler maintenant - GRATUIT]

Si vous ne renouvelez pas, vous perdrez l''accès à :
- Vos emails Outlook
- Vos fichiers OneDrive
- Votre licence Office

Microsoft Support Team',
    1,
    'moyen',
    'Domaine email non officiel (microsoft-account-verification.com), Urgence et menace de perte de données, Les comptes Microsoft n''expirent pas comme ça, Bouton suspect',
    'C''est du PHISHING ! Microsoft utilise microsoft.com pour ses emails. Un compte Microsoft personnel n''expire pas et cette urgence est fausse.',
    20
),
(
    'Offre d''emploi attractive',
    'email',
    'recrutement@entreprise-job.net',
    'Poste à 4500€/mois - Télétravail 100%',
    'Félicitations !

Votre profil a retenu notre attention pour un poste de Gestionnaire Administratif :

💰 Salaire : 4500€ net/mois
🏠 100% télétravail
⏰ 25h/semaine
✅ Aucune expérience requise

Pour postuler, envoyez-nous :
- Copie de votre carte d''identité
- RIB pour le versement du salaire

Répondez vite, il ne reste que 3 places !

Service Recrutement',
    1,
    'difficile',
    'Offre trop belle pour être vraie, Demande de documents sensibles (CNI, RIB), Urgence artificielle, Domaine email générique, Pas de nom d''entreprise réel',
    'C''est du PHISHING et une tentative d''arnaque ! Aucun employeur légitime ne demande votre CNI et RIB avant un entretien. Cette offre irréaliste vise à voler votre identité.',
    25
),
(
    'Fausse page de connexion',
    'website',
    'https://facebook-login.secure-auth.net',
    'Connexion Facebook',
    'Votre session a expiré. Veuillez vous reconnecter pour continuer.

[Champ email]
[Champ mot de passe]
[Bouton Se connecter]

Mot de passe oublié ? | Créer un compte',
    1,
    'moyen',
    'URL qui n''est pas facebook.com, Domaine suspect secure-auth.net, Page imitant Facebook, Demande de connexion inattendue',
    'C''est du PHISHING ! L''URL n''est pas facebook.com. C''est une fausse page de connexion destinée à voler vos identifiants.',
    20
),
(
    'Newsletter légitime FNAC',
    'email',
    'newsletter@fnac.com',
    'Les offres de la semaine',
    'Cher client Fnac,

Découvrez nos offres exceptionnelles cette semaine :

📱 iPhone 15 - 899€ (-10%)
🎮 PS5 + 2 manettes - 499€
📚 3 livres achetés = 1 offert

Voir toutes les offres : https://www.fnac.com/promo

Se désabonner | Préférences email

FNAC SA - 9 rue des Bateaux-Lavoirs, 94200 Ivry-sur-Seine',
    0,
    'facile',
    'Adresse email officielle @fnac.com, Lien vers fnac.com, Mentions légales présentes, Option de désabonnement, Pas de demande d''informations personnelles',
    'Cet email est LÉGITIME. C''est une newsletter commerciale classique de la Fnac avec tous les éléments d''un email professionnel.',
    15
),
(
    'Héritage surprise',
    'email',
    'avocat.succession@gmail.com',
    'Succession de M. Jean DUPONT - 2.5 millions EUR',
    'Madame, Monsieur,

Je suis Maître Bernard, notaire. Je vous contacte concernant la succession de M. Jean DUPONT, décédé sans héritier.

Vous avez été désigné(e) comme bénéficiaire d''un héritage de 2 500 000 EUR.

Pour débloquer ces fonds, merci d''envoyer :
- Vos coordonnées complètes
- Une copie de votre passeport
- Vos coordonnées bancaires
- Frais de dossier : 350€

Maître Pierre Bernard
Notaire - Paris',
    1,
    'facile',
    'Arnaque classique à l''héritage, Adresse gmail (pas professionnelle pour un notaire), Demande de frais à l''avance, Demande de documents d''identité, Promesse d''argent d''un inconnu',
    'C''est une ARNAQUE classique ! Vous ne pouvez pas hériter d''un inconnu. Un vrai notaire n''utiliserait jamais gmail et ne demanderait jamais de frais à l''avance.',
    15
);

-- ============================================
-- DONNÉES: Ressources
-- ============================================
INSERT INTO resources (title, description, category, url, content, icon, difficulty) VALUES
(
    'Les bases de la cybersécurité',
    'Comprendre les fondamentaux de la sécurité informatique : menaces, risques et bonnes pratiques.',
    'article',
    NULL,
    '## Introduction à la cybersécurité

La cybersécurité est devenue un enjeu majeur dans notre monde connecté. Cet article vous présente les concepts fondamentaux.

### Les menaces principales

- **Malwares** : virus, ransomwares, spywares
- **Phishing** : tentatives d''hameçonnage
- **Attaques réseau** : man-in-the-middle, DDoS
- **Ingénierie sociale** : manipulation humaine

### Les bonnes pratiques

1. Utilisez des mots de passe forts et uniques
2. Activez l''authentification à deux facteurs
3. Maintenez vos logiciels à jour
4. Méfiez-vous des emails suspects
5. Sauvegardez régulièrement vos données',
    'shield',
    'debutant'
),
(
    'Guide des mots de passe sécurisés',
    'Comment créer et gérer des mots de passe robustes pour protéger vos comptes.',
    'article',
    NULL,
    '## Créer un mot de passe fort

Un bon mot de passe doit contenir :
- Au moins 12 caractères
- Des majuscules et minuscules
- Des chiffres
- Des caractères spéciaux (!@#$%...)

## Méthode de la phrase secrète

Prenez une phrase que vous retenez facilement et transformez-la :
"J''aime le café le matin à 7h" → "J@1m3L3C@f3L3M@t1n@7h!"

## Gestionnaires de mots de passe

Utilisez un gestionnaire comme :
- Bitwarden (gratuit, open source)
- 1Password
- Dashlane',
    'key',
    'debutant'
),
(
    'Comprendre le chiffrement',
    'Introduction aux concepts de chiffrement et cryptographie pour protéger vos données.',
    'article',
    NULL,
    '## Qu''est-ce que le chiffrement ?

Le chiffrement transforme des données lisibles en données illisibles sans la clé de déchiffrement.

### Types de chiffrement

**Symétrique** : même clé pour chiffrer et déchiffrer
- Exemple : AES-256

**Asymétrique** : clé publique + clé privée
- Exemple : RSA, utilisé pour HTTPS

### Où est-ce utilisé ?

- HTTPS pour les sites web
- Messageries chiffrées (Signal, WhatsApp)
- VPN
- Disques durs chiffrés',
    'lock',
    'intermediaire'
),
(
    'Sécuriser son smartphone',
    'Tutoriel vidéo sur les paramètres de sécurité essentiels pour Android et iOS.',
    'video',
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    NULL,
    'smartphone',
    'debutant'
),
(
    'Have I Been Pwned',
    'Vérifiez si votre email a été compromis dans une fuite de données.',
    'tool',
    'https://haveibeenpwned.com/',
    NULL,
    'search',
    'debutant'
),
(
    'VirusTotal',
    'Analysez des fichiers et URLs suspects avec plusieurs antivirus.',
    'tool',
    'https://www.virustotal.com/',
    NULL,
    'shield-check',
    'debutant'
),
(
    'Bitwarden',
    'Gestionnaire de mots de passe gratuit et open source.',
    'tool',
    'https://bitwarden.com/',
    NULL,
    'key',
    'debutant'
),
(
    'Guide ANSSI - Bonnes pratiques',
    'Recommandations officielles de l''Agence Nationale de la Sécurité des Systèmes d''Information.',
    'documentation',
    'https://www.ssi.gouv.fr/guide/guide-dhygiene-informatique/',
    NULL,
    'book-open',
    'intermediaire'
),
(
    'Cybermalveillance.gouv.fr',
    'Plateforme gouvernementale d''assistance aux victimes de cybermalveillance.',
    'external',
    'https://www.cybermalveillance.gouv.fr/',
    NULL,
    'external-link',
    'debutant'
);

-- ============================================
-- DONNÉES: Badges attribués aux utilisateurs de démo
-- ============================================
INSERT INTO user_badges (user_id, badge_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 6), (1, 7), (1, 9), (1, 12),
(2, 1), (2, 6), (2, 12),
(3, 12);

-- ============================================
-- DONNÉES: Progression des utilisateurs de démo
-- ============================================
INSERT INTO progression (user_id, course_id, is_completed, score, best_score, attempts, completed_at) VALUES
(1, 1, 1, 100, 100, 1, NOW()),
(1, 2, 1, 100, 100, 2, NOW()),
(1, 3, 1, 90, 90, 1, NOW()),
(1, 4, 1, 80, 80, 3, NOW()),
(1, 5, 1, 85, 85, 2, NOW()),
(2, 1, 1, 80, 80, 2, NOW()),
(2, 2, 1, 70, 70, 1, NOW()),
(2, 3, 0, 50, 50, 1, NULL);

-- ============================================
-- DONNÉES: Notifications de bienvenue
-- ============================================
INSERT INTO notifications (user_id, title, message, type) VALUES
(2, 'Bienvenue sur CyberSens!', 'Commencez votre apprentissage de la cybersécurité dès maintenant!', 'info'),
(2, 'Badge débloqué!', 'Vous avez obtenu le badge "Bienvenue"', 'achievement'),
(3, 'Bienvenue sur CyberSens!', 'Créez votre premier cours pour gagner des points XP!', 'info');

-- ============================================
-- FIN DU SCRIPT
-- ============================================
SELECT 'Base de données CyberSens v4.0 installée avec succès!' AS Status;
