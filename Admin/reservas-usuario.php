<?php

$paginaActiva = "usuarios";
$tituloPagina = "Reservas del usuario";
$etiquetaPagina = "Gestión de usuarios";
$cssExtra = [
    "../css/admin-styles/empresas-aprobadas.css",
    "../css/admin-styles/usuarios.css"
];

require_once("head-admin.php");

if(!isset($_GET["id"])){
    header("Location: usuarios.php");
    exit();
}

$idUsuario = (int) $_GET["id"];

$reservas = $bdadmin->ObtenerReservasUsuarioAdmin($idUsuario);

if(count($reservas) > 0){
    $usuario = $reservas[0];
}else{
    $usuario = null;
}
?>

<main class="admin-content">

  <section class="approved-header-card">
    <div>
      <span class="admin-section-tag">Reservas</span>

      <h3>
        <?php if($usuario): ?>
          <?= htmlspecialchars($usuario["nombre"] . " " . $usuario["apellido"]) ?>
        <?php else: ?>
          Usuario sin reservas
        <?php endif; ?>
      </h3>

      <p>
        <?php if($usuario): ?>
          <?= htmlspecialchars($usuario["email"]) ?>
        <?php else: ?>
          Este usuario todavía no ha realizado reservas.
        <?php endif; ?>
      </p>
    </div>

    <div class="approved-summary">
      <span class="approved-summary-number"><?= count($reservas) ?></span>
      <span class="approved-summary-label">Reservas</span>
    </div>
  </section>

  <section class="approved-list">

    <?php if(count($reservas) > 0): ?>

      <?php foreach($reservas as $reserva): ?>

        <?php
          $estado = $reserva["estado"] ?? "confirmada";

          if($estado == "confirmada"){
              $claseEstado = "approved";
              $textoEstado = "Confirmada";
          }else{
              $claseEstado = "blocked";
              $textoEstado = ucfirst($estado);
          }
        ?>

        <article class="approved-company-card">

          <div class="approved-company-main">

            <div class="approved-company-header reservas-header-admin">
              <div class="approved-company-title-block">
                <p class="approved-company-category">
                  <?= htmlspecialchars($reserva["lugar"]) ?>
                </p>

                <h3><?= htmlspecialchars($reserva["nombre_servicio"]) ?></h3>
              </div>

              <div class="reserva-card-right">
                <span class="admin-status-chip <?= $claseEstado ?>">
                  <?= $textoEstado ?>
                </span>

                <a href="../publico/actividad.php?idact=<?= $reserva["id_servicio"] ?>" class="btn-detail">
                  Ver actividad
                </a>
              </div>
            </div>

            <div class="approved-company-grid">

              <div class="approved-info-item">
                <span class="info-label">Fecha reserva</span>
                <span class="info-value">
                  <?= date("d/m/Y H:i", strtotime($reserva["fecha_hora"])) ?>
                </span>
              </div>

              <div class="approved-info-item">
                <span class="info-label">Hora inicio</span>
                <span class="info-value">
                  <?= substr($reserva["hora_inicio"], 0, 5) ?>
                </span>
              </div>

              <div class="approved-info-item">
                <span class="info-label">Hora fin</span>
                <span class="info-value">
                  <?= substr($reserva["hora_fin"], 0, 5) ?>
                </span>
              </div>

              <div class="approved-info-item">
                <span class="info-label">Estado</span>
                <span class="info-value">
                  <?= ucfirst($estado) ?>
                </span>
              </div>

            </div>

          </div>

        </article>

      <?php endforeach; ?>

    <?php else: ?>

      <article class="approved-company-card">
        <div class="approved-company-main">

          <h3>No existen reservas</h3>

          <p class="approved-company-description">
            Este usuario todavía no ha realizado ninguna reserva en la plataforma.
          </p>

        </div>
      </article>

    <?php endif; ?>

  </section>

</main>

</div>
</div>

</body>
</html>