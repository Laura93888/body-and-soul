<?php
require_once("head.php");
require_once("../bd/bdempresa.php");

if(!isset($_SESSION["empresa"])){
    header("Location: login.php");
    exit();
}

$idEmpresa = $_SESSION["empresa"];

if(!isset($_GET["id"])){
    header("Location: mis-servicios.php");
    exit();
}

$idServicio = (int) $_GET["id"];

$empresa = $bdempre->sacardatosempresa($idEmpresa);
$idCategoriaPadre = $bdact->ObtenerIdCategoriaPorNombre($empresa["categoria_empresa"]);
$subcat = $bdact->obtenerSubcat($idCategoriaPadre);

$servicio = $bdempre->obtenerActividadPorIdempresa($idServicio);

$lugarCompleto = $servicio["lugar"] ?? "";

$nombre_lugar = "";
$direccion_lugar = "";

$partesLugar = array_map("trim", explode(",", $lugarCompleto));

if(count($partesLugar) >= 3){

    $nombre_lugar = $partesLugar[0];

    // Quitamos el primer elemento = nombre del lugar
    array_shift($partesLugar);

    // Quitamos el último elemento = municipio
    array_pop($partesLugar);

    // Lo que queda es la dirección
    $direccion_lugar = implode(", ", $partesLugar);

}else if(count($partesLugar) == 2){

    $direccion_lugar = $partesLugar[0];

}else{
    $direccion_lugar = $lugarCompleto;
}

$id_municipio = $servicio["id_municipio"] ?? "";
$codigo_postal = $servicio["codigo_postal"] ?? "";

$id_provincia = "";

if($id_municipio != ""){
    $municipioActual = $bdempre->ObtenerMunicipioPorId($id_municipio);
    $id_provincia = $municipioActual["id_provincia"];
}

$provincias = $bdempre->ObtenerProvincias();

if($servicio == false || $servicio["id_empresa"] != $idEmpresa){
    header("Location: mis-servicios.php");
    exit();
}

$registro_ok = false;
$banderaerror = false;

$nombre_servicio = $servicio["nombre_servicio"];
$nombreservicioerror = "";

$id_categoria = $servicio["id_categoria"];
$categoriaerror = "";

$duracion = $servicio["duracion"];
$duracionerror = "";

$precio = $servicio["precio"];
$precioerror = "";

$descripcion = $servicio["descripcion"];
$descripcionerror = "";

$materiales = $servicio["materiales"];
$materialeserror = "";

if($id_municipio != ""){
    $municipioActual = $bdempre->ObtenerMunicipioPorId($id_municipio);
    $id_provincia = $municipioActual["id_provincia"];
}

$provinciaerror = "";
$municipioerror = "";
$codigopostalerror = "";
$direccionlugarerror = "";

if(isset($_POST["enviar"])){

    $nombre_servicio = trim($_POST["nombre_servicio"]);
    $id_categoria = trim($_POST["id_categoria"]);
    $duracion = trim($_POST["duracion"]);
    $precio = trim($_POST["precio"]);
    $descripcion = trim($_POST["descripcion"]);
    $materiales = trim($_POST["materiales"]);

    $direccion_lugar = trim($_POST["direccion_lugar"]);
    $nombre_lugar = trim($_POST["nombre_lugar"]);

    $id_provincia = $_POST["id_provincia"] ?? "";
    $id_municipio = $_POST["id_municipio"] ?? "";
    $codigo_postal = trim($_POST["codigo_postal"] ?? "");

    if($nombre_servicio == ""){
        $nombreservicioerror = "El nombre de la actividad no puede estar vacío";
        $banderaerror = true;
    }

    if($id_categoria == ""){
        $categoriaerror = "Debes seleccionar una subcategoría";
        $banderaerror = true;
    }

    if($duracion == ""){
        $duracionerror = "La duración no puede estar vacía";
        $banderaerror = true;
    }

    if($precio == ""){
        $precioerror = "El precio no puede estar vacío";
        $banderaerror = true;
    }else if($precio < 0){
        $precioerror = "El precio no puede ser negativo";
        $banderaerror = true;
    }

    if($descripcion == ""){
        $descripcionerror = "La descripción no puede estar vacía";
        $banderaerror = true;
    }else if(strlen($descripcion) > 400){
        $descripcionerror = "La descripción es demasiado larga";
        $banderaerror = true;
    }

    if(strlen($materiales) > 200){
        $materialeserror = "Los materiales no pueden superar los 200 caracteres";
        $banderaerror = true;
    }

    if($direccion_lugar == ""){
        $direccionlugarerror = "Debes indicar la calle y número";
        $banderaerror = true;
    }else if(!preg_match('/\d/', $direccion_lugar)){
        $direccionlugarerror = "La dirección debe incluir un número";
        $banderaerror = true;
    }

    if($id_provincia == ""){
        $provinciaerror = "Debes seleccionar una provincia";
        $banderaerror = true;
    }

    if($id_municipio == ""){
        $municipioerror = "Debes seleccionar un municipio";
        $banderaerror = true;
    }

    if($codigo_postal == ""){
        $codigopostalerror = "Debes indicar el código postal";
        $banderaerror = true;

    }else if(!preg_match('/^[0-9]{5}$/', $codigo_postal)){
        $codigopostalerror = "El código postal debe tener 5 números";
        $banderaerror = true;
    }


    if($banderaerror == false){
      $municipio = $bdempre->ObtenerMunicipioPorId($id_municipio);

      if($nombre_lugar != ""){
          $lugar = $nombre_lugar . ", " . $direccion_lugar . ", " . $municipio["nombre"];
      }else{
          $lugar = $direccion_lugar . ", " . $municipio["nombre"];
      }

      $bdempre->ActualizarServicio(
          $idServicio,
          $idEmpresa,
          $nombre_servicio,
          $descripcion,
          $lugar,
          $id_categoria,
          $precio,
          $duracion,
          $materiales,
          $id_municipio,
          $codigo_postal
      );

      if(isset($_FILES["imagenes"]) && !empty($_FILES["imagenes"]["name"][0])){

        $tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];

        for($i = 0; $i < count($_FILES["imagenes"]["name"]); $i++){

            if($_FILES["imagenes"]["error"][$i] == 0){

                $tipoArchivo = $_FILES["imagenes"]["type"][$i];

                if(in_array($tipoArchivo, $tiposPermitidos)){

                    $nombreArchivo = time() . "_" . $i . "_" . basename($_FILES["imagenes"]["name"][$i]);

                    $rutaBD = "img/" . $empresa["categoria_empresa"] . "/" . $nombreArchivo;
                    $rutaServidor = "../" . $rutaBD;

                    move_uploaded_file($_FILES["imagenes"]["tmp_name"][$i], $rutaServidor);

                    $bdempre->InsertarImagenServicio($idServicio, $rutaBD);
                }
            }
        }
      }

require("../bd/generarJSONact.php");

        header("Location: editar-servicio.php?id=".$idServicio."&ok=1");
        exit();
    }
}
?>

<div class="company-main">

  <header class="company-topbar">
    <div class="company-topbar-left">
      <span class="company-page-tag">Edición</span>
      <h2>Editar servicio</h2>
    </div>

    <div class="company-topbar-right">
      <a href="mis-servicios.php" class="company-back-link">Volver a mis servicios</a>
    </div>
  </header>

  <main class="company-content">

    <section class="company-form-hero">
      <div>
        <span class="company-section-badge">Servicio existente</span>
        <h3><?= htmlspecialchars($servicio["nombre_servicio"]) ?></h3>
        <p>
          Modifica los datos generales de la actividad. Las fechas, horarios y plazas se gestionan desde el apartado de horarios.
        </p>
      </div>
    </section>

    <?php if(isset($_GET["ok"])){ ?>
      <div class="booking-alert booking-alert-ok">
        <p>Los cambios se han guardado correctamente.</p>
      </div>
    <?php } ?>

    <section class="company-form-card">
      <form action="" method="post" class="company-service-form" enctype="multipart/form-data">

        <div class="form-grid">

          <div class="form-group">
            <label for="nombre_servicio">Nombre</label>
            <input 
              type="text" 
              id="nombre_servicio" 
              name="nombre_servicio"
              value="<?php echo htmlspecialchars($nombre_servicio); ?>"
            >
            <span class="form-error"><?php echo $nombreservicioerror; ?></span>
          </div>

          <div class="form-group">
            <label for="id_categoria">Subcategoría</label>
            <select id="id_categoria" name="id_categoria">
              <option value="">Selecciona una subcategoría</option>

              <?php foreach($subcat as $subcategoria){ ?>
                <option 
                  value="<?php echo $subcategoria["id_categoria"]; ?>"
                  <?php if($id_categoria == $subcategoria["id_categoria"]){ echo "selected"; } ?>
                >
                  <?php echo $subcategoria["nombre"]; ?>
                </option>
              <?php } ?>

            </select>
            <span class="form-error"><?php echo $categoriaerror; ?></span>
          </div>

          <div class="form-group">
            <label for="duracion">Duración</label>
            <input 
              type="text" 
              id="duracion" 
              name="duracion"
              placeholder="Ej. 1 hora y 30 minutos"
              value="<?php echo htmlspecialchars($duracion); ?>"
            >
            <span class="form-error"><?php echo $duracionerror; ?></span>
          </div>

          <div class="form-group">
            <label for="precio">Precio (€)</label>
            <input 
              type="number" 
              id="precio" 
              name="precio"
              min="0"
              step="0.01"
              value="<?php echo htmlspecialchars($precio); ?>"
            >
            <span class="form-error"><?php echo $precioerror; ?></span>
          </div>

          <div class="form-group">
            <label for="direccion_lugar">Dirección</label>
            <input 
              type="text" 
              id="direccion_lugar" 
              name="direccion_lugar"
              placeholder="Ej. Calle Alcalá, 25"
              value="<?php echo htmlspecialchars($direccion_lugar); ?>"
            >
            <span class="form-error"><?php echo $direccionlugarerror; ?></span>
          </div>

          <!-- PROVINCIA -->
          <div class="form-group">
            <label for="id_provincia">Provincia</label>
            <select id="id_provincia" name="id_provincia">
              <option value="">Selecciona una provincia</option>

              <?php foreach($provincias as $provincia){ ?>
                <option 
                  value="<?php echo $provincia["id_provincia"]; ?>"
                  <?php if($id_provincia == $provincia["id_provincia"]){ echo "selected"; } ?>
                >
                  <?php echo $provincia["nombre"]; ?>
                </option>
              <?php } ?>
            </select>
            <span class="form-error"><?php echo $provinciaerror; ?></span>
          </div>

          <!-- MUNICIPIO -->
          <div class="form-group">
            <label for="id_municipio">Municipio</label>
            <select id="id_municipio" name="id_municipio">
              <option value="">Selecciona primero una provincia</option>
            </select>
            <span class="form-error"><?php echo $municipioerror; ?></span>
          </div>
            
           <!-- CODIGO POSTAL -->
          <div class="form-group">
            <label for="codigo_postal">Código postal</label>
            <input 
              type="text"
              id="codigo_postal"
              name="codigo_postal"
              maxlength="5"
              value="<?php echo htmlspecialchars($codigo_postal); ?>"
            >
            <span class="form-error"><?php echo $codigopostalerror; ?></span>
          </div>

          <div class="form-group full-width">
            <label for="nombre_lugar">Nombre del lugar <span class="optional-label">(opcional)</span></label>
            <input 
              type="text" 
              id="nombre_lugar" 
              name="nombre_lugar"
              placeholder="Ej. Indoor Pádel Center"
              value="<?php echo htmlspecialchars($nombre_lugar); ?>"
            >
          </div>

          <div class="form-group full-width">
            <label for="descripcion">Descripción</label>
            <textarea 
              id="descripcion" 
              name="descripcion" 
              rows="5"
              maxlength="400"
            ><?php echo htmlspecialchars($descripcion); ?></textarea>

            <small class="form-hint">Máximo 400 caracteres</small>
            <small id="contadorDescripcion" class="form-counter">0 / 400</small>

            <span class="form-error"><?php echo $descripcionerror; ?></span>
          </div>

          <div class="form-group full-width">
            <label for="materiales">Materiales empleados</label>
            <textarea 
              id="materiales" 
              name="materiales" 
              rows="3"
              maxlength="200"
            ><?php echo htmlspecialchars($materiales); ?></textarea>

            <small class="form-hint">Máximo 200 caracteres</small>
            <small id="contadorMateriales" class="form-counter">0 / 200</small>

            <span class="form-error"><?php echo $materialeserror; ?></span>
          </div>

          <div class="form-group full-width">
          <label for="imagenes">Añadir más imágenes</label>
          <input 
            type="file" 
            id="imagenes" 
            name="imagenes[]" 
            accept="image/*"
            multiple
          >
        </div>

        </div>

        <div class="form-actions">
          <a href="gestionar-horarios.php?idservicio=<?=$idServicio?>" class="btn-secondary-company">
            Gestionar horarios
          </a>

          <button type="submit" name="enviar" class="btn-primary-company">
            Guardar cambios
          </button>
        </div>

      </form>
    </section>

  </main>
</div>

<script>
  const selectProvincia = document.getElementById("id_provincia");
  const selectMunicipio = document.getElementById("id_municipio");

  const municipioSeleccionado = "<?php echo $id_municipio; ?>";

  function cargarMunicipios(idProvincia, idMunicipioSeleccionado = ""){

      selectMunicipio.innerHTML = "<option value=''>Cargando municipios...</option>";

      if(idProvincia === ""){
          selectMunicipio.innerHTML = "<option value=''>Selecciona primero una provincia</option>";
          return;
      }

      fetch("obtener-municipios.php?id_provincia=" + idProvincia)
          .then(response => response.json())
          .then(municipios => {

              selectMunicipio.innerHTML = "<option value=''>Selecciona un municipio</option>";

              municipios.forEach(function(municipio){

                  let selected = "";

                  if(idMunicipioSeleccionado == municipio.id_municipio){
                      selected = "selected";
                  }

                  selectMunicipio.innerHTML += `
                      <option value="${municipio.id_municipio}" ${selected}>
                          ${municipio.nombre}
                      </option>
                  `;
              });
          })
          .catch(error => {
              console.error(error);
              selectMunicipio.innerHTML = "<option value=''>Error cargando municipios</option>";
          });
  }

  selectProvincia.addEventListener("change", function(){
      cargarMunicipios(this.value);
  });

  if(selectProvincia.value !== ""){
      cargarMunicipios(selectProvincia.value, municipioSeleccionado);
  }
</script>

</body>
</html>