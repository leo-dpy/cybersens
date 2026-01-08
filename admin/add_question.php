<?php
require_once 'auth.php';
checkAdmin();

// Récupérer tous les cours
$all_courses = $pdo->query("SELECT id, title FROM courses ORDER BY title")->fetchAll();

// Pré-sélectionner un cours si on vient de le créer
$selected_course = isset($_GET['course_id']) ? (int)$_GET['course_id'] : null;
$is_new_course = isset($_GET['new']) && $_GET['new'] == 1;

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = (int)$_POST['course_id'];
    $question = trim($_POST['question']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d'] ?? '');
    $correct_answer = strtoupper($_POST['correct_answer']);
    $explanation = trim($_POST['explanation'] ?? '');
    $points = (int)($_POST['points'] ?? 10);
    
    if (empty($course_id) || empty($question) || empty($option_a) || empty($option_b) || empty($option_c)) {
        $error = "Veuillez remplir tous les champs obligatoires (Question + options A, B, C).";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO questions (course_id, question, option_a, option_b, option_c, option_d, correct_answer, explanation, points) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$course_id, $question, $option_a, $option_b, $option_c, $option_d, $correct_answer, $explanation, $points]);
            
            if (isset($_POST['add_another'])) {
                $success = "✅ Question ajoutée ! Vous pouvez en ajouter une autre.";
                $selected_course = $course_id;
            } else {
                header("Location: questions.php?course_id=" . $course_id . "&msg=created");
                exit;
            }
            
        } catch (PDOException $e) {
            $error = "Erreur lors de la création : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Question - Admin CyberSens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        .option-group {
            display: flex;
            align-items: stretch;
            gap: 0;
            margin-bottom: 1rem;
        }
        .option-radio {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            background: rgba(5, 5, 5, 0.8);
            border: 1px solid var(--glass-border);
            border-right: none;
            border-radius: 8px 0 0 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .option-radio:has(input:checked) {
            background: var(--success);
            border-color: var(--success);
        }
        .option-radio input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .option-letter {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-left: none;
            border-right: none;
            font-weight: 700;
            color: var(--primary-color);
        }
        .option-input {
            flex: 1;
            border-radius: 0 8px 8px 0 !important;
        }
        .question-count {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
        }
        .question-count .number {
            font-size: 3rem;
            font-weight: 700;
            color: #050505;
        }
        .question-count p {
            color: #050505;
        }
        .tips-card {
            background: rgba(0, 243, 255, 0.1);
            border: 1px solid rgba(0, 243, 255, 0.3);
        }
        .sidebar {
            width: 250px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-shield-lock-fill"></i>
            <h4>CyberSens</h4>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cours.php"><i class="bi bi-book"></i> Cours</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="questions.php"><i class="bi bi-question-circle"></i> Questions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users.php"><i class="bi bi-people"></i> Utilisateurs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link back-link" href="../index.html"><i class="bi bi-arrow-left"></i> Retour au site</a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1">Nouvelle Question</h1>
                <p class="text-muted mb-0">Ajoutez une question de quiz pour un cours</p>
            </div>
            <a href="questions.php<?php echo $selected_course ? '?course_id='.$selected_course : ''; ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        <?php if($is_new_course): ?>
        <div class="alert alert-success d-flex align-items-center mb-4">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div><strong>Cours créé avec succès !</strong> Ajoutez maintenant des questions pour le quiz.</div>
        </div>
        <?php endif; ?>

        <?php if($error): ?>
        <div class="alert alert-danger d-flex align-items-center mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
        </div>
        <?php endif; ?>

        <?php if($success): ?>
        <div class="alert alert-success d-flex align-items-center mb-4">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo $success; ?>
        </div>
        <?php endif; ?>

        <?php if(count($all_courses) == 0): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Vous devez d'abord créer un cours. <a href="add_cours.php" class="btn btn-sm btn-warning ms-2">Créer un cours</a>
        </div>
        <?php else: ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card p-4">
                    <form method="POST">
                        <!-- Sélection du cours -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Cours associé <span class="text-danger">*</span></label>
                            <select name="course_id" class="form-select form-select-lg" required>
                                <option value="">-- Sélectionner un cours --</option>
                                <?php foreach($all_courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $selected_course == $c['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['title']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Question -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Question <span class="text-danger">*</span></label>
                            <textarea name="question" class="form-control" rows="3" required
                                placeholder="Ex: Quels sont les trois piliers de la sécurité informatique ?"><?php echo isset($_POST['question']) && !$success ? htmlspecialchars($_POST['question']) : ''; ?></textarea>
                        </div>

                        <!-- Options de réponse -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-3">
                                <i class="bi bi-list-check me-1"></i> Options de réponse
                                <small class="text-muted fw-normal">(cochez la bonne réponse)</small>
                            </label>

                            <!-- Option A -->
                            <div class="option-group">
                                <label class="option-radio">
                                    <input type="radio" name="correct_answer" value="A" <?php echo (!isset($_POST['correct_answer']) || $_POST['correct_answer'] == 'A') ? 'checked' : ''; ?> required>
                                </label>
                                <span class="option-letter">A</span>
                                <input type="text" name="option_a" class="form-control option-input" required
                                    placeholder="Première option (obligatoire)"
                                    value="<?php echo isset($_POST['option_a']) && !$success ? htmlspecialchars($_POST['option_a']) : ''; ?>">
                            </div>

                            <!-- Option B -->
                            <div class="option-group">
                                <label class="option-radio">
                                    <input type="radio" name="correct_answer" value="B" <?php echo (isset($_POST['correct_answer']) && $_POST['correct_answer'] == 'B') ? 'checked' : ''; ?>>
                                </label>
                                <span class="option-letter">B</span>
                                <input type="text" name="option_b" class="form-control option-input" required
                                    placeholder="Deuxième option (obligatoire)"
                                    value="<?php echo isset($_POST['option_b']) && !$success ? htmlspecialchars($_POST['option_b']) : ''; ?>">
                            </div>

                            <!-- Option C -->
                            <div class="option-group">
                                <label class="option-radio">
                                    <input type="radio" name="correct_answer" value="C" <?php echo (isset($_POST['correct_answer']) && $_POST['correct_answer'] == 'C') ? 'checked' : ''; ?>>
                                </label>
                                <span class="option-letter">C</span>
                                <input type="text" name="option_c" class="form-control option-input" required
                                    placeholder="Troisième option (obligatoire)"
                                    value="<?php echo isset($_POST['option_c']) && !$success ? htmlspecialchars($_POST['option_c']) : ''; ?>">
                            </div>

                            <!-- Option D (optionnel) -->
                            <div class="option-group">
                                <label class="option-radio">
                                    <input type="radio" name="correct_answer" value="D" <?php echo (isset($_POST['correct_answer']) && $_POST['correct_answer'] == 'D') ? 'checked' : ''; ?>>
                                </label>
                                <span class="option-letter">D</span>
                                <input type="text" name="option_d" class="form-control option-input"
                                    placeholder="Quatrième option (optionnel)"
                                    value="<?php echo isset($_POST['option_d']) && !$success ? htmlspecialchars($_POST['option_d']) : ''; ?>">
                            </div>
                        </div>

                        <!-- Points et Explication -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Points</label>
                                <input type="number" name="points" class="form-control" value="<?php echo isset($_POST['points']) ? $_POST['points'] : '10'; ?>" min="1" max="100">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Explication (optionnel)</label>
                                <input type="text" name="explanation" class="form-control"
                                    placeholder="Explication affichée après la réponse..."
                                    value="<?php echo isset($_POST['explanation']) && !$success ? htmlspecialchars($_POST['explanation']) : ''; ?>">
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="submit" name="add_another" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-plus-lg me-2"></i>Enregistrer + Ajouter une autre
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Enregistrer et terminer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <?php if($selected_course): 
                    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE course_id = ?");
                    $count_stmt->execute([$selected_course]);
                    $question_count = $count_stmt->fetchColumn();
                ?>
                <div class="question-count mb-4">
                    <div class="number"><?php echo $question_count; ?></div>
                    <div class="text-white-50">Questions pour ce cours</div>
                    <div class="progress mt-3" style="height: 8px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar bg-white" style="width: <?php echo min($question_count * 33.33, 100); ?>%"></div>
                    </div>
                    <small class="text-white-50 mt-2 d-block">Minimum recommandé : 3</small>
                </div>
                <?php endif; ?>

                <div class="card tips-card p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2 text-warning"></i>Conseils</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Questions claires et précises</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Options plausibles (pièges réalistes)</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Une seule bonne réponse</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Explication pédagogique</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Au moins 3 questions par cours</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
