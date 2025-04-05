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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color:rgb(226, 220, 220);
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
            background-color:rgb(122, 153, 10);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
        }

        h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .form-control {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 8px;
            color: #495057;
            padding: 12px 15px;
            margin-bottom: 20px;
        }

        .form-control:focus {
            border-color: #007A33;
            box-shadow: 0 0 0 0.2rem rgb(122, 153, 10);
        }

        .btn-primary {
            background-color: #007A33;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            transition: 0.3s ease-in-out;
            width: 100%;
            margin-top: 15px;
        }

        .btn-primary:hover {
            background-color: #004D00;
            transform: translateY(-2px);
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

        .social-login {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .social-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color:rgb(122, 153, 10);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
        }

        .social-icon:hover {
            background-color: #007A33;
        }

        #forgot-password-form {
            display: none;
            margin-top: 15px;
        }

        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 14px;
        }

        a {
            text-decoration: none;
            color: #6c8eff;
        }

        a:hover {
            text-decoration: underline;
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
                <h2>Registration</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>

                  

                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>

                    <div class="links">
                        <a href="#" class="forgot-password-link" id="forgot-password-link">Mot de passe oublié</a>
                        <a href="register.php" class="register-link">S'inscrire</a>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary">CONNEXION</button>

                    <div class="mt-3 text-center">
                        <p>ou connectez-vous avec</p>
                        <div class="social-login">
                            <div class="social-icon">G</div>
                            <div class="social-icon">f</div>
                            <div class="social-icon">O</div>
                            <div class="social-icon">in</div>
                        </div>
                    </div>
                </form>

                <!-- Formulaire caché pour mot de passe oublié -->
                <div id="forgot-password-form">
                    <form action="reset_password.php" method="post">
                        <div class="mt-3">
                            <input type="email" name="email" class="form-control" placeholder="Entrez votre email" required>
                        </div>
                     
                        <button type="submit" name="forgot_password" class="btn btn-primary w-100 mt-2">Envoyer à l'email</button>
                    </form>
                </div>

                <?php if (isset($message)): ?>
                 <div class="alert alert-info text-center mt-3"><?php echo $message; ?></div>
              <?php endif; ?>
            </div>
            <div class="welcome-side">
                <div class="welcome-title">Welcome Back!</div>
                <a href="login.php" class="welcome-login">Login</a>
            </div>
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