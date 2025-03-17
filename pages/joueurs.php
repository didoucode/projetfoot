<?php
session_start();
include "../config/db.php"; // Connexion à la base de données

// Suppression d'un joueur avec sécurité
if (isset($_GET['supprimer'])) {
    $id = (int) $_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM joueurs WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: joueurs.php");
    exit;
}

// Récupérer tous les joueurs
$stmt = $pdo->query("SELECT * FROM joueurs ORDER BY nom ASC");
$joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Joueurs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #000;
      min-height: 100vh;
      margin: 0;
      padding: 0;
      color: #fff;
    }
    .navbar-custom {
      background-color: #008000;
    }
    .navbar-custom .nav-link {
      color: #fff;
    }
    .btn-custom {
      background-color: #008000;
      color: #fff;
    }
    .btn-custom:hover {
      background-color: #006600;
    }
    table {
      background-color: #fff;
      margin: 0 auto; /* Centrer le tableau */
    }
    .btn-sm {
      width: 80px; 
      text-align: center;
    }
    .actions-col {
      width: 120px; /* Ajuste la largeur de la colonne des actions */
    }
 
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">

      <a class="navbar-brand" href="index.php">Accueil</a>
      <div class="collapse navbar-collapse">
      
        <ul class="navbar-nav ms-auto">
     
          <li class="nav-item">
         
            <a class="nav-link" href="joueurs.php">Joueurs</a>
          </li>
          <img src="../assets/images/logo.jpg" alt="Connexion" id="login-btn" style="  width: 80px;">
        </ul>
      </div>
      
      
    </div>
  </nav>

  <div class="container mt-5">
    <h1 class="text-white">⚽Liste des Joueurs⚽</h1>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Nom</th>
          <th>Prénom</th>
          <th class="actions-col">Actions</th> <!-- Colonne des actions avec largeur ajustée -->
        </tr>
      </thead>
      <tbody>
        <?php foreach ($joueurs as $joueur) : ?>
          <tr>
            <td><?= htmlspecialchars($joueur['nom']) ?></td>
            <td><?= htmlspecialchars($joueur['prenom']) ?></td>
            <td class="actions-col">
              <a href="joueur.php?id=<?= $joueur['id'] ?>" class="btn btn-primary btn-sm">Voir</a>
              <a href="joueurs.php?supprimer=<?= $joueur['id'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('Voulez-vous vraiment supprimer ce joueur ?')">Supprimer</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    
    <!-- Boutons en bas -->
    <div class="d-flex justify-content-between mt-3">
      <a href="dashboard_football.php" class="btn btn-secondary">← Retour au Dashboard</a>
      <a href="ajouter_joueur.php" class="btn btn-custom">Ajouter un Joueur</a>
    </div>
  </div>
</body>
</html>
