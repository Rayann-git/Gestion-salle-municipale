<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT preinscriptions.*, users.nom, users.prenom 
                     FROM preinscriptions 
                     JOIN users ON preinscriptions.user_id = users.id 
                     WHERE preinscriptions.status = 'en attente'");
$preinscriptions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Pré-inscriptions - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Pré-inscriptions en attente</h2>
    <a class="btn" href="historique_preinscriptions.php" style="margin-bottom: 20px; display: inline-block; background-color: #7f8c8d;">📋 Historique</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Date demande</th>
            <th>Action</th>
        </tr>
        <?php foreach($preinscriptions as $p): ?>
        <tr>
            <td><?= clean($p['id']) ?></td>
            <td><?= clean($p['nom']) ?></td>
            <td><?= clean($p['prenom']) ?></td>
            <td><?= clean($p['date_demande']) ?></td>
            <td>
                <a class="btn btn-success" href="valider.php?id=<?= $p['id'] ?>&type=preinscription">Valider</a>
                <a class="btn btn-danger" href="refuser.php?id=<?= $p['id'] ?>&type=preinscription">Refuser</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a class="retour" href="dashboard.php">← Retour</a>
</div>
</body>
</html>