<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Captura la respuesta del reCAPTCHA
$to_email = "henrysaavedra@gmail.com";
$subject = $_POST['subject'];
$mensaje = $_POST['mensaje'];
$remitente = $_POST['remitente'];
$replyTo = $_POST['remitente'];
$headers = "From:".$remitente."\r\n";
$headers .= "Reply-To:".$remitente."\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
if (mail($to_email, $subject, $mensaje, $headers)) {
    echo "Correo enviado correctamente a $to_email";
} else {
    echo "Error al enviar el correo.";
}
$response = array('success' => true, 'message' => '¡Correo enviado correctamente!');
echo json_encode($response);
?>
