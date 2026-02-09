<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../classes/Database.php';

header('Content-Type: application/json');

// Vérifier les données reçues
if (!isset($_POST['observation_id']) || !isset($_POST['observation'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Données manquantes'
    ]);
    exit;
}

$observationId = intval($_POST['observation_id']);
$newObservation = trim($_POST['observation']);
$note = isset($_POST['note']) ? intval($_POST['note']) : null;

if (empty($newObservation)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'L\'observation ne peut pas être vide'
    ]);
    exit;
}

try {
    $database = new Database();
    $database->updateObservation($observationId, $newObservation, $note);

    echo json_encode([
        'success' => true,
        'message' => 'Observation mise à jour avec succès'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
    ]);
}
?>
