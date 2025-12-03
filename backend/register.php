<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$username || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
    exit;
}

// Vérifier si l'email existe déjà
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
    exit;
}

// Création de l'utilisateur
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([$username, $email, $passwordHash]);
    $userId = $pdo->lastInsertId();
    
    // Récupérer l'utilisateur créé pour le renvoyer au frontend
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    unset($user['password']);
    // Ajout de valeurs par défaut pour le frontend
    $user['group'] = 'Aucun';
    $user['xp'] = 0;
    $user['level'] = 'Novice';

    echo json_encode(['success' => true, 'user' => $user]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()]);
}
?>