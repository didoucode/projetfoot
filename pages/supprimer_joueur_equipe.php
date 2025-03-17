<?php
session_start();
// Connexion à la base de données
include "../config/db.php";

// Vérifier si l'ID du joueur et l'ID de l'équipe sont présents dans l'URL
if (isset($_GET['id']) && isset($_GET['equipe'])) {
    $id_joueur = intval($_GET['id']);
    $id_equipe = intval($_GET['equipe']);

    // Retirer le joueur de l'équipe
    $requete = $pdo->prepare("UPDATE joueurs SET equipes = NULL WHERE id = ?");
    $requete->execute([$id_joueur]);

    // Rediriger vers la page de modification de l'équipe
    header("Location: modifier_equipe.php?id=" . $id_equipe);
    exit();
} else {
    die("ID du joueur ou de l'équipe non spécifié.");
}
?>