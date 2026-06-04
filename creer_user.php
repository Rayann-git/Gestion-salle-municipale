<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$erreur = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];

    // Vérifier si l'email existe déjà
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        $erreur = "Cet email est déjà utilisé !";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password, role_id, date_creation) 
                               VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$nom, $prenom, $email, $password, $role_id]);
        $success = "Utilisateur créé avec succès !";
    }
}

$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un utilisateur - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width: 500px;">
    <h2>Créer un utilisateur</h2>

    <?php if ($success): ?>
        <p style="color: green; background: #d4edda; padding: 10px; border-radius: 4px;">✅ <?= $success ?></p>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <p style="color: red; background: #f8d7da; padding: 10px; border-radius: 4px;">❌ <?= $erreur ?></p>
    <?php endif; ?>

    <form method="POST">
        Nom : <input type="text" name="nom" required><br>
        Prénom : <input type="text" name="prenom" required><br>
        Email : <input type="email" name="email" required><br>
        Mot de passe : <input type="password" name="password" required><br>
        Rôle :
        <select name="role_id" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:4px;">
            <?php foreach($roles as $role): ?>
                <option value="<?= $role['id'] ?>"><?= $role['nom'] ?></option>
            <?php endforeach; ?>
        </select><br>
        <button type="submit">Créer</button>
    </form>

    <a class="retour" href="gestion_users.php">← Retour</a>
</div>
</body>
</html>