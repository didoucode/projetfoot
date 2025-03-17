
<?php

  session_start();
  include "../config/db.php";

  
    
    // Récupérer toutes les équipes
    $stmt_equipes = $pdo->query("SELECT id, nom FROM equipe ORDER BY id");


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Équipes - Foot Atlass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: #004D00 !important;
        }
        .navbar .nav-link {
            color: white !important;
            font-weight: bold;
        }
        .navbar .nav-link:hover {
            color: var(--vert-clair) !important;
        }

        .sidebar {
            background-color: #004D00;
            height: 100vh;
            position: fixed;
            width: 60px;
            padding-top: 20px;
        }
     
        .sidebar a {
            display: block;
            text-align: center;
            padding: 15px 0;
            color: white;
            text-decoration: none;
        }
        
          /* Contenu principal */
          .content {
            margin-left: 100px;
            padding: 20px;
        }

        .main-content {
            margin-left: 75px;
        }
        .team-container {
            background-color: white;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .team-header {
            background-color: #006400;
            color: white;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .team-logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            margin-right: 15px;
        }
        .team-players {
            padding: 0;
            display: none;
        }
        .player-item {
            padding: 10px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        .player-item:last-child {
            border-bottom: none;
        }
        .player-item a {
            color: #333;
            text-decoration: none;
        }
        .player-item a:hover {
            color: #006400;
        }
        .player-role {
            background-color: #f0f0f0;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .header-banner {
            background-image: url('../assets/images/back.jpg');
            background-size: cover;
            color: white;
            padding: 80px 0;
            position: relative;
        }
        .header-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .header-content {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="index.php"><i class="fas fa-home fa-2x"></i></a>
        <a href="competitions.php"><i class="fas fa-trophy fa-2x"></i></a>
        <a href="statistiques.php"><i class="fas fa-chart-line fa-2x"></i></a>
        <a href="profil.php"><i class="fas fa-user fa-2x"></i></a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="../assets/images/logo.jpg" alt="Foot Atlass" height="30"> Foot Atlass
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="botola.php">Botola Pro</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="coupe.php">Coupe du Trône</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="equipes.php">Équipes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Se connecter</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Header Banner -->
        <div class="header-banner mb-4">
            <div class="header-overlay"></div>
            <div class="container header-content">
                <h1>Équipes</h1>
                <p>Découvrez toutes les équipes et leurs joueurs</p>
            </div>
        </div>

        <!-- Équipes et Joueurs -->
        <div class="container py-4">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    while ($row_equipe = $stmt_equipes->fetch()) {
                        $equipe_id = $row_equipe['id'];
                        $equipe_nom = $row_equipe['nom'];
                    ?>
                    <div class="team-container">
                        <div class="team-header" onclick="toggleTeam(<?php echo $equipe_id; ?>)">
                            <div class="d-flex align-items-center">
                                <img src="logo/<?php echo $equipe_id; ?>.jpg" alt="<?php echo $equipe_nom; ?>" class="team-logo">
                                <h4 class="mb-0"><?php echo $equipe_nom; ?></h4>
                            </div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <ul class="list-unstyled team-players" id="team-<?php echo $equipe_id; ?>">
                            <?php
                            // Récupérer les joueurs de cette équipe avec PDO
                            $stmt_joueurs = $pdo->prepare("SELECT id, nom, prenom, role FROM joueurs WHERE clubs = ? ORDER BY role, nom");
                            $stmt_joueurs->execute([$equipe_nom]);
                            
                            if ($stmt_joueurs->rowCount() > 0) {
                                while ($row_joueur = $stmt_joueurs->fetch()) {
                                    $joueur_id = $row_joueur['id'];
                                    $joueur_nom = $row_joueur['nom'];
                                    $joueur_prenom = $row_joueur['prenom'];
                                    $joueur_role = $row_joueur['role'];
                            ?>
                            <li class="player-item">
                                <a href="joueur.php?id=<?php echo $joueur_id; ?>">
                                    <?php echo $joueur_prenom . ' ' . $joueur_nom; ?>
                                </a>
                                <p class="player-role"><?php echo $joueur_role; ?></p>
                            </li>
                            <?php
                                }
                            } else {
                                echo '<li class="player-item">Aucun joueur enregistré</li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <?php
                    }
                    
                    // Si aucune équipe n'est trouvée
                    if ($stmt_equipes->rowCount() == 0) {
                        echo "<p>Aucune équipe trouvée</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleTeam(teamId) {
            const teamPlayers = document.getElementById('team-' + teamId);
            if (teamPlayers.style.display === 'block') {
                teamPlayers.style.display = 'none';
            } else {
                teamPlayers.style.display = 'block';
            }
        }
    </script>
</body>
</html>
