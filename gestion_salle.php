<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();
}

$stmt = $pdo->query("SELECT * FROM salle LIMIT 1");
$salle = $stmt->fetch();
?>

<h2>Gestion de la salle</h2>

<form method="POST" action="update_salle.php">

Nom : <input type="text" name="nom" value="<?= $salle['nom'] ?>"><br><br>

Capacité : <input type="number" name="capacite" value="<?= $salle['capacite'] ?>"><br><br>

Tarif : <input type="number" step="0.01" name="tarif" value="<?= $salle['tarif'] ?>"><br><br>

Horaires : <input type="text" name="horaires" value="<?= $salle['horaires'] ?>"><br><br>

<button type="submit">Modifier</button>

</form>

<br>
<a href="dashboard.php">Retour</a>
