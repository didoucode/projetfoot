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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: rgb(226, 220, 220);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 800px;
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 0;
            overflow: hidden;
            display: flex;
        }

        .form-side {
            width: 50%;
            padding: 40px;
            background-color: white;
        }

        .welcome-side {
            width: 50%;
            background-color: #BBF000;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
            position: relative;
            transition: transform 0.6s ease-in-out;
        }

        .welcome-title {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .welcome-login {
            border: 2px solid white;
            padding: 8px 25px;
            border-radius: 8px;
            margin-top: 20px;
            display: inline-block;
            transition: 0.3s;
        }

        .welcome-login:hover {
            background-color: white;
            color: #007A33;
        }
    </style>
</head>
<body>

        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="content">
    
<?php include '../includes/header.php'; ?>

    <div class="container">
        
        <div class="login-box">
            <div class="form-side">
                <h2 class="text-center">S'INSCRIRE</h2>
                <form action="" method="POST">
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Nom utilisateur" required>
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
                    <button type="submit" class="btn btn-primary w-100 mt-3">S'INSCRIRE</button>
                </form>
            </div>
            <div class="welcome-side">
                <div class="welcome-title">Welcome Back!</div>
                <a href="auth.php" class="welcome-login">Login</a>
            </div>
        </div>
    </div>
    </div>
</body>

</html>