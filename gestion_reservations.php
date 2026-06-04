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
                     WHERE reservations.statut = 'en attente'");
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservations - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Liste des réservations en attente</h2>
    <a class="btn" href="creer_reservation.php" style="margin-bottom: 20px; display: inline-block;">+ Créer une réservation</a>
    <a class="btn" href="historique_reservations.php" style="margin-bottom: 20px; margin-left: 10px; display: inline-block; background-color: #7f8c8d;">📋 Historique</a>

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
            <th>Action</th>
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
           <td><?= clean($r['statut']) ?></td>
           <td>
                <a class="btn btn-success" href="valider.php?id=<?= $r['id'] ?>&type=reservation">Valider</a>
                <a class="btn btn-danger" href="refuser.php?id=<?= $r['id'] ?>&type=reservation">Refuser</a>
           </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a class="retour" href="dashboard.php">← Retour</a>
</div>
</body>
</html>