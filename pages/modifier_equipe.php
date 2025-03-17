<?php
session_start();
// Connexion à la base de données
include "../config/db.php";
// Récupérer l'ID de l'équipe depuis l'URL
$id_equipe = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_equipe > 0) {
    // Récupérer les informations de l'équipe
    $requeteEquipe = $pdo->prepare("SELECT * FROM Equipe WHERE id = ?");
    $requeteEquipe->execute([$id_equipe]);
    $equipe = $requeteEquipe->fetch();

    // Vérifier si l'équipe existe
    if (!$equipe) {
        die("Équipe non trouvée.");
    }

    // Récupérer les joueurs de l'équipe
    $requeteJoueurs = $pdo->prepare("SELECT * FROM joueurs WHERE equipes = ?");
    $requeteJoueurs->execute([$id_equipe]);
    $joueurs = $requeteJoueurs->fetchAll();

    // Récupérer les joueurs disponibles (sans équipe)
    $requeteJoueursDispo = $pdo->prepare("SELECT * FROM joueurs WHERE equipes IS NULL");
    $requeteJoueursDispo->execute();
    $joueursDisponibles = $requeteJoueursDispo->fetchAll();
} else {
    die("ID de l'équipe non spécifié.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Équipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Arrière-plan noir */
        body {
            background-color: #000;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #fff;
        }

        /* Conteneur principal */
        .container {
            margin-top: 50px;
        }

        /* Cartes stylisées */
        .card {
            background: #111;
            border: 1px solid #28a745;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
        }

        .card-title {
            color: #28a745;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .card-body p {
            color: #fff;
        }

        /* Liste des joueurs */
        .joueurs-list {
            list-style-type: none;
            padding-left: 0;
        }

        .joueurs-list li {
            background: rgba(40, 167, 69, 0.1);
            padding: 8px;
            margin: 5px 0;
            border-radius: 5px;
            font-size: 0.9rem;
            color: #fff;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .joueurs-list li:hover {
            background: #28a745;
            color: #000;
        }

        /* Boutons stylisés */
        .btn-warning {
            background-color: #ffc107;
            border: none;
            color: #000;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
            color: #fff;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .btn-warning:hover, .btn-danger:hover {
            opacity: 0.9;
            transform: scale(1.05);
        }

        h1 {
            animation: fadeInDown 1s ease;
            color: #28a745;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card:hover {
            border-color: #fff;
        }

        /* Position du logo */
        #logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 80px;
        }
    </style>
</head>
<body>

<!-- Logo -->
<img src="../assets/images/logo.jpg" alt="Connexion" id="login-btn" style="  width: 80px;">

<div class="container">
    <h1 class="text-center mb-4">Modifier l'équipe : <?= htmlspecialchars($equipe['nom']) ?></h1>

    <div class="row">
        <!-- Formulaire pour modifier l'équipe -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Modifier les informations de l'équipe</h5>
                    <form action="update_equipe.php" method="POST">
                        <input type="hidden" name="id" value="<?= $equipe['id'] ?>">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom de l'équipe :</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($equipe['nom']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="ville" class="form-label">Ville :</label>
                            <input type="text" name="ville" class="form-control" value="<?= htmlspecialchars($equipe['ville']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="entraineur" class="form-label">Entraîneur :</label>
                            <input type="text" name="entraineur" class="form-control" value="<?= htmlspecialchars($equipe['entraineur']) ?>">
                        </div>
                        <button type="submit" class="btn btn-warning">Enregistrer</button>
                        <a href="equipe.php" class="btn btn-secondary">Retour</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des joueurs -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Liste des Joueurs</h5>
                    <ul class="joueurs-list">
                        <?php foreach ($joueurs as $joueur): ?>
                            <li class="d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($joueur['prenom']) . " " . htmlspecialchars($joueur['nom']) ?> 
                                <a href="supprimer_joueur_equipe.php?id=<?= $joueur['id'] ?>&equipe=<?= $equipe['id'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Formulaire pour ajouter un joueur existant -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Ajouter un joueur existant</h5>
                    <form action="ajouter_joueur_equipe.php" method="POST">
                        <input type="hidden" name="id_equipe" value="<?= $equipe['id'] ?>">
                        <div class="mb-3">
                            <label for="joueur" class="form-label">Sélectionner un joueur :</label>
                            <select name="id_joueur" id="joueur" class="form-select" required>
                                <option value="">-- Choisir un joueur --</option>
                                <?php foreach ($joueursDisponibles as $joueur): ?>
                                    <option value="<?= $joueur['id'] ?>">
                                        <?= htmlspecialchars($joueur['prenom']) . " " . htmlspecialchars($joueur['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (optionnel) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>