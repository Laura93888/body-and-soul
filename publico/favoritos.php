<?php
session_start();

if(!isset($_SESSION["usuario"])){
  header("Location: index.php");
  exit();
}

$titulo = "<h1>Mis favoritos</h1>";
require_once("head.php");

$idUsuario = $_SESSION["usuario"];
$favoritos = $bdact->obtenerFavoritosUsuario($idUsuario);

?>

  <main class="favorites-page">
  <section class="favorites-section">
    <div class="container">

      <div class="favorites-intro">
        <span class="section-tag">Área personal</span>
        <h2>Mis favoritos</h2>
        <p>
          Aquí tienes las actividades que has guardado para revisarlas más tarde o reservarlas cuando quieras.
        </p>
      </div>

      <?php if(empty($favoritos)){ ?>

        <div class="empty-favorites">
          <h3>Aún no tienes favoritos</h3>
          <p>Cuando guardes una actividad como favorita, aparecerá aquí.</p>
          <a href="index.php" class="btn btn-primary">Explorar actividades</a>
        </div>

      <?php }else{ ?>

        <div class="favorites-grid">

          <?php foreach($favoritos as $fav){ ?>

            <?php
              $imagen = !empty($fav["imagen"]) ? "../" . $fav["imagen"] : "../img/default.jpg";

              if(!empty($fav["categoria_padre"]) && !empty($fav["subcategoria"])){
                $categoriaTexto = $fav["categoria_padre"] . " · " . $fav["subcategoria"];
              }else{
                $categoriaTexto = $fav["subcategoria"] ?? "Actividad";
              }
            ?>

            <article class="favorite-card">
              <div class="favorite-image-wrapper">
                <img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($fav["nombre_servicio"]) ?>">
                <button 
                  type="button"
                  data-url="gestionar-favorito.php?idservicio=<?= $fav["id_servicio"] ?>"
                  class="favorite-icon-btn activity-favorite-btn activo"
                  aria-label="Quitar de favoritos"
                >
                  ❤️
                </button>
              </div>

              <div class="favorite-content">
                <p class="favorite-category"><?= htmlspecialchars($categoriaTexto) ?></p>

                <div class="favorite-top">
                  <h3><?= htmlspecialchars($fav["nombre_servicio"]) ?></h3>
                </div>

                <p class="favorite-description">
                  <?= htmlspecialchars($fav["descripcion"]) ?>
                </p>

                <div class="favorite-info">
                  <span><strong>Duración:</strong> <?= htmlspecialchars($fav["duracion"]) ?></span>
                  <span><strong>Ubicación:</strong> <?= htmlspecialchars($fav["lugar"]) ?></span>
                  <span><strong>Precio:</strong> <?= htmlspecialchars($fav["precio"]) ?> €</span>
                </div>

                <div class="favorite-actions">
                  <a href="actividad.php?idact=<?= $fav["id_servicio"] ?>" class="btn btn-outline">Ver actividad</a>
                  <a href="reserva.php?idact=<?= $fav["id_servicio"] ?>" class="btn btn-primary">Reservar</a>
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

