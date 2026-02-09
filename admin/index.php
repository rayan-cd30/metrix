<?php
require_once '../classes/Auth.php';
require_once '../classes/Database.php';
$user = Auth::requirePageRole('../login.php', '../index.php', ['admin']);
$database = new Database();
$domaines = $database->getDomaines();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Ajouter une clause</title>
    <link rel="stylesheet" href="../styles/admin.css">
</head>

<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Ajouter une clause</h1>
            <p class="subtitle">Complétez les informations ci-dessous pour créer une nouvelle clause</p>
            <p class="subtitle" style="margin-top: 8px;">
                Connecte: <?= htmlspecialchars($user['email']) ?> |
                <a href="../logout.php">Deconnexion</a>
            </p>
        </header>
        <!-- Sidebar Admin Navigation -->
        <?php include __DIR__ . '/components/sidebar.php'; ?>

        <div class="form-card">
            <form id="clause-form">
                <div class="form-section">
                    <h2 class="section-title">Informations générales</h2>
                    
                    <div class="form-group">
                        <label for="domaine">Domaine <span class="required">*</span></label>
                        <select name="domaine" id="domaine" required>
                            <option value="">Sélectionnez un domaine</option>
                            <?php foreach ($domaines as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nom">Nom de la clause <span class="required">*</span></label>
                        <input 
                            type="text"
                            name="nom" 
                            id="nom" 
                            placeholder="Ex: Clause de confidentialité"
                            required
                        />
                    </div>
                </div>

                <div class="form-section">
                    <h2 class="section-title">Détails de la clause</h2>
                    
                    <div class="form-group">
                        <label for="objective">Objectif de la clause <span class="required">*</span></label>
                        <textarea 
                            name="objective" 
                            id="objective" 
                            placeholder="Décrivez l'objectif principal de cette clause..."
                            rows="3"
                            required
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="input">Input de la clause <span class="required">*</span></label>
                        <textarea 
                            name="input" 
                            id="input" 
                            placeholder="Saisissez le contenu ou les paramètres de la clause..."
                            rows="4"
                            required
                        ></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href = '../'">Annuler</button>
                    <button id="add-clause-btn" type="button" class="btn btn-primary" data-action="form-submit" data-form="#clause-form" data-endpoint="../api/create_entity.php" data-entity="clause">
                        Ajouter la clause
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script type="module" src="../js/main.js"></script>
</body>

</html>
