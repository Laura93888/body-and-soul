<?php

$paginaActiva = "empresas-aprobadas";
$tituloPagina = "Empresas aprobadas";
$etiquetaPagina = "Empresas activas";
$cssExtra = [
    "../css/admin-styles/empresas-aprobadas.css"
];

require_once("head-admin.php");

if(isset($_GET["suspender"])){
    $idEmpresa = (int) $_GET["suspender"];
    $bdadmin->SuspenderEmpresa($idEmpresa);

    header("Location: empresas-aprobadas.php");
    exit();
}

if(isset($_GET["activar"])){
    $idEmpresa = (int) $_GET["activar"];
    $bdadmin->ActivarEmpresa($idEmpresa);

    header("Location: empresas-aprobadas.php");
    exit();
}

$empresas = $bdadmin->ObtenerEmpresasAprobadas();
$totalEmpresas = count($empresas);

$categorias = $bdadmin->ObtenerCategoriasFiltroAdmin();
$estados = $bdadmin->ObtenerEstadosEmpresas();

$categoriaSeleccionada = $_GET["categoria"] ?? "";
$estadoSeleccionado = $_GET["estado"] ?? "";

?>

<main class="admin-content">

  <section class="approved-header-card">
    <div>
      <span class="admin-section-tag">Gestión</span>
      <h3>Empresas activas en la plataforma</h3>
      <p>
        Consulta las empresas aprobadas, revisa sus actividades y gestiona su estado dentro del sistema.
      </p>
    </div>

    <div class="approved-summary">
      <span class="approved-summary-number"><?= $totalEmpresas ?></span>
      <span class="approved-summary-label">Aprobadas</span>
    </div>
  </section>

  <section class="approved-filters-card">
    <div class="approved-search">
      <label for="buscar-aprobada" class="sr-only">Buscar empresa</label>
      <input type="text" id="buscar-aprobada" placeholder="Buscar empresa, ciudad o email">
    </div>

    <div class="approved-filters">
      <select id="filtro-categoria-empresa" name="categoria">

          <option value="">Todas las categorías</option>

          <?php foreach($categorias as $categoria){ ?>

              <option value="<?= htmlspecialchars($categoria["nombre"]) ?>"
                  <?= ($categoriaSeleccionada == $categoria["nombre"]) ? "selected" : "" ?>>

                  <?= htmlspecialchars($categoria["nombre"]) ?>

              </option>

          <?php } ?>

      </select>

      <select id="filtro-estado-empresa" name="estado">

          <option value="">Todos los estados</option>

          <?php foreach($estados as $estado){ ?>

              <option value="<?= htmlspecialchars($estado["estado"]) ?>"
                  <?= ($estadoSeleccionado == $estado["estado"]) ? "selected" : "" ?>>

                  <?= ucfirst(htmlspecialchars($estado["estado"])) ?>

              </option>

          <?php } ?>

      </select>
    </div>
  </section>

  <section class="approved-list" id="lista-empresas-aprobadas">

    <?php if(count($empresas) > 0): ?>

      <?php foreach($empresas as $empresa): ?>
        <?php
        $estado = $empresa["estado"] ?? "activa";

        if($estado == "activa"){
            $claseEstado = "approved";
            $textoEstado = "Activa";
        }else{
            $claseEstado = "blocked";
            $textoEstado = "Suspendida";
        }
        ?>
        <article class="approved-company-card">
          <div class="approved-company-main">
            <div class="approved-company-header">

              <img 
                src="<?= htmlspecialchars($empresa["logo_empresa"] ?? "../assets/placeholder.jpg") ?>" 
                alt="<?= htmlspecialchars($empresa["nombre_empresa"]) ?>" 
                class="approved-company-logo"
              >

              <div class="approved-company-title-block">
                <p class="approved-company-category">
                  <?= ucfirst(htmlspecialchars($empresa["categoria_empresa"] ?? "Sin categoría")) ?>
                </p>

                <h3><?= htmlspecialchars($empresa["nombre_empresa"]) ?></h3>

                <p class="approved-company-location">
                  <?= htmlspecialchars($empresa["ciudad_empresa"] ?? "Sin ciudad") ?>
                </p>
              </div>

              <span class="admin-status-chip <?= $claseEstado ?>">
                  <?= $textoEstado ?>
              </span>
            </div>

            <p class="approved-company-description">
              <?= nl2br(htmlspecialchars($empresa["descripcion_empresa"] ?? "")) ?>
            </p>

            <div class="approved-company-grid">
              <div class="approved-info-item">
                <span class="info-label">Actividades</span>
                <span class="info-value"><?= $empresa["total_servicios"] ?></span>
              </div>

              <div class="approved-info-item">
                <span class="info-label">Ciudad</span>
                <span class="info-value">
                  <?= htmlspecialchars($empresa["ciudad_empresa"] ?? "Sin ciudad") ?>
                </span>
              </div>

              <div class="approved-info-item">
                <span class="info-label">Email</span>
                <span class="info-value">
                  <?= htmlspecialchars($empresa["email"]) ?>
                </span>
              </div>

              <div class="approved-info-item">
                <span class="info-label">Teléfono</span>
                <span class="info-value">
                  <?= htmlspecialchars($empresa["telefono"] ?? "No indicado") ?>
                </span>
              </div>
            </div>
          </div>

          <div class="approved-company-actions">
            <?php if($estado == "activa"): ?>
    <button type="button" class="btn-warning"
        onclick="abrirModalSuspender(<?= $empresa['id_empresa'] ?>)">
        Suspender
    </button>
<?php else: ?>
    <button type="button" class="btn-approve"
        onclick="abrirModalActivarEmpresa(<?= $empresa['id_empresa'] ?>)">
        Reactivar
    </button>
<?php endif; ?>
            <a href="detalle-empresa-aprobada.php?id=<?= $empresa["id_empresa"] ?>" class="btn-detail">
              Ver empresa
            </a>

            <a href="servicios-empresa.php?id=<?= $empresa["id_empresa"] ?>" class="btn-secondary-admin">
              Ver actividades
            </a>
          </div>
        </article>

      <?php endforeach; ?>

    <?php else: ?>

      <article class="approved-company-card">
        <div class="approved-company-main">
          <h3>No hay empresas aprobadas</h3>
          <p class="approved-company-description">
            Todavía no hay empresas activas en la plataforma.
          </p>
        </div>
      </article>

    <?php endif; ?>

  </section>

  <!-- Modal suspender -->
<div id="modalSuspender" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:12px; padding:2rem; max-width:420px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
    <h3 style="margin-top:0;">Suspender empresa</h3>
    <p>¿Seguro que deseas suspender esta empresa?</p>
    <p style="color:#c0392b; font-weight:500;">⚠️ También se cancelarán todas sus actividades.</p>
    <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1.5rem;">
      <button onclick="cerrarModalSuspender()"
              style="padding:0.6rem 1.2rem; border-radius:8px; border:1px solid #ccc; background:#f5f5f5; cursor:pointer;">
        Volver
      </button>
      <button onclick="confirmarSuspender()"
              style="padding:0.6rem 1.2rem; border-radius:8px; border:none; background:#e74c3c; color:#fff; cursor:pointer; font-weight:600;">
        Sí, suspender
      </button>
    </div>
  </div>
</div>

<!-- Modal reactivar empresa -->
<div id="modalActivarEmpresa" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:12px; padding:2rem; max-width:420px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
    <h3 style="margin-top:0;">Reactivar empresa</h3>
    <p>¿Seguro que deseas reactivar esta empresa?</p>
    <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1.5rem;">
      <button onclick="cerrarModalActivarEmpresa()"
              style="padding:0.6rem 1.2rem; border-radius:8px; border:1px solid #ccc; background:#f5f5f5; cursor:pointer;">
        Volver
      </button>
      <button onclick="confirmarActivarEmpresa()"
              style="padding:0.6rem 1.2rem; border-radius:8px; border:none; background:#27ae60; color:#fff; cursor:pointer; font-weight:600;">
        Sí, reactivar
      </button>
    </div>
  </div>
</div>

</main>

</div>
</div>

<script>
  const buscadorEmpresa = document.getElementById("buscar-aprobada");
  const filtroCategoriaEmpresa = document.getElementById("filtro-categoria-empresa");
  const filtroEstadoEmpresa = document.getElementById("filtro-estado-empresa");
  const listaEmpresasAprobadas = document.getElementById("lista-empresas-aprobadas");

  let temporizadorEmpresa = null;

  function cargarEmpresasAprobadas(){
      const buscar = buscadorEmpresa.value;
      const categoria = filtroCategoriaEmpresa.value;
      const estado = filtroEstadoEmpresa.value;

      const url = "ajax-empresas-aprobadas.php?buscar=" + encodeURIComponent(buscar) +
                  "&categoria=" + encodeURIComponent(categoria) +
                  "&estado=" + encodeURIComponent(estado);

      fetch(url)
          .then(response => response.text())
          .then(data => {
              listaEmpresasAprobadas.innerHTML = data;
          });
  }

  buscadorEmpresa.addEventListener("input", function(){
      clearTimeout(temporizadorEmpresa);

      temporizadorEmpresa = setTimeout(function(){
          cargarEmpresasAprobadas();
      }, 300);
  });

  filtroCategoriaEmpresa.addEventListener("change", cargarEmpresasAprobadas);
  filtroEstadoEmpresa.addEventListener("change", cargarEmpresasAprobadas);

  let idEmpresaActiva = null;

function abrirModalSuspender(id) {
    idEmpresaActiva = id;
    document.getElementById("modalSuspender").style.display = "flex";
}
function cerrarModalSuspender() {
    idEmpresaActiva = null;
    document.getElementById("modalSuspender").style.display = "none";
}
function confirmarSuspender() {
    if(idEmpresaActiva) window.location.href = "empresas-aprobadas.php?suspender=" + idEmpresaActiva;
}

function abrirModalActivarEmpresa(id) {
    idEmpresaActiva = id;
    document.getElementById("modalActivarEmpresa").style.display = "flex";
}
function cerrarModalActivarEmpresa() {
    idEmpresaActiva = null;
    document.getElementById("modalActivarEmpresa").style.display = "none";
}
function confirmarActivarEmpresa() {
    if(idEmpresaActiva) window.location.href = "empresas-aprobadas.php?activar=" + idEmpresaActiva;
}

</script>

</body>
</html>