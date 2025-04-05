<?php
session_start();
include "../config/db.php"; // Assure-toi que ce fichier est bien accessible

function getTeamInfo($team_id, $pdo) {
    $stmt = $pdo->prepare("SELECT nom, logo FROM equipe WHERE id = ?");
    $stmt->execute([$team_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return [
        'nom' => $result['nom'] ?? 'Équipe inconnue',
        'logo' => $result['logo'] ?? 'default.png' // Si pas de logo, mettre une image par défaut
    ];
}


// Fonction pour récupérer les statistiques des votes
function getVoteStats($match_id, $pdo) {
    $stmt = $pdo->prepare("SELECT 
        SUM(CASE WHEN vote = 1 THEN 1 ELSE 0 END) as votes_team1,
        SUM(CASE WHEN vote = 0 THEN 1 ELSE 0 END) as votes_draw,
        SUM(CASE WHEN vote = 2 THEN 1 ELSE 0 END) as votes_team2,
        COUNT(*) as total_votes
    FROM votes WHERE match_id = ?");
    $stmt->execute([$match_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['total_votes'] > 0) {
        return [
            'team1' => round(($result['votes_team1'] / $result['total_votes']) * 100),
            'draw' => round(($result['votes_draw'] / $result['total_votes']) * 100),
            'team2' => round(($result['votes_team2'] / $result['total_votes']) * 100),
        ];
    }
    
    return ['team1' => 0, 'draw' => 0, 'team2' => 0];
}

// Fonction pour récupérer le vote d'un utilisateur
function getUserVote($match_id, $user_id, $pdo) {
    if (!$user_id) return null;
    $stmt = $pdo->prepare("SELECT vote FROM votes WHERE match_id = ? AND user_id = ?");
    $stmt->execute([$match_id, $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['vote'] ?? null;
}

// Gérer la soumission du vote
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour voter.']);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['match_id'], $data['vote']) || !in_array($data['vote'], [0, 1, 2])) {
        echo json_encode(['success' => false, 'message' => 'Données invalides.']);
        exit;
    }

    $match_id = $data['match_id'];
    $vote = $data['vote'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT id FROM matchs WHERE id = ?");
    $stmt->execute([$match_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Match introuvable.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM votes WHERE match_id = ? AND user_id = ?");
    $stmt->execute([$match_id, $user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Vous avez déjà voté pour ce match.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO votes (match_id, user_id, vote) VALUES (?, ?, ?)");
    if ($stmt->execute([$match_id, $user_id, $vote])) {
        echo json_encode(['success' => true, 'message' => 'Vote enregistré avec succès !']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l’enregistrement du vote.']);
    }

    exit;
}

// Récupération des matchs à venir
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM matchs WHERE date >= ? ORDER BY date ASC");
$stmt->execute([$today]);
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil du Joueur | Football Maroc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <title>Matchs à Venir</title>
    <script>
    function showAlert(message, type) {
    const alertBox = document.createElement("div");
    alertBox.className = "alert " + (type === "success" ? "alert-success" : "alert-error");
    alertBox.innerText = message;
    document.body.prepend(alertBox);
    
    setTimeout(() => {
        alertBox.remove();
    }, 3000);
}

function vote(matchId, voteOption) {
    fetch('sandage.php', { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ match_id: matchId, vote: voteOption })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Vote enregistré avec succès !', 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('Erreur : ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('Une erreur est survenue.', 'error');
    });
}

    </script>

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
        font-family: Arial, sans-serif;
        background-color: var(--beige);
        color: var(--noir);
        margin: 0;
        padding: 20px;
        text-align: center;
    }

    h1 {
        color: var(--vert);
    }

    .match {
        background: var(--blanc);
        border: 2px solid var(--gris);
        border-radius: 10px;
        padding: 20px;
        margin: 20px auto;
        width: 80%;
        max-width: 700px;
        box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
    }

    .poll p {
        font-size: 18px;
        font-weight: bold;
    }

    .poll button {
        background-color: var(--vert);
        color: var(--blanc);
        border: none;
        padding: 10px 20px;
        margin: 5px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: 0.3s;
    }

    .poll button:hover {
        background-color: var(--jaune);
        color: var(--noir);
    }

    .poll button:disabled {
        background-color: var(--gris);
        cursor: not-allowed;
    }

    /* Styles pour les alertes */
    .alert {
        padding: 15px;
        margin: 60px auto;
        width: 80%;
        max-width: 500px;
        text-align: center;
       
        font-weight: bold;
        border-radius: 5px;

        position: absolute;
    top: 80%;
    left: 53%;
    transform: translate(-50%, -50%);
    }

    .alert-success {
        background-color: var(--vert);
        color: var(--blanc);
        
    }

    .alert-error {
        background-color: red;
        color: var(--blanc);
    }
    .team-logo {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 50%;
    margin: 0 10px;
}

</style>

</head>
<body>
   <!-- Sidebar -->
   <?php include '../includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="content">

<?php include '../includes/header.php'; ?>
<br>
<h1>Matchs à Venir</h1>

<?php foreach ($matches as $match): 
    $team1 = getTeamInfo($match['equipe1_id'], $pdo);
    $team2 = getTeamInfo($match['equipe2_id'], $pdo);
?>
    <div class="match">
        <h2>
            <img src="../assets/images/<?php echo $team1['logo']; ?>" alt="<?php echo $team1['nom']; ?>" class="team-logo">
            <?php echo $team1['nom']; ?>  
            vs  
            <?php echo $team2['nom']; ?>
            <img src="../assets/images/<?php echo $team2['logo']; ?>" alt="<?php echo $team2['nom']; ?>" class="team-logo">
        </h2>
        <p>Date : <?php echo date('d M Y', strtotime($match['date'])); ?></p>
        
        <!-- Sondage -->
        <div class="poll">
            <p>Selon vous, qui va gagner ?</p>
            <?php 
                $voteStats = getVoteStats($match['id'], $pdo);
                $userVote = getUserVote($match['id'], $_SESSION['user_id'] ?? null, $pdo);
            ?>
            <button onclick="vote(<?php echo $match['id']; ?>, 1)" <?php echo ($userVote === 1) ? 'disabled' : ''; ?>>
                <?php echo $team1['nom']; ?> (<?php echo $voteStats['team1']; ?>%)
            </button>

            <button onclick="vote(<?php echo $match['id']; ?>, 0)" <?php echo ($userVote === 0) ? 'disabled' : ''; ?>>
                Match nul (<?php echo $voteStats['draw']; ?>%)
            </button>

            <button onclick="vote(<?php echo $match['id']; ?>, 2)" <?php echo ($userVote === 2) ? 'disabled' : ''; ?>>
                <?php echo $team2['nom']; ?> (<?php echo $voteStats['team2']; ?>%)
            </button>
        </div>
    </div>
<?php endforeach; ?>


</body>
</html>
