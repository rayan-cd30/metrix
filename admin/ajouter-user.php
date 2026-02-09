<?php
require_once '../classes/Auth.php';
$user = Auth::requirePageRole('../login.php', '../index.php', ['admin']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Utilisateur | Metrix</title>
    <link rel="stylesheet" href="../styles/admin.css">
</head>

<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Ajouter un utilisateur</h1>
            <p class="subtitle">Cree un nouvel acces applicatif</p>
            <p class="subtitle" style="margin-top: 8px;">
                Connecte: <?= htmlspecialchars($user['email']) ?> |
                <a href="../logout.php">Deconnexion</a>
            </p>
        </header>

        <?php include __DIR__ . '/components/sidebar.php'; ?>

        <div class="form-card">
            <form id="user-form">
                <div class="form-section">
                    <h2 class="section-title">Informations de connexion</h2>

                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            placeholder="Ex: admin@metrix.local"
                            required />
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe <span class="required">*</span></label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Minimum 8 caracteres"
                            minlength="8"
                            required />
                    </div>

                    <div class="form-group">
                        <label for="role">Role <span class="required">*</span></label>
                        <select name="role" id="role" required>
                            <option value="user" selected>user</option>
                            <option value="admin">admin</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href = '../'">Annuler</button>
                    <button
                        id="add-user-btn"
                        type="button"
                        class="btn btn-primary"
                        data-action="form-submit"
                        data-form="#user-form"
                        data-endpoint="../api/create_entity.php"
                        data-entity="user"
                        data-reset-form="true">
                        Ajouter l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script type="module" src="../js/main.js"></script>
</body>

</html>
