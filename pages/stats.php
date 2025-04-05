<?php include "../config/db.php";

$match_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql_match = "SELECT * FROM matchs WHERE id = ?";
$stmt_match = $pdo->prepare($sql_match);
$stmt_match->execute([$match_id]);
$match = $stmt_match->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    die("Aucun match trouvé avec cet ID.");
}


// Récupérer les équipes
$equipe_domicile_id = $match['equipe1_id'];
$equipe_exterieur_id = $match['equipe2_id'];

$stmt_equipe_domicile = $pdo->prepare("SELECT * FROM equipe WHERE id = ?");
$stmt_equipe_domicile->execute([$equipe_domicile_id]);
$equipe_domicile = $stmt_equipe_domicile->fetch(PDO::FETCH_ASSOC);

$stmt_equipe_exterieur = $pdo->prepare("SELECT * FROM equipe WHERE id = ?");
$stmt_equipe_exterieur->execute([$equipe_exterieur_id]);
$equipe_exterieur = $stmt_equipe_exterieur->fetch(PDO::FETCH_ASSOC);

// Récupérer les joueurs et leurs statistiques pour l'équipe à domicile
$sql_stats_domicile = "
    SELECT j.nom, j.prenom, j.numero_club, j.role, sj.*
    FROM statistiques_joueurs sj
    JOIN joueurs j ON sj.joueur_id = j.id
    WHERE sj.match_id = ? AND j.equipes = ?
    ORDER BY j.role, j.numero_club
";


$stmt_stats_domicile = $pdo->prepare($sql_stats_domicile);
$stmt_stats_domicile->execute([$match_id, $equipe_domicile_id]);
$stats_joueurs_domicile = $stmt_stats_domicile->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les joueurs et leurs statistiques pour l'équipe extérieure
$sql_stats_exterieur = "
    SELECT j.nom, j.prenom, j.numero_club, j.role,  sj.*
    FROM statistiques_joueurs sj
    JOIN joueurs j ON sj.joueur_id = j.id
    WHERE sj.match_id = ? AND j.equipes = ?
    ORDER BY j.role, j.numero_club
";

$stmt_stats_exterieur = $pdo->prepare($sql_stats_exterieur);
$stmt_stats_exterieur->execute([$match_id, $equipe_exterieur_id]);
$stats_joueurs_exterieur = $stmt_stats_exterieur->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les statistiques globales du match
$sql_match_stats = "
    SELECT * FROM performance_matchs 
    WHERE match_id = ?
";
$stmt_match_stats = $pdo->prepare($sql_match_stats);
$stmt_match_stats->execute([$match_id]);
$match_stats = $stmt_match_stats->fetch(PDO::FETCH_ASSOC);
echo "Match ID : $match_id, Équipe domicile : $equipe_domicile_id, Équipe extérieur : $equipe_exterieur_id";

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Match - <?php echo htmlspecialchars($equipe_domicile['nom']) . ' vs ' . htmlspecialchars($equipe_exterieur['nom']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .score-display {
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .team-logo {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .match-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .stats-section {
            margin-top: 30px;
        }
        .nav-tabs .nav-link.active {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- En-tête du match -->
        <div class="match-info">
            <div class="row align-items-center">
                <div class="col-md-5 text-center">
                    <?php if (!empty($equipe_domicile['logo'])): ?>
                        <img src="<?php echo htmlspecialchars($equipe_domicile['logo']); ?>" alt="Logo <?php echo htmlspecialchars($equipe_domicile['nom']); ?>" class="team-logo">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($equipe_domicile['nom']); ?></h3>
                </div>
                <div class="col-md-2 text-center">
                    <div class="score-display">
                        <?php echo $match['score_equipe1'] ?? 0; ?> - <?php echo $match['score_equipe2'] ?? 0; ?>
                    </div>
                    <div class="text-muted">
                        <?php 
                        $date = new DateTime($match['date']);
                        echo $date->format('d/m/Y H:i'); 
                        ?>
                    </div>
                </div>
                <div class="col-md-5 text-center">
                    <?php if (!empty($equipe_exterieur['logo'])): ?>
                        <img src="<?php echo htmlspecialchars($equipe_exterieur['logo']); ?>" alt="Logo <?php echo htmlspecialchars($equipe_exterieur['nom']); ?>" class="team-logo">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($equipe_exterieur['nom']); ?></h3>
                </div>
            </div>
        </div>

        <!-- Navigation par onglets -->
        <ul class="nav nav-tabs" id="matchTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="alignments-tab" data-bs-toggle="tab" data-bs-target="#alignments" type="button" role="tab" aria-controls="alignments" aria-selected="true">Alignements</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button" role="tab" aria-controls="stats" aria-selected="false">Statistiques</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab" aria-controls="summary" aria-selected="false">Résumé</button>
            </li>
        </ul>

        <div class="tab-content" id="matchTabsContent">
            <!-- Onglet Alignements -->
            <div class="tab-pane fade show active" id="alignments" role="tabpanel" aria-labelledby="alignments-tab">
                <div class="row mt-4">
                    <!-- Équipe à domicile -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0"><?php echo htmlspecialchars($equipe_domicile['nom']); ?> - Alignement</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Nom</th>
                                            <th>Poste</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stats_joueurs_domicile as $joueur): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($joueur['numero_club']); ?></td>
                                                <td>
                                                    <?php 
                                                    echo htmlspecialchars($joueur['prenom'] ?? '') . ' ' . htmlspecialchars($joueur['nom']); 
                                                    if (isset($joueur['capitaine']) && $joueur['capitaine'] == 1) echo ' (C)';
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($joueur['poste']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Équipe extérieure -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h4 class="mb-0"><?php echo htmlspecialchars($equipe_exterieur['nom']); ?> - Alignement</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Nom</th>
                                            <th>Poste</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stats_joueurs_exterieur as $joueur): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($joueur['numero_club']); ?></td>
                                                <td>
                                                    <?php 
                                                    echo htmlspecialchars($joueur['prenom'] ?? '') . ' ' . htmlspecialchars($joueur['nom']); 
                                                    if (isset($joueur['capitaine']) && $joueur['capitaine'] == 1) echo ' (C)';
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($joueur['poste']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Statistiques -->
            <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                <div class="stats-section">
                    <h3>Statistiques des Joueurs</h3>
                    
                    <h4 class="mt-4"><?php echo htmlspecialchars($equipe_domicile['nom']); ?></h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>Joueur</th>
                                    <th>N°</th>
                                    <th>Buts</th>
                                    <th>Passes D.</th>
                                    <th>Tirs</th>
                                    <th>Tirs Cadrés</th>
                                    <th>CJ</th>
                                    <th>CR</th>
                                    <th>Minutes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats_joueurs_domicile as $stat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($stat['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($stat['numero_club']); ?></td>
                                        <td><?php echo htmlspecialchars($stat['buts'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($stat['passes_decisives'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($stat['tirs'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($stat['tirs_cadres'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($stat['cartons_jaunes'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($stat['cartons_rouges'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($stat['minutes_jouees'] ?? 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <h4 class="mt-4"><?php echo htmlspecialchars($equipe_exterieur['nom']); ?></h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-danger">
                                <tr>
                                    <th>Joueur</th>
                                    <th>N°</th>
                                    <th>Buts</th>
                                    <th>Passes D.</th>
                                    <th>Tirs</th>
                                    <th>Tirs Cadrés</th>
                                    <th>CJ</th>
                                    <th>CR</th>
                                    <th>Minutes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats_joueurs_exterieur as $stat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($stat['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($stat['numero_club']); ?></td>
                                        <td><?php echo htmlspecialchars($stat['buts'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($stat['passes_decisives'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($stat['tirs'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($stat['tirs_cadres'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($stat['cartons_jaunes'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($stat['cartons_rouges'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars($stat['minutes_jouees'] ?? 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Onglet Résumé -->
            <div class="tab-pane fade" id="summary" role="tabpanel" aria-labelledby="summary-tab">
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Résumé du Match</h4>
                            </div>
                            <div class="card-body">
                                <?php if ($match_stats): ?>
                                    <div class="row">
                                        <div class="col-md-4 text-end fw-bold">
                                            <?php echo isset($match_stats['possession_equipe1']) ? $match_stats['possession_equipe1'] . '%' : 'N/A'; ?>
                                        </div>
                                        <div class="col-md-4 text-center">Possession de balle</div>
                                        <div class="col-md-4 fw-bold">
                                            <?php echo isset($match_stats['possession_equipe2']) ? $match_stats['possession_equipe2'] . '%' : 'N/A'; ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4 text-end fw-bold">
                                            <?php echo isset($match_stats['tirs_equipe1']) ? $match_stats['tirs_equipe1'] : 'N/A'; ?>
                                        </div>
                                        <div class="col-md-4 text-center">Tirs</div>
                                        <div class="col-md-4 fw-bold">
                                            <?php echo isset($match_stats['tirs_equipe2']) ? $match_stats['tirs_equipe2'] : 'N/A'; ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4 text-end fw-bold">
                                            <?php echo isset($match_stats['tirs_cadres_equipe1']) ? $match_stats['tirs_cadres_equipe1'] : 'N/A'; ?>
                                        </div>
                                        <div class="col-md-4 text-center">Tirs cadrés</div>
                                        <div class="col-md-4 fw-bold">
                                            <?php echo isset($match_stats['tirs_cadres_equipe2']) ? $match_stats['tirs_cadres_equipe2'] : 'N/A'; ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4 text-end fw-bold">
                                            <?php echo isset($match_stats['corners_equipe1']) ? $match_stats['corners_equipe1'] : 'N/A'; ?>
                                        </div>
                                        <div class="col-md-4 text-center">Corners</div>
                                        <div class="col-md-4 fw-bold">
                                            <?php echo isset($match_stats['corners_equipe2']) ? $match_stats['corners_equipe2'] : 'N/A'; ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4 text-end fw-bold">
                                            <?php echo isset($match_stats['fautes_equipe1']) ? $match_stats['fautes_equipe1'] : 'N/A'; ?>
                                        </div>
                                        <div class="col-md-4 text-center">Fautes</div>
                                        <div class="col-md-4 fw-bold">
                                            <?php echo isset($match_stats['fautes_equipe2']) ? $match_stats['fautes_equipe2'] : 'N/A'; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        Aucune statistique disponible pour ce match.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($match['commentaires'])): ?>
                        <div class="card mt-4">
                            <div class="card-header">
                                <h4>Commentaires</h4>
                            </div>
                            <div class="card-body">
                                <?php echo nl2br(htmlspecialchars($match['commentaires'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>