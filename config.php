<?php
date_default_timezone_set('Europe/Paris');
define('DB_HOST', 'localhost');
define('DB_NAME', 'salle_municipale');
define('DB_USER', 'adminsql');
define('DB_PASS', 'Admin1234');


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERREUR connexion : " . $e->getMessage());
}
// Fonction de sécurité anti-XSS
function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>
