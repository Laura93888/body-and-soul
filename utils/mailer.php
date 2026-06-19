<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . "/../libs/PHPMailer/Exception.php");
require_once(__DIR__ . "/../libs/PHPMailer/PHPMailer.php");
require_once(__DIR__ . "/../libs/PHPMailer/SMTP.php");

define("MAIL_HOST", "smtp-relay.brevo.com");
define("MAIL_USER", "TU_USUARIO_SMTP");
define("MAIL_PASS", "TU_PASSWORD_SMTP");
define("MAIL_FROM", "correo@ejemplo.com");
define("MAIL_FROM_NAME", "Body & Soul");

function enviarCorreoReserva($destinatario, $nombreUsuario, $datosReserva){

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

    try{

        // CONFIG SMTP
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASS;

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        // REMITENTE
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);

        // DESTINATARIO
        $mail->addAddress($destinatario, $nombreUsuario);

        // EMAIL HTML
        $mail->isHTML(true);

        $mail->Subject = 'Reserva confirmada - Body & Soul';

        $mail->Body = "

        <div style='font-family: Arial; max-width:600px; margin:auto; padding:20px;'>

            <h1 style='color:#1B4965;'>Reserva confirmada</h1>

            <p>Hola <strong>{$nombreUsuario}</strong>,</p>

            <p>Tu reserva se ha realizado correctamente.</p>

            <div style='background:#f5f5f5; padding:20px; border-radius:10px;'>

                <h2>{$datosReserva["actividad"]}</h2>

                <p><strong>Fecha:</strong> {$datosReserva["fecha"]}</p>

                <p><strong>Hora:</strong> {$datosReserva["hora"]}</p>

                <p><strong>Duración:</strong> {$datosReserva["duracion"]}</p>

                <p><strong>Ubicación:</strong> {$datosReserva["ubicacion"]}</p>

                <p><strong>Empresa:</strong> {$datosReserva["empresa"]}</p>

                <p><strong>Teléfono:</strong> {$datosReserva["telefono"]}</p>

            </div>

            <p style='margin-top:20px;'>
                Gracias por confiar en Body & Soul 💚
            </p>

        </div>

        ";

        $mail->send();

        return true;

    }catch(Exception $e){

        return false;

    }

}

function enviarCorreoCancelacion($destinatario, $nombreUsuario, $datosReserva){

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

    try{

        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($destinatario, $nombreUsuario);

        $mail->isHTML(true);
        $mail->Subject = 'Reserva cancelada - Body & Soul';

        $mail->Body = "
        <div style='font-family: Arial; max-width:600px; margin:auto; padding:20px;'>
            <h1 style='color:#cc4c66;'>Reserva cancelada</h1>

            <p>Hola <strong>{$nombreUsuario}</strong>,</p>

            <p>Te informamos de que tu reserva ha sido cancelada.</p>

            <div style='background:#f5f5f5; padding:20px; border-radius:10px;'>
                <h2>{$datosReserva["actividad"]}</h2>

                <p><strong>Fecha:</strong> {$datosReserva["fecha"]}</p>
                <p><strong>Hora:</strong> {$datosReserva["hora"]}</p>
                <p><strong>Duración:</strong> {$datosReserva["duracion"]}</p>
                <p><strong>Ubicación:</strong> {$datosReserva["ubicacion"]}</p>
                <p><strong>Empresa:</strong> {$datosReserva["empresa"]}</p>
                <p><strong>Teléfono:</strong> {$datosReserva["telefono"]}</p>
            </div>

            <p style='margin-top:20px;'>
                Puedes consultar otras actividades disponibles en Body & Soul.
            </p>
        </div>
        ";

        $mail->send();
        return true;

    }catch(Exception $e){
      return false;
    }
}

function enviarCorreoBienvenida($destinatario, $nombreUsuario){

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

    try{

        $nombreSeguro = htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8');

        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($destinatario, $nombreUsuario);

        $mail->isHTML(true);
        $mail->Subject = '¡Bienvenido a Body & Soul!';

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width:600px; margin:auto; padding:24px; background:#f6fbfa;'>
            <div style='background:white; border-radius:16px; padding:28px; border:1px solid #d9eeee;'>

                <h1 style='color:#1B4965; margin-top:0;'>¡Bienvenido a Body & Soul!</h1>

                <p>Hola <strong>{$nombreSeguro}</strong>,</p>

                <p>Gracias por registrarte en nuestra plataforma.</p>

                <p>
                    A partir de ahora podrás descubrir actividades de deporte y bienestar,
                    realizar reservas y gestionar tus próximas experiencias desde tu perfil.
                </p>

                <div style='background:#e8f7f5; padding:16px; border-radius:12px; margin:22px 0;'>
                    <strong style='color:#1B4965;'>¿Qué puedes hacer ahora?</strong>
                    <p style='margin-bottom:0;'>
                        Buscar actividades, consultar horarios disponibles, reservar plazas
                        y contactar con las empresas organizadoras.
                    </p>
                </div>

                <p>Esperamos que disfrutes de tu experiencia.</p>

                <p style='margin-top:28px; color:#1B4965;'>
                    El equipo de <strong>Body & Soul</strong>
                </p>
            </div>
        </div>
        ";

        $mail->AltBody = "Hola {$nombreUsuario}, gracias por registrarte en Body & Soul.";

        return $mail->send();

    }catch(Exception $e){
        return false;
    }
}

function enviarCorreoResetPassword($destinatario, $nombreUsuario, $token){

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

    try{

        $nombreSeguro = htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8');

        $enlace = "http://localhost/TFG/Trabajo_Fin_De_Grado/publico/resetear_password.php?token=".$token;

        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($destinatario, $nombreUsuario);

        $mail->isHTML(true);
        $mail->Subject = 'Restablece tu contraseña - Body & Soul';

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width:600px; margin:auto; padding:24px;'>
            <h1 style='color:#1B4965;'>Restablecer contraseña</h1>

            <p>Hola <strong>{$nombreSeguro}</strong>,</p>

            <p>
                Hemos detectado varios intentos fallidos de inicio de sesión en tu cuenta.
                Por seguridad, puedes restablecer tu contraseña desde el siguiente enlace:
            </p>

            <p style='margin:28px 0;'>
                <a href='{$enlace}' style='background:#2EC4B6; color:white; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:bold;'>
                    Cambiar contraseña
                </a>
            </p>

            <p>Si no has sido tú, te recomendamos cambiar la contraseña igualmente.</p>
        </div>
        ";

        $mail->AltBody = "Para restablecer tu contraseña entra en: ".$enlace;

        return $mail->send();

    }catch(Exception $e){
        return false;
    }
}

