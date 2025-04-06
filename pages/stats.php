<?php include "../config/db.php";

$match_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql_match = "SELECT * FROM matchs WHERE id = ?";
$stmt_match = $pdo->prepare($sql_match);
$stmt_match->execute([$match_id]);
$match = $stmt_match->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    die("Aucun match trouvé avec cet ID.");
}


// Récupérer les équipes
$equipe_domicile_id = $match['equipe1_id'];
$equipe_exterieur_id = $match['equipe2_id'];

$stmt_equipe_domicile = $pdo->prepare("SELECT * FROM equipe WHERE id = ?");
$stmt_equipe_domicile->execute([$equipe_domicile_id]);
$equipe_domicile = $stmt_equipe_domicile->fetch(PDO::FETCH_ASSOC);

$stmt_equipe_exterieur = $pdo->prepare("SELECT * FROM equipe WHERE id = ?");
$stmt_equipe_exterieur->execute([$equipe_exterieur_id]);
$equipe_exterieur = $stmt_equipe_exterieur->fetch(PDO::FETCH_ASSOC);

// Récupérer les joueurs et leurs statistiques pour l'équipe à domicile
$sql_stats_domicile = "
    SELECT j.nom, j.prenom, j.numero_club, j.role, sj.*
    FROM statistiques_joueurs sj
    JOIN joueurs j ON sj.joueur_id = j.id
    WHERE sj.match_id = ? AND j.equipes = ?
    ORDER BY j.role, j.numero_club
";


$stmt_stats_domicile = $pdo->prepare($sql_stats_domicile);
$stmt_stats_domicile->execute([$match_id, $equipe_domicile_id]);
$stats_joueurs_domicile = $stmt_stats_domicile->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les joueurs et leurs statistiques pour l'équipe extérieure
$sql_stats_exterieur = "
    SELECT j.nom, j.prenom, j.numero_club, j.role,  sj.*
    FROM statistiques_joueurs sj
    JOIN joueurs j ON sj.joueur_id = j.id
    WHERE sj.match_id = ? AND j.equipes = ?
    ORDER BY j.role, j.numero_club
";

$stmt_stats_exterieur = $pdo->prepare($sql_stats_exterieur);
$stmt_stats_exterieur->execute([$match_id, $equipe_exterieur_id]);
$stats_joueurs_exterieur = $stmt_stats_exterieur->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les statistiques globales du match
$sql_match_stats = "
    SELECT * FROM performance_matchs
    WHERE match_id = ?
";
$stmt_match_stats = $pdo->prepare($sql_match_stats);
$stmt_match_stats->execute([$match_id]);
$match_stats = $stmt_match_stats->fetch(PDO::FETCH_ASSOC);

$id_match = $match_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Requête pour récupérer les stats + les équipes
$sql = "SELECT
    stats.*,
    matchs.equipe1_id,
    matchs.equipe2_id,
    matchs.score_equipe1,
    matchs.score_equipe2,
    eq1.nom AS nom_equipe1,
    eq2.nom AS nom_equipe2,
    eq1.logo AS logo_equipe1,
    eq2.logo AS logo_equipe2
FROM stats_match AS stats
JOIN matchs ON stats.id_match = matchs.id
JOIN equipe AS eq1 ON matchs.equipe1_id = eq1.id
JOIN equipe AS eq2 ON matchs.equipe2_id = eq2.id
WHERE stats.id_match = :id_match";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_match' => $id_match]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$stats) {
    die('Aucune statistique trouvée pour ce match.');
}

// Fonction pour calculer la barre %
function calcPourcentage($valEquipe1, $valEquipe2, $valEquipe) {
    $total = $valEquipe1 + $valEquipe2;
    return $total > 0 ? round(($valEquipe / $total) * 100) : 50;
}

// Liste des stats à afficher
$statistiques = [
    'possession' => 'Possession (%)',
    'xG' => 'xG (Buts attendus)',
    'tirs_total' => 'Tirs totaux',
    'arrets_gardien' => 'Arrêts du gardien',
    'corners' => 'Corners',
    'fautes' => 'Fautes',
    'passes' => 'Passes',
    'tacles' => 'Tacles',
    'coups_francs' => 'Coups francs',
    'cartons_jaunes' => 'Cartons jaunes'
];

// Requête pour les joueurs de l'équipe 1
$sqlJoueursEquipe1 = "
SELECT sj.*, j.nom AS nom_joueur, j.prenom, j.role
FROM statistiques_joueurs sj
JOIN joueurs j ON sj.joueur_id = j.id
WHERE sj.match_id = :match_id AND j.equipes = :equipe_id
";
$stmtJoueurs1 = $pdo->prepare($sqlJoueursEquipe1);
$stmtJoueurs1->execute(['match_id' => $id_match, 'equipe_id' => $stats['equipe1_id']]);
$joueursEquipe1 = $stmtJoueurs1->fetchAll(PDO::FETCH_ASSOC);

// Requête pour les joueurs de l'équipe 2
$sqlJoueursEquipe2 = "
SELECT sj.*, j.nom AS nom_joueur, j.prenom, j.role
FROM statistiques_joueurs sj
JOIN joueurs j ON sj.joueur_id = j.id
WHERE sj.match_id = :match_id AND j.equipes = :equipe_id
";
$stmtJoueurs2 = $pdo->prepare($sqlJoueursEquipe2);
$stmtJoueurs2->execute(['match_id' => $id_match, 'equipe_id' => $stats['equipe2_id']]);
$joueursEquipe2 = $stmtJoueurs2->fetchAll(PDO::FETCH_ASSOC);


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Match - <?php echo htmlspecialchars($equipe_domicile['nom']) . ' vs ' . htmlspecialchars($equipe_exterieur['nom']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
   /* Reset et base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f2f2f2;
    color: #333;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    padding: 40px 20px;
}

/* Container principal */
.container {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 800px;
}

/* Onglets de navigation */
.filters {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #ddd;
}

.filters button {
    border: none;
    background: none;
    padding: 10px 20px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
    position: relative;
}

.filters button.active,
.filters button:hover {
    color: #4e7cff;
}

.filters button.active::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 20%;
    width: 60%;
    height: 2px;
    background: #4e7cff;
}

/* En-tête équipes et score */



.match-info img {
    max-width: 100%;
    height: auto;
    object-fit: contain; /* Important pour éviter zoom ou déformation */
}



.teams-logos {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    text-align: center;
}

.team-logo {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
}

team-logo img {
    width: 50px;
    height: 50px;
    object-fit: contain;
    border-radius: 50%;
    margin-bottom: 5px;
}

.team-logo p {
    margin: 0;
    font-weight: bold;
    font-size: 14px;
}


.score {
    flex: 0 0 80px; /* Largeur fixe pour le score pour l'alignement */
    text-align: center;
    font-size: 20px;
    font-weight: bold;
}
/* Titre section */
.title {
    text-align: center;
    font-size: 18px;
    margin-bottom: 25px;
    color: #444;
}

/* Statistiques */
.stat-line {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.team-value {
    width: 50px;
    text-align: center;
    font-weight: bold;
    font-size: 14px;
}

.team-value.green {
    color: #27ae60;
}

.team-value.blue {
    color: #2980b9;
}

.stat-bar {
    flex: 1;
    position: relative;
    height: 12px;
    background: #e0e0e0;
    border-radius: 6px;
    overflow: hidden;
    margin: 0 10px;
}

.progress {
    position: absolute;
    height: 100%;
    top: 0;
    transition: width 1s ease;
}

.progress.green {
    background: #27ae60;
    left: 0;
}

.progress.blue {
    background: #2980b9;
    right: 0;
}

.stat-label {
    position: absolute;
    width: 100%;
    text-align: center;
    top: -20px;
    font-size: 13px;
    color: #555;
    font-weight: bold;
}
/* Container principal du header du match */
.match-header {
    margin-bottom: 30px;
    text-align: center;
}

/* Ligne d’affichage des équipes et du score */
.match-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

/* Bloc équipe */
.team {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
}

/* Logo équipe */
.team-logo {
    width: 50px;          /* Taille maîtrisée */
    height: 50px;
    object-fit: contain;  /* PAS de zoom ou déformation */
    margin-bottom: 5px;
    background: white;
    border-radius: 50%;
    border: 1px solid #ddd;
    padding: 5px;
}

/* Nom de l’équipe */
.team-name {
    font-size: 14px;
    margin: 0;
    font-weight: bold;
}

/* Score et date */
.match-info {
    flex: 0 0 80px; /* Largeur fixe pour le bloc score/date */
    text-align: center;
}

.match-score {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 5px;
}

.match-date {
    font-size: 12px;
    color: #888;
}

/* Responsive : pour mobile */
@media (max-width: 768px) {
    .team-logo {
        width: 40px;
        height: 40px;
    }

    .team-name {
        font-size: 12px;
    }

    .match-score {
        font-size: 16px;
    }

    .match-info {
        flex: 0 0 60px;
    }
}
.teams-logos .team-logo img {
    width: 50px;
    height: 50px;
    object-fit: contain;
    margin-bottom: 5px;
    border-radius: 50%;
    border: none; /* ✅ Supprime toute bordure par défaut */
    background: none; /* ✅ Supprime le fond blanc ou gris hérité */
    box-shadow: none; /* ✅ Supprime les ombres héritées éventuelles */
    outline: none; /* ✅ Supprime les contours automatiques */
}
.teams-logos .team-logo {
    all: unset; /* ✅ Reset total si nécessaire */
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}
.stat-line {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

/* Nom de la statistique */
.stat-name {
    width: 150px;
    text-align: left;
    font-size: 13px;
    font-weight: bold;
    color: #333;
}

/* Valeur de chaque équipe */
.team-value {
    width: 40px;
    text-align: center;
    font-weight: bold;
    font-size: 13px;
}

/* Barre de progression */
.stat-bar {
    flex: 1;
    position: relative;
    height: 10px;
    background: #e0e0e0;
    border-radius: 5px;
    overflow: hidden;
    margin: 0 10px;
}

.progress {
    position: absolute;
    height: 100%;
    top: 0;
    transition: width 1s ease;
}

.progress.green {
    background: #27ae60;
    left: 0;
}

.progress.blue {
    background: #2980b9;
    right: 0;
}

.players-stats {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
}

.team-players {
    width: 48%;
    background: #f9f9f9;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.team-players .team-logo {
    width: 50px;
    height: 50px;
    object-fit: contain;
    margin-bottom: 10px;
}

.team-players h3 {
    margin-bottom: 15px;
    font-size: 16px;
    color: #333;
}

.player-stat {
    text-align: left;
    font-size: 13px;
    margin-bottom: 10px;
    background: white;
    padding: 8px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.formation-container {
    width: 100%;
    max-width: 1000px;
    margin: auto;
    background: #00793f;
    border-radius: 15px;
    padding: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
}

.field {
    position: relative;
    width: 100%;
    height: 600px;
    background: url('../assets/images/terrain.png') no-repeat center center;
    background-size: cover;
    border-radius: 10px;
    overflow: hidden;
}

/* Style des joueurs */
.player {
    position: absolute;
    text-align: center;
    color: white;
    width: 80px;
    transform: translate(-50%, -50%);
    font-size: 12px;
}

.player-icon img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    padding: 2px;
    object-fit: cover;
}

.player-name {
    margin-top: 5px;
    font-weight: bold;
    color: white;
    font-size: 11px;
}

.player-note {
    margin-top: 3px;
    background: #2ecc71;
    color: white;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    line-height: 22px;
    font-size: 12px;
    font-weight: bold;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
}

/* Responsive terrain */
@media (max-width: 768px) {
    .field {
        height: 400px;
    }

    .player {
        width: 60px;
        font-size: 10px;
    }

    .player-icon img {
        width: 30px;
        height: 30px;
    }

    .player-note {
        width: 18px;
        height: 18px;
        line-height: 18px;
        font-size: 10px;
    }
}

.stats-note {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    background-color: #f0f0f0;
    font-family: Arial, sans-serif;
    overflow: auto;
}

.formation-container {
    position: relative;
    width: 100%;
    max-width: 1000px;
    overflow: hidden;
}


.field {
    position: relative;
    width: 100%;
    height: auto;
    aspect-ratio: 4 / 3; /* Cela garde les proportions du terrain */
    background: repeating-linear-gradient(
        90deg,
        #0a8020,
        #0a8020 40px,
        #0e9026 40px,
        #0e9026 80px
    );
    border: 5px solid white;
    overflow: hidden;
}


        /* Lignes blanches du terrain */
        .field-lines {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        /* Ligne centrale verticale */
        .center-line {
            position: absolute;
            left: 50%;
            top: 0;
            height: 100%;
            width: 2px;
            background-color: white;
        }

        /* Cercle central */
        .center-circle {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 150px;
            height: 150px;
            border: 2px solid white;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        /* Point central */
        .center-dot {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 8px;
            height: 8px;
            background-color: white;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        /* Surfaces de réparation (gauche) */
        .penalty-area-left {
            position: absolute;
            left: 0;
            top: 50%;
            width: 180px;
            height: 440px;
            border: 2px solid white;
            border-left: none;
            transform: translateY(-50%);
        }

        /* Surfaces de réparation (droite) */
        .penalty-area-right {
            position: absolute;
            right: 0;
            top: 50%;
            width: 180px;
            height: 440px;
            border: 2px solid white;
            border-right: none;
            transform: translateY(-50%);
        }

        /* Surfaces de but (gauche) */
        .goal-area-left {
            position: absolute;
            left: 0;
            top: 50%;
            width: 60px;
            height: 220px;
            border: 2px solid white;
            border-left: none;
            transform: translateY(-50%);
        }

        /* Surfaces de but (droite) */
        .goal-area-right {
            position: absolute;
            right: 0;
            top: 50%;
            width: 60px;
            height: 220px;
            border: 2px solid white;
            border-right: none;
            transform: translateY(-50%);
        }

        /* Points de penalty */
        .penalty-spot-left {
            position: absolute;
            left: 120px;
            top: 50%;
            width: 8px;
            height: 8px;
            background-color: white;
            border-radius: 50%;
            transform: translateY(-50%);
        }

        .penalty-spot-right {
            position: absolute;
            right: 120px;
            top: 50%;
            width: 8px;
            height: 8px;
            background-color: white;
            border-radius: 50%;
            transform: translateY(-50%);
        }

        /* Arcs de cercle de penalty */
        .penalty-arc-left {
            position: absolute;
            left: 180px;
            top: 50%;
            width: 80px;
            height: 80px;
            border: 2px solid white;
            border-radius: 50%;
            border-left: none;
            clip-path: polygon(0% 0%, 50% 0%, 50% 100%, 0% 100%);
            transform: translateY(-50%);
        }

        .penalty-arc-right {
            position: absolute;
            right: 180px;
            top: 50%;
            width: 80px;
            height: 80px;
            border: 2px solid white;
            border-radius: 50%;
            border-right: none;
            clip-path: polygon(50% 0%, 100% 0%, 100% 100%, 50% 100%);
            transform: translateY(-50%);
        }

        /* Coins */
        .corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 2px solid white;
            border-radius: 50%;
        }

        .corner-top-left {
            top: 0;
            left: 0;
            border-bottom-right-radius: 0;
            border-top: none;
            border-left: none;
        }

        .corner-top-right {
            top: 0;
            right: 0;
            border-bottom-left-radius: 0;
            border-top: none;
            border-right: none;
        }

        .corner-bottom-left {
            bottom: 0;
            left: 0;
            border-top-right-radius: 0;
            border-bottom: none;
            border-left: none;
        }

        .corner-bottom-right {
            bottom: 0;
            right: 0;
            border-top-left-radius: 0;
            border-bottom: none;
            border-right: none;
        }

        /* Style pour les joueurs */
        .player {
            position: absolute;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 70px;
        }

        .player-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .player-icon img {
            width: 80%;
            height: auto;
        }

        .player-name {
            color: white;
            font-size: 10px;
            text-shadow: 1px 1px 1px #000;
            margin-top: 4px;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .player-note {
            background-color: #ffcc00;
            color: #000;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            line-height: 20px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            margin: 4px auto 0;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- En-tête du match -->
        <div class="match-header">
    <div class="match-row">
        <!-- Équipe domicile -->
        <div class="team">
            <?php if (!empty($equipe_domicile['logo'])): ?>
                <img src="../assets/images/<?php echo htmlspecialchars($equipe_domicile['logo']); ?>" alt="Logo <?php echo htmlspecialchars($equipe_domicile['nom']); ?>" class="team-logo">
            <?php endif; ?>
            <h3 class="team-name"><?php echo htmlspecialchars($equipe_domicile['nom']); ?></h3>
        </div>

        <!-- Score + Date -->
        <div class="match-info">
            <div class="match-score">
                <?php echo $match['score_equipe1'] ?? 0; ?> - <?php echo $match['score_equipe2'] ?? 0; ?>
            </div>
            <div class="match-date">
                <?php
                $date = new DateTime($match['date']);
                echo $date->format('d/m/Y H:i');
                ?>
            </div>
        </div>

        <!-- Équipe extérieur -->
        <div class="team">
            <?php if (!empty($equipe_exterieur['logo'])): ?>
                <img src="../assets/images/<?php echo htmlspecialchars($equipe_exterieur['logo']); ?>" alt="Logo <?php echo htmlspecialchars($equipe_exterieur['nom']); ?>" class="team-logo">
            <?php endif; ?>
            <h3 class="team-name"><?php echo htmlspecialchars($equipe_exterieur['nom']); ?></h3>
        </div>
    </div>
</div>


        <!-- Navigation par onglets -->
        <ul class="nav nav-tabs" id="matchTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="alignments-tab" data-bs-toggle="tab" data-bs-target="#alignments" type="button" role="tab" aria-controls="alignments" aria-selected="true">Alignements</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button" role="tab" aria-controls="stats" aria-selected="false">Statistiques</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab" aria-controls="summary" aria-selected="false">Résumé</button>
            </li>
        </ul>

        <div class="tab-content" id="matchTabsContent">
            <!-- Onglet Alignements -->
            <div class="tab-pane fade show active" id="alignments" role="tabpanel" aria-labelledby="alignments-tab">
                <div class="row mt-4">
                    <!-- Équipe à domicile -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0"><?php echo htmlspecialchars($equipe_domicile['nom']); ?> - Alignement</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Nom</th>
                                            <th>Poste</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stats_joueurs_domicile as $joueur): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($joueur['numero_club']); ?></td>
                                                <td>
                                                    <?php
                                                    echo htmlspecialchars($joueur['prenom'] ?? '') . ' ' . htmlspecialchars($joueur['nom']);
                                                    if (isset($joueur['capitaine']) && $joueur['capitaine'] == 1) echo ' (C)';
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($joueur['role']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Équipe extérieure -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h4 class="mb-0"><?php echo htmlspecialchars($equipe_exterieur['nom']); ?> - Alignement</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Nom</th>
                                            <th>Poste</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stats_joueurs_exterieur as $joueur): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($joueur['numero_club']); ?></td>
                                                <td>
                                                    <?php
                                                    echo htmlspecialchars($joueur['prenom'] ?? '') . ' ' . htmlspecialchars($joueur['nom']);
                                                    if (isset($joueur['capitaine']) && $joueur['capitaine'] == 1) echo ' (C)';
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($joueur['role']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Statistiques -->
            <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                <div class="stats-section">

                    <div class="table-responsive">
                    <div class="teams-logos">
    <div class="team-logo">
        <img src="../assets/images/<?= htmlspecialchars($stats['logo_equipe1']) ?>" alt="<?= htmlspecialchars($stats['nom_equipe1']) ?>">
        <p><?= htmlspecialchars($stats['nom_equipe1']) ?></p>
    </div>
    <div class="score">
        <?= htmlspecialchars($stats['score_equipe1']) ?> - <?= htmlspecialchars($stats['score_equipe2']) ?>
    </div>
    <div class="team-logo">
        <img src="../assets/images/<?= htmlspecialchars($stats['logo_equipe2']) ?>" alt="<?= htmlspecialchars($stats['nom_equipe2']) ?>">
        <p><?= htmlspecialchars($stats['nom_equipe2']) ?></p>
    </div>
</div>
<br>
<h1 >Statistiques du match<h1>

<?php foreach ($statistiques as $key => $label):
    $val1 = $stats[$key . '_equipe1'];
    $val2 = $stats[$key . '_equipe2'];
    $pourcentage1 = calcPourcentage($val1, $val2, $val1);
    $pourcentage2 = calcPourcentage($val1, $val2, $val2);
?>
<div class="stat-line">
    <!-- Nom de la statistique -->
    <div class="stat-name"><?= $label ?></div>

    <!-- Valeur équipe 1 -->
    <div class="team-value green"><?= $val1 ?></div>

    <!-- Barre -->
    <div class="stat-bar">
        <div class="progress green" style="width: <?= $pourcentage1 ?>%;"></div>
        <div class="progress blue" style="width: <?= $pourcentage2 ?>%;"></div>
    </div>

    <!-- Valeur équipe 2 -->
    <div class="team-value blue"><?= $val2 ?></div>
</div>
<?php endforeach; ?>



<!-- stats joueurs -->


<div class="players-stats">
    <!-- Équipe 1 -->
    <div class="team-players">
        <img src="../assets/images/<?= htmlspecialchars($stats['logo_equipe1']) ?>" alt="<?= htmlspecialchars($stats['nom_equipe1']) ?>" class="team-logo">
        <h3><?= htmlspecialchars($stats['nom_equipe1']) ?></h3>
        <?php foreach ($joueursEquipe1 as $joueur): ?>
            <div class="player-stat">
                <strong><?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom_joueur']) ?></strong>
                (<?= htmlspecialchars($joueur['role']) ?>)
                <br>
                Buts: <?= $joueur['buts'] ?> |
                Passes décisives: <?= $joueur['passes_decisives'] ?> |
                Cartons jaunes: <?= $joueur['cartons_jaunes'] ?> |
                Cartons rouges: <?= $joueur['cartons_rouges'] ?> |
                Minutes jouées: <?= $joueur['minutes_jouees'] ?> |
                Tirs cadrés: <?= $joueur['tirs_cadres'] ?> |
                Note: <?= $joueur['note_match'] ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Équipe 2 -->
    <div class="team-players">
        <img src="../assets/images/<?= htmlspecialchars($stats['logo_equipe2']) ?>" alt="<?= htmlspecialchars($stats['nom_equipe2']) ?>" class="team-logo">
        <h3><?= htmlspecialchars($stats['nom_equipe2']) ?></h3>
        <?php foreach ($joueursEquipe2 as $joueur): ?>
            <div class="player-stat">
                <strong><?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom_joueur']) ?></strong>
                (<?= htmlspecialchars($joueur['role']) ?>)
                <br>
                Buts: <?= $joueur['buts'] ?> |
                Passes décisives: <?= $joueur['passes_decisives'] ?> |
                Cartons jaunes: <?= $joueur['cartons_jaunes'] ?> |
                Cartons rouges: <?= $joueur['cartons_rouges'] ?> |
                Minutes jouées: <?= $joueur['minutes_jouees'] ?> |
                Tirs cadrés: <?= $joueur['tirs_cadres'] ?> |
                Note: <?= $joueur['note_match'] ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<h2 class="title">Statistiques des Joueurs</h2>
<section class="stats-note">
    <div class="formation-container">
        <div class="field">
            <!-- Lignes du terrain -->
            <div class="field-lines">
                <div class="center-line"></div>
                <div class="center-circle"></div>
                <div class="center-dot"></div>

                <div class="penalty-area-left"></div>
                <div class="penalty-area-right"></div>

                <div class="goal-area-left"></div>
                <div class="goal-area-right"></div>

                <div class="penalty-spot-left"></div>
                <div class="penalty-spot-right"></div>

                <div class="penalty-arc-left"></div>
                <div class="penalty-arc-right"></div>

                <div class="corner corner-top-left"></div>
                <div class="corner corner-top-right"></div>
                <div class="corner corner-bottom-left"></div>
                <div class="corner corner-bottom-right"></div>
            </div>

            <?php
            // Positions pour équipe domicile (gauche)
           // Position équipe domicile (gauche)// Équipe domicile (gauche) - 1-4-3-3
$positionsDomicile = [
    0 => ['top' => '10%', 'left' => '15%'], // LB

    // Défenseurs (4)
    10 => ['top' => '50%', 'left' => '5%'],   // GK (10=>1)
    2 => ['top' => '80%', 'left' => '15%'],  // Défenseur central gauche
    3 => ['top' => '70%', 'left' => '25%'],  // Défenseur central droit
    4 => ['top' => '75%', 'left' => '40%'],  // Latéral droit

    // Milieux (3)
    5 => ['top' => '55%', 'left' => '15%'], // Milieu défensif
    6 => ['top' => '50%', 'left' => '25%'], // Milieu central
    7 => ['top' => '55%', 'left' => '35%'], // Milieu offensif

    // Attaquants (3)
    8 => ['top' => '35%', 'left' => '15%'], // Ailier gauche
    9 => ['top' => '30%', 'left' => '25%'], // Avant-centre
    1 => ['top' => '34%', 'left' => '40%'], // Ailier droit
];

// Équipe extérieur (droite) - 1-4-3-3
$positionsExterieur = [
    0 => ['top' => '90%', 'left' => '85%'], // Gardien

    // Défenseurs (4)
    1 => ['top' => '75%', 'left' => '60%'], // Latéral gauche
    2 => ['top' => '70%', 'left' => '75%'], // Défenseur central gauche
    3 => ['top' => '70%', 'left' => '85%'], // Défenseur central droit
    4 => ['top' => '55%', 'left' => '95%'], // Latéral droit

    // Milieux (3)
    5 => ['top' => '55%', 'left' => '65%'], // Milieu défensif
    6 => ['top' => '50%', 'left' => '75%'], // Milieu central
    7 => ['top' => '55%', 'left' => '85%'], // Milieu offensif
    // Attaquants (3)
    8 => ['top' => '35%', 'left' => '60%'], // Ailier gauche
    9 => ['top' => '30%', 'left' => '75%'], // Avant-centre
    10 => ['top' => '30%', 'left' => '85%'], // Ailier droit
];



            // Équipe domicile
            foreach ($stats_joueurs_domicile as $index => $joueur):
                $pos = $positionsDomicile[$index];
            ?>
                <div class="player" style="top: <?= $pos['top'] ?>; left: <?= $pos['left'] ?>;">
                    <div class="player-icon">
                        <img src="../assets/images/football-player.png" alt="<?= htmlspecialchars($joueur['nom']) ?>">
                    </div>
                    <div class="player-name"><?= htmlspecialchars($joueur['nom']) ?></div>
                    <div class="player-note"><?= $joueur['note_match'] ?></div>
                </div>
            <?php endforeach; ?>

            <!-- Équipe extérieur -->
            <?php foreach ($stats_joueurs_exterieur as $index => $joueur):
                $pos = $positionsExterieur[$index];
            ?>
                <div class="player" style="top: <?= $pos['top'] ?>; left: <?= $pos['left'] ?>;">
                    <div class="player-icon">
                        <img src="../assets/images/football-player.png" alt="<?= htmlspecialchars($joueur['nom']) ?>">
                    </div>
                    <div class="player-name"><?= htmlspecialchars($joueur['nom']) ?></div>
                    <div class="player-note"><?= $joueur['note_match'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    </section>


</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const progresses = document.querySelectorAll('.progress');
    progresses.forEach(progress => {
        const width = progress.style.width;
        progress.style.width = '0';
        setTimeout(() => {
            progress.style.width = width;
        }, 100);
    });
});
</script>
                </div>
            </div>

            <!-- Onglet Résumé -->
            <div class="tab-pane fade" id="summary" role="tabpanel" aria-labelledby="summary-tab">
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Résumé du Match</h4>
                            </div>
                            <div class="card-body">
                                <?php if ($match_stats): ?>
                                    <div class="row">
                                        <div class="col-md-4 text-end fw-bold">
                                            <?php echo isset($match_stats['possession_equipe1']) ? $match_stats['possession_equipe1'] . '%' : 'N/A'; ?>
                                        </div>
                                        <div class="col-md-4 text-center">Possession de balle</div>
                                        <div class="col-md-4 fw-bold">
                                            <?php echo isset($match_stats['possession_equipe2']) ? $match_stats['possession_equipe2'] . '%' : 'N/A'; ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4 text-end fw-bold">
                                            <?php echo isset($match_stats['tirs_equipe1']) ? $match_stats['tirs_equipe1'] : 'N/A'; ?>
                                        </div>
                                        <div class="col-md-4 text-center">Tirs</div>
                                        <div class="col-md-4 fw-bold">
                                            <?php echo isset($match_stats['tirs_equipe2']) ? $match_stats['tirs_equipe2'] : 'N/A'; ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4 text-end fw-bold">
                                            <?php echo isset($match_stats['tirs_cadres_equipe1']) ? $match_stats['tirs_cadres_equipe1'] : 'N/A'; ?>
                                        </div>
                                        <div class="col-md-4 text-center">Tirs cadrés</div>
                                        <div class="col-md-4 fw-bold">
                                            <?php echo isset($match_stats['tirs_cadres_equipe2']) ? $match_stats['tirs_cadres_equipe2'] : 'N/A'; ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4 text-end fw-bold">
                                            <?php echo isset($match_stats['corners_equipe1']) ? $match_stats['corners_equipe1'] : 'N/A'; ?>
                                        </div>
                                        <div class="col-md-4 text-center">Corners</div>
                                        <div class="col-md-4 fw-bold">
                                            <?php echo isset($match_stats['corners_equipe2']) ? $match_stats['corners_equipe2'] : 'N/A'; ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4 text-end fw-bold">
                                            <?php echo isset($match_stats['fautes_equipe1']) ? $match_stats['fautes_equipe1'] : 'N/A'; ?>
                                        </div>
                                        <div class="col-md-4 text-center">Fautes</div>
                                        <div class="col-md-4 fw-bold">
                                            <?php echo isset($match_stats['fautes_equipe2']) ? $match_stats['fautes_equipe2'] : 'N/A'; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        Aucune statistique disponible pour ce match.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($match['commentaires'])): ?>
                        <div class="card mt-4">
                            <div class="card-header">
                                <h4>Commentaires</h4>
                            </div>
                            <div class="card-body">
                                <?php echo nl2br(htmlspecialchars($match['commentaires'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>