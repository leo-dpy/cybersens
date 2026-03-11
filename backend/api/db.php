<?php
// Configuration de la base de données CyberSens. Inclus par les scripts backend.

// Démarrer la session pour l'admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CONFIGURATION BASE DE DONNÉES (Support des variables d'environnement pour Coolify/AWS)
$host = getenv('DB_HOST') ?: $_SERVER['DB_HOST'] ?? '127.0.0.1';
$dbname = getenv('DB_NAME') ?: $_SERVER['DB_NAME'] ?? 'cybersens';
$user = getenv('DB_USER') ?: $_SERVER['DB_USER'] ?? 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_SERVER['DB_PASS'] ?? '');

// CONNEXION PDO
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
        $user, 
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 5 // Timeout rapide de 5 secondes pour éviter le crash fatal si le pare-feu AWS bloque
        ]
    );
} catch (PDOException $e) {
    // Détection du contexte (API JSON vs Page HTML)
    $isApiRequest = (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) 
                 || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                 || (strpos($_SERVER['REQUEST_URI'], '/api/') !== false);

    if ($isApiRequest) {
        // Gestion d'erreur formatée JSON (Affichage du detail temporaire pour AWS)
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => "Erreur DB: " . $e->getMessage(),
            'host' => $host // Pour vérifier que la variable est bien lue
        ]);
    } else {
        // Affichage HTML pour l'admin ou le site
        http_response_code(500);
        die("
            <div style='font-family: sans-serif; text-align: center; padding: 2rem; color: #333;'>
                <h1><span style='color: #ef4444;'>⚠</span> Erreur de connexion</h1>
                <p>Impossible de se connecter à la base de données ($host).</p>
                <p style='color: #666; font-size: 0.9em;'>Vérifiez vos identifiants ou si le serveur MySQL est démarré.</p>
                <!-- Detail technique: " . htmlspecialchars($e->getMessage()) . " -->
            </div>
        ");
    }
    exit;
}
?>