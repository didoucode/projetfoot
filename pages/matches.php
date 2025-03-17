<?php
include "../config/db.php";

$stmt = $pdo->query("SELECT m.*, e1.nom as equipe1, e2.nom as equipe2 
                     FROM matchs m
                     JOIN equipe e1 ON m.equipe1_id = e1.id
                     JOIN equipe e2 ON m.equipe2_id = e2.id");

$matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($matchs as $match) {
    echo "
    <div class='match-card'>
        <h4>{$match['equipe1']} vs {$match['equipe2']}</h4>
        <p><strong>Score :</strong> {$match['score_equipe1']} - {$match['score_equipe2']}</p>
        <button class='btn btn-success vote-btn' data-match='{$match['id']}' data-equipe='{$match['equipe1_id']}'>Vote {$match['equipe1']}</button>
        <button class='btn btn-danger vote-btn' data-match='{$match['id']}' data-equipe='{$match['equipe2_id']}'>Vote {$match['equipe2']}</button>
        
        <div class='chat-box mt-3' id='chat-{$match['id']}'></div>
        <input type='text' class='form-control message mt-2' data-match='{$match['id']}' placeholder='Envoyer un message'>
    </div>
    ";
}
?>

<script>
$(".vote-btn").click(function() {
    let match_id = $(this).data("match");
    let equipe_id = $(this).data("equipe");
    $.post("vote.php", {match_id: match_id, equipe_id: equipe_id}, function(response) {
        alert(response);
        location.reload();
    });
});

$(".message").keypress(function(e) {
    if (e.which == 13) {
        let match_id = $(this).data("match");
        let message = $(this).val();
        $.post("chat.php", {match_id: match_id, message: message}, function() {
            location.reload();
        });
    }
});
</script>
