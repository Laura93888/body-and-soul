<?php
$titulo="<h1>Bienvenido a Body and Soul</h1>";
require_once("head.php");

function limpiarTexto($texto){
    $texto = mb_strtolower($texto, 'UTF-8');

    $acentos = ['á','é','í','ó','ú','ü','ñ'];
    $sinAcentos = ['a','e','i','o','u','u','n'];

    return str_replace($acentos, $sinAcentos, $texto);
}

//Comprobamos los parametros que nos han llegado
$buscador = trim($_GET["buscador"] ?? "");
$categoria = trim($_GET["categoria"] ?? "");
$subcategoria = trim($_GET["subcategoria"] ?? "");
$precio = trim($_GET["precio"] ?? "");
$ubicacion = trim($_GET["ubicacion"] ?? "");

$rutaJson = "../JSON/actividades.json";
$datos = json_decode(file_get_contents($rutaJson), true);

$resultados = [];

foreach ($datos as $empresa) {
    foreach ($empresa["servicios"] as $servicio) {
      
      if (($servicio["estado"] ?? "activo") != "activo") {
    continue;
}

        $coincide = true;

        if ($buscador !== "") {
            $texto = limpiarTexto(
              ($servicio["nombre_servicio"] ?? "") . " " .
              ($servicio["descripcion"] ?? "") . " " .
              ($servicio["lugar"] ?? "") . " " .
              ($servicio["categoria"] ?? "") . " " .
              ($servicio["subcategoria"] ?? "")
            );

            if (strpos($texto, strtolower($buscador)) === false) {
                $coincide = false;
            }
        }

        if ($ubicacion !== "") {

          $textoUbicacion = strtolower(
              $servicio["lugar"] . " " .
              ($servicio["codigo_postal"] ?? "")
          );

          if (strpos($textoUbicacion, strtolower($ubicacion)) === false) {
              $coincide = false;
          }
      }

        if ($categoria !== "" && $servicio["categoria"] !== $categoria) {
            $coincide = false;
        }

        if ($subcategoria !== "" && $servicio["id_categoria"] != $subcategoria) {
            $coincide = false;
        }

        if ($precio !== "") {
            $precioServicio = (float)$servicio["precio"];

            if ($precio === "gratis" && $precioServicio > 0) {
                $coincide = false;
            }

            if ($precio === "menos20" && $precioServicio >= 20) {
                $coincide = false;
            }

            if ($precio === "20a50" && ($precioServicio < 20 || $precioServicio > 50)) {
                $coincide = false;
            }

            if ($precio === "mas50" && $precioServicio <= 50) {
                $coincide = false;
            }
        }

        if ($coincide) {
            $resultados[] = $servicio;
        }
    }
}

?>

<main>
  <section class="featured-section">
    <div class="container">
      <div class="section-header">
        <span class="section-tag">Resultados</span>
        <h2>Actividades encontradas</h2>
        <p>Hemos encontrado <?= count($resultados) ?> actividades.</p>
        <?php if(!empty($resultados)){ ?>
          <div id="mapa-resultados" style="height: 420px; margin: 2rem 0; border-radius: 20px; overflow: hidden;"></div>
        <?php } ?>
      </div>

      <div class="activities-grid">
        <?php if (!empty($resultados)) { ?>
          <?php foreach ($resultados as $act) { ?>
            <?php
              $esFavorito = false;
              $volver = urlencode($_SERVER["REQUEST_URI"]);

              if(isset($_SESSION["usuario"])){
                $esFavorito = $bdact->esFavorito($_SESSION["usuario"], $act["id_servicio"]);
              }
            ?>
            <article class="activity-card"> 
              <div class="activity-image-wrapper">
                <?php
                $imagen = !empty($act["imagenes"][0])
                    ? "../" . $act["imagenes"][0]
                    : "../img/default.jpg";
                
                if(isset($_SESSION["usuario"])){ ?>
                  <button 
                    type="button"
                    data-url="gestionar-favorito.php?idservicio=<?= $act["id_servicio"] ?>"
                    class="activity-favorite-btn <?= $esFavorito ? 'activo' : '' ?>"
                  >
                    <?= $esFavorito ? '❤️' : '🤍' ?>
                  </button>
                <?php } ?>
                <img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($act["nombre_servicio"]) ?>" class="activity-image">
              </div>

              <div class="activity-content">

                <?php
                  $datosRating = $bdact->obtenerMediaResenas($act["id_servicio"]);
                ?>

                <a href="actividad.php?idact=<?= $act["id_servicio"] ?>#resenas" class="rating-link">
                  <?php pintarRating($datosRating["media"], $datosRating["total"]); ?>
                </a>

                <h3><?= htmlspecialchars($act["nombre_servicio"]) ?></h3>

                <p><?= htmlspecialchars($act["descripcion"]) ?></p>

                <a href="actividad.php?idact=<?= $act["id_servicio"] ?>" class="btn btn-primary btn-full">
                  Ver actividad
                </a>

              </div>
            </article>
          <?php } ?>
        <?php } else { ?>
          <p>No se han encontrado actividades con esos filtros.</p>
        <?php } ?>
      </div>
    </div>
  </section>
</main>
<script>
  const actividadesMapa = <?= json_encode(array_values(array_filter($resultados, function($act){
      return !empty($act["latitud"]) && !empty($act["longitud"]);
  })), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

  const contenedorMapa = document.getElementById("mapa-resultados");

  if (contenedorMapa) {

      const mapa = L.map("mapa-resultados").setView([40.4168, -3.7038], 9);

      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution: "© OpenStreetMap"
      }).addTo(mapa);

      const marcadores = [];

      actividadesMapa.forEach(function(act){

          const marcador = L.marker([
              parseFloat(act.latitud), 
              parseFloat(act.longitud)
          ]).addTo(mapa);

          marcador.bindPopup(`
              <strong>${act.nombre_servicio}</strong><br>
              ${act.lugar}<br>
              <a href="actividad.php?idact=${act.id_servicio}">Ver actividad</a>
          `);

          marcadores.push(marcador);
      });

      if(marcadores.length > 0){
          const grupo = L.featureGroup(marcadores);
          mapa.fitBounds(grupo.getBounds().pad(0.2));
      }
  }
</script>
<?php require_once("footer.php"); ?>
</body>
</html>