<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Intranet - Mairie d'Aubers</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .index-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }
        .logo {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .subtitle {
            color: rgba(255,255,255,0.7);
            font-size: 16px;
            margin-bottom: 40px;
            font-weight: 400;
        }
        .btn-login {
            padding: 16px 40px;
            font-size: 17px;
            border-radius: 980px;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            backdrop-filter: blur(10px);
        }
        .btn-login:hover {
            background: rgba(255,255,255,0.35);
            transform: scale(1.03);
        }
        .version {
            position: fixed;
            bottom: 20px;
            color: rgba(255,255,255,0.3);
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="index-container">
    <div class="logo">🏛️</div>
    <h1 style="font-size: 36px; color: white; margin-bottom: 10px;">Mairie d'Aubers</h1>
    <p class="subtitle">Intranet — Gestion de la salle municipale</p>
    <a href="login.php" class="btn-login">Se connecter</a>
</div>
<p class="version">Espace culturel des étangs © 2026</p>
</body>
</html>