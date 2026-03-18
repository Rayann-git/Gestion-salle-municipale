<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();
}

$stmt = $pdo->query("SELECT * FROM reservations");
$reservations = $stmt->fetchAll();
?>

<h2>Liste des réservations</h2>

<table border="1">

<tr>
<th>ID</th>
<th>User</th>
<th>Date début</th>
<th>Date fin</th>
<th>Statut</th>
</tr>

<?php foreach($reservations as $r): ?>

<tr>
<td><?= $r['id'] ?></td>
<td><?= $r['user_id'] ?></td>
<td><?= $r['date_debut'] ?></td>
<td><?= $r['date_fin'] ?></td>
<td><?= $r['statut'] ?></td>
</tr>

<?php endforeach; ?>

</table>

<br>
<a href="dashboard.php">Retour</a>
