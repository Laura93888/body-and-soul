<?php
session_start();

if(isset($_SESSION["empresa"])){
  header("Location: index.php");
}

require_once("../bd/bdempresa.php");
$bdempre= new bdempresa("localhost",3306,"plataforma_servicios1","root","");

$banderaerror = false;

$email = "";
$emailerror = "";
if(isset($_POST["email"])){
    $email = trim($_POST["email"]);
    if($email == ""){
        $emailerror = "El campo email no puede estar vacío";
        $banderaerror = true;
    }
}

$contraseña = "";
$contraerror = "";
if(isset($_POST["contraseña"])){
    $contraseña = $_POST["contraseña"];
    if($contraseña == ""){
        $contraerror = "El campo contraseña no puede estar vacío";
        $banderaerror = true;
    }
}

$usuarioerror="";
if($banderaerror == false && isset($_POST["enviar"])){

    $respuesta = $bdempre->ComprobarLoginEmpresa($email, $contraseña);

    if($respuesta == "empresanoexiste"){
        $emailerror = "Este email no existe, prueba de nuevo";
        $banderaerror = true;

    }else if($respuesta == "fallocontraseña"){
        $contraerror = "Contraseña incorrecta, prueba de nuevo";
        $banderaerror = true;

    }else{
      //Si todo ha ido bien me ha devuelto el id de la empresa por lo que inicio sesion
  
        $_SESSION["empresa"] = $respuesta;
        header("Location: index.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Body and Soul | Acceso empresa</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Overpass:wght@300;400;500;600;700&family=Sansita:wght@700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/empresa-styles/empresa.css">
</head>
<body class="company-auth-body">

  <main class="company-auth-page">
    <section class="company-auth-section">
      <div class="company-auth-wrapper">

        <div class="company-auth-card">
          <div class="company-auth-glow"></div>

          <div class="company-auth-header">
            <img src="../img/logo.PNG" alt="Logo Body and Soul" class="company-auth-logo">
            <span class="company-auth-badge">Acceso empresa</span>
            <h1>Iniciar sesión</h1>
            <p>
              Accede a tu panel para gestionar servicios, reservas y la información de tu empresa.
            </p>
          </div>

          <form action="#" method="post" class="company-auth-form">

            <div class="form-group">
              <label for="email">Correo electrónico</label>
              <input
                type="email"
                id="email"
                name="email"
                placeholder="Introduce tu correo"
                value="<?=$email?>"
              >
            </div>
            <small><?php echo $emailerror; ?></small>

            <div class="form-group">
              <label for="contraseña">Contraseña</label>
              <input
                type="password"
                id="contraseña"
                name="contraseña"
                placeholder="Introduce tu contraseña"
              >
            </div>
            <small><?php echo $contraerror; ?></small>

            <button type="submit" name="enviar" value="enviar" class="company-auth-btn">
              Entrar al panel
            </button>
          </form>

          <p class="company-auth-alt">
            ¿Todavía no tienes cuenta?
            <a href="registro-empresa.php" class="company-auth-link">Registra tu empresa</a>
          </p>

          <p class="company-auth-help">
            Acceso exclusivo para empresas registradas en la plataforma.
          </p>
        </div>

      </div>
    </section>
  </main>

</body>
</html>