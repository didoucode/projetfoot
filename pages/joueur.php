<?php
session_start();
include "../config/db.php";

// Vérifier si un ID est passé en paramètre
if (!isset($_GET['id'])) {
    header("Location: joueurs.php");
    exit;
}

$id = (int) $_GET['id'];
$joueur = $pdo->prepare("SELECT joueurs.*, equipe.nom AS equipe_nom FROM joueurs
                         LEFT JOIN equipe ON joueurs.equipes = equipe.id
                         WHERE joueurs.id = ?");
$joueur->execute([$id]);
$joueur = $joueur->fetch();

if (!$joueur) {
    header("Location: joueurs.php");
    exit;
}

// Définir un nombre de buts par défaut si non défini
$goals = isset($joueur['goals']) ? (int)$joueur['goals'] : 0;

// Calculer la progression (max 100)
$progression = min(100, $goals * 5); // Supposons que 20 buts = 100% progression

// Définir une image par défaut si non définie
$image = !empty($joueur['image']) ? $joueur['image'] : 'tenu2.jpeg';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du Joueur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: black;
            color: #fff;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: green;
            position: relative;
        }

        .navbar a {
            color: #fff;
            font-weight: bold;
        }

        .navbar a:hover {
            color: #FFD700;
        }

        .navbar-right {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .navbar-image {
            height: 30px;
            width: auto;
        }

        .container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            margin-top: 50px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            position: relative;
            border: 3px solid green;
        }

        .player-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            display: block;
            margin: 0 auto;
            border: 3px solid green;
            position: relative;
        }

        /* Icône emoji de la caméra en haut à droite */
        .camera-icon {
    position: absolute;
    bottom: 10px; /* Déplacer un peu plus bas */
    left: 10px; /* Déplacer un peu plus à droite */
    background-color: rgba(0, 0, 0, 0.6);
    border-radius: 50%;
    padding: 5px;
    cursor: pointer;
}



        h1 {
            text-align: center;
            color: darkgreen;
            font-size: 2rem;
            margin-top: 15px;
            padding: 10px;
            border-radius: 8px;
        }

        .list-group {
            padding: 15px;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.1);
            margin-top: 20px;
        }

        .list-group-item {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: none;
        }

        .progress-container {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            height: 30px;
            margin-top: 10px;
        }

        .progress-bar {
            height: 100%;
            width: <?= $progression ?>%;
            background: linear-gradient(90deg, #ff0000, rgb(22, 94, 22));
            border-radius: 25px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            animation: progressAnimation 2s ease-in-out;
        }

        .progress-bar span {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
        }

        @keyframes progressAnimation {
            0% {
                width: 0;
            }
            100% {
                width: <?= $progression ?>%;
            }
        }

        .btn-action {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-back {
            background-color: #808080;
            color: #fff;
            position: fixed;
            bottom: 20px;
            left: 20px;
            width: auto;
            padding: 10px 20px;
            border: none;
        }

        .btn-back:hover {
            background-color: #666666;
        }

        .btn-edit {
            background-color: #008000;
            color: #fff;
            border: 3px solid green;
        }

        .btn-edit:hover {
            background-color: #006600;
        }
    </style>
</head>
<body>

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="index.php">Accueil</a>
        <div class="navbar-right">
        <img src="../assets/images/logo.jpg" alt="Connexion" id="login-btn" style="  width: 80px;">
        </div>
    </nav>

    <div class="container mt-5 text-center">
        <!-- Image du joueur avec l'icône emoji caméra -->
        <div style="position: relative; display: inline-block;">
            <img src="<?= htmlspecialchars($image) ?>" alt="" class="player-image">
            <label for="upload_image" class="camera-icon">
                📸 <!-- Emoji caméra -->
            </label>
            <input type="file" id="upload_image" style="display: none;" accept="image/*" onchange="uploadImage(event)">
        </div>

        <h1><?= htmlspecialchars($joueur['nom']) . ' ' . htmlspecialchars($joueur['prenom']) ?></h1>

        <ul class="list-group mt-3">
            <li class="list-group-item"><strong>Origine:</strong> <?= htmlspecialchars($joueur['origine']) ?></li>
            <li class="list-group-item"><strong>Nationalité:</strong> <?= htmlspecialchars($joueur['nationalite']) ?></li>
            <li class="list-group-item"><strong>Date de Naissance:</strong> <?= htmlspecialchars($joueur['date_naissance']) ?></li>
            <li class="list-group-item"><strong>Clubs:</strong> <?= htmlspecialchars($joueur['clubs']) ?></li>
            <li class="list-group-item"><strong>Équipes:</strong> <?= htmlspecialchars($joueur['equipe_nom']) ?></li>
            <li class="list-group-item"><strong>Rôle:</strong> <?= htmlspecialchars($joueur['role']) ?></li>
            <li class="list-group-item"><strong>Nombre de buts:</strong> <?= $goals ?></li>
        </ul>

        <!-- Barre de progression -->
        <div class="mt-4">
            <label for="progression" class="form-label"><strong>Progression :</strong></label>
            <div class="progress-container">
                <div class="progress-bar">
                    <span><?= $progression ?>%</span>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <a href="modifier_joueur.php?id=<?= $joueur['id'] ?>" class="btn-action btn-edit">Modifier</a>
    </div>

    <!-- Bouton Retour -->
    <a href="joueurs.php" class="btn-action btn-back">Retour</a>

    <script>
        function uploadImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const image = reader.result;
                document.querySelector('.player-image').src = image; // Met à jour l'image du joueur
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

</body>
</html>
