<?php
$paginaActiva = "reportes";
$tituloPagina = "Reportes";
$etiquetaPagina = "Análisis de la plataforma";
$cssExtra = [
    "../css/admin-styles/reportes.css"
];

require_once("head-admin.php");

$resumen = $bdadmin->ObtenerResumenReportes();
$actividades = $bdadmin->ObtenerActividadesMasReservadas();
$empresas = $bdadmin->ObtenerEmpresasConMasReservas();
$usuarios = $bdadmin->ObtenerUsuariosMasActivos();
$categorias = $bdadmin->ObtenerCategoriasMasPopulares();
?>

<main class="admin-content">

  <section class="reports-header-card">
    <div>
      <span class="admin-section-tag">Estadísticas</span>
      <h3>Resumen general de la plataforma</h3>
      <p>
        Consulta los principales indicadores de uso de Body and Soul: reservas, usuarios,
        empresas, actividades y categorías con mayor actividad.
      </p>
    </div>
  </section>

  <section class="reports-stats-grid">

    <article class="report-stat-card">
      <p>Total reservas</p>
      <h3><?= $resumen["total_reservas"] ?></h3>
      <span><?= $resumen["reservas_confirmadas"] ?> confirmadas</span>
    </article>

    <article class="report-stat-card">
      <p>Reservas canceladas</p>
      <h3><?= $resumen["reservas_canceladas"] ?></h3>
      <span>Control de incidencias</span>
    </article>

    <article class="report-stat-card">
      <p>Usuarios activos</p>
      <h3><?= $resumen["usuarios_activos"] ?></h3>
      <span>Cuentas habilitadas</span>
    </article>

    <article class="report-stat-card">
      <p>Empresas registradas</p>
      <h3><?= $resumen["empresas_total"] ?></h3>
      <span>Empresas aprobadas</span>
    </article>

    <article class="report-stat-card">
      <p>Actividades activas</p>
      <h3><?= $resumen["actividades_activas"] ?></h3>
      <span>Servicios disponibles</span>
    </article>

  </section>

  <section class="reports-grid">

    <article class="report-panel-card">
      <div class="report-panel-header">
        <span class="admin-section-tag">Top actividades</span>
        <h3>Actividades más reservadas</h3>
      </div>

      <div class="report-table">
        <div class="report-table-row report-table-head">
          <span>Actividad</span>
          <span>Empresa</span>
          <span>Reservas</span>
        </div>

        <?php foreach($actividades as $actividad): ?>
          <div class="report-table-row">
            <span><?= htmlspecialchars($actividad["nombre_servicio"]) ?></span>
            <span><?= htmlspecialchars($actividad["nombre_empresa"]) ?></span>
            <strong><?= $actividad["total_reservas"] ?></strong>
          </div>
        <?php endforeach; ?>
      </div>
    </article>

    <article class="report-panel-card">
      <div class="report-panel-header">
        <span class="admin-section-tag">Top empresas</span>
        <h3>Empresas con más reservas</h3>
      </div>

      <div class="report-table">
        <div class="report-table-row report-table-head">
          <span>Empresa</span>
          <span></span>
          <span>Reservas</span>
        </div>

        <?php foreach($empresas as $empresa): ?>
          <div class="report-table-row">
            <span><?= htmlspecialchars($empresa["nombre_empresa"]) ?></span>
            <span></span>
            <strong><?= $empresa["total_reservas"] ?></strong>
          </div>
        <?php endforeach; ?>
      </div>
    </article>

    <article class="report-panel-card">
      <div class="report-panel-header">
        <span class="admin-section-tag">Top usuarios</span>
        <h3>Usuarios más activos</h3>
      </div>

      <div class="report-table">
        <div class="report-table-row report-table-head">
          <span>Usuario</span>
          <span>Email</span>
          <span>Reservas</span>
        </div>

        <?php foreach($usuarios as $usuario): ?>
          <div class="report-table-row">
            <span><?= htmlspecialchars($usuario["nombre"] . " " . $usuario["apellido"]) ?></span>
            <span><?= htmlspecialchars($usuario["email"]) ?></span>
            <strong><?= $usuario["total_reservas"] ?></strong>
          </div>
        <?php endforeach; ?>
      </div>
    </article>

    <article class="report-panel-card">
      <div class="report-panel-header">
        <span class="admin-section-tag">Categorías</span>
        <h3>Categorías más populares</h3>
      </div>

      <div class="report-table">
        <div class="report-table-row report-table-head">
          <span>Categoría</span>
          <span></span>
          <span>Reservas</span>
        </div>

        <?php foreach($categorias as $categoria): ?>
          <div class="report-table-row">
            <span><?= htmlspecialchars($categoria["categoria"]) ?></span>
            <span></span>
            <strong><?= $categoria["total_reservas"] ?></strong>
          </div>
        <?php endforeach; ?>
      </div>
    </article>

  </section>

</main>

</div>
</div>

</body>
</html>