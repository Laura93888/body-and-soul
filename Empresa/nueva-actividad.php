<?php
require_once("head.php");
require_once("../bd/bdempresa.php");

if(!isset($_SESSION["empresa"])){
    header("Location: login.php");
    exit();
}

$idEmpresa = $_SESSION["empresa"];

$empresa = $bdempre->sacardatosempresa($idEmpresa);

if($empresa["estado"] == "suspendida"){
?>
<div class="company-main">

  <header class="company-topbar">
    <div class="company-topbar-left">
      <span class="company-page-tag">Empresa suspendida</span>
      <h2>Añadir nueva actividad</h2>
    </div>
  </header>

  <main class="company-content">
    <section class="company-form-hero">
      <div>
        <h3>No puedes publicar nuevas actividades</h3>
        <p>
          Tu empresa está suspendida temporalmente. Mientras el estado siga suspendido,
          no podrás crear nuevas actividades ni volver a activar servicios cancelados.
        </p>
      </div>
    </section>

    <div class="booking-alert booking-alert-error">
      <p>
        Si necesitas resolver esta situación, contacta con administración.
      </p>
    </div>
  </main>

</div>

</body>
</html>
<?php
    exit();
}

$provincias = $bdempre->ObtenerProvincias();

$id_provincia = $_POST["id_provincia"] ?? "";
$id_municipio = $_POST["id_municipio"] ?? "";

$provinciaerror = "";
$municipioerror = "";

$codigo_postal = "";
$codigopostalerror = "";

$idCategoriaPadre=$bdact->ObtenerIdCategoriaPorNombre($empresa["categoria_empresa"]);

//obtener todas las subcategorias de la categoria de la empresa
$subcat=$bdact->obtenerSubcat($idCategoriaPadre);

$registro_ok = false;
$banderaerror = false;

$nombre_servicio = "";
$nombreservicioerror = "";

$id_categoria = "";
$categoriaerror = "";

$horas = "";
$minutos = "";
$duracion = "";
$duracionerror = "";

$direccion_lugar = "";
$direccionlugarerror = "";

$nombre_lugar = "";
$lugar = "";

$precio = "";
$precioerror = "";

$descripcion = "";
$descripcionerror = "";

$materiales = "";
$materialeserror = "";

$imagen = "";
$imagenerror = "";

if(isset($_POST["nombre_servicio"])){
    $nombre_servicio = trim($_POST["nombre_servicio"]);

    if($nombre_servicio == ""){
        $nombreservicioerror = "El nombre de la actividad no puede estar vacío";
        $banderaerror = true;
    }else{
        // 🔥 VALIDACIÓN DE DUPLICADO (aquí es donde va bien)
        if($bdempre->ExisteServicioEmpresa($idEmpresa, $nombre_servicio)){
            $nombreservicioerror = "Ya tienes una actividad con ese nombre";
            $banderaerror = true;
        }
    }
}

if(isset($_POST["id_categoria"])){
    $id_categoria = (trim($_POST["id_categoria"]));

    if($id_categoria == ""){
        $categoriaerror = "Debes seleccionar una subcategoría";
        $banderaerror = true;
    }
}

if(isset($_POST["horas"])){
    $horas = trim($_POST["horas"]);
}

if(isset($_POST["minutos"])){
    $minutos = trim($_POST["minutos"]);
}

if(isset($_POST["enviar"])){

    $horasNumero = (int)$horas;
    $minutosNumero = (int)$minutos;

    if($horas === "" && $minutos === ""){
        $duracionerror = "Debes indicar la duración";
        $banderaerror = true;

    }else if($horasNumero < 0 || $minutosNumero < 0){
        $duracionerror = "La duración no puede ser negativa";
        $banderaerror = true;

    }else if($minutosNumero > 55){
        $duracionerror = "Los minutos deben estar entre 0 y 55";
        $banderaerror = true;

    }else if($minutosNumero % 5 != 0){
        $duracionerror = "Los minutos deben ir de 5 en 5";
        $banderaerror = true;

    }else if($horasNumero == 0 && $minutosNumero == 0){
        $duracionerror = "La duración debe ser mayor que 0";
        $banderaerror = true;

    }else{

        if($horasNumero > 0 && $minutosNumero > 0){

            if($horasNumero == 1){
                $textoHoras = "1 hora";
            }else{
                $textoHoras = $horasNumero . " horas";
            }

            if($minutosNumero == 1){
                $textoMinutos = "1 minuto";
            }else{
                $textoMinutos = $minutosNumero . " minutos";
            }

            $duracion = $textoHoras . " y " . $textoMinutos;

        }else if($horasNumero > 0){

            if($horasNumero == 1){
                $duracion = "1 hora";
            }else{
                $duracion = $horasNumero . " horas";
            }

        }else{

            if($minutosNumero == 1){
                $duracion = "1 minuto";
            }else{
                $duracion = $minutosNumero . " minutos";
            }
        }
    }
}

if(isset($_POST["direccion_lugar"])){
    $direccion_lugar = trim($_POST["direccion_lugar"]);

    if($direccion_lugar == ""){
        $direccionlugarerror = "Debes indicar la calle y número";
        $banderaerror = true;

    }else if(!preg_match('/\d/', $direccion_lugar)){
        $direccionlugarerror = "La dirección debe incluir un número";
        $banderaerror = true;
    }
}

if(isset($_POST["nombre_lugar"])){
    $nombre_lugar = trim($_POST["nombre_lugar"]);
}

if(isset($_POST["codigo_postal"])){
    $codigo_postal = trim($_POST["codigo_postal"]);

    if($codigo_postal == ""){
        $codigopostalerror = "Debes indicar el código postal";
        $banderaerror = true;
    }else if(!preg_match('/^[0-9]{5}$/', $codigo_postal)){
        $codigopostalerror = "El código postal debe tener 5 números";
        $banderaerror = true;
    }
}

if(isset($_POST["enviar"])){

    if($id_provincia == ""){
        $provinciaerror = "Debes seleccionar una provincia";
        $banderaerror = true;
    }

    if($id_municipio == ""){
        $municipioerror = "Debes seleccionar un municipio";
        $banderaerror = true;
    }
}

if(isset($_POST["precio"])){
    $precio = trim($_POST["precio"]);

    if($precio == ""){
        $precioerror = "El precio no puede estar vacío";
        $banderaerror = true;
    }else if($precio < 0){
        $precioerror = "El precio no puede ser negativo";
        $banderaerror = true;
    }
}

if(isset($_POST["descripcion"])){
    $descripcion = trim($_POST["descripcion"]);

    if($descripcion == ""){
        $descripcionerror = "La descripción no puede estar vacía";
        $banderaerror = true;
    }else if(strlen($descripcion) > 400){
    $descripcionerror = "La descripción es demasiado larga";
    $banderaerror = true;
  }
  
}

if(isset($_POST["materiales"])){
    $materiales = trim($_POST["materiales"]);

    if(strlen($materiales) > 200){
        $materialeserror = "Los materiales no pueden superar los 200 caracteres";
        $banderaerror = true;
    }
}


if(isset($_POST["enviar"])){

    if(!isset($_FILES["imagenes"]) || empty($_FILES["imagenes"]["name"][0])){
        $imagenerror = "Debes subir al menos una imagen";
        $banderaerror = true;
    }else{

        $tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];

        for($i = 0; $i < count($_FILES["imagenes"]["name"]); $i++){

            if($_FILES["imagenes"]["error"][$i] == 0){

                $tipoArchivo = $_FILES["imagenes"]["type"][$i];

                if(!in_array($tipoArchivo, $tiposPermitidos)){
                    $imagenerror = "Todas las imágenes deben ser JPG, PNG o WEBP";
                    $banderaerror = true;
                }
            }
        }
    }
}

    if($banderaerror == false && isset($_POST["enviar"])){

      $municipio = $bdempre->ObtenerMunicipioPorId($id_municipio);

      if($nombre_lugar != ""){
        $lugar = $nombre_lugar . ", " . $direccion_lugar . ", " . $municipio["nombre"];
      }else{
          $lugar = $direccion_lugar . ", " . $municipio["nombre"];
      }

      $direccionMapa = $direccion_lugar . ", " . $codigo_postal . ", " . $municipio["nombre"] . ", " . $municipio["provincia"] . ", España";

      $coordenadas = $bdempre->ObtenerCoordenadas($direccionMapa);

      if($coordenadas == false){
          $direccionlugarerror = "No hemos podido localizar la dirección. Revisa la calle, número, código postal y municipio.";
          $banderaerror = true;
      }

      $idServicio = $bdempre->InsertarServicio(
        $idEmpresa,
        $nombre_servicio,
        $descripcion,
        $lugar,
        $id_categoria,
        $precio,
        $duracion,
        $materiales,
        $id_municipio,
        $codigo_postal,
        $coordenadas["latitud"],
        $coordenadas["longitud"]
      );

      for($i = 0; $i < count($_FILES["imagenes"]["name"]); $i++){

          if($_FILES["imagenes"]["error"][$i] == 0){

              $nombreArchivo = time() . "_" . $i . "_" . basename($_FILES["imagenes"]["name"][$i]);
              $ruta = "img/" . $empresa["categoria_empresa"] . "/" . $nombreArchivo;

              move_uploaded_file($_FILES["imagenes"]["tmp_name"][$i], "../" . $ruta);

              $bdempre->InsertarImagenServicio($idServicio, $ruta);
          }
      }

      header("Location: gestionar-horarios.php?idservicio=".$idServicio);
      exit();
    }
?>

<div class="company-main">

  <header class="company-topbar">
    <div class="company-topbar-left">
      <span class="company-page-tag">Publicación</span>
      <h2>Añadir nueva actividad</h2>
    </div>

    <div class="company-topbar-right">
      <a href="mis-servicios.php" class="company-back-link">Ver mis servicios</a>
    </div>
  </header>

  <main class="company-content">

    <section class="company-form-hero">
      <div>
        <h3>Información general de la actividad</h3>
        <p>
          Completa los datos principales de la actividad. Después podrás añadir fechas,
          horarios y plazas disponibles.
        </p>
      </div>
    </section>

    <?php if($registro_ok == true){ ?>
      <div class="booking-alert booking-alert-ok">
        <p>La actividad se ha guardado correctamente.</p>
      </div>
    <?php } ?>

    <section class="company-form-card">
      <form action="" method="post" enctype="multipart/form-data" class="company-service-form">

  <div class="form-grid">

    <!-- NOMBRE -->
    <div class="form-group">
      <label for="nombre_servicio">Nombre</label>
      <input 
        type="text" 
        id="nombre_servicio" 
        name="nombre_servicio" 
        placeholder="Ej. Yoga Flow Sunset"
        value="<?php echo $nombre_servicio; ?>"
      >
      <span class="form-error"><?php echo $nombreservicioerror; ?></span>
    </div>

    <!-- SUBCATEGORÍA -->
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

    <!-- DURACIÓN -->
    <div class="form-group">
      <label>Duración</label>
      <div class="duration-row">
        <div class="duration-field">
          <input 
            type="number" 
            id="horas" 
            name="horas" 
            min="0"
            placeholder="0"
            value="<?php echo $horas; ?>"
          >
        </div>
        <span class="duration-separator">h</span>
        <div class="duration-field">
          <select id="minutos" name="minutos">
            <option value="0" <?php if($minutos == "0"){ echo "selected"; } ?>>00</option>
            <option value="5" <?php if($minutos == "5"){ echo "selected"; } ?>>05</option>
            <option value="10" <?php if($minutos == "10"){ echo "selected"; } ?>>10</option>
            <option value="15" <?php if($minutos == "15"){ echo "selected"; } ?>>15</option>
            <option value="20" <?php if($minutos == "20"){ echo "selected"; } ?>>20</option>
            <option value="25" <?php if($minutos == "25"){ echo "selected"; } ?>>25</option>
            <option value="30" <?php if($minutos == "30"){ echo "selected"; } ?>>30</option>
            <option value="35" <?php if($minutos == "35"){ echo "selected"; } ?>>35</option>
            <option value="40" <?php if($minutos == "40"){ echo "selected"; } ?>>40</option>
            <option value="45" <?php if($minutos == "45"){ echo "selected"; } ?>>45</option>
            <option value="50" <?php if($minutos == "50"){ echo "selected"; } ?>>50</option>
            <option value="55" <?php if($minutos == "55"){ echo "selected"; } ?>>55</option>
          </select>
        </div>
        <span class="duration-separator">min</span>
      </div>
      <span class="form-error"><?php echo $duracionerror; ?></span>
      </div>

    <!-- PRECIO -->
    <div class="form-group">
      <label for="precio">Precio (€)</label>
      <input 
        type="number" 
        id="precio" 
        name="precio" 
        placeholder="Ej. 18.50" 
        min="0" 
        step="0.01"
        value="<?php echo $precio; ?>"
      >
      <span class="form-error"><?php echo $precioerror; ?></span>
    </div>

    <!-- DIRECCIÓN -->
    <div class="form-group">
      <label for="direccion_lugar">Dirección</label>
      <input 
        type="text" 
        id="direccion_lugar" 
        name="direccion_lugar" 
        placeholder="Ej. Calle Alcalá, 25"
        value="<?php echo $direccion_lugar; ?>"
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

    <div class="form-group">
      <label for="codigo_postal">Código postal</label>

      <input 
        type="text"
        id="codigo_postal"
        name="codigo_postal"
        maxlength="5"
        value="<?php echo $codigo_postal; ?>"
      >

      <span class="form-error"><?php echo $codigopostalerror; ?></span>
    </div>

    <!-- NOMBRE LUGAR -->
    <div class="form-group full-width">
      <label for="nombre_lugar">
        Nombre del lugar <span class="optional-label">(opcional)</span>
      </label>
      <input 
        type="text" 
        id="nombre_lugar" 
        name="nombre_lugar" 
        placeholder="Ej. Indoor Pádel Center"
        value="<?php echo $nombre_lugar; ?>"
      >
    </div>

    <!-- DESCRIPCIÓN -->
    <div class="form-group full-width">
      <label for="descripcion">Descripción</label>
      <textarea 
  id="descripcion" 
  name="descripcion" 
  rows="5" 
  maxlength="400"
  placeholder="Describe la actividad con detalle..."
      ><?php echo $descripcion; ?></textarea>
        <small class="form-hint">
          Máximo 400 caracteres
        </small>

        <small id="contadorDescripcion" class="form-counter">
          0 / 400
        </small>

      <span class="form-error"><?php echo $descripcionerror; ?></span>
    </div>

    <!-- MATERIALES -->
    <div class="form-group full-width">
  <label for="materiales">Materiales empleados</label>

  <textarea 
    id="materiales" 
    name="materiales" 
    rows="3" 
    maxlength="200"
    placeholder="Ej. Esterilla, bloques, cinta elástica..."
  ><?php echo $materiales; ?></textarea>

  <small class="form-hint">
    Máximo 200 caracteres
  </small>

  <small id="contadorMateriales" class="form-counter">
    0 / 200
  </small>

  <span class="form-error"><?php echo $materialeserror; ?></span>
</div>

    <!-- IMAGEN -->
    <div class="form-group full-width">
      <label for="imagenes">Imágenes de la actividad</label>
      <input 
        type="file" 
        id="imagenes" 
        name="imagenes[]" 
        accept="image/*"
        multiple
      >
      <span class="form-error"><?php echo $imagenerror; ?></span>
    </div>

  </div>

  <div class="form-actions">
    <button type="submit" name="enviar" class="btn-primary-company">
      Guardar actividad
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