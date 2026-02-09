<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../classes/Database.php';

header('Content-Type: application/json');

// Vérifier les données reçues
if (!isset($_POST['metrique_id']) || !isset($_POST['value'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Données manquantes'
    ]);
    exit;
}

$metriqueId = intval($_POST['metrique_id']);
$newValue = intval($_POST['value']);

try {
    $database = new Database();
    $database->updateMetricValue($metriqueId, $newValue);
    
    echo json_encode([
        'success' => true,
        'message' => 'Valeur mise à jour avec succès'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
    ]);
}
?>
