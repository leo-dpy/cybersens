<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require 'db.php';

try {
    $result = [
        'database' => $dbname,
        'connection' => 'OK',
        'tables' => [],
        'issues' => []
    ];
    
    // Lister les tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $result['tables'] = $tables;
    
    // Vérifier la structure de users
    if (in_array('users', $tables)) {
        $columns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
        $result['users_columns'] = $columns;
        
        // Vérifier les colonnes critiques
        if (!in_array('role', $columns) && !in_array('is_admin', $columns)) {
            $result['issues'][] = "Table users: ni 'role' ni 'is_admin' trouvé";
        }
        if (!in_array('xp', $columns)) {
            $result['issues'][] = "Table users: colonne 'xp' manquante";
        }
        if (!in_array('level', $columns)) {
            $result['issues'][] = "Table users: colonne 'level' manquante";
        }
        
        // Compter les utilisateurs
        $result['users_count'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        
        // Lister les admins
        if (in_array('role', $columns)) {
            $admins = $pdo->query("SELECT username, email FROM users WHERE role = 'admin'")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $admins = $pdo->query("SELECT username, email FROM users WHERE is_admin = 1")->fetchAll(PDO::FETCH_ASSOC);
        }
        $result['admins'] = $admins;
    } else {
        $result['issues'][] = "Table 'users' non trouvée!";
    }
    
    // Vérifier autres tables
    $required_tables = ['courses', 'questions', 'progression', 'badges', 'user_badges', 'phishing_scenarios', 'resources'];
    foreach ($required_tables as $table) {
        if (!in_array($table, $tables)) {
            $result['issues'][] = "Table '$table' manquante";
        } else {
            $result[$table . '_count'] = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        }
    }
    
    echo json_encode($result, JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    echo json_encode([
        'connection' => 'ERREUR',
        'error' => $e->getMessage()
    ]);
}
?>
