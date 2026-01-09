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
    <style>
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: var(--font-sans);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 2rem;
        }
        .install-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 3rem;
            max-width: 800px;
            width: 100%;
            box-shadow: var(--shadow-2xl);
            position: relative;
            overflow: hidden;
        }
        .install-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--accent-gradient);
        }
        h2 {
            margin-top: 0;
            color: var(--text-primary);
            font-family: 'Outfit', var(--font-sans);
            font-weight: 700;
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .log-container {
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 2rem;
            font-family: var(--font-mono);
            font-size: 0.9rem;
        }
        .log-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }
        .log-item:last-child {
            border-bottom: none;
        }
        .status-icon {
            width: 20px;
            height: 20px;
        }
        .success { color: var(--success); }
        .info { color: var(--info); }
        .warning { color: var(--warning); }
        .danger { color: var(--danger); }
        
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            border-radius: var(--radius-lg);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: 1px solid transparent;
        }
        .btn-primary {
            background: var(--accent-primary);
            color: white;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }
        .btn-primary:hover {
            background: var(--accent-primary-dark);
            transform: translateY(-2px);
        }
        .btn-outline {
            background: transparent;
            border-color: var(--border-color);
            color: var(--text-secondary);
        }
        .btn-outline:hover {
            border-color: var(--text-primary);
            color: var(--text-primary);
            background: var(--bg-tertiary);
        }
    </style>
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
                    // Check columns like role and is_protected
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

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
