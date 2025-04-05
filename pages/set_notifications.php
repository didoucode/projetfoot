<?php
include "../config/db.php";

// Récupérer les matchs à venir
$stmt = $pdo->prepare("
    SELECT 
        m.id AS match_id, 
        m.date AS match_date, 
        m.heure_debut, 
        m.heure_fin, 
        t.nom AS tournoi_nom, 
        e1.nom AS equipe1_nom, 
        e2.nom AS equipe2_nom, 
        s.nom AS stade_nom
    FROM matchs m
    JOIN equipe e1 ON m.equipe1_id = e1.id
    JOIN equipe e2 ON m.equipe2_id = e2.id
    JOIN tournois t ON m.tournoi_id = t.id
    JOIN stade s ON m.stade_id = s.id
    WHERE m.date > NOW()
");
$stmt->execute();
$matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier chaque match et envoyer une notification si nécessaire
foreach ($matchs as $match) {
    // Créer le message de notification
    $notification_message = "
        <strong>Match à venir dans le tournoi {$match['tournoi_nom']} :</strong><br>
        {$match['equipe1_nom']} vs {$match['equipe2_nom']}<br>
        <em>Date : {$match['match_date']} à {$match['heure_debut']} - Stade : {$match['stade_nom']}</em><br>
    ";

    // Récupérer tous les utilisateurs avec le rôle "user"
    $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'user'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Insérer la notification pour chaque utilisateur
    foreach ($users as $user) {
        try {
            // Vérifier si une notification a déjà été envoyée pour ce match et cet utilisateur
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND match_id = ?");
            $stmt_check->execute([$user['id'], $match['match_id']]);
            $existing_notification_count = $stmt_check->fetchColumn();

            // Si aucune notification n'existe pour ce match et cet utilisateur, alors on insère la notification
            if ($existing_notification_count == 0) {
                // Insertion de la notification avec l'ID du match
                $stmt_insert = $pdo->prepare("INSERT INTO notifications (user_id, message, match_id) VALUES (?, ?, ?)");
                $stmt_insert->execute([$user['id'], $notification_message, $match['match_id']]);

                // Optionnel : Vous pouvez aussi ajouter un message pour voir quelles notifications ont été envoyées
                echo "Notification envoyée à l'utilisateur ID: {$user['id']} pour le match ID: {$match['match_id']}<br>";
            } else {
                // Si la notification existe déjà pour ce match et cet utilisateur
                echo "Notification déjà existante pour l'utilisateur ID: {$user['id']} pour le match ID: {$match['match_id']}<br>";
            }
        } catch (PDOException $e) {
            // Affichage de l'erreur en cas de problème
            echo "Erreur lors de l'insertion de la notification pour l'utilisateur ID: {$user['id']} : " . $e->getMessage() . "<br>";
        }
    }

    // Afficher pour quel match la notification a été envoyée
    echo "Notification envoyée pour le match : {$match['equipe1_nom']} vs {$match['equipe2_nom']}<br>";
}
?>