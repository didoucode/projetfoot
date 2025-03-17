
<style>
#login-btn {
    position: fixed;
    top: 10px;
    right: 10px;
    width: 90px;
    cursor: pointer;
    z-index: 1000;
}

</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Foot Atlas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="pages/auth.php">se connecter</a></li>
                <li class="nav-item"><a class="nav-link" href="pages/logout.php">Déconnexion</a></li>
                <li class="nav-item"><a class="nav-link" href="pages/contact.php">Contact</a></li>
                <li class="nav-item"><a class="nav-link" href="pages/joueurs.php"></a>Équipes</li>
            </ul>
        </div>

        <img src="assets/images/logo.jpg" alt="Connexion" id="login-btn">


</nav>


<!--
 
    <nav class="navbar navbar-expand-lg navbar-dark">
    <img src="../assets/images/logo.jpg" alt="Connexion" id="login-btn" style="  width: 80px;">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-trophy me-2"></i>Dashboard 
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user me-1"></i>Profil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user_joueurs.php">
                            <i class="fas fa-users me-1"></i>Joueurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="equipes.php">
                            <i class="fas fa-futbol me-1"></i>Équipes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-home me-2"></i>Acceuil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
-->

<script>document.getElementById("login-btn").addEventListener("click", function() {
    document.getElementById("login-popup").style.display = "block";
});

document.querySelector(".close-btn").addEventListener("click", function() {
    document.getElementById("login-popup").style.display = "none";
});
</script>