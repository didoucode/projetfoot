
<?php
$base_path = (strpos($_SERVER['SCRIPT_FILENAME'], '/pages/') !== false) ? '../' : '';
?>
<link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">

<div class="sidebar">
    <a href="/site_football/index.php"><i class="fas fa-home"></i></a>
    <a href="/site_football/pages/trophees.php"><i class="fas fa-trophy"></i></a>
    <a href="/site_football/pages/statistiques.php"><i class="fas fa-chart-line"></i></a>
    <a href="/site_football/pages/auth.php"><i class="fas fa-user"></i></a>
</div>


