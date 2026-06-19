<?php
$paginaActiva = "gestionar-categoria";
$tituloPagina = "Agregar o eliminar categorías";
$etiquetaPagina = "Gestión de categorías";
$cssExtra = [
    "../css/admin-styles/usuarios.css",
    "../css/admin-styles/crear-categoria.css",
    "../css/admin-styles/reportes.css",
];

require_once("head-admin.php");

if(isset($_GET["eliminar_categoria"])){
    $idCategoria = (int) $_GET["eliminar_categoria"];

    $resultado = $bdadmin->EliminarCategoriaAdmin($idCategoria);

    header("Location: crear-categoria.php?resultado_eliminar=" . $resultado);
    exit();
}

$mensaje = "";
$error = "";

if(isset($_GET["resultado_eliminar"])){
    if($_GET["resultado_eliminar"] == "ok"){
        $mensaje = "Categoría eliminada correctamente.";
    }else if($_GET["resultado_eliminar"] == "tiene_subcategorias"){
        $error = "No puedes eliminar esta categoría porque tiene subcategorías asociadas.";
    }else if($_GET["resultado_eliminar"] == "tiene_servicios"){
        $error = "No puedes eliminar esta subcategoría porque tiene actividades asociadas.";
    }
}

$categoriasPadre = $bdadmin->ObtenerCategoriasPadreAdmin();

if(isset($_POST["crear_categoria"])){

    $nombre = trim($_POST["nombre_categoria"] ?? "");
    $tipo = $_POST["tipo_categoria"] ?? "";
    $idPadre = null;

    if($nombre == ""){
        $error = "Debes introducir un nombre para la categoría.";
    }else{

        if($tipo == "subcategoria"){
            $idPadre = (int) ($_POST["categoria_padre"] ?? 0);

            if($idPadre <= 0){
                $error = "Debes seleccionar una categoría padre.";
            }
        }

        if($error == ""){
            if($bdadmin->ExisteCategoriaAdmin($nombre, $idPadre)){
                $error = "Ya existe una categoría con ese nombre.";
            }else{
                $bdadmin->CrearCategoriaAdmin($nombre, $idPadre);
                header("Location: crear-categoria.php?ok=1");
                exit();
            }
        }
    }
}

if(isset($_GET["ok"])){
    $mensaje = "Categoría creada correctamente.";
}

$categorias = $bdadmin->ObtenerCategoriasAdmin();
$totalPadres = 0;
$totalSubcategorias = 0;

foreach($categorias as $categoria){
    if($categoria["id_categoria_padre"] == null){
        $totalPadres++;
    }else{
        $totalSubcategorias++;
    }
}
?>

<main class="admin-content">

  <section class="admin-hero-card">
    <div class="admin-hero-text">
      <span class="section-badge">Categorías</span>
      <h3>Gestiona categorías</h3>
      <p>
        Añade o elimina categorías principales o subcategorías para organizar mejor las actividades publicadas en la plataforma.
      </p>
    </div>
    <div class="services-stats">
      <div class="admin-hero-stat">
        <span class="admin-hero-number"><?= $totalPadres ?></span>
        <span class="admin-hero-label">Categorías</span>
      </div>
      <div class="admin-hero-stat">
        <span class="admin-hero-number" style="margin-top:0.7rem;"><?= $totalSubcategorias ?></span>
        <span class="admin-hero-label">Subcategorías</span>
      </div>
    </div>
  </section>

  <?php if($mensaje != ""): ?>
    <div class="admin-alert admin-alert-ok">
      <?= htmlspecialchars($mensaje) ?>
    </div>
  <?php endif; ?>

  <?php if($error != ""): ?>
    <div class="admin-alert admin-alert-error">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <section class="admin-form-card">
    <div class="admin-form-header">
      <h3>Datos de la categoría</h3>
      <p>Selecciona si quieres crear una categoría principal o una subcategoría.</p>
    </div>

    <form action="" method="post" class="admin-category-form">

      <div class="form-group">
        <label for="nombre_categoria">Nombre</label>
        <input 
          type="text" 
          id="nombre_categoria" 
          name="nombre_categoria" 
          placeholder="Ejemplo: Natación, Crossfit, Relajación..."
        >
      </div>

      <div class="form-group">
        <label for="tipo_categoria">Tipo</label>
        <select id="tipo_categoria" name="tipo_categoria">
          <option value="principal">Categoría principal</option>
          <option value="subcategoria">Subcategoría</option>
        </select>
      </div>

      <div class="form-group" id="grupo_categoria_padre">
        <label for="categoria_padre">Categoría padre</label>
        <select id="categoria_padre" name="categoria_padre">
          <option value="">Selecciona una categoría padre</option>

          <?php foreach($categoriasPadre as $catPadre): ?>
            <option value="<?= $catPadre["id_categoria"] ?>">
              <?= htmlspecialchars($catPadre["nombre"]) ?>
            </option>
          <?php endforeach; ?>

        </select>
      </div>

      <button type="submit" name="crear_categoria" class="btn-submit-category">
        Crear categoría
      </button>

    </form>
  </section>

  <section class="admin-form-card" style="margin-top: 1.5rem;">
    <div class="admin-form-header">
      <h3>Categorías existentes</h3>
      <p>Listado actual de categorías principales y subcategorías.</p>
    </div>

    <div class="report-table">

      <div class="report-table-row report-table-head">
        <span>Categoría</span>
        <span>Tipo</span>
        <span>Padre</span>
        <span>Acción</span>
      </div>

      <?php foreach($categorias as $categoria): ?>
        <div class="report-table-row categoria-row">
          <span><?= htmlspecialchars($categoria["nombre"]) ?></span>

          <span>
            <?= $categoria["id_categoria_padre"] == null ? "Principal" : "Subcategoría" ?>
          </span>

          <span class="categoria-padre">
            <?= htmlspecialchars($categoria["categoria_padre"] ?? "—") ?>
          </span>

          <a href="crear-categoria.php?eliminar_categoria=<?= $categoria["id_categoria"] ?>"
            class="btn-reject"
            onclick="return confirm('¿Seguro que quieres eliminar esta categoría?');">
            Eliminar
          </a>
        </div>
      <?php endforeach; ?>

    </div>
  </section>

</main>

</div>
</div>

<script>
const tipoCategoria = document.getElementById("tipo_categoria");
const grupoPadre = document.getElementById("grupo_categoria_padre");

function controlarCategoriaPadre(){
    if(tipoCategoria.value === "subcategoria"){
        grupoPadre.style.display = "flex";
    }else{
        grupoPadre.style.display = "none";
    }
}

tipoCategoria.addEventListener("change", controlarCategoriaPadre);
controlarCategoriaPadre();
</script>

</body>
</html>