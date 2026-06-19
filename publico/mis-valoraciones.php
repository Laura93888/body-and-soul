<?php
session_start();

if(!isset($_SESSION["usuario"])){
  header("Location: index.php");
  exit();
}

$titulo = "<h1>Mis valoraciones</h1>";
require_once("head.php");

$idUsuario = $_SESSION["usuario"];
$resenas = $bdact->obtenerResenasUsuario($idUsuario);
?>

<main class="reviews-page">
  <section class="reviews-section">
    <div class="container">

      <div class="reviews-intro">
        <span class="section-tag">Área personal</span>
        <h2>Mis valoraciones</h2>
        <p>Consulta las valoraciones y comentarios que has dejado en las actividades.</p>
      </div>

      <?php if(empty($resenas)){ ?>
        <div class="review-card">
          <div class="review-content">
            <h3>Aún no has dejado valoraciones</h3>
            <p class="review-comment">
              Cuando valores una actividad, aparecerá aquí.
            </p>
            <div class="review-actions">
              <a href="index.php" class="btn btn-primary">Explorar actividades</a>
            </div>
          </div>
        </div>
      <?php }else{ ?>

        <div class="reviews-list">
          <?php foreach($resenas as $resena){ ?>

            <article class="review-card">

              <div class="review-image">
                <?php
                  $imagen = !empty($resena["imagen"]) 
                    ? "../" . $resena["imagen"] 
                    : "../img/placeholder.jpg";
                ?>
                <img src="<?= $imagen ?>" alt="<?= htmlspecialchars($resena["nombre_servicio"]) ?>">
              </div>

              <div class="review-content">

                <div class="review-top">
                  <div>
                    <p class="review-category">Actividad valorada</p>
                    <h3><?= htmlspecialchars($resena["nombre_servicio"]) ?></h3>
                  </div>

                  <span class="review-date">
                    <?= date("d/m/Y", strtotime($resena["fecha"])) ?>
                  </span>
                </div>

                <div class="review-rating">
                  <span class="stars">
                    <?php
                    for($i = 1; $i <= 5; $i++){
                      echo $i <= $resena["puntuacion"] ? "★" : "☆";
                    }
                    ?>
                  </span>
                  <span class="rating-value"><?= (int)$resena["puntuacion"] ?>/5</span>
                </div>

                <p class="review-comment">
                  <?= htmlspecialchars($resena["comentario"]) ?>
                </p>

                <div class="review-actions">
                  <a href="actividad.php?idact=<?= $resena["id_servicio"] ?>" class="btn btn-primary">
                    Ver actividad
                  </a>
                </div>

              </div>
            </article>

          <?php } ?>
        </div>

      <?php } ?>

    </div>
  </section>
</main>

<?php require_once("footer.php"); ?>
</body>
</html>