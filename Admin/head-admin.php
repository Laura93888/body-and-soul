<?php
session_start();

require_once("../bd/bd.php");
$bbdd = new db("localhost",3306,"plataforma_servicios1","root","");

require_once("../bd/bdadmin.php");
$bdadmin = new bdadmin("localhost",3306,"plataforma_servicios1","root","");

if(!isset($_SESSION["usuario"])){
    header("Location: ../publico/login.php");
    exit();
}

$usuarioAdmin = $bbdd->ObtenerUsuario($_SESSION["usuario"]);

if($usuarioAdmin == false || $usuarioAdmin["id_rol"] != 3){
    header("Location: ../publico/index.php");
    exit();
}

$paginaActiva = $paginaActiva ?? "";
$tituloPagina = $tituloPagina ?? "Panel de administración";
$etiquetaPagina = $etiquetaPagina ?? "Administración";
$cssExtra = $cssExtra ?? [];

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Body and Soul | <?= htmlspecialchars($tituloPagina) ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Overpass:wght@300;400;500;600;700&family=Sansita:wght@700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/admin-styles/admin-dashboard.css">
  <link rel="stylesheet" href="../css/admin-styles/usuarios.css">

  <?php foreach($cssExtra as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
  <?php endforeach; ?>
</head>

<body class="admin-dashboard-body">

<div class="admin-layout">

  <aside class="admin-sidebar">
    <div class="admin-sidebar-top">
      <a href="../publico/index.php">
        <img src="../img/logo.PNG" alt="Logo Body and Soul" class="admin-sidebar-logo">
      </a>
      <h1>Admin</h1>
    </div>

    <nav class="admin-sidebar-nav">
      <a href="dashboard.php" class="admin-nav-link <?= $paginaActiva == 'dashboard' ? 'active' : '' ?>">Dashboard</a>
      <a href="empresas-pendientes.php" class="admin-nav-link <?= $paginaActiva == 'empresas-pendientes' ? 'active' : '' ?>">Empresas pendientes</a>
      <a href="empresas-aprobadas.php" class="admin-nav-link <?= $paginaActiva == 'empresas-aprobadas' ? 'active' : '' ?>">Empresas aprobadas</a>
      <a href="actividades.php" class="admin-nav-link <?= $paginaActiva == 'actividades' ? 'active' : '' ?>">Actividades</a>
      <a href="usuarios.php" class="admin-nav-link <?= $paginaActiva == 'usuarios' ? 'active' : '' ?>">Usuarios</a>
      <a href="reportes.php" class="admin-nav-link <?= $paginaActiva == 'reportes' ? 'active' : '' ?>">Reportes</a>
      <a href="crear-categoria.php" class="admin-nav-link <?= $paginaActiva == 'gestionar-categoria' ? 'active' : '' ?>">Gestionar categoría</a>
      <a href="../publico/perfil.php" class="admin-nav-link">Volver a mi perfil</a>
      <a href="../publico/perfil.php?cerrar=si" class="admin-nav-link admin-logout">Cerrar sesión</a>
    </nav>
  </aside>

  <div class="admin-main">

    <header class="admin-topbar">
      <div class="admin-topbar-left">
        <span class="admin-page-tag"><?= htmlspecialchars($etiquetaPagina) ?></span>
        <h2><?= htmlspecialchars($tituloPagina) ?></h2>
      </div>

      <div class="admin-topbar-right">
        <div class="admin-admin-chip">
          <span class="admin-admin-avatar">A</span>
          <span>Administrador</span>
        </div>
      </div>
    </header>