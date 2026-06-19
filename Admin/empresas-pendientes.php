<?php

$paginaActiva = "empresas-pendientes";
$tituloPagina = "Empresas pendientes";
$etiquetaPagina = "Gestión de empresas";
$cssExtra = [
    "../css/admin-styles/empresas-pendientes.css"
];

require_once("head-admin.php");

$solicitudes = $bdadmin->ObtenerSolicitudesPendientes();
$totalPendientes = count($solicitudes);

$categorias = $bdadmin->ObtenerCategoriasFiltroAdmin();
$categoriaSeleccionada = $_GET["categoria"] ?? "";

$ciudades = $bdadmin->ObtenerCiudadesEmpresasPendientes();
$ciudadSeleccionada = $_GET["ciudad"] ?? "";

?>

<main class="admin-content">

  <section class="pending-header-card">
    <div>
      <span class="admin-section-tag">Validación</span>
      <h3>Solicitudes recibidas</h3>
      <p>
        Revisa las empresas registradas y decide si cumplen los requisitos para formar parte de la plataforma.
      </p>
    </div>

    <div class="pending-summary">
      <span class="pending-summary-number"><?= $totalPendientes ?></span>
      <span class="pending-summary-label">Pendientes</span>
    </div>
  </section>

  <section class="pending-filters-card">
    <div class="pending-search">
      <label for="buscar-empresa-pendiente" class="sr-only">Buscar empresa</label>
      <input type="text" id="buscar-empresa-pendiente" placeholder="Buscar empresa, ciudad o email">
    </div>

    <div class="pending-filters">
      <select id="filtro-categoria-pendiente">
        <option value="">Todas las categorías</option>
        <?php foreach($categorias as $cat){ ?>
            <option value="<?= $cat["id_categoria"] ?>"
                <?= ($categoriaSeleccionada == $cat["id_categoria"]) ? "selected" : "" ?>>
                <?= htmlspecialchars($cat["nombre"]) ?>
            </option>
        <?php } ?>
      </select>

      <select id="filtro-ciudad-pendiente">
        <option value="">Todas las ciudades</option>
        <?php foreach($ciudades as $ciudad){ ?>
            <option value="<?= htmlspecialchars($ciudad["ciudad_empresa"]) ?>"
                <?= ($ciudadSeleccionada == $ciudad["ciudad_empresa"]) ? "selected" : "" ?>>
                <?= htmlspecialchars($ciudad["ciudad_empresa"]) ?>
            </option>
        <?php } ?>
      </select>
    </div>
  </section>

  <section class="pending-list" id="lista-empresas-pendientes">

    <?php if(count($solicitudes) > 0): ?>

      <?php foreach($solicitudes as $solicitud): ?>

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

<button type="button" class="btn-approve"
    onclick="abrirModalAprobar(<?= $solicitud['id_solicitud'] ?>)">
    Aprobar
</button>

<button type="button" class="btn-reject"
    onclick="abrirModalRechazar(<?= $solicitud['id_solicitud'] ?>)">
    Rechazar
</button>

          </div>

        </article>

      <?php endforeach; ?>

    <?php else: ?>

      <article class="pending-company-card">
        <div class="pending-company-main">
          <h3>No hay empresas pendientes</h3>

          <p class="pending-company-description">
            Actualmente no existen solicitudes de empresa pendientes de revisión.
          </p>
        </div>
      </article>

    <?php endif; ?>

  </section>

  <!-- Modal aprobar -->
<div id="modalAprobar" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:12px; padding:2rem; max-width:420px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
    <h3 style="margin-top:0;">Aprobar empresa</h3>
    <p>¿Seguro que deseas aprobar esta empresa?</p>
    <p style="color:#27ae60; font-weight:500;">✅ La empresa podrá acceder a la plataforma y publicar actividades.</p>
    <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1.5rem;">
      <button onclick="cerrarModalAprobar()"
              style="padding:0.6rem 1.2rem; border-radius:8px; border:1px solid #ccc; background:#f5f5f5; cursor:pointer;">
        Volver
      </button>
      <button onclick="confirmarAprobar()"
              style="padding:0.6rem 1.2rem; border-radius:8px; border:none; background:#27ae60; color:#fff; cursor:pointer; font-weight:600;">
        Sí, aprobar
      </button>
    </div>
  </div>
</div>

<!-- Modal rechazar -->
<div id="modalRechazar" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:12px; padding:2rem; max-width:420px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
    <h3 style="margin-top:0;">Rechazar empresa</h3>
    <p>¿Seguro que deseas rechazar esta solicitud?</p>
    <p style="color:#c0392b; font-weight:500;">⚠️ La empresa no podrá acceder a la plataforma.</p>
    <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1.5rem;">
      <button onclick="cerrarModalRechazar()"
              style="padding:0.6rem 1.2rem; border-radius:8px; border:1px solid #ccc; background:#f5f5f5; cursor:pointer;">
        Volver
      </button>
      <button onclick="confirmarRechazar()"
              style="padding:0.6rem 1.2rem; border-radius:8px; border:none; background:#e74c3c; color:#fff; cursor:pointer; font-weight:600;">
        Sí, rechazar
      </button>
    </div>
  </div>
</div>

</main>

</div>
</div>

<script>
  const buscadorPendiente = document.getElementById("buscar-empresa-pendiente");
  const filtroCategoriaPendiente = document.getElementById("filtro-categoria-pendiente");
  const filtroCiudadPendiente = document.getElementById("filtro-ciudad-pendiente");
  const listaPendientes = document.getElementById("lista-empresas-pendientes");

  let temporizadorPendiente = null;

  function cargarEmpresasPendientes(){
      const buscar = buscadorPendiente.value;
      const categoria = filtroCategoriaPendiente.value;
      const ciudad = filtroCiudadPendiente.value;

      const url = "ajax-empresas-pendientes.php?buscar=" + encodeURIComponent(buscar) +
                  "&categoria=" + encodeURIComponent(categoria) +
                  "&ciudad=" + encodeURIComponent(ciudad);

      fetch(url)
          .then(response => response.text())
          .then(data => {
              listaPendientes.innerHTML = data;
          });
  }

  buscadorPendiente.addEventListener("input", function(){
      clearTimeout(temporizadorPendiente);

      temporizadorPendiente = setTimeout(function(){
          cargarEmpresasPendientes();
      }, 300);
  });

  filtroCategoriaPendiente.addEventListener("change", cargarEmpresasPendientes);
  filtroCiudadPendiente.addEventListener("change", cargarEmpresasPendientes);

  let idSolicitudActiva = null;

function abrirModalAprobar(id) {
    idSolicitudActiva = id;
    document.getElementById("modalAprobar").style.display = "flex";
}
function cerrarModalAprobar() {
    idSolicitudActiva = null;
    document.getElementById("modalAprobar").style.display = "none";
}
function confirmarAprobar() {
    if(idSolicitudActiva) window.location.href = "detalle-empresa.php?id=" + idSolicitudActiva + "&aprobar=" + idSolicitudActiva;
}

function abrirModalRechazar(id) {
    idSolicitudActiva = id;
    document.getElementById("modalRechazar").style.display = "flex";
}
function cerrarModalRechazar() {
    idSolicitudActiva = null;
    document.getElementById("modalRechazar").style.display = "none";
}
function confirmarRechazar() {
    if(idSolicitudActiva) window.location.href = "detalle-empresa.php?id=" + idSolicitudActiva + "&rechazar=" + idSolicitudActiva;
}

</script>

</body>
</html>