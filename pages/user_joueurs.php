<?php
include "../config/db.php";

$search = $_GET['search'] ?? '';

// Recherche des joueurs
$query = "SELECT id, prenom, nom, goals FROM joueurs WHERE nom LIKE :search OR prenom LIKE :search";
$stmt = $pdo->prepare($query);
$stmt->execute(['search' => "%$search%"]);
$joueurs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profils des Joueurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>/* Couleurs principales */
body {
    background-color: #121212; /* Noir */
    color: #ffffff; /* Texte blanc */
}

h2 {
    color: #1db954; /* Vert Spotify */
}

/* Formulaire de recherche */
.search-input {
    background-color: #222;
    border: 1px solid #1db954;
    color: #fff;
}

.search-input::placeholder {
    color: #bbb;
}

/* Cartes des joueurs */
.custom-card {
    background-color: #1e1e1e;
    border: 1px solid #1db954;
    color: #ffffff;
    box-shadow: 0px 4px 10px rgba(0, 255, 0, 0.3);
    transition: transform 0.3s ease;
}

.custom-card:hover {
    transform: scale(1.05);
}

/* Bouton "Voir Profil" */
.btn-outline-light {
    border-color: #1db954;
    color: #1db954;
}

.btn-outline-light:hover {
    background-color: #1db954;
    color: #000;
}
</style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center text-light">Profils des Joueurs</h2>

    <!-- Formulaire de recherche -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control search-input" placeholder="Rechercher un joueur..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-success" type="submit">Rechercher</button>
        </div>
    </form>

    <div class="row">
        <?php foreach ($joueurs as $joueur): ?>
            <div class="col-md-4">
                <div class="card custom-card mb-4">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) ?></h5>
                        <p class="card-text"><strong>Nombre de buts :</strong> <?= htmlspecialchars($joueur['goals']) ?></p>
                        <a href="player_profile.php?id=<?= $joueur['id'] ?>" class="btn btn-outline-light">Voir Profil</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>

