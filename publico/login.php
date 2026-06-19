<?php
session_start();

require_once("../bd/bd.php");
require_once("../utils/mailer.php");
$bbdd= new db("localhost",3306,"plataforma_servicios1","root","");

require_once("../bd/bdact.php");
$bdact= new bdact("localhost",3306,"plataforma_servicios1","root","");

if(isset($_SESSION["usuario"])){
  header("Location: perfil.php");
  exit();
}//asi si ya tengo sesion iniciada me lleva al index

$banderaerror = false;

$email = "";
$emailerror = "";
if(isset($_POST["email"])){
    $email = htmlentities($_POST["email"]);
    if($email == ""){
        $emailerror = "El campo email no puede estar vacío";
        $banderaerror = true;
    }
}

$contraseña = "";
$contraerror = "";
if(isset($_POST["contraseña"])){
    $contraseña = htmlentities($_POST["contraseña"]);
    if($contraseña == ""){
        $contraerror = "El campo contraseña no puede estar vacío";
        $banderaerror = true;
    }
}

$usuarioerror="";
if($banderaerror == false && isset($_POST["enviar"])){

    $respuesta = $bbdd->ComprobarLogin($email, $contraseña);

    if($respuesta == "usuarionoexiste"){
        $emailerror = "Este email no existe, prueba de nuevo";
        $banderaerror = true;

    }else if($respuesta == "usuariobloqueado"){
      $usuarioBloqueado = $bbdd->ObtenerUsuarioPorEmail($email);
      if($usuarioBloqueado){
          $token = $bbdd->CrearTokenResetPassword($usuarioBloqueado["id_usuario"]);

          enviarCorreoResetPassword(
              $usuarioBloqueado["email"],
              $usuarioBloqueado["nombre"],
              $token
          );
      
      $contraerror = "Usuario bloqueado por exceso de intentos. Te hemos enviado un correo para restablecer tu contraseña.";
      $banderaerror = true;
      }
    }else if($respuesta == "fallocontraseña"){
        $contraerror = "Contraseña incorrecta, prueba de nuevo";
        $banderaerror = true;
    }else{
      //Si todo ha ido bien me ha devuelto el id de usuario por lo que inicio sesion
        $_SESSION["usuario"] = $respuesta;
        header("Location: perfil.php?registro=ok");
        exit();
    }
}

if(isset($_GET["password"]) && $_GET["password"] == "actualizada"){
    $usuarioerror = "Contraseña actualizada correctamente. Ya puedes iniciar sesión.";
}

$titulo="<h1>Bienvenido a Body and Soul</h1>";
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
              <span class="section-tag">Accede a tu cuenta</span>
              <h2>Iniciar sesión</h2>
            </div>
            
            <?php if(isset($_GET["registro"]) && $_GET["registro"] == "ok"){ ?>
                <div class="booking-alert booking-alert-ok">
                    Registro completado correctamente. Ya puedes iniciar sesión.
                </div>
            <?php } 
            if($usuarioerror != ""){ ?>
                <div class="booking-alert booking-alert-ok">
                    <?=$usuarioerror?>
                </div>
            <?php } ?>

            <form action="" method="post" class="login-form">
              <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  value="<?=$email?>"
                  placeholder="Introduce tu correo"
                >
                <small><?php echo $emailerror; ?></small>
              </div>

              <div class="form-group">
                <label for="contraseña">Contraseña</label>
                <input
                  type="password"
                  id="contraseña"
                  name="contraseña"
                  placeholder="Introduce tu contraseña"
                >
                <small><?php echo $contraerror; ?></small>

                <p class="register-link">
                    <a href="recuperar_password.php">¿Has olvidado tu contraseña?</a>
                </p>
                
              </div>

              <button type="submit" name="enviar" value="enviar" class="btn btn-primary btn-full login-btn">
                Iniciar sesión
              </button>
            </form>

            <p class="login-register-text">
              ¿No tienes cuenta?
              <a href="registro.php" class="register-link">Regístrate</a>
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