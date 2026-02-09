<?php
require_once '../classes/Auth.php';
$user = Auth::requirePageRole('../login.php', '../index.php', ['admin']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Domaine | PSSI</title>
    <link rel="stylesheet" href="../styles/admin.css">
</head>

<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Ajouter une domaine</h1>
            <p class="subtitle">Complétez les informations ci-dessous pour créer une nouvelle domaine</p>
            <p class="subtitle" style="margin-top: 8px;">
                Connecte: <?= htmlspecialchars($user['email']) ?> |
                <a href="../logout.php">Deconnexion</a>
            </p>
        </header>
        <?php include __DIR__ . '/components/sidebar.php'; ?>
        <div class="form-card">
            <form id="domaine-form">
                <div class="form-section">
                    <h2 class="section-title">Informations générales</h2>

                    <div class="form-group">
                        <label for="nom">Nom du domaine <span class="required">*</span></label>
                        <input
                            type="text"
                            name="nom"
                            id="nom"
                            placeholder="Ex: Sécurité des données"
                            required />
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href = '../'">Annuler</button>
                    <button id="add-domaine-btn" type="button" class="btn btn-primary" data-action="form-submit" data-form="#domaine-form" data-endpoint="../api/create_entity.php" data-entity="domaine">
                        Ajouter le domaine
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script type="module" src="../js/main.js"></script>
</body>

</html>
