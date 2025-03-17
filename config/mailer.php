<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendVerificationEmail($email, $code) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Utilise le serveur SMTP de ton fournisseur
        $mail->SMTPAuth = true;
        $mail->Username = 'ton_email@gmail.com'; // Remplace par ton email
        $mail->Password = 'ton_mot_de_passe'; // Remplace par ton mot de passe
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('ton_email@gmail.com', 'Mon Site');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Vérification de votre compte";
        $mail->Body = "Votre code de vérification est : <strong>$code</strong>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
