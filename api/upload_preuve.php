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

// Vérifier si un fichier a été uploadé
if (!isset($_FILES['preuve']) || $_FILES['preuve']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload du fichier']);
    exit;
}

$file = $_FILES['preuve'];
$allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$maxSize = 10 * 1024 * 1024; // 10 MB

// Vérifier le type de fichier
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé. Formats acceptés: PDF, PNG, JPG, DOC, DOCX']);
    exit;
}

// Vérifier la taille
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'Fichier trop volumineux (max 10 MB)']);
    exit;
}

// Créer le dossier uploads s'il n'existe pas (relatif à la racine du projet)
$uploadDir = __DIR__ . '/../uploads/preuves/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Générer un nom de fichier unique (avec uniqid pour éviter les collisions)
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'preuve_' . $clauseId . '_' . time() . '_' . uniqid() . '.' . $extension;
$filepath = $uploadDir . $filename;
$webPath = 'uploads/preuves/' . $filename; // Chemin pour le navigateur

// Déplacer le fichier
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    try {
        $database = new Database();
        $preuveId = $database->addPreuve($clauseId, $webPath, $file['name']);
        
        if ($preuveId) {
            echo json_encode([
                'success' => true, 
                'message' => 'Preuve uploadée avec succès',
                'preuveId' => $preuveId,
                'filepath' => $webPath,
                'filename' => $file['name']
            ]);
        } else {
            // Supprimer le fichier si la mise à jour échoue
            unlink($filepath);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de la base de données']);
        }
    } catch (Exception $e) {
        unlink($filepath);
        echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors du déplacement du fichier']);
}
?>
