<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role_id'];
header("Location: dashboard.php");
exit();
} else {
echo "Email ou mot de passe incorrect";
}
}
?>

<form method="POST">
Email : <input type="email" name="email" required><br><br>
Mot de passe : <input type="password" name="password" required><br><br>
<button type="submit">Connexion</button>
</form>
