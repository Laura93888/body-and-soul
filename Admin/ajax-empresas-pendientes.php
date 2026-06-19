<?php
require_once("../bd/bdadmin.php");

$bdadmin = new bdadmin("localhost", 3306, "plataforma_servicios1", "root", "");

$buscar = trim($_GET["buscar"] ?? "");
$categoria = trim($_GET["categoria"] ?? "");
$ciudad = trim($_GET["ciudad"] ?? "");

$solicitudes = $bdadmin->ObtenerSolicitudesPendientes($buscar, $categoria, $ciudad);

if(count($solicitudes) > 0){

    foreach($solicitudes as $solicitud){
        ?>

        <article class="pending-company-card">

          <div class="pending-company-main">

            <div class="pending-company-top">
              <div>
                <p class="pending-company-category">
                  <?= ucfirst(htmlspecialchars($solicitud["categoria_empresa"] ?? "Sin categoría")) ?>
                </p>

                <h3>
                  <?= htmlspecialchars($solicitud["nombre"]) ?>
                </h3>
              </div>

              <span class="admin-status-chip pending">
                Pendiente
              </span>
            </div>

            <p class="pending-company-description">
              <?= nl2br(htmlspecialchars($solicitud["datos"] ?? "")) ?>
            </p>

            <div class="pending-company-grid">

              <div class="pending-info-item">
                <span class="info-label">Fecha de solicitud</span>
                <span class="info-value">
                  <?= date("d/m/Y", strtotime($solicitud["fecha"])) ?>
                </span>
              </div>

              <div class="pending-info-item">
                <span class="info-label">Ubicación</span>
                <span class="info-value">
                  <?= htmlspecialchars($solicitud["ciudad_empresa"] ?? "Sin ciudad") ?>
                </span>
              </div>

              <div class="pending-info-item">
                <span class="info-label">Email</span>
                <span class="info-value">
                  <?= htmlspecialchars($solicitud["email"]) ?>
                </span>
              </div>

              <div class="pending-info-item">
                <span class="info-label">Teléfono</span>
                <span class="info-value">
                  <?= htmlspecialchars($solicitud["telefono"] ?? "No indicado") ?>
                </span>
              </div>

            </div>
          </div>

          <div class="pending-company-actions">

            <a href="detalle-empresa.php?id=<?= $solicitud["id_solicitud"] ?>" class="btn-detail">
              Ver detalle
            </a>

            <a href="detalle-empresa.php?id=<?= $solicitud["id_solicitud"] ?>&aprobar=<?= $solicitud["id_solicitud"] ?>" 
               class="btn-approve"
               onclick="return confirm('¿Aprobar esta empresa?');">
              Aprobar
            </a>

            <a href="detalle-empresa.php?id=<?= $solicitud["id_solicitud"] ?>&rechazar=<?= $solicitud["id_solicitud"] ?>" 
               class="btn-reject"
               onclick="return confirm('¿Rechazar esta empresa?');">
              Rechazar
            </a>

          </div>

        </article>

        <?php
    }

}else{
    ?>

    <article class="pending-company-card">
      <div class="pending-company-main">
        <h3>No hay empresas pendientes</h3>
        <p class="pending-company-description">
          No se han encontrado solicitudes con los filtros seleccionados.
        </p>
      </div>
    </article>

    <?php
}
?>