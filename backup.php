<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$dbname = "salle_municipale";
$username = "adminsql";
$password = "Admin1234";

$date = date('Y-m-d_H-i-s');
$filename = "backup_" . $date . ".sql";
$filepath = "/var/www/html/intranet/backups/" . $filename;

// Créer le dossier backups s'il n'existe pas
if (!file_exists('/var/www/html/intranet/backups/')) {
    mkdir('/var/www/html/intranet/backups/', 0755, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $command = "mysqldump -h $host -u $username -p'$password' $dbname > $filepath";
    exec($command, $output, $return);

    if ($return === 0) {
    $success = "Sauvegarde effectuée avec succès : $filename";
    
    // Envoi vers le serveur backup
    $scp_command = "scp -o ConnectTimeout=5 $filepath sauvegardes@192.168.20.202:/home/sauvegardes/";
    exec($scp_command, $scp_output, $scp_return);
    if ($scp_return === 0) {
        $success .= " — ✅ Envoyée sur le serveur backup";
    } else {
        $success .= " — ⚠️ Échec de l'envoi sur le serveur backup";
    }
}
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sauvegarde - Intranet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width: 500px;">
    <h2>Sauvegarde de la base de données</h2>

    <?php if (isset($success)): ?>
        <p style="color: green; background: #d4edda; padding: 10px; border-radius: 4px;">✅ <?= $success ?></p>
    <?php endif; ?>

    <?php if (isset($erreur)): ?>
        <p style="color: red; background: #f8d7da; padding: 10px; border-radius: 4px;">❌ <?= $erreur ?></p>
    <?php endif; ?>

    <form method="POST">
        <button type="submit">Lancer la sauvegarde</button>
    </form>

    <a class="retour" href="dashboard.php">← Retour</a>
</div>
</body>
</html>