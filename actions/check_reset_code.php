<?php
require '../config/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $token = $_POST['token'];

    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ? 
                           AND created_at >= NOW() - INTERVAL 15 MINUTE");
    $stmt->execute([$email, $token]);
    $reset = $stmt->fetch();

    if ($reset) {
        $_SESSION['reset_email'] = $email;
        header("Location: ../pages/reset_password.php");
        exit();
    } else {
        echo "Code invalide ou expiré.";
    }
}
?>
