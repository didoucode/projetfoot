<?php
include "../config/db.php";

if (isset($_POST['query'])) {
    $search = "%" . $_POST['query'] . "%"; // Ajout des wildcards pour la recherche partielle

    $stmt = $pdo->prepare("
        SELECT m.*, e1.nom AS equipe1, e2.nom AS equipe2 
        FROM matchs m
        JOIN equipe e1 ON m.equipe1_id = e1.id
        JOIN equipe e2 ON m.equipe2_id = e2.id
        WHERE CONCAT(e1.nom, ' vs ', e2.nom) LIKE ?
        OR CONCAT(e2.nom, ' vs ', e1.nom) LIKE ?");

    $stmt->execute([$search, $search]);
    $matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($matchs) > 0) {
        foreach ($matchs as $match) {
            echo "
            <div class='match-card p-3 mt-2'>
                <h4>{$match['equipe1']} vs {$match['equipe2']}</h4>
                <p><strong>Score :</strong> {$match['score_equipe1']} - {$match['score_equipe2']}</p>
            </div>";
        }
    } else {
        echo "<p class='text-muted'>Aucun match trouvé.</p>";
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard  - Espace Utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color:rgb(29, 36, 33);
            --secondary-color:rgb(3, 88, 46);
            --accent-color: #3498db;
            --background-color: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(5, 80, 34, 0.1);
        }

        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .navbar {
            background: var(--primary-color) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 600;
            color: #fff !important;
        }

        .nav-link {
            position: relative;
            padding: 0.5rem 1rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1rem;
            right: 1rem;
            height: 2px;
            background-color: var(--accent-color);
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 1rem 1rem;
        }

        .match-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease;
        }

        .match-card:hover {
            transform: translateY(-5px);
        }

        .team-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .score {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .vote-btn {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            transition: all 0.3s ease;
        }

        .vote-btn:hover {
            transform: scale(1.05);
        }

        .chat-box {
            background: white;
            border-radius: 1rem;
            padding: 1rem;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: var(--card-shadow);
        }

        .message {
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 1rem;
            background: #f8f9fa;
        }

        .loading-spinner {
            width: 3rem;
            height: 3rem;
        }

        @media (max-width: 768px) {
            .match-card {
                padding: 1rem;
            }
            
            .team-logo {
                width: 40px;
                height: 40px;
            }
            
            .score {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    
    <?php session_start(); include "../config/db.php";
    if (!isset($_SESSION['user_id'])) { header("Location: auth.php"); exit(); }
    $user_id = $_SESSION['user_id']; ?>

    <!-- Navbar améliorée -->
    <nav class="navbar navbar-expand-lg navbar-dark">
    <img src="../assets/images/logo.jpg" alt="Connexion" id="login-btn" style="  width: 80px;">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-trophy me-2"></i>Dashboard 
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user me-1"></i>Profil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user_joueurs.php">
                            <i class="fas fa-users me-1"></i>Joueurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="equipes.php">
                            <i class="fas fa-futbol me-1"></i>Équipes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-home me-2"></i>Acceuil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- En-tête du dashboard -->
    <header class="dashboard-header">
        <div class="container">
            <h1 class="text-center mb-0">
                <i class="fas fa-home me-2"></i>Bienvenue sur votre espace
            </h1>
        </div>
    </header>

    <!-- Contenu principal -->
    <main class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fa-regular fa-futbol"></i>Matchs et résultats</h2>
                    <form class="d-flex" role="search">
                       <input class="form-control me-2" type="search" id="search" placeholder="Rechercher un match..." aria-label="Search">
                       <button class="btn btn-outline-success" type="button">Rechercher</button>
                    </form>

                     <div id="search-results" class="mt-3"></div>
                </div>

                <!-- Zone de chargement -->
                <div id="loading" class="text-center d-none">
                    <div class="spinner-border loading-spinner text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>

                <!-- Liste des matchs -->
                <div id="matches-list">
                    <!-- Le contenu sera chargé via AJAX -->
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            function loadMatches(filter = 'all') {
                $('#loading').removeClass('d-none');
                $('#matches-list').addClass('d-none');
                
                $.get("matches.php", { filter: filter }, function(data) {
                    $("#matches-list").html(data);
                    $('#loading').addClass('d-none');
                    $('#matches-list').removeClass('d-none');
                });
            }

            // Chargement initial
            loadMatches();

            // Fonction de filtrage
            window.filterMatches = function(filter) {
                loadMatches(filter);
            }

            // Rafraîchissement automatique pour les matchs en direct
            setInterval(function() {
                if($('.match-card[data-status="live"]').length > 0) {
                    loadMatches('live');
                }
            }, 60000); // Rafraîchit toutes les minutes
        });

     


    </script>
</body>
</html>