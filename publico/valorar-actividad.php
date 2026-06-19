<?php
session_start();

require_once("../bd/bdact.php");
$bdact = new bdact("localhost", 3306, "plataforma_servicios1", "root", "");

if(!isset($_SESSION["usuario"])){
  header("Location: index.php");
  exit();
}

$titulo="<h1>Body and soul</h1>";

if(!isset($_GET["idreserva"])){
  header("Location: mis-reservas.php");
  exit();
}

$idUsuario = $_SESSION["usuario"];
$idReserva = (int) $_GET["idreserva"];

// 🔹 Obtener datos de la reserva
$reserva = $bdact->obtenerReservaPorId($idReserva);

// Seguridad básica
if(!$reserva || $reserva["id_usuario"] != $idUsuario){
  header("Location: mis-reservas.php");
  exit();
}

$idServicio = $reserva["id_servicio"];

// 🔹 Comprobar si puede valorar
$puedeValorar = $bdact->puedeValorar($idUsuario, $idServicio);
$yaValorado = $bdact->yaHaValorado($idUsuario, $idServicio);

if(!$puedeValorar || $yaValorado){
  header("Location: mis-reservas.php");
  exit();
}

// 🔹 Procesar formulario
if($_SERVER["REQUEST_METHOD"] == "POST"){

  $puntuacion = (int) $_POST["puntuacion"];
  $comentario = trim($_POST["comentario"]);

  if($puntuacion >= 1 && $puntuacion <= 5){
    $bdact->insertarResena($idUsuario, $idServicio, $puntuacion, $comentario);

    header("Location: mis-valoraciones.php");
    exit();
  }
}

require_once("head.php");

$titulo = "<h1>Valorar actividad</h1>";
?>

<main class="review-create-page">
  <section class="review-create-section">
    <div class="container">

      <div class="review-create-intro">
        <span class="section-tag">Valoración</span>
        <h2>Valora tu experiencia</h2>
      </div>

      <div class="review-create-card">

        <div class="review-create-summary">
          <span class="review-create-icon">★</span>
          <div>
            <p class="review-create-label">Actividad realizada</p>
            <h3><?= htmlspecialchars($reserva["nombre_servicio"]) ?></h3>
          </div>
        </div>

        <form method="post" class="review-create-form">

          <div class="form-group">
            <label for="puntuacion">Puntuación</label>
            <div class="stars-input">
              <input type="hidden" name="puntuacion" id="puntuacion" required>

              <span data-value="1">★</span>
              <span data-value="2">★</span>
              <span data-value="3">★</span>
              <span data-value="4">★</span>
              <span data-value="5">★</span>
            </div>
          </div>

          <div class="form-group">
            <label for="comentario">Comentario</label>
            <textarea 
              id="comentario"
              name="comentario" 
              rows="5" 
              placeholder="Cuenta tu experiencia, qué te gustó o qué mejorarías..."
              required
            ></textarea>
          </div>

          <div class="review-create-actions">
            <button type="submit" class="btn btn-primary">
              Enviar valoración
            </button>

            <a href="mis-reservas.php" class="btn btn-outline">
              Cancelar
            </a>
          </div>

        </form>

      </div>

    </div>
  </section>
</main>

<?php require_once("footer.php"); ?>

</body>
</html>