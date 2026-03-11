<?php
require_once 'auth.php';
checkResourcesAccess();

$currentUser = getCurrentUser();

// Suppression d'une ressource
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM resources WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: resources.php?msg=deleted");
    exit;
}

// Récupérer toutes les ressources
try {
    $resources = $pdo->query("SELECT * FROM resources ORDER BY category, title")->fetchAll();
} catch (PDOException $e) {
    die("Erreur base de données : " . $e->getMessage());
}

// Labels des catégories
$categoryLabels = [
    'article' => 'Article',
    'video' => 'Vidéo',
    'tool' => 'Outil',
    'documentation' => 'Documentation',
    'external' => 'Lien externe'
];

$categoryColors = [
    'article' => '#3b82f6',
    'video' => '#ef4444',
    'tool' => '#10b981',
    'documentation' => '#f59e0b',
    'external' => '#8b5cf6'
];

$difficultyLabels = [
    'debutant' => 'Débutant',
    'intermediaire' => 'Intermédiaire',
    'avance' => 'Avancé'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Ressources - Admin CyberSens</title>
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
            
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                     <?php echo strtoupper(substr($currentUser['username'], 0, 1)); ?>
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name"><?php echo htmlspecialchars($currentUser['username']); ?></div>
                    <div class="sidebar-user-role"><?php echo getRoleName($currentUser['role']); ?></div>
                </div>
            </div>
        </nav>

        <!-- Contenu Principal -->
        <main class="main-content">
            <div class="content-wrapper">
                <div class="page-header-content" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1>Ressources</h1>
                        <p class="text-muted">Gérez les articles, outils et liens externes de la bibliothèque.</p>
                    </div>
                    <div class="top-actions">
                        <a href="add_resource.php" class="btn btn-primary">
                            <i data-lucide="plus"></i> Nouvelle Ressource
                        </a>
                    </div>
                </div>

                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                <div class="alert alert-success">
                    <i data-lucide="check-circle"></i> Ressource supprimée avec succès.
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'created'): ?>
                <div class="alert alert-success">
                    <i data-lucide="check-circle"></i> Ressource créée avec succès.
                </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
                <div class="alert alert-success">
                    <i data-lucide="check-circle"></i> Ressource mise à jour avec succès.
                </div>
                <?php endif; ?>

                <div class="card-grid">
                    <?php if (count($resources) > 0): ?>
                        <div class="admin-table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Catégorie</th>
                                        <th>Niveau</th>
                                        <th>Description</th>
                                        <th style="text-align: right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resources as $item): ?>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <i data-lucide="<?php echo htmlspecialchars($item['icon'] ?? 'file-text'); ?>" style="width: 16px; height: 16px; color: <?php echo $categoryColors[$item['category']] ?? '#888'; ?>;"></i>
                                                <?php echo htmlspecialchars($item['title']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: <?php echo $categoryColors[$item['category']] ?? '#888'; ?>20; color: <?php echo $categoryColors[$item['category']] ?? '#888'; ?>; border: 1px solid <?php echo $categoryColors[$item['category']] ?? '#888'; ?>40;">
                                                <?php echo $categoryLabels[$item['category']] ?? $item['category']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $diffCss = 'facile';
                                            if($item['difficulty'] == 'intermediaire') $diffCss = 'moyen';
                                            if($item['difficulty'] == 'avance') $diffCss = 'difficile';
                                            ?>
                                            <span class="difficulty-badge <?php echo $diffCss; ?>">
                                                <?php echo $difficultyLabels[$item['difficulty']] ?? $item['difficulty']; ?>
                                            </span>
                                        </td>
                                        <td style="font-size: 0.9em; opacity: 0.8; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars($item['description']); ?>
                                        </td>
                                        <td class="actions-cell">
                                            <a href="edit_resource.php?id=<?php echo $item['id']; ?>" class="action-btn edit" title="Modifier">
                                                <i data-lucide="edit-2"></i>
                                            </a>
                                            <a href="resources.php?delete=<?php echo $item['id']; ?>" class="action-btn delete" title="Supprimer" onclick="return confirmAction(event, 'Voulez-vous vraiment supprimer cette ressource ?');">
                                                <i data-lucide="trash-2"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i data-lucide="library" class="empty-icon"></i>
                            <h3>Aucune ressource</h3>
                            <p>Commencez par ajouter un article, un outil ou un lien externe.</p>
                            <a href="add_resource.php" class="btn btn-primary mt-3">Ajouter</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="../../frontend/js/admin/shared.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
