<?php
// send_email.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php'; // Ajusta la ruta según la estructura de tu proyecto

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function enviarCorreo($destinatario, $asunto, $mensaje) {
    $mail = new PHPMailer(true);

    // Ruta del archivo de registro de errores
    $logFile = __DIR__ . '/error_log';

    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['GMAIL_USERNAME'];
        $mail->Password = $_ENV['GMAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Habilitar depuración
        $mail->SMTPDebug = 2; // Cambiar a 0 para deshabilitar la depuración
        $mail->Debugoutput = function($str, $level) use ($logFile) {
            // Guardar mensajes de depuración en el archivo de registro
            error_log(date('Y-m-d H:i:s') . " - DEBUG: $str\n", 3, $logFile);
        };

        // Destinatarios
        $mail->setFrom($_ENV['GMAIL_USERNAME'], 'Tu Nombre');
        $mail->addAddress($destinatario);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;

        // Enviar el correo
        if ($mail->send()) {
            echo 'El mensaje ha sido enviado';
        } else {
            echo 'El mensaje no pudo ser enviado. Mailer Error: ' . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        echo "El mensaje no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}";
        error_log("Mailer Error: {$mail->ErrorInfo}", 3, $logFile);
    }
}
?>