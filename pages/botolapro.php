<?php
include "../config/db.php"; // Connexion à la base de données

// Récupération des résultats des matchs joués
$sqlMatchs = "SELECT 
    r.id, 
    m.id AS match_id, 
    e1.nom AS equipe1, e1.logo AS logo1, 
    e2.nom AS equipe2, e2.logo AS logo2, 
    r.score_equipe1, r.score_equipe2, 
    r.gagnant_id, g.nom AS gagnant, 
    m.date, t.nom AS tournoi, m.tour
FROM resultats r
JOIN matchs m ON r.match_id = m.id
JOIN equipe e1 ON m.equipe1_id = e1.id
JOIN equipe e2 ON m.equipe2_id = e2.id

JOIN equipe g ON r.gagnant_id = g.id
JOIN tournois t ON m.tournoi_id = t.id
ORDER BY m.date DESC";

$stmtMatchs = $pdo->prepare($sqlMatchs);
$stmtMatchs->execute();
$matchs = $stmtMatchs->fetchAll();

// Si l'utilisateur est connecté
$userConnected = isset($_SESSION['user_id']);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Résultats des Matchs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .match-card {
            background: linear-gradient(to right, #e3f2fd, #ffebee);
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
            margin-bottom: 20px;
        }
        .team-logo {
            width: 70px;
        }
        .vs {
            font-size: 26px;
            font-weight: bold;
            color: #333;
        }
        .score {
            font-size: 28px;
            font-weight: bold;
        }
        .match-status {
            background: #333;
            color: white;
            border-radius: 15px;
            padding: 5px 15px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
        }
        .match-info {
            font-size: 14px;
            color: #666;
        }
        .nav-tabs .nav-link {
            color: #007bff;
            font-weight: bold;
        }
        .nav-tabs .nav-link.active {
            color: #000;
            border-bottom: 3px solid #007bff;
        }
        /*CHAT */
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
    </style>
</head>
<body class="bg-light">
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="content">

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Résultats des Matchs</h2>

    <?php foreach ($matchs as $match): ?>
        <div class="match-card">
            <h5 class="text-dark"><?= htmlspecialchars($match['equipe1']) ?> Vs <?= htmlspecialchars($match['equipe2']) ?></h5>
            <p class="text-muted"><?= htmlspecialchars($match['tournoi']) ?> - journee <?= htmlspecialchars($match['tour']) ?></p>

            <div class="match-status">Fin du match</div>

            <div class="d-flex align-items-center justify-content-center my-3">
                <div class="text-center">
                    <img src="../assets/images/<?= htmlspecialchars($match['logo1']) ?>" class="team-logo" alt="<?= htmlspecialchars($match['equipe1']) ?>">
                    <p class="fw-bold"><?= htmlspecialchars($match['equipe1']) ?></p>
                </div>
                <div class="mx-4 score"><?= $match['score_equipe1'] ?> - <?= $match['score_equipe2'] ?></div>
                <div class="text-center">
                    <img src="../assets/images/<?= htmlspecialchars($match['logo2']) ?>" class="team-logo" alt="<?= htmlspecialchars($match['equipe2']) ?>">
                    <p class="fw-bold"><?= htmlspecialchars($match['equipe2']) ?></p>
                </div>
            </div>

            <p class="text-muted"><?= date("d/m", strtotime($match['date'])) ?></p>

            <ul class="nav nav-tabs justify-content-center mt-3">
                <li class="nav-item"><a class="nav-link active" href="#">Match</a></li>
               <li> <a class="nav-link" href="composition.php?id=<?= $match['match_id'] ?>">Composition</a>
                   </li>
                <li><a class="nav-link" href="stats.php?id=<?= $match['match_id'] ?>">Statistiques</a></li>

                <li class="nav-item"><a class="nav-link" href="#">Team Streaks</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Tête à tête</a></li>
                <li class="nav-item"><a class="nav-link" href="#">commantaire</a></li>
            </ul>

            <!-- Zone de Chat -->
            <div class="row mt-5">
                <div class="col-12">
                    <h3><i class="fas fa-comments me-2"></i>Chat en direct</h3>

                    <!-- Zone de messages -->
                    <div class="chat-box" id="chat-box-<?= $match['id'] ?>">
                        <!-- Les messages seront chargés ici -->
                    </div>

                    <!-- Formulaire d'envoi de message -->
                    <form id="chat-form-<?= $match['id'] ?>" class="mt-3">
                        <div class="form-group">
                            <textarea class="form-control" id="chat-message-<?= $match['id'] ?>" placeholder="Écrivez un message..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Envoyer</button>

                    </form>
                </div>
            </div>

        </div>
    <?php endforeach; ?>
</div>

<script>
    $(document).ready(function() {
        // Charger les messages pour chaque match
        function loadMessages(matchId) {
            $.get("chat.php", { match_id: matchId }, function(data) {
                $('#chat-box-' + matchId).html(data);
                $('#chat-box-' + matchId).scrollTop($('#chat-box-' + matchId)[0].scrollHeight); // Scroller vers le bas
            });
        }

        // Envoi de message
        $('form[id^="chat-form-"]').submit(function(e) {
            e.preventDefault();

            var matchId = $(this).attr('id').split('-')[2]; // Récupère l'id du match
            var message = $('#chat-message-' + matchId).val();

            if (message.trim() != "") {
                // AJOUTER LES MODIFICATIONS ICI
                $.post("chat.php", { 
                    message: message, 
                    match_id: matchId 
                }, function(response) {
                    if (response.error) {
                        alert(response.error);
                    } else {
                        $('#chat-message-' + matchId).val('');
                        loadMessages(matchId);
                    }
                }, 'json').fail(function(xhr) {
                    console.error("Erreur serveur :", xhr.responseText);
                });
            }
        });
        // Rafraîchir les messages automatiquement toutes les 3 secondes
        setInterval(function() {
            <?php foreach ($matchs as $match): ?>
                loadMessages(<?= $match['id'] ?>);
            <?php endforeach; ?>
        }, 3000); // 3 secondes

        // Charger les messages dès que la page est chargée
        <?php foreach ($matchs as $match): ?>
            loadMessages(<?= $match['id'] ?>);
        <?php endforeach; ?>
    });



   
</script>
  

</body>
</html>
