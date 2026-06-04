<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_POST['id'];
$nom = $_POST['nom'];
$capacite = $_POST['capacite'];
$tarif = $_POST['tarif'];
$horaires = $_POST['horraires'];

$stmt = $pdo->prepare("UPDATE salle SET nom=?, `capacité`=?, tarif=?, horraires=? WHERE id=?");
$stmt->execute([$nom, $capacite, $tarif, $horaires, $id]);

header("Location: gestion_salle.php?success=1");
exit();
?>