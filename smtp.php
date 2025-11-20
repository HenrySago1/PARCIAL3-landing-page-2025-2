<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Configuración SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'esdecunumi@gmail.com';
    $mail->Password   = 'bdem uxvq rctt oyrb'; // No la contraseña normal
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Remitente y destinatario
    $mail->setFrom('esdecunumi@gmail.com', 'ESDE Cunumi');
    $mail->addAddress('henryeddysagy@gmail.com', 'Henry');

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Correo de prueba con PHPMailer';
    $mail->Body    = 'Este es un correo de prueba enviado con PHPMailer.';

    $mail->send();
    echo 'Correo enviado correctamente';
} catch (Exception $e) {
    echo "Error al enviar el correo: {$mail->ErrorInfo}";
}
?>
