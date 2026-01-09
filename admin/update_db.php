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
    <link rel="stylesheet" href="../styles.css">
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
        }
        .container {
            width: 100%;
            max-width: 600px;
            padding: 2rem;
        }
        .card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-lg);
        }
        h2 {
            margin-top: 0;
            color: var(--accent-primary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .alert {
            padding: 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--accent-primary);
            color: white;
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 500;
            margin-top: 1rem;
            transition: opacity 0.2s;
        }
        .btn:hover {
            opacity: 0.9;
        }
        hr {
            border: 0;
            border-top: 1px solid var(--border-color);
            margin: 1.5rem 0;
        }
    </style>
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
    <script>lucide.createIcons();</script>
</body>
</html>
