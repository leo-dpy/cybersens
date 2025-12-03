<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require 'db.php';

// Récupérer le classement des groupes
$sql = "SELECT g.name, SUM(u.xp) as totalXp, COUNT(u.id) as members 
        FROM groups g 
        JOIN users u ON g.id = u.group_id 
        GROUP BY g.id, g.name 
        ORDER BY totalXp DESC";

$stmt = $pdo->query($sql);
$leaderboard = $stmt->fetchAll();

echo json_encode($leaderboard);
?>