<?php
require_once("head.php");
require_once("../bd/bdempresa.php");

if(!isset($_SESSION["empresa"])){
    header("Location: login.php");
    exit();
}

$idEmpresa = $_SESSION["empresa"];

if(!isset($_GET["idservicio"])){
    header("Location: mis-servicios.php");
    exit();
}

$empresa=$bdempre->sacardatosempresa($idEmpresa);

$idServicio = (int) $_GET["idservicio"];

$servicio = $bdact->obtenerActividadPorIdConcancelados($idServicio);

if($servicio == false || $servicio["id_empresa"] != $idEmpresa){
    header("Location: mis-servicios.php");
    exit();
}

if(isset($_POST["eliminar_horario"])){

    $idHorario = (int) $_POST["id_horario"];

    if(!$bdempre->HorarioTieneReservasConfirmadas($idHorario)){
        $bdempre->EliminarHorarioEmpresa($idHorario, $idEmpresa);

        require("../bd/generarJSONact.php");

        header("Location: gestionar-horarios.php?idservicio=".$idServicio."&eliminado=1");
        exit();
    }else{
        $plazaserror = "No puedes eliminar un horario con reservas confirmadas.";
    }
}

$tieneHorarios = $bdempre->ServicioTieneHorarios($idServicio);

$horarios = $bdempre->ObtenerHorariosServicioEmpresa($idServicio, $idEmpresa);
$tieneHorarios = count($horarios) > 0;

$registro_ok = false;
$banderaerror = false;

$fecha = "";
$fechaerror = "";

$hora_inicio = "";
$horainicioerror = "";

$plazas_maximas = "";
$plazaserror = "";

$modoEditar = false;
$idHorarioEditar = 0;

if(isset($_GET["editar"])){
    $idHorarioEditar = (int) $_GET["editar"];
    $horarioEditar = $bdempre->ObtenerHorarioPorIdEmpresa($idHorarioEditar, $idEmpresa);

    if($horarioEditar != false){
        $modoEditar = true;
        $fecha = $horarioEditar["fecha"];
        $hora_inicio = substr($horarioEditar["hora_inicio"], 0, 5);
        $plazas_maximas = $horarioEditar["plazas_maximas"];
    }
}

if(isset($_POST["fecha"])){
    $fecha = trim($_POST["fecha"]);

    if($fecha == ""){
        $fechaerror = "Debes indicar una fecha";
        $banderaerror = true;
    }
}

if(isset($_POST["hora_inicio"])){
    $hora_inicio = trim($_POST["hora_inicio"]);

    if($hora_inicio == ""){
        $horainicioerror = "Debes indicar la hora de inicio";
        $banderaerror = true;
    }
}

if(isset($_POST["plazas_maximas"])){
    $plazas_maximas = trim($_POST["plazas_maximas"]);

    if($plazas_maximas == ""){
        $plazaserror = "Debes indicar las plazas disponibles";
        $banderaerror = true;
    }else if($plazas_maximas <= 0){
        $plazaserror = "Las plazas deben ser mayor que 0";
        $banderaerror = true;
    }
}

if(isset($_POST["enviar"]) && $banderaerror == false){

  $minutosDuracion = $bdempre->DuracionTextoAMinutos($servicio["duracion"]);
  $hora_fin = date("H:i", strtotime($hora_inicio . " +" . $minutosDuracion . " minutes"));

  if(isset($_POST["id_horario"]) && $_POST["id_horario"] != ""){
      $idHorario = (int) $_POST["id_horario"];

      $bdempre->ActualizarHorarioEmpresa(
          $idHorario,
          $idEmpresa,
          $fecha,
          $hora_inicio,
          $hora_fin,
          $plazas_maximas
      );

      require("../bd/generarJSONact.php");

      header("Location: gestionar-horarios.php?idservicio=".$idServicio."&editado=1");
      exit();

  }else{
      $bdempre->InsertarDetalleActividad(
          $idServicio,
          $fecha,
          $hora_inicio,
          $hora_fin,
          $plazas_maximas
      );

      $bdempre->ActivarServicio($idServicio, $idEmpresa);

      require("../bd/generarJSONact.php");

      header("Location: gestionar-horarios.php?idservicio=".$idServicio."&ok=1");
      exit();
  }
}




?>

<div class="company-main">

  <header class="company-topbar">
    <div class="company-topbar-left">
      <span class="company-page-tag">Disponibilidad</span>
      <h2>Gestionar horarios</h2>
    </div>

    <div class="company-topbar-right">
      <a href="mis-servicios.php" class="company-back-link">Ver mis servicios</a>
    </div>
  </header>

  <main class="company-content">

    <section class="company-form-hero">
      <div>
        <h3><?= htmlspecialchars($servicio["nombre_servicio"]) ?></h3>
        <p>
          Añade las fechas, horas y plazas disponibles para que los usuarios puedan reservar esta actividad.
        </p>
      </div>
    </section>

  <?php if(isset($_GET["ok"])){ ?>
  <div class="booking-alert booking-alert-ok">
    <p>El horario se ha añadido correctamente.</p>
  </div>
<?php } ?>

    <section class="company-form-card">
      
      <form action="" method="post" class="company-service-form">
        <?php if($modoEditar){ ?>
            <input type="hidden" name="id_horario" value="<?=$idHorarioEditar?>">
        <?php } ?>
        <div class="form-grid">

          <div class="form-group">
            <label for="fecha">Fecha</label>
            <input 
              type="date" 
              id="fecha" 
              name="fecha"
              value="<?php echo $fecha; ?>"
            >
            <span class="form-error"><?php echo $fechaerror; ?></span>
          </div>

          <div class="form-group">
            <label for="plazas_maximas">Plazas disponibles</label>
            <input 
              type="number" 
              id="plazas_maximas" 
              name="plazas_maximas"
              min="1"
              placeholder="Ej. 15"
              value="<?php echo $plazas_maximas; ?>"
            >
            <span class="form-error"><?php echo $plazaserror; ?></span>
          </div>

          <div class="form-group">
            <label for="hora_inicio">Hora de inicio</label>
            <input 
              type="time" 
              id="hora_inicio" 
              name="hora_inicio"
              value="<?php echo $hora_inicio; ?>"
            >
            <span class="form-error"><?php echo $horainicioerror; ?></span>
          </div>

        </div>

        <?php if(!$tieneHorarios){ ?>
  <p class="form-error">Debes añadir al menos un horario para que tus actividades se muestren</p>
<?php } ?>

        <div class="form-actions">
        
          <button type="submit" name="enviar" class="btn-primary-company">
              <?= $modoEditar ? "Guardar cambios" : "Añadir horario" ?>
          </button>

          <a href="mis-servicios.php" 
  class="btn-secondary-company">
  Finalizar
</a>

        </div>

      </form>
    </section>
    <section class="company-form-card">
      <h3>Horarios existentes</h3>

      <?php if(empty($horarios)){ ?>
        <p>No hay horarios añadidos todavía.</p>
      <?php }else{ ?>

        <?php foreach($horarios as $horario){ ?>
          <div class="service-info-item">
            <span class="info-label">
              <?=date("d/m/Y", strtotime($horario["fecha"]))?> · 
              <?=substr($horario["hora_inicio"], 0, 5)?> - <?=substr($horario["hora_fin"], 0, 5)?>
            </span>

            <span class="info-value">
              <?=$horario["plazas_maximas"]?> plazas
            </span>
            <div class="schedule-actions">
              <a 
                href="gestionar-horarios.php?idservicio=<?=$idServicio?>&editar=<?=$horario["id"]?>" 
                class="btn-secondary-company">
                Editar
              </a>

              <form method="post" onsubmit="return confirm('¿Seguro que quieres eliminar este horario?');">
                <input type="hidden" name="id_horario" value="<?=$horario["id"]?>">
                <button type="submit" name="eliminar_horario" class="btn-reject">
                  Eliminar
                </button>
              </form>
            </div>
          </div>
        <?php } ?>

      <?php } ?>
    </section>

  </main>
</div>

</body>
</html>