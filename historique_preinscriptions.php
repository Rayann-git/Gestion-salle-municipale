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
                     WHERE preinscriptions.status != 'en attente'
                     ORDER BY preinscriptions.date_demande DESC");
$preinscriptions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique pré-inscriptions - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Historique des pré-inscriptions</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Date demande</th>
            <th>Statut</th>
        </tr>
        <?php foreach($preinscriptions as $p): ?>
        <tr>
            <td><?= clean($p['id']) ?></td>
            <td><?= clean($p['nom']) ?></td>
            <td><?= clean($p['prenom']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($p['date_demande'])) ?></td>
            <td style="color: <?= $p['status'] == 'validé' ? 'green' : 'red' ?>">
                <?= clean($p['status']) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a class="retour" href="gestion_preincriptions.php">← Retour</a>
</div>
</body>
</html>