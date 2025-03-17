<?php
session_start();
include "../config/db.php"; // Connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare("INSERT INTO joueurs (nom, prenom, origine, nationalite, date_naissance, clubs, equipes, role) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([ 
        $_POST['nom'], $_POST['prenom'], $_POST['origine'], $_POST['nationalite'],
        $_POST['date_naissance'], $_POST['clubs'], $_POST['equipes'], $_POST['role']
    ]);
    header("Location: joueurs.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Joueur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #212121; /* Gris foncé */
            font-family: 'Arial', sans-serif;
            color: #fff;
            background-image: url('img1.jpeg'); /* Chemin de votre image */
            background-size: cover; /* L'image couvre toute la surface de la page */
            background-attachment: fixed; /* L'image reste fixée lors du défilement */
            animation: moveBackground 10s linear infinite; /* Animation pour déplacer l'image */
        }

        /* Animation pour faire défiler l'image */
        @keyframes moveBackground {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 100% 100%;
            }
        }

        .container {
            background-color: #2c2c2c; /* Fond légèrement plus clair que le fond de la page */
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            margin-top: 50px;
            border: 3px solid #00FF00; /* Bordure vert fluo */
        }

        h1 {
            text-align: center;
            color: #00FF00; /* Vert lumineux pour l'en-tête */
            font-size: 2rem;
            margin-bottom: 30px;
        }

        .form-label {
            font-size: 1rem;
            font-weight: bold;
            color: #ccc;
        }

        .form-control {
            border-radius: 5px;
            padding: 10px;
            font-size: 1rem;
            border: 2px solid #FF6600; /* Bordure orange fluo pour chaque champ */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            background-color: #333;
            color: #fff;
        }

        .form-control:focus {
            border-color: #FF6600; /* Bordure orange fluo sur focus */
            box-shadow: 0 0 5px rgba(255, 102, 0, 0.6);
        }

        .btn-custom {
            background-color: #00FF00; /* Vert fluo pour le bouton "Ajouter" */
            color: #222;
            border: none;
            padding: 12px 20px;
            font-size: 1rem;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
            width: 100%;
        }

        .btn-custom:hover {
            background-color: #00cc00;
            transform: scale(1.05);
        }

        .btn-custom:active {
            background-color: #00cc00;
            transform: scale(0.98);
        }

        .btn-back {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        .btn-back:hover {
            background-color: #555;
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn-group a, .btn-group button {
            width: 48%;
        }

        .btn-right {
            margin-left: auto;
        }

        /* Navbar en vert foncé */
        .navbar-custom {
            background-color: #1a3a28; /* Vert foncé */
        }

        .navbar-custom .navbar-brand, 
        .navbar-custom .nav-link {
            color: white; /* Texte en blanc */
        }

        .navbar-custom .navbar-brand:hover, 
        .navbar-custom .nav-link:hover {
            color: #a5d6a7; /* Effet de survol vert clair */
        }

    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Accueil</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="joueurs.php">Joueurs</a>
                    </li>
                </ul>
            </div>
            <!-- Ajout de l'image à droite dans la navbar -->
            <img src="../assets/images/logo.jpg" alt="Connexion" id="login-btn" style="  width: 80px;">
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Ajouter un Joueur</h1>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" placeholder="Entrez le nom du joueur" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" placeholder="Entrez le prénom du joueur" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Origine</label>
                <input type="text" name="origine" class="form-control" placeholder="Entrez l'origine du joueur" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nationalité</label>
                <input type="text" name="nationalite" class="form-control" placeholder="Entrez la nationalité du joueur" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Date de Naissance</label>
                <input type="date" name="date_naissance" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Clubs</label>
                <input type="text" name="clubs" class="form-control" placeholder="Entrez les clubs du joueur">
            </div>
            <div class="mb-3">
                <label class="form-label">Équipes</label>
                <input type="text" name="equipes" class="form-control" placeholder="Entrez les équipes du joueur">
            </div>
            <div class="mb-3">
                <label class="form-label">Rôle</label>
                <input type="text" name="role" class="form-control" placeholder="Entrez le rôle du joueur">
            </div>
            <div class="btn-group">
                <a href="joueurs.php" class="btn-back">Retour à la liste des joueurs</a>
                <button type="submit" class="btn-custom btn-right">Ajouter</button>
            </div>
        </form>
    </div>
</body>
</html>
