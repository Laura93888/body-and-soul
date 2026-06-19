<?php
$paginaActiva = "actividades";
$tituloPagina = "Actividades";
$etiquetaPagina = "Control de contenido";
$cssExtra = [
    "../css/admin-styles/actividades.css",
    "../css/admin-styles/empresas-aprobadas.css"
];

require_once("head-admin.php");

if(isset($_GET["cancelar"])){
    $idServicio = (int) $_GET["cancelar"];
    $bdadmin->CancelarActividadAdmin($idServicio);

    header("Location: actividades.php");
    exit();
}

if(isset($_GET["activar"])){
    $idServicio = (int) $_GET["activar"];
    $bdadmin->ActivarActividadAdmin($idServicio);

    header("Location: actividades.php");
    exit();
}

$actividades = $bdadmin->ObtenerTodasActividades();
$totalActividades = count($actividades);
$categorias = $bdadmin->ObtenerCategoriasFiltroAdmin();

$estados = $bdadmin->ObtenerEstadosServicios();

$categoriaSeleccionada = $_GET["categoria"] ?? "";
$estadoSeleccionado = $_GET["estado"] ?? "";

?>

<main class="admin-content">

  <section class="activities-header-card">
    <div>
      <span class="admin-section-tag">Gestión</span>
      <h3>Actividades publicadas en la plataforma</h3>
      <p>
        Supervisa las actividades registradas por las empresas, revisa su estado y gestiona su visibilidad.
      </p>
    </div>

    <div class="activities-summary">
      <span class="activities-summary-number"><?= $totalActividades ?></span>
      <span class="activities-summary-label">Actividades</span>
    </div>
  </section>

  <section class="activities-filters-card">
    <div class="activities-search">
      <label for="buscar-actividad" class="sr-only">Buscar actividad</label>
      <input type="text" id="buscar-actividad" placeholder="Buscar actividad, empresa o ciudad">
    </div>

    <div class="activities-filters">
      <select id="filtro-categoria">
        <option value="">Todas las categorías</option>
        <?php foreach($categorias as $categoria){ ?>

            <option value="<?= htmlspecialchars($categoria["nombre"]) ?>"
                <?= ($categoriaSeleccionada == $categoria["nombre"]) ? "selected" : "" ?>>

                <?= htmlspecialchars($categoria["nombre"]) ?>

            </option>

        <?php } ?>
      </select>

      <select id="filtro-estado">
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

  <section class="approved-list" id="lista-actividades">

    <?php if(count($actividades) > 0): ?>

      <?php foreach($actividades as $actividad): ?>

        <?php
          $estado = $actividad["estado"] ?? "activo";

          if($estado == "activo"){
              $claseEstado = "approved";
              $textoEstado = "Activa";
          }else{
              $claseEstado = "blocked";
              $textoEstado = "Cancelada";
          }

          $imagen = $actividad["imagen"];

          if($imagen == "" || $imagen == null){
              $imagen = "../assets/placeholder.jpg";
          }else{
              $imagen = "../" . ltrim($imagen, "/");
          }
        ?>

        <article class="approved-company-card">
          <div class="approved-company-main">
            <div class="approved-company-header">

              <img 
                src="<?= htmlspecialchars($imagen) ?>" 
                alt="<?= htmlspecialchars($actividad["nombre_servicio"]) ?>" 
                class="approved-company-logo"
              >

              <div class="approved-company-title-block">
                <p class="approved-company-category">
                  <?= htmlspecialchars($actividad["categoria_padre"] ?? "") ?>
                  <?= $actividad["subcategoria"] ? " · " . htmlspecialchars($actividad["subcategoria"]) : "" ?>
                </p>

                <h3><?= htmlspecialchars($actividad["nombre_servicio"]) ?></h3>

                <p class="approved-company-location">
                  <?= htmlspecialchars($actividad["nombre_empresa"]) ?>
                </p>
              </div>

              <span class="admin-status-chip <?= $claseEstado ?>">
                <?= $textoEstado ?>
              </span>
            </div>

            <p class="approved-company-description">
              <?= nl2br(htmlspecialchars($actividad["descripcion"])) ?>
            </p>

            <div class="approved-company-grid">
              <div class="approved-info-item">
                <span class="info-label">Lugar</span>
                <span class="info-value"><?= htmlspecialchars($actividad["lugar"]) ?></span>
              </div>

              <div class="approved-info-item">
                <span class="info-label">Precio</span>
                <span class="info-value"><?= htmlspecialchars($actividad["precio"]) ?> €</span>
              </div>

              <div class="approved-info-item">
                <span class="info-label">Duración</span>
                <span class="info-value"><?= htmlspecialchars($actividad["duracion"]) ?></span>
              </div>

              <div class="approved-info-item">
                <span class="info-label">Reservas</span>
                <span class="info-value"><?= $actividad["total_reservas"] ?></span>
              </div>
            </div>
          </div>

          <div class="approved-company-actions">
            <a href="../publico/actividad.php?idact=<?= $actividad["id_servicio"] ?>" class="btn-detail">
              Ver actividad
            </a>

           <?php if($estado == "activo"): ?>
    <button type="button" class="btn-reject"
        onclick="abrirModalCancelarAdmin(<?= $actividad['id_servicio'] ?>)">
        Cancelar
    </button>
<?php else: ?>
    <button type="button" class="btn-approve"
        onclick="abrirModalReactivarAdmin(<?= $actividad['id_servicio'] ?>)">
        Reactivar
    </button>
<?php endif; ?>

          </div>
        </article>

      <?php endforeach; ?>

    <?php else: ?>

      <article class="approved-company-card">
        <div class="approved-company-main">
          <h3>No hay actividades publicadas</h3>
          <p class="approved-company-description">
            Todavía no hay actividades registradas en la plataforma.
          </p>
        </div>
      </article>

    <?php endif; ?>

  </section>

  <!-- Modal cancelar -->
<div id="modalCancelarAdmin" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:12px; padding:2rem; max-width:420px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
    <h3 style="margin-top:0;">Cancelar actividad</h3>
    <p>¿Seguro que deseas cancelar esta actividad?</p>
    <p style="color:#c0392b; font-weight:500;">⚠️ Todas las reservas asociadas también serán canceladas.</p>
    <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1.5rem;">
      <button onclick="cerrarModalCancelarAdmin()"
              style="padding:0.6rem 1.2rem; border-radius:8px; border:1px solid #ccc; background:#f5f5f5; cursor:pointer;">
        Volver
      </button>
      <button onclick="confirmarCancelarAdmin()"
              style="padding:0.6rem 1.2rem; border-radius:8px; border:none; background:#e74c3c; color:#fff; cursor:pointer; font-weight:600;">
        Sí, cancelar
      </button>
    </div>
  </div>
</div>

<!-- Modal reactivar -->
<div id="modalReactivarAdmin" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:12px; padding:2rem; max-width:420px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
    <h3 style="margin-top:0;">Reactivar actividad</h3>
    <p>¿Seguro que deseas reactivar esta actividad?</p>
    <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1.5rem;">
      <button onclick="cerrarModalReactivarAdmin()"
              style="padding:0.6rem 1.2rem; border-radius:8px; border:1px solid #ccc; background:#f5f5f5; cursor:pointer;">
        Volver
      </button>
      <button onclick="confirmarReactivarAdmin()"
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
    const buscadorActividad = document.getElementById("buscar-actividad");
    const filtroCategoria = document.getElementById("filtro-categoria");
    const filtroEstado = document.getElementById("filtro-estado");
    const listaActividades = document.getElementById("lista-actividades");

    let temporizador = null;

    function cargarActividades(){
        const buscar = buscadorActividad.value;
        const categoria = filtroCategoria.value;
        const estado = filtroEstado.value;

        const url = "ajax-actividades.php?buscar=" + encodeURIComponent(buscar) +
                    "&categoria=" + encodeURIComponent(categoria) +
                    "&estado=" + encodeURIComponent(estado);

        fetch(url)
            .then(response => response.text())
            .then(data => {
                listaActividades.innerHTML = data;
            });
    }

    buscadorActividad.addEventListener("input", function(){
        clearTimeout(temporizador);

        temporizador = setTimeout(function(){
            cargarActividades();
        }, 300);
    });

    filtroCategoria.addEventListener("change", cargarActividades);
    filtroEstado.addEventListener("change", cargarActividades);

    let idAdminActivo = null;

function abrirModalCancelarAdmin(id) {
    idAdminActivo = id;
    document.getElementById("modalCancelarAdmin").style.display = "flex";
}
function cerrarModalCancelarAdmin() {
    idAdminActivo = null;
    document.getElementById("modalCancelarAdmin").style.display = "none";
}
function confirmarCancelarAdmin() {
    if(idAdminActivo) window.location.href = "actividades.php?cancelar=" + idAdminActivo;
}

function abrirModalReactivarAdmin(id) {
    idAdminActivo = id;
    document.getElementById("modalReactivarAdmin").style.display = "flex";
}
function cerrarModalReactivarAdmin() {
    idAdminActivo = null;
    document.getElementById("modalReactivarAdmin").style.display = "none";
}
function confirmarReactivarAdmin() {
    if(idAdminActivo) window.location.href = "actividades.php?activar=" + idAdminActivo;
}

  </script>

</body>
</html>