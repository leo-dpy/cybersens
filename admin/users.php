<?php
require_once 'auth.php';
checkAdmin();

$message = '';
$messageType = '';

// Déterminer si la table utilise 'role' ou 'is_admin'
$columns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
$useRole = in_array('role', $columns);
$hasXp = in_array('xp', $columns);
$hasLevel = in_array('level', $columns);
$hasGroup = in_array('group_name', $columns);

// Groupes disponibles
$availableGroups = ['Aucun', 'Red Team', 'Blue Team', 'Purple Team', 'Staff', 'VIP'];

// Récupérer tous les utilisateurs
$sql = "SELECT u.*, 
    (SELECT COUNT(*) FROM progression WHERE user_id = u.id AND is_completed = 1) as completed_courses,
    (SELECT COUNT(*) FROM user_badges WHERE user_id = u.id) as badges_count
    FROM users u ORDER BY u.created_at DESC";
$users = $pdo->query($sql)->fetchAll();

// Modifier un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'edit_user') {
        $uid = (int)$_POST['user_id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $xp = (int)$_POST['xp'];
        $level = (int)$_POST['level'];
        $role = $_POST['role'];
        $group_name = trim($_POST['group_name'] ?? 'Aucun');
        
        try {
            if ($useRole) {
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, xp = ?, level = ?, role = ?, group_name = ? WHERE id = ?");
                $stmt->execute([$username, $email, $xp, $level, $role, $group_name, $uid]);
            } else {
                $isAdmin = ($role === 'admin') ? 1 : 0;
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, xp = ?, level = ?, is_admin = ?, group_name = ? WHERE id = ?");
                $stmt->execute([$username, $email, $xp, $level, $isAdmin, $group_name, $uid]);
            }
            
            // Mettre à jour le mot de passe si fourni
            if (!empty($_POST['new_password'])) {
                $hashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $uid]);
            }
            
            $message = "Utilisateur mis à jour avec succès !";
            $messageType = "success";
            
            // Rafraîchir la liste
            $users = $pdo->query($sql)->fetchAll();
            
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "danger";
        }
    }
    
    if ($_POST['action'] === 'add_xp') {
        $uid = (int)$_POST['user_id'];
        $addXp = (int)$_POST['add_xp'];
        
        $stmt = $pdo->prepare("UPDATE users SET xp = xp + ? WHERE id = ?");
        $stmt->execute([$addXp, $uid]);
        
        // Recalculer le niveau
        $stmt = $pdo->prepare("SELECT xp FROM users WHERE id = ?");
        $stmt->execute([$uid]);
        $currentXp = $stmt->fetchColumn();
        $newLevel = floor($currentXp / 100) + 1;
        
        $stmt = $pdo->prepare("UPDATE users SET level = ? WHERE id = ?");
        $stmt->execute([$newLevel, $uid]);
        
        $message = "XP ajoutée ! Nouveau total : " . $currentXp . " XP (Niveau " . $newLevel . ")";
        $messageType = "success";
        
        $users = $pdo->query($sql)->fetchAll();
    }
}

// Promouvoir/rétrograder admin
if (isset($_GET['toggle_admin']) && is_numeric($_GET['toggle_admin'])) {
    $uid = (int)$_GET['toggle_admin'];
    if ($uid != $_SESSION['user_id']) {
        if ($useRole) {
            $stmt = $pdo->prepare("UPDATE users SET role = CASE WHEN role = 'admin' THEN 'user' ELSE 'admin' END WHERE id = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE users SET is_admin = NOT is_admin WHERE id = ?");
        }
        $stmt->execute([$uid]);
    }
    header("Location: users.php?msg=role_updated");
    exit;
}

// Supprimer un utilisateur
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    if ($uid != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$uid]);
    }
    header("Location: users.php?msg=deleted");
    exit;
}

// Fonction pour vérifier si admin
function isUserAdmin($user, $useRole) {
    if ($useRole) {
        return isset($user['role']) && $user['role'] === 'admin';
    }
    return isset($user['is_admin']) && $user['is_admin'] == 1;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Admin CyberSens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="admin-style.css">
    <style>
        /* Users specific styles */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .stat-icon.purple { background: rgba(188, 19, 254, 0.15); color: var(--secondary-color); }
        .stat-icon.green { background: rgba(16, 185, 129, 0.15); color: var(--success); }
        .stat-icon.blue { background: rgba(0, 243, 255, 0.15); color: var(--primary-color); }
        .stat-icon.orange { background: rgba(245, 158, 11, 0.15); color: var(--warning); }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--text-color); }
        .stat-label { font-size: 0.85rem; color: rgba(224, 224, 224, 0.6); }
        
        .users-table {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            overflow: hidden;
        }
        .users-table table {
            margin: 0;
        }
        .users-table th {
            background: rgba(0, 243, 255, 0.05);
            font-weight: 600;
            color: var(--primary-color);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem 1.25rem;
            border: none;
        }
        .users-table td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-color: var(--glass-border);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #050505;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .user-name {
            font-weight: 600;
            color: var(--text-color);
        }
        .user-email {
            font-size: 0.85rem;
            color: rgba(224, 224, 224, 0.6);
        }
        .badge-role {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-admin { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
        .badge-user { background: rgba(0, 243, 255, 0.15); color: var(--primary-color); }
        .xp-display {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .xp-badge {
            background: linear-gradient(135deg, var(--accent-color) 0%, #f59e0b 100%);
            color: #050505;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .level-badge {
            background: rgba(0, 243, 255, 0.15);
            color: var(--primary-color);
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            cursor: pointer;
        }
        .action-btn.edit { background: rgba(0, 243, 255, 0.15); color: var(--primary-color); }
        .action-btn.edit:hover { background: var(--primary-color); color: #050505; }
        .action-btn.xp { background: rgba(245, 158, 11, 0.15); color: var(--warning); }
        .action-btn.xp:hover { background: var(--warning); color: #050505; }
        .action-btn.admin { background: rgba(16, 185, 129, 0.15); color: var(--success); }
        .action-btn.admin:hover { background: var(--success); color: #050505; }
        .action-btn.delete { background: rgba(239, 68, 68, 0.15); color: var(--danger); }
        .action-btn.delete:hover { background: var(--danger); color: white; }
        
        /* Modal overrides for users page */
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #050505;
            border-radius: 16px 16px 0 0;
            padding: 1.25rem 1.5rem;
        }
        .modal-header .modal-title {
            color: #050505;
            font-weight: 700;
        }
        
        @media (max-width: 992px) {
            .sidebar { width: 80px; }
            .sidebar-brand h4, .sidebar .nav-link span { display: none; }
            .sidebar .nav-link { justify-content: center; padding: 1rem; }
            main { margin-left: 80px; }
        }
    </style>
</head>
<body>
<div class="container-fluid p-0">
    <!-- Sidebar -->
    <nav class="sidebar" style="width: 260px;">
        <div class="sidebar-brand">
            <i class="bi bi-shield-lock-fill"></i>
            <h4>CyberSens</h4>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cours.php">
                    <i class="bi bi-book"></i> <span>Cours</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="questions.php">
                    <i class="bi bi-question-circle"></i> <span>Questions</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="users.php">
                    <i class="bi bi-people"></i> <span>Utilisateurs</span>
                </a>
            </li>
            <li class="nav-item" style="margin-top: auto; padding-top: 2rem;">
                <a class="nav-link back-link" href="../index.html">
                    <i class="bi bi-arrow-left"></i> <span>Retour au site</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main style="margin-left: 260px; padding: 2rem;">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1">Gestion des Utilisateurs</h1>
                <p class="text-muted mb-0">Gérez les comptes, XP et permissions</p>
            </div>
        </div>

        <?php if($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?php 
            if($_GET['msg'] == 'deleted') echo 'Utilisateur supprimé !';
            if($_GET['msg'] == 'role_updated') echo 'Rôle mis à jour !';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-value"><?php echo count($users); ?></div>
                    <div class="stat-label">Utilisateurs</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div class="stat-value"><?php echo count(array_filter($users, fn($u) => isUserAdmin($u, $useRole))); ?></div>
                    <div class="stat-label">Administrateurs</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="bi bi-star-fill"></i></div>
                <div>
                    <div class="stat-value"><?php echo array_sum(array_column($users, 'xp')); ?></div>
                    <div class="stat-label">XP Total</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="bi bi-trophy-fill"></i></div>
                <div>
                    <div class="stat-value"><?php echo array_sum(array_column($users, 'badges_count')); ?></div>
                    <div class="stat-label">Badges distribués</div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="users-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Groupe</th>
                        <th>XP / Niveau</th>
                        <th>Progression</th>
                        <th>Inscrit le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($u['username'], 0, 2)); ?>
                                </div>
                                <div>
                                    <div class="user-name">
                                        <?php echo htmlspecialchars($u['username']); ?>
                                        <?php if($u['id'] == $_SESSION['user_id']): ?>
                                        <span class="badge bg-info ms-1">Vous</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="user-email"><?php echo htmlspecialchars($u['email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if(isUserAdmin($u, $useRole)): ?>
                            <span class="badge-role badge-admin"><i class="bi bi-shield-check me-1"></i>Admin</span>
                            <?php else: ?>
                            <span class="badge-role badge-user"><i class="bi bi-person me-1"></i>Utilisateur</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $group = $u['group_name'] ?? 'Aucun';
                            $groupColors = [
                                'Red Team' => '#ef4444',
                                'Blue Team' => '#3b82f6', 
                                'Purple Team' => '#8b5cf6',
                                'Staff' => '#10b981',
                                'VIP' => '#f59e0b'
                            ];
                            $groupColor = $groupColors[$group] ?? '#64748b';
                            ?>
                            <span class="badge" style="background: <?php echo $groupColor; ?>20; color: <?php echo $groupColor; ?>; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                                <?php echo htmlspecialchars($group); ?>
                            </span>
                        </td>
                        <td>
                            <div class="xp-display">
                                <span class="xp-badge"><i class="bi bi-lightning-fill me-1"></i><?php echo $u['xp'] ?? 0; ?></span>
                                <span class="level-badge">Niv. <?php echo $u['level'] ?? 1; ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-journal-check text-success"></i>
                                <span><?php echo $u['completed_courses']; ?> cours</span>
                                <i class="bi bi-award text-warning ms-2"></i>
                                <span><?php echo $u['badges_count']; ?> badges</span>
                            </div>
                        </td>
                        <td class="text-muted">
                            <?php echo date('d/m/Y', strtotime($u['created_at'])); ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-end">
                                <button class="action-btn edit" title="Modifier" 
                                        onclick="openEditModal(<?php echo htmlspecialchars(json_encode($u)); ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-btn xp" title="Ajouter XP" 
                                        onclick="openXpModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['username']); ?>')">
                                    <i class="bi bi-lightning"></i>
                                </button>
                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                <a href="users.php?toggle_admin=<?php echo $u['id']; ?>" 
                                   class="action-btn admin" title="Changer rôle"
                                   onclick="return confirm('Changer le rôle de cet utilisateur ?')">
                                    <i class="bi bi-shield"></i>
                                </a>
                                <a href="users.php?delete=<?php echo $u['id']; ?>" 
                                   class="action-btn delete" title="Supprimer"
                                   onclick="return confirm('Supprimer cet utilisateur ? Cette action est irréversible.')">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Modifier l'utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_user">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom d'utilisateur</label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-lightning-fill text-warning me-1"></i>XP</label>
                            <input type="number" name="xp" id="edit_xp" class="form-control" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-bar-chart-fill text-primary me-1"></i>Niveau</label>
                            <input type="number" name="level" id="edit_level" class="form-control" min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Rôle</label>
                            <select name="role" id="edit_role" class="form-select">
                                <option value="user">Utilisateur</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label"><i class="bi bi-people-fill text-info me-1"></i>Groupe</label>
                            <select name="group_name" id="edit_group" class="form-select">
                                <?php foreach($availableGroups as $g): ?>
                                <option value="<?php echo $g; ?>"><?php echo $g; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nouveau mot de passe <small class="text-muted">(laisser vide pour ne pas changer)</small></label>
                            <input type="password" name="new_password" class="form-control" placeholder="••••••••">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Sauvegarder</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add XP -->
<div class="modal fade" id="xpModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                <h5 class="modal-title"><i class="bi bi-lightning-fill me-2"></i>Ajouter XP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body text-center">
                    <input type="hidden" name="action" value="add_xp">
                    <input type="hidden" name="user_id" id="xp_user_id">
                    
                    <p class="mb-3">Ajouter XP à <strong id="xp_username"></strong></p>
                    
                    <div class="d-flex gap-2 justify-content-center mb-3">
                        <button type="button" class="btn btn-outline-warning" onclick="setXp(10)">+10</button>
                        <button type="button" class="btn btn-outline-warning" onclick="setXp(25)">+25</button>
                        <button type="button" class="btn btn-outline-warning" onclick="setXp(50)">+50</button>
                        <button type="button" class="btn btn-outline-warning" onclick="setXp(100)">+100</button>
                    </div>
                    
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lightning"></i></span>
                        <input type="number" name="add_xp" id="add_xp" class="form-control" min="1" value="10" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning w-100"><i class="bi bi-plus-lg me-1"></i>Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const editModal = new bootstrap.Modal(document.getElementById('editModal'));
const xpModal = new bootstrap.Modal(document.getElementById('xpModal'));

function openEditModal(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_xp').value = user.xp || 0;
    document.getElementById('edit_level').value = user.level || 1;
    document.getElementById('edit_role').value = (user.role === 'admin' || user.is_admin == 1) ? 'admin' : 'user';
    document.getElementById('edit_group').value = user.group_name || 'Aucun';
    editModal.show();
}

function openXpModal(userId, username) {
    document.getElementById('xp_user_id').value = userId;
    document.getElementById('xp_username').textContent = username;
    document.getElementById('add_xp').value = 10;
    xpModal.show();
}

function setXp(value) {
    document.getElementById('add_xp').value = value;
}
</script>
</body>
</html>
