<?php
require_once("../bd/bdadmin.php");

$bdadmin = new bdadmin("localhost", 3306, "plataforma_servicios1", "root", "");

$buscar = trim($_GET["buscar"] ?? "");
$categoria = trim($_GET["categoria"] ?? "");
$estado = trim($_GET["estado"] ?? "");

$actividades = $bdadmin->ObtenerTodasActividades($buscar, $categoria, $estado);

if(count($actividades) > 0){

    foreach($actividades as $actividad){

        $estadoActividad = $actividad["estado"] ?? "activo";

        if($estadoActividad == "activo"){
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
                        <span class="info-value">
                            <?= htmlspecialchars($actividad["lugar"]) ?>
                        </span>
                    </div>

                    <div class="approved-info-item">
                        <span class="info-label">Precio</span>
                        <span class="info-value">
                            <?= htmlspecialchars($actividad["precio"]) ?> €
                        </span>
                    </div>

                    <div class="approved-info-item">
                        <span class="info-label">Duración</span>
                        <span class="info-value">
                            <?= htmlspecialchars($actividad["duracion"]) ?>
                        </span>
                    </div>

                    <div class="approved-info-item">
                        <span class="info-label">Reservas</span>
                        <span class="info-value">
                            <?= $actividad["total_reservas"] ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="approved-company-actions">
                <a href="../publico/actividad.php?idact=<?= $actividad["id_servicio"] ?>" class="btn-detail">
                    Ver actividad
                </a>

                <?php if($estadoActividad == "activo"): ?>
                    <a href="actividades.php?cancelar=<?= $actividad["id_servicio"] ?>" 
                       class="btn-reject"
                       onclick="return confirm('¿Cancelar esta actividad?');">
                        Cancelar
                    </a>
                <?php else: ?>
                    <a href="actividades.php?activar=<?= $actividad["id_servicio"] ?>" 
                       class="btn-approve"
                       onclick="return confirm('¿Reactivar esta actividad?');">
                        Reactivar
                    </a>
                <?php endif; ?>
            </div>
        </article>

        <?php
    }

}else{
    ?>

    <article class="approved-company-card">
        <div class="approved-company-main">
            <h3>No hay actividades</h3>
            <p class="approved-company-description">
                No se han encontrado actividades con los filtros seleccionados.
            </p>
        </div>
    </article>

    <?php
}
?>