<?php
require_once 'auth.php';
checkAdmin();

// Filtre par cours
$course_filter = isset($_GET['course_id']) ? (int)$_GET['course_id'] : null;

// Suppression d'une question
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    
    $redirect = $course_filter ? "questions.php?course_id=$course_filter&msg=deleted" : "questions.php?msg=deleted";
    header("Location: $redirect");
    exit;
}

// Récupérer tous les cours pour le filtre
$all_courses = $pdo->query("SELECT id, title FROM courses ORDER BY title")->fetchAll();

// Récupérer les questions
if ($course_filter) {
    $stmt = $pdo->prepare("SELECT q.*, c.title as course_title FROM questions q JOIN courses c ON q.course_id = c.id WHERE q.course_id = ? ORDER BY q.id");
    $stmt->execute([$course_filter]);
    $questions = $stmt->fetchAll();
    
    // Récupérer le nom du cours filtré
    $course_stmt = $pdo->prepare("SELECT title FROM courses WHERE id = ?");
    $course_stmt->execute([$course_filter]);
    $course_name = $course_stmt->fetchColumn();
} else {
    $questions = $pdo->query("SELECT q.*, c.title as course_title FROM questions q JOIN courses c ON q.course_id = c.id ORDER BY c.id, q.id")->fetchAll();
    $course_name = null;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Questions - Admin CyberSens</title>
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
                        <a class="nav-link" href="cours.php">
                            <i class="bi bi-book"></i> Cours
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="questions.php">
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
                <h1 class="h2 fw-bold">
                    <?php if($course_name): ?>
                        Questions : <?php echo htmlspecialchars($course_name); ?>
                    <?php else: ?>
                        Toutes les Questions
                    <?php endif; ?>
                </h1>
                <a href="add_question.php<?php echo $course_filter ? '?course_id='.$course_filter : ''; ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i> Nouvelle question
                </a>
            </div>

            <?php if(isset($_GET['msg'])): ?>
                <?php if($_GET['msg'] == 'created'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i> Question créée avec succès !
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif($_GET['msg'] == 'deleted'): ?>
                    <div class="alert alert-warning alert-dismissible fade show">
                        <i class="bi bi-trash me-2"></i> Question supprimée !
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif($_GET['msg'] == 'updated'): ?>
                    <div class="alert alert-info alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i> Question mise à jour !
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Filtre par cours -->
            <div class="card-cyber mb-4">
                <div class="card-body-cyber p-4">
                    <form method="GET" class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label class="form-label mb-0 fw-medium">Filtrer par cours :</label>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <select name="course_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Tous les cours --</option>
                                <?php foreach($all_courses as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo $course_filter == $c['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if($course_filter): ?>
                        <div class="col-auto">
                            <a href="questions.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-2"></i> Réinitialiser
                            </a>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="card-cyber">
                <div class="card-body-cyber p-0">
                    <?php if(count($questions) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Cours</th>
                                    <th class="px-4 py-3">Question</th>
                                    <th class="px-4 py-3">Options</th>
                                    <th class="px-4 py-3">Réponse</th>
                                    <th class="px-4 py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($questions as $q): ?>
                                <tr>
                                    <td class="px-4 py-3">#<?php echo $q['id']; ?></td>
                                    <td class="px-4 py-3">
                                        <a href="questions.php?course_id=<?php echo $q['course_id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($q['course_title']); ?>
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="fw-medium"><?php echo htmlspecialchars(substr($q['question'], 0, 60)); ?></div>
                                        <?php if(strlen($q['question']) > 60): ?><small class="text-muted">...</small><?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <small class="d-flex flex-column gap-1">
                                            <span class="<?php echo $q['correct_answer'] == 'A' ? 'correct-option' : ''; ?>">A: <?php echo htmlspecialchars(substr($q['option_a'], 0, 25)); ?><?php echo strlen($q['option_a']) > 25 ? '...' : ''; ?></span>
                                            <span class="<?php echo $q['correct_answer'] == 'B' ? 'correct-option' : ''; ?>">B: <?php echo htmlspecialchars(substr($q['option_b'], 0, 25)); ?><?php echo strlen($q['option_b']) > 25 ? '...' : ''; ?></span>
                                            <span class="<?php echo $q['correct_answer'] == 'C' ? 'correct-option' : ''; ?>">C: <?php echo htmlspecialchars(substr($q['option_c'], 0, 25)); ?><?php echo strlen($q['option_c']) > 25 ? '...' : ''; ?></span>
                                            <?php if(!empty($q['option_d'])): ?>
                                            <span class="<?php echo $q['correct_answer'] == 'D' ? 'correct-option' : ''; ?>">D: <?php echo htmlspecialchars(substr($q['option_d'], 0, 25)); ?><?php echo strlen($q['option_d']) > 25 ? '...' : ''; ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-success"><?php echo $q['correct_answer']; ?></span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="btn-group">
                                            <a href="edit_question.php?id=<?php echo $q['id']; ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="questions.php?delete=<?php echo $q['id']; ?><?php echo $course_filter ? '&course_id='.$course_filter : ''; ?>" 
                                               class="btn btn-sm btn-outline-danger" title="Supprimer" 
                                               onclick="return confirm('Supprimer cette question ?');">
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
                            <i class="bi bi-question-circle display-1 text-muted opacity-50"></i>
                        </div>
                        <h4 class="fw-bold">Aucune question</h4>
                        <p class="text-muted mb-4">
                            <?php if($course_filter): ?>
                                Ce cours n'a pas encore de questions.
                            <?php else: ?>
                                Commencez par créer des questions pour vos cours.
                            <?php endif; ?>
                        </p>
                        <a href="add_question.php<?php echo $course_filter ? '?course_id='.$course_filter : ''; ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i> Ajouter une question
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
