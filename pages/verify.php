<form action="../actions/check_reset_code.php" method="post">
    <input type="email" name="email" value="<?php echo $_GET['email'] ?? ''; ?>" required readonly>
    <input type="text" name="token" placeholder="Entrez le code reçu" required>
    <button type="submit">Vérifier</button>
</form>
