<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Joueurs - Tournois de Football</title>
  <!-- Lien Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Lien Font Awesome pour les icônes (ballon) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <style>
    /* 1. Arrière-plan entièrement noir (non modifié) */
    body {
      background-color:rgb(8, 82, 8); /* Fond noir */
      min-height: 100vh;
      margin: 0;
      padding: 0;
    }

    /* 2. Navbar personnalisée (vert) */
    .navbar-custom {
      background-color: #008000; /* Vert */
    }
    .navbar-custom .navbar-brand,
    .navbar-custom .nav-link {
      color: #fff; /* Texte blanc */
    }
    .navbar-custom .nav-link:hover {
      color: #d4d4d4;
    }

    /* 3. Conteneur principal */
    .main-container {
      min-height: 100vh;
      padding-top: 40px;
      padding-bottom: 40px;
    }

    /* 4. Titre principal (en vert) */
    h1, h2 {
      color: #008000; /* Vert */
      font-weight: bold;
    }

    /* 5. Les cartes (fond noir, bordure verte, en-tête verte, texte blanc) */
    .card {
      background-color: #000; /* Fond noir */
      border: 2px solid #008000; /* Bordure verte */
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      margin-bottom: 30px;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    /* Effet de survol sur les cartes */
    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .card-header {
      background-color: #008000; /* Vert */
      color: #fff; 
      font-size: 1.2rem;
      font-weight: bold;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }
    .card-body p,
    .card-body li {
      color: #fff; /* Texte en blanc */
    }

    /* 6. Boutons verts */
    .btn-custom {
      background-color: #008000;
      color: #fff; 
      border: none;
      transition: background-color 0.3s;
    }
    .btn-custom:hover {
      background-color: #006600; /* Vert plus foncé */
      transform: translateY(-3px);
    }

    /* 7. Icônes de ballon (Font Awesome) */
    .fa-futbol {
      color: #008000;
      font-size: 1.5rem;
      margin-right: 10px;
    }

    /* 8. Footer vert */
    footer {
      background-color: #008000;
      color: #fff;
      padding: 15px 0;
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
    <img src="../assets/images/logo.jpg" alt="Connexion" id="login-btn" style="  width: 80px;">
      <a class="navbar-brand" href="#">  Gestion des Joueurs</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon" style="color:#fff;"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="#matchs">Prochains Matchs</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#actualites">Actualités</a>
          </li>
          <!-- Bouton de déconnexion ajouté ici -->
          <li class="nav-item">
            <a class="nav-link btn btn-danger" href="logout.php" role="button">Se Déconnecter</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- CONTENU PRINCIPAL -->
  <div class="main-container d-flex align-items-start">
    <div class="container text-center">
      
      <!-- Titre principal -->
      <h1 class="mb-5">Gestion des Joueurs - Tournois de Football</h1>

      <!-- CARTES EXISTANTES -->
      <div class="row justify-content-center">
        <!-- Bloc Joueurs -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <i class="fas fa-futbol"></i> Joueurs
            </div>
            <div class="card-body">
              <p>Gérer la liste des joueurs, leurs statistiques et leurs informations.</p>
              <a href="joueurs.php" class="btn btn-custom">Voir les Joueurs</a>
            </div>
          </div>
        </div>
        <!-- Bloc Équipe -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <i class="fas fa-futbol"></i> Équipe
            </div>
            <div class="card-body">
              <p>Créer, modifier et visualiser les équipes et leurs compositions.</p>
              <a href="equipe.php" class="btn btn-custom">Voir l'Équipe</a>
            </div>
          </div>
        </div>
      </div>
      <div class="row justify-content-center">
        <!-- Bloc Staff -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <i class="fas fa-futbol"></i> Staff
            </div>
            <div class="card-body">
              <p>Gérer le personnel d'encadrement (entraîneurs, médecins, etc.).</p>
              <a href="staff.php" class="btn btn-custom">Voir le Staff</a>
            </div>
          </div>
        </div>
        <!-- Bloc Talent -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <i class="fas fa-futbol"></i> Talent
            </div>
            <div class="card-body">
              <p>Suivre le développement et les performances des jeunes talents.</p>
              <a href="talent.php" class="btn btn-custom">Voir les Talents</a>
            </div>
          </div>
        </div>
      </div>
      <div class="row justify-content-center">
        <!-- Bloc Personnel -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <i class="fas fa-futbol"></i> Personnel
            </div>
            <div class="card-body">
              <p>Gérer les employés administratifs et techniques du club.</p>
              <a href="personnel.php" class="btn btn-custom">Voir le Personnel</a>
            </div>
          </div>
        </div>
      </div>

      <!-- PROCHAINS MATCHS -->
      <h2 id="matchs" class="mt-5">Prochains matchs</h2>
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              Calendrier des rencontres
            </div>
            <div class="card-body">
              <ul>
                <li>Dimanche 20/03 : Club A vs Club B (15h00)</li>
                <li>Mercredi 23/03 : Club A vs Club C (19h00)</li>
                <li>Samedi 26/03 : Club B vs Club A (16h00)</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- ACTUALITÉS / NEWS -->
      <h2 id="actualites" class="mt-5">Dernières actualités</h2>
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              Actualités
            </div>
            <div class="card-body">
              <ul>
                <li>Nouvelle recrue : John Doe signe pour 2 ans.</li>
                <li>Stage de préparation : du 10 au 15 avril.</li>
                <li>Victoire 3-1 contre l’équipe X lors du dernier match.</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- /container -->
  </div> <!-- /main-container -->

  <!-- FOOTER -->
  <footer class="text-center">
    <div class="container">
      <p>© 2025 Gestion des Joueurs - Tous droits réservés</p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
