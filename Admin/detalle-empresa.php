<?php
$paginaActiva = "empresas-pendientes";
$tituloPagina = "Detalle de empresa";
$etiquetaPagina = "Revisión empresa";
$cssExtra = [
    "../css/admin-styles/admin-detalle-empresa.css"
];

require_once("head-admin.php");

if(isset($_GET["aprobar"])){
    $idSolicitud = (int) $_GET["aprobar"];
    $bdadmin->AprobarEmpresa($idSolicitud);

    header("Location: empresas-pendientes.php");
    exit();
}

if(isset($_GET["rechazar"])){
    $idSolicitud = (int) $_GET["rechazar"];
    $bdadmin->RechazarEmpresa($idSolicitud);

    header("Location: empresas-pendientes.php");
    exit();
}

if(!isset($_GET["id"])){
    echo "Empresa no encontrada";
    exit();
}

$idSolicitud = (int) $_GET["id"];
$empresa = $bdadmin->ObtenerSolicitudEmpresaPorId($idSolicitud);

if($empresa == false){
    echo "Solicitud no encontrada";
    exit();
}
?>

<main class="admin-content">

    <section class="empresa-detalle-card">

        <div class="empresa-detalle-header">
            <img src="<?= htmlspecialchars($empresa["logo_empresa"] ?? "../assets/placeholder.jpg") ?>" class="empresa-logo" alt="Logo empresa">
            <div>
                <span class="empresa-categoria"><?= ucfirst(htmlspecialchars($empresa["categoria_empresa"] ?? "Sin categoría")) ?></span>
                <h2><?= htmlspecialchars($empresa["nombre"]) ?></h2>
                <p class="empresa-ciudad"><?= htmlspecialchars($empresa["ciudad_empresa"] ?? "Sin ciudad") ?></p>
            </div>
        </div>

        <p class="empresa-descripcion">
        <?= nl2br(htmlspecialchars($empresa["datos"] ?? "")) ?>
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
                <span class="info-label">Fecha solicitud</span>
                <span class="info-value"><?= date("d/m/Y", strtotime($empresa["fecha"])) ?></span>
            </div>
        </div>

    </section>

    <div class="empresa-acciones">
        <a href="detalle-empresa.php?id=<?= $empresa["id_solicitud"] ?>&aprobar=<?= $empresa["id_solicitud"] ?>" 
        class="btn-approve"
        onclick="return confirm('¿Aprobar esta empresa?');">
        Aprobar empresa
        </a>

        <a href="detalle-empresa.php?id=<?= $empresa["id_solicitud"] ?>&rechazar=<?= $empresa["id_solicitud"] ?>" 
        class="btn-reject"
        onclick="return confirm('¿Rechazar esta empresa?');">
        Rechazar empresa
        </a>
    </div>

</main>

</div>
</div>

</body>
</html>