<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Pour le développement
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
// Note: Dans un vrai projet, utilisez password_verify() avec des mots de passe hachés
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// Vérification du mot de passe (supporte haché et clair)
$isValid = false;
if ($user) {
    if (password_verify($password, $user['password'])) {
        $isValid = true;
    } elseif ($user['password'] === $password) {
        $isValid = true;
    }
}

if ($isValid) {
    // On ne renvoie jamais le mot de passe
    unset($user['password']);
    
    // Normalisation pour le frontend
    $user['group'] = 'Aucun';
    $user['xp'] = $user['xp'] ?? 0;
    $user['level'] = $user['level'] ?? 'Novice';
    $user['role'] = $user['role'] ?? 'user';
    
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Identifiants invalides']);
}
?>