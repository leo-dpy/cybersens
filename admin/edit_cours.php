<?php
require_once 'auth.php';
checkAdmin();

// Vérifier l'ID du cours
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: cours.php");
    exit;
}

$id = (int)$_GET['id'];

// Récupérer le cours
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$id]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: cours.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $content = $_POST['content'];
    $difficulty = $_POST['difficulty'];
    
    if (empty($title) || empty($description) || empty($content)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE courses SET title = ?, description = ?, content = ?, difficulty = ? WHERE id = ?");
            $stmt->execute([$title, $description, $content, $difficulty, $id]);
            
            header("Location: cours.php?msg=updated");
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
    <title>Modifier le Cours - Admin CyberSens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="admin-style.css">
    <!-- Quill - Éditeur visuel -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        #editor {
            height: 350px;
            background: rgba(5, 5, 5, 0.8);
            color: var(--text-color);
        }
        .ql-toolbar {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px 8px 0 0;
            border-color: var(--glass-border);
        }
        .ql-container {
            border-radius: 0 0 8px 8px;
            font-size: 16px;
            border-color: var(--glass-border);
        }
        .ql-toolbar .ql-stroke { stroke: var(--text-color); }
        .ql-toolbar .ql-fill { fill: var(--text-color); }
        .ql-toolbar .ql-picker { color: var(--text-color); }
        .ql-editor.ql-blank::before { color: rgba(224, 224, 224, 0.4); }
    </style>
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
                <h1 class="h2 fw-bold">Modifier le Cours</h1>
                <div>
                    <a href="questions.php?course_id=<?php echo $id; ?>" class="btn btn-info text-white me-2">
                        <i class="bi bi-question-circle me-2"></i> Questions
                    </a>
                    <a href="cours.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i> Retour
                    </a>
                </div>
            </div>

            <?php if($error): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div><?php echo $error; ?></div>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-cyber">
                        <div class="card-body-cyber p-4">
                            <h5 class="fw-bold mb-4"><i class="bi bi-pencil me-2"></i>Informations du cours #<?php echo $id; ?></h5>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Titre du cours <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required 
                                           value="<?php echo htmlspecialchars($course['title']); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Description courte <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="2" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Difficulté</label>
                                    <select name="difficulty" class="form-select">
                                        <option value="Facile" <?php echo $course['difficulty'] == 'Facile' ? 'selected' : ''; ?>>🟢 Facile</option>
                                        <option value="Intermédiaire" <?php echo $course['difficulty'] == 'Intermédiaire' ? 'selected' : ''; ?>>🟡 Intermédiaire</option>
                                        <option value="Difficile" <?php echo $course['difficulty'] == 'Difficile' ? 'selected' : ''; ?>>🔴 Difficile</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">Contenu du cours <span class="text-danger">*</span></label>
                                    <!-- Éditeur Quill -->
                                    <div id="editor"><?php echo $course['content']; ?></div>
                                    <input type="hidden" name="content" id="content">
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle me-2"></i> Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card-cyber mb-4">
                        <div class="card-body-cyber p-4">
                            <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-info-circle me-2"></i>Informations</h5>
                            <p class="mb-2"><strong>Créé le :</strong> <?php echo date('d/m/Y H:i', strtotime($course['created_at'])); ?></p>
                            <?php
                            $nb_questions = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE course_id = ?");
                            $nb_questions->execute([$id]);
                            ?>
                            <p class="mb-0"><strong>Questions :</strong> <?php echo $nb_questions->fetchColumn(); ?></p>
                        </div>
                    </div>
                    
                    <div class="card-cyber bg-primary-subtle border-primary-subtle">
                        <div class="card-body-cyber p-4">
                            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-lightbulb me-2"></i>Conseils de rédaction</h5>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                <li><i class="bi bi-check2 text-primary me-2"></i>Utilisez les titres pour structurer le cours</li>
                                <li><i class="bi bi-check2 text-primary me-2"></i>Mettez en <strong>gras</strong> les concepts clés</li>
                                <li><i class="bi bi-check2 text-primary me-2"></i>Utilisez des listes à puces pour la lisibilité</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
// Initialisation de Quill
var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [2, 3, 4, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'code-block'],
            ['clean']
        ]
    }
});

// Avant soumission du formulaire, copier le contenu dans le champ caché
document.querySelector('form').onsubmit = function() {
    document.getElementById('content').value = quill.root.innerHTML;
};
</script>
</body>
</html>
