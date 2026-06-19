<?php

$titulo="<h1>Mi perfil</h1>";
require_once("head.php");

if(!isset($_SESSION["usuario"])){
  header("Location: index.php");
  exit();
}

// Obtener datos actuales del usuario
$usuario = $bbdd->ObtenerUsuario($_SESSION["usuario"]);

$banderaerror = false;
$mensajeok = "";

// Valores iniciales del formulario
$nombre = $usuario["nombre"];
$apellido = $usuario["apellido"];
$email = $usuario["email"];

$errornombre = "";
$errorapellido = "";
$errorcontraseña = "";
$errorconfirm = "";

// Procesar formulario
if(isset($_POST["guardar"])){

    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $contraseña = trim($_POST["contraseña"] ?? "");
    $confirm_contraseña = trim($_POST["confirm_contraseña"] ?? "");

    if($nombre == ""){
        $errornombre = "El nombre no puede estar vacío";
        $banderaerror = true;
    }

    if($apellido == ""){
        $errorapellido = "El apellido no puede estar vacío";
        $banderaerror = true;
    }

    // Solo comprobamos contraseñas si el usuario quiere cambiarla
    if($contraseña != "" && $confirm_contraseña != ""){
        if($contraseña == ""){
            $errorcontraseña = "Introduce la nueva contraseña";
            $banderaerror = true;
        }

        if($confirm_contraseña == ""){
            $errorconfirm = "Confirma la nueva contraseña";
            $banderaerror = true;
        }

        if($contraseña != "" && $confirm_contraseña != "" && $contraseña != $confirm_contraseña){
            $errorconfirm = "Las contraseñas no coinciden";
            $banderaerror = true;
        }
    }


    if(!$banderaerror){

        // Si hay contraseña, actualiza con contraseña
        if($contraseña != ""){
            $bbdd->ModificarUsuarioConcontraseña(
                $_SESSION["usuario"],
                $nombre,
                $apellido,
                $email,
                $contraseña
            );

        }else{
            $bbdd->ModificarUsuarioSincontraseña(
                $_SESSION["usuario"],
                $nombre,
                $apellido,
                $email
            );
        }

        $mensajeok = "Datos actualizados correctamente";

        // Recargar usuario actualizado
        $usuario = $bbdd->ObtenerUsuario($_SESSION["usuario"]);
    }
}
?>

?>

  <main class="login-page">
    <section class="login-section">
      <div class="container">
        <div class="login-wrapper">

          <div class="login-card">
            <div class="login-card-header">
              <span class="section-tag">Área personal</span>
              <h2>Modificar datos</h2>
            </div>

            <?php if($mensajeok != ""){ ?>
              <div id="mensaje-ok"><?php echo $mensajeok; ?></div>
            <?php } ?>

            <form action="" method="post" class="login-form" enctype="multipart/form-data">

            <div class="form-group">
              <label for="nombre">Nombre</label>
              <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
              <small><?php echo $errornombre; ?></small>
            </div>

            <div class="form-group">
              <label for="apellido">Apellido</label>
              <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>">
              <small><?php echo $errorapellido; ?></small>
            </div>

            <div class="form-group full-width">
              <label for="email">Correo</label>
              <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>

            <div class="form-group">
              <label for="password">Nueva contraseña</label>
              <input type="password" id="password" name="password" placeholder="Solo si quieres cambiarla">
              <small><?php echo $errorcontraseña; ?></small>
            </div>

            <div class="form-group">
              <label for="confirm-password">Confirmar contraseña</label>
              <input type="password" id="confirm-password" name="confirm_password" placeholder="Repite la nueva contraseña">
              <small><?php echo $errorconfirm; ?></small>
            </div>

                <button type="submit" name="guardar" value="guardar" class="btn btn-primary btn-full login-btn full-width">
                Guardar cambios
                </button>
                
            <p class="login-register-text">
                ¿Quieres volver a tu área personal?
                <a href="perfil.php" class="register-link">Ir a mi perfil</a>
            </p>
          </div>

        </div>
      </div>
    </section>
  </main>


 <?php
require_once("footer.php");
?>
</body>
</html>