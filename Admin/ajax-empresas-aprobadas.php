<?php
require_once("../bd/bdadmin.php");

$bdadmin = new bdadmin("localhost", 3306, "plataforma_servicios1", "root", "");

$buscar = trim($_GET["buscar"] ?? "");
$categoria = trim($_GET["categoria"] ?? "");
$estado = trim($_GET["estado"] ?? "");

$empresas = $bdadmin->ObtenerEmpresasAprobadas($buscar, $categoria, $estado);

if(count($empresas) > 0){

    foreach($empresas as $empresa){

        $estadoEmpresa = $empresa["estado"] ?? "activa";

        if($estadoEmpresa == "activa"){
            $claseEstado = "approved";
            $textoEstado = "Activa";
        }else{
            $claseEstado = "blocked";
            $textoEstado = "Suspendida";
        }

        $logo = $empresa["logo_empresa"] ?? "";

        if($logo == "" || $logo == null){
            $logo = "../assets/placeholder.jpg";
        }else if(!str_starts_with($logo, "../")){
            $logo = "../" . ltrim($logo, "/");
        }
        ?>

        <article class="approved-company-card">
          <div class="approved-company-main">
            <div class="approved-company-header">

              <img 
                src="<?= htmlspecialchars($logo) ?>" 
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
            <?php if($estadoEmpresa == "activa"): ?>
              <a href="empresas-aprobadas.php?suspender=<?= $empresa["id_empresa"] ?>" 
                 class="btn-warning"
                 onclick="return confirm('¿Suspender esta empresa? También se cancelarán sus actividades.');">
                Suspender
              </a>
            <?php else: ?>
              <a href="empresas-aprobadas.php?activar=<?= $empresa["id_empresa"] ?>" 
                 class="btn-approve"
                 onclick="return confirm('¿Reactivar esta empresa?');">
                Reactivar
              </a>
            <?php endif; ?>

            <a href="detalle-empresa-aprobada.php?id=<?= $empresa["id_empresa"] ?>" class="btn-detail">
              Ver empresa
            </a>

            <a href="servicios-empresa.php?id=<?= $empresa["id_empresa"] ?>" class="btn-secondary-admin">
              Ver actividades
            </a>
          </div>
        </article>

        <?php
    }

}else{
    ?>

    <article class="approved-company-card">
      <div class="approved-company-main">
        <h3>No hay empresas</h3>
        <p class="approved-company-description">
          No se han encontrado empresas con los filtros seleccionados.
        </p>
      </div>
    </article>

    <?php
}
?>