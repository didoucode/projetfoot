<?php
session_start();
// Connexion à la base de données avec PDO

include "../config/db.php";
// Vérifier si l'ID est passé en paramètre
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM staff WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Aucun staff trouvé.";
        exit();
    }
}

// Mettre à jour les informations du staff
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $poste = $_POST['poste'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $date_naissance = $_POST['date_naissance'];
    $nationalite = $_POST['nationalite'];
    $date_embauche = $_POST['date_embauche'];
    $salaire = $_POST['salaire'];

    $sql = "UPDATE staff SET nom=:nom, prenom=:prenom, poste=:poste, email=:email, 
            telephone=:telephone, date_naissance=:date_naissance, nationalite=:nationalite, 
            date_embauche=:date_embauche, salaire=:salaire WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    
    // Lier les paramètres
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':poste', $poste);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':date_naissance', $date_naissance);
    $stmt->bindParam(':nationalite', $nationalite);
    $stmt->bindParam(':date_embauche', $date_embauche);
    $stmt->bindParam(':salaire', $salaire);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: voir_staff.php?id=$id");
        exit();
    } else {
        echo "Erreur lors de la mise à jour.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Staff</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        /* Style pour le navbar */
        .navbar {
            background-color: #006400; /* Dark Green */
        }
        .navbar-nav .nav-link {
            color: white !important;
        }
        .navbar-nav .nav-link:hover {
            color: #ff8c00 !important; /* Changer la couleur au survol */
        }
        
        /* Style pour le bouton Enregistrer */
        .btn-enregistrer {
            background-color: #006400; /* Dark Green */
            color: white;
        }
        .btn-enregistrer:hover {
            background-color: #004d00; /* Couleur un peu plus foncée au survol */
        }

        /* Style pour le formulaire */
        .form-container {
            background-color:rgb(21, 153, 32); /* Vert clair */
            padding: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">Staff</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Accueil</a>
            </li>
        </ul>
    </div>
    <!-- Ajouter l'image avec une largeur plus grande et une hauteur un peu plus petite -->
    <img src="../assets/images/logo.jpg" alt="Connexion" id="login-btn" style="  width: 80px;">
</nav>

    <div class="container mt-5 form-container">
        <h1>⚽Modifier Staff⚽</h1>

        <form method="POST">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $staff['nom']; ?>" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo $staff['prenom']; ?>" required>
            </div>
            <div class="form-group">
                <label for="poste">Poste</label>
                <input type="text" class="form-control" id="poste" name="poste" value="<?php echo $staff['poste']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $staff['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo $staff['telephone']; ?>" required>
            </div>
            <div class="form-group">
                <label for="date_naissance">Date de naissance</label>
                <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?php echo $staff['date_naissance']; ?>" required>
            </div>
            <div class="form-group">
                <label for="nationalite">Nationalité</label>
                <input type="text" class="form-control" id="nationalite" name="nationalite" value="<?php echo $staff['nationalite']; ?>" required>
            </div>
            <div class="form-group">
                <label for="date_embauche">Date d'embauche</label>
                <input type="date" class="form-control" id="date_embauche" name="date_embauche" value="<?php echo $staff['date_embauche']; ?>" required>
            </div>
            <div class="form-group">
                <label for="salaire">Salaire</label>
                <input type="number" class="form-control" id="salaire" name="salaire" value="<?php echo $staff['salaire']; ?>" required>
            </div>

            <!-- Bouton Retour -->
            <a href="voir_staff.php?id=<?php echo $staff['id']; ?>" class="btn btn-secondary ml-2">Retour</a>

            <!-- Bouton Enregistrer (inversé) -->
            <button type="submit" class="btn btn-enregistrer">Enregistrer</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$pdo = null; // Fermer la connexion PDO
?>
