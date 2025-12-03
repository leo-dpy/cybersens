<?php
require 'db.php';

echo "<h1>Debug Users</h1>";

try {
    // Check table structure
    echo "<h2>Table Structure</h2>";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";

    // Check users
    echo "<h2>Users List</h2>";
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "<p>Aucun utilisateur trouvé dans la base de données.</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Password (Raw)</th></tr>";
        foreach ($users as $u) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($u['id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($u['username'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($u['email'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($u['password'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>