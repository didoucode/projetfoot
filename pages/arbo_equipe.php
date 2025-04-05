<?php
// Inclure la connexion à la base de données
include "../config/db.php";

// Vérifier si l'ID de l'équipe est présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Aucune équipe sélectionnée.");
}

$equipe_id = intval($_GET['id']);
$query_stats = "SELECT * FROM stats_equipe WHERE equipe_id = :equipe_id ORDER BY saison DESC LIMIT 1"; // Récupère les données les plus récentes
$stmt = $pdo->prepare($query_stats);
$stmt->execute(['equipe_id' => $equipe_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
// Récupérer les informations de l'équipe
$stmt_equipe = $pdo->prepare("SELECT * FROM equipe WHERE id = ?");
$stmt_equipe->execute([$equipe_id]);
$equipe = $stmt_equipe->fetch();

if (!$equipe) {
    die("Équipe non trouvée.");
}
$query_matchs = "SELECT * FROM matchs WHERE (equipe1_id = ? OR equipe2_id = ?) AND date < NOW() ORDER BY journee DESC";
$stmt = $pdo->prepare($query_matchs);

// Lier les paramètres avec bindParam
$stmt->bindParam(1, $equipe_id, PDO::PARAM_INT);
$stmt->bindParam(2, $equipe_id, PDO::PARAM_INT);
$stmt->execute();
$matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);



$teamId = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Récupérer l'ID de l'équipe depuis l'URL
$queryinfo = "SELECT * FROM informations WHERE equipe_id = :teamId";
$stmt = $pdo->prepare($queryinfo);
$stmt->bindParam(':teamId', $equipe_id, PDO::PARAM_INT);
$stmt->execute();

// Récupérer les résultats
$informations = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($teamId > 0) {
    // Préparer la requête pour récupérer le prochain match
    $stmt_nextmatch = $pdo->prepare("
        SELECT id, equipe1_id, equipe2_id, date_match
        FROM nextmatch
        WHERE (equipe1_id = :teamId OR equipe2_id = :teamId)
        AND date_match > NOW()
        ORDER BY date_match ASC
        LIMIT 1
    ");

    // Lier l'ID de l'équipe à la requête
    $stmt_nextmatch->bindParam(':teamId', $teamId, PDO::PARAM_INT);

    // Exécuter la requête
    $stmt_nextmatch->execute();

    // Récupérer les données du prochain match
    $next_match = $stmt_nextmatch->fetch(PDO::FETCH_ASSOC);

    // Vérifier si un prochain match a été trouvé
    if ($next_match) {
        // Récupérer les logos des équipes
        $stmt_logo1 = $pdo->prepare("SELECT logo FROM equipe WHERE id = :equipe1_id");
        $stmt_logo1->bindParam(':equipe1_id', $next_match['equipe1_id'], PDO::PARAM_INT);
        $stmt_logo1->execute();
        $logo1 = $stmt_logo1->fetchColumn();

        $stmt_logo2 = $pdo->prepare("SELECT logo FROM equipe WHERE id = :equipe2_id");
        $stmt_logo2->bindParam(':equipe2_id', $next_match['equipe2_id'], PDO::PARAM_INT);
        $stmt_logo2->execute();
        $logo2 = $stmt_logo2->fetchColumn();
    }
}
// Fonction pour déterminer le résultat (V = victoire, N = nul, D = défaite)
function getMatchResult($score1, $score2) {
    if ($score1 > $score2) {
        return 'V'; // Victoire
    } elseif ($score1 < $score2) {
        return 'D'; // Défaite
    } else {
        return 'N'; // Nul
    }
}
function getMatchResultClass($score1, $score2, $equipeId) {
    if ($score1 > $score2 && $equipeId == 1) {
        return 'win'; // Victoire pour l'équipe 1
    } elseif ($score1 < $score2 && $equipeId == 2) {
        return 'win'; // Victoire pour l'équipe 2
    } elseif ($score1 == $score2) {
        return 'draw'; // Match nul
    } else {
        return 'lose'; // Défaite pour l'équipe
    }
}
// Récupérer le classement de l'équipe
$stmt_classement = $pdo->prepare("SELECT * FROM classement WHERE equipe_id = ?");
$stmt_classement->execute([$equipe_id]);
$classement = $stmt_classement->fetch();

// Récupérer les trophées de l'équipe
$stmt_trophees = $pdo->prepare("
    SELECT botola, coup_trone, caf, coup_arabe, super_coup
    FROM trophees WHERE equipe_id = ?");
$stmt_trophees->execute([$equipe_id]);
$trophees = $stmt_trophees->fetch();

// Récupérer les joueurs de l'équipe
$stmt_joueurs = $pdo->prepare("SELECT * FROM joueurs WHERE clubs = ? ORDER BY role, nom");
$stmt_joueurs->execute([$equipe['nom']]);
$joueurs = $stmt_joueurs->fetchAll();
function getMoyenneClass($note) {
    if ($note < 6) {
        return 'moyenne-rouge';
    } elseif ($note >= 6 && $note < 7) {
        return 'moyenne-jaune';
    } elseif ($note >= 7 && $note < 9) {
        return 'moyenne-verte';
    } else {
        return 'moyenne-bleu';
    }
}



$sql_classement = "SELECT classement.id, classement.points , classement.recentes
                   FROM classement
                   JOIN equipe ON classement.equipe_id = equipe.id
                   ORDER BY classement.points DESC, classement.id ASC";
// Exécuter la requête avec le statement $stmt_classement
$stmt_classement = $pdo->prepare($sql_classement);
$stmt_classement->execute();

// Récupérer tous les résultats sous forme de tableau associatif
$classement = $stmt_classement->fetchAll(PDO::FETCH_ASSOC);

$query_logo = "
    SELECT m.*, e1.logo AS logo_equipe1, e2.logo AS logo_equipe2
    FROM matchs m
    LEFT JOIN equipe e1 ON m.equipe1_id = e1.id
    LEFT JOIN equipe e2 ON m.equipe2_id = e2.id
    WHERE (m.equipe1_id = ? OR m.equipe2_id = ?) AND m.date < NOW()
    ORDER BY m.journee DESC
";

$stmt = $pdo->prepare($query_logo);

// Lier les paramètres avec bindParam
$stmt->bindParam(1, $equipe_id, PDO::PARAM_INT);
$stmt->bindParam(2, $equipe_id, PDO::PARAM_INT);
$stmt->execute();
$matchs = $stmt->fetchAll();


// Liste des trophées avec leurs images
$trophee_images = [
    "botola" => "botola1.jpg",
    "coup_trone" => "coup_trone.jpeg",
    "caf" => "caf.jpg",
    "coup_arabe" => "coup_arabe.jpg",
    "super_coup" => "super_coup.jpg"
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $equipe['nom']; ?> - Profil</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />    <style>
        :root {
            --primary-color: linear-gradient(to right, green, black);
            --secondary-color:rgb(3, 88, 46);
            --accent-color: #3498db;
            --background-color: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(5, 80, 34, 0.1);
        }
        body {
            background-color: #f8f9fa;
        }
        .team-container {
            width: 100%;
            margin: auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .team-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
        

     
.chart-container {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
}

.stats {
    display: flex;               /* Utilisation de flexbox pour le centrage */
    justify-content: center;     /* Centre horizontalement */
    align-items: center;         /* Centre verticalement */
    height: 400px;               /* Donne une hauteur au conteneur pour l'alignement vertical */
    margin-top: 30px;
    border: #000 solid 2px;          /* Donne un espace au-dessus du graphique */
}

canvas {
    max-width: 90%;              /* Limite la largeur du graphique à 90% de l'écran */
    height: auto;                /* Maintient l'aspect ratio du graphique */
}

#statsRadarChart {
            width: 80%;
            height: 400px;
            margin: auto;
        }

        .next-match {
            background: #ffcc00;
            padding: 10px;
            border-radius: 10px;
        }
        .trophy-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    gap: 20px;  /* Espace entre les trophées */
}

.trophy {
    background-color: black;  /* Fond noir */
    border-radius: 10px;      /* Bords arrondis */
    padding: 10px;
    text-align: center;       /* Centrer le texte */
    width: 120px;             /* Largeur uniforme pour chaque trophée */
    height: 150px;            /* Hauteur uniforme pour chaque trophée */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.trophy img {
    width: 80px;             /* Taille fixe des images */
    height: 80px;            /* Taille fixe des images */
    object-fit: contain;     /* Ajuste l'image sans la déformer */
    margin-bottom: 10px;     /* Espacement entre l'image et le texte */
}

.trophy p {
    color: white;            /* Texte en blanc */
    font-size: 16px;         /* Taille de la police */
    margin: 0;               /* Enlever les marges par défaut */
}
/* Conteneur global des joueurs */
.players-container {
    display: flex;
    justify-content: flex-start;
    padding: 20px;
}

/* Conteneur des joueurs avec fond et bordure */
.players-section {
    width: 100%;
    max-width: 350px;  /* Limiter la largeur */
    background-color: #f5f5f5;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
}

/* Liste des joueurs */
.players-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

/* Style de chaque joueur */
.player-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

/* Liens des joueurs */
.player-link {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: black;
    font-weight: bold;
    flex-grow: 1; /* Permet à l'élément de prendre toute la largeur disponible */
}

/* Image des joueurs */
.player-img {
    width: 35px;
    height: 35px;
    margin-right: 10px;
}

/* Nom des joueurs */
.player-name {
    margin-left: 10px;
    font-size: 16px;
    font-weight: bold;
}

/* Style des rôles */
.role {
    font-style: italic;
    color: #666;
    margin-right: 10px;
}

/* Styles pour la moyenne avec couleurs dynamiques */
.moyenne {
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    color: white;
}

.moyenne-rouge { background-color: red; }
.moyenne-jaune { background-color: yellow; color: black; }
.moyenne-verte { background-color: green; }
.moyenne-bleu { background-color: blue; }

/* Si aucun joueur */
.no-player {
    text-align: center;
    color: #888;
    font-style: italic;
    padding: 10px;
}


/* Style des rôles */
.role {
    font-style: italic;
    color: #666;
}

/* Styles pour la moyenne avec couleurs dynamiques */
.moyenne {
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    color: white;
}

.moyenne-rouge { background-color: red; }
.moyenne-jaune { background-color: yellow; color: black; }
.moyenne-verte { background-color: green; }
.moyenne-bleu { background-color: blue; }

/* Si aucun joueur */
.no-player {
    text-align: center;
    color: #888;
    font-style: italic;
    padding: 10px;
}


/* Style des rôles */
.role {
    font-style: italic;
    color: #666;
}

/* Styles pour la moyenne avec couleurs dynamiques */
.moyenne {
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    color: white;
}

.moyenne-rouge { background-color: red; }
.moyenne-jaune { background-color: yellow; color: black; }
.moyenne-verte { background-color: green; }
.moyenne-bleu { background-color: blue; }

        .classment-info {
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        body {
    font-family: Arial, sans-serif;
    background-color: #f5f5f5;
    margin: 0;
    padding: 20px;
}

.informations {
    width: 80%;
    margin: auto;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.navigation {
    display: flex;
    justify-content: space-between;
}

button {
    padding: 10px;
    border: none;
    background: #007bff;
    color: white;
    font-weight: bold;
    cursor: pointer;
    border-radius: 5px;
}
.match-results {
    text-align: center; /* Centrer le titre */
}

ul {
    list-style: none;
    padding: 0;
}

.match-item {
    display: flex;
    justify-content: center; /* Alignement horizontal */
    align-items: center; /* Alignement vertical */
    gap: 10px; /* Espacement entre les éléments */
    padding: 10px;
    border-bottom: 1px solid #ddd; /* Ligne de séparation entre les matchs */
}

.date {
    font-weight: bold;
    width: 100px; /* Taille fixe pour l’alignement */
}

.team {
    font-weight: bold;
    min-width: 120px; /* Assurer un alignement constant */
}

.score {
    font-size: 1.2rem;
    font-weight: bold;
    min-width: 30px;
    text-align: center;
}

.result {
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 5px;
}


.result.win { background: green; color: white; }
.result.draw { background: gray; color: white; }
.result.lose { background: red; color: white; }

.match-info {
    text-align: center;
}
.team-logo img {
    background-color: white;
}
.match-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: white; /* Assurer qu'il n'y a pas de fond blanc */
}

.match-details img {
    width: 100px;  /* Taille fixe des images */
    height: 100px;  /* Taille fixe des images */
    object-fit: contain; /* Ajuste l'image sans perte de qualité */
    margin: 0 10px;  /* Espace entre les images */
    background-color: white; /* Assurer qu'il n'y a pas de fond blanc sur l'image */
}



.match-details .time {
    font-size: 18px;
    font-weight: bold;
    margin: 0 15px;
}

.more-info {
    margin-top: 10px;
    display: block;
    width: 100%;
}/* Table styling */
/* Table styling */
.ranking-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Table headers with gradient */
.ranking-table th {
    background: linear-gradient(45deg, #28a745, #343a40); /* Gradient from green to black */
    color: white;
    padding: 15px 10px;
    text-align: center;
    font-size: 1.2rem;
    font-weight: bold;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

/* Table data cells */
.ranking-table td {
    padding: 12px 10px;
    text-align: center;
    border-bottom: 1px solid #ddd;
    font-size: 1rem;
}

/* Style for team logo */
.ranking-table td img {
    width: 50px;
    height: 50px;
    object-fit: contain;
    border-radius: 50%;
}

/* Row styles */
.ranking-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Form styles for match result */
.ranking-table .form span {
    margin: 0 5px;
    font-weight: bold;
    font-size: 1.2rem;
}

/* Specific colors for match results */


/* Last row border styling */
.ranking-table tr:last-child td {
    border-bottom: none;
}

/* Styling for the first column (ranking number) */
.ranking-table td:first-child {
    font-weight: bold;
    font-size: 1.2rem;
    color: #333;
}


.form span {
    padding: 5px;
    margin: 2px;
    border-radius: 5px;
    display: inline-block;
}

.win { background: green; color: white; }
.draw { background: gray; color: white; }
.lose { background: red; color: white; }
.prev {
    background: linear-gradient(45deg, #28a745, #343a40); /* Gradient from green to black */

}
.next{
    background: linear-gradient(45deg, #28a745, #343a40); /* Gradient from green to black */

}
/* Container for the team logo and name */
.logo_haute {
    text-align: center;
    margin-top: 20px;
}

/* Style for the team logo */
.logo_haute .team-logo {
    width: 150px; /* Adjust this size to fit the logo perfectly */
    height: auto; /* Maintain the aspect ratio */
    object-fit: contain; /* Ensures the logo is not zoomed or stretched */
    border-radius: 50%; /* Make the logo round if it's a square */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for a sleek look */
}

/* Styling for the team name */
.logo_haute .team-name {
    font-size: 2rem; /* Increase font size for better visibility */
    font-weight: bold; /* Make the name bold */
    color: #000; /* White color to contrast with the dark background */
    text-transform: uppercase; /* Capitalize the team name */
    letter-spacing: 2px; /* Add spacing between letters for style */
    margin-top: 10px; /* Add some space between the logo and the name */
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* Add a subtle shadow for more emphasis */
}
/* Conteneur global */
.container {
    display: flex;
    justify-content: space-between;
    padding: 20px;
}

/* Section des joueurs */
.players-section {
    width: 48%;  /* Pour que cela occupe 48% de la largeur */
    background-color: #f5f5f5;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
}

/* Section des informations de l'équipe */
.team-info-section {
    width: 48%;  /* Pour que cela occupe 48% de la largeur */
    background-color: #f9f9f9;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
}

/* Liste des informations */
.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-item {
    padding: 5px;
    margin-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

/* Si aucune information */
.no-player {
    text-align: center;
    color: #888;
    font-style: italic;
    padding: 10px;
}

    </style>
</head>
<body>
  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="content">

<?php include '../includes/header.php'; ?>

<div class="container py-4">
    <div class="team-container text-center">
        <!-- Logo et nom de l'équipe -->
        <div class="logo_haute">
    <?php if (!empty($equipe['logo'])): ?>
        <img src="../assets/images/<?php echo $equipe['logo']; ?>" alt="<?php echo $equipe['nom']; ?>" class="team-logo">
    <?php endif; ?>
    <h2 class="team-name"><?php echo $equipe['nom']; ?></h2>
</div>



        <!-- Classement -->

        <div class="informations">
        <section class="match-results">
    <h3>Botola Pro League 1 <img src="../assets/images/trophy.png" style="width: 50px; height: 50px;"></h3>

    <!-- Navigation pour les boutons "Précédent" et "Suivant" -->
    <div class="navigation">
        <button class="prev">◀ PRÉCÉDENT</button>
        <button class="next">SUIVANT ▶</button>
    </div>

    <ul>

        <?php foreach ($matchs as $match): ?>
            <?php

                // Déterminer le résultat du match en fonction des scores
                if ($match['score_equipe1'] > $match['score_equipe2']) {
                    // Si l'équipe 1 gagne
                    $result = 'V';
                    $resultClass = 'win';  // Appliquer la classe pour la victoire
                } elseif ($match['score_equipe1'] < $match['score_equipe2']) {
                    // Si l'équipe 2 gagne
                    $result = 'L';
                    $resultClass = 'lose';  // Appliquer la classe pour la défaite
                } else {
                    // Si c'est un match nul
                    $result = 'N';
                    $resultClass = 'draw';  // Appliquer la classe pour le match nul
                }

                // Vérifie si le match concerne l'équipe sélectionnée (ex: $equipe_id)
                if ($match['equipe1_id'] == $equipe_id) {
                    // Si l'équipe 1 est l'équipe sélectionnée, le résultat est basé sur l'équipe 1
                    $result = ($match['score_equipe1'] > $match['score_equipe2']) ? 'V' : (($match['score_equipe1'] < $match['score_equipe2']) ? 'L' : 'N');
                    $resultClass = ($result === 'V') ? 'win' : (($result === 'L') ? 'lose' : 'draw');
                } elseif ($match['equipe2_id'] == $equipe_id) {
                    // Si l'équipe 2 est l'équipe sélectionnée, le résultat est basé sur l'équipe 2
                    $result = ($match['score_equipe2'] > $match['score_equipe1']) ? 'V' : (($match['score_equipe2'] < $match['score_equipe1']) ? 'L' : 'N');
                    $resultClass = ($result === 'V') ? 'win' : (($result === 'L') ? 'lose' : 'draw');
                }
            ?>

            <li class="match-item">
                <span class="date"><?php echo date('d/m/Y', strtotime($match['date'])); ?></span>
                <span class="team">
                    <img src="../assets/images/<?php echo $match['logo_equipe1']; ?>" alt="Logo de l'équipe 1" style="width: 30px; height: 30px;">
                </span>
                <span class="score"><?php echo $match['score_equipe1']; ?></span> -
                <span class="score"><?php echo $match['score_equipe2']; ?></span>
                <span class="team">
                    <img src="../assets/images/<?php echo $match['logo_equipe2']; ?>" alt="Logo de l'équipe 2" style="width: 30px; height: 30px;">
                </span>
                <span class="result <?php echo $resultClass; ?>">  <!-- Ajouter la classe CSS pour le résultat -->
                    <?php echo $result; ?> <!-- Afficher le résultat comme V, L, ou N -->
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</section>


    <section class="match-info">





    <?php if ($next_match): ?>
        <div  style="    border: 2px solid gray;   border-radius: 10px; padding: 10px; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);">
        <h3 >Next Match <i class="fa-solid fa-arrow-right fa-bounce" style="color: #FFD43B;"></i></h3>
        <div class="match-details">
            <!-- Afficher le logo de la première équipe -->
            <div class="team-logo" style="    background: none !important;
">
                <img src="../assets/images/<?php echo $logo1; ?>" alt="Logo de l'équipe 1">
            </div>

            <!-- Afficher l'heure du match -->
            <span class="time"><?php echo date('H:i', strtotime($next_match['date_match'])); ?></span>

            <!-- Afficher le logo de la deuxième équipe -->
            <div class="team-logo">
                <img src="../assets/images/<?php echo $logo2; ?>" alt="Logo de l'équipe 2">
            </div>
        </div>

        <div class="match-details-info">
            <p><strong>Jour:</strong> <?php echo date('l, d F Y', strtotime($next_match['date_match'])); ?></p>
        </div>
    <?php else: ?>
        <p>Aucun prochain match programmé pour cette équipe.</p>
    <?php endif; ?>
    </div>
    <br>

    <h4>Classement <i class="fa-solid fa-medal" style="color: #FFD43B;"></i></h4>
<table class="ranking-table">
    <tr>
        <th><i class="fa-solid fa-ranking-star fa-bounce" style="color: #FFD43B;"></i></th>
        <th>Équipe</th>
        <th>Récents</th>
        <th>Pts</th>
    </tr>

    <?php
    // Récupérer le classement des équipes, trié par points et id
    $stmt_classement = $pdo->prepare("SELECT classement.id, classement.points, classement.recentes,equipe.nom, equipe.id AS equipe_id
                                      FROM classement
                                      JOIN equipe ON classement.equipe_id = equipe.id
                                      ORDER BY classement.points DESC, classement.id ASC");
    $stmt_classement->execute();

    $rank = 1; // Initialiser le rang
    while ($row = $stmt_classement->fetch(PDO::FETCH_ASSOC)):
        // Récupérer les données du classement
        $points = $row['points'];
        $equipe_id = $row['equipe_id']; // ID de l'équipe
        $recentes = explode(',', $row['recentes']); // Convertir la chaîne en tableau


        // Récupérer le logo de l'équipe
        $stmt_logo = $pdo->prepare("SELECT logo FROM equipe WHERE id = ?");
        $stmt_logo->execute([$equipe_id]);
        $logo = $stmt_logo->fetchColumn(); // Récupérer le logo de l'équipe
    ?>
        <tr>
            <td><?php echo $rank; ?></td> <!-- Affichage du rang basé sur la table classement -->
            <td><img src="../assets/images/<?php echo $logo; ?>" alt="Logo de l'équipe"></td> <!-- Affichage du logo -->
            <td class="form">
            <?php foreach ($recentes as $result): ?>
                <?php if ($result === 'V'): ?>
                    <span class="win">V</span>
                <?php elseif ($result === 'L'): ?>
                    <span class="lose">L</span>
                <?php elseif ($result === 'N'): ?>
                    <span class="draw">N</span>
                <?php endif; ?>
            <?php endforeach; ?>
        </td>
            <td><?php echo $points; ?></td> <!-- Affichage des points -->
        </tr>
    <?php
        $rank++; // Incrémenter le rang pour la prochaine équipe
    endwhile;
    ?>
</table>




</div>

    </section>
        <!-- Trophées avec images -->
        <div class="mt-3">
            <br>
    <h4>Trophées 🏆</h4>
    <br>
    <div class="trophy-container">
        <?php foreach ($trophee_images as $trophee => $image): ?>
            <?php if ($trophees[$trophee] > 0): ?>
                <div class="trophy">
                    <img src="../assets/images/<?php echo $image; ?>" alt="<?php echo ucfirst(str_replace("_", " ", $trophee)); ?>">
                    <p>x<?php echo $trophees[$trophee]; ?></p>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
<br>

<div class="container">
    <!-- Section des joueurs -->
    <div class="players-section">
        <h4>Joueurs <i class="fa-solid fa-person-walking fa-bounce" style="color: #FFD43B;"></i></h4>
        <ul class="players-list">
            <?php if ($joueurs): ?>
                <?php foreach ($joueurs as $joueur): ?>
                    <li class="player-item">
                        <a href="joueur.php?id=<?php echo $joueur['id']; ?>" class="player-link">
                            <img src="../assets/images/football-player.png" class="player-img" alt="">
                            <span class="player-name"><?php echo $joueur['prenom'] . ' ' . $joueur['nom']; ?></span>
                        </a>
                        <span class="role"><?php echo $joueur['role']; ?></span>

                        <!-- Affichage de la moyenne avec couleur dynamique -->
                        <span class="moyenne <?php echo getMoyenneClass($joueur['moyenne_note']); ?>">
                            <?php echo number_format($joueur['moyenne_note'], 1); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="no-player">Aucun joueur enregistré</li>
            <?php endif; ?>
        </ul>
    </div>



    <!-- Section des informations de l'équipe -->
    <div class="team-info-section">
        <h4>Informations de l'Équipe <i class="fa-solid fa-circle-info" style="color: #FFD43B;"></i></h4>
        <?php if ($informations): ?>
            <ul class="info-list">
                <?php foreach ($informations as $info): ?>
                    <li class="info-item">
                        <strong>Information :</strong> <?php echo htmlspecialchars($info['information']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucune information disponible pour cette équipe.</p>
        <?php endif; ?>
    </div>

</div>
<div class="stats">
        <canvas id="statsRadarChart"></canvas> <!-- C'est ici que le graphique sera dessiné -->
    </div>




        </div>
    </div>
</div>

</body>
</html>
<script>
   document.addEventListener("DOMContentLoaded", function() {
    const prevBtn = document.querySelector(".prev");
    const nextBtn = document.querySelector(".next");
    const matchList = document.querySelector(".match-results ul");

    let index = 0;
    const matches = matchList.querySelectorAll(".match-item"); // Utilisation de querySelectorAll pour les éléments <li> de chaque match
    const matchesPerPage = 3;

    function showMatches() {
        for (let i = 0; i < matches.length; i++) {
            matches[i].style.display = (i >= index && i < index + matchesPerPage) ? "flex" : "none";
        }
    }

    prevBtn.addEventListener("click", function() {
        if (index > 0) {
            index -= matchesPerPage;
            showMatches();
        }
    });

    nextBtn.addEventListener("click", function() {
        if (index + matchesPerPage < matches.length) {
            index += matchesPerPage;
            showMatches();
        }
    });

    showMatches();
});

// Assurez-vous que les données sont récupérées en PHP et envoyées dans le script JS
const statsData = {
    buts_marques: <?php echo $stats['buts_marques']; ?>,
    defense: <?php echo $stats['defense']; ?>,
    possession: <?php echo $stats['possession']; ?>,
    passes_precises: <?php echo $stats['passes_precises']; ?>,
    tirs_cadres: <?php echo $stats['tirs_cadres']; ?>,
    duels_aeriens: <?php echo $stats['duels_aeriens']; ?>,
    occasions_creees: <?php echo $stats['occasions_creees']; ?>,
    recuperations: <?php echo $stats['recuperations']; ?>,
    contre_attaques: <?php echo $stats['contre_attaques']; ?>,
    jeu_transition: <?php echo $stats['jeu_transition']; ?>,
};

// Le nom des différents axes du graphique
const labels = [
    'Buts Marqués',
    'Défense',
    'Possession',
    'Passes Précises',
    'Tirs Cadres',
    'Duels Aériens',
    'Occasions Créées',
    'Récupérations',
    'Contre-Attaques',
    'Jeu de Transition'
];

// Créer le graphique en toile d'araignée (Radar Chart)
const ctx = document.getElementById('statsRadarChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'radar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Statistiques de l\'Équipe',
            data: Object.values(statsData),
            fill: true,
            backgroundColor: 'rgba(0, 123, 255, 0.2)', // Couleur d'arrière-plan du polygone
            borderColor: 'rgba(0, 123, 255, 1)', // Couleur de la bordure
            borderWidth: 1
        }]
    },
    options: {
        scale: {
            ticks: {
                beginAtZero: true,
                max: 100, // Ou ajuster selon les valeurs maximales attendues
                stepSize: 10
            }
        },
        responsive: true
    }
});
</script>