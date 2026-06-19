<?php
session_start();
require_once("../bd/bdempresa.php");
$bdempre= new bdempresa("localhost",3306,"plataforma_servicios1","root","");

$registro_ok=false;

$banderaerror = false;

$nombre_empresa = "";
$nombreempresaerror = "";
if(isset($_POST["nombre_empresa"])){
    $nombre_empresa = htmlentities(trim($_POST["nombre_empresa"]));
    if($nombre_empresa == ""){
        $nombreempresaerror = "El nombre de la empresa no puede estar vacío";
        $banderaerror = true;
    }
}

$categoria_empresa = "";
$categoriaempresaerror = "";
if(isset($_POST["categoria_empresa"])){
    $categoria_empresa = htmlentities(trim($_POST["categoria_empresa"]));
    if($categoria_empresa == ""){
        $categoriaempresaerror = "Debes seleccionar una categoría";
        $banderaerror = true;
    }
}

$email_empresa = "";
$emailempresaerror = "";
if(isset($_POST["email_empresa"])){
    $email_empresa = htmlentities(trim($_POST["email_empresa"]));
    if($email_empresa == ""){
        $emailempresaerror = "El campo email no puede estar vacío";
        $banderaerror = true;
    }else if(!filter_var($_POST["email_empresa"], FILTER_VALIDATE_EMAIL)){
        $emailempresaerror = "Introduce un email válido";
        $banderaerror = true;
    }
}

$telefono_empresa = "";
$telefonoempresaerror = "";
if(isset($_POST["telefono_empresa"])){
    $telefono_empresa = htmlentities(trim($_POST["telefono_empresa"]));
    if($telefono_empresa == ""){
        $telefonoempresaerror = "El teléfono no puede estar vacío";
        $banderaerror = true;
    }
}


$ciudad_empresa = "";
$ciudadempresaerror = "";
if(isset($_POST["ciudad_empresa"])){
    $ciudad_empresa = htmlentities(trim($_POST["ciudad_empresa"]));
    if($ciudad_empresa == ""){
        $ciudadempresaerror = "La ciudad no puede estar vacía";
        $banderaerror = true;
    }
}


$direccion_empresa = "";
$direccionempresaerror = "";
if(isset($_POST["direccion_empresa"])){
    $direccion_empresa = htmlentities(trim($_POST["direccion_empresa"]));
    if($direccion_empresa == ""){
        $direccionempresaerror = "La dirección no puede estar vacía";
        $banderaerror = true;
    }
}

$password_empresa = "";
$passwordempresaerror = "";
if(isset($_POST["password_empresa"])){
    $password_empresa = htmlentities($_POST["password_empresa"]);
    if($password_empresa == ""){
        $passwordempresaerror = "La contraseña no puede estar vacía";
        $banderaerror = true;
    }
}

$confirm_password_empresa = "";
$confirmpasswordempresaerror = "";
if(isset($_POST["confirm_password_empresa"])){
    $confirm_password_empresa = htmlentities($_POST["confirm_password_empresa"]);
    if($confirm_password_empresa == ""){
        $confirmpasswordempresaerror = "Debes confirmar la contraseña";
        $banderaerror = true;
    }else if($password_empresa != "" && $confirm_password_empresa != $password_empresa){
        $confirmpasswordempresaerror = "Las contraseñas no coinciden";
        $banderaerror = true;
    }
}


$descripcion_empresa = "";
$descripcionempresaerror = "";
if(isset($_POST["descripcion_empresa"])){
    $descripcion_empresa = htmlentities(trim($_POST["descripcion_empresa"]));
    if($descripcion_empresa == ""){
        $descripcionempresaerror = "La descripción no puede estar vacía";
        $banderaerror = true;
    }
}

$logo_empresa = "";
$logoempresaerror = "";
if(isset($_POST["enviar"])){
    if(!isset($_FILES["logo_empresa"]) || $_FILES["logo_empresa"]["error"] == 4){
        $logoempresaerror = "Debes subir el logo de la empresa";
        $banderaerror = true;
    }else{
        $tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];
        $tipoArchivo = $_FILES["logo_empresa"]["type"];

        if(!in_array($tipoArchivo, $tiposPermitidos)){
            $logoempresaerror = "El logo debe ser una imagen JPG, PNG o WEBP";
            $banderaerror = true;
        }
    }
}

if($banderaerror == false && isset($_POST["enviar"])){

    if($bdempre->ExisteEmpresa($email_empresa) == true){
        $emailempresaerror = "Ya existe una empresa registrada con ese email";
        $banderaerror = true;
    }else{

    $fecha = date("Ymd_His");
    $nombreLimpio = str_replace(" ", "-", strtolower($nombre_empresa));
    $nombreArchivo = $fecha . "_" . $nombreLimpio . "_" . basename($_FILES["logo_empresa"]["name"]);
    $ruta = "../img/logos/" . $nombreArchivo;
    move_uploaded_file($_FILES["logo_empresa"]["tmp_name"], $ruta);
    $logo_empresa = $ruta;

    $bdempre->RegistrarSolicitudEmpresa(
    $nombre_empresa,
    $email_empresa,
    $logo_empresa,
    $ciudad_empresa,
    $telefono_empresa,
    $direccion_empresa,
    $password_empresa,
    $categoria_empresa,
    $descripcion_empresa
);

       $registro_ok=true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Body and Soul | Registro empresa</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Overpass:wght@300;400;500;600;700&family=Sansita:wght@700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/empresa-styles/empresa.css">
</head>
<body class="company-auth-body">

  <main class="company-auth-page">
    <section class="company-auth-section">
      <div class="company-auth-wrapper">

        <div class="company-auth-card company-auth-card-register">
          <div class="company-auth-glow"></div>

          <div class="company-auth-header">
            <img src="../img/logo.PNG" alt="Logo Body and Soul" class="company-auth-logo">
            <span class="company-auth-badge">Registro empresa</span>

<?php if($registro_ok){ ?>
  <div class="company-success-box">
    <h2>¡Solicitud enviada con éxito!</h2>
    <p>
      Hemos recibido los datos de tu empresa. Tu cuenta quedará pendiente de revisión
      por parte del administrador.
    </p>
</br>
    <p>
      Te avisaremos cuando el registro haya sido validado y ya puedas iniciar sesión.
    </p>
</br>
    <div class="company-success-actions">
      <a href="../publico/index.php" class="company-auth-link-btn">Volver al inicio</a>
    </div>
  </div>
<?php }else{ ?>

            <h1>Crea tu cuenta</h1>
            <p>
              Registra tu empresa para publicar actividades y gestionar tus servicios en Body and Soul.
            </p>
          </div>

          <form action="#" method="post" class="company-auth-form company-auth-form-register" enctype="multipart/form-data">

            <div class="form-grid">

              <div class="form-group">
                <label for="nombre_empresa">Nombre de la empresa</label>
                <input
                  type="text"
                  id="nombre_empresa"
                  name="nombre_empresa"
                  placeholder="Ej. Zen Balance Studio"
                  value="<?=$nombre_empresa?>"
                >
                <small><?php echo $nombreempresaerror; ?></small>
              </div>

              <div class="form-group">
                <label for="categoria_empresa">Categoría principal</label>
                <select id="categoria_empresa" name="categoria_empresa">
                  <option value="">Selecciona una categoría</option>
                  <option value="bienestar" <?php if($categoria_empresa == "bienestar"){ echo "selected"; } ?>>Bienestar</option>
                  <option value="deporte" <?php if($categoria_empresa == "deporte"){ echo "selected"; } ?>>Deporte</option>
                </select>
                <small><?php echo $categoriaempresaerror; ?></small>
              </div>

              <div class="form-group">
                <label for="email_empresa">Correo electrónico</label>
                <input
                  type="email"
                  id="email_empresa"
                  name="email_empresa"
                  placeholder="correo@empresa.com"
                  value="<?=$email_empresa?>"
                >
                <small><?php echo $emailempresaerror; ?></small>
              </div>

              <div class="form-group">
                <label for="telefono_empresa">Teléfono</label>
                <input
                  type="text"
                  id="telefono_empresa"
                  name="telefono_empresa"
                  placeholder="Ej. 611222333"
                  value="<?=$telefono_empresa?>"
                >
                <small><?php echo $telefonoempresaerror; ?></small>
              </div>

              <div class="form-group">
                <label for="ciudad_empresa">Ciudad</label>
                <input
                  type="text"
                  id="ciudad_empresa"
                  name="ciudad_empresa"
                  placeholder="Ej. Madrid"
                  value="<?=$ciudad_empresa?>"
                >
                <small><?php echo $ciudadempresaerror; ?></small>
              </div>

              <div class="form-group">
                <label for="direccion_empresa">Dirección</label>
                <input
                  type="text"
                  id="direccion_empresa"
                  name="direccion_empresa"
                  placeholder="Ej. Calle Ejemplo, 24"
                  value="<?=$direccion_empresa?>"
                >
                <small><?php echo $direccionempresaerror; ?></small>
              </div>

              <div class="form-group">
                <label for="password_empresa">Contraseña</label>
                <input
                  type="password"
                  id="password_empresa"
                  name="password_empresa"
                  placeholder="Crea una contraseña"
                >
                <small><?php echo $passwordempresaerror; ?></small>
              </div>

              <div class="form-group">
                <label for="confirm_password_empresa">Confirmar contraseña</label>
                <input
                  type="password"
                  id="confirm_password_empresa"
                  name="confirm_password_empresa"
                  placeholder="Repite la contraseña"
                >
                <small><?php echo $confirmpasswordempresaerror; ?></small>
              </div>

              <div class="form-group full-width">
                <label for="descripcion_empresa">Descripción de la empresa</label>
                <textarea
                  id="descripcion_empresa"
                  name="descripcion_empresa"
                  rows="4"
                  placeholder="Describe brevemente tu empresa y los servicios que ofreces..."
                ><?=$descripcion_empresa?></textarea>
                <small><?php echo $descripcionempresaerror; ?></small>
              </div>

              <div class="form-group full-width">
                <label for="logo_empresa">Logo o imagen de empresa</label>
                <input
                  type="file"
                  id="logo_empresa"
                  name="logo_empresa"
                  accept="image/*"
                >
                <small><?php echo $logoempresaerror; ?></small>
              </div>

            </div>

            <button type="submit" name="enviar" value="enviar" class="company-auth-btn">
              Registrar empresa
            </button>
          </form>

          <p class="company-auth-alt">
            ¿Ya tienes cuenta?
            <a href="login-empresa.php" class="company-auth-link">Inicia sesión</a>
          </p>

          <p class="company-auth-help">
            Tu cuenta quedará pendiente de validación por el administrador antes de publicar actividades.
          </p>

          <?php } ?>

        </div>

      </div>
    </section>
  </main>

</body>
</html>