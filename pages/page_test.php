<?php
include "../config/db.php";

// Récupérer les matchs
$stmt = $pdo->prepare("
    SELECT m.id, m.equipe1_id, m.equipe2_id, m.score_equipe1, m.score_equipe2, m.date, e1.nom AS equipe1_nom, e2.nom AS equipe2_nom
    FROM matchs m
    JOIN equipe e1 ON m.equipe1_id = e1.id
    JOIN equipe e2 ON m.equipe2_id = e2.id
    ORDER BY m.date DESC");
$stmt->execute();

$matchs = $stmt->fetchAll();

// Fonction pour déterminer le résultat (V = victoire, N = nul, D = défaite)
function getMatchResult($score1, $score2) {
    if ($score1 > $score2) {
        return 'V'; // Victoire
    } elseif ($score1 < $score2) {
        return 'D'; // Défaite
    } else {
        return 'N'; // Nul
    }
}
function getMatchResultClass($score1, $score2, $equipeId) {
    if ($score1 > $score2 && $equipeId == 1) {
        return 'win'; // Victoire pour l'équipe 1
    } elseif ($score1 < $score2 && $equipeId == 2) {
        return 'win'; // Victoire pour l'équipe 2
    } elseif ($score1 == $score2) {
        return 'draw'; // Match nul
    } else {
        return 'lose'; // Défaite pour l'équipe
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats Football</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f5f5f5;
    margin: 0;
    padding: 20px;
}

.informations {
    width: 30%;
    margin: auto;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.navigation {
    display: flex;
    justify-content: space-between;
}

button {
    padding: 10px;
    border: none;
    background: #007bff;
    color: white;
    font-weight: bold;
    cursor: pointer;
    border-radius: 5px;
}

.match-results ul {
    list-style: none;
    padding: 0;
}

.match-results li {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.result {
    font-weight: bold;
    padding: 5px;
    border-radius: 5px;
}

.result.win { background: green; color: white; }
.result.draw { background: gray; color: white; }
.result.lose { background: red; color: white; }

.match-info {
    text-align: center;
}

.match-details {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.match-details img {
    width: 40px;
    height: 40px;
}

.more-info {
    margin-top: 10px;
    display: block;
    width: 100%;
}

.ranking {
    margin-top: 20px;
}

.ranking table {
    width: 100%;
    border-collapse: collapse;
}

.ranking th, .ranking td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.form span {
    padding: 5px;
    margin: 2px;
    border-radius: 5px;
    display: inline-block;
}

.win { background: green; color: white; }
.draw { background: gray; color: white; }
.lose { background: red; color: white; }
</style>
<body>
<div class="informations">
    <div class="navigation">
        <button class="prev">◀ PRÉCÉDENT</button>
        <button class="next">SUIVANT ▶</button>
    </div>
    <section class="match-results">
        <h3>Botola Pro League 1</h3>
        <ul>
            <?php foreach ($matchs as $match): ?>
                <li class="match-item">
                    <span class="date"><?php echo date('d/m/Y', strtotime($match['date'])); ?></span>
                    <span class="team"><?php echo $match['equipe1_nom']; ?></span>
                    <span class="score"><?php echo $match['score_equipe1']; ?></span> -
                    <span class="score"><?php echo $match['score_equipe2']; ?></span>
                    <span class="team"><?php echo $match['equipe2_nom']; ?></span>
                    <span class="result <?php echo getMatchResultClass($match['score_equipe1'], $match['score_equipe2'], 1); ?>">
                        <?php echo getMatchResult($match['score_equipe1'], $match['score_equipe2']); ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="match-info">
        <h3>Next Match</h3>
        <div class="match-details">
            <img src="assets/images/raja.jpg" alt="Raja">
            <span class="time">16:00</span>
            <img src="assets/images/fus.jpg" alt="fus">
        </div>
        <button class="more-info">VOIR PLUS</button>
        <div class="ranking">
            <h4>Classement</h4>
            <table>
                <tr>
                    <th>#</th>
                    <th>Équipe</th>
                    <th>Récents</th>
                    <th>Pts</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td><img src="assets/images/raja.jpg" alt="RAJA"> RAJA AC</td>
                    <td class="form"><span class="win">V</span><span class="win">V</span><span class="draw">N</span><span class="win">V</span></td>
                    <td>8</td>
                </tr>
                <tr>
                    <td>6</td>
                    <td><img src="assets/images/fus.jpg" alt="Fus"> FUS</td>
                    <td class="form"><span class="lose">D</span><span class="lose">D</span><span class="lose">D</span><span class="lose">D</span></td>
                    <td>0</td>
                </tr>
            </table>
        </div>
    </section>
</div>
</body>
</html>
<script>document.addEventListener("DOMContentLoaded", function() {
    const prevBtn = document.querySelector(".prev");
    const nextBtn = document.querySelector(".next");
    const matchList = document.querySelector(".match-results ul");

    let index = 0;
    const matches = matchList.children;
    const matchesPerPage = 3;

    function showMatches() {
        for (let i = 0; i < matches.length; i++) {
            matches[i].style.display = i >= index && i < index + matchesPerPage ? "flex" : "none";
        }
    }

    prevBtn.addEventListener("click", function() {
        if (index > 0) {
            index -= matchesPerPage;
            showMatches();
        }
    });

    nextBtn.addEventListener("click", function() {
        if (index + matchesPerPage < matches.length) {
            index += matchesPerPage;
            showMatches();
        }
    });

    showMatches();
});
</script>