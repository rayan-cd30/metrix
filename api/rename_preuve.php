<?php
/**
 * API pour renommer une preuve
 */

require_once __DIR__ . '/auth_guard.php';
header('Content-Type: application/json');

require_once __DIR__ . '/../classes/Database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }

    $preuveId = isset($_POST['preuve_id']) ? intval($_POST['preuve_id']) : 0;
    $newFilename = isset($_POST['filename']) ? trim($_POST['filename']) : '';

    if ($preuveId <= 0) {
        throw new Exception('ID de preuve invalide');
    }

    if (empty($newFilename)) {
        throw new Exception('Le nouveau nom ne peut pas être vide');
    }

    $database = new Database();
    $result = $database->renamePreuve($preuveId, $newFilename);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Preuve renommée avec succès',
            'filename' => $newFilename
        ]);
    } else {
        throw new Exception('Erreur lors du renommage de la preuve');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
