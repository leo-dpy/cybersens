<?php
require 'db.php';

echo "<h1>Test de connexion à la base de données</h1>";

try {
    $stmt = $pdo->query("SELECT 1");
    if ($stmt) {
        echo "<p style='color: green; font-weight: bold;'>✅ Connexion réussie !</p>";
        echo "<p>Le serveur PHP fonctionne et la base de données est accessible.</p>";
        
        // Test de lecture des utilisateurs
        $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "<p>Nombre d'utilisateurs dans la base : <strong>$count</strong></p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Erreur de connexion</p>";
    echo "<p>Détails : " . $e->getMessage() . "</p>";
}
?>