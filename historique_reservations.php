<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT reservations.*, users.nom, users.prenom 
                     FROM reservations 
                     JOIN users ON reservations.user_id = users.id
                     WHERE reservations.statut != 'en attente'
                     ORDER BY reservations.date_demande DESC");
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique réservations - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Historique des réservations</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Date début</th>
            <th>Date fin</th>
            <th>Heure début</th>
            <th>Heure fin</th>
            <th>Statut</th>
        </tr>
        <?php foreach($reservations as $r): ?>
        <tr>
           <td><?= clean($r['id']) ?></td>
           <td><?= clean($r['nom']) ?></td>
           <td><?= clean($r['prenom']) ?></td>
           <td><?= date('d/m/Y', strtotime($r['date_debut'])) ?></td>
           <td><?= date('d/m/Y', strtotime($r['date_fin'])) ?></td>
           <td><?= clean($r['heure_debut']) ?></td>
           <td><?= clean($r['heure_fin']) ?></td>
           <td style="color: <?= $r['statut'] == 'validé' ? 'green' : 'red' ?>">
               <?= clean($r['statut']) ?>
           </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a class="retour" href="gestion_reservations.php">← Retour</a>
</div>
</body>
</html>