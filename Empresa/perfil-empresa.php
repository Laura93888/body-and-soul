<?php
require_once("head.php");
require_once("../bd/bdempresa.php");

// Seguridad: comprobar sesión de empresa
if(!isset($_SESSION["empresa"])){
    header("Location: login.php");
    exit();
}
$categorias=$bdact->obtenerCategoriasPadre();

$idEmpresa = $_SESSION["empresa"];
$mensaje = "";

// Obtener datos actuales
$empresa = $bdempre->sacardatosempresa($idEmpresa);

// Guardar cambios
if(isset($_POST["guardar_perfil"])){

    $nombre = trim($_POST["nombre_empresa"]);
    $categoria = trim($_POST["categoria_principal"]);
    $telefono = trim($_POST["telefono_empresa"]);
    $ciudad = trim($_POST["ciudad_empresa"]);
    $direccion = trim($_POST["direccion_empresa"]);
    $descripcion = trim($_POST["descripcion_empresa"]);

    // Mantengo el logo actual por defecto
    $logo = $empresa["logo_empresa"];

    // Si suben nuevo logo
    if(isset($_FILES["logo_empresa"]) && $_FILES["logo_empresa"]["error"] == 0){

        $nombreArchivo = $_FILES["logo_empresa"]["name"];
        $temporal = $_FILES["logo_empresa"]["tmp_name"];

        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        $extensionesPermitidas = ["jpg", "jpeg", "png", "webp"];

        if(in_array($extension, $extensionesPermitidas)){

            $nuevoNombre = "empresa_" . $idEmpresa . "_" . time() . "." . $extension;
            $rutaDestino = "../img/logos/" . $nuevoNombre;

            if(move_uploaded_file($temporal, $rutaDestino)){
                $logo = "img/logos/" . $nuevoNombre;
            }else{
                $mensaje = "Error al subir el logo";
            }

        }else{
            $mensaje = "Formato de imagen no permitido";
        }
    }

    if($mensaje == ""){
        $bdempre->ModificarPerfilEmpresa(
            $idEmpresa,
            $nombre,
            $categoria,
            $telefono,
            $ciudad,
            $direccion,
            $descripcion,
            $logo
        );

        $mensaje = "Perfil actualizado correctamente";

        // Recargar datos actualizados
        $empresa = $bdempre->sacardatosempresa($idEmpresa);
    }
}

?>

    <div class="company-main">

      <header class="company-topbar">
        <div class="company-topbar-left">
          <span class="company-page-tag">Configuración</span>
          <h2>Perfil empresa</h2>
        </div>
      </header>

      <main class="company-content">

        <section class="company-profile-hero">
          <div class="company-profile-brand">
            
            <img src="<?=$empresa["logo_empresa"]?>" alt="<?=$empresa["nombre_empresa"]?>" class="company-profile-logo">
            <div>
              <span class="company-section-badge">Empresa activa</span>
              <h3><?=$empresa["nombre_empresa"]?></h3>
              <p><?=ucfirst($empresa["categoria_empresa"])?> · <?=$empresa["ciudad_empresa"]?></p>
            </div>
          </div>
        </section>

        <section class="company-profile-card">
          <form action="#" method="post" enctype="multipart/form-data" class="company-profile-form">

            <div class="form-grid">

              <div class="form-group">
                <label for="nombre_empresa">Nombre de la empresa</label>
                <input type="text" id="nombre_empresa" name="nombre_empresa" value="<?=$empresa["nombre_empresa"]?>" required>
              </div>

              <div class="form-group">
                <label for="categoria_principal">Categoría principal</label>
                <select id="categoria_principal" name="categoria_principal" required>
                  <?php foreach($categorias as $cat){ 
                  $selected = ($empresa["categoria_empresa"] == $cat["nombre"]) ? "selected" : "";
                ?>
                  <option value="<?=$cat["nombre"]?>" <?=$selected?>>
                    <?=ucfirst($cat["nombre"])?>
                  </option>
                <?php } ?>
                </select>
              </div>

              <div class="form-group">
                <label for="email_empresa">Correo electrónico</label>
                <input type="email" id="email_empresa" name="email_empresa" value="<?=$empresa["email"]?>" readonly>
              </div>

              <div class="form-group">
                <label for="telefono_empresa">Teléfono</label>
                <input type="text" id="telefono_empresa" name="telefono_empresa" value="<?=$empresa["telefono"]?>" required>
              </div>

              <div class="form-group">
                <label for="ciudad_empresa">Ciudad</label>
                <input type="text" id="ciudad_empresa" name="ciudad_empresa" value="<?=$empresa["ciudad_empresa"]?>" required>
              </div>

              <div class="form-group">
                <label for="direccion_empresa">Dirección</label>
                <input type="text" id="direccion_empresa" name="direccion_empresa" value="<?=$empresa["direccion"]?>" required>
              </div>

              <div class="form-group full-width">
                <label for="descripcion_empresa">Descripción de la empresa</label>
                <textarea id="descripcion_empresa" name="descripcion_empresa" rows="5" required><?=$empresa["descripcion_empresa"]?></textarea>
              </div>

              <div class="form-group full-width">
                <label for="logo_empresa">Actualizar logo o imagen</label>
                <input type="file" id="logo_empresa" name="logo_empresa" accept="image/*">
              </div>

            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary-company" name="guardar_perfil">Guardar cambios</button>
            </div>

          </form>
        </section>

      </main>
    </div>
  </div>

</body>
</html>