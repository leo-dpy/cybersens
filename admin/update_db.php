<?php
/**
 * Script de mise à jour de la base de données
 * Exécutez ce script UNE FOIS pour mettre à jour la colonne de progression
 */

require '../db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour BDD - CyberSens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; padding: 2rem; }
        .card { border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-radius: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4 text-primary"><i class="bi bi-database-gear me-2"></i>Mise à jour de la base de données</h2>
                        
                        <?php
                        try {
                            // Vérifier si la colonne is_completed existe et la renommer en completed
                            $result = $pdo->query("SHOW COLUMNS FROM progression LIKE 'is_completed'");
                            if ($result->rowCount() > 0) {
                                $pdo->exec("ALTER TABLE progression CHANGE is_completed completed TINYINT(1) DEFAULT 0");
                                echo "<div class='alert alert-success'><i class='bi bi-check-circle me-2'></i>Colonne 'is_completed' renommée en 'completed'</div>";
                            } else {
                                // Vérifier si completed existe
                                $result2 = $pdo->query("SHOW COLUMNS FROM progression LIKE 'completed'");
                                if ($result2->rowCount() > 0) {
                                    echo "<div class='alert alert-info'><i class='bi bi-info-circle me-2'></i>La colonne 'completed' existe déjà</div>";
                                } else {
                                    $pdo->exec("ALTER TABLE progression ADD COLUMN completed TINYINT(1) DEFAULT 0");
                                    echo "<div class='alert alert-success'><i class='bi bi-check-circle me-2'></i>Colonne 'completed' ajoutée</div>";
                                }
                            }
                            
                            // Vérifier si la colonne is_admin existe dans users
                            $result3 = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
                            if ($result3->rowCount() == 0) {
                                $pdo->exec("ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0");
                                echo "<div class='alert alert-success'><i class='bi bi-check-circle me-2'></i>Colonne 'is_admin' ajoutée à la table users</div>";
                            } else {
                                echo "<div class='alert alert-info'><i class='bi bi-info-circle me-2'></i>La colonne 'is_admin' existe déjà</div>";
                            }
                            
                            echo "<hr class='my-4'>";
                            echo "<p class='fw-bold text-success mb-3'>Mise à jour terminée !</p>";
                            echo "<a href='../index.php' class='btn btn-primary'><i class='bi bi-arrow-left me-2'></i>Retour au site</a>";
                            
                        } catch (PDOException $e) {
                            echo "<div class='alert alert-danger'><i class='bi bi-exclamation-triangle me-2'></i>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
