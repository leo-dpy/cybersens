<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    switch ($method) {
        case 'GET':
            // Récupérer un cours spécifique ou tous les cours
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $stmt = $pdo->prepare("SELECT c.*, 
                    (SELECT COUNT(*) FROM questions WHERE course_id = c.id) as nb_questions 
                    FROM courses c WHERE c.id = ?");
                $stmt->execute([$id]);
                $course = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($course) {
                    echo json_encode(['success' => true, 'course' => $course]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Cours non trouvé']);
                }
            } else {
                // Tous les cours avec comptage des questions
                $stmt = $pdo->query("SELECT c.*, 
                    (SELECT COUNT(*) FROM questions WHERE course_id = c.id) as nb_questions 
                    FROM courses c ORDER BY c.id DESC");
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'courses' => $courses]);
            }
            break;
            
        case 'POST':
            // Créer un cours (admin uniquement)
            $data = json_decode(file_get_contents('php://input'), true);
            
            $title = trim($data['title'] ?? '');
            $description = trim($data['description'] ?? '');
            $content = $data['content'] ?? '';
            $difficulty = $data['difficulty'] ?? 'Facile';
            $icon = trim($data['icon'] ?? 'shield');
            $theme = trim($data['theme'] ?? 'blue');
            $xp_reward = (int)($data['xp_reward'] ?? 25);
            $estimated_time = (int)($data['estimated_time'] ?? 15);
            
            if (empty($title) || empty($description) || empty($content)) {
                echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO courses (title, description, content, difficulty, icon, theme, xp_reward, estimated_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $content, $difficulty, $icon, $theme, $xp_reward, $estimated_time]);
            
            $id = $pdo->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Cours créé', 'id' => $id]);
            break;
            
        case 'PUT':
            // Mettre à jour un cours
            $data = json_decode(file_get_contents('php://input'), true);
            
            $id = (int)($data['id'] ?? 0);
            $title = trim($data['title'] ?? '');
            $description = trim($data['description'] ?? '');
            $content = $data['content'] ?? '';
            $difficulty = $data['difficulty'] ?? 'Facile';
            $icon = trim($data['icon'] ?? 'shield');
            $theme = trim($data['theme'] ?? 'blue');
            $xp_reward = (int)($data['xp_reward'] ?? 25);
            $estimated_time = (int)($data['estimated_time'] ?? 15);
            
            if (!$id || empty($title) || empty($description) || empty($content)) {
                echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE courses SET title = ?, description = ?, content = ?, difficulty = ?, icon = ?, theme = ?, xp_reward = ?, estimated_time = ? WHERE id = ?");
            $stmt->execute([$title, $description, $content, $difficulty, $icon, $theme, $xp_reward, $estimated_time, $id]);
            
            echo json_encode(['success' => true, 'message' => 'Cours mis à jour']);
            break;
            
        case 'DELETE':
            // Supprimer un cours
            $data = json_decode(file_get_contents('php://input'), true);
            $id = (int)($data['id'] ?? 0);
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID requis']);
                exit;
            }
            
            // Supprimer d'abord les questions associées
            $stmt = $pdo->prepare("DELETE FROM questions WHERE course_id = ?");
            $stmt->execute([$id]);
            
            // Supprimer le cours
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Cours supprimé']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Méthode non supportée']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>
