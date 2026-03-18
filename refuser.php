<?php
require 'config.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("UPDATE reservations SET statut='refuse' WHERE id=?");
$stmt->execute([$id]);

header("Location: gestion_preinscriptions.php");
?>
