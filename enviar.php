<?php
// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$remitente = trim($_POST['remitente'] ?? '');
$asunto = trim($_POST['asunto'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if (!$nombre || !$remitente || !$asunto || !$descripcion) {
    header('Location: contact.html?mensaje=error_campos');
    exit;
}

// Verificar reCAPTCHA si está configurado
$recaptcha_secret = getenv('RECAPTCHA_SECRET') ?: '';
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
if ($recaptcha_secret) {
    if (!$recaptcha_response) {
        header('Location: contact.html?mensaje=recaptcha');
        exit;
    }
    $verify = @file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($recaptcha_secret) . '&response=' . urlencode($recaptcha_response));
    $res = $verify ? json_decode($verify, true) : null;
    if (empty($res['success'])) {
        header('Location: contact.html?mensaje=recaptcha');
        exit;
    }
}

// Cargar autoloader de Composer (buscar en ./vendor o ./libs/vendor)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/libs/vendor/autoload.php')) {
    require __DIR__ . '/libs/vendor/autoload.php';
} else {
    error_log('Composer autoload no encontrado');
    echo 'Error interno: dependencias no instaladas.';
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    // Configuración desde variables de entorno (para usar en Render)
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USER') ?: '';
    $mail->Password   = getenv('SMTP_PASS') ?: '';

    $smtp_port = getenv('SMTP_PORT');
    $smtp_secure = getenv('SMTP_SECURE') ?: 'ssl';

    if ($smtp_port) {
        $mail->Port = (int)$smtp_port;
    }

    if (strtolower($smtp_secure) === 'tls' || strtolower($smtp_secure) === 'starttls') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        if (empty($mail->Port)) $mail->Port = 587;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        if (empty($mail->Port)) $mail->Port = 465;
    }

    $mail_from = getenv('MAIL_FROM') ?: $mail->Username;
    $mail_from_name = getenv('MAIL_FROM_NAME') ?: 'SAGOSOFT';

    if (!$mail->Username || !$mail->Password) {
        error_log('Credenciales SMTP no configuradas en variables de entorno');
        echo 'Error interno: correo no configurado.';
        exit;
    }

    // Remitente: usar la cuenta del sitio; establecer Reply-To al email del usuario
    $mail->setFrom($mail_from, $mail_from_name);
    $mail->addAddress($mail_from);
    $mail->addReplyTo($remitente, $nombre);

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = $asunto;
    $body  = '<p><strong>Nombre:</strong> ' . htmlspecialchars($nombre) . '</p>';
    $body .= '<p><strong>Correo:</strong> ' . htmlspecialchars($remitente) . '</p>';
    $body .= '<p><strong>Mensaje:</strong><br>' . nl2br(htmlspecialchars($descripcion)) . '</p>';
    $mail->Body = $body;

    $mail->send();
    header('Location: contact.html?mensaje=ok');
    exit;
} catch (Exception $e) {
    error_log('Error enviando correo: ' . $mail->ErrorInfo);
    header('Location: contact.html?mensaje=error_envio');
    exit;
}