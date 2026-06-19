<?php
require_once("head.php");

$idEmpresa = $_SESSION["empresa"];

$reservas = $bdempre->ObtenerReservasEmpresa($idempresa);
$numreservas = count($reservas);

$servicios = $bdempre->ObtenerServiciosEmpresa($idEmpresa);

?>

    <div class="company-main">

      <header class="company-topbar">
        <div class="company-topbar-left">
          <span class="company-page-tag">Gestión de reservas</span>
          <h2>Reservas recibidas</h2>
        </div>

        <div class="company-topbar-right">
          <a href="mis-servicios.php" class="company-top-link">Ver mis servicios</a>
        </div>
      </header>
 
      <main class="company-content">

        <section class="reservations-header-card">
          <div>
            <span class="company-section-badge">Resumen</span>
            <h3>Controla las reservas de tus actividades</h3>
            <p>
              Consulta quién ha reservado, en qué fecha, para qué servicio y en qué estado se encuentra cada reserva.
            </p>
          </div>

          <div class="reservations-summary">
            <span class="reservations-summary-number"><?=$numreservas?></span>
            <span class="reservations-summary-label">Reservas recibidas</span>
          </div>
        </section>

        <section class="reservations-filters-card">

          <div class="reservations-filters">
            <select id="filtroServicio">
              <option value="">Todos los servicios</option>
              <?php foreach($servicios as $ser){ ?>
            <option value="<?=$ser["id_servicio"]?>">
              <?=htmlspecialchars($ser["nombre_servicio"])?>
            </option>
          <?php } ?>
            </select>

          </div>
        </section>

  <section class="reservations-list">

  <?php if(empty($reservas)){ ?>

    <p>No tienes reservas recibidas todavía.</p>

  <?php }else{ ?>

    <?php foreach($reservas as $reserva){ 
      $fecha = date("d/m/Y", strtotime($reserva["fecha_hora"]));
      $hora = date("H:i", strtotime($reserva["fecha_hora"]));
    ?>

      <article class="reservation-company-card" data-servicio="<?=$reserva["id_servicio"]?>">
        <div class="reservation-company-main">
          <div class="reservation-company-top">
            <div>
              <p class="reservation-company-category">
                <?=htmlspecialchars($reserva["categoria_padre"])?> · <?=htmlspecialchars($reserva["subcategoria"])?>
              </p>

              <h3><?=htmlspecialchars($reserva["nombre_servicio"])?></h3>

              <p class="reservation-company-user">
                Reserva realizada por 
                <strong>
                  <?=htmlspecialchars($reserva["nombre_usuario"] . " " . $reserva["apellido_usuario"])?>
                </strong>
              </p>
            </div>
          </div>

          <p class="reservation-company-description">
            <?=htmlspecialchars($reserva["descripcion"])?>
          </p>

          <div class="reservation-company-grid">
            <div class="reservation-info-item">
              <span class="info-label">Fecha</span>
              <span class="info-value"><?=$fecha?></span>
            </div>

            <div class="reservation-info-item">
              <span class="info-label">Hora</span>
              <span class="info-value"><?=$hora?></span>
            </div>

           <div class="reservation-info-item">
                <span class="info-label">Estado</span>
                <span class="info-value"><?=ucfirst($reserva["estado"])?></span>
            </div>

            <div class="reservation-info-item">
              <span class="info-label">Correo usuario</span>
              <span class="info-value">
                <?=htmlspecialchars($reserva["email_usuario"])?>
              </span>
            </div>
          </div>
        </div>

        <div class="reservation-company-actions">
          <a href="mailto:<?=htmlspecialchars($reserva["email_usuario"])?>" class="btn-secondary-company">
            Contactar
          </a>

        </div>
      </article>

    <?php } ?>

  <?php } ?>

    <!-- MENSAJE PARA FILTROS VACIOS -->
  <p id="mensajeVacio" style="display:none;">
    No hay reservas para este servicio.
  </p>

</section>

      </main>
    </div>
  </div>

</body>
</html>