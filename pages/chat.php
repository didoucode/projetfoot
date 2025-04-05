<?php
/*
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    exit("Accès non autorisé");
}

$user_id = $_SESSION['user_id']; // L'ID de l'utilisateur connecté

// Envoi d'un message
if (isset($_POST['message'])) {
    $message = $_POST['message'];
    
    if (!empty($message)) {
        // Insérer le message dans la base de données
        $stmt = $pdo->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
        $stmt->execute([$user_id, $message]);
    }
    exit(); // Fin de la requête AJAX
}

// Récupérer les messages
$stmt = $pdo->prepare("SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at ASC");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Affichage des messages
foreach ($messages as $message) {
    echo "
    <div class='message'>
        <strong>{$message['username']} :</strong>
        <p>{$message['message']}</p>
        <small>Le {$message['created_at']}</small>
    </div>";
}*/




/*
session_start();
include "../config/db.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Si l'utilisateur n'est pas connecté
    exit(json_encode(["error" => "Vous devez être connecté pour envoyer un message."]));
} else {
    error_log('Utilisateur connecté avec l\'ID: ' . $_SESSION['user_id']);
}

$user_id = $_SESSION['user_id']; // L'ID de l'utilisateur connecté

// Envoi d'un message
$user_id = $_SESSION['user_id']; // L'ID de l'utilisateur connecté

// Envoi d'un message
if (isset($_POST['message'])) {
    $message = $_POST['message'];
    
    if (!empty($message)) {
        // Insérer le message dans la base de données
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
            $stmt->execute([$user_id, $message]);
            
            error_log('Message inséré avec succès');
        } catch (Exception $e) {
            error_log('Erreur lors de l\'insertion du message: ' . $e->getMessage());
        }
    }
    exit(); // Fin de la requête AJAX
}


$stmt = $pdo->prepare("SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at ASC");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($messages) {
    foreach ($messages as $message) {
        echo "<div class='message'>
                <strong>{$message['username']} :</strong>
                <p>{$message['message']}</p>
                <small>Le {$message['created_at']}</small>
              </div>";
    }
} else {
    echo "Aucun message disponible.";
}*/






session_start();
include "../config/db.php";

// ENVOI DE MESSAGE (POST)
if (isset($_POST['message'])) {
    // Vérification de connexion UNIQUEMENT pour l'envoi
    if (!isset($_SESSION['user_id'])) {
        exit(json_encode(["error" => "Vous devez être connecté pour envoyer un message."]));
    }

    $user_id = $_SESSION['user_id'];
    $message = $_POST['message'];
    $match_id = $_POST['match_id'];

    if (!empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (user_id, message, match_id) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $message, $match_id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log('Erreur : ' . $e->getMessage());
            echo json_encode(['error' => 'Erreur technique']);
        }
    }
    exit();
}

// LECTURE DES MESSAGES (GET)
if (isset($_GET['match_id'])) {
    $match_id = $_GET['match_id'];
    
    $stmt = $pdo->prepare("SELECT m.*, u.username 
                          FROM messages m 
                          JOIN users u ON m.user_id = u.id 
                          WHERE m.match_id = ? 
                          ORDER BY m.created_at ASC");
    $stmt->execute([$match_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($messages) {
        foreach ($messages as $message) {
            echo "<div class='message'>
                    <strong>{$message['username']} :</strong>
                    <p>{$message['message']}</p>
                    <small>Le {$message['created_at']}</small>
                  </div>";
        }
    } else {
        echo "Soyez le premier à commenter ce match !";
    }
    exit();
}

?>