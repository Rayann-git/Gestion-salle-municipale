<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role_id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $erreur = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width: 400px;">
    <h2>Connexion</h2>

    <?php if (isset($erreur)): ?>
        <p style="color: red;"><?= $erreur ?></p>
    <?php endif; ?>

    <form method="POST">
        Email : <input type="email" name="email" required><br>
        Mot de passe : <input type="password" name="password" required><br>
        <button type="submit">Connexion</button>
    </form>
</div>
</body>
</html>