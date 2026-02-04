<?php
/**
 * Script de mise à jour de la base de données
 * Exécutez ce script UNE FOIS pour mettre à jour la colonne de progression
 */

require '../backend/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour BDD - CyberSens</title>
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../css/admin/update_db.css">
</head>
<body>
    <div class="bg-grid"></div>
    <div class="container">
        <div class="card">
            <h2><i data-lucide="database"></i>Mise à jour BDD</h2>
            
            <?php
            try {
                // Vérifier si la colonne is_completed existe et la renommer en completed
                $result = $pdo->query("SHOW COLUMNS FROM progression LIKE 'is_completed'");
                if ($result->rowCount() > 0) {
                    $pdo->exec("ALTER TABLE progression CHANGE is_completed completed TINYINT(1) DEFAULT 0");
                    echo "<div class='alert alert-success'><i data-lucide='check-circle'></i>Colonne 'is_completed' renommée en 'completed'</div>";
                } else {
                    // Vérifier si completed existe
                    $result2 = $pdo->query("SHOW COLUMNS FROM progression LIKE 'completed'");
                    if ($result2->rowCount() > 0) {
                        echo "<div class='alert alert-info'><i data-lucide='info'></i>La colonne 'completed' existe déjà</div>";
                    } else {
                        $pdo->exec("ALTER TABLE progression ADD COLUMN completed TINYINT(1) DEFAULT 0");
                        echo "<div class='alert alert-success'><i data-lucide='check-circle'></i>Colonne 'completed' ajoutée</div>";
                    }
                }
                
                // Vérifier si la colonne is_admin existe dans users
                $result3 = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
                if ($result3->rowCount() == 0) {
                    $pdo->exec("ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0");
                    echo "<div class='alert alert-success'><i data-lucide='check-circle'></i>Colonne 'is_admin' ajoutée à la table users</div>";
                } else {
                    echo "<div class='alert alert-info'><i data-lucide='info'></i>La colonne 'is_admin' existe déjà</div>";
                }
                
                echo "<hr>";
                echo "<p style='color: var(--success); font-weight: bold; margin-bottom: 1rem;'>Mise à jour terminée !</p>";
                echo "<a href='../index.html' class='btn'><i data-lucide='arrow-left'></i>Retour au site</a>";
                
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'><i data-lucide='alert-triangle'></i>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            ?>
        </div>
    </div>
    <script src="../js/admin/update_db.js"></script>
</body>
</html>
