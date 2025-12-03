<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Liste des utilisateurs pour l'admin
    $sql = "SELECT id, username, email FROM users ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $users = $stmt->fetchAll();
    
    // Formater pour le frontend
    foreach ($users as &$user) {
        $user['group'] = 'Aucun'; // Valeur par défaut
        $user['xp'] = 0;
        $user['level'] = 'Novice';
        $user['role'] = 'user'; // Valeur par défaut
    }
    
    echo json_encode($users);

} elseif ($method === 'DELETE') {
    // Supprimer un utilisateur
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? 0;
    
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
    }

} elseif ($method === 'PUT') {
    // Mettre à jour le groupe d'un utilisateur
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? 0;
    $groupName = $data['group'] ?? '';

    if ($groupName === 'Aucun') {
        $groupId = null;
    } else {
        // Trouver l'ID du groupe
        $stmt = $pdo->prepare("SELECT id FROM groups WHERE name = ?");
        $stmt->execute([$groupName]);
        $group = $stmt->fetch();
        $groupId = $group ? $group['id'] : null;
    }

    $stmt = $pdo->prepare("UPDATE users SET group_id = ? WHERE id = ?");
    $stmt->execute([$groupId, $id]);
    echo json_encode(['success' => true]);
}
?>