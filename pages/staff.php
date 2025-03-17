<?php
session_start();
// Connexion à la base de données avec PDO
$servername = "localhost";
$username = "root"; // Remplacez par votre nom d'utilisateur
$password = ""; // Remplacez par votre mot de passe
$dbname = "site_foot"; // Remplacez par votre nom de base de données

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Définir le mode d'erreur de PDO pour les exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion échouée: " . $e->getMessage());
}

// Supprimer un staff
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM staff WHERE id = :id";
    
    // Préparer la requête et l'exécuter
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute(); // On exécute la requête sans afficher de message
}

// Afficher tous les staffs
$sql = "SELECT * FROM staff";
$stmt = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Staffs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        /* Appliquer un fond noir à la page */
        body {
            background-color: #000; /* Fond noir */
            color: #fff; /* Texte en blanc */
            font-family: Arial, sans-serif;
        }

        /* Style pour la navbar */
        .navbar {
            background-color: #28a745; /* Vert pour la navbar */
        }

        .navbar a {
            color: white !important; /* Textes blancs dans la navbar */
        }

        .navbar a:hover {
            color: #ddd !important; /* Couleur claire au survol des liens */
        }

        /* Style pour la table */
        table {
            background-color: rgba(255, 255, 255, 0.8); /* Fond blanc semi-transparent */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            text-align: center;
            padding: 10px;
        }

        /* En-têtes de la table en noir */
        th {
            background-color: #000; /* Noir pour les en-têtes */
            color: white;
        }

        /* Alternance des lignes (blanc et gris) */
        tr:nth-child(odd) {
            background-color: #ffffff; /* Ligne impaire : blanc */
        }

        tr:nth-child(even) {
            background-color: #f2f2f2; /* Ligne paire : gris clair */
        }

        /* Réduire la taille de la colonne Actions */
        th:nth-child(3), td:nth-child(3) {
            width: 200px; /* Réduit la largeur de la colonne Actions */
        }

        /* Style pour les boutons */
        .btn-info, .btn-danger {
            width: 48%; /* Définit la largeur de chaque bouton à 48% */
            margin-right: 4%; /* Ajoute un petit espace entre les boutons */
        }

        /* Ajustement pour le bouton "Supprimer" */
        .btn-danger {
            width: 50%; /* Augmente légèrement la largeur du bouton "Supprimer" */
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #117a8b;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .bottom-buttons {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 10;
        }

        /* Bouton Ajouter en vert */
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        /* Style pour le bouton Retour */
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        /* Ajout de marges et de padding pour rendre l'interface plus claire */
        .container {
            margin-top: 50px;
            padding: 20px;
        }

        h1 {
            color: #fff;
            font-size: 2rem;
        }

        /* Style des boutons dans la colonne Actions alignés à gauche */
        td {
            text-align: left;
        }

        /* Alignement des boutons "Voir" et "Supprimer" côte à côte */
        .action-buttons {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #28a745;">
    <a class="navbar-brand" href="#">Staff</a>
    <div class="ml-auto">
    <img src="../assets/images/logo.jpg" alt="Connexion" id="login-btn" style="  width: 80px;">
    </div>
</nav>

    <div class="container mt-5">
        <h1>Liste des Staffs</h1>

        <!-- Affichage de la liste des staffs -->
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($stmt->rowCount() > 0) {
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td>{$row['nom']}</td>
                                <td>{$row['prenom']}</td>
                                <td class='action-buttons'>
                                    <a href='voir_staff.php?id={$row['id']}' class='btn btn-info'>Voir</a>
                                    <a href='staff.php?delete={$row['id']}' class='btn btn-danger' onclick='return confirm(\"Voulez-vous vraiment supprimer ce staff ?\");'>Supprimer</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Aucun staff trouvé.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Boutons de navigation en bas à gauche -->
    <div class="bottom-buttons">
        <a href="dashboard_football.php" class="btn btn-secondary">Retour</a>
        <a href="ajouter_staff.php" class="btn btn-success">Ajouter un Staff</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn = null; // Fermer la connexion
?>
