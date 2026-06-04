<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$erreur = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $badge_uid = $_POST['badge_uid'];
    $type_evenement = $_POST['type_evenement'];

    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, date_debut, date_fin, statut, date_demande, salle_id, heure_debut, heure_fin, type_evenement) 
                           VALUES (?, ?, ?, 'en attente', NOW(), 1, ?, ?, ?)");
    $stmt->execute([$user_id, $date_debut, $date_fin, $heure_debut, $heure_fin, $type_evenement]);

    if (!empty($badge_uid)) {
        $badge = $pdo->prepare("UPDATE users SET badge_uid=? WHERE id=?");
        $badge->execute([$badge_uid, $user_id]);
    }
    $success = "Réservation créée avec succès !";
}

$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une réservation - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width: 500px;">
    <h2>Créer une réservation</h2>

    <?php if ($success): ?>
        <p class="alert-success">✅ <?= $success ?></p>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <p class="alert-danger">❌ <?= $erreur ?></p>
    <?php endif; ?>

    <form method="POST">
        Utilisateur :
        <select name="user_id">
            <?php foreach($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= $user['nom'] ?> <?= $user['prenom'] ?></option>
            <?php endforeach; ?>
        </select><br>

        Date début : <input type="date" name="date_debut" required><br>
        Date fin : <input type="date" name="date_fin" required><br>
        Heure début : <input type="time" name="heure_debut" required><br>
        Heure fin : <input type="time" name="heure_fin" required><br>

        Type d'événement :
<select name="type_evenement">
    <option value="Mariage">💍 Mariage</option>
    <option value="Anniversaire">🎂 Anniversaire</option>
    <option value="Réception">🥂 Réception</option>
    <option value="Assemblée générale">📋 Assemblée générale</option>
    <option value="Événement culturel">🎭 Événement culturel</option>
    <option value="Loto">🎰 Loto</option>
    <option value="Kermesse">🎡 Kermesse</option>
    <option value="Repas de quartier">🍽️ Repas de quartier</option>
    <option value="Autre">📌 Autre</option>
</select><br>

        Badge UID : <input type="text" name="badge_uid" placeholder="Ex: A3:F2:B1:C4"><br>

        <button type="submit">Créer</button>
    </form>

    <a class="retour" href="gestion_reservations.php">← Retour</a>
</div>
</body>
</html>