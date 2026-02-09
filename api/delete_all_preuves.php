<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../classes/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$clauseId = $_POST['clause_id'] ?? null;

if (!$clauseId) {
    echo json_encode(['success' => false, 'message' => 'ID de clause manquant']);
    exit;
}

try {
    $database = new Database();
    $filepaths = $database->deleteAllPreuvesByClause($clauseId);
    
    // Supprimer les fichiers physiques
    foreach ($filepaths as $filepath) {
        $fullPath = __DIR__ . '/../' . $filepath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => count($filepaths) . ' preuve(s) supprimée(s)',
        'count' => count($filepaths)
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>
