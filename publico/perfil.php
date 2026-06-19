<?php
session_start();

if(isset($_GET["cerrar"]) && $_GET["cerrar"] == "si"){
  session_unset();
  session_destroy();
  header("Location: index.php");
  exit();
}

if(!isset($_SESSION["usuario"])){
  header("Location: index.php");
  exit();
}

$titulo="<h1>Mi perfil</h1>";
require_once("head.php");

$id = $_SESSION["usuario"];
$rol = $bbdd->obtenerrolus($id);

?>

  <main class="profile-page">
    <section class="profile-section">
      <div class="container">

        <div class="profile-intro">
          <span class="section-tag">Área personal</span>
          <h2>Gestiona tu cuenta</h2>
          <p>
            Desde aquí podrás consultar tu información, revisar tus reservas y gestionar tus preferencias.
          </p>
        </div>

        <div class="profile-grid">

          <a href="modificar-datos.php" class="profile-card">
            <div class="profile-card-content">
              <h3>Modificar datos</h3>
              <p>Actualiza tu nombre, correo, contraseña y demás información personal.</p>
            </div>
          </a>

          <a href="mis-reservas.php" class="profile-card">
            <div class="profile-card-content">
              <h3>Mis actividades reservadas</h3>
              <p>Consulta las próximas actividades que ya has reservado.</p>
            </div>
          </a>

          <a href="favoritos.php" class="profile-card">
            <div class="profile-card-content">
              <h3>Favoritos</h3>
              <p>Accede rápidamente a tus actividades y experiencias guardadas.</p>
            </div>
          </a>

        <a href="mis-valoraciones.php" class="profile-card">
            <div class="profile-card-content">
                <h3>Mis valoraciones</h3>
                <p>Consulta las valoraciones y comentarios que has dejado en las actividades.</p>
            </div>
        </a>

          <a href="perfil.php?cerrar=si" class="profile-card profile-card-logout">
            <div class="profile-card-content">
              <h3>Cerrar sesión</h3>
              <p>Sal de tu cuenta de forma segura.</p>
            </div>
          </a>

          <?php
          
          if($rol==3){
            
          ?>
          
            <a href="../Admin/dashboard.php" class="profile-card profile-card-logout">
            <div class="profile-card-content">
              <h3>Perfil de Administrador</h3>
              <p>Gestiona usuarios, empresas y contenido de la plataforma.</p>
            </div>
          </a>
          <?php
          }
          ?>

        </div>

      </div>
    </section>
  </main>


 <?php
require_once("footer.php");
?>
</body>
</html>