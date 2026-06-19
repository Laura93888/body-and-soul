<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("valoracion.php");

require_once("../bd/bd.php");
$bbdd= new db("localhost",3306,"plataforma_servicios1","root","");

require_once("../bd/bdact.php");
$bdact= new bdact("localhost",3306,"plataforma_servicios1","root","");

if(isset($_SESSION["usuario"])){

  $usuario=$bbdd->ObtenerUsuario($_SESSION["usuario"]);

  $nombre=$usuario["nombre"];
  $apellido=$usuario["apellido"];
  $inicial=strtoupper($nombre[0]);

  $iniciosesion=$iniciosesion="<a href='perfil.php' class='contenedorinicio'>
                  <span class='profile-avatar'>".$inicial."</span>
                  <span class='profile-name'>".$nombre." ".$apellido."</span>
                  </a>";

}else{
  $iniciosesion = "<a href='login.php' class='btn btn-outline'>
  <span class='btn-icon'>
    <svg viewBox='0 0 24 24' fill='currentColor'>
      <path d='M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z'/>
    </svg>
  </span>
  Iniciar sesión
</a>";
}

$actividadesmasreservadas = $bdact->obtenerActividadesMasReservadas();

// Categorías
$categorias = $bdact->obtenerCategoriasPadre();
$subcategoriasPorPadre = [];

foreach ($categorias as $categoria) {
    $subcategoriasPorPadre[$categoria["nombre"]] = $bdact->obtenerSubcat($categoria["id_categoria"]);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Body and Soul</title>

  <link rel="stylesheet" href="../css/styles.css"/>
  <link rel="stylesheet" href="../css/public-styles/index.css"/>
  <link rel="stylesheet" href="../css/public-styles/login.css"/>
  <link rel="stylesheet" href="../css/public-styles/perfil.css"/>
  <link rel="stylesheet" href="../css/public-styles/categoria.css"/>
  <link rel="stylesheet" href="../css/public-styles/actividad.css"/>
  <link rel="stylesheet" href="../css/public-styles/reserva.css"/>
  <link rel="stylesheet" href="../css/public-styles/mis-reservas.css"/>
  <link rel="stylesheet" href="../css/public-styles/mis-valoraciones.css"/>
  <link rel="stylesheet" href="../css/public-styles/favoritos.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body>

<header class="main-header">
  <div class="container header-container">

    <div class="header-left">
      <a href="index.php">
        <img src="../img/logo.PNG" alt="Logo Body and Soul" class="logo">
      </a>

      <button type="button" id="abrirCategorias" class="btn btn-outline header-categories-btn">
        <span class="cat-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
            <rect x="3" y="3" width="7" height="7" rx="2"/>
            <rect x="14" y="3" width="7" height="7" rx="2"/>
            <rect x="3" y="14" width="7" height="7" rx="2"/>
            <rect x="14" y="14" width="7" height="7" rx="2"/>
          </svg>
        </span>
        Categorías
      </button>
    </div>

    <div class="header-title">
      <?=$titulo?>
    </div>

    <div class="header-right">
      <a href="../Empresa/login-empresa.php" class="btn btn-outline">
        <span class="btn-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 21V3h18v18h-6v-6H9v6H3zm4-4h2v2H7v-2zm0-4h2v2H7v-2zm0-4h2v2H7V9zm4 4h2v2h-2v-2zm0-4h2v2h-2V9zm4 8h2v2h-2v-2zm0-4h2v2h-2v-2z"/>
          </svg>
        </span>
        Acceso empresa
      </a>
      <?=$iniciosesion?>
    </div>

  </div>
</header>

<!-- OVERLAY -->
<div id="overlayCategorias" class="category-overlay"></div>

<!-- PANEL -->
<aside id="panelCategorias" class="category-panel">

  <div class="category-panel-header">
    <h3>Categorías</h3>
    <button id="cerrarCategorias" class="category-close">×</button>
  </div>

  <div class="category-panel-content">

    <?php foreach ($categorias as $categoria) { ?>
      <div class="category-panel-group">

        <button type="button" class="category-toggle">
          <?= htmlspecialchars($categoria["nombre"]) ?>
          <span>⌄</span>
        </button>

        <div class="category-submenu">
          <a class="category-main-link" href="categoria.php?cat=<?= urlencode($categoria["nombre"]) ?>">
            Ver todo en <?= htmlspecialchars($categoria["nombre"]) ?>
          </a>

          <?php foreach ($subcategoriasPorPadre[$categoria["nombre"]] as $subcat) { ?>
            <a class="category-sub-link" href="resultados.php?subcategoria=<?= $subcat["id_categoria"] ?>">
              <?= htmlspecialchars($subcat["nombre"]) ?>
            </a>
          <?php } ?>
        </div>

      </div>
    <?php } ?>

  </div>

</aside>

<!-- JS -->
<script>
document.addEventListener("DOMContentLoaded", function () {

  const abrir = document.getElementById("abrirCategorias");
  const cerrar = document.getElementById("cerrarCategorias");
  const panel = document.getElementById("panelCategorias");
  const overlay = document.getElementById("overlayCategorias");

  // abrir panel
  abrir.addEventListener("click", () => {
    panel.classList.add("activo");
    overlay.classList.add("activo");
  });

  // cerrar panel
  cerrar.addEventListener("click", cerrarPanel);
  overlay.addEventListener("click", cerrarPanel);

  function cerrarPanel(){
    panel.classList.remove("activo");
    overlay.classList.remove("activo");
  }

  // desplegar subcategorías
  document.querySelectorAll(".category-toggle").forEach(btn => {
    btn.addEventListener("click", function () {
      const grupoActual = this.parentElement;
      const estabaAbierto = grupoActual.classList.contains("activo");

      document.querySelectorAll(".category-panel-group").forEach(grupo => {
        grupo.classList.remove("activo");
      });

      if (!estabaAbierto) {
        grupoActual.classList.add("activo");
      }
    });
  });

});
</script>

