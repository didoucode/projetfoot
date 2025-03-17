<?php
session_start();
include "../config/db.php";

if (isset($_POST['modifier'])) {
    $id_equipe = $_POST['id_equipe'];
    $nom = $_POST['nom'];
    $ville = $_POST['ville'];
    $entraineur = $_POST['entraineur'];
    $date_creation = $_POST['date_creation'];

    $stmt = $pdo->prepare("UPDATE Equipe SET nom = ?, ville = ?, entraineur = ?, date_creation = ? WHERE id = ?");
    $stmt->execute([$nom, $ville, $entraineur, $date_creation, $id_equipe]);

    header("Location: modifier_equipe.php?id_equipe=" . $id_equipe);
}

// Ajouter un joueur à l’équipe
if (isset($_POST['ajouter_joueur']) && !empty($_POST['nouveau_joueur'])) {
    $id_equipe = $_POST['id_equipe'];
    $id_joueur = $_POST['nouveau_joueur'];

    // Vérifier si le joueur est déjà dans une équipe
    $stmt = $pdo->prepare("SELECT id_equipe FROM Joueur WHERE id = ?");
    $stmt->execute([$id_joueur]);
    $joueur = $stmt->fetch();

    if (!$joueur['id_equipe']) {
        // Ajouter le joueur à l'équipe
        $stmt = $pdo->prepare("UPDATE Joueur SET id_equipe = ? WHERE id = ?");
        $stmt->execute([$id_equipe, $id_joueur]);

        header("Location: modifier_equipe.php?id_equipe=" . $id_equipe);
    } else {
        echo "⚠️ Ce joueur est déjà dans une autre équipe.";
    }
}

// Supprimer un joueur de l’équipe
if (isset($_POST['supprimer_joueur'])) {
    $id_joueur = $_POST['supprimer_joueur'];

    $stmt = $pdo->prepare("UPDATE Joueur SET id_equipe = NULL WHERE id = ?");
    $stmt->execute([$id_joueur]);

    header("Location: modifier_equipe.php?id_equipe=" . $_POST['id_equipe']);
}
?>
