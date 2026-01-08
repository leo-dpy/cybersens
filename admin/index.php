<?php
require_once 'auth.php';
checkAdmin();

// Statistiques
$stats = [];
$stats['users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$stats['courses'] = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$stats['questions'] = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();
$stats['progressions'] = $pdo->query("SELECT COUNT(*) FROM progression WHERE is_completed = 1")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - CyberSens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky pt-3">
                <div class="sidebar-brand">
                    <i class="bi bi-shield-lock-fill"></i>
                    <h4>CyberSens</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cours.php">
                            <i class="bi bi-book"></i> Cours
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="questions.php">
                            <i class="bi bi-question-circle"></i> Questions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="bi bi-people"></i> Utilisateurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link back-link" href="../index.html">
                            <i class="bi bi-arrow-left"></i> Retour au site
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 fw-bold">Tableau de bord</h1>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card-admin">
                        <div class="icon-box bg-primary-subtle text-primary">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo $stats['users']; ?></h3>
                        <p class="text-muted mb-0">Utilisateurs inscrits</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card-admin">
                        <div class="icon-box bg-success-subtle text-success">
                            <i class="bi bi-book"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo $stats['courses']; ?></h3>
                        <p class="text-muted mb-0">Cours créés</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card-admin">
                        <div class="icon-box bg-warning-subtle text-warning">
                            <i class="bi bi-question-circle"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo $stats['questions']; ?></h3>
                        <p class="text-muted mb-0">Questions de quiz</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card-admin">
                        <div class="icon-box bg-info-subtle text-info">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo $stats['progressions']; ?></h3>
                        <p class="text-muted mb-0">Modules terminés</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity or Quick Actions could go here -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card-cyber h-100">
                        <div class="card-body-cyber p-4">
                            <h5 class="fw-bold mb-4">Actions rapides</h5>
                            <div class="d-grid gap-3">
                                <a href="add_cours.php" class="btn btn-outline-primary text-start p-3 rounded-3">
                                    <i class="bi bi-plus-circle me-2"></i> Ajouter un nouveau cours
                                </a>
                                <a href="add_question.php" class="btn btn-outline-primary text-start p-3 rounded-3">
                                    <i class="bi bi-plus-circle me-2"></i> Ajouter une question
                                </a>
                                <a href="users.php" class="btn btn-outline-secondary text-start p-3 rounded-3">
                                    <i class="bi bi-person-gear me-2"></i> Gérer les utilisateurs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-cyber h-100">
                        <div class="card-body-cyber p-4">
                            <h5 class="fw-bold mb-4">Derniers cours</h5>
                            <?php
                            $recent = $pdo->query("SELECT id, title, difficulty, created_at FROM courses ORDER BY created_at DESC LIMIT 5")->fetchAll();
                            if (count($recent) > 0):
                            ?>
                            <div class="list-group list-group-flush">
                                <?php foreach($recent as $r): ?>
                                <div class="list-group-item border-0 px-0 py-3 d-flex justify-content-between align-items-center" style="background: transparent;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle p-2" style="background: rgba(0,243,255,0.1);">
                                            <i class="bi bi-book" style="color: var(--primary-color);"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium"><?php echo htmlspecialchars($r['title']); ?></div>
                                            <small style="color: var(--text-muted);"><?php echo date('d/m/Y', strtotime($r['created_at'])); ?></small>
                                        </div>
                                    </div>
                                    <span class="badge" style="background: rgba(0,243,255,0.2); color: var(--primary-color); border: 1px solid var(--primary-color);">
                                        <?php echo $r['difficulty']; ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p class="text-muted mb-0">Aucun cours pour le moment.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
