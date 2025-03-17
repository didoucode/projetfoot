
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le mot de passe</title>

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

        .password-box {
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
        <div class="password-box p-4 rounded">
            <h2 class="text-center">Modifier le mot de passe</h2>

            <form action="../actions/update_password.php" method="post">
                <div class="mt-3">
              
                        <input type="text" name="code-verfication" class="form-control mb-2" placeholder=" Entrez le code" required>
                 
                    <input type="password" name="password" class="form-control mb-2" placeholder="Nouveau mot de passe" required>
                    <input type="password" name="confirm_password" class="form-control mb-2" placeholder="Confirmez le mot de passe" required>
                </div>
                <button type="submit" class="btn btn-warning w-100 mt-3">Modifier le mot de passe</button>
            </form>
        </div>
    </div>

</body>
</html>
