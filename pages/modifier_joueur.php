<?php
session_start();
include "../config/db.php";

// Vérifier si un ID est passé
if (!isset($_GET['id'])) {
    header("Location: joueurs.php");
    exit;
}

$id = (int) $_GET['id'];
$joueur = $pdo->prepare("SELECT * FROM joueurs WHERE id = ?");
$joueur->execute([$id]);
$joueur = $joueur->fetch();

if (!$joueur) {
    header("Location: joueurs.php");
    exit;
}

// Mettre à jour les informations du joueur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $origine = $_POST['origine'];
    $nationalite = $_POST['nationalite'];
    $date_naissance = $_POST['date_naissance'];
    $clubs = $_POST['clubs'];
    $equipes = $_POST['equipes'];
    $role = $_POST['role'];
    $goals = (int) $_POST['goals'];
    
    // Mise à jour dans la base de données
    $stmt = $pdo->prepare("UPDATE joueurs SET nom=?, prenom=?, origine=?, nationalite=?, date_naissance=?, clubs=?, equipes=?, role=?, goals=? WHERE id=?");
    $stmt->execute([$nom, $prenom, $origine, $nationalite, $date_naissance, $clubs, $equipes, $role, $goals, $id]);
    
    // Redirection vers joueur.php avec l'ID du joueur
    header("Location: joueur.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Joueur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Image de fond */
        body {
            background: url('../assets/images/img1.jpeg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

        /* Style du formulaire */
        .container {
            background: rgba(0, 0, 0, 0.6); /* Fond semi-transparent */
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            margin-top: 50px;
        }

        /* Champs transparents */
        .form-control {
            background: transparent;
            border: 2px solid #28a745; /* Bordure verte */
            color: white;
        }

        /* Style lorsqu'un champ est en focus */
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.8);
            background-color: #fff; /* Fond blanc lorsqu'en focus */
            color: #000; /* Texte en noir pour une meilleure visibilité */
        }

        /* Bouton "Enregistrer" */
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
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
                <img src="../assets/images/logo.jpg" alt="Connexion" id="login-btn" style="  width: 80px;"> 
            </div>
          
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center">Modifier Joueur</h1>
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($joueur['nom']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($joueur['prenom']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Origine</label>
                <input type="text" name="origine" class="form-control" value="<?= htmlspecialchars($joueur['origine']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Nationalité</label>
                <input type="text" name="nationalite" class="form-control" value="<?= htmlspecialchars($joueur['nationalite']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Date de Naissance</label>
                <input type="date" name="date_naissance" class="form-control" value="<?= htmlspecialchars($joueur['date_naissance']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Clubs</label>
                <input type="text" name="clubs" class="form-control" value="<?= htmlspecialchars($joueur['clubs']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Équipes</label>
                <input type="text" name="equipes" class="form-control" value="<?= htmlspecialchars($joueur['equipes']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Rôle</label>
                <input type="text" name="role" class="form-control" value="<?= htmlspecialchars($joueur['role']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre de Buts</label>
                <input type="number" name="goals" class="form-control" value="<?= htmlspecialchars($joueur['goals']) ?>">
            </div>
            <button type="submit" class="btn btn-success w-100">Enregistrer</button>
        </form>
    </div>
</body>
</html>
