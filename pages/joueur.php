<?php
session_start();
include "../config/db.php";

$isAuthenticated = isset($_SESSION['user_id']); 


if (!isset($_GET['id'])) {
    header("Location: joueurs.php");
    exit;
}

$id = (int) $_GET['id'];

$joueur = $pdo->prepare("SELECT 
                            joueurs.*, 
                            joueurs.image,  -- Ajout de l'image du joueur
                            equipe.nom AS equipe_nom, 
                            equipe.logo AS equipe_logo 
                         FROM joueurs
                         LEFT JOIN equipe ON joueurs.equipes = equipe.id
                         WHERE joueurs.id = ?");
$joueur->execute([$id]);
$joueur = $joueur->fetch();

if (!$joueur) {
    header("Location: joueurs.php");
    exit;
}

// Sécurisation et assignation des valeurs
$nom = htmlspecialchars($joueur['nom'] ?? 'Inconnu');
$prenom = htmlspecialchars($joueur['prenom'] ?? '');
$nationalite = htmlspecialchars($joueur['nationalite'] ?? 'Non spécifiée');
$origine = htmlspecialchars($joueur['origine'] ?? 'Non spécifiée');
$age = isset($joueur['age']) ? (int)$joueur['age'] : 'N/A';
$poids = isset($joueur['poids']) ? number_format($joueur['poids'], 2) . ' kg' : 'N/A';
$taille = isset($joueur['taille']) ? number_format($joueur['taille'], 2) . ' m' : 'N/A';
$numero_club = isset($joueur['numero_club']) ? (int)$joueur['numero_club'] : 'N/A';
$role = htmlspecialchars($joueur['role'] ?? 'Non défini');
$goals = isset($joueur['goals']) ? (int)$joueur['goals'] : 0;
$equipe_nom = htmlspecialchars($joueur['equipe_nom'] ?? 'Sans équipe');
$equipe_logo = !empty($joueur['equipe_logo']) ? "../assets/images/" . htmlspecialchars($joueur['equipe_logo']) : "../assets/images/default-team.png";

// Gestion de l'image du joueur
$image = !empty($joueur['image']) ? "../assets/images/" . htmlspecialchars($joueur['image']) : "../assets/images/default-player.png";

// Autres statistiques avec valeurs par défaut
$expected_goals = isset($joueur['expected_goals']) ? number_format($joueur['expected_goals'], 2) : '0.37';
$passes_penalty_area = isset($joueur['passes_penalty_area']) ? number_format($joueur['passes_penalty_area'], 2) : '1.93';
$passes_forward_percentage = isset($joueur['passes_forward_percentage']) ? $joueur['passes_forward_percentage'] : '33';
$rating = isset($joueur['rating']) ? number_format($joueur['rating'], 2) : '3.67';
$saison = isset($joueur['saison']) ? htmlspecialchars($joueur['saison']) : '2023/24';

// Vérifier si l'utilisateur suit déjà ce joueur
$suivi = false;
if ($isAuthenticated) {
    $stmt = $pdo->prepare("SELECT * FROM abonnements WHERE id_utilisateur = ? AND id_cible = ? AND type_cible = 'joueur'");
    $stmt->execute([$isAuthenticated, $id]);
    $suivi = $stmt->rowCount() > 0;
}

$suivreTexte = $suivi ? "Se désabonner" : "Suivre";

// Données du joueur
$image = !empty($joueur['image']) ? "../assets/images/" . htmlspecialchars($joueur['image']) : "../assets/images/default-player.png";
$equipe_nom = htmlspecialchars($joueur['equipe_nom'] ?? 'Sans équipe');
$equipe_logo = !empty($joueur['equipe_logo']) ? "../assets/images/" . htmlspecialchars($joueur['equipe_logo']) : "../assets/images/default-team.png";
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil du Joueur | Football Maroc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --vert: #007A33;
            --beige: #F5F5DC;
            --gris: #D3D3D3;
            --blanc: #FFFFFF;
            --noir: #222;
            --jaune: #BBF000;
        }

        body {
            background-color: var(--beige);
            color: var(--noir);
            font-family: 'Arial', sans-serif;
        }

        .player-card {
            background-color: var(--blanc);
            max-width: 900px;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            overflow: hidden;
        }

        .player-image-container {
            width: 40%;
            background-color: var(--vert);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .player-image {
            width: 100%;
            border-radius: 10px;
        }

        .player-info-container {
            width: 60%;
            padding: 20px;
        }

        

        .team-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 50px;
            height: 50px;
            background-color: var(--blanc);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--vert);
        }

        .team-badge img {
            width: 85%;
            height: 85%;
            object-fit: contain;
        }

        .player-name {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--vert);
            margin-bottom: 5px;
        }

        .player-team {
            font-size: 1rem;
            color: var(--noir);
            font-weight: 600;
        }

        .season-info {
            font-size: 0.9rem;
            color: var(--noir);
            margin-bottom: 15px;
        }

        .stats-container {
            margin-top: 10px;
            background-color: var(--gris);
            padding: 15px;
            border-radius: 8px;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 5px;
            background-color: var(--blanc);
            border-radius: 5px;
            box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.1);
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--noir);
            font-weight: bold;
        }

        .stat-value {
            background-color: var(--jaune);
            color: var(--noir);
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 3px;
        }
        .team-logo {
            width: 70px;
            height: 50px;
            border-radius: 50%;
            background-color: white;
            margin: 0px 80px 100px 0px 
            display: flex;
           align-items: center;
           justify-content: center;
        }

        .contain{
            display:flex;
            flex-direction:row;
            justify-content: space-between;
        }
        @media (max-width: 768px) {
            .player-card {
                flex-direction: column;
            }
            .player-image-container, .player-info-container {
                width: 100%;
            }
        }

        .stats-container {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    background-color: var(--gris);
    padding: 15px;
    border-radius: 8px;
}

.stats-column {
    width: 48%; /* Chaque colonne prend environ la moitié de l'espace */
}

.stat-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    padding: 8px;
    background-color: var(--blanc);
    border-radius: 5px;
    box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.1);
}

/* Responsive : Sur mobile, les colonnes reviennent en une seule */
@media (max-width: 768px) {
    .stats-container {
        flex-direction: column;
        justify-content: space-between;
    }
    .stats-column {
        width: 100%;
        justify-content: space-between;
    }

    .follow-btn {
            width: 100%;
            text-align: center;
            font-weight: bold;
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            background-color: var(--vert);
            color: var(--blanc);
        }

        .follow-btn:hover {
            background-color: darkgreen;
        }
}

    </style>
</head>
<body>
   <!-- Sidebar -->
   <?php include '../includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="content">

<?php include '../includes/header.php'; ?>
    <div class="player-card">
        <div class="player-image-container">
        <img src="<?= $image ?>" alt="Photo de <?= $nom ?>" class="player-image">

        </div>

        <div class="player-info-container">
          
      
            <h1 class="player-name"><?= htmlspecialchars($joueur['nom']) . ' ' . htmlspecialchars($joueur['prenom']) ?></h1>
            <div class="contain">
            <div class="player-team"><?= htmlspecialchars($joueur['equipe_nom']) ?></div>
            <div class="team-logo">
                <img src="../assets/images/<?= htmlspecialchars($joueur['equipe_logo']) ?>" alt="Logo de <?= htmlspecialchars($joueur['equipe_nom']) ?>" class="team-logo">
            </div>
            </div>
            <div class="season-info"><?= htmlspecialchars($joueur['role']) ?> | <?= htmlspecialchars($saison) ?></div>

            <div class="stats-container">

          <div class="stats-container">
    <!-- Première colonne -->
    <div class="stats-column">
        <div class="stat-row">
            <div class="stat-label">Nationalité</div>
            <div class="stat-value"><?= htmlspecialchars($joueur['nationalite']) ?></div>
        </div>

        <div class="stat-row">
            <div class="stat-label">Origine</div>
            <div class="stat-value"><?= htmlspecialchars($joueur['origine']) ?></div>
        </div>

        <div class="stat-row">
            <div class="stat-label">Âge</div>
            <div class="stat-value"><?= htmlspecialchars($joueur['age']) ?> ans</div>
        </div>

        <div class="stat-row">
            <div class="stat-label">Poids</div>
            <div class="stat-value"><?= htmlspecialchars($joueur['poids']) ?> kg</div>
        </div>

        <div class="stat-row">
            <div class="stat-label">Taille</div>
            <div class="stat-value"><?= htmlspecialchars($joueur['taille']) ?> m</div>
        </div>
        
        <div class="stat-row">
            <div class="stat-label">Date_Naissance</div>
            <div class="stat-value"><?= htmlspecialchars($joueur['date_naissance']) ?></div>
        </div>
    </div>

    <!-- Deuxième colonne -->
    <div class="stats-column">
        <div class="stat-row">
            <div class="stat-label">Numéro</div>
            <div class="stat-value"><?= htmlspecialchars($joueur['numero_club']) ?></div>
        </div>

        <div class="stat-row">
            <div class="stat-label">Buts</div>
            <div class="stat-value"><?= htmlspecialchars($joueur['goals']) ?></div>
        </div>

        <div class="stat-row">
            <div class="stat-label">Expected Goals</div>
            <div class="stat-value"><?= htmlspecialchars($expected_goals) ?></div>
        </div>

        <div class="stat-row">
            <div class="stat-label">Passes dans la Surface</div>
            <div class="stat-value"><?= htmlspecialchars($passes_penalty_area) ?></div>
        </div>

        <div class="stat-row">
            <div class="stat-label">% Passes en Avant</div>
            <div class="stat-value"><?= htmlspecialchars($passes_forward_percentage) ?>%</div>
        </div>

        <div class="stat-row">
            <div class="stat-label">Note</div>
            <div class="stat-value"><?= htmlspecialchars($rating) ?></div>
        </div>
    </div>
</div>

<div class="stat-row" style="width:100%; display: flex; justify-content: center; align-items: center;">
    <div id="follow-btn" class="stat-value" style="width:100%; text-align: center; font-weight: bold; cursor: pointer;">Suivre</div>
</div>

<script>
document.getElementById("follow-btn").addEventListener("click", function(event) {
    event.preventDefault();

    let isAuthenticated = <?= json_encode($isAuthenticated); ?>;
    if (!isAuthenticated) {
        window.location.href = "/site_football/pages/auth.php";
        return;
    }

    let button = this;
    let idCible = button.getAttribute("data-cible");
    let typeCible = button.getAttribute("data-type");
    let action = button.textContent.trim() === "Suivre" ? "subscribe" : "unsubscribe";

    fetch("suivre.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=${action}&id_cible=${idCible}&type_cible=${typeCible}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        button.textContent = (action === "subscribe") ? "Se désabonner" : "Suivre";
    });
});
</script>



</body>
</html>
