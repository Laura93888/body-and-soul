<?php

$paginaActiva = "usuarios";
$tituloPagina = "Usuarios registrados";
$etiquetaPagina = "Gestión de usuarios";
$cssExtra = [
    "../css/admin-styles/usuarios.css"
];

require_once("head-admin.php");

if(isset($_GET["cambiarRol"]) && isset($_GET["rol"])){
    $idUsuario = (int) $_GET["cambiarRol"];
    $nuevoRol = (int) $_GET["rol"];

    if($nuevoRol == 1 || $nuevoRol == 3){

        $usuarioActual = $bbdd->ObtenerUsuario($_SESSION["usuario"]);

        if($usuarioActual["id_usuario"] != $idUsuario){

            $bdadmin->CambiarRolUsuario($idUsuario, $nuevoRol);

        }
    }

    header("Location: usuarios.php");
    exit();
}

if(isset($_GET["suspender"]) && isset($_GET["tipo"])){
    $id = (int) $_GET["suspender"];

    if($_GET["tipo"] == "empresa"){
        $bdadmin->SuspenderEmpresa($id);
    }else{
        $bdadmin->SuspenderUsuario($id);
    }

    header("Location: usuarios.php");
    exit();
}

if(isset($_GET["activar"]) && isset($_GET["tipo"])){
    $id = (int) $_GET["activar"];

    if($_GET["tipo"] == "empresa"){
        $bdadmin->ActivarEmpresa($id);
    }else{
        $bdadmin->ActivarUsuario($id);
    }

    header("Location: usuarios.php");
    exit();
}

$usuarios = $bdadmin->ObtenerUsuariosAdmin();
$datosUsuarios = $bdadmin->ObtenerDatosUsuariosAdmin();
$tiposUsuario = $bdadmin->ObtenerTiposUsuario();
$estadosUsuario = $bdadmin->ObtenerEstadosUsuario();

$tipoSeleccionado = $_GET["tipo_usuario"] ?? "";
$estadoSeleccionado = $_GET["estado_usuario"] ?? "";

?>

<main class="admin-content">

  <section class="admin-hero-card">
    <div class="admin-hero-text">
      <span class="section-badge">Control</span>
      <h3>Gestión de usuarios de la plataforma</h3>
      <p>
        Consulta los usuarios registrados, revisa su actividad y gestiona su estado dentro del sistema.
      </p>
    </div>

    <div class="admin-hero-stat">
      <span class="admin-hero-number"><?= $datosUsuarios["activos"] ?></span>
      <span class="admin-hero-label">Usuarios activos</span>
    </div>
  </section>

  <section class="admin-stats-grid">
    <article class="admin-stat-card">
      <h4>Usuarios activos</h4>
      <strong><?= $datosUsuarios["activos"] ?></strong>
      <p>Cuentas habilitadas</p>
    </article>

    <article class="admin-stat-card">
      <h4>Nuevos registros</h4>
      <strong><?= $datosUsuarios["nuevos_hoy"] ?></strong>
      <p>Hoy</p>
    </article>

    <article class="admin-stat-card">
      <h4>Usuarios con reservas</h4>
      <strong><?= $datosUsuarios["con_reservas"] ?></strong>
      <p>Han realizado al menos una reserva</p>
    </article>

    <article class="admin-stat-card">
      <h4>Cuentas suspendidas</h4>
      <strong><?= $datosUsuarios["suspendidos"] ?></strong>
      <p>Requieren revisión</p>
    </article>
  </section>

  <section class="admin-filters-box">
    <form class="admin-filters-form" action="" method="get">
      <div class="admin-search-wrap">
        <input
          type="text"
          id="buscar-usuario-admin"
          name="buscar_usuario"
          class="admin-search-input"
          placeholder="Buscar usuario o email"
        >
      </div>

      <select id="filtro-tipo-usuario" name="tipo_usuario" class="admin-filter-select">

    <option value="">Todos los tipos</option> 

    <option value="empresa"
        <?= ($tipoSeleccionado == "empresa") ? "selected" : "" ?>>
        Empresa
    </option>

    <?php foreach($tiposUsuario as $tipo){ ?>

        <option value="<?= $tipo["id_rol"] ?>"
            <?= ($tipoSeleccionado == $tipo["id_rol"]) ? "selected" : "" ?>>

            <?= ucfirst(htmlspecialchars($tipo["nombre"])) ?>

        </option>

    <?php } ?>

</select>

      <select id="filtro-estado-usuario" name="estado_usuario" class="admin-filter-select">
        <option value="">Todos los estados</option>
        <?php foreach($estadosUsuario as $estado){ ?>

            <option value="<?= htmlspecialchars($estado["estado"]) ?>"
                <?= ($estadoSeleccionado == $estado["estado"]) ? "selected" : "" ?>>

                <?= ucfirst(htmlspecialchars($estado["estado"])) ?>

            </option>

        <?php } ?>
      </select>
    </form>
  </section>

  <section class="admin-users-list" id="lista-usuarios-admin">

    <?php if(count($usuarios) > 0): ?>

      <?php foreach($usuarios as $usuario): ?>

        <?php
          $nombreCompleto = trim(
              html_entity_decode($usuario["nombre"], ENT_QUOTES, "UTF-8") . " " .
              html_entity_decode($usuario["apellido"], ENT_QUOTES, "UTF-8")
          );
          $inicial = strtoupper(substr($usuario["nombre"], 0, 1));
          $estado = $usuario["estado"] ?? "activo";

          if($usuario["tipo_cuenta"] == "empresa"){
              $rolTexto = "Empresa";
          }else if($usuario["id_rol"] == 3){
              $rolTexto = "Administrador";
          }else{
              $rolTexto = "Cliente";
          }

          if($estado == "activo" || $estado == "activa"){
              $estadoClase = "status-active";
              $estadoTexto = "Activo";
          }else{
              $estadoClase = "status-blocked";
              $estadoTexto = "Suspendido";
          }
        ?>

        <article class="admin-user-card">
          <div class="admin-user-left">
            <div class="admin-user-avatar"><?= htmlspecialchars($inicial) ?></div>
          </div>

          <div class="admin-user-content">
            <div class="admin-user-topline">
              <span class="admin-user-role"><?= $rolTexto ?></span>

              <span class="admin-user-status <?= $estadoClase ?>">
                <?= $estadoTexto ?>
              </span>
            </div>
            <h3 class="admin-user-name"><?= htmlspecialchars($nombreCompleto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h3>

            <p class="admin-user-meta">
              <?= htmlspecialchars($usuario["email"]) ?>
            </p>

            <p class="admin-user-description">
  <?= ($usuario["tipo_cuenta"] == "empresa") 
      ? "Empresa registrada en la plataforma Body and Soul." 
      : "Usuario registrado en la plataforma Body and Soul." ?>
</p>

            <div class="admin-user-data-grid">
              <div class="admin-user-data-box">
                <span class="data-label">Fecha alta</span>
                <strong><?= date("d/m/Y", strtotime($usuario["fecha_registro"])) ?></strong>
              </div>

              <div class="admin-user-data-box">
                <span class="data-label">Reservas</span>
                <strong><?= $usuario["total_reservas"] ?></strong>
              </div>

              <div class="admin-user-data-box">
                <span class="data-label">Rol</span>
                <strong><?= $rolTexto ?></strong>
              </div>

              <div class="admin-user-data-box">
                <span class="data-label">Valoraciones</span>
                <strong><?= $usuario["total_resenas"] ?></strong>
              </div>
            </div>
          </div>

          <div class="admin-user-actions">
            <?php
              $esEmpresa = $usuario["tipo_cuenta"] == "empresa";
              $esAdministrador = $usuario["id_rol"] == 3;

              $nuevoRol = $esAdministrador ? 1 : 3;
              $textoBotonRol = $esAdministrador ? "Quitar admin" : "Hacer admin";
              $claseBotonRol = $esAdministrador ? "btn-warning" : "btn-success";
              $mensajeRol = $esAdministrador
                  ? "¿Quieres quitar permisos de administrador a este usuario?"
                  : "¿Quieres convertir este usuario en administrador?";

              $puedeSuspender = !$esAdministrador && ($estado == "activo" || $estado == "activa");
              $textoEstado = $puedeSuspender ? "Suspender" : "Reactivar";
              $accionEstado = $puedeSuspender ? "suspender" : "activar";
              $claseEstado = $puedeSuspender ? "btn-warning" : "btn-success";
              $mensajeEstado = $puedeSuspender ? "¿Suspender esta cuenta?" : "¿Reactivar esta cuenta?";

              $usuarioActual = $bbdd->ObtenerUsuario($_SESSION["usuario"]);
              $esMiUsuario = $usuarioActual["id_usuario"] == $usuario["id"];
            ?>

            <?php if(!$esEmpresa && !$esMiUsuario): ?>

              <a href="reservas-usuario.php?id=<?= $usuario["id"] ?>" class="btn-admin btn-primary">
                Ver reservas
              </a>

              <a href="usuarios.php?cambiarRol=<?= $usuario["id"] ?>&rol=<?= $nuevoRol ?>"
                class="btn-admin <?= $claseBotonRol ?>"
                onclick="return confirm('<?= $mensajeRol ?>');">
                <?= $textoBotonRol ?>
              </a>

            <?php endif; ?>

            <?php if(!$esAdministrador): ?>

              <a href="usuarios.php?<?= $accionEstado ?>=<?= $usuario["id"] ?>&tipo=<?= $usuario["tipo_cuenta"] ?>"
                class="btn-admin <?= $claseEstado ?>"
                onclick="return confirm('<?= $mensajeEstado ?>');">
                <?= $textoEstado ?>
              </a>

            <?php endif; ?>

          </div>
        </article>

      <?php endforeach; ?>

    <?php else: ?>

      <article class="admin-user-card">
        <div class="admin-user-content">
          <h3 class="admin-user-name">No hay usuarios registrados</h3>
          <p class="admin-user-description">
            Todavía no existen usuarios dados de alta en la plataforma.
          </p>
        </div>
      </article>

    <?php endif; ?>

  </section>

</main>

</div>
</div>

<script>
  const buscadorUsuarioAdmin = document.getElementById("buscar-usuario-admin");
  const filtroTipoUsuario = document.getElementById("filtro-tipo-usuario");
  const filtroEstadoUsuario = document.getElementById("filtro-estado-usuario");
  const listaUsuariosAdmin = document.getElementById("lista-usuarios-admin");

  let temporizadorUsuario = null;

  function cargarUsuariosAdmin(){
      const buscar = buscadorUsuarioAdmin.value;
      const tipo = filtroTipoUsuario.value;
      const estado = filtroEstadoUsuario.value;

      const url = "ajax-usuarios.php?buscar=" + encodeURIComponent(buscar) +
                  "&tipo=" + encodeURIComponent(tipo) +
                  "&estado=" + encodeURIComponent(estado);

      fetch(url)
          .then(response => response.text())
          .then(data => {
              listaUsuariosAdmin.innerHTML = data;
          });
  }

  buscadorUsuarioAdmin.addEventListener("input", function(){
      clearTimeout(temporizadorUsuario);

      temporizadorUsuario = setTimeout(function(){
          cargarUsuariosAdmin();
      }, 300);
  });

  filtroTipoUsuario.addEventListener("change", cargarUsuariosAdmin);
  filtroEstadoUsuario.addEventListener("change", cargarUsuariosAdmin);
</script>

</body>
</html>