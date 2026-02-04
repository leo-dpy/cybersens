<?php
require_once 'auth.php';
// Seulement admin et superadmin peuvent gérer les news
checkAdmin();

$currentUser = getCurrentUser();

// Suppression d'une actualité
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: news.php?msg=deleted");
    exit;
}

// Récupérer toutes les actualités triées par date
try {
    $newsList = $pdo->query("SELECT * FROM news ORDER BY event_date DESC, created_at DESC")->fetchAll();
} catch (PDOException $e) {
    if ($e->getCode() == '42S02') { // Table not found
        // Création automatique de la table si elle manque
        $sql = "CREATE TABLE IF NOT EXISTS news (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            event_date DATE NOT NULL,
            source VARCHAR(100),
            link VARCHAR(255) DEFAULT '#',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $pdo->exec($sql);

        // Insertion des données par défaut
        $major_incidents = [
            ['title' => 'Kiabi', 'description' => 'Fuite des IBAN de 20 000 clients via une attaque par Credential Stuffing.', 'date' => '2026-01-07', 'source' => 'Fuite Bancaire', 'link' => 'https://www.kiabi.com'],
            ['title' => 'Mondial Relay', 'description' => 'Vol de données personnelles et détails de livraison touchant des millions de clients.', 'date' => '2025-12-23', 'source' => 'Vol de Données', 'link' => '#'],
            ['title' => 'La Poste & Banque Postale', 'description' => 'Attaque DDoS massive rendant les services inaccessibles juste avant Noël.', 'date' => '2025-12-22', 'source' => 'Paralysie', 'link' => '#'],
            ['title' => 'Pass\'Sport / Ministère des Sports', 'description' => 'Exfiltration de données de 3,5 millions de foyers (Identités, Sécu, IBAN).', 'date' => '2025-12-19', 'source' => 'Fuite Massive', 'link' => '#'],
            ['title' => 'Ministère de l\'Intérieur', 'description' => 'Intrusion serveurs messagerie, accès fichiers police sensibles (TAJ, FPR).', 'date' => '2025-12-11', 'source' => 'Intrusion Critique', 'link' => '#'],
            ['title' => 'MédecinDirect', 'description' => 'Violation de données de santé très sensibles (motifs consultation, échanges médicaux).', 'date' => '2025-12-05', 'source' => 'Données Santé', 'link' => '#'],
            ['title' => 'Missions Locales', 'description' => 'Fuite impactant 1,6 million de jeunes suivis par le réseau.', 'date' => '2025-12-01', 'source' => 'Données Sociales', 'link' => '#'],
            ['title' => 'Fédération Française de Football', 'description' => 'Troisième cyberattaque en deux ans, touchant les données des licenciés.', 'date' => '2025-11-26', 'source' => 'Piratage', 'link' => '#'],
            ['title' => 'Colis Privé', 'description' => 'Compromission des données de contact de millions de clients (risque phishing).', 'date' => '2025-11-21', 'source' => 'Fuite Clients', 'link' => '#'],
            ['title' => 'Pajemploi / URSSAF', 'description' => 'Vol de données touchant 1,2 million d\'usagers (employeurs/salariés).', 'date' => '2025-11-14', 'source' => 'Fuite Admin', 'link' => '#'],
            ['title' => 'Eurofiber France', 'description' => 'Attaque critique infrastructure, données de 3600 organisations exposées (SNCF, Airbus...).', 'date' => '2025-11-13', 'source' => 'Infrastructure', 'link' => '#'],
            ['title' => 'France Travail', 'description' => 'Nouvelle compromission ciblant 31 000 comptes via infostealers.', 'date' => '2025-10-27', 'source' => 'Piratage Compte', 'link' => '#'],
            ['title' => 'Lycées publics Hauts-de-France', 'description' => 'Ransomware Qilin paralysant 60 000 ordinateurs (80% des lycées) et vol données.', 'date' => '2025-10-10', 'source' => 'Rançongiciel', 'link' => '#'],
            ['title' => 'Hôpitaux publics Hauts-de-France', 'description' => 'Attaque visant les serveurs d\'identité des patients, retour au papier.', 'date' => '2025-09-08', 'source' => 'Hôpital', 'link' => '#'],
            ['title' => 'Auchan', 'description' => 'Cyberattaque ciblant les comptes de fidélité (cagnottes, historiques d\'achat).', 'date' => '2025-08-21', 'source' => 'Commerce', 'link' => '#'],
            ['title' => 'Bouygues Telecom', 'description' => 'Fuite massive 6,4 millions de clients (État civil, IBAN, Coordonnées).', 'date' => '2025-08-06', 'source' => 'Fuite Massive', 'link' => '#'],
            ['title' => 'Air France-KLM', 'description' => 'Fuite de données via prestataire Salesforce, membres Flying Blue touchés.', 'date' => '2025-08-06', 'source' => 'Supply Chain', 'link' => '#'],
            ['title' => 'Sorbonne Université', 'description' => 'Vol de données de 32 000 étudiants et employés.', 'date' => '2025-06-16', 'source' => 'Université', 'link' => '#'],
            ['title' => 'Disneyland Paris', 'description' => 'Revendication de vol de 64 Go de données confidentielles par le groupe Anubis.', 'date' => '2025-06-20', 'source' => 'Vol de Données', 'link' => '#'],
            ['title' => 'Reduction-Impots.fr', 'description' => 'Vente sur dark web de données fiscales de 2 millions de Français.', 'date' => '2025-05-14', 'source' => 'Dark Web', 'link' => '#']
        ];

        $stmt = $pdo->prepare("INSERT INTO news (title, description, event_date, source, link) VALUES (:title, :description, :date, :source, :link)");
        foreach ($major_incidents as $inc) {
            $stmt->execute([
                ':title' => $inc['title'],
                ':description' => $inc['description'],
                ':date' => $inc['date'],
                ':source' => $inc['source'],
                ':link' => $inc['link']
            ]);
        }
        
        // Re-tenter la requête d'affichage
        $newsList = $pdo->query("SELECT * FROM news ORDER BY event_date DESC, created_at DESC")->fetchAll();
    } else {
        die("Erreur base de données : " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Actualités - Admin CyberSens</title>
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link rel="stylesheet" href="../styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/admin/news.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="bg-grid"></div>

    <div class="app-container">
        <!-- Barre latérale -->
        <nav class="sidebar">
            <div class="logo">
                <div class="logo-icon">
                    <i data-lucide="shield-check"></i>
                </div>
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

                <a href="news.php" class="nav-item active">
                    <i data-lucide="rss"></i>
                    <span>Actualités</span>
                </a>
                
                <?php if(hasPermission('manage_users')): ?>
                <a href="users.php" class="nav-item">
                    <i data-lucide="users"></i>
                    <span>Utilisateurs</span>
                </a>
                <?php endif; ?>

                <div class="nav-divider"></div>

                <a href="../index.html" class="nav-item">
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
                        <h1>Actualités & Hacks</h1>
                        <p class="text-muted">Gérez les incidents et actualités affichés sur le site.</p>
                    </div>
                    <div class="top-actions">
                        <a href="add_news.php" class="btn btn-primary">
                            <i data-lucide="plus"></i> Nouvelle Actu
                        </a>
                    </div>
                </div>

                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                <div class="alert alert-success">
                    <i data-lucide="check-circle"></i> Actualité supprimée avec succès.
                </div>
                <?php endif; ?>

                 <?php if (isset($_GET['msg']) && $_GET['msg'] === 'created'): ?>
                <div class="alert alert-success">
                    <i data-lucide="check-circle"></i> Actualité créée avec succès.
                </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
                <div class="alert alert-success">
                    <i data-lucide="check-circle"></i> Actualité mise à jour avec succès.
                </div>
                <?php endif; ?>

                <div class="card-grid">
                    <?php if (count($newsList) > 0): ?>
                        <div class="admin-table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Source / Type</th>
                                        <th>Titre</th>
                                        <th>Description</th>
                                        <th style="text-align: right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($newsList as $item): ?>
                                    <tr>
                                        <td style="white-space: nowrap; color: var(--text-muted);">
                                            <?php echo htmlspecialchars($item['event_date']); ?>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: rgba(220, 38, 38, 0.1); color: var(--danger);">
                                                <?php echo htmlspecialchars($item['source']); ?>
                                            </span>
                                        </td>
                                        <td style="font-weight: 500;">
                                            <?php echo htmlspecialchars($item['title']); ?>
                                        </td>
                                        <td style="font-size: 0.9em; opacity: 0.8;">
                                            <?php echo htmlspecialchars($item['description']); ?>
                                        </td>
                                        <td class="actions-cell">
                                            <a href="edit_news.php?id=<?php echo $item['id']; ?>" class="action-btn edit" title="Modifier">
                                                <i data-lucide="edit-2"></i>
                                            </a>
                                            <a href="news.php?delete=<?php echo $item['id']; ?>" class="action-btn delete" title="Supprimer" onclick="return confirmAction(event, 'Voulez-vous vraiment supprimer cet incident ?');">
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
                            <i data-lucide="rss" class="empty-icon"></i>
                            <h3>Aucune actualité</h3>
                            <p>Commencez par ajouter un incident ou une actualité.</p>
                            <a href="add_news.php" class="btn btn-primary mt-3">Ajouter</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/admin/shared.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
