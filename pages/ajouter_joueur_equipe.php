<?php
session_start();
// Connexion à la base de données
include "../config/db.php";
// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_joueur = $_POST['id_joueur'];
    $id_equipe = $_POST['id_equipe'];

    // Mettre à jour l'équipe du joueur
    $requete = $pdo->prepare("UPDATE joueurs SET equipes = ? WHERE id = ?");
    $requete->execute([$id_equipe, $id_joueur]);

    // Rediriger vers la page de modification de l'équipe
    header("Location: modifier_equipe.php?id=" . $id_equipe);
    exit();
} else {
    die("Méthode de requête non autorisée.");
}
?>