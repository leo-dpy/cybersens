<?php
// Fichier d'authentification admin
session_start();
require_once '../backend/db.php';

// Vérifier si l'utilisateur est connecté et est admin
function checkAdmin() {
    global $pdo;
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.html");
        exit;
    }
    
    // Support des deux formats: 'role' ou 'is_admin'
    try {
        $columns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('role', $columns)) {
            $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            $isAdmin = ($user && $user['role'] === 'admin');
        } else {
            $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            $isAdmin = ($user && $user['is_admin'] == 1);
        }
        
        if (!$isAdmin) {
            header("Location: ../index.html");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: ../index.html");
        exit;
    }
}
?>
