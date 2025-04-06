<?php include "../config/db.php";

$match_id = 69;

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

$id_match = 69;

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
    <title>Terrain de Football avec Joueurs</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }

        .formation-container {
            position: relative;
            width: 100%;
            max-width: 900px;
        }

        .field {
            position: relative;
            width: 800px;
            height: 600px;
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
    <section>
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
</body>
</html>