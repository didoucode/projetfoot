<?php
include "../config/db.php";

// Vérifier si un ID de notification est fourni
if (isset($_GET['id'])) {
    $notification_id = $_GET['id'];

    // Option 1 : Supprimer la notification (si tu veux la supprimer)
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
    $stmt->execute([$notification_id]);

    // Option 2 : Si tu préfères marquer la notification comme ignorée, ajoute une colonne "ignored" et fais cette requête à la place
    // $stmt = $pdo->prepare("UPDATE notifications SET ignored = 1 WHERE id = ?");
    // $stmt->execute([$notification_id]);

    // Répondre avec un message de succès
    echo json_encode(['status' => 'success']);
}
?>
