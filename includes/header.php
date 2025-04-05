<header>
    <!-- Barre de navigation -->
    <?php
$base_path = (strpos($_SERVER['SCRIPT_FILENAME'], '/pages/') !== false) ? '../' : '';
?>
<link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">


    <nav class="navbar navbar-expand-lg navbar-light shadow-sm mb-4" style="background-color: #004d00;">
        <div class="container-fluid">
            <a class="navbar-brand text-white fw-bold" href="#">
            <?php
// Vérifier si la page est dans le dossier "pages"
$path = (strpos($_SERVER['SCRIPT_FILENAME'], '/pages/') !== false) ? '../' : '';
?>
<img src="<?php echo $path; ?>assets/images/logo.jpg" alt="Logo"

                style="height: 40px; margin-right: 10px;">
                Foot Atlass
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="/site_football/pages/botolapro.php">Botola Pro</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="/site_football/pages/CoupeTrone.php">Coupe du Trône</a></li>
                
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="/site_football/pages//site_football/pages/sandage.php">Sandage</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="/site_football/pages/Equipes.php">Equipes</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="#">Contact</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="/site_football/pages/logout.php"> Deconnexion</a></li>
               


            </ul>
        </div>
    </nav>
</header>
