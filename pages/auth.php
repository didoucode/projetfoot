<?php
session_start();
include "../config/db.php"; // Connexion à la base de données

if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/home.php"); // Redirige si déjà connecté
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id,role, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Stocker les informations dans la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role']; // Ajout du rôle dans la session
        
            // Redirection selon le rôle de l'utilisateur
            switch ($user['role']) {
                case 'admin':
                    header("Location: dashboard_football.php"); // Redirection admin
                    break;
                case 'admin_tournoi':
                     header("Location: admin/tournoi.php"); // Redirection admin tournoi
                     break;
                 case 'user':
                    header("Location: home.php"); // Redirection utilisateur normal
                    break;
              default:
                       header("Location: ../index.php"); // Redirection utilisateur normal
                       break;

            }
            exit();
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
        
}
}

// Traitement de la réinitialisation du mot de passe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['forgot_password'])) {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = rand(100000, 999999);
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?) 
                               ON DUPLICATE KEY UPDATE token = ?, created_at = NOW()");
        $stmt->execute([$email, $token, $token]);

        mail($email, "Réinitialisation du mot de passe", "Votre code de réinitialisation est : $token");

        $message = "Un code de réinitialisation a été envoyé à votre email.";
    } else {
        $message = "Adresse email non trouvée.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Résultats Foot</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0a0a0a;
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
            width: 350px;
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

        a {
            text-decoration: none;
            font-size: 14px;
        }

        a.text-white:hover {
            color: #ddd;
        }

        a.text-warning:hover {
            color: #ffa500;
        }

        .btn-warning {
            background-color: #ffa500;
            border: none;
            font-weight: bold;
        }

        .btn-warning:hover {
            background-color: #ff8c00;
        }

        #forgot-password-form {
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>

 

    <div class="background"></div>
   

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="login-box p-4 rounded">
            <h2 class="text-center">LOG IN</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="USERNAME" required>
                </div>

                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="PASSWORD" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="#" class="text-white" id="forgot-password-link">Mot de passe oublié</a>
                    <a href="register.php" class="text-warning">S'inscrire</a>
                </div>

                <button type="submit" name="login" class="btn btn-warning w-100 mt-3">CONNEXION</button>
            </form>

            <!-- Formulaire caché pour mot de passe oublié -->
            <div id="forgot-password-form">
                <form action="reset_password.php" method="post">
                    <div class="mt-3">
                        <input type="email" name="email" class="form-control" placeholder="Entrez votre email" required>
                    </div>
                 
                    <button type="submit" name="forgot_password" class="btn btn-warning w-100 mt-2">Envoyer à l'email</button>
                </form>
            </div>

            <?php if (isset($message)): ?>
             <div class="alert alert-info text-center mt-3"><?php echo $message; ?></div>
          <?php endif; ?>
        </div>

    </div>

    <script>
        document.getElementById('forgot-password-link').addEventListener('click', function(event) {
            event.preventDefault();
            var forgotForm = document.getElementById('forgot-password-form');
            forgotForm.style.display = (forgotForm.style.display === 'none' || forgotForm.style.display === '') ? 'block' : 'none';
        });
    </script>

</body>
</html>
