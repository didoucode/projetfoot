<?php

  session_start();
  include "../config/db.php";


// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si non authentifié
    header("Location: auth.php");
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$query = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Vérifier si on a bien récupéré l'utilisateur
if (!$user) {
    // Détruire la session et rediriger en cas de problème
    session_destroy();
    header("Location: auth.php");
    exit();
}

$nom_utilisateur = htmlspecialchars($user['username']); // Protection contre les injections XSS

  ?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home -  Fooball Atlass</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
<body>
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content">
   
    <header>
    <!-- Barre de navigation -->
    <?php
$base_path = (strpos($_SERVER['SCRIPT_FILENAME'], '/pages/') !== false) ? '../' : '';
?>
<link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">


    <nav class="navbar navbar-expand-lg navbar-light shadow-sm mb-4" style="background-color: #004d00;">
        <div class="container-fluid">
            <a class="navbar-brand text-white fw-bold" href="#">
            <?php
// Vérifier si la page est dans le dossier "pages"
$path = (strpos($_SERVER['SCRIPT_FILENAME'], '/pages/') !== false) ? '../' : '';
?>
<img src="<?php echo $path; ?>assets/images/logo.jpg" alt="Logo"

                style="height: 40px; margin-right: 10px;">
                Foot Atlass
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="#"></a></li>
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="#">Coupe du Trône</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="#">Actualité</a></li>
                <li class="nav-item"><a class="nav-link btn btn-success text-white" href="/site_football/pages/auth.php">Se connecter</a></li>
                <li class="nav-item"><a class="nav-link btn btn-success text-white" href="/site_football/pages/logout.php"> Deconnexion</a></li>
            </ul>
        </div>
    </nav>
</header>


        <!-- Header Banner -->
        <div class="header-banner mb-4">
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
            font-family: 'Arial', sans-serif;
            overflow-x: hidden;
        }
        
        .landing-container {
            height: 100vh;
            position: relative;
            background-color: var(--blanc);
        }
        
        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 60%;
            height: 100%;
            /* Remplacez cette URL par le chemin vers votre image locale */
            background-image: url('../assets/images/back2.jpg');
            background-size: cover;
            background-position: center;
            clip-path: polygon(0 0, 100% 0, 70% 100%, 0 100%);
            z-index: 0;
        }
        
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 60%;
            height: 100%;
            background-color: rgba(0, 122, 51, 0.3);
            clip-path: polygon(0 0, 100% 0, 70% 100%, 0 100%);
            z-index: 1;
        }
        
        .content {
            position: relative;
            z-index: 2;
            height: 100%;
        }
        
        .header {
            padding: 20px;
            color: var(--vert);
        }
        
        .main-content {
            padding: 20px;
        }
        
        .title {
            color: var(--vert);
            font-weight: 800;
            font-size: 3.5rem;
            text-transform: uppercase;
            line-height: 1.1;
        }
        
        .subtitle {
            color: var(--noir);
            font-size: 1rem;
            margin-top: 20px;
        }
        
        .circle-button {
            width: 60px;
            height: 60px;
            background-color: var(--vert);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blanc);
            font-size: 1.5rem;
            margin: 30px 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .circle-button:hover {
            background-color: var(--jaune);
            color: var(--noir);
        }
        
        .footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            padding: 0 20px;
            color: var(--vert);
            font-size: 0.9rem;
        }
        
        .grid-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 60%;
            height: 100%;
            background-image: 
                /* Remplacez cette URL par le chemin vers votre image locale */
                url('votre-image-grid.jpg'),
                linear-gradient(to right, rgba(0, 122, 51, 0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0, 122, 51, 0.1) 1px, transparent 1px);
            background-size: cover, 20px 20px, 20px 20px;
            background-position: center, 0 0, 0 0;
            clip-path: polygon(0 0, 100% 0, 70% 100%, 0 100%);
            z-index: 1;
        }
        
        .welcome-section {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-section h2 {
            color:#222;;
            font-weight: 100;
            margin-bottom: 10px;
        }
        
        .welcome-section p {
            color: var(--noir);
        }

        /* Style pour le bouton de discussion */
.bi.bi-chat-dots {
  display: inline-block;
  margin: 10px 0;
}

.bi.bi-chat-dots a {
  display: flex;
  align-items: center;
  background-color:var(--jaune) ;
  color: var(--blanc);
  padding: 10px 15px;
  border-radius: 5px;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
}

.bi.bi-chat-dots a i {
  margin-right: 8px;
  font-size: 1.1rem;
}

.bi.bi-chat-dots a:hover {
  background-color:var(--vert) ;
  color: var(--noir);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

    </style>
</head>
<body>
    <div class="landing-container">
        <div class="bg-image"></div>
        <div class="overlay"></div>
        <div class="grid-overlay"></div>
        
        <div class="content">
            <div class="header d-flex justify-content-between">
                <div class="company">Football Atlas</div>
                <div class="page-number"></div>
            </div>
            
            <div class="row h-75">
                <div class="col-md-6"></div>
                <div class="col-md-5 d-flex align-items-center">
                    <div class="main-content">
                        <h1 class="title">Bienvenu <?php echo $nom_utilisateur; ?></h1>
                        <div class="bi bi-chat-dots">
                        <a href="discussion.php" class="bi bi-chat-dots">
                         <i class="bi bi-chat-dots"></i> Discussion 
                        </a>
                        </div>
                        <main>
                            <section class="welcome-section">
                            <h5> Bienvenue dans votre espace personnel. Suivez l'actualité de votre équipe, connecté avec la communauté des passionnés de football ! </h5>
                                <p></p>
                            </section>
                        </main>
                    </div>
                </div>
            </div>
            
            <div class="footer d-flex justify-content-between">
                <div>Presented by team fottAtlass</div>
                <div>Getting Started</div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>