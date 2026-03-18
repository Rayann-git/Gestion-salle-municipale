<?php
require 'config.php';

$nom = $_POST['nom'];
$capacite = $_POST['capacite'];
$tarif = $_POST['tarif'];
$horaires = $_POST['horaires'];

$stmt = $pdo->prepare("UPDATE salle SET nom=?, capacite=?, tarif=?, horaires=? WHERE id=1");
$stmt->execute([$nom,$capacite,$tarif,$horaires]);

header("Location: gestion_salle.php");
?>
