<?php
$titulo = "<h1>Modificar reserva</h1>";
require_once("head.php");

if(!isset($_SESSION["usuario"])){
    header("Location: index.php");
    exit();
}

if(!isset($_GET["idreserva"])){
    echo "Reserva no válida";
    exit();
}

$idReserva = (int) $_GET["idreserva"];
$idUsuario = $_SESSION["usuario"];

$reserva = $bdact->ObtenerReservaUsuarioPorId($idUsuario, $idReserva);

if($reserva == false){
    echo "Reserva no encontrada";
    exit();
}

$disponibilidades = $bdact->obtenerDisponibilidadesPorServicio($reserva["id_servicio"]);

/* FECHAS FUTURAS DISPONIBLES */
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
$fechasDisponibles = array_slice($fechasDisponibles, 0, 7);

$fechaSeleccionada = isset($_GET["fecha"]) ? $_GET["fecha"] : ($reserva["fecha"] ?? ($fechasDisponibles[0] ?? null));

if ($fechaSeleccionada && !in_array($fechaSeleccionada, $fechasDisponibles)) {
    $fechaSeleccionada = $fechasDisponibles[0] ?? null;
}

$mensaje = "";
$tipoMensaje = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(!isset($_POST["id_detalle_actividad"])){
        $mensaje = "Debes seleccionar una nueva fecha y hora.";
        $tipoMensaje = "error";
    }else{

        $idDetalle = (int) $_POST["id_detalle_actividad"];
        $detalle = $bdact->obtenerDetalleActividadPorId($idDetalle);

        if($detalle == false){
            $mensaje = "La fecha seleccionada no es válida.";
            $tipoMensaje = "error";
        }else{

            $ocupadas = $bdact->contarReservasConfirmadasPorDetalle($idDetalle);

            if($ocupadas >= $detalle["plazas_maximas"] && $idDetalle != $reserva["id_detalle_actividad"]){
                $mensaje = "No hay plazas disponibles para esa fecha.";
                $tipoMensaje = "error";
            }else{

                if($bdact->usuarioTieneReservaEnMismaFechaHoraModificar($idUsuario,$detalle["fecha"],$detalle["hora_inicio"],$idReserva)){
                    $mensaje = "Ya tienes otra reserva en esa misma fecha y hora.";
                    $tipoMensaje = "error";
                }else{

                    $fechaHora = $detalle["fecha"] . " " . $detalle["hora_inicio"];

                    $esPasada = strtotime($reserva['fecha_hora']) < time();
                    $menos24h = strtotime($reserva['fecha_hora']) <= strtotime('+24 hours') && !$esPasada;

                    if($esPasada || $menos24h){
                        $mensaje = "No se puede modificar esta reserva.";
                        $tipoMensaje = "error";
                    }else{

                        $bdact->ModificarReserva($idUsuario, $idReserva, $idDetalle, $fechaHora);

                        header("Location: perfil.php");
                        exit();
                    }
                }
            }
        }
    }
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
        <span class="section-tag">Área personal</span>
        <h2>Modificar reserva</h2>
        <p>Selecciona una nueva fecha y hora para tu actividad.</p>
      </div>

      <div class="booking-layout">

        <!-- IZQUIERDA: MAPA -->
        <article class="booking-map-card">
          <div class="booking-card-header">
            <span class="section-tag">Ubicación</span>
          </div>

          <div class="map-wrapper">
            <iframe
              src="https://www.google.com/maps?q=<?= urlencode($reserva["lugar"]) ?>&output=embed"
              title="Mapa de ubicación de la actividad"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </article>

        <!-- DERECHA: INFO + MODIFICAR -->
        <article class="booking-form-card">
          <div class="booking-info-header">
            <h3><?= htmlentities($reserva["nombre_servicio"]) ?></h3>
          </div>

          <div class="booking-info-content">

            <div class="info-grid">
              <div class="info-item">
                <h4>Reserva actual</h4>
                <p>
                  <?= date("d/m/Y", strtotime($reserva["fecha"])) ?>
                  de <?= substr($reserva["hora_inicio"], 0, 5) ?>
                  a <?= substr($reserva["hora_fin"], 0, 5) ?>
                </p>
              </div>

              <div class="info-item">
                <h4>Ubicación</h4>
                <p><?= htmlentities($reserva["lugar"]) ?></p>
              </div>

              <div class="info-item">
                <h4>Duración</h4>
                <p><?= !empty($reserva["duracion"]) ? htmlentities($reserva["duracion"]) : 'No especificada' ?></p>
              </div>

              <div class="info-item">
                <h4>Estado</h4>
                <p><?= htmlentities(ucfirst($reserva["estado"])) ?></p>
              </div>
            </div>

            <?php if (!empty($fechasDisponibles)): ?>
              <form method="post" class="booking-form">

                <!-- FECHAS -->
                <div class="available-dates-block">
                  <h4>Selecciona una fecha</h4>

                  <div class="available-dates-grid">
                    <?php foreach ($fechasDisponibles as $fecha) : ?>
                      <a
                        href="modificar-reserva.php?idreserva=<?= $idReserva ?>&fecha=<?= urlencode($fecha) ?>"
                        class="date-chip <?= $fecha === $fechaSeleccionada ? 'active' : '' ?>"
                      >
                        <?= date("d/m/Y", strtotime($fecha)) ?>
                      </a>
                    <?php endforeach; ?>
                  </div>
                </div>

                <!-- HORAS -->
                <div class="time-slots-block">
                  <h4>Selecciona fecha y hora</h4>

                  <div class="time-slots-grid">
                    <?php
                    $hayFranjasEnFecha = false;

                    foreach ($disponibilidades as $franja) :
                      if ($franja["fecha"] === $fechaSeleccionada) :
                        $hayFranjasEnFecha = true;

                        $ocupadas = $bdact->contarReservasConfirmadasPorDetalle($franja["id"]);
                        $plazasRestantes = $franja["plazas_maximas"] - $ocupadas;

                        // Si es la franja actual, no la contamos como ocupada para el propio usuario
                        if ($franja["id"] == $reserva["id_detalle_actividad"]) {
                            $plazasRestantes++;
                        }
                    ?>
                        <label class="time-slot">
                          <input
                            type="radio"
                            name="id_detalle_actividad"
                            value="<?= $franja["id"] ?>"
                            <?= $franja["id"] == $reserva["id_detalle_actividad"] ? "checked" : "" ?>
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

                <div class="reservation-actions">
                  <button type="submit" class="btn btn-primary reserve-activity-btn">
                    Guardar cambios
                  </button>

                  <a href="perfil.php" class="btn btn-outline">
                    Volver
                  </a>
                </div>

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