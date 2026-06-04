<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// Calcul de la semaine affichée
$offset = isset($_GET['semaine']) ? (int)$_GET['semaine'] : 0;
$debut_semaine = new DateTime();
$debut_semaine->modify('monday this week');
$debut_semaine->modify("$offset weeks");
$fin_semaine = clone $debut_semaine;
$fin_semaine->modify('+6 days');

// Récupérer les réservations validées de la semaine
$stmt = $pdo->prepare("
    SELECT reservations.*, users.nom, users.prenom
    FROM reservations
    JOIN users ON reservations.user_id = users.id
    WHERE reservations.statut = 'validé'
    AND reservations.date_debut <= ?
    AND reservations.date_fin >= ?
    ORDER BY reservations.heure_debut ASC
");
$stmt->execute([$fin_semaine->format('Y-m-d'), $debut_semaine->format('Y-m-d')]);
$reservations = $stmt->fetchAll();

// Indexer les réservations par jour
$resa_par_jour = [];
foreach ($reservations as $r) {
    $cur = new DateTime($r['date_debut']);
    $end = new DateTime($r['date_fin']);
    while ($cur <= $end) {
        $key = $cur->format('Y-m-d');
        $resa_par_jour[$key][] = $r;
        $cur->modify('+1 day');
    }
}

$jours_fr = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Calendrier - Intranet</title>
<link rel="stylesheet" href="style.css">
<style>
.cal-nav {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
}
.cal-nav a {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    color: white;
    padding: 8px 18px;
    border-radius: 980px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}
.cal-nav a:hover { background: rgba(255,255,255,0.28); }
.cal-nav .periode {
    font-size: 16px;
    font-weight: 600;
    color: white;
    opacity: 0.9;
}
.cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
}
.cal-day {
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 14px;
    padding: 14px 10px;
    min-height: 160px;
}
.cal-day.today {
    border-color: rgba(255,255,255,0.5);
    background: rgba(255,255,255,0.13);
}
.cal-day-header {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: rgba(255,255,255,0.5);
    margin-bottom: 4px;
}
.cal-day-num {
    font-size: 22px;
    font-weight: 700;
    color: white;
    margin-bottom: 10px;
}
.cal-day.today .cal-day-num {
    background: white;
    color: #302b63;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    margin-bottom: 10px;
}
.resa-event {
    background: rgba(100, 180, 255, 0.25);
    border: 1px solid rgba(100, 180, 255, 0.4);
    border-radius: 8px;
    padding: 7px 9px;
    margin-bottom: 6px;
    font-size: 12px;
    color: white;
}
.resa-event .resa-nom {
    font-weight: 600;
    margin-bottom: 2px;
}
.resa-event .resa-type {
    opacity: 0.8;
    margin-bottom: 2px;
}
.resa-event .resa-heures {
    opacity: 0.65;
    font-size: 11px;
}
.vide {
    color: rgba(255,255,255,0.2);
    font-size: 12px;
    font-style: italic;
    margin-top: 8px;
}
</style>
</head>
<body>
<div class="container">
    <h2>📅 Calendrier des réservations validées</h2>

    <div class="cal-nav">
        <a href="?semaine=<?= $offset - 1 ?>">← Semaine précédente</a>
        <span class="periode">
            Semaine du <?= $debut_semaine->format('d/m/Y') ?> au <?= $fin_semaine->format('d/m/Y') ?>
        </span>
        <a href="?semaine=<?= $offset + 1 ?>">Semaine suivante →</a>
        <?php if ($offset != 0): ?>
        <a href="?semaine=0">Aujourd'hui</a>
        <?php endif; ?>
    </div>

    <div class="cal-grid">
        <?php
        $today = (new DateTime())->format('Y-m-d');
        for ($i = 0; $i < 7; $i++):
            $jour = clone $debut_semaine;
            $jour->modify("+$i days");
            $key = $jour->format('Y-m-d');
            $is_today = ($key === $today);
        ?>
        <div class="cal-day <?= $is_today ? 'today' : '' ?>">
            <div class="cal-day-header"><?= $jours_fr[$i] ?></div>
            <div class="cal-day-num"><?= $jour->format('d') ?></div>

            <?php if (!empty($resa_par_jour[$key])): ?>
                <?php foreach ($resa_par_jour[$key] as $r): ?>
                <div class="resa-event">
                    <div class="resa-nom">👤 <?= clean($r['nom']) ?> <?= clean($r['prenom']) ?></div>
                    <div class="resa-type">🎭 <?= clean($r['type_evenement'] ?? 'Non renseigné') ?></div>
                    <div class="resa-heures">🕐 <?= clean($r['heure_debut']) ?> – <?= clean($r['heure_fin']) ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="vide">Aucune réservation</p>
            <?php endif; ?>
        </div>
        <?php endfor; ?>
    </div>

    <a class="retour" href="dashboard.php">← Retour</a>
</div>
</body>
</html>
```