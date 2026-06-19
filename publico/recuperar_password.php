<?php
session_start();

require_once("../bd/bd.php");
require_once("../utils/mailer.php");

$bbdd = new db("localhost", 3306, "plataforma_servicios1", "root", "");

$email = "";
$emailerror = "";
$mensaje = "";

if(isset($_POST["enviar"])){

    $email = trim($_POST["email"] ?? "");

    if($email == ""){
        $emailerror = "Introduce tu correo electrónico";

    }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $emailerror = "Introduce un correo válido";

    }else{

        $usuario = $bbdd->ObtenerUsuarioPorEmail($email);

        if($usuario){
            $token = $bbdd->CrearTokenResetPassword($usuario["id_usuario"]);

            enviarCorreoResetPassword(
                $usuario["email"],
                $usuario["nombre"],
                $token
            );
        }

        $mensaje = "Si el correo existe en nuestra plataforma, recibirás un enlace para restablecer tu contraseña.";
    }
}

$titulo = "<h1>Recuperar contraseña</h1>";
require_once("head.php");
?>

<body>

<main class="login-page">
    <section class="login-section">
        <div class="container">
            <div class="login-wrapper">

                <div class="login-card">
                    <div class="heart-glow"></div>

                    <div class="login-card-header">
                        <span class="section-tag">Recupera tu acceso</span>
                        <h2>Restablecer contraseña</h2>
                        <p>
                            Introduce tu correo y te enviaremos un enlace para crear una nueva contraseña.
                        </p>
                    </div>

                    <?php if($mensaje != ""){ ?>
                        <div class="booking-alert booking-alert-ok">
                            <?=$mensaje?>
                        </div>
                    <?php } ?>

                    <form action="" method="post" class="login-form">

                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?=htmlspecialchars($email)?>"
                                placeholder="Introduce tu correo"
                            >
                            <small><?=$emailerror?></small>
                        </div>

                        <button type="submit" name="enviar" value="enviar" class="btn btn-primary btn-full login-btn">
                            Enviar enlace
                        </button>
                    </form>

                    <p class="login-register-text">
                        ¿Ya tienes cuenta?
                        <a href="login.php" class="register-link">Volver al login</a>
                    </p>
                </div>

            </div>
        </div>
    </section>
</main>

<?php require_once("footer.php"); ?>
</body>
</html> 