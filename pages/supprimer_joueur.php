<?php
// Connexion à la base de données
session_start();
include "../config/db.php";

// Vérifier si un ID est fourni
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Vérifier si le joueur existe avant de le supprimer
    $stmt = $pdo->prepare("SELECT * FROM joueurs WHERE id = ?");
    $stmt->execute([$id]);
    $joueur = $stmt->fetch();

    if ($joueur) {
        // Suppression du joueur
        $pdo->prepare("DELETE FROM joueurs WHERE id = ?")->execute([$id]);
    }
}

// Redirection vers la liste des joueurs
header("Location: joueurs.php");
exit;
