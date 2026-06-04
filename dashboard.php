<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nb_reservations = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'en attente'")->fetchColumn();
$nb_preinscriptions = $pdo->query("SELECT COUNT(*) FROM preinscriptions WHERE status = 'en attente'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Intranet - Gestion salle municipale</h1>

    <div class="cards">
        <div class="card">
            <h3><?= $nb_reservations ?></h3>
            <p>Réservation(s) en attente</p>
        </div>
        <div class="card">
            <h3><?= $nb_preinscriptions ?></h3>
            <p>Pré-inscription(s) en attente</p>
        </div>
    </div>

    <ul>
        <li><a href="calendrier.php">📅 Calendrier des réservations</a></li>
        <li><a href="gestion_users.php">👤 Gérer les utilisateurs</a></li>
        <li>
            <a href="gestion_preincriptions.php">📋 Voir les préinscriptions
                <?php if ($nb_preinscriptions > 0): ?>
                    <span class="badge"><?= $nb_preinscriptions ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="gestion_reservations.php">📅 Voir les réservations
                <?php if ($nb_reservations > 0): ?>
                    <span class="badge"><?= $nb_reservations ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li><a href="gestion_salle.php">🏛️ Modifier les informations de la salle</a></li>
        <li><a href="backup.php">💾 Sauvegarder la base de données</a></li>
        <li><a href="logs.php">🕐 Historique des actions</a></li>
        <li><a href="logout.php">🚪 Déconnexion</a></li>
    </ul>
</div>
</body>
</html>