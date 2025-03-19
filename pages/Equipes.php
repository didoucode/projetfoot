
<?php

  session_start();
  include "../config/db.php";

  
  $stmt_equipes = $pdo->query("SELECT id, nom, logo FROM equipe ORDER BY id");




?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipe -  Football Atlass</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
     

        
     
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
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content">
   
    <?php include '../includes/header.php'; ?>

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
                            <div class="d-flex align-items-center">
                            <img src="../assets/images/<?php echo htmlspecialchars($row_equipe['logo']); ?>" alt="Logo de <?php echo htmlspecialchars($row_equipe['nom']); ?>" class="team-logo">

  
                            </div>
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
