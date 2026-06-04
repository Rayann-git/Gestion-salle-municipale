<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$type = $_GET['type'];
$user_id = $_SESSION['user_id'];

if ($type == 'preinscription') {
    $stmt = $pdo->prepare("UPDATE preinscriptions SET status='validé' WHERE id=?");
    $stmt->execute([$id]);
    // Enregistrer le log
    $log = $pdo->prepare("INSERT INTO logs (user_id, action, date_action) VALUES (?, ?, NOW())");
    $log->execute([$user_id, "Validation pré-inscription ID: $id"]);
    header("Location: gestion_preincriptions.php");
} else {
    $stmt = $pdo->prepare("UPDATE reservations SET statut='validé' WHERE id=?");
    $stmt->execute([$id]);
    // Enregistrer le log
    $log = $pdo->prepare("INSERT INTO logs (user_id, action, date_action) VALUES (?, ?, NOW())");
    $log->execute([$user_id, "Validation réservation ID: $id"]);
    header("Location: gestion_reservations.php");
}
exit();
?>