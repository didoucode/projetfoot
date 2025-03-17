<?php
require '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = rand(100000, 999999);
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?) 
                               ON DUPLICATE KEY UPDATE token = ?, created_at = NOW()");
        $stmt->execute([$email, $token, $token]);

        mail($email, "Réinitialisation du mot de passe", "Votre code de réinitialisation est : $token");

        header("Location: ../pages/verify_code.php?email=" . urlencode($email));
        exit();
    } else {
        echo "Adresse email non trouvée.";
    }
}
?>
