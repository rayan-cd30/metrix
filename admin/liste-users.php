<?php
require_once '../classes/Auth.php';
require_once '../classes/Database.php';

$user = Auth::requirePageRole('../login.php', '../index.php', ['admin']);
$database = new Database();
$users = $database->getUsers();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste Utilisateurs | Metrix</title>
    <link rel="stylesheet" href="../styles/admin.css">
</head>

<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Liste des utilisateurs</h1>
            <p class="subtitle">Consulte et supprime les comptes existants</p>
            <p class="subtitle" style="margin-top: 8px;">
                Connecte: <?= htmlspecialchars($user['email']) ?> |
                <a href="../logout.php">Deconnexion</a>
            </p>
        </header>

        <?php include __DIR__ . '/components/sidebar.php'; ?>

        <div class="form-card">
            <div class="users-table-wrap">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Creation</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $account): ?>
                            <?php $isCurrentUser = ((int)$account['id'] === (int)$user['id']); ?>
                            <tr data-user-row-id="<?= (int)$account['id'] ?>">
                                <td>#<?= (int)$account['id'] ?></td>
                                <td>
                                    <?= htmlspecialchars($account['email']) ?>
                                    <?php if ($isCurrentUser): ?>
                                        <span class="user-badge">vous</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($account['role']) ?></td>
                                <td><?= htmlspecialchars((string)$account['created_at']) ?></td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-danger user-delete-btn"
                                        data-user-id="<?= (int)$account['id'] ?>"
                                        data-user-email="<?= htmlspecialchars($account['email']) ?>"
                                        <?= $isCurrentUser ? 'disabled title="Impossible de supprimer votre propre compte"' : '' ?>>
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const buttons = document.querySelectorAll('.user-delete-btn');
            buttons.forEach((btn) => {
                if (btn.disabled) return;

                btn.addEventListener('click', async () => {
                    const userId = btn.dataset.userId;
                    const userEmail = btn.dataset.userEmail || '';

                    const confirmed = confirm(`Supprimer le compte ${userEmail} ?`);
                    if (!confirmed) return;

                    btn.disabled = true;
                    const oldText = btn.textContent;
                    btn.textContent = 'Suppression...';

                    try {
                        const formData = new FormData();
                        formData.append('user_id', userId);

                        const response = await fetch('../api/delete_user.php', {
                            method: 'POST',
                            body: formData
                        });

                        const result = await response.json();
                        if (!response.ok || !result.success) {
                            throw new Error(result.message || 'Suppression impossible');
                        }

                        const row = document.querySelector(`[data-user-row-id="${userId}"]`);
                        if (row) row.remove();
                    } catch (error) {
                        alert('Erreur: ' + (error.message || error));
                        btn.disabled = false;
                        btn.textContent = oldText;
                    }
                });
            });
        })();
    </script>
</body>

</html>
