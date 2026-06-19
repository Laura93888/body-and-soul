<?php
$paginaActiva = "empresas-aprobadas";
$tituloPagina = "Detalle empresa";
$etiquetaPagina = "Empresa aprobada";
$cssExtra = [
    "../css/admin-styles/admin-detalle-empresa.css",
    "../css/admin-styles/empresas-aprobadas.css"
];

require_once("head-admin.php");

if(isset($_GET["suspender"])){
    $idEmpresa = (int) $_GET["suspender"];
    $bdadmin->SuspenderEmpresa($idEmpresa);

    header("Location: detalle-empresa-aprobada.php?id=" . $idEmpresa);
    exit();
}

if(isset($_GET["activar"])){
    $idEmpresa = (int) $_GET["activar"];
    $bdadmin->ActivarEmpresa($idEmpresa);

    header("Location: detalle-empresa-aprobada.php?id=" . $idEmpresa);
    exit();
}

if(!isset($_GET["id"])){
    header("Location: empresas-aprobadas.php");
    exit();
}

$idEmpresa = (int) $_GET["id"];
$empresa = $bdadmin->ObtenerEmpresaAprobadaPorId($idEmpresa);

if($empresa == false){
    echo "Empresa no encontrada";
    exit();
}

$estado = $empresa["estado"] ?? "activa";

if($estado == "activa"){
    $claseEstado = "approved";
    $textoEstado = "Activa";
}else{
    $claseEstado = "blocked";
    $textoEstado = "Suspendida";
}
?>

<main class="admin-content">

  <section class="empresa-detalle-card">

    <div class="empresa-detalle-header">

      <img 
        src="<?= htmlspecialchars($empresa["logo_empresa"] ?? "../assets/placeholder.jpg") ?>" 
        class="empresa-logo" 
        alt="Logo empresa"
      >

      <div>
        <span class="empresa-categoria">
          <?= ucfirst(htmlspecialchars($empresa["categoria_empresa"] ?? "Sin categoría")) ?>
        </span>

        <h2><?= htmlspecialchars($empresa["nombre_empresa"]) ?></h2>

        <p class="empresa-ciudad">
          <?= htmlspecialchars($empresa["ciudad_empresa"] ?? "Sin ciudad") ?>
        </p>
      </div>

      <span class="admin-status-chip <?= $claseEstado ?>">
        <?= $textoEstado ?>
      </span>

    </div>

    <p class="empresa-descripcion">
      <?= nl2br(htmlspecialchars($empresa["descripcion_empresa"] ?? "")) ?>
    </p>

    <div class="empresa-info-grid">

      <div class="empresa-info-item">
        <span class="info-label">Email</span>
        <span class="info-value"><?= htmlspecialchars($empresa["email"]) ?></span>
      </div>

      <div class="empresa-info-item">
        <span class="info-label">Teléfono</span>
        <span class="info-value"><?= htmlspecialchars($empresa["telefono"] ?? "No indicado") ?></span>
      </div>

      <div class="empresa-info-item">
        <span class="info-label">Dirección</span>
        <span class="info-value"><?= htmlspecialchars($empresa["direccion"] ?? "No indicada") ?></span>
      </div>

      <div class="empresa-info-item">
        <span class="info-label">Estado</span>
        <span class="info-value"><?= $textoEstado ?></span>
      </div>

      <div class="empresa-info-item">
        <span class="info-label">Actividades</span>
        <span class="info-value"><?= $empresa["total_servicios"] ?></span>
      </div>

      <div class="empresa-info-item">
        <span class="info-label">Reservas totales</span>
        <span class="info-value"><?= $empresa["total_reservas"] ?></span>
      </div>

    </div>

  </section>

  <div class="empresa-acciones">

    <a href="empresas-aprobadas.php" class="btn-detail">
      Volver
    </a>

    <a href="servicios-empresa.php?id=<?= $empresa["id_empresa"] ?>" class="btn-secondary-admin">
      Ver actividades
    </a>

    <?php if($estado == "activa"): ?>
      <a href="detalle-empresa-aprobada.php?id=<?= $empresa["id_empresa"] ?>&suspender=<?= $empresa["id_empresa"] ?>" 
         class="btn-warning"
         onclick="return confirm('¿Suspender esta empresa? También se cancelarán sus actividades y reservas asociadas.');">
        Suspender empresa
      </a>
    <?php else: ?>
      <a href="detalle-empresa-aprobada.php?id=<?= $empresa["id_empresa"] ?>&activar=<?= $empresa["id_empresa"] ?>" 
         class="btn-approve"
         onclick="return confirm('¿Reactivar esta empresa?');">
        Reactivar empresa
      </a>
    <?php endif; ?>

  </div>

</main>

</div>
</div>

</body>
</html>