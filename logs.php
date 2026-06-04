<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT logs.*, users.nom, users.prenom 
                     FROM logs 
                     JOIN users ON logs.user_id = users.id 
                     ORDER BY logs.date_action DESC");
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Logs - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Historique des actions</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Utilisateur</th>
            <th>Action</th>
            <th>Date</th>
        </tr>
        <?php foreach($logs as $log): ?>
        <tr>
            <td><?= clean($log['id']) ?></td>
            <td><?= clean($log['nom']) ?> <?= clean($log['prenom']) ?></td>
            <td><?= clean($log['action']) ?></td>
            <td><?= date('d/m/Y H:i:s', strtotime($log['date_action'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a class="retour" href="dashboard.php">← Retour</a>
</div>
</body>
</html>