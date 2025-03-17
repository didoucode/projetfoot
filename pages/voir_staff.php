<?php
session_start();
// Connexion à la base de données avec PDO

include "../config/db.php";

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']); // Sécurisation de l'ID

        $sql = "SELECT * FROM staff WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $staff = $stmt->fetch();
        } else {
            echo "Aucun staff trouvé.";
        }
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Staff</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        /* Style global */
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        /* Navbar */
        .navbar {
            background-color: #FFA500;
        }

        .navbar-brand, .navbar-nav .nav-link {
            color: #000 !important;
            font-weight: bold;
        }

        /* Conteneur principal */
        .container {
            margin-top: 50px;
            background-color: rgba(0, 0, 0, 0.85);
            padding: 25px;
            border-radius: 10px;
            width: 65%;
            min-width: 400px;
            box-shadow: 0 5px 10px rgba(255, 165, 0, 0.5);
        }

        /* Style du titre */
        h1 {
            color: #FFA500;
            font-size: 2.2rem;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Champs des informations */
        .field {
            border: 2px solid #32CD32;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            background-color: rgba(50, 205, 50, 0.15);
        }

        .field label {
            font-weight: bold;
            color: #FFA500;
            width: 30%;
        }

        .field p {
            margin: 0;
            color: #fff;
            width: 65%;
            text-align: right;
        }

        /* Style des boutons */
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .btn-warning {
            background-color: #32CD32;
            border-color: #28a745;
            color: #000;
        }

        .btn-warning:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#">Gestion du Staff</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Accueil</a>
                    
                </li>
            </ul>
            <img src="../assets/images/logo.jpg" alt="Connexion" id="login-btn" style="  width: 80px;">
        </div>
    </nav>

    <div class="container">
        <h1>⚽ Détails du Staff ⚽</h1>

        <?php if (isset($staff)): ?>
            <div class="field">
                <label>Nom:</label>
                <p><?php echo htmlspecialchars($staff['nom']); ?></p>
            </div>
            <div class="field">
                <label>Prénom:</label>
                <p><?php echo htmlspecialchars($staff['prenom']); ?></p>
            </div>
            <div class="field">
                <label>Poste:</label>
                <p><?php echo htmlspecialchars($staff['poste']); ?></p>
            </div>
            <div class="field">
                <label>Email:</label>
                <p><?php echo htmlspecialchars($staff['email']); ?></p>
            </div>
            <div class="field">
                <label>Téléphone:</label>
                <p><?php echo htmlspecialchars($staff['telephone']); ?></p>
            </div>
            <div class="field">
                <label>Date de naissance:</label>
                <p><?php echo htmlspecialchars($staff['date_naissance']); ?></p>
            </div>
            <div class="field">
                <label>Nationalité:</label>
                <p><?php echo htmlspecialchars($staff['nationalite']); ?></p>
            </div>
            <div class="field">
                <label>Date d'embauche:</label>
                <p><?php echo htmlspecialchars($staff['date_embauche']); ?></p>
            </div>
        
        <?php endif; ?>

        <!-- Boutons alignés -->
        <div class="btn-container">
            <a href="staff.php" class="btn btn-secondary">Retour</a>
            <a href="modifier_staff.php?id=<?php echo htmlspecialchars($staff['id']); ?>" class="btn btn-warning">Modifier</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
