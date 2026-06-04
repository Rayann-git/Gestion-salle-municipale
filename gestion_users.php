<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $badge_uid = $_POST['badge_uid'];
    $stmt = $pdo->prepare("UPDATE users SET badge_uid=? WHERE id=?");
    $stmt->execute([$badge_uid, $id]);
    $success = "Badge mis à jour avec succès !";
}

$stmt = $pdo->query("SELECT users.*, roles.nom as nom_role 
                     FROM users 
                     JOIN roles ON users.role_id = roles.id");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion utilisateurs - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Liste des utilisateurs</h2>
    <a class="btn" href="creer_user.php" style="margin-bottom: 20px; display: inline-block;">+ Créer un utilisateur</a>

    <?php if (isset($success)): ?>
        <p style="color: green; background: #d4edda; padding: 10px; border-radius: 4px;">
            ✅ <?= $success ?>
        </p>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Badge UID</th>
            <th>Action</th>
        </tr>
        <?php foreach($users as $user): ?>
        <tr>
           <td><?= clean($user['id']) ?></td>
           <td><?= clean($user['nom']) ?></td>
           <td><?= clean($user['prenom']) ?></td>
           <td><?= clean($user['email']) ?></td>
           <td><?= clean($user['nom_role']) ?></td>
          <td><?= clean($user['badge_uid'] ?? 'Non assigné') ?></td>
           <td>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <input type="text" name="badge_uid" placeholder="UID du badge" value="<?= $user['badge_uid'] ?>" style="width: 150px; padding: 5px;">
                    <button type="submit">Assigner</button>
                </form>
           </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a class="retour" href="dashboard.php">← Retour</a>
</div>
</body>
</html>