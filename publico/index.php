<?php
$titulo="<h1>Bienvenido a Body and Soul</h1>";

require_once("head.php");
$actividadesMapa = $bdact->obtenerActividadesMapaInicio();
?>

  <main>
    
    <section class="hero-section">
      <div class="container">
        <div class="hero-box">
          <div class="hero-text">
            <span class="section-tag">Descubre experiencias</span>
            <h2>¿Buscando algún plan entretenido?</h2>
            <p>
              Encuentra actividades de deporte, bienestar y experiencias que te ayuden a cuidarte por dentro y por fuera.
            </p>
          </div>

          
            <form class="home-search-form" action="resultados.php" method="get">
              <div class="home-search-block">
              <label for="buscador" class="sr-only">Buscar actividad</label>
              <input
                type="text"
                id="buscador"
                name="buscador"
                class="search-input"
                placeholder="¿Qué te gustaría hacer?"
              />

              <label for="ubicacion" class="sr-only">Ubicación</label>
              <input 
                type="text"
                id="ubicacion"
                name="ubicacion"
                class="search-input"
                placeholder="¿Donde te gustaría realizar la actividad?"
              />
            </div>
            <div class="home-filter-row">
              <select id="categoria_filtro" name="categoria" class="filter-pill filter-select">
                <option value="">Categoría</option>
                <?php foreach ($categorias as $categoria) { ?>
                  <option value="<?= htmlspecialchars($categoria["nombre"]) ?>">
                    <?= htmlspecialchars($categoria["nombre"]) ?>
                  </option>
                <?php } ?>
              </select>
              <button type="button" id="filtroHoy" class="filter-pill">Hoy</button>
              <button type="button" id="filtroSemana" class="filter-pill">Esta semana</button>

              <select id="precio" name="precio" class="filter-pill filter-select">
                <option value="">Precio</option>
                <option value="0-10">0€ - 10€</option>
                <option value="10-25">10€ - 25€</option>
                <option value="25-50">25€ - 50€</option>
                <option value="50+">Más de 50€</option>
              </select>
            </div>
            <button type="submit" style="display:none;"></button>
            </form>
          
        </div>
        <div id="mapa-index" class="mapa-index"></div>
        <div id="resultadosBusqueda" class="resultados-live"></div>
      </div>
    </section>
    <section class="featured-section">
      <div class="container">
        <div class="section-header">
          <span class="section-tag">Top actividades</span>
          <h2>Actividades más reservadas</h2>
          <p>
            Estas son algunas de las experiencias favoritas de nuestros usuarios.
          </p>
        </div>

        <div class="activities-grid">
          <?php
          $numact=4;
           foreach($actividadesmasreservadas as $act){
          if($numact>0){
          ?>
          <article class="activity-card">
            <div class="activity-image-wrapper">
              <?php if(isset($_SESSION["usuario"])){ 
                $esFavorito = $bdact->esFavorito($_SESSION["usuario"], $act["id_servicio"]);
              ?>
                <button 
                  type="button"
                  data-url="gestionar-favorito.php?idservicio=<?= $act["id_servicio"] ?>"
                  class="activity-favorite-btn <?= $esFavorito ? 'activo' : '' ?>"
                >
                  <?= $esFavorito ? '❤️' : '🤍' ?>
                </button>
              <?php } 
              $imagen = !empty($act["imagen"]) ? "../" . $act["imagen"] : "../assets/placeholder.jpg";
              ?>

              <img src="<?= $imagen ?>" alt="<?= htmlspecialchars($act["nombre_servicio"]) ?>" class="activity-image">
            </div>

            <div class="activity-content">
              <?php
                $datosRating = $bdact->obtenerMediaResenas($act["id_servicio"]);
              ?>

              <a href="actividad.php?idact=<?= $act["id_servicio"] ?>#resenas" class="rating-link">
                <?php pintarRating($datosRating["media"], $datosRating["total"]); ?>
              </a>
              <h3><?=$act["nombre_servicio"]?></h3>
              <p>
                <?=$act["descripcion"]?>
              </p>
              <a href="actividad.php?idact=<?=$act["id_servicio"]?>" class="btn btn-primary btn-full" aria-label="Ver actividad <?=$act["nombre_servicio"]?>">Ver actividad</a>
            </div>
          </article>
          <?php
           }
           $numact--;
          }
          ?>  

        </div>
      </div>
    </section>
    
  </main>

<script>
//Necesitamos pasar a mi script las subcategorias organizadas para usarlas con el filtro
let subcategoriasPorPadre = <?= json_encode($subcategoriasPorPadre, JSON_UNESCAPED_UNICODE); ?>;
const actividadesMapa = <?= json_encode($actividadesMapa, JSON_UNESCAPED_UNICODE); ?>;
</script>

<script>
  
  document.addEventListener("DOMContentLoaded", function () {

    const input = document.getElementById("buscador");
    const inputUbicacion = document.getElementById("ubicacion");
    const contenedor = document.getElementById("resultadosBusqueda");
    const categoria = document.getElementById("categoria_filtro");
    const precio = document.getElementById("precio");
    const botonHoy = document.getElementById("filtroHoy");
    const botonSemana = document.getElementById("filtroSemana");

    let fecha = "";

    const mapaIndex = L.map("mapa-index").setView([40.4168, -3.7038], 9);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "© OpenStreetMap"
    }).addTo(mapaIndex);

    let marcadoresMapa = [];

    const formulario = document.querySelector(".home-search-form");
    formulario.addEventListener("submit", function(e){
      e.preventDefault();

      const texto = input.value.trim();
      const ubi = inputUbicacion.value.trim();
      const cat = categoria.value;
      const pre = precio.value;

      window.location.href =
        "resultados.php?buscador=" + encodeURIComponent(texto) +
        "&categoria=" + encodeURIComponent(cat) +
        "&precio=" + encodeURIComponent(pre) +
        "&fecha=" + encodeURIComponent(fecha) +
        "&ubicacion=" + encodeURIComponent(ubi);
    });

    function buscar() {
      const texto = input.value.trim();
      const cat = categoria.value;
      const pre = precio.value;
      const ubi = inputUbicacion.value.trim();

      if (texto.length < 2 && ubi.length < 2 && cat === "" && pre === "" && fecha === "") {
        contenedor.innerHTML = "";
        return;
      }

      fetch(
        "ajax_busqueda.php?buscador=" + encodeURIComponent(texto) +
        "&categoria=" + encodeURIComponent(cat) +
        "&precio=" + encodeURIComponent(pre) +
        "&fecha=" + encodeURIComponent(fecha) +
        "&ubicacion="+ encodeURIComponent(ubi)
      )
        .then(res => res.text())
        .then(data => {
          contenedor.innerHTML = data;
        });
      fetch(
        "ajax_mapa.php?buscador=" + encodeURIComponent(texto) +
        "&categoria=" + encodeURIComponent(cat) +
        "&precio=" + encodeURIComponent(pre) +
        "&fecha=" + encodeURIComponent(fecha) +
        "&ubicacion=" + encodeURIComponent(ubi)
      )
        .then(res => res.json())
        .then(actividades => {

          marcadoresMapa.forEach(marker => mapaIndex.removeLayer(marker));
          marcadoresMapa = [];

          actividades.forEach(act => {
            const marker = L.marker([
              parseFloat(act.latitud),
              parseFloat(act.longitud)
            ]).addTo(mapaIndex);

            marker.bindPopup(`
              <strong>${act.nombre_servicio}</strong><br>
              ${act.lugar}<br>
              <a href="actividad.php?idact=${act.id_servicio}">Ver actividad</a>
            `);

            marcadoresMapa.push(marker);
          });

          if(marcadoresMapa.length > 0){
            const grupo = L.featureGroup(marcadoresMapa);
            mapaIndex.fitBounds(grupo.getBounds().pad(0.2));
          }
        });
    }

    input.addEventListener("input", buscar);
    inputUbicacion.addEventListener("input", buscar);
    categoria.addEventListener("change", buscar);
    precio.addEventListener("change", buscar);

    botonHoy.addEventListener("click", function () {
      const hoy = new Date().toISOString().split("T")[0];

      if (fecha === hoy) {
        // 🔥 desactivar filtro
        fecha = "";
        this.classList.remove("active");
      } else {
        // activar hoy
        fecha = hoy;
        this.classList.add("active");
        botonSemana.classList.remove("active");
      }

      buscar();
    });

    botonSemana.addEventListener("click", function () {

      if (fecha === "semana") {
        // 🔥 desactivar filtro
        fecha = "";
        this.classList.remove("active");
      } else {
        // activar semana
        fecha = "semana";
        this.classList.add("active");
        botonHoy.classList.remove("active");
      }

      buscar();
    });

    actividadesMapa.forEach(actividad => {
      if(!actividad.latitud || !actividad.longitud){
          return;
      }
      const marker = L.marker([
        parseFloat(actividad.latitud),
        parseFloat(actividad.longitud)
      ]).addTo(mapaIndex);

      marker.bindPopup(`
        <strong>${actividad.nombre_servicio}</strong><br>
        ${actividad.nombre_empresa}<br>
        ${actividad.lugar}<br>
        ${actividad.precio} €<br>
        <a href="actividad.php?idact=${actividad.id_servicio}">
          Ver actividad
        </a>
      `);

      marcadoresMapa.push(marker);
    });

    if(marcadoresMapa.length > 0){
      const grupo = L.featureGroup(marcadoresMapa);
      mapaIndex.fitBounds(grupo.getBounds().pad(0.2));
    }

  });
</script>


 <?php
require_once("footer.php");
?>

</body>
</html>