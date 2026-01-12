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
    <title>Installation - CyberSens</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="css/install.css">
</head>
<body>
    <div class="bg-grid"></div>
    
    <div class="install-card">
        <h2><i data-lucide="database-zap"></i> Installation Système</h2>
        
        <div class="log-container">
            <?php
            $results = [];
            
            try {
                // 1. Tables users
                $result = $pdo->query("SHOW TABLES LIKE 'users'");
                if ($result->rowCount() == 0) {
                    $pdo->exec("CREATE TABLE users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(100) NOT NULL,
                        email VARCHAR(255) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        xp INT DEFAULT 0,
                        level VARCHAR(50) DEFAULT 'Novice',
                        role VARCHAR(20) DEFAULT 'user',
                        is_admin TINYINT(1) DEFAULT 0, 
                        is_protected TINYINT(1) DEFAULT 0,
                        group_id INT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )");
                    $results[] = ['success', 'Table "users" créée avec succès'];
                } else {
                    // Vérifier les colonnes comme role et is_protected
                    $cols = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
                    if(!in_array('role', $cols)) {
                        $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user'");
                        $results[] = ['success', 'Colonne "role" ajoutée'];
                    }
                     if(!in_array('is_protected', $cols)) {
                        $pdo->exec("ALTER TABLE users ADD COLUMN is_protected TINYINT(1) DEFAULT 0");
                        $results[] = ['success', 'Colonne "is_protected" ajoutée'];
                    }
                    $results[] = ['info', 'Table "users" vérifiée'];
                }

                // 2. Table courses
                $result = $pdo->query("SHOW TABLES LIKE 'courses'");
                if ($result->rowCount() == 0) {
                    $pdo->exec("CREATE TABLE courses (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        title VARCHAR(255) NOT NULL,
                        description TEXT,
                        content LONGTEXT,
                        difficulty VARCHAR(50) DEFAULT 'Facile',
                        icon VARCHAR(50) DEFAULT 'shield',
                        theme VARCHAR(50) DEFAULT 'blue',
                        xp_reward INT DEFAULT 25,
                        estimated_time INT DEFAULT 15,
                        is_hidden TINYINT(1) DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )");
                    $results[] = ['success', 'Table "courses" créée'];
                } else {
                    $results[] = ['info', 'Table "courses" existe déjà'];
                }

                // 3. Table questions
                $result = $pdo->query("SHOW TABLES LIKE 'questions'");
                if ($result->rowCount() == 0) {
                    $pdo->exec("CREATE TABLE questions (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        course_id INT NOT NULL,
                        question VARCHAR(255) NOT NULL,
                        option_a VARCHAR(255) NOT NULL,
                        option_b VARCHAR(255) NOT NULL,
                        option_c VARCHAR(255) NOT NULL,
                        option_d VARCHAR(255),
                        correct_answer CHAR(1) DEFAULT 'A',
                        explanation TEXT,
                        difficulty VARCHAR(20) DEFAULT 'Facile',
                        xp_reward INT DEFAULT 10,
                        points INT DEFAULT 10,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
                    )");
                    $results[] = ['success', 'Table "questions" créée'];
                } else {
                    $results[] = ['info', 'Table "questions" existe déjà'];
                }

                // 4. Progression
                $result = $pdo->query("SHOW TABLES LIKE 'progression'");
                if ($result->rowCount() == 0) {
                    $pdo->exec("CREATE TABLE progression (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL,
                        course_id INT NOT NULL,
                        score INT DEFAULT 0,
                        is_completed TINYINT(1) DEFAULT 0,
                        completed_at TIMESTAMP NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
                        UNIQUE KEY unique_progress (user_id, course_id)
                    )");
                    $results[] = ['success', 'Table "progression" créée'];
                } else {
                    $results[] = ['info', 'Table "progression" existe déjà'];
                }

                // 5. Create Super Admin
                $check = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'superadmin'")->fetchColumn();
                if ($check == 0) {
                    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, is_protected) VALUES (?, ?, ?, 'superadmin', 1)");
                    $stmt->execute(['SuperAdmin', 'admin@cybersens.local', $adminPassword]);
                    $results[] = ['warning', '<strong>Compte Super Admin créé :</strong> admin@cybersens.local / admin123'];
                }

            } catch (PDOException $e) {
                $results[] = ['danger', 'Erreur Base de données : ' . $e->getMessage()];
            }
            
            // Output results
            foreach ($results as $r) {
                $icon = $r[0] === 'success' ? 'check-circle' : ($r[0] === 'info' ? 'info' : ($r[0] === 'warning' ? 'alert-triangle' : 'x-circle'));
                $colorClass = $r[0];
                echo "<div class='log-item'>";
                echo "<i data-lucide='{$icon}' class='status-icon {$colorClass}'></i>";
                echo "<span class='{$colorClass}'>{$r[1]}</span>";
                echo "</div>";
            }
            ?>
        </div>
        
        <div class="actions">
            <a href="index.html" class="btn btn-primary"><i data-lucide="home"></i> Accéder au site</a>
            <a href="admin/index.php" class="btn btn-outline"><i data-lucide="shield"></i> Accéder à l'Admin</a>
        </div>
    </div>

    <script src="js/install.js"></script>
</body>
</html>
