<?php
session_start();
include "../config/db.php";

// Récupérer les équipes
$sql_equipes = "SELECT * FROM equipe";
$result_equipes = $pdo->query($sql_equipes);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Équipes</title>
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

        /* Animation du titre */
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

        /* Animation des cartes */
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

        /* Effet de survol sur les cartes */
        .card:hover {
            border-color: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center mb-4">Liste des Équipes</h1>
    
    <div class="row">
        <?php while ($equipe = $result_equipes->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($equipe['nom']); ?></h5>
                        <p><strong>Ville :</strong> <?php echo htmlspecialchars($equipe['ville']); ?></p>
                        <p><strong>Entraîneur :</strong> <?php echo htmlspecialchars($equipe['entraineur']); ?></p>
                        <p><strong>Création :</strong> <?php echo htmlspecialchars($equipe['date_creation']); ?></p>
                        
                        <h6 class="text-success">Joueurs :</h6>
                        <ul class="joueurs-list">
                            <?php
                            $equipe_id = $equipe['id'];
                            $sql_joueurs = "SELECT nom FROM joueurs WHERE equipes = ?";
                            $stmt_joueurs = $pdo->prepare($sql_joueurs);
                            $stmt_joueurs->execute([$equipe_id]);

                            if ($stmt_joueurs->rowCount() > 0) {
                                while ($joueur = $stmt_joueurs->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<li>" . htmlspecialchars($joueur['nom']) . "</li>";
                                }
                            } else {
                                echo "<li>Aucun joueur</li>";
                            }
                            ?>
                        </ul>

                        <!-- Bouton Modifier -->
                        <a href="modifier_equipe.php?id=<?php echo $equipe['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>

                        <!-- Bouton Supprimer -->
                        <a href="supprimer_equipe.php?id=<?php echo $equipe['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer cette équipe ?');">Supprimer</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
<!-- Bouton Ajouter une équipe -->
<div class="text-center mt-5">
    <a href="ajouter_equipe.php" class="btn btn-success btn-lg">Ajouter une Équipe</a>
</div>

<?php
$pdo = null; // Fermer la connexion PDO
?>
