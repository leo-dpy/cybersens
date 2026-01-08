<?php
require_once 'auth.php';
checkAdmin();

$error = '';
$success = '';

// Récupérer les icônes disponibles
$icons = [
    'shield' => '🛡️ Sécurité',
    'lock' => '🔒 Mot de passe',
    'key' => '🔑 Authentification',
    'bug' => '🐛 Malware',
    'wifi' => '📶 Réseau',
    'mail' => '📧 Email',
    'globe' => '🌐 Web',
    'smartphone' => '📱 Mobile',
    'database' => '🗄️ Données',
    'cloud' => '☁️ Cloud',
    'code' => '💻 Code',
    'terminal' => '⌨️ Terminal',
    'alert-triangle' => '⚠️ Menaces',
    'eye' => '👁️ Surveillance',
    'users' => '👥 Social Engineering'
];

// Thèmes de couleurs pour les cours
$themes = [
    'blue' => ['name' => 'Bleu Cyber', 'primary' => '#3b82f6', 'gradient' => 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)'],
    'purple' => ['name' => 'Violet', 'primary' => '#8b5cf6', 'gradient' => 'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)'],
    'green' => ['name' => 'Vert Sécurité', 'primary' => '#10b981', 'gradient' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)'],
    'red' => ['name' => 'Rouge Alerte', 'primary' => '#ef4444', 'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)'],
    'orange' => ['name' => 'Orange', 'primary' => '#f59e0b', 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)'],
    'cyan' => ['name' => 'Cyan Tech', 'primary' => '#06b6d4', 'gradient' => 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)'],
    'pink' => ['name' => 'Rose', 'primary' => '#ec4899', 'gradient' => 'linear-gradient(135deg, #ec4899 0%, #db2777 100%)']
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $content = $_POST['content'];
    $difficulty = $_POST['difficulty'];
    $icon = $_POST['icon'] ?? 'shield';
    $theme = $_POST['theme'] ?? 'blue';
    $xp_reward = (int)$_POST['xp_reward'] ?? 25;
    $estimated_time = (int)$_POST['estimated_time'] ?? 15;
    
    if (empty($title) || empty($description) || empty($content)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        try {
            // Vérifier si les colonnes existent
            $columns = $pdo->query("DESCRIBE courses")->fetchAll(PDO::FETCH_COLUMN);
            
            $sql = "INSERT INTO courses (title, description, content, difficulty";
            $params = [$title, $description, $content, $difficulty];
            
            if (in_array('icon', $columns)) {
                $sql .= ", icon";
                $params[] = $icon;
            }
            if (in_array('theme', $columns)) {
                $sql .= ", theme";
                $params[] = $theme;
            }
            if (in_array('xp_reward', $columns)) {
                $sql .= ", xp_reward";
                $params[] = $xp_reward;
            }
            if (in_array('estimated_time', $columns)) {
                $sql .= ", estimated_time";
                $params[] = $estimated_time;
            }
            
            $sql .= ") VALUES (" . implode(',', array_fill(0, count($params), '?')) . ")";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            $course_id = $pdo->lastInsertId();
            
            // Rediriger vers l'ajout de questions
            header("Location: add_question.php?course_id=" . $course_id . "&new=1");
            exit;
            
        } catch (PDOException $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Cours - Admin CyberSens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        :root {
            --bg-color: #050505;
            --primary-color: #00f3ff;
            --secondary-color: #bc13fe;
            --accent-color: #ffe600;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-color: #e0e0e0;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-color);
            min-height: 100vh;
            color: var(--text-color);
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: 
                linear-gradient(var(--glass-border) 1px, transparent 1px),
                linear-gradient(90deg, var(--glass-border) 1px, transparent 1px);
            background-size: 50px 50px;
            opacity: 0.15;
            z-index: -2;
            pointer-events: none;
        }
        
        .creator-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .creator-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .creator-header h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }
        
        .back-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background: rgba(0, 243, 255, 0.1);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .creator-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
        }
        
        .main-form {
            background: var(--glass-bg);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid var(--glass-border);
        }
        
        .preview-panel {
            position: sticky;
            top: 2rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            background: rgba(5, 5, 5, 0.8);
            border: 2px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text-color);
            padding: 0.875rem 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(5, 5, 5, 0.9);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 243, 255, 0.2);
            color: #fff;
        }
        
        .form-control::placeholder {
            color: rgba(224, 224, 224, 0.4);
        }
        
        /* Icon Selector */
        .icon-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.5rem;
        }
        
        .icon-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            background: rgba(5, 5, 5, 0.8);
            border: 2px solid var(--glass-border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            aspect-ratio: 1;
        }
        
        .icon-option:hover {
            border-color: rgba(0, 243, 255, 0.5);
            transform: scale(1.05);
        }
        
        .icon-option.selected {
            border-color: var(--primary-color);
            background: rgba(0, 243, 255, 0.2);
        }
        
        .icon-option span {
            font-size: 1.5rem;
        }
        
        .icon-option small {
            font-size: 0.65rem;
            color: rgba(224, 224, 224, 0.6);
            margin-top: 0.25rem;
            text-align: center;
        }
        
        /* Theme Selector */
        .theme-grid {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .theme-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
            border: 3px solid transparent;
            position: relative;
        }
        
        .theme-option:hover {
            transform: scale(1.1);
        }
        
        .theme-option.selected {
            border-color: #fff;
            box-shadow: 0 0 20px currentColor;
        }
        
        .theme-option.selected::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            text-shadow: 0 0 3px rgba(0,0,0,0.5);
        }
        
        /* Difficulty Pills */
        .difficulty-pills {
            display: flex;
            gap: 0.5rem;
        }
        
        .difficulty-pill {
            flex: 1;
            padding: 1rem;
            text-align: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid var(--glass-border);
            background: rgba(5, 5, 5, 0.8);
        }
        
        .difficulty-pill:hover {
            transform: translateY(-2px);
        }
        
        .difficulty-pill.selected {
            border-color: currentColor;
        }
        
        .difficulty-pill.easy { color: var(--success); }
        .difficulty-pill.easy.selected { background: rgba(16, 185, 129, 0.2); }
        
        .difficulty-pill.medium { color: var(--warning); }
        .difficulty-pill.medium.selected { background: rgba(245, 158, 11, 0.2); }
        
        .difficulty-pill.hard { color: var(--danger); }
        .difficulty-pill.hard.selected { background: rgba(239, 68, 68, 0.2); }
        
        .difficulty-pill .emoji {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 0.25rem;
        }
        
        /* Stats inputs */
        .stats-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .stat-input {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: rgba(5, 5, 5, 0.8);
            border: 2px solid var(--glass-border);
            border-radius: 12px;
        }
        
        .stat-input i {
            font-size: 1.5rem;
        }
        
        .stat-input.xp i { color: var(--accent-color); }
        .stat-input.time i { color: var(--primary-color); }
        
        .stat-input input {
            flex: 1;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.25rem;
            font-weight: 600;
            width: 60px;
        }
        
        .stat-input input:focus {
            outline: none;
        }
        
        .stat-input span {
            color: rgba(224, 224, 224, 0.6);
            font-size: 0.85rem;
        }
        
        /* Quill Editor */
        .editor-wrapper {
            background: rgba(5, 5, 5, 0.8);
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid var(--glass-border);
        }
        
        .ql-toolbar {
            background: rgba(0,0,0,0.3) !important;
            border: none !important;
            border-bottom: 1px solid var(--glass-border) !important;
        }
        
        .ql-toolbar .ql-stroke {
            stroke: rgba(224, 224, 224, 0.6) !important;
        }
        
        .ql-toolbar .ql-fill {
            fill: rgba(224, 224, 224, 0.6) !important;
        }
        
        .ql-toolbar .ql-picker {
            color: rgba(224, 224, 224, 0.6) !important;
        }
        
        .ql-toolbar button:hover .ql-stroke,
        .ql-toolbar .ql-picker-label:hover .ql-stroke {
            stroke: var(--primary-color) !important;
        }
        
        .ql-container {
            border: none !important;
            font-size: 16px !important;
        }
        
        #editor {
            height: 300px;
            color: var(--text-color) !important;
        }
        
        .ql-editor.ql-blank::before {
            color: rgba(224, 224, 224, 0.4) !important;
            font-style: normal !important;
        }
        
        /* Preview Card */
        .preview-card {
            background: var(--glass-bg);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
        }
        
        .preview-header {
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .preview-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--preview-gradient, linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%));
            opacity: 0.9;
        }
        
        .preview-header-content {
            position: relative;
            z-index: 1;
        }
        
        .preview-icon {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
            backdrop-filter: blur(10px);
        }
        
        .preview-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }
        
        .preview-desc {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .preview-body {
            padding: 1.5rem;
        }
        
        .preview-stats {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .preview-stat {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: var(--glass-bg);
            border-radius: 8px;
            font-size: 0.85rem;
        }
        
        .preview-stat.xp { color: var(--accent-color); }
        .preview-stat.time { color: var(--primary-color); }
        .preview-stat.diff-easy { color: var(--success); }
        .preview-stat.diff-medium { color: var(--warning); }
        .preview-stat.diff-hard { color: var(--danger); }
        
        .preview-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .preview-badge.easy { background: rgba(16, 185, 129, 0.2); color: var(--success); }
        .preview-badge.medium { background: rgba(245, 158, 11, 0.2); color: var(--warning); }
        .preview-badge.hard { background: rgba(239, 68, 68, 0.2); color: var(--danger); }
        
        .preview-cta {
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 1.25rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 16px;
            color: #050505;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 40px rgba(0, 243, 255, 0.4);
        }
        
        .submit-btn i {
            font-size: 1.25rem;
        }
        
        /* Tips Panel */
        .tips-panel {
            background: rgba(0, 243, 255, 0.1);
            border: 1px solid rgba(0, 243, 255, 0.3);
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .tips-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .tips-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .tips-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            color: #94a3b8;
        }
        
        .tips-list li i {
            color: #818cf8;
            margin-top: 2px;
        }
        
        /* Error Alert */
        .error-alert {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            color: #fca5a5;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        @media (max-width: 1200px) {
            .creator-grid {
                grid-template-columns: 1fr;
            }
            .preview-panel {
                position: static;
            }
        }
    </style>
</head>
<body>
    <div class="creator-container">
        <div class="creator-header">
            <h1><i class="bi bi-sparkles me-2"></i>Créer un nouveau cours</h1>
            <a href="cours.php" class="back-btn">
                <i class="bi bi-arrow-left"></i> Retour aux cours
            </a>
        </div>
        
        <?php if($error): ?>
        <div class="error-alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" id="courseForm">
            <div class="creator-grid">
                <!-- Main Form -->
                <div class="main-form">
                    <!-- Basic Info -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-info-circle me-2"></i>Informations de base</div>
                        
                        <div class="mb-3">
                            <label class="form-label">Titre du cours *</label>
                            <input type="text" name="title" id="title" class="form-control" required
                                   placeholder="Ex: Introduction à la Cybersécurité"
                                   value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                                   oninput="updatePreview()">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description courte *</label>
                            <textarea name="description" id="description" class="form-control" rows="2" required
                                      placeholder="Une brève description qui donne envie d'apprendre..."
                                      oninput="updatePreview()"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Icon Selection -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-emoji-smile me-2"></i>Icône du cours</div>
                        <input type="hidden" name="icon" id="iconInput" value="shield">
                        <div class="icon-grid">
                            <?php foreach($icons as $value => $label): 
                                $emoji = explode(' ', $label)[0];
                                $name = trim(str_replace($emoji, '', $label));
                            ?>
                            <div class="icon-option <?php echo $value === 'shield' ? 'selected' : ''; ?>" 
                                 onclick="selectIcon('<?php echo $value; ?>', '<?php echo $emoji; ?>')">
                                <span><?php echo $emoji; ?></span>
                                <small><?php echo $name; ?></small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Theme Selection -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-palette me-2"></i>Thème de couleur</div>
                        <input type="hidden" name="theme" id="themeInput" value="blue">
                        <div class="theme-grid">
                            <?php foreach($themes as $key => $theme): ?>
                            <div class="theme-option <?php echo $key === 'blue' ? 'selected' : ''; ?>"
                                 style="background: <?php echo $theme['gradient']; ?>; color: <?php echo $theme['primary']; ?>;"
                                 onclick="selectTheme('<?php echo $key; ?>', '<?php echo $theme['gradient']; ?>')"
                                 title="<?php echo $theme['name']; ?>">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Difficulty -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-speedometer me-2"></i>Difficulté</div>
                        <input type="hidden" name="difficulty" id="difficultyInput" value="Facile">
                        <div class="difficulty-pills">
                            <div class="difficulty-pill easy selected" onclick="selectDifficulty('Facile', 'easy')">
                                <span class="emoji">🟢</span>
                                Facile
                            </div>
                            <div class="difficulty-pill medium" onclick="selectDifficulty('Intermédiaire', 'medium')">
                                <span class="emoji">🟡</span>
                                Intermédiaire
                            </div>
                            <div class="difficulty-pill hard" onclick="selectDifficulty('Difficile', 'hard')">
                                <span class="emoji">🔴</span>
                                Difficile
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-bar-chart me-2"></i>Récompenses & Durée</div>
                        <div class="stats-row">
                            <div class="stat-input xp">
                                <i class="bi bi-lightning-fill"></i>
                                <div>
                                    <input type="number" name="xp_reward" id="xpReward" value="25" min="5" max="200" oninput="updatePreview()">
                                    <span>XP</span>
                                </div>
                            </div>
                            <div class="stat-input time">
                                <i class="bi bi-clock-fill"></i>
                                <div>
                                    <input type="number" name="estimated_time" id="estimatedTime" value="15" min="5" max="120" oninput="updatePreview()">
                                    <span>min</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content Editor -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-file-richtext me-2"></i>Contenu du cours *</div>
                        <div class="editor-wrapper">
                            <div id="editor"><?php echo isset($_POST['content']) ? $_POST['content'] : ''; ?></div>
                        </div>
                        <input type="hidden" name="content" id="content">
                    </div>
                    
                    <!-- Submit -->
                    <button type="submit" class="submit-btn">
                        <i class="bi bi-rocket-takeoff"></i>
                        Créer le cours et ajouter des questions
                    </button>
                </div>
                
                <!-- Preview Panel -->
                <div class="preview-panel">
                    <div class="preview-card">
                        <div class="preview-header" id="previewHeader" style="--preview-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                            <div class="preview-header-content">
                                <div class="preview-icon" id="previewIcon">🛡️</div>
                                <h3 class="preview-title" id="previewTitle">Titre du cours</h3>
                                <p class="preview-desc" id="previewDesc">Description du cours...</p>
                            </div>
                        </div>
                        <div class="preview-body">
                            <div class="preview-stats">
                                <div class="preview-stat xp">
                                    <i class="bi bi-lightning-fill"></i>
                                    <span id="previewXp">25 XP</span>
                                </div>
                                <div class="preview-stat time">
                                    <i class="bi bi-clock"></i>
                                    <span id="previewTime">15 min</span>
                                </div>
                                <span class="preview-badge easy" id="previewDifficulty">
                                    🟢 Facile
                                </span>
                            </div>
                            <button type="button" class="preview-cta" id="previewCta" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
                                <i class="bi bi-play-fill"></i> Commencer le cours
                            </button>
                        </div>
                    </div>
                    
                    <div class="tips-panel">
                        <div class="tips-title">
                            <i class="bi bi-lightbulb-fill"></i>
                            Conseils pour un bon cours
                        </div>
                        <ul class="tips-list">
                            <li><i class="bi bi-check2"></i> Utilisez des titres pour structurer le contenu</li>
                            <li><i class="bi bi-check2"></i> Ajoutez des exemples concrets</li>
                            <li><i class="bi bi-check2"></i> Gardez les paragraphes courts</li>
                            <li><i class="bi bi-check2"></i> Mettez en gras les concepts clés</li>
                            <li><i class="bi bi-check2"></i> Préparez 3-5 questions de quiz</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
        // Initialize Quill Editor
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Écrivez le contenu de votre cours ici...\n\nUtilisez les outils de formatage pour structurer votre texte.',
            modules: {
                toolbar: [
                    [{ 'header': [2, 3, 4, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'code-block'],
                    ['clean']
                ]
            }
        });
        
        // Copy content to hidden field before submit
        document.getElementById('courseForm').onsubmit = function() {
            document.getElementById('content').value = quill.root.innerHTML;
        };
        
        // Icon Selection
        function selectIcon(value, emoji) {
            document.querySelectorAll('.icon-option').forEach(el => el.classList.remove('selected'));
            event.currentTarget.classList.add('selected');
            document.getElementById('iconInput').value = value;
            document.getElementById('previewIcon').textContent = emoji;
        }
        
        // Theme Selection
        function selectTheme(value, gradient) {
            document.querySelectorAll('.theme-option').forEach(el => el.classList.remove('selected'));
            event.currentTarget.classList.add('selected');
            document.getElementById('themeInput').value = value;
            document.getElementById('previewHeader').style.setProperty('--preview-gradient', gradient);
            document.getElementById('previewCta').style.background = gradient;
        }
        
        // Difficulty Selection
        function selectDifficulty(value, level) {
            document.querySelectorAll('.difficulty-pill').forEach(el => el.classList.remove('selected'));
            event.currentTarget.classList.add('selected');
            document.getElementById('difficultyInput').value = value;
            
            const badge = document.getElementById('previewDifficulty');
            badge.className = 'preview-badge ' + level;
            const emojis = { easy: '🟢', medium: '🟡', hard: '🔴' };
            badge.innerHTML = emojis[level] + ' ' + value;
        }
        
        // Update Preview
        function updatePreview() {
            const title = document.getElementById('title').value || 'Titre du cours';
            const desc = document.getElementById('description').value || 'Description du cours...';
            const xp = document.getElementById('xpReward').value || '25';
            const time = document.getElementById('estimatedTime').value || '15';
            
            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewDesc').textContent = desc;
            document.getElementById('previewXp').textContent = xp + ' XP';
            document.getElementById('previewTime').textContent = time + ' min';
        }
        
        // Initial preview update
        updatePreview();
    </script>
</body>
</html>
