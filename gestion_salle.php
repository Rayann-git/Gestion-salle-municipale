<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM salle LIMIT 1");
$salle = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion salle - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Gestion de la salle</h2>
    <?php if (isset($_GET['success'])): ?>
    <p style="color: green; background: #d4edda; padding: 10px; border-radius: 4px;">
        ✅ Les informations ont bien été mises à jour !
    </p>
<?php endif; ?>

    <form method="POST" action="update_salle.php">
        <input type="hidden" name="id" value="<?= $salle['id'] ?>">

        Nom : <input type="text" name="nom" value="<?= $salle['nom'] ?>"><br>

        Capacité : <input type="number" name="capacite" value="<?= $salle['capacité'] ?>"><br>

        Tarif : <input type="number" step="0.01" name="tarif" value="<?= $salle['tarif'] ?>"><br>

        Horaires : <input type="text" name="horraires" value="<?= $salle['horraires'] ?>"><br>

        <button type="submit">Modifier</button>
    </form>

    <a class="retour" href="dashboard.php">← Retour</a>
</div>
</body>
</html>