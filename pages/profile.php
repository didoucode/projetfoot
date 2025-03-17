<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Gérer l'upload de la photo et la modification du username
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && !empty($_POST['username'])) {
        $new_username = trim($_POST['username']);
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute([$new_username, $user_id]);
            $message = "Nom d'utilisateur mis à jour avec succès !";
        } catch(PDOException $e) {
            $error = "Erreur lors de la mise à jour du nom d'utilisateur.";
        }
    }

    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_photo']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($filetype, $allowed)) {
            $upload_dir = "../uploads/profiles/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_filename = "profile_" . $user_id . "." . $filetype;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
                $message .= " Photo de profil mise à jour avec succès !";
            } else {
                $error .= " Erreur lors de l'upload de la photo.";
            }
        } else {
            $error .= " Format de fichier non autorisé.";
        }
    }
}

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT username, email, created_at, verified FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier la photo de profil
$profile_photo = "../uploads/profiles/profile_" . $user_id . ".jpg";
if (!file_exists($profile_photo)) {
    $profile_photo = "../uploads/profiles/profile_" . $user_id . ".jpeg";
}
if (!file_exists($profile_photo)) {
    $profile_photo = "../uploads/profiles/profile_" . $user_id . ".png";
}
if (!file_exists($profile_photo)) {
    $profile_photo = "/assets/default-avatar.png";
}

$created_date = new DateTime($user['created_at']);
$created_formatted = $created_date->format('d/m/Y');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?php echo htmlspecialchars($user['username']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
        }

        .profile-header {
            background-color:rgb(42, 82, 51);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: block;
            background-color: #fff;
            padding: 3px;
            object-fit: cover;
        }

        .details-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            color:rgb(66, 178, 100);
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color:rgb(42, 82, 51);
        }

        .verified-badge {
            color: #28a745;
            margin-left: 0.5rem;
        }

        .detail-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            font-weight: bold;
            color: #666;
        }

        /* Styles additionnels pour l'édition */
        .profile-image-container {
            position: relative;
            width: 150px;
            margin: 0 auto;
        }

        .edit-photo-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .edit-photo-btn:hover {
            background: rgba(0, 0, 0, 0.7);
        }

        #profile_photo {
            display: none;
        }

        .username-input {
            background: transparent;
            border: none;
            color: white;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            width: auto;
            min-width: 200px;
        }

        .username-input:focus {
            background: rgba(255, 255, 255, 0.1);
            outline: none;
        }
    </style>
</head>
<body>
    <div class="container profile-container">
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Image de profil et en-tête -->
        <div class="profile-header text-center">
            <form method="POST" enctype="multipart/form-data" id="profile-form">
                <div class="profile-image-container">
                    <img src="<?php echo $profile_photo; ?>" alt="Photo de profil" class="profile-image">
                    <label for="profile_photo" class="edit-photo-btn" title="Changer la photo">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*" onchange="document.getElementById('profile-form').submit();">
                </div>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" 
                       class="username-input mb-2">
                <?php if($user['verified']): ?>
                    <i class="fas fa-check-circle verified-badge" title="Compte vérifié"></i>
                <?php endif; ?>
            </form>
            <p class="lead">Je suis un utilisateur de la plateforme</p>
        </div>

        <!-- Détails du profil -->
        <div class="details-card">
            <h2 class="text-center mb-4">Mes détails</h2>
            
            <div class="detail-item">
                <span class="detail-label">Nom d'utilisateur :</span>
                <span><?php echo htmlspecialchars($user['username']); ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Email :</span>
                <span><?php echo htmlspecialchars($user['email']); ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Membre depuis :</span>
                <span><?php echo $created_formatted; ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Statut :</span>
                <span>
                    <?php if($user['verified']): ?>
                        <span class="text-success">Vérifié <i class="fas fa-check-circle"></i></span>
                    <?php else: ?>
                        <span class="text-warning">En attente de vérification</span>
                    <?php endif; ?>
                </span>
            </div>

            <!-- Liens sociaux -->
            <div class="social-links">
                <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>

        <!-- Bouton de modification -->
        <div class="text-center mt-4">
        <a href="home.php" class="btn btn-outline-dark">Retour</a>
            <button type="submit" form="profile-form" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>