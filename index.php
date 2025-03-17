<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Live Football Maroc</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Palette de couleurs */
        :root {
            --vert-fonce: #004d00;
            --vert-clair: #00b300;
            --blanc: #ffffff;
            --noir: #222222;
        }

        /* Styles globaux */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        /* Sidebar */
        .sidebar {
            width: 80px;
            height: 100vh;
            background: var(--vert-fonce);
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
            z-index: 1000;
        }
        .sidebar a {
            color: white;
            font-size: 20px;
            margin: 20px 0;
            transition: 0.3s;
        }
        .sidebar a:hover {
            color: var(--vert-clair);
            transform: scale(1.2);
        }

        /* Contenu principal */
        .content {
            margin-left: 100px;
            padding: 20px;
        }

        /* Navbar */
        .navbar {
            background-color: var(--vert-fonce);
        }
        .navbar .nav-link {
            color: white !important;
            font-weight: bold;
        }
        .navbar .nav-link:hover {
            color: var(--vert-clair) !important;
        }

        /* Live Match */
        .live-match {
            position: relative;
            height: 350px;
            color: white;
            padding: 20px;
            display: flex;
            align-items: flex-end;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .slideshow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .slideshow img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        
        .slideshow img.active {
            opacity: 1;
        }
        
        .live-info {
            background: rgba(0, 0, 0, 0.7);
            padding: 15px;
            border-radius: 10px;
            position: relative;
            z-index: 10;
            width: 100%;
        }
        
        .badge.bg-danger {
            background-color: var(--vert-clair) !important;
        }

        /* Match Cards */
        .match-card {
            border-radius: 10px;
            padding: 15px;
            transition: 0.3s;
            background: var(--blanc);
            position: relative;
            overflow: hidden;
        }
        
        .match-card:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .match-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            transition: transform 0.5s ease;
        }
        
        .match-card:hover .match-img {
            transform: scale(1.2);
        }

        /* Actualités */
        .side-news {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .side-news img {
            width: 100%;
            border-radius: 10px;
            transition: transform 0.5s ease;
        }
        
        .side-news:hover img {
            transform: scale(1.1);
        }
        
        .news-title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 77, 0, 0.8);
            color: white;
            padding: 10px;
            margin: 0;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        
        /* Galerie d'images */
        .gallery-container {
            margin-top: 30px;
        }
        
        .gallery {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            gap: 15px;
            padding: 15px 0;
        }
        
        .gallery::-webkit-scrollbar {
            height: 8px;
        }
        
        .gallery::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .gallery::-webkit-scrollbar-thumb {
            background: var(--vert-fonce);
            border-radius: 10px;
        }
        
        .gallery-item {
            min-width: 200px;
            height: 150px;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .gallery-item:hover img {
            transform: scale(1.2);
        }
        
        /* Statistiques avec animation */
        .stats-container {
            margin-top: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: 0.3s;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 20px rgba(0,0,0,0.2);
        }
        
        .stat-icon {
            font-size: 30px;
            color: var(--vert-fonce);
            margin-bottom: 10px;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: var(--vert-fonce);
        }
        
        /* Bannière des équipes */
        .teams-banner {
            margin-top: 30px;
            position: relative;
            height: 150px;
            background: linear-gradient(to right, var(--vert-fonce), var(--vert-clair));
            border-radius: 10px;
            overflow: hidden;
        }
        
        .teams-slider {
            display: flex;
            width: auto;
            position: absolute;
            animation: slideTeams 30s linear infinite;
        }
        
        .team-logo {
    width: 100px;
    height: 100px;
    margin: 25px;
    background-color: white;
    border-radius: 50%;  /* Ceci rend le conteneur circulaire */
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
        
        .team-logo img {
            width: 100px;   
            height: 100px;  
            object-fit: contain;
            border-radius: 50%;
        }
        
        @keyframes slideTeams {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-1000px);
            }
        }
        
        /* Prochains matchs */
        .upcoming-matches {
            margin-top: 30px;
        }
        
        .upcoming-match-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .upcoming-match-card:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .team-info {
            display: flex;
            align-items: center;
        }
        
        .vs-badge {
            background-color: var(--vert-fonce);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            margin: 0 10px;
        }
        
        .match-date {
            color: var(--vert-fonce);
            font-weight: bold;
        }
        
    </style>
</head>
<body>

    <!-- Barre latérale -->
    <div class="sidebar">
        <a href="#"><i class="fas fa-home"></i></a>
        <a href="#"><i class="fas fa-trophy"></i></a>
        <a href="#"><i class="fas fa-chart-line"></i></a>
        <a href="./pages/Equipes.php"><i class="fas fa-user"></i></a>
    </div>

    <!-- Contenu principal -->
    <div class="content">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light shadow-sm mb-4">
            <div class="container-fluid">
            <a class="navbar-brand text-white fw-bold" href="#">
            <img src="assets/images/logo.jpg" alt="Logo" style="height: 40px; margin-right: 10px;">
            Foot Atlass
        </a>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Botola Pro</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Coupe du Trône</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Équipes</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-success text-white" href="pages/logout.php">Se connecter</a></li>
                </ul>
            </div>
        </nav>

        <!-- Live Matches avec slideshow -->
        <div class="live-match">
            <div class="slideshow">
                <img src="assets/images/stade5.jpg" alt="Stade Maroc 1" class="active">
                <img src="assets/images/stade2.jpg" alt="Stade Maroc 2">
                <img src="assets/images/stade3.jpg" alt="Stade Maroc 3">
                <img src="assets/images/stade4.jpg" alt="Stade Maroc 4">
            </div>
            <div class="live-info">
                <h2>Match Actuel</h2>
                <p>Wydad Casablanca vs Raja Casablanca</p>
                <span class="badge bg-danger">Score: 1 - 1</span>
                <span class="badge bg-secondary ms-2">Temps: 67'</span>
            </div>
        </div>

        <!-- Bannière d'équipes défilantes -->
        <div class="teams-banner mt-4">
            <div class="teams-slider">
                <div class="team-logo"><img src="assets/images/wac.jpg" alt="Wydad"></div>
                <div class="team-logo"><img src="assets/images/Rj.jpg" alt="Raja"></div>
                <div class="team-logo"><img src="assets/images/faar.jpg" alt="FAR"></div>
                <div class="team-logo"><img src="assets/images/fus.jpg" alt="FUS"></div>
                <div class="team-logo"><img src="assets/images/mat.jpg" alt="MAT"></div>
                <div class="team-logo"><img src="assets/images/ocsf.jpg" alt="OCS"></div>
                <div class="team-logo"><img src="assets/images/husa.jpg" alt="HUSA"></div>
                <div class="team-logo"><img src="assets/images/dhj.jpg" alt="DHJ"></div>
                <div class="team-logo"><img src="assets/images/rsb.jpg" alt="RSB"></div>
                <div class="team-logo"><img src="assets/images/mco.jpg" alt="MCO"></div>
                <div class="team-logo"><img src="assets/images/wac.jpg" alt="Wydad"></div>
                <div class="team-logo"><img src="assets/images/Rj.jpg" alt="Raja"></div>
                <div class="team-logo"><img src="assets/images/faar.jpg" alt="FAR"></div>
                <div class="team-logo"><img src="assets/images/fus.jpg" alt="FUS"></div>
                <div class="team-logo"><img src="assets/images/mat.jpg" alt="MAT"></div>
            </div>
        </div>

        <!-- Matchs récents et actualités -->
        <div class="row mt-4">
            <div class="col-md-8">
                <h4>Derniers Résultats</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="match-card shadow-sm p-3 mb-3">
                            <div class="d-flex align-items-center">
                                <img src="assets/images/wac.jpg" class="match-img me-2" alt="Wydad">
                                <span class="fw-bold">Wydad</span>
                                <span class="ms-auto">2 - 1</span>
                                <img src="assets/images/fus.jpg" class="match-img ms-2" alt="FUS">
                                <span class="fw-bold">FUS Rabat</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="match-card shadow-sm p-3 mb-3">
                            <div class="d-flex align-items-center">
                                <img src="assets/images/Rj.jpg" class="match-img me-2" alt="Raja">
                                <span class="fw-bold">Raja</span>
                                <span class="ms-auto">0 - 0</span>
                                <img src="assets/images/faar.jpg" class="match-img ms-2" alt="FAR">
                                <span class="fw-bold">FAR Rabat</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="match-card shadow-sm p-3 mb-3">
                            <div class="d-flex align-items-center">
                                <img src="assets/images/mco.jpg" class="match-img me-2" alt="MCO">
                                <span class="fw-bold">MCO</span>
                                <span class="ms-auto">3 - 2</span>
                                <img src="assets/images/husa.jpg" class="match-img ms-2" alt="HUSA">
                                <span class="fw-bold">HUSA</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="match-card shadow-sm p-3 mb-3">
                            <div class="d-flex align-items-center">
                                <img src="assets/images/rsb.jpg" class="match-img me-2" alt="RSB">
                                <span class="fw-bold">RSB</span>
                                <span class="ms-auto">1 - 0</span>
                                <img src="assets/images/dhj.jpg" class="match-img ms-2" alt="DHJ">
                                <span class="fw-bold">DHJ</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Prochains matchs -->
                <div class="upcoming-matches">
                    <h4>Prochains Matchs</h4>
                    <div class="upcoming-match-card">
                        <div class="team-info">
                            <img src="assets/images/faar.jpg" class="match-img" alt="FAR">
                            <span class="fw-bold ms-2">FAR Rabat</span>
                        </div>
                        <span class="vs-badge">VS</span>
                        <div class="team-info">
                            <span class="fw-bold me-2">Wydad</span>
                            <img src="assets/images/wac.jpg" class="match-img" alt="Wydad">
                        </div>
                        <span class="match-date">17 Mars 2025, 19:00</span>
                    </div>
                    
                    <div class="upcoming-match-card">
                        <div class="team-info">
                            <img src="assets/images/husa.jpg" class="match-img" alt="HUSA">
                            <span class="fw-bold ms-2">HUSA</span>
                        </div>
                        <span class="vs-badge">VS</span>
                        <div class="team-info">
                            <span class="fw-bold me-2">Raja</span>
                            <img src="assets/images/Rj.jpg" class="match-img" alt="Raja">
                        </div>
                        <span class="match-date">18 Mars 2025, 17:00</span>
                    </div>
                </div>
                
                <!-- Statistiques -->
                <div class="stats-container">
                    <h4>Statistiques de la saison</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-futbol"></i></div>
                                <div class="stat-number">127</div>
                                <div class="stat-label">Buts marqués</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-user-ninja"></i></div>
                                <div class="stat-number">14</div>
                                <div class="stat-label">Meilleur buteur</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-users"></i></div>
                                <div class="stat-number">42%</div>
                                <div class="stat-label">Taux d'occupation</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-stopwatch"></i></div>
                                <div class="stat-number">23</div>
                                <div class="stat-label">Journées disputées</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actualités avec effet de survol -->
            <div class="col-md-4">
                <h4>Actualités</h4>
                <div class="side-news">
                    <img src="assets/images/coupe-trone.jpg" alt="Coupe du Trône">
                    <p class="news-title">Le Raja remporte la Coupe du Trône 2024 !</p>
                </div>
                <div class="side-news">
                    <img src="assets/images/botola.jpg" alt="Botola Pro">
                    <p class="news-title">Le Wydad toujours leader de la Botola Pro</p>
                </div>
                <div class="side-news">
                    <img src="assets/images/teams2.jpg" alt="Sélection nationale">
                    <p class="news-title">La sélection nationale en préparation pour la CAN 2025</p>
                </div>
                <div class="side-news">
                    <img src="assets/images/talent.jpg" alt="Jeunes talents">
                    <p class="news-title">Les jeunes talents du foot marocain qui montent</p>
                </div>
            </div>
        </div>
        
        <!-- Galerie d'images défilantes -->
        <div class="gallery-container">
            <h4>Galerie Photos</h4>
            <div class="gallery">
                <div class="gallery-item">
                    <img src="assets/images/match1.jpg" alt="Match 1">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/match6.jpg" alt="Match 2">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/match3.jpg" alt="Match 3">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/match4.jpg" alt="Match 4">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/match5.jpg" alt="Match 5">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/match6.jpg" alt="Match 6">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/match3.jpg" alt="Match 7">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/match1.jpg" alt="Match 8">
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script pour le diaporama -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Diaporama pour la section "Live Match"
            const slides = document.querySelectorAll('.slideshow img');
            let currentSlide = 0;
            
            function nextSlide() {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].classList.add('active');
            }
            
            // Changer d'image toutes les 5 secondes
            setInterval(nextSlide, 5000);
        });
    </script>
</body>
</html>