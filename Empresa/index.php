<?php
require_once("head.php");

$datosempresa=$bdempre->sacardatosempresa($idempresa);
$actividadesempresa=$bdempre->ObtenerActividadesPorEmpresa($idempresa);
$catempresa=$bdempre->ObtenerSubcategoriasEmpresa($idempresa);

$actividadesactivas=$bdempre->ObtenerServiciosActivos($idempresa);
$numactactivas=count($actividadesactivas);

?>

    <!-- CONTENIDO -->
    <div class="company-main">

      <header class="company-topbar">
        <div class="company-topbar-left">
          <span class="company-page-tag">Panel de gestión</span>
          <h2>Bienvenida, <?=$datosempresa["nombre_empresa"]?></h2>
        </div>

      </header>

      <main class="company-content">

        <!-- HERO -->
        <section class="company-hero-card">
          <div class="company-hero-text">
            <span class="company-section-badge">Resumen</span>
            <h3>Gestiona tu presencia en la plataforma</h3>
            <p>
              Desde aquí puedes consultar la información de tu empresa, ver las categorías en las que publicas
              y administrar los servicios que has subido a Body and Soul.
            </p>
          </div>

          <div class="company-hero-stat">
            <span class="company-hero-number"><?=$numactactivas?></span>
            <span class="company-hero-label">Servicios activos</span>
          </div>
        </section>

        <!-- TARJETAS RESUMEN LAS QUITAMOS??
        <section class="company-stats-grid">
          <article class="company-stat-card">
            <p class="company-stat-label">Reservas recibidas</p>
            <h3>42</h3>
            <span class="company-stat-detail">+8 esta semana</span>
          </article>

          <article class="company-stat-card">
            <p class="company-stat-label">Servicios activos</p>
            <h3>3</h3>
            <span class="company-stat-detail">Bienestar, Yoga, Meditación</span>
          </article>

          <article class="company-stat-card">
            <p class="company-stat-label">Valoración media</p>
            <h3>4.8</h3>
            <span class="company-stat-detail">Excelente experiencia</span>
          </article>
        </section>
         -->

        <!-- GRID PRINCIPAL -->
        <section class="company-panels-grid">

          <!-- INFO EMPRESA -->
          <article class="company-panel-card">
            <div class="company-panel-header">
              <div>
                <span class="company-section-badge">Empresa</span>
                <h3>Información general</h3>
              </div>
              <a href="perfil-empresa.html" class="company-panel-link">Editar</a>
            </div>

            <div class="company-info-grid">
              <div class="company-info-item">
                <span class="info-label">Nombre comercial</span>
                <span class="info-value"><?=$datosempresa["nombre_empresa"]?></span>
              </div>

              <div class="company-info-item">
                <span class="info-label">Categoría principal</span>
                <span class="info-value"><?=ucfirst($datosempresa["categoria_empresa"])?></span>
              </div>

              <div class="company-info-item">
                <span class="info-label">Ubicación</span>
                <span class="info-value"><?=$datosempresa["ciudad_empresa"]?></span>
              </div>

              <div class="company-info-item">
                <span class="info-label">Email</span>
                <span class="info-value"><?=$datosempresa["email"]?></span>
              </div>
            </div>
          </article>

          <!-- CATEGORÍAS -->
          <article class="company-panel-card">
            <div class="company-panel-header">
              <div>
                <span class="company-section-badge">Publicación</span>
                <h3>Categorías subidas</h3>
              </div>
            </div>

            <div class="company-tags">
              <?php foreach($catempresa as $cat){
                ?>
              <span class="company-tag"><?=$cat["nombre"]?></span>
              <?php
              }
              ?>
            </div>
          </article>

          <!-- SERVICIOS -->
          <article class="company-panel-card company-panel-card-wide">
            <div class="company-panel-header">
              <div>
                <span class="company-section-badge">Servicios</span>
              </div>
            </div>

            <div class="company-services-list">

            <?php foreach($actividadesempresa as $act){
              //Saco el nombre de la subcategoria con el id(todas las act tienen como categoria su subcategoria)
              $res=$bdact->ObtenerCategoriaConPadre($act["id_categoria"]);
             
              $cat=$res["subcategoria"];
              $subcat=$res["categoria_padre"];
              ?>
              <div class="company-service-item">
                <div class="company-service-info">
                  <h4><?=$act["nombre_servicio"]?></h4>
                  <p><?=$cat?> · <?=$subcat?> · <?=$act["precio"]?> €</p>
                </div>
                <span class="company-status-chip active">Activa</span>
              </div>
              <?php
              }
              ?>

            </div>
          </article>


        </section>

      </main>
    </div>
  </div>

</body>
</html>