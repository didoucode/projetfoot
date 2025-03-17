<?php
session_start();
// Connexion à la base de données
include "../config/db.php";

// Vérifier si l'ID de l'équipe est bien passé dans l'URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sécurisation de l'ID

    try {
        // Démarrer une transaction
        $pdo->beginTransaction();

        // Supprimer les matchs où l'équipe est impliquée
        $sql_matchs = "DELETE FROM matchs WHERE equipe1_id = ? OR equipe2_id = ?";
        $stmt_matchs = $pdo->prepare($sql_matchs);
        $stmt_matchs->execute([$id, $id]);

        // Supprimer les joueurs liés à l'équipe
        $sql_joueurs = "DELETE FROM joueurs WHERE equipes = ?";
        $stmt_joueurs = $pdo->prepare($sql_joueurs);
        $stmt_joueurs->execute([$id]);

        // Supprimer l'équipe
        $sql = "DELETE FROM equipe WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        // Valider la transaction
        $pdo->commit();

        // Rediriger vers equipe.php après suppression
        header("Location: equipe.php?success=1");
        exit();
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
} else {
    echo "ID invalide.";
}

?>
