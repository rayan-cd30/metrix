<?php
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Database.php';

if (Auth::user()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim(strtolower($_POST['email'])) : '';
    $password = isset($_POST['password']) ? (string)$_POST['password'] : '';

    if ($email === '' || $password === '') {
        $error = 'Email et mot de passe obligatoires.';
    } else {
        try {
            $database = new Database();
            $user = $database->getUserByEmail($email);

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $error = 'Identifiants invalides.';
            } else {
                Auth::login($user);
                header('Location: index.php');
                exit;
            }
        } catch (Throwable $e) {
            $error = 'Erreur serveur pendant la connexion.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Metrix</title>
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <form class="card" method="post" action="login.php">
        <h1>Connexion</h1>
        <p>Connecte-toi pour accéder à Metrix.</p>

        <?php if ($error !== ''): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="email" value="<?= htmlspecialchars($email ?? '') ?>" required />

        <label for="password">Mot de passe</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required />

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
