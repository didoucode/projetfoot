<?php
session_start();
// Connexion à la base de données
include "../config/db.php";

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $ville = $_POST['ville'];
    $entraineur = $_POST['entraineur'];

    // Mettre à jour les informations de l'équipe
    $requete = $pdo->prepare("UPDATE Equipe SET nom = ?, ville = ?, entraineur = ? WHERE id = ?");
    $requete->execute([$nom, $ville, $entraineur, $id]);

    // Rediriger vers la page equipe.php après la mise à jour
    header("Location: equipe.php");
    exit();
} else {
    die("Méthode de requête non autorisée.");
}
?>