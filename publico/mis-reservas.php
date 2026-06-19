  <?php
$titulo="<h1>Mi perfil</h1>";
require_once("head.php");

if(!isset($_SESSION["usuario"])){
  header("Location: index.php");
  exit();
}

$id=$_SESSION["usuario"];

$reservas = $bdact->ObtenerReservasUsuario($id);
?>

<main class="reservations-page">
  <section class="reservations-section">
    <div class="container">

      <div class="reservations-intro">
        <span class="section-tag">Área personal</span>
        <h2>Mis actividades reservadas</h2>
        <p>
          Aquí puedes consultar tus próximas reservas, revisar la información de cada actividad
          y gestionar cualquier cambio necesario.
        </p>
      </div>

      <div class="reservations-filters">
        <button class="filter-chip active" type="button" data-filter="proximas">Próximas</button>
        <button class="filter-chip" type="button" data-filter="hoy">Hoy</button>
        <button class="filter-chip" type="button" data-filter="semana">Esta semana</button>
        <button class="filter-chip" type="button" data-filter="pasadas">Pasadas</button>
      </div>

      <div class="reservations-list">

        <?php if ($reservas && count($reservas) > 0): ?>
          <?php foreach ($reservas as $reserva): ?>

            <?php
              
              $categoriaTexto = "";
              if (!empty($reserva["categoria_padre"]) && !empty($reserva["subcategoria"])) {
                  $categoriaTexto = $reserva["categoria_padre"] . " · " . $reserva["subcategoria"];
              } else if (!empty($reserva["subcategoria"])) {
                  $categoriaTexto = $reserva["subcategoria"];
              } else {
                  $categoriaTexto = "Actividad";
              }

              $fechaFormateada = date("d/m/Y", strtotime($reserva["fecha"]));
              $horaFormateada = date("H:i", strtotime($reserva["hora_inicio"]));
            ?>

            <article class="reservation-card" data-fecha="<?= $reserva["fecha"] ?>">
              <div class="reservation-image">
                <?php
                  $imagen = !empty($reserva["imagen"]) ? "../" . $reserva["imagen"] : "../img/placeholder.jpg";
                ?>

                <img src="<?= $imagen ?>" alt="<?= htmlspecialchars($reserva["nombre_servicio"]) ?>">
              </div>

              <div class="reservation-content">
                <div class="reservation-top">
                  <div>
                    <p class="reservation-category"><?= htmlspecialchars($categoriaTexto) ?></p>
                    <h3><?= htmlspecialchars($reserva["nombre_servicio"]) ?></h3>
                  </div>
                  <?php
                    $esPasada = strtotime($reserva['fecha_hora']) < time();

                    $estadoMostrar = $reserva["estado"];
                    $estadoClase = $reserva["estado"];

                    if($esPasada && $reserva["estado"] == "confirmada"){
                        $estadoMostrar = "realizada";
                        $estadoClase = "pasada";
                    }
                  ?>
                  <span class="reservation-status status-<?= $estadoClase ?>">
                    <?= ucfirst($estadoMostrar) ?>
                  </span>
                </div>

                <p class="reservation-description">
                  <?= htmlspecialchars($reserva["descripcion"]) ?>
                </p>

                <div class="reservation-info-grid">
                  <div class="reservation-info-item">
                    <span class="info-label">Fecha</span>
                    <span class="info-value"><?= $fechaFormateada ?></span>
                  </div>

                  <div class="reservation-info-item">
                    <span class="info-label">Hora</span>
                    <span class="info-value"><?= $horaFormateada ?></span>
                  </div>

                  <div class="reservation-info-item">
                    <span class="info-label">Duración</span>
                    <span class="info-value"><?= htmlspecialchars($reserva["duracion"]) ?></span>
                  </div>

                  <div class="reservation-info-item">
                    <span class="info-label">Ubicación</span>
                    <span class="info-value"><?= htmlspecialchars($reserva["lugar"]) ?></span>
                  </div>
                </div>

              <?php
                $esPasada = strtotime($reserva['fecha_hora']) < time();
                $menos24h = strtotime($reserva['fecha_hora']) <= strtotime('+24 hours') && !$esPasada;

                $puedeGestionar = $reserva['estado'] == 'confirmada' && !$esPasada && !$menos24h;
              ?>

              <div class="reservation-actions">

                <a href="actividad.php?idact=<?= $reserva["id_servicio"] ?>" class="btn btn-outline">
                  Ver actividad
                </a>

                <?php
                  $puedeValorar = false;

                  if (
                    $esPasada &&
                    $reserva["estado"] == "confirmada"
                  ) {

                    $puedeValorar = $bdact->puedeValorar($id, $reserva["id_servicio"]);
                    $yaValorado = $bdact->yaHaValorado($id, $reserva["id_servicio"]);

                    if ($puedeValorar && !$yaValorado) {
                ?>
                      <a href="valorar-actividad.php?idreserva=<?= $reserva["id_reserva"] ?>" class="btn btn-secondary">
                        Valorar actividad
                      </a>
                <?php
                    } elseif ($yaValorado) {
                ?>
                      <span class="reservation-reviewed">
                        Ya has valorado esta actividad ⭐
                      </span>
                <?php
                    }
                  }
                ?>

                <?php if($puedeGestionar): ?>

                  <a href="modificar-reserva.php?idreserva=<?= $reserva["id_reserva"] ?>" class="btn btn-secondary">
                    Modificar
                  </a>

                  <a 
                    href="cancelar-reserva.php?idreserva=<?=$reserva['id_reserva']?>" 
                    class="btn btn-primary" 
                    onclick="return confirm('¿Seguro que quieres cancelar esta reserva?');"
                  >
                    Cancelar
                  </a>

                <?php elseif($menos24h && $reserva['estado'] == 'confirmada'): ?>

                  <p class="reservation-warning">
                    Esta reserva ya no puede modificarse ni cancelarse porque quedan menos de 24 horas.
                  </p>

                <?php endif; ?>

              </div>  

              </div>
            </article>

          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-reservations">
            <p>No tienes actividades reservadas.</p>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </section>
</main>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const botones = document.querySelectorAll(".filter-chip");
    const tarjetas = document.querySelectorAll(".reservation-card");

    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    const finSemana = new Date(hoy);
    finSemana.setDate(hoy.getDate() + 7);

    function aplicarFiltro(filtro) {
      tarjetas.forEach(tarjeta => {
        const fechaTexto = tarjeta.dataset.fecha; // formato YYYY-MM-DD
        const fechaReserva = new Date(fechaTexto + "T00:00:00");

        let mostrar = false;

        if (filtro === "proximas") {
          mostrar = fechaReserva >= hoy;
        } 
        else if (filtro === "hoy") {
          mostrar = fechaReserva.getTime() === hoy.getTime();
        } 
        else if (filtro === "semana") {
          mostrar = fechaReserva >= hoy && fechaReserva <= finSemana;
        }else if (filtro === "pasadas") {
          mostrar = fechaReserva < hoy;
        }

        tarjeta.style.display = mostrar ? "grid" : "none";
      });
    }

    botones.forEach(boton => {
      boton.addEventListener("click", function () {
        botones.forEach(b => b.classList.remove("active"));
        this.classList.add("active");

        let filtro = this.dataset.filter;
        aplicarFiltro(filtro);
      });
    });

    aplicarFiltro("proximas");
  });
  </script>

<?php require_once("footer.php"); ?>
</body>
</html>