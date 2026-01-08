<?php
require_once 'auth.php';
checkAdmin();

// Suppression d'un cours
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: cours.php?msg=deleted");
    exit;
}

// Récupérer tous les cours
$cours = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM questions WHERE course_id = c.id) as nb_questions FROM courses c ORDER BY c.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Cours - Admin CyberSens</title>
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
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cours.php">
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
                <h1 class="h2 fw-bold">Gestion des Cours</h1>
                <a href="add_cours.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i> Nouveau cours
                </a>
            </div>

            <?php if(isset($_GET['msg'])): ?>
                <?php if($_GET['msg'] == 'created'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i> Cours créé avec succès !
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif($_GET['msg'] == 'updated'): ?>
                    <div class="alert alert-info alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i> Cours mis à jour !
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif($_GET['msg'] == 'deleted'): ?>
                    <div class="alert alert-warning alert-dismissible fade show">
                        <i class="bi bi-trash me-2"></i> Cours supprimé !
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="card-cyber">
                <div class="card-body-cyber p-0">
                    <?php if(count($cours) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Titre</th>
                                    <th class="px-4 py-3">Difficulté</th>
                                    <th class="px-4 py-3">Questions</th>
                                    <th class="px-4 py-3">Date création</th>
                                    <th class="px-4 py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($cours as $c): ?>
                                <tr>
                                    <td class="px-4 py-3">#<?php echo $c['id']; ?></td>
                                    <td class="px-4 py-3">
                                        <div class="fw-medium"><?php echo htmlspecialchars($c['title']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars(substr($c['description'], 0, 50)); ?>...</small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php 
                                        $diffClass = $c['difficulty'] == 'Facile' ? 'success' : ($c['difficulty'] == 'Intermédiaire' ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge bg-<?php echo $diffClass; ?>">
                                            <?php echo $c['difficulty']; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-secondary">
                                            <?php echo $c['nb_questions']; ?> questions
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-muted"><?php echo date('d/m/Y', strtotime($c['created_at'])); ?></td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="btn-group">
                                            <a href="edit_cours.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="questions.php?course_id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-primary" title="Gérer les questions">
                                                <i class="bi bi-question-circle"></i>
                                            </a>
                                            <a href="cours.php?delete=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours et toutes ses questions ?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-book display-1 text-muted opacity-50"></i>
                        </div>
                        <h4 class="fw-bold">Aucun cours</h4>
                        <p class="text-muted mb-4">Commencez par créer votre premier cours.</p>
                        <a href="add_cours.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i> Créer un cours
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
