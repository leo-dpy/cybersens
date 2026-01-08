<?php
require_once 'auth.php';
checkAdmin();

// Vérifier l'ID de la question
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: questions.php");
    exit;
}

$id = (int)$_GET['id'];

// Récupérer la question
$stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->execute([$id]);
$question = $stmt->fetch();

if (!$question) {
    header("Location: questions.php");
    exit;
}

// Récupérer tous les cours
$all_courses = $pdo->query("SELECT id, title FROM courses ORDER BY title")->fetchAll();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = (int)$_POST['course_id'];
    $question_text = trim($_POST['question']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d'] ?? '');
    $correct_answer = strtoupper($_POST['correct_answer']);
    $explanation = trim($_POST['explanation'] ?? '');
    
    if (empty($course_id) || empty($question_text) || empty($option_a) || empty($option_b) || empty($option_c)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE questions SET course_id = ?, question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ?, explanation = ? WHERE id = ?");
            $stmt->execute([$course_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $explanation, $id]);
            
            header("Location: questions.php?course_id=" . $course_id . "&msg=updated");
            exit;
            
        } catch (PDOException $e) {
            $error = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Question - Admin CyberSens</title>
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
                <h1 class="h2 fw-bold">Modifier la Question #<?php echo $id; ?></h1>
                <a href="questions.php?course_id=<?php echo $question['course_id']; ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Retour
                </a>
            </div>

            <?php if($error): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div><?php echo $error; ?></div>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4"><i class="bi bi-question-circle me-2"></i>Informations de la question</h5>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Cours associé <span class="text-danger">*</span></label>
                                    <select name="course_id" class="form-select" required>
                                        <?php foreach($all_courses as $c): ?>
                                            <option value="<?php echo $c['id']; ?>" <?php echo $question['course_id'] == $c['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($c['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Question <span class="text-danger">*</span></label>
                                    <textarea name="question" class="form-control" rows="3" required><?php echo htmlspecialchars($question['question']); ?></textarea>
                                </div>
                                
                                <hr class="my-4" style="border-color: var(--glass-border);">
                                <h6 class="mb-3 fw-bold text-uppercase small" style="color: var(--primary-color);"><i class="bi bi-list-check me-2"></i>Options de réponse</h6>
                                
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <input type="radio" name="correct_answer" value="A" class="form-check-input mt-0" required <?php echo $question['correct_answer'] == 'A' ? 'checked' : ''; ?>>
                                        </span>
                                        <span class="input-group-text fw-bold">A</span>
                                        <input type="text" name="option_a" class="form-control" required
                                               value="<?php echo htmlspecialchars($question['option_a']); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <input type="radio" name="correct_answer" value="B" class="form-check-input mt-0" <?php echo $question['correct_answer'] == 'B' ? 'checked' : ''; ?>>
                                        </span>
                                        <span class="input-group-text fw-bold">B</span>
                                        <input type="text" name="option_b" class="form-control" required
                                               value="<?php echo htmlspecialchars($question['option_b']); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <input type="radio" name="correct_answer" value="C" class="form-check-input mt-0" <?php echo $question['correct_answer'] == 'C' ? 'checked' : ''; ?>>
                                        </span>
                                        <span class="input-group-text fw-bold">C</span>
                                        <input type="text" name="option_c" class="form-control" required
                                               value="<?php echo htmlspecialchars($question['option_c']); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <input type="radio" name="correct_answer" value="D" class="form-check-input mt-0" <?php echo $question['correct_answer'] == 'D' ? 'checked' : ''; ?>>
                                        </span>
                                        <span class="input-group-text fw-bold">D</span>
                                        <input type="text" name="option_d" class="form-control"
                                               value="<?php echo htmlspecialchars($question['option_d'] ?? ''); ?>" placeholder="Option D (optionnel)">
                                    </div>
                                </div>
                                
                                <hr class="my-4" style="border-color: var(--glass-border);">
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">Explication (optionnel)</label>
                                    <textarea name="explanation" class="form-control" rows="2"><?php echo htmlspecialchars($question['explanation'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="d-grid d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i> Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card" style="background: rgba(0, 243, 255, 0.1); border-color: rgba(0, 243, 255, 0.3);">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3" style="color: var(--primary-color);"><i class="bi bi-lightbulb me-2"></i>Rappel</h5>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                <li><i class="bi bi-check2 me-2" style="color: var(--primary-color);"></i>Cochez le bouton radio à côté de la bonne réponse</li>
                                <li><i class="bi bi-check2 me-2" style="color: var(--primary-color);"></i>Une seule réponse correcte par question</li>
                                <li><i class="bi bi-check2 me-2" style="color: var(--primary-color);"></i>L'option D est optionnelle</li>
                                <li><i class="bi bi-check2 me-2" style="color: var(--primary-color);"></i>L'explication s'affiche après le quiz</li>
                            </ul>
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
