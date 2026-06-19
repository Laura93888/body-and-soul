<?php
$titulo="<h1>Bienvenido a Body and Soul</h1>";
require_once("head.php");

if (!isset($_GET['idact'])) {
  echo $_GET['idact'];
    echo "Actividad no encontrada";
    exit;
}else{
  $id = (int) $_GET['idact'];
}

$datosResenas = $bdact->obtenerMediaResenas($id);
$resenas = $bdact->obtenerResenasServicio($id);

$media = $datosResenas["media"] ? round($datosResenas["media"], 1) : 0;
$totalResenas = $datosResenas["total"];

$esFavorito = false;

if(isset($_SESSION["usuario"])){
    $esFavorito = $bdact->esFavorito($_SESSION["usuario"], $id);
}

$rutaJson = "../JSON/actividades.json";
//VERIFICO QUE EXISTA EN ARCHIVO
if (!file_exists($rutaJson)) {
    echo "No se encuentra el archivo JSON";
    exit;
}

$contenidoJson = file_get_contents($rutaJson);
$datos = json_decode($contenidoJson, true);

if (!$datos) {
    echo "Error al cargar los datos";
    exit;
}

$actividad = null;
$empresaActividad = null;
//CARGO LOS DATOS DE LA ACTIVIDAD CONCRETA
foreach ($datos as $empresa) {
    if (isset($empresa['servicios']) && is_array($empresa['servicios'])) {
        foreach ($empresa['servicios'] as $servicio) {
            if (isset($servicio['id_servicio']) && $servicio['id_servicio'] == $id) {
                $actividad = $servicio;
                $empresaActividad = $empresa['nombre_empresa'];
                break 2;
            }
        }
    }
}

if (!$actividad) {
    echo "Actividad no encontrada";
    exit;
}

if (isset($actividad["estado"]) && $actividad["estado"] == "cancelado") {
    echo "<br><div class='activity-breadcrumb'>Esta actividad ha sido cancelada y ya no está disponible para reservar.</div>";
    exit;
}


?>

  <main class="activity-page">
    <section class="activity-section">
      <div class="container">

        <div class="activity-breadcrumb">
          <a href="categoria.php?cat=<?=$actividad['categoria']?>"><?=$actividad['categoria']?></a>
          <span>/</span>
          <a href="#"><?=$actividad['subcategoria']?></a>
        </div>

        <div class="activity-layout">

          <!-- GALERÍA -->
          <article class="activity-gallery-card">
            <div class="activity-gallery-header">
              <span class="section-tag">Experiencia destacada</span>
              <?php if(isset($_SESSION["usuario"])){ ?>
                <button 
                  type="button"
                  data-url="gestionar-favorito.php?idservicio=<?= $id ?>"
                  class="activity-favorite-btn <?= $esFavorito ? 'activo' : '' ?>"
                >
                  <?= $esFavorito ? '❤️' : '🤍' ?>
                </button>
              <?php } ?>
        
            </div>

            <div class="activity-gallery">
              
              <?php if (!empty($actividad["imagenes"]) && count($actividad["imagenes"]) > 1) { ?>
                <button class="gallery-btn gallery-btn-left" type="button" aria-label="Imagen anterior">
                  &#10094;
                </button>
              <?php } ?>

              <div class="activity-image-wrapper">
                <img
                  src="../<?=$actividad['imagenes'][0]?>"
                  alt="<?=$actividad['nombre_servicio']?>"
                  class="activity-main-image"
                >
              </div>
              <?php if (!empty($actividad["imagenes"]) && count($actividad["imagenes"]) > 1) { ?>
                <button class="gallery-btn gallery-btn-right" type="button" aria-label="Imagen siguiente">
                  &#10095;
                </button>
              <?php } ?>
            </div>
          </article>

          <!-- INFORMACIÓN -->
          <article class="activity-info-card">
            <div class="activity-info-header">
              <h2><?=$actividad['nombre_servicio']?></h2>
            </div>

            <div class="activity-info-content">
              <div class="info-block">
                <h3>Descripción</h3>
                <p><?=$actividad['descripcion']?></p>
              </div>

              <div class="info-grid">
                <div class="info-item">
                  <h3>Duración</h3>
                  <p><?=$actividad['duracion']?></p>
</br> 
                  <h3>Precio</h3>
                  <p><?=$actividad['precio']?></p>
                </div>

                <div class="info-item">
                  <h3>Ubicación</h3>
                  <p><?=$actividad['lugar']?></p>
                </div>

                <div class="info-item info-item-full">
                  <h3>Materiales necesarios</h3>
                  <p><?=$actividad['materiales']?></p>
                </div>
                <div>
                  <?php
                    $datosRating = $bdact->obtenerMediaResenas($actividad["id_servicio"]);
                  ?>
                  
                    <?php pintarRating($datosRating["media"], $datosRating["total"]); ?>
                  </a>
                </div>
              </div>

              <a href="reserva.php?idact=<?= $id ?>" class="btn btn-primary btn-full reserve-btn">
                Reservar actividad
              </a>
            </div>
          </article>

        </div>
      </div>
    </section>
    <section class="activity-reviews-section" id="resenas">
      <div class="container">

        <div class="reviews-intro">
          <span class="section-tag">Opiniones</span>
          <h2>Reseñas de la actividad</h2>
        </div>

        <?php if(empty($resenas)){ ?>

          <div class="empty-reviews">
            <p>Esta actividad todavía no tiene reseñas.</p>
          </div>

        <?php }else{ ?>

          <div class="activity-reviews-grid">

            <?php foreach($resenas as $resena){ ?>

              <article class="activity-review-card">

                <div class="review-header">
                  <div>
                    <h3><?= htmlspecialchars($resena["nombre"] . " " . $resena["apellido"]) ?></h3>
                    <span><?= date("d/m/Y", strtotime($resena["fecha"])) ?></span>
                  </div>

                  <div class="stars">
                    <?php
                      $puntos = (int)$resena["puntuacion"];
                      for($i=1; $i<=5; $i++){
                        echo $i <= $puntos ? "★" : "☆";
                      }
                    ?>
                  </div>
                </div>

                <p class="review-text">
                  <?= htmlspecialchars($resena["comentario"]) ?>
                </p>

              </article>

            <?php } ?>

          </div>

        <?php } ?>

      </div>
    </section>
  </main>
  <script>
    const imagenesActividad = <?= json_encode($actividad['imagenes']) ?>;
    let imagenActual = 0;

    const imagenPrincipal = document.querySelector(".activity-main-image");
    const botonAnterior = document.querySelector(".gallery-btn-left");
    const botonSiguiente = document.querySelector(".gallery-btn-right");

    function mostrarImagen() {
      imagenPrincipal.src = "../" + imagenesActividad[imagenActual];
    }

    botonSiguiente.addEventListener("click", function () {
      imagenActual++;

      if (imagenActual >= imagenesActividad.length) {
        imagenActual = 0;
      }

      mostrarImagen();
    });

    botonAnterior.addEventListener("click", function () {
      imagenActual--;

      if (imagenActual < 0) {
        imagenActual = imagenesActividad.length - 1;
      }

      mostrarImagen();
    });
  </script>

 <?php
require_once("footer.php");
?>
</body>
</html>