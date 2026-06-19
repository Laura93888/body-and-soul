<?php
session_start();
require_once("../bd/bdact.php");
$bdact= new bdact("localhost",3306,"plataforma_servicios1","root","");

require_once("../bd/bdempresa.php");
$bdempre= new bdempresa("localhost",3306,"plataforma_servicios1","root","");

if(!isset($_SESSION["empresa"])){
  header("Location: registro-empresa.php");
  exit();
}else{

  $idempresa=$_SESSION["empresa"];

}

$paginaActual = basename($_SERVER["PHP_SELF"]);

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Body and Soul | Panel empresa</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Overpass:wght@300;400;500;600;700&family=Sansita:wght@700;800;900&display=swap" rel="stylesheet">

   <link rel="stylesheet" href="../css/empresa-styles/empresa-index.css">
   <link rel="stylesheet" href="../css/empresa-styles/perfil-empresa.css">
   <link rel="stylesheet" href="../css/styles.css">
   <link rel="stylesheet" href="../css/empresa-styles/mis-servicios.css">
   <link rel="stylesheet" href="../css/empresa-styles/editar-servicio.css">
   <link rel="stylesheet" href="../css/empresa-styles/empresa.css">
   <link rel="stylesheet" href="../css/empresa-styles/reservas.css">
    <link rel="stylesheet" href="../css/empresa-styles/nueva-actividad.css">
</head>

<script src="../js/filtrosempresaservicio.js"></script>
<script src="../js/validacionesanadirservicio.js"></script>
<script src="../js/buscadoractempre.js"></script>

<script>
let formIdActivo = null;

function abrirModalCancelar(id) {
    formIdActivo = id;
    document.getElementById("modalCancelar").style.display = "flex";
}

function cerrarModalCancelar() {
    formIdActivo = null;
    document.getElementById("modalCancelar").style.display = "none";
}

function confirmarCancelacion() {
    if (formIdActivo) {
        const form = document.getElementById("formCancelar-" + formIdActivo);
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "cancelar";
        input.value = "1";
        form.appendChild(input);
        form.submit();
    }
}

// Este va aparte porque necesita que el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("modalCancelar").addEventListener("click", function (e) {
        if (e.target === this) cerrarModalCancelar();
    });
});
</script>

<script>

let formReactivarActivo = null;

function abrirModalReactivar(id) {
    formReactivarActivo = id;
    document.getElementById("modalReactivar").style.display = "flex";
}

function cerrarModalReactivar() {
    formReactivarActivo = null;
    document.getElementById("modalReactivar").style.display = "none";
}

function confirmarReactivacion() {
    if (formReactivarActivo) {
        const form = document.getElementById("formReactivar-" + formReactivarActivo);
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "reactivar_servicio";
        input.value = "1";
        form.appendChild(input);
        form.submit();
    }
}

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("modalReactivar").addEventListener("click", function (e) {
        if (e.target === this) cerrarModalReactivar();
    });
});

</script>

<body class="company-body">

  <div class="company-layout">

    <!-- SIDEBAR -->
    <aside class="company-sidebar">
      <div class="company-sidebar-top">
        <a href="../publico/index.php">
          <img src="../img/logo.PNG" alt="Logo Body and Soul" class="company-sidebar-logo">
        </a>

        <h1>Empresa</h1>
      </div>

     <nav class="company-sidebar-nav">
      <a href="index.php" class="company-nav-link <?= $paginaActual == 'index.php' ? 'active' : '' ?>">Inicio</a>

      <a href="mis-servicios.php" class="company-nav-link <?= $paginaActual == 'mis-servicios.php' ? 'active' : '' ?>">Mis servicios</a>

      <a href="nueva-actividad.php" class="company-nav-link <?= $paginaActual == 'nueva-actividad.php' ? 'active' : '' ?>">Añadir servicio</a>

      <a href="reservas.php" class="company-nav-link <?= $paginaActual == 'reservas.php' ? 'active' : '' ?>">Reservas</a>

      <a href="perfil-empresa.php" class="company-nav-link <?= $paginaActual == 'perfil-empresa.php' ? 'active' : '' ?>">Perfil</a>

      <a href="logout.php" class="company-nav-link company-nav-link-logout">Cerrar sesión</a>
    </nav>

    </aside>


