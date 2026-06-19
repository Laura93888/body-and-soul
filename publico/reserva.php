<?php
$titulo = "<h1>Reservar actividad</h1>";
require_once("head.php");

require_once("../bd/bd.php");
require_once("../bd/bdact.php");

$bbdd = new db("localhost", 3306, "plataforma_servicios1", "root", "");
$bdact = new bdact("localhost", 3306, "plataforma_servicios1", "root", "");

// Validar id
if (!isset($_GET["idact"])) {
    echo "Actividad no encontrada";
    require_once("footer.php");
    exit;
}

$idServicio = (int) $_GET["idact"];

// Obtener datos
$actividad = $bdact->obtenerActividadPorId($idServicio);
$disponibilidades = $bdact->obtenerDisponibilidadesPorServicio($idServicio);

if (!$actividad) {
    echo "Actividad no encontrada";
    require_once("footer.php");
    exit;
}

/* =======================
   FECHAS FUTURAS DISPONIBLES
======================= */

$fechasDisponibles = [];
$hoy = date("Y-m-d");

foreach ($disponibilidades as $franja) {
    if (!empty($franja["fecha"]) && $franja["fecha"] >= $hoy) {
        if (!in_array($franja["fecha"], $fechasDisponibles)) {
            $fechasDisponibles[] = $franja["fecha"];
        }
    }
}

sort($fechasDisponibles);

// Mostrar solo las próximas 7 fechas disponibles
$fechasDisponibles = array_slice($fechasDisponibles, 0, 7);

$fechaSeleccionada = isset($_GET["fecha"]) ? $_GET["fecha"] : ($fechasDisponibles[0] ?? null);

// Si la fecha recibida no está en la lista válida, usar la primera
if ($fechaSeleccionada && !in_array($fechaSeleccionada, $fechasDisponibles)) {
    $fechaSeleccionada = $fechasDisponibles[0] ?? null;
}

/* =======================
   MENSAJES
======================= */

$mensaje = "";
$tipoMensaje = "";

if (isset($_GET["error"])) {
    if ($_GET["error"] === "duplicada") {
        $mensaje = "Ya tienes una reserva para esta actividad.";
        $tipoMensaje = "error";
    } elseif ($_GET["error"] === "sinplazas") {
        $mensaje = "No quedan plazas disponibles para esta franja.";
        $tipoMensaje = "error";
    } elseif ($_GET["error"] === "franja") {
        $mensaje = "La franja seleccionada no existe.";
        $tipoMensaje = "error";
    } elseif ($_GET["error"] === "franja_invalida") {
        $mensaje = "La franja no pertenece a esta actividad.";
        $tipoMensaje = "error";
    } elseif ($_GET["error"] === "bd") {
        $mensaje = "Error al realizar la reserva.";
        $tipoMensaje = "error";
    } elseif ($_GET["error"] === "ocupada") {
        $mensaje = "Ya tienes una reserva confirmada en esa misma fecha y hora.";
        $tipoMensaje = "error";
    }
}

if (isset($_GET["ok"])) {
    $mensaje = "Reserva realizada correctamente.";
    $tipoMensaje = "ok";
}
?>

<main class="booking-page">
  <section class="booking-section">
    <div class="container">

      <?php if (!empty($mensaje)) : ?>
        <div class="booking-alert booking-alert-<?= $tipoMensaje ?>">
          <?= htmlentities($mensaje) ?>
        </div>
      <?php endif; ?>

      <div class="booking-title-block">
        <span class="section-tag">Reserva tu experiencia</span>
        <h2><?= htmlentities($actividad["nombre_servicio"]) ?></h2>
      </div>

      <div class="booking-layout">

        <!-- IZQUIERDA: MAPA -->
        <article class="booking-map-card">
          <div class="booking-card-header">
            <span class="section-tag">Ubicación</span>
          </div>

          <div class="map-wrapper">
            <iframe
              src="https://www.google.com/maps?q=<?= urlencode($actividad["lugar"]) ?>&output=embed"
              title="Mapa de ubicación de la actividad"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </article>

        <!-- DERECHA: INFO + RESERVA -->
        <article class="booking-form-card">
           <div class="booking-card-header">
            <span class="section-tag">Información</span>
          </div>

          <div class="booking-info-content">

            <div class="info-grid">
              <div class="info-item">
                <h4>Ubicación</h4>
                <p><?= htmlentities($actividad["lugar"]) ?></p>
              </div>

              <div class="info-item">
                <h4>Duración</h4>
                <p><?= !empty($actividad["duracion"]) ? htmlentities($actividad["duracion"]) : 'No especificada' ?></p>
                <h4>Precio</h4>
                  <p><?=$actividad['precio']?>€</p>
                
              </div>
            </div>

            <div class="info-box">
              <h4>Materiales necesarios</h4>
              <p><?= !empty($actividad["materiales"]) ? htmlentities($actividad["materiales"]) : 'No especificados' ?></p>
            </div>

            <?php if (!isset($_SESSION["usuario"])): ?>
              <div class="booking-empty-state">
                <h4>Debes iniciar sesión para reservar</h4>
                <p>Accede con tu cuenta para poder seleccionar una franja horaria.</p>
                <a href="login.php" class="btn btn-primary" style="margin-top: 1rem;">Iniciar sesión</a>
              </div>

            <?php elseif (!empty($fechasDisponibles)): ?>
              <form action="reservar_actividad.php" method="post" class="booking-form">
                <input type="hidden" name="id_servicio" value="<?= $idServicio ?>">

                <!-- FECHAS -->
                <div class="available-dates-block">
                  <h4>Selecciona una fecha</h4>

                  <div class="available-dates-grid">
                    <?php foreach ($fechasDisponibles as $fecha) : ?>
                      <a
                        href="reserva.php?idact=<?= $idServicio ?>&fecha=<?= urlencode($fecha) ?>"
                        class="date-chip <?= $fecha === $fechaSeleccionada ? 'active' : '' ?>"
                      >
                        <?= date("d/m/Y", strtotime($fecha)) ?>
                      </a>
                    <?php endforeach; ?>
                  </div>
                </div>

                <!-- HORAS -->
                <div class="time-slots-block">
                  <h4>Selecciona hora</h4>

                  <div class="time-slots-grid">
                    <?php
                    $hayFranjasEnFecha = false;
                    foreach ($disponibilidades as $franja) :
                      if ($franja["fecha"] === $fechaSeleccionada) :
                        $hayFranjasEnFecha = true;
                        $ocupadas = $bdact->contarReservasConfirmadasPorDetalle($franja["id"]);
                        $plazasRestantes = $franja["plazas_maximas"] - $ocupadas;
                    ?>
                        <label class="time-slot">
                          <input
                            type="radio"
                            name="id_detalle_actividad"
                            value="<?= $franja["id"] ?>"
                            <?= $plazasRestantes <= 0 ? "disabled" : "" ?>
                            required
                          >
                          <span>
                            <?= substr($franja["hora_inicio"], 0, 5) ?> - <?= substr($franja["hora_fin"], 0, 5) ?>
                            <br>
                            <small>
                              <?= $plazasRestantes > 0 ? $plazasRestantes . " plazas disponibles" : "Completo" ?>
                            </small>
                          </span>
                        </label>
                    <?php
                      endif;
                    endforeach;
                    ?>

                    <?php if (!$hayFranjasEnFecha) : ?>
                      <div class="booking-empty-state">
                        <p>No hay horarios disponibles para la fecha seleccionada.</p>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full reserve-activity-btn">
                  Reservar actividad
                </button>
              </form>

            <?php else: ?>
              <div class="booking-empty-state">
                <h4>No hay horarios disponibles</h4>
                <p>Esta actividad todavía no tiene sesiones futuras abiertas.</p>
              </div>
            <?php endif; ?>

          </div>
        </article>

      </div>
    </div>
  </section>
</main>

<?php require_once("footer.php"); ?>
</body>
</html>