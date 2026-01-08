<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
    exit;
}

// Récupération de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// Vérification du mot de passe (supporte haché et clair pour dev)
$isValid = false;
if ($user) {
    if (password_verify($password, $user['password'])) {
        $isValid = true;
    } elseif ($user['password'] === $password) {
        $isValid = true;
    }
}

if ($isValid) {
    // Stocker l'ID en session pour l'admin
    $_SESSION['user_id'] = $user['id'];
    
    // On ne renvoie jamais le mot de passe
    unset($user['password']);
    
    // Récupérer les stats
    $statsStmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT p.course_id) as courses_completed,
            COUNT(DISTINCT ub.badge_id) as badges_count
        FROM users u
        LEFT JOIN progression p ON u.id = p.user_id AND p.is_completed = 1
        LEFT JOIN user_badges ub ON u.id = ub.user_id
        WHERE u.id = ?
    ");
    $statsStmt->execute([$user['id']]);
    $stats = $statsStmt->fetch();
    
    $user['completedCourses'] = (int)($stats['courses_completed'] ?? 0);
    $user['badgesCount'] = (int)($stats['badges_count'] ?? 0);
    $user['certificatesCount'] = 0;
    
    // XP et level viennent directement de la BDD maintenant
    $user['xp'] = (int)($user['xp'] ?? 0);
    $user['level'] = (int)($user['level'] ?? 1);
    
    // Vérifier si l'utilisateur est admin (supporte 'role' ET 'is_admin' pour compatibilité)
    $isAdmin = false;
    if (isset($user['role']) && $user['role'] === 'admin') {
        $isAdmin = true;
    } elseif (isset($user['is_admin']) && $user['is_admin'] == 1) {
        $isAdmin = true;
    }
    $user['role'] = $isAdmin ? 'admin' : 'user';
    
    // Mettre à jour last_login
    try {
        $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
    } catch (Exception $e) {}
    
    // Attribuer le badge "first_login" si premier login
    try {
        $badgeStmt = $pdo->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) 
            SELECT ?, id FROM badges WHERE slug = 'first_login' OR name = 'Première Connexion' LIMIT 1");
        $badgeStmt->execute([$user['id']]);
    } catch (Exception $e) {}
    
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Identifiants invalides']);
}
?>