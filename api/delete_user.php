<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../classes/Database.php';

header('Content-Type: application/json');

function jsonResponse($payload, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Methode non autorisee'], 405);
}

$currentUser = Auth::requireApiRole(['admin']);

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
if ($userId <= 0) {
    jsonResponse(['success' => false, 'message' => 'ID utilisateur invalide'], 400);
}

if ((int)$currentUser['id'] === $userId) {
    jsonResponse(['success' => false, 'message' => 'Vous ne pouvez pas supprimer votre propre compte'], 400);
}

try {
    $database = new Database();
    $targetUser = $database->getUserById($userId);

    if (!$targetUser) {
        jsonResponse(['success' => false, 'message' => 'Utilisateur introuvable'], 404);
    }

    if (($targetUser['role'] ?? '') === 'admin') {
        $adminCount = $database->countUsersByRole('admin');
        if ($adminCount <= 1) {
            jsonResponse(['success' => false, 'message' => 'Impossible de supprimer le dernier compte admin'], 400);
        }
    }

    $deleted = $database->deleteUserById($userId);
    if (!$deleted) {
        jsonResponse(['success' => false, 'message' => 'Suppression echouee'], 500);
    }

    jsonResponse([
        'success' => true,
        'message' => 'Utilisateur supprime avec succes',
        'data' => [
            'id' => $userId
        ]
    ]);
} catch (Throwable $e) {
    jsonResponse(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
}

?>
