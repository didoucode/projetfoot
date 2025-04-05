<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les notifications pour l'utilisateur connecté
$stmt = $pdo->prepare("SELECT n.id, n.message, n.created_at 
                       FROM notifications n 
                       WHERE n.user_id = ? 
                       ORDER BY n.created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($notifications) > 0) {
    echo json_encode(['notifications' => $notifications]);
} else {
    echo json_encode(['notifications' => []]);
}
?>