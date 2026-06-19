<?php
session_start();

require_once("../bd/bdact.php");
$bdact = new bdact("localhost", 3306, "plataforma_servicios1", "root", "");

require_once("valoracion.php");
$rutaJson = "../JSON/actividades.json";

function limpiarTexto($texto){
    $texto = mb_strtolower($texto, 'UTF-8');

    $acentos = ['á','é','í','ó','ú','ü','ñ'];
    $sinAcentos = ['a','e','i','o','u','u','n'];

    return str_replace($acentos, $sinAcentos, $texto);
}  

if (!file_exists($rutaJson)) {
    echo "<p>No se encuentra el archivo JSON.</p>";
    exit;
}

$contenidoJson = file_get_contents($rutaJson);
$datos = json_decode($contenidoJson, true);

if (!$datos) {
    echo "<p>Error al cargar los datos.</p>";
    exit;
}

$buscador = trim($_GET["buscador"] ?? "");
$categoria = $_GET["categoria"] ?? "";
$precio = $_GET["precio"] ?? "";
$fecha = $_GET["fecha"] ?? "";
$ubicacion = trim($_GET["ubicacion"] ?? "");
$resultados = [];

foreach ($datos as $empresa) {
    if (!isset($empresa["servicios"]) || !is_array($empresa["servicios"])) {
        continue;
    }

    foreach ($empresa["servicios"] as $servicio) {

      // No mostrar servicios cancelados
    if (isset($servicio["estado"]) && $servicio["estado"] != "activo") {
        continue;
    }

        $texto = 
            $servicio["nombre_servicio"] . " " .
            $servicio["descripcion"] . " " .
            $servicio["lugar"] . " " .
            $servicio["categoria"] . " " .
            $servicio["subcategoria"]
        ;

        $coincide = true;

        $textoNormalizado = limpiarTexto($texto);
        $buscadorNormalizado = limpiarTexto($buscador);

        if ($buscador !== "" && strpos($textoNormalizado, $buscadorNormalizado) === false) {
            $coincide = false;
        }
        
        $ubicacionNormalizada = limpiarTexto($ubicacion);

        if($ubicacion !== ""){

            $textoUbicacion = 
                $servicio["lugar"] . " " .
                ($servicio["codigo_postal"] ?? "");

            $textoUbicacionNormalizado = limpiarTexto($textoUbicacion);

            if(strpos($textoUbicacionNormalizado, $ubicacionNormalizada) === false){
                $coincide = false;
            }
        }

        if ($categoria !== "" && $servicio["categoria"] !== $categoria) {
            $coincide = false;
        }

        if ($precio !== "") {
            $precioServicio = (float)$servicio["precio"];

            if ($precio == "0-10" && ($precioServicio < 0 || $precioServicio > 10)) {
                $coincide = false;
            }

            if ($precio == "10-25" && ($precioServicio < 10 || $precioServicio > 25)) {
                $coincide = false;
            }

            if ($precio == "25-50" && ($precioServicio < 25 || $precioServicio > 50)) {
                $coincide = false;
            }

            if ($precio == "50+" && $precioServicio <= 50) {
                $coincide = false;
            }
        }

        if ($fecha !== "") {

        $coincideFecha = false;

        if (isset($servicio["detalles"]) && is_array($servicio["detalles"])) {
            foreach ($servicio["detalles"] as $detalle) {

                if ($fecha == "semana") {
                    $hoy = date("Y-m-d");
                    $finSemana = date("Y-m-d", strtotime("+7 days"));

                    if ($detalle["fecha"] >= $hoy && $detalle["fecha"] <= $finSemana) {
                        $coincideFecha = true;
                        break;
                    }

                } else {
                    if ($detalle["fecha"] == $fecha) {
                        $coincideFecha = true;
                        break;
                    }
                }
            }
        }

    if (!$coincideFecha) {
        $coincide = false;
    }
}

        if ($coincide) {
            $resultados[] = $servicio;
        }
    }
}

if (empty($resultados)) {
    echo "<h3 style='color:#1b4965; font-size:2em; text-align:center;'>No se han encontrado actividades.</h3>";
    exit;
}
?>

<div class="activities-grid live-results-grid">
  <?php foreach ($resultados as $act) { ?>
    <?php
        $esFavorito = false;
        $volver = urlencode($_SERVER["HTTP_REFERER"] ?? "index.php");

        if(isset($_SESSION["usuario"])){
        $esFavorito = $bdact->esFavorito($_SESSION["usuario"], $act["id_servicio"]);
        }
    ?>
    <article class="activity-card">
      <div class="activity-image-wrapper">
        <?php
        $imagen = !empty($act["imagenes"][0]) ? "../" . $act["imagenes"][0] : "../assets/placeholder.jpg";
        ?>
        <?php if(isset($_SESSION["usuario"])){ ?>
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

        <p>
          <?= htmlspecialchars($act["descripcion"]) ?>
        </p>

        <a href="actividad.php?idact=<?= $act["id_servicio"] ?>" class="btn btn-primary btn-full" aria-label="Ver actividad <?= htmlspecialchars($act["nombre_servicio"]) ?>">
          Ver actividad
        </a>
      </div>
    </article>
  <?php } ?>
</div>