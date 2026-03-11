<?php
require_once 'auth.php';
checkResourcesAccess();

$currentUser = getCurrentUser();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: resources.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM resources WHERE id = ?");
$stmt->execute([$id]);
$resource = $stmt->fetch();

if (!$resource) {
    header("Location: resources.php?error=notfound");
    exit;
}

// Icônes disponibles
$icons = [
    'file-text' => '📄 Article',
    'shield' => '🛡️ Sécurité',
    'key' => '🔑 Mot de passe',
    'lock' => '🔒 Chiffrement',
    'search' => '🔍 Recherche',
    'shield-check' => '✅ Vérification',
    'book-open' => '📖 Guide',
    'external-link' => '🔗 Lien',
    'smartphone' => '📱 Mobile',
    'globe' => '🌐 Web',
    'video' => '🎬 Vidéo',
    'terminal' => '⌨️ Terminal',
    'database' => '🗄️ Données',
    'cloud' => '☁️ Cloud',
    'bug' => '🐛 Malware',
    'wifi' => '📶 Réseau'
];

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $url = trim($_POST['url']);
    $content = trim($_POST['content'] ?? '');
    $icon = $_POST['icon'] ?? 'file-text';
    $difficulty = $_POST['difficulty'] ?? 'debutant';

    if (empty($title) || empty($category)) {
        $error = "Le titre et la catégorie sont obligatoires.";
    } else {
        if (empty($url)) $url = null;
        if (empty($content)) $content = null;

        try {
            $stmt = $pdo->prepare("UPDATE resources SET title = ?, description = ?, category = ?, url = ?, content = ?, icon = ?, difficulty = ? WHERE id = ?");
            $stmt->execute([$title, $description, $category, $url, $content, $icon, $difficulty, $id]);
            
            header("Location: resources.php?msg=updated");
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de la modification : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Ressource - Admin CyberSens</title>
    <link rel="icon" type="image/svg+xml" href="../../frontend/favicon.svg">
    <link rel="stylesheet" href="../../frontend/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../frontend/css/admin/news.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="bg-grid"></div>

    <div class="app-container">
        <!-- Barre latérale -->
        <nav class="sidebar">
            <div class="logo">
                <span class="logo-text">CyberSens</span>
            </div>
            
            <div class="nav-menu">
                <a href="index.php" class="nav-item">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Dashboard</span>
                </a>
                
                <?php if(hasPermission('manage_courses')): ?>
                <a href="cours.php" class="nav-item">
                    <i data-lucide="book-open"></i>
                    <span>Gestion Cours</span>
                </a>
                <a href="questions.php" class="nav-item">
                    <i data-lucide="help-circle"></i>
                    <span>Banque Questions</span>
                </a>
                <?php endif; ?>

                <?php if(hasPermission('manage_content')): ?>
                <a href="news.php" class="nav-item">
                    <i data-lucide="rss"></i>
                    <span>Actualités</span>
                </a>
                <?php endif; ?>

                <?php if(hasPermission('manage_resources')): ?>
                <a href="resources.php" class="nav-item active">
                    <i data-lucide="library"></i>
                    <span>Ressources</span>
                </a>
                <?php endif; ?>
                
                <?php if(hasPermission('manage_users')): ?>
                <a href="users.php" class="nav-item">
                    <i data-lucide="users"></i>
                    <span>Utilisateurs</span>
                </a>
                <?php endif; ?>

                <div class="nav-divider"></div>

                <a href="../../index.html" class="nav-item">
                    <i data-lucide="arrow-left"></i>
                    <span>Retour au site</span>
                </a>
            </div>
        </nav>

        <!-- Contenu Principal -->
        <main class="main-content">
            <div class="content-wrapper">
                <div class="page-header-content">
                    <h1>Modifier la Ressource</h1>
                    <p class="text-muted"><?php echo htmlspecialchars($resource['title']); ?></p>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i data-lucide="alert-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST" action="" class="admin-form">
                        <div class="form-group">
                            <label for="title">Titre <span class="required">*</span></label>
                            <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($resource['title']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="category">Catégorie <span class="required">*</span></label>
                            <select id="category" name="category" class="form-input" required onchange="toggleContentField()">
                                <option value="article" <?php echo $resource['category'] === 'article' ? 'selected' : ''; ?>>📄 Article</option>
                                <option value="video" <?php echo $resource['category'] === 'video' ? 'selected' : ''; ?>>🎬 Vidéo</option>
                                <option value="tool" <?php echo $resource['category'] === 'tool' ? 'selected' : ''; ?>>🛠️ Outil</option>
                                <option value="documentation" <?php echo $resource['category'] === 'documentation' ? 'selected' : ''; ?>>📖 Documentation</option>
                                <option value="external" <?php echo $resource['category'] === 'external' ? 'selected' : ''; ?>>🔗 Lien externe</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="difficulty">Niveau</label>
                            <select id="difficulty" name="difficulty" class="form-input">
                                <option value="debutant" <?php echo $resource['difficulty'] === 'debutant' ? 'selected' : ''; ?>>Débutant</option>
                                <option value="intermediaire" <?php echo $resource['difficulty'] === 'intermediaire' ? 'selected' : ''; ?>>Intermédiaire</option>
                                <option value="avance" <?php echo $resource['difficulty'] === 'avance' ? 'selected' : ''; ?>>Avancé</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="icon">Icône</label>
                            <select id="icon" name="icon" class="form-input">
                                <?php foreach ($icons as $value => $label): ?>
                                <option value="<?php echo $value; ?>" <?php echo ($resource['icon'] ?? 'file-text') === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($resource['description']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="url">URL (pour vidéos, outils, liens externes)</label>
                            <input type="url" id="url" name="url" value="<?php echo htmlspecialchars($resource['url'] ?? ''); ?>" placeholder="https://...">
                        </div>

                        <div class="form-group" id="content-group">
                            <label for="content">Contenu (Markdown, pour les articles)</label>
                            <textarea id="content" name="content" rows="10" style="font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;"><?php echo htmlspecialchars($resource['content'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-actions">
                            <a href="resources.php" class="btn btn-outline">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <i data-lucide="save"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();

        function toggleContentField() {
            const category = document.getElementById('category').value;
            const contentGroup = document.getElementById('content-group');
            contentGroup.style.display = category === 'article' ? 'block' : 'none';
        }
        toggleContentField();
    </script>
</body>
</html>
