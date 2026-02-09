<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../classes/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$preuveId = $_POST['preuve_id'] ?? null;

if (!$preuveId) {
    echo json_encode(['success' => false, 'message' => 'ID de preuve manquant']);
    exit;
}

try {
    $database = new Database();
    $filepath = $database->deletePreuve($preuveId);
    
    if ($filepath) {
        // Supprimer le fichier physique
        $fullPath = __DIR__ . '/../' . $filepath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Preuve supprimée avec succès'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Preuve non trouvée']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>
