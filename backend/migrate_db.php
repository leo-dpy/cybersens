<?php
/**
 * Script de migration pour adapter la base de données à la nouvelle structure
 * Exécuter une seule fois : http://localhost/Cybersens/backend/migrate_db.php
 */

header('Content-Type: text/html; charset=utf-8');
echo "<h1>Migration de la base de données CyberSens</h1>";

require 'db.php';

$migrations = [];

try {
    // Vérifier la structure de la table progression
    echo "<h2>Table progression</h2>";
    $columns = $pdo->query("SHOW COLUMNS FROM progression")->fetchAll(PDO::FETCH_COLUMN);
    
    // Ajouter is_completed si elle n'existe pas
    if (!in_array('is_completed', $columns)) {
        // Vérifier si completed existe
        if (in_array('completed', $columns)) {
            // Renommer completed en is_completed
            try {
                $pdo->exec("ALTER TABLE progression CHANGE completed is_completed TINYINT(1) DEFAULT 0");
                $migrations[] = "✅ Colonne 'completed' renommée en 'is_completed' dans progression";
            } catch (PDOException $e) {
                // Si erreur, ajouter une nouvelle colonne
                $pdo->exec("ALTER TABLE progression ADD COLUMN is_completed TINYINT(1) DEFAULT 0");
                $pdo->exec("UPDATE progression SET is_completed = completed WHERE completed IS NOT NULL");
                $migrations[] = "✅ Colonne 'is_completed' ajoutée et synchronisée avec 'completed'";
            }
        } else {
            $pdo->exec("ALTER TABLE progression ADD COLUMN is_completed TINYINT(1) DEFAULT 0");
            $migrations[] = "✅ Colonne 'is_completed' ajoutée dans progression";
        }
    } else {
        $migrations[] = "ℹ️ Colonne 'is_completed' existe déjà dans progression";
    }
    
    // Ajouter completed_at si elle n'existe pas
    if (!in_array('completed_at', $columns)) {
        $pdo->exec("ALTER TABLE progression ADD COLUMN completed_at DATETIME DEFAULT NULL");
        $migrations[] = "✅ Colonne 'completed_at' ajoutée dans progression";
    }
    
    // Ajouter score si elle n'existe pas
    if (!in_array('score', $columns)) {
        $pdo->exec("ALTER TABLE progression ADD COLUMN score INT DEFAULT 0");
        $migrations[] = "✅ Colonne 'score' ajoutée dans progression";
    }
    
    // Ajouter best_score si elle n'existe pas
    if (!in_array('best_score', $columns)) {
        $pdo->exec("ALTER TABLE progression ADD COLUMN best_score INT DEFAULT 0");
        $migrations[] = "✅ Colonne 'best_score' ajoutée dans progression";
    }
    
    // Ajouter attempts si elle n'existe pas
    if (!in_array('attempts', $columns)) {
        $pdo->exec("ALTER TABLE progression ADD COLUMN attempts INT DEFAULT 0");
        $migrations[] = "✅ Colonne 'attempts' ajoutée dans progression";
    }
    
    echo "<ul>";
    foreach ($migrations as $m) echo "<li>$m</li>";
    echo "</ul>";
    
    // Vérifier la structure de la table badges
    echo "<h2>Table badges</h2>";
    $migrations = [];
    
    $badgeColumns = $pdo->query("SHOW COLUMNS FROM badges")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('requirement_type', $badgeColumns)) {
        if (in_array('condition_type', $badgeColumns)) {
            $pdo->exec("ALTER TABLE badges CHANGE condition_type requirement_type VARCHAR(50)");
            $migrations[] = "✅ Colonne 'condition_type' renommée en 'requirement_type'";
        } else {
            $pdo->exec("ALTER TABLE badges ADD COLUMN requirement_type VARCHAR(50) DEFAULT NULL");
            $migrations[] = "✅ Colonne 'requirement_type' ajoutée";
        }
    }
    
    if (!in_array('requirement_value', $badgeColumns)) {
        if (in_array('condition_value', $badgeColumns)) {
            $pdo->exec("ALTER TABLE badges CHANGE condition_value requirement_value INT DEFAULT 0");
            $migrations[] = "✅ Colonne 'condition_value' renommée en 'requirement_value'";
        } else {
            $pdo->exec("ALTER TABLE badges ADD COLUMN requirement_value INT DEFAULT 0");
            $migrations[] = "✅ Colonne 'requirement_value' ajoutée";
        }
    }
    
    if (!in_array('color', $badgeColumns)) {
        $pdo->exec("ALTER TABLE badges ADD COLUMN color VARCHAR(20) DEFAULT '#6366f1'");
        $migrations[] = "✅ Colonne 'color' ajoutée";
    }
    
    if (!in_array('category', $badgeColumns)) {
        $pdo->exec("ALTER TABLE badges ADD COLUMN category VARCHAR(50) DEFAULT 'progression'");
        $migrations[] = "✅ Colonne 'category' ajoutée";
    }
    
    if (!in_array('xp_bonus', $badgeColumns)) {
        $pdo->exec("ALTER TABLE badges ADD COLUMN xp_bonus INT DEFAULT 0");
        $migrations[] = "✅ Colonne 'xp_bonus' ajoutée";
    }
    
    if (!in_array('is_secret', $badgeColumns)) {
        $pdo->exec("ALTER TABLE badges ADD COLUMN is_secret TINYINT(1) DEFAULT 0");
        $migrations[] = "✅ Colonne 'is_secret' ajoutée";
    }
    
    echo "<ul>";
    foreach ($migrations as $m) echo "<li>$m</li>";
    if (empty($migrations)) echo "<li>ℹ️ Aucune migration nécessaire</li>";
    echo "</ul>";
    
    // Vérifier la structure de la table user_badges
    echo "<h2>Table user_badges</h2>";
    $migrations = [];
    
    $ubColumns = $pdo->query("SHOW COLUMNS FROM user_badges")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('earned_at', $ubColumns)) {
        if (in_array('unlocked_at', $ubColumns)) {
            $pdo->exec("ALTER TABLE user_badges CHANGE unlocked_at earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
            $migrations[] = "✅ Colonne 'unlocked_at' renommée en 'earned_at'";
        } else {
            $pdo->exec("ALTER TABLE user_badges ADD COLUMN earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
            $migrations[] = "✅ Colonne 'earned_at' ajoutée";
        }
    }
    
    echo "<ul>";
    foreach ($migrations as $m) echo "<li>$m</li>";
    if (empty($migrations)) echo "<li>ℹ️ Aucune migration nécessaire</li>";
    echo "</ul>";
    
    // Vérifier la structure de la table courses
    echo "<h2>Table courses</h2>";
    $migrations = [];
    
    $courseColumns = $pdo->query("SHOW COLUMNS FROM courses")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('icon', $courseColumns)) {
        $pdo->exec("ALTER TABLE courses ADD COLUMN icon VARCHAR(50) DEFAULT 'shield'");
        $migrations[] = "✅ Colonne 'icon' ajoutée";
    }
    
    if (!in_array('theme', $courseColumns)) {
        $pdo->exec("ALTER TABLE courses ADD COLUMN theme VARCHAR(20) DEFAULT 'blue'");
        $migrations[] = "✅ Colonne 'theme' ajoutée";
    }
    
    if (!in_array('xp_reward', $courseColumns)) {
        $pdo->exec("ALTER TABLE courses ADD COLUMN xp_reward INT DEFAULT 25");
        $migrations[] = "✅ Colonne 'xp_reward' ajoutée";
    }
    
    if (!in_array('estimated_time', $courseColumns)) {
        $pdo->exec("ALTER TABLE courses ADD COLUMN estimated_time INT DEFAULT 15");
        $migrations[] = "✅ Colonne 'estimated_time' ajoutée";
    }
    
    echo "<ul>";
    foreach ($migrations as $m) echo "<li>$m</li>";
    if (empty($migrations)) echo "<li>ℹ️ Aucune migration nécessaire</li>";
    echo "</ul>";
    
    // Créer la table certificates si elle n'existe pas
    echo "<h2>Table certificates</h2>";
    
    $tables = $pdo->query("SHOW TABLES LIKE 'certificates'")->fetchAll();
    if (empty($tables)) {
        $pdo->exec("CREATE TABLE certificates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            course_id INT NOT NULL,
            certificate_code VARCHAR(50) NOT NULL UNIQUE,
            score INT DEFAULT 0,
            issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_course (user_id, course_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<ul><li>✅ Table 'certificates' créée</li></ul>";
    } else {
        echo "<ul><li>ℹ️ Table 'certificates' existe déjà</li></ul>";
    }
    
    echo "<h2 style='color: green;'>✅ Migration terminée avec succès !</h2>";
    echo "<p><a href='../index.html'>Retour à l'application</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Erreur lors de la migration</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
