<?php
require 'send_email.php';

$destinatario = 'martindiazpose@gmail.com';
$asunto = 'Prueba de correo';
$mensaje = 'Este es un correo de prueba.';

if (enviarCorreo($destinatario, $asunto, $mensaje)) {
    echo 'Correo enviado correctamente.';
} else {
    echo 'Error al enviar el correo. Revisa el archivo de registro.';
}
?>