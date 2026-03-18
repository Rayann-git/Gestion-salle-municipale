<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();
}

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>

<h2>Liste des utilisateurs</h2>

<table border="1">
<tr>
<th>ID</th>
<th>Nom</th>
<th>Email</th>
<th>Role</th>
</tr>

<?php foreach($users as $user): ?>
<tr>
<td><?= $user['id'] ?></td>
<td><?= $user['nom'] ?></td>
<td><?= $user['email'] ?></td>
<td><?= $user['role_id'] ?></td>
</tr>
<?php endforeach; ?>

</table>

<br>
<a href="dashboard.php">Retour</a>
