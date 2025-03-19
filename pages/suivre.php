<?php
session_start();
header("Content-Type: application/json");
include "../config/db.php";

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(["error" => "Vous devez être connecté."]);
    exit;
}

$action = $_POST['action'] ?? null;
$idCible = $_POST['id_cible'] ?? null;
$typeCible = $_POST['type_cible'] ?? null;

if (!$idCible || !$typeCible) {
    echo json_encode(["error" => "Données manquantes."]);
    exit;
}

try {
    if ($action === "subscribe") {
        // Vérifier si l'utilisateur est déjà abonné
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM abonnements WHERE id_utilisateur = ? AND id_cible = ? AND type_cible = ?");
        $checkStmt->execute([$userId, $idCible, $typeCible]);
        $isSubscribed = $checkStmt->fetchColumn();

        if ($isSubscribed > 0) {
            echo json_encode(["error" => "Vous suivez déjà ce joueur."]);
            exit;
        }

        // Insérer l'abonnement
        $stmt = $pdo->prepare("INSERT INTO abonnements (id_utilisateur, id_cible, type_cible) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $idCible, $typeCible]);
        echo json_encode(["message" => "Vous suivez maintenant ce joueur !"]);
        exit;
    } elseif ($action === "unsubscribe") {
        // Supprimer l'abonnement
        $stmt = $pdo->prepare("DELETE FROM abonnements WHERE id_utilisateur = ? AND id_cible = ? AND type_cible = ?");
        $stmt->execute([$userId, $idCible, $typeCible]);
        echo json_encode(["message" => "Vous ne suivez plus ce joueur."]);
        exit;
    } else {
        echo json_encode(["error" => "Action invalide."]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Erreur serveur : " . $e->getMessage()]);
    exit;
}
