<?php
include "../config/db.php";

$query = $_POST['query'];

// Rechercher équipes
$stmt_equipes = $pdo->prepare("SELECT * FROM equipes WHERE nom LIKE ?");
$stmt_equipes->execute(["%$query%"]);
$equipes = $stmt_equipes->fetchAll(PDO::FETCH_ASSOC);

// Rechercher joueurs
$stmt_joueurs = $pdo->prepare("SELECT j.nom, j.position, e.nom as equipe 
                               FROM joueurs j 
                               JOIN equipes e ON j.equipe_id = e.id
                               WHERE j.nom LIKE ?");
$stmt_joueurs->execute(["%$query%"]);
$joueurs = $stmt_joueurs->fetchAll(PDO::FETCH_ASSOC);

// Affichage des équipes
foreach ($equipes as $equipe) {
    echo "<div class='card p-2 m-1'>
            <h5>{$equipe['nom']}</h5>
            <p>Ville : {$equipe['ville']}</p>
            <p>Entraîneur : {$equipe['entraineur']}</p>
            <button class='btn btn-primary subscribe' data-id='{$equipe['id']}'>S'abonner</button>
          </div>";
}

// Affichage des joueurs
foreach ($joueurs as $joueur) {
    echo "<div class='card p-2 m-1'>
            <h5>{$joueur['nom']}</h5>
            <p>Position : {$joueur['position']}</p>
            <p>Équipe : {$joueur['equipe']}</p>
          </div>";
}
?>
<script>
$(".subscribe").click(function() {
    let equipe_id = $(this).data("id");
    $.post("subscribe.php", {equipe_id: equipe_id}, function() {
        alert("Abonnement réussi !");
        location.reload();
    });
});
</script>
