<?php
require 'vendor/autoload.php';
//importar php mailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//instancia la clase php y con true permite gestionar las excepciones con el try catch
$mail = new PHPMailer(true);

// contact.php - Script para enviar emails desde el formulario
header('Content-Type: text/html; charset=UTF-8');

// Configuración de errores (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Variables para mensajes
$mensaje_exito = "";
$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Debug: ver qué datos llegan
    // Descomenta estas líneas para ver qué se está enviando
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Validar que todos los campos requeridos estén presentes
    /*if (!isset($_POST['terms']) || $_POST['terms'] != 'on') {
        $mensaje_error = "Ha ocurrido un error... Recuerde que debe aceptar los términos y condiciones.";
        echo 'Error por los terminos' . $mensaje_error;
    } else {*/
        
        // Obtener y limpiar datos del formulario
        $nombre = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $asunto = isset($_POST['email_subject']) ? trim($_POST['email_subject']) : '';
        $mensaje = isset($_POST['message']) ? trim($_POST['message']) : '';
        
        // Validaciones básicas
        if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
            $mensaje_error = "Por favor, completa todos los campos.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje_error = "El formato del email no es válido.";
        } else {
            echo "instanciando el mail";
            $mail = new PHPMailer(true);
            try {
                // Configuración SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'andres.arancibia.lados@gmail.com';
                $mail->Password = 'asvi udqb idzd lwsc';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // IMPORTANTE: setFrom debe usar el mismo email que Username
                $mail->setFrom('andresweb@fedora.com', 'Blog de Noticias'); //andres.arancibia.lados@gmail.com
                $mail->addReplyTo($email, $nombre);
                $mail->addAddress('arancibiaandres625@gmail.com', 'Andres Arancibia');

                // Contenido del email
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = "Contacto desde el blog: " . $asunto;
                $mail->Body = "
                    <html>
                    <head><meta charset='UTF-8'><title>Nuevo mensaje de contacto</title></head>
                    <body>
                        <h2>Nuevo mensaje desde el blog</h2>
                        <p><strong>Nombre:</strong> " . htmlspecialchars($nombre) . "</p>
                        <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                        <p><strong>Asunto:</strong> " . htmlspecialchars($asunto) . "</p>
                        <p><strong>Mensaje:</strong></p>
                        <p>" . nl2br(htmlspecialchars($mensaje)) . "</p>
                        <hr>
                        <p><small>IP del remitente: " . $_SERVER['REMOTE_ADDR'] . "</small></p>
                        <p><small>Fecha: " . date('Y-m-d H:i:s') . "</small></p>
                        <p><small>Este mensaje fue enviado desde el formulario de contacto del blog.</small></p>
                    </body>
                    </html>
                ";

                $mail->send();
                $mensaje_exito = "¡Gracias por contactarnos! Tu mensaje ha sido enviado correctamente.";
                // Limpiar variables después del éxito
                $nombre = $email = $asunto = $mensaje = "";
                
            } catch (Exception $e) {
                $mensaje_error = "Lo sentimos, hubo un error al enviar tu mensaje: " . $mail->ErrorInfo;
                echo $mensaje_error;
            }
        }
    }
//}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Mi Blog De Noticias - Contacto</title>
    <link rel="stylesheet" href="blog.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .mensaje {
            margin: 20px auto;
            padding: 15px;
            border-radius: 5px;
            max-width: 600px;
            text-align: center;
        }
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="">Deporte</a></li>
                <li><a href="">El clima</a></li>
                <li><a href="">Actualidad</a></li>
                <li><a href="">Tecnologia</a></li>
                <li class="contact"><a class="active" href="">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <?php if (!empty($mensaje_exito)): ?>
            <div class="mensaje mensaje-exito">
                <h2>Mensaje enviado con exito</h2>
                <p><?php echo $mensaje_exito; ?></p>
                <a href="index.html" class="btn-volver">Volver al inicio</a>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensaje_error)): ?>
            <div class="mensaje mensaje-error">
                <?php echo $mensaje_error; ?>
            </div>
        <?php endif; ?>
    </section>

    <footer>
        <div class="final-foot">
            <p>Derechos del autor Andres Arancibia © 2025. Todos los derechos reservados.</p>
            <div class="dropdown">
                <img src="escudoUNSa.jpg" width="70"/>
            </div>
        </div>
    </footer>
</body>
</html>