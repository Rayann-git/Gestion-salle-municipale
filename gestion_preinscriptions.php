<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();
}

$stmt = $pdo->query("SELECT * FROM reservations WHERE statut='en_attente'");
$preinscriptions = $stmt->fetchAll();
?>

<h2>Pré-inscriptions</h2>

<table border="1">

<tr>
<th>ID</th>
<th>User</th>
<th>Date début</th>
<th>Date fin</th>
<th>Action</th>
</tr>

<?php foreach($preinscriptions as $p): ?>

<tr>
<td><?= $p['id'] ?></td>
<td><?= $p['user_id'] ?></td>
<td><?= $p['date_debut'] ?></td>
<td><?= $p['date_fin'] ?></td>
<td>
<a href="valider.php?id=<?= $p['id'] ?>">Valider</a>
<a href="refuser.php?id=<?= $p['id'] ?>">Refuser</a>
</td>
</tr>

<?php endforeach; ?>

</table>

<a href="dashboard.php">Retour</a>
