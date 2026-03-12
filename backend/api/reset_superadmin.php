<?php
// SCRIPT TEMPORAIRE - A SUPPRIMER APRES UTILISATION
require 'db.php';

$newPassword = 'SuperAdmin2024!'; // <-- Changez ce mot de passe si besoin

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE role = 'superadmin'");
$stmt->execute([$hashedPassword]);

$rows = $stmt->rowCount();

if ($rows > 0) {
    echo "Mot de passe superadmin reinitialise avec succes. ($rows compte(s) mis a jour)\n";
    echo "Nouveau mot de passe : $newPassword\n";
    echo "\n*** SUPPRIMEZ CE FICHIER IMMEDIATEMENT ***";
} else {
    echo "Aucun utilisateur superadmin trouve dans la base.";
}
?>
