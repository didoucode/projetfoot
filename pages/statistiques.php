<?php
require '../config/db.php'; // Connexion à la base de données

// Récupérer le classement par tournoi
$stmt = $pdo->prepare("
 SELECT 
    c.id, 
    c.points, 
    c.recentes, 
    e.nom AS equipe_nom, 
    e.id AS equipe_id, 
    t.nom AS tournoi_nom, 
    t.id AS tournoi_id,
    (SELECT COUNT(*) 
     FROM matchs m 
     JOIN resultats r ON m.id = r.match_id 
     WHERE (r.equipe1_id = e.id OR r.equipe2_id = e.id) AND m.tournoi_id = t.id) AS matchs_joues,
    (SELECT COUNT(*) 
     FROM matchs m 
     JOIN resultats r ON m.id = r.match_id 
     WHERE ((r.equipe1_id = e.id AND r.score_equipe1 > r.score_equipe2) OR (r.equipe2_id = e.id AND r.score_equipe2 > r.score_equipe1)) AND m.tournoi_id = t.id) AS victoires,
    (SELECT COUNT(*) 
     FROM matchs m 
     JOIN resultats r ON m.id = r.match_id 
     WHERE ((r.equipe1_id = e.id AND r.score_equipe1 = r.score_equipe2) OR (r.equipe2_id = e.id AND r.score_equipe2 = r.score_equipe1)) AND m.tournoi_id = t.id) AS nuls,
    (SELECT COUNT(*) 
     FROM matchs m 
     JOIN resultats r ON m.id = r.match_id 
     WHERE ((r.equipe1_id = e.id AND r.score_equipe1 < r.score_equipe2) OR (r.equipe2_id = e.id AND r.score_equipe2 < r.score_equipe1)) AND m.tournoi_id = t.id) AS defaites,  -- Correction ici
    (SELECT SUM(CASE WHEN r.equipe1_id = e.id THEN r.score_equipe1 ELSE r.score_equipe2 END) 
     FROM matchs m 
     JOIN resultats r ON m.id = r.match_id 
     WHERE (r.equipe1_id = e.id OR r.equipe2_id = e.id) AND m.tournoi_id = t.id) AS buts_marques,
    (SELECT SUM(CASE WHEN r.equipe1_id = e.id THEN r.score_equipe2 ELSE r.score_equipe1 END) 
     FROM matchs m 
     JOIN resultats r ON m.id = r.match_id 
     WHERE (r.equipe1_id = e.id OR r.equipe2_id = e.id) AND m.tournoi_id = t.id) AS buts_encaisses
FROM classement c
JOIN equipe e ON c.equipe_id = e.id
JOIN tournois t ON t.id IN (1, 2) /* Botola Pro (id=1) et Coupe du Trône (id=2) */
WHERE (e.id, t.id) IN (
    SELECT DISTINCT 
        CASE WHEN r.equipe1_id = e.id THEN r.equipe1_id ELSE r.equipe2_id END,
        m.tournoi_id
    FROM equipe e
    JOIN resultats r ON r.equipe1_id = e.id OR r.equipe2_id = e.id
    JOIN matchs m ON r.match_id = m.id
    WHERE m.tournoi_id IN (1, 2)
)
GROUP BY t.id, e.id
ORDER BY t.id, c.points DESC, (buts_marques - buts_encaisses) DESC;
");
$stmt->execute();

$classements = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $classements[$row['tournoi_nom']][] = [
        'equipe' => $row['equipe_nom'],
        'equipe_id' => $row['equipe_id'],
        'points' => $row['points'],
        'forme' => $row['recentes'],
        'matchs_joues' => $row['matchs_joues'] ?? 0,
        'victoires' => $row['victoires'] ?? 0,
        'nuls' => $row['nuls'] ?? 0,
        'defaites' => $row['defaites'] ?? 0,
        'buts_marques' => $row['buts_marques'] ?? 0,
        'buts_encaisses' => $row['buts_encaisses'] ?? 0,
        'difference' => ($row['buts_marques'] ?? 0) - ($row['buts_encaisses'] ?? 0)
    ];
}

// Récupérer les performances des joueurs par tournoi
$stmt = $pdo->prepare("

SELECT 
    j.id AS joueur_id, 
    j.nom AS joueur_nom, 
    e.nom AS equipe_nom,
    e.id AS equipe_id,
    t.nom AS tournoi_nom,
    t.id AS tournoi_id,
    COUNT(DISTINCT m.id) AS matchs_joues,
    IFNULL(SUM(s.buts), 0) AS buts,
    IFNULL(SUM(s.passes_decisives), 0) AS passes,
    IFNULL(SUM(s.cartons_jaunes), 0) AS cartons_jaunes,
    IFNULL(SUM(s.cartons_rouges), 0) AS cartons_rouges,
    IFNULL(SUM(s.minutes_jouees), 0) AS minutes_jouees
FROM joueurs j
JOIN equipe e ON j.equipes = e.id
JOIN statistiques_joueurs s ON j.id = s.joueur_id
JOIN matchs m ON s.match_id = m.id
JOIN tournois t ON m.tournoi_id = t.id
WHERE t.id IN (1, 2) /* Botola Pro (id=1) et Coupe du Trône (id=2) */
GROUP BY j.id, t.id
ORDER BY t.id, buts DESC, passes DESC;
");

$stmt->execute();

$stats_joueurs = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $stats_joueurs[$row['tournoi_nom']][] = [
        'joueur' => $row['joueur_nom'],
        'joueur_id' => $row['joueur_id'],
        'equipe' => $row['equipe_nom'],
        'equipe_id' => $row['equipe_id'],
        'matchs' => $row['matchs_joues'],
        'buts' => $row['buts'],
        'passes' => $row['passes'],
        'cartons_jaunes' => $row['cartons_jaunes'],
        'cartons_rouges' => $row['cartons_rouges'],
        'minutes_jouees' => $row['minutes_jouees'],
        'buts_par_90min' => $row['buts']
    ];
}

// Récupérer les confrontations directes entre équipes
$stmt = $pdo->prepare("
SELECT 
    m.id AS match_id,
    m.date,
    t.id AS tournoi_id,
    t.nom AS tournoi_nom,
    e1.id AS equipe1_id,
    e1.nom AS equipe1_nom,
    e2.id AS equipe2_id,
    e2.nom AS equipe2_nom,
    r.score_equipe1,
    r.score_equipe2
FROM matchs m
JOIN resultats r ON m.id = r.match_id
JOIN equipe e1 ON r.equipe1_id = e1.id
JOIN equipe e2 ON r.equipe2_id = e2.id
JOIN tournois t ON m.tournoi_id = t.id
WHERE t.id IN (1, 2) /* Botola Pro (id=1) et Coupe du Trône (id=2) */
ORDER BY m.date DESC
LIMIT 10
");
$stmt->execute();

$derniers_matchs = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $derniers_matchs[] = [
        'tournoi' => $row['tournoi_nom'],
        'tournoi_id' => $row['tournoi_id'],
        'date' => $row['date'],
        'equipe1' => $row['equipe1_nom'],
        'equipe1_id' => $row['equipe1_id'],
        'equipe2' => $row['equipe2_nom'],
        'equipe2_id' => $row['equipe2_id'],
        'score1' => $row['score_equipe1'],
        'score2' => $row['score_equipe2']
    ];
}

// Récupérer les statistiques générales par tournoi
$stmt = $pdo->prepare("
SELECT 
    t.id AS tournoi_id,
    t.nom AS tournoi_nom,
    COUNT(DISTINCT m.id) AS total_matchs,
    SUM(r.score_equipe1 + r.score_equipe2) AS total_buts,
    ROUND(AVG(r.score_equipe1 + r.score_equipe2), 2) AS moyenne_buts_par_match,
    COUNT(DISTINCT e.id) AS total_equipes,
    (SELECT COUNT(DISTINCT j.id) 
     FROM joueurs j 
     JOIN statistiques_joueurs s ON j.id = s.joueur_id 
     JOIN matchs m2 ON s.match_id = m2.id 
     WHERE m2.tournoi_id = t.id) AS total_joueurs
FROM tournois t
LEFT JOIN matchs m ON t.id = m.tournoi_id
LEFT JOIN resultats r ON m.id = r.match_id
LEFT JOIN equipe e ON r.equipe1_id = e.id OR r.equipe2_id = e.id
WHERE t.id IN (1, 2) /* Botola Pro (id=1) et Coupe du Trône (id=2) */
GROUP BY t.id
ORDER BY t.id
");
$stmt->execute();

$stats_tournois = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $stats_tournois[$row['tournoi_nom']] = [
        'tournoi_id' => $row['tournoi_id'],
        'total_matchs' => $row['total_matchs'],
        'total_buts' => $row['total_buts'],
        'moyenne_buts' => $row['moyenne_buts_par_match'],
        'total_equipes' => $row['total_equipes'],
        'total_joueurs' => $row['total_joueurs']
    ];
}

// Fonction pour afficher les indicateurs de forme
function afficheFormation($forme) {
    if (empty($forme)) return '<span class="text-muted">Pas de données</span>';
    
    $resultats = str_split($forme);
    $html = '';
    
    foreach ($resultats as $resultat) {
        switch ($resultat) {
            case 'V':
                $html .= '<span class="badge badge-success mr-1">V</span>';
                break;
            case 'N':
                $html .= '<span class="badge badge-warning mr-1">N</span>';
                break;
            case 'D':
                $html .= '<span class="badge badge-danger mr-1">D</span>';
                break;
            default:
                $html .= '<span class="badge badge-secondary mr-1">-</span>';
        }
    }
    
    return $html;
}

// Fonction pour obtenir la couleur des équipes (pour les graphiques)
function getTeamColor($index) {
    $colors = [
        'rgba(40, 167, 69, 0.7)',   // Vert
        'rgba(220, 53, 69, 0.7)',   // Rouge
        'rgba(255, 193, 7, 0.7)',   // Jaune
        'rgba(23, 162, 184, 0.7)',  // Cyan
        'rgba(111, 66, 193, 0.7)',  // Violet
        'rgba(255, 102, 0, 0.7)',   // Orange
        'rgba(0, 123, 255, 0.7)',   // Bleu
        'rgba(108, 117, 125, 0.7)', // Gris
        'rgba(40, 167, 120, 0.7)',  // Vert clair
        'rgba(220, 53, 120, 0.7)',  // Rose
        'rgba(255, 193, 100, 0.7)', // Jaune clair
        'rgba(23, 102, 184, 0.7)',  // Bleu foncé
        'rgba(150, 66, 193, 0.7)',  // Violet clair
        'rgba(200, 102, 0, 0.7)',   // Orange foncé
        'rgba(0, 183, 255, 0.7)',   // Bleu clair
        'rgba(160, 117, 125, 0.7)'  // Gris foncé
    ];
    
    return $colors[$index % count($colors)];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Tournois Marocains</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    :root {
        /* Palette de couleurs professionnelle */
        --couleur-principale:  #006400;     /* Bleu marine profond */
        --couleur-secondaire: #34495E;     /* Bleu ardoise */
        --couleur-accent: #BBF000;         /* Bleu azure */
        --couleur-fond: #ECF0F1;           /* Gris très clair */
        --couleur-texte: #2C3E50;          /* Texte sombre */
        --couleur-carte: #FFFFFF;          /* Blanc pour les cartes */
        --couleur-success: #27AE60;        /* Vert pour les succès */
        --couleur-danger: #E74C3C;         /* Rouge pour les alertes */
        --couleur-neutre: #95A5A6;         /* Gris neutre */
    }

    body {
        background-color: var(--couleur-fond);
        color: var(--couleur-texte);
        font-family: 'Roboto', 'Segoe UI', sans-serif;
    }

    .main-container {
        background-color: transparent;
        padding: 30px;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border: none;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px);
    }

    .card-header {
        background-color: var(--couleur-principale);
        color: white;
        border-bottom: none;
        padding: 15px;
        display: flex;
        align-items: center;
        border-radius: 10px 10px 0 0;
    }

    .card-header i {
        margin-right: 10px;
        color: var(--couleur-accent);
    }

    .nav-pills .nav-link {
        color: var(--couleur-secondaire);
        margin: 0 10px;
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link.active {
        background-color: var(--couleur-accent);
        color: white;
    }

    .table {
        border-radius: 10px;
        overflow: hidden;
    }

    .table thead {
        background-color: var(--couleur-secondaire);
        color: white;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(52, 73, 94, 0.05);
    }

    .stats-header {
        background-color: white;
        border: 1px solid var(--couleur-neutre);
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stats-header:hover {
        border-color: var(--couleur-accent);
        box-shadow: 0 4px 10px rgba(52, 152, 219, 0.1);
    }

    .stats-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--couleur-accent);
    }

    .stats-label {
        color: var(--couleur-secondaire);
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.8rem;
    }

    .chart-container {
        background-color: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .badge-success {
        background-color: var(--couleur-success);
    }

    .badge-danger {
        background-color: var(--couleur-danger);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .main-container {
            padding: 15px;
        }

        .card {
            margin-bottom: 15px;
        }
    }

    /* Specific Data Highlighting */
    .text-performance-positive {
        color: var(--couleur-success);
    }

    .text-performance-negative {
        color: var(--couleur-danger);
    }

    .team-form-indicator {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        margin-right: 5px;
    }

    .team-form-win { background-color: var(--couleur-success); }
    .team-form-draw { background-color: var(--couleur-neutre); }
    .team-form-loss { background-color: var(--couleur-danger); }
</style>
</head>
<body>

    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content">
   
    <?php include '../includes/header.php'; ?>

<div class="container main-container">
    <h1 class="text-center my-4">
        <i class="fas fa-chart-line mr-2"></i>
        Statistiques des Tournois Marocains
    </h1>
    
    <ul class="nav nav-pills mb-4 justify-content-center" id="tournoisTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="botola-tab" data-toggle="pill" href="#botola" role="tab" aria-selected="true">Botola Pro</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="coupe-tab" data-toggle="pill" href="#coupe" role="tab" aria-selected="false">Coupe du Trône</a>
        </li>
       
    </ul>
    
    <div class="tab-content" id="tournoisTabContent">
        <?php foreach (['Botola Pro', 'Coupe du Trône'] as $index => $tournoi): 
            $tournoi_id = strtolower(str_replace(' ', '', $tournoi));
            $tournoi_nav_id = ($tournoi == 'Botola Pro') ? 'botola' : 'coupe';
            $is_active = ($tournoi == 'Botola Pro') ? 'show active' : '';
        ?>
        
        <div class="tab-pane fade <?php echo $is_active; ?>" id="<?php echo $tournoi_nav_id; ?>" role="tabpanel">
            <div id="<?php echo $tournoi_id; ?>">
                <!-- Vue d'ensemble du tournoi -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-trophy mr-2"></i>Vue d'ensemble - <?php echo $tournoi; ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <?php if (isset($stats_tournois[$tournoi])): ?>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="stats-header">
                                        <div class="stats-value"><?php echo $stats_tournois[$tournoi]['total_equipes']; ?></div>
                                        <div class="stats-label">ÉQUIPES</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="stats-header">
                                        <div class="stats-value"><?php echo $stats_tournois[$tournoi]['total_matchs']; ?></div>
                                        <div class="stats-label">MATCHS JOUÉS</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="stats-header">
                                        <div class="stats-value"><?php echo $stats_tournois[$tournoi]['total_buts']; ?></div>
                                        <div class="stats-label">BUTS MARQUÉS</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="stats-header">
                                        <div class="stats-value"><?php echo $stats_tournois[$tournoi]['moyenne_buts']; ?></div>
                                        <div class="stats-label">BUTS PAR MATCH</div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="col-12">
                                    <p class="text-center text-muted">Aucune donnée disponible pour ce tournoi.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Classement et Statistiques -->
                <div class="row">
                    <!-- Classement -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0"><i class="fas fa-list-ol mr-2"></i>Classement des Équipes</h4>
                                <button class="btn btn-sm btn-outline-light" type="button" data-toggle="collapse" data-target="#collapseClassement<?php echo $index; ?>">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="collapse show" id="collapseClassement<?php echo $index; ?>">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Équipe</th>
                                                    <th>Pts</th>
                                                    <th>J</th>
                                                    <th>V</th>
                                                    <th>N</th>
                                                    <th>D</th>
                                                    <th>BP</th>
                                                    <th>BC</th>
                                                    <th>Diff</th>
                                                    <th>Forme</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (isset($classements[$tournoi])): ?>
                                                    <?php foreach ($classements[$tournoi] as $position => $equipe): ?>
                                                        <tr>
                                                            <td class="font-weight-bold"><?php echo $position + 1; ?></td>
                                                            <td><?php echo $equipe['equipe']; ?></td>
                                                            <td class="font-weight-bold"><?php echo $equipe['points']; ?></td>
                                                            <td><?php echo $equipe['matchs_joues']; ?></td>
                                                            <td><?php echo $equipe['victoires']; ?></td>
                                                            <td><?php echo $equipe['nuls']; ?></td>
                                                            <td><?php echo $equipe['defaites']; ?></td>
                                                            <td><?php echo $equipe['buts_marques']; ?></td>
                                                            <td><?php echo $equipe['buts_encaisses']; ?></td>
                                                            <td class="<?php echo ($equipe['difference'] >= 0) ? 'text-success' : 'text-danger'; ?>">
                                                                <?php echo ($equipe['difference'] >= 0) ? '+' . $equipe['difference'] : $equipe['difference']; ?>
                                                            </td>
                                                            <td><?php echo afficheFormation($equipe['forme']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="11" class="text-center">Aucune donnée disponible pour ce tournoi.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Graphique Points des Équipes -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Points des Équipes</h4>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="pointsChart<?php echo $index; ?>"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistiques Équipes -->
                    <div class="col-lg-4">
                        <!-- Meilleurs Buteurs -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="mb-0"><i class="fas fa-futbol mr-2"></i>Meilleurs Buteurs</h4>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Joueur</th>
                                                <th>Équipe</th>
                                                <th>Buts</th>
                                            </tr>
                                        </thead>
                                        <tbody>
    <?php if (!empty($stats_joueurs[$tournoi])): ?>
        <?php 
        $buteurs = array_slice($stats_joueurs[$tournoi], 0, 5);
        foreach ($buteurs as $position => $joueur): 
        ?>
            <tr>
                <td class="font-weight-bold"><?php echo $position + 1; ?></td>
                <td><?php echo htmlspecialchars($joueur['joueur']); ?></td>
                <td><?php echo htmlspecialchars($joueur['equipe']); ?></td>
                <td class="font-weight-bold"><?php echo (int) $joueur['buts']; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" class="text-center">Aucune donnée disponible.</td>
        </tr>
    <?php endif; ?>
</tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Meilleurs Passeurs -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="mb-0"><i class="fas fa-shoe-prints mr-2"></i>Meilleurs Passeurs</h4>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Joueur</th>
                                                <th>Équipe</th>
                                                <th>PD</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (isset($stats_joueurs[$tournoi])): ?>
                                                <?php 
                                                // Trier par passes décisives
                                                $passeurs = $stats_joueurs[$tournoi];
                                                usort($passeurs, function($a, $b) {
                                                    return $b['passes'] - $a['passes'];
                                                });
                                                $passeurs = array_slice($passeurs, 0, 5);
                                                
                                                foreach ($passeurs as $position => $joueur): 
                                                ?>
                                                      <tr>
                                                    <td class="font-weight-bold"><?php echo $position + 1; ?></td>
                                                    <td><?php echo $joueur['joueur']; ?></td>
                                                    <td><?php echo $joueur['equipe']; ?></td>
                                                    <td class="font-weight-bold"><?php echo $joueur['passes']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Aucune donnée disponible.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Graphiques pour les points des équipes -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const classements = <?php echo json_encode($classements); ?>;
    Object.keys(classements).forEach((tournoi, index) => {
        const canvasId = 'pointsChart' + index;
        const canvas = document.getElementById(canvasId);

        if (canvas) {
            const ctx = canvas.getContext('2d');
            const data = {
                labels: classements[tournoi].map(e => e.equipe),
                datasets: [{
                    label: 'Points',
                    data: classements[tournoi].map(e => e.points),
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: '#E74C3C',
                    borderWidth: 1
                }]
            };
        

            new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Points'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Classement des Équipes - ' + tournoi
                        }
                    }
                }
            });
        }
    });
});

</script>

</body>
</html>