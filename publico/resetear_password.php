<?php
session_start();

require_once("../bd/bd.php");
require_once("../bd/bdact.php");
$bbdd = new db("localhost", 3306, "plataforma_servicios1", "root", "");

$token = $_GET["token"] ?? "";

if($token == ""){
    echo "Enlace no válido";
    exit();
}

$reset = $bbdd->ObtenerResetPorToken($token);

if(!$reset){
    echo "El enlace no es válido o ya ha sido utilizado";
    exit();
}

$error = "";
$mensaje = "";

if(isset($_POST["cambiar"])){

    $password = $_POST["password"] ?? "";
    $password2 = $_POST["password2"] ?? "";

    if($password == "" || $password2 == ""){
        $error = "Debes rellenar los dos campos";

    }else if($password != $password2){
        $error = "Las contraseñas no coinciden";

    }else if(strlen($password) < 6){
        $error = "La contraseña debe tener al menos 6 caracteres";

    }else{

        $cambiada = $bbdd->CambiarPasswordConToken(
            $reset["id_usuario"],
            $token,
            $password
        );

        if($cambiada){
            header("Location: login.php?password=actualizada");
            exit();
        }else{
            $error = "No se pudo actualizar la contraseña";
        }
    }
}

$titulo = "<h1>Restablecer contraseña</h1>";
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
                        <span class="section-tag">Seguridad de la cuenta</span>
                        <h2>Crear nueva contraseña</h2>
                        <p>
                            Hola <?=htmlspecialchars($reset["nombre"])?>, introduce una nueva contraseña para recuperar el acceso.
                        </p>
                    </div>

                    <?php if($error != ""){ ?>
                        <div class="booking-alert booking-alert-error">
                            <?=$error?>
                        </div>
                    <?php } ?>

                    <form action="" method="post" class="login-form">

                        <div class="form-group">
                            <label for="password">Nueva contraseña</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Introduce la nueva contraseña"
                            >
                        </div>

                        <div class="form-group">
                            <label for="password2">Repetir contraseña</label>
                            <input
                                type="password"
                                id="password2"
                                name="password2"
                                placeholder="Repite la nueva contraseña"
                            >
                        </div>

                        <button type="submit" name="cambiar" value="cambiar" class="btn btn-primary btn-full login-btn">
                            Cambiar contraseña
                        </button>
                    </form>

                    <p class="login-register-text">
                        ¿Ya recuerdas tu contraseña?
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