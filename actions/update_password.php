<?php
require '../config/db.php';
session_start();

if (!isset($_SESSION['reset_email'])) {
    exit("Accès non autorisé.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        exit("Les mots de passe ne correspondent pas.");
    }

    $email = $_SESSION['reset_email'];

    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$password, $email]);

    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->execute([$email]);

    session_destroy();
    echo "Mot de passe mis à jour avec succès.";
}
?>
