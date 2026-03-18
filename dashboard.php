<?php
session_start();
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();
}
?>

<h1>Intranet - Gestion salle municipale</h1>

<ul>
<li><a href="gestion_users.php">Gérer les utilisateurs</a></li>
<li><a href="gestion_preinscriptions.php">Voir les préinscriptions</a></li>
<li><a href="gestion_reservations.php">Voir les réservations</a></li>
<li><a href="gestion_salle.php">Modifier les informations de la salle</a></li>
<li><a href="logout.php">Déconnexion</a></li>
</ul>
