<?php
session_start();
include "../config/db.php"; // Connexion à la base de données

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Vérification des champs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Tous les champs doivent être remplis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = "Cet email est déjà utilisé.";
            } else {
               

                // Insérer l'utilisateur dans la base de données
                $sql = "INSERT INTO users (username, email, password, verified, created_at) VALUES (?, ?, ?, 1, NOW())";
                $stmt = $pdo->prepare($sql);

                if ($stmt->execute([$username, $email,$password])) {
                    $success = "Inscription réussie ! Redirection vers la connexion...";
                    header("refresh:2;url=auth.php"); // Redirection après 2 secondes
                    exit();
                } else {
                    $error = "Une erreur est survenue lors de l'inscription.";
                }
            }
        } catch (PDOException $e) {
            $error = "Erreur de base de données : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0a0a0a;
            overflow: hidden;
        }

        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../assets/images/background.jpg') repeat center center/cover;
            animation: moveBackground 8s linear infinite alternate;
            filter: brightness(0.4);
        }

        @keyframes moveBackground {
            0% { background-position: center top; }
            100% { background-position: center bottom; }
        }

        .login-box {
            width: 400px;
            background-color: rgba(0, 0, 0, 0.8);
            box-shadow: 0px 0px 15px rgba(255, 165, 0, 0.7);
            border-radius: 10px;
            color: white;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .form-control {
            background-color: #222;
            border: none;
            color: white;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .btn-warning {
            background-color: #ffa500;
            border: none;
            font-weight: bold;
        }

        .btn-warning:hover {
            background-color: #ff8c00;
        }
    </style>
</head>
<body>

    <div class="background"></div>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="login-box p-4 rounded">
            <h2 class="text-center">S'INSCRIRE</h2>

            <!-- Affichage des messages d'erreur ou de succès -->
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success)) : ?>
                <div class="alert alert-success text-center"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Nom complet" required>
                </div>

                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>

                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
                </div>

                <div class="mb-3">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirmer le mot de passe" required>
                </div>

                <button type="submit" class="btn btn-warning w-100 mt-3">S'INSCRIRE</button>
            </form>

            <div class="text-center mt-3">
                <a href="auth.php" class="text-white">Déjà un compte ? Connectez-vous</a>
            </div>
        </div>
    </div>

</body>
</html>
