<?php
include "../config/db.php";

// Supposons que l'ID du match soit passé en paramètre ou sélectionné par l'administrateur
$match_id = 1;  // Exemple : ID du match à notifier

// Requête pour récupérer les informations du match
$stmt = $pdo->prepare("
    SELECT m.id, m.date, m.heure_debut, m.stade_id, m.tournoi_id, m.equipe1_id, m.equipe2_id,
           t.nom AS tournoi_nom, e1.nom AS equipe1_nom, e2.nom AS equipe2_nom, s.nom AS stade_nom
    FROM matchs m
    JOIN tournois t ON m.tournoi_id = t.id
    JOIN equipe e1 ON m.equipe1_id = e1.id
    JOIN equipe e2 ON m.equipe2_id = e2.id
    JOIN stade s ON m.stade_id = s.id
    WHERE m.id = :match_id
");

$stmt->execute(['match_id' => $match_id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

// Si le match existe, on crée la notification
if ($match) {
    $tournoi_nom = $match['tournoi_nom'];
    $equipe1_nom = $match['equipe1_nom'];
    $equipe2_nom = $match['equipe2_nom'];
    $stade_nom = $match['stade_nom'];
    $date_match = $match['date'];  // La date du match
    $heure_debut = $match['heure_debut'];  // L'heure du début du match

    // Préparer le message de notification
    $notification_message = "
        <strong>Match à venir dans le tournoi {$tournoi_nom} :</strong><br>
        {$equipe1_nom} vs {$equipe2_nom}<br>
        <em>Date : {$date_match}</em><br>
        <em>Heure : {$heure_debut}</em><br>
        <em>Lieu : {$stade_nom}</em><br>
    ";

    // Enregistrer la notification dans la table "notifications" (si tu en as une)
    // Ou envoyer l'email/mise à jour des utilisateurs ici
    $stmt = $pdo->prepare("INSERT INTO notifications (message, date_notification) VALUES (?, NOW())");
    $stmt->execute([$notification_message]);
}
?>