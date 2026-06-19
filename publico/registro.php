<?php

session_start();
require_once("../utils/mailer.php");
require_once("../bd/bd.php");

$bbdd = new db("localhost",3306,"plataforma_servicios1","root","");

$titulo="<h1>Bienvenido a Body and Soul</h1>";

if(isset($_SESSION["usuario"])){
  header("Location: index.php");
  exit();
}//asi si ya tengo sesion iniciada me lleva al index


$banderaerror=false;

$email="";
$erroremail="";
if(isset($_POST["email"])){
    $email=htmlentities($_POST["email"]);
    if($email==""){
        $erroremail="El campo no puede estar vacio";
        $banderaerror=True;
    }else if($bbdd->emailexiste($email)){ //Compruebo que el usuario metido no exista para que no se repita el nombre
        $erroremail="Este email ya está registrado";
        $banderaerror=True;
    }
}

$nombre="";
$errornombre="";
if(isset($_POST["nombre"])){
    $nombre=htmlentities($_POST["nombre"]);
    if($nombre==""){
        $errornombre="El campo nombre no puede estar vacio";
        $banderaerror=True;
    }
}

$apellido="";
$errorapellido="";
if(isset($_POST["apellido"])){
    $apellido=htmlentities($_POST["apellido"]);
    if($apellido==""){
        $errorapellido="El campo apellido no puede estar vacio";
        $banderaerror=True;
    }
}

$telefono="";
$errortelefono="";
if(isset($_POST["telefono"])){
    $telefono=htmlentities($_POST["telefono"]);
    if($telefono==""){
        $errortelefono="El campo telefono no puede estar vacio";
        $banderaerror=True;
    }
}

$contraseña="";
$contraerror="";
if(isset($_POST["contraseña"])){
    $contraseña=htmlentities($_POST["contraseña"]);
    if($contraseña==""){
        $contraerror="El campo no puede estar vacio";
        $banderaerror=True;
    }
}

$confirm_password="";
$errorconfirm="";

if(isset($_POST["confirm_password"])){
    $confirm_password=htmlentities($_POST["confirm_password"]);

    if($confirm_password==""){
        $errorconfirm="Debes repetir la contraseña";
        $banderaerror=True;
    }
}

if($banderaerror == false && isset($_POST["enviar"])){
    $registroOk = $bbdd->RegistrarUsuario($email, $contraseña, $nombre, $apellido, $telefono);
    if($registroOk){
      enviarCorreoBienvenida($email, $nombre);
      header("Location:login.php?registro=ok");
      exit();
    }else{
        $erroremail = "No se pudo completar el registro";
    }
}

require_once("head.php"); 

?>

  <main class="login-page">
    <section class="login-section">
      <div class="container">
        <div class="login-wrapper">

          <div class="login-card">
            <div class="login-card-header">
              <span class="section-tag">Crea tu cuenta</span>
              <h2>Regístrate</h2>
            </div>
            
            <form action="" method="post" class="login-form">
              <div class="form-group">
                <label for="nombre">Nombre</label>
                <input
                  type="text"
                  id="nombre"
                  name="nombre"
                  placeholder="Introduce tu nombre"
                  value=<?=$nombre?>
                >
                <small><?php echo $errornombre; ?></small>
              </div>

              <div class="form-group">
                <label for="apellido">Apellido</label>
                <input
                  type="text"
                  id="apellido"
                  name="apellido"
                  placeholder="Introduce tu apellido"
                  value=<?=$apellido?>
                >
                <small><?php echo $errorapellido; ?></small>
              </div>

              <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input
                  type="text"
                  id="email"
                  name="email"
                  placeholder="Introduce tu correo"
                  value=<?=$email?>
                >
                <small><?php echo $erroremail; ?></small>
              </div>

              <div class="form-group">
                <label for="telefono">Telefono</label>
                <input
                  type="tel"
                  id="telefono"
                  name="telefono"
                  placeholder="Introduce tu telefono"
                  value=<?=$telefono?>
                >
                <small><?php echo $errortelefono; ?></small>
              </div>

              <div class="form-group">
                <label for="contraseña">Contraseña</label>
                <input
                  type="password"
                  id="contraseña"
                  name="contraseña"
                  placeholder="Crea una contraseña"        
                >
                <small><?php echo $contraerror; ?></small>
              </div>

              <div class="form-group">
                <label for="confirm-password">Confirmar contraseña</label>
                <input
                  type="password"
                  id="confirm-password"
                  name="confirm_password"
                  placeholder="Repite la contraseña"
                >
                <small><?php echo $errorconfirm; ?></small>
              </div>

              <button type="submit" name="enviar" value="enviar" class="btn btn-primary btn-full login-btn">
                Registrarme
              </button>
            </form>

            <p class="login-register-text">
              ¿Ya tienes cuenta?
              <a href="login.php" class="register-link">Inicia sesión</a>
            </p>
          </div>

        </div>
      </div>
    </section>
  </main>

 <?php
    //HE QUITADO EL REQUIRED PARA LAS VALIDACIONES MANUALES
require_once("footer.php");
?>

<script src="../js/validacionesRegistroUsuario.js"></script>

</body>
</html>