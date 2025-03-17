<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$query = "SELECT * FROM joueurs WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $id]);
$joueur = $stmt->fetch();

if (!$joueur) {
    die("Joueur non trouvé !");
}

$profile_photo = "../uploads/players/player_" . $id . ".jpg";
if (!file_exists($profile_photo)) {
    $profile_photo = "../uploads/players/player_" . $id . ".jpeg";
}
if (!file_exists($profile_photo)) {
    $profile_photo = "../uploads/players/player_" . $id . ".png";
}
if (!file_exists($profile_photo)) {
    $profile_photo = "/assets/default-avatar.png";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
        }
        .profile-header {
            background-color: rgb(42, 82, 51);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: block;
            background-color: #fff;
            padding: 3px;
            object-fit: cover;
        }
        .details-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .detail-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container profile-container">
        <div class="profile-header text-center">
            <img src="<?= $profile_photo; ?>" alt="Photo du joueur" class="profile-image">
            <h2><?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) ?></h2>
            <p class="lead">Joueur professionnel</p>
        </div>

        <div class="details-card">
            <h2 class="text-center mb-4">Détails du Joueur</h2>
            <div class="detail-item"><span class="detail-label">Rôle :</span> <?= htmlspecialchars($joueur['role']) ?></div>
            <div class="detail-item"><span class="detail-label">Âge :</span> <?= htmlspecialchars($joueur['age']) ?> ans</div>
            <div class="detail-item"><span class="detail-label">Club :</span> <?= htmlspecialchars($joueur['clubs']) ?></div>
            <div class="detail-item"><span class="detail-label">Nationalité :</span> <?= htmlspecialchars($joueur['nationalite']) ?></div>
            <div class="detail-item"><span class="detail-label">Nombre de buts :</span> <?= htmlspecialchars($joueur['goals']) ?></div>
        </div>

        <div class="text-center mt-4">
            <a href="home.php" class="btn btn-outline-dark">Retour</a>
            <a href="subscribe.php?id=<?= $joueur['id'] ?>" class="btn btn-success">S'abonner</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
