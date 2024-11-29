<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './PHPMailer/PHPMailer.php';
require './PHPMailer/Exception.php';
require './PHPMailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = $_POST['from_name'];
    $correo = $_POST['from_email'];
    $telefono = $_POST['phone'];
    $mensaje = $_POST['message'];

    // Crear instancia de PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // El servidor SMTP de Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'tanianuno0499@alumnos.udg.mx';  // Tu correo de Gmail
        $mail->Password = 'tu_contraseña';  // Tu Contraseña de aplicación o contraseña de Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;  // Puerto recomendado para TLS

        // Remitente y destinatario
        $mail->setFrom($correo, $nombre);  // Usar el correo y nombre del formulario
        $mail->addAddress('tanianuno03@gmail.com', 'Nombre del destinatario');  // Cambia esto al correo destino
        $mail->Subject = 'Mensaje de contacto desde el formulario';
        
        // Cuerpo del mensaje
        $mail->Body    = "Nombre: $nombre\nCorreo: $correo\nTeléfono: $telefono\n\nMensaje:\n$mensaje";

        // Enviar el correo
        $mail->send();
        echo 'Correo enviado exitosamente';
    } catch (Exception $e) {
        echo "Hubo un error al enviar el correo: {$mail->ErrorInfo}";
    }
}
?>
