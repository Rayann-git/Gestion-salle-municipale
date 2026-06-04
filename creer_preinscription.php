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
    $user_id = $_POST['user_id'];

    // Vérifier si une pré-inscription existe déjà
    $check = $pdo->prepare("SELECT id FROM preinscriptions WHERE user_id = ? AND status = 'en attente'");
    $check->execute([$user_id]);
    if ($check->fetch()) {
        $erreur = "Cet utilisateur a déjà une pré-inscription en attente !";
    } else {
        $stmt = $pdo->prepare("INSERT INTO preinscriptions (user_id, status, date_demande) VALUES (?, 'en attente', NOW())");
        $stmt->execute([$user_id]);
        $success = "Pré-inscription créée avec succès !";
    }
}

$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une pré-inscription - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width: 500px;">
    <h2>Créer une pré-inscription</h2>

    <?php if ($success): ?>
        <p style="color: green; background: #d4edda; padding: 10px; border-radius: 4px;">✅ <?= $success ?></p>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <p style="color: red; background: #f8d7da; padding: 10px; border-radius: 4px;">❌ <?= $erreur ?></p>
    <?php endif; ?>

    <form method="POST">
        Utilisateur :
        <select name="user_id" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:4px;">
            <?php foreach($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= $user['nom'] ?> <?= $user['prenom'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit">Créer</button>
    </form>

    <a class="retour" href="gestion_preincriptions.php">← Retour</a>
</div>
</body>
</html>