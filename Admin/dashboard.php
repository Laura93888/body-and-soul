<?php
$paginaActiva = "dashboard";
$tituloPagina = "Dashboard";
$etiquetaPagina = "Panel de administración";

require_once("head-admin.php");

$datos = $bdadmin->ObtenerDatosDashboard();
$solicitudes = $bdadmin->ObtenerUltimasSolicitudes();
$actividades = $bdadmin->ObtenerUltimasActividades();
?>

<main class="admin-content">

  <section class="admin-stats-grid">
    <article class="admin-stat-card">
      <p class="admin-stat-label">Empresas pendientes</p>
      <h3><?= $datos["pendientes"] ?></h3>
      <span class="admin-stat-detail"><?= $datos["pendientes_hoy"] ?> nuevas hoy</span>
    </article>

    <article class="admin-stat-card">
      <p class="admin-stat-label">Empresas aprobadas</p>
      <h3><?= $datos["empresas"] ?></h3>
      <span class="admin-stat-detail">+<?= $datos["empresas_mes"] ?> este mes</span>
    </article>

    <article class="admin-stat-card">
      <p class="admin-stat-label">Actividades publicadas</p>
      <h3><?= $datos["actividades"] ?></h3>
      <span class="admin-stat-detail"><?= $datos["actividades_canceladas"] ?> suspendidas</span>
    </article>

    <article class="admin-stat-card">
      <p class="admin-stat-label">Usuarios registrados</p>
      <h3><?= $datos["usuarios"] ?></h3>
      <span class="admin-stat-detail"><?= $datos["usuarios_semana"] ?> nuevos esta semana</span>
    </article>
  </section>

  <section class="admin-panels-grid">

    <article class="admin-panel-card">
      <div class="admin-panel-header">
        <div>
          <span class="admin-section-tag">Revisión</span>
          <h3>Empresas pendientes</h3>
        </div>
        <a href="empresas-pendientes.php" class="admin-panel-link">Ver todas</a>
      </div>

      <div class="admin-list">
        <?php if(count($solicitudes) > 0): ?>
          <?php foreach($solicitudes as $solicitud): ?>
            <div class="admin-list-item">
              <div class="admin-list-info">
                <h4><?= htmlspecialchars($solicitud["nombre"]) ?></h4>
                <p>
                  Registro enviado el <?= date("d/m/Y", strtotime($solicitud["fecha"])) ?> ·
                  <?= htmlspecialchars($solicitud["ciudad_empresa"] ?? "Sin ciudad") ?>
                </p>
              </div>

              <div class="admin-list-actions">
                <a href="detalle-empresa.php?id=<?= $solicitud["id_solicitud"] ?>" class="btn-approve">
                  Revisar
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="admin-list-item">
            <div class="admin-list-info">
              <h4>No hay solicitudes pendientes</h4>
              <p>Actualmente no hay empresas esperando revisión.</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </article>

    <article class="admin-panel-card">
      <div class="admin-panel-header">
        <div>
          <span class="admin-section-tag">Control</span>
          <h3>Actividades recientes</h3>
        </div>
        <a href="actividades.php" class="admin-panel-link">Gestionar</a>
      </div>

      <div class="admin-list">
        <?php if(count($actividades) > 0): ?>
          <?php foreach($actividades as $actividad): ?>
            <div class="admin-list-item">
              <div class="admin-list-info">
                <h4><?= htmlspecialchars($actividad["nombre_servicio"]) ?></h4>
                <p>
                  Subida por <?= htmlspecialchars($actividad["nombre_empresa"]) ?> ·
                  <?= htmlspecialchars($actividad["categoria_padre"] ?? $actividad["subcategoria"] ?? "Sin categoría") ?>
                </p>
              </div>

              <span class="admin-status-chip approved">
                <?= ucfirst(htmlspecialchars($actividad["estado"] ?? "activo")) ?>
              </span>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="admin-list-item">
            <div class="admin-list-info">
              <h4>No hay actividades recientes</h4>
              <p>Todavía no hay servicios registrados.</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </article>

    <article class="admin-panel-card">
      <div class="admin-panel-header">
        <div>
          <span class="admin-section-tag">Acciones</span>
          <h3>Accesos rápidos</h3>
        </div>
      </div>

      <div class="admin-quick-actions">
        <a href="empresas-pendientes.php" class="admin-quick-card">Revisar empresas</a>
        <a href="actividades.php" class="admin-quick-card">Gestionar actividades</a>
        <a href="usuarios.php" class="admin-quick-card">Ver usuarios</a>
        <a href="reportes.php" class="admin-quick-card">Consultar reportes</a>
        <a href="crear-categoria.php" class="admin-quick-card">Crear categoría</a>
      </div>
    </article>

    <article class="admin-panel-card">
      <div class="admin-panel-header">
        <div>
          <span class="admin-section-tag">Resumen</span>
          <h3>Actividad reciente</h3>
        </div>
      </div>

      <ul class="admin-timeline">
        <?php foreach($solicitudes as $solicitud): ?>
          <li>
            <span class="timeline-dot"></span>
            <p><strong><?= htmlspecialchars($solicitud["nombre"]) ?></strong> ha solicitado alta como empresa.</p>
          </li>
        <?php endforeach; ?>

        <?php foreach($actividades as $actividad): ?>
          <li>
            <span class="timeline-dot"></span>
            <p><strong><?= htmlspecialchars($actividad["nombre_empresa"]) ?></strong> ha publicado “<?= htmlspecialchars($actividad["nombre_servicio"]) ?>”.</p>
          </li>
        <?php endforeach; ?>
      </ul>
    </article>

  </section>
</main>

</div>
</div>

</body>
</html> 