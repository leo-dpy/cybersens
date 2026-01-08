<?php
/**
 * Script d'installation de la base de données pour CyberSens
 * Exécutez ce script UNE FOIS pour créer toutes les tables nécessaires
 */

require 'backend/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation BDD - CyberSens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #050505 0%, #1a1a2e 100%);
            min-height: 100vh;
            padding: 2rem;
            color: #e0e0e0;
        }
        .card { 
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px; 
        }
        .alert { border-radius: 8px; }
        h2 { color: #00f3ff; }
        .btn-primary { 
            background: #00f3ff; 
            border: none; 
            color: #000;
        }
        .btn-primary:hover { 
            background: #00d4e0; 
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4"><i class="bi bi-database-gear me-2"></i>Installation de la base de données CyberSens</h2>
                        
                        <?php
                        $results = [];
                        
                        try {
                            // 1. Vérifier/créer la table users
                            $result = $pdo->query("SHOW TABLES LIKE 'users'");
                            if ($result->rowCount() == 0) {
                                $pdo->exec("CREATE TABLE users (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    username VARCHAR(100) NOT NULL,
                                    email VARCHAR(255) NOT NULL UNIQUE,
                                    password VARCHAR(255) NOT NULL,
                                    xp INT DEFAULT 0,
                                    level VARCHAR(50) DEFAULT 'Novice',
                                    is_admin TINYINT(1) DEFAULT 0,
                                    group_id INT NULL,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                                )");
                                $results[] = ['success', 'Table users créée'];
                            } else {
                                // Vérifier si la colonne is_admin existe
                                $check = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
                                if ($check->rowCount() == 0) {
                                    $pdo->exec("ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0");
                                    $results[] = ['success', 'Colonne is_admin ajoutée à users'];
                                }
                                $results[] = ['info', 'Table users existe déjà'];
                            }

                            // 2. Vérifier/créer la table courses
                            $result = $pdo->query("SHOW TABLES LIKE 'courses'");
                            if ($result->rowCount() == 0) {
                                $pdo->exec("CREATE TABLE courses (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    title VARCHAR(255) NOT NULL,
                                    description TEXT,
                                    content LONGTEXT,
                                    difficulty VARCHAR(50) DEFAULT 'Facile',
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                                )");
                                $results[] = ['success', 'Table courses créée'];
                            } else {
                                $results[] = ['info', 'Table courses existe déjà'];
                            }

                            // 3. Vérifier/créer la table questions
                            $result = $pdo->query("SHOW TABLES LIKE 'questions'");
                            if ($result->rowCount() == 0) {
                                $pdo->exec("CREATE TABLE questions (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    course_id INT NOT NULL,
                                    question_text TEXT NOT NULL,
                                    option_a VARCHAR(255) NOT NULL,
                                    option_b VARCHAR(255) NOT NULL,
                                    option_c VARCHAR(255) NOT NULL,
                                    correct_option CHAR(1) DEFAULT 'A',
                                    explanation TEXT,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
                                )");
                                $results[] = ['success', 'Table questions créée'];
                            } else {
                                $results[] = ['info', 'Table questions existe déjà'];
                            }

                            // 4. Vérifier/créer la table progression
                            $result = $pdo->query("SHOW TABLES LIKE 'progression'");
                            if ($result->rowCount() == 0) {
                                $pdo->exec("CREATE TABLE progression (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    user_id INT NOT NULL,
                                    course_id INT NOT NULL,
                                    score INT DEFAULT 0,
                                    completed TINYINT(1) DEFAULT 0,
                                    completed_at TIMESTAMP NULL,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                                    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
                                    UNIQUE KEY unique_progress (user_id, course_id)
                                )");
                                $results[] = ['success', 'Table progression créée'];
                            } else {
                                $results[] = ['info', 'Table progression existe déjà'];
                            }

                            // 5. Vérifier/créer la table groups
                            $result = $pdo->query("SHOW TABLES LIKE 'groups'");
                            if ($result->rowCount() == 0) {
                                $pdo->exec("CREATE TABLE groups (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    name VARCHAR(100) NOT NULL UNIQUE,
                                    description TEXT,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                                )");
                                // Insérer les groupes par défaut
                                $pdo->exec("INSERT INTO groups (name) VALUES ('Red Team'), ('Blue Team'), ('Purple Team'), ('Staff')");
                                $results[] = ['success', 'Table groups créée avec groupes par défaut'];
                            } else {
                                $results[] = ['info', 'Table groups existe déjà'];
                            }

                            // 6. Créer un admin par défaut si aucun n'existe
                            $check = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 1")->fetchColumn();
                            if ($check == 0) {
                                $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
                                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, 1)");
                                $stmt->execute(['Admin', 'admin@cybersens.local', $adminPassword]);
                                $results[] = ['warning', 'Compte admin créé: admin@cybersens.local / admin123 (Changez ce mot de passe!)'];
                            }

                        } catch (PDOException $e) {
                            $results[] = ['danger', 'Erreur: ' . $e->getMessage()];
                        }
                        
                        // Afficher les résultats
                        foreach ($results as $r) {
                            $icon = $r[0] === 'success' ? 'check-circle' : ($r[0] === 'info' ? 'info-circle' : ($r[0] === 'warning' ? 'exclamation-triangle' : 'x-circle'));
                            echo "<div class='alert alert-{$r[0]} d-flex align-items-center mb-2'>";
                            echo "<i class='bi bi-{$icon} me-2'></i>";
                            echo "<div>{$r[1]}</div>";
                            echo "</div>";
                        }
                        ?>
                        
                        <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
                        <p class="text-success fw-bold mb-3"><i class="bi bi-check-circle-fill me-2"></i>Installation terminée !</p>
                        <div class="d-flex gap-2">
                            <a href="index.html" class="btn btn-primary"><i class="bi bi-house me-2"></i>Accéder au site</a>
                            <a href="admin/index.php" class="btn btn-outline-light"><i class="bi bi-shield me-2"></i>Accéder à l'admin</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
