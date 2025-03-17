<?php
include "../config/db.php";

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_staff"])) {
    // Récupération des données du formulaire
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $date_naissance = $_POST["date_naissance"];
    $nationalite = trim($_POST["nationalite"]);
    $poste = trim($_POST["poste"]);
    $telephone = !empty($_POST["telephone"]) ? trim($_POST["telephone"]) : NULL;
    $email = trim($_POST["email"]);
    $date_embauche = $_POST["date_embauche"];
    $created_at = date("Y-m-d H:i:s"); // Date actuelle

    try {
        // Requête préparée sans la colonne `salaire`
        $sql = "INSERT INTO staff (nom, prenom, date_naissance, nationalite, poste, telephone, email, date_embauche, created_at) 
                VALUES (:nom, :prenom, :date_naissance, :nationalite, :poste, :telephone, :email, :date_embauche, :created_at)";
        
        $stmt = $pdo->prepare($sql);
        
        // Exécution de la requête avec les valeurs sécurisées
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':date_naissance' => $date_naissance,
            ':nationalite' => $nationalite,
            ':poste' => $poste,
            ':telephone' => $telephone,
            ':email' => $email,
            ':date_embauche' => $date_embauche,
            ':created_at' => $created_at
        ]);

        // Redirection après succès
        echo "<script>alert('Le staff a été ajouté avec succès !'); window.location.href='staff.php';</script>";
    } catch (PDOException $e) {
        echo "Erreur lors de l'insertion : " . $e->getMessage();
    }
}

// Fermeture de la connexion (automatique avec PDO)
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Staff</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        /* Image de fond blanche */
        body {
            background-color: #fff;
            color: #fff; /* Texte en blanc */
            font-family: Arial, sans-serif;
        }

        /* Navbar dark green */
        .navbar {
            background-color: #004d00 !important; /* Dark green */
        }
        .navbar a {
            color: #fff !important;
        }

        /* Image à droite dans la navbar */
        .navbar img {
            width: 100px; /* Vous pouvez ajuster cette taille selon votre besoin */
            height: auto;
        }

        /* Formulaire avec fond dark green */
        .container {
            margin-top: 50px;
            padding: 20px;
            background-color: #004d00; /* Dark green */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
        }

        /* Champs de formulaire */
        .form-control {
            background-color: #003300; /* Vert foncé */
            color: #fff;
            border: 2px solid #28a745;
        }

        .form-control:focus {
            border-color: #6cfc6d;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.8);
        }

        /* Boutons */
        .btn-primary {
            background-color: rgb(10, 235, 63);
            border-color: #28a745;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        /* Conteneur pour les boutons */
        .button-container {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
   
        <a class="navbar-brand" href="index.php">Accueil</a>
        <!-- Image ajoutée à droite -->
        <div class="ml-auto">
            <img src="../assets/images/logo.jpg" alt="Connexion" id="login-btn" style="  width: 80px;">
        </div>
    </nav>

    <div class="container mt-5">
        <h1>⚽ Ajouter un nouveau Staff ⚽</h1>
        <form action="ajouter_staff.php" method="POST">
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" name="nom" class="form-control" id="nom" placeholder="Nom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom:</label>
                <input type="text" name="prenom" class="form-control" id="prenom" placeholder="Prénom" required>
            </div>
            <div class="form-group">
                <label for="date_naissance">Date de Naissance:</label>
                <input type="date" name="date_naissance" class="form-control" id="date_naissance" required>
            </div>
            <div class="form-group">
                <label for="nationalite">Nationalité:</label>
                <input type="text" name="nationalite" class="form-control" id="nationalite" placeholder="Nationalité" required>
            </div>
            <div class="form-group">
                <label for="poste">Poste:</label>
                <input type="text" name="poste" class="form-control" id="poste" placeholder="Poste" required>
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone:</label>
                <input type="tel" name="telephone" class="form-control" id="telephone" placeholder="Téléphone">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="date_embauche">Date d'Embauche:</label>
                <input type="date" name="date_embauche" class="form-control" id="date_embauche" required>
            </div>

            <!-- Conteneur pour les boutons -->
            <div class="button-container">
                <a href="staff.php" class="btn btn-secondary">Retour</a>
                <button type="submit" name="add_staff" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</body>
</html>
