<?php

  session_start();
  include "../config/db.php";
  
  
  
  
  
  
  
  

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si non authentifié
    header("Location: auth.php");
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$query = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Vérifier si on a bien récupéré l'utilisateur
if (!$user) {
    // Détruire la session et rediriger en cas de problème
    session_destroy();
    header("Location: auth.php");
    exit();
}

$nom_utilisateur = htmlspecialchars($user['username']); // Protection contre les injections XSS

  ?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home -  Fooball Atlass</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
<body>

      <!-- Affichage des notifications -->
      <div class="notifications">
            <h2></h2>
            <ul class="list-group" id="notifications-list">
                <!-- Les notifications seront affichées ici dynamiquement -->
            </ul>
     </div>



    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content">
   
    <?php include '../includes/header.php'; ?>

        <!-- Header Banner -->
        <div class="header-banner mb-4">
        <style>
       :root {
            --vert: #007A33;
            --beige: #F5F5DC;
            --gris: #D3D3D3;
            --blanc: #FFFFFF;
            --noir: #222;
            --jaune:rgb(122, 153, 10);
        }
        html {
        scroll-behavior: smooth;
        }

        
        body {
            font-family: 'Arial', sans-serif;
            overflow-x: hidden;
        }
        
        .landing-container {
            height: 100vh;
            position: relative;
            background-color: var(--blanc);
        }
        
        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 60%;
            height: 100%;
            /* Remplacez cette URL par le chemin vers votre image locale */
            background-image: url('../assets/images/back2.jpg');
            background-size: cover;
            background-position: center;
            clip-path: polygon(0 0, 100% 0, 70% 100%, 0 100%);
            z-index: 0;
        }
        
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 60%;
            height: 100%;
            background-color: rgba(0, 122, 51, 0.3);
            clip-path: polygon(0 0, 100% 0, 70% 100%, 0 100%);
            z-index: 1;
        }
        
        .content {
            position: relative;
            z-index: 2;
            height: 100%;
        }
        
        .header {
            padding: 20px;
            color: var(--vert);
        }
        
        .main-content {
            padding: 20px;
        }
        
        .title {
            color: var(--vert);
            font-weight: 800;
            font-size: 3.5rem;
            text-transform: uppercase;
            line-height: 1.1;
        }
        
        .subtitle {
            color: var(--noir);
            font-size: 1rem;
            margin-top: 20px;
        }
        
        .circle-button {
            width: 60px;
            height: 60px;
            background-color: var(--vert);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blanc);
            font-size: 1.5rem;
            margin: 30px 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .circle-button:hover {
            background-color: var(--jaune);
            color: var(--noir);
        }
        
        .footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            padding: 0 20px;
            color: var(--vert);
            font-size: 0.9rem;
        }
        
        .grid-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 60%;
            height: 100%;
            background-image: 
                /* Remplacez cette URL par le chemin vers votre image locale */
                url('votre-image-grid.jpg'),
                linear-gradient(to right, rgba(0, 122, 51, 0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0, 122, 51, 0.1) 1px, transparent 1px);
            background-size: cover, 20px 20px, 20px 20px;
            background-position: center, 0 0, 0 0;
            clip-path: polygon(0 0, 100% 0, 70% 100%, 0 100%);
            z-index: 1;
        }
        
        .welcome-section {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-section h2 {
            color:#222;;
            font-weight: 100;
            margin-bottom: 10px;
        }
        
        .welcome-section p {
            color: var(--noir);
        }

        /* Style pour le bouton de discussion */
.bi.bi-chat-dots {
  display: inline-block;
  margin: 10px 0;
}

.bi.bi-chat-dots a {
  display: flex;
  align-items: center;
  background-color:var(--jaune) ;
  color: var(--blanc);
  padding: 10px 15px;
  border-radius: 5px;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
}

.bi.bi-chat-dots a i {
  margin-right: 8px;
  font-size: 1.1rem;
}

.bi.bi-chat-dots a:hover {
  background-color:var(--vert) ;
  color: var(--noir);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}



/*NOTIFICATION*/
/* Conteneur de notifications */
.notifications {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    width: 320px; /* Largeur du conteneur des notifications */
    max-height: 80vh; /* Limite la hauteur du conteneur */
    overflow-y: auto; /* Permet le défilement si le nombre de notifications est trop élevé */
}

/* Style de chaque notification */
.notification {
    background-color: rgba(255, 255, 255, 0.9); /* Fond blanc transparent */
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column; /* Empile les éléments de manière verticale */
    max-width: 100%;
    font-size: 14px; /* Taille de texte plus petite */
    word-wrap: break-word;
}

/* Style de l'emoji (sonnerie) */
.notification .emoji {
    font-size: 20px; /* Taille de l'emoji */
    margin-right: 10px;
    color: black; /* Couleur de l'emoji d'alarme */
    margin-bottom: 5px;
}

/* Contenu texte de la notification */
.notification p {
    flex-grow: 1;
    margin: 0;
    color: #333;
    font-size: 14px;
}

/* Styliser le lien "Ignorer" */
.ignore-link {
    color: #007A33;
    text-decoration: none;  /* Enlever le soulignement */
    margin-top: 5px;
    display: inline-block;
}

/* Ajoute une petite animation au survol du lien "Ignorer" */
.ignore-link:hover {
    text-decoration: underline;
    cursor: pointer;
}

/* Animation de la notification qui slide depuis le côté */
@keyframes slideIn {
    0% {
        transform: translateX(100%);
    }
    100% {
        transform: translateX(0);
    }
}

/* Défilement des notifications lorsque la liste est longue */
#notifications-list {
    padding: 0;
}

.chat-box {
            background: white;
            border-radius: 1rem;
            padding: 1rem;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: var(--card-shadow);
        }

        .message {
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 1rem;
            background: #f8f9fa;
        }


    </style>
</head>
<body>
    <div class="landing-container">
        <div class="bg-image"></div>
        <div class="overlay"></div>
        <div class="grid-overlay"></div>
        
        <div class="content">
            <div class="header d-flex justify-content-between">
                <div class="company">Football Atlas</div>
                <div class="page-number"></div>
            </div>
            
            <div class="row h-75">
                <div class="col-md-6"></div>
                <div class="col-md-5 d-flex align-items-center">
                    <div class="main-content">
                        <h1 class="title">Bienvenu <?php echo $nom_utilisateur; ?></h1>
                        <div class="bi bi-chat-dots">
                       
                        <a href="discussion.php#chat-section" class="bi bi-chat-dots">
                        <i class="bi bi-chat-dots"></i> Discussion 
                           </a>
                        </a>
                        </div>
                        <main>
                            <section class="welcome-section">
                            <h5> Bienvenue dans votre espace personnel. Suivez l'actualité de votre équipe, connecté avec la communauté des passionnés de football ! </h5>
                                <p></p>
                            </section>
                        </main>
                    </div>
                </div>
            </div>
            
            <div class="footer d-flex justify-content-between">
                <div>Presented by team fottAtlass</div>
                <div>Getting Started</div>
            </div>
        </div>
    </div>

 <!-- Zone de Chat -->
 <div id="chat-section" class="row mt-5">
   
    <div class="col-12">
      <h3><i class="fas fa-comments me-2"></i> Chat en direct</h3>
      </div>

        <!-- Zone de messages -->
        <div class="chat-box" id="chat-box">
            <!-- Les messages seront chargés ici -->
        </div>

        <!-- Formulaire d'envoi de message -->
        <form id="chat-form" class="mt-3">
            <div class="form-group">
                <textarea class="form-control" id="chat-message" placeholder="Écrivez un message..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Envoyer</button>
        </form>
    
</div>





    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- NOTIFICATIONS-->    
     
<script>
    // Fonction pour charger les notifications à partir du serveur
    function loadNotifications() {
        // Effectuer une requête AJAX pour récupérer les notifications
        fetch('get_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.notifications && data.notifications.length > 0) {
                    const notificationsList = document.getElementById('notifications-list');
                    notificationsList.innerHTML = ''; // Vider la liste avant d'ajouter de nouvelles notifications
                    
                    // Afficher chaque notification
                    data.notifications.forEach(notification => {
                        const notificationElement = document.createElement('div');
                        notificationElement.classList.add('notification');  // Appliquer la classe 'notification'
                        notificationElement.id = 'notification-' + notification.id;  // Attribuer un id unique pour chaque notification
                        
                        // Ajouter un timestamp pour la notification (quand elle a été vue pour la dernière fois)
                        const notificationTimestamp = new Date().getTime();

                        // Créer le contenu de la notification avec l'icône de la cloche et le lien Ignorer
                        notificationElement.innerHTML = `
                            <div class="emoji"><i class="fa fa-bell"></i></div> <!-- Icône de cloche -->
                            <p>${notification.message}</p>
                            <a href="#" class="ignore-link" onclick="ignoreNotification(${notification.id}, ${notificationTimestamp})">Ignorer</a>
                        `;
                        
                        // Ajouter la notification à la liste
                        notificationsList.appendChild(notificationElement);

                        // Vérifier si la notification doit être cachée ou réapparaître
                        checkNotificationVisibility(notificationElement, notificationTimestamp);
                    });
                }
            })
            .catch(error => console.error('Erreur lors de la récupération des notifications:', error));
    }

    // Fonction pour vérifier la visibilité de la notification en fonction du timestamp
    function checkNotificationVisibility(notificationElement, notificationTimestamp) {
        // Calculer le temps écoulé depuis l'affichage de la notification
        const currentTime = new Date().getTime();
        const timeElapsed = currentTime - notificationTimestamp;

        // Vérifier si 1 heure est écoulée (3600000 ms)
        if (timeElapsed >= 3600000) {
            // Si 1 heure est écoulée, cacher la notification
            setTimeout(() => {
                notificationElement.style.display = 'none';
            }, 3600000); // 1 heure en millisecondes

            // Si 2 heures sont écoulées, réafficher la notification
            setTimeout(() => {
                notificationElement.style.display = 'block';
            }, 7200000); // 2 heures en millisecondes
        }
    }

    // Fonction pour ignorer une notification
    function ignoreNotification(notificationId, notificationTimestamp) {
        // Cacher la notification de l'interface
        const notificationElement = document.getElementById('notification-' + notificationId);
        if (notificationElement) {
            notificationElement.style.display = 'none';  // Masquer la notification
        }

        // Envoyer une requête AJAX pour marquer la notification comme ignorée, si nécessaire
        fetch('ignore_notification.php?id=' + notificationId)
            .then(response => response.json())
            .then(data => console.log('Notification ignorée'))
            .catch(error => console.error('Erreur lors de l\'ignorance de la notification:', error));

        // Ajouter un délai pour réapparaître après 2 heures (ou selon l'intervalle souhaité)
        setTimeout(() => {
            notificationElement.style.display = 'block';
        }, 7200000); // 2 heures en millisecondes
    }

    // Charger les notifications initiales
    loadNotifications();

    // Mettre à jour les notifications toutes les 5 secondes
    setInterval(loadNotifications, 5000);


/***********************************************************************************/
//pour une petite chat
$(document).ready(function() {
    // Charger les messages
    function loadMessages() {
        $.get("chat.php", function(data) {
            $('#chat-box').html(data);
            $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight); // Faites défiler vers le bas
        });
    }

    // Envoyer un message
    $('#chat-form').submit(function(e) {
        e.preventDefault();

        var message = $('#chat-message').val();
        if (message.trim() != "") {
            $.post("chat.php", { message: message }, function(response) {
                $('#chat-message').val('');
                loadMessages(); // Recharge les messages après l'envoi
            });
        }
    });

    // Rafraîchir les messages automatiquement toutes les 3 secondes
    setInterval(loadMessages, 3000); // 3 secondes

    // Charger les messages dès que la page est chargée
    loadMessages();
});



</script>



</body>
</html>